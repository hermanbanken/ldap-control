<?php if ( $_GET['page'] == 'home'){ ?>
<ul class='menu' style='margin:0 auto; width:640px'>
	<?php foreach($pages as $page => $title){ if($page != 'home'){ ?>
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
	$title = $pages[$page];
?>
	<div class="container-fluid">
	      <div class="sidebar">
	        <div class="well">
	          <img src='banken-img/<?php echo $page ?>.png' style='width:180px' />
			  <h5 style='text-align:center'><?php echo $title; ?></h5>
	        </div>
	      </div>
	      <div class="content">
			<?php include('pages/'.$_GET['page']).'.php'; ?>
	      </div>
	    </div>
<?php } ?>