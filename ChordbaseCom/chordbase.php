<?php
/**
* @version $Id: chordbase.php,v 1.3.2.1 2005/06/10 10:45:26 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/mos_cbDBTable.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/song.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/category.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/writer.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/set.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/chordbase.class.php");
require_once( $mainframe->getPath( 'front_html' ) );

$chordBase = new ChordBase( $database,"#__cb_" );

$query = "select * from #__cb_permissions";
$database->setQuery( $query );
$rows = $database->loadObjectList();
foreach($rows as $row){

	$cb_user_permissions[$row->user_id] = $row->permission;
}

if($my->id != 0){
	$cb_user_permission = (isset($cb_user_permissions[$my->id]))?$cb_user_permissions[$my->id]:$cb_user_permissions["-2"];
// not logged in
} else {
	$cb_user_permission = $cb_user_permissions["-1"];
}


$message = mosGetParam( $_GET, 'message', "" );
if ($message != "") {
		?>
		<script>
		<!--//
		alert( "<?php echo $message; ?>" );
		//-->
		</script>
		<?php
}

$params =& new mosParameters( '' );
// global pront|pdf|email
$params->def( 'icons', $mainframe->getCfg( 'icons' ) );
switch($task) {
	case "viewSong":
		if(!$chordBase->has_permission("View Song",$cb_user_permission)){
			echo "<center><font color='red'>". _PERMISSION_DENIED ."</font></center>";
			break;
		}
		$categories = $chordBase->getCategories();
		$writers = $chordBase->getWriters();

		$song_id = mosGetParam( $_GET, 'song_id', '' );
		$key = mosGetParam( $_GET, 'key', '' );
		$showChords = mosGetParam( $_GET, 'showChords', '' );
		$showWords = mosGetParam( $_GET, 'showWords', '' );

		if(!$chordBase->has_permission("View Chords",$cb_user_permission)){
			$showChords = "no";
		}

		if(!$chordBase->has_permission("View Words",$cb_user_permission)){
			$showWords = "no";
		}

		$song = new cbSong($database,"#__cb_");
		$song->load($song_id);
		if($_REQUEST["pop"] !=1) {
			$editSong = $chordBase->has_permission("Edit Song",$cb_user_permission)
				|| ($song->published == 0 && $song->submitted_by == $my->id);
			HTML_chordbase::songHeader($showChords, $params, &$song, $editSong);
		} else {
			HTML_chordbase::printHeader($showChords, $params, &$song);
		}
		$song->songHTML($key, $showChords, $showWords, $categories, $writers);
		if($_REQUEST["pop"] !=1) {
			HTML_chordbase::footer();
		}
		$serializedSong = serialize($song);
		session_start();
		session_register($serializeSong);
		break;

	case "addSong":

		if(!$chordBase->has_permission("Add Song",$cb_user_permission)){
			HTML_chordbase::cb_header( "Error!" );
			HTML_chordbase::cb_message( _PERMISSION_DENIED );
			break;
		} else {
			editSong();
			break;
		}

	case "editSong":
		$song_id = mosGetParam( $_GET, 'song_id', '' );		$song = new cbSong($database,"#__cb_");
		$song->load($song_id);		if($chordBase->has_permission("Edit Song",$cb_user_permission)
			|| ($song->published == 0 && $song->submitted_by == $my->id)){
			$song_id = mosGetParam( $_REQUEST, 'song_id', '' );
			editSong($song_id);
			break;
		} else {
			HTML_chordbase::cb_header( "Error!" );
			HTML_chordbase::cb_message( _PERMISSION_DENIED );
			break;
		}

	case "saveSong":

		if($_POST["writer"] == "-1") {
			$query = "insert into `#__cb_writers` ( `name`, `published` ) values ( '".$_POST["new_writer"]."', '1' )";
			$database->setQuery( $query );
			$database->query();
			$_POST["writer"] = $database->insertid();
		}

		if($_POST["category"] == "-1") {
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
		mosRedirect( "index.php?option=com_chordbase&Itemid=".$Itemid."&task=viewSong&song_id=$song->song_id&message=" . _NEW_MUSIC_MESSAGE );
		break;

	case "unpublished":

		$reqArray = $_SERVER['REQUEST_METHOD'] == "GET" ? $_GET : $_POST;
		$criteria["writerSearch"] = mosGetParam( $reqArray, 'writerSearch', _WRITER.'...' );
		$criteria["titleSearch"] = mosGetParam( $reqArray, 'titleSearch', _TITLE.'...' );
		$criteria["lyricSearch"] = mosGetParam( $reqArray, 'lyricSearch', _LYRIC.'...' );

		$criteria["initial"] = mosGetParam( $_GET, 'initial', '' );
		$criteria["mode"] = mosGetParam( $_GET, 'mode', '' );
		$criteria["search"] = mosGetParam( $_GET, 'search', '' );
		$criteria["order_by"] = mosGetParam( $_GET, 'order_by', '' );
		$criteria["search"] = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$criteria["unpublished_only"] = TRUE;

		// get the limits
		$criteria["limit"] = trim( mosGetParam( $_REQUEST, 'limit', 20 ) );
		$criteria["limitstart"] = trim( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

		$chordBase->setCriteria($criteria);

		$songs =  $chordBase->songlist();

		$categories = $chordBase->getCategories();
		$writers = $chordBase->getWriters();

		include_once( "includes/pageNavigation.php" );
		if(!$criteria["showAll"]){
			$pageNav = new mosPageNav( $chordBase->numSongs(), $criteria["limitstart"], $criteria["limit"] );
		} else {
			$pageNav = new mosPageNav( $chordBase->numSongs(), 0, $chordBase->numSongs() );
		}

		$permissions["Add Song"] = $chordBase->has_permission("Add Song",$cb_user_permission);
		$permissions["Publish Song"] = $chordBase->has_permission("Publish Song",$cb_user_permission);

		HTML_chordbase::songlist( $songs, $categories, $writers, $search, &$pageNav, $permissions, $criteria );
		HTML_chordbase::footer();

		break;

	case "setlist":

		break;

	default:

		$reqArray = $_SERVER['REQUEST_METHOD'] == "GET" ? $_GET : $_POST;
		$criteria["writerSearch"] = mosGetParam( $reqArray, 'writerSearch', _WRITER.'...' );
		$criteria["titleSearch"] = mosGetParam( $reqArray, 'titleSearch', _TITLE.'...' );
		$criteria["lyricSearch"] = mosGetParam( $reqArray, 'lyricSearch', _LYRIC.'...' );

		$criteria["initial"] = mosGetParam( $_GET, 'initial', '' );
		$criteria["mode"] = mosGetParam( $_GET, 'mode', '' );
		$criteria["search"] = mosGetParam( $_GET, 'search', '' );
		$criteria["order_by"] = mosGetParam( $_GET, 'order_by', '' );
		$criteria["search"] = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$criteria["published_only"] = $task != "unpublished";
		if(strtolower($criteria["initial"]) == "all"){
			$criteria["showAll"] = TRUE;
		} else {
			$criteria["showAll"] = FALSE;
		}

		// get the limits
		$criteria["limit"] = trim( mosGetParam( $_REQUEST, 'limit', 20 ) );
		$criteria["limitstart"] = trim( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

		$chordBase->setCriteria($criteria);

		$songs =  $chordBase->songlist();

		$categories = $chordBase->getCategories();
		$writers = $chordBase->getWriters();

		include_once( "includes/pageNavigation.php" );

		$pageNav = new mosPageNav( $chordBase->numSongs(), $criteria["limitstart"], $criteria["limit"] );

		$permissions["Add Song"] = $chordBase->has_permission("Add Song",$cb_user_permission);
		$permissions["Publish Song"] = $chordBase->has_permission("Publish Song",$cb_user_permission);

		HTML_chordbase::songlist( $songs, $categories, $writers, $search, &$pageNav, $permissions, $criteria );
		HTML_chordbase::footer();

		break;
}

function editSong( $song_id = "" ) {

	global $database, $Itemid, $my;

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
	$song->form("index.php?option=com_chordbase&amp;Itemid=".$Itemid."&amp;task=saveSong", $categories, $writers,$my->id, TRUE);
	HTML_chordbase::footer();

}

?>