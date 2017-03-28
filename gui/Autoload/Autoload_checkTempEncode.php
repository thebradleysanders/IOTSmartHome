<?php
//this page is ran on all gui main pages and is used to check if the temp_encode text is still valid.
//if it is not valid the page will reload to refresh all temp_encode variables
//this prevents pages from being saved/bookmarked and being read dynamically
?>
<?php if(temp_decode($_GET['TempStringCheck'])=="[EXPIRED]"):?>
	<script>location.href="index.php"</script>
<?php endif;