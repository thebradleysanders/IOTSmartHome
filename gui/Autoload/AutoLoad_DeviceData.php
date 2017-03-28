<script>
	<?php 
		$query = "SELECT * FROM devices ORDER BY ID ASC ";
		$devices = mysqli_query($GS_DBCONN, $query);
		while($device = mysqli_fetch_assoc($devices)) {
			$deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>".trim($device["deviceXML"])) or die("Error: Cannot create object");
	?>
		<?php if($device['enabled']=='1'):?>
			$('.deviceDisabled_<?php echo $device['ID'];?>').hide();
			
			<?php if ($device['device_state']=='1') :?>
				$('.turnOn_<?php echo $device['ID'];?>').hide();
				$('.turnOff_<?php echo $device['ID'];?>').show();
				
				<?php if (strpos($device['tags'],'Dimmable')!==false) : //Dimmable Device?>
					/* set device brightness in slider */
					if($(".device_brightness_value_<?php echo $device['ID'];?>").val()=="0" ){	
						$(".DeviceBrightnessSlider<?php echo $device['ID'];?>").slider( "option", "value", <?php echo $deviceConfig[0]->brightness;?> );
					}
				<?php endif; //End Dimmable Device ?>
				
			<?php else : //Device state = 0 ?>
				$('.turnOff_<?php echo $device['ID'];?>').hide();
				$('.turnOn_<?php echo $device['ID'];?>').show();
				<?php if (strpos($device['tags'],'Dimmable')!==false) : //Dimmable Device?>
					if($(".device_brightness_value_<?php echo $device['ID'];?>").val()=="0" ){	
						$(".DeviceBrightnessSlider<?php echo $device['ID'];?>").slider( "option", "value", 0);
					}
				<?php endif;?>
			<?php endif;?>
			
		<?php elseif($device['enabled']=='3'): //Device Disabled By SysMon. ?> 
			$('.turnOff_<?php echo $device['ID'];?>').hide();
			$('.turnOn_<?php echo $device['ID'];?>').hide();
			$('.deviceDisabled_<?php echo $device['ID'];?>').show();
			$('.deviceDisabled_<?php echo $device['ID'];?> button').css("border","2px dashed #d9534f");
			$('.deviceDisabled_<?php echo $device['ID'];?>').prop("disabled",true);
			$('.deviceDisabled_<?php echo $device['ID'];?>').prop("title","This Device Has Been Disabled By System Monitor");		
			
		<?php elseif($device['enabled']=='0'): //Device Disabled By User ?>
			$('.turnOff_<?php echo $device['ID'];?>').hide();
			$('.turnOn_<?php echo $device['ID'];?>').hide();
			$('.deviceDisabled_<?php echo $device['ID'];?>').show();
			$('.deviceDisabled_<?php echo $device['ID'];?>').prop("disabled",true);
			$('.deviceDisabled_<?php echo $device['ID'];?> button').css("border","1px solid grey");
			$('.deviceDisabled_<?php echo $device['ID'];?>').prop("title","This Device Has Been Disabled");
			
		<?php endif; //End Enabled Check ?>
				
	<?php } ?>
</script>