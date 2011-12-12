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

################ Actions ###############

function user_title($title){
	global $user;
	return str_replace( '%u', $user['sn'][0], $title );
}
register_action('page_title', 'user_title');

?>