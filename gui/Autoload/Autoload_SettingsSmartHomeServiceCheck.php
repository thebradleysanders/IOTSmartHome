<script>
	<?php 
	$count = 0;
	$query = "SELECT * FROM shbroker";
	$results_proc = mysqli_query($GS_DBCONN, $query);
	while($process = mysqli_fetch_assoc($results_proc)){ 
	?>
	
		<?php if($process['state']=="1"): $count++; ?>
			$('#SystemService<?php echo $process['ID'];?>_start').hide();
			$('#SystemService<?php echo $process['ID'];?>_stop').show();
		<?php else:?>
			$('#SystemService<?php echo $process['ID'];?>_stop').hide();
			$('#SystemService<?php echo $process['ID'];?>_start').show();
		<?php endif;?>
	
	<?php }?>
	
	<?php if ($count >0):?>
		$('#AllSystemService<?php echo $process['ID'];?>_start').hide();
		$('#AllSystemService<?php echo $process['ID'];?>_stop').show();
	<?php else:?>
		$('#AllSystemService<?php echo $process['ID'];?>_stop').hide();
		$('#AllSystemService<?php echo $process['ID'];?>_start').show();	
	<?php endif;?>
</script>