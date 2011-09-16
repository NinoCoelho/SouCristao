<?php
function com_uninstall() {
	global $database;
	$query = "DROP TABLE `#__cb_songs`\n"
	. "DROP TABLE `#__cb_categories`\n"
	. "DROP TABLE `#__cb_permissions`\n"
	. "DROP TABLE `#__cb_sets`\n"
	. "DROP TABLE `#__cb_writers`\n"
	. "DROP TABLE `#__cb_config`\n"
	. "DROP TABLE `#__cb_guitar_chords`\n"
	. "DROP TABLE `#__cb_chords`\n";
	$database->setQuery( $query );
	$database->Query();
	?>

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="100%" align="left"><img src="../components/com_chordbase/images/ChordBase.png" /></td>
		</tr>
		<tr>
			<td>
				<h2>Succesfully Uninstalled</h2>
				<p>This is an alpha version of ChordBase. This means it will likely be buggy. Do not use this for procution environments. Bugs reports are appreciated, however this is not a supported version.</p>
				<p>Please email bug reports to <a href="mailto:jonathan@chordbase.com">Jonathan at Chordbase</a>.</p>
				<p>Thank you for using ChordBase, enjoy.</p>
			</td>
		</tr>
	</table>
	<?php
	unlink( $mosConfig_absolute_path."/com_chordbase/css/style.css" );
	unlink( $mosConfig_absolute_path."/com_chordbase/images/ChordBase.png" );
	unlink( $mosConfig_absolute_path."/com_chordbase/languages/english.php" );
	echo "Uninstalled.";
}
?>