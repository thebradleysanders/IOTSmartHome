<?php
require( '../hue.php' );

$bridge = 'IP Address';
$key = "Key";
$hue = new Hue( $bridge, $key );
$lightRange = [ 1, 3 ];

while ( true )
{
    $target = rand( $lightRange[0], $lightRange[1] );
    $hue->lights()[$target]->setLight( $hue->randomColor() );
    usleep( 100000 );
}
?>
