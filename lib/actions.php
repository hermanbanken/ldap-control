<?php
################ Functions ###############

$actions = array();
function register_action($a, $callback, $params = array()){
	global $actions;
	if(!isset($actions[$a])) $actions[$a] = array();
	$actions[$a][] = array($callback, $params);
}
function do_action($name, $data){
	global $actions;
	if(isset($actions[$name]))
	foreach($actions[$name] as $a){
		$data = call_user_func_array($a[0], array($data) + $a[1] );
	}
	return $data;
}

function print_breadcrumb($bread){
	$parts = array();
	while($crumb = each($bread)){
		$parts[] = "<a href='$crumb[key]'>$crumb[value]</a>";
	}
	echo "<ul class='breadcrumb'>";
	foreach($parts as $key => $part){
		if(isset($parts[$key+1]))
			echo "<li>$part <span class='divider'>/</span></li>";
		else
			echo "<li class='active'>".end($bread)."</li>";
	}
	echo "</ul>";
}

function alert_message($message, $type='info', $session = false){
	$alert = "<div class='alert-message $type'>
	  <a class='close' href='#' onclick='this.parentNode.parentNode.removeChild(this.parentNode);'>&#215;</a>
	  <p>".implode("</p><p>", explode("\n\n", $message))."</p></div>";
	if($session){
		if(!$_SESSION['alert']) $_SESSION['alert'] = array();
		$_SESSION['alert'][] = array($message, $type); 
	}else echo $alert;
}

function alert_stashed_messages(){
	if(isset($_SESSION['alert']) && count($_SESSION['alert']) > 0){
		foreach($_SESSION['alert'] as $key => $a){
			alert_message($a[0], $a[1]);
			unset($_SESSION['alert'][$key]);
		}
	}
}

function merge($a, $b){
	$c = (array)($a) + array();
	foreach((array) $b as $k => $v){
		$c[$k] = $v;
	}
	return $c;
}

################ Actions ###############

function user_title($title){
	global $user;
	return str_replace( '%u', $user->sn, $title );
}
register_action('page_title', 'user_title');

?>