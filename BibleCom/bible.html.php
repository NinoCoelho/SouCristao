<?php

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once( "includes/HTML_toolbar.php" );

/**
* Utility class for writing the HTML for content
*/
class HTML_bible {
	function showBooksList( &$books, &$Itemid ) {
		global $Itemid, $my;
		global $mosConfig_hideAuthor, $mosConfig_offset, $mosConfig_live_site;

		if ($sectionid!=0) {
			$id = $sectionid;
		}

		if (get_class( $title ) == 'mossection') {
			$catid = 0;
		} else {
			$catid = $title->id;
		}
		?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0" align="center">
		  <tr>
		    <td width="60%" valign="top" class="contentheading" colspan="2">
		      <?php	echo _BIBLE_BOOKS_LIST_TITLE; ?>
		    </td>
		  </tr>
		  <?php
		  if ($books) {
			  ?>
			  <tr>
			    <td colspan="2">
				<table width="100%" border="0" cellspacing="0" cellpadding="1">
		        <?php
		          $i = 1;
		          $location = '';
			      foreach ($books as $row) {
			      	if ($location != $row->location) {
			      		echo '<tr><td colspan=3 class="sectiontableheader">' . ($row->location == 'O' ? _BIBLE_BOOKS_OLD : _BIBLE_BOOKS_NEW) . '</td></tr>';
			      		$location = $row->location;
			      	}
				    if ($i == 1) echo '<tr>';
				    ?>
			          <td><a href="<?php echo sefRelToAbs("index.php?option=bible&amp;task=viewBook&amp;id=".$row->ordering."&amp;Itemid=".$Itemid); ?>"><?php echo $row->bookName; ?></a></td>
			        <?php
				    if ($i == 3) {
				    	echo '</tr>';
				    	$i = 0;
				    }
				    $i++;
			      } ?>
			      </table>
			    </td>
			  </tr>
	          <?php
			}
		?>
		</table>
		<?php
	}

	function showBook( $Itemid, $book, $verses, $limitstart, $pageNav ) {
		global $Itemid, $my, $HTTP_SERVER_VARS;
		global $mosConfig_hideAuthor, $mosConfig_offset, $mosConfig_live_site;
		$link = sefRelToAbs("index.php?option=bible&amp;task=viewBook&amp;id=".$book->ordering."&amp;Itemid=".$Itemid);

		$showNav = !strpos($HTTP_SERVER_VARS['PHP_SELF'],"index2.php");

		require_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/pageNavigation.php' );

		if ($showNav)
			$pageNav = new mosPageNav( $book->qtdChapters, $limitstart, 1);

		if ($sectionid!=0) {
			$id = $sectionid;
		}

		if (get_class( $title ) == 'mossection') {
			$catid = 0;
		} else {
			$catid = $title->id;
		}
		?>
		<div class="contentheading">
			<span style="float: right" class="small">
				<?php
				if ($showNav)
				{

					$chapters = array();
					for ($i=0; $i < $book->qtdChapters; $i++) {
						$chapters[] = mosHTML::makeOption( "$i", $i+1 );
					}

					// build the html select list
					$link = sefRelToAbs($link.'&amp;limitstart=\' + (this.options[selectedIndex].value) + \'');
					echo mosHTML::selectList( $chapters, 'limitstart',
						'class="inputbox" size="1" onchange="document.location.href=\''.$link.'\';"',
						'value', 'text', $limitstart );
				}
				?>
			</span>
			<?php
	      	echo $book->bookName . " " . ($limitstart+1);
		?></div>
		<table class="contentpaneopen">
		  <?php
		  if ($verses) {
			  ?>
			  <tr>
			    <td>
				<table width="100%" border="0" cellspacing="0" cellpadding="3">
		        <?php
			      foreach ($verses as $row) {
				    ?>
			        <tr><td valign='top'><i><a name='<?php echo $row->verse; ?>'><?php echo $row->verse; ?></a></i></td><td><?php echo $row->verseText; ?></td></tr>
			        <?php
			      } ?>
			      </table>
			    </td>
			  </tr>
	          <?php
		  }
		  ?>
	    </table>
		<?php
		if ($showNav)
		{
			?>
			<div class="small">
				<span style="float: right">
					<?php echo $pageNav->writePagesCounter(); ?>
				</span>
				<?php
					echo "\n<a href=\"".sefRelToAbs("index.php?option=bible&amp;task=viewBooks&amp;Itemid=".$Itemid)."\" class=\"readon\">"._BIBLE_SELECT_BOOK."</a>";
				?>
			</div>
			<div class="small" style="text-align: center;">
				<?php
					if ($showNav)
						echo $pageNav->writePagesLinks( sefRelToAbs("index.php?option=bible&amp;task=viewBook&amp;id=".$book->ordering."&amp;Itemid=".$Itemid));
				?>
			</div>
			<?php
		}
	}

}
?>
