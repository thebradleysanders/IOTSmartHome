<?php

require("../phpMQTT.php");


if ($mqtt->connect()) {
	$mqtt->publish("topic","message 1",0);
	sleep(1);
	$mqtt->publish("topic","message 2",0);
	$mqtt->close();
}
?>