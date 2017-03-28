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
    	include("Autoload/Autoload_TempSensors.php");
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
    $page_title="Manage Rooms";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    	
    //check if room exists 
    $query = "SELECT * FROM home_rooms WHERE ID='".clean_text(temp_decode($_GET['room_id']),11)."'";
    $rooms = mysqli_query($GS_DBCONN, $query);
    $room_page = mysqli_fetch_assoc($rooms);
    $room_page_count = mysqli_num_rows($rooms);
	if(empty($_POST)){
		if($room_page_count==0 && $_GET['add']!='1'){echo "<script>location.href='no_access.php';</script>";exit;}
	}
    
    //check permissions
    if(GetUserPermissions($room_page['ID'])==true || $_GET['add']=='1'){
    	//access granted
    }elseif($room_page['guest_access']=='1' && $permis_result['type']=="guest"){
    	//access granted
    }else{
    	echo "<script>location.href='no_access.php';</script>";
    }
    
    	
	//update room settings
	if(temp_decode($_POST['type'])=='room_settings' && GetUserPermissions("edit")==true){
		$autoWakeUpTime= $_POST['awu_monday']." ".$_POST['awu_tuesday']." ".$_POST['awu_wednesday']." ".$_POST['awu_thursday']." ".$_POST['awu_friday']." ".$_POST['awu_saturday']." ".$_POST['awu_sunday'];
		
		$insert_query="UPDATE home_rooms SET 
		room_name='".clean_text(ucwords($_POST['room_name']),50)."',
		room_icon='".clean_text($_POST['room_icon'],25)."',
		guest_access='".(int)clean_text($_POST['guest_access'],1)."',
		timeout_enable='".(int)clean_text($_POST['timeout_enable'],1)."',
		timeout='".clean_text($_POST['timeout'],50)."',
		autoWakeUpTime='".clean_text(((int)$_POST['awu_enabled'])."|".date("H:i",strtotime($_POST['awu_time']))."|".$autoWakeUpTime,50)."'
		WHERE ID='".clean_text($room_page['ID'],11)."'";
		mysqli_query($GS_DBCONN, $insert_query);
		
		echo "<script>location.href='';</script>";
	}
	
	//delete room
	if(temp_decode($_GET['delete'])=="1" && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		$insert_query="DELETE FROM home_rooms WHERE ID='".clean_text($room_page['ID'],11)."'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
		echo "<script>location.href='index.php';</script>";
	}

	//add new room
	if(temp_decode($_POST['type'])=="add_new_room" && trim($_POST['room_name'])!="" && GetUserPermissions("add")==true){
		$insert_query="INSERT INTO home_rooms (room_name,guest_access,timeout,timeout_enable)VALUES
		('".clean_text(ucwords($_POST['room_name']),100)."',
		'".clean_text((int)$_POST['guest_access'],1)."',
		'".clean_text($_POST['timeout_time'],3)."',
		'".clean_text((int)$_POST['enable_timeout'],1)."')";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
		
		if(strpos($permis_result['user_permissions'], (":manage_users.php:")) !== false){
			echo "<script>location.href='manage_users.php';</script>";
		}else{
			echo "<script>location.href='index.php';</script>";
		}
	}
				
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
	
	//update auto-off
	if(GetUserPermissions("edit")==true) {
		if(temp_decode($_POST['type'])=="update_autooff" && GetUserPermissions("edit")==true){
			$insert_query="UPDATE devices SET enable_auto_off='".clean_text((int)$_POST['auto_off'],1)."',last_on_time='".($_POST['auto_off'] == '1' ? time() : '')."' WHERE ID='".clean_text($_POST['device'],11)."'";
			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
		}
	}
	
	if(GetUserPermissions("edit")==true) {
		//quick change sensor enabled disabled
		if(temp_decode($_POST['type'])=='enableSensor'){ //Enable
			$insert_query="UPDATE sensors SET enabled='1',last_changed_by='".clean_text($_SESSION['id'],11)."' WHERE ID='".clean_text($_POST['sensorID'],11)."'";
			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));		
		}
		if(temp_decode($_POST['type'])=='disableSensor'){ //Disable
			$insert_query="UPDATE sensors SET enabled='0',last_changed_by='".clean_text($_SESSION['id'],11)."' WHERE ID='".clean_text($_POST['sensorID'],11)."'";
			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));		
		}
	} 
	
	//toggle group on
    if(temp_decode($_POST['type'])=="toggle_group_on"){
    	//send to all rooms since there is not specific room set
    	$query    = "SELECT ID FROM devices WHERE group_id='".clean_text($_POST['group_id'],11)."'";
    	GF_transmitToDevice($query, "1", "100", "noColor", "noEffect", "USER:".$_SESSION['id']);    
    }
    //toggle group off
    if(temp_decode($_POST['type'])=="toggle_group_off"){
    	//send to all rooms since there is not specific room set
    	$query    = "SELECT ID FROM devices WHERE group_id='".clean_text($_POST['group_id'],11)."'";
    	GF_transmitToDevice($query, "1", "100", "noColor", "noEffect", "USER:".$_SESSION['id']);    
    }
	
	//Change Device Brightness
    if(temp_decode($_POST['type'])=="ChangeDeviceBrightness"){	
		$query    = "SELECT * FROM devices WHERE ID='".clean_text($_POST['device'],11)."' LIMIT 1";
		GF_transmitToDevice($query, "1", clean_text($_POST['brightness'],5), "noColor", "noEffect", "USER:".$_SESSION['id']);
    }		
    
    	
    ?>
	<?php if ($_GET['add']!="1") :?>
		<div class="" style="margin-bottom:30px;">
			<h3>
				<?php echo ucwords($room_page['room_name']);?>&nbsp;&nbsp;&nbsp; 
				<?php if(GetUserPermissions("edit")==true):?>
					<button class="btn btn-default" data-toggle="modal" data-target="#room_settings" title="You do not have permission"><i class="fa fa-pencil"></i></button>
				<?php endif;?>
				<!--<div style="font-size:16px;border-radius:100px;background-color:#EF553A;width:auto;height:auto;padding:10px;color:#fff;float:right;"><i class="fa fa-power-off"></i></div>-->
				<div style="margin-top:-5px;">
					<span style="font-size:12px;font-family:Arial;">Last Active: <?php echo ago(date("Y-m-d H:i:s",$room_page['last_active']));?></span>
				</div>
			</h3>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<ul id="container" style="width:100%;list-style:none;padding:0px;display:inline-block;">
					
					<!----------------------------------- Sensor List ----------------------------------->
					<?php 
						$sensor_stringArray = "";
						$query = "SELECT * FROM sensors WHERE room='".$room_page['ID']."' ";
						$sensors = mysqli_query($GS_DBCONN, $query);
						$sensorCount = mysqli_num_rows($sensors);
						while($sensor = mysqli_fetch_assoc($sensors)) {
							$sensor_stringArray.=$sensor['ID'].":";
						}
						$sensorStyleArray = array();
						$sensorStyleArray['card_style']="0:280";
						$sensorStyleArray['ID']="1";
						if($sensorCount>0){
							SensorCard("","Sensors",trim($sensor_stringArray,":"),$sensorStyleArray);
						}
					?>
					<!----------------------------------- Data Sensor List ----------------------------------->
					<?php 
						$Datasensor_stringArray = "";
						$query = "SELECT * FROM data_sensors WHERE room='".$room_page['ID']."' AND enabled='1'";
						$sensors = mysqli_query($GS_DBCONN, $query);
						$DataSensorCount = mysqli_num_rows($sensors);
						while($sensor = mysqli_fetch_assoc($sensors)) {
							$DataSensor_stringArray.=$sensor['ID'].":";
						}
						$DataSensorStyleArray = array();
						$DataSensorStyleArray['card_style']="0:280";
						$DataSensorStyleArray['ID']="1";
						if($DataSensorCount>0){
							DataSensor("","Data Sensors",trim($DataSensor_stringArray,":"),$DatsSensorStyleArray);
						}
					?>
					<!----------------------------------- Group List ----------------------------------->
					<?php 
						$group_stringArray = "";
						$query = "SELECT * FROM device_groups ORDER BY group_name ASC LIMIT 8 ";
						$groups = mysqli_query($GS_DBCONN, $query);
						$groupCount = mysqli_num_rows($groups);
						while($group = mysqli_fetch_assoc($groups)) {
							$device_count = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM devices WHERE room='".$room_page['ID']."' AND group_id='".$group['ID']."' ORDER BY ID ASC LIMIT 1"));
							if($device_count==0){continue;}
							$group_stringArray.=$group['ID'].":";
						}
						$GroupStyleArray = array();
						$GroupStyleArray['card_style']="0:280";
						$GroupStyleArray['ID']="2";
						if($groupCount>0){	
							GroupList("","Groups",trim($group_stringArray,":"),$GroupStyleArray);
						}
					?>
					<!----------------------------------- Device Card ----------------------------------->
					<?php 
						$device_stringArray = "";
						$query = "SELECT * FROM devices WHERE room='".$room_page['ID']."' ORDER BY ID ASC LIMIT 8 ";
						$devices = mysqli_query($GS_DBCONN, $query);
						$deviceCount = mysqli_num_rows($devices);
						while($device = mysqli_fetch_assoc($devices)) { 
							$device_stringArray.=$device['ID'].":";
						}
						$DeviceStyleArray = array();
						$DeviceStyleArray['card_style']="0:280";
						$DeviceStyleArray['ID']="3";
						if($deviceCount>0){
							DeviceCard("","Devices",trim($device_stringArray,":"),$DeviceStyleArray);
						}
					?>
					<!----------------------------------- Weather ----------------------------------->
					<?php
						$WeatherStyleArray = array();
						$WeatherStyleArray['card_style']="0:280";
						WeatherCard("",$WeatherStyleArray);
					?>
					<!----------------------------------- Cameras ----------------------------------->
					<?php 
						$featured_cameras_count=0;
						$query = "SELECT * FROM camera_list WHERE room='".$room_page['ID']."' ORDER BY ID ASC LIMIT 5";
						$cameras = mysqli_query($GS_DBCONN, $query);
						while($camera = mysqli_fetch_assoc($cameras)) { $featured_cameras_count++;
							CameraCard("",$camera['camera_name'],$camera['ID'], $data="");
						}
					?>
					
				</ul>
			</div>
			<!--########################################### Room Settings Modal #################################### -->
			<!-- Modal -->
			<div class="modal fade" id="room_settings" role="dialog" style="z-index:99999;margin-top:150px;">
				<div class="modal-dialog" style="width:400px;">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 style="" class="modal-title">Room Settings</h4>
						</div>
						<div class="modal-body" style="overflow:auto;">
							<!--send command to find sensors in settings table -->
							<form method="POST" class="autoform_na">
								<input type="hidden" name="type" value="<?php echo temp_encode("room_settings");?>" />
								<div style="">
									<h4 style="text-align:center;">General</h4>
									<div class="input-group">
										<span class="input-group-addon"><b>Room Name</b></span>
										<input type="text" name="room_name" value="<?php echo ucfirst($room_page['room_name']);?>" class="form-control1"/>
									</div>
									<div class="input-group">
										<span class="input-group-addon"><b>Icon</b></span>
										<input type="text" name="room_icon" value="<?php echo $room_page['room_icon'];?>" class="form-control1"/>
									</div>
									<input type="checkbox" name="timeout_enable" value="1" id="timeout_enable" />
									Turn Off Devices After:&nbsp;
									<input type="number" name="timeout" min="0" max="9999" value="<?php echo $room_page['timeout'];?>" style="height:30px;width:50px;" class="form-control1"/> &nbsp; Minute(s)
									<br/>
									<input type="checkbox" name="guest_access" value="1" id="guest_access"/>
									Allow Guest To Access This Room
								</div>
								<hr/>
								<div style="margin-top:20px;margin-bottom:20px;">
									<h4 style="text-align:center;padding-left:45px;height:30px;">
										Auto WakeUp 
										<div style="float:right;font-size:16px;">
											<input type="checkbox" data-toggle="toggle" data-size="small" name="awu_enabled" value="1" id="autoWakeUpEnabled"/>
										</div>
									</h4>
									<p style="text-align:center;">Slowly turns up the lights over a period of 30 minutes</p>
									<input type="time" value="<?php echo explode("|",$room_page['autoWakeUpTime'])[1];?>" name="awu_time" class="form-control1" style="width:100%;height:30px;margin-bottom:10px;"/>
									<ul style="list-style:none;padding:0px;">
										<li style="display:inline-block;"><input type="checkbox" name="awu_monday" value="mon" id="autoWakeUpMonday"/> Monday</li>
										<li style="display:inline-block;"><input type="checkbox" name="awu_tuesday" value="tue" id="autoWakeUpTuesday"/> Tuesday</li>
										<li style="display:inline-block;"><input type="checkbox" name="awu_wednesday" value="wed" id="autoWakeUpWednesday"/> Wednesday</li>
										<li style="display:inline-block;"><input type="checkbox" name="awu_thursday" value="thu" id="autoWakeUpThursday"/> Thursday</li>
										<li style="display:inline-block;"><input type="checkbox" name="awu_friday" value="fri" id="autoWakeUpFriday"/> Friday&nbsp;&nbsp;</li>
										<li style="display:inline-block;"><input type="checkbox" name="awu_saturday" value="sat" id="autoWakeUpSaturday"/> Saturday</li>
										<li style="display:inline-block;"><input type="checkbox" name="awu_sunday" value="sun" id="autoWakeUpSunday"/> Sunday</li>
									</ul>
								</div>
								<script>
									<?php if ($room_page['timeout_enable']=='1') :?>
										$('#timeout_enable').prop("checked", "true");
									<?php endif;?>
									<?php if ($room_page['guest_access']=='1') :?>
										$('#guest_access').prop("checked", "true");
									<?php endif;?>
									
									/*format: enabled|time|days*/
									
									<?php $awu_days = explode("|",$room_page['autoWakeUpTime'])[2];?>
									<?php if ( explode("|",$room_page['autoWakeUpTime'])[0]=="1"):?>
										$("#autoWakeUpEnabled").prop("checked",true);
									<?php endif;?>
									
									<?php if(strpos($awu_days,"mon")!==false):?>
										$("#autoWakeUpMonday").prop("checked",true);
									<?php endif;?>
									<?php if(strpos($awu_days,"tue")!==false):?>
										$("#autoWakeUpTuesday").prop("checked",true);
									<?php endif;?>
									<?php if(strpos($awu_days,"wed")!==false):?>
										$("#autoWakeUpWednesday").prop("checked",true);
									<?php endif;?>
									<?php if(strpos($awu_days,"thu")!==false):?>
										$("#autoWakeUpThursday").prop("checked",true);
									<?php endif;?>
									<?php if(strpos($awu_days,"fri")!==false):?>
										$("#autoWakeUpFriday").prop("checked",true);
									<?php endif;?>
									<?php if(strpos($awu_days,"sat")!==false):?>
										$("#autoWakeUpSaturday").prop("checked",true);
									<?php endif;?>
									<?php if(strpos($awu_days,"sun")!==false):?>
										$("#autoWakeUpSunday").prop("checked",true);
									<?php endif;?>
								</script>
								<div style="margin-top:10px;width:100%;">
									<?php if (GetUserPermissions("delete")==true):?>
										<a href="?room_id=<?php echo temp_encode($room_page['ID']);?>&delete=<?php echo temp_encode("1");?>" onclick="return confirm('Are You Sure You Want To Delete This Room?');" style="float:left;">
											<button class="btn btn-danger" type="button" >
												<i class="fa fa-trash-o"></i>
												Delete
											</button>
										</a>
									<?php else:?>
										<a href="#" style="float:left;">
											<button class="btn btn-danger" type="button" disabled>
												<i class="fa fa-trash-o"></i>
												Delete
											</button>
										</a>
									<?php endif;?>
									<div style="float:right;">
										<?php if(GetUserPermissions("edit")==true):?>
											<input type="submit" class="btn btn-primary" value="Save" />
										<?php else :?>
											<input type="button" disabled class="btn btn-primary" value="Save" />
										<?php endif;?>
										<input type="button" class="btn btn-default" value="Cancel" data-dismiss="modal"/>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<!--###################################################################################################### -->
    <?php else:?>
		<!--############################################## Add New Room Modal #################################### -->
		<center>
			<form method="Post" style="padding:10px;margin-bottom:20px;text-align:left;min-width:300px;" class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
				<div class="modal-content">
					<div class="modal-header">
						<h4 style="" class="modal-title">Add a New Room</h4>
					</div>
					<div class="modal-body" style="height:240px;overflow:auto;">
						<input type="hidden" name="type" value="<?php echo temp_encode("add_new_room");?>"/>
						<div style="margin-bottom:10px;">
							<label><b>Room Name:</b></label><br/>
							<input type="text" name="room_name" value="" class="form-control1" style="width:100%;height:30px;"/>
						</div>
						<div style="margin-bottom:10px;">
							<label><b>Enable Device Timeout:</b></label>
							<input type="checkbox" name="enable_timeout" value="1" checked />
							<label><b>After:</b>&nbsp;</label>
							<input type="number" min="0" max="999" name="timeout_time" value="15" style="width:18%;height:30px;" class="form-control1"/>
							<label>&nbsp;<b>Minutes</b></label>
						</div>
						<div style="margin-bottom:10px;">
							<label><b>Enable Guest Access:</b></label>
							<input type="checkbox" name="guest_access" value="1" style="margin-left:8px;"/>
						</div>
						<p style="color:red;font-size:12px;font-family:Arial;"><b>Tip:</b><br/>
							<?php if($_SESSION['type']=="Admin" || strpos($permis_result['user_permissions'], (":manage_users.php:")) !== false): ?>
							After adding this room, you may need to visit the Manage Users page to assign users to it.
							<?php else :?>
							After adding this room, you may need to contact your administrator to assign users to it.
							<?php endif;?>
						</p>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Add</button>
					</div>
				</div>
			</form>
		</center>
		<!--###################################################################################################### -->
    <?php endif; /* If $_GET[add] = 1 */ ?>					
    <div class="clearfix"> </div>
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
</body>
</html>