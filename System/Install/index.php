<?php
	include("Config.php");
	
	$configFile = fopen("Config.php", "a") or die("Unable to open file!");
	//step 3
	if($_POST['type']=="InstallSql"){
		$config = '';
		$config .= '########################################### Database Setup ###########################################'.PHP_EOL;
		$config .= '$GS_Config["MySqlServer"] = "'.$_POST['sqlSvr'].'";'.PHP_EOL;
		$config .= '$GS_Config["dbUsername"] = "'.$_POST['sqlUsername'].'";'.PHP_EOL;
		$config .= '$GS_Config["dbPassword"] = "'.$_POST['sqlPassword'].'";'.PHP_EOL;
		$config .= '$GS_Config["dbName"] = "smarthome";'.PHP_EOL;
		$config .= '######################################################################################################'.PHP_EOL;

		$config .= '##########################################  Decryption Password ######################################'.PHP_EOL;
		$config .= '$GS_Config["EncryptKey"] = "SH'.mt_rand(9999999,999999999999999).'";'.PHP_EOL;
		$config .= '######################################################################################################'.PHP_EOL;
		$config .= '$GS_Config["installCheck"] = "true";'.PHP_EOL;
		
		fwrite($configFile, $config);
		fclose($configFile);
	}
	//step 5
	if($_POST['type']=="InstallBasic"){
		$name = $_POST['basicName'];
		$address = $_POST['basicAddress'];
		$zip = $_POST['basicZip'];
		$email = $_POST['basicEmail'];
		
		$GS_DBCONN = mysqli_connect($GS_Config['MySqlServer'], $GS_Config['dbUsername'], $GS_Config['dbPassword'], $GS_Config['dbName']);
		$query="UPDATE settings SET name='".$name."', home_address='".$address."', zip_code='".$zip."', outgoing_email_list='".$email."' WHERE ID='1'";
		mysqli_query($GS_DBCONN,$query);
	}
	//step 6
	if($_POST['type']=="InstallUser"){
		$name = $_POST['userName'];
		$username = $_POST['userUsername'];
		$password = $_POST['userPassword'];
		
		$GS_DBCONN = mysqli_connect($GS_Config['MySqlServer'], $GS_Config['dbUsername'], $GS_Config['dbPassword'], $GS_Config['dbName']);
		$query="UPDATE users SET user_name='".$name."', username='".$username."', password='".$password."' WHERE ID='1'";
		mysqli_query($GS_DBCONN,$query);
	}
	

    
 ?>
<!DOCTYPE HTML>
<html style="background-image:none;">
    <head>
        <title><?php echo $GS_Config['SiteName'];?> - Install</title>
        <link rel="icon" href="../../images/favicons/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" sizes="57x57" href="../../images/favicons/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="../../images/favicons/apple-touch-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="../../images/favicons/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="../../images/favicons/apple-touch-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="../../images/favicons/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="../../images/favicons/apple-touch-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="../../images/favicons/apple-touch-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="../../images/favicons/apple-touch-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="../../images/favicons/apple-touch-icon-180x180.png">
        <link rel="icon" type="image/png" href="../../images/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="../../images/favicons/android-chrome-192x192.png" sizes="192x192">
        <link rel="icon" type="image/png" href="../../images/favicons/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="../../images/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="../../images/favicons/manifest.json">
        <link rel="mask-icon" href="../../images/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-TileImage" content="../../images/favicons/mstile-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap Core CSS -->
        <link href="../../css/bootstrap.3.3.7.min.css" rel='stylesheet' type='text/css' />
        <!-- Custom CSS -->
        <link href="../../gui/css/style.less" rel='stylesheet/less' type='text/css' />
        <link href="../../gui/css/font-awesome.css" rel="stylesheet">
        <link rel="stylesheet" href="../../gui/css/font-awesome-animation.min.css">
        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <!----webfonts--->
        <link href='//fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
        <!---//webfonts--->
        <!-- Nav CSS -->
        <link href="../../gui/css/custom.css" rel="stylesheet">
        <!--For BootStrap Modals-->
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>		
        <script src="../../gui/js/bootstrap.3.3.7.min.js"></script>
        <script src="../../gui/js/less.js"></script>	
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
                <div style="height:40px;width:100%;text-align:center;">
                    <span style="font-size:40px;color:#000;"><?php echo $GS_Config['SiteName'];?></span>
                </div>
				
				<?php include("Step/1.php");?>
				<?php include("Step/2.php");?>
				<?php include("Step/3.php");?>
				<?php include("Step/4.php");?>
				<?php include("Step/5.php");?>
				<?php include("Step/6.php");?>
				<?php include("Step/7.php");?>
            </div>
        </div>
		
		<script>
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
		
        <div style="width:100%;max-width:550px;margin:0 auto;border-radius:5px;box-shadow:1px 1px 3px 2px rgba(0, 0, 0, 0.2);margin-bottom:10px;" >
            <?php include("../../gui/includes/footer.php");?>
        </div>
    </body>
</html>