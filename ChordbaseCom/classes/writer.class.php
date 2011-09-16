<?php
/**
* @version $Id: writer.class.php,v 1.1 2005/04/05 22:30:19 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/mos_cbDBTable.class.php");


class cbWriter extends cbDBTable {
	
	/** @var int Primary key */
	var $writer_id=null;
	/** @var text */
	var $name=null;
	/** @var int */
	var $published=null;

	/**
	* @param db A database connector object
	* @param table_prefix prefix for tables
	*/
	function cbWriter( &$db, $table_prefix = 'cb_' ) {
		$this->cbDBTable( $table_prefix.'writers', 'writer_id', $db );
	}

	function form( $action ){

		$key_selected[$this->song_key] = " selected";
		?>

		<form name="adminForm" method="post" action="<?php echo $action ?>">

			<input type="hidden" name="mode" value="simple" />

			<!-- BEGIN META -->
			<table cellspacing="2" cellpadding="2" border="0">
				<tr>
					<td>
						Writer Name:
						<input name="name" type="text" size="50" value="<?php echo $this->name; ?>" />
					</td>
				</tr>
			</table>

			<p style="color: #000; border-top: dashed 1px #CCC;padding-top:10px;margin-top:10px;">Press save when you are finished entering all the content</p>
			<input type="hidden" name="writer_id" value="<?php echo $this->writer_id; ?>" />
			<input type="hidden" name="published" value="<?php echo $this->published; ?>" />
			<input type="hidden" name="task" value="saveWriter" />

		</form>

		<?php
	}

}

?>