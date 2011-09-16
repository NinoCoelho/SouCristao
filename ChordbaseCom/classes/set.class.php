<?php
/**
* @version $Id: set.class.php,v 1.1 2005/04/05 22:30:19 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/mos_cbDBTable.class.php");


class cbSet extends cbDBTable {
	
	/** @var int Primary key */
	var $set_id=null;
	/** @var text */
	var $name=null;
	/** @var int */
	var $category=null;
	/** @var int */
	var $creator=null;
	/** @var text */
	var $songs=null;
	/** @var text */
	var $comments=null;
	/** @var int */
	var $views=null;
	/** @var int */
	var $published=null;

	/**
	* @param db A database connector object
	* @param table_prefix prefix for tables
	*/
	function cbSet( &$db, $table_prefix = 'cb_' ) {
		$this->cbDBTable( $table_prefix.'sets', 'set_id', $db );
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
						Set Name:
						<input name="name" type="text" size="50" value="<?php echo $this->name; ?>" />
					</td>
				</tr>
			</table>

			<p style="color: #000; border-top: dashed 1px #CCC;padding-top:10px;margin-top:10px;">Press save when you are finished entering all the content</p>
			<input type="hidden" name="set_id" value="<?php echo $this->writer_id; ?>" />
			<input type="hidden" name="published" value="<?php echo $this->published; ?>" />
			<input type="hidden" name="task" value="saveWriter" />

		</form>

		<?php
	}

	function getSongArray() {
		
		return explode(",",$this->songs);
	}

	function addSong($song_id) {
		
		$this->songs = ($this->songs == "")?$song_id:$this->songs.",".$song_id;
	}

	function removeSong($song_id) {
		
		$song_sort = explode(",",$this->songs);

		$key = array_search($song_id,$song_array);

		$new_list = array();
		$space = 0;
		for($i = 0; $i < count($song_sort); $i++){

			// find the song to delete
			if($song_sort[$i] != $_GET["song_id"]){
				// swap them
				$new_list[$i-$space] = $song_sort[$i];

			} else {
				
				$space = 1;
			}
		}

		$this->songs = implode(",",$new_list);
	}

	function songUp($song_id){

		$song_sort = explode(",",$this->songs);
		
		$key = array_search($song_id,$song_sort);

		// find the song to move up
		if($key != 0){
			// swap them
			$song_sort[$key] = $song_sort[$i-1];
			$song_sort[$key-1] = $song_id;
			break;
		}

		$this->songs = implode(",",$song_sort);

	} 
	
	function songDown($song_id){

		$song_sort = explode(",",$this->songs);

		// find the song to move down
		if($key != count($this->songs)-1){
			// swap them
			$song_sort[$key] = $song_sort[$i+1];
			$song_sort[$key+1] = $song_id;
			break;
		}

		$this->songs = implode(",",$song_sort);

	}
}

?>