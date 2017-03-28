<?php 
$upParrentDir="../../";
require_once(realpath($upParrentDir .'System/Install/Config.php'));
?>

@base: <?php echo $GS_Config['themeColorMain'];?>;
@tone: <?php echo $GS_Config['themeColorSub'];?>;

.sh-logo{
	box-shadow:1px 1px 20px 1px @base;
	overflow:hidden;
	color:#fff;
	font-size:100px;
	width:100px;
	height:100px; 
	border-radius:10px;
	text-align:center;
	background-color:@base;
}

/*** Works on common browsers ***/
::selection {
    background-color: @base;
    color: #fff;
}

/*** For Webkit ***/
::-webkit-selection {
    background-color: @base;
    color: #fff;
}