<?php
/**
 * class.SqueezeAlarm.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: class.SqueezeAlarm.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 11/09/2009 4:27:21 PM
 * @package SqueezePHPAPI
 */

if($_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
	exit();
}

/**
 * SqueezeAlarm
 *
 * <p>SqueezePlayer Alarms interface</p>
 * @author David Broz <db-dev__AT__0r9.org>
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @package SqueezePHPAPI
 */
final class SqueezeAlarm
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
	 * SqueezeAlarm (constructor)
	 *
	 * @param SqueezeConnection $CLI
	 * @param string $playerid
	 * @return SqueezeAlarm
	 */
	function __construct(SqueezeConnection $CLI, $playerid)
	{
		if (empty($playerid))
		{
			user_error('SqueezeAlarm \$playerid required.');
		}
		$this->CLI = &$CLI;
		$this->SqueezePlyrID = $playerid;
	}


	/**
	 * add
	 * 
	 * <p>Adds a new alarm.</p>
	 * @param string $dow Day Of Week. 0 is Sunday, 1 is Monday, etc. up to 6 being Saturday. You can define a group of days by concatenating them with "," as separator. Default: 0-6.
	 * @param string $dowAdd Add a single day of the week to the alarm list This takes precendence over anything sent in the dow tag
	 * @param string $dowDel Removes a single day (0-6) of the week from the alarm list This takes precendence over anything sent in the dow tag
	 * @param boolean $enabled 1 if the alarm is enabled. Default: 0.
	 * @param boolean $repeat  1 if the alarm repeats. Default: 1.
	 * @param integer $time Time of the alarm, in seconds from midnight. Mandatory when add command is issued
	 * @param string $volume Mixer volume of the alarm. Default: use the default volume for alarms. Mandatory when defaultvolume command is issued
	 * @param string $url URL of the alarm playlist. Default: the current playlist. url should be a valid Squeezecenter audio url. The special value 0 means the current playlist.
	 * @return array <taggedParameters>
	 */
	public function add($dow = "0,1,2,3,4,5,6", $dowAdd  = "", $dowDel = "", $enabled = false, $repeat = true, $time = -1, $volume = "", $url = "")
	{
		$enabled = $enabled ?  "1" : "0";
		$repeat = $repeat ?  "1" : "0";
		$time = $time > 0 ? (string) $time : "";
		if (empty($time))
		{
			return false;
		}
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery($this->SqueezePlyrID." alarm add ".$this->CLI->argsToTaggedParams(array(0=>"dow", 1=>"dowAdd", 2=>"dowDel", 3=>"enabled", 4=>"repeat", 5=>"time", 6=>"volume", 7=>"url"),$fnArgs));
	}


	/**
	 * update
	 * 
	 * <p>Updates an alarm.</p>
	 * @param string $id The id of an existing alarm. This value is mandatory unless you "add" a new alarm.
	 * @param string $dow Day Of Week. 0 is Sunday, 1 is Monday, etc. up to 6 being Saturday. You can define a group of days by concatenating them with "," as separator. Default: 0-6.
	 * @param string $dowAdd Add a single day of the week to the alarm list This takes precendence over anything sent in the dow tag
	 * @param string $dowDel Removes a single day (0-6) of the week from the alarm list This takes precendence over anything sent in the dow tag
	 * @param boolean $enabled 1 if the alarm is enabled. Default: 0.
	 * @param boolean $repeat  1 if the alarm repeats. Default: 1.
	 * @param integer $time Time of the alarm, in seconds from midnight. Mandatory when add command is issued
	 * @param string $volume Mixer volume of the alarm. Default: use the default volume for alarms. Mandatory when defaultvolume command is issued
	 * @param string $url URL of the alarm playlist. Default: the current playlist. url should be a valid Squeezecenter audio url. The special value 0 means the current playlist.
	 * @return array <taggedParameters>
	 */
	public function update($id = "", $dow = "0,1,2,3,4,5,6", $dowAdd  = "", $dowDel = "", $enabled = false, $repeat = true, $time = 0, $volume = "", $url = "")
	{
		if (empty($id))
		{
			return false;
		}
		$enabled = $enabled ?  "1" : "0";
		$time = $time > 0 ? (string) $time : "";
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery($this->SqueezePlyrID." alarm update ".$this->CLI->argsToTaggedParams(array(0=>"id", 1=>"dow", 2=>"dowAdd", 3=>"dowDel", 4=>"enabled", 5=>"repeat", 6=>"time", 7=>"volume", 8=>"url"),$fnArgs));
	}


	/**
	 * delete
	 * 
	 * <p>Deletes an alarm.</p>
	 * @param string $id The id of an existing alarm. This value is mandatory unless you "add" a new alarm.
	 * @param string $dow Day Of Week. 0 is Sunday, 1 is Monday, etc. up to 6 being Saturday. You can define a group of days by concatenating them with "," as separator. Default: 0-6.
	 * @param string $dowAdd Add a single day of the week to the alarm list This takes precendence over anything sent in the dow tag
	 * @param string $dowDel Removes a single day (0-6) of the week from the alarm list This takes precendence over anything sent in the dow tag
	 * @param boolean $enabled 1 if the alarm is enabled. Default: 0.
	 * @param boolean $repeat  1 if the alarm repeats. Default: 1.
	 * @param integer $time Time of the alarm, in seconds from midnight. Mandatory when add command is issued
	 * @param string $volume Mixer volume of the alarm. Default: use the default volume for alarms. Mandatory when defaultvolume command is issued
	 * @param string $url URL of the alarm playlist. Default: the current playlist. url should be a valid Squeezecenter audio url. The special value 0 means the current playlist.
	 * @return array <taggedParameters>
	 */
	public function delete($id = "", $dow = "0,1,2,3,4,5,6", $dowAdd  = "", $dowDel = "", $enabled = false, $repeat = true, $time = 0, $volume = "", $url = "")
	{
		if (empty($id))
		{
			return false;
		}
		$enabled = $enabled ?  "1" : "0";
		$time = $time > 0 ? (string) $time : "";
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery($this->SqueezePlyrID." alarm delete ".$this->CLI->argsToTaggedParams(array(0=>"id", 1=>"dow", 2=>"dowAdd", 3=>"dowDel", 4=>"enabled", 5=>"repeat", 6=>"time", 7=>"volume", 8=>"url"),$fnArgs));
	}


	/**
	 * enableall
	 * 
	 * <p>Enables all alarms.</p>
	 * @param string $id The id of an existing alarm. This value is mandatory unless you "add" a new alarm.
	 * @param string $dow Day Of Week. 0 is Sunday, 1 is Monday, etc. up to 6 being Saturday. You can define a group of days by concatenating them with "," as separator. Default: 0-6.
	 * @param string $dowAdd Add a single day of the week to the alarm list This takes precendence over anything sent in the dow tag
	 * @param string $dowDel Removes a single day (0-6) of the week from the alarm list This takes precendence over anything sent in the dow tag
	 * @param boolean $enabled 1 if the alarm is enabled. Default: 0.
	 * @param boolean $repeat  1 if the alarm repeats. Default: 1.
	 * @param integer $time Time of the alarm, in seconds from midnight. Mandatory when add command is issued
	 * @param string $volume Mixer volume of the alarm. Default: use the default volume for alarms. Mandatory when defaultvolume command is issued
	 * @param string $url URL of the alarm playlist. Default: the current playlist. url should be a valid Squeezecenter audio url. The special value 0 means the current playlist.
	 * @return array <taggedParameters>
	 */
	public function enableall($id = "", $dow = "0,1,2,3,4,5,6", $dowAdd  = "", $dowDel = "", $enabled = false, $repeat = true, $time = 0, $volume = "", $url = "")
	{
		if (empty($id))
		{
			return false;
		}
		$enabled = $enabled ?  "1" : "0";
		$time = $time > 0 ? (string) $time : "";
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery($this->SqueezePlyrID." alarm enableall ".$this->CLI->argsToTaggedParams(array(0=>"id", 1=>"dow", 2=>"dowAdd", 3=>"dowDel", 4=>"enabled", 5=>"repeat", 6=>"time", 7=>"volume", 8=>"url"),$fnArgs));
	}


	/**
	 * disableall
	 * 
	 * <p>Disables all alarms.</p>
	 * @param string $id The id of an existing alarm. This value is mandatory unless you "add" a new alarm.
	 * @param string $dow Day Of Week. 0 is Sunday, 1 is Monday, etc. up to 6 being Saturday. You can define a group of days by concatenating them with "," as separator. Default: 0-6.
	 * @param string $dowAdd Add a single day of the week to the alarm list This takes precendence over anything sent in the dow tag
	 * @param string $dowDel Removes a single day (0-6) of the week from the alarm list This takes precendence over anything sent in the dow tag
	 * @param boolean $enabled 1 if the alarm is enabled. Default: 0.
	 * @param boolean $repeat  1 if the alarm repeats. Default: 1.
	 * @param integer $time Time of the alarm, in seconds from midnight. Mandatory when add command is issued
	 * @param string $volume Mixer volume of the alarm. Default: use the default volume for alarms. Mandatory when defaultvolume command is issued
	 * @param string $url URL of the alarm playlist. Default: the current playlist. url should be a valid Squeezecenter audio url. The special value 0 means the current playlist.
	 * @return array <taggedParameters>
	 */
	public function disableall($id = "", $dow = "0,1,2,3,4,5,6", $dowAdd  = "", $dowDel = "", $enabled = false, $repeat = true, $time = 0, $volume = "", $url = "")
	{
		if (empty($id))
		{
			return false;
		}
		$enabled = $enabled ?  "1" : "0";
		$time = $time > 0 ? (string) $time : "";
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery($this->SqueezePlyrID." alarm disableall ".$this->CLI->argsToTaggedParams(array(0=>"id", 1=>"dow", 2=>"dowAdd", 3=>"dowDel", 4=>"enabled", 5=>"repeat", 6=>"time", 7=>"volume", 8=>"url"),$fnArgs));
	}


	/**
	 * defaultvolume
	 * 
	 * <p>Sets the default volume of an alarm.</p>
	 * @param string $id The id of an existing alarm. This value is mandatory unless you "add" a new alarm.
	 * @param string $dow Day Of Week. 0 is Sunday, 1 is Monday, etc. up to 6 being Saturday. You can define a group of days by concatenating them with "," as separator. Default: 0-6.
	 * @param string $dowAdd Add a single day of the week to the alarm list This takes precendence over anything sent in the dow tag
	 * @param string $dowDel Removes a single day (0-6) of the week from the alarm list This takes precendence over anything sent in the dow tag
	 * @param boolean $enabled 1 if the alarm is enabled. Default: 0.
	 * @param boolean $repeat  1 if the alarm repeats. Default: 1.
	 * @param integer $time Time of the alarm, in seconds from midnight. Mandatory when add command is issued
	 * @param string $volume Mixer volume of the alarm. Default: use the default volume for alarms. Mandatory when defaultvolume command is issued
	 * @param string $url URL of the alarm playlist. Default: the current playlist. url should be a valid Squeezecenter audio url. The special value 0 means the current playlist.
	 * @return array <taggedParameters>
	 */
	public function defaultvolume($id = "", $dow = "0,1,2,3,4,5,6", $dowAdd  = "", $dowDel = "", $enabled = false, $repeat = true, $time = 0, $volume = "", $url = "")
	{
		if (empty($id) || empty($volume))
		{
			return false;
		}
		$enabled = $enabled ?  "1" : "0";
		$time = $time > 0 ? (string) $time : "";
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery($this->SqueezePlyrID." alarm defaultvolume ".$this->CLI->argsToTaggedParams(array(0=>"id", 1=>"dow", 2=>"dowAdd", 3=>"dowDel", 4=>"enabled", 5=>"repeat", 6=>"time", 7=>"volume", 8=>"url"),$fnArgs));
	}


	/**
	 * playlists
	 * 
	 * <p>The "alarm playlists" returns all the playlists, sounds, favorites etc. available to alarms.</p>
	 * @return array <taggedParameters>
	 */
	public function playlists()
	{
		return $this->CLI->arrayQuery("alarm playlists");
	}


	/**
	 * alarms
	 * 
	 * <p>The "alarms" query returns information about player alarms.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $dow If present, the query returns information about this Day Of Week only. Note this takes precedence over any "filter" parameter. 0 is Sunday, 1 is Monday, etc. up to 6 being Saturday.
	 * @param string $filter One of "all" or "enabled" (the default). To get all possible alarms, use "all". To get only enabled alarms, use "enabled"
	 * @return array <taggedParameters>
	 */
	public function alarms($start = 0, $itemsPerResponse = 5, $dow = "", $filter = "enabled")
	{
		$filter = strtolower($filter);
		if (!empty($filter))
		{
			if (!in_array($filter,array("all","enabled")))
			{
				$filter = "";
			}
		}
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery("alarms ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"dow",3=>"filter"),$fnArgs));
	}


	/**
	 * SqueezeAlarm (destructor)
	 */
	function __destruct()
	{
		unset($this);
	}
}
?>