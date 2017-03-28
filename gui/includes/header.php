<!DOCTYPE HTML >
<html style="background-image:none;background-color:#f5f5f5;">
    <title><?php echo $GS_Config['SiteName']." - ".ucwords($page_title);?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <link rel="icon" href="images/favicons/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" sizes="57x57" href="images/favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="images/favicons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="images/favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="images/favicons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="images/favicons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="images/favicons/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicons/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="images/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="images/favicons/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="images/favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="images/favicons/favicon-16x16.png" sizes="16x16">
    <link rel="mask-icon" href="images/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="images/favicons/mstile-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.3.3.7.min.css" rel='stylesheet' type='text/css' />
    <!-- Custom CSS -->
    <link href="css/style.less" rel='stylesheet/less' type='text/css' />
    <!-- FontAwesome -->
    <link href="css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="css/font-awesome-animation.min.css" type='text/css'>
    <!-- Nav CSS -->
    <link href="css/custom.css" rel="stylesheet" type='text/css'>
    <!--For Jquery-->
    <script src="js/jquery-1.11.3.js"></script>
    <script src="js/jquery1.12.1UI.js"></script>
    <script src="js/touch-punch.min.js"></script>
    <link rel="stylesheet" href="css/jquery1.12.1UI.css" type='text/css'>
    <script src="js/less.js"></script>
    <script src="js/bootstrap.3.3.7.min.js"></script>
    
    
    <!---- JS Toggles -->
    <link href="css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="js/bootstrap-toggle.min.js"></script>

    <style>
        @media only screen and (max-device-width : 1024px)  {
	        button.btn{padding:10px;font-size:16px;height:auto;}
	        .stats h5{font-size:22px;}
	        .stats span{font-size:16px;}
	        .small-font{font-size:16px;}
	        #side-menu li a{font-size:18px;}
        }
    </style>
    <script>
        function ScreenSaver(show,interval){
			
        	if (show==true){
				if (/iPad/i.test(navigator.userAgent)) { /* Reset timer if clicked/touched */
					$('#LoadingFadeInEffect').animate({"opacity":"0"},interval);
					$('#screenSaver').fadeIn(interval);
					$("#screenSaver h1").show();
					$("#screenSaver").css({"background-color":"rgba(0,0,0,0.9)"});
					$("#SlideShowContainer").css({"width":"90%"});
					$("#SlideShowContainer").css({"padding":"60px"});
					$("#SlideShowContainer").css({"height":"320px"});
					$("#SlideShowContainer .sh-logo").css({"font-size":"200px"});
					$("#SlideShowContainer .sh-logo").css({"width":"200px"});
					$("#SlideShowContainer .sh-logo").css({"height":"200px"});
				}else{ /* Not on ipad */
					/* show login dialog */
					location.href="logout.php?CookieDeleteBypass=1";
				}
        	}else{
				$('#screenSaver').fadeOut(interval);
				$('#LoadingFadeInEffect').animate({"opacity":"1"},interval);
        	}
        }
        
        $(document).ready(function(){ 
        	setTimeout(function(){
        		ScreenSaver(false,500);
        	}, 300);
		
			
			/* screen saver auto-on	*/
			var sreenSaverTimeout = 0;
			$(document).click(function(){
				sreenSaverTimeout=0;
				ScreenSaver(false,500);
			});
			
			$(document).scroll(function(){ /* Reset timer if scrolled */
				sreenSaverTimeout=0;
				ScreenSaver(false,500);
			});
			
			document.addEventListener('touchstart', function(e) {
				sreenSaverTimeout=0;
				ScreenSaver(false,500);
			}, false);
			
			
		
			/* Show screeSaver after x seconds*/
			setInterval(function(){
				sreenSaverTimeout++;
				if(sreenSaverTimeout==(60*10)){
					ScreenSaver(true,1000);
				}
			},1000);
			
			setInterval(function(){
				$("#screenSaver .sh-logo").addClass("fa").addClass("faa-tada").addClass("animated");
				setTimeout(function(){
					$("#screenSaver .sh-logo").removeClass("animated");
				},2000);
			},10000);
	
		});
		
		$(document).ready(function(){
			$(".btn").click(function(){
				$(this).animate({"opacity":"0.3"},200).delay(1000).animate({"opacity":"1"});				
			});
		});
		

    </script>
    
    <script>
        /* Hide the logout,account seetings dialog when user clicks outside of it. */
         $(document).on('click','#page-wrapper *',function(){
        	 if($("#dropdown-menu-user").css("display")!="none"){
        		 $("#dropdown-menu-user").hide();
        	 }
		 });
    </script>
    </head>
    <body style="background-color:#f5f5f5;">
        <div id="screenSaver" style="background-color:#f5f5f5;text-align:center;position:fixed;width:100%;height:100%;z-index:99999;margin:0 auto;left:0px;right:0px;top:0px;bottom:0px;">
	        <div id="SlideShowContainer" style="background-color:#fff;position: absolute; margin: auto; top: 0; left: 0; right: 0;  bottom: 0;border-radius:6px;padding:40px;height:180px;width:180px;">
            	<div class="sh-logo" style="width:100px;height:100px;display:inline-block;margin-right:40px;font-size:100px;">
                    <i class="fa fa-home" aria-hidden="true"></i>
                </div>
            	<h1 id="date" style='display:none;font-family:"Trebuchet MS", Helvetica, sans-serif;font-size:120px;float:right;margin-top:15px;background-color:#F2F2F2;padding:20px;border-radius:6px;box-shadow:2px 3px 2px #E5E5E5; '>Loading
	            	<script src="js/date.format.js"></script>
	            	<script>
			            setInterval(function(){
							var hours = dateFormat(new Date(), "h");
							var minuites = dateFormat(new Date(), "MM TT");
							$('#screenSaver #date').html(hours+ "<span class='fa faa-flash animated' style='font-size:150px'>:</span>" + minuites);
						},1000);	
		            </script>
	            </h1>
	        </div>
        </div>
        <!----------------- END SCREENSAVER ------------------------->
        <div style="opacity:0;padding:0px;margin:0px;" id="LoadingFadeInEffect">
        <div id="wrapper" style="background:#f5f5f5;">
            <!-- Navigation -->
            <nav class="top1 navbar navbar-default navbar-static-top" role="navigation" id="TopNav" style="top:0px;margin-bottom: 0;position:fixed;width:100%;z-index:9999;">
                <div class="navbar-header" style="z-index:99999;width:65%;display:inline-block;">
                    <ul class="nav navbar-nav navbar-left" style="float:left;margin-left:5px;margin-top:0px;">
                        <li class="dropdown" style="">
                            <a onclick="$('#dropdown-menu-user').toggle();" style="cursor:pointer;">
								<img src="images/users/<?php echo $_SESSION['user_img'];?>" style="border:2px solid <?php echo $GS_Config['themeColorSub'];?>;box-shadow:3px 3px 3px <?php echo $GS_Config['themeColorSub'];?>;margin-top:0px;width:45px;height:45px;border-radius:60px;background-color:#fff;"/>								
                            </a>
                            <ul class="dropdown-menu" id="dropdown-menu-user" style="margin-left:0px;">
                                <li class="dropdown-menu-header text-center">
                                    <strong><?php echo ucfirst($_SESSION['name']);?></strong>
                                </li>
                                <li class="m_2">
                                    <a href="#" data-toggle="modal" data-target="#userAccountSettingsModal" id="userAccountSettingsBtn" onclick="$('#dropdown-menu-user').hide();">
										<i class="fa fa-shield"></i>
										Account Settings
                                    </a>
                                </li>
                                <li class="m_2">
                                    <a href="events_log.php?type=<?php echo temp_encode("search");?>&searchText=Warnings">
										<i class="fa fa-bell-o"></i> System Warnings 
										<span class="label label-danger"><?php echo $SysWarnCount;?></span>
                                    </a>
                                </li>
                                <li class="m_2"><a href="logout.php"><i class="fa fa-lock"></i> Logout</a></li>
                                <?php if($_SESSION['type']=='Admin'):?>
                                <li class="dropdown-menu-header text-center">
                                    <strong>Settings</strong>
                                </li>
                                <li class="m_2"><a href="manage_settings.php"><i class="fa fa-wrench"></i> Settings</a></li>
                                <li class="m_2"><a href="?type=<?php echo temp_encode('Restart_SmartHome');?>" onclick="return confirm('Are You Sure You Want To Restart SmartHome?');"><i class="fa fa-retweet"></i>Restart SmartHome</a></li>
                                <li class="m_2"><a href="?type=<?php echo temp_encode('Shutdown_SmartHome');?>" onclick="return confirm('Are You Sure You Want To Shutdown SmartHome?');"><i class="fa fa-power-off"></i>Shutdown SmartHome</a></li>
                                <?php endif;?>
                            </ul>
                        </li>
                    </ul>
                    <a class="navbar-brand" href="index.php" style="padding:20px 0px 0px 10px;"><?php echo $GS_Config['SiteName'];?></a>
                </div>
                <div style="float:right;display:inline-block;">
                    <button type="button" class="navbar-toggle" onclick="$('#navmenu').toggle();" style="border:none;background-color:#000;color:#fff;width:40px;font-size:20px;padding:2px;">
						<i class="fa fa-bars" aria-hidden="true" title="Menu"></i>
                    </button>
                    <?php if($GS_Config['InstallType']=="lite"):?>
						<button type="button" class="btn btn-primary" onclick="AutoLoadFunction();" style="display:inline-block;border:none;color:#fff;width:40px;font-size:20px;padding:2px;margin-top:10px;margin-right:30px;">
							<i class="fa fa-refresh" aria-hidden="true" title="Refresh"></i>
						</button>
                    <?php endif;?>
                </div>
            </nav>
            <!-- /.navbar-header -->
            <div class="navbar-default sidebar" id="navmenu" role="navigation" style="margin-top:-0px;height:100%;position:fixed;z-index:99;">
                <div class="sidebar-nav navbar-collapse" style="margin-top:32px;overflow-y:auto;height:85%;">
                    <ul class="nav" id="side-menu">
                        <?php $SysWarnCount = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT ID FROM event_log WHERE tags LIKE '%|Warnings|%' AND event_read='0'"));?>
                        <?php if($SysWarnCount>0):?>
                        <li style="color:#999;background-color:#191919;padding:2px;margin-bottom:15px;text-align:center;font-family:Roboto, sans-serif;">
                            <a href="events_log.php?type=<?php echo temp_encode("ShowWarnings");?>">
                            <i id="sysWarnCount" class="fa fa-exclamation-triangle" style="color:#ffff7f;"></i>&nbsp;<b>System Warnings: <u><i><?php echo $SysWarnCount;?></i></u></b>
                            </a>
                        </li>
                        <script>
                            setInterval(function(){
                             $("#sysWarnCount").animate({"opacity":"1"},1000);
                             $("#sysWarnCount").animate({"opacity":".05"},1000);
                             },2000);
                        </script>
                        <?php endif;?>
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <?php if(basename($_SERVER['PHP_SELF'])=="index.php" && GetUserPermissions("edit","index.php")==true):?>
                            <a onclick="$('#nav_dash_list').slideToggle();"><i class="fa fa-dashboard fa-fw nav_icon"></i><b>Dashboard</b> 
                            <i style="float:right;"class="fa fa-angle-down"></i>
                            </a>
                            <?php else :?>
                            <a href="index.php"><i class="fa fa-dashboard fa-fw nav_icon"></i><b>Dashboard</b></a>
                            <?php endif;?>
                            <ul style="display:none;list-style:none;padding:10px;" id="nav_dash_list">
                                <?php if(basename($_SERVER['PHP_SELF'])=="index.php" && GetUserPermissions("add","index.php")==true):?>
                                <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;" onclick="addNewDashboardCard();" data-target="#CardUIModal" data-toggle="modal">
                                    <a href="#" >
										<i class="fa fa-plus nav_icon"></i>Add Widgets
                                    </a>
                                </li>
                                <?php endif;?>
                                <?php if(GetUserPermissions("edit","index.php")==true):?>
                                <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;" onclick="enableCardArrange();">
                                    <a href="#"><i class="fa fa-pencil nav_icon"></i>Enable Editting</a>
                                </li>
                                <?php endif;?>
                            </ul>
                        </li>
                        <?php if(GetUserPermissions("read","manage_room.php")==true) :?>
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a onclick="$('#nav_room_list').slideToggle();" style="cursor:pointer;"><i class="fa fa-home nav_icon"></i><b>Rooms</b> <i style="float:right;"class="fa fa-angle-down"></i></a>
                            <ul style="display:none;list-style:none;padding:10px;" id="nav_room_list">
                                <?php $count=0;
                                    $query = "SELECT * FROM home_rooms";
                                    $rooms = mysqli_query($GS_DBCONN, $query);
                                    while($room = mysqli_fetch_assoc($rooms)) { $count++;
                                    	if($room['guest_access']=="0" && $_SESSION['type']=='Guest'){continue;}
                                    	if(GetUserPermissions($room['ID'],"manage_room.php")==false) {continue;}
                                    ?>
									<a href="manage_room.php?room_id=<?php echo temp_encode($room['ID']);?>">
										<li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">
											<i class="fa <?php echo $room['room_icon'];?> nav_icon "></i>
											<?php echo ucfirst($room['room_name']);?>
										</li>
									</a>
                                <?php }?>
                                <?php if(GetUserPermissions("add","manage_room.php")==true):?>
                                <a href="manage_room.php?add=1">
                                    <li style="margin-bottom:5px;background-color:#404040;padding:5px;">
                                        <i class="fa fa-plus nav_icon "></i>Add Room
                                    </li>
                                </a>
                                <?php endif;?>
                            </ul>
                        </li>
                        <?php endif;?>
                        <?php if(GetUserPermissions("read","manage_music.php")==true) :?>
                        <?php if ($GS_squeezeBoxServiceEnabled == true): //Check if service is enabled ?>
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a href="manage_music.php"><i class="fa fa-music nav_icon"></i><b>Play Music</b></a>
                        </li>
                        <?php endif;?>
                        <?php endif;?>
                        <?php if(GetUserPermissions("read","ifttt_simple.php")==true) :?>
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a href="ifttt_simple.php"><i class="fa fa-indent nav_icon"></i><b>IFTTT</b></a>
                        </li>
                        <?php endif;?>
                        <?php if(GetUserPermissions("read","manage_scene.php")==true) :?>
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a onclick="$('#nav_scene_list').slideToggle();" style="cursor:pointer;">
								<i class="fa fa-picture-o nav_icon"></i>
								<b>Manage Scenes</b>
								<i style="float:right;"class="fa fa-angle-down"></i>
                            </a>
                            <ul style="display:none;list-style:none;padding:10px;" id="nav_scene_list">
                                <?php $count=0;
                                    $query = "SELECT * FROM scene";
                                    $scenes = mysqli_query($GS_DBCONN, $query);
                                    while($scene = mysqli_fetch_assoc($scenes)) { $count++;
                                    	 if(GetUserPermissions($scene['ID'],"manage_scene.php")==false) {continue;}
                                    ?>
                                <a href="manage_scene.php?scene_id=<?php echo $scene['ID'];?>">
                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">
										<i class="fa <?php echo $scene['scene_icon'];?> nav_icon "></i>
                                        <?php echo ucfirst($scene['scene_name']);?>
                                    </li>
                                </a>
                                <?php }?>
                                <?php if(GetUserPermissions("add","manage_scene.php")==true):?>
                                <a href="manage_scene.php">
                                    <li style="margin-bottom:5px;background-color:#404040;padding:5px;">
                                        <i class="fa fa-plus nav_icon"></i>Add Scene
                                    </li>
                                </a>
                                <?php endif;?>
                            </ul>
                        </li>
                        <?php endif;?>
                        <?php if(GetUserPermissions("read","manage_buttons.php")==true) :?>
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a href="manage_buttons.php"><i class="fa fa-power-off nav_icon"></i><b>Manage Buttons</b></a>
                        </li>
                        <?php endif;?>
                       
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a onclick="$('#nav_sensor_list').slideToggle();" style="cursor:pointer;">
								<i class="fa fa-cogs nav_icon"></i>
								<b>Manage Sensors</b>
								<i style="float:right;"class="fa fa-angle-down"></i>
                            </a>
                            <ul style="display:none;list-style:none;padding:10px;" id="nav_sensor_list">
	                            <?php if(GetUserPermissions("read","manage_sensors.php")==true) :?>
	                                <a href="manage_sensors.php?type=<?php echo temp_encode("motion");?>">
	                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">Motion</li>
	                                </a>
	                                <a href="manage_sensors.php?type=<?php echo temp_encode("door");?>">
	                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">Doors</li>
	                                </a>
	                                <a href="manage_sensors.php?type=<?php echo temp_encode("window");?>">
	                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">Windows</li>
	                                </a>
	                                <a href="manage_sensors.php?type=<?php echo temp_encode("custom");?>">
	                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">Custom</li>
	                                </a>
								<?php endif;?>
								<?php if(GetUserPermissions("read","manage_dataSensors.php")==true) :?>
	                                <a href="manage_dataSensors.php">
	                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">Data Sensors</li>
	                                </a>
	                            <?php endif;?>
                            </ul>
                        </li>
                 
                        <?php if(GetUserPermissions("read","manage_devices.php")==true) :?>
	                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
	                            <a href="manage_devices.php"><i class="fa fa-lightbulb-o nav_icon"></i><b>Manage Devices</b></a>
	                        </li>
                        <?php endif;?>
                        <?php if(GetUserPermissions("read","manage_cameras.php")==true) :?>
	                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
	                            <a href="manage_cameras.php"><i class="fa fa-camera nav_icon"></i><b>Manage Cameras</b></a>
	                        </li>
                        <?php endif;?>
                        <?php if(GetUserPermissions("read","manage_users.php")==true || $_SESSION['type']=="Admin") :?>
	                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
	                            <a href="manage_users.php"><i class="fa fa-user nav_icon"></i><b>Manage Users</b></a>
	                        </li>
                        <?php endif;?>
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a href="#" onclick="$('#nav_historyLog_list').slideToggle();">
								<i class="fa fa-bar-chart nav_icon"></i>
								<b>History Logs</b>
								<i style="float:right;"class="fa fa-angle-down"></i>
                            </a>
                            <ul style="display:none;list-style:none;padding:10px;" id="nav_historyLog_list">
                                <?php if(GetUserPermissions("read","sensor_history_log.php")==true) :?>
                                <a href="sensor_history_log.php">
                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">
                                        Sensor History Log
                                    </li>
                                </a>
                                <?php endif;?>
                                <?php if(GetUserPermissions("read","device_history_log.php")==true) :?>
                                <a href="device_history_log.php">
                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">
                                        Device History Log
                                    </li>
                                </a>
                                <?php endif;?>
                            </ul>
                        </li>
                        <?php if(GetUserPermissions("read","events_log.php")==true) :?>
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a href="events_log.php"><i class="fa fa-book nav_icon"></i><b>Event Log</b></a>
                        </li>
                        <?php endif;?>
                        <?php if($_SESSION['type']=="Admin") :?>   
                        <li style="background-color:#191919;padding:2px;margin-bottom:5px;">
                            <a onclick="$('#nav_settings_list').slideToggle();" style="cursor:pointer;">
								<i class="fa fa-cogs nav_icon"></i><b>Settings</b> 
								<i style="float:right;"class="fa fa-angle-down"></i>
                            </a>
                            <ul style="display:none;list-style:none;padding:10px;" id="nav_settings_list">
                                <a href="manage_nodes.php">
                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">
                                        <i class="nav_icon"></i>Wireless Nodes
                                    </li>
                                </a>
                                <?php if($_SESSION['type']=="Admin" || GetUserPermissions("read","manage_users.php")==true) :?>
                                <a href="manage_settings.php">
                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">
                                        <i class="nav_icon"></i>SmartHome Settings
									</li>
                                </a>
                                <?php endif;?>
                                <?php if($_SESSION['type']=='Admin'):?>
                                <a href="?type=<?php echo temp_encode("Restart_SmartHome");?>" onclick="return confirm('Are You Sure You Want To Restart SmartHome?');">
                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">
                                        <i style="font-size:12px;" class="fa fa-undo nav_icon"></i>
                                        <i class="nav_icon"></i>
                                        Restart SmartHome											
                                    </li>
                                </a>
                                <a href="?type=<?php echo temp_encode("Shutdown_SmartHome");?>" onclick="return confirm('Are You Sure You Want To Shutdown SmartHome?');">
                                    <li style="margin-bottom:5px;background-color:#2d2d2d;padding:5px;">
                                        <i style="font-size:12px;" class="fa fa-power-off nav_icon"></i>
                                        <i class="nav_icon"></i>
                                        Shutdown SmartHome
                                    </li>
                                </a>
                                <?php endif;?>
                            </ul>
                        </li>
                        <?php endif;?>
                    </ul>
                </div>
            </div>
            <!-- /.navbar-static-side -->
        </div>
        <div id="page-wrapper" style="background-color:#f5f5f5;margin-top:40px;">
        <div class="graphs" style="background-color:#f5f5f5;">
        <!---------------------------- AUTOLOAD DataStates--------------------------->
        <div id="DataStatesContainer" style="z-index:999;">
            <div style="margin:0px;padding:5px;background-color:#fff;overflow:hidden;border-bottom: solid 3px <?php echo $GS_Config['themeColorMain'];?>">
                <div class="col-md-3 widget" style="overflow:hidden;height:60px;">
                    <div class="r3_counter_box" style="height:40px;font-size:18px;padding:0px;">
                        <form method="POST" class="autoform" title="Change Alarm Mode">
                            <input type="hidden" name="type" value="<?php echo temp_encode("change_alarm_mode");?>"/>
                            <input type="hidden" name="mode" value="0" id="datastates_alarmMode_value"/>
                            <button style="border:none;background-color:#fff;float:left;padding-left:0px;margin-right:20px;" type="submit">
								<i class="pull-left fa fa-home icon-rounded" style="height:60px;line-height:60px;padding:0px;border-radius:0px;font-size:25px;"></i>
                            </button>
                        </form>
                        <div class="stats" style="padding:0px;">
                            <h5><strong id="datastates_alarm_mode"></strong></h5>
                            <p style="overflow:hidden;height:20px;"><span>Alarm Mode</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 widget" style="overflow:hidden;height:60px;">
                    <div class="r3_counter_box" style="height:40px;font-size:18px;padding:0px;">
                        <button data-toggle="modal" data-target="#AlarmDialog" style="border:none;background-color:#fff;float:left;padding-left:0px;margin-right:20px;" type="button">
							<i class="pull-left fa fa-bell user1 icon-rounded" id="datastates_alarmState" style="background-color:rgb(120, 205, 81);height:60px;line-height:60px;border-radius:0px;font-size:25px;"></i>
                        </button>
                        <div class="stats">
                            <h5><strong id="datastates_alarm_state"></strong></h5>
                            <p style="overflow:hidden;height:20px;"><span>Alarm State</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 widget" style="overflow:hidden;height:60px;">
                    <div class="r3_counter_box" style="height:40px;font-size:18px;padding:0px;">
                        <i class=" fa fa-power-off dollar1 icon-rounded" style="height:60px;line-height:60px;border-radius:0px;font-size:25px;margin:0px;float:left;" onclick="location.href='manage_devices.php'" title="View Devices"></i>
                        <div style="float:left;width:60px;margin:0px;margin-left:5px;margin-top:-1px;">
							<a href="?type=<?php echo temp_encode("All_Devices_On");?>"><button type="button" class="btn btn-default" style="display:inline-block;font-size:10px;padding:8px;">All On</button></a>
							<a href="?type=<?php echo temp_encode("All_Devices_Off");?>"><button type="button" class="btn btn-default" style="display:inline-block;font-size:10px;padding:8px;">All Off</button></a>
						</div>
					    <div class="stats">
							<h5><strong id="datastates_active_devices"></strong></h5>
							<p style="overflow:hidden;height:20px;"><span>Devices Active</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 widget" style="overflow:hidden;height:60px;">
                    <div class="r3_counter_box" style="height:0px;font-size:18px;padding:0px;">
                        <a style="color:#000;" href="events_log.php" title="View Event Log">
							<i class="pull-left fa fa-book user2 icon-rounded" style="height:60px;line-height:60px;border-radius:0px;font-size:25px;"></i>
                        </a>
                        <div class="stats">
                            <h5><strong id="datastates_events"></strong></h5>
                            <p style="overflow:hidden;height:20px;"><span>Events</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <!----------------------------------- End DataStaes ------------------------------------->
            <!----------------------------------- CAMERA ALERTS ------------------------------------->
            <div style="overflow:hidden;width:100%;z-index:9999;height:55px;">
                <?php 
                    $query = "SELECT * FROM camera_list  ";
                    $results1 = mysqli_query($GS_DBCONN, $query);
                    while($camera_info=mysqli_fetch_assoc($results1)){ 
                    	$query = "SELECT * FROM sensors WHERE ID='".$camera_info['sensor_assign']."' ";
                    	$results2 = mysqli_query($GS_DBCONN, $query);
                    	$sensor_info=mysqli_fetch_assoc($results2);
                    	if(GetUserPermissions($camera_info['ID'],"manage_cameras.php")==false){continue;}
                    ?>
                <div style="
                    border-right:2px solid #fff;
                    border-bottom:2px solid #fff;
                    margin:-2px;
                    display:none; 
                    overflow:hidden;
                    border-radius:2px;
                    width:0px;
                    height:25px;
                    background-color:<?php echo $camera_info['alert_color'];?>;
                    color:#fff;
                    padding:2px;" id="CameraAlert<?php echo $camera_info['ID'];?>" class="CameraAlert">
                    <p style="text-align:center;"> 
                        <?php echo $sensor_info['sensor_name'];?>:
                        <a data-toggle="modal" data-target="#CameraModal_alerts" href="#" style="color:#fff;" onclick="showCameraModal<?php echo $camera_info['ID'];?>();"><u>View Camera</u></a>
                    </p>
                    <script>	
                        function showCameraModal<?php echo $camera_info['ID'];?>(){
                        	$("#camera_name").text("<?php echo $camera_info['camera_name'];?>");
                        	$("#camera_alerts_url").prop("src","<?php echo $camera_info['ip_address'];?>");
                        	$("#CameraModal_ActSignal_alert").addClass("featured_camera_alert_id<?php echo $camera_info['room'];?>");
                        	$("#camera_alerts_clickEvent").val("<?php echo $camera_info['click_trigger'];?>");
                        	$("#ShowCameraModalBtn").click();
                        }
                    </script>
                </div>
                <?php }?>
                <!------------------------------- Node ALERTS ----------------------------->
                <?php 
                    $query = "SELECT * FROM iot_nodes ORDER BY ID ASC";
                    $results1 = mysqli_query($GS_DBCONN, $query);
                    $node_count=mysqli_num_rows($results1);
                    while($node_info=mysqli_fetch_assoc($results1)){ 
                    ?>
                <div style="
                    border-right:2px solid #fff;
                    border-bottom:2px solid #fff;
                    margin:-2px;
                    margin-top:-4px;
                    display:none; 
                    overflow:hidden;
                    border-radius:2px;
                    width:0px;
                    height:25px;
                    background-color:red;
                    color:#fff;
                    font-family:Tahoma, Geneva, sans-serif;
                    padding:2px;" id="NodeAlert<?php echo $node_info['ID'];?>" class="NodeAlert">
                    <p style="text-align:center;"> 
                        <?php echo $node_info['node_id'];?>:
                        <a href="manage_nodes.php" style="color:#fff;"><u>View Nodes</u></a>
                    </p>
                </div>
                <?php }?>
            </div>
        </div>
        <script>  
            $(document).ready(function(){
            	if (/Android|iPad|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    // tasks to do if it is a Mobile Device
                    $('#navmenu').hide();
            		$('.hideForMobile').hide();
            		$('#page-wrapper').css("margin","0px");
            		$('#page-wrapper').css("margin-top","60px");
            		$('.navbar-toggle').show();
            		$('#TopNav').css({"padding-top":"20px"});
                 }else{
            		$(window).scroll(function(e) {
            			if ($(this).scrollTop() > 70  ) {
							$('#TopNav img').css({width: "40px",height: "40px" });
            				$('.navbar-brand').animate({fontSize: "20px"},0);
            				$('#TopNav').css({width: "250px" });						
            				
            				//$("#DataStatesContainer").css({'top': '0px'}); 
            				//$("#DataStatesContainer").css({"width":"85%"}); 
            				//$("#datastatesClearfix").css({'height': '140px'}); 
            			}
            			if ($(this).scrollTop() <= 70 ) {	                
            				$('#TopNav').stop().css({"width":"100%"},10 );
							$('#TopNav img').css({width: "45px",height: "45px"});
            				$('.navbar-brand').animate({"fontSize": "30px"},10);
            				//$("#DataStatesContainer").css({'position': 'relative'});
            				//$("#DataStatesContainer").css({'top': '0px'});
            				//$("#DataStatesContainer").css({"width":"100%"});
            				//$("#datastatesClearfix").css({'height': '20px'}); 
            			}
            		});	
            	}	
            });
            
            /* Autoform */
            $(document).ready(function() {
				 $('.autoform').on('submit', function(e) {
					 e.preventDefault();
					 $.ajax({
						 url: $(this).attr('action') || window.location.pathname,
						 type: "POST",
						 data: $(this).serialize(),
						 success: function(){},
						 error: function(jXHR, textStatus, errorThrown) {}
					 });
				 });
			 });

        </script>