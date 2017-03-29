<div class="app-cam" style="width:100%;min-width:200px;max-width:500px;text-align:center;display:none;" id="step6">
	<P style="font-size:20px;"><b>Admin Account</b></p>
	
	<form method="POST" class="autoform">
		<input type="hidden" name="type" value="InstallUser"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>Name:</b></label>
		<input type="text" name="userName" value="" class="form-control1" id="userName"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>Username</b></label>
		<input type="text" name="userUsername" value="" class="form-control1"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>Password:</b></label>
		<input type="password" name="userPassword" value="" class="form-control1" style=""/>

		<button type="button" onclick="step5();" class="btn btn-default" style="width:80px;padding:10px;font-size:20px;">Back</button> 
		<button type="submit" onclick="step6();" class="btn btn-primary" style="width:200px;padding:10px;font-size:20px;">Next</button> 
	</form>

</div>

<script>
	function step6(){
		setTimeout(function(){
			$("#step6").fadeOut(100);
			$("#step7").delay(100).fadeIn();
		},500);
	}
</script>