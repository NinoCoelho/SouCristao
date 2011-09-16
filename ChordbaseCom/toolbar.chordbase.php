<?php
/**
* @version $Id: admin.chordbase.php,v 1.0 11/27/2004 $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// Restrict direct access
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once( $mainframe->getPath( 'toolbar_html' ) );

switch( $task ) {

	case "songlist":
		menuartdir::SONG_MENU();
		break;
	
	case "editSong":
		menuartdir::EDITSONG_MENU();
		break;
	
	case "defineParts":
		menuartdir::PARTS_MENU();
		break;
	
	case "newSong":
		menuartdir::EDITSONG_MENU();
		break;

	case "categoryList":
		menuartdir::CATEGORY_MENU();
		break;
	
	case "editCategory":
		menuartdir::EDITCATEGORY_MENU();
		break;
	
	case "newCategory":
		menuartdir::EDITCATEGORY_MENU();
		break;

	case "writerList":
		menuartdir::WRITER_MENU();
		break;
	
	case "editWriter":
		menuartdir::EDITWRITER_MENU();
		break;
	
	case "newWriter":
		menuartdir::EDITWRITER_MENU();
		break;

	case "setList":
		menuartdir::SET_MENU();
		break;
	
	case "editSet":
		menuartdir::EDITSET_MENU();
		break;
	
	case "newSet":
		menuartdir::EDITSET_MENU();
		break;

	case "config":
		menuartdir::CONFIG_MENU();
		break;

	case "permissions":
		menuartdir::PERMISSION_MENU();
		break;

	case "savePermissions":
		menuartdir::PERMISSION_MENU();
		break;

	default:
		break;
} ?>