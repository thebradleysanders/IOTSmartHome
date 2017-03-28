<?php 
##############################################################################
/*///////////////////////////////////////////////////////////////////////////
	ABOUT THIS PAGE:
		This page connectects to the DB and gets page permissions
	
	SmartHome - Created By: Brad Sanders
///////////////////////////////////////////////////////////////////////////*/
##############################################################################

require(realpath(__DIR__ . "/../../Install/Config.php"));
$GS_DBCONN = mysqli_connect($GS_Config['MySqlServer'], $GS_Config['dbUsername'], $GS_Config['dbPassword'], $GS_Config['dbName']);
if (!$GS_DBCONN){die("Database Connection Failed " . mysqli_error($GS_DBCONN));}


$ExcludeList = array(
	"IOTRecieveQue.php",
	"test.php"
);

try{
	//Add all SH Broker Files To Exclude Array
	foreach (new DirectoryIterator(realpath(__DIR__ . "/SH_Broker")) as $file) {
		if ($file->isFile()) { array_push($ExcludeList, strtolower($file->getFilename())); }
	}

	//Add all Autoload Files To Exclude Array
	foreach (new DirectoryIterator(realpath(__DIR__ . "/../../../gui/Autoload")) as $file) {
		if ($file->isFile()) { array_push($ExcludeList, strtolower($file->getFilename())); }
	}

	//Add all CustomScripts Files To Exclude Array
	foreach (new DirectoryIterator(realpath(__DIR__ . "/../../../gui/CustomScripts/PHP")) as $file) {
		if ($file->isFile()) { array_push($ExcludeList, strtolower($file->getFilename())); }
	}
//catch exception
}catch(Exception $e) {
  echo 'DBConn.php error 38, Message: ' .$e->getMessage();
}

//lowercase all items in exclude list
$ExcludeList = array_map('strtolower', $ExcludeList);

if(in_array(basename(strtolower($_SERVER['PHP_SELF'])), $ExcludeList) == FALSE && $_GET['autoload']!="true"){
	//session_start seems to severly slow down page load times on automated timers and nodes so we just disable it
	session_start();
	
	
	
	//verify user Has Permissions before allowing them to each page
	if(basename($_SERVER['PHP_SELF'])!="logout.php" && strtolower(basename($_SERVER['PHP_SELF']))!="ajax_getlogin.php"){
		if($_SESSION['username']!='' && $_SESSION['password']!='' && $_SESSION['type']!=''){
			$query = "SELECT user_permissions,type FROM users WHERE username='".$_SESSION['username']."' And password='".$_SESSION['password']."' AND enabled='1'";
			$permission_results = mysqli_query($GS_DBCONN, $query);
			$permis_result = mysqli_fetch_assoc($permission_results);
			$permis_result_count = mysqli_num_rows($permission_results);
			
			if($permis_result_count>0){
				//set account type incase changed while logged in
				$_SESSION['type']=$permis_result['type'];
			
				if((GetUserPermissions("read",basename($_SERVER['PHP_SELF']), $permis_result['user_permissions'])==true) 
					|| (basename($_SERVER['PHP_SELF'])=='manage_users.php' && $_SESSION['type']=="Admin")
					|| (basename($_SERVER['PHP_SELF'])=='manage_nodes.php' && $_SESSION['type']=="Admin")){
					//user has permissions
				}elseif(basename($_SERVER['PHP_SELF'])!="no_access.php" ){ //if not admin or no permissions then no access
					header("Location: no_access.php");
				}
			}else{ //user dont exist so we logout
				session_unset();header("Location: login.php"); exit;
			}//end user dont exist
		} //end if isset password, username and type
	} //end if not invalid page
		
	if($_SESSION['id']=="" && basename($_SERVER['PHP_SELF'])!="login.php" && strtolower(basename($_SERVER['PHP_SELF']))!="ajax_getlogin.php"){
		echo "<script>location.href='login.php'</script>";
		exit;
	}
}

//get user permsiions
function GetUserPermissions($search_permis, $pageName="", $permisArray=""){	
	if($pageName==""){
		$search_text = basename($_SERVER['PHP_SELF']);
	}else{
		$search_text =$pageName;
	}
	if($permisArray==""){
		global $permis_result;
		$array = explode(":",$permis_result['user_permissions']);
	}else{
		$array = explode(":",$permisArray);
	}
	foreach($array as $key=> $value){
		if (strpos($value,$search_text) !== false)  {	
			if (strpos($value,$search_permis) !== false)  {
				return true;
			}else{
				return false;
			}
		}
	}
}

