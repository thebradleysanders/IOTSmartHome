<?php
include("../Config.php");

// Create connection
$GS_DBCONN = mysqli_connect($GS_Config['MySqlServer'], $GS_Config['dbUsername'], $GS_Config['dbPassword']);
if (!$GS_DBCONN){
	echo "<p style='color:red;'><i class='fa fa-times'></i> Database Connection Failed ".mysqli_error($GS_DBCONN)."</p>";
}else{
	
	$sql = "CREATE DATABASE ".$GS_Config['dbName'];
	if(mysqli_query($GS_DBCONN, $sql)){
		//success

		// Select database
		mysqli_select_db($GS_DBCONN,$GS_Config['dbName']) or die('Error selecting MySQL database: ' . mysql_error());

		// Temporary variable, used to store current query
		$templine = '';
		// Read in entire file
		$lines = file("../smarthome.sql");
		// Loop through each line
		foreach ($lines as $line)	{
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '')
				continue;

			// Add this line to the current segment
			$templine .= $line;
			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -1, 1) == ';'){
				// Perform the query
				mysqli_query($GS_DBCONN,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
				// Reset temp variable to empty
				$templine = '';
			}
		}
		echo "<script>$('#step4Spinner').hide();</script>";
		echo "<script>$('#step4Btn').attr('disabled',false);</script>";
		echo "<p style='color:green;'><i class='fa fa-check'></i> Tables imported successfully</p>";
		
	}else{
		echo "<p style='color:red;'><i class='fa fa-times'></i> Error Creating The Database: ".$GS_Config['dbName']."</p>";
	}
}

?>
