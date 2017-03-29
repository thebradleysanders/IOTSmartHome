<?php
$GS_Config = array();
/* INITIAL CONFIG SETTINGS   Created By: Brad Sanders */

##############################################  General ##############################################
 $GS_Config['SiteName'] = "IOT";
 $GS_Config['InstallType'] = "normal"; //lite or normal
 $GS_Config['LocalIP'] = "10.0.0.200";
 $GS_Config['notificationIcon'] = "http://".$_SERVER['SERVER_NAME']."/sh/gui/images/favicons/apple-icon-152x152.png";
 $GS_Config['MusicDir'] = "http://10.0.0.200/sh/gui/music/";

//Theme Color
 $GS_Config['themeColorMain'] = "#3b5998"; //#2980b9
 $GS_Config['themeColorSub'] = "#466ab4"; //#3498db

 $GS_Config['MaxLoginTime'] = 30; //30 Days
######################################################################################################

############################################### MQTT #################################################
$GS_Config['MQTTRecievePath'] = "WirelessNode/toBroker/#";
######################################################################################################

##################################### Default User Permissions #######################################
//default permissions users get when there account is created
$GS_Config['DefaultUserPermissions'] = ":index.php|read:index.html|read:";
######################################################################################################
 ########################################### Database Setup ###########################################
$GS_Config["MySqlServer"] = "localhost";
$GS_Config["dbUsername"] = "root";
$GS_Config["dbPassword"] = "1234";
$GS_Config["dbName"] = "smarthome";
######################################################################################################
##########################################  Decryption Password ######################################
$GS_Config["EncryptKey"] = "SH337808119235775";
######################################################################################################
$GS_Config["installCheck"] = "true";
