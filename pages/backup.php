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
		$backups = $plan->get_backups();
		$option = array();
		foreach($backups as $b){
			$sel = $_GET['ref'] == $b->time ? 'selected' : '';
			$option[] = "<option value='$b->time' $sel>".date("Y-m-d H:i:s", $b->time)."</option>";
		}
		
		echo "
			<form method='get'><div class='backup single'>
				<input type='hidden' name='page' value='".urlencode($_GET['page'])."' />
				<input type='hidden' name='id'   value='".urlencode($_GET['id'])."' />
				<div class='col1'>
				<div class='box changes'>
					<div class='bar'><h5>
						Changes <small>since <select name='ref' class='ref'>".implode('', $option)."</select></small>
					</h5></div>";
		
		$item = array();
		$counts = array('a' => 0, 'd' => 0, 'c' => 0, 't' => 0);
		$diff = $plan->changed(isset($_GET['ref']) ? $_GET['ref'] : false);
		$labels = array('a'=>'success', 'd'=>'important', 'c'=>'warning');
		$title = array('a'=>'New', 'd'=>'Deleted', 'c'=>'Changed');
		foreach($diff->changelist() as $file){
			$name = $file['old'] === $file['new'] ? $file['old'] : $file['old'] .' &rarr; '. $file['new'];
			$item[] = "<li><span class='label ".$labels[$file['status']]."'>".$title[$file['status']]."</span> $name</li>";
			$counts[$file['status']]++;
			$counts['t']++;
		}
		
		echo "		<div class='actionbar'>
						$counts[t] files <small>$counts[a] additions, $counts[d] deletions, $counts[c] changes</small>
					</div>
					<ul class='filelist selectable'>";
		
		foreach($item as $i) echo $i;
		
		echo "		</ul>
					<div class='bottom folder'><small>Source: $plan->source</small></div>
				</div>
				<div class='box backups'>
					<div class='bar'><h5>Backups by date 
					<button type='submit' class='btn primary' name='action' value='backup'>Backup now</button></h5></div>
					<ul class='backuplist'>";
		
		foreach($backups as $b){
			$dd = time() - $b->time;
			if($dd < 60)
				$datediff = "less than a minute ago";
			elseif($dd < 3600*2)
				$datediff = sprintf("%d minutes ago", floor($dd / 60));
			elseif($dd < 3600*24*2)
				$datediff = sprintf("%d hours ago", floor($dd / 3600));
			else
				$datediff = sprintf("%d days ago", floor($dd / 3600*24));
			
			global $user;
			$link = "afp://".$user->uid.'@'.shell_exec('hostname -f').':'.$b->dir();
			echo "<li>".date("Y-m-d H:i:s", $b->time)." <span class='hover'><a class='finder' title='Open in Finder' href='$link'>Open in Finder</a></span><small>$datediff</small></li>";
		}
	
		echo "		</ul>
				</div>
				</div>
				<div class='col2'>
				<div class='box diff'>
					<div class='bar'><h5>Wijzigingen</h5></div>
					<div class='diff-view'>".$diff."</div>
					<!--<ul><li>No file selected</li></ul>-->
				</div>
				</div>
			</div></form>";
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
	
	if(isset($single) && isset($_GET['action']) && $_GET['action'] == 'backup')
		$single->do_backup();
	
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
	
	$('form .ref').change(function(){
		$(this).closest('form').submit();
	});
</script>