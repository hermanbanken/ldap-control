<?php
	require_once('lib/backup.class.php');
	require_once('lib/backup-manager.class.php');
	require_once('lib/diff.class.php');

	$bread = array(
		"?page=backup" => "Backups"
	);
	
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
		echo $p->renderTemplate("backup-plan.list-item");
	}
	echo '</div>';
	
	// Print single
	if(isset($single)){ 
		?><div class='single'>
			<?php echo $single->renderTemplate("backup-plan.single"); ?>
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