<?php
require_once('interface.auth.php');
require_once('user.php');

class LDAPAuth implements iAuth {
	private $ds = false;
	public $user = false;
	private static $instance;
	
	public static function getInstance(){
		if(isset(self::$instance)) return self::$instance;
		else return false;
	}
	
	public function __construct($settings){
		self::$instance = $this;
		$this->settings = (object) $settings;
		if(!$this->ds) $this->connect();
		$this->is_authenticated();
	}
	
	public function is_connected(){
		return $this->ds && true;
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
		try{
			$this->ds = ldap_connect($this->settings->host, $this->settings->port);
			ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, $this->settings->version);
		} catch(Exception $e){
			
			$this->ds = false;
		}
	}
	
	public function is_authenticated(){
    	global $PHP_AUTH_USER;
    	global $PHP_AUTH_PW;
    	if ( $this->user ) 
    		return $this->user;
    	elseif (
			!empty($_SESSION['PHP_AUTH_USER']) && 
			!empty($_SESSION['PHP_AUTH_PW']) && 
			$this->user = $this->auth_user($_SESSION['PHP_AUTH_USER'], $_SESSION['PHP_AUTH_PW'])
		) {
			return $this->user;
		} else 
			return false;
	}
	
	public function get_users(){
    	$r = @ldap_search( $this->ds, $this->settings->basedn, 'objectclass=posixAccount');
		$u = array();
		if ($r) {
			$result = ldap_get_entries( $this->ds, $r);
			foreach($result as $user){
				if(!is_numeric($user))
				$u[] = User::FromLDAP($user);
			}
		} else {
			die("<h1 style='color:white;text-align: center'>Not connected to LDAP</h1>");
		}
		return $u;
	}
	
	public function auth_user($uid, $pass){
		$r = @ldap_search( $this->ds, $this->settings->basedn, 'uid=' . $uid);
        if ($r) {
            $result = ldap_get_entries( $this->ds, $r);
			if ($result[0]) {
                if (ldap_bind( $this->ds, $result[0]['dn'], $pass) ) {
					$this->user = User::FromLDAP($result[0]);
					$_SESSION['PHP_AUTH_USER'] = $uid;
					$_SESSION['PHP_AUTH_PW'] = $pass;
					$_SESSION['PHP_AUTH_CACHE'] = $result[0];
					return $this->user;
                }
            }
        } else {
			die("<h1 style='color:white;text-align: center'>Not connected to LDAP</h1>");
		}
		return false;
	}
	
	public function modify($dn, $entry){
		$r = ldap_modify( $this->ds, $dn, $entry);
		return $r;
	}
	
	public function logout_user(){
		unset($_SESSION['PHP_AUTH_USER']);
		unset($_SESSION['PHP_AUTH_PW']);
		unset($_SESSION['PHP_AUTH_CACHE']);
	}
	
	public function ask_auth(){
	    header('WWW-Authenticate: Basic realm="'.$this->settings->authrealm.'"');
	    header('HTTP/1.0 401 Unauthorized');
	    return NULL;
	}
	
}
?>