<?php 

$ldapconfig['host'] = '10.0.0.188';
$ldapconfig['port'] = NULL;
$ldapconfig['basedn'] = 'dc=fs,dc=hermanbanken,dc=nl';
$ldapconfig['authrealm'] = 'Banken';

class LDAPAuth {
	const host = '10.0.0.188';
	const port = NULL;
	const basedn = 'dc=fs,dc=hermanbanken,dc=nl';
	const authrealm = 'Banken';
	const ou = 'users';
	const version = 3;
	private $ds = false;
	private $user = false;
	
	public function __construct(){
		if(!$this->ds) $this->connect();
	}
	
	public static function ssha_encode($text) {
	  for ($i=1;$i<=10;$i++) {
	    $salt .= substr('0123456789abcdef',rand(0,15),1);
	  }
	  $hash = "{SSHA}".base64_encode(pack("H*",sha1($text.$salt)).$salt);
	  return $hash;
	}
	
	private function connect(){
		// Connect to LDAP server
		$this->ds = ldap_connect(self::host, self::port);
		ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, self::version);
	}
	
	public function is_authenticated(){
    	global $PHP_AUTH_USER;
    	global $PHP_AUTH_PW;
		if (
			$_SESSION['PHP_AUTH_USER'] != "" && 
			$_SESSION['PHP_AUTH_PW'] != "" && 
			$user = $this->auth_user($_SESSION['PHP_AUTH_USER'], $_SESSION['PHP_AUTH_PW'])
		) {
			return $user;
		} else 
			return false;
	}
	
	public function get_users(){
    	$r = ldap_search( $this->ds, self::basedn, 'objectclass=posixAccount');
		$u = array();
		if ($r) {
			$result = ldap_get_entries( $this->ds, $r);
			foreach($result as $user){
				if(!is_numeric($user))
				$u[] = (object)$user;
			}
		}
		return $u;
	}
	
	public function auth_user($uid, $pass){
		$r = @ldap_search( $this->ds, self::basedn, 'uid=' . $uid);
        if ($r) {
            $result = ldap_get_entries( $this->ds, $r);
			if ($result[0]) {
                if (ldap_bind( $this->ds, $result[0]['dn'], $pass) ) {
                	$this->user = $result[0];
					return $this->user;
                }
            }
        }
		return false;
	}
	
	public function ask_auth(){
	    header('WWW-Authenticate: Basic realm="'.self::authrealm.'"');
	    header('HTTP/1.0 401 Unauthorized');
	    return NULL;
	}
	
}

function ldap_authenticate() {
    global $ldapconfig;
    global $PHP_AUTH_USER;
    global $PHP_AUTH_PW;
    $PHP_AUTH_USER = 'herman';
	$PHP_AUTH_PW = 'de9db99e';
    if ($PHP_AUTH_USER != "" && $PHP_AUTH_PW != "") {
        $ds= ldap_connect($ldapconfig['host'],$ldapconfig['port']);
        $r = ldap_search( $ds, $ldapconfig['basedn'], 'uid=' . $PHP_AUTH_USER);
		
        if ($r) {
            $result = ldap_get_entries( $ds, $r);
			var_dump($result);
			exit;
            if ($result[0]) {
                if (@ldap_bind( $ds, $result[0]['dn'], $PHP_AUTH_PW) ) {
                    return $result[0];
                }
            }
        }
    }
    header('WWW-Authenticate: Basic realm="'.$ldapconfig['authrealm'].'"');
    header('HTTP/1.0 401 Unauthorized');
    return NULL;
}
/*
if (($result = ldap_authenticate()) == NULL) {
    echo('Authorization Failed');
    exit(0);
}
echo('Authorization success');
print_r($result);
*/
?>