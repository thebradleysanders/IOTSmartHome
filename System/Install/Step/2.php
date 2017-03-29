<div class="app-cam" style="width:100%;min-width:200px;max-width:500px;text-align:center;display:none;" id="step2">
	<P style="font-size:20px;"><b>Checking Compatibility</b></p>
	<ul style="text-align:left;margin-bottom:20px;list-style:none;">
		<?php if (function_exists('mysqli_connect')) :?>
			<li style="color:green;"><i class="fa fa-check" aria-hidden="true"></i> MySqli is installed</li>
		<?php else: $error=1;?>
			<li style="color:red;"><i class="fa fa-times" aria-hidden="true"></i> MySqli is not installed</li>
		<?php endif;?>

		<?php if(function_exists('curl_version')):?>
			<li style="color:green;"><i class="fa fa-check" aria-hidden="true"></i> cURL is installed</li>
		<?php else : $error=1;?>
			<li style="color:red;"><i class="fa fa-times" aria-hidden="true"></i> cURL is not installed</li>
		<?php endif;?>
		
		<?php if((float)phpversion()>=5.5):?>
			<li style="color:green;"><i class="fa fa-check" aria-hidden="true"></i> PHP <?php echo phpversion();?> meets the minimum requirements of PHP 5.5 </li>
		<?php else: $error=1;?>
			<li style="color:red;"><i class="fa fa-times" aria-hidden="true"></i> PHP <?php echo phpversion();?> does not meet the requirements of PHP 5.5+</li>
		<?php endif;?>
	</ul>
	
	<?php if($error!=1):?>
		<button type="button" onclick="step2();" class="btn btn-primary" style="width:200px;padding:10px;font-size:20px;">Next</button> 
	<?php else:?>
		<button type="button" onclick="step2();" class="btn btn-primary" style="width:200px;padding:10px;font-size:20px;" disabled>Next</button> 
	<?php endif;?>
	

</div>

<script>
		function step2(){
			setTimeout(function(){
				$("#step2").fadeOut(500);
				$("#step3").delay(510).fadeIn();
			},0);
		}
	</script>