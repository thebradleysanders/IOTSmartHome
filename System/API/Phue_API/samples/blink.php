<?php
require( '../hue.php' );

$bridge = 'IP Address';
$key = "Key";
$hue = new Hue( $bridge, $key );

$hue->lights()[1]->setAlert( "lselect" );
$hue->lights()[2]->setAlert( "lselect" );
$hue->lights()[3]->setAlert( "lselect" );

sleep( 10 );
$hue->lights()[1]->setAlert( "none" );
$hue->lights()[2]->setAlert( "none" );
$hue->lights()[3]->setAlert( "none" );

?>
