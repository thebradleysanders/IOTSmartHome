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
    	include("Autoload/AutoLoad_DeviceData.php");
    	exit;	
    }
    ################################################
    
    #################### API #######################
    $page_title="Manage Devices";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    
    
	//start find device
	if(temp_decode($_POST['type'])=="start_find_sensors"){
		mysqli_query($GS_DBCONN, "UPDATE settings SET scan_for_new_sensors='1' WHERE ID='1'");
	}    
	//end find device
	if(temp_decode($_POST['type'])=="end_find_sensors"){
		mysqli_query($GS_DBCONN, "UPDATE settings SET scan_for_new_sensors='0' WHERE ID='1'");
		
		//clear found sensors list
		mysqli_query($GS_DBCONN, "DELETE FROM find_sensors_list");
	}        
	
	//add new devices
	if(temp_decode($_POST["type"])=="add_device" && $_POST['device_name']!="" && GetUserPermissions("add")==true){
		if($_POST["device_type"]=="phue"){$icon="fa fa-lightbulb-o";}else{$icon="fa fa-power-off";}
		
		//rebuild xml
		$xmlstr = "
			<device>
				<type>".clean_text($_POST['device_type'],50)."</type>
				".$_POST['deviceXML']."
			</device>";

		$insert_query="INSERT INTO devices (device_name,deviceXML,device_state,group_id,device_icon,room,timeout,enable_auto_off,last_off_time,last_on_time,type,tags,enabled)
		VALUES('".clean_text(ucwords(strtolower($_POST['device_name'])),200)."'
		,'".$xmlstr."'
		,'0'
		,'0'
		,'".$icon."'
		,'".(int)clean_text($_POST['room'],11)."'
		,'10'
		,'1'
		,'0'
		,'0'
		,'".clean_text($_POST['device_type'],50)."'
		,'".clean_text($_POST['device_tags'],200)."'
		,'1')";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
		
		//chage hue device name
		if($_POST['device_type']=="phue"){
			$deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>".trim($xmlstr)) or die("Error: Cannot create object");
			$GS_phueService->lights()[(int)$deviceConfig[0]->light]->setName($name);
		}
										
	}
	
	//update devices
	if(temp_decode($_POST['type'])=='update_device' && GetUserPermissions("edit")==true){

		if ($_POST['device_name']!='') {$name = clean_text(ucwords(strtolower($_POST['device_name'])),200);}else{$name = "{No Name}";}
		if($_POST['timeout']=="0"){$enable_auto_off="0";}else{$enable_auto_off="1";}
		
		//rebuild xml
		$xmlstr = "
			<device>
				<type>".clean_text($_POST['device_type'],50)."</type>
				".$_POST['deviceXML']."
			</device>";
		
		$insert_query="UPDATE devices SET 
		device_name='".$name."'
		,deviceXML='".$xmlstr."'
		,room='".(int)clean_text($_POST['room'],11)."'
		,group_id='".(int)clean_text($_POST['group'],11)."'
		,device_icon='".clean_text($_POST['device_icon'],50)."'
		,device_method='".(int)clean_text($_POST['device_method'],2)."'
		,timeout='".clean_text($_POST['timeout'],50)."'
		,enable_auto_off='".(int)clean_text($enable_auto_off,1)."'
		,enabled='".(int)clean_text($_POST['enabled'],1)."'
		WHERE ID='".clean_text($_POST['DeviceID'],11)."'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
		
		//chage hue device name
		if($_POST['device_type']=="phue"){
			$deviceConfig = simplexml_load_string("<?xml version='1.0' standalone='yes'?>".trim($xmlstr)) or die("Error: Cannot create object");
			$GS_phueService->lights()[(int)$deviceConfig[0]->light]->setName($name);
		}
	}

	
	if(temp_decode($_POST['type'])=="AddGroup" && GetUserPermissions("add")==true){
		$insert_query="INSERT INTO device_groups (group_name)VALUES('".clean_text($_POST['group_name'],100)."')";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}
	
	//delete device
	if($_GET['delete']!="" && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		mysqli_query($GS_DBCONN, "DELETE FROM devices WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."'") or die (mysqli_error($GS_DBCONN));
	}
        
?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px;margin-bottom:30px;">
    <h3>
        Manage Devices
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
          $total = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM devices"));
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
            <div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);height:114px;">
	            <div style="text-align:center;font-size:35px;margin-top:-10px;">
	                <i class="fa fa-plus"></i>
	            </div>
	            <div style="">
	                <?php if(GetUserPermissions("add")==true):?>
						<button type="button" class="btn btn-primary" onclick="add();" data-toggle="modal" data-target="#add_device_modal"  style="width:100%;">Add New</button>
	                <?php else:?>
						<button type="button" class="btn btn-primary" disabled title="You do not have permission" style="width:100%;">Add New</button>
	                <?php endif;?>
	            </div>
	        </div>
        </div>
        <?php
            $count = 0;
            $query = "SELECT * FROM devices ORDER BY ID LIMIT ".$limit." OFFSET ".$offset;
            $results = mysqli_query($GS_DBCONN, $query);
            while($result = mysqli_fetch_assoc($results)) { $count++;
        ?>
        <div class="well1 col-xs-12 col-sm-5 col-md-3 col-lg-2 " style="padding:5px;height:120px;min-width:200px;background-color:#f5f5f5;margin-bottom:5px;">
            <div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
	            <p style="width:100%;height:50px;overflow:auto;">
	                <?php echo $result['device_name'];?><br/>
	                <span style="font-size:12px;">Type: <?php echo strtoupper($result['type']);?></span>
	            </p>
	            <div style="text-align:center;">
	                <!-- On Button -->
	                <div style="display:inline-block;">
	                    <!--- Turn On --->
	                    <span class="turnOn_<?php echo $result['ID'];?>" >
							<a href="?type=<?php echo temp_encode("toggle_device");?>&device_id=<?php echo $result['ID'];?>&state=1">
								<button type="button" class="btn btn-default"><i class="fa <?php echo $result['device_icon'];?>"></i></button>
							</a>
	                    </span>
	                </div>
	                <!-- Off Button -->
	                <div style="display:inline-block;">
	                    <!--- Turn Off --->
	                    <span class="turnOff_<?php echo $result['ID'];?>" style="display:none;">
							<a href="?type=<?php echo temp_encode("toggle_device");?>&device_id=<?php echo $result['ID'];?>&state=0">
								<button type="button" class="btn btn-primary"><i class="fa <?php echo $result['device_icon'];?>"></i></button>
							</a>
	                    </span>
	                </div>
	                <div  style="display:inline-block;">
	                    <span class="deviceDisabled_<?php echo $result['ID'];?>" style="display:none;">
							<a href="#"><button style="padding:6px;" class="btn btn-default">Disabled</button></a>
	                    </span>
	                </div>
	                <?php if(GetUserPermissions("edit")==true):?>
						<button data-toggle="modal" data-target="#add_device_modal"  onclick='edit_device(
							"<?php echo $result['ID'];?>",
							"<?php echo $result['device_name'];?>",
							"<?php echo preg_replace('/\s+/', ' ', $result['deviceXML']);?>",
							"<?php echo $result['room'];?>",
							"<?php echo $result['group_id'];?>",
							"<?php echo $result['device_icon'];?>",
							"<?php echo $result['device_method'];?>",
							"<?php echo $result['timeout'];?>",
							"<?php echo $result['type'];?>",
							"<?php echo $result['enabled'];?>");'
							type="button" class="btn btn-default"><i class="fa fa-pencil"></i>
						</button>  							
	                <?php else:?>
						<button type="button" class="btn btn-default" disabled title="You do not have permission"><i class="fa fa-pencil"></i></button>  
	                <?php endif;?>
	                <?php if (GetUserPermissions("delete")==true):?>
						<a onclick="return confirm('Are You Sure You Want To Delete This?');" href="?delete=<?php echo temp_encode($result['ID']);?>&type=<?php echo $_GET['type'];?>">
							<button type="button" class="btn btn-danger">
								<i class="fa fa-trash-o"></i>
							</button>
						</a>  
	                <?php else:?>
						<button type="button" class="btn btn-danger" disabled title="You do not have permission"><i class="fa fa-trash-o"></i></button>
	                <?php endif;?>						
	            </div>
	        </div>
        </div>
        <?php } ?>
    </div>
    <!--########################################### Add Device ############################# -->
    <!-- Modal -->
    <div class="modal fade" id="add_device_modal" role="dialog" style="z-index:9999;margin-top:100px;">
        <div class="modal-dialog" style="width:400px;">
            <!-- Modal content-->
            <form method="Post" id="add_device_frm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="" class="modal-title" id="addEditModalTitle">Add/Edit A Device</h4>
                    </div>
                    <div class="modal-body" style="height:350px;overflow:auto;">
                        <input type="hidden" id="InsertAddSelection" name="type" value="" />
                        <input type="hidden" id="EditDeviceID" name="DeviceID" value="" />
                        <div class="input-group">
                            <span class="input-group-addon"><b>Type</b></span>
                            <select onchange="deviceType();" name="device_type" id="add_device_device_type" class="form-control1" style="height:30px;width:100%;">
                                <option value="">Not Set</option>
                                <?php if($GS_phueServiceEnabled == true):  //Check If Service Is Enabled ?>
									<option value="phue">Phillips Hue</option>
                                <?php endif;?>
                                <?php if($GS_wemoServiceEnabled == true):  //Check If Service Is Enabled ?>
									<option value="wemo">Belkin Wemo</option>
                                <?php endif;?>
                                <?php if($GS_mqttServiceEnabled == true):  //Check If Service Is Enabled ?>
									<option value="mqtt">MQTT</option>
									<option value="mqttNode">Send To Node</option>
                                <?php endif;?>
                            </select>
                        </div>
                        <div style="display:none;" id="device_type_selected">
                            <div class="input-group">
                                <span class="input-group-addon"><b>Device Name</b></span>
                                <input type="text" name="device_name" id="device_name" class="form-control1" style="height:30px;width:100%;" />
                            </div>
                            <!-- MQTT -->
                            <div id="div_mqtt">
                                <label for="device_on_address"><b>Device On Address:</b></label>
                                <input type="text" name="device_on_address" id="device_on_address" class="form-control1" style="height:30px;width:300px;margin-bottom:10px;" />
                                <button class="btn btn-default" style="height:30px;width:45px;" onclick="find_sensor('device_on_address');return false;" title="Find Device">...</button>
                                <label for="device_off_address"><b>Device Off Address:</b></label>
                                <input type="text" name="device_off_address" id="device_off_address" class="form-control1" style="height:30px;width:300px;margin-bottom:10px;" />
                                <button class="btn btn-default" style="height:30px;width:45px;" onclick="find_sensor('device_off_address');return false;" title="Find Device">...</button><br/>
                                <label for="mqtt_topic"><b>MQTT Topic</b></label>
                                <input type="text" id="mqtt_topic" class="form-control1" style="height:30px;width:100%;margin-bottom:10px;" />
                            </div>
                            <!-- Phillips Hue -->
                            <div id="div_phillips_hue">
                                <label for="light_number"><b>Light Bulb:</b></label>
                                <div id="getPhueLightsList"></div>
                                <!-- loads lights from AUtoload_phueLightslist.php -->
                                <div class="input-group">
                                    <span class="input-group-addon"><b>Brightness</b></span>
                                    <select id="device_on_brightness" class="form-control1" style="height:30px;width:100%;">
                                        <option value="0">0%</option>
                                        <option value="25">10%</option>
                                        <option value="51">20%</option>
                                        <option value="76">30%</option>
                                        <option value="102">40%</option>
                                        <option value="127">50%</option>
                                        <option value="153">60%</option>
                                        <option value="178">70%</option>
                                        <option value="204">80%</option>
                                        <option value="229">90%</option>
                                        <option value="254">100%</option>
                                    </select>
                                </div>
                            </div>
                            <!-- WEMO -->
                            <div id="div_wemo">
                                <label for="light_number"><b>Wemo IP Address:</b></label>
                                <input type="text" id="wemo_ip_address" class="form-control1" style="height:30px;width:70%;margin-bottom:10px;" placeholder="IP Address"/>
                                <input type="text" value="49153"  id="wemo_port" class="form-control1" style="height:30px;width:28%;margin-bottom:10px;" placeholder="Port (49153)"/>
                            </div>
                            <!-- Send to MQTT Node -->
                            <div id="div_mqtt_node" style="display:none;">
                                <label><b>Select a Node:</b></label>
                                <ul style="list-style:none;padding:0px;">
                                    <?php
                                        $query = "SELECT * FROM iot_nodes WHERE enabled='1' AND node_type='Transmit' ORDER BY ID ASC";
                                        $nodes = mysqli_query($GS_DBCONN, $query);
                                        while($node = mysqli_fetch_assoc($nodes)){
                                    ?>
										<li style="margin:5px;font-size:12px;display:inline-block;">
											<input type="checkbox" value="<?php echo $node['ID'];?>" class="sendToNodeCheckbox" id="SendToNode_<?php echo $node['ID'];?>"/>
											&nbsp;<?php echo $node['node_id'];?>
										</li>
                                    <?php }?>
                                </ul>
                                <label><b>Device On Address:</b></label>
                                <input type="text" id="ToNode_device_on_address" class="form-control1" style="height:30px;width:300px;margin-bottom:10px;" />
                                <button class="btn btn-default" style="height:30px;width:45px;" onclick="find_sensor('ToNode_device_on_address');return false;" title="Find Device">...</button>
                                <label><b>Device Off Address:</b></label>
                                <input type="text" id="ToNode_device_off_address" class="form-control1" style="height:30px;width:300px;margin-bottom:10px;" />
                                <button class="btn btn-default" style="height:30px;width:45px;" onclick="find_sensor('ToNode_device_off_address');return false;" title="Find Device">...</button><br/>
                            </div>
                            <div style="" id="EditDeviceDiv">
                                <div class="input-group">
                                    <span class="input-group-addon"><b>Room</b></span>
                                    <select name="room" id="device_room" style="height:30px;width:100%;" class="form-control1">
                                        <option value="0">No Room</option>
                                        <?php
                                            $query = "SELECT * FROM home_rooms";
                                            $results = mysqli_query($GS_DBCONN, $query);
                                            while($result = mysqli_fetch_assoc($results)) { 
										?>
											<option value="<?php echo $result['ID'];?>"><?php echo $result['room_name'];?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><b>Group</b></span>
                                    <select onchange="add_new_group();" name="group" id="device_group" style="height:30px;width:100%;" class="form-control1">
                                        <option value="0">No Group</option>
                                        <option value="Add Group" >Add New</option>
                                        <?php
                                            $query = "SELECT * FROM device_groups";
                                            $results = mysqli_query($GS_DBCONN, $query);
                                            while($result = mysqli_fetch_assoc($results)) { 
										?>
											<option value="<?php echo $result['ID'];?>"><?php echo $result['group_name'];?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><b>Method</b></span>
                                    <select name="device_method" id="device_method" style="height:30px;width:100%;" class="form-control1">
                                        <option value="1">On/Off</option>
                                        <option value="2">Unlock/Lock</option>
                                        <option value="3">Open/Close</option>
                                        <option value="4">Arm/Disarm</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><b>Icon</b></span>
                                    <input type="text" name="device_icon" value="" id="device_icon" style="height:30px;width:100%;" class="form-control1"/>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><b>Timeout: (Minutes)</b></span>
                                    <input type="number" name="timeout" value="" id="device_timeout" style="height:30px;width:100%;" class="form-control1"/>
                                </div>
                                <label for="enabled"><b>Enabled:</b></label>
                                <input type="checkbox" name="enabled" value="1" style="margin-right:20px;" id="device_enabled"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="deviceXML" id="deviceXML"/>
                        <input type="hidden" name="device_tags" id="device_tags"/>
                        <button type="button" onclick="saveDevice();" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        var editAdd="";
        function add(){
        	editAdd="add";
        	$("#EditDeviceDiv").hide();
        	$("#addEditModalTitle").text("Add a Device");
        	$("#InsertAddSelection").val("<?php echo temp_encode("add_device");?>");
        	
        	 /**** Clear out all feilds ***/
        	 $('#device_name').val("");
        	 $("#add_device_device_type").val("");
            /*phue*/
            $("#light_number").val("");
            $("#device_on_brightness").val("");
            /*MqttNode*/
            $("#ToNode_device_on_address").val("");
            $("#ToNode_device_off_address").val("");
            $("#MQTTSelectedNode").val("");
            /*wemo*/
            $("#wemo_ip_address").val("");
            $("#wemo_port").val("49153");
            /*mqtt*/
            $("#mqtt_topic").val("");
            $("#device_on_address").val("");
            $("#device_off_address").val("");
        	
        	/*** Hide all services ***/
        	$("#device_type_selected").hide();
        	$("#div_wemo").hide();
        	$("#div_phillips_hue").hide();
        	$("#div_mqtt").hide();
        	$("#div_mqtt_node").hide()
        	$("#EditDeviceDiv").hide();
        }
        
        function deviceType(){
          $("#device_type_selected").show();
          $("#div_wemo").hide();
          $("#div_phillips_hue").hide();
          $("#div_mqtt").hide();
          $("#div_mqtt_node").hide()
          $("#EditDeviceDiv").hide();
          
          if(editAdd=="edit"){$("#EditDeviceDiv").show();}
        	
          if($("#add_device_device_type").val()=="phue"){			
        	  $("#div_phillips_hue").show();
        	  $("#device_on_brightness").val("254");
        	  <?php if ($GS_phueServiceEnabled == true) : //Check If Service Is Enabled ?>
        		$('#device_name').show(); $("#device_nameLabel").show();
        	  <?php else:?>
        		$('#device_name').hide(); $("#device_nameLabel").hide();
        	  <?php endif;?>
           }else if($("#add_device_device_type").val()=="mqtt"){
        	  $("#div_mqtt").show();
        	  <?php if ($GS_mqttServiceEnabled == true) : //Check If Service Is Enabled ?>
        		$('#device_name').show(); $("#device_nameLabel").show();
        	  <?php else:?>
        		$('#device_name').hide(); $("#device_nameLabel").hide();
        	  <?php endif;?>
           }else if($("#add_device_device_type").val()=="wemo"){
        	   $("#div_wemo").show();
        	   <?php if ($GS_wemoServiceEnabled == true) : //Check If Service Is Enabled ?>
        		$('#device_name').show(); $("#device_nameLabel").show();
        	  <?php else:?>
        		$('#device_name').hide(); $("#device_nameLabel").hide();
        	  <?php endif;?>
           }else if($("#add_device_device_type").val()=="mqttNode"){
        	   $("#div_mqtt_node").show()
        	  <?php if ($GS_mqttServiceEnabled == true) : //Check If Service Is Enabled ?>
        		$('#device_name').show(); $("#device_nameLabel").show();
        	  <?php else:?>
        		$('#device_name').hide(); $("#device_nameLabel").hide();
        	  <?php endif;?>
           }
        }
        
        function saveDevice(){
           if($("#add_device_device_type").val()=="phue"){ /* Phillips Hue */
        		$("#deviceXML").val("<light>" + $("#light_number").val().split("|")[0] + "</light><brightness>254</brightness>");
        		$("#device_tags").val("Dimmable");
        		if($("#light_number").val().split("|")[1].indexOf("color") >= 0){
        			$("#device_tags").val("Dimmable Color");
        		}else{
        			$("#device_tags").val("Dimmable");
        		}
        	 
           }else if($("#add_device_device_type").val()=="wemo"){ /* Belkin Wemo */
        	 $("#deviceXML").val("<ip>" + $("#wemo_ip_address").val() + "</ip><port>" + $("#wemo_port").val() + "</port>");
        	 $("#device_tags").val("");
           }else if($("#add_device_device_type").val()=="mqtt"){ /* MQTT */
        	 $("#deviceXML").val("<onID>" + $("#device_on_address").val() + "</onID>" + "<offID>" + $("#device_off_address").val() + "</offID><mqttTopic>" + $("#mqtt_topic").val() + "</mqttTopic>");
        	 $("#device_tags").val("");
           }else if($("#add_device_device_type").val()=="mqttNode"){ /* MQTTNode */
         	<?php
            $count=0;
            $query = "SELECT * FROM iot_nodes WHERE enabled='1' AND node_type='Transmit' ORDER BY ID ASC";
            $nodes = mysqli_query($GS_DBCONN, $query);
            while($node = mysqli_fetch_assoc($nodes)){$count++;
            	if($count==1):?>var SendToNodeList="";<?php endif;
            ?>
        		if($("#SendToNode_<?php echo $node['ID'];?>").prop("checked")==true){
        			SendToNodeList = SendToNodeList + "<node><id>" + $("#SendToNode_<?php echo $node['ID'];?>").val() +"</id></node>";
        		}
        	<?php }?>
        	$("#deviceXML").val("<nodes>" + SendToNodeList + "</nodes><onID>" + $("#ToNode_device_on_address").val() + "</onID><offID>" + $("#ToNode_device_off_address").val() + "</offID>");
        	$("#device_tags").val("");
         }
          $("#add_device_frm").submit();
        }
        
        
        function edit_device(id, name, xml, room, group, icon, method, timeout, type, enabled) {
        	var  parser, xmlDoc;
        	parser = new DOMParser();
        	xmlDoc = parser.parseFromString(xml,"text/xml");
        		
        	editAdd="edit";
        	$("#addEditModalTitle").text("Edit");
        	$('#EditDeviceID').val(id);
        	$('#device_name').val(name);	
        	$('#device_room').val(room);
        	$('#device_group').val(group);
        	$('#device_icon').val(icon);
        	$('#device_method').val(method);
        	$('#device_timeout').val(timeout);
        	if(enabled=="1"){$('#device_enabled').prop("checked", true);}else{$('#device_enabled').prop("checked", false);}
        				
        	$("#device_type_selected").show();
        	$("#div_wemo").hide();
        	$("#div_phillips_hue").hide();
        	$("#div_mqtt").hide();
        	$("#div_mqtt_node").hide();
        	$("#EditDeviceDiv").show();
        	
           if(type=="phue"){
        	  $("#div_phillips_hue").show();
        	  $("#add_device_device_type").val("phue");
        	  $("#light_number").val(xmlDoc.getElementsByTagName("light")[0].childNodes[0].nodeValue);
        	  $("#device_on_brightness").val(xmlDoc.getElementsByTagName("brightness")[0].childNodes[0].nodeValue);
           }else if(type=="mqtt"){
        	  $("#div_mqtt").show();
        	  $("#add_device_device_type").val("mqtt");
        	  $("#mqtt_topic").val(xmlDoc.getElementsByTagName("mqttTopic")[0].childNodes[0].nodeValue);
        	  $("#device_on_address").val(xmlDoc.getElementsByTagName("onID")[0].childNodes[0].nodeValue);
        	  $("#device_off_address").val(xmlDoc.getElementsByTagName("offID")[0].childNodes[0].nodeValue);
           }else if(type=="wemo"){
        	   $("#div_wemo").show();
        	   $("#add_device_device_type").val("wemo");
        	   $("#wemo_ip_address").val(xmlDoc.getElementsByTagName("ip")[0].childNodes[0].nodeValue);
        	   $("#wemo_port").val(xmlDoc.getElementsByTagName("port")[0].childNodes[0].nodeValue);
           }else if(type=="mqttNode"){
	           $(".sendToNodeCheckbox").prop("checked", false);
        	   $("#div_mqtt_node").show();
        	   $("#add_device_device_type").val("mqttNode");
        	   $("#ToNode_device_on_address").val(xmlDoc.getElementsByTagName("onID")[0].childNodes[0].nodeValue);
        	   $("#ToNode_device_off_address").val(xmlDoc.getElementsByTagName("offID")[0].childNodes[0].nodeValue);
        	   
        	   //foreach node in xml check the checkboxes
        		var nodes =  xmlDoc.getElementsByTagName("id");
        		for (var i = 0; i < nodes.length; i++) {   
        			var node = nodes[i].childNodes[0].nodeValue;
        			$("#SendToNode_" + node).prop("checked",true);
        		}   
           }
           
           /*Tell php we want to update the table*/
           $("#InsertAddSelection").val("<?php echo temp_encode("update_device");?>");
           
        }
        
    </script>
    <!--############################################ Add Group ############################## -->
    <a href="#" data-toggle="modal" id="showAddGroupModal" data-target="#addNewGroup_modal" style="display:none;">
        <!--- HIDDEN -->
    </a>
    <!-- Modal -->
    <div class="modal fade" id="addNewGroup_modal" role="dialog" style="z-index:9999;margin-top:200px;">
        <div class="modal-dialog" style="width:500px;">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 style="" class="modal-title">Add New Group</h4>
                </div>
                <div class="modal-body">
                    <form method="Post" class="autoform" onsubmit="saveAddedGroup();">
                        <label>Group Name:</label>
                        <input type="hidden" name="type" value="<?php echo temp_encode("AddGroup");?>"/>
                        <input type="text" name="group_name" value="" id="AddNewGroupNameValue" style="width:40%;margin-right:2%;height:30px;" class="form-control1"/>
                        <input type="submit" value="Save" class="btn btn-primary" style="width:17%;" />
                        <input data-dismiss="modal" type="button" value="Cancel" class="btn btn-default" style="width:17%;"/>
                        <a href="#" data-dismiss="modal" style="display:none" id="hideAddGroupModalOnSave">
                            <!-- HIDDEN -->
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function add_new_group(){
        	if($('#device_group').val()=="Add Group"){
        		$('#AddNewGroupNameValue').val("");
        		$('#showAddGroupModal').click();
        	}
        }
        					
        function saveAddedGroup(){
        	$('#hideAddGroupModalOnSave').click();
        	$('#device_group').append($("<option></option>").attr("value",$('#AddNewGroupNameValue').val()).text($('#AddNewGroupNameValue').val())); 
        	$('#device_group').val($('#AddNewGroupNameValue').val());
        }
    </script>	
</div>
    <div class="clearfix"> </div>
    <?php $deviceOrSensor="Device";?>
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
<script>
	$(document).ready(function(){
		setInterval(function(){
	    	$("#find_sensor_autoload").load("Autoload/AutoLoad_FindNewSensor.php");
    		//$("#getPhueLightsList").load("Autoload/Autoload_phueLightsList.php");
		},1000);	
	});
    		
</script>
</body>
</html>