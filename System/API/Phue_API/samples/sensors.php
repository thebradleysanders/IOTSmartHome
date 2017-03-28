<?php
require( '../hue.php' );

$bridge = 'IP Address';
$key = "Key";
$hue = new Hue( $bridge, $key );

print_r($hue->sensors());