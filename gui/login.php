<?php
    $GS_phueServiceBypass = true;
    $GS_wemoServiceBypass = true;
    $GS_mqttServiceBypass = true;
    $GS_emailServiceBypass = true;
    $GS_squeezeBoxServiceBypass = true;
    $GS_webIncludesIncluded = false;
    
    //Bypassed variables are passed to this include 
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/DBConn.php"));
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    if($_SESSION['id']!=""){echo "<script>location.href='index.php'</script>";exit;}
    
    //login is handeled by Autoload/Autoload_GetLogin.php
    
    if($_GET['autoload']=="true"){
    	exit;
    }
		
	function checkCookie($lognBool){
		global $GS_DBCONN;
		
		if(count($_COOKIE) > 0) {
			if($_COOKIE['lastLogin']!=""){ //Check if Cookies are Enabled
				$query = "SELECT * FROM users WHERE ID='".trim(safe_decode($_COOKIE['lastLogin']))."'";
				$results = mysqli_query($GS_DBCONN, $query);
				$result = mysqli_fetch_assoc($results);
				$result_count = mysqli_num_rows($results);
				
				if($result_count>0){
					return processLogin($result['username'], $result['password'], $lognBool);
				}
			}else{
				return false;
			}
		}else{// Cookies not Enabled
			return false;
		}
	}
	
	$LS_cookieData = explode("|",checkCookie(false)); //set to false to not login
	
	//Login Cached User
	if($_POST['type']=="LoginCachedUser"){
		checkCookie(true); //set to true to login
		echo "<script>location.href='index.php';</script>";
	}
    
    ?>
<!DOCTYPE HTML>
<html style="background-image:none;">
    <head>
        <title><?php echo $GS_Config['SiteName'];?> - Please Login</title>
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
        <link rel="manifest" href="images/favicons/manifest.json">
        <link rel="mask-icon" href="images/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-TileImage" content="images/favicons/mstile-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.3.3.7.min.css" rel='stylesheet' type='text/css' />
        <!-- Custom CSS -->
        <link href="css/style.less" rel='stylesheet/less' type='text/css' />
        <link href="css/font-awesome.css" rel="stylesheet">
        <link rel="stylesheet" href="css/font-awesome-animation.min.css">
        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <!----webfonts--->
        <link href='//fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
        <!---//webfonts--->
        <!-- Nav CSS -->
        <link href="css/custom.css" rel="stylesheet">
        <!--For BootStrap Modals-->
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>		
        <script src="js/bootstrap.3.3.7.min.js"></script>
        <script src="js/less.js"></script>	
    </head>
    <body style="background-color:rgb(242, 244, 248);height:100%;padding:15px;">
        <div style="background-color:#fff;;padding:20px;width:100%;max-width:550px;margin:0 auto;border-radius:5px;margin-top:100px;box-shadow:1px 1px 3px 2px rgba(0, 0, 0, 0.2);margin-bottom:10px;" id="logincontainer">
            <div id="logo" style="margin-top:-50px;margin-left:0px;">
                <div class="sh-logo">
                    <i class="fa fa-home" aria-hidden="true"></i>
                </div>
                <!-- user img -->
                <img id="userImg" src="" style="display:none;width:80px;height:80px;border-radius:100px;"/>
                <div style="display:none;margin-left:150px;margin-top:-60px;font-size:20px;font-weight:bold;" id="welcomeText">
                    <i style="font-weight:bold;font-size:22px;" class="fa fa-spinner fa-spin"></i>
                    <span id="username"></span>	
                </div>
            </div>
            <div style="width:100%;" id="loginForm">
                <div style="height:60px;width:100%;text-align:center;">
                    <span style="font-size:40px;color:#000;"><?php echo $GS_Config['SiteName'];?></span>
                </div>
				
				<?php if($LS_cookieData[0]!=""):?> <!-- Continue to login Modal -->
					<div class="app-cam" style="width:100%;min-width:200px;max-width:500px;text-align:center;">
						<form method="POST">
							<input type="hidden" name="type" value="LoginCachedUser"/>
							<!-- user img -->
							<div style="font-size:22px;font-weight:bold;display:inline-block;margin-left:20px;text-align:Center;padding:10px; background-color:#f2f2f2;border-radius:4px; box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.2);">
								
								<?php if($LS_cookieData[0]!="error"):?>
									<img id="userImg" src="images/users/<?php echo $LS_cookieData[2];?>" style="width:80px;height:80px;border-radius:100px;margin-right:20px;background-color:#fff;"/>
								
									<p style="display:inline-block;font-size:18px;"><?php echo $LS_cookieData[1];?></p><!-- Welcome Message -->
									<button type="submit" class="btn btn-primary" style="width:70%;height:40px;margin-top:20px;display:inline-block;">Continue</button>
									<a href="logout.php">
										<button type="button" class="btn btn-default" style="width:25%;height:40px;margin-top:20px;display:inline-block;">Logout</button>
									</a>
								<?php else:?>
									<?php echo $LS_cookieData[1];?>
									<a href="logout.php">
										<button type="button" class="btn btn-default" style="width:100%;height:40px;margin-top:20px;display:inline-block;">Logout</button>
									</a>
								<?php endif;?>
							</div>
						</form>
					</div>
				<?php else:?><!-- Username/Password login Modal -->
					<div class="app-cam" style="width:80%;min-width:200px;max-width:300px;">
						<form class="autoform">
							<p id="loginError" style="display:none;width:100%;text-align:center;background-color:rgb(239, 85, 58);font-size:14px;color:#fff;padding:4px;border-radius:4px;"></p>
							<input id="txtUsername" style="width:100%;" name="username" type="text" class="text" value="Username" onfocus="this.value='';" onblur="if(this.value=='') {this.value='Username';}">
							<input id="txtpassword" style="width:100%;" name="password" type="password" value="Password" onfocus="this.value='';" onblur="if (this.value=='') {this.value='Password';}">
							<button type="submit" class="btn btn-primary" style="width:100%;height:40px;" onclick="ProccessLogin();">Login</button>
						</form>
						<ul class="new" style="width:100%;">
							<li class="new_left">
								<p><a href="#" onclick="alert('Please Contact Your System Administrator.')"><u>Forgot Your Password?</u></a></p>
							</li>
							<div class="clearfix"></div>
						</ul>
					</div>
				<?php endif;?>

            </div>
        </div>
        <script>
            function ProccessLogin(){
            	
            	/*This proccesses the login and does the effects*/
            	$.get("Ajax/Ajax_GetLogin.php?username=" + $('#txtUsername').val() + "&password=" +$('#txtpassword').val(), function(data, status){
            		 data = data.split("|");
            		if(status=="success" && data!=""){
            			if(data[0]=="success"){
            				$('#username').text(data[1]);
            				$('#loginForm').fadeOut(200);
            				$("#welcomeText").show();
            				$("#logo").animate(
            					{"margin":"0 auto"},
            					{"top":"0px"},
            					{"bottom":"0px"},
            					{"left":"0px"},
            					{"right":"0px"}
            				);
            				$("#logincontainer").animate(
								{"height":"120px"},
								{"width":"100%"},300
            				);
            				$(".sh-logo").fadeOut(20);
            				$("#welcomeText").fadeIn();
            				$("#userImg").fadeIn();
            				if(data[2]!=""){
            					$("#userImg").attr("src", "images/users/"+data[2]);
            				}else{
            					$("#userImg").attr("src", "images/users/default.png");
            				}
            				
            				setTimeout(function(){location.href="index.php";},2000);
            			}else if(data[0]=="error"){
							$("#loginError").text(data[2]);
							$("#loginError").fadeIn();
						}
            		}else{
            			alert("Error Getting Login Information");
            			location.href="";
            		}
            	});
            	return false;
            }
            
            

            $(document).ready(function() {
				 $('.autoform').on('submit', function(e) {
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
		
        <div style="width:100%;max-width:550px;margin:0 auto;border-radius:5px;box-shadow:1px 1px 3px 2px rgba(0, 0, 0, 0.2);margin-bottom:10px;" >
            <?php include("includes/footer.php");?>
        </div>
    </body>
</html>