<!--#########################################################################################################################################################################-->
<!--############################################################################## IF THIS ##################################################################################-->
<!--#########################################################################################################################################################################-->
<!-- Modal -->
<div class="modal fade" id="if_more" role="dialog" style="z-index:99999;margin-top:200px;">
    <div class="modal-dialog" style="width:400px;">
        <!-- this open the that more dialog -->
        <a href="#" data-toggle="modal" data-target="#if_more" id="ifMoreDialogLink" style="display:none;"></a>
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 style="" class="modal-title" id="if_moreHeader"></h4>
            </div>
            <div class="modal-body" style="overflow:auto;">
                <div style="padding:5px;display:none;" id="Schedule_box">
                    <label>Select A Date/Time:</label>
                    <input type="datetime-local" value="" onchange="ifttt_ifThis_schedule()" class="datetimepicker_if form-control1" style="height:30px;width:100%;"/>
                </div>
                <script>
                    function ifttt_ifThis_schedule(){
                    	var endString = '<IF>Schedule: ' + $(".datetimepicker_if").val() + '</IF> Schedule ' + $(".datetimepicker_if").val();
                    	save_if_event(endString);
                    }
                </script>
				
				 <!-- DATA SENSORS -->
                <div style="padding:5px;display:none;" class="DataSensor_box">
                  <div class="input-group" style="margin-top:10px;">
						<span class="input-group-addon"><b>Sensor</b></span>
						<select class="form-control1" style="height:30px;width:100%;" id="dataSensor_sensorID" onchange="saveSetDataSensor();">
							<option value="">Not Set</option>
							<?php 
								$query1_a = "SELECT * FROM data_sensors WHERE enabled='1'";
								$results1_a = mysqli_query($GS_DBCONN, $query1_a);
								while($result1_a = mysqli_fetch_assoc($results1_a)) { 
							?>	
								<option value="<?php echo $result1_a['ID'];?>:<?php echo ucwords($result1_a['sensor_nicename']);?>">
									<?php echo ucwords($result1_a['sensor_nicename']);?>
								</option>
							<?php }?>
						</select>
					</div>
					<!-- Select a Field -->
					 <?php 
						$query1_a = "SELECT * FROM data_sensors WHERE enabled='1'";
						$results1_a = mysqli_query($GS_DBCONN, $query1_a);
						while($result1_b = mysqli_fetch_assoc($results1_a)) { 
					?>
					<div class="input-group dataSensor_sensorField" id="dataSensor_sensorField<?php echo $result1_b['ID'];?>" style="display:none;">
                       <span class="input-group-addon"><b>If</b></span>
					   <select class="form-control1" style="width:100%;height:30px;" onchange="saveSetDataSensor();">
						<?php
							$fieldIndex= -1;
							foreach(explode(":",$result1_b['sensor_dataTitle_array']) as $field){ $fieldIndex++;
						?>	
							<option value="<?php echo $fieldIndex;?>:<?php echo ucwords($field);?>"><?php echo ucwords($field);?></option>
						<?php }?>
					  </select>
					</div>
				  <?php }?>
					
                    <center>
						<input value="=" type="radio" name="dataSensor_compare" onclick="saveSetDataSensor();"/> <span style="font-size:12px;">Equal To</span>&nbsp;
						<input value="!=" type="radio" name="dataSensor_compare" onclick="saveSetDataSensor();"/> <span style="font-size:12px;">Not Equal To</span>&nbsp;
                        <input value="<" type="radio" name="dataSensor_compare" onclick="saveSetDataSensor();"/> <span style="font-size:12px;">Less Than</span>&nbsp;
                        <input value=">" type="radio" name="dataSensor_compare" onclick="saveSetDataSensor();"/> <span style="font-size:12px;">Greater Than</span>&nbsp;
                    </center>
					
					<div class="input-group" style="margin-top:10px;">
						<span class="input-group-addon"><b>Value</b></span>
						<input type="text" class="form-control1" style="height:30px;width:100%;" id="dataSensor_compareTo" onkeyup="saveSetDataSensor();"/>
					</div>
                       
                  
                    <script>
                        function saveSetDataSensor(){
							var compareOption = $("input[name='dataSensor_compare']:checked").val();
							var sensorData = $('#dataSensor_sensorID').val().split(":");
							var fieldData = $('#dataSensor_sensorField'+ sensorData[0] +' select').val().split(":");
							
							$('.dataSensor_sensorField').hide();
							$('#dataSensor_sensorField'+sensorData[0]).show();

							var niceString = sensorData[1] + "."+fieldData[1]+" "+ compareOption +" "+$('#dataSensor_compareTo').val();
                        	var endString = '<IF>DataSensor:' + sensorData[0] + ':' + fieldData[0]  +':'+ compareOption + ':' + $('#dataSensor_compareTo').val() + '</IF> ' + niceString;
                        	save_if_event(endString);
                        }
                    </script>
                </div>
				
                <div style="padding:5px;display:none;" id="main_select_box">
                    <!-- SENSORS -->
                    <label class="ifttt_ifThis_Sensor" for="">Select a Sensor:</label>
                    <select onchange="ifttt_ifThis_Sensor()" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_Sensor">
                        <?php 
                            $query1_b = "SELECT * FROM sensors ORDER BY sensor_kind ASC";
                            $results1_b = mysqli_query($GS_DBCONN, $query1_b);
                            while($result1_b = mysqli_fetch_assoc($results1_b)) { 
                        ?>	
							<option value="<?php echo $result1_b['ID'];?>|<?php echo $result1_b['sensor_name'];?>"><?php echo $result1_b['sensor_name'];?></option>
                        <?php }?>
                    </select>
                    <label class="ifttt_ifThis_Sensor" for="" style="margin-top:10px;">Select a State:</label>
                    <select onchange="ifttt_ifThis_Sensor()" style="height:30px;width:100%;" id="ifttt_ifThis_SensorState" class="form-control1 ifttt_ifThis_Sensor">
                        <option value="1">Open/Active</option>
                        <option value="0">Closed/Inactive</option>
                    </select>
                    <script>
                        function ifttt_ifThis_Sensor(){
                        	var niceText = $("select.ifttt_ifThis_Sensor").val().split("|")[1]+" "+$("#ifttt_ifThis_SensorState option:selected").text();
                        	var endString = "<IF>Sensor:" +  $("select.ifttt_ifThis_Sensor").val().split("|")[0] +":"+$('#ifttt_ifThis_SensorState').val() + "</IF> " + niceText;
                        	save_if_event(endString);
                        }
                    </script>
                    <!-- DEVICE STATE-->
                    <select onchange="ifttt_ifThis_Device()" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_Device">
                        <?php 
                            $query2 = "SELECT * FROM devices ORDER BY device_name ASC";
                            $results2 = mysqli_query($GS_DBCONN, $query2);
                            while($result2 = mysqli_fetch_assoc($results2)) { 
                        ?>	
							<option value="<?php echo $result2['ID'];?>|<?php echo $result2['device_name'];?>"><?php echo $result2['device_name'];?></option>
                        <?php }?>
                    </select>
                    <label class="ifttt_ifThis_Device" for="" style="margin-top:10px;">Select a State:</label>
                    <select onchange="ifttt_ifThis_Device()" style="height:30px;width:100%;" id="ifttt_ifThis_DeviceState" class="form-control1 ifttt_ifThis_Device">
                        <option value="1">On</option>
                        <option value="0">Off</option>
                    </select>
                    <script>
                        function ifttt_ifThis_Device(){
                        	var niceText = $("select.ifttt_ifThis_Device").val().split("|")[1]+" "+$("#ifttt_ifThis_DeviceState option:selected").text();
                        	var endString = "<IF>Device:" +  $("select.ifttt_ifThis_Device").val().split("|")[0] +":"+$('#ifttt_ifThis_DeviceState').val() + "</IF> " + niceText;
                        	save_if_event(endString);
                        }
                    </script>
                    <!-- TIME SELECTION -->
                    <label for="" class="ifttt_ifThis_TimeSelection" style="margin-top:10px;">Select a type</label>
                    <select onchange="ifttt_ifThis_timeType()" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_TimeSelection">
                        <option value="">Select an Option</option>
                        <option value="at">At Time</option>
                        <option value="before">Before Time</option>
                        <option value="after">After Time</option>
                        <option value="dayofweek">Day of the Week</option>
                    </select>
                    <!--AT TIME -->
                    <label for="" class="ifttt_ifThis_Time" style="margin-top:10px;"></label>
                    <select onchange="ifttt_ifThis_time()" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_Time">
                        <option value="<IF>Time:12:00 PM</IF> 12:00 PM (Noon)">12:00 PM (Noon)</option>
                        <option value="<IF>Time:12:30 PM</IF> 12:30 PM">12:30 PM</option>
                        <?php $pm=0; while($pm < 11){ $pm++; ?>
							<option value="<IF>Time:<?php echo $pm;?>:00 PM</IF> <?php echo $pm;?>:00 PM"><?php echo $pm;?>:00 PM</option>
							<option value="<IF>Time:<?php echo $pm;?>:30 PM</IF> <?php echo $pm;?>:30 PM"><?php echo $pm;?>:30 PM</option>
                        <?php } ?>
							<option value="<IF>Time:12:00 AM</IF> 12:00 AM (Midnight)">12:00 AM (Midnight)</option>
							<option value="<IF>Time:12:30 AM</IF> 12:30 AM">12:30 AM</option>
                        <?php $am=0; while($am < 11){ $am++; ?>
							<option value="<IF>Time:<?php echo $am;?>:00 AM</IF> <?php echo $am;?>:00 AM"><?php echo $am;?>:00 AM</option>
							<option value="<IF>Time:<?php echo $am;?>:30 AM</IF> <?php echo $am;?>:30 AM"><?php echo $am;?>:30 AM</option>
                        <?php } ?>
                    </select>
                    <!--- Before TIME --->
                    <label for="" class="ifttt_ifThis_BeforeTime" style="margin-top:10px;">Select a time</label>
                    <select onchange="ifttt_ifThis_time()" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_BeforeTime">
                        <option value="<IF>BeforeTime:01</IF> Before: 1:00 AM">Before 1:00 AM</option>
                        <option value="<IF>BeforeTime:02</IF> Before: 2:00 AM">Before 2:00 AM</option>
                        <option value="<IF>BeforeTime:03</IF> Before: 3:00 AM">Before 3:00 AM</option>
                        <option value="<IF>BeforeTime:04</IF> Before: 4:00 AM">Before 4:00 AM</option>
                        <option value="<IF>BeforeTime:05</IF> Before: 5:00 AM">Before 5:00 AM</option>
                        <option value="<IF>BeforeTime:06</IF> Before: 6:00 AM">Before 6:00 AM</option>
                        <option value="<IF>BeforeTime:07</IF> Before: 7:00 AM">Before 7:00 AM</option>
                        <option value="<IF>BeforeTime:08</IF> Before: 8:00 AM">Before 8:00 AM</option>
                        <option value="<IF>BeforeTime:09</IF> Before: 9:00 AM">Before 9:00 AM</option>
                        <option value="<IF>BeforeTime:10</IF> Before: 10:00 AM">Before 10:00 AM</option>
                        <option value="<IF>BeforeTime:11</IF> Before: 11:00 AM">Before 11:00 AM</option>
                        <option value="<IF>BeforeTime:12</IF> Before: 12:00 PM (Noon)">Before 12:00 PM (Noon)</option>
                        <option value="<IF>BeforeTime:13</IF> Before: 1:00 PM" >Before 1:00 PM</option>
                        <option value="<IF>BeforeTime:14</IF> Before: 2:00 PM">Before 2:00 PM</option>
                        <option value="<IF>BeforeTime:15</IF> Before: 3:00 PM" >Before 3:00 PM</option>
                        <option value="<IF>BeforeTime:16</IF> Before: 4:00 PM">Before 4:00 PM</option>
                        <option value="<IF>BeforeTime:17</IF> Before: 5:00 PM" >Before 5:00 PM</option>
                        <option value="<IF>BeforeTime:18</IF> Before: 6:00 PM">Before 6:00 PM</option>
                        <option value="<IF>BeforeTime:19</IF> Before: 7:00 PM" >Before 7:00 PM</option>
                        <option value="<IF>BeforeTime:20</IF> Before: 8:00 PM">Before 8:00 PM</option>
                        <option value="<IF>BeforeTime:21</IF> Before: 9:00 PM" >Before 9:00 PM</option>
                        <option value="<IF>BeforeTime:22</IF> Before: 10:00 PM">Before 10:00 PM</option>
                        <option value="<IF>BeforeTime:23</IF> Before: 11:00 PM">Before 11:00 PM</option>
                        <option value="<IF>BeforeTime:24</IF> Before: 12:00 AM (Midnight)">Before 12:00 AM (Midnight)</option>
                    </select>
                    <!--- AFTER TIME --->
                    <label for="" class="ifttt_ifThis_AfterTime" style="margin-top:10px;">Select a time</label>
                    <select onchange="ifttt_ifThis_time()" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_AfterTime">
                        <option value="<IF>AfterTime:01</IF> After: 1:00 AM">After 1:00 AM</option>
                        <option value="<IF>AfterTime:02</IF> After: 2:00 AM">After 2:00 AM</option>
                        <option value="<IF>AfterTime:03</IF> After: 3:00 AM">After 3:00 AM</option>
                        <option value="<IF>AfterTime:04</IF> After: 4:00 AM">After 4:00 AM</option>
                        <option value="<IF>AfterTime:05</IF> After: 5:00 AM">After 5:00 AM</option>
                        <option value="<IF>AfterTime:06</IF> After: 6:00 AM">After 6:00 AM</option>
                        <option value="<IF>AfterTime:07</IF> After: 7:00 AM">After 7:00 AM</option>
                        <option value="<IF>AfterTime:08</IF> After: 8:00 AM">After 8:00 AM</option>
                        <option value="<IF>AfterTime:09</IF> After: 9:00 AM">After 9:00 AM</option>
                        <option value="<IF>AfterTime:10</IF> After: 10:00 AM">After 10:00 AM</option>
                        <option value="<IF>AfterTime:11</IF> After: 11:00 AM">After 11:00 AM</option>
                        <option value="<IF>AfterTime:12</IF> After: 12:00 PM (Noon)">After 12:00 PM (Noon)</option>
                        <option value="<IF>AfterTime:13</IF> After: 1:00 PM" >After 1:00 PM</option>
                        <option value="<IF>AfterTime:14</IF> After: 2:00 PM">After 2:00 PM</option>
                        <option value="<IF>AfterTime:15</IF> After: 3:00 PM" >After 3:00 PM</option>
                        <option value="<IF>AfterTime:16</IF> After: 4:00 PM">After 4:00 PM</option>
                        <option value="<IF>AfterTime:17</IF> After: 5:00 PM" >After 5:00 PM</option>
                        <option value="<IF>AfterTime:18</IF> After: 6:00 PM">After 6:00 PM</option>
                        <option value="<IF>AfterTime:19</IF> After: 7:00 PM" >After 7:00 PM</option>
                        <option value="<IF>AfterTime:20</IF> After: 8:00 PM">After 8:00 PM</option>
                        <option value="<IF>AfterTime:21</IF> After: 9:00 PM" >After 9:00 PM</option>
                        <option value="<IF>AfterTime:22</IF> After: 10:00 PM">After 10:00 PM</option>
                        <option value="<IF>AfterTime:23</IF> After: 11:00 PM">After 11:00 PM</option>
                        <option value="<IF>AfterTime:24</IF> After: 12:00 AM (Midnight)">After 12:00 AM (Midnight)</option>
                    </select>
                    <!-- TIME OF WEEK -->
                    <label for="" class="ifttt_ifThis_Time_of_week" style="margin-top:10px;">Select a day of the week</label>
                    <select onchange="ifttt_ifThis_time()" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_Time_of_week">
                        <option value="<IF>DayOfWeek:Sunday</IF> Day Is Sunday">Sunday</option>
                        <option value="<IF>DayOfWeek:Monday</IF> Day Is Monday">Monday</option>
                        <option value="<IF>DayOfWeek:Tuesday</IF> Day Is Tuesday">Tuesday</option>
                        <option value="<IF>DayOfWeek:Wednesday</IF> Day Is Wednesday">Wednesday</option>
                        <option value="<IF>DayOfWeek:Thursday</IF> Day Is Thursday">Thursday</option>
                        <option value="<IF>DayOfWeek:Friday</IF> Day Is Friday">Friday</option>
                        <option value="<IF>DayOfWeek:Saturday</IF> Day Is Saturday">Saturday</option>
                        <option value="<IF>DayOfWeek:Weekday</IF> Its a Weekday">Weekday (Monday - Friday)</option>
                        <option value="<IF>DayOfWeek:Weekend</IF> Its a Weekend">Weekend (Saturday & Sunday)</option>
                    </select>
                    <script>
                        function ifttt_ifThis_timeType(){
                        	if($("select.ifttt_ifThis_TimeSelection").val()=="at"){
                        		$(".ifttt_ifThis_AfterTime").hide();
                        		$(".ifttt_ifThis_BeforeTime").hide();
                        		$(".ifttt_ifThis_Time_of_week").hide();
                        		$(".ifttt_ifThis_Time").show();
                        		$('.if_moreHeader').text("Select a time");
                        	}else if($("select.ifttt_ifThis_TimeSelection").val()=="before"){
                        		$(".ifttt_ifThis_Time").hide();
                        		$(".ifttt_ifThis_AfterTime").hide();
                        		$(".ifttt_ifThis_Time_of_week").hide();
                        		$(".ifttt_ifThis_BeforeTime").show();
                        		$('.if_moreHeader').text("Select a time");
                        	}else if($("select.ifttt_ifThis_TimeSelection").val()=="after"){
                        		$(".ifttt_ifThis_Time").hide();
                        		$(".ifttt_ifThis_BeforeTime").hide();
                        		$(".ifttt_ifThis_Time_of_week").hide();
                        		$(".ifttt_ifThis_AfterTime").show();
                        		$('.if_moreHeader').text("Select a time");
                        	}else if($("select.ifttt_ifThis_TimeSelection").val()=="dayofweek"){
                        		$(".ifttt_ifThis_Time").hide();
                        		$(".ifttt_ifThis_BeforeTime").hide();
                        		$(".ifttt_ifThis_AfterTime").hide();
                        		$(".ifttt_ifThis_Time_of_week").show();
                        		$('.if_moreHeader').text("Select a day of the week");
                        	}
                        }
                        
                        function ifttt_ifThis_time(){
                        	if($("select.ifttt_ifThis_TimeSelection").val()=="at"){
                        		save_if_event($("select.ifttt_ifThis_Time").val());									
                        	}else if($("select.ifttt_ifThis_TimeSelection").val()=="before"){
                        		save_if_event($("select.ifttt_ifThis_BeforeTime").val());
                        	}else if($("select.ifttt_ifThis_TimeSelection").val()=="after"){
                        		save_if_event($("select.ifttt_ifThis_AfterTime").val());
                        	}else if($("select.ifttt_ifThis_TimeSelection").val()=="dayofweek"){
                        		save_if_event($("select.ifttt_ifThis_Time_of_week").val());
                        	}							
                        }
                        					
                    </script>						
                    <!--STATUS -->						
                    <select onchange="save_if_event($(this).val());" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_Status">
                        <option value="">Select an Option</option>
                        <option value="<IF>Status: Somebody Home</IF> Somebody Is Home">Somebody Is Home</option>
                        <option value="<IF>Status: Nobody Home</IF> Nobody Is Home">Nobody Is Home</option>
                    </select>
                    <!-- ALARM -->					
                    <select onchange="save_if_event($(this).val());" style="height:30px;width:100%;" class="form-control1 ifttt_ifThis_Alarm">
                        <option value="">Select an Option</option>
                        <option value="<IF>Alarm:On</IF> Alarm On">Alarm On</option>
                        <option value="<IF>Alarm:On:Home</IF> Alarm On Home">Alarm On Home</option>
                        <option value="<IF>Alarm:On:Away</IF> Alarm On Away">Alarm On Away</option>
                        <option value="<IF>Alarm:Off</IF> Alarm Off">Alarm Off</option>
						<option value="<IF>Alarm:Triggered</IF> Alarm Triggered">Alarm Triggered</option>
						<option value="<IF>Alarm:Activated</IF> Alarm Activated">Alarm Activated</option>
                    </select>
                    <!-- WEATHER -->
                    <select onchange="save_if_event($(this).val());" style="height:30px;width:100%;" class="ifttt_ifThis_weather form-control1">
                        <option value="<IF>Sunrise</IF> It is Sunrise">It is Sunrise</option>
                        <option value="<IF>BeforeSunrise</IF> Its Before Sunrise">Its Before Sunrise</option>
                        <option value="<IF>AfterSunrise</IF> Its After Sunrise">Its After Sunrise</option>
                        <option value="<IF>Sunset</IF> It is Sunset">It is Sunset</option>
                        <option value="<IF>BeforeSunset</IF> Its Before Sunset">Its Before Sunset</option>
                        <option value="<IF>AfterSunset</IF> Its After Sunset">Its After Sunset</option>
                        <?php $temp=0; while($temp < 10){ $temp++; ?>
							<!-- <option class="weather" value="<IF>Temperature:<?php echo $temp;?>0</IF> Temperature:<?php echo $temp;?>0"><?php echo $temp;?>0 Fahrenheit - NOT YET WORKING</option> -->
							<!-- <option class="weather" value="<IF>Temperature:<?php echo $temp;?>5</IF> Temperature:<?php echo $temp;?>5"><?php echo $temp;?>5 Fahrenheit - NOT YET WORKING</option> -->
                        <?php } ?>									
                    </select>
                </div>
                <div style="margin-top:20px;float:right;">
                    <button class="btn btn-primary"  data-dismiss="modal">Save</button>
                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    /*number of ifttt row */
    var ifNumber = 0;
    
    function save_if_event(endString){
    	if(endString!=""){
    		/* user selected an item now set it to that combo box */
    		$('#if_this' + ifNumber).append($("<option></option>").attr("value",endString).text(removeIfttTags('<IF>','</IF>',4,endString)));
    		$('#if_this' + ifNumber).val(endString);
    	}else{
    		/*user dident select anything in modal so set to 'not set' */
    		$('#if_this' + ifNumber).val("");
    	}
    }
    
    
    function show_if_modal(if_number,type){
    	/*show modal */
    	if(type==""){return false;}
    	ifNumber = if_number;
    	$('#ifMoreDialogLink').click();
    	$('#type_header').text(type);
    	
    	function hide_all(){
    	  /* hide all items in modal select box */
    	  $("#if_moreHeader").text("Select an Option");
    	  $("#Schedule_box").hide();
    	  $("#main_select_box").show();
    	  $('.datetimepicker_if').attr('id','');
    	  
    	  $(".ifttt_ifThis_Sensor").hide();
    	  $(".ifttt_ifThis_Device").hide();
    	  $(".ifttt_ifThis_Time_of_week").hide();
    	  $(".ifttt_ifThis_Time").hide();
    	  $(".ifttt_ifThis_BeforeTime").hide();
    	  $(".ifttt_ifThis_AfterTime").hide();
    	  $(".ifttt_ifThis_TimeSelection").hide();
    	  $(".ifttt_ifThis_Status").hide();
    	  $(".ifttt_ifThis_weather").hide();
    	  $(".current_temp_category").hide();
    	  $(".ifttt_ifThis_Alarm").hide();
    	  
    	  $(".Schedule").hide();
    	  $('.DataSensor_box').hide();
    	}
       
    	/* show items in modal based on selection of ifttt if select box */
    	if (type=="Sensor"){ hide_all(); $(".ifttt_ifThis_Sensor").show(); $("#if_moreHeader").text("Select a Sensor");}		
    	if (type=="Alarm"){ hide_all(); $(".ifttt_ifThis_Alarm").show(); $("#if_moreHeader").text("Select an Alarm State");}
    	if (type=="DataSensor"){ hide_all();  $("#main_select_box").hide();$(".DataSensor_box").show(); $("#if_moreHeader").text("Select a Data Sensor");}
    	if (type=="weather"){ hide_all(); $(".ifttt_ifThis_weather").show(); }
    	if (type=="Time"){ hide_all(); $(".ifttt_ifThis_TimeSelection").show(); $("#if_moreHeader").text("Select a Time");}
    	if (type=="DeviceState"){ hide_all(); $(".ifttt_ifThis_Device").show(); $("#if_moreHeader").text("Select a Device");}
    	if (type=="Status"){ hide_all(); $(".ifttt_ifThis_Status").show();}
    	if (type=="Schedule"){ hide_all(); $(".Schedule").show(); $("#main_select_box").hide(); $("#Schedule_box").show(); $("#if_moreHeader").text("Select a Date/Time");}
    }				
</script>
<!--#########################################################################################################################################################################-->
<!--############################################################################# THEN THAT #################################################################################-->
<!--#########################################################################################################################################################################-->
<!-- Modal -->
<div class="modal fade" id="that_more" role="dialog" style="z-index:99999;margin-top:200px;">
    <div class="modal-dialog" style="width:400px;">
        <!-- this open the that more dialog -->
        <a href="#" data-toggle="modal" data-target="#that_more" id="ThatMoreDialogLink" style="display:none;"></a>
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 style="" class="modal-title" id="that_moreHeader"></h4>
            </div>
            <div class="modal-body" style="overflow:auto;">
                <div style="display:none;" class="then_that_devices">
                    <!---- Light ---->
                    <select onchange="addHueBulb();" id="device_id" style="width:100%;margin-bottom:2px;" class="form-control1">
                        <?php 
                            $query1 = "SELECT * FROM devices ";
                            $results_device_list = mysqli_query($GS_DBCONN, $query1);
                            while($result_device= mysqli_fetch_assoc($results_device_list)) { 
                        ?>	
							<option value="<?php echo $result_device["ID"];?>|<?php echo $result_device["device_name"];?>"><?php echo ucfirst($result_device["device_name"]);?></option>
                        <?php }?>
                    </select>
                    <!---- State ---->
                    <select onchange="addHueBulb();" id="device_state" style="width:100%;margin-bottom:2px;" class="form-control1">
                        <option value="noState">No State</option>
                        <option value="1">On</option>
                        <option value="0">Off</option>
                    </select>
                    <!-- Brightness -->
                    <select onchange="addHueBulb();" id="device_brightness" style="width:100%;margin-bottom:2px;" class="form-control1" >
                        <option value="0">No Brightness</option>
                        <option value="10">10%</option>
                        <option value="20">20%</option>
                        <option value="30">30%</option>
                        <option value="40">40%</option>
                        <option value="50">50%</option>
                        <option value="60">60%</option>
                        <option value="70">70%</option>
                        <option value="80">80%</option>
                        <option value="90">90%</option>
                        <option value="100">100%</option>
                    </select>
                    <!-- Color -->
                    <select onchange="addHueBulb();" id="device_color" style="width:100%;margin-bottom:2px;" class="form-control1">
                        <option value="noColor">No Color</option>
                        <option value="coolwhite">Cool White</option>
                        <option value="warmwhite">Warm White</option>
                        <option value="green">Green</option>
                        <option value="red">Red</option>
                        <option value="blue">Blue</option>
                        <option value="purple">purple</option>
                        <option value="pink">Pink</option>
                        <option value="yellow">Yellow</option>
                        <option value="orange">Orange</option>
                    </select>
                    <!---- Effect ---->
                    <select onchange="addHueBulb();" id="device_effect" style="width:100%;margin-bottom:2px;" class="form-control1">
						<option value="noEffect">No Effect</option>
						<option value="BlinkOnce">Blink Once</option>
                        <option value="BlinkTimes">Blink(2 Sec)</option>
                    </select>
                    <script>
                        function addHueBulb(){
	                        var niceText;
                        	var deviceID = $('#device_id').val().split("|");	
                        	if($("#device_state").val()=="1"){
                        		niceText = "Turn " + deviceID[1] + " on at brightness " + $("#device_brightness").val() + "%";
                        	}else if($("#device_state").val()=="0"){
                        		niceText = "Turn " + deviceID[1] + " off";
                        	}else{
	                        	niceText = "Turn "+deviceID[1];
                        	}
                        	
                        	if($("#device_color").val()!=""){
	                        	niceText=niceText+" Color: " +$("#device_color").val();
                        	}
                        	
                        	if($("#device_effect").val()!=""){
	                        	niceText=niceText+" Effect: " +$("#device_effect").val();
                        	}
					
                        	var endString = "<THEN>Device:"+ deviceID[0] +":"+ $('#device_state').val() + ":" + $('#device_brightness').val() + ":" + $('#device_color').val()+ ":" +$("#device_effect").val()+"</THEN> " + niceText; 
                        	save_that_event(endString);
                        }
                    </script>
                </div>
                <!---- Send Email -->
                <div style="display:none;" class="Send_email">
                    <!---- Email To ---->
                    <label>Send To:</label>
                    <input onKeyUp="saveEmailTemplate()" type="text" id="email_to" style="width:100%;margin-bottom:2px;" class="form-control1"/>
                    <!---- TEXT ---->
                    <label>Email Text:</label>
                    <textarea onKeyUp="saveEmailTemplate()" id="email_text" style="width:100%;margin-bottom:2px;" class="form-control1"></textarea>
                    <script>
                        function saveEmailTemplate(){
                        	var endString = "<THEN>EMAIL:"+$("#email_to").val()+":"+$('#email_text').val()+"</THEN> Send an Email To: " +$("#email_to").val(); 
                        	save_that_event(endString);
                        }
                    </script>
                </div>
                <!---- UI_Notification -->
                <div style="display:none;" class="UINotification">
                    <!---- select a user ---->
                    <label>Send To User:</label>
                    <select onchange="saveUINotification()" id="UINotification_selectedUser" style="width:100%;margin-bottom:5px;" class="form-control1">
                        <option value="0|All Users">All Users</option>
                        <?php 
                            $query= "SELECT * FROM users WHERE enabled='1' ORDER BY ID ASC";
                            $users = mysqli_query($GS_DBCONN,$query);
                            while ($user = mysqli_fetch_assoc($users)){ 
						?>
							<option value="<?php echo $user['ID'];?>|<?php echo $user['user_name'];?>"><?php echo $user['user_name'];?></option>
                        <?php } ?>
                    </select>
                    <label>Select a Type:</label>
                    <select onchange="saveUINotification()" id="UINotification_selectedType" style="width:100%;margin-bottom:5px;" class="form-control1">
                        <option value="">Not Set</option>
                        <option value="CameraModal">Show Camera Dialog</option>
                        <option value="MessageModal">Show Message Dialog</option>
                        <option value="AlarmModal">Show Alarm Panel</option>
                    </select>
                    <div id="UINotification_ShowCameraDialog" style="display:none;">
                        <label>Select a Camera:</label>
                        <select onchange="saveUINotification()" id="UINotification_selectedCamera" style="width:100%;margin-bottom:5px;" class="form-control1">
                            <?php 
                                $query= "SELECT * FROM camera_list WHERE enabled='1' ORDER BY ID ASC";
                                $cameras = mysqli_query($GS_DBCONN,$query);
                                while ($camera = mysqli_fetch_assoc($cameras)){ 
                                	if(GetUserPermissions($camera['ID'],"manage_cameras.php")==false){continue;}
								?>
								<option value="<?php echo $camera['ID'];?>"><?php echo $camera['camera_name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div id="UINotification_ShowMessageDialog" style="display:none;">
                        <label>Message Title:</label>
                        <input type="text" onkeyup="saveUINotification();" class="form-control1" style="width:100%;margin-botom:5px;" id="UINotification_messageTitle"/>
                        <label>Message Text:</label>
                        <input type="text" onkeyup="saveUINotification();" class="form-control1" style="width:100%;margin-botom:5px;" id="UINotification_messageText"/>
						<label>Image:</label>
                        <input type="text" onkeyup="saveUINotification();" class="form-control1" style="width:100%;margin-botom:5px;" id="UINotification_messageImage"/>
                    </div>
                    <label>Display Length (Seconds):</label>
                    <input type="number" onkeyup="saveUINotification();" class="form-control1" min="10" value="30" style="width:100%;margin-botom:5px;" id="UINotification_displayLength"/>			
                    <script>
                        function saveUINotification(){
                        	var niceString = "";
                        	var selectedUser = $("#UINotification_selectedUser").val().split("|");
                        	var endString="<THEN>UINotification:"+selectedUser[0] + ":";
                        	endString = endString + $("#UINotification_selectedType").val() + ":";
                        	
                        	$("#UINotification_ShowCameraDialog").hide();
                        	$("#UINotification_ShowMessageDialog").hide();
                        	
                        	if($("#UINotification_selectedType").val()=="CameraModal"){
                        		$("#UINotification_ShowCameraDialog").show();
                        		endString = endString + $("#UINotification_selectedCamera").val() + "|";
                        		niceString = "Show Camera Dialog On User: " + selectedUser[1] + " for " + $("#UINotification_displayLength").val() + " seconds";
                        	}else if($("#UINotification_selectedType").val()=="MessageModal"){
                        		$("#UINotification_ShowMessageDialog").show();
                        		endString = endString + $("#UINotification_messageTitle").val() + "|";
                        		endString = endString + $("#UINotification_messageText").val() + "|";
								endString = endString + $("#UINotification_messageImage").val() + "|";
                        		niceString = "Show Message Dialog On User: " + selectedUser[1] + " for " + $("#UINotification_displayLength").val() + " seconds";
                        	}else if($("#UINotification_selectedType").val()=="AlarmModal"){
                        		niceString = "Show Alarm Dialog On User: " + selectedUser[1] + " for " + $("#UINotification_displayLength").val() + " seconds";
                        	}
                        	endString = endString + $("#UINotification_displayLength").val() + "</THEN> " + niceString;
                        							
                        	save_that_event(endString);							
                        }
                    </script>
                </div>
                <!---- Speak Text In Room -->
                <div style="display:none;" class="Speak_in_room">
                    <!---- Rooms ---->
                    <label><b>Room:</b></label><br/>
                    <ul style="list-style:none;">
                        <li style="display:inline-block;margin-right:5px;">
                            <?php 
                                $query= "SELECT * FROM home_rooms ORDER BY ID ASC";
                                $rooms = mysqli_query($GS_DBCONN,$query);
                                while ($room = mysqli_fetch_assoc($rooms)){ 
							?>
								<label><?php echo $room['room_name'];?>:</label>
								<input onchange="saveSpeakTemplate()" type="checkbox" id="SpeakInRoom_RoomID<?php echo $room['ID'];?>" value="<?php echo $room['ID'];?>"/>
                            <?php } ?>
                        </li>
                    </ul>
                    <!---- TEXT ---->
                    <br/><label><b>Speak:</b></label>
                    <textarea onKeyUp="saveSpeakTemplate()" id="speak_text" style="width:100%;margin-bottom:2px;height:80px;" class="form-control1"></textarea>
                    <script>
                        function saveSpeakTemplate(){
                        	var rooms = "";
                        	<?php 
                            $query= "SELECT * FROM home_rooms ORDER BY ID ASC";
                            $rooms = mysqli_query($GS_DBCONN,$query);
                            while ($room = mysqli_fetch_assoc($rooms)){ 
							?>
                        		if($('#SpeakInRoom_RoomID<?php echo $room['ID'];?>').prop("checked")==true){
                        			 rooms = rooms + $('#SpeakInRoom_RoomID<?php echo $room['ID'];?>').val() + "|";
                        		}
                        	<?php } ?>
                        	
                        	var endString = "<THEN>SPEAK:"+rooms+":"+$('#speak_text').val()+"</THEN> Speak Text: " +$("#speak_text").val(); 
                        	save_that_event(endString);
                        }
                    </script>
                </div>
                <!---- Write To Log -->
                <div style="display:none;" class="Write_log">
                    <!---- TEXT ---->
                    <label>Log Text:</label>
                    <textarea onKeyUp="saveToLog()" id="log_text" style="width:100%;margin-bottom:2px;" class="form-control1"></textarea>
                    <script>
                        function saveToLog(){
                        	var endString="<THEN>LOG:"+$("#log_text").val()+"</THEN> Add To Log:"+$("#log_text").val();
                        	save_that_event(endString);							
                        }
                    </script>
                </div>
                <!---- Play Music -->
                <div style="display:none;" class="MusicControl">
                    <label>Select A Room:</label>
                    <select onChange="saveSongChoice()" onKeyUp="saveSongChoice()" id="Then_MC_Room" style="width:100%;margin-bottom:2px;" class="form-control1">
                        <?php 
                            $query_music = "SELECT * FROM home_rooms";
                            $resultsMusic_a = mysqli_query($GS_DBCONN, $query_music);
                            while($resultMusic = mysqli_fetch_assoc($resultsMusic_a)) { 
                        ?>	
							<option value="<?php echo $resultMusic['ID'];?>*<?php echo $resultMusic['room_name'];?>">
								<?php echo ucwords($resultMusic['room_name']);?>
							</option>
                        <?php }?>
                    </select>
                    <label>Select A Command:</label>
                    <select onChange="saveSongChoice()" onKeyUp="saveSongChoice()" id="Then_MC_Command" style="width:100%;margin-bottom:2px;" class="form-control1">
                        <option value="Play">Play Song</option>
                        <option value="Playlist">Play Playlist</option>
                        <option value="Pause">Pause</option>
                        <option value="Resume">Resume</option>
                        <option value="Stop">Stop</option>
                        <option value="Mute">Mute</option>
                        <option value="Unmute">Unmute</option>
                        <option value="Sync">Sync Rooms</option>
                    </select>
                    <div style="" id="Then_MC_SongPlaylist">
                        <label>Select A Song/Playlist:</label>
                        <!-- Songs-->
                        <select onChange="saveSongChoice()" onKeyUp="saveSongChoice()" id="Then_MC_Song" style="width:100%;margin-bottom:2px;" class="form-control1">
                            <?php 
                                $query_music = "SELECT * FROM music_data";
                                $resultsMusic_b = mysqli_query($GS_DBCONN, $query_music);
                                while($resultMusic = mysqli_fetch_assoc($resultsMusic_b)) { 
                            ?>	
								<option value="<?php echo $resultMusic['song_location'];?>*<?php echo $resultMusic['song_name'];?>">
									<?php if(trim($resultMusic['song_name'])!=""){
											echo ucwords($resultMusic['song_name']);
										}else{
											echo ucwords($resultMusic['song_location']);
										} ?>
								</option>
                            <?php }?>
                        </select>
                        <!-- Playlists -->
                        <select onChange="saveSongChoice()" onKeyUp="saveSongChoice()" id="Then_MC_Playlist" style="width:100%;margin-bottom:2px;display:none;" class="form-control1">
                            <option value="0*Play All Songs">
                                Play All Songs
                            </option>
                            <?php 
                                $query_playlist = "SELECT * FROM music_playlists";
                                $resultsPlaylist_b = mysqli_query($GS_DBCONN, $query_playlist);
                                while($resultPlaylist = mysqli_fetch_assoc($resultsPlaylist_b)) { 
                            ?>	
								<option value="<?php echo $resultPlaylist['ID'];?>*<?php echo $resultPlaylist['playlist_name'];?>">
									<?php echo ucwords($resultPlaylist['playlist_name']); ?>
								</option>
                            <?php }?>
                        </select>
                    </div>
                    <div style="display:none;" id="Then_MC_RoomSync">
                        <label>Add a Room To the Sync Group:</label>
                        <select onChange="saveSongChoice()" onKeyUp="saveSongChoice()" id="Then_MC_RoomSyncValue" style="width:100%;margin-bottom:2px;" class="form-control1">
                            <?php 
                                $query_music = "SELECT * FROM home_rooms";
                                $resultsMusic_a = mysqli_query($GS_DBCONN, $query_music);
                                while($resultRooms = mysqli_fetch_assoc($resultsMusic_a)) { 
                                	$query_music = "SELECT * FROM music_servers WHERE room_id='".$resultRooms['ID']."'";
                                	$resultsMusic_b = mysqli_query($GS_DBCONN, $query_music);
                                	$resultMusicServer = mysqli_fetch_assoc($resultsMusic_b);
                            ?>	
								<option value="<?php echo $resultMusicServer['player_id'];?>*<?php echo $resultRooms['room_name'];?>">
									<?php echo ucwords($resultRooms['room_name']);?>
								</option>
                            <?php }?>
                        </select>
                    </div>
                    <label>Volume:</label>
                    <select onChange="saveSongChoice()" onKeyUp="saveSongChoice()" id="Then_MC_Volume" style="width:100%;margin-bottom:2px;" class="form-control1">
                        <?php 
                            $i=0;  while($i<100){ $i=$i+10;
                        ?>	
							<option value="<?php echo $i;?>"><?php echo ($i);?></option>
                        <?php }?>
                    </select>
                    <script>
                        var songPlaylist;
                        function saveSongChoice(){
                        	$("#Then_MC_RoomSync").hide();
                        	$("#Then_MC_Playlist").hide();
                        	$("#Then_MC_RoomSync").hide();
                        	$("#Then_MC_Song").hide();
                        	$("#Then_MC_SongPlaylist").hide();
                        	
                        	if($("#Then_MC_Command").val()=="Play"){ /* Play */
                        		$("#Then_MC_SongPlaylist").show();
                        		$("#Then_MC_Song").prop("disabled",false);
                        		$("#Then_MC_Song").show();
                        		songPlaylist=$("#Then_MC_Song").val().split("*");
                        		var niceText = "Play Song: "+songPlaylist[1]+" at volume: "+$("#Then_MC_Volume").val();
                        		var endString="<THEN>PLAYSONG|"+$("#Then_MC_Room").val().split("*")[0]+"|"+ songPlaylist[0] +"|"+$("#Then_MC_Volume").val() +"|"+$("#Then_MC_Command").val() + "</THEN> " + niceText; 
                        	}else if($("#Then_MC_Command").val()=="Playlist"){ /* Playlist */
                        		$("#Then_MC_SongPlaylist").show();
                        		$("#Then_MC_Playlist").show();
                        		songPlaylist=$("#Then_MC_Playlist").val().split("*");
                        		var niceText = "Play Playlist: "+songPlaylist[1]+" at volume: "+$("#Then_MC_Volume").val();
                        		var endString="<THEN>PLAYSONG|"+$("#Then_MC_Room").val().split("*")[0]+"|"+ songPlaylist[0] +"|"+$("#Then_MC_Volume").val() +"|"+$("#Then_MC_Command").val() + "</THEN> " + niceText; 
                        	}else if($("#Then_MC_Command").val()=="Sync"){ /* Sync */
                        		$("#Then_MC_RoomSync").show();
                        		var niceText = "Sync Players: "+ $("#Then_MC_Room").val().split("*")[1] + ", " + $("#Then_MC_RoomSyncValue").val().split("|")[1] + " at volume: "+$("#Then_MC_Volume").val();
                        		var endString="<THEN>PLAYSONG|"+$("#Then_MC_Room").val().split("*")[0]+"|"+ $("#Then_MC_RoomSyncValue").val().split("|")[0]+"|"+$("#Then_MC_Volume").val() +"|"+$("#Then_MC_Command").val() + "</THEN> " + niceText; 
                        	}else{
                        		$("#Then_MC_Song").prop("disabled",true);
                        	}
                        	
                        	save_that_event(endString);
                        }
                    </script>
                </div>
                <!---- Add Delay -->
                <div style="display:none;" class="AddDelay">
                    <label>Delay in Milliseconds:</label>
                    <input type="number" onChange="saveDelay()" onKeyUp="saveDelay()" id="Then_Delay" min="0" value="0" style="width:100%;margin-bottom:2px;" class="form-control1"/>
                    <script>
                        function saveDelay(){
                        	var endString = "<THEN>DELAY:" + $("#Then_Delay").val() + "</THEN> Delay For: " + $("#Then_Delay").val() + " Milliseconds";
                        	save_that_event(endString);
                        }
                    </script>
                </div>
                <!-- Sensors -->
                <label class="then_that_EnableSensors" for="">Select a Sensor:</label>
                <select class="form-control1 then_that_EnableSensors" style="height:30px;width:100%;" onchange="ifttt_sensor()">
                    <option value="">Not Set</option>
                    <?php 
                        $query5 = "SELECT * FROM sensors ORDER BY sensor_kind ASC";
                        $results5 = mysqli_query($GS_DBCONN, $query5);
                        while($result5 = mysqli_fetch_assoc($results5)) { 
                    ?>	
						<option value="<?php echo $result5['ID'];?>|<?php echo $result5['sensor_name'];?>"><?php echo $result5['sensor_name'];?></option>
                    <?php }?>
                </select>
                <label class="then_that_EnableSensors" for="">Select a State:</label>
                <select class="form-control1 then_that_EnableSensors" id="selectSensor_state" style="height:30px;width:100%;" onchange="ifttt_sensor()">
                    <option value="Enable">Enable</option>
                    <option value="Disable">Disable</option>
                </select>
                <script>
                    function ifttt_sensor(){
                    	var niceText = $("#selectSensor_state option:selected").text() + " " + $("select.then_that_EnableSensors").val().split("|")[1];
                    	var endString = "<THEN>"+$('#selectSensor_state').val()+" Sensor:" + $("select.then_that_EnableSensors").val().split("|")[0] + "</THEN> " + niceText;
                    	save_that_event(endString);
                    }
                </script>
                <!-- Cameras -->
                <label class="then_that_Cameras" for="">Select a Camera:</label>
                <select class="form-control1 then_that_Cameras" style="height:30px;width:100%;" onchange="ifttt_camera()">
                    <option value="">Not Set</option>
                    <?php 
                        $query6 = "SELECT * FROM camera_list ORDER BY ID ASC";
                        $results6 = mysqli_query($GS_DBCONN, $query6);
                        while($result6 = mysqli_fetch_assoc($results6)) { 
                    ?>	
						<option value="<?php echo $result6['ID'];?>|<?php echo $result6['camera_name'];?>"><?php echo $result6['camera_name'];?></option>
                    <?php }?>
                </select>
                <label class="then_that_Cameras" for="">Select a State:</label>
                <select class="form-control1 then_that_Cameras" id="selectCamera_state" style="height:30px;width:100%;" onchange="ifttt_camera()">
                    <option value="Enable">Enable</option>
                    <option value="Disable">Disable</option>
                </select>
                <script>
                    function ifttt_camera(){
                    	var niceText = $("#selectCamera_state option:selected").text() + " " + $("select.then_that_Cameras").val().split("|")[1]+ " Camera";
                    	var endString = "<THEN>"+$('#selectCamera_state').val()+" Camera:" + $("select.then_that_Cameras").val().split("|")[0] + "</THEN> " + niceText;
                    	save_that_event(endString);
                    }
                </script>
                <!-- Scenes -->
                <label class="then_that_Scenes" for="">Select a Scene:</label>
                <select class="form-control1 then_that_Scenes" style="height:30px;width:100%;" onchange="save_that_event($(this).val())">
                    <option value="">Not Set</option>
                    <?php 
                        $query7 = "SELECT * FROM scene ";
                        $results7 = mysqli_query($GS_DBCONN, $query7);
                        while($result7 = mysqli_fetch_assoc($results7)) { 
                    ?>	
						<option value="<THEN>Scene:<?php echo $result7['ID'];?></THEN> Scene: <?php echo $result7['scene_name'];?>">Scene: <?php echo $result7['scene_name'];?></option>
                    <?php }?>
                </select>
                
                <!-- Rooms -->
               <div class="input-group then_that_Rooms">
					<span class="input-group-addon"><b>Room:</b></span>
	                <select class="form-control1" id="selectRoom_id" style="height:30px;width:100%;" onchange="ifttt_room()">
	                    <option value="">Not Set</option>
	                    <?php 
	                        $query9 = "SELECT * FROM home_rooms";
	                        $results9 = mysqli_query($GS_DBCONN, $query9);
	                        while($result9 = mysqli_fetch_assoc($results9)) { 
	                    ?>	
							<option value="<?php echo $result9['ID'];?>|<?php echo $result9['room_name'];?>"><?php echo $result9['room_name'];?></option>
	                    <?php }?>
	                </select>
               </div>
                <div class="input-group then_that_Rooms">
					<span class="input-group-addon"><b>Group:</b></span>
	                <select class="form-control1" id="selectRoom_group" style="height:30px;width:100%;" onchange="ifttt_room()">
	                    <option value="">All Devices</option>
	                    <?php 
	                        $query9_b = "SELECT * FROM device_groups";
	                        $results9_b = mysqli_query($GS_DBCONN, $query9_b);
	                        while($result9_b = mysqli_fetch_assoc($results9_b)) { 
	                    ?>	
							<option value="<?php echo $result9_b['ID'];?>|<?php echo $result9_b['group_name'];?>"><?php echo $result9_b['group_name'];?></option>
	                    <?php } ?>
	                </select>
                </div>
                <div class="input-group then_that_Rooms">
					<span class="input-group-addon"><b>State:</b></span>
	                <select class="form-control1" id="selectRoom_state" style="height:30px;width:100%;" onchange="ifttt_room()">
	                    <option value="noState">No State</option>
	                    <option value="1">On</option>
	                    <option value="0">Off</option>
	                </select>
                </div>
                <!--brightness -->
				<div class="input-group then_that_Rooms">
					<span class="input-group-addon"><b>Brightness:</b></span>
					<select class="form-control1" id="selectRoom_brightness" style="height:30px;width:100%;" onchange="ifttt_room()">
						<option value="0">No Brightness</option>
						<option value="10">10%</option>
						<option value="20">20%</option>
						<option value="30">30%</option>
						<option value="40">40%</option>
						<option value="50">50%</option>
						<option value="60">60%</option>
						<option value="70">70%</option>
						<option value="80">80%</option>
						<option value="90">90%</option>
						<option value="100">100%</option>
					</select>
				</div>

				<!-- color -->
				<div class="input-group then_that_Rooms">
					<span class="input-group-addon"><b>Color:</b></span>
					<select class="form-control1" id="selectRoom_color" style="height:30px;width:100%;" onchange="ifttt_room()">
						<option value="noColor">No Color</option>
		                <option value="coolwhite">Cool White</option>
		                <option value="warmwhite">Warm White</option>
		                <option value="green">Green</option>
		                <option value="red">Red</option>
		                <option value="blue">Blue</option>
		                <option value="purple">purple</option>
		                <option value="pink">Pink</option>
		                <option value="yellow">Yellow</option>
		                <option value="orange">Orange</option>
					</select>
				</div>
                
                <!--effect -->
				<div class="input-group then_that_Rooms">
					<span class="input-group-addon"><b>Effect:</b></span>
					<select class="form-control1" id="selectRoom_effect" style="height:30px;width:100%;" onchange="ifttt_room()">
						<option value="noEffect">No Effect</option>
						<option value="BlinkOnce">Blink Once</option>
						<option value="BlinkTimes">Blink (2 Sec)</option>
					</select>
				</div>
                <script>
                    function ifttt_room(){
                    	if($("#selectRoom_group").val()==""){ //room
   
                			var niceText = "Turn " + $("#selectRoom_state option:selected").text() + " " + $("#selectRoom_id").val().split("|")[1];
                			niceText = niceText + " Brightness: "+$("#selectRoom_brightness").val() + " Color: "+$("#selectRoom_color").val() + " Effect: "+$("#selectRoom_effect").val();

                			var endString = "<THEN>RoomState:"+ $("#selectRoom_id").val().split("|")[0]+":"+$('#selectRoom_state').val() + ":" + $("#selectRoom_brightness").val()+":"+$("#selectRoom_color").val()+":"+ $("#selectRoom_effect").val() + "</THEN> " + niceText;
                			save_that_event(endString)
                    		
                    	}else{ //room Group
                    		var niceText = "Turn " + $("#selectRoom_state option:selected").text() + " " + $("#selectRoom_id").val().split("|")[1] + " " +$('#selectRoom_group option:selected').text();
                    		niceText = niceText + " Brightness: "+$("#selectRoom_brightness").val() + " Color: "+$("#selectRoom_color").val() + " Effect: "+$("#selectRoom_effect").val();
                    		var endString = "<THEN>RG Room:"+ $("#selectRoom_id").val().split("|")[0]+ ":"+$('#selectRoom_group').val().split("|")[0]+":"+$('#selectRoom_state').val()+":"+$('#selectRoom_brightness').val()+":"+$('#selectRoom_color').val()+":"+$('#selectRoom_effect').val()+ "</THEN> " + niceText;
                    		save_that_event(endString);
                    	}// end else
                    } //end function
                </script>
                
                <!-- Groups -->
                <div class="input-group then_that_Groups">
					<span class="input-group-addon"><b>Group:</b></span>
	                <select class="form-control1" id="selectGroup_id" style="height:30px;width:100%;" onchange="ifttt_group()">
	                    <?php 
	                        $query9_b = "SELECT * FROM device_groups";
	                        $results9_b = mysqli_query($GS_DBCONN, $query9_b);
	                        while($result9_b = mysqli_fetch_assoc($results9_b)) { 
	                    ?>	
							<option value="<?php echo $result9_b['ID'];?>|<?php echo $result9_b['group_name'];?>"><?php echo $result9_b['group_name'];?></option>
	                    <?php } ?>
	                </select>
                </div>
                <div class="input-group then_that_Groups">
					<span class="input-group-addon"><b>State:</b></span>
	                <select class="form-control1" id="selectGroup_state" style="height:30px;width:100%;" onchange="ifttt_group()">
	                    <option value="noState">No State</option>
	                    <option value="1">On</option>
	                    <option value="0">Off</option>
	                </select>
                </div>
                <!--brightness -->
				<div class="input-group then_that_Groups">
					<span class="input-group-addon"><b>Brightness:</b></span>
					<select class="form-control1" id="selectGroup_brightness" style="height:30px;width:100%;" onchange="ifttt_group()">
						<option value="0">No Brightness</option>
						<option value="10">10%</option>
						<option value="20">20%</option>
						<option value="30">30%</option>
						<option value="40">40%</option>
						<option value="50">50%</option>
						<option value="60">60%</option>
						<option value="70">70%</option>
						<option value="80">80%</option>
						<option value="90">90%</option>
						<option value="100">100%</option>
					</select>
				</div>

				<!-- color -->
				<div class="input-group then_that_Groups">
					<span class="input-group-addon"><b>Color:</b></span>
					<select class="form-control1" id="selectGroup_color" style="height:30px;width:100%;" onchange="ifttt_group()">
						<option value="noColor">No Color</option>
		                <option value="coolwhite">Cool White</option>
		                <option value="warmwhite">Warm White</option>
		                <option value="green">Green</option>
		                <option value="red">Red</option>
		                <option value="blue">Blue</option>
		                <option value="purple">purple</option>
		                <option value="pink">Pink</option>
		                <option value="yellow">Yellow</option>
		                <option value="orange">Orange</option>
					</select>
				</div>
                
                <!--effect -->
				<div class="input-group then_that_Groups">
					<span class="input-group-addon"><b>Effect:</b></span>
					<select class="form-control1" id="selectGroup_effect" style="height:30px;width:100%;" onchange="ifttt_group()">
						<option value="noEffect">No Effect</option>
						<option value="BlinkOnce">Blink Once</option>
						<option value="BlinkTimes">Blink (2 Sec)</option>
					</select>
				</div>
                <script>
                    function ifttt_group(){
            			var niceText = "Turn " + $("#selectGroup_state option:selected").text()+" "+$("#selectGroup_id").val().split("|")[1];
            			niceText = niceText + " Brightness: "+$("#selectGroup_brightness").val() + " Color: "+$("#selectGroup_color").val() + " Effect: "+$("#selectGroup_effect").val();

            			var endString = "<THEN>Group:"+ $("#selectGroup_id").val().split("|")[0]+":"+$('#selectGroup_state').val()+":"+$("#selectGroup_brightness").val()+":"+$("#selectGroup_color").val()+":"+$("#selectGroup_effect").val()+"</THEN> " + niceText;
            			save_that_event(endString)
                    } //end function
                </script>                
                
                
                <!-- Scripts -->
                <label class="then_that_Scripts" for="">Select a Script:</label>
                <select class="form-control1 then_that_Scripts" style="height:30px;width:100%;" onchange="save_that_event($(this).val())">
                    <option value="">Not Set</option>
                    <?php 
                        $query10_a = "SELECT * FROM custom_scripts ORDER BY ID ASC";
                        $results10_a = mysqli_query($GS_DBCONN, $query10_a);
                        while($result10_a = mysqli_fetch_assoc($results10_a)) { 
                    ?>	
						<option value="<THEN>Script: <?php echo $result10_a['ID'];?>:1</THEN> Execute: <?php echo $result10_a['script_name'];?>">
							<?php echo $result10_a['script_name'];?>
						</option>
                    <?php } ?>
                </select>
                <!-- Alarm -->
                <label class="then_that_Alarm" for="">Select a state:</label>
                <select class="form-control1 then_that_Alarm" style="height:30px;width:100%;" onchange="save_that_event($(this).val())">
                    <option value="">Not Set</option>
                    <option value="<THEN>Alarm:On</THEN> Alarm On">Alarm On</option>
                    <option value="<THEN>Alarm:On:Home</THEN> Alarm on in Home mode">Alarm On Home</option>
                    <option value="<THEN>Alarm:On:Away</THEN> Alarm on in  Away mode">Alarm On Away</option>
                    <option value="<THEN>Alarm:Off</THEN> Alarm Off">Alarm Off</option>
                </select>
                <!-- Occupancy Status -->
                <label class="then_that_Occupancy" for="">Select a status:</label>
                <select class="form-control1 then_that_Occupancy" style="height:30px;width:100%;" onchange="save_that_event($(this).val())">
                    <option value="">Not Set</option>
                    <option value="<THEN>Occupancy:Home</THEN> Set to Home">Set to Home</option>
                    <option value="<THEN>Occupancy:Away</THEN> Set to Away">Set to Away</option>
                </select>
                <div style="margin-top:20px;float:right;">
                    <button class="btn btn-primary"  data-dismiss="modal">Save</button>
                    <button class="btn btn-default" data-dismiss="modal" id="thatMoreCancelBtn">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function removeIfttTags(start,end,length,text){
    	var start_pos = text.indexOf(start) + length;
    	var end_pos = text.indexOf(end,start_pos);
    	var text_to_get = text.substring(start_pos,end_pos);
    	return text.replace((start+text_to_get+end),"");
    }
    
    
    /* number of ifttt row */
    var thatNumber = 0;
    function save_that_event(endText){
    	if(endText!=""){
    		/* user selected an item now set it to that combo box */
    		$('#then_that' + thatNumber).append($("<option></option>").attr("value",endText).text(removeIfttTags('<THEN>','</THEN>',6,endText)));
    		$('#then_that' + thatNumber).val(endText);
    	}else{
    		/* user dident select anything in modal so set to 'not set' */
    		$('#then_that' + thatNumber).val("");
    	}
    }
    
    function show_that_modal(that_number,type){
    	/* show modal */
    	if(type==""){return false;}
    	thatNumber = that_number;;
    	$('#ThatMoreDialogLink').click();
    	$('#type_header_that').text(type);
    	
    	function hide_all(){
    	  /* hide all select boxes in modal */
    	  
    	  $("#that_moreHeader").text("Select an Option");
    	 
    	  $(".then_that_Alarm").hide();
    	  $(".then_that_BlinkRooms").hide();
    	  $(".then_that_Alarm").hide();
    	  $(".then_that_RoomGroups").hide();
    	  $(".then_that_Groups").hide();
    	  $(".then_that_Rooms").hide();
    	  $(".then_that_Scenes").hide();
    	  $(".then_that_Cameras").hide();
    	  $(".then_that_EnableSensors").hide();
    	  $(".then_that_devices").hide();
    	  $(".then_that_Occupancy").hide();
    	  $(".Send_email").hide();
    	  $(".MusicControl").hide();
    	  $(".Write_log").hide();
    	  $(".AddDelay").hide();
    	  $(".then_that_Scripts").hide();
    	  $(".Speak_in_room").hide();
    	}
       
    	/* show items in modal based on selection of ifttt if select box */
    	if (type=="Alarm"){  hide_all();  $(".then_that_Alarm").show(); $("#that_moreHeader").text("Select an Alarm State");}
    	if (type=="Status"){hide_all();  $(".then_that_Occupancy").show(); $("#that_moreHeader").text("Occupancy Status");}
    	if (type=="Device Settings"){ hide_all(); $(".then_that_devices").show(); $("#that_moreHeader").text("Select a Device");}
    	if (type=="Sensor Settings"){   hide_all();  $(".then_that_EnableSensors").show(); $("#that_moreHeader").text("Select a Sensor");}		
    	if (type=="Camera Settings"){  hide_all(); $(".then_that_Cameras").show();$("#that_moreHeader").text("Select a Camera");}	
    	if (type=="Scene"){  hide_all(); $(".then_that_Scenes").show(); $("#that_moreHeader").text("Select a Scene");}
    	if (type=="group_onOff"){ hide_all(); $(".then_that_RoomGroups").show(); $(".then_that_Groups").show(); $("#that_moreHeader").text("Select a Group");}
    	if (type=="room"){ hide_all(); $(".then_that_Rooms").show(); $("#that_moreHeader").text("Select a Room");}
    								
    	if (type=="Blink Phillips Hue"){ hide_all(); $(".Blink_Phillips_hue").show(); $("#that_moreHeader").text("Select a Device");}
    	
    	if (type=="Send Email"){ hide_all(); $(".Send_email").show(); $("#that_moreHeader").text("Send an Email");}
    	if (type=="Write To Log"){ hide_all(); $(".Write_log").show(); $("#that_moreHeader").text("Write to Log");}
    	if (type=="Music Control"){ hide_all(); $(".MusicControl").show();$("#that_moreHeader").text("Music Control");}
    	if (type=="Delay"){ hide_all();  $(".AddDelay").show(); $("#that_moreHeader").text("Add a Delay");}
    	if (type=="Script"){ hide_all(); $(".then_that_Scripts").show(); $("#that_moreHeader").text("Select a Script");}
    	if (type=="Speak"){ hide_all(); $(".Speak_in_room").show(); $("#that_moreHeader").text("Speak In Room(s)");}
    	if (type=="UINotification"){ hide_all(); $(".UINotification").show(); $("#that_moreHeader").text("Send UI Notification");}
    }
    
</script>
<script>
    function ifThisList(id){
    	$(id).append($("<option></option>").attr("value","").text("Not Set"));
    	$(id).append($("<option></option>").attr("value","Sensor").text("Sensor State"));
		$(id).append($("<option></option>").attr("value","DataSensor").text("Data Sensor"));
    	$(id).append($("<option></option>").attr("value","DeviceState").text("Device State"));
    	$(id).append($("<option></option>").attr("value","Schedule").text("Schedule"));
    	$(id).append($("<option></option>").attr("value","Time").text("Time"));
    	<?php if($GS_weatherServiceEnabled==true) :?>
    		$(id).append($("<option></option>").attr("value","weather").text("Outdoor Weather"));
    	<?php endif;?>
    	$(id).append($("<option></option>").attr("value","Alarm").text("Alarm Status"));
    	$(id).append($("<option></option>").attr("value","Status").text("Occupancy Status"));
    }	
    
    					
    function thenThatList(id){
    	$(id).append($("<option></option>").attr("value","").text("Not Set"));
    	$(id).append($("<option></option>").prop("disabled",true).text("Devices:").css({"font-weight":"bold","font-size":"14px","color":"<?php echo $GS_Config['themeColorMain'];?>"}));
    	$(id).append($("<option></option>").attr("value","Device Settings").text("Change Device State"));
    	$(id).append($("<option></option>").attr("value","room").text("Turn Room On/Off"));
    	$(id).append($("<option></option>").attr("value","group_onOff").text("Turn Group On/Off"));
    	$(id).append($("<option></option>").attr("value","Scene").text("Activate Scene"));
    	$(id).append($("<option></option>").prop("disabled",true).text("Settings").css({"font-weight":"bold","font-size":"14px","color":"<?php echo $GS_Config['themeColorMain'];?>"}));
    	$(id).append($("<option></option>").attr("value","Sensor Settings").text("Sensor Settings"));
    	$(id).append($("<option></option>").attr("value","Camera Settings").text("Camera Settings"))
    	$(id).append($("<option></option>").prop("disabled",true).text("Misc..").css({"font-weight":"bold","font-size":"14px","color":"<?php echo $GS_Config['themeColorMain'];?>"}));
    	<?php if($GS_emailServiceEnabled == true):?>
    		$(id).append($("<option></option>").attr("value","Send Email").text("Send An Email"));
    	<?php endif;?>
    	$(id).append($("<option></option>").attr("value","Write To Log").text("Write To Log"));
    	$(id).append($("<option></option>").attr("value","UINotification").text("Send UI Notification"));
    	$(id).append($("<option></option>").attr("value","Speak").text("Speak In Room"));
    	<?php if($GS_squeezeBoxServiceEnabled == true):?>
    		$(id).append($("<option></option>").attr("value","Music Control").text("Music Control"));
    	<?php endif;?>
    	$(id).append($("<option></option>").attr("value","Script").text("Execute a Script"));
    	$(id).append($("<option></option>").attr("value","Delay").text("Add a Delay"));
    	$(id).append($("<option></option>").prop("disabled",true).text("Alarm/Ststus:").css({"font-weight":"bold","font-size":"14px","color":"<?php echo $GS_Config['themeColorMain'];?>"}));
    	$(id).append($("<option></option>").attr("value","Alarm").text("Alarm Status"));
    	$(id).append($("<option></option>").attr("value","Status").text("Occupancy Status"));
    	$(id).append($("<option></option>").prop("disabled",true).text("Current Selection:").css({"font-weight":"bold","font-size":"14px","color":"<?php echo $GS_Config['themeColorMain'];?>"}));
    }  
</script>