<script>
	<?php 		
		$query = "SELECT * FROM device_groups ORDER BY ID ASC LIMIT 8 ";
		$devices = mysqli_query($GS_DBCONN, $query);
		while($GroupDevice = mysqli_fetch_assoc($devices)) {
			if($_GET['room_id']!=""){
				//this is for manage_room.php
				$query = "SELECT * FROM devices WHERE group_id='".$GroupDevice['ID']."' AND device_state='1' AND enabled='1' AND room='".temp_decode($_GET['room_id'])."' ORDER BY ID ASC ";
			}else{
				//this is for index.php
				$query = "SELECT * FROM devices WHERE group_id='".$GroupDevice['ID']."' AND device_state='1' AND enabled='1' ORDER BY ID ASC ";
			}
			
			$devices_a = mysqli_query($GS_DBCONN, $query);
			$DeviceCount = mysqli_num_rows($devices_a);
		?>
		<?php if ($DeviceCount>0) :?>
			$('#turnOnGroup_<?php echo $GroupDevice['ID'];?>').hide();
			$('#turnOffGroup_<?php echo $GroupDevice['ID'];?>').show();
		<?php else :?>
			$('#turnOffGroup_<?php echo $GroupDevice['ID'];?>').hide();
			$('#turnOnGroup_<?php echo $GroupDevice['ID'];?>').show();
		<?php endif;?>
				
	<?php } ?>
</script>