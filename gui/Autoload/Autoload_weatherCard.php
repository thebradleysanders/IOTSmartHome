<?php	
if($GS_weatherServiceEnabled == true): //Check if service is enabled 							
	
	//Current Day
	$query    = "SELECT * FROM weather_data WHERE day_added='".date("l")."'";
	$results = mysqli_query($GS_DBCONN, $query);
	$weather = mysqli_fetch_assoc($results);
	
	//Prevoius Day
	$query    = "SELECT * FROM weather_data WHERE day_added='".date("l", strtotime(' -1 day'))."'";
	$results = mysqli_query($GS_DBCONN, $query);
	$prevWeather = mysqli_fetch_assoc($results);
	
	//for heat index or temp diffence
	if((int)$weather['temp']>80){
		$temp = $weather['heat_index'];
		$prevTemp = $prevWeather['heat_index'];
	}else{
		$temp = $weather['temp'];
		$prevTemp = $prevWeather['temp'];
	}
?>
	<script>
		$("#DashboardCardWeather_city").text("<?php echo substr($weather['city_name'],0,20);?>");
		
		<?php if(strpos($weather['temp_condition'], "snow") || strpos($weather['temp_condition'], "sleet") !== false) :?>
			$("#DashboardCardWeather_icon").html("<i class='fa fa-snowflake-o' title='Snow'></i>");
		<?php elseif(strpos($weather['temp_condition'], "rain") || strpos($weather['temp_condition'], "drizzle") !== false):?>
			$("#DashboardCardWeather_icon").html("<i class='fa fa-tint' title='Rain'></i>");
		<?php elseif(strpos($weather['temp_condition'], "thunderstorm") !== false):?>
			$("#DashboardCardWeather_icon").html("<i class='fa fa-bolt' title='Thunderstorms'></i>");
		<?php elseif(strpos($weather['temp_condition'], "cloud") !== false):?>
			$("#DashboardCardWeather_icon").html("<i class='fa fa-cloud' title='Clouds'></i>");
		<?php elseif(strpos($weather['temp_condition'], "clear") !== false):?>
			$("#DashboardCardWeather_icon").html("<i class='fa fa-sun-o' title='Clear'></i>");
		<?php endif;?>
		
		$("#DashboardCardWeather_temp").html("<?php echo $temp;?>&deg;");
		$("#DashboardCardWeather_sunrise").text("<?php echo substr($weather['sunrise_time'],0,5);?>");
		$("#DashboardCardWeather_sunset").text("<?php echo substr($weather['sunset_time'],0,5);?>");
		$("#DashboardCardWeather_lastUpdate").prop("title","Last Updated: <?php echo date('h:i:s A',$weather['last_updated']);?>");
		
		
		<?php if($temp > $prevTemp):?>/* Warmer */
			<?php $tempCompare = $temp-$prevTemp; ?>
			$('#DashboardCardWeather_tempCompare').html('<?php echo $tempCompare;?>&deg; Warmer Than Yesterday');
		<?php elseif($temp < $prevTemp):?>/* Cooler */
			<?php $tempCompare = $prevTemp-$temp; ?>
			$('#DashboardCardWeather_tempCompare').html('<?php echo $tempCompare;?>&deg; Cooler Than Yesterday');
		<?php else :?>
			$('#DashboardCardWeather_tempCompare').html('Same Temp as Yesterday');
		<?php endif;?>
	</script>
<?php endif;?>
