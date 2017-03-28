<?php
	
	//toggle device POST
	if(temp_decode($_POST['type'])=="toggle_deviceOn"){
		$query    = "SELECT * FROM devices WHERE ID='".clean_text($_POST['device_id'],11)."' LIMIT 1";
		GF_transmitToDevice($query, "1", "100", "noColor", "noEffect", "USER:".$_SESSION['id']);
	}
	
	if(temp_decode($_POST['type'])=="toggle_deviceOff"){
		$query = "SELECT * FROM devices WHERE ID='".clean_text($_POST['device_id'],11)."' LIMIT 1";
		GF_transmitToDevice($query, "0", "0", "noColor", "noEffect", "USER:".$_SESSION['id']);
	}
	
	//toggle device GET
	if(temp_decode($_GET['type'])=="toggle_device"){  
		$query = "SELECT * FROM devices WHERE ID='".clean_text($_GET['device_id'],50)."' LIMIT 1";
		GF_transmitToDevice($query, clean_text($_GET['state'],1), clean_text($_GET['brightness'],1), "noColor", "noEffect", "USER:".$_SESSION['id']);
	}
	 
	//turn on all devices
	if(temp_decode($_GET['type'])=="All_Devices_On"){
		$query = "SELECT * FROM devices WHERE device_state='0' AND enabled='1'";
		GF_transmitToDevice($query, "1", "100", "noColor", "noEffect", "USER:".$_SESSION['id']);
		echo "<script>location.href='?';</script>";
	}
	
	//turn off all devices
	if(temp_decode($_GET['type'])=="All_Devices_Off"){
		$query = "SELECT * FROM devices WHERE enabled='1'";
		GF_transmitToDevice($query, "0", clean_text($_POST['brightness'],5), "noColor", "noEffect", "USER:".$_SESSION['id']);
		echo "<script>location.href='?';</script>";
	}

	
	//alarm Functions
	if (php_sapi_name() != "cli"){
		if(strpos($_SERVER["PHP_SELF"],"Autoload/")!==false){}else{
			if(GetUserPermissions("edit","alarm_access")==true ){ //check and see if current user has permission
				$query = "SELECT ID FROM users WHERE alarm_pin='".$_POST['pin']."' AND alarm_pin<>'0' AND enabled='1' LIMIT 1";
				$results = mysqli_query($GS_DBCONN, $query);
				$correct = mysqli_num_rows($results);
			
				if(temp_decode($_POST['type'])=="change_alarm_state" && $correct>0 ){
					GF_ChangeAlarmState("",clean_text($_POST['state'],1));
				}
				if(temp_decode($_POST['type'])=="change_alarm_mode"){
					GF_ChangeAlarmState(clean_text($_POST['mode'],25),"");	
				}
			}
		}
	}
	
	//camera click event
	if(temp_decode($_POST['type'])=="camera_click_event"){
		GF_ThenThat(clean_text($_POST['click_event'],500),"USER:".$_SESSION['id']);
	}
	
	//Change Account Settings
	if(temp_decode($_POST['type'])=="user_accountSettings"){
		$query = "SELECT ID FROM users WHERE password='".$_POST['old']."' AND ID='".$_SESSION['id']."' LIMIT 1";
		$correct = mysqli_num_rows(mysqli_query($GS_DBCONN, $query));
		if($correct==1 && $_POST['new1']==$_POST['new2'] && strlen(trim($_POST['new1']))>=4 && strlen(trim($_POST['new1']))<=100){
			mysqli_query($GS_DBCONN,"UPDATE users SET password='".trim($_POST['new1'])."' WHERE password='".trim($_POST['old'])."' AND ID='".$_SESSION['id']."'");
			$userAccountSettingsLoginError = "no_error";
		}elseif(trim($_POST['new1'])!="" || trim($_POST['old'])!="" || trim($_POST['new2'])!=""){
			$userAccountSettingsLoginError = "error";
		}
		
		//image upload
		$image = upload($_FILES["user_img"], "images/users/");
		if($image!=false){
			mysqli_query($GS_DBCONN,"UPDATE users SET user_img='".$image."' WHERE ID='".$_SESSION['id']."'");
			$_SESSION['user_img'] = $image;
		}
		
	}
	 
	//System Function Shutdown/restart

	//Restart
	if(temp_decode($_GET['type'])=="Restart_SmartHome" && $_SESSION['type']=="Admin"){
		GF_logging("<span style='color:red'>System Event:</span> The User ".$_SESSION['name']." (ID:".$_SESSION['id'].") is Restarting SmartHome");       
		GF_restart_smarthome();
		echo "<script>location.href=location.href='?';</script>";
	}	
	//Shutdown
	if(temp_decode($_GET['type'])=="Shutdown_SmartHome" && $_SESSION['type']=="Admin"){
		GF_logging("<span style='color:red'>System Event:</span> The User ".$_SESSION['name']." (ID:".$_SESSION['id'].") is Shutting Down SmartHome");       	
		GF_shutdown_smarthome();
		echo "<script>location.href=location.href='?';</script>";
	}	
	
	
	//fix permssions on linux: chmod 777 /var/www/html/sh/gui/images/users/
	################################### Upload File #########################################
	function upload($file, $path){
		//File Upload
		if(!empty($file["name"])){
			$target_dir = $path;
			$target_file = $target_dir . basename($file["name"]);
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			// Check if image file is a actual image or fake image

			$check = getimagesize($file["tmp_name"]);
			if($check !== false) {
				$uploadOk = 1;
			} else {
				$uploadOk = 0;
			}
			
			// Check if file already exists 
			if (file_exists($target_file)) {
				//echo "<script>alert('Sorry, file already exists.');</script>";
				$uploadOk = 1;
			}
	
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 1) {
				if (move_uploaded_file($file["tmp_name"], $target_file)) {
					//echo "The file ". basename( $file["name"]). " has been uploaded.";
				} else {
					echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
				}
			}
			
			if ($uploadOk == 0) {
				return false;
			}else{
				return basename($file["name"]);
			}
			
		}
	}
	
	
	//define excryption key
	define("ENCRYPTION_KEY", $GS_Config['EncryptKey']);

	################################### Encrypt Function #########################################
	function encrypt($encrypt, $key){
		$encrypt = serialize($encrypt);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
		$key = pack('H*', $key);
		$mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
		$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
		$encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
		return $encoded;
	}

	################################### Decrypt Function #########################################
	function decrypt($decrypt, $key){
		$decrypt = explode('|', $decrypt.'|');
		$decoded = base64_decode($decrypt[0]);
		$iv = base64_decode($decrypt[1]);
		if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
		$key = pack('H*', $key);
		$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
		$mac = substr($decrypted, -64);
		$decrypted = substr($decrypted, 0, -64);
		$calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
		if($calcmac!==$mac){ return false; }
		$decrypted = unserialize($decrypted);
		return $decrypted;
	}

	################################### Clean Text #########################################
	function clean_text($text, $length){
	   return substr(trim($text), 0, $length);
	}

	
	################################### Temp Encode #########################################
	function safe_encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+','/','='),array('!','_',''),$data);
		return $data;
	}

	################################### Safe Decode #########################################
	function safe_decode($string) {
		$data = str_replace(array('!','_'),array('+','/'),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}

	################################### Temp Encode #########################################
	function temp_encode($string) {
		$string = "|".date("m-d-Y")."|".$string;
		$data = base64_encode($string);
		$data = str_replace(array('+','/','='),array('!','_',''),$data);
		return $data;
	}

	################################### Temp Decode #########################################
	function temp_decode($string) {
		$data = str_replace(array('!','_'),array('+','/'),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		$textArray = base64_decode($data);	
		$array = explode("|",$textArray);
		
		if($array[1]==date("m-d-Y")){
			return $array[2];
		}else{
			return "[EXPIRED]";
		}
	}
	
	######################################### Time Ago ######################################
	function ago($datetime, $full = false) {
		if($datetime==""){return;}
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	############################################# Ajust HEX ################################
	function adjustBrightness($hex, $steps) {
		// Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max(-255, min(255, $steps));

		// Normalize into a six character long hex string
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
		}

		// Split into three parts: R, G and B
		$color_parts = str_split($hex, 2);
		$return = '#';

		foreach ($color_parts as $color) {
			$color   = hexdec($color); // Convert to decimal
			$color   = max(0,min(255,$color + $steps)); // Adjust color
			$return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
		}

		return $return;
	}
	
	function checkURL($url){
		if(@get_headers($url)[0] == 'HTTP/1.1 404 Not Found'){
			return false;
		}else{
			return true;
		}
	}
	
	
	##################################### Process Login #######################################
	
	function processLogin($username, $password, $lognBool){ //if $loginBool=true then store session
		global $GS_Config;
		global $GS_DBCONN;
		
		$username= preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', substr($username,0,100));
		$password= preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', substr($password,0,100));
		
		if($username!=''){	
			$query = "SELECT * FROM users WHERE username='".$username."' AND password='".$password."'";
			$results = mysqli_query($GS_DBCONN, $query);
			$result = mysqli_fetch_assoc($results);
			$result_count = mysqli_num_rows($results);
			
			if($result_count==0){
				//wrong username/password
				return "error|wrong_password|Incorrect Username/Password";
			}else{
				if($result['enabled']=="1"){
					//get welcome message
					if($result['last_login']=="0"){$welcomeMessage="Welcome, ".$result['user_name']."!";}else{$welcomeMessage="Welcome Back, ".$result['user_name']."!";}
					
					if($lognBool==true){
						$_SESSION['id']=$result['ID'];
						$_SESSION['name']=$result['user_name'];
						$_SESSION['username']=$result['username'];
						$_SESSION['password']=$result['password'];
						$_SESSION['last_login']=$result['last_login'];
						$_SESSION['type']=$result['type'];
						
						if($result['user_img']==""){
							$_SESSION['user_img'] = "Default.png";
						}else{
							$_SESSION['user_img'] = $result['user_img'];
						}
						
						ini_set('session.gc_maxlifetime', 3600*24*$GS_Config['MaxLoginTime']); // Default: 30 days
						ini_set('session.gc_probability', 1);
						ini_set('session.gc_divisor', 100);
						ini_set('session.cookie_secure', false);
						ini_set('session.use_only_cookies', true);
						
						$localIP = getHostByName(getHostName());
						$insert_query="UPDATE users SET last_login='".time()."', last_access_ip='".$localIP."' WHERE ID='".$result['ID']."'";
						mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
						
						//set PHP Cookie
						setcookie("lastLogin", safe_encode($result['ID']), time() + (86400 * 30), "/"); // 86400 = 1 day
						
						
					}
					
					return "success|".$welcomeMessage."|".$result['user_img']."|";
				}else{
					//account is disabled
					return "error|account_disabled|".explode(" ",$result['user_name'])[0].", Your Account is Disabled.";
				}
			}
		}
	}
	