<?php
    if(!empty($_POST) || !empty($_GET)){
    	$GS_phueServiceBypass = true;
    	$GS_wemoServiceBypass = true;
    	$GS_mqttServiceBypass = true;
    	$GS_emailServiceBypass = false;
    	$GS_squeezeBoxServiceBypass = true;
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
    	$GS_phueServiceBypass = false;
    	$GS_wemoServiceBypass = true;
    	$GS_mqttServiceBypass = true;
    	$GS_emailServiceBypass = false;
    	$GS_squeezeBoxServiceBypass = true;
    	$GS_webIncludesIncluded = false;
    	
    	$upParrentDir = '/../../..';
    	require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));	
    	
    	include("Autoload/AutoLoad_DataStates.php");
    	include("Autoload/AutoLoad_SHAlerts.php");
    	include("Autoload/Autoload_checkTempEncode.php");
    	include("Autoload/Autoload_SettingsSmartHomeServiceCheck.php");
    	exit;	
    }
    ################################################
    
    #################### API #######################
    $page_title="Settings";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    
    
    //Check all SmartHome Services
    $query1 = "SELECT * FROM shbroker WHERE state<>'0'";
    $results_proc = mysqli_query($GS_DBCONN, $query1);
    while($process = mysqli_fetch_assoc($results_proc)){
    	checkSmartHomeService($process['proc_id']);
    }
    
    	
    	
    if(temp_decode($_POST['type'])=='save_settings' && GetUserPermissions("edit")==true){ //update settings
    
    	//settings
    	$insert_query="UPDATE settings SET 
    	name='".clean_text($_POST['general_name'],100)."',
    	home_address='".clean_text($_POST['general_homeAddress'],100)."',
    	home_latLong='',
    	city_state='".clean_text($_POST['general_cityState'],100)."',
    	zip_code='".clean_text($_POST['general_zipcode'],10)."',
    	outgoing_email_list='".clean_text($_POST['email_list'],500)."' WHERE ID='1'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	
    	//email
    	$insert_query="UPDATE enabled_services SET 
    	service_attr3='".clean_text($_POST['smtp_email'],200)."',
    	service_attr4='".clean_text($_POST['smtp_password'],100)."',
    	service_attr2='".clean_text($_POST['smtp_port'],5)."',
    	service_attr1='".clean_text($_POST['smtp_host'],100)."',
    	enabled='".(int)clean_text($_POST['smtp_enabled'],1)."'
    	WHERE service_name='Email'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    		
    	//Phue
    	$insert_query="UPDATE enabled_services SET 
    	service_attr1='".clean_text($_POST['phue_ip'],20)."',
    	service_attr2='".clean_text($_POST['phue_user'],200)."',
    	enabled='".(int)clean_text($_POST['phue_enabled'],1)."'
    	WHERE service_name='Phillips Hue'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	
    	if($_POST['phue_enabled']=="1"){
    		mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='1' WHERE type='phue' AND enabled='3'")or die (mysqli_error($GS_DBCONN)); //Enable All Phillips Hue Devices
    	}else{
    		mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='3' WHERE type='phue' AND enabled='1'")or die (mysqli_error($GS_DBCONN)); //Disable All Phillips Hue Devices
    	}
    
    	//MQTT
		$connection = clean_text($_POST['mqtt_ip'],20).":".clean_text($_POST['mqtt_port'],5);
		$auth = clean_text($_POST['mqtt_username'],20)."".clean_text($_POST['mqtt_password'],20);
    	$insert_query="UPDATE enabled_services SET 
    	service_attr1='".$connection."',
    	service_attr2='".$auth."',
    	enabled='".(int)clean_text($_POST['mqtt_enabled'],1)."'
    	WHERE service_name='MQTT'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	
    	//Wemo
    	$insert_query="UPDATE enabled_services SET 
    	enabled='".(int)clean_text($_POST['wemo_enabled'],1)."'
    	WHERE service_name='Belkin Wemo'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	
    	if($_POST['wemo_enabled']=="1"){
    		mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='1' WHERE enabled='3' AND type='wemo'");
    	}else{
    		mysqli_query($GS_DBCONN, "UPDATE devices SET enabled='3' WHERE enabled='1' AND type='wemo'");
    	}
    	
    	//SqueezeBox
    	$insert_query="UPDATE enabled_services SET 
    	enabled='".(int)clean_text($_POST['squeezeBox_enabled'],1)."'
    	WHERE service_name='SqueezeBox'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	
    	if($_POST['squeezeBox_enabled']=="1"){
    		mysqli_query($GS_DBCONN, "UPDATE music_servers SET enabled='1' WHERE enabled='3'");
    	}else{
    		mysqli_query($GS_DBCONN, "UPDATE music_servers SET enabled='3' WHERE enabled='1'");
    	}
    	
    	//Weather
    	$insert_query="UPDATE enabled_services SET 
    	service_attr1='".clean_text($_POST['weather_apikey'],100)."'
    	,enabled='".(int)clean_text($_POST['weather_enabled'],1)."'
    	WHERE service_name='Weather'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	
    	$insert_query="UPDATE weather_data SET 
    	set_zip='".clean_text($_POST['general_zipcode'],10)."'
    	WHERE ID='1'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	
    	//Spotify Squeezbox Service
    	$insert_query="UPDATE enabled_services SET 
    	service_attr1='".clean_text($_POST['spotify_apikey1'],100)."',
    	service_attr2='".clean_text($_POST['spotify_apikey2'],100)."',
    	service_attr3='".clean_text($_POST['spotify_userID'],100)."',
    	enabled='".(int)clean_text($_POST['spotify_enabled'],1)."'
    	WHERE service_name='Spotify'";
    	mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    
    	
    }
    
    //SmartHome Services
    if(temp_decode($_GET['type'])=="StartSystemService" && GetUserPermissions("edit")==true){
    	if(temp_decode($_GET['page'])=="All"){
			$query="SELECT * FROM shbroker ";
			$processes = mysqli_query($GS_DBCONN,$query);
			while($service = mysqli_fetch_assoc($processes)){
				startSmartHomeService($service['page_name']);
			}
		}else{
			startSmartHomeService(temp_decode($_GET['page']));
		}
    }
    
    if(temp_decode($_GET['type'])=="StopSystemService" && GetUserPermissions("edit")==true){
		if(temp_decode($_GET['page'])=="All"){
			$query="SELECT * FROM shbroker ";
			$processes = mysqli_query($GS_DBCONN,$query);
			while($service = mysqli_fetch_assoc($processes)){
				endSmartHomeService($service['page_name']);
			}
		}else{
			endSmartHomeService(temp_decode($_GET['page']));
		}
    }
    
    if(temp_decode($_GET['type'])=="Test_email" && temp_decode($_GET['type'])!="[EXPIRED]" && GetUserPermissions("edit")==true){
    	GF_sendEmail($GS_settings['outgoing_email_list'],$GS_Config['SiteName']."-Test","This is a test email from ".$GS_Config['SiteName']."","USER:".$_SESSION['id']);
    }
    		
    
    ?>
<form method="post" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:30px;padding:0px;">
    <input type="hidden" name="type" value="<?php echo temp_encode("save_settings");?>"/>
    <div style="margin-bottom:10px;">
        <h3 style="display:inline-block;width:40%;"><?php echo $GS_Config['SiteName'];?> Settings</h3>
        <div style="display:inline-block;min-width:250px;width:30%;">
            <?php if(GetUserPermissions("edit")==true):?>
				<button type="submit" class="btn btn-primary" style="width:120px;">Save</button>
            <?php else:?>
				<button type="button" disabled class="btn btn-primary" style="width:120px;" title="You do not have permission">Save</button>
            <?php endif;?>
            <button type="button" class="btn btn-default" onclick="location.href='/';" style="width:120px;">Cancel</button>
        </div>
    </div>
    <!-- Running Services ---->	
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding:0px;min-width:300px;">
        <div class="collection" style="background-color:#fff;height:320px;;padding:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin:2px;height:330px;">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <h3 style="text-align:center;width:100%;color:#000;font-size:16px;">
                        <b>SmartHome Services</b>
                    </h3>
                    <table style="width:100%;">
						<tr>
							<td><b>Start/Stop All</b></td>
							<td  style="padding:10px;">
								<a href="?type=<?php echo temp_encode("StartSystemService");?>&page=<?php echo temp_encode("All");?>" style="display:none;" id="AllSystemService<?php echo $process['ID'];?>_start">
									<button type="button" style="border:none;background-color:<?php echo $GS_Config['themeColorMain'];?>;color:#fff;padding:6px;margin-top:-10px;width:70px;" class="btn">Start</button>
								</a>
								<a href="?type=<?php echo temp_encode("StopSystemService");?>&page=<?php echo temp_encode("All");?>" style="display:none;" id="AllSystemService<?php echo $process['ID'];?>_stop">
									<button type="button" style="border:none;background-color:#ef553a;color:#fff;padding:6px;margin-top:-10px;width:70px;" class="btn">Stop</button>
								</a>
							</td>
						</tr>
                        <?php
                            $query="SELECT * FROM shbroker ";
                            $processes = mysqli_query($GS_DBCONN,$query);
                            while($process = mysqli_fetch_assoc($processes)){
                        ?>
							<tr>
								<td style="padding:10px;"><?php echo $process['page_name'];?></td>
								<td style="padding:10px;">
									<a href="?type=<?php echo temp_encode("StartSystemService");?>&page=<?php echo temp_encode($process['page_name']);?>" style="display:none;" id="SystemService<?php echo $process['ID'];?>_start">
										<button type="button" style="border:none;background-color:<?php echo $GS_Config['themeColorMain'];?>;color:#fff;padding:6px;margin-top:-10px;width:70px;" class="btn">Start</button>
									</a>
									<a href="?type=<?php echo temp_encode("StopSystemService");?>&page=<?php echo temp_encode($process['page_name']);?>" style="display:none;" id="SystemService<?php echo $process['ID'];?>_stop">
										<button type="button" style="border:none;background-color:#ef553a;color:#fff;padding:6px;margin-top:-10px;width:70px;" class="btn">Stop</button>
									</a>
								</td>
							</tr>
                        <?php }?>
                    </table>
                    <div id="SmartHomeServiceCheck"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- General ---->	
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding:0px;">
        <div class="collection" style="background-color:#fff;height:320px;;padding:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin:2px;height:330px;">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <h3 style="text-align:center;width:100%;color:#000;font-size:16px;">
                        <b>General Settings</b>
                    </h3>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Name</b></span>
                        <input title="Site Admin" required="" type="text" name="general_name" value="<?php echo ucwords($GS_settings['name']); ?>" placeholder="Site Admin" class="form-control1" style="width:100%;" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Address</b></span>
                        <input title="Home Address" required="" type="text" name="general_homeAddress" value="<?php echo ucwords($GS_settings['home_address']);?>" placeholder="Home Address" class="form-control1" style="float:left;width:100%;" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><b>City/St</b></span>
                        <input title="City/State" required="" type="text" name="general_cityState" value="<?php echo ucwords($GS_settings['city_state']);?>" placeholder="City/State (City, ST)" class="form-control1" style="display:block;width:100%;" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Zip Code</b></span>
                        <input title="Zip Code" required="" type="text" name="general_zipcode" value="<?php echo ucwords($GS_settings['zip_code']);?>" placeholder="Zip Code" class="form-control1" style="display:block;width:100%;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- EMAIL ---->	
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding:0px;">
        <div class="collection" style="background-color:#fff;height:auto;padding:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin:2px;height:330px;">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <h3 style="text-align:center;width:100%;color:#000;font-size:16px;height:30px;margin-bottom:20px;">
                        <div style="float:left;font-size:14px;">
                            <input type="checkbox" data-toggle="toggle" data-size="small" name="smtp_enabled" value="1" <?php if($GA_enabledService_email['enabled']=="1"){echo "checked";}?>/>
                        </div>
                        <b>SMTP/Email Settings</b>
                    </h3>
                    <?php if($GA_enabledService_email['enabled']=="3"):?>
						<h6 style="color:red;">
							This Service Has Been Disabled By System Monitor
						</h6>
                    <?php endif;?>
                    <div style="width:68%;display:inline-block;">
                        <div class="input-group">
                            <span class="input-group-addon"><b>Host</b></span>
                            <input title="Host" type="text" name="smtp_host" value="<?php echo $GA_enabledService_email['service_attr1']; ?>" placeholder="SMTP Host" class="form-control1"  />
                        </div>
                    </div>
                    <div style="display:inline-block;width:30%;">
                        <div class="input-group">
                            <input title="Port" type="text" name="smtp_port" value="<?php echo $GA_enabledService_email['service_attr2'];?>" placeholder="Port" class="form-control1" />
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Email</b></span>
                        <input title="Email" type="text" name="smtp_email" value="<?php echo $GA_enabledService_email['service_attr3'];?>" placeholder="SMTP Email" class="form-control1" style="display:block;width:100%;" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Password</b></span>
                        <input title="Password" type="password" name="smtp_password" value="<?php echo $GA_enabledService_email['service_attr4'];?>" placeholder="SMTP Password" class="form-control1" style="display:block;width:100%;" />
                    </div>
                    <textarea title="Outgoing Email List" name="email_list" placeholder="Outgoing Email List" class="form-control1" style="display:block;width:100%;margin-bottom:10px;height:60px;" ><?php echo $GS_settings['outgoing_email_list'];?></textarea>
                    <span style="font-size:11px;color:grey;">Separate each email with a comma</span>
                    <?php if(GetUserPermissions("edit")==true):?>
						<a href="?type=<?php echo temp_encode("Test_email");?>" style="float:right;font-size:12px;"><u>Send Test Email</u></a>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <!--- PHILLIPS HUE -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding:0px;">
        <div class="collection" style="background-color:#fff;height:auto;padding:7px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin:2px;height:280px;">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <h3 style="text-align:center;width:100%;color:#000;font-size:16px;height:30px;margin-bottom:20px;">
                        <div style="float:left;font-size:14px;">
                            <input type="checkbox" data-toggle="toggle" data-size="small" name="phue_enabled" value="1" <?php if($GA_enabledService_phue['enabled']!="0"){echo "checked";}?>/>
                        </div>
                        <b>Phillips Hue Settings</b>
                    </h3>
                    <?php if($GA_enabledService_phue['enabled']=="3"):?>
						<h6 style="color:red;">
							This Service Has Been Disabled By System Monitor
						</h6>
                    <?php endif;?>
                    <div class="input-group">
                        <span class="input-group-addon"><b>IP</b></span>
                        <input title="IP Address" type="text" name="phue_ip" value="<?php echo $GA_enabledService_phue['service_attr1'];?>" placeholder="Phillips Hue IP" class="form-control1" style="width:100%;" />
                    </div>
                    <div style="width:80%;display:inline-block;">
                        <div class="input-group">
                            <span class="input-group-addon"><b>Key</b></span>
                            <input title="Hue Key" type="text" name="phue_user" value="<?php echo $GA_enabledService_phue['service_attr2'];?>" placeholder="Phillips Hue Key" class="form-control1" id="PhueSettingsHueKey"/>						
                        </div>
                    </div>
                    <button type="button" data-toggle="modal" data-target="#getPhueKey" class="btn btn-default" style="width:15%;height:40px;display:inline-block;margin-top:-62px;" title="Get Key From Hub">...</button>
                    <div id="PhueSettingsHueKey"></div>
                    <!-- phue key autoload -->
                    <!-- Modal -->
                    <div class="modal fade" id="getPhueKey" role="dialog" style="z-index:9999;margin-top:100px;">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" id="phueSettingsCloseBtn" class="close" data-dismiss="modal">&times;</button>
                                    <h4 style="" class="modal-title">Configure Phillips Hue</h4>
                                </div>
                                <div class="modal-body" style="overflow:auto;padding:0px;">
                                    <div style="width:48%;display:inline-block;">
                                        <img src="images/config_philips_hue.jpg" style=""/>
                                    </div>
                                    <div style="width:48%;display:inline-block;text-align:center;">
                                        <h4>Press the button on the Bridge</h4>
                                        <h5 style="color:red;" id="phueSettingsErrorText">
                                            <!-- ERROR TEXT -->
                                        </h5>
                                        <button type="button" class="btn btn-default" style="width:85%;" onclick="getPhueKey()">Continue</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function getPhueKey(){
                        	$("#PhueSettingsHueKey").load("Autoload/Autoload_RegisterHueHub.php");
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
    <!---- MQTT -->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding:0px;">
        <div class="collection" style="background-color:#fff;height:auto;padding:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin:2px;height:280px;">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <h3 style="text-align:center;width:100%;color:#000;font-size:16px;height:30px;margin-bottom:20px;">
                        <div style="float:left;font-size:14px;">
                            <input type="checkbox" data-toggle="toggle" data-size="small" name="mqtt_enabled" value="1" <?php if($GA_enabledService_mqtt['enabled']!="0"){echo "checked";}?>/>
                        </div>
                        <b>MQTT Settings</b>
                    </h3>
                    <?php if($GA_enabledService_mqtt['enabled']=="3"):?>
						<h6 style="color:red;">
							This Service Has Been Disabled By System Monitor
						</h6>
                    <?php endif;?>
                    <div class="input-group">
                        <span class="input-group-addon"><b>IP</b></span>
                        <input title="Broker IP Address" type="text" name="mqtt_ip" value="<?php echo explode(":",$GA_enabledService_mqtt['service_attr1'])[0];?>" placeholder="MQTT Broker IP" class="form-control1" style="width:100%;" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Port</b></span>
                        <input title="Broker Port" type="text" name="mqtt_port" value="<?php echo explode(":",$GA_enabledService_mqtt['service_attr1'])[1];?>" placeholder="MQTT Broker Port (1883)" class="form-control1" style="width:100%;" />
                    </div>
					<div class="input-group">
                        <span class="input-group-addon"><b>Username</b></span>
                        <input title="Broker Username" type="text" name="mqtt_username" value="<?php echo explode(":",$GA_enabledService_mqtt['service_attr2'])[0];?>" placeholder="(Optional)" class="form-control1" style="width:100%;" />
                    </div>
					<div class="input-group">
                        <span class="input-group-addon"><b>Password</b></span>
                        <input title="Broker Password" type="password" name="mqtt_password" value="<?php echo explode(":",$GA_enabledService_mqtt['service_attr2'])[1];?>" placeholder="(Optional)" class="form-control1" style="width:100%;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---- SQUEEZEBOX --->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding:0px;">
        <div class="collection" style="background-color:#fff;height:auto;padding:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin:2px;height:280px;">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <h3 style="text-align:center;width:100%;color:#000;font-size:16px;height:30px;margin-bottom:20px;">
                        <div style="float:left;font-size:14px;">
                            <input type="checkbox" data-toggle="toggle" data-size="small" name="squeezeBox_enabled" value="1" <?php if($GA_enabledService_squeezebox['enabled']!="0"){echo "checked";}?>/>
                        </div>
                        <b>SqueezeBox</b>
                    </h3>
                    <?php if($GA_enabledService_squeezebox['enabled']=="3"):?>
						<h6 style="color:red;">
							This Service Has Been Disabled By System Monitor
						</h6>
                    <?php endif;?>
                    <center>
                        <b>Spotify Plugin:</b> &nbsp;
                        <input type="checkbox" name="spotify_enabled" value="1" <?php if($GA_enabledService_spotify['enabled']!="0"){echo "checked";}?>/>
                    </center>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Client ID</b></span>
                        <input type="text" name="spotify_apikey1" value="<?php echo $GA_enabledService_spotify['service_attr1'];?>" placeholder="Client ID" class="form-control1" style="width:100%;" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Client Secret</b></span>
                        <input type="text" name="spotify_apikey2" value="<?php echo $GA_enabledService_spotify['service_attr2'];?>" placeholder="Client Secret" class="form-control1" style="width:100%;" />
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><b>User ID</b></span>
                        <input title="Spotify User ID" type="text" name="spotify_userID" value="<?php echo $GA_enabledService_spotify['service_attr3'];?>" placeholder="User ID (Optional)" class="form-control1" style="width:100%;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---- WEATHER --->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding:0px;">
        <div class="collection" style="background-color:#fff;height:auto;padding:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin:2px;height:130px;">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <h3 style="text-align:center;width:100%;color:#000;font-size:16px;height:30px;margin-bottom:20px;">
                        <div style="float:left;font-size:14px;">
                            <input type="checkbox" data-toggle="toggle" data-size="small" name="weather_enabled" value="1" <?php if($GA_enabledService_weather['enabled']!="0"){echo "checked";}?>/>
                        </div>
                        <b>Open Weather Map</b>
                    </h3>
                    <?php if($GA_enabledService_weather['enabled']=="3"):?>
						<h6 style="color:red;">
							This Service Has Been Disabled By System Monitor
						</h6>
                    <?php endif;?>
                    <div class="input-group">
                        <span class="input-group-addon"><b>Key</b></span>
                        <input title="API Key" type="text" name="weather_apikey" value="<?php echo $GA_enabledService_weather['service_attr1'];?>" placeholder="Weather API Key" class="form-control1" style="width:100%;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--- WEMO --->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding:0px;">
        <div class="collection" style="background-color:#fff;height:auto;padding:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin:2px;height:130px;">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <h3 style="text-align:center;width:100%;color:#000;font-size:16px;height:30px;margin-bottom:20px;">
                        <div style="float:left;font-size:14px;">
                            <input type="checkbox" data-toggle="toggle" data-size="small" name="wemo_enabled" value="1" <?php if($GA_enabledService_wemo['enabled']!="0"){echo "checked";}?>/>
                        </div>
                        <b>Belkin Wemo</b>
                    </h3>
                    <?php if($GA_enabledService_wemo['enabled']=="3"):?>
						<h6 style="color:red;">
							This Service Has Been Disabled By System Monitor
						</h6>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="clearfix"> </div>
<?php include("includes/footer.php");?>
<?php include("includes/modals.php");?>
</body>
</html>