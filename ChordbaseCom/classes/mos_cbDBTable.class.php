<?php
/**
* @version $Id: mos_cbDBTable.class.php,v 1.1 2005/04/05 22:30:19 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


class cbDBTable extends mosDBTable {

	function cbDBTable( $table, $key, &$db ) {
		$this->mosDBTable( $table, $key, $db );
	}
}

?>