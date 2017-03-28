<!--- Find Sensor --->
  <!-- Modal -->
	<div class="modal fade" id="find_sensor_modal" role="dialog" style="z-index:99999;margin-top:200px;">
		<div class="modal-dialog" style="width:400px;">
			  <!-- this open the that more dialog -->
			  <a href="#" data-toggle="modal" data-target="#find_sensor_modal" id="FindSensorDialogLink" style="display:none;"></a>
			  <!-- Modal content-->
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 style="" class="modal-title" >Find <?php echo $deviceOrSensor;?></h4>
				</div>
				<div class="modal-body" style="overflow:auto;">

					<center>Activate The New <?php echo $deviceOrSensor;?> To Find It</center>

					<center id="find_sensor_loading"><i style="font-size:20px;" class="fa fa-spinner faa-spin animated"></i> Searching</center>
					<style>
						table td {  height: 50px; }
					</style>
					<input type="hidden" id="submited_sensor" value="" />

					<div style="height:auto;overflow:auto;" id="find_sensor_autoload"></div>
					
					<!--send command to find sensors in settings table -->
					<form method="POST" class="autoform">
						<input type="hidden" name="type" value="<?php echo temp_encode("start_find_sensors");?>" />
						<input type="submit" id="start_find_sensors_btn" style="display:none;" />
					</form>
					<form method="POST" class="autoform">
						<input type="hidden" name="type" value="<?php echo temp_encode("end_find_sensors");?>" />
						<input type="submit" id="end_find_sensors_btn" style="display:none;" />
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<script>
		function find_sensor(sensor) {
			$('#end_find_sensors_btn').click();
			$('#start_find_sensors_btn').click();
			$('#find_sensor_table').html("");
			$('#submited_sensor').val(sensor);
			$("#FindSensorDialogLink").click();	
			$("#find_sensor_loading").show();		
		}

		function set_sensor_address(address) {
			$('#end_find_sensors_btn').click();
			$('#' + $('#submited_sensor').val()).val(address);
		}
	</script>

	
	
