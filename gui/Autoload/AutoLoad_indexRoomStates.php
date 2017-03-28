<script>
	<?php 
		$query = "SELECT * FROM home_rooms ORDER BY ID ASC LIMIT 8 ";
		$rooms = mysqli_query($GS_DBCONN, $query);
		while($Room = mysqli_fetch_assoc($rooms)) {
			$query = "SELECT * FROM devices WHERE room='".$Room['ID']."' AND device_state='1' AND enabled='1' ORDER BY ID ASC ";
			$DeviceCount = mysqli_num_rows(mysqli_query($GS_DBCONN, $query));	

			$query = "SELECT * FROM devices WHERE room='".$Room['ID']."' AND tags LIKE '%Dimmable%' AND enabled='1' ORDER BY ID ASC ";
			$PhueDeviceCount = mysqli_num_rows(mysqli_query($GS_DBCONN, $query));
	?>
		<?php if ($DeviceCount>0) :?>
			$('#turnOnRoom_<?php echo $Room['ID'];?>').hide();
			$('#turnOffRoom_<?php echo $Room['ID'];?>').show();
		<?php else :?>
			$('#turnOnRoom_<?php echo $Room['ID'];?>').show();
			$('#turnOffRoom_<?php echo $Room['ID'];?>').hide();			
		<?php endif;?>
		
		/* get avg brightness from phue devices */
		<?php
			$brightnessTotal = 0;
			$count = 0;
			$query = "SELECT * FROM devices WHERE room='".$Room['ID']."' AND tags LIKE '%Dimmable%' AND device_state='1'";
			$roomDevices = mysqli_query($GS_DBCONN, $query);
			while($device = mysqli_fetch_assoc($roomDevices)) { $count++;
				$deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>".trim($device['deviceXML'])) or die("Error: Cannot create object");
				$brightnessTotal = $brightnessTotal + (int)$deviceConfig[0]->brightness;
			}
			$BrightnessAvg = $brightnessTotal/$count;
		?>		
			
		
		/* brightness slider */
		<?php if ($PhueDeviceCount>0) :?>
			$(".DeviceRoomBrightnessSlider<?php echo $Room['ID'];?>").slideDown(200);
			if($(".device_room_brightness_value_<?php echo $Room['ID'];?>").val()=="0" ){	
				$(".DeviceRoomBrightnessSlider<?php echo $Room['ID'];?>").slider( "option", "value", <?php echo (int)$BrightnessAvg;?> );
			}		
		<?php endif;?>
		
		
		<?php //Groups for dashboard card room modal
			$query = "SELECT * FROM device_groups ORDER BY ID ASC LIMIT 8 ";
			$Rgroups = mysqli_query($GS_DBCONN, $query);
			while($group = mysqli_fetch_assoc($Rgroups)) { 
				if(mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM devices WHERE room='".$Room['ID']."' AND group_id='".$group['ID']."'"))==0){continue;} ?>
				
				<?php if(mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM devices WHERE room='".$Room['ID']."' AND group_id='".$group['ID']."' AND device_state='1'"))>0) : ?>
					$("#DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $Room['ID'];?>_Off").hide();
					$("#DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $Room['ID'];?>_On").show();
					$("#DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $Room['ID'];?>_State").val('0');
				<?php else :?>
					$("#DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $Room['ID'];?>_On").hide();
					$("#DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $Room['ID'];?>_Off").show();
					$("#DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $Room['ID'];?>_State").val('1');
				<?php endif;?>
		
		<?php }?>
		
				
	<?php } ?>
</script>