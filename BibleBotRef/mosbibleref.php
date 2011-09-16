<?php
/**
* @version $Id: mosbibleref.php,v 1.2 2005/03/31 23:18:22 nino Exp $
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once("components/com_bible/bible.inc.php");

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botMosBibleRef' );

/**
*/
function botMosBibleRef( $published, &$row, &$params, $page=0 ) {
	$row->text = textBibleReferencesToLinks($row->text);

	return true;
}

?>