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
    $page_title="Manage Data Sensors";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    
	//edit sensor
	if(temp_decode($_POST['type'])=="edit_sensor_modal" && GetUserPermissions("edit")==true){
		mysqli_query($GS_DBCONN, "UPDATE data_sensors SET 
		sensor_nicename='".clean_text($_POST['sensor_name'],50)."',
		room ='".(int)clean_text($_POST['sensor_room'],11)."',
		enabled='".(int)clean_text($_POST['enabled'],1)."' 
		WHERE ID='".clean_text($_POST['sensor_ID'],11)."'") or die (mysqli_error($GS_DBCONN));
	}
	
	//delete sensor
	if($_GET['delete']!="" && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		mysqli_query($GS_DBCONN, "DELETE FROM data_sensors WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."'") or die (mysqli_error($GS_DBCONN));
	}
    
   ?>
<div class="col-md-12" style="margin-bottom:30px;">
    <h3>
        Manage Data Sensors
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
         $total = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM data_sensors"));
        // How many items to list per page
        $limit = 25;
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
        <?php
            $count = -1;
            $query = "SELECT * FROM data_sensors ORDER BY sensor_nicename ASC LIMIT ".$limit." OFFSET ".$offset;
            $results = mysqli_query($GS_DBCONN, $query);
            while($sensor = mysqli_fetch_assoc($results)) { $count++;
				$TitleArray = explode(":",$sensor['sensor_dataTitle_array']);
				$ValueArray = explode(":",$sensor['sensor_dataValue_array']);
				$VisibleArray = explode(":",$sensor['sensor_dataVisible_array']);
        ?>
        <div class="well1 col-xs-12 col-sm-5 col-md-4 col-lg-3 " style="padding:5px;height:300px;min-width:300px;background-color:#f5f5f5;margin-bottom:10px;">
            <div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);height:310px;width:100%;">
	            <p style="width:100%;overflow:auto;">
	                <div style="font-weight:bold;"><?php echo ucwords($sensor['sensor_nicename']);?></div>
	                
					<span style="font-size:12px;">Type: <?php echo strtoupper($sensor['sensor_type']);?></span>
					<span style="font-size:12px;float:right;"><?php if($sensor['last_update']!=""){echo ago(date('Y/m/d H:i:s', $sensor['last_update']));}else{echo "";}?></span>
	            </p>
	      
				<ul class="list-unstyled" style="overflow:auto;height:200px;">
					<?php 
						$count=-1;
						foreach($TitleArray as $title){ $count++;
					?>
						<li style="padding:0px;margin-bottom:4px;height:35px;border:1px solid #e8e8e8;width:100%;overflow:auto;">
							<div style="border:none;background-color:#fff;color:#337ab7;font-size:14px;line-height:32px;display:inline-block;padding-left:3px;">
								<span class="small-font" style='color:#333;font-size:14px;'><?php echo $TitleArray[$count];?>:</span>
							</div>
							
							<div class='text-success pull-right' style='min-width:35%;display:inlin-block;font-size:14px;text-align:center;color:#fff;background-color:silver;padding:4px;line-height:28px;height:33px;'> <?php echo $ValueArray[$count];?></div>
						</li>
					<?php }?>
				</ul>
				<div style="height:30px;text-align:center;">
					<?php if (GetUserPermissions("edit")==true):?>					
						<button type="button" class="btn btn-default" style="margin-right:10px;" data-toggle="modal" data-target="#sensor_edit_modal"
						onclick="edit_sensor('<?php echo $sensor['ID'];?>', '<?php echo $sensor['sensor_nicename'];?>', '<?php echo $sensor['room'];?>', '<?php echo $sensor['enabled'];?>');">
							<i class="fa fa-pencil"></i>
						</button>
					<?php else:?>
						<button type="button" class="btn btn-default" style="margin-right:10px;" disabled title="You do not have permission">
							<i class="fa fa-pencil"></i>
						</button>
					<?php endif;?>
					
					 <?php if (GetUserPermissions("delete")==true):?>
						<a href="?delete=<?php echo temp_encode($sensor['ID']);?>">
							<button type="button" class="btn btn-danger" onclick="return confirm('Are You Sure You Want To Delete This Sensor?');">
								<i class="fa fa-trash-o"></i>
							</button>
						</a>
					<?php else:?>
						<button type="button" class="btn btn-danger" disabled title="You do not have permission">
							<i class="fa fa-trash-o"></i>
						</button>
					<?php endif;?>
				</div>
	        </div>
        </div>
        <?php } ?>
    </div>
	
	 <!--########################################### Sensor Edit Modal ############################# -->
    <!-- Modal -->
    <div class="modal fade" id="sensor_edit_modal" role="dialog" style="z-index:9999;margin-top:100px;">
        <div class="modal-dialog" style="width:400px;">
            <!-- Modal content-->
            <form method="Post">
                <div class="modal-content" style="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 style="" class="modal-title">Edit Sensor</h4>
                    </div>
                    <div class="modal-body" style="height:150px;overflow:auto;">
                        <input type="hidden" name="type" value="<?php echo temp_encode("edit_sensor_modal");?>" />
                        <input type="hidden" name="sensor_ID" value="" id="sensor_ID" />
                        <div class="input-group">
                            <span class="input-group-addon"><b>Sensor Name</b></span>
                            <input type="text" name="sensor_name" value="" id="sensor_name" style="width:100%;height:30px;" class="form-control1"/>
                        </div>
						<div class="input-group">
							<span class="input-group-addon"><b>Room</b></span>
							<select name="sensor_room" id="sensor_room" style="height:30px;width:100%;" class="form-control1">
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

                        <label for="enabled"><b>Enabled:</b></label>&nbsp;&nbsp;
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
        function edit_sensor(id, name, room, enabled) {
        	$('#sensor_ID').val(id);
        	$('#sensor_name').val(name);
        	if(enabled=="1"){$('#sensor_enabled').prop("checked", true);}else{$('#sensor_enabled').prop("checked", false);}
        	$('#sensor_room').val(room);
        }
    </script>
    <!--###################################################################################################### -->


</div>
    <div class="clearfix"> </div>
    <?php $deviceOrSensor="Sensor";?>
    <?php include("includes/findSensorModal.php");?>
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
<script>
    setTimeout(function(){
    	$("#getPhueLightsList").load("Autoload/Autoload_phueLightsList.php");
    	$("#find_sensor_autoload").load("Autoload/AutoLoad_FindNewSensor.php");
    },10000);			
</script>
</body>
</html>