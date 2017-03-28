<script>
	<?php $count=0;
	$query = "SELECT * FROM sensors WHERE sensor_kind='".ucfirst(temp_decode($_GET['type']))."' AND enabled='1'";
	$resultsSensors = mysqli_query($GS_DBCONN, $query);
	while($result = mysqli_fetch_assoc($resultsSensors)) { $count++;	
	?>
		<?php if ($result['sensor_state']=='1') :?>
			$('#sensor<?php echo $result['ID'];?>_not_active').hide();
			$('#sensor<?php echo $result['ID'];?>_active').show();
		<?php else :?>
			$('#sensor<?php echo $result['ID'];?>_active').hide();
			$('#sensor<?php echo $result['ID'];?>_not_active').show();
		<?php endif;?>
	<?php } ?>
</script>