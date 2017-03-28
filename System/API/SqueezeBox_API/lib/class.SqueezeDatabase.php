<?php
/**
 * class.SqueezeDatabase.php
 *
 * @author David Broz <db-dev__AT__0r9.org>
 * @version $Id: class.SqueezeDatabase.php 136 2009-09-16 16:07:43Z dave $
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @since 09/09/2009 6:16:08 PM
 * @package SqueezePHPAPI
 */

if($_SERVER['SCRIPT_FILENAME'] == __FILE__ )
{
	exit();
}

/**
 * SqueezeDatabase
 *
 * <p>SqueezeCenter database commands and queries</p>
 * @author David Broz <db-dev__AT__0r9.org>
 * @copyright (c) 2009 by David Broz - {@link http://org.0r9.org Disorganization} Zurich, Switzerland
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License v2
 * @package SqueezePHPAPI
 */
final class SqueezeDatabase
{

	/**
	 * CLI
	 * 
	 * <p>Command Line Interface</p>
	 * @var SqueezeConnection
	 */
	private $CLI;



	/**
	 * SqueezeDatabase (constructor)
	 *
	 * @param SqueezeConnection $CLI
	 * @return SqueezeDatabase
	 */
	function __construct(SqueezeConnection $CLI)
	{
		$this->CLI = &$CLI;
	}


	/**
	 * rescan
	 * 
	 * <p>The "rescan" command causes SqueezeCenter to rescan the entire music library, reloading the music file information. If "playlists" is indicated ("rescan playlists"), only the playlist directory is rescanned.</p>
	 * @param string $query <|playlists|?>
	 * @return boolean (success)
	 */
	public function rescan($query = "?")
	{
		if ($query == "?")
		{
			return $this->CLI->boolQuery("rescan ?");
		}
		return $this->CLI->pingQuery("rescan".($query == "playlists" ? " ".$query:""));
	}


	/**
	 * rescanprogress
	 * 
	 * <p>The "rescanprogress" query returns details on the scanning progress.</p>
	 * @return array <taggedParameters>  <taggedParameters>
	 */
	public function rescanprogress()
	{
		return $this->CLI->arrayQuery("rescanprogress");
	}


	/**
	 * abortscan
	 * 
	 * <p>The "abortscan" command causes SqueezeCenter to cancel a running scan.</p>
	 * @return boolean (success)
	 */
	public function abortscan()
	{
		return $this->CLI->pingQuery("abortscan");
	}


	/**
	 * wipecache
	 * 
	 * <p>The "wipecache" command allows the caller to have the SqueezeCenter rescan its music library, reloading the music file information.</p>
	 * @return boolean (success)
	 */
	public function wipecache()
	{
		return $this->CLI->pingQuery("wipecache");
	}


	/**
	 * info_total_genres
	 * 
	 * <p>The "info total genres ?" query returns the number of unique genres in the server music database.</p>
	 * @return integer
	 */
	public function info_total_genres()
	{
		return $this->CLI->intQuery("info total genres ?");
	}


	/**
	 * info_total_artists
	 * 
	 * <p>The "info total artists ?" query returns the number of unique artists in the server music database.</p>
	 * @return integer
	 */
	public function info_total_artists()
	{
		return $this->CLI->intQuery("info total artists ?");
	}


	/**
	 * info_total_albums
	 * 
	 * <p>The "info total albums ?" query returns the number of unique albums in the server music database.</p>
	 * @return integer
	 */
	public function info_total_albums()
	{
		return $this->CLI->intQuery("info total albums ?");
	}


	/**
	 * info_total_songs
	 * 
	 * <p>The "info total songs ?" query returns the number of unique songs in the server music database.</p>
	 * @return integer
	 */
	public function info_total_songs()
	{
		return $this->CLI->intQuery("info total songs ?");
	}


	/**
	 * genres
	 * 
	 * <p>The "genres" query returns all genres known by the server.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $search Search string. The search is case insensitive and obeys the "Search Within Words" server parameter.
	 * @param string $artist_id Limit results to those genres proposed by the artist identified by "artist_id".
	 * @param string $album_id Limit results to those genres available on the album identified by "album_id".
	 * @param string $track_id Limit results to the genres of the track identified by "track_id". If present, other filters are ignored.
	 * @param string $year Limit results to the genres of the tracks of the given "year".
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter. The default value is empty.
	 * @return array <taggedParameters>
	 */
	public function genres($start = 0, $itemsPerResponse = 5, $search = "", $artist_id = "", $album_id = "", $track_id = "", $year = "", $tags = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->splitQuery("genres ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"search",3=>"artist_id",4=>"album_id",5=>"track_id",6=>"year",7=>"tags"),$fnArgs),"id","genres");
	}


	/**
	 * artists
	 * 
	 * <p>The "artists" query returns all artists known by the server.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $search Search substring. The search is case insensitive and obeys the "Search Within Words" server parameter.
	 * @param string $genre_id Genre ID, to restrict the results to those artists with songs of that genre.
	 * @param string $album_id Genre ID, to restrict the results to those artists with songs of that genre.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter. The default value is empty.
	 * @return array <taggedParameters>
	 */
	public function artists($start = 0, $itemsPerResponse = 5, $search = "", $genre_id = "", $album_id = "", $tags = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->splitQuery("artists ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"search",3=>"genre_id",4=>"album_id",5=>"tags"),$fnArgs),"id","artists");
	}


	/**
	 * albums
	 * 
	 * <p>The "albums" query returns all albums known by the server.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $search Search substring. The search is case insensitive and obeys the "Search Within Words" server parameter.
	 * @param string $genre_id Genre ID, to restrict the results to those albums with songs of that genre.
	 * @param string $artist_id Artist ID, to restrict the results to those albums by "artist_id".
	 * @param string $track_id Track ID, to restrict the results to the album of "track_id". If specified, all other filters are ignored.
	 * @param string $year Year, to restrict the results to those albums of that year.
	 * @param boolean $compilation Compilation, to restrict the results to those albums that are (true) or aren't (false) compilations.
	 * @param string $sort Sort order of the returned list of albums. One of "album", (the default), "new" which replicates the "New Music" browse mode of the web interface, or "artflow" which sorts by artist, year, album for use with artwork-centric interfaces.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter. The default value is "l".
	 * @return array <taggedParameters>
	 */
	public function albums($start = 0, $itemsPerResponse = 5, $search = "", $genre_id = "", $artist_id = "",  $track_id = "", $year = "", $compilation = false, $sort = "album", $tags = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->splitQuery("albums ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"search",3=>"genre_id",4=>"artist_id",5=>"track_id",6=>"year",7=>"compilation",8=>"sort",9=>"tags"),$fnArgs),"id","albums");
	}


	/**
	 * years
	 * 
	 * <p>The "years" query returns all years known by the server.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @return array <taggedParameters>
	 */
	public function years($start = 0, $itemsPerResponse = 5)
	{
		return $this->CLI->splitQuery("years ".((string) $start)." ".((string) $itemsPerResponse),"year","years");
	}


	/**
	 * musicfolder
	 * 
	 * <p>The "musicfolder" query returns the content of a given music folder, starting from the top level directory configured in SqueezeCenter.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $folder_id Browses the folder identified by "folder_id".
	 * @param string $url Browses the folder identified by "url". If the content of "url" did not happen to be in the SqueezeCenter database, it is added to it. "url" has precedence over "folder_id" if both are provided.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter. The default value is empty.
	 * @return array <taggedParameters>
	 */
	public function musicfolder($start = 0, $itemsPerResponse = 5, $folder_id = "", $url = "", $tags = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->splitQuery("musicfolder ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"folder_id",3=>"url",4=>"tags"),$fnArgs),"id","musicfolder");
	}


	/**
	 * playlists
	 * 
	 * <p>The "playlists" query returns all playlists known by the server.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $search  Search substring. The search is case insensitive and obeys the "Search Within Words" server parameter.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter. The default value is empty.
	 * @return array <taggedParameters>
	 */
	public function playlists($start = 0, $itemsPerResponse = 5, $search = "", $tags = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->splitQuery("playlists ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"search",3=>"tags"),$fnArgs),"id","playlists");
	}


	/**
	 * playlists_tracks
	 * 
	 * <p>The "playlists tracks" query returns the tracks of a given playlist.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $playlist_id Playlist ID as returned by the "playlists" query. This is a mandatory parameter.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter (see command "songinfo" for a list of possible fields and their identifying letter). The default tags value for this command is "gald".
	 * @return array <taggedParameters>
	 */
	public function playlists_tracks($start = 0, $itemsPerResponse = 5, $playlist_id = "", $tags = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->splitQuery("playlists tracks ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"playlist_id",3=>"tags"),$fnArgs),"playlist%20index","tracks");
	}


	/**
	 * playlists_rename
	 * 
	 * <p>This command renames a saved playlist.</p>
	 * @param string $playlist_id The id of the playlist to rename.
	 * @param string $newname The new name of the playlist (without .m3u).
	 * @param string $dry_run Used to check if the new name is already used by another playlist. The command performs the name check but does not overwrite the existing playlist. If a name conflict occurs, the command will return a "overwritten_playlist_id" parameter.
	 * @return array <taggedParameters>
	 */
	public function playlists_rename($playlist_id = "", $newname = "", $dry_run = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery("playlists rename ".$this->CLI->argsToTaggedParams(array(0=>"playlist_id",1=>"newname",2=>"dry_run"),$fnArgs));
	}


	/**
	 * playlists_new
	 * 
	 * <p>This command creates an empty saved playlist, to be further manipulated by other commands.</p>
	 * @param string $name The name of the playlist (without .m3u).
	 * @return array <taggedParameters>
	 */
	public function playlists_new($name = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery("playlists new ".$this->CLI->argsToTaggedParams(array(0=>"name"),$fnArgs));
	}


	/**
	 * playlists_delete
	 * 
	 * <p>This command deletes a saved playlist.</p>
	 * @param string $playlist_id The id of the playlist to delete.
	 * @return boolean (success)
	 */
	public function playlists_delete($playlist_id = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->pingQuery("playlists delete ".$this->CLI->argsToTaggedParams(array(0=>"playlist_id"),$fnArgs));
	}


	/**
	 * playlists_edit
	 * 
	 * <p>This command manipulates the track content of a saved playlist.</p>
	 * @param string $playlist_id The id of the playlist to manipulate.
	 * @param string $cmd One of "up", "down", "move", "delete" or "add", in order to move up, down, delete or add a track.
	 * @param string $index For "cmd:up", "cmd:down", "cmd:move" and "cmd:delete" the index of the track to edit.
	 * @param string $toindex For "cmd:move" the new index of the track to be moved.
	 * @param string $title For "cmd:add", the title of the track to add.
	 * @param string $url For "cmd:add", the url of the track to add.
	 * @return boolean (success)
	 */
	public function playlists_edit($playlist_id = "", $cmd = "", $index = "", $toindex = "", $title = "", $url = "")
	{
		$cmd = strtolower($cmd);
		switch($cmd)
		{
			case "up":
			case "down":
			case "move":
			case "delete":
			case "add":
				break;
			default:
				$cmd = "";
				break;
		}
		if (! (!empty($index) && ($cmd == "up" || $cmd == "down" || $cmd == "move" || $cmd == "delete")))
		{
			$index = "";
		}
		if (! (!empty($toindex) && $cmd == "move"))
		{
			$toindex = "";
		}
		if (! (!empty($title) && $cmd == "add"))
		{
			$title = "";
		}
		if (! (!empty($url) && $cmd == "add"))
		{
			$url = "";
		}
		$fnArgs = func_get_args();
		return $this->CLI->boolQuery("playlists edit ".$this->CLI->argsToTaggedParams(array(0=>"playlist_id",1=>"cmd",2=>"index",3=>"toindex",4=>"title",5=>"url"),$fnArgs));
	}


	/**
	 * songinfo
	 * 
	 * <p>The "songinfo" command returns all the information on a song known by the server. Please note that the <start> and <itemsPerResponse> parameters apply to the individual data fields below and not, as they do in other extended CLI queries, to the number or songs (or artists, genres, etc.) returned; the "songinfo" only ever returns information about a single song.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $url Song path as returned by other CLI commands. This is a mandatory parameter, except if "track_id" is provided.
	 * @param string $track_id Track ID as returned by other CLI commands. This is a mandatory parameter, except if "url" is provided.
	 * @param string $tags  Determines which tags are returned. Each returned tag is identified by a letter. The default value is all info except the url (u) and the multi-valued tags for genre(s) (G & P) and artists (A & S)
	 * @return array <taggedParameters> (bool false on failure)
	 */
	public function songinfo($start = 0, $itemsPerResponse = 5, $url = "", $track_id = "", $tags = "")
	{
		if (empty($url) && empty($track_id))
		{
			user_error("songinfo() empty url or track_id.");
			return false;
		}
		$fnArgs = func_get_args();
		return $this->CLI->arrayQuery("songinfo ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"url",3=>"track_id",4=>"tags"),$fnArgs));
	}


	/**
	 * titles
	 * 
	 * <p>The "titles" command returns all titles known by the SqueezeCenter.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $genre_id Genre ID, to restrict the results to the titles of that genre.
	 * @param string $artist_id  Artist ID, to restrict the results to the titles of that artist.
	 * @param string $album_id  Album ID, to restrict the results to the titles of that album.
	 * @param string $year Year, to restrict the results to the titles of that year.
	 * @param string $search Search substring. The search is case insensitive and obeys the "Search Within Words" server parameter.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter (see command "songinfo" for a list of possible fields and their identifying letter). The default tags value for this command is "gald".
	 * @param string $sort Sorting, one of "title" (the default) or "tracknum" (in which case the track field ("t") is added automatically to the response). Sorting by tracks is possible only if tracks are defined and for a single album.
	 * @return array <taggedParameters>
	 */
	public function titles($start = 0, $itemsPerResponse = 5, $genre_id = "", $artist_id = "", $album_id  = "", $year = "", $search = "", $tags  = "", $sort = "")
	{
		return $this->titles_songs_tracks('titles',$start,$itemsPerResponse,$genre_id,$artist_id,$album_id,$year,$search,$tags,$sort);
	}


	/**
	 * songs
	 * 
	 * <p>The "songs" command returns all titles known by the SqueezeCenter.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $genre_id Genre ID, to restrict the results to the titles of that genre.
	 * @param string $artist_id  Artist ID, to restrict the results to the titles of that artist.
	 * @param string $album_id  Album ID, to restrict the results to the titles of that album.
	 * @param string $year Year, to restrict the results to the titles of that year.
	 * @param string $search Search substring. The search is case insensitive and obeys the "Search Within Words" server parameter.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter (see command "songinfo" for a list of possible fields and their identifying letter). The default tags value for this command is "gald".
	 * @param string $sort Sorting, one of "title" (the default) or "tracknum" (in which case the track field ("t") is added automatically to the response). Sorting by tracks is possible only if tracks are defined and for a single album.
	 * @return array <taggedParameters>
	 */
	public function songs($start = 0, $itemsPerResponse = 5, $genre_id = "", $artist_id = "", $album_id  = "", $year = "", $search = "", $tags  = "", $sort = "")
	{
		return $this->titles_songs_tracks('songs',$start,$itemsPerResponse,$genre_id,$artist_id,$album_id,$year,$search,$tags,$sort);
	}


	/**
	 * tracks
	 * 
	 * <p>The "tracks" command returns all titles known by the SqueezeCenter.</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $genre_id Genre ID, to restrict the results to the titles of that genre.
	 * @param string $artist_id  Artist ID, to restrict the results to the titles of that artist.
	 * @param string $album_id  Album ID, to restrict the results to the titles of that album.
	 * @param string $year Year, to restrict the results to the titles of that year.
	 * @param string $search Search substring. The search is case insensitive and obeys the "Search Within Words" server parameter.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter (see command "songinfo" for a list of possible fields and their identifying letter). The default tags value for this command is "gald".
	 * @param string $sort Sorting, one of "title" (the default) or "tracknum" (in which case the track field ("t") is added automatically to the response). Sorting by tracks is possible only if tracks are defined and for a single album.
	 * @return array <taggedParameters>
	 */
	public function tracks($start = 0, $itemsPerResponse = 5, $genre_id = "", $artist_id = "", $album_id  = "", $year = "", $search = "", $tags  = "", $sort = "")
	{
		return $this->titles_songs_tracks('tracks',$start,$itemsPerResponse,$genre_id,$artist_id,$album_id,$year,$search,$tags,$sort);
	}


	/**
	 * titles_songs_tracks
	 *
	 * @param string $mode
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $genre_id Genre ID, to restrict the results to the titles of that genre.
	 * @param string $artist_id  Artist ID, to restrict the results to the titles of that artist.
	 * @param string $album_id  Album ID, to restrict the results to the titles of that album.
	 * @param string $year Year, to restrict the results to the titles of that year.
	 * @param string $search Search substring. The search is case insensitive and obeys the "Search Within Words" server parameter.
	 * @param string $tags Determines which tags are returned. Each returned tag is identified by a letter (see command "songinfo" for a list of possible fields and their identifying letter). The default tags value for this command is "gald".
	 * @param string $sort Sorting, one of "title" (the default) or "tracknum" (in which case the track field ("t") is added automatically to the response). Sorting by tracks is possible only if tracks are defined and for a single album.
	 * @return array <taggedParameters>
	 */
	private function titles_songs_tracks($mode = "titles", $start = 0, $itemsPerResponse = 5, $genre_id = "", $artist_id = "", $album_id  = "", $year = "", $search = "", $tags  = "", $sort = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->splitQuery($mode." ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(3=>"genre_id",4=>"artist_id",5=>"album_id",6=>"year",7=>"search",8=>"tags",9=>"sort"),$fnArgs),"id",$mode);
	}


	/**
	 * search
	 * 
	 * <p>The "search" command returns artists, albums and tracks matching a search string.<br />Please note that "start" and "itemsPerResponse" are calculated per category. If you eg. have genres and tracks with the search term in them, you'll get "itemsPerResponse" number of each of them. The total number of items returned therefore can be a multiple of "itemsPerResponse".</p>
	 * @param integer $start <start>
	 * @param integer $itemsPerResponse <itemsPerResponse>
	 * @param string $term Search string
	 * @return array <taggedParameters>
	 */
	public function search($start = 0, $itemsPerResponse = 5, $term = "")
	{
		$fnArgs = func_get_args();
		return $this->CLI->searchQuery("search ".((string) $start)." ".((string) $itemsPerResponse)." ".$this->CLI->argsToTaggedParams(array(2=>"term"),$fnArgs));
	}


	/**
	 * SqueezeDatabase (destructor)
	 */
	function __destruct()
	{
		unset($this);
	}
}
?>