<?php if ( $_GET['page'] == 'home'){ ?>
<ul class='menu' style='margin:0 auto; width:640px'>
	<?php foreach($pages as $page => $details){
			list($title, $in_menu) = $details;
			if($page != 'home' && $in_menu){ ?>
		<li class=''><a href='?page=<?php echo $page ?>'>
			<img src='img/<?php echo $page ?>.png' />
			<span><?php echo $title ?></span>
		</a></li>
	<?php }} ?>
</ul>
<?php 
}
elseif ( array_key_exists($_GET['page'], $pages)) 
{
	$page = $_GET['page'];
	$details = $pages[$page];
	list($title, $in_menu) = $details;
?>
	<div class="container-fluid">
	      <div class="sidebar">
	        <div class="well">
				
	          <img id="pageimg" src='img/<?php echo $page ?>.png' style='width:180px' />
			  <h5 style='text-align:center'>
				<?php echo do_action('page_title', $title); ?>
			  </h5>
	        </div>
	      </div>
	      <div class="content">
			<?php alert_stashed_messages(); ?>
			<?php include('pages/'.$_GET['page']).'.php'; ?>
	      </div>
	    </div>
<?php } ?>