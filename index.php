<?php 
	session_start();
	// This file CAN be accessed directly
	define("BASEPATH", getcwd());
	
	require_once('lib/actions.php');
	require_once('lib/auth.ldap.php');
	require_once('lib/auth.dummy.php');
	
	// Fetch settings
	$DEFAULT_SETTINGS = json_decode(file_get_contents('settings.default.json'));
	$USER_DEFINED_SETTINGS = json_decode(file_get_contents('settings.json'));
	$SETTINGS = merge($DEFAULT_SETTINGS, $USER_DEFINED_SETTINGS);
	
	// Default page
	if( !isset($_GET['page']))
		$_GET['page'] = 'home';
		
	$pages = array(
		'home'=> array("Home", true), 
		//'calendar' => array("Agenda's", true), 
		//'address' => array("Adresboek", true), 
		//'media' => array("Mediatheek", true), 
		'backup' => array("Backups", true),
		'user' => array("%u", false)
	);
	
	$l = $SETTINGS['dummy'] ? new DummyAuth($SETTINGS) : new LDAPAuth($SETTINGS);

	// Logout when requested
	if( isset($_GET['logout'])){
		$l->logout_user();
		header('Location: .');
	}

	if(!$l->is_connected())
		die("Connecting LDAP server failed.");

	// Login when processing form
	if( $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['formid'] == 'user.login'){
		$l->auth_user($_POST['uid'],$_POST['pass']);
	}

	// Fetch user
	$user = $l->is_authenticated();
	
	// Process User updates
	User::processForm();
	
?><!DOCTYPE html>
<html>
<head>
<title>LDAP control</title>
<link rel='stylesheet/less' type='text/less' href='bootstrap/lib/bootstrap.less' />
<link rel='stylesheet/less' type='text/less' href='style.less' />
<link rel='stylesheet/less' type='text/less' href='backup.less' />
<link rel='stylesheet/less' type='text/less' href='diff.less' />
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
<script type="text/javascript" charset="utf-8">
    less.env = "development";
   // less.watch();
</script>
</head>

<body>
<?php

// Display menu
include('header.php');

// Show appropriate content
if($user) {
	include('content.php');
} else {
	include('login.php');
}

?>
<div class='clear'></div>
</body>

</html>