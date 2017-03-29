<div class="app-cam" style="width:100%;min-width:200px;max-width:500px;text-align:center;display:none;" id="step4">
	<P style="font-size:20px;"><b>Creating Database Structure</b></p>
	
	<span id="step4Text">
		<h3 id="step4Spinner">Installing <i class="faa-spin animated fa fa-spinner"></i></h3>
	</span>
	<button type="button" disabled onclick="step4();" class="btn btn-primary" style="width:200px;padding:10px;font-size:20px;margin-top:20px;" id="step4Btn">Next</button>
</div>

<script>
		function checkDB(){ //check db and create tables
			$("#step4Text").load("Step/4_Ajax.php");
		}
      

		function step4(){
			setTimeout(function(){
				$("#step4").fadeOut(100);
				$("#step5").delay(100).fadeIn();
			},0);
		}
	</script>