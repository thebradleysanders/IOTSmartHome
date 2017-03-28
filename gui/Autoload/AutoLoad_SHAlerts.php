<?php
$query = "SELECT user_permissions,type FROM users WHERE ID='".temp_decode($_GET['user_id'])."' AND enabled='1'";
$permission_results = mysqli_query($GS_DBCONN, $query);
$permis_result = mysqli_fetch_assoc($permission_results);

 /******************************** CAMERA ALERTS ***********************************/
	$totalCount=0;
	$query = "SELECT * FROM sensors WHERE enabled='1' AND sensor_state='1' ORDER BY ID ASC";
	$results1 = mysqli_query($GS_DBCONN, $query);
	while($sensor_info=mysqli_fetch_assoc($results1)){ 
		$query = "SELECT * FROM camera_list WHERE sensor_assign='".$sensor_info['ID']."' AND enabled='1' ";
		$camera_info=mysqli_fetch_assoc(mysqli_query($GS_DBCONN, $query));
		//Check Permissions
		if(GetUserPermissions($camera_info['ID'],"manage_cameras.php",$permis_result['user_permissions'])==false){continue;}else{$totalCount++;}
	}


	$query = "SELECT * FROM sensors ORDER BY ID ASC";
	$results1 = mysqli_query($GS_DBCONN, $query);
	while($sensor_info=mysqli_fetch_assoc($results1)){ 
		$query = "SELECT * FROM camera_list WHERE sensor_assign='".$sensor_info['ID']."' AND enabled='1' ";
		$results2 = mysqli_query($GS_DBCONN, $query);
		$camera_info=mysqli_fetch_assoc($results2);		
		//Check Permissions
		if(GetUserPermissions($camera_info['ID'],"manage_cameras.php",$permis_result['user_permissions'])==false){continue;}else{$camera_count++;}
?>
	<?php if ($sensor_info['sensor_state']=="1" && $sensor_info['enabled']=="1" && $camera_count>0):?>		
		<script>
			$("#CameraAlert<?php echo $camera_info['ID'];?>").animate({"width":"<?php echo 100/$totalCount;?>%"},500);	
			setTimeout(function(){
				$("#CameraAlert<?php echo $camera_info['ID'];?>").css({"display":"inline-block"});
			},500)
		</script>
	<?php else :?>
		<script>			
			$("#CameraAlert<?php echo $camera_info['ID'];?>").animate({"width":"0px"},500);
			setTimeout(function(){
				$("#CameraAlert<?php echo $camera_info['ID'];?>").css({"display":"none"});
			},400)
		</script>
	<?php endif;?>
	
<?php }?>

<?php /******************************** Node ALERTS ***********************************/
	$query = "SELECT * FROM iot_nodes ORDER BY last_connected_time ASC";
	$results1 = mysqli_query($GS_DBCONN, $query);
	$node_count=mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM iot_nodes WHERE enabled='1' AND notifications='1' AND state='Disconnected'"));
	while($node_info=mysqli_fetch_assoc($results1)){ 
?>
	<?php if ($node_info['state']=="Disconnected" && $node_info['enabled']=="1" && $node_info['notifications']=="1"):?>
		<script>
			$("#NodeAlert<?php echo $node_info['ID'];?>").animate({"width":"<?php echo 100/$node_count;?>%"},500);
			setTimeout(function(){
				$("#NodeAlert<?php echo $node_info['ID'];?>").css({"display":"inline-block"});
			},500);
		</script>
	<?php else :?>
		<script>
			$("#NodeAlert<?php echo $node_info['ID'];?>").animate({"width":"0px"},500);
			setTimeout(function(){
				$("#NodeAlert<?php echo $node_info['ID'];?>").css({"display":"none"});
			},400)
		</script>
	<?php endif;?>
	
<?php }

	$query = "SELECT * FROM sensors ORDER BY sensor_state ASC";
	$results = mysqli_query($GS_DBCONN, $query);
	while($result = mysqli_fetch_assoc($results)) { 
?>		
	<?php if ($result['sensor_state']=='1' && $result['enabled']=='1'):?>
		<script>
			$('.featured_camera_alert_id<?php echo $result['room'];?>').css({"background-color":"rgb(239, 85, 58)"});
			$('.featured_camera_alert_id<?php echo $result['room'];?>').fadeOut(500);
			$('.featured_camera_alert_id<?php echo $result['room'];?>').fadeIn(200);
		</script>
	<?php elseif($result['sensor_state']=='0' && $result['enabled']=='1') :?>
		<script>
			$('.featured_camera_alert_id<?php echo $result['room'];?>').css({"background-color":"<?php echo $GS_Config['themeColorSub'];?>"});
			$('.featured_camera_alert_id<?php echo $result['room'];?>').fadeIn();
		</script>
	<?php elseif($result['enabled']=='0'):?>
		<script>
			$('.featured_camera_alert_id<?php echo $result['room'];?>').css({"background-color":"white"});
			$('.featured_camera_alert_id<?php echo $result['room'];?>').fadeIn();
		</script>
	<?php endif;?>
		
<?php } 
	

