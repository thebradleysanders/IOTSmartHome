
<?php #####################################################  Window/Door/Motion Sensors #################################################################### ?>
<?php $count=0;
	$query = "SELECT * FROM sensors";
	$results = mysqli_query($GS_DBCONN, $query);
	while($result = mysqli_fetch_assoc($results)) { 
?>		
	<?php if($result['notifications']=="1" && $result['already_notified']=="0" && $result['enabled']=="1") :
		//Change state to notified
		mysqli_query($GS_DBCONN, "UPDATE sensors SET already_notified='1' WHERE ID='".$result['ID']."'");			
	?>
		<!--- Show Notification ---->
		<script>
		  if (!('Notification' in window)) {
		  } else {
			var notificationEvents = ['onclick', 'onshow', 'onerror', 'onclose'];
			var title;
			var options;
				title = '<?php echo $result['sensor_kind'];?> Sensor';
				options = {
				  body: '<?php echo ucwords($result['sensor_name']);?> has been triggered',
				  tag: '<?php echo $result['sensor_kind'];?>',
				  icon: '<?php echo $GS_Config['notificationIcon'];?>'
				};
			  
			  Notification.requestPermission(function() {
				var notification = new Notification(title, options);
				notificationEvents.forEach(function(eventName) {
				  notification[eventName] = function(event) {
					log.innerHTML = 'Event "' + event.type + '" triggered for notification "' + notification.tag + '"<br />' + log.innerHTML;
				  };
				});
			  });
		  }
		</script>
	<?php endif;?>
	<?php $count++;?>  
	
		<?php if ($result['enabled']=='1') :?>
			<script>$('.showSensorDisabledstate_<?php echo $result['ID'];?>').hide();$('.showSensorEnabledstate_<?php echo $result['ID'];?>').fadeIn();</script>
			<script>$('.sensorType_<?php echo $result['ID'];?>').val('<?php echo temp_encode("disableSensor");?>');</script>
		<?php endif;?>
								
		<?php if ($result['sensor_state']=='1' && $result['enabled']=='1'){
				if($result['sensor_kind']=="Door"){
					$text="<div class='pull-right' style='font-size:17px;text-align:center;width:70px;background-color:rgb(239, 85, 58);color:#fff;padding:6px;line-height:36px;height:44px;'>Open</div>";
				}else{
					$text="<div class='pull-right' style='font-size:17px;text-align:center;width:70px;background-color:rgb(239, 85, 58);color:#fff;padding:6px;line-height:36px;height:44px;'>Active</div>";
				}
				
			}elseif($result['sensor_state']=='0' && $result['enabled']=='1'){
				if($result['sensor_kind']=="Door"){
					$text="<div class='text-success pull-right' style='font-size:17px;text-align:center;width:70px;color:#fff;background-color:".$GS_Config['themeColorMain'].";padding:6px;line-height:36px;height:44px;'>Closed</div>";
				}else{
					$text="<div class='text-success pull-right' style='font-size:17px;text-align:center;width:70px;color:#fff;background-color:".$GS_Config['themeColorMain'].";padding:6px;line-height:36px;height:44px;'>Inactive</div>";
				}
			}elseif($result['enabled']=='3'){ //disabled by system monitor
				$text="<div class='text-success pull-right' style='text-align:center;width:60px;color:#fff;background-color:#fff;border:2px dashed #d9534f;color:#333;padding:1px;'>Error</div>";
				
			}elseif($result['enabled']=='0'){
				echo "<script>$('.showSensorEnabledstate_".$result['ID']."').hide();$('.showSensorDisabledstate_".$result['ID']."').fadeIn();</script>";
				$text="<div style='color:grey;padding:4px;line-height:36px;height:44px;background-color:silver;color:#fff;' class='pull-right'>Disabled</div>";
				echo "<script>$('.sensorType_".$result['ID']."').val('".temp_encode("enableSensor")."');</script>";
			}
		?>
		
		<script>
			$('.indexSensorName_<?php echo $result['ID'];?>').text('<?php echo ucwords($result['sensor_name']);?>');
			$('.indexSensorState_<?php echo $result['ID'];?>').html("<?php echo $text;?>");
		</script>
<?php }?>
	
	

<?php ##############################################################  Data Sensors #################################################################### ?>
<?php
 	$count=0;
	$query = "SELECT * FROM data_sensors WHERE enabled='1'";
	$results = mysqli_query($GS_DBCONN, $query);
	while($sensor = mysqli_fetch_assoc($results)) { 
		$TitleArray = explode(":",$sensor['sensor_dataTitle_array']);
		$ValueArray = explode(":",$sensor['sensor_dataValue_array']);
		$VisibleArray = explode(":",$sensor['sensor_dataVisible_array']);
?>	
	<script>
		$("#DashboardCardDataSensor_name<?php echo $sensor['ID'];?>").text("<?php echo ucwords($sensor['sensor_nicename']);?>:");
	</script>

	<?php 
		$count=-1;
		foreach($TitleArray as $title){ $count++;
		if($VisibleArray[$count]!="1"){continue;}
	?>
		<script>
			$("#DashboardCardDataSensor_colTitle<?php echo $sensor['ID'];?>_<?php echo $count;?>").text("<?php echo $TitleArray[$count];?>:");
			$("#DashboardCardDataSensor_colValue<?php echo $sensor['ID'];?>_<?php echo $count;?>").html("<b><?php echo $ValueArray[$count];?></b>");
		</script>
	<?php }?>

<?php }
