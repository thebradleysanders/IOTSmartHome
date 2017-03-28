<?php

######################### PLAY SONG IN ROOM ########################
function GF_roomSoundControl($room,$song,$volume,$command, $calledBy=""){	
	global $GS_DBCONN;
	global $GS_Config;
	global $GS_squeezeBoxServiceEnabled;

	if($GS_squeezeBoxServiceEnabled == true){ //Check Service Enabled
		//get Squeezebox Info
		$query1 = "SELECT * FROM music_servers where room_id='".trim($room)."' AND enabled='1'";
		$results1_a = mysqli_query($GS_DBCONN, $query1);
		$server = mysqli_fetch_assoc($results1_a); 
		$server_count = mysqli_num_rows($results1_a);
		
		if($server_count==1){
			$mySqueezeConnection = new SqueezeConnection($server['ip_address'],"9090","","");
			if ($mySqueezeConnection->connect()){ $mySqueezeCenter = new SqueezeCenter($mySqueezeConnection);}			
			$mySqueezeCenter->Players->count();
			$mySqueezeCenter->setCurrentPlayer((int)$server['player_index']);			

			if($command=="Play"){ 
				$mySqueezeCenter->CurrentPlayer->power("1"); //Power On
				if(strpos($song, 'http://') !== false || strpos($song, 'https://') !== false){
					$mySqueezeCenter->CurrentPlayer->Playlist->play($song); //Play Radio
				}elseif(strpos($song, 'spotify:') !== false) {
					$mySqueezeCenter->CurrentPlayer->Playlist->play(str_replace("spotify:","",$song)); //Play Radio
				}elseif(strpos($song, 'stored:') !== false){
					$mySqueezeCenter->CurrentPlayer->Playlist->play($GS_Config['MusicDir'].str_replace("stored:","",$song)); //Play Stored
				}else{
					$mySqueezeCenter->CurrentPlayer->Playlist->play(trim($song)); 
				}
			}elseif($command=="Up Next"){ //Up Next
				$mySqueezeCenter->CurrentPlayer->Playlist->insert($GS_Config['MusicDir'].trim($song)); //Play Stored		
			}elseif($command=="Add"){ //Queue
				$mySqueezeCenter->CurrentPlayer->Playlist->add($GS_Config['MusicDir'].trim($song)); //Play Stored						
			}elseif($command=="Pause"){
				$mySqueezeCenter->CurrentPlayer->pause("1",5); //Pause
			}elseif($command=="Resume"){
				$mySqueezeCenter->CurrentPlayer->pause("0",1); //Resume
			}elseif($command=="Stop"){
				$mySqueezeCenter->CurrentPlayer->stop(); //Stop
			}elseif($command=="Mute"){
				$mySqueezeCenter->CurrentPlayer->Mixer->muting("1"); //Mute
			}elseif($command=="Unmute"){
				$mySqueezeCenter->CurrentPlayer->Mixer->muting("0"); //Unmute
			}elseif($command=="VolumeUp"){
				$mySqueezeCenter->CurrentPlayer->Mixer->volume(($mySqueezeCenter->CurrentPlayer->Mixer->volume("?"))+10); //Volume Up
			}elseif($command=="VolumeDown"){
				$mySqueezeCenter->CurrentPlayer->Mixer->volume(($mySqueezeCenter->CurrentPlayer->Mixer->volume("?"))-10); //Volume Down
			}elseif($command=="Repeat"){
				$mySqueezeCenter->CurrentPlayer->Playlist->repeat("1"); //Repeat
			}elseif($command=="NoRepeat"){
				$mySqueezeCenter->CurrentPlayer->Playlist->repeat("0"); //Repeat
			}elseif($command=="Sync"){
				$mySqueezeCenter->CurrentPlayer->sync($song); //Sync Players
			}elseif($command=="Power-on"){
				$mySqueezeCenter->CurrentPlayer->power("1"); //Power On
			}elseif($command=="Power-off"){
				$mySqueezeCenter->CurrentPlayer->power("0"); //Power Off
			}elseif($command=="Next"){
				$currentIndex = $mySqueezeCenter->CurrentPlayer->Playlist->index("?",0); //Next
				$mySqueezeCenter->CurrentPlayer->Playlist->index(($currentIndex+1),0);
			}elseif($command=="Back"){
				$currentIndex = $mySqueezeCenter->CurrentPlayer->Playlist->index("?",0); //Back
				$mySqueezeCenter->CurrentPlayer->Playlist->index(($currentIndex-1),0);
			}elseif($command=="Playlist"){ //Play Playlist
				if(strpos($song, ("spotify:")) !== false) {
					$mySqueezeCenter->CurrentPlayer->Playlist->play($song); 
				}else{
					$count=0;
					$query1 = "SELECT * FROM music_data WHERE playlist LIKE '%".$song."|%' AND song_type='stored' ORDER BY ID ASC";
					$results1 = mysqli_query($GS_DBCONN, $query1);
					while($playlist = mysqli_fetch_assoc($results1)) { $count++;
						if($count==1){ //play first item in list
							$mySqueezeCenter->CurrentPlayer->Playlist->play($GS_Config['MusicDir'].trim($playlist['song_location'])); //Play Stored
						}else{ //add songs to list
							$mySqueezeCenter->CurrentPlayer->Playlist->add($GS_Config['MusicDir'].trim($playlist['song_location'])); //Play Stored
						}						
					}
				}
			}
			
			GF_logging("SqueezeBox In Room: ".$room.", Command: ".$command." Song: ".$song,"|Music|SqueezeBox|Services|".$command."|");
			if (trim($volume)!=""){$mySqueezeCenter->CurrentPlayer->Mixer->muting("0");$mySqueezeCenter->CurrentPlayer->Mixer->volume($volume);} //Volume
			
		}else{GF_logging("Server Not Found For Room: ".trim($room));}
	}
}//end function

################################ System Monitor ############################
function GF_squeezebox_SystemMonitor(){
	global $GS_DBCONN;
	global $GA_enabledService_squeezebox;
	
	if($GA_enabledService_squeezebox['enabled']=="1" || $GA_enabledService_squeezebox['enabled']=="3"){
		//Check each SqueezeBox server
		$query = "SELECT * FROM music_servers WHERE enabled<>'0' ORDER BY enabled ASC";
		$results_SB = mysqli_query($GS_DBCONN, $query);
	
		while($SBServer = mysqli_fetch_assoc($results_SB)){
			if (pingAddress('http://'.$SBServer['ip_address'])==false){
				//error could not connect to sb server, set enabled to 3 and keep looking for the SB server
				mysqli_query($GS_DBCONN, "UPDATE music_servers SET enabled='3' WHERE ID='".$SBServer['ID']."' AND enabled='1'");
			}else{
				mysqli_query($GS_DBCONN, "UPDATE music_servers SET enabled='1' WHERE ID='".$SBServer['ID']."' AND enabled='3'");
			}
		}//End While
	}
}

