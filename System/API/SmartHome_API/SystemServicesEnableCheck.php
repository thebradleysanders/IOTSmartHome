<?php
##############################################################################
/*///////////////////////////////////////////////////////////////////////////
	ABOUT THIS PAGE:
		This page checks the enabled state of system services
	
	SmartHome - Created By: Brad Sanders
///////////////////////////////////////////////////////////////////////////*/
##############################################################################

$GA_enabledService_phue       = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM enabled_services WHERE service_name='Phillips Hue'"));
$GA_enabledService_mqtt       = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM enabled_services WHERE service_name='MQTT'"));
$GA_enabledService_wemo       = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM enabled_services WHERE service_name='Belkin Wemo'"));
$GA_enabledService_squeezebox = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM enabled_services WHERE service_name='SqueezeBox'"));
$GA_enabledService_email      = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM enabled_services WHERE service_name='Email'"));
$GA_enabledService_weather    = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM enabled_services WHERE service_name='Weather'"));
$GA_enabledService_spotify    = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM enabled_services WHERE service_name='Spotify'"));



$GS_phueServiceEnabled 		 = ($GA_enabledService_phue['enabled'] == '1' ) ? true : false;
$GS_mqttServiceEnabled 		 = ($GA_enabledService_mqtt['enabled'] == '1' ) ? true : false;
$GS_wemoServiceEnabled		 = ($GA_enabledService_wemo['enabled'] == '1' ) ? true : false;
$GS_squeezeBoxServiceEnabled = ($GA_enabledService_squeezebox['enabled'] == '1' ) ? true : false;
$GS_emailServiceEnabled 	 = ($GA_enabledService_email['enabled'] == '1' ) ? true : false;
$GS_weatherServiceEnabled 	 = ($GA_enabledService_weather['enabled'] == '1' ) ? true : false;
$GS_spotifyServiceEnabled 	 = ($GA_enabledService_spotify['enabled'] == '1' ) ? true : false;


$GS_settings = mysqli_fetch_array(mysqli_query($GS_DBCONN, "SELECT * FROM settings WHERE ID='1' LIMIT 1"));