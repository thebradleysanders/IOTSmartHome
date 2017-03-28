<?php 


//############################ Orvibo Smart Outlets #########################
function OrviboAPI($ip,$state,$mac,$port){
	$LS_OrviboAPI = new Orvibo($ip, $mac $port);
	$LS_OrviboAPI->ChangeState((bool)$state);
}