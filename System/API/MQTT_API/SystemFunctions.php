<?php
class Mqtt_Client{
	################################ Device Control ############################
	function Device($device_info,$deviceConfig,$state, $calledBy="") { //Device SQL data, DeviceXML, State
		global $GS_mqttService;
		global $GS_mqttServiceEnabled;
		global $GS_DBCONN;
		
		if ($GS_mqttServiceEnabled==true && $GS_mqttService->connect()) { 
	
			if($deviceConfig[0]->type=="mqtt"){ //regular mqtt device
				if($state=="1"){
					$GS_mqttService->publish($deviceConfig[0]->mqttTopic,$deviceConfig[0]->onID,1);
					GF_history_log($device_info['ID'],"device","1",$calledBy); //history log
					mysqli_query($GS_DBCONN, "UPDATE devices SET device_state='1', last_on_time='".time()."' WHERE ID='".$device_info["ID"]."' AND device_state='0'");
					GF_logging($device_info['device_name'].": State changed to on","|".$deviceConfig[0]->type."|Services|Devices|".$device_info['device_name']."|On|");
				}elseif($state=="0"){
					$GS_mqttService->publish($deviceConfig[0]->mqttTopic,$deviceConfig[0]->offID,1);
					GF_history_log($device_info['ID'],"device","0",$calledBy); //history log
					mysqli_query($GS_DBCONN, "UPDATE devices SET device_state='0', last_off_time='".time()."' WHERE ID='".$device_info["ID"]."' AND device_state='1'");
					GF_logging($device_info['device_name'].": State changed to off","|".$deviceConfig[0]->type."|Services|Devices|".$device_info['device_name']."|Off|");
				}
			}elseif($deviceConfig[0]->type=="mqttNode"){ //send to node mqtt device
				//get node info from db
				foreach($deviceConfig->nodes->node as $nodeXML){
					$query = "SELECT * FROM iot_nodes WHERE ID='".$nodeXML->id."' AND enabled='1' AND node_type='Transmit' ORDER BY ID ASC LIMIT 1";
					$node = mysqli_fetch_array(mysqli_query($GS_DBCONN, $query));
					$nodeExists = mysqli_num_rows(mysqli_query($GS_DBCONN, $query));
					
					if($state=="1" && $nodeExists==1){
						$GS_mqttService->publish("WirelessNode/".$node['node_id']."/ToNode",$deviceConfig[0]->onID,1);					
						GF_history_log($device_info['ID'],"device","1",$calledBy); //history log
						mysqli_query($GS_DBCONN, "UPDATE devices SET device_state='1', last_on_time='".time()."', enabled='1' WHERE ID='".$device_info["ID"]."' AND device_state='0'");
						GF_logging($device_info['device_name'].": State changed to on","|".$deviceConfig[0]->type."|Services|Devices|".$device_info['device_name']."|On|");
					}elseif($state=="0" && $nodeExists==1){
						$GS_mqttService->publish("WirelessNode/".$node['node_id']."/ToNode",$deviceConfig[0]->offID,1);
						GF_history_log($device_info['ID'],"device","0",$calledBy); //history log
						mysqli_query($GS_DBCONN, "UPDATE devices SET device_state='0', last_off_time='".time()."', enabled='1' WHERE ID='".$device_info["ID"]."' AND device_state='1'");
						GF_logging($device_info['device_name'].": State changed to off","|".$deviceConfig[0]->type."|Services|Devices|".$device_info['device_name']."|Off|");
					}elseif($nodeExists==0){
						mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='3' WHERE ID='".$device_info["ID"]."'");
						GF_logging($device_info['device_name'].": has been disabled, cannot find MQTT Node","|".$deviceConfig[0]->type."|Services|Warnings|MQTT|Devices|".$device_info['device_name']."|Disabled|");
					}	
				}
			}
		}
	}
	
	################################## System Monitor ##############################
	function SystemMonitor(){
		global $GS_DBCONN;
		global $GS_settings;
		global $GS_Config;
		global $GS_mqttServiceEnabled;
		
		if ($GS_mqttServiceEnabled==true){
			$query = "SELECT * FROM iot_nodes WHERE enabled='1' ORDER BY ID ASC";
			$results_NodeAlert = mysqli_query($GS_DBCONN, $query);
			while($nodeAlert = mysqli_fetch_assoc($results_NodeAlert)) { 	
				
				//check if enabled and notification are set to 1
				if ((time() > strtotime('+' . $nodeAlert['error_timeout'] . ' minutes', $nodeAlert['last_connected_time'])) &&  $nodeAlert['state']!='Disconnected') {
					//Timed Out	
					mysqli_query($GS_DBCONN, "UPDATE iot_nodes SET state='Disconnected' WHERE ID='".$nodeAlert['ID']."'");
					GF_logging($nodeAlert['node_id'].": Notificatons are enabled for this node and an email has been sent to: ".$GS_settings['outgoing_email_list']. ", this node is offline","Node Alerts|Services|Warnings|Devices|System|System Monitor|Offline|");
					if($nodeAlert['notifications']=="1"){
						$email= $GS_settings['outgoing_email_list'];
						$subject=  $GS_Config['SiteName']." - ALERT";
						$body="Node: ".$nodeAlert['node_id']." is offline, last connection time was: ".date("M/d/Y h:i:s A",$nodeAlert['last_connected_time']);
						GF_logging($body,"Node Alerts|Services|Warnings|Devices|System|System Monitor|Offline|");
						GF_sendEmail($email,$subject,$body,'','Node Alerts');
					}
					//disable devices that use this node
					//mysqli_query($GS_DBCONN,"UPDATE devices SET enabled='3' WHERE enabled<>'0' AND deviceXML LIKE '%<node><id>".$nodeAlert['ID']."</id></node>%'");
					
				}elseif ((time() < strtotime('+' . $nodeAlert['error_timeout'] . ' minutes', $nodeAlert['last_connected_time'])) &&  $nodeAlert['state']!='Connected') {
					//Connected
					mysqli_query($GS_DBCONN, "UPDATE iot_nodes SET state='Connected' WHERE ID='".$nodeAlert['ID']."'");
					GF_logging($nodeAlert['node_id'].": Notificatons are enabled for this node and an email has been sent to: ".$GS_settings['outgoing_email_list'].", this node is online","Node Alerts|Services|Warnings|Devices|System|System Monitor|Online|");
					if($nodeAlert['notifications']=="1"){
						$email= $GS_settings['outgoing_email_list'];
						$subject=  $GS_Config['SiteName']." - ALERT";
						$body="Node: ".$nodeAlert['node_id']." is online, last connection time was: ".date("M/d/Y h:i:s A",$nodeAlert['last_connected_time']);
						GF_logging($body,"Node Alerts|Services|Warnings|Devices|System|System Monitor|Online|");
						GF_sendEmail($email,$subject,$body,'','Node Alerts');
					}
					//enable devices that use this node
					mysqli_query($GS_DBCONN,"UPDATE devices SET enabled='1' WHERE enabled='3' AND deviceXML LIKE '%<node><id>".$nodeAlert['ID']."</id></node>%'");
				}	
			}
		}
	}
}