<?php 
	include("System/Install/Config.php");
	
	if($GS_Config['installCheck'] == "true"){
		header("Location: /gui/index.php");
	}else{
		header("Location: /System/install/index.php");
	}
	
	