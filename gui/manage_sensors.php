<?php
    if(!empty($_POST) || !empty($_GET['triggerSensor'])){
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
    	$GS_phueServiceBypass = false;
    	$GS_wemoServiceBypass = true;
    	$GS_mqttServiceBypass = true;
    	$GS_emailServiceBypass = true;
    	$GS_squeezeBoxServiceBypass = true;
    	$GS_webIncludesIncluded = false;
    	
    	$upParrentDir = '/../../..';
    	require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    	
    	include("Autoload/AutoLoad_DataStates.php");
    	include("Autoload/AutoLoad_SHAlerts.php");
    	include("Autoload/Autoload_SensorStates.php");
    	include("Autoload/Autoload_checkTempEncode.php");
    	exit;	
    }
    ################################################
    
    #################### API #######################
    $page_title="Manage Sensors";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    
    	
	//update sensor from modal
	if(temp_decode($_POST['type'])=='edit_sensor_modal' && GetUserPermissions("edit")==true){
		
		if(clean_text($_POST['sensor_close_address'],200)==""){
			$sensor_close_address = "SAD_".safe_encode("".rand(100,20000));
		}else{
			$sensor_close_address = clean_text($_POST['sensor_close_address'],200);
		}
		
		$insert_query="UPDATE sensors SET 
			sensor_name='".clean_text(ucwords($_POST['sensor_name']),200)."'
			,sensor_address='".clean_text($_POST['sensor_address'],200)."'
			,sensor_close_address='".$sensor_close_address."'
			,room='".(int)clean_text($_POST['room'],11)."'
			,enabled='".(int)clean_text($_POST['enabled'],1)."'
			,notifications='".(int)clean_text($_POST['notifications'],1)."'
			,last_changed_by='".$_SESSION['id']."'
			,is_alarmSensorHome='".(int)clean_text($_POST['is_alarmSensorHome'],1)."'
			,is_alarmSensorAway='".(int)clean_text($_POST['is_alarmSensorAway'],1)."'
			WHERE ID='".clean_text($_POST['sensor_ID'],11)."'";
			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}

	//start find sensor
	if(temp_decode($_POST['type'])=="start_find_sensors"){
		mysqli_query($GS_DBCONN, "UPDATE settings SET scan_for_new_sensors='1' WHERE ID='1'");
	}    
	//end find sensor
	if(temp_decode($_POST['type'])=="end_find_sensors"){
		mysqli_query($GS_DBCONN, "UPDATE settings SET scan_for_new_sensors='0' WHERE ID='1'");
		
		//clear found sensors list
		mysqli_query($GS_DBCONN, "DELETE FROM find_sensors_list");
	}        
	
	//add new sensor
	if(temp_decode($_POST['type'])=='add_sensor' && GetUserPermissions("add")==true){
			$insert_query="INSERT INTO sensors (sensor_name,sensor_address,sensor_close_address,sensor_state,sensor_kind,sensor_type,enabled,notifications,room,time_triggered,last_changed_by,is_alarmSensorHome,is_alarmSensorAway)
			VALUES('".clean_text($_POST['sensor_name'],200)."'
			,'".clean_text($_POST['sensor_address'],200)."'
			,'".clean_text($_POST['sensor_close_address'],200)."'
			,'0'
			,'".ucfirst(clean_text(temp_decode($_GET['type']),50))."'
			,'".clean_text($_POST['sensor_type'],20)."'
			,'1'
			,'0'
			,'".(int)clean_text($_POST['room'],50)."'
			,'0'
			,'".$_SESSION['id']."'
			,'1'
			,'1')";
			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}
	
	if($_POST['type']=="triggerSensor"){
		GF_ifttt(temp_decode($_POST['SensorAddress'],500),"0","1","USER:".$_SESSION['id']);	
	}
	
	//delete sensor
	if($_GET['delete']!="" && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		mysqli_query($GS_DBCONN, "DELETE FROM sensors WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."'") or die (mysqli_error($GS_DBCONN));
	}
    	

    ?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px;margin-bottom:30px;">
    <h3>
        Manage <?php echo ucfirst(strtolower(temp_decode($_GET['type'])));?> Sensors
        <div style="overflow:auto;height:40px;text-align:right;">
            <a href="manage_sensors.php?type=<?php echo temp_encode("motion");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Motion</button></a>
            <a href="manage_sensors.php?type=<?php echo temp_encode("door");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Doors</button></a>
            <a href="manage_sensors.php?type=<?php echo temp_encode("window");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Windows</button></a>
            <a href="manage_sensors.php?type=<?php echo temp_encode("custom");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Custom</button></a>
            <a href="manage_devices.php"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Devices</button></a>
        </div>
    </h3>
    <?php
        // Find out how many items are in the table
          $total = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM sensors WHERE sensor_kind='".temp_decode($_GET['type'])."'"));
        // How many items to list per page
        $limit = 40;
        // How many pages will there be
        $pages = ceil($total / $limit);
        // What page are we currently on?
        $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array('options' => array('default'   => 1,'min_range' => 1))));
        // Calculate the offset for the query
        $offset = ($page - 1)  * $limit;
        // Some information to display to the user
        $start = $offset + 1;
        $end = min(($offset + $limit), $total);
        // The "back" link
        $prevlink = ($page > 1) ? "
        <a style='font-size:20px;color:#333;' href='?page=1' title='Next page'><i class='fa fa-step-backward'></i></a>&nbsp;
        <a style='font-size:25px;color:#333;' href='?page=".($page - 1)."' title='Last page'><i class='fa fa-caret-left'></i></a>"
        :"";
        // The "forward" link
        $nextlink = ($page < $pages) ? "
        <a style='font-size:25px;color:#333;' href='?page=".($page + 1)."' title='Next page'><i class='fa fa-caret-right'></i></a>&nbsp;
        <a style='font-size:20px;color:#333;' href='?page=".$pages."' title='Last page'><i class='fa fa-step-forward'></i></a>"
        :"";
        // Display the paging information
        echo "<center><div style='width:100%;font-size:18px;'><b>".$prevlink." Page ".$page." of ".$pages." ".$nextlink."</b></div></center>";
    ?>
    <div style="margin-top:20px;margin-bottom:40px;">
       <div class="well1 col-xs-12 col-sm-5 col-md-3 col-lg-2 " style="padding:5px;height:120px;min-width:200px;background-color:#f5f5f5;margin-bottom:5px;">
            <div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);height:113px;">
	            <div style="text-align:center;font-size:35px;margin-top:-10px;">
	                <i class="fa fa-plus"></i>
	            </div>
	            <div style="">
	                <?php if(GetUserPermissions("add")==true):?>
	                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_sensor_modal" style="width:100%;">Add New</button>
	                <?php else:?>
	                <button type="button" class="btn btn-primary" disabled title="You do not have permission" style="width:100%;">Add New</button>
	                <?php endif;?>
	            </div>
	        </div>
       </div>
        <?php
            $count = 0;
            $query = "SELECT * FROM sensors WHERE sensor_kind='".temp_decode($_GET['type'])."' ORDER BY ID LIMIT ".$limit." OFFSET ".$offset;
            $results = mysqli_query($GS_DBCONN, $query);
            while($result = mysqli_fetch_assoc($results)) { $count++;
		?>
			<div class="well1 col-xs-12 col-sm-5 col-md-3 col-lg-2 " style="padding:5px;height:120px;min-width:200px;background-color:#f5f5f5;margin-bottom:5px;">
           		<div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
					<p style="width:100%;height:50px;overflow:auto;">
						<?php echo $result['sensor_name'];?><br/>
						<span style="font-size:12px;"><?php if($result['last_triggered']!=""){ echo ago($result['last_triggered']);}?></span>
					</p>
					<div style="text-align:center;">
						<?php if ($result['enabled']=='1') :?>
							<div style="display:inline-block;">
								<span id="sensor<?php echo $result['ID'];?>_not_active" style="">
									<form method="POST" class="autoform">
										<input type="hidden" value="triggerSensor" name="type"/>
										<input type="hidden" value="<?php echo temp_encode($result['sensor_address']);?>" name="SensorAddress"/>
										<button type="submit" class="btn btn-default">Trigger</button>
									</form>
								</span>
							</div>
							<!-- check if this sensor has a close address -->
							<?php if ($result['sensor_close_address']!="") :?> 
								<div style="display:inline-block;">
									<span id="sensor<?php echo $result['ID'];?>_active" style="display:none;">
										<form method="POST" class="autoform">
											<input type="hidden" value="triggerSensor" name="type"/>
											<input type="hidden" value="<?php echo temp_encode($result['sensor_close_address']);?>" name="SensorAddress"/>
											<button type="submit" class="btn btn-primary">Trigger</button>
										</form>
									</span>
								</div>
							<?php else:?>
								<div style="display:inline-block;">
									<span id="sensor<?php echo $result['ID'];?>_active" style="display:none;">
										<form method="POST" class="autoform">
											<input type="hidden" value="triggerSensor" name="type"/>
											<input type="hidden" value="<?php echo temp_encode($result['sensor_address']);?>" name="SensorAddress"/>
											<button type="submit" class="btn btn-primary">Trigger</button>
										</form>
									</span>
								</div>
							<?php endif;?>
						<?php else : //If Disabled ?>
							<div id="sensor<?php echo $result['ID'];?>_not_active" style="display:inline-block;"><a href="#"><button style="padding:6px;" class="btn btn-danger">Disabled</button></a></div>
						<?php endif;?>
						<?php if(GetUserPermissions("edit")==true):?>
							<button data-toggle="modal" data-target="#sensor_edit_modal"  onclick="edit_sensor(
								'<?php echo $result['ID'];?>',
								'<?php echo $result['sensor_name'];?>',
								'<?php echo $result['sensor_address'];?>',
								'<?php echo $result['sensor_close_address'];?>',
								'<?php echo $result['room'];?>',
								'<?php echo $result['notifications'];?>',
								'<?php echo $result['is_alarmSensorHome'];?>',
								'<?php echo $result['is_alarmSensorAway'];?>',
								'<?php echo $result['enabled'];?>');"
								type="button" class="btn btn-default"><i class="fa fa-pencil"></i>
							</button>  								  
						<?php else:?>
							<button type="button" class="btn btn-default" disabled title="You do not have permission"><i class="fa fa-pencil"></i></button>  
						<?php endif;?>
						<?php if(GetUserPermissions("delete")==true):?>
							<a onclick="return confirm('Are You Sure You Want To Delete This?');" href="?delete=<?php echo temp_encode($result['ID']);?>&type=<?php echo $_GET['type'];?>">
								<button type="button" class="btn btn-danger">
									<i class="fa fa-trash-o"></i>
								</button>
							</a>
						<?php else :?>
							<button type="button" class="btn btn-danger" disabled title="You do not have permission"><i class="fa fa-trash-o"></i></button>
						<?php endif;?>
					</div>
				</div>
			</div>
        <?php } ?>
    </div>
    <!--########################################### Add Sensor ############################# -->
    <!-- Modal -->
    <div class="modal fade" id="add_sensor_modal" role="dialog" style="z-index:9999;margin-top:100px;">
        <div class="modal-dialog" style="width:400px;">
            <!-- Modal content-->
            <form method="Post" id="add_sensor_frm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="" class="modal-title">Add A Sensor</h4>
                    </div>
                    <div class="modal-body" style="height:350px;overflow:auto;">
                        <input type="hidden" name="type" value="<?php echo temp_encode("add_sensor");?>" />
                        <div class="input-group">
                            <span class="input-group-addon"><b>Type</b></span>
                            <select onchange="sensorType();" name="sensor_type" id="add_sensor_sensor_type" class="form-control1" style="height:30px;width:100%;">
                                <option value="">Not Set</option>
                                <?php if($GS_phueServiceEnabled == true):  //Check If Service Is Enabled ?>
									<option value="phue">Phillips Hue</option>
                                <?php endif;?>
                                <option value="433RF">433Mhz RF</option>
                                <option value="315RF">315Mhz RF</option>
                            </select>
                        </div>
                        <div style="display:none;" id="sensor_type_selected">
                            <div class="input-group">
                                <span class="input-group-addon"><b>Sensor Name</b></span>
                                <input type="text" name="sensor_name" id="sensor_name_add" class="form-control1" style="height:30px;width:100%;" />
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><b>Room</b></span>
                                <select name="room" id="room_add" style="height:30px;width:100%;" class="form-control1">
                                    <option value="">No Room</option>
                                    <?php
                                        $query = "SELECT * FROM home_rooms";
                                        $results = mysqli_query($GS_DBCONN, $query);
                                        while($result = mysqli_fetch_assoc($results)) {
									?>
										<option value="<?php echo $result['ID'];?>"><?php echo $result['room_name'];?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <!-- MQTT -->
                            <div id="div_sensor_address">
                                <label for="sensor_on_address"><b>Sensor On Address:</b></label>
                                <input type="text" name="sensor_address" id="sensor_on_address" class="form-control1" style="height:30px;width:85%;margin-bottom:10px;" />
                                <button class="btn btn-default" style="height:30px;width:10%;" onclick="find_sensor('sensor_on_address');return false;" title="Find Sensor">...</button>
                                <label for="sensor_off_address"><b>Sensor Off Address:</b></label>
                                <input type="text" name="sensor_close_address" id="sensor_off_address" class="form-control1" style="height:30px;width:85%;margin-bottom:10px;" />
                                <button class="btn btn-default" style="height:30px;width:10%;" onclick="find_sensor('sensor_off_address');return false;" title="Find Sensor">...</button><br/>
                            </div>
                            <!-- Phillips Hue -->
                            <div id="div_phillips_hue">
                                <label for="sensor_number"><b>Sensor:</b></label>
                                <div id="getPhueSensorList"></div>
                                <!-- loads sensors from Autoload_phueSensorlist.php -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="saveSensor();" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function sensorType(){ 
          $("#sensor_type_selected").show();
          $("#div_phillips_hue").hide();
          $("#div_sensor_address").hide();
        	
          if($("#add_sensor_sensor_type").val()=="phue"){			
        	  $("#div_phillips_hue").show();
        	  $("#getPhueSensorList").load("Autoload/AutoLoad_phueSensorList.php");
           }else if($("#add_sensor_sensor_type").val()=="433RF"){
        	  $("#div_sensor_address").show();
           }else if($("#add_sensor_sensor_type").val()=="315RF"){
        	  $("#div_sensor_address").show();
           }else{
        	   $("#div_sensor_address").show();
           }
        }
        
        function saveSensor(){
        if($("#add_sensor_sensor_type").val()=="phue"){
        	$("#sensor_on_address").val($("#sensor_number").val());
          }
          $("#add_sensor_frm").submit();
        }
    </script>
    <!--########################################### Sensor Edit Modal ############################# -->
    <!-- Modal -->
    <div class="modal fade" id="sensor_edit_modal" role="dialog" style="z-index:9999;margin-top:100px;">
        <div class="modal-dialog" style="width:400px;">
            <!-- Modal content-->
            <form method="Post">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="" class="modal-title">Edit <?php echo $result['_name'];?></h4>
                    </div>
                    <div class="modal-body" style="height:300px;overflow:auto;">
                        <input type="hidden" name="type" value="<?php echo temp_encode("edit_sensor_modal");?>" />
                        <input type="hidden" name="sensor_ID" value="" id="sensor_ID" />
                        <div class="input-group">
                            <span class="input-group-addon"><b>Sensor Name</b></span>
                            <input type="text" name="sensor_name" value="" id="sensor_name" style="width:100%;height:30px;" class="form-control1"/>
                        </div>
                        <label for="sensor_address"><b>Sensor Address:</b>&nbsp; <a id="modal_get_url_address" href="#" >Get Url</a></label>
                        <input type="text" name="sensor_address" value="" id="sensor_address" style="width:85%;margin-bottom:10px;height:30px;" class="form-control1"/>
                        <button type="button" class="btn btn-default" style="height:30px;width:12%;" onclick="find_sensor('sensor_address');return false;" title="Find Sensor">...</button>
                        <label for="sensor_close_address"><b>Sensor Close Address:</b>&nbsp; <a id="modal_get_url_close_address" href="#">Get Url</a></label>
                        <input type="text" name="sensor_close_address" value="" id="sensor_close_address" style="width:85%;margin-bottom:10px;height:30px;" class="form-control1"/>
                        <button type="button" class="btn btn-default" style="height:30px;width:12%;" onclick="find_sensor('sensor_close_address');return false;" title="Find Sensor">...</button><br/>
                        <div class="input-group">
                            <span class="input-group-addon"><b>Room</b></span>
                            <select name="room" id="room" style="width:100%;height:30px;" class="form-control1">
                                <option value="">No Room</option>
                                <?php
                                    $query = "SELECT * FROM home_rooms";
                                    $results = mysqli_query($GS_DBCONN, $query);
                                    while($result = mysqli_fetch_assoc($results)) {
								?>
									<option value="<?php echo $result['ID'];?>"><?php echo $result['room_name'];?></option>
                                <?php }?>
                            </select>
                        </div>
                        <label for="is_alarmSensorHome"><b>Alarm `Home` Sensor:</b></label>
                        <input type="checkbox" name="is_alarmSensorHome" value="1" style="" id="sensor_is_alarmSensorHome"/>
                        &nbsp;&nbsp;
                        <label for="is_alarmSensorAway"><b>Alarm `Away` Sensor:</b></label>
                        <input type="checkbox" name="is_alarmSensorAway" value="1" style="" id="sensor_is_alarmSensorAway"/>
                        <br/>
                        <label for="notifications"><b>Notifications:</b></label>
                        <input type="checkbox" name="notifications" value="1" style="" id="sensor_notifications"/>
                        &nbsp;&nbsp;
                        <label for="enabled"><b>Enabled:</b></label>
                        <input type="checkbox" name="enabled" value="1" style="margin-right:20px;" id="sensor_enabled"/>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function edit_sensor(id, name, address1, address2, room, notifications, is_alarmSensorHome, is_alarmSensorAway, enabled) {
        	$('#sensor_ID').val(id);
        	$('#sensor_name').val(name);
        	$('#sensor_address').val(address1);
        	$('#sensor_close_address').val(address2);
        						
        	if(notifications=="1"){$('#sensor_notifications').prop("checked", true);}else{$('#sensor_notifications').prop("checked", false);}
        	if(is_alarmSensorHome=="1"){$("#sensor_is_alarmSensorHome").prop("checked", true);}else{$("#sensor_is_alarmSensorHome").prop("checked", false);}
        	if(is_alarmSensorAway=="1"){$("#sensor_is_alarmSensorAway").prop("checked", true);}else{$("#sensor_is_alarmSensorAway").prop("checked", false);}
        	if(enabled=="1"){$('#sensor_enabled').prop("checked", true);}else{$('#sensor_enabled').prop("checked", false);}
        	
        	$('#room').val(room);
        	$('#modal_get_url_address').attr("href","");
        	$('#modal_get_url_close_address').attr("href","");
        }
    </script>
    <!--###################################################################################################### -->
</div>
    <div class="clearfix"></div>
    <?php $deviceOrSensor="Sensor";?>
    <?php include("includes/findSensorModal.php");?>
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
<script>
    setTimeout(function(){
    	$("#find_sensor_autoload").load("Autoload/AutoLoad_FindNewSensor.php");
    },10000);			
</script>
</body>
</html>