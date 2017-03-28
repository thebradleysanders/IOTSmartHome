<?php
##########################################################################
########################### UI Notications ###############################
##########################################################################
		
$query    = "SELECT * FROM UI_notifications WHERE user_id='".temp_decode($_GET['user_id'])."' OR user_id='0'";
$results_notification = mysqli_query($GS_DBCONN, $query);
while ($notification_info = mysqli_fetch_assoc($results_notification)) {
	mysqli_query($GS_DBCONN, "DELETE FROM UI_notifications WHERE ID='".$notification_info['ID']."'"); 
	$data = explode("|",$notification_info['notificatonData']); ?>
	

	<?php if($notification_info['notificationType']=="MessageModal"):?>
		<script>
			$("#UINotificationModal_MessageTitle").text("<?php echo $data[0];?>");
			$("#UINotificationModal_Message").html("<?php echo $data[1];?>");
			$("#UINotificationModal_Image").attr("src","http://<?php echo $data[2];?>");
			$("#UINotification_MessageModalBtn").click();
			
			setTimeout(function(){$("#UINotification_MessageModalCloseBtn").click();},<?php echo $data[3];?>000);
		</script>
	<?php elseif($notification_info['notificationType']=="CameraModal"):?>
		<script>
			showCameraModal<?php echo $data[0];?>();
			setTimeout(function(){$("#ShowCameraModalCloseBtn").click();},<?php echo $data[1];?>000);
		</script>
	<?php elseif($notification_info['notificationType']=="AlarmModal"):?>
		<script>
			$("#AlarmDialogModalBtn").click();
			setTimeout(function(){$("#AlarmDialogModalCloseBtn").click();},<?php echo $data[0];?>000);
		</script>
	<?php endif;?>

<?php }
	
	