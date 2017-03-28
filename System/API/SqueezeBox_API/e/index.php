<?php
/**
 * basic_example.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: basic_example.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 07/09/2009 3:06:05 PM
 * @package SqueezePHPAPI
 
 
 
 <?php $mySqueezeCenter->CurrentPlayer->play(5);?>
 <?php $mySqueezeCenter->CurrentPlayer->pause();?>
 
 <?php echo $mySqueezeCenter->CurrentPlayer->mode();?> //returns play,pause,stop
 
 <?php $mySqueezeCenter->CurrentPlayer->Mixer->muting("0");?>
 <?php $mySqueezeCenter->CurrentPlayer->Mixer->muting("1");?>

<?php echo $mySqueezeCenter->CurrentPlayer->title();?>
<?php echo $mySqueezeCenter->CurrentPlayer->genre();?>
<?php echo $mySqueezeCenter->CurrentPlayer->artist();?> 

<?php $mySqueezeCenter->CurrentPlayer->Playlist->play("http://sampleswap.org/mp3/artist/2590/steve_counting-sheep-01-160.mp3","test",1);?>

 */

// Error reporting
error_reporting(-1);
ini_set('display_errors', 'on');


// Autoload Classes
function myExceptionHandler($e)
{
	echo $e;
}
function autoloader($class)
{

	if (class_exists($class, false) || interface_exists($class, false))
	{
		return;
	}
	try
	{
		$path = dirname(dirname(__FILE__)).'/lib/class.' . $class . '.php';
		require_once($path);
		if (class_exists($class, false) || interface_exists($class, false))
		{
			return;
		}
		throw new Exception('Class ' . $class . ' not found');
	}
	catch (Exception $e)
	{
		myExceptionHandler($e);
	}
}
spl_autoload_register('autoloader');

// Instatiate SqueezeCenter
$mySqueezeConnection = new SqueezeConnection("10.0.0.22","9090","","");
if ($mySqueezeConnection->connect())
{
	$mySqueezeCenter = new SqueezeCenter($mySqueezeConnection);
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SqueezePHPAPI Basic Test/Info Suite</title>
<style type="text/css">
* {
	margin: 0;
	padding: 0;
}
html {overflow: scroll}
body {
	background-color: #ccc;
	color: #333;
	font-family: typewriter,monospace,serif;
	font-size: 10pt;
	text-align: center;
}

#body {
	text-align: left;
	margin: 1em 2em;
	padding: 2em;
	max-width: 70%;
	background-color: #fff;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
}

fieldset {
	margin-bottom: 2em;
	padding: 1em 3em;
	padding-bottom: 3em;
	border:  1px solid;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
}

h3 {
	margin-bottom: 0.25em;
}

legend {
	font-weight: bold;
	font-size: 1.7em;
	padding: 1em;
}

table {
	margin-bottom: 1em;
	margin-left: 1em;
	margin-right: 1em;
	width: 100%;
}

td {
	color: #999;
	vertical-align: top;
	margin-bottom: 0.25em;
	padding-right: 1em;
	text-align: left;
}

td.desc {
	color: #333;
	width: 15em;
}
div#buffer
{
color: #000;
background-color: #ccc;
-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	padding:1em;
	margin-top:1em;
}
</style>
</head>
<body>
<div align="center">
<div id="body">
<!-- crazy logo start--><div style="margin-bottom:0.75em;"><a href="http://org.0r9.org" style="color:#000;text-decoration:none;" title="0r9.org - Generally just disorganized."><span style="padding-bottom:0.75em;"><strong><font size="7" face="Verdana, Arial, Helvetica, sans-serif"><em>0</em></font><font color="#FF0000"size="7" face="Times,serif;">r</font></strong><span style="position:relative;top:0.5em;"><font size="7" face="Verdana, Arial, Helvetica, sans-serif"><strong><em>9</em></strong></font></span>&nbsp;<span style="position:relative;top:-0.5em;"><font size="5"face="Verdana, Arial, Helvetica, sans-serif"><strong><em>.org</em></strong></font></span></span></a><div style="margin-bottom:-0.75em;">&nbsp;</div><tt>&ldquo;Organization ((unofficial)) of the disorganized.<br />Don't ask! We can't find it.&rdquo;</tt></div><!-- crazy logo end-->
<br />
<br />
<h1>SqueezePHPAPI Basic Test/Info Suite</h1>
<br />
<div>@version : $Id: basic_example.php 136 2009-09-16 16:07:43Z dave $</div>
<br />
<div>This is the basic test suit for the PHP-&gt;Command Line Interface-&gt;PHP
API of SqueezeCenter server.<br /> This is only a small sample of the API, however take a look at the source code of this file to help you get started.
<br /> With this set of classes you should be able to control all your SqueezeCenter servers and players via PHP.</div>
<fieldset><legend>SqueezeCenter</legend>

<h3>General</h3>
<table>
	<tr>
		<td class="desc">Version</td>
		<td><?php echo $mySqueezeCenter->version();?></td>
	</tr>
</table>
<h3>Players</h3>
<table>
	<tr>
		<td class="desc">Count Players</td>
		<td><?php echo $mySqueezeCenter->Players->count();?></td>
	</tr>
	<tr>
		<td class="desc">Players</td>
		<td><?php var_dump($mySqueezeCenter->Players->players());?></td>
	</tr>
	<tr>
		<td class="desc">Sync Groups</td>
		<td><?php echo $mySqueezeCenter->Players->syncgroups();?></td>
	</tr>
</table>
<h3>Database</h3>
<table>
	<tr>
		<td class="desc">Total Songs</td>
		<td><?php echo $mySqueezeCenter->Database->info_total_songs();?></td>
	</tr>
	<tr>
		<td class="desc">Total Artists</td>
		<td><?php echo $mySqueezeCenter->Database->info_total_artists();?></td>
	</tr>
	<tr>
		<td class="desc">Total Albums</td>
		<td><?php echo $mySqueezeCenter->Database->info_total_albums();?></td>
	</tr>

</table>
<h3>CurrentPlayer</h3>
<table>
	<tr>
		<td class="desc">Player ID</td>
		<td><?php $mySqueezeCenter->setCurrentPlayer(1);echo $mySqueezeCenter->CurrentPlayer->id();?></td>
	</tr>
</table>
</fieldset>

<fieldset><legend>SqueezePlayer</legend>

<h3>General</h3>
<table>
	<tr>
		<td class="desc">Player ID</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->id();?></td>
	</tr>

	<tr>
		<td class="desc">Player Model</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->model();?></td>
	</tr>
	<tr>
		<td class="desc">Player Display Type</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->displaytype();?></td>
	</tr>
	<tr>
		<td class="desc">Player Current Display</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->display_current();?></td>
	</tr>
</table>


<?php $mySqueezeCenter->CurrentPlayer->Playlist->play("http://opml.radiotime.com/Tune.ashx?id=s35368&formats=aac,ogg,mp3,wmpro,wma,wmvoice&partnerId=16&serial=63daa5ce6e3ccfe6c5bcca21d7f88e7a","test",1);?>

<h3>Mixer</h3>
<table>
	<tr>
		<td class="desc">Player Mixer Volume</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->Mixer->volume("?");?></td>
	</tr>
	<tr>
		<td class="desc">Player Mixer Muting</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->Mixer->muting("?");?></td>
	</tr>
	<tr>
		<td class="desc">Player Mixer Treble</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->Mixer->treble("?");?></td>
	</tr>
	<tr>
		<td class="desc">Player Mixer Bass</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->Mixer->bass("?");?></td>
	</tr>
	<tr>
		<td class="desc">Player Mixer Pitch</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->Mixer->pitch("?");?></td>
	</tr>
</table>
<h3>Playlist</h3>
<table>
	<tr>
		<td class="desc">Shuffle</td>
		<td><?php echo var_export($mySqueezeCenter->CurrentPlayer->Playlist->shuffle("?"));?></td>
	</tr>
	<tr>
		<td class="desc">Repeat</td>
		<td><?php echo var_export($mySqueezeCenter->CurrentPlayer->Playlist->repeat("?"));?></td>
	</tr>
	<tr>
		<td class="desc">Tracks in Playlist</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->Playlist->tracks();?></td>
	</tr>
	<tr>
		<td class="desc">Current Track Index</td>
		<td><?php echo $mySqueezeCenter->CurrentPlayer->Playlist->index("?");?></td>
	</tr>
</table>
<h3>Alarms</h3>
<table>
	<tr>
		<td class="desc">Alarms</td>
		<td><?php $mySqueezeCenter->CurrentPlayer->Playlist->play("http://soundbible.com/grab.php?id=2155&type=wav","",10);?></td>
	</tr>
</table>
</fieldset>

<fieldset><legend>SqueezeConnection</legend>
<h3>General</h3>
<table>
	<tr>
		<td class="desc">Connected</td>
		<td><?php echo var_export($mySqueezeConnection->connected);?></td>
	</tr>
	<tr>
		<td class="desc">Server Hostname</td>
		<td><?php echo $mySqueezeConnection->getHostname();?></td>
	</tr>
	<tr>
		<td class="desc">Server Port</td>
		<td><?php echo $mySqueezeConnection->getPort();?></td>
	</tr>
	<tr>
		<td class="desc">Security Enabled</td>
		<td><?php echo var_export($mySqueezeConnection->security_enabled);?></td>
	</tr>
	<tr>
		<td class="desc">Errors</td>
		<td><?php var_dump($mySqueezeConnection->errors);?></td>
	</tr>
	<tr>
		<td class="desc">Last Read Buffer</td>
		<td><?php echo urldecode($mySqueezeConnection->lastReadBuffer);?></td>
	</tr>
	<tr>
		<td class="desc">Buffer</td>
		<td><?php echo  urldecode($mySqueezeConnection->buffer);?></td>
	</tr>
	<tr>
		<td class="desc">Connected</td>
		<td><?php $mySqueezeConnection->disconnect();echo var_export($mySqueezeConnection->connected);?></td>
	</tr>
</table>
</fieldset>

<div>For more possibilities, read the <a href="../docs/index.html">phpdocs</a>, add a little imagination, and have fun :-)</div>
</div>
</div>
</body>
</html>
