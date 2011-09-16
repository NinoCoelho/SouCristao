<?php
/**
* @version $Id: song.class.php,v 1.2 2005/04/07 02:20:29 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/mos_cbDBTable.class.php");
require_once( $mosConfig_absolute_path."/components/com_chordbase/classes/chord.class.php");

$chordsToAdd = array();

class cbSong extends cbDBTable {

	/** @var int Primary key */
	var $song_id=null;
	/** @var text */
	var $title=null;
	/** @var text */
	var $writer=null;
	/** @var text */
	var $song=null;
	/** @var int */
	var $bpm=null;
	/** @var int */
	var $year=null;
	/** @var text */
	var $keywords=null;
	/** @var text */
	var $copyrights=null;
	/** @var text */
	var $chords=null;
	/** @var text */
	var $notes=null;
	/** @var int */
	var $category=null;
	/** @var text */
	var $song_key=null;
	/** @var text */
	var $signature=null;
	/** @var int */
	var $rating=null;
	/** @var int */
	var $views=null;
	/** @var int */
	var $ccli=null;
	/** @var int */
	var $add_time=null;
	/** @var int */
	var $submitted_by=null;
	/** @var int */
	var $published=null;
	/** @var int */
	var $words=null;

	/**
	* @param db A database connector object
	* @param table_prefix prefix for tables
	*/
	function cbSong( &$db, $table_prefix = 'cb_' ) {
		$this->cbDBTable( $table_prefix.'songs', 'song_id', $db );
	}

	function songHTML( $key = "", $showChords = "yes", $showWords = "yes", $categories = NULL, $writers = NULL ){

		$this->views++;
		$this->store();

		global $mosConfig_live_site, $Itemid;

	?>
		<div id="SongHeader">
			<h2 style="margin-bottom: 5px;border-bottom: 1px #cccccc solid;">
				<?php echo $this->title; ?>
			</h2>
			<span style="float: right"><?php echo _KEY . ': ' . $this->song_key; ?></span><?php echo _WRITER . ': ' . $writers[$this->writer]->name; ?>
			<!--
			<ul style="margin-top: 3px">
				<li><a href="songlist.php?writer="></a> - </li>
			</ul>
			-->
		</div>
		<br>
		<?php
		if($this->notes != "") {
		?>
		<div id="Notes">
			<?php echo _NOTES . "." . $this->notes; ?>
		</div>
		<?php
		}
		?>

		<div class="Song"><?php echo $this->getSongBody($key, $showChords, $showWords); ?></div>

		<hr />
		<div id="SongFooter">
		<?php
		$tabs = new mosTabs(0);
	    $tabs->startPane("content-pane");
		$tabs->startTab("Teclado","keyboard-page");
		$tabs->endTab();
		$tabs->startTab("Violão","frets-page");
		$tabs->endTab();
		$tabs->endPane();
		echo $this->getChordList();

		if($this->year || $this->copyrights != "") {
			?><p><span id="CopyYear"><?php echo $this->year ? "&copy;$this->year - " : ""; ?></span><?php echo $this->copyrights; ?></p><?php
		}

		/*if($this->ccli != "") {
			echo '<p>CCLI Song Number: '.$this->ccli.'</p>';
		}*/

		?>
		</div>
	<?php
	}

	function getSongBody($key = "", $showChords = "yes", $showWords = "yes"){
		global $mosConfig_live_site;

		$chopro = $this->song;
		$chopro = preg_replace(
			array("/\</","/\>/"),
			array("&lt;","&gt;"),
			$chopro);

		$diretRegex = array(
			"/(\{soc\}|\{start_of_chorus\}?)/i",
			"/(\{eoc\}|\{end_of_chorus\}?)/i",
			"/(\{sot\}|\{start_of_tab\}?)/i",
			"/(\{eot\}|\{end_of_tab\}?)/i",
			"/^\#(.*)/",
			"/\{(gc|guitar_comment):(.*)\}/i",
			"/\{(c|comment):(.*)\}/i"
		);

		$replaceText = array(
			"\n<div class=\"choproChorus\">\n",
			"\n</div>",
			"\n<div class=\"choproTab\">\n",
			"\n</div>",
			"\n<!-- $1 -->",
			($showChords = "yes" ? "\n<div class=\"choproGuitarComment\">$2</div>" : ""),
			"\n<div class=\"choproComment\">$2</div>"
		);

		$chopro = preg_replace($diretRegex, $replaceText, $chopro);

		$cholines = preg_split("/\n/", $chopro);

		$html = "";

		$tablatureOn = false;
		$blankLine = false;

		foreach ($cholines as $line)
		{
			if (preg_match("/^(\<div|\<\/div)/",$line))
			{
				if ($line=="<div class=\"choproTab\">")
				{
					$tablatureOn = true;
				} else if ($tablatureOn)
				{
					$tablatureOn = false;
				}
				$html .= $line;
			}
			else if ($tablatureOn)
			{
				$blankLine = false;
				$html .= "$line<br>";
			}
			else if (trim($line) == "")
			{
				//if (!$blankLine) {
					//$blankLine = true;
					$html .= "<br>";
				//}
			}
			else {

				$line = $line . "[]";
				$html .= "<table cellspacing=0 cellpadding=0>";
				if ($showChords = "yes")
				{
					$html .= "<tr id=\"lyricRow\">";
					if (!preg_match("/^\[.*].*/",$line))
						$html .= encloseChord(array("",""));
					$html .= preg_replace_callback("/(.*?)\[(.*?)\]/","encloseChord",$line);
					$html .= "</tr>";
				}
				$html .= "<tr id=\"lyricRow\">";
				$html .= preg_replace_callback("/(.*?)\[(.*?)\]/","encloseLyric",$line);
				$html .= "</tr></table>";

				$html .= "\n";

				$blankLine = false;

			}
		}

		return $html;
	}

	function getChordList()
	{
		global $mosConfig_live_site, $chordsToAdd;

		if (count($chordsToAdd))
		{
			$html = "<script language=\"Javascript1.2\" " .
					"src=\"components/com_chordbase/js/chordControl.js\">" .
					"</script>" .
					"<script language='javascript1.2'>";
			foreach ($chordsToAdd as $key => $chordObj)
			{

				if ($chordObj->getBassNote() != "")
					$bassNote = $chordObj->getNote($chordObj->getBassNote());
				else $bassNote = $chordObj->getNote($chordObj->getMainNote());

				$html .= "chord = new Chord('".
					$chordObj->getChordAsString().
					"', '".
					$chordObj->getChordAsHTML().
					"', '".
					implode("_",$chordObj->getChordBasicNotesAsNumbers()).
					"', '".
					$bassNote.
					"', '".
					implode("_",$chordObj->getChordNotesAsNumbers()).
					"');\n";

				foreach ($chordObj->guitarChordNumberRepresentation as $chordCode)
				{
					$html .= "chord.addGuitarCode($chordCode);\n";
				}
				$html .= "addChord(chord);\n";

			}
			$html .= "	document.all[\"keyboard-page\"].innerHTML = getChordList(KEYBOARD);";
			$html .= "	document.all[\"frets-page\"].innerHTML = getChordList(GUITAR);";
			$html .= "	updateSelectedInstrument();";
			$html .= "</script>";
		}
		return $html;
	}

	function form( $action, $categories = NULL, $writers = NULL, $submitted_by=0, $showSubmit=FALSE ){
		global $mosConfig_live_site;

		$key_selected[$this->song_key] = " selected";

		$infoImage = mosAdminMenus::ImageCheck( 'con_info.png', '/images/M_images/', NULL, NULL, "" );

		echo "<script language='javascript1.2' src='$mosConfig_live_site/components/com_chordbase/js/chord.class.js'></script>";
		?>

		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
		<form name="adminForm" method="post" action="<?php echo $action ?>" id="adminForm">
			<!-- BEGIN META -->
			<center>
			<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td colspan="2">
						<b><?php echo _TITLE ?>:</b>
						<input id="song_title" name="title" type="text" size="25" value="<?php echo $this->title; ?>" />
						<b><?php echo _KEY ?>:</b>
						<select name="song_key">
							<option value="C"<?php echo $key_selected["C"]; ?>>C</option>
							<option value="G"<?php echo $key_selected["G"]; ?>>G</option>
							<option value="D"<?php echo $key_selected["D"]; ?>>D</option>
							<option value="A"<?php echo $key_selected["A"]; ?>>A</option>
							<option value="E"<?php echo $key_selected["E"]; ?>>E</option>
							<option value="B"<?php echo $key_selected["B"]; ?>>B</option>
							<option value="F#"<?php echo $key_selected["Fs"]; ?>>F#</option>
							<option value="C#"<?php echo $key_selected["Cs"]; ?>>C#</option>

							<option value="F"<?php echo $key_selected["F"]; ?>>F</option>
							<option value="Bb"<?php echo $key_selected["Bb"]; ?>>Bb</option>
							<option value="Eb"<?php echo $key_selected["Eb"]; ?>>Eb</option>
							<option value="Ab"<?php echo $key_selected["Ab"]; ?>>Ab</option>
							<option value="Db"<?php echo $key_selected["Db"]; ?>>Db</option>
							<option value="Gb"<?php echo $key_selected["Gb"]; ?>>Gb</option>

							<option value="Am"<?php echo $key_selected["Am"]; ?>>Am</option>
							<option value="Em"<?php echo $key_selected["Em"]; ?>>Em</option>
							<option value="Bm"<?php echo $key_selected["Bm"]; ?>>Bm</option>
							<option value="F#m"<?php echo $key_selected["Fam"]; ?>>F#m</option>
							<option value="C#m"<?php echo $key_selected["Cam"]; ?>>C#m</option>
							<option value="G#m"<?php echo $key_selected["Gam"]; ?>>G#m</option>
							<option value="D#m"<?php echo $key_selected["Dam"]; ?>>D#m</option>
							<option value="A#m"<?php echo $key_selected["Aam"]; ?>>A#m</option>

							<option value="Dm"<?php echo $key_selected["Dm"]; ?>>Dm</option>
							<option value="Gm"<?php echo $key_selected["Gm"]; ?>>Gm</option>
							<option value="Cm"<?php echo $key_selected["Cm"]; ?>>Cm</option>
							<option value="Fm"<?php echo $key_selected["Fm"]; ?>>Fm</option>
							<option value="Bbm"<?php echo $key_selected["Bbm"]; ?>>Bbm</option>
							<option value="Ebm"<?php echo $key_selected["Ebm"]; ?>>Ebm</option>
							<option value="Abm"<?php echo $key_selected["Abm"]; ?>>Abm</option>
						</select>
						<b><?php echo _BPM ?>:</b>
						<input name="bpm" type="text" size="3" value="<?php echo $this->bpm; ?>" />
						<b><?php echo _SIG ?>:</b>
						<input name="signature" type="text" size="3" value="<?php echo $this->signature; ?>" />
					</td>
				</tr>
				</tr>
			</table>
		<!-- END META -->

		<!-- BEGIN SONG -->
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td valign="top">
						<br><b><?php echo _SONG_BODY ?>:</b>
						<script>
							function enclose(begin, end)
							{
								document.all.song.focus();
								range = document.selection.createRange();
								if (range.parentElement() == document.all.song)
								{
									range.text = begin+range.text+end;
								}
							}
						</script>
						<input type="button" value="<?php echo _CHORDPRO_CM_CHORD ?>" class="button" onclick="enclose('[',']')">
						<input type="button" value="<?php echo _CHORDPRO_CM_SOC ?>" class="button" onclick="enclose('{soc}','{eoc}')">
						<input type="button" value="<?php echo _CHORDPRO_CM_SOT ?>" class="button" onclick="enclose('{sot}','{eot}')">
						<input type="button" value="<?php echo _CHORDPRO_CM_C ?>" class="button" onclick="enclose('{c:','}')">
						<input type="button" value="<?php echo _CHORDPRO_CM_GC ?>" class="button" onclick="enclose('{gc:','}')">
						<input type="button" value="<?php echo _CHORDPRO_CM_COMENT ?>" class="button" onclick="enclose('#','')">
						<span onMouseOver="return overlib('<?php echo _CHORDPRO_INSTRUCTIONS ?>', CAPTION, '<?php echo _CHORDPRO_INSTRUCTIONS_TITLE ?>', WIDTH, 300, BELOW, RIGHT);" onMouseOut="return nd();">
							<?php echo $infoImage ?>
						</span>
  						<textarea name="song" id="song" rows="20" style="width: 100%"><?php echo $this->song; ?></textarea>
						<div align="center"><input type="button" value="<?php echo _TEXT_CONV ?>" class="button" onclick="song.value = textToChordPro(song.value)" /></div>
					</td>
				</tr>
			</table>
			<!-- END SONG -->

			<!-- BEGIN AUTHORS & COPYRIGHTS -->
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td><b><?php echo _WRITER ?>:</b></td>
					<td><b><?php echo _CATEGORY ?>:</b></td>
				</tr>
				<tr>
					<td valign="top">
						&nbsp;&nbsp;<select name="writer">
										<option value="-1"><?php echo _NEW_WRITER ?></option>
					<?php
						foreach($writers as $writer){
							if($writer->writer_id == $this->writer)
								echo '<option value="'.$writer->writer_id.'" selected>'.$writer->name.'</option>';
							else
								echo '<option value="'.$writer->writer_id.'">'.$writer->name.'</option>';
						}

					?>
						</select>
					</td>
					<td valign="top">
						&nbsp;&nbsp;<select name="category">
										<option value="-1"><?php echo _NEW_CATEGORY ?></option>
					<?php
						foreach($categories as $category){
							if($category->category_id == $this->category && $this->category != "")
								echo '<option value="'.$category->category_id.'" selected>'.$category->title.'</option>';
							else
								echo '<option value="'.$category->category_id.'">'.$category->title.'</option>';
						}

					?>
						</select>
					</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;<?php echo _NEW ?>: <input type="text" name="new_writer" size="25" /></td>
					<td>&nbsp;&nbsp;<?php echo _NEW ?>: <input type="text" name="new_category" size="25" /></td>
				</tr>
			<!-- END AUTHORS & COPYRIGHTS -->

			<!-- BEGIN NOTES & KEYWORDS -->
				<tr>
					<td><b><?php echo _NOTES ?>:</b></td>
					<td><b><?php echo _KEYWORDS ?>:</b> <?php echo _KEYWORDS_INSTRUCTIONS ?></td>
				</tr>
				<tr>
					<td><textarea name="notes" rows="5" cols="25"><?php echo $this->notes; ?></textarea></td>
					<td><textarea name="keywords" rows="5" cols="25"><?php echo $this->keywords; ?></textarea></td>
				</tr>
			<!-- END NOTES & KEYWORDS -->

			<!-- BEGIN CHORDS & CATEGORY -->
				<tr>
					<td><b><?php echo _CHORDS ?>:</b> <?php echo _CHORDS_INSTRUCTIONS ?></td>
					<td><b><?php echo _COPYRIGHTS ?>:</b></td>
				</tr>
				<tr>
					<td>
						<textarea name="chords" rows="5" cols="20"><?php echo $this->chords; ?></textarea>
					</td>
					<td>
						<textarea name="copyrights" rows="2" cols="25"><?php echo $this->copyrights; ?></textarea><br />
						<!-- table>
							<tr>
								<td><b>Year:</b></td>
								<td><b>CCLI:</b></td>
							</tr>
							<tr>
								<td><input name="year" type="text" size="4" value="<?php echo $this->year; ?>" /></td>
								<td><input type="text" name="ccli" value="<?php echo $this->ccli; ?>" /></td>
							</tr>
						</table -->
					</td>
				</tr>
			<?php if($showSubmit) { ?>
				<tr>
					<td colspan="2" align="right"><input type="submit" name="submit" class="button" value="<?php echo _SAVE_SONG ?>" /></td>
				</tr>
			<?php } ?>
			<!-- END CHORDS & CATEGORY -->
			</table>
			</center>
			<input type="hidden" name="option" value="com_chordbase" />
			<input type="hidden" name="submitted_by" value="<?php echo $submitted_by ?>" />
			<input type="hidden" name="add_time" value="<?php echo $this->add_time ?>" />
			<input type="hidden" name="task" value="saveSong" />
			<input type="hidden" name="song_id" value="<?php echo $this->song_id ?>" />
		</form>

		<?php
	}

	function partsForm( $action ){

		$song_lines = $this->getPartsArray();
		?>

		<form name="adminForm" method="post" action="<?php echo $action ?>" id="adminForm">

			<!-- BEGIN META -->
			<table cellspacing="1" cellpadding="1" border="0">
				</tr>
		<!-- END META -->

		<!-- BEGIN PARTS -->
				<tr>
					<td valign="top" colspan="5" align="center"><b>Song Parts</b></td>
				</tr>
				<tr>
					<td>Part</td>
					<td>Chords</td>
					<td>Words</td>
					<td>Repeat</td>
					<td>Text</td>
				</tr>

			<?php
		$k = 0;
		foreach ($song_lines as $song_line) {
			 ?>
				<tr>
					<td align="center" class="sectiontableentry<?php echo ($k+1) ?>">
						<input type="radio" name="line_type_<?php echo $song_line["number"] ?>" value="P"<?php echo $song_line["selected_type"]["SongPart"] ?> />
					</td>
					<td align="center" class="sectiontableentry<?php echo ($k+1) ?>">
						<input type="radio" name="line_type_<?php echo $song_line["number"] ?>" value="C"<?php echo $song_line["selected_type"]["Chord"] ?> />
					</td>
					<td align="center" class="sectiontableentry<?php echo ($k+1) ?>">
						<input type="radio" name="line_type_<?php echo $song_line["number"] ?>" value="W"<?php echo $song_line["selected_type"]["SongWords"] ?> />
					</td>
					<td align="center" class="sectiontableentry<?php echo ($k+1) ?>">
						<input type="radio" name="line_type_<?php echo $song_line["number"] ?>" value="R"<?php echo $song_line["selected_type"]["PartRepeat"] ?> />
					</td>
					<td class="sectiontableentry<?php echo ($k+1) ?>">
						<pre class="line_define" style="margin:0px;padding:0px;"><?php echo str_replace("\n","",$song_line["content"]) ?></pre>
					</td>
					<input type="hidden" name="line_content_<?php echo $song_line["number"] ?>" value="<?php echo $song_line["content"] ?>" />
				</tr>
		<?php
			$k = 1 - $k;
		} ?>

			<!-- END PARTS -->

			</table>
			<input type="hidden" name="task" value="saveParts" />
			<input type="hidden" name="option" value="com_chordbase" />
			<input type="hidden" name="num_lines" value="<?php echo count($song_lines) ?>" />
			<input type="hidden" name="song_id" value="<?php echo $this->song_id ?>" />
		</form>

		<?php
	}

	function getPartsArray( ) {

		$song = explode("\n",$this->song);

		$i = 0;
		$song_lines = array();
		foreach($song as $line){

			$song_lines[$i] = $this->line_explode($line);
			$song_lines[$i]["abrev_content"] = substr($song_lines[$i]["content"],0,20);
			$song_lines[$i]["content"] = $song_lines[$i]["content"];
			$song_lines[$i]["selected_type"][$song_lines[$i]["type"]] = " checked";
			$song_lines[$i]["number"] = $i;
			$i++;
		}

		return $song_lines;
	}

	// separates line type from line content
	function line_explode($line){

		$part_types["C"] = "Chord";
		$part_types["P"] = "SongPart";
		$part_types["R"] = "PartRepeat";
		$part_types["W"] = "SongWords";

		$line_array = array();

		if($line{0} != "["){

			$line = substr($line,0,-1);

			if(substr($line,0,2) == "//"){

				$line_array["type"] = "Chord";
				$line_array["content"] = substr($line,2);

			} else if(substr($line,0,1) == "#"){

				$line_array["type"] = "SongPart";
				$line_array["content"] = substr($line,1);

			} else if(substr($line,0,1) == "*"){

				$line_array["type"] = "PartRepeat";
				$line_array["content"] = substr($line,1);

			} else {
				$line_array["type"] = "SongWords";
				$line_array["content"] = $line;
			}

		} else {

			$type_letter = $line{1};
			$line_array["type"] = $part_types[strtoupper($type_letter)];

			$search = array("[".strtoupper($type_letter)."]","[/".strtoupper($type_letter)."]");
			$replace = array("","");

			$line = str_replace($search,$replace,$line);
			$line = rtrim($line);
			$line_array["content"] = $line;
		}

		return $line_array;

	}

	// transpose a line of chords
	function transpose($chords, $newKey){

		// major sharps
		$transpose["C"] = array("C","D","E","F","G","A","B");
		$transpose["G"] = array("G","A","B","C","D","E","F#");
		$transpose["D"] = array("D","E","F#","G","A","B","C#");
		$transpose["A"] = array("A","B","C#","D","E","F#","G#");
		$transpose["E"] = array("E","F#","G#","A","B","C#","D#");
		$transpose["B"] = array("B","C#","D#","E","F#","G#","A#");
		$transpose["F#"] = array("F#","G#","A#","B","C#","D#","F");
		$transpose["C#"] = array("C#","D#","F","F#","G#","A#","A");
		// major flats
		$transpose["F"] = array("F","G","A","Bb","C","D","E");
		$transpose["Bb"] = array("Bb","C","D","Eb","F","G","A");
		$transpose["Eb"] = array("Eb","F","G","Ab","Bb","C","D");
		$transpose["Ab"] = array("Ab","Bb","C","Db","Eb","F","G");
		$transpose["Db"] = array("Db","Eb","F","Gb","Ab","Bb","C");
		$transpose["Gb"] = array("Gb","Ab","Bb","B","Db","Eb","F");
		// minor sharps
		$transpose["Am"] = array("A","B","C","D","E","F","G");
		$transpose["Em"] = array("E","F#","G","A","B","C","D");
		$transpose["Bm"] = array("B","C#","D","E","F#","G","A");
		$transpose["F#m"] = array("F#","G#","A","B","C#","D","E");
		$transpose["C#m"] = array("C#","D#","E","F#","G#","A","B");
		$transpose["G#m"] = array("G#","A#","B","C#","D#","E","F#");
		$transpose["D#m"] = array("D#","F","F#","G#","A#","B","C#");
		$transpose["A#m"] = array("A#","C","C#","D#","F","F#","G#");
		// minior flats
		$transpose["Dm"] = array("D","E","F","G","A","Bb","C");
		$transpose["Gm"] = array("G","A","Bb","C","D","Eb","F");
		$transpose["Cm"] = array("C","D","Eb","F","G","Ab","Bb");
		$transpose["Fm"] = array("F","G","Ab","Bb","C","Db","Eb");
		$transpose["Bbm"] = array("Bb","C","Db","Eb","F","Gb","Ab");
		$transpose["Ebm"] = array("Eb","F","Gb","Ab","Bb","B","Db");
		$transpose["Abm"] = array("Ab","Bb","B","Db","Eb","E","Gb");

		$transposeKey = array("[1]","[2]","[3]","[4]","[5]","[6]","[7]");

		$transposeChords = str_replace($transpose[$this->song_key],$transposeKey,$chords);
		return str_replace($transposeKey,$transpose[$newKey],$transposeChords);


	}

	function transposeLinks() {

		global $Itemid;

		$is_minor = strpos( $this->song_key, "m" );

		if( $is_minor === FALSE ) {
		?>
			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=C">C</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=G">G</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=D">D</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=A">A</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=E">E</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=B">B</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=F%23">F#</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=C%23">C#</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=F">F</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Bb">Bb</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Eb">Eb</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Ab">Ab</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Db">Db</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Gb">Gb</a>

			<?php
		} else {
			?>
			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Am">Am</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Em">Em</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Bm">Bm</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=F%23m">F#m</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=C%23m">C#m</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=G%23m">G#m</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=D%23m">D#m</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=A%23m">A#m</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Dm">Dm</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Gm">Gm</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Cm">Dm</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Fm">Fm</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Bbm">Bbm</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Ebm">Ebm</a> :

			<a class="alpha" href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $this->song_id ?>&key=Abm">Abm</a>

		<?php
		}
	}
}

function encloseLyric($matches) {
    return $matches[1] != "" ? "<td class=\"choproLyric\">".preg_replace("/\s/","&nbsp;", $matches[1])."</td>" : "";
}

function encloseChord($matches) {
	global $mosConfig_live_site, $chordsToAdd;

	$chord = $matches[2];

	if (trim($chord) == "")
		return "<td>&nbsp;</td>";

	$chordObj = new Chord($chord);

	if ($chordObj->getBassNote() != "")
		$bassNote = $chordObj->getNote($chordObj->getBassNote());
	else $bassNote = $chordObj->getNote($chordObj->getMainNote());

	$chord = $chordObj->getChordAsString();
	$chordHTML = $chordObj->getChordAsHTML();

	if (!in_array($chordObj->getChordAsHTML(), $chordsToAdd))
		$chordsToAdd[$chordObj->getChordAsHTML()] = $chordObj;

	return "<td><a class=\"choproChord\" href=\"\" onclick=\"javascript: playChordMidi(this.innerHTML); return false;\" onMouseOver=\"return overlib(getChordHint(this.innerHTML), WIDTH, 100, ABOVE);\" onMouseOut=\"return nd();\">$chordHTML</a>&nbsp;</td>";
}
?>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
<script language="Javascript" src="<?php echo $mosConfig_live_site; ?>/includes/js/overlib_mini.js"></script>
