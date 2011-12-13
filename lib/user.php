<?php 
class User {
	
	public static function FromLDAP($ldap){
		$user = new User();
		
		// Copy values
		foreach($ldap as $key => $value){
			if(is_object($value)){
				$i = 0;
				$user->{$key} = array();
				while(isset($value->{$i}) && $single = $value->{$i++}) 
					$user->{$key}[] = $single;
				if(count($user->{$key}) == 1){
					$user->{$key} = end($user->{$key});
				}
			} else {
				$user->{$key} = $value;
			}
		}
		return $user;
	}
	
}
?>