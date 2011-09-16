<?php
// $Id: bible.php,v 1.2 2005/02/12 17:33:54 nino Exp $
/**
* Content frontend event handler
* @package Mambo Open Source
* @Copyright (C) 2000 - 2003 Miro International Pty Ltd
* @ All rights reserved
* @ Mambo Open Source is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.2 $
**/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once( $mainframe->getPath( 'front_html', 'com_bible' ) );

$sectionid = trim( mosGetParam( $_REQUEST, 'sectionid', 0 ) );
$pop = mosGetParam( $_REQUEST, 'pop', 0 );

// Editor usertype check
$access = new stdClass();
$access->canEdit = $acl->acl_check( 'action', 'edit', 'users', $my->usertype, 'content', 'all' );
$access->canEditOwn = $acl->acl_check( 'action', 'edit', 'users', $my->usertype, 'content', 'own' );
$access->canPublish = $acl->acl_check( 'action', 'publish', 'users', $my->usertype, 'content', 'all' );

$task = mosGetParam( $_REQUEST, 'task', "" );

$id = mosGetParam( $_REQUEST, 'id', 0 );
$Itemid = intval( mosGetParam( $_REQUEST, 'Itemid', 0 ) );
$limitstart = intval( mosGetParam( $_REQUEST, 'limitstart', 0 ) );

if (file_exists("components/com_bible/language/".$mosConfig_lang.".php") ) {

    include_once("components/com_bible/language/".$mosConfig_lang.".php");

} else {

    include_once("components/com_bible/language/brazilian_portuguese.php");

}


switch (strtolower($task)) {
	case "viewbooks":
		showBooks($Itemid);
		break;

	case "viewbook":
		showBook($Itemid, $id, $limitstart);
		break;

	default:
		showBooks($Itemid);
		break;
}

function showBooks($Itemid) {
	global $database, $mainframe, $mosConfig_offset;

	$noauth = !$mainframe->getCfg( 'shownoauth' );

	$database->setQuery( "SELECT ordering, bookName, location "
	. "\nFROM #__bible_book"
	. "\nORDER BY location desc, ordering, bookName "
	);

	$books = $database->loadObjectList();

	HTML_bible::showBooksList( $books, $Itemid );
}

function showBook($Itemid, $id, $limitstart) {
	global $database, $mainframe, $mosConfig_offset, $mosConfig_list_limit;

	$noauth = !$mainframe->getCfg( 'shownoauth' );

	if ($limitstart == null) $limitstart = 0;

	$database->setQuery( "SELECT bookId, ordering, bookName, location, qtdChapters "
		. "\nFROM #__bible_book"
		. "\nwhere ordering = '{$id}'"
		);

	$database->loadObject($book);

	$database->setQuery( "SELECT bookOrdering, chapter, verse, verseText "
		. "\nFROM #__bible"
		. "\nwhere bookOrdering = '{$id}'"
		. "\nand chapter = " . ($limitstart+1)
		. "\norder by verse"
		);

	$verses = $database->loadObjectList();

	HTML_bible::showBook( $Itemid, $book, $verses, $limitstart, $pageNav );
}

?>
