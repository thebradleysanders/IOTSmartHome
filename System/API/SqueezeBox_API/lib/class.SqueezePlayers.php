<?php
/**
 * class.SqueezePlayers.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: class.SqueezePlayers.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 09/09/2009 6:38:37 PM
 * @package SqueezePHPAPI
 */

if($_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
	exit();
}

/**
 * SqueezePlayers
 *
 * <p>Squeeze Center player management.</p>
 * @author David Broz <db-dev__AT__0r9.org>
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @package SqueezePHPAPI
 */
final class SqueezePlayers
{
	/**
	 * CLI
	 * 
	 * <p>Command Line Interface</p>
	 * @var SqueezeConnection
	 */
	private $CLI;


	/**
	 * SqueezePlayers (constructor)
	 *
	 * @param SqueezeConnection $CLI
	 * @return SqueezePlayers
	 */
	function __construct(SqueezeConnection $CLI)
	{
		$this->CLI = &$CLI;
	}


	/**
	 * count
	 * 
	 * <p>The "player count ?" query returns the number of players connected to the server.</p>
	 * @return integer
	 */
	public function count()
	{
		return $this->CLI->intQuery("player count ?");
	}


	/**
	 * id
	 * 
	 * <p>The "player id ?" query returns the unique identifier of a player, (<playerid> parameter of many CLI commands). For physical players this is generally the MAC address. The IP address is used for remote streams.</p>
	 * @param string $playerindex <playerindex>
	 * @return string
	 */
	public function id($playerindex = "")
	{
		return $this->CLI->stringQuery("player id ".$playerindex." ?");
	}


	/**
	 * uuid
	 * 
	 * <p>The "player uuid ?" query returns the player uuid. The uuid is used by SqueezeNetwork.</p>
	 * @param string $playerindex <playerindex>
	 * @return string
	 */
	public function uuid($playerindex = "")
	{
		return $this->CLI->stringQuery("player uuid ".$playerindex." ?");
	}


	/**
	 * name
	 * 
	 * <p>The "player name ?" query returns the human-readable name for the specified player. If the name has not been specified by the user in the Player Settings, then a default name will be used, usually the IP address.</p>
	 * @param string $playerindex_id <playerindex|playerid>
	 * @return string
	 */
	public function name($playerindex_id = "")
	{
		return $this->CLI->stringQuery("player name ".$playerindex_id." ?");
	}


	/**
	 * name
	 * 
	 * <p>The "player ip ?" query returns the IP address (along with port number) of the specified player.</p>
	 * @param string $playerindex_id <playerindex|playerid>
	 * @return string
	 */
	public function ip($playerindex_id = "")
	{
		return $this->CLI->stringQuery("player ip ".$playerindex_id." ?");
	}


	/**
	 * model
	 * 
	 * <p>The "player model ?" query returns the model of the player, currently one of "transporter", "squeezebox2", "squeezebox", "slimp3", "softsqueeze", or "http" (for remote streaming connections).</p>
	 * @param string $playerindex_id <playerindex|playerid>
	 * @return string
	 */
	public function model($playerindex_id = "")
	{
		return $this->CLI->stringQuery("player model ".$playerindex_id." ?");
	}


	/**
	 * isplayer
	 * 
	 * <p>Whether a player is a known player model. Currently know models are "transporter", "squeezebox2", "squeezebox", "slimp3", "softsqueeze", or "http" (for remote streaming connections). Will return 0 for streaming connections.</p>
	 * @param string $playerindex_id <playerindex|playerid>
	 * @return boolean
	 */
	public function isplayer($playerindex_id = "")
	{
		return  $this->CLI->boolQuery("player isplayer ".$playerindex_id." ?");
	}


	/**
	 * displaytype
	 * 
	 * <p>The "player displaytype ?" query returns the display model of the player. Graphical display types start with "graphic-", non-graphical display type with "noritake-".</p>
	 * @param string $playerindex_id <playerindex|playerid>
	 * @return string
	 */
	public function displaytype($playerindex_id = "")
	{
		return $this->CLI->stringQuery("player displaytype ".$playerindex_id." ?");
	}


	/**
	 * canpoweroff
	 * 
	 * <p>Returns wether a player can be powered off or not. Current hardware players and SoftSqueeze would return 1, web clients 0.</p>
	 * @param string $playerindex_id <playerindex|playerid>
	 * @return boolean
	 */
	public function canpoweroff($playerindex_id = "")
	{
		return  $this->CLI->boolQuery("player canpoweroff ".$playerindex_id." ?");
	}


	/**
	 * syncgroups
	 * 
	 * <p>The "syncgroups" query returns a comma separated list of sync groups members (IDs and names).</p>
	 * @return string
	 */
	public function syncgroups()
	{
		return $this->CLI->stringQuery("syncgroups ?");
	}


	/**
	 * players
	 * 
	 * <p>The "players" query returns information about all "players" (physical players as well as streaming clients) known by the SqueezeCenter.</p>
	 * @param integer $start
	 * @param integer $itemsPerResponse
	 * @param string $playerprefs Comma separated list of preference values to return (for each player).
	 * @return array
	 */
	public function players($start = 0, $itemsPerResponse = 5,$playerprefs = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->splitQuery("players ".$start." ".$itemsPerResponse." ".$this->CLI->argsToTaggedParams(array(2=>"playerprefs"),$fnArgs),"playerindex","players");
	}


	/**
	 * SqueezePlayers (destructor)
	 */
	function __destruct()
	{
		unset($this);
	}
}
?>