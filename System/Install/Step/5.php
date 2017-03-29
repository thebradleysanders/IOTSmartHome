<div class="app-cam" style="width:100%;min-width:200px;max-width:500px;text-align:center;display:none;" id="step5">
	<P style="font-size:20px;"><b>Basic Info</b></p>
	
	<form method="POST" class="autoform">
		<input type="hidden" name="type" value="InstallBasic"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>Name:</b></label>
		<input type="text" name="basicName" value="" class="form-control1" id="basicName"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>Home Address:</b></label>
		<input type="text" name="basicAddress" value="" class="form-control1"/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>Zip Code:</b></label>
		<input type="text" name="basicZip" value="" class="form-control1" style=""/>
		<label style="font-size:18px;width:100%;text-align:left;"><b>Email Address:</b></label>
		<input type="text" name="basicEmail" value="" class="form-control1" style=""/>
		<button type="button" onclick="step4();" class="btn btn-default" style="width:80px;padding:10px;font-size:20px;">Back</button> 
		<button type="submit" onclick="step5();" class="btn btn-primary" style="width:200px;padding:10px;font-size:20px;">Next</button> 
	</form>

</div>

<script>
	function step5(){
		setTimeout(function(){
			$("#step5").fadeOut(100);
			$("#step6").delay(100).fadeIn();
			$("#userName").val($("#basicName").val());
		},500);
	}
</script>