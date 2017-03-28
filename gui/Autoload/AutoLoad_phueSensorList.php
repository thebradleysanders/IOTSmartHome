<?php
$GS_phueServiceBypass = false;
$GS_wemoServiceBypass = true;
$GS_mqttServiceBypass = true;
$GS_emailServiceBypass = true;
$GS_squeezeBoxServiceBypass = true;
$GS_webIncludesIncluded = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require(realpath(__DIR__ ."/../../System/API/SmartHome_API/DBConn.php"));
require(realpath(__DIR__ ."/../../System/API/SmartHome_API/IOTIncludes.php"));
if($GS_phueServiceEnabled == true){ /* Check If Service Is Enabled */}else{exit;}
?>

<select name="sensor_number" id="sensor_number" class="form-control1" style="height:30px;width:100%;margin-bottom:10px;" >
	<?php
		$sensor_count=0;
		foreach ($GS_phueService->getSensors() as $sensor){
			$sensorID = "phue:".str_replace(":","",$sensor['uniqueid']);
			$query = "SELECT ID FROM sensors WHERE sensor_type='phue' AND sensor_address='phue:".$sensorID."' LIMIT 1";
			$results = mysqli_query($GS_DBCONN, $query);
			$result_count = mysqli_num_rows($results);
			if($result_count>0){continue;}
			$sensor_count++;
	?>
		<option value="<?php echo $sensorID;?>">
			<?php echo "Name: ".$sensor['name'];?>
			<?php echo "Type: ".$sensor['type'];?>
		</option>
	<?php } if ($sensor_count==0):?>
		<option value="" disabled>No New Hue Sensors Found</option>
	<?php endif;?>
</select>	

