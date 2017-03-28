<?php if(GetUserPermissions("edit","alarm_access")==true): //check and see if current user has permission ?>
	<!-------- Alarm Dialog ------>
	<!-- Modal -->
	<div class="modal fade" id="AlarmDialog" role="dialog" style="z-index:99999;margin-top:100px;">
		<div class="modal-dialog" style="width:325px;z-index:9999;">
		
		  <!-- Modal content-->
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
			  <h4 style="" class="modal-title">Change Alarm State</h4>
			</div>
			<div class="modal-body" style="overflow:auto;text-align:center;">
				<form method="POST" class="autoform" title="Change Alarm State">
					<input type="hidden" name="type" value="<?php echo temp_encode("change_alarm_state");?>"/>
					<input type="hidden" value="" name="state" id="datastates_alarmState_value"/>
					
					<div class="input-group" style="width:285px;">
						<span class="input-group-addon"><i class="fa fa-lock"></i></span>
						<input type="password" name="pin" id="alarmPanelKeyInput" class="form-control1" style="font-size:30px;"/>
					</div>
					
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('1');">1</button>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('2');">2</button>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('3');">3</button>
					<br/>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('4');">4</button>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('5');">5</button>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('6');">6</button>
					<br/>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('7');">7</button>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('8');">8</button>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('9');">9</button>
					<br/>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" disabled></button>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="alarmPanelEnterKey('0');">0</button>
					<button type="button" class="btn btn-default" style="height:60px;width:60px;font-size:20px;margin:5px;" onclick="$('#alarmPanelKeyInput').val('');">C</button>
					<br/>
					<button type="submit" class="btn btn-success" style="width:285px;font-size:20px;margin:5px;" id="alarmPanelSubmitButton" onclick="alarmPanelSubmited();">Submit</button>
					
					<a href="#" style="display:none;" data-toggle="modal" data-target="#AlarmDialog" id="AlarmDialogModalBtn"></a>
					<a href="#" style="display:none;" data-dismiss="modal" id="AlarmDialogModalCloseBtn"></a>
					
					<script>
						function alarmPanelEnterKey(number){
							$("#alarmPanelKeyInput").val($("#alarmPanelKeyInput").val() + number);
						}
						function alarmPanelSubmited(){
							setTimeout(function(){$("#alarmPanelKeyInput").val("");},1000);
							$("#AlarmDialog").find(".close").click();
						}
					</script>
					
				</form>		
			</div>
		  </div>
		</div>
	</div>	
<?php endif;?>

	
<!-------- Camera Alerts ------>
<!-- Modal -->
<div class="modal fade" id="CameraModal_alerts" role="dialog" style="z-index:99999;margin-top:100px;">
	<div class="modal-dialog">
	
	  <!-- Modal content-->
	  <div class="modal-content" style="max-height:550px;overflow:auto;">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  <h4 style="" class="modal-title" id="camera_name"></h4>
		</div>
		<div class="modal-body" style="overflow:auto;padding:0px;">
			
			<form method="POST" class="autoform">
				<input type="hidden" name="type" value="<?php echo temp_encode("camera_click_event");?>"/>
				<input type="hidden" name="click_event" value="" id="camera_alerts_clickEvent"/>
			
				<button type="submit" style="width:100%;border:none;background-color:#fff;">
					<img src="images/camera_disabled.jpg" id="camera_alerts_url" style="width:100%;cursor:pointer;" title=""/>
				</button>
			</form>
			

			<div style="font-size:12px;background-color:#333;color:#fff;border:1px solid #333;width:100%;overflow:hidden;height:20px;">
				<div style="text-align:left;padding:2px;padding-left:5px;float:left;width:170px;"><!-- Name Here --></div>
		
				<div style="float:right;display:inline;padding:0px;">
					<div style="text-align:left;padding:2px;float:left;padding-right:10px;">Activity: &nbsp;</div>
					<div id="CameraModal_ActSignal_alert" title="Room Activity" style="display:inline-block;width:10px;height:18px;background-color:#fff;border-bottom-right-radius: 6px;"></div>										
				</div>
			</div>
		
		</div>
	  </div>
	</div>
	<a href="#" style="display:none;" data-toggle="modal" data-target="#CameraModal_alerts" id="ShowCameraModalBtn"></a>
	<a href="#" style="display:none;" data-dismiss="modal" id="ShowCameraModalCloseBtn"></a>
</div>	



<!-------- UI notification Message Modal ------>
<!-- Modal -->
<div class="modal fade" id="UINotification_MessageModal" role="dialog" style="z-index:99999;margin-top:50px;">
	<div class="modal-dialog" style="width:80vw;max-width:800px;">
	
	  <!-- Modal content-->
	  <div class="modal-content" style="max-height:100vh;overflow:auto;">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  <h4 style="" class="modal-title" id="UINotificationModal_MessageTitle"></h4>
		</div>
		<div class="modal-body" style="overflow:auto;" >
			<img src="" id="UINotificationModal_Image" style="min-height:300px;height:100%;width:40%;vertical-align: top;"/>
			<p id="UINotificationModal_Message" style="display:inline-block;width:55%;padding:10px;"></p>
		</div>
	  </div>
	</div>
	<a href="#" data-dismiss="modal" id="UINotification_MessageModalCloseBtn" style="display:none"></a>
	<a href="#" data-toggle="modal" data-target="#UINotification_MessageModal" id="UINotification_MessageModalBtn" style="display:none"></a>
</div>	





<!-------- Account Settings Modal ------>
<!-- Modal -->
<div class="modal fade" id="userAccountSettingsModal" role="dialog" style="z-index:99999;margin-top:100px;">
	<div class="modal-dialog">
	
	  <!-- Modal content-->
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  <h4 style="" class="modal-title">Account Settings</h4>
		</div>
		<div class="modal-body" style="overflow:auto;">
			
			<form method="POST" enctype="multipart/form-data">
				<div style="margin-bottom:20px;">
					<img onclick="$('#userAccountSettingsFile').click();" src="images/users/<?php echo $_SESSION['user_img'];?>" id="userAccountSettingsImage" style="height:80px;width:80px;display:inline-block;cursor:pointer;"/>
					<div style="display:none;"><input type="file" name="user_img" id="userAccountSettingsFile"/></div>
					
					<div style="width:80px;background-color:rgba(255,255,255,0.5);height:20px;position:absolute;margin-top:-20px;text-align:center;cursor:pointer;" onclick="$('#userAccountSettingsFile').click();">
						<i class="fa fa-camera"></i>&nbsp;
						<span style="font-size:12px;"><b>Change</b></span>
					</div>
					
					<h3 style="display:inline-block;"><?php echo $_SESSION['name'];?></h3>
				</div>
				
				
				<h4 style="font-size:18px;"><b>Change Password:</b></h4>
				<h5 style="text-align:center;" id="userAccountSettingsMessage"></h5>
			
				<input type="hidden" name="type" value="<?php echo temp_encode("user_accountSettings");?>"/>
				<div class="input-group" style="margin:10px;">
					<span class="input-group-addon" style="width:142px;"><b>Old Password</b></span>
					<input type="password" class="form-control1" name="old"/>
				</div>
				<div class="input-group" style="margin:10px;">
					<span class="input-group-addon" style="width:142px;"><b>New Password</b></span>
					<input type="password" class="form-control1" name="new1"/>
				</div>
				<div class="input-group" style="margin:10px;">
					<span class="input-group-addon" style="width:142px;"><b>Retype Password</b></span>
					<input type="password" class="form-control1" name="new2"/>
				</div>
			
				<div class="modal-footer">
				  <button type="submit" class="btn btn-primary">Save</button>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>

			</form>
			
			<script>
				function readURL(input) { /*Image Preview Function */
					if (input.files && input.files[0]) {
						var reader = new FileReader();
						reader.onload = function (e) {
							$('#userAccountSettingsImage').attr('src', e.target.result);
						}
						reader.readAsDataURL(input.files[0]);
					}
				}

				$("#userAccountSettingsFile").change(function(){readURL(this);});
			
			
				function userAccountSettingsMessage(message,color){
					setTimeout(function(){
						$("#userAccountSettingsBtn").click();
						$("#userAccountSettingsMessage").html(message);
						$("#userAccountSettingsMessage").css("color",color);
					},1000);							
				}
			</script>
			
			<?php if($userAccountSettingsLoginError=="error"):?>
				<script>userAccountSettingsMessage("There was an error changing your password.<br/> Please use a password with at least 4 characters.",'red');</script>
			<?php elseif($userAccountSettingsLoginError=="no_error") :?>
				<script>userAccountSettingsMessage("Password changed successfully.",'green');</script>
			<?php endif;?>
	
		</div>
	  </div>
	</div>
</div>


<!--- Find Sensor --->
  <!-- Modal -->
	<div class="modal fade" id="find_sensor_modal" role="dialog" style="z-index:99999;margin-top:200px;">
		<div class="modal-dialog" style="width:400px;">
			  <!-- this open the that more dialog -->
			  <a href="#" data-toggle="modal" data-target="#find_sensor_modal" id="FindSensorDialogLink" style="display:none;"></a>
			  <!-- Modal content-->
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 style="" class="modal-title" >Find <?php echo $deviceOrSensor;?></h4>
				</div>
				<div class="modal-body" style="overflow:auto;">

					<center>Activate The New <?php echo $deviceOrSensor;?> To Find It</center>

					<center id="find_sensor_loading"><i style="font-size:20px;" class="fa fa-spinner faa-spin animated"></i> Searching</center>
					<style>
						table td {  height: 50px; }
					</style>
					<input type="hidden" id="submited_sensor" value="" />

					<div style="height:auto;overflow:auto;" id="find_sensor_autoload"></div>
					
					<!--send command to find sensors in settings table -->
					<form method="POST" class="autoform">
						<input type="hidden" name="type" value="<?php echo temp_encode("start_find_sensors");?>" />
						<input type="submit" id="start_find_sensors_btn" style="display:none;" />
					</form>
					<form method="POST" class="autoform">
						<input type="hidden" name="type" value="<?php echo temp_encode("end_find_sensors");?>" />
						<input type="submit" id="end_find_sensors_btn" style="display:none;" />
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<script>
		function find_sensor(sensor) {
			$('#end_find_sensors_btn').click();
			$('#start_find_sensors_btn').click();
			$('#find_sensor_table').html("");
			$('#submited_sensor').val(sensor);
			$("#FindSensorDialogLink").click();	
			$("#find_sensor_loading").show();		
		}

		function set_sensor_address(address) {
			$('#end_find_sensors_btn').click();
			$('#' + $('#submited_sensor').val()).val(address);
		}
	</script>