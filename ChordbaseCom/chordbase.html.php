<?php
/**
* @version $Id: chordbase.html.php,v 1.2.2.1 2005/06/10 10:45:25 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

//Get the right language if it exists
if (file_exists($mosConfig_absolute_path.'/components/com_chordbase/languages/'.$mosConfig_lang.'.php')) {
	include($mosConfig_absolute_path.'/components/com_chordbase/languages/'.$mosConfig_lang.'.php');
} else {
	include($mosConfig_absolute_path.'/components/com_chordbase/languages/english.php');
}

global $my, $Itemid; ?>


<?php
class HTML_chordbase {

	function cb_header( $pageTitle="" ) {

		?>
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td class="componentheading" nowrap="nowrap" valign="top" align="center"><?php echo $pageTitle ?></td>
			</tr>
		</table>
		<?php

	}

	function printHeader () {
		global $mosConfig_live_site;
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_live_site ?>/components/com_chordbase/css/pf.css" />

		<?php
	}

	function songHeader ( $showChords="yes", $params=NULL, $song=null, $editSongLink=FALSE ) {

		global $mainframe, $my, $database, $acl;
		global $mosConfig_absolute_path, $mosConfig_sitename, $mosConfig_live_site, $task;
		global $Itemid, $song_id, $hide_js;

		if ( $params->get( 'icons' ) ) {
			$printImage = mosAdminMenus::ImageCheck( 'printButton.png', '/images/M_images/', NULL, NULL, _CMN_PRINT );
			$editImage = mosAdminMenus::ImageCheck( 'edit.png', '/images/M_images/', NULL, NULL, _E_EDIT );
		} else {
			$printImage = _ICON_SEP .'&nbsp;'. _CMN_PRINT. '&nbsp;'. _ICON_SEP;
			$editImage = _ICON_SEP .'&nbsp;'. _E_EDIT. '&nbsp;'. _ICON_SEP;
		}

		?>
		<link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_live_site ?>/components/com_chordbase/css/style.css" />
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100%" style="text-align:right" valign="top">
<?php /*					[<a href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=songlist"><?php echo _SONGLIST ?></a>]<br />
					[<a href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&song_id=<?php echo $song_id ?>&showChords=<?php echo $showChords == "no"?"yes":"no" ?>"><?php echo ($showChords == "no"?_SHOW:_HIDE) . " " . _CHORDS ?></a>]<br />
*/ ?>
					<a href="<?php echo $mosConfig_live_site ?>/index2.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=viewSong&showChords=<?php echo $showChords == "no"?"no":"yes" ?>&song_id=<?php echo $song_id ?>&pop=1" target="_blank"><?php echo $printImage; ?></a>
				<?php if($editSongLink) { ?>
					<a href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=editSong&song_id=<?php echo $song_id ?>"><?php echo $editImage ?></a>
				<?php } ?>
				</td>
			</tr>
		<?php
/*			<tr>
				<td align="center">
					<center>
						<u><?php echo _TRANSPOSE ?></u><br />
						<?php $song->transposeLinks() ?>
					</center>
				</td>
			</tr>
		</table>*/
	}

	function footer ( ) {
		global $Itemid, $song_id;
	}

	function songlist ( &$rows, $categories, $writers, $search, $pageNav, $permissions, $criteria )
	{
		global $Itemid, $database, $option, $mosConfig_live_site, $_REQUEST, $chordBase, $my, $cb_user_permission;
			?>
			<table cellpadding="4" cellspacing="0" border="0" width="100%">
				<tr>
					<td nowrap="nowrap" align="left" valign="top">
						<?php echo _DISPLAY ?>: <?php echo $pageNav->writeLimitBox($chordBase->songBookListUrl); ?>
					</td>
					<td align="right" valign="top">

						<form method="get">
							<?php
								 foreach ($_GET as $name => $value)
								 {
								 	if (strpos(" writerSearch titleSearch lyricSearcg",$name) == FALSE)
								 		echo "<input type=hidden name='$name' value='$value'>";
								 }
							?>
							<input alt="search" class="inputbox" type="text" name="writerSearch" size="20" value="<?php echo $criteria["writerSearch"]; ?>"  onblur="if(this.value=='') this.value='<?php echo _WRITER; ?>...';" onfocus="if(this.value=='<?php echo _WRITER; ?>...') this.value='';" />
							<input alt="search" class="inputbox" type="text" name="titleSearch" size="20" value="<?php echo $criteria["titleSearch"]; ?>"  onblur="if(this.value=='') this.value='<?php echo _TITLE; ?>...';" onfocus="if(this.value=='<?php echo _TITLE; ?>...') this.value='';" />
							<input alt="search" class="inputbox" type="text" name="lyricSearch" size="20" value="<?php echo $criteria["lyricSearch"]; ?>"  onblur="if(this.value=='') this.value='<?php echo _LYRIC; ?>...';" onfocus="if(this.value=='<?php echo _LYRIC; ?>...') this.value='';" />
							<input type="submit" name="Submit" class="button" value="<?php echo _SEARCH_TITLE; ?>" />
						</form>
					</td>
				</tr>
			</table>


			<table cellpadding="4" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="50%" align="right" valign="top">
				<?php if($permissions["Add Song"]) { ?>
						[<a href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=addSong"><?php echo _ADD_SONG ?></a>]<br />
				<?php } ?>
				<?php if($permissions["Publish Song"] && $_REQUEST["task"] != "unpublished") { ?>
						[<a href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=unpublished"><?php echo _UNPUBLISHED ?></a>]<br />
				<?php } else if($_REQUEST["task"] == "unpublished") { ?>
						[<a href="index.php?option=com_chordbase&Itemid=<?php echo $Itemid ?>&task=songList"><?php echo _PUBLISHED ?></a>]<br />
				<?php } ?>
					</td>
<?php /*					<td nowrap="nowrap" align="right" valign="top"><?php echo _DISPLAY ?>: <?php echo $pageNav->writeLimitBox("index.php?option=com_chordbase&Itemid=$Itemid"); ?> <?php echo _SEARCH ?><input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" /></td> */ ?>
				</tr>
			</table>

			<center>
				<div id="ALPHA_BAR">
					<?php
						$alphaURL = preg_replace("/\&amp\;initial\=./","",$chordBase->songBookListUrl);					?>					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=A">A</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=B">B</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=C">C</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=D">D</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=E">E</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=F">F</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=G">G</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=H">H</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=I">I</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=J">J</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=K">K</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=L">L</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=M">M</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=N">N</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=O">O</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=P">P</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=Q">Q</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=R">R</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=S">S</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=T">T</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=U">U</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=V">V</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=W">W</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=Y">Y</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0&amp;initial=Z">Z</a> :
					<a class="alpha" href="<?php echo $alphaURL . "&amp;limit=" . $pageNav->limit ?>&limitstart=0"><b><?php echo _ALL ?></b></a>
				</div>
			</center>

			<table cellpadding="2" cellspacing="0" width="100%"  class="sectiontable">
				<tr>
					<th class="sectiontableheader" align="left"><?php echo _TITLE; ?></th>
					<th class="sectiontableheader" align="left"><?php echo _WRITERS; ?></th>
					<th class="sectiontableheader" align="left"><?php echo _KEY; ?></th>
					<th class="sectiontableheader" align="left"><?php echo _CATEGORY; ?></th>
					<th class="sectiontableheader" align="left" nowrap><?php echo _SUBMITTED_BY; ?></th>
					<th class="sectiontableheader" align="left"><?php echo _VIEWS; ?></th>
				</tr>
			<?php

		$showUnpublished = $chordBase->has_permission("Edit Song",$cb_user_permission)
			|| $chordBase->has_permission("Publish Song",$cb_user_permission);

		$showSelfUnpublished = $chordBase->has_permission("Add Song",$cb_user_permission);

		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$query = "SELECT name from #__users"
				. "\n WHERE id=$row->submitted_by";
			$database->setQuery( $query );
			$submited_by = $database->loadResult();

			?>
				<tr>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php
						echo $row->published == 1 ? "" : "<i>";

						if ($row->published == 1
							|| ($showSelfUnpublished && $row->submitted_by == $my->id)) {
							?><a href="index.php?option=com_chordbase&amp;Itemid=<?php echo $Itemid ?>&amp;task=viewSong&amp;song_id=<?php echo $row->song_id; ?>"><?php echo $row->title ?></a><?php
						} else {
							echo "$row->title";
						}
						echo $row->published == 1 ? "" : "</i>";
					?></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $writers[$row->writer]->name; ?></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->song_key; ?></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $categories[$row->category]->title; ?></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $submited_by; ?></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->views; ?></td>
					</tr>
				<?php
				$k = 1 - $k;
		}
		echo "</table>";

		if($pageNav != null){
			?>
			<div class="sectiontableheader" align="center" colspan="6"><?php echo $pageNav->writePagesCounter(); ?></div>
			<div align="center"><?php echo $pageNav->writePagesLinks($chordBase->songBookListUrl); ?></div>
			<?php
		}
	}

	function setlist ( &$rows, $categories, $writers, $search, $pageNav, $permissions ) {

		global $Itemid, $database, $option, $mosConfig_live_site, $_REQUEST;
			?>
			<table cellpadding="2" cellspacing="0" width="100%">
				<tr>
					<th class="title" align="left"><?php echo _TITLE; ?></th>
					<th class="title" align="left"><?php echo _WRITERS; ?></th>
					<th class="title" align="left"><?php echo _KEY; ?></th>
					<th class="title" align="left"><?php echo _CATEGORY; ?></th>
					<th class="title" align="left"><?php echo _VIEWS; ?></th>
				</tr>
			<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
				<tr>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><a href="index.php?option=com_chordbase&amp;Itemid=<?php echo $Itemid ?>&amp;task=viewSong&amp;song_id=<?php echo $row->song_id; ?>"><?php echo $row->title ?></a></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $writers[$row->writer]->name; ?></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->song_key; ?></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $categories[$row->category]->title; ?></td>
					<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->views; ?></td>
				</tr>
		<?php
			$k = 1 - $k;
		}

		if($pageNav != null){?>
				<tr>
					<th align="center" colspan="5"><?php echo $pageNav->writePagesLinks("index.php?option=com_chordbase&Itemid=$Itemid"); ?></th>
				</tr>
				<tr>
					<td align="center" colspan="5"><?php echo $pageNav->writePagesCounter(); ?></td>
				</tr>
		<?php } ?>
			</table>
		<?php
	}

	function songForm( $song = NULL, $categories = NULL, $writers = NULL ){
		global $mosConfig_live_site;

		if($song != NULL)
			$song_info = $song->getInfo();

		$key_selected[$songInfo["song_key"]] = " selected";

		?>

		<form name="adminForm" method="post" action="<?php echo $action ?>" id="adminForm">

			<input type="hidden" name="mode" value="simple" />

			<!-- BEGIN META -->
			<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td>
						Song Title:
						<input id="song_title" name="title" type="text" size="38" value="<?php echo $songInfo["title"]; ?>" />
					</td>
					<td width="10">&nbsp;</td>
					<td>
						Key:
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
					</td>
					<td width="10">&nbsp;</td>
					<td>
						BPM:
						<input name="bpm" type="text" size="3" value="<?php echo $songInfo["bpm"]; ?>" />
					</td>
					<td width="10">&nbsp;</td>
					<td>
						Sig:
						<input name="signature" type="text" size="3" value="<?php echo $songInfo["signature"]; ?>" />
					</td>
					<td width="10">&nbsp;</td>
					<td>
						Year:
						<input name="year" type="text" size="4" value="<?php echo $songInfo["year"]; ?>" />
					</td>
				</tr>
			</table>
			<table cellspacing="2" cellpadding="2" border="0">
				</tr>
		<!-- END META -->

		<!-- BEGIN SONG -->
				<tr>
					<td valign="top" colspan="2"><b>Song</b></td>
				</tr>
				<tr>
					<td valign="top">
						<font size="-2">Note:<br />&nbsp;&nbsp;&nbsp;<i>Enclose chorded lines with [C][/C] <br />&nbsp;&nbsp;&nbsp;Song part titles [Chorus, Verse 2, etc] with [T][/T]<br />&nbsp;&nbsp;&nbsp;Repeat song part titles [Chorus(later on)] with [R][/R]</font></i>
					</td>
					<td valign="top">
						<font size="-2">Or:<br />&nbsp;&nbsp;&nbsp;<i>Define Parts in next step:</i></font>
						<input type="radio" name="song_part_define" value="1"<?php echo (!$songInfo["song_id"])?" checked":""; ?>>Yes</input>
						<input type="radio" name="song_part_define" value="0"<?php echo ($songInfo["song_id"])?" checked":""; ?>>No</input>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea name="song" rows="20" cols="80"><?php echo $songInfo["song"]; ?></textarea>
					</td>
				</tr>
			<!-- END SONG -->

			<!-- BEGIN AUTHORS & COPYRIGHTS -->
				<tr>
					<td>Writer:</td>
					<td>Copyrights:</td>
				</tr>
				<tr>
					<td valign="top">
						<select name="writer">
					<?php
						foreach($writers as $writer){
							if($writer->category_id == $songInfo["writer"])
								echo '<option value="'.$writer->writer_id.'" selected>'.$writer->name.'</option>';
							else
								echo '<option value="'.$writer->writer_id.'">'.$writer->name.'</option>';
						}

					?>
						</select>
					</td>
					<td><textarea name="copyrights" rows="5" cols="35"><?php echo $songInfo["copyrights"]; ?></textarea></td>
				</tr>
			<!-- END AUTHORS & COPYRIGHTS -->

			<!-- BEGIN NOTES & KEYWORDS -->
				<tr>
					<td>Notes:</td>
					<td>Keywords (comma separated, no spaces):</td>
				</tr>
				<tr>
					<td><textarea name="notes" rows="5" cols="35"><?php echo $songInfo["notes"]; ?></textarea></td>
					<td><textarea name="keywords" rows="5" cols="35"><?php echo $songInfo["keywords"]; ?></textarea></td>
				</tr>
			<!-- END NOTES & KEYWORDS -->

			<!-- BEGIN CHORDS & CATEGORY -->
				<tr>
					<td>Chords and Fingerings (&lt;chord&gt; : &lt;fingering&gt;):</td>
					<td>Category:</td>
				</tr>
				<tr>
					<td>
						<textarea name="chords" rows="5" cols="20"><?php echo $songInfo["chords"]; ?></textarea>
					</td>
					<td valign="top">
						<select name="category">
					<?php
						foreach($categories as $category){
							if($category->category_id == $songInfo["category"])
								echo '<option value="'.$category->category_id.'" selected>'.$category->title.'</option>';
							else
								echo '<option value="'.$category->category_id.'">'.$category->title.'</option>';
						}

					?>
						</select>
					</td>
				</tr>
			<!-- END CHORDS & CATEGORY -->
			</table>
			<input type="hidden" name="task" value="saveSong" />
			<input type="hidden" name="song_id" value="<?php echo $songInfo["song_id"] ?>" />
		</form>

		<?php
	}

	function cb_message( $message="" ) {

		?>
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td nowrap="nowrap" valign="top"><p><?php echo $message ?></p></td>
			</tr>
		</table>
		<?php

	}

}
?>
