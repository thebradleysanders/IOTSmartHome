<?php
    if(!empty($_POST) || !empty($_GET)){
    	$GS_phueServiceBypass = false;
    	$GS_wemoServiceBypass = false;
    	$GS_mqttServiceBypass = false;
    	$GS_emailServiceBypass = false;
    	$GS_squeezeBoxServiceBypass = false;
    	$GS_webIncludesIncluded = true;
    }else{
    	$GS_phueServiceBypass = true;
    	$GS_wemoServiceBypass = true;
    	$GS_mqttServiceBypass = true;
    	$GS_emailServiceBypass = true;
    	$GS_squeezeBoxServiceBypass = true;
    	$GS_webIncludesIncluded = true;
    }
    
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/DBConn.php"));
    
    ################## AUTOLOAD ####################
    if($_GET['autoload']=="true"){
    	$GS_phueServiceBypass = true;
    	$GS_wemoServiceBypass = true;
    	$GS_mqttServiceBypass = true;
    	$GS_emailServiceBypass = true;
    	$GS_squeezeBoxServiceBypass = false;
    	$GS_webIncludesIncluded = false;
    	
    	$upParrentDir = '/../../..';
    	require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    	
    	include("Autoload/AutoLoad_DataStates.php");
    	include("Autoload/AutoLoad_SHAlerts.php");
    	include("Autoload/Autoload_checkTempEncode.php");
    	include("Autoload/AutoLoad_MusicPage.php");
    	include("Autoload/IFTTT_Autoload.php");
    	exit;	
    }
    ################################################
    
    #################### API #######################
    $page_title="Manage Music";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    
            
    // SqueezeBox Music Control 
    if(temp_decode($_POST['type'])=="SqueezeBox" && $GS_squeezeBoxServiceEnabled == true){ //Check If Service Is Enabled 
    	if($_POST['Command']=='Resume'){GF_roomSoundControl($_POST['room_id'],"","","Resume","USER:".$_SESSION['id']);} //Resume
    	if($_POST['Command']=='Pause'){GF_roomSoundControl($_POST['room_id'],"","","Pause","USER:".$_SESSION['id']);} //Pause
    	if($_POST['Command']=="Volume"){GF_roomSoundControl($_POST['room_id'],"",$_POST['volume'],"Volume","USER:".$_SESSION['id']); echo "<script>alert('".$_POST['room_id']."')</script>";} //Volume
    	if($_POST['Command']=='Mute'){GF_roomSoundControl($_POST['room_id'],"","","Mute","USER:".$_SESSION['id']);} //Mute
    	if($_POST['Command']=='Unmute'){GF_roomSoundControl($_POST['room_id'],"","","Unmute","USER:".$_SESSION['id']);} //Unmute			
    	if($_POST['Command']=='VolumeUp'){GF_roomSoundControl($_POST['room_id'],"","","VolumeUp","USER:".$_SESSION['id']);} //Volume Up
    	if($_POST['Command']=='VolumeDown'){GF_roomSoundControl($_POST['room_id'],"","","VolumeDown","USER:".$_SESSION['id']);} //Volume Down
    	if($_POST['Command']=='Play'){GF_roomSoundControl($_POST['room_id'],$_POST['play_song'],"","Play","USER:".$_SESSION['id']); } //Play
    	if($_POST['Command']=='Add'){GF_roomSoundControl($_POST['room_id'],$_POST['play_song'],"","Add","USER:".$_SESSION['id']); } //Add
    	if($_POST['Command']=='Up Next'){GF_roomSoundControl($_POST['room_id'],$_POST['play_song'],"","Up Next","USER:".$_SESSION['id']); } //Up Next
    	if($_POST['Command']=='Sync'){GF_roomSoundControl($_POST['room_id'],$_POST['sync_id'],"","Sync","USER:".$_SESSION['id']);} //Sync
    	if($_POST['Command']=='Power-on'){GF_roomSoundControl($_POST['room_id'],"","","Power-on","USER:".$_SESSION['id']);} //Power On
    	if($_POST['Command']=='Power-off'){GF_roomSoundControl($_POST['room_id'],"","","Power-off","USER:".$_SESSION['id']);} //Power Off
    	if($_POST['Command']=='Next'){GF_roomSoundControl($_POST['room_id'],"","","Next","USER:".$_SESSION['id']);} //Next
    	if($_POST['Command']=='Back'){GF_roomSoundControl($_POST['room_id'],"","","Back","USER:".$_SESSION['id']);} //Back
    	if($_POST['Command']=='Repeat'){GF_roomSoundControl($_POST['room_id'],"","","Repeat","USER:".$_SESSION['id']);} //Repeat
    	if($_POST["Command"]=="PlayPlaylist"){GF_roomSoundControl($_POST['room_id'],$_POST['playlist'],"","Playlist","USER:".$_SESSION['id']); } //Playlist
    }
    
    
    function getSpotifyMusic(){
    	global $GS_DBCONN; 
    	global $GS_spotifyServiceEnabled; 
    	global $GA_enabledService_spotify;
    	
    	if($GS_spotifyServiceEnabled==true){
    		try{
    			require("C:\inetpub\wwwroot\sh\system\API\Spotify_API\src\SpotifyWebAPI.php");
    			require("C:\inetpub\wwwroot\sh\system\API\Spotify_API\src\Session.php");
    			require("C:\inetpub\wwwroot\sh\system\API\Spotify_API\src\Request.php");
    			require("C:\inetpub\wwwroot\sh\system\API\Spotify_API\src\SpotifyWebAPIException.php");
    			$session = new SpotifyWebAPI\Session($GA_enabledService_spotify['service_attr1'], $GA_enabledService_spotify['service_attr2'], 'http://10.0.0.200/manage_music.php');
    			$api = new SpotifyWebAPI\SpotifyWebAPI();
    			
    			if (isset($_GET['code'])) {
    				$session->requestAccessToken($_GET['code']);
    				$api->setAccessToken($session->getAccessToken());
    			} else {
    				header('Location: ' . $session->getAuthorizeUrl(array(
    					'scope' => array(
    						'playlist-modify-private',
    						'playlist-modify-public',
    						'playlist-read-private',
    					)
    				)));
    			}
    
    			$tracks = $api->getMySavedTracks();
    
    			//tracks
    			foreach ($tracks->items as $track) {
    				$track = $track->track;
    				 //see if file exists in db
    				 $query1 = "SELECT * FROM music_data WHERE song_type='stored' AND (song_location='".$track->uri ."')";
    				 $results1 = mysqli_query($GS_DBCONN, $query1);
    				 $exists = mysqli_num_rows($results1);    
    				  
    				if($exists==0){
    					$insert_query="INSERT INTO music_data (song_location,song_added,song_type,song_name,playlist)
    					VALUES('spotify:".$track->uri ."','".time()."','stored','".$track->name."','0')";
    					mysqli_query($GS_DBCONN, $insert_query);
    				} //end exists
    			}
    			
    			
    			//playlists
    			$playlists = $api->getMyPlaylists();
    			foreach ($playlists->items as $playlist) {
    				$insert_query="INSERT INTO music_playlists (playlist_name,date_added,created_by,playlist_url)
    				VALUES('spotify:".$playlist->name ."','".date("m-d-Y")."','Spotify','".$playlist->uri."')";
    				mysqli_query($GS_DBCONN, $insert_query);
    			}
    		}catch(exception $e){}
    	}
    }
    
    if($_GET['code']!=""){getSpotifyMusic();}
    
    
    //Search New Music
    if(temp_decode($_POST['type'])=="SeachMusic" && GetUserPermissions("edit")==true){
    	
    	//delete list to readd
    	mysqli_query($GS_DBCONN, "DELETE FROM music_playlists WHERE created_by='Spotify'");
    	mysqli_query($GS_DBCONN, "DELETE FROM music_data WHERE song_type='stored'");
    	
    	
    	foreach (new DirectoryIterator("music") as $file) {
    	  if ($file->isFile()) {
    		 
    		$ext = pathinfo("music/".$file->getFilename(), PATHINFO_EXTENSION);
    		if($ext != "exe"){
    			//remove spaces and - to prevent file issues
    			$newSongName =str_replace("-","_",$file->getFilename());
    			$newSongName = str_replace(" ","_", $newSongName);
    			$newSongName = str_replace("[","_", $newSongName);
    			$newSongName = str_replace("]","_", $newSongName);
    			$newSongName = str_replace(".","", $newSongName);
    			$newSongName = str_replace($ext,"", $newSongName);
    			$newSongName = trim($newSongName,"");
    			$newSongName= substr($newSongName,0,200);
    			$newSongName.= ".".$ext;
    			
    			
    			rename("music/".$file->getFilename(), ("music/".$newSongName));	              
    			  
    			 //see if file exists in db
    			 $query1 = "SELECT * FROM music_data WHERE song_type='stored' AND (song_location='stored:".$newSongName."')";
    			 $results1 = mysqli_query($GS_DBCONN, $query1);
    			 $exists = mysqli_num_rows($results1);    
    			  
    			  if($exists==0){
    				  $insert_query="INSERT INTO music_data (song_location,song_added,song_type,song_name,playlist)
    				  VALUES('stored:".$newSongName."','".time()."','stored','".$newSongName."','0|')";
    				  mysqli_query($GS_DBCONN, $insert_query);
    			  } //end exists
    		 }//end if mp3
    	  } //end is file
    	}// end foreach
    	getSpotifyMusic();
    
    } //end if POST
    
	//Delete Song
	if($_GET['delete']!="" && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		$query1 = "SELECT * FROM music_data WHERE ID='".temp_decode($_GET['delete'])."'";
		$results1 = mysqli_query($GS_DBCONN, $query1);
		$file = mysqli_fetch_array($results1);
		 
		mysqli_query($GS_DBCONN, "DELETE FROM music_data WHERE ID='".$file['ID']."'");
		unlink("music/".$file['song_location']);
	}    
	
	//Update Server
	if(temp_decode($_POST['type'])=="SqueezeBoxSettingsUpdate" && GetUserPermissions("edit")==true){
		$insert_query="UPDATE music_servers SET ip_address='".clean_text($_POST['server_ip'],25)."',player_id='".clean_text($_POST['player_id'],11)."' WHERE ID='".clean_text($_POST['server_id'],11)."'";
		mysqli_query($GS_DBCONN, $insert_query);
	}
	
	//add Server
	if(temp_decode($_POST['type'])=="SqueezeBox_New" && GetUserPermissions("add")==true){
		$insert_query="INSERT INTO music_servers (ip_address,room_id,player_id,enabled, player_index)
		VALUES('".clean_text($_POST['server_ip'],25)."','".clean_text($_POST['room_id'],11)."',".clean_text($_POST['player_id'],11).",'1','0')";
		mysqli_query($GS_DBCONN, $insert_query);
	}
	
	//Delete Server
	if($_GET['deleteSqueezeBox']!="" && temp_decode($_GET['deleteSqueezeBox'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		$insert_query="DELETE FROM music_servers WHERE ID='".clean_text(temp_decode($_GET['deleteSqueezeBox']),11)."'";
		mysqli_query($GS_DBCONN, $insert_query);
	}	
	
	//Update Playlist
	if(temp_decode($_POST['type'])=="SavePlaylist" && GetUserPermissions("edit")==true){
		$query = "SELECT * FROM music_data WHERE song_type='stored' ORDER BY ID DESC";
		$songs = mysqli_query($GS_DBCONN, $query);
		while($song = mysqli_fetch_assoc($songs)) {
			if(($_POST["song".$song['ID']])=="1"){
				mysqli_query($GS_DBCONN, "UPDATE music_data SET playlist='".str_replace(($_POST["playlist"]."|"),"",$song['playlist']).$_POST["playlist"]."|' WHERE ID='".$song["ID"]."'");
			}else{
				mysqli_query($GS_DBCONN, "UPDATE music_data SET playlist='".str_replace(($_POST["playlist"]."|"),"",$song['playlist'])."' WHERE ID='".$song["ID"]."'");
			}
		}	
	}
	
	//add playlist
	if(temp_decode($_POST['type'])=="addNewPlaylist" && GetUserPermissions("add")==true && trim($_POST['playlistName'])!=""){ 
		mysqli_query($GS_DBCONN, "INSERT INTO music_playlists (playlist_name,date_added,created_by,playlist_url)
		VALUES('".trim($_POST['playlistName'])."', '".date('m-d-Y')."', '".$_SESSION['id']."', '')");
	} 
	
	//add new Remote URL
	if(temp_decode($_POST['type'])=="addNewRemoteURL" && GetUserPermissions("add")==true && trim($_POST['remoteURL'])!=""){ 
		mysqli_query($GS_DBCONN, "INSERT INTO music_data (song_location,song_added,song_type, song_name, playlist)
		VALUES('".trim($_POST['remoteURL'])."', '".date('m-d-Y')."', 'Radio', '".trim($_POST['remoteURLName'])."', '')");
	} 
	
	//Upload a new song
	if(temp_decode($_POST['type'])=="addNewSong" && GetUserPermissions("add")==true ){
		//see if file exists in db
		$song = upload($_FILES["songName"], "music/");
		$query1 = "SELECT * FROM music_data WHERE song_type='stored' AND (song_location='".$song."')";
		$results1 = mysqli_query($GS_DBCONN, $query1);
		$exists = mysqli_num_rows($results1);    
		  
		if($exists==0){			  
			$insert_query="INSERT INTO music_data (song_location,song_added,song_type,song_name,playlist)
			VALUES('".$song."','".time()."','stored','".$song."','0|')";
			mysqli_query($GS_DBCONN, $insert_query);
		} //end exists
		
		
	}	
	
	//delete playlist
	if($_GET['deletePlaylist']!="" && temp_decode($_GET['deletePlaylist'])!="[EXPIRED]" && GetUserPermissions("delete")==true){ 
		mysqli_query($GS_DBCONN, "DELETE FROM music_playlists WHERE ID='".temp_decode($_GET['deletePlaylist'])."'");
	} 
    	
    
    ?>
<div style="margin-bottom:30px;">
    <?php //Check if service is enabled // ?>
    <?php if ($GS_squeezeBoxServiceEnabled == false):?>
		<center>
			<h3>This service is not enabled</h3>
		</center>
    <?php exit; endif;?>
    <h3>
        <div class="col-md-4">Manage Music</div>
        <form method="POST" style="display:inline-block;margin-top:10px;margin-left:15px;" > 
            <input type="hidden" name="type" value="<?php echo temp_encode("SeachMusic");?>"/>
            <?php if(GetUserPermissions("add")==true):?>
				<a href="#" data-target="#SqueezeBoxModal_addNew" data-toggle="modal"><button type="button" class="btn btn-default"><i class="fa fa-plus"></i> Add Server</button></a>
				<button type="submit" onclick="$('i',this).addClass('fa-spin');" class="btn btn default" style="color:#fff;background-color:#9358AC"><i class="fa fa-refresh"></i> Scan For Music</button>
            <?php else :?>
				<button disabled type="button" class="btn btn-default" title="You do not have permission"><i class="fa fa-plus"></i> Add Server</button>
				<button disabled type="button" class="btn btn default" style="color:#fff;background-color:#9358AC"><i class="fa fa-refresh"></i> Scan For Music</button>
            <?php endif;?>
        </form>
    </h3>
    <div class="col-md-4 email-list1">
        <div class="collection" style="margin-left:5%;">
            <form method="POST" class="autoform" style="display:inline-block;">
                <input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
                <input type="hidden" name="Command" value="Sync" />
                <input type="hidden" name="sync_id" value="-" />
                <input type="hidden" class="room_id" name="room_id" value="" />
                <button type="submit" class="btn btn-default">End Sync</button>
            </form>
        </div>
        <ul class="collection" id="room_list" style="background-color:#f2f4f8;">
            <?php $count=0;
                $query = "SELECT * FROM music_servers ORDER BY player_index ASC";
                $musicServers = mysqli_query($GS_DBCONN, $query);
                while($server = mysqli_fetch_assoc($musicServers)) { $count++;      
                	
                	//get room Info
                	$query = "SELECT * FROM home_rooms WHERE ID='".$server['room_id']."'";
                	$homeRooms= mysqli_query($GS_DBCONN, $query);
                	$room = mysqli_fetch_assoc($homeRooms); 
                	$room_count = mysqli_num_rows($homeRooms);    
            ?>
				<li style="overflow-x:auto;overflow-y:hidden;margin-top:5px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);background-color:#fff;margin-bottom:10px;padding:15px;cursor:pointer;" 
					class="select_room_btn" id="select_room_btn<?php echo $room['ID'];?>" <?php if($server['enabled']=="1"):?> onclick="select_room('<?php echo $room['ID'];?>','<?php echo $server['player_id'];?>');" <?php endif;?>>
					<!-- Power On -->
					<form method="POST" class="avatar_left autoform" id="room<?php echo $room["ID"];?>PowerOn" style="display:inline-block;margin-top:-10px;">
						<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
						<input type="hidden" name="room_id" value="<?php echo $room["ID"];?>" />
						<input type="hidden" name="Command" value="Power-on" />
						<button type="submit" style="border:none;background-color:#fff;">
							<i class="fa fa-power-off icon_4 icon_6" style="background-color:grey;"></i>
						</button>
					</form>
					<!-- Power Off -->
					<form method="POST" class="avatar_left autoform" id="room<?php echo $room["ID"];?>PowerOff" style="display:none;margin-top:-10px;">
						<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
						<input type="hidden" name="room_id" value="<?php echo $room["ID"];?>" />
						<input type="hidden" name="Command" value="Power-off" />
						<button type="submit" style="border:none;background-color:#fff;">
							<i class="icon_4 icon_6"><?php echo substr(ucfirst($room['room_name']),0,1);?></i>
						</button>							
					</form>
					<div class="avatar_left" style="width:70%;">
						<span class="email-title">
						<?php echo ucfirst($room['room_name']);?>
						</span>
						<div class="blue-text ultra-small" style="float:left;width:100%;" >
							<?php if($server['enabled']=="1"):?>
								<span id="show_playing<?php echo $room['ID'];?>"></span>
							<?php elseif($server['enabled']=="3"):?>
								<span style='color:red;'>Disabled By System Monitor</span>
							<?php endif;?>
						</div>
					</div>
					<div class="secondary-content" style="float:right;width:100%;padding:2px;border-radius:2px;">
						<form method="POST" id="FrmSyncPlayers<?php echo $room['ID'];?>" class="autoform_na" style="display:inline-block;">
							<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
							<input type="hidden" name="room_id" value="<?php echo $room["ID"];?>" />
							<input type="hidden" name="Command" value="Sync" />						
							<label style="display:inline-block;margin-top:5px;"><b>Sync To Player:</b></label>
							<select id="SyncGroups<?php echo $room['ID'];?>"  name ="sync_id" class="secondary-content form-control1" style="border:none;border-bottom:1px solid #888;width:120px;height:30px;" onchange="$('#FrmSyncPlayers<?php echo $room['ID'];?>').submit();">
								<option value="">Not Set</option>
								<option value="-">Don't Sync</option>
								<?php
									$query1 = "SELECT * FROM music_servers ORDER BY player_index ASC";
									$results1_ = mysqli_query($GS_DBCONN, $query1);
									while($server1 = mysqli_fetch_assoc($results1_)) {      
										
										//get Squeezebox Info
										$query1 = "SELECT * FROM home_rooms WHERE ID='".$server1['room_id']."'";
										$results1_a_ = mysqli_query($GS_DBCONN, $query1);
										$SyncRoom = mysqli_fetch_assoc($results1_a_); 
								?>
									<option value="<?php echo $server1['player_id'];?>"><?php echo $SyncRoom['room_name'];?></option>
								<?php  } ?>
							</select>
						</form>
						<?php if(GetUserPermissions("edit")==true):?>
							<a href="#" data-toggle="modal" data-target="#SqueezeBoxModal_settings_<?php echo $server['ID'];?>" class="secondary-content" style="float:right;">
								<button onclick="$('#squeezeboxPlayerList<?php echo $room['ID'];?>').load('Autoload/Autoload_GetSqueezeboxPlayerList.php');" type="button" class="btn btn-default" title="Settings"><i class="fa fa-gear"></i></button>
							</a>
						<?php else :?>
							<a href="#" class="secondary-content" style="float:right;">
								<button type="button" disabled class="btn btn-default" title="Settings"><i class="fa fa-gear"></i></button>
							</a>
						<?php endif;?>
					</div>
				</li>
				<!-- Volume -->
				<form method="POST" id="volume_form<?php echo $room['ID'];?>" class="autoform">
					<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>                                      
					<input type="hidden" name="Command" value="Volume" />
					<input type="hidden" name="room_id" value="<?php echo $room['ID'];?>" />
					<input type="hidden" value="0" name="volume" id="volume_ctrl<?php echo $room['ID'];?>"/>
					<div class="MusicVolumeSlider<?php echo $room['ID'];?>" style="margin-top:-22px;border-radius:0px;"></div>
					<script>
						/* Brightness Slider Initates slider and sets values */ 
						$(".MusicVolumeSlider<?php echo $room['ID'];?>").slider({
							orientation: "horizontal",
							range: false,
							min: 0,
							max: 100,
							value: 0,
							step: 1,
							animate: false,
							slide: function(event, ui){
								$('#volume_ctrl<?php echo $room['ID'];?>').val(ui.value);
							},
							stop: function (event, ui) {
								$('#volume_form<?php echo $room['ID'];?>').submit();
								$('#volume_ctrl<?php echo $room['ID'];?>').val("0");
							}
						});
					</script>
				</form>
				<!-- Modal Play Settings -->
				<div class="modal fade" id="SqueezeBoxModal_settings_<?php echo $server['ID'];?>" role="dialog" style="z-index:9999;margin-top:100px;">
					<div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content" style="width:400px;">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 style="" class="modal-title">SqueezeBox - <?php echo ucfirst($room['room_name']);?></h4>
							</div>
							<div class="modal-body" style="height:300px;overflow:auto;">
								<form method="post">
									<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBoxSettingsUpdate");?>"/>
									<input type="hidden" name="server_id" value="<?php echo $server['ID'];?>"/>
									<label for="" style="display:block;"><b>Server Address:</b></label>
									<input type="text" name="server_ip" value="<?php echo $server['ip_address'];?>" style="width:100%;margin-bottom:10px;"/>	
									<label for="" style="display:block;"><b>Player Index:</b></label>
									<select id="squeezeboxPlayerList<?php echo $room['ID'];?>" name="player_id" style="width:100%;margin-bottom:10px;">
									</select>
									<script>$("#squeezeboxPlayerList<?php echo $room['ID'];?>").val("<?php echo $server['player_index'];?>");</script>								
									<label for="" style="display:block;"><b>Player Name:</b> <span id="playerName<?php echo $room['ID'];?>"></span></label>
									<label for="" style="display:block;"><b>Player ID:</b> <span id="playerAddress<?php echo $room['ID'];?>"></span></label>
									<label for="" style="display:block;"><b>Enabled:</b>
									<?php if ($server['enabled']=="1"):?>
										Yes
									<?php elseif ($server['enabled']=="3"):?>
										<span style="color:red;">Disabled By System Monitor</span>
									<?php else : ?>
										No
									<?php endif;?>
									</label>
									<button type="submit" style="width:58%;margin-top:20px;" class="btn btn-default">Save</button>
									<?php if(GetUserPermissions("delete")==true):?>
										<a href="?deleteSqueezeBox=<?php echo temp_encode($server['ID']);?>">
											<button type="button" style="width:40%;margin-top:20px;" class="btn btn-danger">Delete <i class="fa fa-trash-o"></i></button>
										</a>
									<?php else :?>
										<button disabled type="button" style="width:40%;margin-top:20px;" class="btn btn-danger">Delete <i class="fa fa-trash-o"></i></button>
									<?php endif;?>
								</form>
							</div>
						</div>
					</div>
				</div>
            <?php } ?>
        </ul>
    </div>
    <div class="col-md-8 inbox_right">
        <form method="GET">
            <div class="input-group">
                <input type="hidden" name="type" value="<?php echo temp_encode("search");?>"/>
                <input type="text" name="search" class="form-control1 input-search" placeholder="Search..." value="<?php echo $_GET['search'];?>">
                <span class="input-group-btn">
                <button class="btn btn-success" type="submit" style="max-height:40px;"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        <div class="mailbox-content" style="padding:10px;height:115px;overflow-x:hidden;overflow-y:auto;margin-top:5px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin-bottom:20px;">
            <h4 style="border-bottom:1px solid #f2f2f2;text-align:center;">
                <?php if(GetUserPermissions("add")==true) :?>
					<a data-toggle="modal" data-target="#AddRemoteURLModal" style="cursor:pointer;float:left;" title="Add New URL">
						<i class="fa fa-plus" style="font-size:20px;color:<?php echo $GS_Config['themeColorMain'];?>"></i>
					</a>
                <?php endif;?>
                Remote Music URL's
            </h4>
            <?php $count=0;
                $query = "SELECT * FROM `music_data` WHERE song_type='radio' ";
                $results = mysqli_query($GS_DBCONN, $query);
                while($result = mysqli_fetch_assoc($results)) { 
			?>
            <div style="display:inline-block;padding:10px;width:20%;min-width:180px;height:60px;overflow-x:auto;overflow-y:hidden;margin-top:5px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin-right:10px;">
                <?php echo ucfirst($result['song_name']);?>
                <form method="POST" class="autoform">
                    <input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
                    <input type="hidden" name="Command" value="Play" />
                    <input type="hidden" name="play_song" value="<?php echo $result['song_location'];?>" />
                    <input type="hidden" class="room_id" name="room_id" value="" />
                    <button title="Play Song" type="submit" style="border-radius:2px;border:none;text-align:center;color:#fff;background-color:<?php echo $GS_Config['themeColorMain'];?>;width:70%;height:25px;">
						Play <i class="fa fa-play-circle"></i>
                    </button>
                    <?php if(GetUserPermissions("delete")==true):?>
						<a href="?delete=<?php echo temp_encode($result['ID']);?>" onclick="return confirm('Are your sure you want to delte the remote URL: <?php echo $result['song_name'];?>?');">
							<button title="Delete Song" type="button" class="btn btn-danger" style="border-radius:2px;border:none;width:27%;height:25px;padding:0px;">
								<i class="fa fa-trash-o"></i>
							</button>
						</a>
                    <?php else:?>
						<button disabled title="Delete Song" type="button" class="btn btn-danger" style="border-radius:2px;border:none;width:27%;height:25px;padding:0px;">
							<i class="fa fa-trash-o"></i>
						</button>
                    <?php endif;?>
                </form>
            </div>
            <?php }?>
        </div>
        <div class="mailbox-content" style="overflow-x:auto;overflow-y:hidden;margin-top:5px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin-bottom:20px;">
            <div style="margin-bottom:10px;">
                <button class="btn btn-primary" onclick="showPlaylists();" id="Btnshow_playlists" type="button"><i class="fa fa-list-ol"></i>&nbsp;Playlists</button>
                <button class="btn btn-default" onclick="showMusic();" id="Btnshow_library" type="button"><i class="fa fa-music"></i>&nbsp;Music Library</button>
            </div>
            <script>
                function select_room(roomid,player_id) {						
                	$('.room_audio').hide();
                	$('.select_room_btn').css("border", "none");
                	$('#select_room_btn' + roomid).css("border-left", "4px solid grey");
                	$('.room_id').val(roomid);
                	$('#room_audio_' + roomid).show();					
                	$('#btnSyncPlayers').prop("disabled",false);
                	$('#btnSyncPlayers').prop("title","Select The Server Before Syncing");
                }
                
                function showMusic(){
                	$("#Playlists").hide();
                	$("#Music").fadeIn(500);
                	$("#Btnshow_library").removeClass("btn-default").addClass("btn-primary");
                	$("#Btnshow_playlists").removeClass("btn-primary").addClass("btn-default");
                }
                function showPlaylists(){
                	$("#Music").hide();
                	$("#Playlists").fadeIn(500);
                	$("#Btnshow_playlists").removeClass("btn-default").addClass("btn-primary");
                	$("#Btnshow_library").removeClass("btn-primary").addClass("btn-default");
                }
            </script>
            <?php $count=0;
                $query = "SELECT * FROM `home_rooms`";
                $results = mysqli_query($GS_DBCONN, $query);
                while($result = mysqli_fetch_assoc($results)) { $count++;     	                           
            ?>
				<div class="room_audio" id="room_audio_<?php echo $result['ID'];?>" style="margin-bottom:10px;display:none;">
					<h3 style="text-align:center;margin-top:0px;height:10px;"><?php echo ucwords($result['room_name']);?></h3>
					<hr/>
					<!--Back -->
					<form method="POST" class="autoform" style="display:none;margin-right:10px;" id="room<?php echo $result["ID"];?>Back">
						<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
						<input type="hidden" name="room_id" value="<?php echo $result['ID'];?>" />
						<input type="hidden" name="Command" value="Back" />
						<button type="submit" class="btn btn-primary"><i class="fa fa-step-backward"></i> Back</button>
					</form>
					<!-- Play -->
					<form method="POST" class="autoform" style="display:none;margin-right:10px;" id="room<?php echo $result["ID"];?>Play">
						<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
						<input type="hidden" name="room_id" value="<?php echo $result['ID'];?>" />
						<input type="hidden" name="Command" value="Resume" />
						<button type="submit" class="btn btn-primary"><i class="fa fa-play"></i> Play</button>
					</form>
					<!-- Pause -->
					<form method="POST" class="autoform" style="display:none;margin-right:10px;" id="room<?php echo $result["ID"];?>Pause">
						<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
						<input type="hidden" name="room_id" value="<?php echo $result['ID'];?>" />
						<input type="hidden" name="Command" value="Pause" />
						<button type="submit" class="btn btn-primary"><i class="fa fa-pause"></i> Pause</button>
					</form>
					<!--Next -->
					<form method="POST" class="autoform" style="display:none;margin-right:10px;" id="room<?php echo $result["ID"];?>Next">
						<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
						<input type="hidden" name="room_id" value="<?php echo $result['ID'];?>" />
						<input type="hidden" name="Command" value="Next" />
						<button type="submit" class="btn btn-primary">Next <i class="fa fa-step-forward"></i></button>
					</form>
					<!-- Mute -->
					<form method="POST" class="autoform" style="display:none;margin-right:20px;" id="room<?php echo $result["ID"];?>Mute">
						<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
						<input type="hidden" name="room_id" value="<?php echo $result['ID'];?>" />
						<input type="hidden" name="Command" value="Mute" />
						<button type="submit" class="btn btn-primary">Mute</button>
					</form>
					<!-- Unmute -->
					<form method="POST" class="autoform" style="display:none;margin-right:20px;" id="room<?php echo $result["ID"];?>Unmute">
						<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
						<input type="hidden" name="room_id" value="<?php echo $result['ID'];?>" />
						<input type="hidden" name="Command" value="Unmute" />
						<button type="submit" class="btn btn-primary">Unmute</button>
					</form>
				</div>
            <?php } ?>
            <div id="Playlists" style="font-size:18px;overflow:auto;width:100%;">
                <style>
                    table { table-layout: fixed; }
                    table td { overflow: hidden;}
                </style>
                <table class="table" style="border:2px solid #f2f2f2;min-width:600px;">
                    <col width="50">
                    <thead>
                        <tr>
                            <th>
                                <?php if(GetUserPermissions("add")==true) :?>
									<a data-toggle="modal" data-target="#AddPlaylistModal" style="cursor:pointer;" title="Add New Playlist">
										<i class="fa fa-plus" style="font-size:20px;color:<?php echo $GS_Config['themeColorMain'];?>"></i>
									</a>
                                <?php endif;?>
                            </th>
                            <th>Playlist Name</th>
                            <th>Date Added</th>
                            <th>Created By</th>
                            <th>Controls</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- load default playlist -->
                        <tr style="margin-bottom:5px;" class="active">
                            <td> <i class="fa fa-list-ol icon-state-warning"></i> </td>
                            <td title="Play All Songs">Play All Songs</td>
                            <td></td>
                            <td>System</td>
                            <td>
                                <form method="POST" class="autoform" style="float:left;margin-right:3px;">
                                    <input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
                                    <input type="hidden" name="Command" value="PlayPlaylist"/>
                                    <input type="hidden" name="playlist" value="0"/>
                                    <input type="hidden" class="room_id" name="room_id" value=""/>
                                    <button title="Play Playlist" type="submit" class="btn btn-primary"><i class="fa fa-play-circle"></i></button>											
                                </form>
                            </td>
                        </tr>
                        <!-- get Playlists from DB -->
                        <?php
                            $query = "SELECT * FROM music_playlists ORDER BY ID DESC";
                            $results = mysqli_query($GS_DBCONN, $query);
                            while($result = mysqli_fetch_assoc($results)) { $count++;
						?>
							<tr style="margin-bottom:5px;" class="active">
								<td> <i class="fa fa-list-ol icon-state-warning"></i> </td>
								<td title="<?php echo ucfirst($result['playlist_name']);?>"><?php echo ucfirst(substr($result['playlist_name'],0,50));?></td>
								<td><?php echo $result['date_added'];?></td>
								<td>
									<?php //get users name from user id
										$query1 = "SELECT * FROM users WHERE ID='".$result['created_by']."'";
										$results1_a = mysqli_query($GS_DBCONN, $query1);
										$createdBy = mysqli_fetch_assoc($results1_a); 
										echo ucwords($createdBy["user_name"]);
									?>
								</td>
								<td>
									<form method="POST" class="autoform" style="float:left;margin-right:3px;">
										<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
										<input type="hidden" name="Command" value="PlayPlaylist"/>
										<?php if ($result['playlist_url']=="") :?>
											<input type="hidden" name="playlist" value="<?php echo $result['ID'];?>"/>
										<?php else :?>
											<input type="hidden" name="playlist" value="<?php echo $result['playlist_url'];?>"/>
										<?php endif;?>
										<input type="hidden" class="room_id" name="room_id" value=""/>
										<button title="Play Playlist" type="submit" class="btn btn-primary"><i class="fa fa-play-circle"></i></button>
										<?php if(GetUserPermissions("edit")==true) :?>
											<button data-target="#editPlaylist<?php echo $result['ID'];?>" data-toggle="modal" title="Edit Playlist" type="button" class="btn btn-primary"><i class="fa fa-pencil"></i></button>
										<?php else:?>
											<button disabled title="Edit Playlist" type="button" class="btn btn-primary"><i class="fa fa-pencil"></i></button>
										<?php endif;?>
										<?php if(GetUserPermissions("delete")==true) :?>
											<a href="?deletePlaylist=<?php echo temp_encode($result['ID']);?>" >
												<button style="margin-bottom:2px;" type="button" class="btn btn-danger" onclick="return confirm('Are You Sure You Want To Delete The Playlist: <?php echo $result['playlist_name'];?>?');">
													<i class="fa fa-trash-o" title="Delete Song"></i>
												</button>
											</a>
										<?php else:?>
											<button type="button" class="btn btn-danger" disabled title="You do not have permission">
												<i class="fa fa-trash-o"></i>
											</button>
										<?php endif;?>
									</form>
									<!-- Modal -->
									<div class="modal fade" id="editPlaylist<?php echo $result['ID'];?>" role="dialog" style="z-index:9999;margin-top:100px;">
										<div class="modal-dialog">
											<!-- Modal content-->
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 style="" class="modal-title">Playlist: <?php echo $result["playlist_name"];?></h4>
												</div>
												<div class="modal-body" style="overflow:auto;">
													<form method="post">
														<input type="hidden" name="type" value="<?php echo temp_encode("SavePlaylist");?>"/>
														<input type="hidden" name="playlist" value="<?php echo $result["ID"];?>"/>
														<div style="overflow:auto;max-height:400px;">
															<table>
																<?php
																	$query = "SELECT * FROM music_data WHERE song_type='stored' ORDER BY ID DESC";
																	$songs = mysqli_query($GS_DBCONN, $query);
																	while($song = mysqli_fetch_assoc($songs)) { $count++;
																?>
																	<tr>
																		<td>
																			<?php if(strpos($song["playlist"],$result["ID"]."|")!==false):?>
																				<input type="checkbox" name="song<?php echo $song['ID'];?>" value="1" checked />
																			<?php else :?>
																				<input type="checkbox" name="song<?php echo $song['ID'];?>" value="1"/>
																			<?php endif;?>
																		</td>
																		<td><?php echo $song["song_location"];?></td>
																	</tr>
																<?php }?>
															</table>
														</div>
														<div style="float:right;margin-top:5px;">
															<?php if(GetUserPermissions("edit")==true):?>
																<button type="submit" class="btn btn-primary">Save</button>
															<?php else :?>
																<button type="button" disabled class="btn btn-primary">Save</button>
															<?php endif;?>
															<button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
														</div>
													</form>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
                        <?php } if ($count==0){echo "<tr><td colspan='5' style='text-align:center;'>You Currently Have No Playlists</td></tr>";} ?>
                    </tbody>
                </table>
            </div>
            <div id="Music" style="display:none;font-size:18px;overflow:auto;width:100%;">
                <?php
                    // Find out how many items are in the table
                    $total = mysqli_num_rows(mysqli_query($GS_DBCONN, 'SELECT * FROM  music_data WHERE song_type="stored" ORDER BY ID ASC'));
                    // How many items to list per page
                    $limit = 20;
                    // How many pages will there be
                    $pages = ceil($total / $limit);
                    // What page are we currently on?
                    $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array('options' => array('default'   => 1,'min_range' => 1))));
                    // Calculate the offset for the query
                    $offset = ($page - 1)  * $limit;
                    // Some information to display to the user
                    $start = $offset + 1;
                    $end = min(($offset + $limit), $total);
                    // The "back" link
                    $prevlink = ($page > 1) ? "
                    <a style='font-size:20px;color:#333;' href='?page=1' title='Next page'><i class='fa fa-step-backward'></i></a>&nbsp;
                    <a style='font-size:25px;color:#333;' href='?page=".($page - 1)."' title='Last page'><i class='fa fa-caret-left'></i></a>"
                    :"";
                    // The "forward" link
                    $nextlink = ($page < $pages) ? "
                    <a style='font-size:25px;color:#333;' href='?page=".($page + 1)."' title='Next page'><i class='fa fa-caret-right'></i></a>&nbsp;
                    <a style='font-size:20px;color:#333;' href='?page=".$pages."' title='Last page'><i class='fa fa-step-forward'></i></a>"
                    :"";
                    // Display the paging information
                    echo "<center><div style='width:200px;font-size:18px;'><b>".$prevlink." Page ".$page." of ".$pages." ".$nextlink."</b></div></center>";
                    
                    $count = 0;
                    if(temp_decode($_GET['type'])!="search"){
                      $query = "SELECT * FROM music_data WHERE song_type='stored' ORDER BY Song_location DESC LIMIT ".$limit." OFFSET ".$offset;
                    }else{
                    	$query = "SELECT * FROM music_data WHERE (song_name LIKE '%".str_replace(" ","_",$_GET['search'])."%') AND song_type='stored' ";
                    	$query.=" ORDER BY Song_location DESC LIMIT ".$limit." OFFSET ".$offset;
                    }
                ?>
                <style>
                    table { table-layout: fixed; }
                    table td { overflow: hidden;}
                </style>
                <table class="table" style="border:2px solid #f2f2f2;min-width:500px;">
                    <col width="50">
                    <col width="250">
                    <col width="100">
                    <col width="150">
                    <thead>
                        <tr>
                            <th>
                                <?php if(GetUserPermissions("add")==true) :?>
									<a data-toggle="modal" data-target="#AddNewSong" style="cursor:pointer;float:left;" title="Add New URL">
										<i class="fa fa-plus" style="font-size:20px;color:<?php echo $GS_Config['themeColorMain'];?>"></i>
									</a>
                                <?php endif;?>
                            </th>
                            <th>Song Name</th>
                            <th>Date Added</th>
                            <th>Controls</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $results = mysqli_query($GS_DBCONN, $query);
                            while($result = mysqli_fetch_assoc($results)) { $count++;
						?>
							<tr class="active">
								<td> <i class="fa fa-music icon-state-warning"></i> </td>
								<td title="<?php echo ucfirst($result['song_name']);?>"><?php echo ucfirst(substr($result['song_name'],0,50));?></td>
								<td> <?php echo gmdate("m/d/y",$result['song_added']);?></td>
								<td>
									<form method="POST" class="autoform" style="display:inline-block;margin-right:3px;">
										<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
										<input type="hidden" name="Command" value="Add"/>
										<input type="hidden" name="play_song" value="<?php echo $result['song_location'];?>"/>
										<input type="hidden" class="room_id" name="room_id" value=""/>
										<button onclick="$('i',this).removeClass('fa-plus').addClass('fa-check');"
											title="Queue Song" type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
									</form>
									<form method="POST" class="autoform" style="display:inline-block;margin-right:3px;">
										<input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox");?>"/>
										<input type="hidden" name="Command" value="Play"/>
										<input type="hidden" name="play_song" value="<?php echo $result['song_location'];?>"/>
										<input type="hidden" class="room_id" name="room_id" value=""/>
										<button title="Play Song" type="submit" class="btn btn-primary"><i class="fa fa-play-circle"></i></button>
										<?php if(GetUserPermissions("delete")==true) :?>
											<a href="?delete=<?php echo temp_encode($result['ID']);?>" >
												<button type="button" class="btn btn-danger" onclick="return confirm('Are You Sure You Want To Delete The Song: <?php echo $result['song_location'];?>?');">
													<i class="fa fa-trash-o" title="Delete Song"></i>
												</button>
											</a>
										<?php else:?>
											<button type="button" class="btn btn-danger" disabled title="You do not have permission">
												<i class="fa fa-trash-o"></i>
											</button>
										<?php endif;?>
									</form>
								</td>
							</tr>
                        <?php } if ($count==0){echo "<tr><td colspan='4' style='text-align:center;'>FTP Into The SmartHome Server To Add/Manage Your Music Library</td></tr>";} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Play Settings -->
    <div class="modal fade" id="SqueezeBoxModal_addNew" role="dialog" style="z-index:9999;margin-top:100px;">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" style="width:400px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 style="" class="modal-title">SqueezeBox - Add New</h4>
                </div>
                <div class="modal-body" style="height:300px;">
                    <form method="post">
                        <input type="hidden" name="type" value="<?php echo temp_encode("SqueezeBox_New");?>"/>
                        <label for="server_ip" style="display:block;"><b>Server Address:</b></label>
                        <input type="text" name="server_ip" value="" style="width:100%;margin-bottom:10px;" class="form-control1"/>	
                        <label for="player_id" style="display:block;"><b>Player ID:</b></label>
                        <input type="number" min="0" name="player_id" value="" style="width:100%;margin-bottom:10px;" class="form-control1"/>	
                        <label for="room_id" style="display:block;"><b>Room:</b></label>
                        <select name="room_id" style="width:100%;margin-bottom:10px;" class="form-control1">
                            <?php
                                $query = "SELECT * FROM home_rooms ORDER BY room_name ASC";
                                $results = mysqli_query($GS_DBCONN, $query);
                                while($room = mysqli_fetch_assoc($results)) { 
                                	//see if server already exists for room
                                	$query = "SELECT * FROM music_servers WHERE room_id='".$room['ID']."'";
                                	$results1 = mysqli_query($GS_DBCONN, $query);
                                	$server_exists = mysqli_num_rows($results1);
                                	if($server_exists>0){continue;}
                            ?>
								<option value="<?php echo $room['ID'];?>"><?php echo ucwords($room['room_name']);?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" style="width:100%;margin-top:20px;" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-------- Add Playlist Modal ------>
    <!-- Modal -->
    <div class="modal fade" id="AddPlaylistModal" role="dialog" style="z-index:9999;margin-top:100px;">
        <div class="modal-dialog" style="width:500px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 style="" class="modal-title">Add Playlist</h4>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="type" value="<?php echo temp_encode("addNewPlaylist");?>"/>
                        <label><b>Playlist Name:</b></label>
                        <input type="text" name="playlistName" value="" class="form-control1"/>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-------- Add Remote URL Modal ------>
    <!-- Modal -->
    <div class="modal fade" id="AddRemoteURLModal" role="dialog" style="z-index:9999;margin-top:100px;">
        <div class="modal-dialog" style="width:500px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 style="" class="modal-title">Add Remote URL</h4>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="type" value="<?php echo temp_encode("addNewRemoteURL");?>"/>
                        <label><b>URL Name:</b></label>
                        <input type="text" name="remoteURLName" value="" class="form-control1" style="margin-bottom:10px;"/>
                        <label><b>URL Location:</b></label>
                        <input type="text" name="remoteURL" value="" class="form-control1"/>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-------- Add New Song ------>
    <!-- Modal -->
    <div class="modal fade" id="AddNewSong" role="dialog" style="z-index:9999;margin-top:100px;">
        <div class="modal-dialog" style="width:500px;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 style="" class="modal-title">Add New Song</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="<?php echo temp_encode("addNewSong");?>"/>
                        <label><b>Upload a New Song:</b></label>
                        <input type="file" name="songName" />
                        <div class="modal-footer" style="margin-top:10px;">
                            <button type="submit" class="btn btn-primary">Add</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
<script>
    /* select first room */
      $('#room_list li:first').click();
</script>
</body>
</html>