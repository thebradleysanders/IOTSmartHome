<?php
##############################################################################
/*///////////////////////////////////////////////////////////////////////////
ABOUT THIS PAGE:
This page is ran in SH Broker and subcripes to a particular mqtt topic
and proccesses the info.

SmartHome - Created By: Brad Sanders
///////////////////////////////////////////////////////////////////////////*/
##############################################################################
if (php_sapi_name() != "cli") { //close if not ran in CLI (Commnad Line)
    exit("This Page Must Be Ran Via PHP CLI. Use The SmartHome Broker.");
}

$GS_phueServiceBypass       = false;
$GS_wemoServiceBypass       = false;
$GS_mqttServiceBypass       = false;
$GS_emailServiceBypass      = false;
$GS_weatherServiceBypass    = false;
$GS_squeezeBoxServiceBypass = false;
$GS_webIncludesIncluded     = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require(realpath(__DIR__ . "/../DBConn.php"));
require(realpath(__DIR__ . "/../IOTIncludes.php"));

if ($GS_mqttServiceEnabled == false) {
    exit;
}

GF_logging("System Service, Node Recieve Queue Started", "|System Services|Warnings|System|MQTT|Online|Node Recieve Queue|");

if (!$GS_mqttService->connect()) {
    GF_logging("Error starting SHBroker:NodeRecieveQueue.php, Cannot connect to MQTT server", "|System Services|Warnings|System|MQTT|Online|Node Recieve Queue|");
    exit(1);
}

$topics[$GS_Config['MQTTRecievePath']] = array(
    "qos" => 0,
    "function" => "procmsg"
);
$GS_mqttService->subscribe($topics, 0);
while ($GS_mqttService->proc()) {
}
$GS_mqttService->close();

function procmsg($topic, $fromNode) //forever loop
{
	//refresh system services settings
    require(realpath(__DIR__ . "/../SystemServicesEnableCheck.php"));
    
	if(strpos($topic, "/Status")!==false){
		GF_UpdateNode(trim($fromNode));
	}elseif(strpos($topic, "/Sensor")!==false){
		GF_ifttt(trim($fromNode), $debug, "0", "NodeRecieveQueue"); //the last attribute is 0 because the data is in an array
	}
    
    
} //end proc message
