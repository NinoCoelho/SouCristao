<?php
/**
* @version $Id: category.class.php,v 1.1 2005/04/05 22:30:20 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/mos_cbDBTable.class.php");


class cbCategory extends cbDBTable {
	
	/** @var int Primary key */
	var $category_id=null;
	/** @var text */
	var $title=null;
	/** @var int */
	var $published=null;

	/**
	* @param db A database connector object
	* @param table_prefix prefix for tables
	*/
	function cbCategory( &$db, $table_prefix = 'cb_' ) {
		$this->cbDBTable( $table_prefix.'categories', 'category_id', $db );
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
						Category Title:
						<input name="title" type="text" size="50" value="<?php echo $this->title; ?>" />
					</td>
				</tr>
			</table>

			<p style="color: #000; border-top: dashed 1px #CCC;padding-top:10px;margin-top:10px;">Press save when you are finished entering all the content</p>
			<input type="hidden" name="category_id" value="<?php echo $this->category_id; ?>" />
			<input type="hidden" name="published" value="<?php echo $this->published; ?>" />
			<input type="hidden" name="task" value="saveCategory" />

		</form>

		<?php
	}

}

?>