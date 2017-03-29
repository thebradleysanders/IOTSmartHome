<?php

#####################################################################################################################
##########################################		IFTTT EVENT FILTER			#########################################
#####################################################################################################################
//get the text in between the <IF></IF> tags
function getIftttTagValue_IF($lump){
	 if(trim($lump)==""){RETURN "";}
	 $start_tag = '<IF>';
	 $end_tag = '</IF>';
	 $startpos = strpos($lump, $start_tag) + strlen($start_tag);
	 if ($startpos !== false) {
	     $endpos = strpos($lump, $end_tag, $startpos);
	     if ($endpos !== false) {
	         return trim(substr($lump, $startpos, $endpos - $startpos));
	     }
	 }
}
#####################################################################################################################
############################################		IFTTT EVENT 			#########################################
#####################################################################################################################
// this checks the if events to see if they are true or false

function GF_iftttEvent($ifttt_info_Raw,$ifttt_ID=""){
	global $GS_DBCONN;
	
	$ifttt_info = getIftttTagValue_IF($ifttt_info_Raw);

	if(trim($ifttt_info)==""){RETURN TRUE;}
	
	
	switch (true){
			
		//Check if Someone is home or not
		case strpos($ifttt_info,'Status: ') !== false:
			$ifttt_info = str_replace("Status: ","",$ifttt_info);
			$is_home=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM alarm_status WHERE alarm_mode='Home' LIMIT 1"));	
			if($is_home==1){
				if($ifttt_info =="Somebody Home"){
					RETURN TRUE;
				}elseif($ifttt_info =="Nobody Home"){
					RETURN 0;
				}
			}elseif ($is_home==0){
				if($ifttt_info =="Nobody Home"){
					RETURN TRUE;
				}elseif($ifttt_info =="Somebody Home"){
					RETURN 0;
				}
			}
			break;
			
		//Device On/Off
		case strpos($ifttt_info,'Device:') !== false:
			   $array = explode(":",$ifttt_info);
			   $device_id=$array[1];
			   $device_state=$array[2];
			   
			   $device_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM devices WHERE device_state='".$device_state."' AND ID = '".$device_id."' LIMIT 1"));	
			   if ($device_count>0){RETURN TRUE;}else{RETURN 0;}
			   break;
			   
		//Temperature	   
		case strpos($ifttt_info,'Temperature:') !== false:
			$array = explode(":",$ifttt_info);
			$id = trim($array[1]);
			$ifttt_temp = trim($array[2]);
			$compare = trim($array[3]);
			
			$sensor_data=mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM data_sensors WHERE enabled='1' AND sensor_type='Temp_Humidity_HTTP' AND ID='".$id."'"));	
			$array2 = explode(";",$sensor_data['sensor_data']);
			$humid = trim($array2[1]);
			$temp = trim($array2[2]);
			$heatindex = trim($array2[3]);		
			
			if($compare=="<" && $temp < $ifttt_temp){
				RETURN TRUE;
			}elseif($compare==">" && $temp > $ifttt_temp){
				RETURN TRUE;
			}elseif($compare=="=" && substr($temp,0,2) == $ifttt_temp){
				RETURN TRUE;
			}else{RETURN 0;}
			
			break;
		//Time is Before
		case strpos($ifttt_info,'BeforeTime:') !== false:
			$ifttt_info = str_replace("BeforeTime:","",trim($ifttt_info));
			$get_ifttt=mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM ifttt WHERE ID='".$ifttt_ID."' LIMIT 1"));
			if (time() > strtotime('+1 minute', $get_ifttt['last_ran']) || $get_ifttt['last_ran']=="") {
				if (strtotime(date("Y/m/d H:i")) < strtotime(date("Y/m/d")." ".$ifttt_info.":00")) { // $iftt_info is greater than current time
					RETURN TRUE;
				}else{RETURN 0;}
			}
			break;
		//Time is After
		case strpos($ifttt_info,'AfterTime:') !== false:
			$ifttt_info = str_replace("AfterTime:","",trim($ifttt_info));
			$get_ifttt=mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM ifttt WHERE ID='".$ifttt_ID."' LIMIT 1"));
			if (time() > strtotime('+1 minute', $get_ifttt['last_ran']) || $get_ifttt['last_ran']=="") {
				if (strtotime(date("Y/m/d H:i")) > strtotime(date("Y/m/d")." ".$ifttt_info.":00")) { // $iftt_info is greater than current time
					RETURN TRUE;
				}else{RETURN 0;}
			}
			break;
		//Time
		case strpos($ifttt_info,'Time:') !== false:
			$get_ifttt=mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM ifttt WHERE ID='".$ifttt_ID."' LIMIT 1"));
			if (time() > strtotime('+1 minute', $get_ifttt['last_ran']) || $get_ifttt['last_ran']=="") {
				$specified_time=str_replace("Time:","",$ifttt_info);
				if(trim($specified_time)==(string)date("g:i A")){RETURN TRUE;}else{RETURN 0;}
			}
			break;
		//Day Of Week
		case strpos($ifttt_info,'DayOfWeek:') !== false:
			$get_ifttt=mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM ifttt WHERE ID='".$ifttt_ID."' LIMIT 1"));
			if (time() > strtotime('+1 minute', $get_ifttt['last_ran']) || $get_ifttt['last_ran']=="") {
				$specified_time=str_replace("DayOfWeek:","",$ifttt_info);
				$day=(string)date("l");
				if($specified_time=="Weekday" && ($day=="Monday" || $day=="Tuesday" || $day=="Wednesday" || $day=="Thursday" || $day=="Friday" )){
					RETURN TRUE;
				}elseif($specified_time=="Weekend" && ($day=="Sunday" || $day=="Saturday")){
					RETURN TRUE;
				}else{
					if(trim($specified_time)==$day){RETURN TRUE;}else{RETURN 0;}
				}
			}
			break;
		//Schedule
		case strpos($ifttt_info,'Schedule:') !== false:
			$get_ifttt=mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM ifttt WHERE ID='".$ifttt_ID."' LIMIT 1"));
			if (time() > strtotime('+2 minutes', $get_ifttt['last_ran']) || $get_ifttt['last_ran']=="") {
				$formated_date =date("Y-m-d##H:i");
				$formated_date = str_replace('##','T',$formated_date);
				if(str_replace("Schedule:","",trim($ifttt_info))==$formated_date){ RETURN TRUE;}else{RETURN 0;}
			}
			break;
		//After Sunrise
		case strpos($ifttt_info,'AfterSunrise') !== false:
			$weather_info = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM weather_data WHERE ID='1'"));
			if(date("h:i:s A") > $weather_info['sunrise_time']){RETURN TRUE;}else{RETURN 0;}
			break;
		//After Sunset
		case strpos($ifttt_info,'AfterSunset') !== false:
			$weather_info = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM weather_data WHERE ID='1'"));
			if(date("h:i:s A") > $weather_info['sunset_time']){RETURN true;}else{RETURN 0;}
			break;
		//Before Sunrise
		case strpos($ifttt_info,'BeforeSunrise') !== false:
			$weather_info = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM weather_data WHERE ID='1'"));
			if(date("h:i:s A") < $weather_info['sunrise_time']){RETURN TRUE;}else{RETURN 0;}
			break;
		//Before Sunset
		case strpos($ifttt_info,'BeforeSunset') !== false:
			$weather_info = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM weather_data WHERE ID='1'"));
			if(date("h:i:s A") < $weather_info['sunset_time']){RETURN true;}else{RETURN 0;}
			break;
		//Sunrise
		case strpos($ifttt_info,'Sunrise') !== false:
			$weather_info = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM weather_data WHERE ID='1'"));
			if($weather_info['sunrise_time']==date("h:i:s A")){RETURN TRUE;}else{RETURN 0;}
			break;
		//Sunset
		case strpos($ifttt_info,'Sunset') !== false:
			$weather_info = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM weather_data WHERE ID='1'"));
			if($weather_info['sunset_time']==date("h:i:s A")){RETURN true;}else{RETURN 0;}
			break;

		//Status
		case strpos($ifttt_info,'Status:') !== false:
		  $ifttt_info = str_replace("Status:","",$ifttt_info);
			if (trim($ifttt_info) == 'Somebody Home') {
			  $status_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM alarm_status WHERE  ORDER BY ID ASC LIMIT 1"));	
			}
			if (trim($ifttt_info) == 'Nobody Home') {
			   $status_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM alarm_status WHERE  ORDER BY ID ASC LIMIT 1"));
			}
			if ($status_count>0){RETURN TRUE;}else{RETURN 0;}
			break;
		//Alarm
		case strpos($ifttt_info,'Alarm:') !== false:
			$ifttt_info = str_replace("Alarm:","",$ifttt_info);
			if (trim($ifttt_info) == 'On') { //alarm on
			  $alarm_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM alarm_status WHERE alarm_state='1' ORDER BY ID ASC LIMIT 1"));	
			}
			if (trim($ifttt_info) == 'On:Home') { //alarm on home
			  $alarm_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM alarm_status WHERE alarm_state='1' AND alarm_mode='Home' ORDER BY ID ASC LIMIT 1"));
			}
			if (trim($ifttt_info) == 'On:Away') { //alarm on Away
			  $alarm_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM alarm_status WHERE alarm_state='1' AND alarm_mode='Away' ORDER BY ID ASC LIMIT 1"));
			}
			if (trim($ifttt_info) == 'Off') { //alarm off
			  $alarm_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM alarm_status WHERE alarm_state='0' ORDER BY ID ASC LIMIT 1"));
			}
			if (trim($ifttt_info) == 'Triggered') { //alarm triggered
			  $alarm_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM alarm_status WHERE alarm_triggered='1' ORDER BY ID ASC LIMIT 1"));
			}
			if (trim($ifttt_info) == 'Activated') { //alarm activated
			  $alarm_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM alarm_status WHERE alarm_activated='1' ORDER BY ID ASC LIMIT 1"));
			}
			if ($alarm_count>0){RETURN TRUE;}else{RETURN 0;}
			break;
		//Data Sensor
		case strpos($ifttt_info,'DataSensor:') !== false:
			//DataSensor:ID:COL:OPPERATION:USERENTRY		
			$array = explode(":",$ifttt_info);
			$sensor_id=(int)trim($array[1]);
			$col_number=(int)trim($array[2]);
			$comparison = trim($array[3]);
			$compareTo = trim($array[4]);
			
			$sensorData=mysqli_fetch_array(mysqli_query($GS_DBCONN, "SELECT * FROM data_sensors WHERE ID='".$sensor_id."' AND enabled='1' LIMIT 1"));	
			$sensor_dataValues = explode(":",$sensorData['sensor_dataValue_array']);
			
			if(($sensor_dataValues[$col_number]==$compareTo) && $comparison=="="){Return true; }
			elseif(($sensor_dataValues[$col_number]!=$compareTo) && $comparison=="!="){Return true;}
			elseif(((int)$sensor_dataValues[$col_number]>(int)$compareTo) && $comparison==">"){Return true;}
			elseif(((int)$sensor_dataValues[$col_number]<(int)$compareTo) && $comparison=="<"){Return true;}
			elseif(((int)$sensor_dataValues[$col_number]>=(int)$compareTo) && $comparison==">="){Return true;} //not in UI
			elseif(((int)$sensor_dataValues[$col_number]<=(int)$compareTo) && $comparison=="<="){Return true;} //not in UI
			else{RETURN 0;}
			break;
		//User Defined Variable
		case strpos($ifttt_info,'UserVariable:') !== false:
			$array = explode(":",$ifttt_info);
			$variable_id=(int)trim($array[1]);
			$comparison = trim($array[2]);
			$compareTo = trim($array[3]);
			
			$variableData=mysqli_fetch_array(mysqli_query($GS_DBCONN, "SELECT * FROM ifttt_userdefinedvariables WHERE ID='".$variable_id."' LIMIT 1"));	
			$variableValue = $variableData['variable_value'];
			
			if(($variableValue==$compareTo) && $comparison=="="){Return true; }
			elseif(($variableValue!=$compareTo) && $comparison=="!="){Return true;}
			elseif(((int)$variableValue>(int)$compareTo) && $comparison==">"){Return true;}
			elseif(((int)$variableValue<(int)$compareTo) && $comparison=="<"){Return true;}
			elseif(((int)$variableValue>=(int)$compareTo) && $comparison==">="){Return true;} //not in UI
			elseif(((int)$variableValue<=(int)$compareTo) && $comparison=="<="){Return true;} //not in UI
			else{RETURN 0;}
			break;
		//Sensor State	
		case strpos($ifttt_info,'Sensor:') !== false:
			$array = explode(":",$ifttt_info);
			$sensor_id=(int)$array[1];
			$sensor_state=$array[2];
		
			$sensor_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM sensors WHERE sensor_state='".$sensor_state."' AND ID = '".$sensor_id."' ORDER BY ID ASC LIMIT 1"));	
			if ($sensor_count==1){RETURN TRUE;}else{RETURN 0;}
			break;	
		
		default:
			Gf_logging("Empty IFTTT");
			RETURN 0;
			break;
	}
}
