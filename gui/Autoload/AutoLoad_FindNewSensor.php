<?php
$GS_phueServiceBypass = true;
$GS_wemoServiceBypass = true;
$GS_mqttServiceBypass = true;
$GS_emailServiceBypass = true;
$GS_squeezeBoxServiceBypass = true;
$GS_webIncludesIncluded = false;

//Bypassed variables are passed to this include
$upParrentDir = '/../../..';
require(realpath(__DIR__ ."/../../System/API/SmartHome_API/DBConn.php"));
require(realpath(__DIR__ ."/../../System/API/SmartHome_API/IOTIncludes.php"));
?>

<table id="find_sensor_table">
	<col width="900">
	<col width="50">
	<?php $count=0;
		$query = "SELECT DISTINCT sensor_address FROM find_sensors_list ORDER BY ID DESC LIMIT 10";
		$results = mysqli_query($GS_DBCONN, $query);
		while($result = mysqli_fetch_assoc($results)) { $count++;
			if ($count==1) :?>
				<script>$('#find_sensor_loading').fadeOut('100');</script>
			<?php endif;?>

			<tr>
				<td>Address: <b><?php echo $result['sensor_address'];?></b></td>
				
				<td>
					<button type="button" onclick="set_sensor_address('<?php echo trim($result['sensor_address']);?>')"
						style="margin-left:20px;" class="btn btn-primary" data-dismiss="modal">
						Select
					</button>
				</td>
			</tr>
	
	<?php }?>
	
	<?php if ($count==0):?>
		<script>$('#find_sensor_loading').fadeIn('100');</script>
	<?php endif;?>
</table>