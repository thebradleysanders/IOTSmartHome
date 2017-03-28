	<div class="copy">
		<p>
			<div style="font-size:14px;display:inline-block;">Copyright &copy; <?php echo gmdate("Y",time());?> SmartHome. All Rights Reserved. </div>
			<div style="font-size:14px;display:inline-block;"><b>Created By:</b> Brad Sanders</div>
		</p>
	</div>
	  
	<div id="Autoload"></div>

	<script>
		function AutoLoadFunction() {
			$("#Autoload").load("<?php echo basename($_SERVER['PHP_SELF']);?>?autoload=true&user_id=<?php echo temp_encode($_SESSION['id']);?>&type=<?php echo $_GET['type'];?>&room_id=<?php echo $_GET['room_id'];?>&TempStringCheck=<?php echo temp_encode("CheckString");?>");
			
			<?php if($GS_Config['InstallType']!="lite"):?>
				setTimeout(AutoLoadFunction,1000);
			<?php endif;?>
		}
		AutoLoadFunction();
	</script>