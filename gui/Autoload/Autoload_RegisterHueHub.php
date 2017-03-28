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

$temp = $GS_phueService->register();;
$decoded = json_decode($temp);

if(trim($decoded[0]->success->username)!=""){
	$key = trim($decoded[0]->success->username);
?>
	<script>
		$("#phueSettingsErrorText").text("");
		$("#PhueSettingsHueKey").val("<?php echo $key;?>");
		$("#phueSettingsCloseBtn").click();
	</script>	
	
<?php
}else{ //error Link button net pressed
	$error= trim(ucwords($decoded[0]->error->description));
?>
	<script>$("#phueSettingsErrorText").text("<?php echo $error;?>");</script>
<?php }

// This page is only used in settings to get user key from Phillips Hue Hub