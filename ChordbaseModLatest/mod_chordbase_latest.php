<?php
/**
* @version $Id: mod_chordbase_latest.php,v 1.0 11/27/2004 $
* @package ChordBase Latest
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_offset, $mosConfig_live_site, $mainframe;

$count = intval( $params->get( 'count', 5 ) );
$catid = trim( $params->get( 'catid' ) );
$secid = trim( $params->get( 'secid' ) );
$show_front = $params->get( 'show_front', 1 );
$moduleclass_sfx = $params->get( 'moduleclass_sfx' );

$now = date( 'Y-m-d H:i:s', time()+$mosConfig_offset*60*60 );

$query = "SELECT a.*"
. "\n FROM #__cb_songs AS a"
. "\n WHERE ( a.published = '1' )"
. ( $catid ? "\n AND ( a.catid IN (". $catid .") )" : '' )
. ( $secid ? "\n AND ( a.sectionid IN (". $authorid .") )" : '' )
. "\n ORDER BY a.add_time DESC LIMIT $count"
;
$database->setQuery( $query );
$rows = $database->loadObjectList();

// Output
echo '<ul>';
echo '<table cellpadding="0" cellspacing="0" width="100%">';
foreach ( $rows as $row ) {
	// get Itemid
	//$Itemid = $mainframe->getItemid( $row->id, 0, 0, $bs, $bc, $gbs );
	// Blank itemid checker for SEF
	/*
	if ($Itemid == NULL) {
		$Itemid = '';
	} else {
		$Itemid = '&amp;Itemid='. $Itemid;
	}*/
	echo '<tr><td align="left" width="100%"><li><a href="'. sefRelToAbs( 'index.php?option=com_chordbase&amp;task=viewSong&amp;song_id='. $row->song_id ) .'">'. $row->title .'</a></li></td><td align="right">'.date("n/j",$row->add_time).'</td></tr>';
	//echo '<tr><td colspan="2"><div style="margin-left:10px">'.$row->introtext.'</div></td></tr>';
}
echo '</table>';
echo '</ul>';
?>