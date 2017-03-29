<?php

############################################################################################################
################################################   IFTTT   #################################################
############################################################################################################
function GF_ifttt($dataRaw, $debug, $isRaw, $calledBy=""){
	global $GS_DBCONN;
	//Check if $data if formated or not
	if($isRaw=="0"){
		$data = trim(GF_updateNode($dataRaw));
	}else{
		$data = trim($dataRaw);
	}
	
	//If $data = noting then exit function
	if(trim($data) == "") {RETURN FALSE;}
	
	//Query iot_recieve_que for last item
	$query = "SELECT * FROM iot_recieve_que ORDER BY ID DESC LIMIT 1";
	$results3 = mysqli_query($GS_DBCONN, $query);
	$last_recieved_item = mysqli_fetch_assoc($results3);	

	//Check last item within 5 seconds to prevent repeats
	if ((strtotime('+5 seconds', $last_recieved_item['time_recieved']) > time()) && ($last_recieved_item['data_recieved'] == $data)) {
	    //To Early To Add To Queue, The Same Event Is In Progress. 
	    return false;
	}
	
	//Add it to the recieve queue this prevents re-runs
	mysqli_query($GS_DBCONN, "INSERT INTO iot_recieve_que (from_node_id,time_recieved,data_recieved,errors)VALUES('0','" . time() . "','" . $data . "','')");	
			
	//Sensor count by address
	$query       = "SELECT * FROM sensors WHERE (sensor_address='" . $data . "' OR sensor_close_address='" . $data . "') AND enabled='1'";
	$results1    = mysqli_query($GS_DBCONN, $query);
	$sensor_info = mysqli_fetch_assoc($results1);
	$is_sensor   = mysqli_num_rows($results1);
	
	
	//Button count by address
	$query       = "SELECT * FROM buttons WHERE button_address='" . $data . "'";
	$results2    = mysqli_query($GS_DBCONN, $query);
	$button_info = mysqli_fetch_assoc($results2);
	$is_button   = mysqli_num_rows($results2);

	
	 
	############################### SENSORS ##########################################
	if ($is_sensor > 0 && $sensor_info['enabled'] == '1') { //make sure device is enabled
		
		//Set sensor state
		GF_updateSensor($sensor_info,$data,$calledBy);

		//Execute ifttt
		$query = "SELECT * FROM ifttt WHERE (if_Array LIKE '%<IF>Sensor:".$sensor_info['ID']."%') AND enabled='1' ORDER BY ID ASC";
		$results5 = mysqli_query($GS_DBCONN, $query);
		while ($ifttt_info = mysqli_fetch_assoc($results5)) {
			$delayArray = explode("+",$ifttt_info['delayArray']);
			
			$opperatorArray = explode("+",$ifttt_info['opperatorArray']);
			$parenthaseArrayAll = explode("+", $ifttt_info['parenthaseArray']);
			
			$conditionsALL = explode("<Condition>", rtrim($ifttt_info['if_Array'],"<Condition>"));
			$actionsALL = explode("<Action>", rtrim($ifttt_info['ifThen_Array'],"<Action>"));

			//IF
			$conditionCount=0;
			foreach($conditionsALL as $condition){$conditionCount++;
				if (time() > strtotime('+'.$delayArray[$conditionCount-1].' seconds', $ifttt_info['last_ran'])) {
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
						foreach ($then_Array as $thenItem){$count++; GF_ThenThat($thenItem,$calledBy);  }
						//set last ran
						if($count>0){
							mysqli_query($GS_DBCONN, "UPDATE ifttt SET last_ran='".time()."' WHERE ID='".$ifttt_info['ID']."'");
							GF_logging('IFTTT Event: <b>(Receive Queue.IF:'.$conditionCount.')</b> <u>'.ucfirst($ifttt_info['name']).'</u> Has Been Activated','|IFTTT|Sensors|System Services|'.$ifttt_info['name'].'|');
							break;
						}
					}
				}
			}	
		} //End execute ifttt
		
		//trigger alarm if alarm is set to home and on
		if(getAlarmState("Home")==true || getAlarmState("Away")==true){ 
			GF_triggerAlarm($data,$calledBy);
		}	
	//End if sensor	
	############################### BUTTONS ##########################################
	}elseif ($is_button > 0) {
		
		//Set button state
		if ($button_info['button_state'] == "0") {
			mysqli_query($GS_DBCONN, "UPDATE buttons SET button_state='1',last_triggered='".date("Y-m-d H:i:s")."' WHERE button_address='" . $data . "'");
		   GF_ThenThat($button_info['button_event'],$calledBy);
			
		}elseif ($button_info['button_state'] == "1") {
			mysqli_query($GS_DBCONN, "UPDATE buttons SET button_state='0',last_triggered='".date("Y-m-d H:i:s")."' WHERE button_address='" . $data . "'");
			GF_ThenThat($button_info['button_event2'],$calledBy);
		}		
		
	}else{
		################## NEW SENSOR #########################
		GF_ScanForNewSensors($data,"USER");
	}
	################### END BUTTONS #######################
		
	
}