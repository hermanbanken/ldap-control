<?php 
require_once('mustache/Mustache.php');

class User extends Mustache {
	
	public static function processForm(){
		$l = LDAPAuth::getInstance();
		
		if($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		if(isset($_POST['formid']) && $_POST['formid'] == 'user.form'){
			$user = $l->is_authenticated();
			$editable = array("cn", "displayName", "sn", "mail", "jpegPhoto", "telephoneNumber", "postalCode", "l", "street");
			if($user && $user->uid === $_POST['uid']){
				$entry = array();
				// Fields
				foreach($_POST as $key => $value){
					if(in_array($key, $editable)){
						$entry[$key] = !empty($value) ? array(0 => $value) : array();
					}
				}
				// Password
				if(!empty($_POST['oldPassword'])){ 
					if($_POST['oldPassword'] === $_SESSION['PHP_AUTH_PW']){
						if(strlen($_POST['userPassword']) > 6){
							if($_POST['userPassword'] === $_POST['userPasswordC']){
								$entry['userPassword'] = array(LDAPAuth::ssha_encode($_POST['userPassword']));
							} else {
								alert_message("The entered passwords do not match", 'error', true);
							}
						} else {
							alert_message("The entered password was to short", 'error', true);
						}
					} else {
						alert_message("The old password is incorrect", 'error', true);
					}
				}
				// Perform mod
				try {
					$result = $l->modify($user->dn, $entry);
					alert_message("Successfully changed the profile data", 'success', true);
				} catch ( Exception $e ) {
					alert_message($e, 'error', true);
				}	
				header("Location: ".$_SERVER['REQUEST_URI']);
				exit;
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
	
	private function cache(){
		$_SESSION['User'][$this->uid] = $this;
	}
}
?>