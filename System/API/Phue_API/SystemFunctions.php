<?php

//###################### PHILLIPS HUE #################################
class Phillips_Hue {
	function Device($light, $state, $brightness, $color, $effect, $calledBy=""){
		global $GS_DBCONN;
		global $GS_phueServiceEnabled;
		global $GS_phueService;//Phillips Hue
		
		if($GS_phueServiceEnabled == true){
	
			//Convert brightness from percent
			if($state=="1"){
				$brightness=round(((int)($brightness) * 254)/100,1);
			}else{
				$brightness=0;
			}
	
			if($state!="noState"){
				$command = array( 'on' => (bool)$state, 'ct' => 380, 'bri' => (int)$brightness, 'transitiontime' =>10);
				$GS_phueService->setHueLight((int)$light, $command);
			}
			
			if($color!="noColor"){ //set color
				$GS_phueService->setHueLight((int)$light, $GS_phueService->predefinedColors($color));
			}
			
			if($effect=="BlinkOnce"){ //lselect,select,none
				$GS_phueService->lights()[(int)$light]->setAlert("select");
			}elseif($effect=="BlinkTimes"){
				$GS_phueService->lights()[(int)$light]->setAlert("lselect");
			}elseif($effect=="StopBlink"){
				$GS_phueService->lights()[(int)$light]->setAlert("none");
			}	
		}//end if service enabled
	} //end function
	
	
	###################### GET ALL PHUE DEIVCE STATES #####################
	function DeviceStates(){
		global $GS_DBCONN;
		global $GS_phueService;
		global $GS_phueServiceEnabled;
		
		if($GS_phueServiceEnabled==true){
			$count = -1;
			$lightsIDs = $GS_phueService->lightIds();
			$lightsList = $GS_phueService->GetLights();
			
			foreach ($lightsList as $light){ $count++;
				$LightName = $light['name'];
				$LightState = (int)$light['state']['on'];
				$LightBrightness = round(((int)$light['state']['bri']/254)* 100);
				$LightReachable = (int)$light['state']['reachable'];
				
				$query    = "SELECT * FROM devices WHERE type='phue' AND enabled<>'0' AND deviceXML LIKE '%<light>".$lightsIDs[$count]."</light>%'";
				$results_hue = mysqli_query($GS_DBCONN, $query);
				$hue = mysqli_fetch_assoc($results_hue);
					
				if($LightReachable==1){
					$deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>" . trim($hue["deviceXML"])) or die("Error: Cannot create object");
					$xmlstr = '
						<device>
							<type>phue</type>
							<light>'.$lightsIDs[$count].'</light>
							<brightness>'.$LightBrightness.'</brightness>
							<reachable>'.$LightReachable.'</reachable>
						</device>';
			
					if($hue['device_state']=="0" && $LightState=="1"){
						//reset room timeout_last_active time where sensor is
						if($hue['room']!="0"){ $roomArray .=$hue['room'].":"; }
						mysqli_query($GS_DBCONN, "UPDATE devices SET device_name='".$LightName."', device_state='1',enabled='1',last_on_time='".time()."', deviceXML='".trim($xmlstr)."' WHERE ID='" . $hue['ID'] . "'");
						GF_history_log($hue['ID'],"device","1");
						GF_logging("Phillips Hue: ".$hue['device_name']." Has State Changed To On With A Brightness Of ".$LightBrightness,"|Phillips Hue|Services|Devices|".$hue['device_name']."|On|");					
					}elseif($hue['device_state']=="1" && $LightState=="0"){
						mysqli_query($GS_DBCONN, "UPDATE devices SET device_name='".$LightName."', device_state='0',enabled='1',last_off_time='".time()."', deviceXML='".trim($xmlstr)."' WHERE ID='" . $hue['ID'] . "'");
						GF_history_log($hue['ID'],"device","0");
						GF_logging("Phillips Hue: ".$hue['device_name']." State Has Changed To Off With A Brightness Of ".$LightBrightness,"|Phillips Hue|Services|Devices|".$hue['device_name']."|Off|");
					}elseif($deviceConfig[0]->brightness != $LightBrightness){ //if Brightness Change Then Update the DB
						mysqli_query($GS_DBCONN, "UPDATE devices SET deviceXML='".$xmlstr."' WHERE ID='".$hue['ID']."'");
					}				
				}else{	
					//the light is not reachable so we show that it is off and set enabled to 3
					GF_history_log($hue['ID'],"device","0");
					mysqli_query($GS_DBCONN, "UPDATE devices SET device_state='0',enabled='3', last_off_time='".time()."' WHERE ID='" . $hue['ID'] . "'");			
				}	
			}
	
			//Set room last active
			if(trim($roomArray)!=""){
				foreach (explode(":",array_unique($roomArray)) as $room){
					$sqlIDWhereList.=" ID='".$room."' OR";
				}
				mysqli_query($GS_DBCONN, "UPDATE home_rooms SET timeout_last_active='".time()."', last_active='".time()."' WHERE ".trim($sqlIDWhereList,"OR"));
			}		
		}
	}
	
	####################### Phillips Hue Sensors ##########################
	function SensorStates(){
		global $GS_DBCONN;
		global $GS_phueServiceEnabled;
		global $GS_phueService;
			
		if($GS_phueServiceEnabled == true){
			
			$sensorList = $GS_phueService->GetSensors();
			foreach ($sensorList as $sensor){
				//Check If Battery less than 10%
				if((int)$sensor["config"]["battery"]<=.1){
					//GF_logging("Phillips Hue Sensor: ".$sensor["name"]." has a battery percentage less than 10%.","|Phillips Hue|Services|Sensors|System Monitor|Warnings|");
				}
				
				//PRESENCE
				if(trim($sensor["type"])=="ZLLPresence" && trim($sensor["state"]["presence"])=="1"){
					$sensorName = $sensor["name"];
					$sensorState = $sensor["state"]["presence"];
					$sensorBattery= $sensor["config"]["battery"];
					$sensorReachable= $sensor["config"]["reachable"];
					$sensorEnabled= $sensor["config"]["on"];
					$sensorLightLevel= $sensor["config"]["on"];
					$sensorAddress= "phue:".trim(str_replace(":","",$sensor["uniqueid"]));
					
					$query = "SELECT sensor_name,sensor_state FROM sensors WHERE enabled<>'0' AND sensor_address='".$sensorAddress."' LIMIT 1";
					$currentSensorData= mysqli_fetch_array(mysqli_query($GS_DBCONN, $query));
					
					if($currentSensorData['sensor_state']=="0" && $sensorState=="1" && $sensorReachable=="1"){
						GF_ifttt($sensorAddress, "0", "1", "Phillips Hue");
					}elseif($sensorReachable=="0"){
						mysqli_query($GS_DBCONN, "UPDATE sensors SET enabled='3' WHERE sensor_address='".$sensorAddress."'");
					}
				}				
				//TEMPERATURE
				if(trim($sensor["type"])=="ZLLTemperature"){
					$sensorAddress= trim(str_replace(":","",$sensor["uniqueid"]));
					$sensorName = $sensor["name"];
					$tempC= ((int)$sensor["state"]["temperature"]) * 0.01;
					$tempF = (($tempC * 1.8) + 32);
					$temp = round($tempF);
					$reachable=$sensor["config"]["reachable"];
					$battery=$sensor["config"]["battery"];
					
					//Create Phue Data Sensor
					$sensorDataTitleArray="SensorID:Temperature(F):Reachable:Battery";
					$sensorDataValueArray=$sensorAddress.":".$temp.":".$reachable.":".$battery."%";
					$sensorDataVisibleArray="0:1:0:1";
					InsertUpdate_DataSensor($sensorName,$sensorDataTitleArray,$sensorDataValueArray,$sensorDataVisibleArray,"ZLL Temperature",$room);
				}
				//LIGHT LEVEL
				if(trim($sensor["type"])=="ZLLLightLevel"){
					$sensorAddress= trim(str_replace(":","",$sensor["uniqueid"]));
					$sensorName = $sensor["name"];
					$lightLevel=$sensor["state"]["lightlevel"];
					$reachable=$sensor["config"]["reachable"];
					$daylight=$sensor["state"]["daylight"];
					$dark=$sensor["state"]["dark"];
					$battery=$sensor["config"]["battery"];
					
					//Create Phue Data Sensor
					$sensorDataTitleArray="SensorID:Light Level(Lux):Daylight:Dark:Reachable:Battery";
					$sensorDataValueArray=$sensorAddress.":".$lightLevel.":".$daylight.":".$dark.":".$reachable.":".$battery."%";
					$sensorDataVisibleArray="0:1:0:0:0:1";
					InsertUpdate_DataSensor($sensorName,$sensorDataTitleArray,$sensorDataValueArray,$sensorDataVisibleArray,"ZLL LightLevel",$room);
				}
				//Hue Wireless Dimmer Switch
				if(trim($sensor["type"])=="ZLLSwitch"){
					$sensorAddress= trim(str_replace(":","",$sensor["uniqueid"]));
					$sensorName = $sensor["name"];
					$button=$sensor["state"]["buttonevent"];
					$reachable=$sensor["config"]["reachable"];
					$battery=$sensor["config"]["battery"];
					
					//Create Phue Data Sensor
					$sensorDataTitleArray="SensorID:Button Event:Reachable:Battery";
					$sensorDataValueArray=$sensorAddress.":".$button.":".$reachable.":".$battery."%";
					$sensorDataVisibleArray="0:1:0:1";
					InsertUpdate_DataSensor($sensorName,$sensorDataTitleArray,$sensorDataValueArray,$sensorDataVisibleArray,"ZLL Switch",$room);
				}
			}//end for each
		}//end if service enabled
	}//end function
	
	function SystemMonitor(){
		global $GS_DBCONN;
		global $GA_enabledService_phue;
		
		if($GA_enabledService_phue['enabled']=="1" || $GA_enabledService_phue['enabled']=="3"){
			if (pingAddress("http://".$GA_enabledService_phue['service_attr1']."/api/".$GA_enabledService_phue['service_attr2']."/")==false){ 
				//Cannot find hue bridge if enabled =1 then set enabled = 3
				mysqli_query($GS_DBCONN, "UPDATE enabled_services SET enabled='3' WHERE service_name='Phillips Hue'"); //Disable Phillips Hue Service
				mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='3' WHERE type='phue' AND enabled='1'"); //Disable All Phillips Hue Devices
				GF_logging("All Phillips Hue devices have been disabled, the Phillips Hue Bridge is Offline.","Phillips Hue|Services|Warnings|Devices|System|System Monitor|Offline|");
			}else{
				//hue bridge found, if enabled =3 then set enabled = 1
				mysqli_query($GS_DBCONN, "UPDATE enabled_services SET enabled='1' WHERE service_name='Phillips Hue' AND enabled='3'"); //Re-enable Phillips Hue Service
				mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='1' WHERE type='phue' AND enabled='3'"); //Enable All Phillips Hue Devices Where Enabled =3
				
				if($GA_enabledService_phue['enabled']=="3"){
					GF_logging("All Phillips Hue devices have been enabled, the Phillips Hue Bridge is Online.","Phillips Hue|Services|Warnings|Devices|System|System Monitor|Online|");
				}
			}
		}
	}
}



