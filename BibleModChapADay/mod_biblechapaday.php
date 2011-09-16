<?php

/**
* Chapter a Day Module.
*
* @version $Id$
*/


// following line is to prevent direct access to this script via the url
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once("components/com_bible/bible.inc.php");

function loadSQLData()
{
	global $database;
	$sqlLines = explode("\n",file_get_contents("modules/ChapADay.sql"));
	for ($index = 0; $index < sizeof($sqlLines); $index++) {
		$sql = $sqlLines[$index];
		$database->setQuery($sql);
		$database->query();
	}
}

function getTodaysMessageText()
{
	global $database;
	$database->setQuery(
		"select message ".
		"from jos_bible_chap_a_day ".
		"where id = (select min(id)+mod(TO_DAYS(NOW())+795, count(*)) messageId " .
		"from jos_bible_chap_a_day)");
	$rows = $database->loadObjectList();
	if ($rows) {
		return $rows[0]->message;
	} else {
		return null;
	}
}

$message = getTodaysMessageText();
if ($message == null) {
	loadSQLData();
	$message = getTodaysMessageText();
}
echo "<div align=center>" . textBibleReferencesToLinks($message) . "</div>";
?>
<address>por Norman Berry - 1911-2001</address>

