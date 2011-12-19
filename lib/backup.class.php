<?php
require_once("lib/view-model.class.php");

class Backup extends ViewModel {
	public $fn;
	public $name;
	public $time;
	public $new;
	public $path;
	public $selected;
	
	public function __construct($name, $path){
		if(self::parse_filename($name)){
			$this->constructFilename($name);
			$this->new = false;
		} else {
			$this->constructNew($name);
			$this->new = true;
		}
		$this->path = $path;
	}
	
	public function selected(){
		if(isset($_GET['ref']) && $this->time == $_GET['ref']) return true;
		return false;
	}
	
	public function constructFilename($filename){
		$this->fn = $filename;
		list($this->name, $this->time) = self::parse_filename($filename);
	}
	
	public function constructNew($name){
		$name = preg_replace('/[^a-z\-0-9A-Z]/', '-', $name);
		$this->fn = self::make_filename($name, time());
		$this->name = $name;
		$this->time = time();
	}
	
	public function dir(){
		return $this->path . '/' . self::make_filename($this->name, $this->time);
	}
	
	public static function parse_filename($filename){
		$r_d = '(?P<Y>\d{4})(?P<m>\d{2})(?P<d>\d{2}).(?P<h>\d{2})(?P<i>\d{2})(?P<s>\d{2})';
		$r = preg_match("/^(?P<name>[a-z\-0-9A-Z]+)_$r_d$/", $filename, $m);
		
		return $r ? array(
			$m['name'],
			mktime($m['h'], $m['i'], $m['s'], $m['m'], $m['d'], $m['Y'])
		) : false;
	}
	public static function make_filename($name, $time){
		return $name . '_' . date('Ymd.His', $time);
	}
	
	public function link(){
		global $user;
		return "afp://".$user->uid.'@'.shell_exec('hostname -f').':'.$this->dir();
	}
	
	public function equals(Backup $p){
		return (is_object($p) && $this->name === $p->name && $this->time === $p->time);
	}
	
	public static function test1(){
		$backup1 = new Backup("test");
		return $backup1->name == "test";
	}
	
	public static function test2(){
		$backup2 = new Backup("test_20110801.103211");
		return $backup2->name == "test";
	}
	
	public static function test3(){
		$backup2 = new Backup("test_20110801.103211");
		return $backup2->time == mktime(10, 32, 11, 8, 1, 2011);
	}
	
	public static function test4(){
		$backup3 = new Backup("test!4-af_13");
		return $backup3->name == "test-4-af-13";
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

// Direct access: test
if (!defined('BASEPATH')) Backup::test();
?>