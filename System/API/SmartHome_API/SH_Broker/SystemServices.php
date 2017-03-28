<?php
##############################################################################
/*///////////////////////////////////////////////////////////////////////////
	ABOUT THIS PAGE:
		This page get sensor/device states from enabled services
	
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
$GS_weatherServiceBypass = true;
$GS_squeezeBoxServiceBypass = false;
$GS_webIncludesIncluded = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require(realpath(__DIR__ . "/../DBConn.php"));
require(realpath(__DIR__ . "/../IOTIncludes.php"));

GF_logging("System Service2, System Services Started","|System Services|Warnings|System|Online|");

while(true){
	usleep(50000);
	
	//refresh system services settings
	include(realpath(__DIR__ . "/../SystemServicesEnableCheck.php"));
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;

	$hue = new Phillips_Hue;
	$hue->SensorStates();
	$hue->DeviceStates();
	GF_belkinWemo_DeviceStates();
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$total_time = round(($finish - $start), 4);
	//echo "System Services: Time:".$total_time."\r\n"; 
	
}
