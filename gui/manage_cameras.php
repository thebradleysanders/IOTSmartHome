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
    $page_title="Manage Cameras";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    
	//update camera
	if(temp_decode($_POST['type'])=='update_camera' && GetUserPermissions("edit")==true){
		$insert_query="UPDATE camera_list SET 
		 camera_name='".clean_text(ucwords($_POST['camera_name']),200)."'
		,ip_address='".$_POST['ip_address']."'
		,sensor_assign='".(int)clean_text($_POST['sensor_assign'],11)."'
		,click_trigger='".clean_text($_POST['click_event'],500)."'
		,alert_color='".clean_text($_POST['alert_color'],10)."'
		,room='".(int)clean_text($_POST['room'],11)."'
		,enabled='".(int)clean_text($_POST['enabled'],1)."'
		WHERE ID='".$_POST['ID']."'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}
	
	//add new camera
	if(temp_decode($_POST['type'])=='add_camera' && GetUserPermissions("add")==true){  

		$insert_query="INSERT INTO camera_list ( camera_name,ip_address,sensor_assign,click_trigger,alert_color,room,enabled)
			VALUES(
			'".clean_text(ucwords($_POST['camera_name']),200)."',
			'".$_POST['ip_address']."',
			'".(int)clean_text($_POST['sensor_assign'],11)."',
			'".clean_text($_POST['click_event'],500)."',
			'".clean_text($_POST['alert_color'],10)."',
			'".(int)clean_text($_POST['room'],11)."',
			'1'
			)";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}
	
	//delete camera
	if($_GET['delete']!="" && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		$insert_query="DELETE FROM camera_list WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}
    
    ?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px;margin-bottom:30px;">
    <h3>
        Manage Cameras
        <div style="overflow:auto;text-align:right;">
            <a href="manage_sensors.php?type=<?php echo temp_encode("motion");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Motion</button></a>
            <a href="manage_sensors.php?type=<?php echo temp_encode("door");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Doors</button></a>
            <a href="manage_sensors.php?type=<?php echo temp_encode("window");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Windows</button></a>
            <a href="manage_sensors.php?type=<?php echo temp_encode("custom");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Custom</button></a>
            <a href="manage_devices.php"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Devices</button></a>
        </div>
    </h3>
    <?php		
        // Find out how many items are in the table
           $total = mysqli_num_rows(mysqli_query($GS_DBCONN, 'SELECT * FROM  camera_list'));
        // How many items to list per page
        $limit = 30;
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
    <div style="margin-top:10px;">
        <div style="max-width:220px;width:45%;height:200px;background-color:#fff;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);display:inline-block;margin-right:10px;margin-bottom:15px;padding:10px;">
            <div style="width:100%;height:150px;text-align:center;padding-top:40px;">
                <i class="fa fa-plus" style="font-size:60px;"></i>
            </div>
            <div style="text-align:center;">
                <?php if(GetUserPermissions("add")==true):?>
					<button class="btn btn-primary" style="width:100%;" data-toggle="modal" data-target="#addNewCamera">Add New</button>
                <?php else :?>
					<button class="btn btn-primary" style="width:100%;" disabled>Add New</button>
                <?php endif;?>
            </div>
        </div>
        <!--########################################### Add New Camera Modal ############################# -->
        <!-- Modal -->
        <div class="modal fade" id="addNewCamera" role="dialog" style="z-index:9999;margin-top:100px;">
            <div class="modal-dialog" style="width:500px;">
                <!-- Modal content-->
                <form method="Post" style="padding:10px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 style="" class="modal-title">Add New Camera</h4>
                        </div>
                        <div class="modal-body" style="height:400px;overflow:auto;">
                            <input type="hidden" name="type" value="<?php echo temp_encode("add_camera");?>"/>
                            <div class="input-group">
                                <span class="input-group-addon"><b>Camera Name</b></span>
                                <input class="form-control1" type="text" name="camera_name" value="" style="width:100%;height:30px;"/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><b>URL</b></span>
                                <input class="form-control1" type="text" name="ip_address" value="" style="width:100%;height:30px;"/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><b>Alert Color</b></span>
                                <input class="form-control1" type="text" name="alert_color" value="" style="width:100%;height:30px;"/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><b>Assigned Sensor:</b></span>
                                <select name="sensor_assign" style="width:100%;height:30px;" class="form-control1">
                                    <?php 
                                        $query = "SELECT * FROM sensors ORDER BY ID ASC";
                                        $sensors = mysqli_query($GS_DBCONN, $query);
                                        while($sensor_list = mysqli_fetch_assoc($sensors)){
									?>
										<option value="<?php echo $sensor_list['ID'];?>"><?php echo $sensor_list['sensor_name'];?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon"><b>Room</b></span>
                                <select name="room" style="width:100%;height:30px;" id="room<?php echo $result['ID'];?>" class="form-control1">
                                    <option value="">Not Set</option>
                                    <?php 
                                        $query = "SELECT * FROM home_rooms ORDER BY ID ASC";
                                        $rooms = mysqli_query($GS_DBCONN, $query);
                                        while($room_list = mysqli_fetch_assoc($rooms)){
									?>
										<option value="<?php echo $room_list['ID'];?>"><?php echo $room_list['room_name'];?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <label for="" style="display:block;"><b>Click Event:</b></label>
                            <select onchange="show_that_modal('NewCamera',$(this).val());" class="form-control1 CameraClickEvent click_event_newCamera" name="click_event" style="width:100%;margin-bottom:10px;height:30px;" id="then_thatNewCamera">
                                <!-- Options are added here by jquery in iftttOptions.php -->
                            </select>
                        </div>
                        <div class="modal-footer">
                            <div style="float:left;width:230px;text-align:left;font-size:12px;">
                                <p style="color:red;font-family:Arial;"><b>Note:</b> You Will Need To Add Permissions To This Camera After Adding it.</p>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!---------------------------------- CAMERA LISTINGS ---------------------------------------->
        <?php
            $count = 0;
            $query = "SELECT * FROM camera_list ORDER BY ID LIMIT ".$limit." OFFSET ".$offset;
            $results = mysqli_query($GS_DBCONN, $query);
            while($result = mysqli_fetch_assoc($results)) { $count++;   
            	//check permissions
            	if(GetUserPermissions($result['ID'])==false){continue;} 
            	$url=$result['ip_address'];
        ?>
			<div style="max-width:220px;width:45%;height:200px;background-color:#fff;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);display:inline-block;margin-right:10px;margin-bottom:15px;padding:10px;">
				<?php if(GetUserPermissions("edit")==true):?>
					<a href="#" data-toggle="modal" data-target="#camerasSettings_<?php echo $result['ID'];?>" style="background-color:#fff;width:30px;padding:5px;position:absolute;border-radius:4px;text-align:center;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin-top:-20px;margin-left:-20px;"><i class="fa fa-cog"></i></a>
				<?php endif;?>
				<?php if($result['enabled']=="1"):?>
					<a href="#" data-toggle="modal" data-target="#CameraModal_alerts" style="color:#fff;" onclick="showCameraModal<?php echo $result['ID'];?>();">
						<img src="<?php echo $url;?>" title="<?php echo $result['camera_name'];?>" style="height:150px;width:100%;"/>
					</a>
				<?php else :?>
					<img src="images/camera_disabled.jpg" style="width:100%;height:150px;"/>
				<?php endif;?>
				<div style="display:inline-block;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);width:100%;height:30px;padding:4px;">
					<?php echo $result['camera_name'];?>
				</div>
			</div>
			<script>
				function showmodal<?php echo $result['ID'];?>() {
					$('#modal_bkg_<?php echo $result['ID'];?>').fadeIn();
					$('#modal_settings_<?php echo $result['ID'];?>').fadeIn();
				}
				function hidemodal<?php echo $result['ID'];?>() {
					$('#modal_bkg_<?php echo $result['ID'];?>').fadeOut();
					$('#modal_settings_<?php echo $result['ID'];?>').fadeOut();
				}
			</script>
			<!--########################################### Edit Camera Modal ############################# -->
			<!-- Modal -->
			<div class="modal fade" id="camerasSettings_<?php echo $result['ID'];?>" role="dialog" style="z-index:9999;margin-top:100px;">
				<div class="modal-dialog" style="width:500px;">
					<!-- Modal content-->
					<form method="Post" style="padding:10px;">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 style="" class="modal-title">Edit <?php echo $result['camera_name'];?></h4>
							</div>
							<div class="modal-body" style="height:400px;overflow:auto;">
								<input type="hidden" name="type" value="<?php echo temp_encode("update_camera");?>"/>
								<input type="hidden" name="ID" value="<?php echo $result['ID'];?>"/>
								<div class="input-group">
									<span class="input-group-addon"><b>Camera Name</b></span>
									<input class="form-control1" type="text" name="camera_name" value="<?php echo $result['camera_name'];?>" style="height:30px;width:100%;"/>
								</div>
								<div class="input-group">
									<span class="input-group-addon"><b>URL</b></span>
									<input class="form-control1" type="text" name="ip_address" value="<?php echo $result['ip_address'];?>" style="height:30px;width:100%;"/>
								</div>
								<div class="input-group">
									<span class="input-group-addon"><b>Alert Color</b></span>
									<input class="form-control1" type="text" name="alert_color" value="<?php echo $result['alert_color'];?>" style="width:100%;height:30px;border: 1px solid <?php echo $result['alert_color'];?>" onchange="$(this).css({'border-color':$(this).val()});"/>
								</div>
								<div class="input-group">
									<span class="input-group-addon"><b>Assigned Sensor</b></span>
									<select name="sensor_assign" style="width:100%;height:30px;"id="sensor_assign<?php echo $result['ID'];?>" class="form-control1">
										<?php 
											$query = "SELECT * FROM sensors ORDER BY ID ASC";
											$sensors = mysqli_query($GS_DBCONN, $query);
											while($sensor_list = mysqli_fetch_assoc($sensors)){
										?>
											<option value="<?php echo $sensor_list['ID'];?>"><?php echo $sensor_list['sensor_name'];?></option>
										<?php }?>
									</select>
								</div>
								<div class="input-group">
									<span class="input-group-addon"><b>Room</b></span>
									<select name="room" style="width:100%;height:30px;" id="room<?php echo $result['ID'];?>" class="form-control1">
										<option value="">Not Set</option>
										<?php 
											$query = "SELECT * FROM home_rooms ORDER BY ID ASC";
											$rooms = mysqli_query($GS_DBCONN, $query);
											while($room_list = mysqli_fetch_assoc($rooms)){
										?>
											<option value="<?php echo $room_list['ID'];?>"><?php echo $room_list['room_name'];?></option>
										<?php }?>
									</select>
								</div>
								<label for="" style="display:block;"><b>Click Event:</b></label>
								<select onchange="show_that_modal('Camera<?php echo $result['ID'];?>',$(this).val());" class="form-control1 CameraClickEvent click_event<?php echo $result['ID'];?>" name="click_event" style="height:30px;width:100%;margin-bottom:10px;" id="then_thatCamera<?php echo $result['ID'];?>">
									<!-- Options are added here by jquery in iftttOptions.php -->
								</select>
								<label for="" style=""><b>Enabled:</b></label>
								<input type="checkbox" name="enabled" value="1" id="enabled<?php echo $result['ID'];?>" style="display:inline;margin-bottom:10px;margin-left:10px;margin-right:20px;"/>
								<script>
									$(document).ready(function(){
										<?php if ($result['enabled']=='1') :?>
											$('#enabled<?php echo $result['ID'];?>').prop("checked", "true");
										<?php endif;?>
										<?php if ($result['featured']=='1') :?>
											$('#featured<?php echo $result['ID'];?>').prop("checked", "true");
										<?php endif;?>
										$('#room<?php echo $result['ID'];?>').val('<?php echo $result['room'];?>');
										$('#sensor_assign<?php echo $result['ID'];?>').val("<?php echo $result['sensor_assign'];?>");
										$('.click_event<?php echo $result['ID'];?>').append($("<option></option>").attr("value","<?php echo trim($result['click_trigger']);?>").text(removeIfttTags("<THEN>","</THEN>",6,"<?php echo trim($result['click_trigger']);?>")));
										$('.click_event<?php echo $result['ID'];?>').val("<?php echo trim($result['click_trigger']);?>");
									});
								</script>
							</div>
							<div class="modal-footer">
								<?php if(GetUserPermissions("delete")==true) :?>
								<a href="?delete=<?php echo temp_encode($result['ID']);?>" style="float:left;" onclick="return confirm('Are You Sure You Want to Delete This Camera?');">
								<button type="button" class="btn btn-danger"><i class="fa fa-trash-o"></i> Delete</button>
								</a>
								<?php endif;?>
								<button type="submit" class="btn btn-primary">Save</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</form>
				</div>
			</div>
        <?php }?>
    </div>
    <script>
        function edit_sensor(id, name, address1, address2, room) {
        
        	$('#sensor_ID').val(id);
        	$('#sensor_name').val(name);
        	$('#sensor_address').val(address1);
        	$('#sensor_close_address').val(address2);
        	$('#room').val(room);
        	$('#edit_sensor_modal_container').fadeIn();
        	$('#sensor_edit_modal').fadeIn();
        }
        
        $(document).ready(function(){
        	thenThatList(".CameraClickEvent");
        });
    </script>
    <?php include("includes/iftttOptions.php");//ifttt modals and options ?>
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
</body>
</html>