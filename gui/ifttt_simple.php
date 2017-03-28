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
    $page_title="IFTTT"; 
    $upParrentDir = '/../../..';
    require_once(realpath(__DIR__ ."/../System/API/SmartHome_API/IOTIncludes.php"));
    ################################################
	
	//delete ifttt
	if($_GET['delete']!='' && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete","ifttt_advanced.php")==true){
		$insert_query="DELETE FROM ifttt WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."'";
		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
	}		
    
    ?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:0px;margin-bottom:30px;">
    <h3>If This Then That</h3>
    <?php		
        // Find out how many items are in the table
        $total = mysqli_num_rows(mysqli_query($GS_DBCONN, 'SELECT * FROM  ifttt'));
        // How many items to list per page
        $limit = 40;
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
        <a style='font-size:20px;' href='?page=1' title='Next page'><i class='fa fa fa-step-backward'></i></a>&nbsp;
        <a style='font-size:25px;' href='?page=".($page - 1)."' title='Last page'><i class='fa fa-caret-left'></i></a>"
        :"";
        // The "forward" link
        $nextlink = ($page < $pages) ? "
        <a style='font-size:25px;' href='?page=".($page + 1)."' title='Next page'><i class='fa fa fa-caret-right'></i></a>&nbsp;
        <a style='font-size:20px;' href='?page=".$pages."' title='Last page'><i class='fa fa-step-forward'></i></a>"
        :"";
        // Display the paging information
        echo "<center><p style='font-size:18px;'><b>".$prevlink." Page ".$page." of ".$pages." ".$nextlink."</b></p></center>";
    ?>
    <div style="margin-top:10px;">
        <div class="well1 col-xs-12 col-sm-5 col-md-3 col-lg-2 " style="min-width:220px;padding:5px;background-color:#f5f5f5;">
			<div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
	            <?php echo $result['name'];?> 
	            <div style="text-align:center;font-size:45px;">
	                <i class="fa fa-plus"></i>
	            </div>
	            <div style="width:100%;">
	                <?php if(GetUserPermissions("add","ifttt_advanced.php")==true):?>
	                	<a href="ifttt_advanced.php">
							<button style="width:100%;"  type="button" class="btn btn-primary">Add New</button> 
	                	</a>
	                <?php else:?>
						<button style="width:100%;" type="button" class="btn btn-primary" disabled title="You do not have permission">Add New</button>
	                <?php endif;?>
	            </div>
			</div>
        </div>
        <?php
            $count = 0;
            $query = "SELECT * FROM ifttt ORDER BY name ASC LIMIT ".$limit." OFFSET ".$offset;
            $results = mysqli_query($GS_DBCONN, $query);
            while($result = mysqli_fetch_assoc($results)) { $count++;
		?>
			<div class="well1 col-xs-12 col-sm-5 col-md-3 col-lg-2 " style="min-width:220px;padding:5px;background-color:#f5f5f5;margin-bottom:5px;">
				<div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
					<p style="width:100%;height:65px;overflow:auto;">
						<?php echo ucwords(strtolower($result['name']));?><br/>
						<span style="font-size:12px;"><?php if($result['last_ran']!=""){echo ago(date('Y/m/d H:i:s', $result['last_ran']));}else{echo "";}?></span>
					</p>
					<div style="text-align:center;">
						<?php if(GetUserPermissions("edit","ifttt_advanced.php")==true):?>
						<a href="ifttt_advanced.php?id=<?php echo temp_encode($result['ID']);?>">
							<button type="button" class="btn btn-default"><i class="fa fa-pencil"></i></button>
						</a>
						<?php else:?>
							<button type="button" class="btn btn-default" disabled title="You do not have permission"><i class="fa fa-pencil"></i></button>  
						<?php endif;?>
						<?php if (GetUserPermissions("delete","ifttt_advanced.php")==true):?>
							<a onclick="return confirm('Are You Sure You Want To Delete This?');" href="?delete=<?php echo temp_encode($result['ID']);?>">
								<button type="button" class="btn btn-danger">
									<i class="fa fa-trash-o"></i>
								</button>
							</a> 
						<?php else:?>
							<button type="button" class="btn btn-danger" disabled title="You do not have permission"><i class="fa fa-trash-o"></i></button>
						<?php endif;?>
					</div>
				</div>
			</div>
        <?php } ?>
    </div>
 </div>	
    <div class="clearfix"> </div>
    <?php include("includes/iftttOptions.php");//ifttt modals and options ?>		
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
</body>
</html>