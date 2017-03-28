<?php
$GS_phueServiceBypass = true;
$GS_wemoServiceBypass = true;
$GS_mqttServiceBypass = true;
$GS_emailServiceBypass = true;
$GS_squeezeBoxServiceBypass = true;
$GS_webIncludesIncluded = true;

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
	
	include("Autoload/Autoload_Datastates.php");
	include("Autoload/Autoload_checkTempEncode.php");
	include("Autoload/Autoload_UINotifications.php");
	exit;	
}
################################################

#################### API #######################
	$page_title="No Access"; 
	$upParrentDir = '/../../..';
	require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
################################################

//permissions
$query = "SELECT * FROM users WHERE ID='".$_SESSION['id']."' AND enabled='1'";
$permission_results = mysqli_query($GS_DBCONN, $query);
$permis_result = mysqli_fetch_assoc($permission_results);
$permis_result_count = mysqli_num_rows($permission_results);

?>

           
    <div class="col-md-12 graphs">
        <div class="xs" style="text-align:center;margin-top:40px;margin-bottom:40px;">
	        <h1><i style="font-size:100px;" class="fa fa-exclamation-triangle" aria-hidden="true"></i></h1>
			<h2>Sorry, You Do Not Have Access To This Page</h2>
			Please Contact Your System Administrator
        </div>

		<?php include("includes/footer.php");?>
	</div>

	<?php include("includes/modals.php");?>
    
</body>
</html>