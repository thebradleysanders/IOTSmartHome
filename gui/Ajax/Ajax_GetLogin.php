<?php 
$GS_phueServiceBypass = true;
$GS_wemoServiceBypass = true;
$GS_mqttServiceBypass = true;
$GS_emailServiceBypass = true;
$GS_squeezeBoxServiceBypass = true;
$GS_webIncludesIncluded = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require_once(realpath(__DIR__ ."/../../System/API/SmartHome_API/DBConn.php"));
require_once(realpath(__DIR__ ."/../../System/API/SmartHome_API/IOTIncludes.php"));
	


echo processLogin($_GET['username'], $_GET['password'],true);