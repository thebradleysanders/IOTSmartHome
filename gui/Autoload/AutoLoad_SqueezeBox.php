<?php 
	if($GS_squeezeBoxServiceEnabled == true){ /* Check If Service Is Enabled */}else{echo "<script>$('#MusicRoomShortcuts').hide();</script>";exit;}

	
	$musicRoomCount=0;
	if($_GET['room_id']!=""){ /* this one is for manage_rooms.php */
		$query1 = "SELECT * FROM home_rooms WHERE ID='".$_GET['room_id']."'";
	}else{ /* this one is for index.php */
		$query1 = "SELECT * FROM home_rooms";
	}
	
	$results1 = mysqli_query($GS_DBCONN, $query1);
	while($result1 = mysqli_fetch_assoc($results1)) {     
				  
		//get Squeezebox Info
		$query1 = "SELECT * FROM music_servers where enabled='1' AND room_id='".$result1['ID']."'";
		$results1_a = mysqli_query($GS_DBCONN, $query1);
		$server = mysqli_fetch_assoc($results1_a); 
		$server_count = mysqli_num_rows($results1_a);    
		
		if($server_count==1){ $musicRoomCount++;
			$mySqueezeConnection= new SqueezeConnection((String)$server['ip_address'],"9090","","");
			if ($mySqueezeConnection->connect()){ $mySqueezeCenter = new SqueezeCenter($mySqueezeConnection);}			
			$mySqueezeCenter->Players->count();
			$mySqueezeCenter->setCurrentPlayer((int)$server['player_index']);	
			$song = $mySqueezeCenter->CurrentPlayer->title();
		}else{
			continue;
		}  	
	?>	
		
		<?php if($_GET['room_id']==""):?>
			<div style="height:100px;width:220px;display:inline-block;margin-right:20px;color:#999999;font-size:14px;padding:5px;border:1px solid #f1f1f1;text-align:center;overflow:auto;">
				<b>Room: <?php echo ucwords($result1['room_name']);?></b><br/>
		<?php else :?>
			<div style="text-align:center;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);max-height:150px;width:100%;margin-bottom:15px;color:#999999;font-size:14px;padding:5px;">
		<?php endif;?>
			<a href="manage_music.php">
				<?php if ($mySqueezeCenter->CurrentPlayer->mode()=="play"):?>
					<?php echo substr($mySqueezeCenter->CurrentPlayer->title(),0,30);?> -
					<?php echo substr($mySqueezeCenter->CurrentPlayer->artist(),0,30);?> 
				<?php elseif($mySqueezeCenter->CurrentPlayer->mode()=="pause") : ?>
					<b>(Paused)</b> 
					<?php echo substr($mySqueezeCenter->CurrentPlayer->title(),0,30);?> -
					<?php echo substr($mySqueezeCenter->CurrentPlayer->artist(),0,30);?> 
				<?php elseif($mySqueezeCenter->CurrentPlayer->mode()=="stop") : ?>
					<b>(Stopped)</b>
				<?php endif;?>
			</a>
			
			<br/>
			
			<!--- PLAY/PAUSE/STOP --->
			<form method="POST" class="autoform_RM" style="display:inline">	
				<input type="hidden" value="<?php echo $result1['ID'];?>" name="room_id"/>
				<input type="hidden" value="<?php echo temp_encode("SqueezeBox");?>" name="type"/>
				
				<?php if ($mySqueezeCenter->CurrentPlayer->mode()=="play") :?>
					<input type="hidden" value="Pause" name="Command"/>
					<button type="submit" class="btn btn-default"><i class="fa fa-pause"></i></button>
				<?php elseif ($mySqueezeCenter->CurrentPlayer->mode()=="pause") :?>
					<input type="hidden" value="Resume" name="Command"/>
					<button type="submit" class="btn btn-default"><i class="fa fa-play"></i></button>	
				<?php elseif ($mySqueezeCenter->CurrentPlayer->mode()=="stop") :?>
					<input type="hidden" value="Play" name="Command"/>
					<button type="submit" class="btn btn-default"><i class="fa fa-play"></i></button>
				<?php endif;?>
			</form>	
			
			<?php if ($mySqueezeCenter->CurrentPlayer->Mixer->muting("?")=="1") :?>
				<!--- UNMUTE --->
				<form method="POST" class="autoform_RM" style="display:inline">	
					<input type="hidden" value="<?php echo $result1['ID'];?>" name="room_id"/>
					<input type="hidden" value="<?php echo temp_encode("SqueezeBox");?>" name="type"/>
					<input type="hidden" value="Unmute" name="Command"/>
					<button type="submit" class="btn btn-default"><i class="fa fa-volume-off"></i></button>		
				</form>	
			<?php else :?>
				<!-- VOLUME -->
				<form method="POST" class="autoform_RM" style="display:inline">	
					<input type="hidden" value="<?php echo $result1['ID'];?>" name="room_id"/>
					<input type="hidden" value="<?php echo temp_encode("SqueezeBox");?>" name="type"/>
					<input type="hidden" value="VolumeDown" name="Command"/>
					<button type="submit" class="btn btn-default"><i class="fa fa-volume-down"></i></button>
				</form>	
				<form method="POST" class="autoform_RM" style="display:inline">	
					<input type="hidden" value="<?php echo $result1['ID'];?>" name="room_id"/>
					<input type="hidden" value="<?php echo temp_encode("SqueezeBox");?>" name="type"/>
					<input type="hidden" value="VolumeUp" name="Command"/>
					<button type="submit" class="btn btn-default"><i class="fa fa-volume-up"></i></button>	
				</form>	
			<?php endif;?>
			
		</div>
	<?php } if($musicRoomCount==0):?>
		<script>$("#MusicRoomShortcuts").hide();</script>
	<?php else :?>
		<script>$("#MusicRoomShortcuts").slideDown();</script>
	<?php endif;?>
			
			
	<script>
		$(document).ready(function() {
			$('.autoform_RM').on('submit', function(e) {
				e.preventDefault();
				$.ajax({
					url: $(this).attr('action') || window.location.pathname,
					type: "POST",
					data: $(this).serialize(),
					success: function(){},
					error: function(jXHR, textStatus, errorThrown) {
						alert(errorThrown);
					}
				});
			});
		});
	</script>