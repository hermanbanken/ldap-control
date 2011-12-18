<div class="topbar-wrapper" style="z-index: 5;">
<div class="topbar" data-dropdown="dropdown">
  <div class="topbar-inner">
    <div class="container">
      <h3><a href="."><?php echo $SETTINGS['authrealm']; ?></a></h3>
      <?php if($user) : ?>
	  <ul class="nav">
		<?php
			foreach($pages as $page => $details){
				list($title, $in_menu) = $details;
				if(!$in_menu) continue;
				$class = $page == $_GET['page'] ? 'active' : '';
				echo "<li class='$class'><a href='?page=$page'>";
				echo do_action('menu_title', $title);
				echo "</a></li>";
			}
		?>
      </ul>
      <!--<form class="pull-left" action="">
        <input type="text" placeholder="Search">
      </form>-->
      <ul class="nav secondary-nav">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle">
			<?php echo $user ? $user->cn : 'Account'; ?>
		  </a>
          <ul class="dropdown-menu">
            <li><a href="?page=user">Wijzig wachtwoord</a></li>
            <li><a href="?page=user">Pas gegevens aan</a></li>
            <li class="divider"></li>
            <li><a href="?logout=1">Uitloggen</a></li>
          </ul>
        </li>
      </ul>
	  <?php endif; ?>
    </div>
  </div><!-- /topbar-inner -->
</div><!-- /topbar -->
</div>