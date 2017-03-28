<?php
##############################################################################
/*///////////////////////////////////////////////////////////////////////////
	ABOUT THIS PAGE:
		This page pings hue,squeezebox and wemo devices to check connectivity
		and if needed it will enabled/disable services or change device settings
		
	SmartHome - Created By: Brad Sanders
///////////////////////////////////////////////////////////////////////////*/
##############################################################################
if (php_sapi_name() != "cli") { //close if not ran in CLI (Commnad Line)
	exit("This Page Must Be Ran Via PHP CLI. Use The SmartHome Broker.");
}

$GS_phueServiceBypass = true;
$GS_wemoServiceBypass = true;
$GS_mqttServiceBypass = true;
$GS_emailServiceBypass = false;
$GS_weatherServiceBypass = true;
$GS_squeezeBoxServiceBypass = true;
$GS_webIncludesIncluded = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require(realpath(__DIR__ . "/../DBConn.php"));
require(realpath(__DIR__ . "/../IOTIncludes.php"));

GF_logging("System Service, System Monitor Started","|System Services|Warnings|System|System Monitor|Online|");

function pingAddress($ip) {	
	$context = stream_context_create(array('http'=>array('timeout'=>2.0)));
	$contents = fopen($ip, 'r', false, $context );  
	if($contents === false) {RETURN false;}else{RETURN true; }
}

while(true){ 
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
	

	####################################################################################################################
	###-------------------- Checks For: Node Errors, Enabled Services Errors and Server Errors ----------------------###
	####################################################################################################################		
	
	//refresh system services settings
	include(realpath(__DIR__ . "/../SystemServicesEnableCheck.php"));
	
	##########################################################################
	############################## SERVER ALERTS #############################
	##########################################################################
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$sysInfo =  preg_split("/\\r\\n|\\r|\\n/",substr(shell_exec("systeminfo.exe"),0,5000));
			$diskInfo = preg_split("/\\r\\n|\\r|\\n/",shell_exec('DIR | FIND "free"'));
			
			$hostname = trim( shell_exec("hostname"));
			$uptime = trim(explode(":", shell_exec('systeminfo | find "System Boot Time:"'))[1]);
			$totalDiskSize = trim(explode(":", shell_exec('wmic logicaldisk get size,caption | find "C:"'))[1]);
			$freeDiskSize = round(trim(explode(":", shell_exec('wmic logicaldisk get freespace,caption | find "C:"'))[1])/1024/1024.1024/1024, 1);
			$disk = $freeDiskSize;
			
			$totalMemory = round(str_replace("TotalPhysicalMemory", "", shell_exec("wmic computersystem get TotalPhysicalMemory"))/1024/1024,0);
			$freeMemory = round(explode("=",shell_exec("wmic OS get FreePhysicalMemory /Value"))[1]/1024,0);
			$memory = $freeMemory;
					
			//Create Phue Data Sensor
			$sensorDataTitleArray="Host:Free Memory (MB):Free Storage (GB):Uptime";
			$sensorDataValueArray=$hostname.":".$memory.":".$disk.":".date("m-d-y",str_replace(",","",$uptime));
			$sensorDataVisibleArray="1:1:1:1";
			InsertUpdate_DataSensor("System Information",$sensorDataTitleArray,$sensorDataValueArray,$sensorDataVisibleArray,"System Information",$room);			
		}else{//Linux
			$hostname = trim( shell_exec("hostname"));
			$uptime = trim(explode(" ", trim(shell_exec('cat /proc/uptime"')))[0])/60;
			//$totalDiskSize = trim(explode(":", shell_exec('wmic logicaldisk get size,caption | find "C:"'))[1]);
			//$freeDiskSize = round(trim(explode(":", shell_exec('wmic logicaldisk get freespace,caption | find "C:"'))[1])/1024/1024.1024/1024, 1);
			$disk = $freeDiskSize;
			
			$totalMemory = round(str_replace("kB", "", explode(":", shell_exec("cat /proc/meminfo"))[1])/1024,0);
			$freeMemory = round(str_replace("kB", "", explode(":", shell_exec("cat /proc/meminfo"))[2])/1024,0);
			$memory = $freeMemory;
			
			$cpuTemp= trim(shell_exec("cat /sys/class/thermal/thermal_zone0/temp"));
					
			//Create Phue Data Sensor
			$sensorDataTitleArray="Host:Free Memory (MB):Free Storage (GB):Uptime:CPU Temp";
			$sensorDataValueArray=$hostname.":".$memory.":".$disk.":".date("m-d-y",str_replace(",","",$uptime)).":".$cpuTemp;
			$sensorDataVisibleArray="1:1:1:1:1";
			InsertUpdate_DataSensor("System Information",$sensorDataTitleArray,$sensorDataValueArray,$sensorDataVisibleArray,"System Information",$room);
		}
		
		
		//Check all SmartHome Services
		$query1 = "SELECT * FROM shbroker WHERE state<>'0'";
		$results_proc = mysqli_query($GS_DBCONN, $query1);
		while($process = mysqli_fetch_assoc($results_proc)){
			if(checkSmartHomeService($process['proc_id']) == false){
				startSmartHomeService($process['page_name']); //restart proccess
			}
		}
	##########################################################################
	############################# SERVICE ALERTS #############################
	##########################################################################	
	
		############ PHILLIPS HUE ########### 
		$hue = new Phillips_Hue;
		$hue->SystemMonitor();
			
		############ SQUEEZEBOX #############
		GF_squeezebox_SystemMonitor();
			
		############ BELKIN WEMO ############
		GF_belkinWemo_SystemMonitor();
			
		########### MQTT Nodes ##############
		$mqtt = new Mqtt_Client;
		$mqtt->SystemMonitor();
		
		
	
		
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	//echo "System Monitor: Time:".$total_time."\r\n";
	sleep(20);
}