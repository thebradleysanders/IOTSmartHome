<?php  
    if(!empty($_POST) || !empty($_GET)){
    	$GS_phueServiceBypass = false;
    	$GS_wemoServiceBypass = false;
    	$GS_mqttServiceBypass = false;
    	$GS_emailServiceBypass = false;
    	$GS_squeezeBoxServiceBypass = false;
    	$GS_webIncludesIncluded = true;
    }else{
    	$GS_phueServiceBypass = true;
    	$GS_wemoServiceBypass = true;
    	$GS_mqttServiceBypass = true;
    	$GS_emailServiceBypass = true;
    	$GS_squeezeBoxServiceBypass = true;
    	$GS_webIncludesIncluded = true;
    }
     
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/DBConn.php"));
    
    
    ################## AUTOLOAD ####################
    if($_GET['autoload']=="true"){
    	$GS_phueServiceBypass = true;
    	$GS_wemoServiceBypass = true;
    	$GS_mqttServiceBypass = true;
    	$GS_emailServiceBypass = true;
    	$GS_squeezeBoxServiceBypass = true;
    	$GS_webIncludesIncluded = false;
    	
    	$upParrentDir = '/../../..';
    	require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    	
    	include("Autoload/AutoLoad_indexSensors.php");
    	include("Autoload/AutoLoad_DeviceData.php");
    	include("Autoload/AutoLoad_indexRoomStates.php");
    	include("Autoload/AutoLoad_indexGroupStates.php");
    	include("Autoload/AutoLoad_DataStates.php");
    	include("Autoload/AutoLoad_SHAlerts.php");
    	include("Autoload/Autoload_UINotifications.php");
		include("Autoload/Autoload_weatherCard.php");
    	//include("Autoload/AutoLoad_SqueezeBox.php");
    	include("Autoload/Autoload_checkTempEncode.php");
    	exit;	
    }
    ################################################
    
    #################### API #######################
    $page_title="Dashboard"; 
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################

    
    //Update Auto-off
    if(temp_decode($_POST['type'])=='update_autooff'){
    	$insert_query="UPDATE devices SET 
    	enable_auto_off='".clean_text((int)$_POST['auto_off'],1)."',
    	last_on_time='".($_POST['auto_off'] == '1' ? time() : '')."'
    	WHERE ID='".clean_text($_POST['device'],11)."'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    }
    	 
    //update Weather
    if(temp_decode($_POST['type'])=='update_weather' ){
    	GF_updateWeather();
    }
    
    //quick change sensor enabled disabled
    if(GetUserPermissions("edit")==true) {
    	//quick change sensor enabled disabled
    	if(temp_decode($_POST['type'])=='enableSensor'){ //Enable
    		$insert_query="UPDATE sensors SET enabled='1',last_changed_by='".clean_text($_SESSION['id'],11)."' WHERE ID='".clean_text($_POST['sensorID'],11)."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));	
			GF_logging("Sensor: ".clean_text($_POST['sensorID'],11)." has been enabled by user: ".$_SESSION['name'],"");			
    	}
    	if(temp_decode($_POST['type'])=='disableSensor'){ //Disable
    		$insert_query="UPDATE sensors SET enabled='0',last_changed_by='".clean_text($_SESSION['id'],11)."' WHERE ID='".clean_text($_POST['sensorID'],11)."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
			GF_logging("Sensor: ".clean_text($_POST['sensorID'],11)." has been disabled by user: ".$_SESSION['name'],"");
    	}
    }   
    
    //Change Device Brightness
    if(temp_decode($_POST['type'])=="ChangeDeviceBrightness"){	
		$query    = "SELECT * FROM devices WHERE ID='".clean_text($_POST['device'],11)."' LIMIT 1";
		GF_transmitToDevice($query, "1", clean_text($_POST['brightness'],5), "noColor", "noEffect", "USER:".$_SESSION['id']);
    }		
    
    //toggle room on
    if(temp_decode($_POST['type'])=="toggle_room_on"){
    	//send to all rooms since there is not specific room set
    	$query    = "SELECT * FROM devices WHERE room='".clean_text($_POST['room_id'],11)."' ORDER BY type ASC";
		GF_transmitToDevice($query, "1", "100", "noColor", "noEffect", "USER:".$_SESSION['id']);
    }
    //toggle room off
    if(temp_decode($_POST['type'])=="toggle_room_off"){
    	//send to all rooms since there is not specific room set
    	$query    = "SELECT * FROM devices WHERE room='".clean_text($_POST['room_id'],11)."'";
    	GF_transmitToDevice($query, "0", "0", "noColor", "noEffect", "USER:".$_SESSION['id']);
    	GF_roomSoundControl(clean_text($_POST['room_id'],11),"","","Power-off","USER:".$_SESSION['id']); //Stop SqueezeBox Servers    
    }		
    //toggle group on
    if(temp_decode($_POST['type'])=="toggle_group_on"){
    	//send to all rooms since there is not specific room set
    	$query    = "SELECT * FROM devices WHERE group_id='".clean_text($_POST['group_id'],11)."'";
    	GF_transmitToDevice($query, "1", "100", "noColor", "noEffect", "USER:".$_SESSION['id']);    
    }
    //toggle group off
    if(temp_decode($_POST['type'])=="toggle_group_off"){
    	//send to all rooms since there is not specific room set
    	$query    = "SELECT * FROM devices WHERE group_id='".clean_text($_POST['group_id'],11)."'";
    	GF_transmitToDevice($query, "0", "0", "noColor", "noEffect", "USER:".$_SESSION['id']);    
    }
    
    //room Brightness
    if(temp_decode($_POST['type'])=="RoomBrightness"){
    	$query = "SELECT * FROM devices WHERE room='".clean_text($_POST['room_id'],11)."' AND tags LIKE '%Dimmable%'";
    	GF_transmitToDevice($query, "1", $_POST['brightness'], "noColor", "noEffect", "USER:".$_SESSION['id']);
    }
    //Room Modal Groups
    if(temp_decode($_POST['type'])=="modalRoomGroup"){
    	$query = "SELECT * FROM devices WHERE room='".$_POST['room_id']."' AND group_id='".clean_text($_POST['group_id'],11)."'";
    	GF_transmitToDevice($query, $_POST['state'], "100", "noColor", "noEffect", "USER:".$_SESSION['id']);
    } //end if
    
    // SqueezeBox Music Control 
    if(temp_decode($_POST['type'])=="SqueezeBox" && $GS_squeezeBoxServiceEnabled == true){ //Check If Service Is Enabled 
    	if($_POST['Command']=='Resume'){GF_roomSoundControl($_POST['room_id'],"","","Resume","USER:".$_SESSION['id']);} //Resume
    	if($_POST['Command']=='Pause'){GF_roomSoundControl($_POST['room_id'],"","","Pause","USER:".$_SESSION['id']);} //Pause
    	if($_POST['Command']=="Volume"){GF_roomSoundControl($_POST['room_id'],"",$_POST['volume'],"Volume","USER:".$_SESSION['id']);} //Volume
    	if($_POST['Command']=='Mute'){GF_roomSoundControl($_POST['room_id'],"","","Mute","USER:".$_SESSION['id']);} //Mute
    	if($_POST['Command']=='Unmute'){GF_roomSoundControl($_POST['room_id'],"","","Unmute","USER:".$_SESSION['id']);} //Unmute			
    	if($_POST['Command']=='VolumeUp'){GF_roomSoundControl($_POST['room_id'],"","","VolumeUp","USER:".$_SESSION['id']);} //Volume Up
    	if($_POST['Command']=='VolumeDown'){GF_roomSoundControl($_POST['room_id'],"","","VolumeDown","USER:".$_SESSION['id']);} //Volume Down
    
    }
    //activate Scene
    if($_POST['type']=="activateScene"){
    	GF_ThenThat("<THEN>Scene:".$_POST['scene']."</THEN>");
    }
    //activate script
    if(temp_decode($_POST['type'])=="execute_script"){
    	GF_executeScript($_POST['script']);
    }
    
    
    //save card ui order
    if(temp_decode($_POST['type'])=="SaveCardUIOrder"){
    	$cardCount = 0;
    	foreach(explode(":",$_POST['order']) as $card){
    		$insert_query="UPDATE dashboard_cards SET index_order='".$cardCount."' WHERE ID='".$card."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));	
    		$cardCount++;
    	}
    }
    //Add dashborad card
    if(temp_decode($_POST["type"])=="addDashboardCard" && GetUserPermissions("add")==true){
    	$style=(int)$_POST['card_styleWidth'].":".(int)$_POST['card_styleHeight'];
    	
    	//Sensors
    	if($_POST["card_type"]=="Sensor"){
    		$attr1="";
    		$query = "SELECT ID FROM sensors ORDER BY sensor_kind ASC";
    		$sensors = mysqli_query($GS_DBCONN, $query);
    		while($sensor = mysqli_fetch_assoc($sensors)){
    			if($_POST["sensor".$sensor['ID']]!=""){
    				$attr1.=$_POST["sensor".$sensor['ID']].":";
    			}
    		}
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	//Cameras
    	if($_POST["card_type"]=="Camera"){
    		$attr1="";
    		$query = "SELECT ID FROM camera_list";
    		$cameras = mysqli_query($GS_DBCONN, $query);
    		while($camera = mysqli_fetch_assoc($cameras)){
    			if($_POST["camera".$camera['ID']]!=""){
    				$attr1.=$_POST["camera".$camera['ID']].":";
    			}
    		}
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//User
    	if($_POST["card_type"]=="User"){
    		$attr1=$_POST['user_id'];
    
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1)."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Room List
    	if($_POST["card_type"]=="Roomlist"){
    		$attr1="";
    		$query = "SELECT ID FROM home_rooms";
    		$rooms = mysqli_query($GS_DBCONN, $query);
    		while($room = mysqli_fetch_assoc($rooms)){
    			if($_POST["room".$room['ID']]!=""){
    				$attr1.=$_POST["room".$room['ID']].":";
    			}
    		}
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Group List
    	if($_POST["card_type"]=="Grouplist"){
    		$attr1="";
    		$query = "SELECT ID FROM device_groups";
    		$groups = mysqli_query($GS_DBCONN, $query);
    		while($group = mysqli_fetch_assoc($groups)){
    			if($_POST["group".$group['ID']]!=""){
    				$attr1.=$_POST["group".$group['ID']].":";
    			}
    		}
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Device
    	if($_POST["card_type"]=="Device"){
    		$attr1="";
    		$query = "SELECT ID FROM devices ";
    		$devices = mysqli_query($GS_DBCONN, $query);
    		while($device = mysqli_fetch_assoc($devices)){
    			if($_POST["device".$device['ID']]!=""){
    				$attr1.=$_POST["device".$device['ID']].":";
    			}
    		}
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Gas Level
    	if($_POST["card_type"]=="GasLevel"){
    		$attr1="";
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Weather
    	if($_POST["card_type"]=="Weather"){
    		$attr1="";
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','"."Weather"."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Scene Control
    	if($_POST["card_type"]=="Scene Control"){
    		$attr1="";
    		$query = "SELECT ID FROM scene WHERE scene_enabled='1'";
    		$scenes = mysqli_query($GS_DBCONN, $query);
    		while($scene = mysqli_fetch_assoc($scenes)){
    			if($_POST["scene".$scene['ID']]!=""){
    				$attr1.=$_POST["scene".$scene['ID']].":";
    			}
    		}
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Execute Script
    	if($_POST["card_type"]=="Execute Script"){
    		$attr1="";
    		$query = "SELECT ID FROM custom_scripts WHERE enabled='1'";
    		$scripts = mysqli_query($GS_DBCONN, $query);
    		while($script = mysqli_fetch_assoc($scripts)){
    			if($_POST["script".$script['ID']]!=""){
    				$attr1.=$_POST["script".$script['ID']].":";
    			}
    		}
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
		
		//Data Sensors
    	if($_POST["card_type"]=="Data Sensors"){
    		$attr1="";
    		$query = "SELECT ID FROM data_sensors WHERE enabled='1'";
    		$sensors = mysqli_query($GS_DBCONN, $query);
    		while($sensor = mysqli_fetch_assoc($sensors)){
    			if($_POST["dataSensor".$sensor['ID']]!=""){
    				$attr1.=$_POST["dataSensor".$sensor['ID']].":";
    			}
    		}
    		$insert_query="INSERT INTO dashboard_cards (user_id,card_name,card_type,card_style,attr1,enabled,index_order)
    						VALUES('".$_SESSION['id']."','".ucwords(strtolower($_POST['card_name']))."','".$_POST['card_type']."','".$style."','".trim($attr1,":")."','1','0')";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    		
    
    }
    
    //Edit Dashboard Card
    if(temp_decode($_POST["type"])=="editDashboardCard" && GetUserPermissions("edit")==true){
    	$style=(int)$_POST['card_styleWidth'].":".(int)$_POST['card_styleHeight'];
    	//Sensors
    	if($_POST["card_type"]=="Sensor"){
    		$attr1="";
    		$query = "SELECT ID FROM sensors ORDER BY sensor_kind ASC";
    		$sensors = mysqli_query($GS_DBCONN, $query);
    		while($sensor = mysqli_fetch_assoc($sensors)){
    			if($_POST["sensor".$sensor['ID']]!=""){
    				$attr1.=$_POST["sensor".$sensor['ID']].":";
    			}
    		}
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1,":")."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	//Cameras
    	if($_POST["card_type"]=="Camera"){
    		$attr1="";
    		$query = "SELECT ID FROM camera_list";
    		$cameras = mysqli_query($GS_DBCONN, $query);
    		while($camera = mysqli_fetch_assoc($cameras)){
    			if($_POST["camera".$camera['ID']]!=""){
    				$attr1.=$_POST["camera".$camera['ID']].":";
    			}
    		}
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1,":")."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//User
    	if($_POST["card_type"]=="User"){
    		$attr1=$_POST['user_id'];
    		
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1)."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Room List
    	if($_POST["card_type"]=="Roomlist"){
    		$attr1="";
    		$query = "SELECT ID FROM home_rooms";
    		$rooms = mysqli_query($GS_DBCONN, $query);
    		while($room = mysqli_fetch_assoc($rooms)){
    			if($_POST["room".$room['ID']]!=""){
    				$attr1.=$_POST["room".$room['ID']].":";
    			}
    		}
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1,":")."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Group List
    	if($_POST["card_type"]=="Grouplist"){
    		$attr1="";
    		$query = "SELECT ID FROM device_groups";
    		$groups = mysqli_query($GS_DBCONN, $query);
    		while($group = mysqli_fetch_assoc($groups)){
    			if($_POST["group".$group['ID']]!=""){
    				$attr1.=$_POST["group".$group['ID']].":";
    			}
    		}
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1,":")."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Device
    	if($_POST["card_type"]=="Device"){
    		$attr1="";
    		$query = "SELECT ID FROM devices ";
    		$devices = mysqli_query($GS_DBCONN, $query);
    		while($device = mysqli_fetch_assoc($devices)){
    			if($_POST["device".$device['ID']]!=""){
    				$attr1.=$_POST["device".$device['ID']].":";
    			}
    		}
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1,":")."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Gas Level
    	if($_POST["card_type"]=="GasLevel"){
    		$attr1="";
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Weather
    	if($_POST["card_type"]=="Weather"){
    		$attr1="";
    		$insert_query="UPDATE dashboard_cards SET card_name='"."Weather"."',attr1='',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Scene Control
    	if($_POST["card_type"]=="Scene Control"){
    		$attr1="";
    		$query = "SELECT ID FROM scene WHERE scene_enabled='1'";
    		$scenes = mysqli_query($GS_DBCONN, $query);
    		while($scene = mysqli_fetch_assoc($scenes)){
    			if($_POST["scene".$scene['ID']]!=""){
    				$attr1.=$_POST["scene".$scene['ID']].":";
    			}
    		}
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1,":")."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    	
    	//Execute Script
    	if($_POST["card_type"]=="Execute Script"){
    		$attr1="";
    		$query = "SELECT ID FROM custom_scripts WHERE enabled='1'";
    		$scripts = mysqli_query($GS_DBCONN, $query);
    		while($script = mysqli_fetch_assoc($scripts)){
    			if($_POST["script".$script['ID']]!=""){
    				$attr1.=$_POST["script".$script['ID']].":";
    			}
    		}
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1,":")."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
		
		//Data Sensors
    	if($_POST["card_type"]=="Data Sensors"){
    		$attr1="";
    		$query = "SELECT ID FROM data_sensors WHERE enabled='1'";
    		$sensors = mysqli_query($GS_DBCONN, $query);
    		while($sensor = mysqli_fetch_assoc($sensors)){
    			if($_POST["dataSensor".$sensor['ID']]!=""){
    				$attr1.=$_POST["dataSensor".$sensor['ID']].":";
    			}
    		}
    		$insert_query="UPDATE dashboard_cards SET card_name='".ucwords(strtolower($_POST['card_name']))."',attr1='".trim($attr1,":")."',card_style='".$style."' WHERE ID='".$_POST['ID']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    
    }
    
    //delete Dashboard Card
    if($_GET['deleteDashboardCard']!="" && GetUserPermissions("delete")==true){
    	$insert_query="DELETE FROM dashboard_cards WHERE ID='".$_GET['deleteDashboardCard']."' AND user_id='".$_SESSION['id']."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    }
    
    
    
    
    ?>
<!-------------------------------- SqueezeBox ------------------------------------------->
<?php if(GetUserPermissions("read","manage_music.php")==true) :?>	
<div class="col-md-12 stats-info" style="width:100%;margin-bottom:10px;margin-right:0px;overflow-x:auto;overflow-y:hidden;padding:8px;display:none;" id="MusicRoomShortcuts">
    <div id="RemoteMusicIndex"></div>
</div>
<?php endif; //end check permissions ?>

<ul id="container" style="list-style:none;padding:0px;">
    <?php echo getDashboardCards();?>
</ul>
<div class="clearfix"> </div>
<div style="display:none;position:fixed;margin:0 auto;left:0px;right:0px;top:0px;background-color:#fff; padding: 10px;height:100px;width:205px;z-index:99999;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2)" id="SaveCardUIOrder">
    <div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
        <h4 class="panel-title" style="text-align:center;font-size:20px;">Save Layout?</h4>
    </div>
    <form method="POST" class="autoform_na" >
        <input type="hidden" name="type" value="<?php echo temp_encode("SaveCardUIOrder");?>" />
        <input type="hidden" name="order" value="" id="DraggableCardUIIds"/>
        <button type="submit" class="btn btn-primary" style="width:90px;" onclick="$('#SaveCardUIOrder').slideUp(100);">Save</button>
        <a href="">
        <button type="button" class="btn btn-default" style="width:90px;">Cancel</button>
        </a>
    </form>
</div>
<script>
    var ids;
    $("#container").sortable({
    cursor: "move",
    cursorAt: { left: 5 },
    delay: 200,
    grid: [ 10, 10 ],
    /* handle: ".draggableCardIcon", */
    items: "> li", /* Important */
    opacity: 0.9,
    
    update: function(e, ui) {
    	ids="";
    	$("#container .draggable").each(function(i, elm) {
    		ids = ids + ":"+$(elm).find(".DraggableCardsUIIds").val();			  
    		arr =  $.unique(ids.split(':'));
    		$("#DraggableCardUIIds").val(arr.join(":").replace('undefined:', '')); 
    	});
    	$("#SaveCardUIOrder").slideDown(200);
    
    }
    });
    /* disable editting from the start */
    $( '#container').sortable('disable');
    
    function enableCardArrange(){ /* this is in header.php */
    $('#container').sortable('enable');
    $(".draggableCardIcon").animate({"width":"30px"});
    $(".draggableCardEditIcon i").animate({"width":"30px"});
    }
</script>
<!-- Card UI Modal -->
<div class="modal fade" id="CardUIModal" role="dialog" style="z-index:9999;margin-top:100px;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="padding:10px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 style="" class="modal-title">Manage Dashboard</h4>
            </div>
            <form method="post">
                <input type="hidden" name="type" id="CardEditAddType" value="<?php echo temp_encode("addDashboardCard");?>"/>
                <input type="hidden" name="ID" value="" id="EditDashboardCardID"/>
                <div class="input-group" style="margin-top:10px;">
                    <span class="input-group-addon"><b>Type</b></span>
                    <select required="" name="card_type" id="DashboardcardTypeSelection" style="width:100%;" onchange="ShowSelectedCardType();" class="form-control1">
                        <option value="">Select a Type</option>
                        <option value="Sensor">Sensor</option>
						<option value="Data Sensors">Data Sensors</option>
                        <option value="Camera">Camera</option>
                        <option value="Roomlist">Room List</option>
                        <option value="Grouplist">Group List</option>
                        <option value="Device">Device List</option>
                        <option value="Scene Control">Scene Control</option>
                        <option value="User">User Info</option>
                        <option value="Weather">Weather</option>
                        <option value="GasLevel">Gas Level</option>
                        <option value="Execute Script">Execute Script</option>
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-addon"><b>Name</b></span>
                    <input required="" name="card_name" id="DashboardCardName" value="" placeholder="Card Name" style="width:100%;" class="form-control1"/>
                </div>
                <div style="display:inline-block;width:49%;">
                    <div class="input-group">
                        <span class="input-group-addon"><b>Width</b></span>
                        <input name="card_styleWidth" id="DashboardCardStyleWidth" value="" placeholder="Card Width (Optional)" style="width:100%;" class="form-control1"/>
                    </div>
                </div>
                <div style="display:inline-block;width:49%;">
                    <div class="input-group">
                        <span class="input-group-addon"><b>Height</b></span>
                        <input name="card_styleHeight" id="DashboardCardStyleHeight" value="" placeholder="Card Height (Optional)" style="width:100%;" class="form-control1"/>
                    </div>
                </div>
                <!----- Sensors --->
                <div id="DashboardCardSensors" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Include Sensors:</b></h4>
                    <?php
                        $query = "SELECT DISTINCT sensor_kind FROM sensors ORDER BY sensor_kind ASC";
                        $sensors_types = mysqli_query($GS_DBCONN, $query);
                        while($type = mysqli_fetch_assoc($sensors_types)){
                        ?>
                    <div style="margin-left:20px;margin-bottom:5px;">
                        <h5><b><?php echo ucwords($type['sensor_kind']);?>:</b></h5>
                        <ul>
                            <?php
                                $sensor_count=0;
                                $query = "SELECT * FROM sensors WHERE sensor_kind='".$type['sensor_kind']."'";
                                $sensors = mysqli_query($GS_DBCONN, $query);
                                while($sensor = mysqli_fetch_assoc($sensors)){ $sensor_count++;
                                ?>
                            <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                                <label><?php echo $sensor['sensor_name'];?>:</label>
                                <input id="SensorCardInput<?php echo $sensor['ID'];?>" type="checkbox" name="sensor<?php echo $sensor['ID'];?>" value="<?php echo $sensor['ID'];?>" style=""/>
                            </li>
                            <?php }?>
                            <?php if ($sensor_count==0):?>
								There Are No Sensors
                            <?php endif;?>
                        </ul>
                    </div>
                    <?php }?>
                </div>
				<!----- Data Sensors --->
                <div id="DashboardCardDataSensors" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Include Data Sensors:</b></h4>
                    <?php
                        $query = "SELECT DISTINCT sensor_type FROM data_sensors ORDER BY sensor_type ASC";
                        $sensors_types = mysqli_query($GS_DBCONN, $query);
                        while($type = mysqli_fetch_assoc($sensors_types)){
                        ?>
                    <div style="margin-left:20px;margin-bottom:5px;">
                        <h5><b><?php echo ucwords($type['sensor_type']);?>:</b></h5>
                        <ul>
                            <?php
                                $sensor_count=0;
                                $query = "SELECT * FROM data_sensors WHERE enabled='1' AND sensor_type='".$type['sensor_type']."' ORDER BY sensor_nicename ASC";
                                $sensors = mysqli_query($GS_DBCONN, $query);
                                while($sensor = mysqli_fetch_assoc($sensors)){ $sensor_count++;
                                ?>
                            <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                                <label><?php echo ucwords($sensor['sensor_nicename']);?>:</label>
                                <input id="DataSensorCardInput<?php echo $sensor['ID'];?>" type="checkbox" name="dataSensor<?php echo $sensor['ID'];?>" value="<?php echo $sensor['ID'];?>" style=""/>
                            </li>
                            <?php }?>
                            <?php if ($sensor_count==0):?>
								There Are No Data Sensors
                            <?php endif;?>
                        </ul>
                    </div>
                    <?php }?>
                </div>
                <!----- Cameras --->
                <div id="DashboardCardCameras" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Include Cameras:</b></h4>
                    <ul>
                        <?php
                            $camera_count=0;
                            $query = "SELECT * FROM camera_list ORDER BY room ASC";
                            $cameras = mysqli_query($GS_DBCONN, $query);
                            while($camera = mysqli_fetch_assoc($cameras)){ 
                            	if(GetUserPermissions($camera['ID'],"manage_cameras.php")==false){continue;}else{$camera_count++;}
                            ?>
                        <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                            <label><?php echo $camera['camera_name'];?>:</label>
                            <input id="CameraCardInput<?php echo $camera['ID'];?>" type="checkbox" name="camera<?php echo $camera['ID'];?>" value="<?php echo $camera['ID'];?>" style=""/>
                        </li>
                        <?php }?>
                        <?php if ($camera_count==0):?>
							You Have No Cameras
                        <?php endif;?>
                    </ul>
                </div>
                <!----- User --->
                <div id="DashboardCardUser" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Select A User:</b></h4>
                    <select name="user_id" style="width:100%;" class="form-control1"  id="UserCardInput">
                        <?php
                            $user_count=0;
                            $query = "SELECT ID,user_name FROM users ORDER BY ID ASC";
                            $users = mysqli_query($GS_DBCONN, $query);
                            while($user = mysqli_fetch_assoc($users)){ $user_count++;?>
                        <option value="<?php echo $user['ID'];?>"><?php echo $user['user_name'];?></option>
                        <?php } ?>
                        <?php if ($user_count==0):?>
							There Are No Users
                        <?php endif;?>
                    </select>
                </div>
                <!----- Rooms --->
                <div id="DashboardCardRooms" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Include Rooms:</b></h4>
                    <ul>
                        <?php
                            $room_count=0;
                            $query = "SELECT * FROM home_rooms ORDER BY ID ASC";
                            $rooms = mysqli_query($GS_DBCONN, $query);
                            while($room = mysqli_fetch_assoc($rooms)){ 
                            	if(GetUserPermissions("read","manage_room.php")==false){continue;}else{$room_count++;}
                            ?>
                        <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                            <label><?php echo $room['room_name'];?>:</label>
                            <input id="RoomCardInput<?php echo $room['ID'];?>" type="checkbox" name="room<?php echo $room['ID'];?>" value="<?php echo $room['ID'];?>" style=""/>
                        </li>
                        <?php }?>
                        <?php if ($room_count==0):?>
							You Have No Rooms
                        <?php endif;?>
                    </ul>
                </div>
                <!----- Groups --->
                <div id="DashboardCardGroups" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Include Groups:</b></h4>
                    <ul>
                        <?php
                            $group_count=0;
                            $query = "SELECT * FROM device_groups ORDER BY ID ASC";
                            $groups = mysqli_query($GS_DBCONN, $query);
                            while($group = mysqli_fetch_assoc($groups)){ $group_count++;
                            ?>
                        <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                            <label><?php echo $group['group_name'];?>:</label>
                            <input id="GroupCardInput<?php echo $group['ID'];?>" type="checkbox" name="group<?php echo $group['ID'];?>" value="<?php echo $group['ID'];?>" style=""/>
                        </li>
                        <?php }?>
                        <?php if ($group_count==0):?>
							You Have No Groups
                        <?php endif;?>
                    </ul>
                </div>
                <!----- Devices --->
                <div id="DashboardCardDevices" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Include Devices:</b></h4>
                    <div style="margin-left:20px;margin-bottom:5px;">
                        <h5><?php echo ($room_name['room_name']!="") ? ucwords($room_name['room_name']) : 'No Room Assigned';?>:</h5>
                        <ul>
                            <?php
                                $device_count=0;
                                $query = "SELECT * FROM devices WHERE room=''";
                                $devices = mysqli_query($GS_DBCONN, $query);
                                while($device = mysqli_fetch_assoc($devices)){ $device_count++;
                                ?>
                            <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                                <label><?php echo $device['device_name'];?>:</label>
                                <input id="DeviceCardInput<?php echo $device['ID'];?>" type="checkbox" name="device<?php echo $device['ID'];?>" value="<?php echo $device['ID'];?>" style=""/>
                            </li>
                            <?php }?>
                        </ul>
                    </div>
                    <?php
                        $query = "SELECT DISTINCT room FROM devices ORDER BY room ASC";
                        $rooms = mysqli_query($GS_DBCONN, $query);
                        while($room = mysqli_fetch_assoc($rooms)){
                        	if(GetUserPermissions("read","manage_room.php")==false){continue;}
                        	//get room name
                        	$room_name = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT room_name FROM home_rooms WHERE ID='".$room['room']."'"));
                        ?>
                    <div style="margin-left:20px;margin-bottom:5px;">
                        <h5><b><?php echo ($room_name['room_name']!="") ? ucwords($room_name['room_name']) : 'No Room Assigned';?>:</b></h5>
                        <ul>
                            <?php
                                $device_count=0;
                                $query = "SELECT * FROM devices WHERE room='".$room['room']."'";
                                $devices = mysqli_query($GS_DBCONN, $query);
                                while($device = mysqli_fetch_assoc($devices)){ $device_count++;
                                ?>
                            <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                                <label><?php echo $device['device_name'];?>:</label>
                                <input id="DeviceCardInput<?php echo $device['ID'];?>" type="checkbox" name="device<?php echo $device['ID'];?>" value="<?php echo $device['ID'];?>" style=""/>
                            </li>
                            <?php }?>
                            <?php if ($device_count==0):?>
								You Have No Devices In This Room
                            <?php endif;?>
                        </ul>
                    </div>
                    <?php }?>
                </div>
                <!----- Scenes --->
                <div id="DashboardCardScene" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Include Scenes:</b></h4>
                    <ul>
                        <?php
                            $scene_count=0;
                            $query = "SELECT * FROM scene WHERE scene_enabled='1'";
                            $scenes = mysqli_query($GS_DBCONN, $query);
                            while($scene = mysqli_fetch_assoc($scenes)){ 
                            //check permissions
                            if(GetUserPermissions("read","manage_scene.php")==false){continue;}else{$scene_count++;}
                            ?>
                        <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                            <label><?php echo $scene['scene_name'];?>:</label>
                            <input id="SceneCardInput<?php echo $scene['ID'];?>" type="checkbox" name="scene<?php echo $scene['ID'];?>" value="<?php echo $scene['ID'];?>" style=""/>
                        </li>
                        <?php }?>
                        <?php if ($scene_count==0):?>
							You Have No Scenes
                        <?php endif;?>
                    </ul>
                </div>
                <!----- Execute Script --->
                <div id="DashboardCardScript" style="display:none;overflow-y:auto;max-height:300px;">
                    <h4 style="margin-top:5px;"><b>Execute a Script:</b></h4>
                    <ul>
                        <?php
                            $script_count=0;
                            $query = "SELECT * FROM custom_scripts WHERE enabled='1'";
                            $scripts = mysqli_query($GS_DBCONN, $query);
                            while($script= mysqli_fetch_assoc($scripts)){ $script_count++;
                            ?>
                        <li style="display:inline-block;border:1px solid #f1f1f1; margin-right:2px;padding:5px;">
                            <label><?php echo $script['script_name'];?>:</label>
                            <input id="ScriptCardInput<?php echo $script['ID'];?>" type="checkbox" name="script<?php echo $script['ID'];?>" value="<?php echo $script['ID'];?>" style=""/>
                        </li>
                        <?php }?>
                        <?php if ($script_count==0):?>
							You Have No Custom Scripts
                        <?php endif;?>
                    </ul>
                </div>
                <script>
                    function hideAllCardLists(){
                    	$("#DashboardCardSensors").slideUp(100);
						$("#DashboardCardDataSensors").slideUp(100);
                    	$("#DashboardCardCameras").slideUp(100);
                    	$("#DashboardCardRooms").slideUp(100);
                    	$("#DashboardCardGroups").slideUp(100);
                    	$("#DashboardCardDevices").slideUp(100);
                    	$("#DashboardCardUser").slideUp(100);
                    	$("#DashboardCardScene").slideUp(100);
                    	$("#DashboardCardScript").slideUp(100);
                    }
                    
                    function ShowSelectedCardType(){
                    	hideAllCardLists();
                    	
                    	if($("#DashboardcardTypeSelection").val()=="Sensor"){
                    		$("#DashboardCardSensors").slideDown(100);
						}else if($("#DashboardcardTypeSelection").val()=="Data Sensors"){
                    		$("#DashboardCardDataSensors").slideDown(100);
                    	}else if($("#DashboardcardTypeSelection").val()=="Camera"){
                    		$("#DashboardCardCameras").slideDown(100);
                    	}else if($("#DashboardcardTypeSelection").val()=="Roomlist"){
                    		$("#DashboardCardRooms").slideDown(100);
                    	}else if($("#DashboardcardTypeSelection").val()=="Grouplist"){
                    		$("#DashboardCardGroups").slideDown(100);
                    	}else if($("#DashboardcardTypeSelection").val()=="Device"){
                    		$("#DashboardCardDevices").slideDown(100);
                    	}else if($("#DashboardcardTypeSelection").val()=="User"){
                    		$("#DashboardCardUser").slideDown(100);
                    	}else if($("#DashboardcardTypeSelection").val()=="System Info"){
                    		/* this has no options */
                    	}else if ($("#DashboardcardTypeSelection").val()=="Scene Control"){
                    		$("#DashboardCardScene").slideDown(100);
                    	}else if ($("#DashboardcardTypeSelection").val()=="Execute Script"){
                    		$("#DashboardCardScript").slideDown(100);
                    	}
                    }
                    
                    function addNewDashboardCard(){
                    	hideAllCardLists();
                    	$("#DashboardCardName").val("");
                    	$("#DashboardcardTypeSelection").val("");
                    	$("#deleteDashboardCardBtn").hide();
                    	$("#CardEditAddType").val("<?php echo temp_encode("addDashboardCard");?>");
                    	$("#DashboardcardTypeSelection").attr("disabled",false);
                    }
                    
                    function editSensorCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$('#DashboardCardSensors input:checkbox').removeAttr('checked');
                    	$("#DashboardCardSensors").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Sensor");
                    	$("#DashboardcardTypeSelection").attr("readOnly",true);
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    	var attr1 = attr1.split(":");
                    	attr1.forEach(function(item) {
                    		$("#SensorCardInput" + item).prop("checked",true);
                    	});
                    }
					function editDataSensorCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$('#DashboardCardDataSensors input:checkbox').removeAttr('checked');
                    	$("#DashboardCardDataSensors").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Data Sensors");
                    	$("#DashboardcardTypeSelection").attr("readOnly",true);
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    	var attr1 = attr1.split(":");
                    	attr1.forEach(function(item) {
                    		$("#DataSensorCardInput" + item).prop("checked",true);
                    	});
                    }
                    function editCameraCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$('#DashboardCardCameras input:checkbox').removeAttr('checked');
                    	$("#DashboardCardCameras").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Camera");
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    	var attr1 = attr1.split(":");
                    	attr1.forEach(function(item) {
                    		$("#CameraCardInput" + item).prop("checked",true);
                    	});
                    }
                    function editDeviceCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$('#DashboardCardDevices input:checkbox').removeAttr('checked');
                    	$("#DashboardCardDevices").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Device");
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    	var attr1 = attr1.split(":");
                    	attr1.forEach(function(item) {
                    		$("#DeviceCardInput" + item).prop("checked",true);
                    	});
                    }
                    function editGroupCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$('#DashboardCardGroups input:checkbox').removeAttr('checked');
                    	$("#DashboardCardGroups").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Grouplist");
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    	var attr1 = attr1.split(":");
                    	attr1.forEach(function(item) {
                    		$("#GroupCardInput" + item).prop("checked",true);
                    	});
                    }
                    function editRoomCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$('#DashboardCardRooms input:checkbox').removeAttr('checked');
                    	$("#DashboardCardRooms").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Roomlist");
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    	var attr1 = attr1.split(":");
                    	
                    	attr1.forEach(function(item) {
                    		$("#RoomCardInput" + item).prop("checked",true);
                    	});
                    }
                    function editUserCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$("#DashboardCardUser").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("User");
                    	$("#UserCardInput").val(attr1);
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    }

                    function editGasLevel(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("GasLevel");
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    }
                    
                    function editWeatherCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Weather");
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    }
                    
                    function editSceneCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$("#DashboardCardScene").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Scene Control");
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    	var attr1 = attr1.split(":");
                    	attr1.forEach(function(item) {
                    		$("#SceneCardInput" + item).prop("checked",true);
                    	});
                    }
                    
                    function editScriptCard(name,attr1,style,ID){
                    	hideAllCardLists();
                    	$("#deleteDashboardCardBtn").attr("href","?deleteDashboardCard="+ID)
                    	$("#deleteDashboardCardBtn").show();
                    	$("#CardEditAddType").val("<?php echo temp_encode("editDashboardCard");?>");
                    	$("#EditDashboardCardID").val(ID);
                    	$("#DashboardCardScript").slideDown(100);
                    	$("#DashboardCardName").val(name);
                    	$("#DashboardcardTypeSelection").val("Execute Script");
                    	$("#DashboardCardStyleWidth").val(style.split(":")[0]);
                    	$("#DashboardCardStyleHeight").val(style.split(":")[1]);
                    	var attr1 = attr1.split(":");
                    	attr1.forEach(function(item) {
                    		$("#ScriptCardInput" + item).prop("checked",true);
                    	});
                    }
                    				
                </script>	
                <div style="text-align:right;margin-right:20px;margin-top:20px;">
                    <hr/>
                    <?php if(GetUserPermissions("delete")==true):?>
                    <a href="#" id="deleteDashboardCardBtn">
                    <button class="btn btn-danger" type="button" style="float:left;" onclick="return confirm('Are You Sure You Want To Delete This Card?');"><i class="fa fa-trash-o"></i> Delete</button>
                    </a>
                    <?php else:?>
                    <button class="btn btn-danger" type="button" style="float:left;" disabled><i class="fa fa-trash-o"></i> Delete</button>
                    <?php endif;?>
                    <button class="btn btn-primary" type="submit">Save</button>
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include("includes/footer.php");?>
<?php include("includes/modals.php");?>
</body>
</html>