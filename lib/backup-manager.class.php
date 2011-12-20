<?php
require_once("lib/view-model.class.php");

class BackupPlan extends ViewModel {
	public $id;
	public $name;
	public $source;
	public $backup_dir;
	public $except = array();
	public $maxsize = 0;
	public $starttime = -1;
	public $interval = -1;
	protected static $excludes = array(".*");
	
	// Restore from settingsfile
	public function __construct($id = null, $data = array()){
		if(!$id){
			$c = "abcdefghijklmnopqrstuvwxyz0123456789";
			$this->id = "plan_";
			for($i = 0; $i < 10; $i++)
				$this->id .= $c[rand(0, strlen($c))];
		} else {
			$this->id = $id;
		}	
		foreach($data as $key => $value){
			$this->{$key} = $value;
		}
	}
	
	public static function diff($prev, $current){
		$cmd = "diff ".self::excludeOption()." -r -N $prev/ $current/";
		if($output = shell_exec($cmd)){
			return new Diff($output, $prev, $current);
		} else {
			if( $output === null ) return new Diff("", $prev, $current);
			else return "Something went wrong, we can't show the differences between the versions of the selected file.";
		}
	}
	
	public static function formatCommand($source, $dest, $current=false, $exclude=true, $dry = false){
		$cmd = "";
		
		// Gather options
		$options = array("-aP" => null);
		
		// Check for excluded files / dirs
		if($exclude === true)
			$options["--exclude"] = self::$excludes;
		if(is_array($exclude)){
			$options["--exclude"] = $exclude;
		}
		
		// Check if current backups exists
		if($current && file_exists($current)){
			$options['--link-dest'] = $current;
		}
		
		// Make command
		$cmd.= "rsync " . self::formatOptions($options) . " '$source/' '$dest'\n";
		if($current && !$dry){
			if(file_exists($current)) $cmd.= "rm -f '$current'\n"; // current
			$cmd.= "ln -s '$dest' '$current'\n"; // dest
		}
		return $cmd;
	}
	
	public function type(){
		if($starttime < 0 && $interval < 0)
			return 'manual';
		elseif($interval >= 3600)
			return 'scheduled';
	}
	
	public function equals(BackupPlan $p){
		return ($this->id === $p->id && $this->id != null);
	}
	
	public function get_backups(){
		if(isset($this->_backups)) return $this->_backups;
		
		$this->_backups = array();
		$dir = $this->backup_dir.'/'.$this->id;
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		        	if(substr($file,0,1) != '.' && $file != 'current')
		        	$this->_backups[] = new Backup($file, $dir);
		        }
		        closedir($dh);
		    }
		}
		return $this->_backups;
	}
	
	public function next_backup($ref = false){
		// Find current/compare backup
		$compare = $this->find_ref($ref);
		$current = $this->backup_dir."/$this->id/current";
		
		$new = new Backup($this->id, $this->backup_dir . '/'. $this->id);
		return (self::formatCommand($this->source, $new->dir(), $current, true, false));
	}
	
	public function do_backup(){
		if(!is_writable($this->backup_dir) || !is_readable($this->source)){
			if(!is_writable($this->backup_dir))
				alert_message("The backup directory is not writeable. Maybe missing some permissions.", 'error');
			if(!is_readable($this->source))
				alert_message("The source directory is not readable. Maybe missing some permissions.", 'error');
				
			alert_message("Commands that would have been run if permissions where OK:\n\n<pre style='color:black;'>".$this->next_backup()."</pre>", 'info');
		} else {
			alert_message(shell_exec($this->next_backup()), 'info');
			unset($this->_backups);
		}
	}
	
	public function changed($ref_since = false){
		if($ref_since === false && isset($_GET['ref'])){
			$ref_since = intval($_GET['ref']);
		}
		$compare = $this->find_ref($ref_since);
		
		return $compare ? $this->diff($compare->dir(), $this->source) : new Diff("");
	}
	
	private function find_ref($ref){
		$backups = $this->get_backups();
		
		// Find current/compare backup
		$compare = false;
		if(count($backups) > 0){
			foreach($backups as $b){
				if(is_a($ref, 'Backup') && $b->equals($ref) || $b->time == $ref || $b === end($backups)){
					$compare = $b;
				}
			}
		}
		return $compare;
	}
	
	public static function excludeOption(){
		$o = '';
		foreach(self::$excludes as $i) $o.= "-x '$i' ";
		return $o;
	}
	
	public static function formatOptions($options){
		$str = array();
		foreach($options as $key => $val){
			if(is_array($val)){ foreach($val as $v){ $str[] = "$key='$v'"; }}
			if($val === null) $str[] = $key;
			if(is_string($val)) $str[] = "$key='$val'";
		}
		return implode(" ", $str);
	}
	
	public static function test1(){
		$p = new BackupPlan();
		return $p->id;
	}
	public static function test2(){
		$p = new BackupPlan("hoi");
		return $p->id == "hoi";
	}
	public static function test3(){
		$p = new BackupPlan("hoi");
	}
	
	public static function test(){
		echo "Testing class ".get_class()."<br>";
		$i = 1;
		while(method_exists(get_class(), "test$i")){
			echo "Test $i: ". (call_user_func(array(get_class(), 'test'.$i)) ? "succeeded" : "failed") . "<br>";
			$i++;
		}
		echo "Tests done<br>";
	} 
}

class BackupManager {
	public $base_dir;
	public $settings;
	public $settingsfile = "backup.settings.json";
	
	public function __construct($base){
		if(is_file($base) || is_link($base)){
			$this->base_dir = dirname($base);
			$this->settingsfile = basename($base);
		} else if(is_dir($base)){
			$this->base_dir = $base;
		} else {
			throw new Exception("Given no valid file or directory.");
		}
		
		if(file_exists("$this->base_dir/$this->settingsfile")){
			$this->settings = json_decode(file_get_contents("$this->base_dir/$this->settingsfile"));
		}
	}
	
	public function get_plans(){
		return $settings['plans'];
	}
	
	public function add_plan(BackupPlan $plan){
		return array_push($settings['plans'], $plan);
	}
	
	public function remove_plan(BackupPlan $plan){
		foreach($settings['plans'] as $key => $p)
			if($plan->equals($p))
				unset( $settings['plans'][$key] );
		return count($settings['plans']);
	}
	
	public function __destruct(){
		file_put_contents("$this->base_dir/$this->settingsfile", json_encode($this->settings));
	}

	public static function test1(){
		if(file_exists('tmpfolder')) rrmdir('tmpfolder');
		try {
			$man = new BackupManager("tmpfolder");
			return false;
		} catch(Exception $e) { return true; }
	}
	
	public static function test2(){
		if(!file_exists('tmpfolder')) mkdir("tmpfolder");
		$man = new BackupManager("tmpfolder");
		return $man->base_dir == "tmpfolder";
	}
	
	public static function test3(){
		touch("tmp");
		$man = new BackupManager("tmp");
		return $man->settingsfile == "tmp";
	}
	
	public static function test4(){
		touch("tmp");
		$man = new BackupManager("tmp");
		return realpath($man->base_dir) == realpath(getcwd());
	}

	public static function test(){
		echo "Testing class ".get_class()."<br>";
		$i = 1;
		while(method_exists(get_class(), "test$i")){
			echo "Test $i: ". (call_user_func(array(get_class(), 'test'.$i)) ? "succeeded" : "failed") . "<br>";
			$i++;
		}
		echo "Tests done<br>";
	}
}

function rrmdir($dir) { 
	if (is_dir($dir)) { 
		$objects = scandir($dir); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
				if (filetype($dir."/".$object) == "dir") 
					rrmdir($dir."/".$object); else unlink($dir."/".$object); 
  			} 
		} 
		reset($objects); 
		rmdir($dir); 
	} 
}

// Direct access: test
if (!defined('BASEPATH')) BackupManager::test();
if (!defined('BASEPATH')) BackupPlan::test();
?>