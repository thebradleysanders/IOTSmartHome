<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/


##############################################################################
/*///////////////////////////////////////////////////////////////////////////
ABOUT THIS PAGE:
This page contains all the includes for system services

SmartHome - Created By: Brad Sanders
///////////////////////////////////////////////////////////////////////////*/
##############################################################################

//$GS_phueServiceBypass = false;
//$GS_wemoServiceBypass = false;
//$GS_mqttServiceBypass = false;
//$GS_emailServiceBypass = false;
//$GS_weatherServiceBypass = false;
//$GS_squeezeBoxServiceBypass = false;
//$GS_webIncludesIncluded = false;

require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SmartHome_API/SystemServicesEnableCheck.php"));

//email
if ($GS_emailServiceEnabled == true && $GS_emailServiceBypass == false) {
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/Email_API/class.phpmailer.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/Email_API/class.smtp.php"));
}
//wemo
if ($GS_wemoServiceEnabled == true && $GS_wemoServiceBypass == false) {
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/Belkin_API/Device.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/Belkin_API/Outlet.php"));
}
//mqtt
if ($GS_mqttServiceEnabled == true && $GS_mqttServiceBypass == false) {
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/MQTT_API/phpMQTT.php"));
	$connection  = explode(":",$GA_enabledService_mqtt['service_attr1']);
	$auth  = explode(":",$GA_enabledService_mqtt['service_attr2']);
    $GS_mqttService = new phpMQTT($connection[0], $connection[1], $auth[0], $auth[1], "phpSmartHomeClient" . mt_rand(99, 999999)); //Change client name to something unique
}
//hue
if ($GS_phueServiceEnabled == true && $GS_phueServiceBypass == false) {
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/Phue_API/hue.php"));
    $GS_phueService = new Hue($GA_enabledService_phue['service_attr1'], $GA_enabledService_phue['service_attr2']);
}
//squeezebox
if ($GS_squeezeBoxServiceEnabled == true && $GS_squeezeBoxServiceBypass == false) { //Check Service Enabled
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezeAlarm.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezeCenter.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezeConnection.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezeDatabase.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezeMixer.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezePlayer.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezePlayers.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezePlaylist.php"));
    require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/lib/class.SqueezeStreamProxy.php"));
}

//TTS (Text To Speech)
require_once(realpath(__DIR__ . $upParrentDir . "/System/API/CLI_TTS_API/SystemFunctions.php"));

require_once(realpath(__DIR__ . $upParrentDir . "/System/API/Email_API/SystemFunctions.php"));
require_once(realpath(__DIR__ . $upParrentDir . "/System/API/Belkin_API/SystemFunctions.php"));
require_once(realpath(__DIR__ . $upParrentDir . "/System/API/Phue_API/SystemFunctions.php"));
require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SqueezeBox_API/SystemFunctions.php"));
require_once(realpath(__DIR__ . $upParrentDir . "/System/API/MQTT_API/SystemFunctions.php"));

//SmartHome API

require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SmartHome_API/IFTTT/ifThis.php"));
require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SmartHome_API/IFTTT/ifttt.php"));
require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SmartHome_API/IFTTT/thenThat.php"));
require_once(realpath(__DIR__ . $upParrentDir . "/System/API/SmartHome_API/SystemFunctions.php"));
require_once(realpath(__DIR__ . $upParrentDir . "/gui/includes/WebFunctions.php"));
if ($GS_webIncludesIncluded == true) { //Check Enabled		
    require_once(realpath(__DIR__ . $upParrentDir . "/gui/includes/dashboardCards.php"));
    include(realpath(__DIR__ . $upParrentDir . "/gui/includes/header.php"));
}
