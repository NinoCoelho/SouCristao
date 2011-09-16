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

class menuArtdir {

	function SONG_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::addNew( 'newSong' );
		mosMenuBar::editList( 'editSong' );
		mosMenuBar::custom( 'defineParts', 'edit.png', 'edit_f2.png', 'Define Parts' );
		mosMenuBar::deleteList( '', 'removeSong' );
		mosMenuBar::spacer();
		mosMenuBar::publishList( 'publishSong' );
		mosMenuBar::unpublishList( 'unpublishSong' );
		mosMenuBar::endTable();
	}
	
	function EDITSONG_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'saveSong' );
		mosMenuBar::back();
		mosMenuBar::endTable();
	}

	function PARTS_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'saveParts' );
		mosMenuBar::back();
		mosMenuBar::endTable();
	}

	function CATEGORY_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::addNew( 'newCategory' );
		mosMenuBar::editList( 'editCategory' );
		mosMenuBar::deleteList( '', 'removeCategory' );
		mosMenuBar::spacer();
		mosMenuBar::publishList( 'publishCategory' );
		mosMenuBar::unpublishList( 'unpublishCategory' );
		mosMenuBar::endTable();
	}
	
	function EDITCATEGORY_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'saveCategory' );
		mosMenuBar::back();
		mosMenuBar::endTable();
	}

	function WRITER_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::addNew( 'newWriter' );
		mosMenuBar::editList( 'editWriter' );
		mosMenuBar::deleteList( '', 'removeWriter' );
		mosMenuBar::spacer();
		mosMenuBar::publishList( 'publishWriter' );
		mosMenuBar::unpublishList( 'unpublishWriter' );
		mosMenuBar::endTable();
	}
	
	function EDITWRITER_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'saveWriter' );
		mosMenuBar::back();
		mosMenuBar::endTable();
	}

	function SET_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::addNew( 'newSet' );
		mosMenuBar::editList( 'editSet' );
		mosMenuBar::deleteList( '', 'removeSet' );
		mosMenuBar::spacer();
		mosMenuBar::publishList( 'publishSet' );
		mosMenuBar::unpublishList( 'unpublishSet' );
		mosMenuBar::endTable();
	}
	
	function EDITSET_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'saveSet' );
		mosMenuBar::back();
		mosMenuBar::endTable();
	}

	function CONFIG_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'savesettings' );
		mosMenuBar::back();
		mosMenuBar::endTable();
	}
	
	function PERMISSION_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::save( 'savePermissions' );
		mosMenuBar::back();
		mosMenuBar::endTable();
	}
	
	function DEFAULT_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::endTable();		
	}
}
?>