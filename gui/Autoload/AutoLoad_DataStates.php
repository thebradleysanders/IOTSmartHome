<?php 
		//get alarm data
		$query = "SELECT * FROM alarm_status WHERE ID='1' LIMIT 1";
		$results = mysqli_query($GS_DBCONN, $query);
		$alarm_state=mysqli_fetch_assoc($results);
		
		//alarm On Away Contdown
		if($alarm_state['alarm_on_time']!="" && $alarm_state['alarm_state']=="0" && $alarm_state['alarm_mode']=="Away"){
			$countdown = (int)(60-(time()-$alarm_state['alarm_on_time']));
		}
		//alarm Triggered Contdown
		if($alarm_state['alarm_time']!="" && $alarm_state['alarm_state']=="1" && $alarm_state['alarm_activated']=="0"){
			$countdown = (int)(60-(time()-$alarm_state['alarm_time']));
		}
		
		//active devices count
		$device_on_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM devices WHERE device_state='1' AND enabled='1'"));
		
		//total devices count
		$device_total_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM devices WHERE enabled='1'"));
		
		//check alarm ready
		if($alarm_state['alarm_mode']=='Home' && $alarm_state['alarm_state']=="0"){
			$sensor_not_ready_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM sensors WHERE sensor_state='1' AND enabled='1' AND is_alarmSensorHome='1'"));
		}elseif($alarm_state['alarm_mode']=='Away' && $alarm_state['alarm_state']=="0"){
			$sensor_not_ready_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM sensors WHERE sensor_state='1' AND enabled='1' AND is_alarmSensorAway='1'"));
		}
		
		//get event log count
		$results_count=mysqli_query($GS_DBCONN, "SELECT ID FROM event_log LIMIT 10000");
		$event_count=mysqli_num_rows($results_count);		
?>
	<script>
		<?php if($alarm_state['alarm_state']=='1' && $alarm_state['alarm_activated']=='0'):?>
			$('#datastates_alarmState_value').val('0');
			$('#datastates_alarm_state').css("font-size","22px").text("Active");
			$('#datastates_alarmState').css({"background-color":"rgb(120, 205, 81)","color":"white"});
			
			//alarm modal
			$("#alarmPanelSubmitButton").html("Turn Off Alarm <b><?php echo $countdown;?></b>");
			$('#alarmPanelSubmitButton').css({"background-color":"rgb(239, 85, 58)","color":"white"});
			$("#alarmPanelSubmitButton").prop("disabled",false);
		<?php elseif($alarm_state['alarm_state']=='1' && $alarm_state['alarm_activated']=='1'):?>
			$('#datastates_alarmState_value').val('0');
			$('#datastates_alarm_state').css("font-size","22px").text("Activated");
			$('#datastates_alarmState').css({"background-color":"#ffb732","color":"white"});
			
			//alarm modal
			$("#alarmPanelSubmitButton").html("Turn Off Alarm <b><?php echo $countdown;?></b>");
			$('#alarmPanelSubmitButton').css({"background-color":"#ffb732","color":"white"});
			$("#alarmPanelSubmitButton").prop("disabled",false);
		<?php elseif($alarm_state['alarm_state']=='0' && $sensor_not_ready_count==0):?>
			$('#datastates_alarmState_value').val('1');
			$('#datastates_alarm_state').css("font-size","22px").text("OFF-Ready ");
			$('#datastates_alarmState').css({"background-color":"rgb(239, 85, 58)","color":"white"});
			
			//alarm modal
			$("#alarmPanelSubmitButton").html("Turn On Alarm <b><?php echo $countdown;?></b>");
			$("#alarmPanelSubmitButton").css({"background-color":"rgb(120, 205, 81)","color":"#fff","border":"0px"});
			$("#alarmPanelSubmitButton").prop("disabled",false);	
		<?php elseif($alarm_state['alarm_state']=='0' && $sensor_not_ready_count >0):?>
			$('#datastates_alarmState_value').val('0');
			$('#datastates_alarm_state').css("font-size","20px").text("OFF-Not Ready ");
			$('#datastates_alarmState').css({"background-color":"rgb(239, 85, 58)","color":"white"});
			$('#datastates_alarmState').html("<?php echo $countdown;?>");
			
			//alarm modal
			$("#alarmPanelSubmitButton").text("Not Ready");
			$("#alarmPanelSubmitButton").prop("disabled",true);
		<?php endif;?>

		
		//Alarm Trigger Countdoun and Alarm On Away Countdown
		<?php if($countdown>0):?>
			$('#datastates_alarmState').removeClass("fa-bell");
			$('#datastates_alarmState').html("<b><?php echo $countdown;?></b>");
		<?php else:?>
			$('#datastates_alarmState').addClass("fa-bell");
			$('#datastates_alarmState').html("");
		<?php endif;?>		
			

		
		$('#datastates_alarm_mode').text("<?php echo ucfirst($alarm_state['alarm_mode']);?>");
		<?php if($alarm_state['alarm_mode']=='Home'):?>
			$('#datastates_alarmMode_value').val('Away');
		<?php else :?>
			$('#datastates_alarmMode_value').val('Home');
		<?php endif?>
		
		$('#datastates_events').text('<?php if($event_count<=9999){echo $event_count;}else{echo "9999+";}?>');
		$('#datastates_active_devices').text('<?php echo $device_on_count;?>/<?php echo $device_total_count;?>');
	</script>