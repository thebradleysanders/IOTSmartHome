<?php
$GS_phueServiceBypass = false;
$GS_wemoServiceBypass = true;
$GS_mqttServiceBypass = true;
$GS_emailServiceBypass = true;
$GS_squeezeBoxServiceBypass = false;
$GS_webIncludesIncluded = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require(realpath(__DIR__ ."/../../System/API/SmartHome_API/DBConn.php"));
require(realpath(__DIR__ ."/../../System/API/SmartHome_API/IOTIncludes.php"));
 


	$mySqueezeConnection = new SqueezeConnection("10.0.0.201","9090","","");
	if ($mySqueezeConnection->connect()){ $mySqueezeCenter = new SqueezeCenter($mySqueezeConnection);}			
	$mySqueezeCenter->Players->count();
	
	$count=0;
	foreach($mySqueezeCenter->Players->Players() as $player) {?>
		<option value="<?php echo $mySqueezeCenter->Players->Players()['players'][$count]['playerindex'];?>">
			<?php echo $mySqueezeCenter->Players->Players()['players'][$count]['name'];?>
		</option>
	<?php $count++; }?>