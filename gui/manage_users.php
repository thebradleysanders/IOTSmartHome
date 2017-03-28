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
    	$GS_squeezeBoxServiceBypass = true;
    	$GS_webIncludesIncluded = false;
    	
    	$upParrentDir = '/../../..';
    	require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    	
    	include("Autoload/AutoLoad_DataStates.php");
    	include("Autoload/AutoLoad_SHAlerts.php?userID=".$_SESSION['id']);
    	include("Autoload/Autoload_checkTempEncode.php");
    	exit;	
    }
    ################################################
    
    #################### API #######################
    $page_title="Manage Users";
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    
    ################################################
    
    function LF_Checkrights_MU($user){
    	if($_SESSION['type']=="Guest"){RETURN false;}
    	if($_SESSION['type']=="Family" && $user=="Admin"){RETURN false;}
    	if($_SESSION['type']=="Family" && $user=="Family"){RETURN false;}
    	if($_SESSION['type']=="Family" && $user=="Guest"){RETURN true;}
    	if($_SESSION['type']=="Admin"){RETURN true;}
    }
    
    
    
	if(temp_decode($_POST['type'])=='add_user' && GetUserPermissions("add")==true){
		$insert_query="INSERT INTO users (user_name,username,password,last_login,type,enabled,decription,user_img,user_workAddress,user_workLatLong,room_assign,user_permissions)
		VALUES(
		'".clean_text(ucwords($_POST['name']),200)."',
		'".clean_text($_POST['username'],100)."',
		'".clean_text($_POST['password'],200)."',
		'0',
		'".clean_text($_POST['acctype'],50)."',
		'1',
		'".clean_text($_POST['decription'],200)."',
		'Default.png',
		'',
		'',
		'0',
		'".$GS_Config['DefaultUserPermissions']."'
		)";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
		
	}
	
	//delete user
	if($_GET['delete']!='' && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
		//this decides who has permissions on this page
		$query = "SELECT * FROM users WHERE ID='".temp_decode($_GET['delete'])."'";
		$results = mysqli_query($GS_DBCONN, $query);
		$this_user = mysqli_fetch_assoc($results);
			
		if(LF_Checkrights_MU($this_user['type'])==true){
			$insert_query="DELETE FROM users WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."' ";
			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));

			//delete dshboard cards
			$insert_query="DELETE FROM dashboard_cards WHERE user_id='".clean_text(temp_decode($_GET['delete']),11)."' ";
			mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
			echo "<script>location.href='manage_users.php';</script>";
		}else{
			echo "<script>alert('You Do Not Have The Right Permissions to Delete This User');</script>";
		}
	}
	
	//update user
	if(temp_decode($_POST['type'])=='update_users' && (GetUserPermissions("edit")==true || $_SESSION['type']=="Admin")){
		//this decides who has permissions on this page
		$query = "SELECT * FROM users WHERE ID='".clean_text($_POST['ID'],11)."'";
		$results = mysqli_query($GS_DBCONN, $query);
		$this_user = mysqli_fetch_assoc($results);
		
		if(LF_Checkrights_MU($this_user['type'])==true){
			//page permissions
			$permissions = $permissions.":alarm_access|read".$_POST['alarm_access_edit'];
			$permissions = $permissions.$_POST['dashboard_access_read'].$_POST['dashboard_access_edit'].$_POST['dashboard_access_add'].$_POST['dashboard_access_delete'];
			$permissions = $permissions.$_POST['ifttt_access_read'].$_POST['ifttt_access_edit'].$_POST['ifttt_access_add'].$_POST['ifttt_access_delete'];
			$permissions = $permissions.$_POST['buttons_access_read'].$_POST['buttons_access_edit'].$_POST['buttons_access_add'].$_POST['buttons_access_delete'];
			$permissions = $permissions.$_POST['music_access_read'].$_POST['music_access_edit'].$_POST['music_access_add'].$_POST['music_access_delete'];
			$permissions = $permissions.$_POST['users_access_read'].$_POST['users_access_edit'].$_POST['users_access_add'].$_POST['users_access_delete'];
			$permissions = $permissions.$_POST['device_access_read'].$_POST['device_access_edit'].$_POST['device_access_add'].$_POST['device_access_delete'];
			$permissions = $permissions.$_POST['sensor_access_read'].$_POST['sensor_access_edit'].$_POST['sensor_access_add'].$_POST['sensor_access_delete'];
			$permissions = $permissions.$_POST['dataSensor_access_read'].$_POST['dataSensor_access_edit'].$_POST['dataSensor_access_add'].$_POST['dataSensor_access_delete'];
			$permissions = $permissions.$_POST['log_access_read'].$_POST['log_access_edit'].$_POST['log_access_add'].$_POST['log_access_delete'];
			$permissions = $permissions.$_POST['settings_access_read'].$_POST['settings_access_edit'];
			$permissions = $permissions.$_POST['SensorHistoryLog_access_read'].$_POST['DeviceHistoryLog_access_read'];
			
			//Room
			$permissions = $permissions.$_POST['room_access_read'].$_POST['room_access_edit'].$_POST['room_access_add'].$_POST['room_access_delete'];
			$query = "SELECT * FROM home_rooms ORDER BY ID ASC ";
			$results1 = mysqli_query($GS_DBCONN, $query);
			while($result1 = mysqli_fetch_assoc($results1)) { 	
				$post_name ="room_permis_".$result1['ID'];			
				$permissions = $permissions.$_POST[$post_name];
			}
			//Cameras
			$permissions = $permissions.$_POST['camera_access_read'].$_POST['camera_access_edit'].$_POST['camera_access_add'].$_POST['camera_access_delete'];
			$query = "SELECT * FROM camera_list ORDER BY ID ASC ";
			$results2 = mysqli_query($GS_DBCONN, $query);
			while($result2 = mysqli_fetch_assoc($results2)) { 	
				$post_name ="camera_permis_".$result2['ID'];			
				$permissions = $permissions.$_POST[$post_name];
			}
			//Scene
			$permissions = $permissions.$_POST['scene_access_read'].$_POST['scene_access_edit'].$_POST['scene_access_add'].$_POST['scene_access_delete'];
			$query = "SELECT * FROM scene ORDER BY ID ASC ";
			$results3 = mysqli_query($GS_DBCONN, $query);
			while($result3 = mysqli_fetch_assoc($results3)) { 	
				$post_name ="scene_permis_".$result3['ID'];			
				$permissions = $permissions.$_POST[$post_name];
			}
			
			$image = upload($_FILES["user_img"], "images/users/");
			if($image==false){
				$image = $this_user['user_img'];
			}

			$insert_query="UPDATE users SET 
			user_name='".clean_text($_POST['user_name'],200)."',
			username='".clean_text($_POST['username'],100)."',
			password='".clean_text($_POST['password'],100)."',
			alarm_pin='".clean_text($_POST['pin'],11)."',
			type='".clean_text($_POST['account_type'],50)."',
			enabled='".(int)clean_text($_POST['enabled'],1)."',
			decription='".clean_text($_POST['description'],200)."',
			user_img='".$image."',
			user_workAddress='".clean_text($_POST['user_workAddress'],100)."',
			user_workLatLong='',
			room_assign='".(int)clean_text($_POST['room_assign'],11)."',
			user_permissions='".$permissions."'
			WHERE ID='".clean_text($_POST['ID'],11)."'";
			mysqli_query($GS_DBCONN, $insert_query);
		}else{
			echo "<script>alert('You Do Not Have The Right Permissions To Edit This User');</script>";
		}
		
		echo "<script>location.href='';</script>";
	}
	
	//user location settings 
	if(temp_decode($_POST['type'])=="UserLocationSettings" && GetUserPermissions("edit")==true){
		
		if($_POST['checkMethod']=="icloud"){
			$checkString="|".$_POST['apple_id']."|".$_POST['apple_password']."||".$_POST['geolocation_radius'];
		}
		
		$insert_query="UPDATE whoishome SET check_method='".$_POST['checkMethod']."',check_string='".$checkString."',enabled='".(int)$_POST['geolocation_enable']."' WHERE ID='".$_POST['ID']."'";
		mysqli_query($GS_DBCONN, $insert_query);
		
		$insert_query="UPDATE users SET user_workAddress='".$_POST['work_address']."' WHERE ID='".$_POST['user_id']."'";
		mysqli_query($GS_DBCONN, $insert_query);
	}
	
?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px;margin-bottom:30px;">
    <h3>Manage Users</h3>
    <div class="col-md-4 email-list1" style="max-width:380px;">
        <div class="collection" style="background-color:#fff;padding:10px;padding:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
            <div class="collection-item avatar email-unread">
                <div class="">
                    <form method="Post">
                        <h3 style="text-align:center;width:100%;color:#000;font-size:20px;">Add User</h3>
                        <input type="hidden" name="type" value="<?php echo temp_encode("add_user");?>" />
                        <div class="input-group">
                            <span class="input-group-addon">Name</span>
                            <input type="text" name="name" value="" placeholder="Name" class="form-control1" style="display:block;width:100%;" />
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input type="text" name="username" value="" placeholder="Username" class="form-control1" style="display:block;width:100%;" />
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input type="text" name="password" value="" placeholder="Password" class="form-control1" style="display:block;width:100%;" />
                        </div>
                        <select name="acctype" class="form-control1" style="display:block;width:100%;margin-bottom:10px;" />
                            <option value="Guest">Guest</option>
                            <option value="Family">Family</option>
                            <option value="Admin">Admin</option>
                        </select>
                        <input type="text" name="decription" value="" placeholder="Decription" class="form-control1" style="display:block;width:100%;margin-bottom:10px;" />
                        <?php if((GetUserPermissions("add")==true) || $permis_result['type']=="Admin"):?>
							<input type="submit" value="Add" class="btn btn-primary" style="width:90px;display:inline-block;" />
                        <?php else:?>
							<input type="button" value="Add" class="btn btn-primary" style="width:90px;display:inline-block;" disabled title="You do not have permission"/>
                        <?php endif;?>
                        <input type="reset" value="Reset" class="btn btn-default" style="display:inline-block;margin-left:5px;" />
                    </form>
                </div>
            </div>
        </div>
        <div class="content-box  mrg15B" style="margin-bottom:10px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
            <div class="content-box-wrapper text-center">
                <h4 class="content-box-header">
                    At Home
                </h4>
                <?php
                    $count = 0;
                    $query = "SELECT * FROM users WHERE enabled='1' ORDER BY ID ASC";
                    $results = mysqli_query($GS_DBCONN, $query);
                    while($result = mysqli_fetch_assoc($results)) { $count++;   
                    	$query = "SELECT * FROM whoishome WHERE user_id='".$result['ID']."'";
                    	$usersHome = mysqli_query($GS_DBCONN, $query);
                    	$isHome = mysqli_fetch_assoc($usersHome);
                    	
                    	$check_string = explode("|", $isHome['check_string']);
                   ?>
	                <div class="status-badge mrg10A">
	                    <a href="#" title="View" data-toggle="modal" data-target="#UserLocationSettings_<?php echo $isHome['ID'];?>">
							<?php if($result['user_img']!=""):?>
								<img class="img-circle" width="50" height="50" src="images/users/<?php echo $result['user_img'];?>" alt="">
							<?php else :?>
								<img class="img-circle" width="50"height="50" src="images/users/Default.png"/>
							<?php endif;?>
						</a>
	                    <?php if($isHome['home']=="1") :?>
							<div class="small-badge bg-green1"></div>
	                    <?php else:?>
							<div class="small-badge bg-red"></div>
	                    <?php endif;?>
	                </div>
	                <!-- Modal User Location Settings -->
	                <div class="modal fade" id="UserLocationSettings_<?php echo $isHome['ID'];?>" role="dialog" style="z-index:9999;margin-top:100px;">
	                    <div class="modal-dialog">
	                        <!-- Modal content-->
	                        <div class="modal-content" style="width:400px;">
	                            <div class="modal-header">
	                                <button type="button" class="close" data-dismiss="modal">&times;</button>
	                                <h4 style="" class="modal-title">Location Settings</h4>
	                            </div>
	                            <div class="modal-body" style="height:430px;overflow:auto;">
	                                <form method="post" id="UserGeolocationFrm<?php echo $isHome['ID'];?>">
	                                    <input type="hidden" name="type" value="<?php echo temp_encode("UserLocationSettings");?>"/>
	                                    <input type="hidden" name="ID" value="<?php echo $isHome['ID'];?>"/>
	                                    <input type="hidden" name="user_id" value="<?php echo $isHome['user_id'];?>"/>
	                                    <div style="" id="UserLocationSelectType">
	                                        <label style="display:block;"><b>Check Method:</b></label>
	                                        <select class="form-control1" style="height:30px;width:100%;" name="checkMethod" id="UserLocationCheckMethod<?php echo $isHome['ID'];?>" onchange="userLocationCheckMethod<?php echo $isHome['ID'];?>()">
	                                            <option Value="">Not Set</option>
	                                            <option Value="icloud">iCloud</option>
	                                        </select>
	                                    </div>
	                                    <div style="display:none;" id="UserLocationIcloud<?php echo $isHome['ID'];?>">
	                                        <label style="text-align:left;display:block;"><b>Apple ID:</b></label>
	                                        <input class="form-control1" style="height:30px;width:100%;" type="text" name="apple_id" value="<?php echo $check_string[1];?>"/>
	                                        <label style="text-align:left;display:block;"><b>Password:</b></label>
	                                        <input class="form-control1" style="height:30px;width:100%;" type="password" name="apple_password" value="<?php echo $check_string[2];?>"/>
	                                    
	                                        <label style="text-align:left;display:block;"><b>Work Address:</b></label>
	                                        <input class="form-control1" style="height:30px;width:100%;" type="text" name="work_address" value="<?php echo $result['user_workAddress'];?>"/>
	                                        <label style="text-align:left;display:block;"><b>Geolocation Distance:</b></label>
	                                        <input placeholder="0.3" class="form-control1" style="height:30px;width:100%;" type="text" name="geolocation_radius" value="<?php echo $check_string[4];?>"/>
	                                    </div>
	                                    <script>
	                                        function userLocationCheckMethod<?php echo $isHome['ID'];?>(){
	                                        	$("#UserLocationIcloud<?php echo $isHome['ID'];?>").hide();
	                                        	
	                                        	if($("#UserLocationCheckMethod<?php echo $isHome['ID'];?>").val()=="icloud"){ //iCloud
	                                        		$("#UserLocationIcloud<?php echo $isHome['ID'];?>").show();
	                                        	}
	                                        }
	                                        
	                                        //set to values stored in db
	                                        $('#UserLocationCheckMethod<?php echo $isHome['ID'];?>').val('<?php echo $isHome['check_method'];?>');
	                                        userLocationCheckMethod<?php echo $isHome['ID'];?>();
	                                    </script>
	                                    <div>
	                                        <label style="margin-top:20px;">
	                                        <b>Enable Location Services:</b>&nbsp;&nbsp;
	                                        <input type="checkbox" data-toggle="toggle" data-size="small" name="geolocation_enable" value="1" <?php if($isHome['enabled']=="1"):?>checked<?php endif;?>/>
	                                        </label>
	                                    </div>
	                                    <?php if(GetUserPermissions("edit")==true || $permis_result['type']=="Admin"):?>
											<button type="submit" style="width:58%;margin-top:20px;" class="btn btn-default" >Save</button>
	                                    <?php else:?>
											<button disabled type="button" style="width:58%;margin-top:20px;" class="btn btn-default" title="You do not have permission">Save</button>
	                                    <?php endif;?>
	                                    <?php if(GetUserPermissions("edit")==true):?>
											<a href="?deleteSqueezeBox=<?php echo temp_encode($server['ID']);?>">
												<button type="button" style="width:40%;margin-top:20px;" class="btn btn-danger">Delete <i class="fa fa-trash-o"></i></button>
											</a>
	                                    <?php else :?>
											<button disabled type="button" style="width:40%;margin-top:20px;" class="btn btn-danger" title="You do not have permission">Delete <i class="fa fa-trash-o"></i></button>
	                                    <?php endif;?>
	                                </form>
	                            </div>
	                        </div>
	                    </div>
	                </div>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 inbox_right" style="overflow:auto;">
        <form action="#" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control1 input-search" placeholder="Search..." value="<?php echo $_GET['search'];?>">
                <span class="input-group-btn">
                <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        <?php
            // Find out how many items are in the table
            if($_GET['search']!=""){
            	$total = mysqli_num_rows(mysqli_query($GS_DBCONN, "SELECT * FROM users WHERE (user_name LIKE '%".$_GET['search']."%' OR username LIKE '%".$_GET['search']."%')"));
            }else{
            	$total = mysqli_num_rows(mysqli_query($GS_DBCONN, 'SELECT * FROM  users'));
            }
            // How many items to list per page
            $limit = 30;
            // How many pages will there be
            $pages = ceil($total / $limit);
            // What page are we currently on?
            $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array('options' => array('default'   => 1,'min_range' => 1))));
            // Calculate the offset for the query
            $offset = ($page - 1)  * $limit;
            // Some information to display to the user
            $start = $offset + 1;
            $end = min(($offset + $limit), $total);
            if($_GET['search']!=""){
            	// The "back" link
            	$prevlink = ($page > 1) ? "
            	<a style='font-size:20px;' href='?page=1' title='Next page'><i class='fa fa fa-step-backward'></i></a>&nbsp;
            	<a style='font-size:25px;' href='?page=".($page - 1)."' title='Last page'><i class='fa fa-caret-left'></i></a>"
            	:"";
            	// The "forward" link
            	$nextlink = ($page < $pages) ? "
            	<a style='font-size:25px;' href='?page=".($page + 1)."' title='Next page'><i class='fa fa fa-caret-right'></i></a>&nbsp;
            	<a style='font-size:20px;' href='?page=".$pages."' title='Last page'><i class='fa fa-step-forward'></i></a>"
            	:"";
            }else{
            	// The "back" link
            	$prevlink = ($page > 1) ? "
            	<a style='font-size:20px;' href='?search=".$_GET['search']."&page=1' title='Next page'><i class='fa fa fa-step-backward'></i></a>&nbsp;
            	<a style='font-size:25px;' href='?search=".$_GET['search']."&page=".($page - 1)."' title='Last page'><i class='fa fa-caret-left'></i></a>"
            	:"";
            	// The "forward" link
            	$nextlink = ($page < $pages) ? "
            	<a style='font-size:25px;' href='?search=".$_GET['search']."&page=".($page + 1)."' title='Next page'><i class='fa fa fa-caret-right'></i></a>&nbsp;
            	<a style='font-size:20px;' href='?search=".$_GET['search']."&page=".$pages."' title='Last page'><i class='fa fa-step-forward'></i></a>"
            	:"";
            }
            // Display the paging information
            echo "<center style='margin-bottom:15px;'><p style='font-size:18px;'><b>".$prevlink." Page ".$page." of ".$pages." ".$nextlink."</b></p></center>";
        ?>
        <?php
            $count = 0;
            if($_GET['search']!=""){
            	$query = "SELECT * FROM users WHERE (user_name LIKE '%".$_GET['search']."%' OR username LIKE '%".$_GET['search']."%') ORDER BY ID LIMIT ".$limit." OFFSET ".$offset;
            }else{
            	$query = "SELECT * FROM users ORDER BY ID LIMIT ".$limit." OFFSET ".$offset;
            }
            $results = mysqli_query($GS_DBCONN, $query);
            while($result = mysqli_fetch_assoc($results)) { $count++;   	
            ?>
        <form method="POST" enctype="multipart/form-data" style="display:inline-block;">
            <input type="hidden" name="type" value="<?php echo temp_encode("update_users");?>"/>
            <div style="width:220px;height:200px;background-color:#fff;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin-right:10px;margin-bottom:15px;padding:10px;display:inline-block;">
                <a href="#" data-toggle="modal" data-target="#userSettings_<?php echo $result['ID'];?>" style="background-color:#fff;width:30px;padding:5px;position:absolute;border-radius:4px;text-align:center;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin-top:-20px;margin-left:-20px;"><i class="fa fa-cog"></i></a>
                <?php if($result['user_img']!=""):?>
					<img src="images/users/<?php echo $result['user_img'];?>" title="<?php echo $result['user_name'];?>" style="width:200px;height:150px;"/>
                <?php else :?>
					<img src="images/users/Default.png" style="width:200px;height:150px;"/>
                <?php endif;?>
                <div style="display:inline-block;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);width:100%;height:30px;padding:4px;">
                    <?php echo $result['user_name'];?>
                </div>
            </div>
            <script>
                function showmodal<?php echo $result['ID'];?>() {
                	$('#modal_bkg_<?php echo $result['ID'];?>').fadeIn();
                	$('#modal_settings_<?php echo $result['ID'];?>').fadeIn();
                }
                function hidemodal<?php echo $result['ID'];?>() {
                	$('#modal_bkg_<?php echo $result['ID'];?>').fadeOut();
                	$('#modal_settings_<?php echo $result['ID'];?>').fadeOut();
                }
            </script>
            <!--########################################### Manage Users Modal ############################# -->
            <!-- Modal -->
            <div class="modal fade" id="userSettings_<?php echo $result['ID'];?>" role="dialog" style="z-index:9999;margin-top:100px;">
                <div class="modal-dialog" style="width:780px;">
                    <!-- Modal content-->
                    <div class="modal-content" style="background-color:#f2f4f8;width:800x;overflow:auto;height:600px;">
                        <div class="modal-header" style="background-color:#fff;" >
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Edit User: <?php echo $result['user_name'];?></h4>
                        </div>
                        <div class="modal-body" style="background-color:#f2f4f8;overflow:auto;width:780px;">
                            <div class="modal-body" style="display:inline-block;overflow:auto;width:310px;height:400px;background-color:#fff;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
                                <input type="hidden" name="ID" value="<?php echo $result['ID'];?>"/>
                                <div class="input-group">
                                    <span class="input-group-addon">Name</span>
                                    <input type="text" name="user_name" class="form-control1" value="<?php echo $result['user_name'];?>" style="height:30px;width:100%;"/>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input type="text" name="username" class="form-control1" value="<?php echo $result['username'];?>" style="height:30px;width:100%;"/>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control1" value="<?php echo $result['password'];?>" style="height:30px;width:150px;margin-right:10px;" id="user_password<?php echo $result['ID'];?>"/>
                                    <?php if (LF_Checkrights_MU($result['type'])==true):?>
										<input type="checkbox" data-toggle="toggle" data-size="small" data-on="Show" data-off="Hide" onchange="if($(this).prop('checked')==true){$('#user_password<?php echo $result['ID'];?>').attr('type','text');}else{$('#user_password<?php echo $result['ID'];?>').attr('type','password');}">
                                    <?php endif;?>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">Pin</span> 
                                    <input type="password" name="pin" maxLength="11" class="form-control1" value="<?php echo $result['alarm_pin'];?>" style="height:30px;width:140px;margin-right:10px;" id="user_pin<?php echo $result['ID'];?>"/>
                                    <?php if (LF_Checkrights_MU($result['type'])==true):?>
										<input type="checkbox" data-toggle="toggle" data-size="small" data-on="Show" data-off="Hide" onchange="if($(this).prop('checked')==true){$('#user_pin<?php echo $result['ID'];?>').attr('type','text');}else{$('#user_pin<?php echo $result['ID'];?>').attr('type','password');}">
                                    <?php endif;?>
                                </div>
                                <?php if($_SESSION['type']=="Admin" || $result['ID']!=$_SESSION['id']) :?>
									<label for="" style="display:block;"><b>Account Type:</b></label>
									<select name="account_type" class="form-control1" style="height:30px;width:100%;margin-bottom:10px;"id="account_type<?php echo $result['ID'];?>">
										<option value="Guest">Guest</option>
										<option value="Family">Family</option>
										<option value="Admin">Admin</option>
									</select>
                                <?php else:?>
									<label style="display:block;"><b>Account Type:</b> <?php echo $result['type'];?></label> <br/>
                                <?php endif;?>
                                <label for="" style="display:block;"><b>User Image:</b></label>
                                <input type="file" name="user_img" class="form-control1" style="width:100%;margin-bottom:10px;">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-bed"></i></span>
                                    <select name="room_assign" class="form-control1" style="height:30px;width:100%;" id="room<?php echo $result['ID'];?>">
                                        <option value="0">No Room</option>
										<?php 
                                            $query = "SELECT * FROM home_rooms ORDER BY ID ASC";
                                            $rooms = mysqli_query($GS_DBCONN, $query);
                                            while($room_list = mysqli_fetch_assoc($rooms)){
										?>
											<option value="<?php echo $room_list['ID'];?>"><?php echo $room_list['room_name'];?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-briefcase"></i></span>
                                    <input type="text" name="user_workAddress" value="<?php echo $result['user_workAddress'];?>" class="form-control1" style="height:30px;width:100%;"/>
                                </div>
                                <label for="" style=""><b>Enabled:</b></label>
                                <input type="checkbox" data-toggle="toggle" data-size="small" id="enabled<?php echo $result['ID'];?>" name="enabled" value="1" style="display:inline;margin-bottom:10px;margin-left:10px;margin-right:20px;"/>
                            </div>
                            <div class="modal-body" style="display:inline-block;overflow:auto;width:430px;height:400px;background-color:#fff;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
                                <div style="top:70px;background-color:#fff;padding:5px;position:fixed;overflow:hidden;width:400px;">
                                    <h4>Permissions</h4>
                                    <style> 
                                        table { table-layout: fixed;}
                                        table td { overflow: hidden;}
                                    </style>
                                    <table style="font-size:14px;">
                                        <col width="170">
                                        <col width="50">
                                        <col width="50">
                                        <col width="50">
                                        <col width="50">
                                        <tr>
                                            <th>Name</th>
                                            <th>Read</th>
                                            <th>Edit</th>
                                            <th>Add</th>
                                            <th>Delete</th>
                                        </tr>
                                    </table>
                                </div>
                                <table class="" style="font-size:14px;margin-top:50px;">
                                    <col width="180">
                                    <col width="50">
                                    <col width="50">
                                    <col width="50">
                                    <col width="50">
                                    <tr style="height:30px;">
                                        <td><b>Select All</b></td>
                                        <td><input type="checkbox" onchange="$('.checkboxRead_<?php echo $result['ID'];?>').prop('checked',$(this).prop('checked'));"/></td>
                                        <td><input type="checkbox" onchange="$('.checkboxEdit_<?php echo $result['ID'];?>').prop('checked',$(this).prop('checked'));"/></td>
                                        <td><input type="checkbox" onchange="$('.checkboxAdd_<?php echo $result['ID'];?>').prop('checked',$(this).prop('checked'));"/></td>
                                        <td><input type="checkbox" onchange="$('.checkboxDelete_<?php echo $result['ID'];?>').prop('checked',$(this).prop('checked'));"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Alarm Access:</td>
                                        <td><input type="checkbox" name="alarm_access_read" checked disabled id="alarm_access_read<?php echo $result['ID'];?>" value=":alarm_access|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="alarm_access_edit" id="alarm_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Dashboard Access:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="dashboard_access_read" id="dashboard_access_read<?php echo $result['ID'];?>" value=":index.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="dashboard_access_edit" id="dashboard_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="dashboard_access_add" id="dashboard_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="dashboard_access_delete" id="dashboard_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td><a href="#" onclick="showPermisSettings<?php echo $result['ID'];?>('Room');" data-toggle="modal" data-target="#userSettings_permis_<?php echo $result['ID'];?>"><u>Room Access:</u></a></td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="room_access_read" id="room_access_read<?php echo $result['ID'];?>" value=":manage_room.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="room_access_edit" id="room_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="room_access_add" id="room_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="room_access_delete" id="room_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td><a href="#" onclick="showPermisSettings<?php echo $result['ID'];?>('Scene')" data-toggle="modal" data-target="#userSettings_permis_<?php echo $result['ID'];?>"><u>Scene Access:</u></a></td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="scene_access_read" id="scene_access_read<?php echo $result['ID'];?>" value=":manage_scene.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="scene_access_edit" id="scene_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="scene_access_add" id="scene_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="scene_access_delete" id="scene_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>IFTTT Access:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="ifttt_access_read" id="ifttt_access_read<?php echo $result['ID'];?>" value=":ifttt_simple.php|read|:ifttt_advanced.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="ifttt_access_edit" id="ifttt_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="ifttt_access_add" id="ifttt_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="ifttt_access_delete" id="ifttt_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Manage Buttons:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="buttons_access_read" id="buttons_access_read<?php echo $result['ID'];?>" value=":manage_buttons.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="buttons_access_edit" id="buttons_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="buttons_access_add" id="buttons_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="buttons_access_delete" id="buttons_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Manage Devices:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="device_access_read" id="device_access_read<?php echo $result['ID'];?>" value=":manage_devices.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="device_access_edit" id="device_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="device_access_add" id="device_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="device_access_delete" id="device_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Manage Sensors:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="sensor_access_read" id="sensor_access_read<?php echo $result['ID'];?>" value=":manage_sensors.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="sensor_access_edit" id="sensor_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="sensor_access_add" id="sensor_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="sensor_access_delete" id="sensor_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
									<tr style="height:30px;">
                                        <td>Manage Data Sensors:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="dataSensor_access_read" id="dataSensor_access_read<?php echo $result['ID'];?>" value=":manage_dataSensors.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="dataSensor_access_edit" id="dataSensor_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="dataSensor_access_add" id="dataSensor_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="dataSensor_access_delete" id="dataSensor_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Manage Music:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="music_access_read" id="music_access_read<?php echo $result['ID'];?>" value=":manage_music.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="music_access_edit" id="music_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="music_access_add" id="music_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="music_access_delete" id="music_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td><a href="#" onclick="showPermisSettings<?php echo $result['ID'];?>('Camera');" data-toggle="modal" data-target="#userSettings_permis_<?php echo $result['ID'];?>"><u>Camera Access:</u></a></td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="camera_access_read" id="camera_access_read<?php echo $result['ID'];?>" value=":manage_cameras.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="camera_access_edit" id="camera_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="camera_access_add" id="camera_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="camera_access_delete" id="camera_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Event Log Access:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="log_access_read" id="log_access_read<?php echo $result['ID'];?>" value=":events_log.php|read|"/></td>
                                        <td><input type="checkbox" name="log_access_edit" id="log_access_edit<?php echo $result['ID'];?>" value="edit|" disabled /></td>
                                        <td><input type="checkbox" name="log_access_add" id="log_access_add<?php echo $result['ID'];?>" value="add|" disabled /></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="log_access_delete" id="log_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Device Log Access:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="DeviceHistoryLog_access_read" id="DeviceHistoryLog_access_read<?php echo $result['ID'];?>" value=":device_history_log.php|read|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Sensor Log Access:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="SensorHistoryLog_access_read" id="SensorHistoryLog_access_read<?php echo $result['ID'];?>" value=":sensor_history_log.php|read|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>Manage Users:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="users_access_read" id="users_access_read<?php echo $result['ID'];?>" value=":manage_users.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="users_access_edit" id="users_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                        <td><input class="checkboxAdd_<?php echo $result['ID'];?>" type="checkbox" name="users_access_add" id="users_access_add<?php echo $result['ID'];?>" value="add|"/></td>
                                        <td><input class="checkboxDelete_<?php echo $result['ID'];?>" type="checkbox" name="users_access_delete" id="users_access_delete<?php echo $result['ID'];?>" value="delete|"/></td>
                                    </tr>
                                    <tr style="height:30px;">
                                        <td>SmartHome Settings:</td>
                                        <td><input class="checkboxRead_<?php echo $result['ID'];?>" type="checkbox" name="settings_access_read" id="settings_access_read<?php echo $result['ID'];?>" value=":manage_settings.php|read|"/></td>
                                        <td><input class="checkboxEdit_<?php echo $result['ID'];?>" type="checkbox" name="settings_access_edit" id="settings_access_edit<?php echo $result['ID'];?>" value="edit|"/></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-body" style="text-align:center;font-size:14px;overflow:auto;width:99%;background-color:#fff;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);padding:4px;">
                                <b>Description:</b>&nbsp;
                                <input type="text" name="description" class="form-control1" style="height:30px;width:620px;" value="<?php echo $result['decription'];?>" maxLength="200"/>
                            </div>
                        </div>
                        <script>
                            <?php if ($result['enabled']=='1') :?>
                            	$('#enabled<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            
                            $('#account_type<?php echo $result['ID'];?>').val('<?php echo $result['type'];?>');
                            $('#room<?php echo $result['ID'];?>').val("<?php echo $result['room_assign'];?>");
                            
                            /* check the checkboxes where the user has permissions */
                            <?php if(GetUserPermissions("read","alarm_access",$result['user_permissions'])==true) :?>
                            	$('#alarm_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","alarm_access",$result['user_permissions'])==true) :?>
                            	$('#alarm_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*Dashboard */
                            <?php if(GetUserPermissions("read","index.php",$result['user_permissions'])==true) :?>
                            	$('#dashboard_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","index.php",$result['user_permissions'])==true) :?>
                            	$('#dashboard_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","index.php",$result['user_permissions'])==true) :?>
                            	$('#dashboard_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","index.php",$result['user_permissions'])==true) :?>
                            	$('#dashboard_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*Mange Room */
                            <?php if(GetUserPermissions("read","manage_room.php",$result['user_permissions'])==true) :?>
                            	$('#room_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_room.php",$result['user_permissions'])==true) :?>
                            	$('#room_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_room.php",$result['user_permissions'])==true) :?>
                            	$('#room_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_room.php",$result['user_permissions'])==true) :?>
                            	$('#room_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*Manage Scenes */
                            <?php if(GetUserPermissions("read","manage_scene.php",$result['user_permissions'])==true) :?>
                            	$('#scene_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_scene.php",$result['user_permissions'])==true) :?>
                            	$('#scene_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_scene.php",$result['user_permissions'])==true) :?>
                            	$('#scene_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_scene.php",$result['user_permissions'])==true) :?>
                            	$('#scene_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*IFTTT */
                            <?php if(GetUserPermissions("read","ifttt_simple.php",$result['user_permissions'])==true) :?>
                            	$('#ifttt_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","ifttt_advanced.php",$result['user_permissions'])==true) :?>
                            	$('#ifttt_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","ifttt_advanced.php",$result['user_permissions'])==true) :?>
                            	$('#ifttt_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","ifttt_advanced.php",$result['user_permissions'])==true) :?>
                            	$('#ifttt_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*Manage Buttons */
                            <?php if(GetUserPermissions("read","manage_buttons.php",$result['user_permissions'])==true) :?>
                            	$('#buttons_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_buttons.php",$result['user_permissions'])==true) :?>
                            	$('#buttons_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_buttons.php",$result['user_permissions'])==true) :?>
                            	$('#buttons_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_buttons.php",$result['user_permissions'])==true) :?>
                            	$('#buttons_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*manage Devices */
                            <?php if(GetUserPermissions("read","manage_devices.php",$result['user_permissions'])==true) :?>
                            	$('#device_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_devices.php",$result['user_permissions'])==true) :?>
                            	$('#device_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_devices.php",$result['user_permissions'])==true) :?>
                            	$('#device_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_devices.php",$result['user_permissions'])==true) :?>
                            	$('#device_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*Manage Sensors */
                            <?php if(GetUserPermissions("read","manage_sensors.php",$result['user_permissions'])==true) :?>
                            	$('#sensor_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_sensors.php",$result['user_permissions'])==true) :?>
                            	$('#sensor_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_sensors.php",$result['user_permissions'])==true) :?>
                            	$('#sensor_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_sensors.php",$result['user_permissions'])==true) :?>
                            	$('#sensor_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
							/*Manage Data Sensors */
                            <?php if(GetUserPermissions("read","manage_dataSensors.php",$result['user_permissions'])==true) :?>
                            	$('#dataSensor_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_dataSensors.php",$result['user_permissions'])==true) :?>
                            	$('#dataSensor_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_dataSensors.php",$result['user_permissions'])==true) :?>
                            	$('#dataSensor_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_dataSensors.php",$result['user_permissions'])==true) :?>
                            	$('#dataSensor_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*manage Music */
                            <?php if(GetUserPermissions("read","manage_music.php",$result['user_permissions'])==true) :?>
                            	$('#music_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_music.php",$result['user_permissions'])==true) :?>
                            	$('#music_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_music.php",$result['user_permissions'])==true) :?>
                            	$('#music_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_music.php",$result['user_permissions'])==true) :?>
                            	$('#music_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*Manage Cameras */
                            <?php if(GetUserPermissions("read","manage_cameras.php",$result['user_permissions'])==true) :?>
                            	$('#camera_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_cameras.php",$result['user_permissions'])==true) :?>
                            	$('#camera_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_cameras.php",$result['user_permissions'])==true) :?>
                            	$('#camera_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_cameras.php",$result['user_permissions'])==true) :?>
                            	$('#camera_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*events Log */
                            <?php if(GetUserPermissions("read","events_log.php",$result['user_permissions'])==true) :?>
                            	$('#log_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","events_log.php",$result['user_permissions'])==true) :?>
                            	$('#log_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","events_log.php",$result['user_permissions'])==true) :?>
                            	$('#log_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","events_log.php",$result['user_permissions'])==true) :?>
                            	$('#log_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*history log  */
                            <?php if(GetUserPermissions("read","device_history_log.php",$result['user_permissions'])==true) :?>
                            	$('#DeviceHistoryLog_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("read","sensor_history_log.php",$result['user_permissions'])==true) :?>
                            	$('#SensorHistoryLog_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /*manage users */
                            <?php if(GetUserPermissions("read","manage_users.php",$result['user_permissions'])==true) :?>
                            	$('#users_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_users.php",$result['user_permissions'])==true) :?>
                            	$('#users_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("add","manage_users.php",$result['user_permissions'])==true) :?>
                            	$('#users_access_add<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("delete","manage_users.php",$result['user_permissions'])==true) :?>
                            	$('#users_access_delete<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            /* Settings */
                            <?php if(GetUserPermissions("read","manage_settings.php",$result['user_permissions'])==true) :?>
                            	$('#settings_access_read<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                            <?php if(GetUserPermissions("edit","manage_settings.php",$result['user_permissions'])==true) :?>
                            	$('#settings_access_edit<?php echo $result['ID'];?>').prop("checked", "true");
                            <?php endif;?>
                        </script>
                        <div class="modal-footer" style="background-color:#fff;">
                            <?php if (LF_Checkrights_MU($result['type'])==true):?>
								<?php if(GetUserPermissions("delete")==true || $permis_result['type']=="Admin"):?>
									<a href="?delete=<?php echo temp_encode($result['ID']);?>">
										<button class="btn btn-danger" style="float:left;margin-left:10px;" type="button" onclick="return confirm('Are You Sure You Want To Delete The User: <?php echo $result['user_name'];?>?');"><i class="fa fa-trash-o"></i> Delete</button>
									</a>
								<?php else:?>
									<button class="btn btn-danger" style="float:left;margin-left:10px;" type="button" disabled title="You do not have permission"><i class="fa fa-trash-o"></i> Delete</button>
								<?php endif;?>
								
								<?php if(GetUserPermissions("edit")==true || $permis_result['type']=="Admin"):?>
									<button type="submit" class="btn btn-primary">Save</button>
								<?php else:?>
									<button type="button" class="btn btn-primary" disabled title="You do not have permission">Save</button>
								<?php endif;?>
                            <?php else :?>
								<p style="float:left;color:red;font-size:12px;padding:5px;"><b>You Do Not Have Permission To Edit This User</b></p>
                            <?php endif;?>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--###################################################################################################### -->
            <!--########################################### Manage Users->extra Modal ############################# -->
            <!-- Modal -->
            <div class="modal fade" id="userSettings_permis_<?php echo $result['ID'];?>" role="dialog" style="z-index:9999;margin-top:100px;">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content" style="background-color:#f2f4f8;">
                        <div class="modal-header" style="background-color:#fff;" >
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Edit Permissions: <?php echo $result['user_name'];?></h4>
                        </div>
                        <div class="modal-body" style="background-color:#f2f4f8;">
                            <div class="modal-body" style="overflow:auto;width:300px;height:350px;background-color:#fff;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);float:left;">
                                <!---------- Room -------->
                                <table style="display:none;" id="RoomPermisModal<?php echo $result['ID'];?>">
                                    <th>Room Name</th>
                                    <th>Allow</th>
                                    <tr>
                                        <td style="width:300px;"><b>Select All</b></td>
                                        <td><input type="checkbox" onchange="$('.room_permis_<?php echo $result['ID'];?>').prop('checked',$(this).prop('checked'));" style="display:inline;margin-bottom:10px;margin-left:10px;margin-right:20px;"/></td>
                                    </tr>
                                    <?php 
                                        $query = "SELECT * FROM home_rooms ORDER BY ID ASC";
                                        $rooms = mysqli_query($GS_DBCONN, $query);
                                        while($room_list = mysqli_fetch_assoc($rooms)){
									?>
										<tr style="height:30px;">
											<td style="width:300px;"><?php echo $room_list['room_name'];?></td>
											<td><input class="room_permis_<?php echo $result['ID'];?>" type="checkbox" id="room_permis_<?php echo $room_list['ID'];?><?php echo $result['ID'];?>" name="room_permis_<?php echo $room_list['ID'];?>" value="<?php echo $room_list['ID']."|";?>" style="display:inline;margin-bottom:10px;margin-left:10px;margin-right:20px;"/></td>
										</tr>
                                    <?php }?>
                                </table>
                                <!--------- Camera -------> 
                                <table style="display:none;" id="CameraPermisModal<?php echo $result['ID'];?>">
                                    <th>Camera Name</th>
                                    <th>Allow</th>
                                    <tr>
                                        <td style="width:300px;"><b>Select All</b></td>
                                        <td><input type="checkbox" onchange="$('.camera_permis_<?php echo $result['ID'];?>').prop('checked',$(this).prop('checked'));" style="display:inline;margin-bottom:10px;margin-left:10px;margin-right:20px;"/></td>
                                    </tr>
                                    <?php 
                                        $query = "SELECT * FROM camera_list ORDER BY ID ASC";
                                        $cameras = mysqli_query($GS_DBCONN, $query);
                                        while($camera_list = mysqli_fetch_assoc($cameras)){
									?>
										<tr style="height:30px;">
											<td style="width:300px;"><?php echo $camera_list['camera_name'];?></td>
											<td><input class="camera_permis_<?php echo $result['ID'];?>" type="checkbox" id="camera_permis_<?php echo $camera_list['ID'];?><?php echo $result['ID'];?>" name="camera_permis_<?php echo $camera_list['ID'];?>" value="<?php echo $camera_list['ID']."|";?>" style="display:inline;margin-bottom:10px;margin-left:10px;margin-right:20px;"/></td>
										</tr>
                                    <?php }?>
                                </table>
                                <!----- Scenes ----->
                                <table style="display:none;" id="ScenePermisModal<?php echo $result['ID'];?>">
                                    <th>Scene Name</th>
                                    <th>Allow</th>
                                    <tr>
                                        <td style="width:300px;"><b>Select All</b></td>
                                        <td><input type="checkbox" onchange="$('.scene_permis_<?php echo $result['ID'];?>').prop('checked',$(this).prop('checked'));" style="display:inline;margin-bottom:10px;margin-left:10px;margin-right:20px;"/></td>
                                    </tr>
                                    <?php 
                                        $query = "SELECT * FROM scene ORDER BY ID ASC";
                                        $scenes = mysqli_query($GS_DBCONN, $query);
                                        while($scene_list = mysqli_fetch_assoc($scenes)){
									?>
										<tr style="height:30px;">
											<td style="width:300px;"><?php echo $scene_list['scene_name'];?></td>
											<td><input type="checkbox" class="scene_permis_<?php echo $result['ID'];?>" id="scene_permis_<?php echo $scene_list['ID'];?><?php echo $result['ID'];?>" name="scene_permis_<?php echo $scene_list['ID'];?>" value="<?php echo $scene_list['ID']."|";?>" style="display:inline;margin-bottom:10px;margin-left:10px;margin-right:20px;"/></td>
										</tr>
                                    <?php }?>
                                </table>
                                <script>
                                    <?php
                                        $query = "SELECT user_permissions FROM users WHERE ID='".$result['ID']."' AND enabled='1'";
                                        $permission_results = mysqli_query($GS_DBCONN, $query);
                                        $permis_result2 = mysqli_fetch_assoc($permission_results);
                                        
                                        $query = "SELECT * FROM home_rooms ORDER BY ID ASC ";
                                        $results1 = mysqli_query($GS_DBCONN, $query);
                                        while($result1 = mysqli_fetch_assoc($results1)) { 	
                                        	 if(GetUserPermissions($result1['ID'],"manage_room.php",$permis_result2['user_permissions'])==true) :?>
												$('#room_permis_<?php echo $result1['ID'];?><?php echo $result['ID'];?>').prop("checked", "true");
                                    	 <?php endif;?>
                                    <?php }?>
                                    
                                    <?php
                                        ///// Cameras	
                                        $query = "SELECT * FROM camera_list ORDER BY ID ASC ";
                                        $results2 = mysqli_query($GS_DBCONN, $query);
                                        while($result2 = mysqli_fetch_assoc($results2)) { 	
                                        	 if(GetUserPermissions($result2['ID'],"manage_cameras.php",$permis_result2['user_permissions'])==true) :?>
												$('#camera_permis_<?php echo $result2['ID'];?><?php echo $result['ID'];?>').prop("checked", "true");
                                    	 <?php endif;?>
                                    <?php }?>
                                    
                                    <?php
                                        //// Scene
                                        $query = "SELECT * FROM scene ORDER BY ID ASC ";
                                        $results3 = mysqli_query($GS_DBCONN, $query);
                                        while($result3 = mysqli_fetch_assoc($results3)) { 	
                                        	 if(GetUserPermissions($result3['ID'],"manage_scene.php",$permis_result2['user_permissions'])==true) :?>
												$('#scene_permis_<?php echo $result3['ID'];?><?php echo $result['ID'];?>').prop("checked", "true");
                                    	 <?php endif;?>
                                    <?php }?>
                                </script>
                            </div>
                        </div>
                        <div class="modal-footer" style="background-color:#fff;margin-top:350px;">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <script>
            function showPermisSettings<?php echo $result['ID'];?>(type){
            	$('#RoomPermisModal<?php echo $result['ID'];?>').hide();
            	$('#CameraPermisModal<?php echo $result['ID'];?>').hide();
            	$('#ScenePermisModal<?php echo $result['ID'];?>').hide();
            	if(type=="Room"){
            		$('#RoomPermisModal<?php echo $result['ID'];?>').show();
            	}
            	if(type=="Camera"){
            		$('#CameraPermisModal<?php echo $result['ID'];?>').show();
            	}
            	if(type=="Scene"){
            		$('#ScenePermisModal<?php echo $result['ID'];?>').show();
            	}
            }
        </script>
        <!--###################################################################################################### -->
        <?php }if($count==0){echo "<center><b>No Users Found</b></center>";} ?>
    </div>
</div>
<div class="clearfix"> </div>
<?php include("includes/footer.php");?>
<?php include("includes/modals.php");?>
</body>
</html>