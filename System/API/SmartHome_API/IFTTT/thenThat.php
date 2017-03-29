<?php

#####################################################################################################################
##########################################		THEN THAT FILTER FUNCTION	#########################################
#####################################################################################################################
//get the text in between the <THEN></THEN> tags
function getIftttTagValue_THEN($lump){
	 if(trim($lump)==""){RETURN "";}
	 $start_tag = '<THEN>';
	 $end_tag = '</THEN>';
	 $startpos = strpos($lump, $start_tag) + strlen($start_tag);
	 if ($startpos !== false) {
	     $endpos = strpos($lump, $end_tag, $startpos);
	     if ($endpos !== false) {
	         return trim(substr($lump, $startpos, $endpos - $startpos));
	     }
	 }
}

############################################################################################################
############################################   THEN THAT   #################################################
############################################################################################################
function GF_ThenThat($RawData, $calledBy=""){
	global $GS_DBCONN;
	global $GS_Config;

	$data = getIftttTagValue_THEN($RawData);

	if(trim($data)==""){RETURN FALSE;}
		
	switch(true){
		//Device
		case strpos($data, 'Device:') !== false:
			// get the device id from device name
			$array = explode(":",$data);
			$device_id=(int)$array[1];
			$device_state=$array[2];
			$device_brightness=(int)$array[3];
			$device_color=$array[4];
			$device_effect=$array[5];
		
			$query = "SELECT * FROM devices WHERE enabled='1' AND ID='".$device_id."' LIMIT 1";
			GF_transmitToDevice($query, $device_state, $device_brightness, $device_color, $device_effect, "USER:".$_SESSION['id']);
			break;			
		//Enable Sensor
		case strpos($data, 'Enable Sensor:') !== false:
			$ifttt_sensor_ID = str_replace('Enable Sensor:','',$data);
			GF_enableDisableSensor($ifttt_sensor_ID,1,$calledBy);
			break;
		//Disable Sensor
		case strpos($data, 'Disable Sensor:') !== false:
			$ifttt_sensor_ID = str_replace('Disable Sensor:','',$data);
			GF_enableDisableSensor($ifttt_sensor_ID,0,$calledBy);
			break;
		//Enable Camera
		case strpos($data, 'Enable Camera:') !== false:
			$ifttt_camera_ID = str_replace('Enable Camera:','',$data);
			GF_enableDisableCamera($ifttt_camera_ID,1,$calledBy);
			break;
		//Disable Camera
		case strpos($data, 'Disable Camera:') !== false:
			$ifttt_camera_ID = str_replace('Disable Camera:','',$data);
			GF_enableDisableCamera($ifttt_camera_ID,0,$calledBy);
			break;			
		//Music Control
		case strpos($data, 'PLAYSONG|') !== false:
			$array = explode("|",$data);
			$room=$array[1];
			$song = $array[2];
			$volume = $array[3];
			$command = $array[4]; //Command = Play,Volume,Pause,Stop
			GF_roomSoundControl($room,$song,$volume,$command,$calledBy); 
			break;			
		//Alarm
		case strpos($data, 'Alarm:') !== false:
			if (trim($data) == "Alarm:On") {GF_ChangeAlarmState("", "1",$calledBy);}
			if (trim($data) == "Alarm:Off") {GF_ChangeAlarmState("", "0",$calledBy);}
			if (trim($data) == "Alarm:On:Home") {GF_ChangeAlarmState("Home", "1",$calledBy);}
			if (trim($data) == "Alarm:On:Away") {GF_ChangeAlarmState("Away", "1",$calledBy);}
			if (trim($data) == "Alarm:Off:Home") {GF_ChangeAlarmState("Home", "0",$calledBy);}
			if (trim($data) == "Alarm:Off:Away") {GF_ChangeAlarmState("Away", "0",$calledBy);}
			break;
		//Set Occupancy Status To Home
		case ($data =="Occupancy:Home"):
			mysqli_query($GS_DBCONN, "UPDATE alarm_status SET alarm_mode='Home' WHERE ID='1'");
			break;
		//Set Occupancy Status To Away
		case ($data =="Occupancy:Away"):
			mysqli_query($GS_DBCONN, "UPDATE alarm_status SET alarm_mode='Away' WHERE ID='1'");
			break;		
		//Rooms
		case strpos($data, 'RoomState:') !== false:
			$array = explode(":",$data);
			$ifttt_room_id=(int)$array[1];
			$state=$array[2];
			$brightness=(int)$array[3];
			$color=$array[4];
			$effect=$array[5];
			
			$query = "SELECT * FROM devices WHERE room='" . trim($ifttt_room_id) . "' AND enabled='1'";
			GF_transmitToDevice($query, $state, $brightness, $color, $effect, "USER:".$_SESSION['id']);
			break;
		//Groups
		case strpos($data, 'Group:') !== false:
			$array = explode(":",$data);
			$ifttt_group_id=(int)$array[1];
			$state=$array[2];
			$brightness=$array[3];
			$color=$array[4];
			$effect=$array[5];
			
			$query = "SELECT * FROM devices WHERE group_id='" . trim($ifttt_group_id) . "' AND enabled='1' ORDER BY room ASC";
			GF_transmitToDevice($query, $state, $brightness, $color, $effect, "USER:".$_SESSION['id']);
			break;
		//Room Group RGROOM
		case strpos($data, 'RG Room:') !== false:
			$arrayRG = explode(":",$data);
			$ifttt_room_id = $arrayRG[1];
			$ifttt_group_id = $arrayRG[2];
			$state = $arrayRG[3];
			$brightness = $arrayRG[4];
			$color = $arrayRG[5];
			$effect = $arrayRG[6];
			
			$query = "SELECT * FROM devices WHERE room='".$ifttt_room_id."' AND group_id='".$ifttt_group_id."' AND enabled='1'";
			GF_transmitToDevice($query, $state, $brightness, $color, $effect, "USER:".$_SESSION['id']);
			break;
		//Send Email
		case strpos($data, 'EMAIL:') !== false:
			$arrayEmail = explode(":",$data);
			$email=$arrayEmail[1];
			$subject=$GS_Config['SiteName']." - ".$calledBy;
			$body=$arrayEmail[2];
			GF_sendEmail($email,$subject,$body,$image,$calledBy);
			break;
		//Write To LOG
		case strpos($data, 'LOG:') !== false:
			$arrayLog = explode(":",$data);
			$text=$arrayLog[1];
			GF_logging("<b>Created By ".$calledBy." Event:</b> ".$text);
			break;
		//Delay
		case strpos($data, 'DELAY:') !== false:
			$arrayLog = explode(":",$data);
			usleep(1000 * $arrayLog[1]);
			break;
		//execute script
		case strpos($data, 'SCRIPT:') !== false:
			$ifttt_script_id = str_replace('SCRIPT:','',$data);
			GF_executeScript(trim($ifttt_script_id));
			break;
		//Speak In Room
		case strpos($data, 'SPEAK:') !== false:
			$speakArray = explode(":",$data);
			speak($speakArray[2],$speakArray[1]);
			break;
		//Case UI Notifications
		case strpos($data, 'UINotification:') !== false:
			$notification = explode(":",$data);
			UINotification($notification[1],$notification[2],$notification[3]);// userID,type,data
			break;
		//User Defined Variable
		case strpos($data, 'UserVariable:') !== false:
			$array = explode(":",$data);
			$variableID=(int)$array[1];
			$value=$array[2];
			
			$query = "UPDATE ifttt_userdefinedvariables SET variable_value='".$value."' WHERE ID='".$variableID."'";
			mysqli_query($GS_DBCONN,$query);
			break;
		//Scenes
		case strpos($data, 'Scene:') !== false:
			$scene = str_replace("Scene:", "", $data);
			$query = "SELECT * FROM scene_events WHERE event_name NOT LIKE '%_ignore' AND scene_id='" . $scene . "' ORDER BY ID ASC";
			$scene_devices = mysqli_query($GS_DBCONN, $query);
			while ($scene_device = mysqli_fetch_assoc($scene_devices)) {
				GF_ThenThat("<THEN>".$scene_device['event_name']."</THEN>", "Scene: ".$scene);
			}
			break;
		//SYSTEM FUNCTIONS
		case strpos($data, 'Restart_SmartHome') !== false:
			GF_restartSmarthome($calledBy);
			break;
		case strpos($data, 'Shutdown_SmartHome') !== false:
			GF_shutdownSmarthome($calledBy);
			break;
		default:
			RETURN FALSE;
			break;	
	}	
}