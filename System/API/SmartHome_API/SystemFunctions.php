<?php

###########################################################################################
##################################### FUNCTIONS ###########################################
###########################################################################################


//############################ LOGGING ##########################

function GF_logging($text, $tags = "", $calledBy = "")
{
    global $GS_DBCONN;
    
    $eventlog_count = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM event_log LIMIT 10001"));
    
    if ($eventlog_count > 10000) {
        mysqli_query($GS_DBCONN, "DELETE FROM event_log ORDER BY ID ASC LIMIT 5000");
    }
    //insert into event_log table
    mysqli_query($GS_DBCONN, "INSERT INTO event_log (event,event_date,tags)VALUES('" . $calledBy . "  " . $text . "','" . date("m/d/Y h:i:s A") . "','" . $tags . "')"); 
}

//########################## HISTORY LOG #######################

function GF_history_log($device_id, $device_type, $state, $reason = "Unknown")
{
    global $GS_DBCONN;
    
    if ($device_type == "device") {
        $query   = "SELECT * FROM devices WHERE ID='" . $device_id . "'";
        $devices = mysqli_query($GS_DBCONN, $query);
        $device  = mysqli_fetch_array($devices);
        
        //insert into history log table
        if ($state == "1" && $device['device_state'] == "0") {
            mysqli_query($GS_DBCONN, "INSERT INTO history_log (device_id, state, startTime, endTime, reason, date_added, device_type)
                VALUES('" . (int) $device_id . "','" . $state . "','" . date("H:i:s") . "','','" . $reason . "','" . date("Y-m-d") . "','" . $device_type . "')");
        } elseif ($state == "0" && $device['device_state'] == "1") {
            $lastID = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT ID FROM history_log WHERE device_id='" . $device_id . "' AND state='1' AND device_type='device' ORDER BY ID DESC LIMIT 1"));
            mysqli_query($GS_DBCONN, "UPDATE history_log SET state='0', endTime='" . date("H:i:s") . "' WHERE device_id='" . $device_id . "' AND ID = '" . $lastID['ID'] . "'");
        }
    } elseif ($device_type == "sensor") {
        $query   = "SELECT * FROM sensors WHERE ID='" . $device_id . "'";
        $sensors = mysqli_query($GS_DBCONN, $query);
        $sensor  = mysqli_fetch_array($sensors);
        
        //insert into history log table
        if ($state == "1" && $sensor['sensor_state'] == "0") {
            mysqli_query($GS_DBCONN, "INSERT INTO history_log (device_id, state, startTime, endTime, reason, date_added, device_type)
                VALUES('" . (int) $device_id . "','" . $state . "','" . date("H:i:s") . "','','" . $reason . "','" . date("Y-m-d") . "','" . $device_type . "')");
        } elseif ($state == "0" && $sensor['sensor_state'] == "1") {
            $endTime = date('H:i:s');
            $lastID = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT ID FROM history_log WHERE device_id='" . $device_id . "' AND state='1' AND device_type='sensor' ORDER BY ID DESC LIMIT 1"));
            mysqli_query($GS_DBCONN, "UPDATE history_log SET state='0', endTime='" . $endTime . "' WHERE device_id='" . $device_id . "' AND date_added='" . date("Y-m-d") . "' AND ID = '" . $lastID['ID'] . "'");
        }
    }
}

########################## UI Notifications ####################

function UINotification($user_id, $type, $data)
{
    global $GS_DBCONN;
    mysqli_query($GS_DBCONN, "INSERT INTO UI_notifications (notificationType, notificatonData, user_id)
        VALUES('" . $type . "','" . $data . "','" . $user_id . "')");
}

//###################### TRANSMIT TO DEVICE ######################

function GF_transmitToDevice($query, $state, $brightness = "0", $color = "", $effect = "", $calledBy = "")
{
    global $GS_DBCONN;
    global $GS_mqttService;
    global $GS_mqttServiceEnabled;
    
    $results = mysqli_query($GS_DBCONN, $query);
    while ($DeviceArray = mysqli_fetch_assoc($results)) {
        $deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>" . trim($DeviceArray["deviceXML"])) or die("Error: Cannot create object");
        
        //MQTT
        if ($GS_mqttServiceEnabled == true && ($DeviceArray['type'] == "mqtt" || $DeviceArray['type'] == "mqttNode")) {
            $mqtt = new Mqtt_Client;
            $mqtt->Device($DeviceArray, $deviceConfig, $state, $calledBy);
            if ($DeviceArray['room'] != "0") {
                $roomArray .= $DeviceArray['room'] . ":";
            }
        } elseif ($DeviceArray['type'] == "phue") {
            //Phillips Hue        
            $hue = new Phillips_Hue;
            $hue->Device($deviceConfig[0]->light, $state, $brightness, $color, $effect, $calledBy);
        } elseif ($DeviceArray['type'] == "wemo") {
            //Belkin Wemo
            GF_belkinWemo_Device($deviceConfig[0]->ip, $deviceConfig[0]->port, $state, $calledBy);
        }
    }
    
    //Set room last active
    $sqlIDWhereList = "";
    if (trim($roomArray) != "") {
        foreach (explode(":", array_unique($roomArray)) as $room) {
            $sqlIDWhereList .= " ID='" . $room . "' OR";
        }
        mysqli_query($GS_DBCONN, "UPDATE home_rooms SET timeout_last_active='" . time() . "', last_active='" . time() . "' WHERE " . trim($sqlIDWhereList, "OR"));
    }
}

//############################ EXECUTE SCRIPT #########################

function GF_executeScript($scriptID)
{
    global $GS_DBCONN;
    
    $query   = "SELECT * FROM custom_scripts WHERE enabled='1' AND ID='" . $scriptID . "'";
    $scripts = mysqli_query($GS_DBCONN, $query);
    $script  = mysqli_fetch_assoc($scripts);
    
   if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        
        $runPath        = 'php -c "' . php_ini_loaded_file() . '" "' . $cwd = __DIR__ . '/../../../gui/CustomScripts/PHP/' . $script['script_location'] . '"';
        $descriptorspec = array(
            0 => array( "pipe", "r" ),
            1 => array( "pipe", "w" )
        );
        
        if (is_resource($prog = proc_open($runPath, $descriptorspec, $pipes))) {
			 GF_logging("PHP Script: " . $script["script_name"] . " Executed.");
        } else {
             GF_logging("PHP Script: " . $script["script_name"] . " Failed.");
        }

    } else { //linux
        $cwd            = __DIR__ . "/../../../gui/CustomScripts/PHP/";
        $runPath        = 'php -c ' . php_ini_loaded_file() . ' ' . $script['script_location'];
        $descriptorspec = array(  0 => array(  "pipe","w"  )
        );
        if (is_resource($prog = proc_open($runPath, $descriptorspec, $pipes, $cwd, NULL))) {
			 GF_logging("PHP Script: " . $script["script_name"] . " Executed.");
        } else {
            GF_logging("PHP Script: " . $script["script_name"] . "  Failed.");
            exit();
        }
    }
}

//###################### DATA SENSORS ######################

function InsertUpdate_DataSensor($name, $DataTitleArray, $DataValueArray, $DataVisibleArray, $type, $room)
{
    global $GS_DBCONN;
    if (trim($name) == "") {
        return false;
    }
    
    //check if need to update or insert
    $update = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM data_sensors WHERE sensor_name='" . $name . "' AND sensor_type='" . $type . "' LIMIT 1"));
    
    if ($update == 1) {
        mysqli_query($GS_DBCONN, "UPDATE data_sensors SET sensor_dataTitle_array='" . $DataTitleArray . "', sensor_dataValue_array='" . $DataValueArray . "', sensor_dataVisible_array='" . $DataVisibleArray . "', last_update='" . time() . "'  WHERE sensor_name='" . $name . "' AND sensor_type='" . $type . "' AND enabled='1'");
    } else {
        mysqli_query($GS_DBCONN, "INSERT INTO data_sensors (sensor_name, sensor_nicename, sensor_dataTitle_array, sensor_dataValue_array, sensor_dataVisible_array, enabled, sensor_type, room, last_update)
        VALUES('" . $name . "', '" . $name . "', '" . $DataTitleArray . "','" . $DataValueArray . "','" . $DataVisibleArray . "','1','" . $type . "','0','" . time() . "')");
    }  
}

//######################### UPDATE WEATHER DATA #######################

function GF_updateWeather()
{
    global $GS_DBCONN;
    global $GS_weatherServiceEnabled;
    global $GA_enabledService_weather;
    global $GS_settings;
    if ($GS_weatherServiceEnabled == true) {
		
        //get current weather data
        $temp         = file_get_contents("http://api.openweathermap.org/data/2.5/weather?zip=" . $GS_settings['zip_code'] . ",us&appid=" . $GA_enabledService_weather['service_attr1'] . "&units=imperial");
        $decoded      = json_decode($temp);
	
		//get HeatIndex
		$T=$decoded->main->temp;
		$R=$decoded->main->humidity;
		(float)$c1 = -42.38;(float)$c2 = 2.049;(float)$c3 = 10.14;(float)$c4 = -0.2248;(float)$c5= -6.838e-3;(float)$c6=-5.482e-2;(float)$c7=1.228e-3;(float)$c8=8.528e-4;(float)$c9=-1.99e-6; 
		(float)$T2 = $T*$T;(float)$R2 = $R*$R;(float)$TR = $T*$R;
		$heatIndex = round($c1 + $c2*$T + $c3*$R + $c4*$T*$R + $c5*$T2 + $c6*$R2 + $c7*$T*$TR + $c8*$TR*$R + $c9*$T2*$R2,0);
	
        $insert_query = "UPDATE weather_data SET 
            city_name='" . $decoded->name . "',
            temp='" . $decoded->main->temp . "',
            humidity='" . $decoded->main->humidity . "',
			heat_index='" . $heatIndex . "',
            temp_min='" . $decoded->main->temp_min . "',
            temp_max='" . $decoded->main->temp_max . "',
            sunrise_time='" . date("h:i:s A", $decoded->sys->sunrise) . "',
            sunset_time='" . date("h:i:s A", $decoded->sys->sunset) . "',
            wind_speed='" . $decoded->wind->speed . "',
            temp_condition ='" . $decoded->weather[0]->description . "',
            last_updated='" . time() . "' WHERE day_added='".date("l")."'";
        mysqli_query($GS_DBCONN, $insert_query);
		
		//Create Phue Data Sensor
		$sensorDataTitleArray="City Name:Temperature:Humidity:Heat Index:Pressure:Temp Min:Temp Max:Sunrise:Sunset:Wind Speed:Condition";
		$sensorDataValueArray=$decoded->name.":".$decoded->main->temp.":".$decoded->main->humidity.":".$heatIndex.":".$decoded->main->pressure.":".$decoded->main->temp_min.":".$decoded->main->temp_max.":";
		$sensorDataValueArray.=date("h&#58;i A", $decoded->sys->sunrise).":".date("h&#58;i A", $decoded->sys->sunset).":".$decoded->wind->speed.":".$decoded->weather[0]->description;
		$sensorDataVisibleArray="0:1:1:0:0:0:0:0:0:1:1";
		InsertUpdate_DataSensor("Open Weather Map",$sensorDataTitleArray,$sensorDataValueArray,$sensorDataVisibleArray,"Outdoor Temperature",$room);
    }
}

//##################### ENABLE/DISABLE SENSOR ######################

function GF_enableDisableSensor($ID, $state, $calledBy = "")
{
    global $GS_DBCONN;
    //change sensor state
    $insert_query = "UPDATE sensors SET enabled='" . $state . "',last_changed_by='IFTTT' WHERE ID='" . $ID . "'";
    mysqli_query($GS_DBCONN, $insert_query);
    //log to event log
    if ($state == 1) {
        GF_logging("Sensor: " . $ID . " Has Been Enabled", "|Enable|Sensors|" . $ID . "|");
    } else {
        GF_logging("Sensor: " . $ID . " Has Been Disabled", "|Disable|Sensors|" . $ID . "|");
    }
}

//###################### ENABLE/DISABLE CAMERA ######################

function GF_enableDisableCamera($ID, $state, $calledBy = "")
{
    global $GS_DBCONN;
    //change camera enable/disable
    $insert_query = "UPDATE camera_list SET enabled='" . $state . "' WHERE ID='" . $ID . "'";
    mysqli_query($GS_DBCONN, $insert_query);
    //log to event log
    if ($state == 1) {
        GF_logging("Camera: " . $ID . " Has Been Enabled", "|Camera|Enable|" . $ID . "|");
    } else {
        GF_logging("Camera: " . $ID . " Has Been Disabled", "|Camera|Disable|" . $ID . "|");
    }
}

//########################## UPDATE SENSOR #########################

function GF_updateSensor($sensor_info, $data, $calledBy = "")
{
    global $GS_DBCONN;
    
    //Update sensor state and room active
    if ($sensor_info['sensor_close_address'] != "") { 
        if ($sensor_info['sensor_address'] == $data && $sensor_info['sensor_state'] == "0") {
			//add to history log
            GF_history_log($sensor_info['ID'], "sensor", "1", $calledBy);
            mysqli_query($GS_DBCONN, "UPDATE sensors SET sensor_state='1',already_notified='0',time_triggered='".time()."', last_triggered='".date("Y-m-d h:i:s")."' WHERE sensor_address='" . $data . "'");
        } elseif ($sensor_info['sensor_close_address'] == $data && $sensor_info['sensor_state'] == "1") {
			 //add to history log
            GF_history_log($sensor_info['ID'], "sensor", "0", $calledBy);
            mysqli_query($GS_DBCONN, "UPDATE sensors SET sensor_state='0',time_triggered='' WHERE sensor_close_address='" . $data . "'");
        }
    }
    //Update room last active    
    if ($sensor_info['enabled'] == "1") {
        mysqli_query($GS_DBCONN, "UPDATE home_rooms SET timeout_last_active='" . time() . "', last_active='" . time() . "' WHERE ID='" . $sensor_info['room'] . "'");
    }
}

//##################### SCAN FOR NEW SENSORS ######################

function GF_scanForNewSensors($data, $calledBy = "")
{
    global $GS_DBCONN;
    //scan for new sensors/devices
    $query   = "SELECT * FROM settings WHERE ID ='1' LIMIT 1";
    $results = mysqli_query($GS_DBCONN, $query);
    $result  = mysqli_fetch_assoc($results);
    if ($result['scan_for_new_sensors'] == "1") {
        
        $query        = "SELECT ID FROM sensors WHERE sensor_address='" . $data . "' OR sensor_close_address='" . $data . "' LIMIT 1";
        $results      = mysqli_query($GS_DBCONN, $query);
        $sensor_exist = mysqli_num_rows($results);
        
        $query        = "SELECT ID FROM devices WHERE device_on_id='" . $data . "' OR device_off_id='" . $data . "' LIMIT 1";
        $results      = mysqli_query($GS_DBCONN, $query);
        $device_exist = mysqli_num_rows($results);
        
        $query    = "SELECT ID FROM find_sensors_list WHERE sensor_address='" . $data . "'";
        $results  = mysqli_query($GS_DBCONN, $query);
        $in_table = mysqli_num_rows($results);
        
        if ($sensor_exist == 0 && $device_exist == 0 && $in_table == 0 && trim($data) != "") {
            mysqli_query($GS_DBCONN, "INSERT INTO find_sensors_list (sensor_address)VALUES('" . $data . "')");
        }
    }
}

//################### RETURN ALARM STATE #####################

function getAlarmState($mode, $calledBy = "")
{
    global $GS_DBCONN;
    //check if alarm in on
    $query    = "SELECT * FROM alarm_status WHERE alarm_state='1' AND alarm_mode='" . $mode . "' LIMIT 1";
    $results  = mysqli_query($GS_DBCONN, $query);
    $alarm_on = mysqli_num_rows($results);
    //return alarm state
    if ($alarm_on == 1) {
        RETURN TRUE;
    } else {
        RETURN FALSE;
    }
}

//################### START SmartHome SERVICE #####################
//http://www.c-sharpcorner.com/code/30/how-to-invokestart-a-process-in-php-and-kill-it-using-process-id.aspx

function startSmartHomeService($page)
{
    global $GS_DBCONN;
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        
        $runPath        = 'php -c "' . php_ini_loaded_file() . '" "' . $cwd = __DIR__ . '/../../../System/API/SmartHome_API/SH_Broker/' . $page . '"';
        $descriptorspec = array( 0 => array( "pipe",  "r" ), 1 => array("pipe", "w" ) );
        
        if (is_resource($prog = proc_open($runPath, $descriptorspec, $pipes))) {
            //Get Parent process Id  
            $ppid = proc_get_status($prog);
            $pid  = $ppid['pid'];
			GF_logging( $runPath);
        } else {
           GF_logging("Failed to start SmartHome Broker service: ".$page);
		   return false;
        }
        $output = array_filter(explode(" ", shell_exec("wmic process get parentprocessid,processid | find \"$pid\"")));
        array_pop($output);
        
        //add proccess id to sql
        mysqli_query($GS_DBCONN, "UPDATE shbroker SET proc_id='" . end($output) . "',state='1' WHERE page_name='" . $page . "'");
        
        //Process Id is  
        return end($output);
    } else { //linux
        $cwd            = __DIR__ . "/../../../System/API/SmartHome_API/SH_Broker/";
        $runPath        = 'php -c ' . php_ini_loaded_file() . ' ' . $page;
        $descriptorspec = array(  0 => array( "pipe", "w" )  );
        if (is_resource($prog = proc_open($runPath, $descriptorspec, $pipes, $cwd, NULL))) {
            
            //Get Parent process Id   
            $ppid = proc_get_status($prog);
            $pid  = ($ppid['pid'] + 1);
            
            //add proccess id to sql
            mysqli_query($GS_DBCONN, "UPDATE shbroker SET proc_id='" . $pid . "',state='1' WHERE page_name='" . $page . "'");
            
            return $pid;
        } else {
			GF_logging("Failed to start SmartHome Broker service: ".$page);
            return false;
        }
    }
}

//################### END SmartHome SERVICE #####################

function endSmartHomeService($page)
{
    global $GS_DBCONN;
    $process = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT proc_id FROM shbroker WHERE page_name='" . $page . "'"));
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec("taskkill /pid " . $process['proc_id'] . " /F", $out);
        mysqli_query($GS_DBCONN, "UPDATE shbroker SET state='0' WHERE page_name='" . $page . "'");
    } else { //linux
        exec("kill -9 " . $process['proc_id']);
        mysqli_query($GS_DBCONN, "UPDATE shbroker SET state='0' WHERE page_name='" . $page . "'");
    }
}

//###################### CHECK SmartHome SERVICE ######################

function checkSmartHomeService($pid)
{
    global $GS_DBCONN;
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $processes = explode("\n", shell_exec("tasklist.exe"));
        foreach ($processes as $process) {
            if (strpos("Image Name", $process) === 0 || strpos("===", $process) === 0)
                continue;
            $matches = false;
            preg_match("/(.*?)\s+(\d+).*$/", $process, $matches);
            
            if (trim($matches[2]) == $pid) {
                return true;
            }
        }
        mysqli_query($GS_DBCONN, "UPDATE shbroker SET state='3' WHERE proc_id='" . $pid . "'");
        return false;
        
    } else { //linux
        if (file_exists('/proc/' . $pid)) {
            return true;
        } else {
            mysqli_query($GS_DBCONN, "UPDATE shbroker SET state='3' WHERE proc_id='" . $pid . "'");
            return false;
        }
    }
}

//###################### RESTART SERVER ######################

function GF_restart_smarthome($calledBy = "")
{
    GF_logging("System Event: Restart SmartHome", "|System|Warnings|Restart|");
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        shell_exec('shutdown.exe /r /f /t 300 /c "SmartHome WebUI Called By: ' . $calledBy . '"'); // reboot
    } else { //Linux
        exec("sudo reboot");
    }
}

//###################### SHUTDOWN SERVER ######################

function GF_shutdown_smarthome($calledBy = "")
{
    GF_logging("System Event: Shutdown SmartHome", "|System|Warnings|Shutdown|");
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        shell_exec('shutdown.exe /s /f /t 300 /c "SmartHome WebUI Called By: ' . $calledBy . '"'); // shutdown
    } else { //Linux
        exec("sudo poweroff");
    }
}

#####################################################################################################################
#################################################### Alarm Functions ################################################
#####################################################################################################################

function GF_triggerAlarm($sensorAddress, $calledBy = "")
{
    global $GS_DBCONN;
    global $GS_Config;
    
    $query      = "SELECT * FROM alarm_status WHERE alarm_state='1'";
    $results1   = mysqli_query($GS_DBCONN, $query);
    $alarm_info = mysqli_fetch_assoc($results1);
    
    if ($alarm_info['alarm_triggered'] == "0") {
        //sensor count by address
        if ($alarm_info['alarm_mode'] == "Home") {
            $query = "SELECT * FROM sensors WHERE (sensor_address='" . trim($sensorAddress) . "' OR sensor_close_address='" . trim($sensorAddress) . "') AND is_alarmSensorHome='1' AND enabled='1'";
        } elseif ($alarm_info['alarm_mode'] == "Away") {
            $query = "SELECT * FROM sensors WHERE (sensor_address='" . trim($sensorAddress) . "' OR sensor_close_address='" . trim($sensorAddress) . "') AND is_alarmSensorAway='1' AND enabled='1'";
        }
        //find sensor who activated the alarm
        $results2    = mysqli_query($GS_DBCONN, $query);
        $sensor_info = mysqli_fetch_assoc($results2);
        $is_sensor   = mysqli_num_rows($results2);
        
        if ($is_sensor > 0) { //the alarm was tripped by a valid sensor
            GF_logging("Alarm Triggered By Sensor: " . $sensor_info['sensor_name'] . " Sensor Kind: " . $sensor_info['sensor_kind'], "|Alarm|Triggered|" . $sensor_info['sensor_name'] . "|");
            
            //notify
            if ($sensor_info['notifications'] == "1") {
                $Message = "Sensor: " . $sensor_info['sensor_name'] . " Triggered <br/> Alarm State: " . ucfirst($alarm_info['alarm_state'] . " Alarm Mode: " . ucfirst($alarm_info['alarm_mode']));
                GF_sendEmail($GS_settings['outgoing_email_list'], $GS_Config['SiteName'], $Message, '', $calledBy);
            }
            
            //set state to triggered
            mysqli_query($GS_DBCONN, "UPDATE alarm_status SET alarm_triggered='1', alarm_time='" . time() . "' WHERE ID='1'");
            
        } elseif ($is_sensor == 0) { //the alarm was NOT tripped by a valid sensor
            RETURN "The alarm was not tripped by a valid sensor";
        }
    }
}

//########################## Activate Alarm #############################

function GF_ActivateAlarm()
{
    /* This is execute in system timer after a timeout time */
    global $GS_DBCONN;
    global $GS_Config;
    
    //log this event
    GF_logging("<span style='color:red;'>Alarm activated!</span>", "|Alarm|Triggered|");
    
    //blink lights
    $query = "SELECT * FROM devices WHERE enabled='1' ORDER BY room ASC";
    GF_transmitToDevice($query, "noState", "100", "noColor", "BlinkTimes", "USER:" . $_SESSION['id']);
    
    //play sound in each room
    $query     = "SELECT * FROM music_servers WHERE enabled='1'";
    $SBPlayers = mysqli_query($GS_DBCONN, $query);
    while ($SBPlayer = mysqli_fetch_assoc($SBPlayers)) {
        GF_roomSoundControl($SBPlayer['room_id'], "Alarm.mp3", "100", "Play");
        GF_roomSoundControl($SBPlayer['room_id'], "", "", "Repeat");
    }
}

//########################## Change Alarm State #############################

function GF_ChangeAlarmState($mode, $state, $calledBy = "")
{
    global $GS_DBCONN;
    
    //insert into alarmState table
    if ($mode != "") {
        $insert_query = "UPDATE alarm_status SET alarm_mode='" . $mode . "' WHERE ID='1'";
        mysqli_query($GS_DBCONN, $insert_query);
    }
    
    if ($state != "") {
        if ($state == "0") {
            
            $query   = "SELECT * FROM home_rooms";
            $results = mysqli_query($GS_DBCONN, $query);
            while ($room_list = mysqli_fetch_assoc($results)) {
                GF_roomSoundControl($room_list['ID'], "", "", "Stop");
            }
            $alarm_on_time = ""; //set to nothong since alarm is off now
        } else {
            $alarm_on_time = time();
        }
        $insert_query = "UPDATE alarm_status SET alarm_state='".$state."', alarm_triggered='0', alarm_time='', alarm_on_time='" . $alarm_on_time . "', alarm_activated='0' WHERE ID='1'";
        mysqli_query($GS_DBCONN, $insert_query);
    }
    
    //log this event
    $state_nicename = ($state == '1' ? "On" : "Off");
    GF_logging("Alarm Status Changed, <b>Mode:</b> " . $mode . " <b>State</b>: " . $state_nicename, "Alarm State|System Services|" . $mode . "|" . $state_nicename . "|");
}

############################################################################################################
##########################################   UPDATE NODE RAW   #############################################
############################################################################################################
function GF_UpdateNode($Rawdata)
{
    global $GS_DBCONN;
    
    $array      = explode(":", trim($Rawdata));
    $Name       = trim($array[1]);
    $version    = trim($array[2]);
    $SSID       = trim($array[3]);
    $PASSWORD   = trim($array[4]);
    $IP         = trim($array[5]);
    $Signal     = trim($array[6]);
    $MfDate     = trim($array[7]);
    $Desc       = trim($array[8]);
    $LastPacket = trim($array[9]);
    $Uptime     = trim($array[10]);
    $Type       = trim($array[11]);
    
    //check if need to update or insert
    $update = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM iot_nodes WHERE node_id='" . $Name . "' AND node_type='" . $Type . "' LIMIT 1"));
    
    if ($update == 1) {
        $insert_query = "UPDATE iot_nodes SET 
        last_connected_time='" . time() . "',
        ip_address='" . $IP . "',
        signal_strength='" . $Signal . "',
        SSID='" . $SSID . "',
        SSID_PASSWORD='" . $PASSWORD . "',
        version='" . $version . "',
        uptime='" . $Uptime . "',
        mfDate='" . $MfDate . "',
        state='Connected'
        WHERE node_id='" . $Name . "'";
        mysqli_query($GS_DBCONN, $insert_query);
    } else {
        if ($Name != "" && $Type != "") {
            $insert_query = "INSERT INTO iot_nodes (node_id,node_type,room,time_added,enabled,last_connected_time,decription,notifications,error_timeout,ip_address,signal_strength,SSID,SSID_PASSWORD,version,uptime,mfDate)
            VALUES('" . $Name . "','" . $Type . "','0','" . time() . "','0','" . time() . "','" . $Desc . "','0','5','" . $IP . "','" . $Signal . "','" . $SSID . "','" . $PASSWORD . "','" . $version . "','" . $uptime . "','" . $MfDate . "')";
            mysqli_query($GS_DBCONN, $insert_query);
            GF_logging("New Sensor/Device Found: " . $Name, "|Find Sensor/Device|Sensors|System Services|" . $Name . "|");
        }
    }
    
    $enabled = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM iot_nodes WHERE node_id='" . $Name . "' AND node_type='" . $Type . "' AND enabled='1' LIMIT 1"));
    if ($enabled == 1) {
        return $LastPacket; //Return data out of array
    } else { //node not enabled
        return "";
    }
}

############################################################################################################