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
    	
    	include("Autoload/AutoLoad_DataStates.php");
    	include("Autoload/AutoLoad_SHAlerts.php");
    	include("Autoload/Autoload_checkTempEncode.php");
    	exit;	
    }
    ################################################
    
    #################### API #######################
    $page_title="Manage Scenes";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    	
    //update auto-off
    if(temp_decode($_POST['type'])=='update_autooff'){
    	$insert_query="UPDATE devices SET enable_auto_off='".clean_text((int)$_POST['auto_off'],1)."' WHERE ID='".clean_text($_POST['device'],11)."'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    }
    
    
    //get scene data from table
    $scene_id= clean_text($_GET['scene_id'],11);
    if($scene_id!=''){
    	$query = "SELECT * FROM scene WHERE ID='$scene_id'";
    	$scene1 = mysqli_query($GS_DBCONN, $query);
    	$scene_page = mysqli_fetch_assoc($scene1);
    	$scene_id = $scene_page['ID'];
    	if($scene_id==''){echo"<script>location.href='manage_scene.php';</script>";exit;}
    }else{
    	$query = "SELECT * FROM scene ORDER BY ID DESC LIMIT 1";
    	$scene1 = mysqli_query($GS_DBCONN, $query);
    	$scene_page = mysqli_fetch_assoc($scene1);
    	$scene_id = $scene_page['ID']+1;
    }
    
    //check permissions
    if(GetUserPermissions($scene_page['ID'])==true || $_GET['scene_id']==""){}else{echo "<script>location.href='/no_access.php';</script>";}
    
    	//update scene
    	if(temp_decode($_POST['type'])=='update_scene' && GetUserPermissions("edit")==true){
    		
    		if(clean_text($_GET['scene_id'],11)==''){
    			//insert new row in scenes table
				if(clean_text($_POST['scene_icon'],25)!=""){$icon =clean_text($_POST['scene_icon'],25);}else{$icon ="fa-power-off";}
    			$insert_query="INSERT INTO scene (ID,scene_name,scene_icon,scene_enabled)VALUES('".clean_text($scene_id,11)."','".clean_text($_POST['scene_name'],200)."','".$icon."','1')";
    			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    		}
    		
    		//clear table where scene == scene
    		$insert_query="DELETE FROM scene_events WHERE scene_id='".clean_text($scene_id,11) ."' AND event_name LIKE 'Device%'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    			
    		$device_count=0;
    		$ifttt_count=0;
    		
    		$query = "SELECT * FROM devices";
    		$devices0 = mysqli_query($GS_DBCONN, $query);
    		$devices_count = mysqli_num_rows($devices0) +1;
    		
    		$query = "SELECT * FROM ifttt_listings";
    		$ifttt0 = mysqli_query($GS_DBCONN, $query);
    		$ifttts_count = mysqli_num_rows($ifttt0)+1;
    		//devices
    		while($device_count<$devices_count){
    			if($_POST['state'.$device_count]!=''){
    				$insert_query="INSERT INTO scene_events (event_name,event_title,scene_id) 
    				VALUES(
    				'".clean_text($_POST['state'.$device_count],500)."',
    				'".clean_text($_POST['state'.$device_count],500)."',
    				'".clean_text($scene_id,11)."')";
    				mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    			}
    			$device_count++;	
    		}
    		//iftt then
    		while($ifttt_count<$ifttts_count){
    			if($_POST['thenchk'.$ifttt_count]!=''){
    				$insert_query="INSERT INTO scene_events (event_name,event_title,scene_id) 
    				VALUES(
    				'".clean_text($_POST['thenchk'.$ifttt_count],1)."',
    				'".clean_text($_POST['thenchk'.$ifttt_count],1)."',
    				'".clean_text($scene_id,11)."')";
    				mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    			}
    			$ifttt_count++;	
    		}
    
    		if(strpos($permis_result['user_permissions'], (":manage_users.php:")) !== false){
    			echo "<script>location.href='manage_users.php';</script>";
    		}else{
    			echo "<script>location.href='index.php';</script>";
    		}
    		
    	}
    	
    	//delete
    	if(temp_decode($_GET['delete'])!='' && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
    		//remove data from both tables
    		$insert_query="DELETE FROM scene WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    		
    		$insert_query="DELETE FROM scene_events WHERE scene_id='".clean_text(temp_decode($_GET['delete']),11)."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    		
    		echo "<script>location.href='index.php';</script>";
    	}
    
    
    	//enable disable sensor
    	if(temp_decode($_POST['type'])=="EnableDisable_Sensors" && GetUserPermissions("edit")==true){
    		$query = "SELECT * FROM sensors ORDER By ID ASC";
    		$EnableDisable_Sensors_list = mysqli_query($GS_DBCONN, $query);
    
    		//delete current items instead of updating
    		$insert_query="DELETE FROM scene_events WHERE 
    		(
    		event_name LIKE 'Enable Sensor:%' OR event_name LIKE 'Disable Sensor:%' OR event_name LIKE 'Ignore Sensor:%'				
    		) && scene_id='".clean_text($scene_id,11)."' ";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    
    		while($EnableDisable_Sensors= mysqli_fetch_assoc($EnableDisable_Sensors_list)){
    			if($_POST['state_'.$EnableDisable_Sensors['ID']]!=''){
    				$insert_query="INSERT INTO scene_events (event_name,event_title,scene_id) 
    				VALUES(
    				'".clean_text($_POST['state_'.$EnableDisable_Sensors['ID']],500)."',
    				'".clean_text($_POST['state_'.$EnableDisable_Sensors['ID']],500)."',
    				'".clean_text($scene_id,11)."')";
    				mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    			}	
    		}
    	}
    	
    	
    	//enable disable camera
    	if(temp_decode($_POST['type'])=="EnableDisable_cameras" && GetUserPermissions("edit")==true){
    		$query = "SELECT * FROM camera_list ORDER By ID ASC";
    		$EnableDisable_cameras_list = mysqli_query($GS_DBCONN, $query);
    
    		//delete current items instead of updating
    		$insert_query="DELETE FROM scene_events WHERE 
    		(
    			event_name LIKE 'Enable Camera:%' OR event_name LIKE 'Disable Camera:%' OR event_name LIKE 'Ignore Camera:%'
    		) && scene_id='".$scene_id."' ";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    
    		while($EnableDisable_cameras= mysqli_fetch_assoc($EnableDisable_cameras_list)){
    			if($_POST['state_'.$EnableDisable_cameras['ID']]!=''){
    				$insert_query="INSERT INTO scene_events (event_name,event_title,scene_id) 
    				VALUES(
    				'".clean_text($_POST['state_'.$EnableDisable_cameras['ID']],500)."',
    				'".clean_text($_POST['state_'.$EnableDisable_cameras['ID']],500)."',
    				'".clean_text($scene_id,11)."')";
    				mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    			}	
    		}
    	}
    	
    	
    	//Room Music Control
    	if(temp_decode($_POST['type'])=="RoomMusicControl" && GetUserPermissions("edit")==true){
    		$query = "SELECT * FROM home_rooms ORDER By ID ASC";
    		$homeRoomsMusic_list = mysqli_query($GS_DBCONN, $query);
    
    		//delete current items instead of updating
    		$insert_query="DELETE FROM scene_events WHERE 
    		(event_name LIKE 'PLAYSONG|%' OR event_name LIKE 'PLAYSONG|%') && scene_id='".$scene_id."' ";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    
    		while($roomMusicControl= mysqli_fetch_assoc($homeRoomsMusic_list)){
    			if($_POST['MusicControlRoomValue_'.$roomMusicControl['ID']]==''){
    				$text="PLAYSONG|".$roomMusicControl['ID']."|||Ignore";
    			}else{
    				$text = $_POST['MusicControlRoomValue_'.$roomMusicControl['ID']];
    			}
    			
    			$insert_query="INSERT INTO scene_events (event_name,event_title,scene_id) 
    			VALUES(
    			'".clean_text($text,500)."',
    			'".clean_text($text,500)."',
    			'".clean_text($scene_id,11)."')";
    			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    		}	
    	}
    	
    	
    	//Execute Scripts
    	if(temp_decode($_POST['type'])=="ExecuteScripts" && GetUserPermissions("edit")==true){
    		
    		//delete current items instead of updating
    		$insert_query="DELETE FROM scene_events WHERE 
    		(event_name LIKE 'Script:%' OR event_name LIKE 'Script:%') && scene_id='".$scene_id."' ";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    		
    		$query = "SELECT * FROM custom_scripts ORDER By ID ASC";
    		$script_list = mysqli_query($GS_DBCONN, $query);
    		while($script= mysqli_fetch_assoc($script_list)){
    			if($_POST['state_'.$script['ID']]!=''){
    				$text = $_POST['state_'.$script['ID']];
    				$insert_query="INSERT INTO scene_events (event_name,event_title,scene_id) 
    				VALUES(
    				'".clean_text($text,500)."',
    				'".clean_text($text,500)."',
    				'".clean_text($scene_id,11)."')";
    				mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    			}
    		}	
    	}
    	
    
    	
    ?>
<div>
    <form method="POST">
        <h3>
            <?php if ($_GET['scene_id']!=""):?>
            <?php echo ucfirst($scene_page['scene_name']);?>
            <?php else :?>
            <input type="text" name="scene_name" value="" placeholder="Scene Name" class="form-control1" style="width:540px;"/>
			<input type="text" name="scene_icon" value="" placeholder="Icon" class="form-control1" style="width:200px;"/>
            <p style="font-family:Arial;margin-left:5px;margin-top:5px;color:red;font-size:12px;">
                <b>Note:</b> You Will Need To Add Permissions To This Scene After Adding it.
            </p>
            <?php endif;?>
            <?php if(GetUserPermissions("delete")==true):?>
				<a href="manage_scene.php?delete=<?php echo temp_encode($scene_id);?>" title="Delete">
					<button type="button" class="btn btn-danger" style="float:right;margin-right:20px;" onclick="return confirm('Are You Sure You Want To Delete This Scene?');">
						<i class="fa fa-trash-o"></i> Delete
					</button>
				</a>
            <?php else :?>
				<button type="button" disabled class="btn btn-danger" style="float:right;margin-right:20px;">
					<i class="fa fa-trash-o"></i> Delete
				</button>
            <?php endif;?>
            <?php if(GetUserPermissions("edit")==true):?>
				<button type="submit" class="btn btn-primary" style="float:right;margin-right:20px;">Save</button>
            <?php else:?>
				<button disabled type="button" class="btn btn-primary" style="float:right;margin-right:20px;">Save</button>
            <?php endif;?>
        </h3>
        <input type='hidden' value="<?php echo temp_encode("update_scene");?>" name='type' />
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3" >
            <div style="background-color:#fff;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);padding:10px;margin-bottom:20px;margin-top:5px;margin-right:10px;" class="email-list1">
                <?php
                    $query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name LIKE 'Enable Sensor:%' OR event_name LIKE 'Disable Sensor:%')";
                    $EnableDisable_Sensors_ = mysqli_query($GS_DBCONN, $query);
                    $EnableDisable_Sensors = mysqli_num_rows($EnableDisable_Sensors_);
                    ?>
                <div class="" style="background-color:#fff;padding:5px;margin-bottom:10px;width:100%;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
                    <button style="width:80%" type="button" class="btn btn-default" data-toggle="modal" data-target="#EnableDisable_sensors">Enable/Disable Sensors</button>
                    <input type="checkbox" data-toggle="toggle" data-size="small" name="thenchk1" value="EnableDisable_Sensors" style="float:right;margin-top:10px;" <?php if ($EnableDisable_Sensors>0):?> checked<?php endif;?>/>
                </div>
                <?php
                    $query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name LIKE 'Enable Camera:%' OR event_name LIKE 'Disable Camera:%')";
                    $EnableDisable_Cameras_ = mysqli_query($GS_DBCONN, $query);
                    $EnableDisable_Cameras = mysqli_num_rows($EnableDisable_Cameras_);
                    ?>
                <div class="" style="background-color:#fff;padding:5px;margin-bottom:10px;width:100%;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
                    <button style="width:80%;" type="button" class="btn btn-default" data-toggle="modal" data-target="#EnableDisable_cameras">Enable/Disable Cameras</button>
                    <input type="checkbox" data-toggle="toggle" data-size="small" name="thenchk4" value="EnableDisable_Cameras" style="float:right;margin-top:10px;"  <?php if ($EnableDisable_Cameras>0):?> checked<?php endif;?>/>
                </div>
                <?php
                    $query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name LIKE 'PLAYSONG|%')";
                    $MusicControl_ = mysqli_query($GS_DBCONN, $query);
                    $MusicControl = mysqli_num_rows($MusicControl_);
                    ?>
                <div class="" style="background-color:#fff;padding:5px;margin-bottom:10px;width:100%;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
                    <button style="width:80%;" type="button" class="btn btn-default" data-toggle="modal" data-target="#MusicControl">Music Control</button>
                    <input type="checkbox" data-toggle="toggle" data-size="small" name="thenchk4" value="EnableDisable_Music" style="float:right;margin-top:10px;"  <?php if ($MusicControl>0):?> checked<?php endif;?>/>
                </div>
                <?php
                    $query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name LIKE 'Script:%')";
                    $CustomScripts_ = mysqli_query($GS_DBCONN, $query);
                    $CustomScripts = mysqli_num_rows($CustomScripts_);
                    ?>
                <div class="" style="background-color:#fff;padding:5px;margin-bottom:10px;width:100%;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
                    <button style="width:80%;" type="button" class="btn btn-default" data-toggle="modal" data-target="#ExecuteScripts">Execute Script</button>
                    <input type="checkbox" data-toggle="toggle" data-size="small" name="thenchk4" value="CustomScripts" style="float:right;margin-top:10px;"  <?php if ($CustomScripts>0):?> checked<?php endif;?>/>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-5" style="">
            <div class="mailbox-content" style="padding:0px;overflow:auto;margin-top:5px;margin-bottom:20px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
                <table class="table">
                    <col width="150">
                    <col width="200">
                    <col width="150">
                    <col width="80">
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>Device State</th>
                            <th>Device State</th>
                            <th>Don't Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count2=0;
                            $query = "SELECT * FROM devices ";
                            $devices = mysqli_query($GS_DBCONN, $query);
                            while($device = mysqli_fetch_assoc($devices)) { 
                            
                            //get Device config
							 $deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>".trim($device["deviceXML"])) or die("Error: Cannot create object");
							$count2++;
							$query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name LIKE 'Device:".$device['ID'].":1%' OR event_name LIKE 'Device:".$device['ID'].":0%' OR event_name='Device:".$device['ID']."_ignore')";
							$scenes2 = mysqli_query($GS_DBCONN, $query);
							$scene2 = mysqli_fetch_assoc($scenes2);
						?>
                        <tr>
                            <td>
                                <span class="small-font"><?php echo ucfirst($device['device_name']);?></span>
                            </td>
                            <td>
                                <input id="device_state<?php echo $device['ID'];?>" type="hidden" name="state<?php echo $count2;?>" value="" />
                                <input id="device_id<?php echo $count2;?>" type="hidden" name="device<?php echo $count2;?>" value="<?php echo $device['ID'];?>" />
                                <button id="btnon_<?php echo $device['ID'];?>" onclick="changeState<?php echo $device['ID'];?>('1');
                                    $(this).addClass('btn-primary');$(this).removeClass('btn-default');
                                    $('#btnoff_<?php echo $device['ID'];?>').addClass('btn-default');
                                    $('#btnoff_<?php echo $device['ID'];?>').removeClass('btn-primary');
                                    $('#btnignore_<?php echo $device['ID'];?>').addClass('btn-default');
                                    $('#btnignore_<?php echo $device['ID'];?>').removeClass('btn-primary');
                                    $('#device_brightness<?php echo $device['ID'];?>').val('100');"
                                    type="button" class="btn btn-default" <?php if(strpos($device['tags'],"Dimmable")):?>style=""<?php endif;?>>
                                    <i class="fa <?php if($device['device_icon']==''){echo "fa-power-off";}else{echo $device['device_icon'];}?>"></i> 
                                    <?php if ($device['device_method']=="1") :?><!-- On/Cff -->
										Turn On
                                    <?php elseif ($device['device_method']=="2") :?><!-- Unlock/Lock -->
										Unlock
                                    <?php elseif ($device['device_method']=="3") :?><!-- Open/Close -->
										Open
                                    <?php elseif ($device['device_method']=="4") :?><!-- Arm/Disarm -->
										Arm
                                    <?php endif;?>
                                </button>
                                <?php if(strpos($device['tags'],"Dimmable")!==false):?>
									<select class="form-control1" style="width:40px;height:30px;" id="device_brightness<?php echo $device['ID'];?>" 
										onchange="changeState<?php echo $device['ID'];?>('1')">
										<option value="0">0</option>
										<option value="10">10</option>
										<option value="20">20</option>
										<option value="30">30</option>
										<option value="40">40</option>
										<option value="50">50</option>
										<option value="60">60</option>
										<option value="70">70</option>
										<option value="80">80</option>
										<option value="90">90</option>
										<option value="100">100</option>
									</select>
                                <?php endif;?>
                                <?php if(strpos($device['tags'],"Color")!==false):?>
									<select class="form-control1" style="width:132px;height:30px;" id="device_color<?php echo $device['ID'];?>" 
										onchange="changeState<?php echo $device['ID'];?>('1')">
										<option value="noColor">No Color</option>
										<option value="warmwhite">Warm White</option>
										<option value="coolwhite">Cool White</option>
										<option value="green">Green</option>
										<option value="red">Red</option>
										<option value="blue">Blue</option>
										<option value="orange">Orange</option>
										<option value="yellow">Yellow</option>
										<option value="pink">Pink</option>
										<option value="purple">Purple</option>
									</select>
                                <?php endif;?>
                            </td>
                            <td>
                                <button id="btnoff_<?php echo $device['ID'];?>" onclick="
                                    changeState<?php echo $device['ID'];?>('0');
                                    $(this).addClass('btn-primary');$(this).removeClass('btn-default');
                                    $('#btnon_<?php echo $device['ID'];?>').addClass('btn-default');
                                    $('#btnon_<?php echo $device['ID'];?>').removeClass('btn-primary');
                                    $('#btnignore_<?php echo $device['ID'];?>').addClass('btn-default');
                                    $('#btnignore_<?php echo $device['ID'];?>').removeClass('btn-primary');
                                    $('#device_brightness<?php echo $device['ID'];?>').val('0');
                                    $('#device_color<?php echo $device['ID'];?>').val('warmwhite');"
                                    type="button" class="btn btn-default" style="width:100%;">
                                    <i class="fa <?php if($device['device_icon']==''){echo "fa-power-off";}else{echo $device['device_icon'];}?>"></i> 
                                    <?php if ($device['device_method']=="1") :?><!-- On/Cff -->
										Turn Off
                                    <?php elseif ($device['device_method']=="2") :?><!-- Unlock/Lock -->
										Lock
                                    <?php elseif ($device['device_method']=="3") :?><!-- Open/Close -->
										Close
                                    <?php elseif ($device['device_method']=="4") :?><!-- Arm/Disarm -->
										Disarm
                                    <?php endif;?>
                                </button>
                            </td>
                            <td>
                                <button id="btnignore_<?php echo $device['ID'];?>" onclick="
                                    changeState<?php echo $device['ID'];?>('3');
                                    $(this).addClass('btn-primary');$(this).removeClass('btn-default');
                                    $('#btnon_<?php echo $device['ID'];?>').addClass('btn-default');
                                    $('#btnon_<?php echo $device['ID'];?>').removeClass('btn-primary');
                                    $('#btnoff_<?php echo $device['ID'];?>').addClass('btn-default');
                                    $('#btnoff_<?php echo $device['ID'];?>').removeClass('btn-primary');
                                    $('#device_brightness<?php echo $device['ID'];?>').val('0');
                                    $('#device_color<?php echo $device['ID'];?>').val('warmwhite');" 
                                    type="button" class="btn btn-default">
                                Ignore
                                </button>
                            </td>
                            <?php if ($device['enable_auto_off']=="1") :?>
								<script>
									$('#auto_off_chk<?php echo $device['ID'];?>').attr("checked", "true");
								</script>
                            <?php endif;?>
                            <script>
                                function changeState<?php echo $device['ID'];?>(DeviceState){
                                	var sendstring = 'Device:'+$('#device_id<?php echo $count2;?>').val()+':'+DeviceState;
                                	  <?php if(strpos($device['tags'],"Dimmable")!==false):?>
                                	  	sendstring=sendstring + ':' + $('#device_brightness<?php echo $device['ID'];?>').val();
                                	 <?php endif;?>
                                	 <?php if(strpos($device['tags'],"Color")!==false):?>
                                	 	sendstring=sendstring + ':' + $('#device_color<?php echo $device['ID'];?>').val();
                                	 <?php endif;?>
                                	 
                                	$('#device_state<?php echo $device['ID'];?>').val(sendstring);
                                }
                                
                                
                                <?php $sceneEvent = explode(":",$scene2['event_name']);?>
                                
                                <?php if ($sceneEvent[2]=="1") :?>										
                                	$('#device_state<?php echo $device['ID'];?>').val("Device:" + $('#device_id<?php echo $count2;?>').val() + ":1:<?php echo $sceneEvent[3];?>");
                                	$('#btnon_<?php echo $device['ID'];?>').removeClass("btn-default");
                                	$('#btnon_<?php echo $device['ID'];?>').addClass("btn-primary");
                                	$('#btnoff_<?php echo $device['ID'];?>').removeClass("btn-primary");
                                	$('#btnoff_<?php echo $device['ID'];?>').addClass("btn-default");
                                	$('#btnignore_<?php echo $device['ID'];?>').removeClass("btn-primary");
                                	$('#btnignore_<?php echo $device['ID'];?>').addClass("btn-default");
                                	<?php if(strpos($device['tags'],"Dimmable")!==false):?>
                                		$("#device_brightness<?php echo $device['ID'];?>").val("<?php echo $sceneEvent[3];?>");
                                	<?php endif;?>
                                	<?php if(strpos($device['tags'],"Color")!==false):?>
                                		$("#device_color<?php echo $device['ID'];?>").val("<?php echo $sceneEvent[4];?>");
                                	<?php endif;?>
                                
                                <?php elseif ($sceneEvent[2]=="0") :?>	
                                	$('#device_state<?php echo $device['ID'];?>').val("Device:" + $('#device_id<?php echo $count2;?>').val() + ":0:0");
                                	$('#btnoff_<?php echo $device['ID'];?>').removeClass("btn-default");
                                	$('#btnoff_<?php echo $device['ID'];?>').addClass("btn-primary");
                                	$('#btnon_<?php echo $device['ID'];?>').removeClass("btn-primary");
                                	$('#btnon_<?php echo $device['ID'];?>').addClass("btn-default");
                                	$('#btnignore_<?php echo $device['ID'];?>').removeClass("btn-primary");
                                	$('#btnignore_<?php echo $device['ID'];?>').addClass("btn-default");
                                	<?php if(strpos($device['tags'],"Dimmable")!==false):?>
                                		$("#device_brightness<?php echo $device['ID'];?>").val("0");
                                	<?php endif;?>
                                	<?php if(strpos($device['tags'],"Color")!==false):?>
                                		$("#device_color<?php echo $device['ID'];?>").val("warmwhite");
                                	<?php endif;?>
                                	
                                <?php elseif (trim($scene2['event_name'])== ("Device:".$device['ID']."_ignore")) :?>
                                	$('#device_state<?php echo $device['ID'];?>').val("Device:" + $('#device_id<?php echo $count2;?>').val() + "_ignore");   
                                	$('#btnoff_<?php echo $device['ID'];?>').removeClass("btn-primary");
                                	$('#btnoff_<?php echo $device['ID'];?>').addClass("btn-default");
                                	$('#btnon_<?php echo $device['ID'];?>').removeClass("btn-primary");
                                	$('#btnon_<?php echo $device['ID'];?>').addClass("btn-default");
                                	$('#btnignore_<?php echo $device['ID'];?>').removeClass("btn-default");
                                	$('#btnignore_<?php echo $device['ID'];?>').addClass("btn-primary");
                                	<?php if(strpos($device['tags'],"Dimmable")!==false):?>
                                		$("#device_brightness<?php echo $device['ID'];?>").val("0");
                                	<?php endif;?>
                                	<?php if(strpos($device['tags'],"Color")!==false):?>
                                		$("#device_color<?php echo $device['ID'];?>").val("warmwhite");
                                	<?php endif;?>
                                <?php endif;?>
                                 
                            </script>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <div class="clearfix"> </div>
    <div id="Load_TempEncodeCheck"></div>
</div>
<!--########################################### Enable/Disable Sensors Modal ############################# -->
<!-- Modal -->
<div class="modal fade" id="EnableDisable_sensors" role="dialog" style="z-index:9999;margin-top:100px;">
    <div class="modal-dialog" >
        <!-- Modal content-->
        <form method="Post">
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Enable/Disable Sensors</h4>
                </div>
                <div class="modal-body" style="height:300px;overflow:auto;">
                    <input type="hidden" name="type" value="<?php echo temp_encode("EnableDisable_Sensors");?>"/>
                    <table class="table">
                        <th>Sensor Name</th>
                        <th>Enable</th>
                        <th>Disable</th>
                        <th>Ignore</th>
                        <?php
                            $query = "SELECT * FROM sensors ORDER BY ID ASC ";
                            $sensors = mysqli_query($GS_DBCONN, $query);
                            while($sensor = mysqli_fetch_assoc($sensors)) { 
                            	
                            	$query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name='Enable Sensor:".$sensor['ID']."' 
                            	OR event_name='Disable Sensor:".$sensor['ID']."' OR event_name='Ignore Sensor:".$sensor['ID']."')";
                            	$EnableDisable_Sensors_ = mysqli_query($GS_DBCONN, $query);
                            	$EnableDisable_Sensors = mysqli_fetch_array($EnableDisable_Sensors_);
                            ?>
                        <tr style="padding:0px;margin:0px;">
                            <td><?php echo $sensor['sensor_name'];?></td>
                            <td><input type="radio" name="state_<?php echo $sensor['ID'];?>" id="EnableSensor_<?php echo $sensor['ID'];?>" value="Enable Sensor:<?php echo $sensor['ID'];?>" title="Enable" style="margin-left:10px;"/></td>
                            <td><input type="radio" name="state_<?php echo $sensor['ID'];?>" id="DisableSensor_<?php echo $sensor['ID'];?>" value="Disable Sensor:<?php echo $sensor['ID'];?>" title="Disable" style="margin-left:10px;"/></td>
                            <td><input type="radio" name="state_<?php echo $sensor['ID'];?>" id="IgnoreSensor_<?php echo $sensor['ID'];?>" value="Ignore Sensor:<?php echo $sensor['ID'];?>" title="Ignore" style="margin-left:10px;"/></td>
                        </tr>
                        <?php if($EnableDisable_Sensors['event_name']==('Enable Sensor:'.$sensor['ID'])) :?>
							<script>$('#EnableSensor_<?php echo $sensor['ID'];?>').prop("checked",true);</script>
                        <?php elseif($EnableDisable_Sensors['event_name']==('Disable Sensor:'.$sensor['ID'])) :?>
							<script>$('#DisableSensor_<?php echo $sensor['ID'];?>').prop("checked",true);</script>
                        <?php elseif($EnableDisable_Sensors['event_name']==('Ignore Sensor:'.$sensor['ID'])) :?>
							<script>$('#IgnoreSensor_<?php echo $sensor['ID'];?>').prop("checked",true);</script>
                        <?php endif;?>
                        <?php }?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" >Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!--###################################################################################################### -->
<!--########################################### Music Control Modal ################################## -->
<!-- Modal -->
<div class="modal fade" id="MusicControl" role="dialog" style="z-index:9999;margin-top:100px;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form method="Post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Music Control</h4>
                </div>
                <div class="modal-body" style="height:300px;overflow:auto;">
                    <input type="hidden" name="type" value="<?php echo temp_encode("RoomMusicControl");?>"/>
                    <table class="table">
                        <th>Room</th>
                        <th>Command</th>
                        <th>Song/Playlist</th>
                        <th>volume</th>
                        <?php
                            $query = "SELECT * FROM home_rooms ORDER BY ID ASC ";
                            $rooms = mysqli_query($GS_DBCONN, $query);
                            while($room = mysqli_fetch_assoc($rooms)) { 
                            	
                            	$query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name LIKE 'PLAYSONG|".$room['ID']."|%' )";
                            	$SceneMusicControl_ = mysqli_query($GS_DBCONN, $query);
                            	$SceneMusicControl = mysqli_fetch_array($SceneMusicControl_);
                            ?>
							<tr style="padding:0px;margin:0px;">
								<input type="hidden" name="MusicControlRoomValue_<?php echo $room['ID'];?>" id="MusicControl_value<?php echo $room['ID'];?>"/>
								<td><?php echo $room['room_name'];?></td>
								<td>
									<select onChange="saveSongChoice<?php echo $room['ID'];?>()" onKeyUp="saveSongChoice<?php echo $room['ID'];?>()" id="Then_MC_Command<?php echo $room['ID'];?>" style="width:100px;margin-bottom:2px;">
										<option value="Ignore">Ignore</option>
										<option value="Play">Play Song</option>
										<option value="Playlist">Play Playlist</option>
										<option value="Pause">Pause</option>
										<option value="Resume">Resume</option>
										<option value="Stop">Stop</option>
										<option value="Mute">Mute</option>
										<option value="Unmute">Unmute</option>
									</select>
								</td>
								<td>
									<!-- Songs -->
									<select onChange="saveSongChoice<?php echo $room['ID'];?>()" onKeyUp="saveSongChoice<?php echo $room['ID'];?>()" id="Then_MC_Song<?php echo $room['ID'];?>" style="width:200px;margin-bottom:2px;">
										<?php 
											$query_music = "SELECT * FROM music_data";
											$resultsMusic_b = mysqli_query($GS_DBCONN, $query_music);
											while($resultMusic = mysqli_fetch_assoc($resultsMusic_b)) { 
											?>	
											<option value="<?php echo $resultMusic['song_location'];?>">
												<?php if(trim($resultMusic['song_name'])!=""){
													echo ucwords($resultMusic['song_name']);
													}else{
													echo ucwords($resultMusic['song_location']);
													} ?>
											</option>
										<?php }?>
									</select>
									<!--- Playlist -->
									<select onChange="saveSongChoice<?php echo $room['ID'];?>()" onKeyUp="savePlaylistChoice<?php echo $room['ID'];?>()" id="Then_MC_Playlist<?php echo $room['ID'];?>" style="display:none;width:200px;margin-bottom:2px;">
										<option value="0">Play All Songs</option>
										<?php 
											$query_music = "SELECT * FROM music_playlists";
											$resultsMusic_b = mysqli_query($GS_DBCONN, $query_music);
											while($resultMusic = mysqli_fetch_assoc($resultsMusic_b)) { 
											?>	
											<option value="<?php echo $resultMusic['ID'];?>">
												<?php echo ucwords($resultMusic['playlist_name']);?>
											</option>
										<?php }?>
									</select>
								</td>
								<td>
									<select onChange="saveSongChoice<?php echo $room['ID'];?>()" onKeyUp="saveSongChoice<?php echo $room['ID'];?>()" id="Then_MC_Volume<?php echo $room['ID'];?>" style="width:50px;margin-bottom:2px;">
										<?php $i=0;  while($i<100){ $i=$i+10;?>	
											<option value="<?php echo $i;?>"><?php echo ($i);?></option>
										<?php }?>
									</select>
								</td>
								<script>
									function saveSongChoice<?php echo $room['ID'];?>(){
										var playItem = "";
										
										$("#Then_MC_Volume<?php echo $room['ID'];?>").prop("disabled",false);
										
										if($("#Then_MC_Command<?php echo $room['ID'];?>").val()=="Play" ){
											$("#Then_MC_Song<?php echo $room['ID'];?>").prop("disabled",false);
											$("#Then_MC_Playlist<?php echo $room['ID'];?>").hide();
											$("#Then_MC_Song<?php echo $room['ID'];?>").show();
											playItem = $("#Then_MC_Song<?php echo $room['ID'];?>").val();
										}else if($("#Then_MC_Command<?php echo $room['ID'];?>").val()=="Playlist"){
											$("#Then_MC_Song<?php echo $room['ID'];?>").hide();
											$("#Then_MC_Playlist<?php echo $room['ID'];?>").show();
											playItem = $("#Then_MC_Playlist<?php echo $room['ID'];?>").val();
										}else{
											playItem = "";
											$("#Then_MC_Song<?php echo $room['ID'];?>").prop("disabled",true);
											if($("#Then_MC_Command<?php echo $room['ID'];?>").val()=="Ignore"){
												$("#Then_MC_Volume<?php echo $room['ID'];?>").prop("disabled",true);
											}
										}
										
										$("#MusicControl_value<?php echo $room['ID'];?>").val("PLAYSONG|<?php echo $room['ID'];?>|"+ playItem +"|"+$("#Then_MC_Volume<?php echo $room['ID'];?>").val() +"|"+$("#Then_MC_Command<?php echo $room['ID'];?>").val()); 
									}
								</script>
							</tr>
							<script>
								<?php $SceneMusicdata = explode("|",$SceneMusicControl['event_name']);?>
								
								<?php if ($SceneMusicdata[4]=="Play") :?>
									$("#Then_MC_Playlist<?php echo $room['ID'];?>").hide();
									$("#Then_MC_Song<?php echo $room['ID'];?>").show();
									$("#Then_MC_Song<?php echo $room['ID'];?>").val("<?php echo $SceneMusicdata[2];?>");
								<?php elseif($SceneMusicdata[4]=="Playlist"):?>
									$("#Then_MC_Song<?php echo $room['ID'];?>").hide();
									$("#Then_MC_Playlist<?php echo $room['ID'];?>").show();
									$("#Then_MC_Playlist<?php echo $room['ID'];?>").val("<?php echo $SceneMusicdata[2];?>");
								<?php endif;?>
								
								$("#Then_MC_Volume<?php echo $room['ID'];?>").val("<?php echo $SceneMusicdata[3];?>");
								$("#Then_MC_Command<?php echo $room['ID'];?>").val("<?php echo $SceneMusicdata[4];?>"); 
							</script>	
                        <?php }?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!--###################################################################################################### -->
<!--########################################### Enable/Disable Cameras Modal ############################# -->
<!-- Modal -->
<div class="modal fade" id="EnableDisable_cameras" role="dialog" style="z-index:9999;margin-top:100px;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form method="Post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Enable/Disable Cameras</h4>
                </div>
                <div class="modal-body" style="height:300px;overflow:auto;">
                    <input type="hidden" name="type" value="<?php echo temp_encode("EnableDisable_cameras");?>"/>
                    <table class="table">
                        <th>Sensor Name</th>
                        <th>Enable</th>
                        <th>Disable</th>
                        <th>Ignore</th>
                        <?php
                            $query = "SELECT * FROM camera_list ORDER BY ID ASC ";
                            $cameras = mysqli_query($GS_DBCONN, $query);
                            while($camera = mysqli_fetch_assoc($cameras)) { 
                            	
                            	$query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name='Enable Camera:".$camera['ID']."' 
                            	OR event_name='Disable Camera:".$camera['ID']."' OR event_name='Ignore Camera:".$camera['ID']."')";
                            	$EnableDisable_cameras_ = mysqli_query($GS_DBCONN, $query);
                            	$EnableDisable_Cameras = mysqli_fetch_array($EnableDisable_cameras_);
                            ?>
							<tr style="padding:0px;margin:0px;">
								<td><?php echo $camera['camera_name'];?></td>
								<td><input type="radio" name="state_<?php echo $camera['ID'];?>" id="Enable_Camera_<?php echo $camera['ID'];?>" value="Enable Camera:<?php echo $camera['ID'];?>" title="Enable" style="margin-left:10px;"/></td>
								<td><input type="radio" name="state_<?php echo $camera['ID'];?>" id="Disable_Camera_<?php echo $camera['ID'];?>" value="Disable Camera:<?php echo $camera['ID'];?>" title="Disable" style="margin-left:10px;"/></td>
								<td><input type="radio" name="state_<?php echo $camera['ID'];?>" id="Ignore_Camera_<?php echo $camera['ID'];?>" value="Ignore Camera:<?php echo $camera['ID'];?>" title="Ignore" style="margin-left:10px;"/></td>
							</tr>
							<?php if($EnableDisable_Cameras['event_name']==('Enable Camera:'.$camera['ID'])) :?>
								<script>$('#Enable_Camera_<?php echo $camera['ID'];?>').prop("checked",true);</script>
							<?php elseif($EnableDisable_Cameras['event_name']==('Disable Camera:'.$camera['ID'])) :?>
								<script>$('#Disable_Camera_<?php echo $camera['ID'];?>').prop("checked",true);</script>
							<?php elseif($EnableDisable_Cameras['event_name']==('Ignore Camera:'.$camera['ID'])) :?>
								<script>$('#Ignore_Camera_<?php echo $camera['ID'];?>').prop("checked",true);</script>
							<?php endif;?>
                        <?php }?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!--########################################### Execute Scripts Modal ############################# -->
<!-- Modal -->
<div class="modal fade" id="ExecuteScripts" role="dialog" style="z-index:9999;margin-top:100px;">
    <div class="modal-dialog" >
        <!-- Modal content-->
        <form method="Post">
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Execute a Script</h4>
                </div>
                <div class="modal-body" style="height:300px;overflow:auto;">
                    <input type="hidden" name="type" value="<?php echo temp_encode("ExecuteScripts");?>"/>
                    <table class="table">
                        <th>Script Name</th>
                        <th>Execute</th>
                        <th>Ignore</th>
                        <?php
                            $query = "SELECT * FROM custom_scripts ORDER BY ID ASC ";
                            $scripts = mysqli_query($GS_DBCONN, $query);
                            while($script = mysqli_fetch_assoc($scripts)) { 
                            	
                            	$query = "SELECT * FROM scene_events WHERE scene_id='".$scene_id."' AND (event_name='Script: ".$script['ID']."')";
                            	$scriptStates_ = mysqli_query($GS_DBCONN, $query);
                            	$scriptState = mysqli_fetch_array($scriptStates_);
                            ?>
                        <tr style="padding:0px;margin:0px;">
                            <td><?php echo $script['script_name'];?></td>
                            <td><input type="radio" name="state_<?php echo $script['ID'];?>" id="RunScript_<?php echo $script['ID'];?>" value="Script:<?php echo $script['ID'];?>" title="Run" style="margin-left:10px;"/></td>
                            <td><input type="radio" name="state_<?php echo $script['ID'];?>" id="IgnoreScript_<?php echo $script['ID'];?>" value="" title="Don't Run" style="margin-left:10px;"/></td>
                        </tr>
                        <?php if($scriptState['event_name']==('Script: '.$script['ID'])) :?>
                        <script>$('#RunScript_<?php echo $script['ID'];?>').prop("checked",true);</script>
                        <?php endif;?>
                        <?php }?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" >Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include("includes/footer.php");?>
<?php include("includes/modals.php");?>
</body>
</html>