<?php	
	// This page is for manage_users.php for the user geolocation 
			
	if(temp_decode($_GET['type'])=="icloud"){
		require_once('C:\inetpub\wwwroot\sh\system\API\ICloud_API\FindMyiPhone.php');
		
		$query    = "SELECT * FROM whoishome WHERE check_method='icloud' AND user_id='".temp_decode($_GET['userID'])."'";
		$results= mysqli_query($GS_DBCONN, $query);
		$user = mysqli_fetch_assoc($results);
		
		//get icloud username/password and settings
		$login = explode("|",$user['check_string']);
		
		//login with username and password
		$FindMyiPhone = new FindMyiPhone($login[1], $login[2]);
		// get the device ids for each device found
		$count = -1;
		foreach ($FindMyiPhone->devices as $device){ $count++; ?>
			<option value="<?php echo $count;?>">
				<?php echo $device->deviceDisplayName;?> - <?php echo $device->name;?>
			</option>
	<?php }
	}

		
		
		