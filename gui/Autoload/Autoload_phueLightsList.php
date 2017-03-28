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

<select name="light_number" id="light_number" class="form-control1" style="height:30px;width:100%;margin-bottom:10px;" >
	<?php
		$lights = $GS_phueService->lightids();
		$bulb_count=0;
		foreach($lights as $item){ 
			$bulb_count++;
	?>
		<option value="<?php echo $GS_phueService->lights()[$item]->id(); ?>|<?php echo $GS_phueService->lights()[$item]->type();?>">
			<?php echo "Name: ".$GS_phueService->lights()[$item]->name();?>
			<?php echo "Type: ".$GS_phueService->lights()[$item]->type();?>
		</option>
	<?php } if ($bulb_count==0):?>
		<option value="" disabled>No New Hue Devices Found</option>
	<?php endif;?>
</select>