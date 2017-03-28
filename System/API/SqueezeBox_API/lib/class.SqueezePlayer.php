<?php
/**
 * class.SqueezePlayer.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: class.SqueezePlayer.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 07/09/2009 6:03:19 PM
 * @package SqueezePHPAPI
 */

if($_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
	exit();
}

/**
 * SqueezePlayer
 *
 * <p>SqueezePlayer Interface</p>
 * @author David Broz <db-dev__AT__0r9.org>
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @package SqueezePHPAPI
 */
final class SqueezePlayer
{
	/**
	 * CLI
	 * 
	 * <p>Command Line Interface</p>
	 * @var SqueezeConnection
	 */
	private $CLI;


	/**
	 * SqueezePlyrID
	 *
	 * @var string
	 */
	private $SqueezePlyrID;


	/**
	 * $Playlist
	 * 
	 * <p>SqueezePlayer playlist interface</p>
	 * @var SqueezePlaylist
	 */
	protected $Playlist;


	/**
	 * $Alarms
	 * 
	 * <p>SqueezePlayer alarm interface</p>
	 * @var SqueezeAlarm
	 */
	protected $Alarms;


	/**
	 * $Mixer
	 * 
	 * <p>SqueezePlayer mixer interface</p>
	 * @var SqueezeMixer
	 */
	protected $Mixer;



	/**
	 * $Players
	 * 
	 * <p>SqueezePlayers interface</p>
	 * @var SqueezePlayers
	 */
	protected $Players;



	/**
	 * SqueezePlayer (constructor)
	 *
	 * @param SqueezeConnection $CLI
	 * @param string $SqueezePlyrID
	 * @return SqueezePlayer
	 */
	function __construct(SqueezeConnection $CLI, $SqueezePlyrID = "")
	{
		if (empty($SqueezePlyrID))
		{
			user_error('SqueezePlayer \$SqueezePlyrID required.');
		}
		$this->CLI = &$CLI;
		$this->SqueezePlyrID = $SqueezePlyrID;
		$this->setPlayers();
	}

	/**
	 * id
	 * 
	 * <p>Returns the current player id.</p>
	 * @return string
	 */
	public function id()
	{
		return $this->SqueezePlyrID ;
	}
	
	/**
	 * play
	 * 
	 * <p>The "play" command allows to start playing the current playlist.</p>
	 * @param integer $fadeInSecs <fadeInSecs>
	 * @return boolean (success)
	 */
	public function play($fadeInSecs = -1)
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." play ".($fadeInSecs >= 0 ? (string) $fadeInSecs : ""));
	}


	/**
	 * stop
	 * 
	 * <p>The "stop" command allows to stop playing the current playlist.</p>
	 * @return boolean (success)
	 */
	public function stop()
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." stop");
	}


	/**
	 * pause
	 * 
	 * <p>You may use "pause 1" to force the player to pause, "pause 0" to force the player to unpause and "pause" to toggle the pause state.</p>
	 * @param integer $state <0|1|-1> Default: -1 (toggle)
	 * @param integer $fadeInSecs <fadeInSecs>
	 * @return boolean (success)
	 */
	public function pause($state = -1 ,$fadeInSecs = -1)
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." pause ".($state >= 0 ? (string) $state : "")." ".(($state == 0 && $fadeInSecs >= 0) ? (string) $fadeInSecs : ""));
	}


	/**
	 * mode
	 * 
	 * <p>The "mode" command allows to query the player state and returns one of "play", "stop" or "pause". If the player is off, "mode ?" returned value is undefined.</p>
	 * @return string
	 */
	public function mode()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." mode ?");
	}


	/**
	 * time
	 * 
	 * <p>The "time" command allows you to query the current number of seconds that the current song has been playing by passing in a "?". You may jump to a particular position in a song by specifying a number of seconds to seek to. You may also jump to a relative position within a song by putting an explicit "-" or "+" character before a number of second you would like to seek.</p>
	 * @param string $number <number|-number|+number|?>
	 * @return mixed boolean (success) or string on query (current track time).
	 */
	public function time($number = '')
	{
		if (empty($number) || $number == "?")
		{
			return $this->CLI->stringQuery($this->SqueezePlyrID." time ?");
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." time ".((string) $number));
	}


	/**
	 * genre
	 * 
	 * <p>Returns "genre" of song currently playing.</p>
	 * @return string
	 */
	public function genre()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." genre ?");
	}


	/**
	 * artist
	 * 
	 * <p>Returns "artist" of song currently playing.</p>
	 * @return string
	 */
	public function artist()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." artist ?");
	}


	/**
	 * album
	 * 
	 * <p>Returns "album" of song currently playing.</p>
	 * @return string
	 */
	public function album()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." album ?");
	}


	/**
	 * title
	 * 
	 * <p>Returns "title" of song currently playing.</p>
	 * @return string
	 */
	public function title()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." title ?");
	}


	/**
	 * duration
	 * 
	 * <p>Returns "duration" of song currently playing.</p>
	 * @return string
	 */
	public function duration()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." duration ?");
	}


	/**
	 * remote
	 * 
	 * <p>Returns if the song currently playing is a remote stream.</p>
	 * @return boolean
	 */
	public function remote()
	{
		return $this->CLI->boolQuery($this->SqueezePlyrID." remote ?");
	}


	/**
	 * current_title
	 * 
	 * <p>Returns "current_title" of song currently playing.</p>
	 * @return string
	 */
	public function current_title()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." current_title ?");
	}


	/**
	 * path
	 * 
	 * <p>Returns "path" of song currently playing.</p>
	 * @return string
	 */
	public function path()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." path ?");
	}


	/**
	 * name
	 * 
	 * <p>Sets the name of the player. You may query the player name by passing in "?" or "" (default)</p>
	 * @param string $newname <newname|?>
	 * @return string
	 */
	public function name($newname = "")
	{
		if (empty($newname) || $newname == "?")
		{
			return $this->Players->name($this->SqueezePlyrID);
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." name ".urlencode($newname));
	}


	/**
	 * ip
	 * 
	 * <p>Returns the IP address (along with port number) of the player.</p>
	 * @return string
	 */
	public function ip()
	{
		return $this->Players->ip($this->SqueezePlyrID);
	}


	/**
	 * model
	 * 
	 * <p>Returns the model of the player, currently one of "transporter", "squeezebox2", "squeezebox", "slimp3", "softsqueeze", or "http" (for remote streaming connections).</p>
	 * @return string
	 */
	public function model()
	{
		return $this->Players->model($this->SqueezePlyrID);
	}


	/**
	 * isplayer
	 * 
	 * <p>Returns the if a player is a known player model. Currently know models are "transporter", "squeezebox2", "squeezebox", "slimp3", "softsqueeze", or "http" (for remote streaming connections). Will return 0 for streaming connections.</p>
	 * @return boolean
	 */
	public function isplayer()
	{
		return $this->Players->isplayer($this->SqueezePlyrID);
	}


	/**
	 * displaytype
	 * 
	 * <p>Returns the display model of the player. Graphical display types start with "graphic-", non-graphical display type with "noritake-".</p>
	 * @return string
	 */
	public function displaytype()
	{
		return $this->Players->displaytype($this->SqueezePlyrID);
	}


	/**
	 * canpoweroff
	 * 
	 * <p>Returns whether the player can be powered off or not. Current hardware players and SoftSqueeze would return 1, web clients 0.</p>
	 * @return boolean
	 */
	public function canpoweroff()
	{
		return $this->Players->canpoweroff($this->SqueezePlyrID);
	}



	/**
	 * signalstrength
	 * 
	 * <p>Returns the wireless signal strength for the player, range is 1 to 100. Returns 0 if not connected wirelessly.</p>
	 * @return integer
	 */
	public function signalstrength()
	{
		return $this->CLI->intQuery($this->SqueezePlyrID." signalstrength ?");
	}


	/**
	 * connected
	 * 
	 * <p>Returns the connected state of the player, 1 or 0 depending on the state of the TCP connection to the player. SLIMP3 players, since they use UDP, always return 1.</p>
	 * @return boolean
	 */
	public function connected()
	{
		return $this->CLI->boolQuery($this->SqueezePlyrID." connected ?");
	}


	/**
	 * sleep_time
	 * 
	 * <p>The "sleep" command specifies a number of seconds to continue playing before powering off the player. You may query the amount of time until the player sleeps by passing in "?".</p>
	 * @param string $number <number|?>
	 * @return mixed boolean (success) or string on query (sleep time).
	 */
	public function sleep_time($number = "")
	{
		if (empty($number) || $number == "?")
		{
			return $this->CLI->stringQuery($this->SqueezePlyrID." sleep ?");
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." sleep ".((string) $number));
	}


	/**
	 * sync
	 * 
	 * <p>The "sync" command specifies the player to synchronise with the given playerid. The command accepts only one playerindex or playerid. To unsync the player, use the "-" parameter. Note that in both cases the first <playerid> is the player which is already a member of a sync group. When adding a player to a sync group, the second specified player will be added to the group which includes the first player, if necessary first removing the second player from its existing sync-group. You may query which players are already synced with this player by passing in a "?" parameter. Multiple playerids are separated by a comma. If the player is not synced, "-" is returned.</p>
	 * @param string $playerindex_id
	 * @return mixed boolean (success) or string on query.
	 */
	public function sync($playerindex_id = "")
	{
		if (empty($playerindex_id) || $playerindex_id == "?")
		{
			return $this->CLI->stringQuery($this->SqueezePlyrID." sync ?");
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." sync ".((string) $playerindex_id));
	}

	/**
	 * syncgroups
	 * 
	 * <p>The "syncgroups" query returns a comma separated list of sync groups members (IDs and names).</p>
	 * @return string
	 */
	public function syncgroups()
	{
		return $this->Players->syncgroups();
	}


	/**
	 * power
	 * 
	 * <p>The "power" command turns the player on or off. Use 0 to turn off, 1 to turn on, ? to query and no parameter to toggle the power state of the player. For remote streaming connections, the command does nothing and the query always returns 1.</p>
	 * @param string $state
	 * @return boolean
	 */
	public function power($state = "")
	{
		if (!in_array($state,array("","0","1","?")))
		{
			user_error("power() only accepts values <0|1|?|>.");
			return false;
		}
		if ($state == "?")
		{
			return $this->CLI->boolQuery($this->SqueezePlyrID." power ?");
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." power ".$state);
	}


	/**
	 * rate
	 * 
	 * <p>The "rate" command returns or sets the current play rate for the player. 1 is normal, 2 is 2x fast forward, 0 is paused, -1 is rewind, etc.</p>
	 * @param string $rate <rate|?>
	 * @return mixed boolean (success) or integer on query.
	 */
	public function rate($rate = "")
	{
		if (empty($rate) || $rate == "?")
		{
			return $this->CLI->intQuery($this->SqueezePlyrID." rate ?");
		}
		if (!is_numeric($rate))
		{
			user_error("rate() only accepts numeric values or ?.");
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." rate ".$rate);
	}


	/**
	 * show
	 * 
	 * <p>The "show" command displays a message on the player display for a given duration. Various options are provided to customize the appearance of the message (font size, centering). If the mesage is too long to fit on the display, it scrolls. This command is designed to display the message, and by default temporarily cancels any screensaver and increases the brightness to the maximum value. This command is only echoed once the message display is done.</p>
	 * @param string $line1 First line of the display.
	 * @param string $line2 Second line of the display. This is the line used for single line display mode (font = huge).
	 * @param integer $duration Time in seconds to display the message; this time does not take into account any scrolling time necessary, which will be performed to its completion. The default is 3 seconds.
	 * @param string $brightness Brightness to use to display the message, either 'powerOn', 'powerOff', 'idle' or a value from 0 to 4. The default value is 4. The display brightness is reset to its configured value after the message.
	 * @param string $font Use value "huge" to have line2 displayed on a large font using the entire display. The actual font used depends on the player model. Otherwise the command uses the standard, 2 lines display font.
	 * @param string $centered Use value "1" to center the lines on the display. There is no scrolling in centered mode.
	 * @return boolean (success)
	 */
	public function show($line1 = "", $line2 = "", $duration = 3, $brightness = "4", $font = "", $centered = "")
	{
		// @todo fireAndForgetQuery??
		$fnArgs = func_get_args();
		return $this->CLI->pingQuery($this->SqueezePlyrID." show ".$this->CLI->argsToTaggedParams(array(0=>"line1", 1=>"line2", 2=>"duration", 3=>"brightness", 4=>"font", 5=>"centered"),$fnArgs));
	}

	/**
	 * display
	 * 
	 * <p>The "display" command specifies some text to be displayed on the player screen for a specified amount of time (in seconds).</p>
	 * @param string $line1 First line of the display.
	 * @param string $line2 Second line of the display.
	 * @param integer $duration Time in seconds to display the message.
	 * @return boolean (success)
	 */
	public function display($line1 = "", $line2 = "", $duration = 3)
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." show ".urlencode($line1)." ".urlencode($line2)." ".((string) $duration));
	}


	/**
	 * display_current
	 * 
	 * <p>The "display ? ?" command may be used to obtain the text that is currently displayed on the screen.</p>
	 * @return string
	 */
	public function display_current()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." display ? ?");
	}


	/**
	 * displaynow
	 * 
	 * <p>The "displaynow" command provides access to the data currently on the display. This differs from the "display ? ?" display_current() command in that it returns the latest data sent to the display, including any animation, double-size fonts, etc...</p>
	 * @return string
	 */
	public function displaynow()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." displaynow ? ?");
	}


	/**
	 * playerpref
	 * 
	 * <p>The "playerpref" command allows the caller to set and query the SqueezeCenter's internal player-specific preferences values.</p>
	 * @param string $prefname <prefname|namespace:prefname>
	 * @param string $prefvalue <prefvalue|?>
	 * @return mixed boolean (success) or string on query.
	 */
	public function playerpref($prefname = "",$prefvalue = "")
	{
		if (empty($prefname))
		{
			user_error("playerpref() empty prefname.");
			return false;
		}
		if (empty($prefvalue) || $prefvalue == "?")
		{
			return $this->CLI->stringQuery($this->SqueezePlyrID." playerpref ".urlencode($prefname)." ?");
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playerpref ".urlencode($prefname)." ".urlencode($prefvalue));
	}


	/**
	 * playerpref_validate
	 * 
	 * <p>The "playerpref validate" command allows the caller to validate a SqueezeCenter's internal player-specific preference value without setting it.</p>
	 * @param string $prefname <prefname|namespace:prefname>
	 * @param string $prefvalue <prefvalue>
	 * @return boolean (success)
	 */
	public function playerpref_validate($prefname = "",$prefvalue = "")
	{
		if (empty($prefname))
		{
			user_error("playerpref_validate() empty prefname.");
			return false;
		}
		if (empty($prefvalue) || $prefvalue == "?")
		{
			user_error("playerpref_validate() empty prefname.");
			return false;
		}
		$result = $this->CLI->arrayQuery($this->SqueezePlyrID." playerpref validate ".urlencode($prefname)." ".urlencode($prefvalue));
		if (isset($result['valid']))
		{
			return (bool)  $result['valid'];
		}
		return false;
	}


	/**
	 * button
	 * 
	 * <p>The "button" command simulates a button press. Valid button codes correspond to the functions defined in the Default.map file.</p>
	 * @param string $buttoncode
	 * @return boolean (success)
	 */
	public function button($buttoncode = "")
	{
		if (empty($buttoncode))
		{
			user_error("button() empty buttoncode.");
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." button ".urlencode($buttoncode));
	}

	/**
	 * ir
	 * 
	 * <p>The "ir" command simulates an IR code. Valid IR codes are defined in the Default.map file.</p>
	 * @param string $ircode <ircode>
	 * @param string $time <time>
	 * @return boolean (success)
	 */
	public function ir($ircode = "",$time = "")
	{
		if (empty($ircode))
		{
			user_error("ir() empty ircode.");
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." ir ".urlencode($ircode)." ".urlencode($time));
	}

	/**
	 * irenable
	 * 
	 * <p>The "irenable" command enables or disables IR processing for the player on or off. Use 0 to disable, 1 to enable, ? to query and no parameter to toggle IR processing of the player. For remote streaming connections, the command does nothing and the query always returns 1.</p>
	 * @param string $state <0|1|?|>
	 * @return boolean
	 */
	public function irenable($state = "")
	{
		if (!in_array($state,array("","0","1","?")))
		{
			user_error("irenable() \$state only accepts string values <0|1|?|>.");
			return false;
		}
		if ($state == "?")
		{
			return $this->CLI->boolQuery($this->SqueezePlyrID." irenable ?");
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." irenable ".$state);
	}


	/**
	 * connect
	 * 
	 * <p>The "connect" command tells a Squeezebox 2 or newer player to connect to a different server address or to SqueezeNetwork.</p>
	 * @param string $ip_address
	 * @return boolean (success)
	 */
	public function connect($ip_address = "")
	{
		if (empty($ip_address))
		{
			user_error("connect() expects an ip address to another server or to SqueezeNetwork.");
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." connect ".urlencode($ip_address));
	}

	/**
	 * client_forget
	 * 
	 * <p>The "client forget" command deletes the client/player from the server database.</p>
	 * @return boolean (success)
	 */
	public function client_forget()
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." client forget");
	}


	/**
	 * disconnect
	 * 
	 * <p>The "disconnect" command tells a Squeezebox 2 or newer player on another SqueezeCenter instance to disconnect from its server and connect to another server or SqueezeNetwork.</p>
	 * @param string $ip_address
	 * @return boolean (success)
	 */
	public function disconnect($ip_address = "")
	{
		if (empty($ip_address))
		{
			user_error("disconnect() expects an ip address to another server or to SqueezeNetwork.");
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." connect ".urlencode($ip_address));
	}


	/**
	 * __get (getter)
	 *
	 * @param string $property_name
	 * @return mixed
	 */
	function __get($property_name)
	{
		if (in_array($property_name,array("Playlist","Alarms","Mixer","Players")))
		{
			switch($property_name)
			{
				case "Playlist":
					if ( !($this->Playlist instanceof SqueezePlaylist) )
					{
						$this->Playlist = new SqueezePlaylist($this->CLI,$this->SqueezePlyrID);
					}
					return $this->Playlist;
					break;
				case "Alarms":
					if ( !($this->Alarms instanceof SqueezeAlarm) )
					{
						$this->Alarms = new SqueezeAlarm($this->CLI,$this->SqueezePlyrID);
					}
					return $this->Alarms;
					break;
				case "Mixer":
					if ( !($this->Mixer instanceof SqueezeMixer) )
					{
						$this->Mixer = new SqueezeMixer($this->CLI,$this->SqueezePlyrID);
					}
					return $this->Mixer;
					break;
				case "Players":
					if ( !($this->Players instanceof SqueezePlayers) )
					{
						$this->Players = new SqueezePlayers($this->CLI);
					}
					return $this->Players;
					break;
			}
		}
		return $this->${$property_name};
	}

	/**
	 * setPlayers
	 *
	 * @return void
	 */
	private function setPlayers()
	{
		if ( !($this->Players instanceof SqueezePlayers) )
		{
			$this->Players = new SqueezePlayers($this->CLI);
		}
	}

	/**
	 * SqueezePlayer (destructor)
	 */
	function __destruct()
	{
		unset($this);
	}
}
?>