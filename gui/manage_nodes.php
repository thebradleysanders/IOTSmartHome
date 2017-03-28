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
    $page_title="Manage Nodes";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    
    
    	if(temp_decode($_POST['type'])=='update_nodes' ){
    		$query = "SELECT * FROM iot_nodes";
    		$nodes = mysqli_query($GS_DBCONN, $query);
    		$node_max = mysqli_num_rows($nodes)  +1;
    		
    		$node_count=1;
    		While($node_count <$node_max ){
    			$insert_query="UPDATE iot_nodes SET 
    			 room='".clean_text($_POST['room'.$node_count],100)."',
    			 enabled='".(int)clean_text($_POST['enabled'.$node_count],1)."',
    			 error_timeout='".(int)clean_text($_POST['timeout'.$node_count],4)."',
    			 notifications='".(int)clean_text($_POST['notify'.$node_count],1)."',
    			 decription='".clean_text($_POST['decription'.$node_count],200)."'
    			 WHERE ID='".$_POST['ID'.$node_count]."'";
    			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    			$node_count++;
    		}
    	}
    	 
    	if($_GET['delete']!="" && temp_decode($_GET['delete'])!="[EXPIRED]" ){
    		$delete= preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', substr(temp_decode($_GET['delete']),0,11));
    		$insert_query="DELETE FROM iot_nodes WHERE ID='".$delete."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    
    ?>
<div class="col-md-12" style="margin-bottom:30px;">
    <h3>Manage Nodes</h3>
    <div class="bs-example4" data-example-id="contextual-table" style="overflow:auto;margin-bottom:20px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
        <form method="POST" class="autoform_na">
            <input value="<?php echo temp_encode("update_nodes");?>" name="type" type="hidden" />
            <button type="button" onclick="edit_mode()" class="edit_mode_off btn btn-primary"><i class="fa fa-pencil"></i></button>
            <button type="submit" class="edit_mode btn btn-danger" style="display:none;">Save Changes</button>
            <div style="overflow:auto;height:40px;display:inline-block;float:right;">
                <a href="manage_sensors.php?type=<?php echo temp_encode("motion");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Motion</button></a>
                <a href="manage_sensors.php?type=<?php echo temp_encode("door");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Doors</button></a>
                <a href="manage_sensors.php?type=<?php echo temp_encode("window");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Windows</button></a>
                <a href="manage_sensors.php?type=<?php echo temp_encode("custom");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Custom</button></a>
                <a href="manage_devices.php"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Devices</button></a>
            </div>
            <script>
                var editMode = 0;
                
                function edit_mode() {
                	if (editMode == 0) {
                		editMode = 1;
                		$('.edit_mode_off').hide();
                		$('.edit_mode').show();
                	} else {
                		editMode = 0;
                		$('.edit_mode').hide();
                		$('.edit_mode_off').show();
                	}
                }
            </script>
            <style>
                table {  table-layout: fixed;  }
                table td {  overflow: hidden; }
            </style>
            <table class="table">
                <col width="120">
                <col width="100">
                <col width="200">
                <col width="80">
                <col width="80">
                <col width="80">
                <col width="100">
                <col width="140">
                <col width="50">
                <col width="50">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room</th>
                        <th>MQTT Channel</th>
                        <th>Timeout</th>
                        <th>Enabled</th>
                        <th>Notify</th>
                        <th>State</th>
                        <th>Description</th>
                        <th>Signal</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <?php
                    // Find out how many items are in the table
                      $total = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM  iot_nodes WHERE node_id<>'SYS'"));
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
                    echo "<center><p style='font-size:18px;'><b>".$prevlink." Page ".$page." of ".$pages." ".$nextlink."</b></p></center>";
                    
                    $count = 0;
                    $query = "SELECT * FROM iot_nodes WHERE node_id<>'SYS' ORDER BY ID LIMIT ".$limit." OFFSET ".$offset;
                    $results = mysqli_query($GS_DBCONN, $query);
                    while($result = mysqli_fetch_assoc($results)) { $count++;   
                ?>
					<tr class="active">
						<input type="hidden" name="ID<?php echo $count;?>" value="<?php echo $result['ID'];?>" />
						<td style="overflow:auto;">
							<span><?php echo $result['node_id'];?></span>
						</td>
						<td>
							<?php
								$query = "SELECT * FROM home_rooms WHERE ID='".$result['room']."'";
								$results_room = mysqli_query($GS_DBCONN, $query);
								$room_name = mysqli_fetch_assoc($results_room); 
							 ?>
							<span class="edit_mode_off"><?php echo ($room_name['room_name']=="") ? "No Room" : $room_name['room_name'];?></span>
							<select name="room<?php echo $count;?>" id="room<?php echo $count;?>" class="edit_mode form-control1" style="display:none;">
								<option value="0">No Room</option>
								<?php
									$query = "SELECT * FROM home_rooms";
									$results_room = mysqli_query($GS_DBCONN, $query);
									while($result_home_rooms = mysqli_fetch_assoc($results_room)) { 
								?>
									<option value="<?php echo $result_home_rooms['ID'];?>"><?php echo $result_home_rooms['room_name'];?></option>
								<?php } ?>
							</select>
						</td>
						<td style="overflow:auto;">
							<span title="WirelessNodes/<?php echo $result['node_id'];?>/ToNode" >WirelessNodes/<?php echo $result['node_id'];?>/ToNode</span>
						</td>
						<td>
							<span class="edit_mode_off"><?php echo $result['error_timeout'];?> Min.</span>
							<input name="timeout<?php echo $count;?>" type="number" value="<?php echo $result['error_timeout'];?>" class="edit_mode form-control1" style="display:none;width:100%;" />
						</td>
						<td>
							<span class="edit_mode_off"><?php if($result['enabled']=="1"){echo "Yes";}else{echo "<b>No</b>";};?></span>
							<div style="display:none;" class="edit_mode">
								<input name="enabled<?php echo $count;?>" id="enabled<?php echo $count;?>" type="checkbox" value="1" data-toggle="toggle" data-size="small" data-on="Yes" data-off="No"/>
							</div>
						</td>
						<td>
							<span class="edit_mode_off"><?php if($result['notifications']=="1"){echo "Yes";}else{echo "No";};?></span>
							<div style="display:none;" class="edit_mode">
								<input name="notify<?php echo $count;?>" id="notify<?php echo $count;?>" data-toggle="toggle" data-size="small"
									type="checkbox" value="1" title="Get email notifications when this node connects/disconnects"/>
							</div>
						</td>
						<td>
							<?php if (time() > strtotime('+' . $result['error_timeout'] . ' minutes', $result['last_connected_time'])) :?>
							<span style="color:red;">Timed Out</span>
							<?php else:?>
							<span style="color:green;" title="<?php echo date("M/d/Y h:i:s A",$result['last_connected_time']);?>">
							Connected
							</span>
							<?php endif;?>
						</td>
						<td style="overflow:auto;">
							<span class="edit_mode_off"><?php echo substr(ucfirst($result['decription']),0,15);?></span>
							<input name="decription<?php echo $count;?>" id="decription<?php echo $count;?>" class="edit_mode form-control1" style="display:none;width:100%;" type="text" value="<?php echo $result['decription'];?>" />
						</td>
						<td>
							<?php
								//convert dbm to percent
								if($result['signal_strength'] <= -100){
									$quality = 0;
								}elseif($result['signal_strength'] >= -50){
									$quality = 100;
								}else{
									$quality = 2 * ($result['signal_strength'] + 100);
								}
								?>
							<button type="button" class="btn btn-default" data-toggle="modal" data-target="#about_node" onclick="
								about_node('<?php echo $result['node_id'];?>',
								'<?php echo $result['ip_address'];?>',
								'<?php echo $quality;?>%',
								'<?php echo $result['SSID'];?>',
								'<?php echo $result['SSID_PASSWORD'];?>',
								'<?php echo $result['version'];?>',
								'<?php echo $result['node_type'];?>',
								'<?php echo $result['mfDate'];?>',
								'<?php echo $result['uptime'];?>',
								'<?php echo $result['decription'];?>',
								'<?php echo date("M/d/Y",$result['time_added']);?>',
								'<?php echo ago(date("Y-m-d H:i:s",$result['last_connected_time']));?>'
								);">
							<i class="fa fa-globe"></i>
							</button>
						</td>
						<td>
							<a href="manage_nodes.php?delete=<?php echo temp_encode($result['ID']);?>">
							<button type="button" class="btn btn-danger" onclick="return confirm('Are You Sure?');"><i class="fa fa-trash-o"></i></button>
							</a>
						</td>
					</tr>
					<script>
						<?php if ($result['notifications']=='1') :?>
							$('#notify<?php echo $count;?>').prop("checked", "true");
						<?php endif;?>
						<?php if ($result['enabled']=='1') :?>
							$('#enabled<?php echo $count;?>').prop("checked", "true");
						<?php endif;?>
						$('#room<?php echo $count;?>').val('<?php echo $result['room'];?>');
					</script>
                <?php }?>
                <?php if ($count==0):?>
                <tr>
                    <td colspan="10" style="text-align:center;">No Nodes Found</td>
                </tr>
                <?php endif;?>
            </table>
        </form>
    </div>
    <!--- Node Modal --->
    <div class="modal fade" id="about_node" role="dialog" style="z-index:99999;margin-top:200px;">
        <div class="modal-dialog" style="width:400px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 style="" class="modal-title"  id="about_node_header"></h4>
                </div>
                <div class="modal-body" style="overflow:auto;">
                    <table>
                        <col width="140">
                        <tr>
                            <td><b>IP Address:</b></td>
                            <td id="node_ip_address"></td>
                        </tr>
                        <tr>
                            <td><b>Signal Strength:</b></td>
                            <td id="node_signal"></td>
                        </tr>
                        <tr>
                            <td><b>SSID:</b></td>
                            <td id="node_ssid"></td>
                        </tr>
                        <tr>
                            <td><b>SSID Password:</b></td>
                            <td id="node_password"></td>
                        </tr>
                        <tr>
                            <td><b>Version:</b></td>
                            <td id="node_version"></td>
                        </tr>
                        <tr>
                            <td><b>Type:</b></td>
                            <td id="node_type"></td>
                        </tr>
                        <tr>
                            <td><b>Manufacture:</b></td>
                            <td id="node_manufacture"></td>
                        </tr>
                        <tr>
                            <td><b>Uptime (Seconds):</b></td>
                            <td id="node_uptime"></td>
                        </tr>
                        <tr>
                            <td><b>Description:</b></td>
                            <td id="node_decription"></td>
                        </tr>
                        <tr>
                            <td><b>Time Added:</b></td>
                            <td id="node_timeAdded"></td>
                        </tr>
                        <tr>
                            <td><b>Last Connected:</b></td>
                            <td id="node_lastConn"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function about_node(node,ip,signal,SSID,Password,Version,Type,Manufacture,Uptime,Desc,timeAdded,lastConn) {
        	$('#about_node_header').text(node);
        	$('#node_ip_address').text(ip);
        	$('#node_signal').text(signal);
        	$('#node_ssid').text(SSID);
        	$('#node_password').text(Password);
        	$('#node_version').text(Version);
        	$('#node_type').text(Type);
        	$('#node_manufacture').text(Manufacture);
        	$('#node_uptime').text(Uptime); /* need to multiply by 30 */
        	$('#node_decription').text(Desc);
        	$('#node_timeAdded').text(timeAdded);
        	$('#node_lastConn').text(lastConn);
           
        	$('#about_node_header').text("About Node ID: "+node);
        	$('#about_node').fadeIn();
        	$('#about_node_background').fadeIn();
        }
        
        function close_about_node() {
        	$('#about_node_background').fadeOut();
        	$('#about_node').fadeOut();
          }
    </script>
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
</body>
</html>