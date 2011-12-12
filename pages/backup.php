<?php

	$bread = array(
		"?page=backup" => "Backups"
	);
	
	function template_BackupPlanListItem(BackupPlan $plan){
		echo "
			<div class='breadcrumb'>
				<h5>$plan->name <small>(id: $plan->id)</small></h5>
				<span>$plan->source &rarr; $plan->backup_dir</span>
				<div class='actions'>
					<div class='one'>
						<a href='?page=backup&id=$plan->id&action=backup'>Backup</a>
					</div>
					<div class='two'>
						<a href='?page=backup&id=$plan->id&action=backup'>Settings</a>
					</div>
				</div>
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