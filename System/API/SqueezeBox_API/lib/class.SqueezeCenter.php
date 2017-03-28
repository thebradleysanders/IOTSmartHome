<?php
/**
 * class.SqueezeCenter.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: class.SqueezeCenter.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 07/09/2009 5:01:28 PM
 * @package SqueezePHPAPI
 */

if($_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
	exit();
}


/**
 * SqueezeCenter
 *
 * <p>Squeezecenter Command Line Inteface API</p>
 * @author David Broz <db-dev__AT__0r9.org>
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @package SqueezePHPAPI
 */
final class SqueezeCenter
{
	/**
	 * CLI
	 * 
	 * <p>Command Line Interface</p>
	 * @var SqueezeConnection
	 */
	private $CLI;

	/**
	 * Database
	 * 
	 * <p>Connection to SqueezeDatabase Object of server.</p>
	 * @var SqueezeDatabase
	 */
	protected $Database;

	/**
	 * Players
	 * 
	 * <p>Connection to SqueezePlayers Object of server.</p>
	 * @var SqueezePlayers
	 */
	protected $Players;

	/**
	 * CurrentPlayer
	 * 
	 * <p>A currently selected SqueezePlayer Object</p>
	 * @var SqueezePlayer
	 */
	protected $CurrentPlayer;



	/**
	 * SqueezeCenter (constructor)
	 *
	 * @param SqueezeConnection $CLI
	 * @return SqueezeCenter
	 */
	function __construct(SqueezeConnection $CLI)
	{
		$this->CLI = &$CLI;
	}


	/**
	 * connect
	 * 
	 * <p>Connects to SqueezeCenter CLI socket</p>
	 * @return boolean
	 */
	public function connect()
	{
		if (!($this->CLI instanceof SqueezeConnection))
		{
			return false;
		}
		return $this->CLI->connect();
	}


	/**
	 * disconnect
	 * 
	 * <p>Disconnects from SqueezeCenter CLI socket</p>
	 * @return boolean
	 */
	public function disconnect()
	{
		if ($this->CLI instanceof SqueezeConnection)
		{
			return $this->CLI->disconnect();
		}
		return true;
	}


	/**
	 * can
	 * 
	 * <p>The "can" query allows the caller to determine if the command or query indicated by <request terms> is available.</p>
	 * @param string $request_terms
	 * @return boolean
	 */
	public function can($request_terms = "")
	{
		if (empty($request_terms))
		{
			user_error("can() empty request_terms.");
			return false;
		}
		$command = "can ".$request_terms." ?";
		return (bool) $this->CLI->stringQuery($command);
	}


	/**
	 * version
	 * 
	 * <p>Returns SqueezeCenter version</p>
	 * @return string
	 */
	public function version()
	{
		return $this->CLI->stringQuery("version ?");
	}


	/**
	 * listen
	 * 
	 * <p>!CURRENTLY NOT SUPPORTED</p>
	 * @return boolean
	 */
	public function listen()
	{
		return false;
	}


	/**
	 * subscribe
	 * 
	 * <p>!CURRENTLY NOT SUPPORTED</p>
	 * @return boolean
	 */
	public function subscribe()
	{
		return false;
	}


	/**
	 * pref
	 * 
	 * <p>The "pref" command allows the caller to set and query the SqueezeCenter's internal preference values.</p>
	 * @param string $prefname <prefname|namespace:prefname>
	 * @param $prefvalue <prefvalue|?>
	 * @return mixed (boolean success) OR (string from query)
	 */
	public function pref($prefname = "",$prefvalue = "")
	{
		if (empty($prefname))
		{
			user_error("pref() empty prefname.");
			return false;
		}
		$command = "pref ".$prefname." ".$prefvalue;
		if ($prefvalue == "?")
		{
			return $this->CLI->stringQuery($command);
		}
		return $this->CLI->boolQuery($command);
	}


	/**
	 * pref_validate
	 * 
	 * <p>The "pref validate" command allows the caller to validate a SqueezeCenter's internal preference value without setting it.</p>
	 * @param string $prefname <prefname|namespace:prefname>
	 * @param $prefvalue <prefvalue>
	 * @return string
	 */
	public function pref_validate($prefname = "",$prefvalue = "")
	{
		if (empty($prefname))
		{
			user_error("pref_validate() empty prefname.");
			return false;
		}
		$command = "pref validate ".$prefname." ".$prefvalue;
		return $this->CLI->stringQuery($command);
	}


	/**
	 * getstring
	 * 
	 * <p>The "getstring" command allows the caller to query one or several localized strings. String tokens can be passed as a single, concatenated value.</p>
	 * @param string $stringtoken
	 * @return string
	 */
	public function getstring($stringtoken = "")
	{
		$command = "getstring ".$stringtoken;
		return $this->CLI->stringQuery($command);
	}


	/**
	 * debug
	 * 
	 * <p>The "debug" command allows the caller to query or set the server's internal debugging categories. Use 'OFF' to silence, 'FATAL' for only seeing fatal errors, 'ERROR' for non-fatal errors, etc. Finally, using ? will query the current level for the category.</p>
	 * @param string $debug_category
	 * @param string $state
	 * @return string
	 */
	public function debug($debug_category = "", $state = "")
	{
		if (empty($debug_category))
		{
			user_error("debug() empty debug_category.");
			return false;
		}
		$state = strtoupper($state);
		$allowed = array("","OFF","FATAL","ERROR","WARN","INFO","DEBUG","?");
		if (!in_array($state,$allowed))
		{
			return false;
		}
		$command = "debug ".$debug_category." ".$state;
		return $this->CLI->stringQuery($command);
	}


	/**
	 * shutdown
	 * 
	 * <p>Shuts down the SqueezeCenter server. (WARNING!) There is no way to come back up without restarting the server manually.</p>
	 * @return boolean
	 */
	public function shutdown()
	{
		return (bool) !$this->CLI->boolQuery("shutdown");
	}


	/**
	 * __get (getter)
	 *
	 * @param string $property_name
	 * @return mixed
	 */
	function __get($property_name)
	{
		if (in_array($property_name,array("Database","Players","CurrentPlayer")))
		{
			switch($property_name)
			{
				case "Database":
					if ( !($this->Database instanceof SqueezeDatabase) )
					{
						$this->Database = new SqueezeDatabase($this->CLI);
					}
					return $this->Database;
					break;
				case "Players":
					if ( !($this->Players instanceof SqueezePlayers) )
					{
						$this->Players = new SqueezePlayers($this->CLI);
					}
					return $this->Players;
					break;
				case "CurrentPlayer":
					if ( !($this->CurrentPlayer instanceof SqueezePlayer) )
					{
						$this->setCurrentPlayer();
					}
					return $this->CurrentPlayer;
					break;
			}
		}
		return $this->${$property_name};
	}
	
	/**
	 * setCurrentPlayer
	 * 
	 * <p>Sets the current player at playerindex. Query Players for a list of players.</p>
	 * @param integer $playerindex
	 * @return void
	 */
	public function setCurrentPlayer($playerindex = 0)
	{
		$this->CurrentPlayer = new SqueezePlayer($this->CLI,$this->Players->id((string) $playerindex));
	}


	/**
	 * SqueezeCenter (destructor)
	 */
	function __destruct()
	{
		unset($this);
	}
}
?>