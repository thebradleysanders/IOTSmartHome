<?php
/**
 * class.SqueezePlaylist.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: class.SqueezePlaylist.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 09/09/2009 5:39:06 PM
 * @package SqueezePHPAPI
 */

if($_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
	exit();
}

/**
 * SqueezePlaylist
 *
 * <p>SqueezePlayer Playlist interface</p>
 * @author David Broz <db-dev__AT__0r9.org>
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @package SqueezePHPAPI
 */
final class SqueezePlaylist
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
	 * SqueezePlaylist (constructor)
	 *
	 * @param SqueezeConnection $CLI
	 * @param string $playerid
	 * @return SqueezePlaylist
	 */
	function __construct(SqueezeConnection $CLI, $playerid)
	{
		$this->CLI = &$CLI;
		$this->SqueezePlyrID = $playerid;
	}


	/**
	 * play
	 * 
	 * <p>The "playlist play" command puts the specified song URL, playlist or directory contents into the current playlist and plays starting at the first item. <br />Any songs previously in the playlist are discarded. An optional title value may be passed to set a title. This can be useful for remote URLs. The "fadeInSecs" parameter may be passed to specify fade-in period.</p>
	 * @param string $item <item>
	 * @param string $title <title>
	 * @param integer $fadeInSecs <fadeInSecs>
	 * @return boolean (success)
	 */
	public function play($item = "", $title = "", $fadeInSecs = -1)
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist play ".urlencode($item)." ".urlencode($title)." ".($fadeInSecs > 0 ? (string) $fadeInSecs : ""));
	}


	/**
	 * add
	 * 
	 * <p>The "playlist add" command adds the specified song URL, playlist or directory contents to the end of the current playlist. Songs currently playing or already on the playlist are not affected.</p>
	 * @param string $item <item>
	 * @return boolean (success)
	 */
	public function add($item = "")
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist add ".urlencode($item));
	}

	
	/**
	 * insert
	 * 
	 * <p>The "playlist insert" command inserts the specified song URL, playlist or directory contents to be played immediately after the current song in the current playlist. Any songs currently playing or already on the playlist are not affected.</p>
	 * @param string $item <item>
	 * @return boolean (success)
	 */
	public function insert($item = "")
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist insert ".urlencode($item));
	}

	
	/**
	 * deleteitem
	 * 
	 * <p>The "playlist deleteitem" command removes the specified song URL, playlist or directory contents from the current playlist.</p>
	 * @param string $item <item>
	 * @return boolean (success)
	 */
	public function deleteitem($item = "")
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist deleteitem ".urlencode($item));
	}


	/**
	 * move
	 * 
	 * <p>The "playlist move" command moves the song at the specified index to a new index in the playlist. An offset of zero is the first song in the playlist.</p>
	 * @param integer $fromindex <fromindex>
	 * @param integer $toindex <toindex>
	 * @return boolean (success)
	 */
	public function move($fromindex = 0, $toindex = 5)
	{
		if ($fromindex < 0 || $toindex < 0)
		{
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist move ".((string) $fromindex)." ".((string) $toindex));
	}


	/**
	 * delete
	 * 
	 * <p>The "playlist delete" command deletes the song at the specified index from the current playlist.</p>
	 * @param integer $songindex <songindex>
	 * @return boolean (success)
	 */
	public function delete($songindex = -1)
	{
		if ($songindex < 0)
		{
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist delete ".((string) $songindex));
	}

	
	/**
	 * resume
	 * 
	 * <p>Replace the current playlist with the playlist specified by p2, starting at the song that was playing when the file was saved. (Resuming works only with M3U files saved with the "playlist save" command below.) Shortcut: use a bare playlist name (without leading directories or trailing .m3u suffix) to load a playlist in the saved playlists folder.</p>
	 * @param string $playlist <playlist>
	 * @return boolean (success)
	 */
	public function resume($playlist = "")
	{
		if (empty($playlist))
		{
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist resume ".urlencode($playlist));
	}

	
	/**
	 * save
	 * 
	 * <p>Saves a playlist file in the saved playlists directory. Accepts a playlist filename (without .m3u suffix) and saves in the top level of the playlists directory.</p>
	 * @param string $filename <filename>  
	 * @return boolean (success)
	 */
	public function save($filename = "")
	{
		if (empty($filename))
		{
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist save ".urlencode($filename));
	}


	/**
	 * loadalbum
	 * 
	 * <p>The "playlist loadalbum" command puts songs matching the specified genre artist and album criteria on the playlist. Songs previously in the playlist are discarded.</p>
	 * @param string $genre <genre> 
	 * @param string $artist <artist>
	 * @param string $album <album>
	 * @return boolean (success)
	 */
	public function loadalbum($genre = "", $artist = "", $album = "")
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist loadalbum ".urlencode($genre)." ".urlencode($artist)." ".urlencode($album));
	}


	/**
	 * addalbum
	 * 
	 * <p>The "playlist addalbum" command appends all songs matching the specified criteria onto the end of the playlist. Songs currently playing or already on the playlist are not affected.</p>
	 * @param string $genre <genre> 
	 * @param string $artist <artist>
	 * @param string $album <album>
	 * @return boolean (success)
	 */
	public function addalbum($genre = "", $artist = "", $album = "")
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist addalbum ".urlencode($genre)." ".urlencode($artist)." ".urlencode($album));
	}

	
	/**
	 * loadtracks
	 * 
	 * <p>The "playlist loadtracks" command puts tracks matching the specified query on the playlist. Songs previously in the playlist are discarded. 
	 * Note: you must provide a particular form to the searchparam (see examples)</p>
	 * @param string $searchparam <searchparam>
	 * @return boolean (success)
	 */
	public function loadtracks($searchparam = "")
	{
		if (empty($searchparam))
		{
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist loadtracks ".urlencode($searchparam));
	}

	
	/**
	 * addtracks
	 * 
	 * <p>The "playlist addtracks" command appends all songs matching the specified criteria onto the end of the playlist. Songs currently playing or already on the playlist are not affected. Note: you must provide a particular form to the searchparam (see examples)</p>
	 * @param string $searchparam <searchparam>
	 * @return boolean (success)
	 */
	public function addtracks($searchparam = "")
	{
		if (empty($searchparam))
		{
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist addtracks ".urlencode($searchparam));
	}


	/**
	 * insertalbum
	 * 
	 * <p>The "playlist insertalbum" command inserts all songs matching the specified criteria at the top of the playlist. Songs already on the playlist are not affected.</p>
	 * @param string $genre <genre> 
	 * @param string $artist <artist>
	 * @param string $album <album>
	 * @return boolean (success)
	 */
	public function insertalbum($genre = "", $artist = "", $album = "")
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist insertalbum ".urlencode($genre)." ".urlencode($artist)." ".urlencode($album));
	}


	/**
	 * deletealbum
	 * 
	 * <p>The "playlist deletealbum" command removes songs matching the specified genre artist and album criteria from the playlist.</p>
	 * @param string $genre <genre> 
	 * @param string $artist <artist>
	 * @param string $album <album>
	 * @return boolean (success)
	 */
	public function deletealbum($genre = "", $artist = "", $album = "")
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist deletealbum ".urlencode($genre)." ".urlencode($artist)." ".urlencode($album));
	}


	/**
	 * clear
	 * 
	 * <p>The "playlist clear" command removes any song that is on the playlist. The player is stopped.</p>
	 * @return boolean (success)
	 */
	public function clear()
	{
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist clear");
	}


	/**
	 * zap
	 * 
	 * <p>The "playlist zap" command adds the song at index songindex into the zapped song playlist.</p>
	 * @param integer $songindex <songindex>
	 * @return boolean (success)
	 */
	public function zap($songindex = -1)
	{
		if ($songindex < 0)
		{
			return false;
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist zap ".((string) $songindex));
	}


	/**
	 * name
	 * 
	 * <p>The "playlist name" command returns the name of the saved playlist last loaded into the Now Playing playlist, if any.</p>
	 * @return string
	 */
	public function name()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." playlist name ?");
	}


	/**
	 * url
	 * 
	 * <p>The "playlist url" command returns the URL of the saved playlist last loaded into the Now Playing playlist, if any.</p>
	 * @return string
	 */
	public function url()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." playlist url ?");
	}


	/**
	 * modified
	 * 
	 * <p>The "playlist modified" returns the modification state of the saved playlist last loaded into the Now Playing playlist, if any. If "1", the playlist has been modified since it was loaded.</p>
	 * @return string
	 */
	public function modified()
	{
		return $this->CLI->stringQuery($this->SqueezePlyrID." playlist modified ?");
	}


	/**
	 * playlistsinfo
	 * 
	 * <p>The "playlist playlistsinfo" query returns information on the saved playlist last loaded into the Now Playing playlist, if any.
	 * @return array
	 */
	public function playlistsinfo()
	{
		return $this->CLI->arrayQuery($this->SqueezePlyrID." playlist playlistsinfo");
	}

	
	/**
	 * index
	 * 
	 * <p>The "playlist index" command sets or queries the song that is currently playing by index. When setting, a zero-based value may be used to indicate which song to play. An explicitly positive or negative number may be used to jump to a song relative to the currently playing song. If an index parameter is set then "fadeInSecs" may be passed to specify fade-in period. The value of the current song index may be obtained by passing in "?" as a parameter.</p>
	 * @param string $index <index|+index|-index|?>
	 * @param integer $fadeInSecs <fadeInSecs>
	 * @return mixed (integer on query) (boolean success)
	 */
	public function index($index = "?", $fadeInSecs = -1)
	{
		if ($index == "?")
		{
			return $this->CLI->intQuery($this->SqueezePlyrID." playlist index ?");
		}
		return $this->CLI->boolQuery($this->SqueezePlyrID." playlist index ".$index." ".($fadeInSecs > 0 ? (string)  $fadeInSecs :  ""));
	}


	/**
	 * genre
	 * 
	 * <p>The "playlist genre" returns the requested information for a given song at an index position in the current playlist.</p>
	 * @param integer $index <index>
	 * @return string
	 */
	public function genre($index = -1)
	{
		return $this->index_query('genre',$index);
	}

	
	/**
	 * artist
	 * 
	 * <p>The "playlist artist" returns the requested information for a given song at an index position in the current playlist.</p>
	 * @param integer $index <index>
	 * @return string
	 */
	public function artist($index = -1)
	{
		return $this->index_query('artist',$index);
	}


	/**
	 * album
	 * 
	 * <p>The "playlist album" returns the requested information for a given song at an index position in the current playlist.</p>
	 * @param integer $index <index>
	 * @return string
	 */
	public function album($index = -1)
	{
		return $this->index_query('album',$index);
	}


	/**
	 * title
	 * 
	 * <p>The "playlist title" returns the requested information for a given song at an index position in the current playlist.</p>
	 * @param integer $index <index>
	 * @return string
	 */
	public function title($index = -1)
	{
		return $this->index_query('title',$index);
	}


	/**
	 * path
	 * 
	 * <p>The "playlist path" returns the requested information for a given song at an index position in the current playlist.</p>
	 * @param integer $index <index>
	 * @return string
	 */
	public function path($index = -1)
	{
		return $this->index_query('path',$index);
	}


	/**
	 * remote
	 * 
	 * <p>The "playlist remote" returns the requested information for a given song at an index position in the current playlist. "playlist remote" returns 1 if the "song" is a remote stream.</p>
	 * @param integer $index <index>
	 * @return string
	 */
	public function remote($index = -1)
	{
		return $this->index_query('remote',$index);
	}


	/**
	 * duration
	 * 
	 * <p>The "playlist duration" returns the requested information for a given song at an index position in the current playlist.</p>
	 * @param integer $index <index>
	 * @return string
	 */
	public function duration($index = -1)
	{
		return $this->index_query('duration',$index);
	}


	/**
	 * index_query
	 * 
	 * <p>The "playlist genre", "playlist artist", "playlist album", "playlist title", "playlist path", "playlist remote" and "playlist duration" queries return the requested information for a given song at an index position in the current playlist. "playlist remote" returns 1 if the "song" is a remote stream.</p>
	 * @param string $mode
	 * @param integer $index <index>
	 * @return string
	 */
	private function index_query($mode = "genre",$index = -1)
	{
		if ( !is_numeric($index) || $index < 0 )
		{
			return false;
		}
		return $this->CLI->stringQuery($this->SqueezePlyrID." playlist ".$mode." ".((string) $index)." ?");
	}


	/**
	 * tracks
	 * 
	 * <p>The "playlist tracks" command returns the the total number of tracks in the current playlist</p>
	 * @return integer
	 */
	public function tracks()
	{
		return $this->CLI->intQuery($this->SqueezePlyrID." playlist tracks ?");
	}


	/**
	 * shuffle
	 * 
	 * <p>The "playlist shuffle" command is used to shuffle, unshuffle or query the shuffle state for the current playlist. A value of "0" indicates that the playlist is not shuffled, "1" indicates that the playlist is shuffled by song, and "2" indicates that the playlist is shuffled by album. Used with no parameter, the command toggles the shuffling state.</p>
	 * @param string $state <0|1|2|?|>
	 * @return boolean (success)
	 */
	public function shuffle($state = "")
	{
		if (!in_array($state, array("","?","0","1","2")))
		{
			return false;
		}
		if ($state == "?")
		{
			return $this->CLI->boolQuery($this->SqueezePlyrID." playlist shuffle ?");
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist shuffle ".$state);
	}


	/**
	 * repeat
	 * 
	 * <p>The "playlist repeat" command is used to indicate or query if the player will stop playing at the end of the playlist, repeat the current song indefinitely, or repeat the current playlist indefinitely. A value of "0" indicates that the player will stop at the end of the playlist, "1" indicates that the player will repeat the current song indefinitely and a value of "2" indicates that the player will repeat the entire playlist indefinitely. Used with no parameter, the command toggles the repeat state.</p>
	 * @param string $state <0|1|2|?|>
	 * @return boolean (success)
	 */
	public function repeat($state = "")
	{
		if (!in_array($state, array("","?","0","1","2")))
		{
			return false;
		}
		if ($state == "?")
		{
			return $this->CLI->boolQuery($this->SqueezePlyrID." playlist repeat ?");
		}
		return $this->CLI->pingQuery($this->SqueezePlyrID." playlist repeat ".$state);
	}


	/**
	 * playlistcontrol
	 * 
	 * <p>The "playlistcontrol" command enables playlist operations using IDs as returned by extended CLI queries (titles, artists, playlists, etc).</p>
	 * @param string $cmd Command to perform on the playlist, one of "load", "add", "insert" or "delete". This parameter is mandatory. If no additional parameter is provided, the entire DB is loaded/added/inserted/deleted.
	 * @param string $genre_id Genre ID, to restrict the results to the titles of that genre.
	 * @param string $artist_id Artist ID, to restrict the results to the titles of that artist.
	 * @param string $album_id Album ID, to restrict the results to the titles of that album.
	 * @param string $track_id Comma-separated list of track IDs, to restrict the results to these track_ids. If this parameter is provided, then any genre_id, artist_id and/or album_id parameter is ignored. The tracks are added to the playlist in the given order.
	 * @param string $year Year, to restrict the results to the given year.
	 * @param string $playlist_id Playlist ID, to restrict the results to this playlist_id. If this parameter is provided, then any genre_id, artist_id, album_id and/or track_id parameter is ignored.
	 * @param string $folder_id Folder ID, to restrict the results to files in this folder_id. If this parameter is provided, then any all the others are ignored. Note that "cmd:delete" is not supported for folders.
	 * @param string $playlist_name Playlist name, to restrict the results to this playlist_name. If this parameter is provided, then any genre_id, artist_id, album_id, track_id and/or playlist_id parameter is ignored.
	 * @return array <taggedParameters>  
	 */
	public function playlistcontrol($cmd = "", $genre_id = "", $artist_id = "", $album_id = "", $track_id = "", $year = "", $playlist_id = "", $folder_id = "", $playlist_name = "")
	{
		$cmd = strtolower($cmd);
		if (!in_array($cmd,array("load", "add", "insert" ,"delete")))
		{
			return false;
		}
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery($this->SqueezePlyrID." playlistcontrol ".$this->CLI->argsToTaggedParams(array(0=>"cmd", 1=>"genre_id", 2=>"artist_id", 3=>"album_id", 4=>"track_id", 5=>"year", 6=>"playlist_id", 7=>"folder_id",8=>"playlist_name"),$fnArgs));
	}

	
	/**
	 * SqueezePlaylist (destructor)
	 */
	function __destruct()
	{
		unset($this);
	}
}
?>