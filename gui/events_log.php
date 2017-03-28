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
    	include("Autoload/AutoLoad_SHAlerts.php");
    	include("Autoload/Autoload_checkTempEncode.php");
    	exit;	
    }
    ################################################
    
    #################### API #######################
    	$page_title="Events Log";
    	$upParrentDir = '/../../..';
    	require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    ################################################
    
    	if(temp_decode($_POST['type'])=='clear_log' && GetUserPermissions("delete")==true){
    		$insert_query="truncate table event_log";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}
    
    ?>
<div class="col-md-12" style="margin-bottom:30px;">
    <h3>
        Event Log
        <?php if(GetUserPermissions("delete")==true):?>
        <form method="POST" style="float:right;">
            <input type="hidden" name="type" value="<?php echo temp_encode("clear_log");?>"/>
            <input onclick="return confirm('Are you sure you want to delete all items from the event log?');" type="submit" value="Clear Log" class="btn btn-primary"/>
        </form>
        <?php else :?>
        <div style="float:right;margin-top:-10px;">
            <input disabled type="button" value="Clear Log" class="btn btn-primary" title="You do not have permission"/>
        </div>
        <?php endif;?>
    </h3>
    <form method="get" style="margin-bottom:10px;margin-top:20px;">
        <input type="hidden" name="type" value="<?php echo temp_encode("search");?>"/>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding-left:0px;">
            <label><b>Search Text:</b></label>
            <input placeholder="Search" style="margin-bottom:5px;display:inline-block;" class="form-control1" type="text" name="searchText" value="<?php echo $_GET['searchText'];?>"/>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding-left:0px;">
            <label><b>Date:</b></label>
            <input style="margin-bottom:5px;display:inline-block;"  class="form-control1" type="date" name="searchDate" value="<?php echo $_GET['searchDate'];?>"/>
        </div>
        <div class="col-xs-11 col-sm-11 col-md-3 col-lg-3" style="padding-left:0px;">
            <label><b>Time:</b></label>
            <input style="margin-bottom:5px;display:inline-block;"  class="form-control1" type="time" name="searchTime" value="<?php echo $_GET['searchTime'];?>"/>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" >
            <br/>
            <button class="btn btn-success" type="submit" ><i class="fa fa-search"></i></button>
        </div>
    </form>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 stats-info stats-info1" style="box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);margin-bottom:20px;">
        <div class="panel-body panel-body2" style="overflow:auto;">
            <ul class="list-unstyled" style="max-height:800px;">
                <?php
                    // Find out how many items are in the table
                    if($_GET['type']==""){
                    	$query = "SELECT * FROM  event_log ORDER BY ID ASC";
                    }elseif(temp_decode($_GET['type'])=="search"){
                    	$query = "SELECT * FROM event_log WHERE (event LIKE '%".$_GET['searchText']."%' OR tags LIKE '%|".$_GET['searchText']."|%' OR ID='".$_GET['searchText']."') ";
                    	if($_GET['searchDate']!=""){
                    		$query.=" AND event_date LIKE '".date("m/d/Y",strtotime($_GET['searchDate']))."%'";
                    	}  
                    	if($_GET['searchTime']!=""){
                    		$query.=" AND event_date LIKE '%".date("h:i",strtotime($_GET['searchTime'])).":%'";
                    	} 
                    	 $query.=" ORDER BY ID DESC";
                    }elseif(temp_decode($_GET['type'])=="ShowWarnings"){
                    	 $query = "SELECT * FROM event_log WHERE tags LIKE '%|Warnings|%' AND event_read='0' ORDER BY ID DESC";
                    }
                    $total = mysqli_num_rows(mysqli_query($GS_DBCONN, $query));
                    
                    // How many items to list per page
                    $limit = 60;
                    // How many pages will there be
                    $pages = ceil($total / $limit);
                    // What page are we currently on?
                    $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array('options' => array('default'   => 1,'min_range' => 1))));
                    // Calculate the offset for the query
                    $offset = ($page - 1)  * $limit;
                    // Some information to display to the user
                    $start = $offset + 1;
                    $end = min(($offset + $limit), $total);
                    
                    
                    
                    if($_GET['type']==""){
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
                    }elseif(temp_decode($_GET['type'])!=""){
                    	// The "back" link
                    	$prevlink = ($page > 1) ? "
                    	<a style='font-size:20px;color:#333;' href='".$_SERVER['REQUEST_URI']."&page=1' title='Next page'><i class='fa fa-step-backward'></i></a>&nbsp;
                    	<a style='font-size:25px;color:#333;' href='".$_SERVER['REQUEST_URI']."&page=".($page - 1)."' title='Last page'><i class='fa fa-caret-left'></i></a>"
                    	:"";
                    	// The "forward" link
                    	$nextlink = ($page < $pages) ? "
                    	<a style='font-size:25px;color:#333;' href='".$_SERVER['REQUEST_URI']."&page=".($page + 1)."' title='Next page'><i class='fa fa-caret-right'></i></a>&nbsp;
                    	<a style='font-size:20px;color:#333;' href='".$_SERVER['REQUEST_URI']."&page=".$pages."' title='Last page'><i class='fa fa-step-forward'></i></a>"
                    	:"";
                    }
                    // Display the paging information
                    echo "<center><p style='font-size:18px;'><b>".$prevlink." Page ".$page." of ".$pages." ".$nextlink."</b></p></center>";
                    
                    $count = 0;
                    if($_GET['type']==""){
                      $query = "SELECT * FROM event_log ORDER BY ID DESC LIMIT ".$limit." OFFSET ".$offset;
                    }elseif(temp_decode($_GET['type'])=="ShowWarnings"){
                    	 $query = "SELECT * FROM event_log WHERE tags LIKE '%|Warnings|%' AND event_read='0' ORDER BY ID DESC LIMIT ".$limit." OFFSET ".$offset;
                    }elseif(temp_decode($_GET['type'])=="search"){
                    	$query = "SELECT * FROM event_log WHERE (event LIKE '%".$_GET['searchText']."%' OR tags LIKE '%|".$_GET['searchText']."|%' OR ID='".$_GET['searchText']."') ";
                    	if($_GET['searchDate']!=""){
                    		$query.=" AND event_date LIKE '".date("m/d/Y",strtotime($_GET['searchDate']))."%'";
                    	}  
                    	if($_GET['searchTime']!=""){
                    		$query.=" AND event_date LIKE '%".date("h:i",strtotime($_GET['searchTime'])).":%'";
                    	} 
                    	 $query.=" ORDER BY ID DESC LIMIT ".$limit." OFFSET ".$offset;
                    }
                     
                    $results = mysqli_query($GS_DBCONN, $query);
                    while($result = mysqli_fetch_assoc($results)) { $count++;
                    
                    //make this event read
                     mysqli_query($GS_DBCONN, "UPDATE event_log SET event_read='1' WHERE ID='".$result['ID']."'");
                ?>
					<li><b><?php echo $result['event_date'];?></b> - <?php echo $result['event'];?> </li>
                <?php }?>
            </ul>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
</body>
</html>