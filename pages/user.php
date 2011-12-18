<script>
$("#pageimg").attr('src', <?php 
	echo "\"data:image/jpeg;base64,".base64_encode($l->user->jpegphoto)."\"";
?>);
</script>
<?php
	echo $l->user->render(file_get_contents('templates/user.form.mustache'));
	// "Hello World!
?>