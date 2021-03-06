<?php
require_once("lib/view-model.class.php");

class Diff extends ViewModel {
	private $diff;
	private $out = false;
	private $changes = false;
	private $files_add = false;
	private $files_mod = false;
	private $files_del = false;
	
	public function __construct($diff, $dir_orig = false, $dir_new = false){
		$this->diff = empty($diff) ? false : $diff;
		$this->dir_orig = $dir_orig;
		$this->dir_new = $dir_new;
	}
	
	public function has_diff(){ return $this->diff; }
	
	private function nochanges(){
		return "<div class='nochanges'>There a no changes</div>";
	}
	
	private function parse(){
		// If no changes
		if(!$this->diff) return $this->nochanges();
		// If changes
		$lines = explode("\n", $this->diff);
		$out = '';
		$changes = array();
		while($line = current($lines)){
			next($lines);
			
			// File names
			if(preg_match('/^diff (?<options>.*) (?<from>.*) (?<to>.*)$/', $line, $m)){
				// Do the files still exist
				$aE = file_exists($m['from']);
				$bE = file_exists($m['to']);
				// Complete file mod
				$status = $aE ? ($bE ? 'c' : 'd') : 'a';
				// Compress names
				if($this->dir_orig) 
					$m['from'] = str_replace($this->dir_orig . '/', '', $m['from']);
				if($this->dir_new) 
					$m['to'] = str_replace($this->dir_new . '/', '', $m['to']);
				// Save
				$changes[] = new DiffFile($m['from'], $m['to'], array("mods" => array(), "status"=>$status));
				$out .= "<tr class='file'><th class='orig'>Old</th><th class='new'>New</th><th>".$m['from']." -> ".$m['to']."</th></tr>\n";
			}
			// Modification identifiers
			elseif (preg_match('/^((?<o_s>[0-9]+)(,(?<o_e>[0-9])+)?)(?<mod>[a-z])((?<n_s>[0-9]+)(,(?<n_e>[0-9])+)?)$/', $line, $m)){
				$mod = $m;
				$changes[count($changes)-1]->{'mods'}[] = $m;
				$mod['o'] = $mod['o_s'];
				$mod['n'] = $mod['n_s'];
			}
			// Changed line
			elseif (preg_match('/^(?<what>[\<\>\\-]|-{3})( (?<code>.*))?$/', $line, $m)) {
				$class = 'cod';
				$out .= "<tr>";
				if($m['what'] == '<'){
					$class.= ' deleted';
					$out .= "<td class='orig'>".($mod['o']++)."</td><td class='new'>&nbsp;</td>";
				} elseif($m['what'] == '>'){
					$class.= ' added';
					$out .= "<td class='orig'>&nbsp;</td><td class='new'>".($mod['n']++)."</td>";
				}
				if( $m['what'] != '---')
					$out.= "<td width='100%' class='$class'><pre>".$m['code']."</pre></td>\n";
				
				$out .= "</tr>";
			}
		}
		$this->changes = $changes;
		$this->out = "<table class='code-diff'>$out</table>";
		return $this->out;
	}
	
	public function __toString(){
		return $this->out ? $this->out : $this->parse();
	}
	public function html(){ return $this->__toString(); }
	
	public function changelist(){
		if(!$this->changes) $this->parse();
		return $this->changes ? $this->changes : array();
	}
	
	public function add(){
		$key = 'files_add';
		if($this->{$key}) return $this->{$key};
		$this->{$key} = array();
		foreach($this->changelist() as $c) if($c->status == 'a') array_push($this->{$key},  $c);
		return $this->{$key};
	}
	public function mod(){
		$key = 'files_mod';
		if($this->{$key}) return $this->{$key};
		$this->{$key} = array();
		foreach($this->changelist() as $c) if($c->status == 'c') array_push($this->{$key},  $c);
		return $this->{$key};
	}
	public function del(){
		$key = 'files_del';
		if($this->{$key}) return $this->{$key};
		$this->{$key} = array();
		foreach($this->changelist() as $c) if($c->status == 'd') array_push($this->{$key},  $c);
		return $this->{$key};
	}
	public function files(){ return $this->changelist(); }
		
	public static function test(){}
}

class DiffFile extends ViewModel {
	public $status;
	public $old;
	public $new;
	public $mods;
	
	public function __construct($arg1, $arg2 = null, $data = array()){
		if(is_array($arg1) && $arg2 === null){
			$data = $arg1;
		} elseif(isset($arg2)){
			$this->old = $arg1;
			$this->new = $arg2;
		}
		foreach($data as $key => $val) $this->{$key} = $val;
	}
	
	public function name(){
		return $this->old === $this->new ? $this->old : $this->old .' &rarr; '. $this->new;
	}
	public function status_label(){
		$labels = array('a'=>'success', 'd'=>'important', 'c'=>'warning');
		return $labels[$this->status];
	}
	public function status_title(){
		$title = array('a'=>'New', 'd'=>'Deleted', 'c'=>'Changed');
		return $title[$this->status];
	}
}

// Direct access: test
if (!defined('BASEPATH')) Diff::test();
?>