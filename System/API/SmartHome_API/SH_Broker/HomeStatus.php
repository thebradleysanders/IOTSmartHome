<?php
##############################################################################
/*///////////////////////////////////////////////////////////////////////////
	ABOUT THIS PAGE:
		This page contains all the needed apis and functions to check user pressence
	
	SmartHome - Created By: Brad Sanders
///////////////////////////////////////////////////////////////////////////*/
##############################################################################
if (php_sapi_name() != "cli") { //close if not ran in CLI (Commnad Line)
	echo "This Page Must Be Ran Via PHP CLI. Use The SmartHome Broker.";
	exit;
}


$GS_phueServiceBypass = true;
$GS_wemoServiceBypass = true;
$GS_mqttServiceBypass = true;
$GS_emailServiceBypass = true;
$GS_weatherServiceBypass = true;
$GS_squeezeBoxServiceBypass = true;
$GS_webIncludesIncluded = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require(realpath(__DIR__ . "/../DBConn.php"));
require(realpath(__DIR__ . "/../IOTIncludes.php"));
//icloud api
require_once(realpath(__DIR__ ."/../../ICloud_API/FindMyiPhone.php"));

function convertAddressToCord($Address){	
  $Address = urlencode($Address);
  $request_url = "https://maps.googleapis.com/maps/api/geocode/xml?address=".$Address."&sensor=true";
  $xml = simplexml_load_file($request_url) or die("url not loading");
  $status = $xml->status;
  if ($status=="OK") {
      $Lat = $xml->result->geometry->location->lat;
      $Lon = $xml->result->geometry->location->lng;
  }
  return $Lat.",".$Lon;
}

function distance($lat1, $lon1, $lat2, $lon2) {
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;

  return $miles;
}


//update latLong from address in settings table
if($GS_settings['home_latLong']==""){
	$homeAddress=$GS_settings['home_address']." ".$GS_settings['city_state'];
	$latlong=convertAddressToCord($homeAddress);
	mysqli_query($GS_DBCONN, "UPDATE settings SET home_latLong='".$latlong."' WHERE ID='1'" );
}else{
	//get home address lat/long form settings table
	$homelatLong = explode(",",$GS_settings['home_latLong']);
	$homeLat=$homelatLong[0];
	$homeLong=$homelatLong[1];
}


while (true){ 
	
	//refresh system services settings
	require(realpath(__DIR__ . "/../SystemServicesEnableCheck.php"));
		
	$query    = "SELECT * FROM whoishome WHERE check_method='icloud' AND enabled='1'";
	$results= mysqli_query($GS_DBCONN, $query);
	while ($user = mysqli_fetch_assoc($results)) {
		//Get user info from users table
		$user_info=mysqli_fetch_assoc(mysqli_query($GS_DBCONN, "SELECT * FROM users WHERE ID='".$user['user_id']."'"));
		
		//update latLong from address in users table
		if($user_info['user_workLatLong']==""){
			$latlong=convertAddressToCord($user_info['user_workAddress']." ".$GS_settings['city_state']);
			mysqli_query($GS_DBCONN, "UPDATE users SET user_workLatLong='".$latlong."' WHERE ID='".$user['user_id']."'" );
			continue; //skip the rest as there is no data to compare until the next run
		}
		
		//get work lat long from users table
		$user_WorkLatLong= explode(",", $user_info['user_workLatLong']);
		$workLat=$user_WorkLatLong[0];
		$workLong=$user_WorkLatLong[1];
		
		//get icloud username/password and settings
		$login = explode("|",$user['check_string']);
		
		// This is where we log in to iCloud
		try {
			$fmi = new FindMyiPhone($login[1], $login[2],false);
		} catch (Exception $e) {
			GF_logging( "ICloud Error: ".$e->getMessage());
			exit;
		}
		$fmi->printDevices2(); //get devices
		// Find a device that is reporting its location and attempt to get its current location
		foreach ($fmi->devices as $device) {
			if ($device->location->timestamp != "") {
				// Locate the device
				$location = $fmi->locate($device->ID);
				
				$deviceID=$device->ID;
				$displayName=$device->displayName;
				$batteryStatus=$device->batteryStatus;
				$batteryLevel=$device->batteryLevel*100;
				$positionType=$location->positionType;
				$long =$location->longitude;
				$lat = $location->latitude;
				
				//get distance
				$distanceFromHome = distance((float)$lat, (float)$long, (float)$homeLat, (float)$homeLong);
				$distanceFromWork = distance((float)$lat, (float)$long, (float)$workLat, (float)$workLong);
		
				//Home
				if($distanceFromHome <= (float)$login[4]){ //home
					$userHome = "true";
				}elseif($distanceFromHome > (float)$login[4]){//not home
					$userHome = "false";
				}
				
				if($distanceFromWork <= (float)$login[4]){ //work
					$userWork = "true";
				}elseif($distanceFromWork > (float)$login[4]){//not work
					$userWork = "false";
				}
				
				$sensorDataTitleArray="ID:Display Name:Battery Status:Battery Level:UserHome:UserWork:Position Type:Latitude:Longitude";
				$sensorDataValueArray=$deviceID.":".$displayName.":".$batteryStatus.":".$batteryLevel.":".$userHome.":".$userWork.":".$positionType.":".$lat.":".$long;
				$sensorDataVisibleArray="0:1:0:1:1:1:0:0:0";
				InsertUpdate_DataSensor($displayName,$sensorDataTitleArray,$sensorDataValueArray,$sensorDataVisibleArray,"ICloud",$room);
				
				// Play a sound on the device
				//$fmi->playSound($device->ID, "You've been located!");
				
				// Send Message to the device
				//$fmi->sendMessage($device->ID, "SmartHome", "You've been located!");
				
				// Lock the device
				//$fmi->lostMode($device->ID, "You got locked out", "555-555-5555");
			}
		}		
	}
	sleep(60);
}

