<?php 
	session_start(); 
	
	// Logout when requested
	if( isset($_GET['logout']) && $_GET['logout'] == '1'){
		$_SESSION = array();
		header('Location: .');
	}
	
	if( !isset($_GET['page']))
		$_GET['page'] = 'home';
		
	$pages = array(
		'home'=> "Home", 
		'calendar' => "Agenda's", 
		'address' => "Adresboek", 
		'media' => "Mediatheek", 
		'backup' => "Backups"
	);
	
?><!DOCTYPE html>
<html>
<head>
<title>Banken</title>
<link rel='stylesheet' type='text/css' href='banken.css' />
<link rel='stylesheet' type='text/css' href='bootstrap/bootstrap.min.css' />
<link rel='stylesheet/less' type='text/less' href='bootstrap/lib/bootstrap.less' />
<script src="jquery.min.js"></script>
<script src='bootstrap/js/bootstrap-alerts.js'></script>
<script src='bootstrap/js/bootstrap-buttons.js'></script>
<script src='bootstrap/js/bootstrap-dropdown.js'></script>
<script src='bootstrap/js/bootstrap-modal.js'></script>
<script src='bootstrap/js/bootstrap-twipsy.js'></script>
<script src='bootstrap/js/bootstrap-popover.js'></script>
<script src='bootstrap/js/bootstrap-scrollspy.js'></script>
<script src='bootstrap/js/bootstrap-tabs.js'></script>
<script src="less-1.1.5.min.js"></script>
</head>

<body>
<?php
include('ldap.php');
$l = new LDAPAuth();

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
	include('home.php');
} else {
	include('login.php');
}

?>
<div class='clear'></div>
</body>

</html>