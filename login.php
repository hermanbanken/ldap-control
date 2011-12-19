<ul class='users'>
	<?php 
		foreach($l->get_users() as $user){
			if(isset($user->jpegphoto)){
			?><li class="avatar" uid="<?php echo $user->uid; ?>">
				<div class='jpegPhoto'>
					<div class='glossy'></div>
					<img src="data:image/jpeg;base64,<?php echo base64_encode($user->jpegphoto); ?>" />
				</div>
				<span class='uid'><?php echo $user->cn; ?></span>
			</li><?php
			}
		}
	?>
	<form id='login' method='post'>
		<input type='hidden' name='formid' value='user.login' />
		<input type='hidden' id='uid' name='uid' />
		<input type='password' placeholder='Password...' id='password' name='pass' />
	</form>
	<script>
		$('.avatar').click(function(){
			$(this).addClass('active').parent().addClass('single');
			$('#uid').val($(this).attr('uid'));
			$('#password').val("").focus();
		});
		
		$('body').keyup(function(e){
			// Escape
			if(e.keyCode == 27){
				$('.avatar.active').removeClass('active');
				$('.users.single').removeClass('single');
			}
		});
	</script>
</ul>