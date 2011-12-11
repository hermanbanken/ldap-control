<div class="topbar-wrapper" style="z-index: 5;">
<div class="topbar" data-dropdown="dropdown">
  <div class="topbar-inner">
    <div class="container">
      <h3><a href="#">Banken</a></h3>
      <?php if($user) : ?>
	  <ul class="nav">
		<?php
			foreach($pages as $page => $title){
				$class = $page == $_GET['page'] ? 'active' : '';
				echo "<li class='$class'><a href='?page=$page'>$title</a></li>";
			}
		?>
      </ul>
      <form class="pull-left" action="">
        <input type="text" placeholder="Search">
      </form>
      <ul class="nav secondary-nav">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle">
			<?php echo $user ? $user['sn'][0] : 'Account'; ?>
		  </a>
          <ul class="dropdown-menu">
            <li><a href="#">Wijzig wachtwoord</a></li>
            <li><a href="#">Pas gegevens aan</a></li>
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