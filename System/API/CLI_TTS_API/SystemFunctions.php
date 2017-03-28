<?php
 //WINDOWS TTS
 function speak($text,$room="All"){
	global $GS_DBCONN;
	global $GS_Config;
	//check if rinning on Windows
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		//get text and turn it to tts and save it to a file 
		// SOURCE: https://github.com/brookhong/tts
		$save="C:\inetpub\wwwroot\sh\gui\music\\tts\Voice";
		exec('C:\inetpub\wwwroot\sh\System\API\CLI_TTS_API\tts.exe -f 10 -v 0 "'.$text.'" -o '.$save.' -t',$out);
	}else{//linux
		exec("/usr/bin/flite -voice slt -t '".$text."' -o /var/www/html/sh/gui/music/TestTTS.mp3");
	}
	
	usleep(10000); 
		 
	if($room=="All"){ //play sound in each room
		$query   = "SELECT * FROM music_servers WHERE enabled='1'";
		$SBPlayers = mysqli_query($GS_DBCONN, $query);
		while($SBPlayer  = mysqli_fetch_assoc($SBPlayers)){
			GF_roomSoundControl($SBPlayer['room_id'],"","100","NoRepeat"); //make sure repeat is off
			GF_roomSoundControl($SBPlayer['room_id'],"http://".$GS_Config['LocalIP']."/music/tts/Voice0.mp3","100","Play");
		}
	}else{ //play in selected rooms
		foreach (explode("|",trim($room,"|")) as $room){
			$query   = "SELECT * FROM music_servers WHERE room_id='".$room."' AND enabled='1'";
			$SBPlayers = mysqli_query($GS_DBCONN, $query);
			while($SBPlayer  = mysqli_fetch_assoc($SBPlayers)){
				GF_roomSoundControl($SBPlayer['room_id'],"","100","NoRepeat"); //make sure repeat is off
				GF_roomSoundControl($SBPlayer['room_id'],"http://".$GS_Config['LocalIP']."/music/tts/Voice0.mp3","100","Play");
			}
		}
	}
	return $out; //reurn exec output
}
 