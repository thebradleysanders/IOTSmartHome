<?php
if(!empty($_POST) || !empty($_GET['triggerButton'])){
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
	include("Autoload/AutoLoad_ButtonStates.php");
	include("Autoload/AutoLoad_DataStates.php");
	exit;	
}
################################################

#################### API #######################
$page_title="Manage Buttons"; 
$upParrentDir = '/../../..';
require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));

################################################


	//update buttons
	if(temp_decode($_POST['type'])=='edit_button_modal' && GetUserPermissions("edit")==true){
		$insert_query="UPDATE buttons SET 
		button_name='".clean_text($_POST['button_name'],200)."',
		button_address='".clean_text($_POST['button_address'],200)."',
		button_event='".clean_text($_POST['thenThat1'],500)."',
		button_event2='".clean_text($_POST['thenThat2'],500)."',
		button_icon='".clean_text($_POST['button_icon'],50)."',
		room='".clean_text($_POST['room'],11)."',
		last_changed_by='".$_SESSION['id']."'
		WHERE ID='".clean_text($_POST['button_ID'],11)."'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
		
	}
	
	//add new button
	if(temp_decode($_POST['type'])=='add_button' && GetUserPermissions("add")==true){
		$addbutton_max=5;
		$addbutton_count=0;
		While($addbutton_count <$addbutton_max ){
			if(trim($_POST['button_name'.$addbutton_count])!=""){

				$insert_query="INSERT INTO buttons (button_name,button_address,button_state,button_event,button_event2,room,button_icon,last_triggered,last_changed_by)
				VALUES(
				'".clean_text($_POST['button_name'.$addbutton_count],200)."',
				'".clean_text($_POST['button_address'.$addbutton_count],200)."',
				'0',
				'".clean_text($_POST['thenThat1'.$addbutton_count],500)."',
				'".clean_text($_POST['thenThat2'.$addbutton_count],500)."',
				'".clean_text($_POST['room'.$addbutton_count],11)."',
				'".clean_text($_POST['button_icon'.$addbutton_count],50)."',
				'0',
				'".$_SESSION['id']."')";
				mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
			}
			$addbutton_count++;
		}
		echo "<script>location.href='';</script>";
	}
	
	//delete button
	if($_GET['delete']!='' && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		$insert_query="DELETE FROM buttons WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}
	
	//start find sensors/buttons
	if(temp_decode($_POST['type'])=="start_find_sensors"){
		$insert_query="UPDATE settings SET scan_for_new_sensors='1' WHERE ID='1'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}    
	// end find sensors/buttons
	if(temp_decode($_POST['type'])=="end_find_sensors"){
		$insert_query="UPDATE settings SET scan_for_new_sensors='0' WHERE ID='1'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
		
		//clear found sensors list
		$insert_query="DELETE FROM find_sensors_list";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}  

	if($_GET['triggerButton']!="" && temp_decode($_GET['triggerButton'])!="[EXPIRED]"){
		GF_ifttt(clean_text(temp_decode($_GET['triggerButton']),500),"0",1,"USER:".$_SESSION['id']);
	}
	
?>
         
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px;margin-bottom:30px;">
		<h3>
			Manage Buttons
			<div style="overflow:auto;height:40px;text-align:right;">
				<a href="manage_sensors.php?type=<?php echo temp_encode("motion");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Motion</button></a>
				<a href="manage_sensors.php?type=<?php echo temp_encode("door");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Doors</button></a>
				<a href="manage_sensors.php?type=<?php echo temp_encode("window");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Windows</button></a>
				<a href="manage_sensors.php?type=<?php echo temp_encode("custom");?>"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Custom</button></a>
				<a href="manage_devices.php"><button style="margin-right:5px;" type="button" class="btn btn-primary btn-sm">Devices</button></a>
			</div>
		</h3>
	
		
		<?php if (temp_decode($_GET['type'])!='add') :?>	
			<?php
				// Find out how many items are in the table
			   $total = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM buttons"));
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
						   <?php echo $result['name'];?> 
						   <div style="text-align:center;font-size:35px;margin-top:-10px;">
							 <i class="fa fa-plus"></i>
						   </div>   
						   <div style="">
								<?php if(GetUserPermissions("add")==true):?>
									 <a href="?type=<?php echo temp_encode("add");?>"><button type="button" class="btn btn-primary" style="width:100%;" >Add New</button></a>
								<?php else:?>
									 <button type="button" class="btn btn-primary" disabled title="You do not have permission" style="width:100%;">Add New</button>
								<?php endif;?>
							</div>  
						</div>
					</div>
					
					<?php
					$count = 0;
					$query = "SELECT * FROM buttons ORDER BY ID LIMIT ".$limit." OFFSET ".$offset;
					$results = mysqli_query($GS_DBCONN, $query);
					while($result = mysqli_fetch_assoc($results)) { $count++;?>

						<div class="well1 col-xs-12 col-sm-5 col-md-3 col-lg-2 " style="padding:5px;height:120px;min-width:200px;background-color:#f5f5f5;margin-bottom:5px;">
							<div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">       
							   <p style="width:100%;height:50px;overflow:auto;">
									<?php echo $result['button_name'];?><br/>
									<span style="font-size:12px;"><?php if($result['last_triggered']!="0"){ echo ago($result['last_triggered']);}?></span>
							   </p>
	
							   <div style="text-align:center;">
									<div id="button<?php echo $result['ID'];?>_not_active" style="display:inline-block;">
										<a href="?triggerButton=<?php echo temp_encode($result['button_address']);?>">
										   <button type="button" class="btn btn-default">
												<i class="fa <?php if($result['button_icon']==''){echo "fa-power-off";}else{echo $result['button_icon'];}?>"></i>&nbsp;Trigger
										   </button>
										</a>
									</div>
									<div id="button<?php echo $result['ID'];?>_active" style="display:inline-block;">
										<a href="?triggerButton=<?php echo temp_encode($result['button_address']);?>">
										   <button type="button" class="btn btn-primary">
											 <i class="fa <?php if($result['button_icon']==''){echo "fa-power-off";}else{echo $result['button_icon'];}?>"></i>&nbsp;Trigger
										   </button>
										</a>
									</div>
									
								   
								   <?php if(GetUserPermissions("edit")==true):?>
										 <button data-toggle="modal" data-target="#button_edit_modal"  onclick="edit_button(
											 '<?php echo $result['ID'];?>',
											 '<?php echo $result['button_name'];?>',
											 '<?php echo $result['button_address'];?>',
											 '<?php echo $result['button_icon'];?>',
											 '<?php echo $result['button_event'];?>',
											 '<?php echo $result['button_event2'];?>',
											 '<?php echo $result['room'];?>' );"
											type="button" class="btn btn-default"><i class="fa fa-pencil"></i></button>  
									<?php else:?>
										<button type="button" class="btn btn-default" disabled title="You do not have permission"><i class="fa fa-pencil"></i></button>  
									<?php endif;?>
									
									<?php if (GetUserPermissions("delete")==true):?>
										<a onclick="return confirm('Are You Sure You Want To Delete This?');" href="?delete=<?php echo temp_encode($result['ID']);?>">
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
		
	<?php else :?>
		<style>
			table {table-layout: fixed;}
			table td {overflow: hidden;}
		</style>
		<!------------------- ADD BUTTON ------------------>
		<form method="POST" style="margin-bottom:30px;">
			<table class="table">
				<col width="100">
				<col width="120">
				<col width="150">
				<col width="150">
				<col width="150">
				<col width="100">

				<thead>
					<tr>
						<th>Name</th>
						<th>Address</th>
						<th>Icon</th>
						<th>Click Event 1</th>
						<th>Click Event 2</th>
						<th>Room</th>
					</tr>
				</thead>
				<?php $i=0; while ($i<5) {?>
				<tr class="active">
					
					<td style="padding-left:10px;"><input type="text" name="button_name<?php echo $i;?>" placeholder="Name" class="form-control1" style="height:30px;width:100%;"/></td>
					<td style="padding-left:10px;">
						<input id="new_button<?php echo $i;?>" type="text" name="button_address<?php echo $i;?>" placeholder="Button Address" class="form-control1" style="height:30px;width:75%;" />
						<input title="Find Button" class="btn btn-default" style="padding:6px;width:20%;height:35px;margin-left:2px;" onclick="find_sensor('new_button<?php echo $i;?>');return false;" type="button" value="..." />
					</td>
					<td style="padding-left:10px;">
						<input id="icon<?php echo $i;?>" type="text" name="button_icon<?php echo $i;?>" placeholder="Button Icon" class="form-control1" style="height:30px;width:100%;" />
					</td>
					<td style="">
						<select name="thenThat1<?php echo $i;?>" onchange="show_that_modal('1New',$(this).val());" id="then_that1New" class="form-control1 " style="width:90%;height:30px;margin-bottom:5px;">
						</select>
					</td>
					<td style="">
						<select name="thenThat2<?php echo $i;?>" onchange="show_that_modal('2New',$(this).val());" id="then_that2New" class="form-control1 " style="width:90%;height:30px;margin-bottom:5px;">
						</select>
					</td>
					<td style="padding-left:30px;">
						<select name="room<?php echo $i;?>" id="room" class="form-control1" style="height:30px;">
							<option value="">No Room</option>
							<?php
							$query = "SELECT * FROM home_rooms";
							$results = mysqli_query($GS_DBCONN, $query);
							while($result = mysqli_fetch_assoc($results)) { ?>
								<option value="<?php echo $result['ID'];?>"><?php echo $result['room_name'];?></option>
							<?php }?>
						</select>
					</td>

				</tr>
				<?php $i++;}?>
			</table>
			<input type="hidden" name="type" value="<?php echo temp_encode("add_button");?>" />
			<input type="submit" value="Add/Save" class="btn btn-primary" style="margin-top:30px;" />
		</form>
	<?php endif;?>



		
		
	<!--########################################### Button Edit Modal ############################# -->

	<!-- Modal -->
	<div class="modal fade" id="button_edit_modal" role="dialog" style="z-index:9999;margin-top:100px;">
		<div class="modal-dialog" style="width:400px;">
		
		  <!-- Modal content-->
		 <form method="Post">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 style="" class="modal-title">Edit <?php echo $result['button_name'];?></h4>
				</div>
				<div class="modal-body" style="height:300px;overflow:auto;">
					<input type="hidden" name="type" value="<?php echo temp_encode("edit_button_modal");?>" />
					<input type="hidden" name="button_ID" value="" id="button_ID" />

					<label for="button_name"><b>Button Name:</b></label>
					<input type="text" name="button_name" value="" id="button_name" style="width:100%;margin-bottom:10px;" class="form-control1"/>
					<label for="button_icon"><b>Button Icon:</b></label>
					<input type="text" name="button_icon" value="" id="button_icon" style="width:100%;margin-bottom:10px;" class="form-control1"/>
					<label for="button_address"><b>Button Address:</b>&nbsp; <a id="modal_get_url_address" href="#">Get Url</a></label>
					<input type="text" name="button_address" value="" id="button_address" style="width:85%;margin-bottom:10px;" class="form-control1"/>
					<button type="button" class="btn btn-default" style="height:38px;width:12%;" onclick="find_sensor('button_address');return false;" title="Find Sensor">...</button>
					<label for="room"><b>Click Event 1:</b></label>
					<select name="thenThat1" onchange="show_that_modal('1',$(this).val());" id="then_that1" class="form-control1 " style="width:100%;height:30px;margin-bottom:5px;">
					</select>
					<label for="room"><b>Click Event 2:</b></label>		
					<select name="thenThat2" onchange="show_that_modal('2',$(this).val());" id="then_that2" class="form-control1 " style="width:100%;height:30px;margin-bottom:5px;">
					</select>
								
					<label for="room"><b>Room:</b></label>
					<select name="room" id="room" style="width:100%;margin-bottom:10px;height:30px;" class="form-control1">
						<option value="">No Room</option>
						<?php
						$query = "SELECT * FROM home_rooms";
						$results = mysqli_query($GS_DBCONN, $query);
						while($result = mysqli_fetch_assoc($results)) { ?>
							<option value="<?php echo $result['ID'];?>"><?php echo $result['room_name'];?></option>
						<?php }?>
					</select>
					
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
		function edit_button(id, name, address, icon, thenThat1,thenThat2,room) {
			$('#button_ID').val(id);
			$('#button_name').val(name);
			$('#button_address').val(address);
			$('#button_icon').val(icon);
			$('#room').val(room);
			
			$('#then_that1').append($("<option></option>").attr("value",thenThat1).text(removeIfttTags("<THEN>","</THEN>",6,thenThat1)));$('#then_that1').val(thenThat1);
			$('#then_that2').append($("<option></option>").attr("value",thenThat2).text(removeIfttTags("<THEN>","</THEN>",6,thenThat2)));$('#then_that2').val(thenThat2);
							
			$('#room').val(room);
			$('#modal_get_url_address').attr("href","");
		}
		
		$(document).ready(function(){
			thenThatList("#then_that1");
			thenThatList("#then_that2");
			thenThatList("#then_that1New");
			thenThatList("#then_that2New");
		});
	</script>
		
	<!--###################################################################################################### -->
				
	</div>
	<div class="clearfix"> </div>

	<?php $deviceOrSensor="Button";?>
	<?php include("includes/findSensorModal.php");?>
	<?php include("includes/iftttOptions.php");//ifttt modals and options ?>	
	<?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
	
	<script>
		setInterval(function(){
			$("#find_sensor_autoload").load("Autoload/AutoLoad_FindNewSensor.php");
		},10000);			
	</script>
</body>
</html>