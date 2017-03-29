<div class="app-cam" style="width:100%;min-width:200px;max-width:500px;text-align:center;display:none;" id="step3">
	<P style="font-size:20px;"><b>We Now Need To Install The Database</b></p>
	
	<form method="POST" class="autoform">
		<input type="hidden" name="type" value="InstallSql"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>SQL Server:</b></label>
		<input type="text" name="sqlSvr" value="localhost" class="form-control1"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>SQL Username:</b></label>
		<input type="text" name="sqlUsername" value="root" class="form-control1"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>SQL Password:</b></label>
		<input type="text" name="sqlPassword" value="" class="form-control1" style=""/>
		<button type="button" onclick="step2();" class="btn btn-default" style="width:80px;padding:10px;font-size:20px;">Back</button> 
		<button type="submit" onclick="step3();" class="btn btn-primary" style="width:200px;padding:10px;font-size:20px;">Next</button> 
	</form>
</div>

<script>
	function step3(){
		setTimeout(function(){
			$("#step3").fadeOut(500);
			$("#step4").delay(510).fadeIn();
			checkDB();
		},500);
	}
</script>