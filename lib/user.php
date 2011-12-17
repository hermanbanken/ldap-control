<?php 
require_once('mustache/Mustache.php');

class User extends Mustache {
	
	public function __construct(){
		if($_SERVER['REQUEST_METHOD'] == 'POST') $this->processForm();
	}
	
	public function processForm(){
		$l = LDAPAuth::getInstance();
		
		if(isset($_POST['formid']) && $_POST['formid'] == 'user.form'){
			$user = $l->is_authenticated();
			$editable = array("cn", "displayName", "sn", "mail", "jpegPhoto", "phone", "postalCode", "l");
			if($user && $user->uid === $this->uid){
				$entry = array();
				foreach($_POST as $key => $value){
					if(in_array($key, $editable)){
						$entry[$key] = array(0 => $value);
					}
				}
				$l->modify($user->dn, $entry);
			}
		}
	}
	
	public static function FromLDAP($ldap){
		$user = new User();
		
		// Copy values
		foreach($ldap as $key => $value){
			if(is_numeric($key)) continue;
			if(is_object($value)) $value = (array) $value;
			if(is_array($value)){
				$i = 0;
				// Parse multi-values
				while(current($value)){
					$single = current($value);
					if(key($value) !== 'count'){
						if(isset($user->{$key})){
							if(is_array($user->{$key})) {
								$user->{$key}[] = $single;
							} else {
								$user->{$key} = array($user->{$key}, $single);
							}
						} else {	
							$user->{$key} = $single;
						}
					}
					next($value);
				}
			} else {
				$user->{$key} = $value;
			}
		}
		return $user;
	}
}
?>