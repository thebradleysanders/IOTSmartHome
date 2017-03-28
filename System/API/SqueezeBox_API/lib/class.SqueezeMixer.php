<?php
/**
 * class.SqueezeMixer.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: class.SqueezeMixer.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 11/09/2009 3:12:19 PM
 * @package SqueezePHPAPI
 */

if($_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
	exit();
}

/**
 * SqueezeMixer
 *
 * <p>SqueezePlayer Mixer interface</p>
 * @author David Broz <db-dev__AT__0r9.org>
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @package SqueezePHPAPI
 */
final class SqueezeMixer
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
	 * SqueezeMixer (constructor)
	 *
	 * @param SqueezeConnection $CLI
	 * @param string $playerid
	 * @return SqueezeMixer
	 */
	function __construct(SqueezeConnection $CLI, $playerid = "")
	{
		if (empty($playerid))
		{
			user_error('SqueezeMixer (constructor) empty playerid.');
		}
		$this->CLI = &$CLI;
		$this->SqueezePlyrID = $playerid;
	}


	/**
	 * volume
	 * 
	 * <p>The "mixer volume" command returns or sets the current volume setting for the player. The scale is 0 to 100, in real numbers (i.e. 34.5 is valid). If the player is muted, the volume is returned as a negative value. Note that players display a 0 to 40 scale, that is, the 0..100 volume divided by 2,5.</p>
	 * @param string $value <0 .. 100|-100 .. +100|?>
	 * @return mixed (boolean success) or (integer value)
	 */
	public function volume($value = "")
	{
		return $this->mixer('volume',$value);
	}


	/**
	 * muting
	 * 
	 * <p>The "mixer muting" command mutes or unmutes the player. Use 0 to unmute, 1 to mute, ? to query and no parameter to toggle the muting state of the player. Note also the "mixer volume" command returns a negative value if the player is muted.</p>
	 * @param string $value <0|1|?|>
	 * @return mixed (boolean success) or (integer value)
	 */
	public function muting($value = "")
	{
		return $this->mixer('muting',$value,array("","0","1","?"),true);
	}

	/**
	 * bass
	 * 
	 * <p>The "mixer bass" command returns or sets the current bass setting for the player. This is only supported by SliMP3 and SqueezeBox (SB1) players. For more information on the 0 to 100 scale, please refer to the "mixer volume" command.</p>
	 * @param string $value <0 .. 100|-100 .. +100|?>
	 * @return mixed (boolean success) or (integer value)
	 */
	public function bass($value = "")
	{
		return $this->mixer('bass',$value);
	}


	/**
	 * treble
	 * 
	 * <p>The "mixer treble" command returns or sets the current treble setting for the player. This is only supported by SliMP3 and SqueezeBox (SB1) players. For more information on the 0 to 100 scale, please refer to the "mixer volume" command.</p>
	 * @param string $value <0 .. 100|-100 .. +100|?>
	 * @return mixed (boolean success) or (integer value)
	 */
	public function treble($value = "")
	{
		return $this->mixer('treble',$value);
	}


	/**
	 * pitch
	 * 
	 * <p>The "mixer pitch" command returns or sets the current pitch setting for the player (only supported by SqueezeBox (SB1) players).</p>
	 * @param string $value <80 .. 120|-40 .. +40|?>
	 * @return mixed (boolean success) or (integer value)
	 */
	public function pitch($value = "")
	{
		return $this->mixer('pitch',$value);
	}

	/**
	 * mixer
	 *
	 * @param string $mode
	 * @param string $value
	 * @param array $allowed
	 * @param boolean $toggle
	 * @return mixed (boolean success) or (integer value)
	 */
	private function mixer($mode,$value,$allowed = array(),$toggle = false)
	{
		if (!empty($allowed))
		{
			if (!in_array($value,$allowed))
			{
				user_error("mixer() invalid value.");
				return false;
			}
		}
		if ((!$toggle && (empty($value) || $value == "?")) || ($toggle && $value == "?"))
		{
			return $this->CLI->intQuery($this->SqueezePlyrID." mixer ".$mode." ?");
		}
		if (!is_numeric($value))
		{
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." mixer ".$mode." ".$value);
	}


	/**
	 * SqueezeMixer (destructor)
	 */
	function __destruct()
	{
		unset($this);
	}
}
?>