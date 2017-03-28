<?php
	
function getDashboardCards($type=""){ global $permis_result; global $GS_DBCONN;

	$cardCount=0;
	$query = "SELECT * FROM dashboard_cards WHERE enabled='1' AND user_id='".$_SESSION['id']."' ORDER BY index_order ASC";
	$results = mysqli_query($GS_DBCONN, $query);
	while($result = mysqli_fetch_assoc($results)) { $type=$result['card_type']; $cardCount++;
		
		$OrderInput="<input type='hidden' value='".$result['ID']."' class='DraggableCardsUIIds'/>";
		
		if($type=="Camera"){
			CameraCard($OrderInput,$result['card_name'],$result['attr1'],$result);
		}elseif($type=="Sensor"){
	    	SensorCard($OrderInput,$result['card_name'],$result['attr1'],$result);
		}elseif($type=="Data Sensors"){
			DataSensor($OrderInput,$result['card_name'],$result['attr1'],$result);
	    }elseif($type=="Roomlist"){
			RoomListCard($OrderInput,$result['card_name'],$result['attr1'],$result);
		}elseif($type=="Grouplist"){
			GroupList($OrderInput,$result['card_name'],$result['attr1'],$result);
		}elseif($type=="Device"){
			DeviceCard($OrderInput,$result['card_name'],$result['attr1'],$result);
		}elseif($type=="Weather"){
			WeatherCard($OrderInput,$result);
		}elseif($type=="Music"){
			
		}elseif($type=="GasLevel"){
			GasLevelCard($OrderInput,$result['card_name'],$result);		
		}elseif($type=="User"){
			UserCard($OrderInput,$result['card_name'],$result['attr1'],$result);
		}elseif($type=="Scene Control"){
			SceneControl($OrderInput,$result['card_name'],$result['attr1'],$result);
		}elseif($type=="Execute Script"){
			ExecuteScript($OrderInput,$result['card_name'],$result['attr1'],$result);
		}
	
	} 
	if ($cardCount==0){Return "
		<center style='font-weight:bold;margin-top:50px;margin-bottom:50px;'>
			You Have No Cards To View, To Add Your First Card Click 
			<a href='#' data-target='#CardUIModal' data-toggle='modal' onclick='addNewDashboardCard();'>Add</a>.
		</center>";
	}
	
}




function DataSensor($OrderInput,$cardName,$sensorArray, $data=""){  global $permis_result; global $GS_DBCONN; ?>
	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-2 draggable" style="<?php echo $setStyle;?>margin-bottom:10px;padding:1px;"> 	
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<b><?php echo $cardName;?></b>
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editDataSensorCard('<?php echo ucfirst($cardName);?>','<?php echo $sensorArray;?>','<?php echo $data['card_style'];?>', '<?php echo $data['ID'];?>')" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>				
				</h4>
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<div class="panel-body" style="padding:0px;padding-left:5px;">
				<ul class="list-unstyled">
					<?php
						foreach (explode(":",$sensorArray) as $DataSensor){
							$query = "SELECT * FROM data_sensors WHERE ID='".$DataSensor."' AND enabled='1'";
							$dataSensors = mysqli_query($GS_DBCONN, $query);
							$sensor = mysqli_fetch_assoc($dataSensors);
							
							//skip if id dont exitst
							if($sensor['ID']==""){continue;}
							
							$TitleArray = explode(":",$sensor['sensor_dataTitle_array']);
							$ValueArray = explode(":",$sensor['sensor_dataValue_array']);
							$VisibleArray = explode(":",$sensor['sensor_dataVisible_array']);
						?>
						<h5 style="font-size:15px;color:#999999;margin:5px;" id="DashboardCardDataSensor_name<?php echo $sensor['ID'];?>"><b></b></h5>
						<div style="color:#999999;margin-left:20px;font-size:14px;margin-bottom:10px;width:90%;">
							<?php 
								$count=-1;
								foreach($TitleArray as $title){ $count++;
								if($VisibleArray[$count]!="1"){continue;}
							?>
								<li style="padding:0px;margin-bottom:5px;height:45px;border:1px solid #e8e8e8;width:100%;">
									<div style="border:none;background-color:#fff;color:#337ab7;font-size:14px;line-height:40px;display:inline-block;padding-left:5px;">
										<span class="small-font" style='color:#333;font-size:14px;' id="DashboardCardDataSensor_colTitle<?php echo $sensor['ID'];?>_<?php echo $count;?>">
											<?php echo $TitleArray[$count];?>:
										</span>
									</div>
									
									<div class='text-success pull-right' id="DashboardCardDataSensor_colValue<?php echo $sensor['ID'];?>_<?php echo $count;?>" 
										style='width:35%;display:inlin-block;font-size:14px;text-align:center;color:#fff;background-color:silver;padding:6px;line-height:36px;height:44px;'>
										<b><?php echo $ValueArray[$count];?></b>
									</div>
								</li>
							<?php }?>
						</div>
					<?php }?>
				</ul>
				
			</div>
		</div>
	</li>
	
<?php }



function ExecuteScript($OrderInput,$cardName,$scriptArray, $data=""){  global $permis_result; global $GS_DBCONN; ?> 
	
	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-2 draggable" style="<?php echo $setStyle;?>margin-bottom:10px;padding:1px;min-width:200px;"> 	
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="fa fa-file-text-o"></i> <b><?php echo $cardName;?></b>
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editScriptCard('<?php echo ucfirst($cardName);?>','<?php echo $scriptArray;?>','<?php echo $data['card_style'];?>', '<?php echo $data['ID'];?>')" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>				
				</h4>
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<div class="panel-body" style="padding:10px;">
				<?php foreach (explode(":",$scriptArray) as $scriptItem){
					$query = "SELECT * FROM custom_scripts WHERE ID='".$scriptItem."'";
					$scipts = mysqli_query($GS_DBCONN, $query);
					$script = mysqli_fetch_assoc($scipts);
					
					//skip if id dont exitst
					if($script['ID']==""){continue;}
				?>
					<form method="post" class="autoformf">
						<input type="hidden" name="type" value="<?php echo temp_encode("execute_script");?>"/>
						<input type="hidden" name="script" value="<?php echo $script['ID'];?>"/>
						<button class="btn btn-default" style="width:100%;margin-bottom:10px;">
							<?php echo $script['script_name'];?>
						</button>
					</form>
				<?php }?>
			</div>
		</div>
	</li>
	
	
<?php }


function CameraCard($OrderInput,$cardName,$cameraArray, $data=""){  global $permis_result; global $GS_DBCONN; ?>   

   <li class="col-xs-12 col-sm-12 col-md-4 col-lg-2 draggable" style="margin-bottom:10px;padding:1px;min-width:200px;"> 
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<b><?php echo $cardName;?></b>	
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editCameraCard('<?php echo ucfirst($cardName);?>','<?php echo $cameraArray;?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>				
				</h4>
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<div class="panel-body" style="padding:0px;">

				<?php 
					foreach (explode(":",$cameraArray) as $cameraID){

						$featured_cameras_count=0;
						$query = "SELECT * FROM camera_list WHERE ID='".$cameraID."'";
						$cameras = mysqli_query($GS_DBCONN, $query);
						$camera = mysqli_fetch_array($cameras);
						
						//skip if id dont exitst
						if($camera['ID']==""){continue;}
				
						//check permissions
						if(GetUserPermissions($camera['ID'],"manage_cameras.php")==false){continue;}
						
						$url=$camera['ip_address'];
						
						$query = "SELECT * FROM home_rooms WHERE ID='".$camera['room']."'";
						$room_names = mysqli_query($GS_DBCONN, $query);
						$room_name = mysqli_fetch_assoc($room_names); 	
				?>					
								
				
					<?php if($camera['enabled']=='1'):?>
						<a href="#" onclick="showCameraModal<?php echo $camera['ID'];?>();"
					<?php else:?>
						<a href="#"
					<?php endif;?> 
						title="<?php echo "Camera: ".ucfirst($camera['camera_name'])."  Room: ".ucfirst($room_name['room_name']);?>"
						style="cursor:pointer;">
						<center>
							<?php if($camera['enabled']=='1') :?>	
								<img src="<?php echo $url;?>" style="max-width:300px;width:100%;max-height:200px;" alt=""/>
							<?php else :?>
								<img src="images/camera_disabled.jpg" style="max-width:300px;width:100%;max-height:200px;" alt="Image Not Found"/>
							<?php endif;?>
						</center>
						<div style="font-size:10px;background-color:#333;color:#fff;border:1px solid #333;width:100%;overflow:hidden;height:20px;">
							<div style="text-align:left;padding:2px;padding-left:5px;float:left;width:60%;"><b><?php echo ucfirst($room_name['room_name']);?></b></div>
							
							<div style="float:right;display:inline;padding:0px;">
								<div style="text-align:left;padding:2px;float:left;padding-right:10px;"><b>Activity:</b></div>
								<div class="featured_camera_alert_id<?php echo $camera['room'];?>" title="Room Activity" style="display:inline-block;width:10px;height:18px;background-color:#fff;"></div>										
							</div>
						</div>
					</a>
					
				<?php }?>
			</div>
		</div>
	</li>
<?php } 



function SensorCard($OrderInput,$cardName,$sensorArray, $data=""){  global $permis_result; global $GS_DBCONN; ?>

	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-3 draggable" style="margin-bottom:10px;padding:1px;">
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!-- This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<b><?php echo $cardName;?></b>		
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editSensorCard('<?php echo ucfirst($cardName);?>','<?php echo $sensorArray;?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>
				</h4>
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<div class="panel-body">
				<ul class="list-unstyled">
					<?php foreach (explode(":",$sensorArray) as $sensorID){
						$results = mysqli_query($GS_DBCONN, "SELECT * FROM sensors WHERE ID='".$sensorID."'");
						$result = mysqli_fetch_assoc($results);
						if($result['ID']==""){continue;}//skip if id dont exitst
					?>
						
						<li style="padding:0px;margin-bottom:5px;height:45px;border:1px solid #e8e8e8;">
							<form method="POST" class="autoform" style="display:inline-block;">
								<input type="hidden" name="sensorID" value="<?php echo $result['ID'];?>"/>
								<input type="hidden" class="sensorType_<?php echo $result['ID'];?>" name="type" value=""/>
								
								<button type="submit" style="border:none;background-color:#fff;color:#337ab7;font-size:18px;line-height:36px;">
									<i class="fa fa-eye showSensorEnabledstate_<?php echo $result['ID'];?>" style="display:none;" title=""></i>
									<i class="fa fa-eye-slash showSensorDisabledstate_<?php echo $result['ID'];?>" style="display:none;" title=""></i>
									&nbsp;&nbsp;
									<span class="indexSensorName_<?php echo $result['ID'];?> small-font" style='color:#333;font-size:16px;'></span>
								</button>
							</form>
							<span class="indexSensorState_<?php echo $result['ID'];?> small-font"></span>
						</li>

					<?php }?>
				</ul>
			</div>
		</div>
	</li>
<?php } 




function DeviceCard($OrderInput,$cardName,$deviceArray, $data=""){ global $permis_result; global $GS_DBCONN; global $GS_Config; ?>

	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-3 draggable" style="margin-bottom:10px;padding:1px;">
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<b><?php echo $cardName;?></b>		
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editDeviceCard('<?php echo ucfirst($cardName);?>','<?php echo $deviceArray;?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>
				</h4>
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<div class="panel-body" style="width:100%;max-height:450px;">
				
				<table class="table" style="width:100%;padding:0px;margin:0px;">
					<col width="120">
					<col width="160">
					<col width="80">
					
					<tbody>
				
					<?php foreach (explode(":",$deviceArray) as $deviceID){
						$query = "SELECT * FROM devices WHERE  ID='".$deviceID."' AND  (enabled='1' OR enabled='3') ";
						$devices = mysqli_query($GS_DBCONN, $query);
						$device = mysqli_fetch_assoc($devices);
						if($device['ID']==""){continue;}//skip if id dont exitst
					?>
						<tr class="active">
							
							<td class="deviceDisabled_<?php echo $device['ID'];?>" style="display:none;">
								<button type="submit" class="btn btn-default" style="width:100%;margin-bottom:5px;border-radius:0px;margin:0px;padding:0px;text-align:left;border:1px solid #f2f2f2;margin-bottom:5px;">
									<div style="display:inline-block;background-color:<?php echo $GS_Config['themeColorMain'];?>;text-align:center;height:45px;width:26%;margin:2px;padding:12px;color:#fff;font-size:18px;">
										<i class="fa <?php if($device['device_icon']==''){echo "fa-power-off";}else{echo $device['device_icon'];}?>"></i> 
									</div>
									<?php echo ucfirst($device['device_name']);?>
								</button>
							</td>

							<td class="turnOn_<?php echo $device['ID'];?>" style="display:none;">
								<form method="POST" class="autoform">
									<input type="hidden" name="type" value="<?php echo temp_encode("toggle_deviceOn");?>" />
									<input type="hidden" name="device_id" value="<?php echo $device['ID'];?>" />
									<input type="hidden" name="room_id" value="<?php echo $device['room'];?>" />
									
									<button type="submit" class="btn btn-default" style="width:100%;margin-bottom:5px;border-radius:0px;margin:0px;padding:0px;text-align:left;border:1px solid #f2f2f2;margin-bottom:5px;">
										<div style="display:inline-block;background-color:silver;text-align:center;height:45px;width:26%;margin:2px;padding:12px;color:#fff;font-size:18px;">
											<i class="fa <?php if($device['device_icon']==''){echo "fa-power-off";}else{echo $device['device_icon'];}?>"></i> 
										</div>
										<?php echo ucfirst($device['device_name']);?>
									</button>
								</form>
								
								<!-- Brightness Slider -->					
								<div class="DeviceBrightnessSlider<?php echo $device['ID'];?>" style="width:150px;"></div>
							</td>

							<td class="turnOff_<?php echo $device['ID'];?>" style="">
								<form method="POST" class="autoform" style="display:inline;">
									<input type="hidden" name="type" value="<?php echo temp_encode("toggle_deviceOff");?>" />
									<input type="hidden" name="device_id" value="<?php echo $device['ID'];?>" />
									<input type="hidden" name="room_id" value="<?php echo $device['room'];?>" />
									<button type="submit" class="btn btn-default" style="width:100%;margin-bottom:5px;border-radius:0px;margin:0px;padding:0px;text-align:left;border:1px solid #f2f2f2;margin-bottom:5px;">
										<div style="display:inline-block;background-color:<?php echo $GS_Config['themeColorMain'];?>;text-align:center;height:45px;width:26%;margin:2px;padding:12px;color:#fff;font-size:18px;">
											<i class="fa <?php if($device['device_icon']==''){echo "fa-power-off";}else{echo $device['device_icon'];}?>"></i> 
										</div>
										<?php echo ucfirst($device['device_name']);?>
									</button>
								</form>
								
								<!-- Brightness Slider -->					
								<div class="DeviceBrightnessSlider<?php echo $device['ID'];?>" style="width:150px;"></div>
							</td>
							
							<?php if (strpos($device['tags'],"Dimmable")!==false) :  //if this is Dimmable ?>
								<form method="POST" class="set_device_brightness_<?php echo $device['ID'];?> autoform" style="display:inline;">
									<input value="<?php echo temp_encode("ChangeDeviceBrightness");?>" type="hidden" name="type"/>
									<input value="<?php echo $device['ID'];?>" type="hidden" name="device"/>
									<input value="0" type="hidden" name="brightness" class="device_brightness_value_<?php echo $device['ID'];?>"/>
								</form>
								
								<script>
									/* Brightness Slider Initates slider and sets default values */ 
									$(".DeviceBrightnessSlider<?php echo $device['ID'];?>").slider({
										orientation: "horizontal",
										range: false,
										min: 0,
										max: 100,
										value: 0,
										step: 1,
										animate: "slow",
										slide: function(event, ui){
											$('.device_brightness_value_<?php echo $device['ID'];?>').val(ui.value);
										},
										stop: function (event, ui) {
											$('.set_device_brightness_<?php echo $device['ID'];?>').submit();
											$('.device_brightness_value_<?php echo $device['ID'];?>').val("0");
										}
									});
									//change color of slider
									//$( ".DeviceBrightnessSlider<?php echo $device['ID'];?>" ).css('background', 'red');
								</script>
							<?php endif;?>

							<td>
								<form method="POST" class="autoform" id="auto_off_frm<?php echo $device['ID'];?>" style="margin-top:10px;">
									<input type="hidden" name="type" value="<?php echo temp_encode("update_autooff");?>" />
									<input type="hidden" name="device" value="<?php echo $device['ID'];?>" />
									<input class="auto_off_chk<?php echo $device['ID'];?>" type="checkbox" data-toggle="toggle" data-size="medium" 
									data-off="Auto-off" data-on="Auto-off" name="auto_off" value="1" onchange="$('#auto_off_frm<?php echo $device['ID'];?>').submit();" />
								</form>
								<?php if ($device['enable_auto_off']=="1") :?>
									<script>
										$('.auto_off_chk<?php echo $device['ID'];?>').attr("checked", "true");
									</script>
								<?php endif;?>
							</td>

						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</li>
	
<?php }
	
	
	
	

function WeatherCard($OrderInput, $data=""){ global $permis_result; global $GS_DBCONN; global $GS_weatherServiceEnabled;?>  

	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-3 draggable" style="margin-bottom:10px;padding:1px;min-width:300px;">
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->

			<h4 class="panel-title" style="font-size:17px;">
				<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editWeatherCard('<?php echo ucfirst("Weather");?>','<?php echo $user;?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
					title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>
			</h4>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>	

			<div class="panel-body">
				<div class="cloud">
					<?php if($GS_weatherServiceEnabled == true): //Check if service is enabled ?>
						<p class="monday" style="box-shadow:rgba(0, 0, 0, 0.2) 0px 1px 3px 0px;padding:12px;"> <?php echo date("l, F jS");?> </p>
						<div class="grid-date" style="padding:4px;border-radius:0px;margin-top:5px;box-shadow:rgba(0, 0, 0, 0.2) 0px 1px 3px 0px;">
							<div class="date">
								<p class="date-in">
									<form method="post" style="float:left;" class="autoform">
									   <input type="hidden" name="type" value="<?php echo temp_encode("update_weather");?>"/>
									   <button type="submit" style="color:#fff;background-color:transparent;border:none;font-size:20px;" id="DashboardCardWeather_lastUpdate" onclick="dashboardCardWeatherRefresh();">
										 <i class="fa fa-refresh"></i>
									   </button>
									   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									   <span style="color:#fff;" id="DashboardCardWeather_city"></span>
									</form>
								</p>
								<script>
									function dashboardCardWeatherRefresh(){
										$("#DashboardCardWeather_lastUpdate i").addClass("faa-spin animated");
										setTimeout(function(){
											$("#DashboardCardWeather_lastUpdate i").removeClass("faa-spin animated");
										},2000);
									}
								</script>
								
								<span class="date-on">°F </span>
								<div class="clearfix"></div>
							</div>
							<h4 style="padding:0px;margin-top:0px;"> 		
								<span id="DashboardCardWeather_icon"></span>
								<span style="font-size:45px;" title="Temperature" id="DashboardCardWeather_temp"></span>
				
								<div style="font-size:16px;margin-top:10px;margin-bottom:10px;font-weight:bold" id="DashboardCardWeather_tempCompare">20&deg; Warmer than yesterday</div>
							 </h4>
						</div>
					<?php endif;?>
				</div>
				<p class="monday" style="margin-bottom:5px;box-shadow:rgba(0, 0, 0, 0.2) 0px 1px 3px 1px;font-size:16px;margin-top:6px;padding:12px;">
					<b>Sunrise:</b> <span style="" id="DashboardCardWeather_sunrise"></span>&nbsp;&nbsp;
					<b>Sunset:</b> <span style="" id="DashboardCardWeather_sunset"></span>
				</p>
			</div>
		</div>
	</li>
			
<?php }
	
function UserCard($OrderInput,$cardName,$user, $data=""){  global $permis_result; global $GS_DBCONN; ?>
	<?php
		
	//get user info
	$query = "SELECT * FROM users WHERE ID='".$user."'";
	$users = mysqli_query($GS_DBCONN, $query);
	$user = mysqli_fetch_assoc($users);
	
	//skip if id dont exitst
	if($user['ID']==""){return false;}
	
	//get user location info
	$query = "SELECT * FROM whoishome WHERE user_id='".$user['ID']."' ";
	$usersLOC = mysqli_query($GS_DBCONN, $query);
	$userLOC = mysqli_fetch_assoc($usersLOC);
	$userLOCcount = mysqli_num_rows($usersLOC);
	?>
		
	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-2 draggable" style="margin-bottom:10px;padding:1px;">
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<b><?php echo $cardName;?></b>		
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editUserCard('<?php echo ucfirst($cardName);?>','<?php echo $user['ID'];?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>
				</h4>	
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<?php if ($userLOCcount >0 && $userLOC['enabled']=="1") :?>
				<div class="panel-body" style="padding:10px;">
					<div style="margin-top:-10px;margin-bottom:5px;">
						<?php if($user['user_img']!=""):?>
							<img src="/images/users/<?php echo $user['user_img'];?>" style="height:50px;width:50px;border-radius:40px;"/>
						<?php else :?>
							<img src="/images/users/Default.png" style="height:50px;width:50px;border-radius:40px;"/>
						<?php endif;?>
						
						<?php if($userLOC['home']=="1"):?>
							<b>Home</b>
						<?php elseif($userLOC['work']=="1"):?>
						 	<b>Work</b>
						<?php else :?>
							<b>Away</b>
						<?php endif;?>	
					</div>	
					
					<div style="display:inline-block;">
						<?php if ($userLOC['check_method']=="icloud"):
							$data = explode("|", $userLOC['check_data']);
						?>
							<ul style="padding:0px;font-size:14px;list-style:none;">
								<li style="padding:2px;"><b>Device:</b> <?php echo $data[1];?></li>
								<li style="padding:2px;"><b>Charging:</b> <?php echo str_replace("tC","t C",$data[3]);?></li>
								<li style="padding:2px;"><b>Battery:</b> <?php echo $data[2]*100;?>%<br/>
								<li style="padding:2px;"><b>Arrived at Home:</b> <?php echo ago($userLOC['last_at_home']);?></li>
								<li style="padding:2px;"><b>Arrived at Work:</b> <?php echo ago($userLOC['last_at_work']);?></li>
								<!--<a href="#" data-toggle="modal" data-target="#UserCard<?php echo $data['ID'];?>"><u>View Location</u></a>-->
							</ul>
						<?php endif;?>	
					</div>
				</div>
			<?php else : //User location is disabled ?>
				<div class="panel-body">
					<div style="padding:5px;margin-top:-10px;margin-bottom:5px;">
						User Location Disabled
					</div>
				</div>
			<?php endif;?>
		</div>
	</li>
	
	
	<!-- Modal -->
	<div class="modal fade" id="UserCard<?php echo $data['ID'];?>" role="dialog" style="z-index:9999;margin-top:100px;">
		<div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 style="" class="modal-title"><?php echo $cardName;?></h4>
				</div>
				<div class="modal-body">
					
					
					<?php //echo $cord[4].$cord[5];?>
				</div>
			</div>
		</div>
	</div>			
					
<?php }
	
function MusicCard($OrderInput,$roomID, $data=""){ global $permis_result; global $GS_DBCONN; ?>   	
	<script> /* This only executes when not on a mobile device, this is for the width an height */
		if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
			<?php 
				if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
				if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
			?>
		}
	</script>
	
<?php }
	
	
function SceneControl($OrderInput,$cardName,$sceneArray, $data=""){ global $permis_result; global $GS_DBCONN; global $GS_Config; ?>  
	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-2 draggable" style="margin-bottom:10px;padding:1px;min-width:200px;">
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order--> 
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<b><?php echo $cardName;?></b>
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editSceneCard('<?php echo ucfirst($cardName);?>','<?php echo $sceneArray;?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>
				</h4>
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>

			<div class="panel-body">
				<?php foreach (explode(":",$sceneArray) as $SceneID) {
					$query = "SELECT * FROM scene WHERE ID='".$SceneID."'";
					$scenes = mysqli_query($GS_DBCONN, $query);
					$scene = mysqli_fetch_assoc($scenes);
					
					//skip if id dont exitst
					if($scene['ID']==""){continue;}
					
					//check permissions
					if(GetUserPermissions($scene['ID'],"manage_scene.php")==false){continue;}
				?>
					<form method="POST" class="autoform">
						<input type="hidden" name="type" value="activateScene"/>
						<input type="hidden" name="scene" value="<?php echo $SceneID;?>"/>
						
						<button type="submit" class="btn btn-default" style="width:100%;margin-bottom:5px;border-radius:0px;margin:0px;padding:0px;text-align:left;border:1px solid #f2f2f2;margin-bottom:5px;">
							<div style="display:inline-block;background-color:<?php echo $GS_Config['themeColorMain'];?>;text-align:center;height:45px;width:26%;margin:2px;padding:12px;color:#fff;font-size:18px;">
								<i class="fa <?php echo $scene['scene_icon'];?>"></i>
							</div>
							<?php echo $scene['scene_name'];?>
						</button>
					</form>
				<?php } ?>
			</div>
		</div>
	</li>

<?php }	
	
	
function GroupList($OrderInput,$cardName,$groupArray, $data=""){ global $permis_result; global $GS_DBCONN; global $GS_Config; ?>  
	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-2 draggable" style="margin-bottom:10px;padding:1px;min-width:200px;">
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<b><?php echo $cardName;?></b>
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editGroupCard('<?php echo ucfirst($cardName);?>','<?php echo $groupArray;?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>
				</h4>
				
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<div class="panel-body">
				<ul style="list-style:none;margin:0px;padding:0px;">
					<?php 
					foreach (explode(":",$groupArray) as $groupID){
						$query = "SELECT * FROM device_groups WHERE ID='".$groupID."'";
						$groups = mysqli_query($GS_DBCONN, $query);
						$group = mysqli_fetch_assoc($groups);
						
						//skip if id dont exitst
						if($group['ID']==""){continue;}
					?>
						<li id="turnOnGroup_<?php echo $group['ID'];?>" style="display:none;border:none;padding:0px;">
							<form method="POST" class="autoform">
								<input type="hidden" name="type" value="<?php echo temp_encode("toggle_group_on");?>" />
								<input type="hidden" name="group_id" value="<?php echo $group['ID'];?>" />
								<button type="submit" class="btn btn-default" style="width:100%;margin-bottom:5px;border-radius:0px;margin:0px;padding:0px;text-align:left;border:1px solid #e8e8e8;margin-bottom:5px;">
									<div style="display:inline-block;background-color:silver;text-align:center;height:45px;width:26%;margin:2px;padding:12px;color:#fff;font-size:18px;">
										<i class="fa <?php echo $group['group_icon'];?>"></i>
									</div>
									<?php echo $group['group_name'];?>
								</button>
							</form>
						</li>

						<li id="turnOffGroup_<?php echo $group['ID'];?>" style="display:none;border:none;padding:0px;">
							<form method="POST" class="autoform">
								<input type="hidden" name="type" value="<?php echo temp_encode("toggle_group_off");?>" />
								<input type="hidden" name="group_id" value="<?php echo $group['ID'];?>" />
								<button type="submit" class="btn btn-default" style="width:100%;margin-bottom:5px;border-radius:0px;margin:0px;padding:0px;text-align:left;border:1px solid #e8e8e8;margin-bottom:5px;">
									<div style="display:inline-block;background-color:<?php echo $GS_Config['themeColorMain'];?>;text-align:center;height:45px;width:26%;margin:2px;padding:12px;color:#fff;font-size:18px;">
										<i class="fa <?php echo $group['group_icon'];?>"></i>
									</div>
									<?php echo $group['group_name'];?>
								</button>
							</form>
						</li>
			  
					<?php } ?>
			   </ul>
			</div>
		</div>
	</li>

<?php }
	

function RoomListCard($OrderInput,$cardName,$roomArray, $data=""){ global $permis_result; global $GS_DBCONN; global $GS_Config; ?>   	
	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-2 draggable" style="margin-bottom:10px;padding:1px;min-width:200px;">
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h4 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<b><?php echo $cardName;?></b>
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editRoomCard('<?php echo ucfirst($cardName);?>','<?php echo $roomArray;?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>
				</h4>
			</div>
  
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<div class="panel-body">
				<ul style="list-style:none;margin:0px;padding:0px;">
					 <?php 
						 foreach (explode(":",$roomArray) as $roomID){

							$query = "SELECT * FROM home_rooms WHERE ID='".$roomID."'";
							$rooms = mysqli_query($GS_DBCONN, $query);
							$room = mysqli_fetch_assoc($rooms);
							
							//skip if id dont exitst
							if($room['ID']==""){continue;}
							
							$query = "SELECT * FROM devices WHERE room='".$roomID."' AND tags LIKE '%Dimmable%'";
							$PhueDeviceCount = mysqli_num_rows(mysqli_query($GS_DBCONN, $query));
			
							if($room['guest_access']=="0" && $_SESSION['type']=='Guest'){continue;}
							if(GetUserPermissions($room['ID'],"manage_room.php")==false){continue;}
					?>
						<li id="turnOnRoom_<?php echo $room['ID'];?>" style="display:none;border:none;padding:0px;margin-bottom:5px;">
							<form method="POST" class="autoform" style="width:78%;border-radius:0px;margin:0px;padding:0px;text-align:left;border:1px solid #e8e8e8;margin-bottom:5px;display:inline-block;">
								<input type="hidden" name="type" value="<?php echo temp_encode("toggle_room_on");?>" />
								<input type="hidden" name="room_id" value="<?php echo $room['ID'];?>" />
								<button type="submit" class="btn btn-default" style="width:30%;padding:0px;height:46px;border:0px;line-height:35px;">
									<div style="display:inline-block;background-color:silver;text-align:center;height:46px;width:100%;margin:2px;padding:8px;color:#fff;font-size:18px;">
										<i class="fa <?php echo $room['room_icon'];?>"></i>
									</div>
								</button>
								
								<!-- Brightness Slider -->	
								<div class="DeviceRoomBrightnessSlider<?php echo $room['ID'];?>" style="height:50px;width:64%;display:inline-block;padding:0px;float:right;border:none;line-height:50px;color:#333;">
									<?php echo $room['room_name'];?>
								</div>
							</form>
							<button type="button" data-toggle="modal" data-target="#INDEX_ROOM_MODAL<?php echo $room['ID'];?>" class="btn btn-default" style="height:55px;width:16%;padding:0px;">
								<i class="fa fa-bars"></i>
							</button>
						</li>

						<li id="turnOffRoom_<?php echo $room['ID'];?>" style="display:none;border:none;padding:0px;margin-bottom:5px;">
							<form method="POST" class="autoform" style="width:78%;border-radius:0px;margin:0px;padding:0px;text-align:left;border:1px solid #e8e8e8;margin-bottom:5px;display:inline-block;">
								<input type="hidden" name="type" value="<?php echo temp_encode("toggle_room_off");?>" />
								<input type="hidden" name="room_id" value="<?php echo $room['ID'];?>" />
								<button type="submit" class="btn btn-default" style="width:30%;padding:0px;height:46px;border:0px;line-height:35px;">
									<div style="display:inline-block;background-color:<?php echo $GS_Config['themeColorMain'];?>;text-align:center;height:46px;width:100%;margin:2px;padding:8px;color:#fff;font-size:18px;">
										<i class="fa <?php echo $room['room_icon'];?>"></i>
									</div>
								</button>
								
								<!-- Brightness Slider -->	
								<div class="DeviceRoomBrightnessSlider<?php echo $room['ID'];?>" style="height:50px;width:64%;display:inline-block;padding:0px;float:right;border:none;line-height:50px;color:#000;">
									<?php echo $room['room_name'];?>
								</div>
							</form>
							<button type="button" data-toggle="modal" data-target="#INDEX_ROOM_MODAL<?php echo $room['ID'];?>" class="btn btn-default" style="height:55px;width:16%;padding:0px;">
								<i class="fa fa-bars"></i>
							</button>
						</li>	
						
						<?php if ($PhueDeviceCount>0) :?><!-- Check if Phue Devices in Room if so brightness -->
							<form method="POST" id="frmRoomBrightness<?php echo $room['ID'];?>" class="autoform">
								<input type="hidden" name="type" value="<?php echo temp_encode("RoomBrightness");?>"/>
								<input type="hidden" name="room_id" value="<?php echo $room['ID'];?>"/>
								<input type="hidden" name="brightness" class="device_room_brightness_value_<?php echo $room['ID'];?>" value="0"/>
							</form>

							<script>
								/* Brightness Slider Initates slider and sets default values */ 
								$(".DeviceRoomBrightnessSlider<?php echo $room['ID'];?>").slider({
									orientation: "horizontal",
									range: false,
									min: 1,
									max: 100,
									value: 0,
									step: 1,
									animate: "slow",
									slide: function(event, ui){
										$('.device_room_brightness_value_<?php echo $room['ID'];?>').val(ui.value);
									},
									stop: function (event, ui) {
										$('#frmRoomBrightness<?php echo $room['ID'];?>').submit();
									}
								});
								//change color of slider
								//$(".DeviceRoomBrightnessSlider<?php echo $room['ID'];?>").css('background', 'red');
							</script>
							
						<?php endif;?>
			 
			  
						<div class="modal fade" id="INDEX_ROOM_MODAL<?php echo $room['ID'];?>" role="dialog" style="z-index:9998;margin-top:100px;">
							<div class="modal-dialog" style="width:450px;">
								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header">
									  <button type="button" class="close" data-dismiss="modal">&times;</button>
									  <h4 style="" class="modal-title"><?php echo $room['room_name'];?></h4>
									</div>
									<div class="modal-body" style="min-height:300px;max-height:460px;overflow-y:auto;">
										<div style="width:45%;margin:0px;height:300px;overflow-y:auto;overflow-x:hidden;display:inline-block;margin-right:10px;">
											<label><b>Groups:</b></label>
											<?php
											$count=0;
											$query = "SELECT * FROM device_groups ORDER BY ID ASC LIMIT 8 ";
											$Rgroups = mysqli_query($GS_DBCONN, $query);
											while($group = mysqli_fetch_assoc($Rgroups)) {
												if(mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM devices WHERE room='".$room['ID']."' AND group_id='".$group['ID']."'"))==0){continue;}else{$count++;} ?>
													<form method="post" class="autoform">
													 <input type="hidden" name="type" value="<?php echo temp_encode("modalRoomGroup");?>"/>
													 <input type="hidden" name="room_id" value="<?php echo $room['ID'];?>"/>
													 <input type="hidden" name="group_id" value="<?php echo $group['ID'];?>"/>
													 <input type="hidden" name="state" value="0" id="DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $room['ID'];?>_State"/>
													
													 <button class="btn btn-primary" type="submit" style="width:100%;margin-bottom:4px;" id="DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $room['ID'];?>_On">
														<i style="float:left;" class="fa <?php echo $group['group_icon'];?>"></i>&nbsp;<?php echo $group['group_name'];?>
													 </button>
													 <button class="btn btn-default" type="submit" style="display:none;width:100%;;margin-bottom:4px;" id="DashboardCardRoomModal_Group<?php echo $group['ID'];?>_Room<?php echo $room['ID'];?>_Off">
														<i style="float:left;" class="fa <?php echo $group['group_icon'];?>"></i>&nbsp;<?php echo $group['group_name'];?>
													 </button>												
												</form>
											<?php } if($count==0):?>
												<p>No Groups</p>
											<?php endif;?>
										</div>
										
										<div style="width:45%;margin:0px;height:300px;overflow-y:auto;overflow-x:hidden;display:inline-block;">											
											<label><b>Devices:</b></label>
											<?php
											$count = 0;
											$query = "SELECT * FROM devices WHERE room='".$room['ID']."' ORDER BY ID ASC LIMIT 8 ";
											$Rdevices = mysqli_query($GS_DBCONN, $query);
											while($Rdevice = mysqli_fetch_assoc($Rdevices)) { $count++;
											?>
												<form method="POST" class="turnOff_<?php echo $Rdevice['ID'];?> autoform" style="display:none;" >
													<input type="hidden" name="type" value="<?php echo temp_encode("toggle_deviceOff");?>" />
													<input type="hidden" name="device_id" value="<?php echo trim($Rdevice['ID']);?>" />
													<button type="submit" class="btn btn-primary" style="display:block;margin-bottom:4px;width:100%;">
														<i style="float:left;" class="fa <?php if($Rdevice['device_icon']==''){echo "fa-power-off";}else{echo $Rdevice['device_icon'];}?>"></i> 
														<?php echo $Rdevice['device_name'];?>
													</button>
												</form>
												<form method="POST" class="turnOn_<?php echo $Rdevice['ID'];?> autoform" style="display:none;">
													<input type="hidden" name="type" value="<?php echo temp_encode("toggle_deviceOn");?>" />
													<input type="hidden" name="device_id" value="<?php echo trim($Rdevice['ID']);?>" />
													<button type="submit" class="btn btn-default" style="display:block;margin-bottom:4px;width:100%;">
														<i style="float:left;" class="fa <?php if($Rdevice['device_icon']==''){echo "fa-power-off";}else{echo $Rdevice['device_icon'];}?>"></i> 
														<?php echo $Rdevice['device_name'];?>
													</button>																	
												</form>	
											<?php } if($count==0):?>
												<p>No Devices</p>
											<?php endif;?>
										</div>
									</div> <!--END MODAL BODY -->
								</div><!-- END MODAL CONTENT -->
							</div><!-- END MODAL DIALOG -->
						</div><!-- END MODAL FADE -->
					<?php } //END FOR EACH ROOM ?>

				</ul>
			</div>
		</div><!-- END DC STYLE -->
	</li>
		
<?php }





function GasLevelCard($OrderInput,$cardName, $data=""){ global $permis_result; global $GS_DBCONN; ?>   
	<li class="col-xs-12 col-sm-12 col-md-4 col-lg-2 draggable" style="margin-bottom:10px;padding:1px;min-width:190px;">
		<div id="DC_style<?php echo $data['ID'];?>" style="background-color:#fff;margin:2px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);overflow:auto;" class="stats-info">
			<?php echo $OrderInput;?> <!--This return an input with the id of the card this is used to save the order-->
			<div class="panel-heading" style="padding:5px;border:1px solid #f1f1f1;margin-bottom:5px;">
				<h5 class="panel-title" style="text-align:center;font-size:17px;">
					<i class="draggableCardIcon fa fa-bars" aria-hidden="true" style="float:left;overflow:hidden;width:0px;color:silver"></i>
					<b><?php echo $cardName;?></b>
					<a href="#" data-target="#CardUIModal" data-toggle="modal" onclick="editGasLevel('<?php echo ucfirst($cardName);?>','<?php echo $roomArray;?>','<?php echo $data['card_style'];?>','<?php echo $data['ID'];?>');" 
						title="Edit" class="draggableCardEditIcon"><i style="cursor:pointer;float:right;overflow:hidden;width:0px;"class="fa fa-pencil"></i></a>
				</h5>
			</div>
			
			<script> /* This only executes when not on a mobile device, this is for the width an height */
				if (/Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
					<?php 
						if(explode(":",$data['card_style'])[0]!="0"){echo "$('#DC_style".$data['ID']."').css({'width':'".explode(":",$data['card_style'])[0]."px','overflow':'auto'});";}
						if(explode(":",$data['card_style'])[1]!="0"){echo "$('#DC_style".$data['ID']."').css({'height':'".explode(":",$data['card_style'])[1]."px','overflow':'auto'});";}		
					?>
				}
			</script>
			
			<div class="panel-body">
				<div class="box_1" >
					<div class="col-md-12" style="margin-bottom:10px;max-width:250px;">
						<a class="tiles_info">
							<div class="tiles-head red1" style="padding:5px;border-radius:0px;">
								<div class="text-center small-font" style="width:100%;overflow:hidden;height:20px;text-transform:none;">Carbon Monoxide</div>
							</div>
							<div class="tiles-body red" style="padding:0px;border-radius:0px;">0%</div>
						</a>
					</div>
					<div class="col-md-12" style="max-width:250px;">
						<a class="tiles_info tiles_blue">
							<div class="tiles-head tiles_blue1" style="padding:5px;border-radius:0px;">
								<div class="text-center small-font" style="text-transform:none;">Methane</div>
							</div>
							<div class="tiles-body blue1" style="padding:0px;border-radius:0px;">0%</div>
						</a>
					</div>				
				</div>
			</div>
		</div>
	</li>

<?php }

