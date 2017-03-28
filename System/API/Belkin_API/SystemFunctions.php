<?php

################################ Device Control ############################
function GF_belkinWemo_Device($ip, $port, $state, $calledBy=""){
	global $GS_DBCONN;
	global $GS_wemoServiceEnabled;
	if($GS_wemoServiceEnabled == true){ //Check Service Enabled	
		//Belkin Wemo
		$LS_wemoService = new Outlet(trim($ip));
		$LS_wemoService->setIsOn($state,$port); 
	}
}

######################### GET ALL WEMO DEIVCE STATES ########################
function GF_belkinWemo_DeviceStates(){
	global $GS_DBCONN;
	global $GS_wemoServiceEnabled;
	
	if($GS_wemoServiceEnabled==true){
		$query    = "SELECT * FROM devices WHERE type='wemo' AND enabled='1'";
		$results3_wemo = mysqli_query($GS_DBCONN, $query);
		while ($wemo = mysqli_fetch_assoc($results3_wemo)) {
			$deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>".trim($wemo['deviceXML'])) or die("Error: Cannot create object");

			$LS_wemoService = new Outlet($deviceConfig[0]->ip);
		
			if($LS_wemoService->getIsOn($deviceConfig[0]->port)=="1" && $wemo['device_state']=="0" ){
				GF_history_log($wemo['ID'],"device","1");
				mysqli_query($GS_DBCONN, "UPDATE devices SET device_state='1',last_on_time='".time()."' WHERE ID='" . $wemo['ID'] . "'");	
				//reset room timeout_last_active time where sensor is
				mysqli_query($GS_DBCONN, "UPDATE home_rooms SET timeout_last_active='" . time() . "' WHERE ID='" . $wemo['room'] . "'");
				GF_logging("Belkin Wemo: ".$wemo['device_name']." State Has Changed To On","Belkin Wemo|Services|Devices|".$wemo['device_name']."|On|");
				
			}elseif($LS_wemoService->getIsOn($deviceConfig[0]->port)=="0" && $wemo['device_state']=="1" ){
				GF_history_log($wemo['ID'],"device","0");
				mysqli_query($GS_DBCONN, "UPDATE devices SET device_state='0',last_off_time='".time()."' WHERE ID='" . $wemo['ID'] . "'");
				GF_logging("Belkin Wemo: ".$wemo['device_name']." State Has Changed To Off","Belkin Wemo|Services|Devices|".$wemo['device_name']."|Off|");
			}
		}			
	}
}

################################## System Monitor ###############################
function GF_belkinWemo_SystemMonitor(){
	global $GS_DBCONN;
	global $GA_enabledService_wemo;

	if($GA_enabledService_wemo['enabled']=="1" || $GA_enabledService_wemo['enabled']=="3"){
		//Check each wemo device
		$query = "SELECT * FROM devices WHERE type='wemo' AND enabled<>'0' ORDER BY enabled ASC";
		$results_wemo = mysqli_query($GS_DBCONN, $query);
		while($wemoDevice = mysqli_fetch_assoc($results_wemo)){
			//get current wemo info
			$deviceConfig = simplexml_load_string(trim($wemoDevice["deviceXML"])) or die("Error: Cannot create object");
			$ip=$deviceConfig[0]->ip;
			$port = $deviceConfig[0]->port;
			
			//Check currenly configured port
			if(pingAddress("http://" .$ip. ":".$port."/setup.xml")==false){ 
				//not connected so we set enabled =3 and reconfigure
				mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='3' WHERE ID='" . $wemoDevice['ID'] . "' ");
			}else{					
				continue; //connected so we skip 
			}

			if(pingAddress("http://" .$ip. ":49154/setup.xml")==true && $port!="49154"){ //PORT: 49154
				//connected on port 49154,enable wemo if enabled = 3
				$xmlstr = "
				<?xml version='1.0' standalone='yes'?>
					<device>
						<type>wemo</type>
						<ip>".$deviceConfig[0]->ip."</ip>
						<port>49154</port>
						<brightness>0</brightness>
					</device>";
					
				mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='1' deviceXML='".$xmlstr."' WHERE ID='" . $wemoDevice['ID'] . "' AND enabled<>'0' ");
			}elseif(pingAddress("http://" .$ip. ":49153/setup.xml")==true && $port!="49153"){ //PORT: 49153
				//connected on port 49153,enable wemo if enabled = 3
				$xmlstr = "
				<?xml version='1.0' standalone='yes'?>
					<device>
						<type>wemo</type>
						<ip>".$deviceConfig[0]->ip."</ip>
						<port>49153</port>
						<brightness>0</brightness>
					</device>";
				mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='1' deviceXML='".$xmlstr."' WHERE ID='" . $wemoDevice['ID'] . "' AND enabled<>'0' ");
			}elseif(pingAddress("http://" .$ip. ":49152/setup.xml")==true && $port!="49152"){ //PORT: 49152
				//connected on port 49152, enable wemo if enabled =3
				$xmlstr = "
				<?xml version='1.0' standalone='yes'?>
					<device>
						<type>wemo</type>
						<ip>".$deviceConfig[0]->ip."</ip>
						<port>49152</port>
						<brightness>0</brightness>
					</device>";						
				mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='1' deviceXML='".$xmlstr."' WHERE ID='" . $wemoDevice['ID'] . "' AND enabled<>'0' ");
			}else{
				//not connected, set enable =3 and keep looking
				mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='3' WHERE ID='" .$wemoDevice['ID']. "' AND enabled ='1'");
			}
		}//End While
	}
}

