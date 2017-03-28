<?php 
	

	$GS_phueServiceBypass = false;
	$GS_wemoServiceBypass = false;
	$GS_mqttServiceBypass = false;
	$GS_emailServiceBypass = false;
	$GS_squeezeBoxServiceBypass = false;
	$GS_webIncludesIncluded = false;
	$upParrentDir = '/../../..';
	require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/DBConn.php"));

	if($_GET['CookieDeleteBypass']!="1"){ //dont delete cookie
		setcookie("lastLogin", "", 0,'/');
	}
	
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);
	
	header("Location: login.php");

