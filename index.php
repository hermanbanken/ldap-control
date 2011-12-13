<?php 
	session_start();
	// This file CAN be accessed directly
	define("BASEPATH", getcwd());
	
	include('lib/actions.php');
	
	// Fetch settings
	$DEFAULT_SETTINGS = json_decode(file_get_contents('settings.default.json'));
	$USER_DEFINED_SETTINGS = json_decode(file_get_contents('settings.json'));
	$SETTINGS = merge($DEFAULT_SETTINGS, $USER_DEFINED_SETTINGS);
	
	// Logout when requested
	if( isset($_GET['logout']) && $_GET['logout'] == '1'){
		$_SESSION = array();
		header('Location: .');
	}
	
	// Default page
	if( !isset($_GET['page']))
		$_GET['page'] = 'home';
		
	$pages = array(
		'home'=> array("Home", true), 
		'calendar' => array("Agenda's", true), 
		'address' => array("Adresboek", true), 
		'media' => array("Mediatheek", true), 
		'backup' => array("Backups", true),
		'user' => array("%u", false)
	);
	
?><!DOCTYPE html>
<html>
<head>
<title>LDAP control</title>
<link rel='stylesheet' type='text/css' href='style.css' />
<link rel='stylesheet' type='text/css' href='bootstrap/bootstrap.min.css' />
<link rel='stylesheet/less' type='text/less' href='bootstrap/lib/bootstrap.less' />
<script src="js/jquery.min.js"></script>
<script src='bootstrap/js/bootstrap-alerts.js'></script>
<script src='bootstrap/js/bootstrap-buttons.js'></script>
<script src='bootstrap/js/bootstrap-dropdown.js'></script>
<script src='bootstrap/js/bootstrap-modal.js'></script>
<script src='bootstrap/js/bootstrap-twipsy.js'></script>
<script src='bootstrap/js/bootstrap-popover.js'></script>
<script src='bootstrap/js/bootstrap-scrollspy.js'></script>
<script src='bootstrap/js/bootstrap-tabs.js'></script>
<script src="js/less-1.1.5.min.js"></script>
</head>

<body>
<?php
include('lib/auth.ldap.php');
$l = new LDAPAuth($SETTINGS);

if(!$l->is_connected())
	die("Connecting LDAP server failed.");

// Login when processing form
if( $_SERVER['REQUEST_METHOD'] == 'POST'){
	$_SESSION['PHP_AUTH_USER'] = $_POST['uid'];
    $_SESSION['PHP_AUTH_PW'] = $_POST['pass'];
}

// Fetch user
$user = $l->is_authenticated();

// Display menu
include('header.php');

// Show appropriate content
if( $user) {
	include('content.php');
} else {
	include('login.php');
}

?>
<div class='clear'></div>
</body>

</html>