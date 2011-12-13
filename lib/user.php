<?php 
class User {
	
	public static function FromLDAP($ldap){
		$user = new User();
		
		// Copy values
		foreach($ldap as $key => $value){
			if(is_object($value)){
				$i = 0;
				// Parse multi-values
				while(isset($value->{$i}) && $single = $value->{$i++}){
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
			} else {
				$user->{$key} = $value;
			}
		}
		return $user;
	}
	
}
?>