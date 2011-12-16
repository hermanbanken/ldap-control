<?php

	$bread = array(
		"?page=backup" => "Backups"
	);
	
	function template_BackupPlanListItem(BackupPlan $plan){
		echo "
			<div class='breadcrumb backup'>
				<h5>$plan->name <small>(id: $plan->id)</small></h5>
				<span>$plan->source &rarr; $plan->backup_dir</span>
				<ul class='actions'>
					<li class='one'>
						<a href='?page=backup&id=$plan->id&action=backup'>Backup</a>
					</li>
					<li class='two'>
						<a href='?page=backup&id=$plan->id&action=backup'>Settings</a>
					</li>
				</ul>
			</div>";
	}
	
	print_breadcrumb($bread);
	
	include('lib/backup.class.php');
	include('lib/backup-manager.class.php');
	
	$plans = array(
		new BackupPlan("herman", array(
			"name"=>"Thuismap Herman"
		)),
		new BackupPlan("system", array(
			"name"=>"Server Systeem"
		)),
	);
	
	foreach($plans as $p) template_BackupPlanListItem($p);
	
?>