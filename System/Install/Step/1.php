<div class="app-cam" style="width:100%;min-width:200px;max-width:500px;text-align:center;" id="step1">
	<P style="font-size:20px;"><b>Welcome, To Install <?php echo $GS_Config['SiteName'];?> Click The Button Below</b></p>
	<button onclick="step1();" type="button" class="btn btn-primary" style="width:200px;padding:10px;font-size:20px;">Install</button> 
</div>

<script>
	function step1(){
		setTimeout(function(){
			$("#step1").fadeOut(100);
			$("#step2").delay(100).fadeIn();
		},0);
	}
</script>