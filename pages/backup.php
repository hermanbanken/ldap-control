<?php
	require_once('lib/backup.class.php');
	require_once('lib/backup-manager.class.php');
	require_once('lib/diff.class.php');

	$bread = array(
		"?page=backup" => "Backups"
	);
	
	function template_BackupPlanListItem(BackupPlan $plan){
		echo "
			<div class='backup list-item'>
				<h5>
					<a href='?page=backup&id=$plan->id'>$plan->name</a>
					<small>(id: $plan->id)</small>
				</h5>
				<span>$plan->source &rarr; $plan->backup_dir</span>
				<div class='menu'><ul class='actions'>
					<li class='one'>
						<a href='?page=backup&id=$plan->id&action=backup'>Backup</a>
					</li>
					<li class='two'>
						<a href='?page=backup&id=$plan->id&action=backup'>Settings</a>
					</li>
				</ul></div>
			</div>";
	}
	function template_BackupPlanSingle(BackupPlan $plan){
		echo "
			<div class='backup single'>
				<div class='col1'>
				<div class='box changes'>
					<div class='bar'><h5>
						Changes since <select><option>15-12-2011</option></select>
					</h5></div>
					<div class='actionbar'>
						Select all <small>3 files, 2 additions, 3 deletions</small>
					</div>
					<ul class='filelist selectable'>
						<li><span class='label success'>New</span> File 1</li>
						<li>File 2</li>
						<li><span class='label important'>DELETED</span> File 3</li>
					</ul>
					<div class='bottom folder'><small>Source: $plan->source</small></div>
				</div>
				<div class='box backups'>
					<div class='bar'><h5>Backups by date 
					<button type='submit' class='btn primary'>Backup now</button></h5></div>
					<ul class='backuplist'>
						<li>Backup 1 <small>2 days ago</small></li>
						<li>Backup 2 <small>4 days ago</small></li>
						<li>Backup 3 <small>1 week ago</small></li>
					</ul>
				</div>
				</div>
				<div class='col2'>
				<div class='box diff'>
					<div class='bar'>Wijzigingen</div>
					<div class='diff-view'>".BackupPlan::diff($plan->backup_dir.'/'.$plan->id, $plan->source)."</div>
					<ul><li>No file selected</li></ul>
				</div>
				</div>
			</div>";
	}
	
	// All plans
	$plans = array(
		new BackupPlan("herman", array(
			"name"=>"Thuismap Herman",
			"source"=> realpath('./test/homefolder'),
			"backup_dir"=> realpath('./test/backups')
		)),
		new BackupPlan("system", array(
			"name"=>"Server Systeem",
			"source"=> realpath('./test/system'),
			"backup_dir"=> realpath('./test/backups')
		)),
	);
	
	// By ID
	$ids = array();
	foreach($plans as $p){
		$ids[$p->id] = $p;
	}
	
	// Is single?
	if(isset($_GET['id']) && array_key_exists($_GET['id'], $ids)){ 
		$single = $ids[$_GET['id']];
		$bread["?page=backup&id=".$single->id] = $single->name;
	}
	
	// Print navigation
	print_breadcrumb($bread);
	
	// Print all
	echo '<div class="backup-content">';
	echo '<div class="plans">';
	foreach($plans as $p){
		$ids[] = $p->id;
		template_BackupPlanListItem($p);
	}
	echo '</div>';
	
	// Print single
	if(isset($single)){ 
		?><div class='single'>
			<?php template_BackupPlanSingle($single); ?>
		</div><?php 
	}
	echo '</div>';
?>
<script>
	$('.backup > .menu').click(function(e){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		} else {
			var menu = this;
			$(this).addClass('active');
			setTimeout(function(){
				$(window).one('click', function(){
					$(menu).removeClass('active');
				});
			}, 0);
		}
	});
	
	$('li.selectable, .selectable > li').click(function(){
		$(this).parent().find('.selected').removeClass('selected');
		$(this).addClass('selected');
	});
</script>