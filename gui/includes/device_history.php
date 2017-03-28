<?php
	function getTimeDiffrence($date,$start,$end){
		$to_time = strtotime($date." ".$end);
		$from_time = strtotime($date." ".$start);
		return ((abs($to_time - $from_time) / 60)/60);
	}
		
	function showDeviceHistory($device_id,$date="") {
		global $GS_DBCONN;
		//get specified date or set default as today
		if($date==""){$date = date("Y-m-d");}else{$date = date("Y-m-d", strtotime(str_replace("-","/",$date)));}
		
		
?>

	<state-history-charts class="style-scope more-info-dialog">
	   <div class="style-scope state-history-charts">
		  <template is="dom-if" class="style-scope state-history-charts"></template>
		  <state-history-chart-timeline class="style-scope state-history-charts" style="display: block;">
			 <div style="position: relative;">
				<div dir="ltr" style="position: relative; width: 400px; height: 97px;">
				   <div style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%;">
					  <svg width="400" height="97" aria-label="A chart." style="overflow: hidden;">
						 <defs id="defs"></defs>
						 <g>
							<rect x="0" y="0" width="400" height="40.992" stroke="none" stroke-width="0" fill="#ffffff"></rect>
							<path d="M0,0L0,40.992" stroke="#e6e6e6" stroke-width="1" fill-opacity="1" fill="none"></path>
							<path d="M51.36110648148147,0L51.36110648148147,40.992" stroke="#e6e6e6" stroke-width="1" fill-opacity="1" fill="none"></path>
							<path d="M151.36110648148147,0L151.36110648148147,40.992" stroke="#e6e6e6" stroke-width="1" fill-opacity="1" fill="none"></path>
							<path d="M251.36110648148147,0L251.36110648148147,40.992" stroke="#e6e6e6" stroke-width="1" fill-opacity="1" fill="none"></path>
							<path d="M351.36110648148147,0L351.36110648148147,40.992" stroke="#e6e6e6" stroke-width="1" fill-opacity="1" fill="none"></path>
							<rect x="0" y="0" width="400" height="40.992" stroke="#9a9a9a" stroke-width="1" fill-opacity="1" fill="none"></rect>
						 </g>
						 <g>
							<text text-anchor="middle" x="51.36110648148147" y="62.042" font-family="Arial" font-size="13" font-weight="bold" stroke="none" stroke-width="0" fill="#000000">1:00 am</text>
							<text text-anchor="middle" x="151.36110648148147" y="62.042" font-family="Arial" font-size="13" stroke="none" stroke-width="0" fill="#000000">5:00</text>
							<text text-anchor="middle" x="251.36110648148147" y="62.042" font-family="Arial" font-size="13" font-weight="bold" stroke="none" stroke-width="0" fill="#000000">1:00 pm</text>
							<text text-anchor="middle" x="351.36110648148147" y="62.042" font-family="Arial" font-size="13" stroke="none" stroke-width="0" fill="#000000">5:00</text>
						 </g>
						 <g></g>
						 <g>
							<!--- 57.3 * (x-1) )+40 = x = # of hours -->
							<?php
								$count = 0;
								$query    = "SELECT * FROM devices WHERE ID = '".$device_id."' LIMIT 1";
								$devices = mysqli_query($GS_DBCONN, $query);
								while ($device = mysqli_fetch_assoc($devices)) { $count++;			
									
									$logCount = 0;
									$query    = "SELECT * FROM history_log WHERE device_id='".$device['ID']."' AND date_added='".$date."' ORDER BY ID ASC ";
									$logs = mysqli_query($GS_DBCONN, $query);
									while ($log = mysqli_fetch_assoc($logs)) { 
										
										//get off times
										$query    = "SELECT * FROM history_log WHERE device_id='".$device['ID']."' AND date_added='".$date."' AND ID < '".$log['ID']."' ORDER BY ID DESC LIMIT 1";
										$prevLogs = mysqli_query($GS_DBCONN, $query);
										$prevLogCount = mysqli_num_rows($prevLogs);
										$prevLog = mysqli_fetch_assoc($prevLogs);
										
										if($prevLogCount>0){
											$startTimeOff = $prevLog['endTime'];
											$endTimeOff = $log['startTime'];
											$showOff = "1";
										}else{
											$startTimeOff = "00:00:00";
											$endTimeOff = $log['startTime'];
											$showOff = "1";	
										}
						
										
										$startTimeOn = $log['startTime'];
										if($log['endTime']!=""){
											$endTimeOn = $log['endTime'];
										}else{
											//only show time if on todats date
											if($date == date("Y-m-d")){
												$endTimeOn = date("H:i:s");
											}else{
												$endTimeOn = "24:00:00";
											}	
										}
										
										
										
									
										$hoursOn = getTimeDiffrence($date,$startTimeOn,$endTimeOn);
										$hoursOff = getTimeDiffrence($date,$startTimeOff,$endTimeOff);
										//lastest Off time
										if($date == date("Y-m-d")){
											$hoursLatestOff = getTimeDiffrence($date,$log['endTime'],date("H:i:s"));
										}else{
											$hoursLatestOff = getTimeDiffrence($date,$log['endTime'],"24:00:00");
										}
										$widthLatestOff = (25 * ($hoursLatestOff));
										if($widthLatestOff<35){ $hideTextLatest = true;}else{$hideTextLatest = false;}
										
										//on
										//$hoursOff=1;
										if($logCount==0) {
											$widthOn =   (16.9 * ($hoursOn));
										}else{
											$widthOn = (16.9 * ($hoursOn));
										}
										//off
										if($logCount==0) {
											$widthOff =  (25 * ($hoursOff));
										}else{
											$widthOff = (25 * ($hoursOff));
										}
										
										
										if($widthOn<25){ $hideTextOn = true;}else{$hideTextOn = false;}
										if($widthOff<25){ $hideTextOff = true;}else{$hideTextOff = false;}
										if($logCount==0){$x ="1";} //align the first entry to the orgin
										?>
											<?php if ($showOff == "1") :?>
												<rect x="<?php echo $x;?>" y="9" width="<?php echo $widthOff;?>" height="22.991999999999997" stroke="none" stroke-width="0" fill="#5e97f5"></rect>
												<text text-anchor="start" x="<?php echo $x+10;?>" y="24.695999999999998" font-family="Arial" font-size="12" stroke="none" stroke-width="0" fill="#202020"><?php if($hideTextOff==false):?>off<?php endif;?></text>
												<?php $x=$x + ($widthOff);?>
											<?php endif;?>
											
											<rect x="<?php echo $x;?>" y="9" width="<?php echo $widthOn;?>" height="22.991999999999997" stroke="none" stroke-width="0" fill="#4285f4"></rect>
											<text text-anchor="start" x="<?php echo $x+10;?>" y="24.695999999999998" font-family="Arial" font-size="12" stroke="none" stroke-width="0" fill="#ffffff"><?php if($hideTextOn==false):?>on<?php endif;?></text>
											<?php $x=$x + ($widthOn);$logCount++;?>
											
											<?php if ($log['endTime']!=""){$showLatestOff = true;}else{$showLatestOff = false;}?>
								<?php }?>
									<?php if($showLatestOff == true):?>
									  <rect x="<?php echo $x;?>" y="9" width="<?php echo $widthLatestOff;?>" height="22.991999999999997" stroke="none" stroke-width="0" fill="#5e97f5"></rect>
									  <text text-anchor="start" x="<?php echo $x+10;?>" y="24.695999999999998" font-family="Arial" font-size="12" stroke="none" stroke-width="0" fill="#202020"><?php if($hideTextLatest==false):?>off<?php endif;?></text>
									<?php endif;?>
								<?php }?>
							
							 
							
						 </g>
						 <g></g>
						 <g></g>
						 <g></g>
						 <g></g>
					  </svg>
				   </div>
				</div>
				<div aria-hidden="true" style="display: none; position: absolute; top: 107px; left: 410px; white-space: nowrap; font-family: Arial; font-size: 13px; font-weight: bold;">0:00</div>
				<div></div>
			 </div>
		  </state-history-chart-timeline>
	   </div>
	</state-history-charts>
<?php }?> 