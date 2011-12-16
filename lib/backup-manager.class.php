<?php
class BackupPlan {
	public $id;
	public $source;
	public $backup_dir;
	public $except = array();
	public $maxsize = 0;
	public $starttime = -1;
	public $interval = -1;
	
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
	
	public static function formatCommand($source, $dest, $current=false, $exclude=array(), $dry = false){
		$cmd = ""; $excl = "";
		if(count($exclude) > 0){
			foreach($exclude as $ex)
				$excl.= "--exclude '$ex' ";
		}
		$cmd.= "rsync -aP ".($dry ? '--dry-run ' : '')."$excl" . ($current ? "--link-dest='$current' " : '') . "'$source/' '$dest'\n";
		if($current && !$dry){
			$cmd.= "rm -f '$current'\n"; // current
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
		$backups = array();
		$dir = $this->backup_dir.'/'.$this->id;
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		        	if(substr($file,0,1) != '.')
		        	$backups[] = new Backup($file, $dir);
		        }
		        closedir($dh);
		    }
		}
		return $backups;
	}
	
	public function next_backup($ref = false){
		// Find current/compare backup
		$compare = $this->find_ref($ref);
		$current = $this->backup_dir."/$this->id/current";
		
		$new = new Backup($this->id, $this->backup_dir);
		return (self::formatCommand($this->source, $new->dir(), $current, array(), false));
	}
	
	public function do_backup(){
		print_r(shell_exec($this->next_backup()));
	}
	
	public function changed($ref_since = false){
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
		$e = array(".*");
		$o = '';
		foreach($e as $i) $o.= "-x '$i' ";
		return $o;
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