<script>
	<?php
	$query = "SELECT * FROM buttons";
	$results = mysqli_query($GS_DBCONN, $query);
	while($result = mysqli_fetch_assoc($results)) { 
	?>
		<?php if ($result['button_state']=='1') :?>
			$('#button<?php echo $result['ID'];?>_not_active').hide();
			$('#button<?php echo $result['ID'];?>_active').show();
		<?php else :?>
			$('#button<?php echo $result['ID'];?>_active').hide();
			$('#button<?php echo $result['ID'];?>_not_active').show();
		<?php endif;?>
	<?php } ?>
</script>