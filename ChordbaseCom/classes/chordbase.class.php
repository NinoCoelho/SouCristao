<?php
/**
* @version $Id: chordbase.class.php,v 1.2 2005/04/07 02:20:29 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class ChordBase {

	/** @var int Primary key */
	var $database=null;
	/** @var text */
	var $prefix=null;
	/** @var text */
	var $song_table=null;
	/** @var text */
	var $category_table=null;
	/** @var text */
	var $writer_table=null;
	/** @var text */
	var $config_table=null;
	/** @var text */
	var $permission_table=null;
	/** @var text */
	var $where=null;
	/** @var text */
	var $limit=null;
	/** @var text */
	var $order_by=null;
	/** @var text */
	var $config=null;
	/** @var text */
	var $songBookListUrl=null;

	/**
	* @param db A database connector object
	* @param song_table table for songs
	* @param category_table table for categories
	* @param writers_table table for writers
	* @param table_prefix prefix for tables
	*/
	function ChordBase( &$db, $prefix = 'cb_', $song_table = 'songs', $category_table = 'categories', $writer_table = 'writers', $config_table = 'config', $permission_table = 'permissions' ) {
		$this->database = $db;
		$this->prefix = $prefix;
		$this->song_table = $song_table;
		$this->category_table = $category_table;
		$this->writer_table = $writer_table;
		$this->config_table = $config_table;
		$this->permission_table = $permission_table;

		$query = "select * from ".$this->prefix.$this->config_table." limit 1";
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		foreach($rows as $row){

			$config = $row;
		}
		// parse the permissions
		$permissions = explode("\n",$config->permissions);
		$config->permissions = array();
		foreach($permissions as $permission){

			$permission = explode(":", $permission);
			$config->permissions[$permission[0]] = pow(2,trim($permission[1]));
		}

		$this->config = $config;
	}

	function getCategories() {

		$db = $this->database;

		$query = "select * from ".$this->prefix.$this->category_table;
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		foreach($rows as $row){
			$categories[$row->category_id] = $row;
		}

		return $categories;
	}

	function getWriters() {

		$db = $this->database;

		$query = "select * from ".$this->prefix.$this->writer_table;
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		foreach($rows as $row)
			$writers[$row->writer_id] = $row;

		return $writers;
	}

	function numSongs() {

		$db = $this->database;

		// query for num rows of songs
//		$query = "select * from `#__cb_songs`"
		$query = "select #__cb_songs.* from #__cb_songs, #__cb_writers"
		. "\nWHERE #__cb_songs.writer = #__cb_writers.writer_id"
		. (count( $this->where ) ? "\nAND " . implode( ' AND ', $this->where ) : "");
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return count($rows);

	}

	function setCriteria( $criteria ) {

		global $Itemid, $mosConfig_live_site;
		global $cb_user_permission, $my;

		$this->songBookListUrl = "$mosConfig_live_site/index.php?option=com_chordbase&amp;Itemid=$Itemid";

		$where = array();

		if ($criteria["writerSearch"] != _WRITER.'...' )
		{
			$this->songBookListUrl .= "&amp;writerSearch=".$criteria["writerSearch"];
			$where[$i] = "#__cb_writers.name like '".str_replace(" ","%",$criteria["writerSearch"])."%'";
			$i++;
		}

		if ($criteria["titleSearch"] != _TITLE.'...' )
		{
			$this->songBookListUrl .= "&amp;titleSearch=".$criteria["titleSearch"];
			$where[$i] = "#__cb_songs.title like '".str_replace(" ","%",$criteria["titleSearch"])."%'";
			$i++;
		}

		if ($criteria["lyricSearch"] != _LYRIC.'...' )
		{
			$words = explode(" ",$criteria["lyricSearch"]);
			foreach ($words as $index => $word)
			{
				$words[$index] = "(#__cb_songs.words LIKE '%$word%' OR #__cb_songs.keywords LIKE '%$word%')";
			}
			$this->songBookListUrl .= "&amp;lyricSearch=".$criteria["lyricSearch"];
			$where[$i] = implode(" AND ",$words);
			$i++;
		}

		if($criteria["mode"] == "writer") {
			$this->songBookListUrl .= "&amp;mode=writer";
			// list songs with the given parameters
			$where[$i] = "`writer` = '".urldecode($writer)."'";
			$i++;

		} else if($criteria["mode"] == "title") {

			$this->songBookListUrl .= "&amp;mode=title";
			// list songs with the given parameters
			$where[$i] = "`title` LIKE '%".urldecode($criteria["search"])."%'";
			$i++;

		} else if($criteria["mode"] == "text") {

			$this->songBookListUrl .= "&amp;mode=text";
			// list songs with the given parameters
			$where[$i] = "MATCH (`title`,`song`,`keywords`,`scripture`) AGAINST ('".urldecode($criteria["search"])."')";
			$i++;

		} else if($criteria["mode"] == "category"){

			$this->songBookListUrl .= "&amp;mode=category";
			// list songs with the given parameters
			$where[$i] = "`category` = '".$category."'";
			$i++;

		} else if($criteria["mode"] == "key"){

			$this->songBookListUrl .= "&amp;mode=key";
			// list songs with the given parameters
			$where[$i] = "`song_key` = '$song_key'";
			$i++;

		} else if($criteria["mode"] == "recent"){

			$this->songBookListUrl .= "&amp;mode=recent";
			// list songs with the given parameters
			$this->order_by = "`add_time` DESC";

		} else if($criteria["mode"] == "popular"){

			$this->songBookListUrl .= "&amp;mode=popular";
			// list songs with the given parameters
			$this->order_by = "`views` DESC";

		} else {

			if($criteria["order"]["by"] == "") {
				// list songs with the given parameters
				$this->order_by = "`title` ASC";
			} else {

				$criteria["order"]["direction"] = $criteria["order"]["direction"] == ""?"ASC":$criteria["order"]["direction"];
				$this->order_by = "`".$criteria["order"]["by"]."` ".$criteria["order"]["direction"];

				$this->songBookListUrl .= "&amp;order=" . $criteria["order"]["by"]
					. "&amp;direction=" . $criteria["order"]["direction"];
			}
		}


		if( !$criteria["published_only"] ) {
			// list songs with the given parameters
			$this->songBookListUrl .= "&amp;task=unpublished";
			$where[$i] = "#__cb_songs.published = '0'";
			$i++;
		} else
		{
			$this->songBookListUrl .= "&amp;task=songlist";
			$showUnpublished = $this->has_permission("Edit Song",$cb_user_permission)
				|| $this->has_permission("Publish Song",$cb_user_permission);

			$showSelfUnpublished = $this->has_permission("Add Song",$cb_user_permission);

			if ($showSelfUnpublished) {
				$where[$i] = "(#__cb_songs.published = 1 OR (#__cb_songs.published = 0 and submitted_by = ".$my->id."))";
				$i++;
			} else if (!$showUnpublished)
			{
				$where[$i] = "#__cb_songs.published = 1";
				$i++;
			}
		}



		// if an initial is chosen
		if($criteria["initial"] != ""){
			$this->songBookListUrl .= "&amp;initial=" . $criteria["initial"];
			$where[$i] = "title LIKE '".$criteria["initial"]."%'";
			$i++;
		}

		if ( !isset($criteria["search"]) && $criteria["search"]!= "") {
			$this->songBookListUrl .= "&amp;search=" . $criteria["search"];
			$where[$i] = "(LOWER(name) LIKE '%".$criteria["search"]."%')";
			$i++;
		}

		$this->where = $where;

		$this->limit = $criteria["limitstart"].", ".$criteria["limit"];

	}

	/**
	* @param criteria an assiciative array of the list criteria
	*/
	function songlist( ) {

		$db = $this->database;

		// query for songlist
		$query = "select #__cb_songs.* from `#__cb_songs`, #__cb_writers"
		. "\nWHERE #__cb_songs.writer = #__cb_writers.writer_id"
		. (count( $this->where ) ? "\nAND " . implode( ' AND ', $this->where ) : "")
		. "\nORDER BY ".$this->order_by
		. ($this->limit != ", " ? "\nLIMIT ".$this->limit : "");

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		return $rows;

	}

	/**
	* @param criteria an assiciative array of the list criteria
	*/
	function setlist( ) {

		$db = $this->database;

		// query for songlist
		$query = "select * from `#__cb_sets`";
		/*
		. (count( $this->where ) ? "\nWHERE " . implode( ' AND ', $this->where ) : "")
		//. "\nLEFT JOIN #__cb_categories ON #__cb_songs.category = #__cb_categories.category_id"
		. "\nORDER BY ".$this->order_by;
		if($showAll){
			$query .= "\nLIMIT ".$this->limit;
		}
		*/
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		return $rows;

	}

	// verify permission
	function has_permission($auth = "", $user_permission){
		global $mainframe;
		$my = $mainframe->getUser();
		if ($my->username == "admin") return true;

		if ($user_permission == 2)
			return TRUE;

		$config = $this->config;

		$auth = ($auth == "")? 0 : $config->permissions[$auth];

		// $is_ok will be true if the user is authorized
		$is_ok = $auth & $user_permission;

		if ( $is_ok == $auth || (2 & $user_permission) == 2) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


}

?>