<?php
/**
 * class.SqueezeConnection.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: class.SqueezeConnection.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 12/09/2009 4:43:37 PM
 * @package SqueezePHPAPI
 */

if($_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
	exit();
}

/**
 * SqueezeConnection
 *
 * <p>SqueezeCenter CLI socket connection interface</p>
 * @author David Broz <db-dev__AT__0r9.org>
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @package SqueezePHPAPI
 */
final class SqueezeConnection
{
	/**
	 * _hostname
	 *
	 * @var string
	 */
	private $_hostname;

	/**
	 * _port
	 *
	 * @var string
	 */
	private $_port;

	/**
	 * _username
	 *
	 * @var string
	 */
	private $_username;

	/**
	 * _password
	 *
	 * @var string
	 */
	private $_password;

	/**
	 * _socket
	 *
	 * @var resource
	 */
	private $_socket;

	/**
	 * LINEFEED
	 *
	 * @var string
	 */
	private $LINEFEED;

	/**
	 * NULLCHAR
	 *
	 * @var string
	 */
	private $NULLCHAR;

	/**
	 * buffer
	 * 
	 * <p>Connection Buffer</p>
	 * @var string
	 */
	public $buffer;


	/**
	 * lastReadBuffer
	 * 
	 * <p>Connection Last Read Buffer</p>
	 * @var string
	 */
	public $lastReadBuffer;

	/**
	 * connected
	 * 
	 * <p>Connection State</p>
	 * @var boolean
	 */
	public $connected;

	/**
	 * security_enabled
	 * 
	 * <p>Security enabled state</p>
	 * @var boolean
	 */
	public $security_enabled;

	/**
	 * errors
	 * 
	 * <p>Error Stack</p>
	 * @var array
	 */
	public $errors;


	/**
	 * SqueezeConnection (constructor)
	 *
	 * @return SqueezeConnection
	 */
	function __construct($hostname = "localhost",$port = "9090",$username = "",$password = "")
	{
		$this->setUsername($username);
		$this->setPassword($password);
		$this->setHostname($hostname);
		$this->setPort($port);
		$this->_socket = NULL;
		$this->LINEFEED = "\n";
		$this->NULLCHAR = "\0";
	}


	/**
	 * setUsername
	 * 
	 * <p>Sets connection username</p>
	 * @param string $username
	 * @return void
	 */
	public function setUsername($username = "")
	{
		$this->_username = $username;
	}


	/**
	 * setPassword
	 * 
	 * <p>Sets connection password</p>
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password = "")
	{
		$this->_password = $password;
	}


	/**
	 * setHostname
	 * 
	 * <p>Sets connection hostname</p>
	 * @param string $hostname
	 * @return void
	 */
	public function setHostname($hostname = "")
	{
		$this->_hostname = $hostname;
	}

	/**
	 * getHostname
	 * 
	 * <p>Gets the connection hostname</p>
	 * @return string
	 */
	public function getHostname()
	{
		return $this->_hostname;
	}


	/**
	 * setPort
	 * 
	 * <p>Sets connection port</p>
	 * @param string $port
	 * @return void
	 */
	public function setPort($port = "")
	{
		$this->_port = $port;
	}


	/**
	 * getPort
	 * 
	 * <p>Gets the connection port</p>
	 * @return string
	 */
	public function getPort()
	{
		return $this->_port;
	}

	
	/**
	 * connect
	 * 
	 * <p>Establish connection to SqueezeCenter CLI socket</p>
	 * @return boolean (success)
	 */
	public function connect()
	{
		$this->errors = array();
		$this->security_enabled = NULL;
		$this->_socket = fsockopen($this->_hostname,$this->_port);
		if (!$this->_socket)
		{
			$this->errors[] = "Unable to open a socket connection: " . socket_strerror($this->_socket);
			$this->connected = false;
		}
		else
		{
			socket_set_timeout($this->_socket,10,0);
			$this->security_enabled = $this->boolQuery('pref authorize ?');
			if ($this->security_enabled)
			{
				$this->connected = $this->login($this->_username,$this->_password);
			}
			else
			{
				$this->connected = true;
			}
		}
		return $this->connected;
	}


	/**
	 * login
	 * 
	 * <p>Login Procedure for SqueezeCenter CLI</p>
	 * @param string $username
	 * @param string $password
	 * @return boolean (success)
	 */
	private function login($username,$password)
	{
		if (!$this->_socket)
		{
			$this->errors[] = "Socket is not open.";
			$this->connected = false;
			return $this->connected;
		}
		$success = false;
		if (!empty($username))
		{
			if ($this->connected)
			{
				$command = "login ".$username." ".$password;
				$result = $this->cmd($command);
				$success = (bool) ($result === "login ".$username." ******");
				if (!$success)
				{
					$this->errors[] = "Authorization is required.";
					$this->disconnect();
				}
			}
		}
		return $success;
	}


	/**
	 * readTo
	 *
	 * @param string $string readTo termination string
	 * @return boolean (success)
	 */
	private function readTo($string = "\n")
	{
		if (!$this->_socket)
		{
			$this->errors[] = "Socket is not open.";
			$this->connected = false;
			return $this->connected;
		}
		$this->readBuffer = '';
		while (true)
		{
			$char = $this->readChar();
			if ($char === false)
			{
				$this->errors[] = "Reached EOF and could not find the requested data: '".$string."'";
				return false;
			}
			if ($char === $this->NULLCHAR)
			{
				continue;
			}
			$this->readBuffer .= $char;
			$this->buffer .= $char;
			if ((substr($this->readBuffer,strlen($this->readBuffer)-strlen($string))) == $string)
			{
				return true;
			}
		}
	}


	/**
	 * readLine
	 *
	 * @return string
	 */
	private function readLine()
	{
		$this->readTo($this->LINEFEED);
		$lines = explode($this->LINEFEED,$this->readBuffer);
		$c = count($lines);
		if ($c > 0)
		{
			return $lines[0];
		}
		return "";
	}


	/**
	 * writeLine
	 *
	 * @param string $string
	 * @return boolean (success)
	 */
	private function writeLine($string)
	{
		if (!$this->_socket)
		{
			$this->errors[] = "Socket is not open.";
			$this->connected = false;
			return $this->connected;
		}
		if (fwrite($this->_socket,$string.$this->LINEFEED) < 0)
		{
			$this->errors[] = "Error writing to socket";
			return false;
		}
		return true;
	}


	/**
	 * readChar
	 * 
	 * <p>Gets character from file pointer</p>
	 * @return string
	 */
	private function readChar()
	{
		return fgetc($this->_socket);
	}


	/**
	 * cleanCommand
	 *
	 * @param string $command
	 * @return string
	 */
	private function cleanCommand($command = "")
	{
		return trim($command);
	}


	/**
	 * cmd
	 * 
	 * <p>Execute CLI command - returns the raw response from SqueezeCenter socket.</p>
	 * @param string $command
	 * @return string (boolean false on failure)
	 */
	public function cmd($command = "")
	{
		$command = $this->cleanCommand($command);
		if (!$this->_socket)
		{
			if (!$this->connect())
			{
				$this->errors[] = "Socket is not open.";
				$this->connected = false;
				return $this->connected;
			}
		}
		if (empty($command))
		{
			$this->errors[] = "Empty command.";
			return false;
		}
		$command = rtrim($command,$this->LINEFEED.$this->NULLCHAR);
		if ($this->writeLine($command))
		{
			return $this->readLine();
		}
		return false;
	}


	/**
	 * stringQuery
	 * 
	 * <p>Executes a CLI command and returns a string response.</p>
	 * @param string $command
	 * @return string
	 */
	public function stringQuery($command = "",$urldecode = true)
	{
		$command = $this->cleanCommand($command);
		$result = $this->cmd($command);
		if ($result !== false)
		{
			$command = rtrim($command,"? ");
			$return = trim(str_replace($command,"",($urldecode ? urldecode($result) : $result)));
			return $urldecode ? urldecode($return) :  $return;
		}
		return false;
	}


	/**
	 * boolQuery
	 * 
	 * <p>Executes a CLI command and returns a boolean response.</p>
	 * @param string $command
	 * @return boolean
	 */
	public function boolQuery($command = "")
	{
		return (bool) $this->stringQuery($command);
	}


	/**
	 * intQuery
	 * 
	 * <p>Executes a CLI command and returns a integer response.</p>
	 * @param string $command
	 * @return integer
	 */
	public function intQuery($command = "")
	{
		return (integer) $this->stringQuery($command);
	}


	/**
	 * intQuery
	 * 
	 * <p>Executes a CLI command and returns a associative array response.<br/>(Used for tagged responses)</p>
	 * @param string $command
	 * @return array
	 */
	public function arrayQuery($command = "")
	{
		$results = $this->stringQuery($command,false);
		if ($results !== false)
		{
			return $this->taggedParamsToArray($results);
		}
		return false;
	}


	/**
	 * splitQuery
	 * 
	 * <p>Executes a CLI command and returns a associative array response, but splits repetitive parts by delimiter.<br/>(Used for repetitive tagged responses)</p>
	 * @param string $command
	 * @param string $delimiter
	 * @param string $keyname
	 * @return array
	 */
	public function splitQuery($command = "", $delimiter = "", $keyname = "")
	{
		$data = $this->stringQuery($command,false);
		$results = array();
		if ($data !== false)
		{
				$results = $this->doSplitQuery($data,$delimiter,$keyname);
		}
		return $results;
	}

	/**
	 * doSplitQuery
	 * 
	 * @param string $data
	 * @param string $delimiter
	 * @param string $keyname
	 * @return array
	 */
	private function doSplitQuery($data,$delimiter,$keyname)
	{
		$results = array();
		$cSplit = explode("count%3A",$data);
		if (count($cSplit)>1)
		{
			$cvSplit = explode(" ",$cSplit[1],2);

			if (count($cvSplit)>0)
			{
				$s = "count:".$cvSplit[0];
			}
		}
		if (isset($s))
		{
			$data = trim(str_replace(urlencode($s),'',$data));
			$results["count"] = $cvSplit[0];
		}
		$split = explode($delimiter,$data);
		$c = count($split);
		if ($c > 0)
		{
			$results[$keyname] = array();
			for($i = 1;$i < $c;$i++)
			{
				$dump = $this->taggedParamsToArray($delimiter.$split[$i]);
				if (!isset($paramCount))
				{
					$paramCount = count($dump);
				}
				if ($paramCount == count($dump))
				{
					$results[$keyname][] = $dump;
				}
				else
				{
					$results[$keyname][] = array_slice($dump,0,$paramCount);
				}
			}
		}
		return $results;
	}

	/**
	 * searchQuery
	 * 
	 * <p>Executes a CLI command and returns a associative array response.<br />(Used for database search responses)</p>
	 * @param string $command
	 * @return array
	 */
	public function searchQuery($command = "")
	{
		$data = $this->stringQuery($command,false);
		$results = array();
		if ($data !== false)
		{
			$rawSplit = explode(" ",$data);
			foreach($rawSplit as $item)
			{
				if (!isset($term) && strpos($item,"term%3A") === 0)
				{
					$term = $item;
					continue;
				}
				if (!isset($count) && strpos($item,"count%3A") === 0)
				{
					$count = $item;
					continue;
				}
				if (!isset($contributors_count) && strpos($item,"contributors_count%3A") === 0)
				{
					$contributors_count = $item;
					$contributors = array();
					$dumpbucket = &$contributors;
					continue;
				}
				if (!isset($albums_count) && strpos($item,"albums_count%3A") === 0)
				{
					$albums_count = $item;
					$albums = array();
					$dumpbucket = &$albums;
					continue;
				}
				if (!isset($tracks_count) && strpos($item,"tracks_count%3A") === 0)
				{
					$tracks_count = $item;
					$tracks = array();
					$dumpbucket = &$tracks;
					continue;
				}
				if (isset($dumpbucket))
				{
					$dumpbucket[] = $item;
				}
			}
			if (isset($term))
			{
				$results = array_merge($results,$this->taggedParamsToArray($term));
			}
			if (isset($count))
			{
				$results = array_merge($results,$this->taggedParamsToArray($count));
			}
			if (isset($contributors))
			{
				$results = array_merge($results,$this->taggedParamsToArray($contributors_count));
				$results = array_merge($results,$this->doSplitQuery(implode(" ",$contributors),"contributor_id","contributors"));
			}
			if (isset($albums))
			{
				$results = array_merge($results,$this->taggedParamsToArray($albums_count));
				$results = array_merge($results,$this->doSplitQuery(implode(" ",$albums),"album_id","albums"));
			}
			if (isset($tracks))
			{
				$results = array_merge($results,$this->taggedParamsToArray($tracks_count));
				$results = array_merge($results,$this->doSplitQuery(implode(" ",$tracks),"track_id","tracks"));
			}
			
		}
		return $results;
	}


	/**
	 * pingQuery
	 * 
	 * <p>Executes a CLI command and returns success if the command equates the output.<br />(Used for setting direct queries)</p>
	 * @param string $command
	 * @return boolean (success)
	 */
	public function pingQuery($command = "")
	{
		$command = $this->cleanCommand($command);
		$result = $this->cmd($command);
		return ($result === $command);
	}


	/**
	 * taggedParamsToArray
	 * 
	 * <p>Converts tagged parameters to associative array.</p>
	 * @param string $params
	 * @return array
	 */
	public function taggedParamsToArray($params)
	{
		$results = array();
		$paramlist = explode(" ",$params);
		$target = &$results;
		foreach($paramlist as $paramItem)
		{
			$param = urldecode($paramItem);
			$split = explode(':',$param,2);
			if (count($split)>1)
			{
				if (!isset($target[$split[0]]))
				{
					$target[$split[0]] = $split[1];
				}
				else
				{
					$target = &$target[];
					$target[$split[0]] = $split[1];
				}
			}
		}
		return $results;
	}


	/**
	 * arrayToTaggedParams
	 * 
	 * <p>Converts an associative array to tagged parameters.</p>
	 * @param array $params
	 * @return string
	 */
	public function arrayToTaggedParams(array $params)
	{
		$results = "";
		foreach($params as $key=>$value)
		{
			if (!empty($key))
			{
				$results = $key.":".(urlencode((string) $value))." ";
			}
		}
		return trim($results);
	}


	/**
	 * argsToTaggedParams
	 * 
	 * <p>Converts matched tags/function argument pairs to tagged parameters.</p>
	 * @param array $tags
	 * @param array $arguments
	 * @return string
	 */
	public function argsToTaggedParams(array $tags,array $arguments)
	{
		$params = array();
		foreach($tags as $key=>$value)
		{
			if (isset($arguments[$key]) && !empty($arguments[$key]))
			{
				$params[$value] = (string) $arguments[$key];
			}
		}
		return $this->arrayToTaggedParams($params);
	}


	/**
	 * clearBuffer
	 * 
	 * <p>Clears the connection buffer.</p>
	 * @return void
	 */
	public function clearBuffer()
	{
		$this->buffer = "";
	}


	/**
	 * disconnect
	 * 
	 * <p>Disconnect from SqueezeCenter CLI socket</p>
	 * @return boolean (success)
	 */
	public function disconnect()
	{
		if ($this->_socket)
		{
			if (!fclose($this->_socket))
			{
				$this->errors[] = "Error while closing socket.";
				return false;
			}
			$this->_socket = NULL;
		}
		$this->connected = false;
		return !$this->connected;
	}


	/**
	 * getLastError
	 * 
	 * <p>Returns the last connection error.</p>
	 * @return string
	 */
	public function getLastError()
	{
		if ( count($this->errors) > 0 )
		{
			$err = $this->errors;
			return array_pop($err);
		}
		return "";
	}


	/**
	 * SqueezeConnection (destructor)
	 */
	function __destruct()
	{
		if ($this->connected)
		{
			$this->disconnect();
		}
		unset($this);
	}
}
?>