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
    
    	//save all ifttt
    	if(temp_decode($_POST['type'])=='update'){
			if($_POST['txt_name']!=""){$name=$_POST['txt_name'];}else{$name="[No Name]";}
			
			$currentIFTTTConditionCount=0;
			while ($currentIFTTTConditionCount <= (int)$_POST['iftttConditionCount']){$currentIFTTTConditionCount++;
				
				$ifCount=(int)$_POST['appendedIfCount'.$currentIFTTTConditionCount];
				$thenCount=(int)$_POST['appendedThenCount'.$currentIFTTTConditionCount];
				$whileIfCount=0;
				$parenthasesLeft_Cond="";
				$parenthasesRight_Cond="";
				$opperators_Cond="";
				
				//IF
				while ($whileIfCount <= $ifCount){$whileIfCount++;
					if(trim($_POST['ifThis'.$currentIFTTTConditionCount.$whileIfCount])!=""){
						$if_Array .= $_POST['ifThis'.$currentIFTTTConditionCount.$whileIfCount]."<Done>";
						$opperators_Cond .= $_POST['opperator'.$currentIFTTTConditionCount.$whileIfCount].":";
						$parenthasesLeft_Cond.=$_POST['parenthaseLeft'.$currentIFTTTConditionCount.$whileIfCount].":";
						$parenthasesRight_Cond.=$_POST['parenthaseRight'.$currentIFTTTConditionCount.$whileIfCount].":";
					}
				}
				
				if(trim($_POST['ifThis'.$currentIFTTTConditionCount."1"])!=""){
					$if_Array.="<Condition>";
				}
				
				$parenthases .=":".$parenthasesLeft_Cond."|:".$parenthasesRight_Cond."+";
				$opperators.= $opperators_Cond."+";
				
				//THEN
				$whileThenCount=0;
				while ($whileThenCount <= $thenCount){$whileThenCount++;
					if(trim($_POST['ThenThat'.$currentIFTTTConditionCount.$whileThenCount])!=""){
						$then_Array .= $_POST['ThenThat'.$currentIFTTTConditionCount.$whileThenCount]."<Done>";
					}
				}
				
				if(trim($_POST['ThenThat'.$currentIFTTTConditionCount."1"])!=""){
					$then_Array.="<Action>";
				}
				
			}//end while
			
			
			$parenthasesOpen = substr_count($parenthases,"(");
    		$parenthasesClose = substr_count($parenthases,")");
    		
    		if($parenthasesOpen!=$parenthasesClose){
				$parenthases=""; 
    		}
		
			
			
			if((int)$_POST['ID']==0 && GetUserPermissions("add")==true){
				$insert_query="INSERT INTO ifttt (name,if_Array,ifthen_Array,opperatorArray,parenthaseArray,last_ran,delay,enabled)
				VALUES(
				'".clean_text($name,100)."',
				'".trim($if_Array)."',
				'".trim($then_Array)."',
				'".$opperators."',
				'".$parenthases."',
				'',
				'".clean_text($_POST['delay'],100)."',
				'".clean_text((int)$_POST['enabled'],1)."'
				)";
				mysqli_query($GS_DBCONN, $insert_query) or GF_logging("error");
    		}elseif(GetUserPermissions("edit")==true){
				$insert_query="UPDATE ifttt SET
				name='".clean_text($name,100)."',
				if_Array='".trim($if_Array)."',
				ifThen_Array='".trim($then_Array)."',
				opperatorArray='".$opperators."',
				parenthaseArray='".$parenthases."',
				delay='".clean_text($_POST['delay'],100)."',
				enabled='".clean_text((int)$_POST['enabled'],1)."'
				WHERE ID='".clean_text((int)$_POST['ID'],11)."'";
				mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
			}
    	}
	
    	
    	//delete ifttt
    	if($_GET['delete']!='' && temp_decode($_GET['delete'])!="[EXPIRED]" && GetUserPermissions("delete")==true){
    		$insert_query="DELETE FROM ifttt WHERE ID='".clean_text(temp_decode($_GET['delete']),11)."'";
    		mysqli_query($GS_DBCONN, $insert_query) or die (mysqli_error($GS_DBCONN));
    	}		
    
    ?>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:30px;width:100%;padding:0px;margin:0px;">
    <h3>If This Then That</h3>
   
	
	<?php 
		$query = "SELECT * FROM ifttt WHERE ID='".temp_decode($_GET['id'])."'";
		$results = mysqli_query($GS_DBCONN, $query);
		$result = mysqli_fetch_assoc($results);
		
		$IFTTTIfArray = explode("<Condition>", $result['if_Array']);
		$IFTTTThenArray = explode("<Action>", $result['ifThen_Array']);
		$IFTTToperatorArray = explode("+", $result['opperatorArray']);
		$IFTTTparenthaseArrayALL = explode("+", $result['parenthaseArray']);
	?>
	
	<form method="Post" style="padding:0px;" id="iftttFrm" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 autoform">
		<input type="hidden" name="ID" value="<?php echo $result['ID'];?>" id="txt_ID">
		<input type="hidden" name="type" value="<?php echo temp_encode("update");?>">
		<div class="well1 col-xs-12 col-sm-12 col-md-12 col-lg-12" style="min-width:220px;background-color:#f5f5f5;padding:5px;">
			<div style="">
				<!----- ENABLED ----->
				<div class=" col-xs-12 col-sm-12 col-md-12 col-lg-2" style="text-align:center;background-color:#fff;padding:10px;height:115px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
					<h4 style="text-align:center;background-color:#f1f1f1;padding:5px;border:1px solid #d8d8d8;overflow:hidden;padding:8px;width:100%;">
						<span><b>Enabled:</b></span>
					</h4>
					<input type="checkbox" data-toggle="toggle" data-size="medium" data-off="Enable" data-on="Enable" id="chk_enabled" name="enabled"  value="1"/>
				</div>
				<!----- DESCRIPTION ----->
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-5" style="background-color:#fff;padding:10px;height:115px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
					<h4 style="background-color:#f1f1f1;padding:5px;border:1px solid #d8d8d8;overflow:hidden;padding:8px;width:100%;">
						<b>Description:</b>
					</h4>
					<input id="txt_name" name="txt_name" type="text" style="width:100%;" class="form-control1" value="<?php echo $result['name'];?>">
				</div>
				<!----- DELAY ----->
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3" style="background-color:#fff;padding:10px;height:115px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
					<h4 style="text-align:center;background-color:#f1f1f1;padding:5px;border:1px solid #d8d8d8;overflow:hidden;padding:8px;width:100%;">
						<span><b>Delay:</b> (sec.)</span>
					</h4>
					<input id="txt_delay" name="delay" type="number" style="width:100%;" class="form-control1" value="<?php echo $result['delay'];?>"/>
				</div>
				<!----- SAVE/CANCEL ----->
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-2" style="padding:10px;background-color:#fff;height:115px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
					<button type="submit" class="btn btn-primary" style="width:100%;display:inline-block;">Save</button>
					<a href="ifttt_simple.php">
						<button type="button" class="btn btn-default" style="width:100%;margin-top:5px;display:inline-block;">Cancel</button>
					</a>
				</div>

				<script>
					 <?php if($result['enabled']=="1"):?>
						$("#chk_enabled").prop("checked",true);
					 <?php endif;?>
				</script>
			</div>
		</div>
		
		
		
		
		 <?php
		$iftttConditionCount = 0;
		foreach ($IFTTTIfArray as $row) {  $iftttConditionCount++;

			$ifArray = explode("<Done>", $row);
			$operatorArray_Cond = explode(":", $IFTTToperatorArray[$iftttConditionCount-1]);
			$IFTTTparenthaseArraySet = explode("|", $IFTTTparenthaseArrayALL[$iftttConditionCount-1]);
			$IFTTTparenthaseArrayLeft = explode(":", $IFTTTparenthaseArraySet[0]);
			$IFTTTparenthaseArrayRight = explode(":", $IFTTTparenthaseArraySet[1]);
		?>
		
			<!----- IF Start ----->
			<div class="well1 col-xs-12 col-sm-12 col-md-12 col-lg-6" style="min-width:220px;padding:5px;background-color:#f5f5f5;" >
				<div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);" id="IFLIST<?php echo $iftttConditionCount;?>">
				
					<?php if($iftttConditionCount==1):?>
						<h4 style="margin-top:10px;background-color:#f1f1f1;padding:8px;border:1px solid #d8d8d8;"><b>If This:</b></h4>
					<?php else :?>
						<h4 style="margin-top:10px;background-color:#f1f1f1;padding:8px;border:1px solid #d8d8d8;"><b>Else If:</b></h4>
					<?php endif;?>

					<?php
						$appendedIfCount=0;
						foreach ($ifArray as $item){$appendedIfCount++;
					?>
						<div id="IF<?php echo $iftttConditionCount.$appendedIfCount;?>">
							<div class="input-group">
								<?php if ($appendedIfCount==1) :?>
									<span class="input-group-addon" style="cursor:pointer;"><b>If</b></span>
								<?php else :?>
									<span class="input-group-addon" onclick="iftttChangeOperator('<?php echo $iftttConditionCount.$appendedIfCount;?>');" id="iftttAndLabel<?php echo $iftttConditionCount.$appendedIfCount;?>" style="cursor:pointer;"><b></b></span>
									<input type="hidden" name="opperator<?php echo $iftttConditionCount.$appendedIfCount;?>" value="" id="OperatorValue<?php echo $iftttConditionCount.$appendedIfCount;?>"/>
								<?php endif;?>
								
								<span class="input-group-addon" id="ParenthaseLeft<?php echo $iftttConditionCount.$appendedIfCount;?>" onclick="iftttChangeparenthase('<?php echo $iftttConditionCount.$appendedIfCount;?>','Left','(');" style="cursor:pointer;"><b>(</b></span>
								<input type="hidden" name="parenthaseLeft<?php echo $iftttConditionCount.$appendedIfCount;?>" value="(" id="ParenthaseLeftValue<?php echo $iftttConditionCount.$appendedIfCount;?>"/>
								
								<select name="ifThis<?php echo $iftttConditionCount.$appendedIfCount;?>" onchange="show_if_modal('<?php echo $iftttConditionCount.$appendedIfCount;?>',$(this).val());" id="if_this<?php echo $iftttConditionCount.$appendedIfCount;?>" class="form-control1" style="width:100%;">
								</select>
								
								<span class="input-group-addon" id="ParenthaseRight<?php echo $iftttConditionCount.$appendedIfCount;?>" onclick="iftttChangeparenthase('<?php echo $iftttConditionCount.$appendedIfCount;?>','Right',')');" style="cursor:pointer;"><b>)</b></span>
								<input type="hidden" name="parenthaseRight<?php echo $iftttConditionCount.$appendedIfCount;?>" value=")" id="ParenthaseRightValue<?php echo $iftttConditionCount.$appendedIfCount;?>"/>
							</div>
						</div>
						
						<script>
							$(document).ready(function(){
								ifThisList("#if_this<?php echo $iftttConditionCount.$appendedIfCount;?>");
								$('#if_this<?php echo $iftttConditionCount.$appendedIfCount;?>').append($("<option></option>").attr("value","<?php echo $item;?>").text(removeIfttTags("<IF>","</IF>",4,"<?php echo $item;?>")));
								$('#if_this<?php echo $iftttConditionCount.$appendedIfCount;?>').val("<?php echo $item;?>");
								
								<?php if($operatorArray_Cond[$appendedIfCount-1]=="||"):?>
									$('#OperatorValue<?php echo $iftttConditionCount.$appendedIfCount;?>').val('||');
									$('#iftttAndLabel<?php echo $iftttConditionCount.$appendedIfCount;?>').html("<b>OR</b>");
								<?php else :?>
									$('#OperatorValue<?php echo $iftttConditionCount.$appendedIfCount;?>').val('&&');
									$('#iftttAndLabel<?php echo $iftttConditionCount.$appendedIfCount;?>').html("<b>And</b>");
								<?php endif;?>
				
								<?php if($IFTTTparenthaseArrayLeft[$appendedIfCount]=="("):?>
									$('#ParenthaseLeftValue<?php echo$iftttConditionCount.$appendedIfCount;?>').val("(");
									$('#ParenthaseLeft<?php echo $iftttConditionCount.$appendedIfCount;?>').html("<b>(</b>");
								<?php else:?>
									$('#ParenthaseLeftValue<?php echo $iftttConditionCount.$appendedIfCount;?>').val("");
									$('#ParenthaseLeft<?php echo $iftttConditionCount.$appendedIfCount;?>').html("<b> </b>");
								<?php endif;?>
									
								<?php if($IFTTTparenthaseArrayRight[$appendedIfCount]==")"):?>
									$('#ParenthaseRightValue<?php echo $iftttConditionCount.$appendedIfCount;?>').val(")");
									$('#ParenthaseRight<?php echo $iftttConditionCount.$appendedIfCount;?>').html("<b>)</b>");
								<?php else :?>
									$('#ParenthaseRightValue<?php echo $iftttConditionCount.$appendedIfCount;?>').val("");
									$('#ParenthaseRight<?php echo $iftttConditionCount.$appendedIfCount;?>').html("<b> </b>");
								<?php endif;?>

							});
						</script>
					<?php }?>
					<input type="hidden" name="appendedIfCount<?php echo $iftttConditionCount;?>" value="<?php echo $appendedIfCount;?>"/>

					
					<script>
						function iftttChangeOperator(id){
							if($('#OperatorValue'+id).val()=="&&"){
								$('#OperatorValue'+id).val('||');
								$('#iftttAndLabel'+id).html("<b>OR</b>");
							}else{
								$('#OperatorValue'+id).val('&&');
								$('#iftttAndLabel'+id).html("<b>And</b>");
							}
						}
						
						function iftttChangeparenthase(id,side,value){
							if($('#Parenthase'+side+'Value'+id).val()==""){
								$('#Parenthase'+side+'Value'+id).val(value);
								$('#Parenthase'+side+id).html("<b>"+value+"</b>");
							}else{
								$('#Parenthase'+side+'Value'+id).val("");
								$('#Parenthase'+side+id).html("<b> </b>");
							}
						}
					</script>
					<!----- IF ADD BUTTON ----->
					<div id="ADDIF">
						<ul style="padding:0px;list-style:none;">
							<li onclick="$('#iftttFrm').submit();location.reload();" style="cursor:pointer;width:100%;;border:1px solid #000;margin-bottom:5px;text-align:center;background-color:#f2f2f2;padding-top:5px;padding-bottom:5px;">
								<span style="float:left;margin-left:20px;font-size:16px;"><i class="fa fa-plus"></i></span>
								<span style="Margin-left:-20px;">Add If This</span>
							</li>
						</ul>
					</div>
				</div>
			</div>

		
		<?php
			$row = $IFTTTThenArray[$iftttConditionCount-1];
			$iftttActionCount=$iftttConditionCount;
			$thenArray = explode("<Done>", $row);
		?>
		
			<!----- THEN Start ----->
			<div class="well1 col-xs-12 col-sm-12 col-md-12 col-lg-6" style="min-width:220px;padding:5px;background-color:#f5f5f5;" >
				<div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);" id="THENLIST<?php echo $iftttActionCount;?>">
					<h4 style="margin-top:10px;background-color:#f1f1f1;padding:8px;border:1px solid #d8d8d8;"><b>Then:</b></h4>
					
					<?php
						$appendedThenCount=0;
						foreach ($thenArray as $item){$appendedThenCount++;
					?>
						<div id="THEN<?php echo $appendedThenCount;?>">
							<div class="input-group">
								<span class="input-group-addon"><b>Then</b></span>
								<select name="ThenThat<?php echo $iftttActionCount.$appendedThenCount;?>" onchange="show_that_modal('<?php echo $iftttActionCount.$appendedThenCount;?>',$(this).val());" id="then_that<?php echo $iftttActionCount.$appendedThenCount;?>" class="form-control1" style="width:100%;"></select>
							</div>
						</div>
						
						<script>
							$(document).ready(function(){
								thenThatList("#then_that<?php echo $iftttActionCount.$appendedThenCount;?>");
								$('#then_that<?php echo $iftttActionCount.$appendedThenCount;?>').append($("<option></option>").attr("value","<?php echo $item;?>").text(removeIfttTags("<THEN>","</THEN>",6,"<?php echo $item;?>")));
								$('#then_that<?php echo $iftttActionCount.$appendedThenCount;?>').val("<?php echo $item;?>");							
							
								//match if box height
								if (/Android|iPad|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {}else{
									if($("#IFLIST<?php echo $iftttActionCount;?>").css("height")>$("#THENLIST<?php echo $iftttActionCount;?>").css("height")){
										$("#THENLIST<?php echo $iftttActionCount;?>").css({"height":$("#IFLIST<?php echo $iftttActionCount;?>").css("height")});
									}else{
										$("#IFLIST<?php echo $iftttActionCount;?>").css({"height":$("#THENLIST<?php echo $iftttActionCount;?>").css("height")});
									}
								}
							});
						</script>
					<?php }?>
					<input type="hidden" name="appendedThenCount<?php echo $iftttConditionCount;?>" value="<?php echo $appendedThenCount;?>"/>
			
					<!----- THEN ADD BUTTON ----->
					<div id="ADDTHEN">
						<ul style="padding:0px;list-style:none;">
							<li onclick="$('#iftttFrm').submit();location.reload();" style="cursor:pointer;width:100%;;border:1px solid #000;margin-bottom:5px;text-align:center;background-color:#f2f2f2;padding-top:5px;padding-bottom:5px;">
								<span style="float:left;margin-left:20px;font-size:16px;"><i class="fa fa-plus"></i></span>
								<span style="Margin-left:-20px;">Add Then That</span>
							</li>
						</ul>
					</div>
				</div>
			</div>
		<?php } ?>
		<input type="hidden" name="iftttConditionCount" value="<?php echo $iftttConditionCount;?>"/>
	</form>

	

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
        //echo "<center><p style='font-size:18px;'><b>".$prevlink." Page ".$page." of ".$pages." ".$nextlink."</b></p></center>";
    ?>
    <div style="height:800px;overflow:auto;padding:0px;" class ="col-xs-12 col-sm-5 col-md-4 col-lg-4">
       <div class="well1 col-xs-12 col-sm-5 col-md-12 col-lg-6" style="min-width:220px;padding:5px;background-color:#f5f5f5;min-width:200px;">
			<div style="background-color:#fff;padding:15px;box-shadow:0 1px 3px 0px rgba(0, 0, 0, 0.2);">
	            <div style="text-align:center;font-size:45px;">
	                <i class="fa fa-plus"></i>
	            </div>
	            <div style="width:100%;">
	                <?php if(GetUserPermissions("add")==true):?>
						<a href="ifttt_advanced.php">
							<button style="width:100%;" type="button" class="btn btn-primary">Add New</button>
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
			<div class ="col-xs-12 col-sm-5 col-md-12 col-lg-6" style="padding:5px;background-color:#f5f5f5;min-width:200px;">
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
	
	


 </div>	
    <div class="clearfix"> </div>
    <?php include("includes/iftttOptions.php");//ifttt modals and options ?>		
    <?php include("includes/footer.php");?>
	<?php include("includes/modals.php");?>
</body>
</html>