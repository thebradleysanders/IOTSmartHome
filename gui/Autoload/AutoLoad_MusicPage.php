<?php 
	
	$count=0;
	$query = "SELECT * FROM music_servers WHERE enabled='1' ORDER BY player_index ASC";
	$musicServers = mysqli_query($GS_DBCONN, $query);
	while($server = mysqli_fetch_assoc($musicServers)){ $count++;
		//get room Info
		$query = "SELECT * FROM home_rooms WHERE ID='".$server['room_id']."'";
		$room = mysqli_fetch_assoc(mysqli_query($GS_DBCONN, $query));	                           

		$mySqueezeConnection = new SqueezeConnection($server['ip_address'],"9090","","");
		if ($mySqueezeConnection->connect()){ $mySqueezeCenter = new SqueezeCenter($mySqueezeConnection);}			
		$mySqueezeCenter->Players->count();
		$mySqueezeCenter->setCurrentPlayer((int)$server['player_index']);	
		
		$playerID = $mySqueezeCenter->CurrentPlayer->id();
		$playerName =$mySqueezeCenter->CurrentPlayer->name("?");
		$power = $mySqueezeCenter->CurrentPlayer->power("?");
		$song = $mySqueezeCenter->CurrentPlayer->title();
		$artist = $mySqueezeCenter->CurrentPlayer->artist();
		$muting_state=$mySqueezeCenter->CurrentPlayer->Mixer->muting("?");	
		$volume=$mySqueezeCenter->CurrentPlayer->Mixer->volume("?");	
		$mode_state = $mySqueezeCenter->CurrentPlayer->mode();
		$SyncGroups = $mySqueezeCenter->CurrentPlayer->sync("?");
		
		mysqli_query($GS_DBCONN, "UPDATE music_servers SET player_id='".$playerID."', player_name='".$playerName."' WHERE room_id='".$room['ID']."'");
		
?>

	<script>	
	//Sync Groups
		$("#SyncGroups<?php echo $room['ID'];?>").val("<?php echo $SyncGroups;?>");
	
	//Player Name
		$("#playerName<?php echo $room['ID'];?>").text("<?php echo $playerName;?>");

	//Player Address
		$("#playerAddress<?php echo $room['ID'];?>").text("<?php echo $playerID;?>");
	
	//Next
		$("#room<?php echo $room["ID"];?>Next").css("display","inline-block");
	//Back
		$("#room<?php echo $room["ID"];?>Back").css("display","inline-block");

	//Play/Pause
		<?php if($mode_state=="pause"):?>
			$("#room<?php echo $room["ID"];?>Pause").hide();
			$("#room<?php echo $room["ID"];?>Play").css("display","inline-block");
		<?php else :?>		
			$("#room<?php echo $room["ID"];?>Play").hide();
			$("#room<?php echo $room["ID"];?>Pause").css("display","inline-block");
		<?php endif;?>
	//Muste/Unmute
		<?php if ($muting_state=="0") :?>
			$("#room<?php echo $room["ID"];?>Unmute").hide();
			$("#room<?php echo $room["ID"];?>Mute").css("display","inline-block");
		<?php else:?>
			$("#room<?php echo $room["ID"];?>Mute").hide();
			$("#room<?php echo $room["ID"];?>Unmute").css("display","inline-block");
		<?php endif;?>
	//Volume
		if($("#volume_ctrl<?php echo ucfirst($room['ID']);?>").val()=="0"){
			$(".MusicVolumeSlider<?php echo $room['ID'];?>").slider( "option", "value", <?php echo $volume;?>);
		}
	//Now Playing
	<?php if ($mode_state=="play"):?>
		$("#show_playing<?php echo $room['ID'];?>").html("<?php echo substr($song,0,40);?> - <?php echo substr($artist,0,40);?>");
	<?php elseif($mode_state=="pause") : ?>
		$("#show_playing<?php echo $room['ID'];?>").html("<b>(Paused)</b>  <?php echo substr($song,0,40);?> - <?php echo substr($artist,0,40);?>");
	<?php elseif($mode_state=="stop") : ?>
		$("#show_playing<?php echo $room['ID'];?>").html("<b>(Stopped)</b>");
	<?php endif;?>
	
	//Power
	<?php if ($power=="1") :?>
		$("#room<?php echo $room["ID"];?>PowerOn").hide();
		$("#room<?php echo $room["ID"];?>PowerOff").show();
	<?php else :?>
		$("#room<?php echo $room["ID"];?>PowerOff").hide();
		$("#room<?php echo $room["ID"];?>PowerOn").show();
	<?php endif;?>
	</script>

<?php }