<?php
/**
* @version $Id: admin.chordbase.php,v 0.8a2 11/27/2004 $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/song.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/category.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/writer.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/set.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/chordbase.class.php");
require_once( $mainframe->getPath( 'admin_html' ) );

switch ($task) {

	case "songlist":
		$criteria["initial"] = mosGetParam( $_GET, 'initial', '' );
		$criteria["mode"] = mosGetParam( $_GET, 'mode', '' );
		$criteria["search"] = mosGetParam( $_GET, 'search', '' );
		$criteria["order_by"] = mosGetParam( $_GET, 'order_by', '' );
		$criteria["search"] = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$criteria["published_only"] = FALSE;

		songlist( $criteria, "#__cb_" );
		break;
	
	case "newSong":
		editSong();
		break;
  
	case "editSong":
		$song_id = mosGetParam( $_REQUEST, 'song_id', '' );
		editSong($song_id);
		break;
  
	case "defineParts":
		defineParts($cid[0]);
		break;
  
	case "saveParts":
		
		$song_num_lines = mosGetParam( $_POST, 'num_lines', 0 );
		$song = new cbSong($database,"#__cb_");
		$song->load($song_id);

		$song_body = "";
		for($i=0; $i<$song_num_lines; $i++){

			if($song_body != "")
				$song_body .= "\n";

			$line_type = mosGetParam( $_POST, 'line_type_'.$i, '' );
			$line_content = mosGetParam( $_POST, 'line_content_'.$i, '' );

			$song_body .= "[".$line_type."]".$line_content."[/".$line_type."]";
		}
		echo $song_body;
		$song->set("song", $song_body);
  
		if (!$song->store()) {
			echo "<script> alert('".$song->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		mosRedirect( "index2.php?option=com_chordbase&task=songlist" );
		break;

	case "saveSong":
		
		if($_POST["new_writer"] != "") {
			$query = "insert into `#__cb_writers` ( `name`, `published` ) values ( '".$_POST["new_writer"]."', '1' )";
			$database->setQuery( $query );
			$database->query();
			$_POST["writer"] = $database->insertid();
		}

		if($_POST["new_category"] != "") {
			$query = "insert into `#__cb_categories` ( `title`, `published` ) values ( '".$_POST["new_category"]."', '1' )";
			$database->setQuery( $query );
			$database->query();
			$_POST["category"] = $database->insertid();
		}

		$song = new cbSong( $database, "#__cb_" );
		if (!$song->bind( $_POST )) {
			echo "<script> alert('Bind error: ".$song->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$song->store()) {
			echo "<script> alert('Store error: ".$song->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		mosRedirect( "index2.php?option=com_chordbase&task=songlist" );
		break;

	case "removeSong":
		remove($cid,"songs","song_id","songlist");
		break;

	case "publishSong":
		publish( $cid, 1, "songs", "songlist", "published", "song_id" );
		break;
	
	case "unpublishSong":
		publish( $cid, 0, "songs", "songlist", "published", "song_id" );
		break;
	
	case "categoryList":
		categoryList();
		break;
	
	case "newCategory":
		HTML_chordbase::cb_header( "Category" );
		$category = new cbCategory($database,"#__cb_");
		$category->published = 1;
		$category->form("index2.php?option=com_chordbase&task=saveCategory");
		break;
  
	case "editCategory":
		HTML_chordbase::cb_header( "Category Editor" );
		$category = new cbCategory($database,"#__cb_");
		$category->load($category_id);
		$category->form("index2.php?option=com_chordbase&task=saveCategory");
		break;
  
	case "saveCategory":

		$category = new cbCategory( $database, "#__cb_" );
		if (!$category->bind( $_POST )) {
			echo "<script> alert('".$category->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$category->store()) {
			echo "<script> alert('".$category->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		mosRedirect( "index2.php?option=com_chordbase&task=categoryList" );
		break;

	case "removeCategory":
		remove($cid,"categories","category_id","categoryList");
		break;

	case "publishCategory":
		publish( $cid, 1, "categories", "categoryList", "published", "category_id" );
		break;
	
	case "unpublishCategory":
		publish( $cid, 0, "categories", "categoryList", "published", "category_id" );
		break;
	
	case "writerList":
		writerList();
		break;
	
	case "newWriter":
		HTML_chordbase::cb_header( "Writer" );
		$writer = new cbWriter($database,"#__cb_");
		$writer->published = 1;
		$writer->form("index2.php?option=com_chordbase&task=saveWriter");
		break;
  
	case "editWriter":
		HTML_chordbase::cb_header( "Writer Editor" );
		$writer = new cbWriter($database,"#__cb_");
		$writer->load($writer_id);
		$writer->form("index2.php?option=com_chordbase&task=saveWriter");
		break;
  
	case "saveWriter":

		$writer = new cbWriter( $database, "#__cb_" );
		if (!$writer->bind( $_POST )) {
			echo "<script> alert('".$writer->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$writer->store()) {
			echo "<script> alert('".$writer->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		mosRedirect( "index2.php?option=com_chordbase&task=writerList" );
		break;

	case "removeWriter":
		remove($cid,"writers","writer_id","writerList");
		break;

	case "publishWriter":
		publish( $cid, 1, "writers", "writerList", "published", "writer_id" );
		break;
	
	case "unpublishWriter":
		publish( $cid, 0, "writers", "writerList", "published", "writer_id" );
		break;
	
	case "setList":
		setList();
		break;
	
	case "newSet":
		HTML_chordbase::cb_header( "Sets" );
		$set = new cbSet($database,"#__cb_");
		$set->published = 1;
		$set->form("index2.php?option=com_chordbase&task=saveSet");
		break;
  
	case "editSet":
		HTML_chordbase::cb_header( "Set Editor" );
		$set = new cbSet($database,"#__cb_");
		$set->load($set_id);
		$set->form("index2.php?option=com_chordbase&task=saveSet");
		break;
  
	case "saveSet":

		$set = new cbSet( $database, "#__cb_" );
		if (!$set->bind( $_POST )) {
			echo "<script> alert('".$set->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$set->store()) {
			echo "<script> alert('".$set->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		mosRedirect( "index2.php?option=com_chordbase&task=setList" );
		break;

	case "removeSet":
		remove($cid,"sets","set_id","setList");
		break;

	case "publishSet":
		publish( $cid, 1, "sets", "setList", "published", "set_id" );
		break;
	
	case "unpublishSet":
		publish( $cid, 0, "sets", "setList", "published", "set_id" );
		break;
	
	case "permissions":
		
		$chordBase = new ChordBase( $database,"#__cb_" );

		$config = $chordBase->config;

		$query = "select * from #__cb_permissions";
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		foreach($rows as $row){
			
			$cb_user_permissions[$row->user_id] = $row->permission;
		}

		// visitor permissions
		$visitor_permissions = array();
		$i = 0;
		foreach ($config->permissions as $key => $value){

			$permissions[$i]["value"] = $value;
			$permissions[$i]["name"] = $key;

			$is_ok = $value & $cb_user_permissions[-1];
			if($is_ok == $value){
				$permissions[$i]["visitor_selected"] = " checked";
			}

			$is_ok = $value & $cb_user_permissions[-2];
			if($is_ok == $value){
				$permissions[$i]["default_selected"] = " checked";
			}
			$i++;
		}

		HTML_chordbase::cb_header( "Permissions" );
		HTML_chordbase::permissions( $option, $permissions );
		break;
	
	case "savePermissions":

		// set the visitor permissions
		if(isset($_POST["visitor_permissions"])){
			// add up permissions
			$permission = 0;
			foreach ($_POST["visitor_permissions"] as $value)
				$permission += $value;

			// update config query
			$query = "UPDATE `#__cb_permissions` SET `permission` = '$permission' where `user_id` = '-1'";

			$database->setQuery($query);
			$database->query();
		}

		if(isset($_POST["default_permissions"])){
			// add up permissions
			$permission = 0;
			foreach ($_POST["default_permissions"] as $value)
				$permission += $value;
			// update config query
			$query = "UPDATE `#__cb_permissions` SET `permission` = '$permission' where `user_id` = '-2'";

			$database->setQuery($query);
			$database->query();

		}

		HTML_chordbase::cb_header( "Permissions Saved" );
		HTML_chordbase::message( "Permissions Saved" );
		break;
	
	case "configuration":
		HTML_chordbase::cb_header( "Configuration" );
		HTML_chordbase::configuration( $option );
		break;
	
	case "help":
		HTML_chordbase::cb_header( "Help" );
		HTML_chordbase::showhelp( $option );
		break;
	
	default:
		$criteria["initial"] = mosGetParam( $_GET, 'initial', '' );
		$criteria["mode"] = mosGetParam( $_GET, 'mode', '' );
		$criteria["search"] = mosGetParam( $_GET, 'search', '' );
		$criteria["order_by"] = mosGetParam( $_GET, 'order_by', '' );
		$criteria["search"] = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$criteria["published_only"] = FALSE;

		songlist( $criteria, "#__cb_" );
		break;
}

function songlist ( $criteria, $table_prefix = 'cb_' ) {

	global $database, $mainframe;

	// get the limits
	$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
	$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );

	$chordBase = new ChordBase( $database,"#__cb_" );
	$chordBase->setCriteria($criteria);

	$songs =  $chordBase->songlist( $limitstart, $limit );

	$categories = $chordBase->getCategories();
	$writers = $chordBase->getWriters();

	include_once( "includes/pageNavigation.php" );
	$pageNav = new mosPageNav( $chordBase->numSongs(), $limitstart, $limit );

	HTML_chordbase::songlist( $songs, $categories, $writers, users_array(), $search, &$pageNav );

}

function editSong( $song_id = "" ) {

	global $database, $my;

	HTML_chordbase::cb_header( "Song Editor" );

	$chordBase = new ChordBase( $database,"#__cb_" );
	$chordBase->setCriteria($criteria);

	$categories = $chordBase->getCategories();
	$writers = $chordBase->getWriters();

	$song = new cbSong($database,"#__cb_");
	if($song_id){
		$song->load($song_id);
		$song->add_time = ($song->add_time == 0)?time():$song->add_time;
	} else {
		$song->add_time = time();
	}
	$song->form("index2.php", $categories, $writers, $my->id);
  
}

function defineParts( $song_id = "" ) {

	global $database, $Itemid;

	if($song_id == "")
		//mosRedirect("index.php?option=com_chordbase&amp;Itemid=$Itemid&amp;task=songlist");

	HTML_chordbase::cb_header( "Define Parts" );

	$song = new cbSong($database,"#__cb_");
	$song->load($song_id);
	$song->partsForm("index2.php");
  
}

function categoryList ( ) {

	global $database, $mainframe, $option;

	$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
	$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
	$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$search = $database->getEscaped( trim( strtolower( $search ) ) );

	$database->setQuery( "SELECT COUNT(*)"
	. "\nFROM `#__cb_categories` AS a"
	. (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
	);
	$total = $database->loadResult();
	echo $database->getErrorMsg();
  
	include_once( "includes/pageNavigation.php" );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "select #__cb_categories.* from #__cb_categories"
    . (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
    . "\nORDER BY `title` ASC"
    . "\nLIMIT $pageNav->limitstart, $pageNav->limit";
	$database->setQuery( $query );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	HTML_chordbase::categoryList( $option, $rows, $search, $pageNav );
}

function writerList ( ) {

	global $database, $mainframe, $option;

	$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
	$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
	$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$search = $database->getEscaped( trim( strtolower( $search ) ) );

	$database->setQuery( "SELECT COUNT(*)"
	. "\nFROM `#__cb_writers` AS a"
	. (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
	);
	$total = $database->loadResult();
	echo $database->getErrorMsg();
  
	include_once( "includes/pageNavigation.php" );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "select #__cb_writers.* from #__cb_writers"
    . (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
    . "\nORDER BY `name` ASC"
    . "\nLIMIT $pageNav->limitstart, $pageNav->limit";
	$database->setQuery( $query );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	HTML_chordbase::writerList( $option, $rows, $search, $pageNav );
}

function setList ( ) {

	global $database, $mainframe, $option;

	$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
	$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
	$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$search = $database->getEscaped( trim( strtolower( $search ) ) );

	$chordBase = new ChordBase( $database,"#__cb_" );
	$chordBase->setCriteria($criteria);

	$categories = $chordBase->getCategories();

	$database->setQuery( "SELECT COUNT(*)"
	. "\nFROM `#__cb_sets` AS a"
	. (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
	);
	$total = $database->loadResult();
	echo $database->getErrorMsg();
  
	include_once( "includes/pageNavigation.php" );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
	
	$query = "select #__cb_sets.* from #__cb_sets"
    . (count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : "")
    . "\nORDER BY `name` ASC"
    . "\nLIMIT $pageNav->limitstart, $pageNav->limit";
	$database->setQuery( $query );

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	HTML_chordbase::setList( $option, $rows, $categories, users_array(), $search, $pageNav );
}

function remove( $cid, $table, $key, $task ) {

	global $database, $option;
	if (count( $cid )) {
		$cids = implode( ',', $cid );
		$query = "DELETE FROM #__cb_$table WHERE $key IN ($cids)";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
	}
	mosRedirect( "index2.php?option=$option&task=$task" );
}

function publish( $cid=null, $publish=1, $table, $task, $column, $key ) {

	global $database;

	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode( ',', $cid );
	$query = "UPDATE #__cb_$table SET $column='$publish' WHERE `$key` IN ($cids)";
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}
	mosRedirect( "index2.php?option=com_chordbase&task=$task" );
}

function users_array() {

	global $database, $option;

	$query = "select `id`, `username`, `name` from `#__users` where 1";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	$users = array();

	foreach( $rows as $row ){
		if($row->id != '' && $row != NULL) {
			$users[$row->id] = $row;
		}
	}
	return $users;
}

?>