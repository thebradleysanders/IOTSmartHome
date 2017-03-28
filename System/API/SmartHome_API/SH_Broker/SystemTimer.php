<?php
##############################################################################
/*///////////////////////////////////////////////////////////////////////////
	ABOUT THIS PAGE:
		This page controls most of the automation and this page is ran within 
		SH Broker every second or so.
	
	SmartHome - Created By: Brad Sanders
///////////////////////////////////////////////////////////////////////////*/
##############################################################################

if (php_sapi_name() != "cli") { //close if not ran in CLI (Commnad Line)
	exit("This Page Must Be Ran Via PHP CLI. Use The SmartHome Broker.");
}

$GS_phueServiceBypass = false;
$GS_wemoServiceBypass = false;
$GS_mqttServiceBypass = false;
$GS_emailServiceBypass = false;
$GS_weatherServiceBypass = false;
$GS_squeezeBoxServiceBypass = false;
$GS_webIncludesIncluded = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require(realpath(__DIR__ . "/../DBConn.php"));
require(realpath(__DIR__ . "/../IOTIncludes.php"));

##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--
##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--
##--##--##--##- 								 TIMERS 									##--##--##--
##--##--##--##-	This php script is ran inside a vb program that executes every 0.5 seconds	##--##--##--
##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--
##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--##--

GF_logging("System Service, System Timer Started","|System Services|Warnings|System|System Timer|Online|");

function getTimeDiffrence($start,$end){
	$date = date("Y-m-d");
	$to_time = strtotime($date." ".$end);
	$from_time = strtotime($date." ".$start);
	return ((abs($to_time - $from_time) / 60/60));
}
	

while(true){
	sleep(1);	
	
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
	
	//refresh system services and settings
	include(realpath(__DIR__ . "/../SystemServicesEnableCheck.php"));
							
	##########################################################################
	########################### EXECUTE IFTTT ################################
	##########################################################################
	
	//This will not execute event triggered by sensors those
	//will be handled by function ifttt in functions.php

	$query    = "SELECT * FROM ifttt WHERE (if_Array NOT LIKE '%<IF>Sensor:%') AND enabled='1'";
	$results5 = mysqli_query($GS_DBCONN, $query);
	while ($ifttt_info = mysqli_fetch_assoc($results5)) {
		if (time() > strtotime('+'.$ifttt_info['delay'].' seconds', $ifttt_info['last_ran'])) {
			
			$opperatorArray = explode("+",$ifttt_info['opperatorArray']);
			$parenthaseArrayAll = explode("+", $ifttt_info['parenthaseArray']);
			
			$conditionsALL = explode("<Condition>", rtrim($ifttt_info['if_Array'],"<Condition>"));
			$actionsALL = explode("<Action>", rtrim($ifttt_info['ifThen_Array'],"<Action>"));

			//IF
			$conditionCount=0;
			foreach($conditionsALL as $condition){$conditionCount++;
				//IF Array
				$if_Array = explode("<Done>",rtrim($conditionsALL[$conditionCount-1],"<Done>"));
				//THEN Array
				$then_Array = explode("<Done>",rtrim($actionsALL[$conditionCount-1],"<Done>"));

				//Opperator/Paranthases Array
				$opperatorArray_Cond=explode(":",$opperatorArray[$conditionCount-1]);
				$parenthaseArraySet = explode("|", $parenthaseArrayAll[$conditionCount-1]);
				$parenthaseArrayLeft_Cond = explode(":", $parenthaseArraySet[0]);
				$parenthaseArrayRight_Cond = explode(":", $parenthaseArraySet[1]);
			
				$count=0;
				$cond="";
				foreach ($if_Array as $ifItem){ $count++;
					$ifStatement = GF_iftttEvent($ifItem,$ifttt_info['ID']);
					$cond.=$parenthaseArrayLeft_Cond[$count]." $ifStatement==true ".$parenthaseArrayRight_Cond[$count].' '.$opperatorArray_Cond[$count].' ';
				}
				
				if(eval("return $cond;")){
					$count=0;
					foreach ($then_Array as $thenItem){$count++; GF_ThenThat($thenItem,$calledBy); }
					//set last ran
					if($count>0){
						mysqli_query($GS_DBCONN, "UPDATE ifttt SET last_ran='".time()."' WHERE ID='".$ifttt_info['ID']."'");
						GF_logging('IFTTT Event: <b>(System Timer.IF:'.$conditionCount.')</b> <u>'.ucfirst($ifttt_info['name']).'</u> Has Been Activated','|IFTTT|Sensors|System Services|'.$ifttt_info['name'].'|');
						break;
					}	
				}
			}
		}
	} //End execute ifttt	
	
	##########################################################################
	############################### Sensor Timer #############################
	##########################################################################
	//SAD_ = Sensor Automatic Delay
	$query = "SELECT * FROM sensors WHERE sensor_state='1' AND sensor_close_address LIKE 'SAD_%' AND enabled='1'";
	$results_ST     = mysqli_query($GS_DBCONN, $query);
	while ($sensor_timer = mysqli_fetch_assoc($results_ST)) {
		if (time() >  strtotime(('+20 seconds'),$sensor_timer['time_triggered'])) {
			GF_ifttt($sensor_timer['sensor_close_address'], '0','1'); //IFTTT
		}
	}

	##########################################################################
	############################## Device Timer ##############################
	##########################################################################

	$query    = "SELECT * FROM devices WHERE last_on_time<>'' AND enable_auto_off='1' AND device_state='1' AND (room='0' OR room='')";
	$results_DT = mysqli_query($GS_DBCONN, $query);
	while ($device_timer = mysqli_fetch_assoc($results_DT)) {
		if (time() > strtotime('+' . $device_timer['timeout'] . ' minutes', $device_timer['last_on_time'])) {
			GF_transmitToDevice($query, "0","0","noColor","noEffect","Device Timer");

			//make last-on time = noting so timer cant run again
			mysqli_query($GS_DBCONN, "UPDATE devices SET last_on_time='' WHERE ID='" . $device_timer['ID'] . "'");
		}
	}

	##########################################################################
	########################## Room Inactive Timer ###########################
	##########################################################################

	$query    = "SELECT * FROM home_rooms WHERE timeout_last_active<>'' AND timeout_enable='1'";
	$results_RT = mysqli_query($GS_DBCONN, $query);
	while ($room_timer = mysqli_fetch_assoc($results_RT)) {
		if (time() > strtotime('+' . $room_timer['timeout'] . ' minutes', $room_timer['timeout_last_active'])) {
			
			$last = (int)$room_timer['timeout_last_active'];
			$timeout = (int)$room_timer['timeout'];
			$lastTimeoutPlusTimeoutTime = $last + (60*$timeout);
			$diffrence = 10-round((($lastTimeoutPlusTimeoutTime+(60*10))-time())/60);
			$nextbrightness = (100-($diffrence*10)); //set brightness to 100-30 =70 
				
					
			if($nextbrightness>0){
				$query = "SELECT * FROM devices WHERE room='".$room_timer['ID']."' AND tags LIKE '%Dimmable%' AND enable_auto_off='1' AND device_state='1' AND enabled='1'";
				$results_RTDevices = mysqli_query($GS_DBCONN, $query);
				while ($device = mysqli_fetch_assoc($results_RTDevices)) {
					$deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>" . trim($device["deviceXML"]));
					
					if((int)$deviceConfig[0]->brightness > $nextbrightness){
						$query = "SELECT * FROM devices WHERE room='".$room_timer['ID']."' AND enable_auto_off='1' AND enabled='1' AND ID='".$device['ID']."'";	
						GF_transmitToDevice($query, "1", $nextbrightness, "noColor", "noEffect", "Room Timer");	
					}//end if
				}//end while
			}elseif($nextbrightness==0){
				$query = "SELECT * FROM devices WHERE room='".$room_timer['ID']."' AND tags LIKE '%Dimmable%' AND enable_auto_off='1' AND device_state='1' AND enabled='1'";	
				GF_transmitToDevice($query, "0", "0", "noColor", "noEffect", "Room Timer");	
				
				//send to log
				GF_logging("Room: ".$room_timer['room_name']." Inactive Timer Triggered.");
				
				//make last on time = noting so timer cant run again
				mysqli_query($GS_DBCONN, "UPDATE home_rooms SET timeout_last_active='' WHERE ID='" . $room_timer['ID'] . "'");
				
				GF_roomSoundControl($room_timer['ID'],"","","Power-off"); //Stop SqueezeBox Servers
			}//end if brightness 0
		}//end if time after last timeout time
	}//end room while

	##########################################################################
	############################### GET WEATHER DATA #########################
	##########################################################################
	
	if($GS_weatherServiceEnabled == true){ //Check if service is enabled
		//get Interval and last updated time
		$query    = "SELECT update_interval,last_updated FROM weather_data WHERE day_added='".date('l')."'";
		$results_Weather = mysqli_query($GS_DBCONN, $query);
		$weather_data_old = mysqli_fetch_assoc($results_Weather);

		if (time() > strtotime('+' . $weather_data_old['update_interval'] . ' minutes', $weather_data_old['last_updated'])) {
			GF_updateWeather();		  
		}
	}
	
	##########################################################################
	############################# Wake Up Function ###########################
	##########################################################################
	
	//this is the room wakeup function
	$numberOfMinuites = 30;
	$counts = 0;
	$query    = "SELECT * FROM home_rooms WHERE autoWakeUpTime LIKE '1|%'";
	$results_autoWake = mysqli_query($GS_DBCONN, $query);
	while ($room_wake = mysqli_fetch_assoc($results_autoWake)) {
		if(strpos(explode("|",$room_wake['autoWakeUpTime'])[2],strtolower(date("D")))!==false){
			
			if (strtotime(date("H:i")) >= strtotime(explode("|",$room_wake['autoWakeUpTime'])[1]) 
				&& strtotime(date("H:i")) <= strtotime('+'.$numberOfMinuites.' minutes', strtotime(explode("|",$room_wake['autoWakeUpTime'])[1])))
			{
				if($counts<$numberOfMinuites){ 
					if (time() > (int)$room_wake['autoWakeUpLastRan']) {
						$counts++;
						$nextbrightness = ((100/$numberOfMinuites)*$counts);
						$lastRan = strtotime('+1 minute', time());
						mysqli_query($GS_DBCONN,"UPDATE home_rooms SET autoWakeUpLastRan='".$lastRan."' WHERE ID='".$room_wake['ID']."'");
										
						//echo $numberOfMinuites." Minuites at:".(100/$numberOfMinuites)." levels of brightness per minute ";
						$query = "SELECT * FROM devices WHERE room='".$room_wake['ID']."' AND tags LIKE '%Dimmable%' AND deviceXML NOT LIKE '%<brightness>100</brightness>%' AND enabled='1'";	
						GF_transmitToDevice($query, "1", $nextbrightness, "noColor", "noEffect", "Room WakeUp Time");
				
					}
				}
			}// end if within time
		}// end if on slected weekdays
	}//end while
	
	
	##########################################################################
	################################ Alarm Timer #############################
	##########################################################################
	$AlarmTimeout = 60;
	$AlarmOnCountdown = 60;
	$query       = "SELECT * FROM alarm_status WHERE alarm_activated='0'";
	$results_AT    = mysqli_query($GS_DBCONN, $query);
	$alarm_info = mysqli_fetch_assoc($results_AT);
	$foundQuery = mysqli_num_rows($results_AT);
	if($foundQuery==1){
		
		if($alarm_info['alarm_state']=="1" && $alarm_info['alarm_triggered']=="1"){
			//check if alarm triggered for a $AlarmTimeout time
			if (time() > strtotime('+'.$AlarmTimeout.' seconds', $alarm_info['alarm_time'])) {
				GF_ActivateAlarm();
				//set state to triggered
				mysqli_query($GS_DBCONN, "UPDATE alarm_status SET alarm_activated='1' WHERE ID='1'");
			}
			
		//Alarm On Timer
		}elseif($alarm_info['alarm_on_time']!="" && $alarm_info['alarm_state']=="0"){ 
			if($alarm_info['alarm_mode']=="Away"){ //Activate contdoiwn timer if alarm set to away
				//Start Timer
				if (time() > strtotime('+'.$AlarmOnCountdown.' seconds', $alarm_info['alarm_on_time'])) {
					mysqli_query($GS_DBCONN, "UPDATE alarm_status SET alarm_state='1',alarm_on_time='' WHERE ID='1'");
				}
				
			// Do not use countdown if alarm on home
			}else{
				mysqli_query($GS_DBCONN, "UPDATE alarm_status SET alarm_state='1', alarm_on_time='' WHERE ID='1'");
			}
		}
	}
	
	
	########################### DONT PASS THIS LINE ####################################
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);

	//echo "System Timer: Time:".$total_time."\r\n";
	
}// END WHILE

