<form class='well' method='post' action='?page=user'>
	<div class='row'>
	<div class='span7'>
	<fieldset>
    	<legend>Address data</legend>
        <div class="clearfix">
            <label for="commonName">Full name</label>
            <div class="input">
              <input class="large" id="commonName" name="commonName" size="30" type="text" placeholder="CommonName">
            </div>
		</div>
		<div class="clearfix">
			<label for="mail">Email</label>
            <div class="input">
              <input class="large" id="mail" name="mail" size="30" type="text" placeholder="name@mailserver.tld">
            </div>
		</div>
		<div class="clearfix">
	        <label for="localityName">Address</label>
			<div class="input">
				<input class="large" type="text" placeholder="Street" name="postalAddress">
			</div>
		</div>
		<div class="clearfix">
			<div class="input"><div class="inline-inputs">
            	<input class="small" type="text" placeholder="City" name="localityName">
	           	<input class="mini" type="text" placeholder="Postalcode" name="postalCode">
            </div></div>
		</div>
	</fieldset>
	<fieldset>
    	<legend>Login data</legend>
        <div class="clearfix">
            <label for="oldPassword">Old Password</label>
            <div class="input">
              <input class="large" id="oldPassword" name="oldPassword" size="30" type="text" placeholder="secret">
            </div>
		</div>
		<div class="clearfix">
			<label for="userPassword">Password</label>
            <div class="input">
              <input class="large" id="userPassword" name="userPassword" size="30" type="text" placeholder="secret">
            </div>
		</div>
		<div class="clearfix">
			<label for="userPasswordC">Confirm</label>
            <div class="input">
              <input class="large" id="userPasswordC" name="userPasswordC" size="30" type="text" placeholder="secret">
            </div>
		</div>
	</fieldset>
	</div>
	<div class='span3 form-stacked'>
		<fieldset>
	    	<legend>Profile photo</legend>
	        <div class="clearfix">
	            <div class="input">
	              <input class="large" id="jpegPhoto" name="jpegPhoto" type="file">
	            </div>
			</div>
		</fieldset>
	</div>
	</div>
	<div class="actions">
 		<input type="submit" class="btn primary" value="Save changes">
		<button type="reset" class="btn">Cancel</button>
	</div>
</form>