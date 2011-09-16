<?php
/**
* @version $Id: admin.chordbase.php,v 0.8a2 11/27/2004 $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>

<?php
class HTML_chordbase {

	function cb_header( $pageTitle="" ) {

		global $mosConfig_live_site;

		?>
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="100%"><a href="http://www.chordbase.com" target="_blank"><img src="<?php echo $mosConfig_live_site ?>/components/com_chordbase/images/ChordBase.png" border="0" /></a></td>
				<td nowrap="nowrap" valign="top"><h1><?php echo $pageTitle ?></h1></td>
			</tr>
		</table>
		<?php

	}
  
	function songlist ( &$rows, $categories, $writers, $users_array, $search, $pageNav ) {

		global $mosConfig_live_site, $option; ?>

		<form action="index2.php" method="post" name="adminForm" >
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td><a href="http://www.chordbase.com" target="_blank"><img src="<?php echo $mosConfig_live_site ?>/components/com_chordbase/images/ChordBase.png" border="0" /></a></td>
				<td width="100%" align="center" valign="bottom"><h1>Songs</h1></td>
				<td nowrap="nowrap">Display #</td>
				<td><?php echo $pageNav->writeLimitBox(); ?></td>
				<td>Search:</td>
				<td><input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" /></td>
			</tr>
		</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
				<th class="title"><div align="left">ID</div></th>
				<th class="title"><div align="left">Title</div></th>
				<th class="title"><div align="left">Writer</div></th>
				<th class="title"><div align="left">Submitted By</div></th>
				<th class="title"><div align="left">Category</div></th>
				<th class="title"><div align="center">Key</div></th>
				<th class="title"><div align="center">Views</div></th>
				<th class="title"><div align="center">Published</div></th>
			</tr>
			<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr>
				<td width="5%"><input type="checkbox" id="cb<?php echo $i ?>" name="cid[]" value="<?php echo $row->song_id ?>" onclick="isChecked(this.checked);" /></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->song_id ?></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><a href="index2.php?option=com_chordbase&task=editSong&song_id=<?php echo $row->song_id ?>"><?php echo $row->title ?></a></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $writers[$row->writer]->name ?></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $users_array[$row->submitted_by]->username ?> (<?php echo $users_array[$row->submitted_by]->name ?>) </td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $categories[$row->category]->title ?></td>
				<td align="center" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->song_key ?></td>
				<td align="center" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->views ?></td>
		<?php
				$task = $row->published ? 'unpublishSong' : 'publishSong';
				$img = $row->published ? 'publish_g.png' : 'publish_x.png'; 
			?>
				<td width="10%" align="center"><a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
			</tr>
		<?php 
			$k = 1 - $k;
		} ?>
			<tr>
				<th align="center" colspan="9"><?php echo $pageNav->writePagesLinks(); ?></th>
			</tr>
			<tr>
				<td align="center" colspan="9"><?php echo $pageNav->writePagesCounter(); ?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="songlist" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
	<?php }

	function categoryList( $option, &$rows, $search, $pageNav ) { 

		global $mosConfig_live_site; ?>

		<form action="index2.php" method="post" name="adminForm" >
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td><img src="<?php echo $mosConfig_live_site ?>/components/com_chordbase/images/ChordBase.png" /></td>
				<td width="100%" align="center" valign="bottom"><h1>Categories</h1></td>
				<td nowrap="nowrap">Display #</td>
				<td><?php echo $pageNav->writeLimitBox(); ?></td>
				<td>Search:</td>
				<td><input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" /></td>
			</tr>
		</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
				<th class="title"><div align="left">ID</div></th>
				<th class="title"><div align="left">Title</div></th>
				<th class="title"><div align="center">Publish</div></th>
			</tr>
			<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr>
				<td width="5%"><input type="checkbox" id="cb<?php echo $i ?>" name="cid[]" value="<?php echo $row->category_id ?>" onclick="isChecked(this.checked);" /></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->category_id ?></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><a href="index2.php?option=com_chordbase&task=editCategory&category_id=<?php echo $row->category_id ?>"><?php echo $row->title ?></a></td>
		<?php
				$task = $row->published ? 'unpublishCategory' : 'publishCategory';
				$img = $row->published ? 'publish_g.png' : 'publish_x.png'; 
			?>
				<td width="10%" align="center"><a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
			</tr>
		<?php 
			$k = 1 - $k;
		} ?>
			<tr>
				<th align="center" colspan="4"><?php echo $pageNav->writePagesLinks(); ?></th>
			</tr>
			<tr>
				<td align="center" colspan="4"><?php echo $pageNav->writePagesCounter(); ?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="categoryList" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
	<?php }

	function writerList( $option, &$rows, $search, $pageNav ) { 

		global $mosConfig_live_site; ?>

		<form action="index2.php" method="post" name="adminForm" >
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td><img src="<?php echo $mosConfig_live_site ?>/components/com_chordbase/images/ChordBase.png" /></td>
				<td width="100%" align="center" valign="bottom"><h1>Writers</h1></td>
				<td nowrap="nowrap">Display #</td>
				<td><?php echo $pageNav->writeLimitBox(); ?></td>
				<td>Search:</td>
				<td><input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" /></td>
			</tr>
		</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
				<th class="title"><div align="left">ID</div></th>
				<th class="title"><div align="left">Name</div></th>
				<th class="title"><div align="center">Publish</div></th>
			</tr>
			<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr>
				<td width="5%"><input type="checkbox" id="cb<?php echo $i ?>" name="cid[]" value="<?php echo $row->writer_id ?>" onclick="isChecked(this.checked);" /></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->writer_id ?></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><a href="index2.php?option=com_chordbase&task=editWriter&writer_id=<?php echo $row->writer_id ?>"><?php echo $row->name ?></a></td>
		<?php
				$task = $row->published ? 'unpublishWriter' : 'publishWriter';
				$img = $row->published ? 'publish_g.png' : 'publish_x.png'; 
			?>
				<td width="10%" align="center"><a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
			</tr>
		<?php 
			$k = 1 - $k;
		} ?>
			<tr>
				<th align="center" colspan="4"><?php echo $pageNav->writePagesLinks(); ?></th>
			</tr>
			<tr>
				<td align="center" colspan="4"><?php echo $pageNav->writePagesCounter(); ?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="writerList" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
	<?php }

	function setList( $option, &$rows, &$categories, &$users, $search, $pageNav ) { 

		global $mosConfig_live_site; ?>

		<form action="index2.php" method="post" name="adminForm" >
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td><img src="<?php echo $mosConfig_live_site ?>/components/com_chordbase/images/ChordBase.png" /></td>
				<td width="100%" align="center" valign="bottom"><h1>Sets</h1></td>
				<td nowrap="nowrap">Display #</td>
				<td><?php echo $pageNav->writeLimitBox(); ?></td>
				<td>Search:</td>
				<td><input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" /></td>
			</tr>
		</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
				<th class="title"><div align="left">ID</div></th>
				<th class="title"><div align="left">Name</div></th>
				<th class="title"><div align="left">Category</div></th>
				<th class="title"><div align="left">Creator</div></th>
				<th class="title"><div align="left">Views</div></th>
				<th class="title"><div align="center">Publish</div></th>
			</tr>
			<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i]; ?>
			<tr>
				<td width="5%"><input type="checkbox" id="cb<?php echo $i ?>" name="cid[]" value="<?php echo $row->set_id ?>" onclick="isChecked(this.checked);" /></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->set_id ?></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><a href="index2.php?option=com_chordbase&task=editSet&set_id=<?php echo $row->set_id ?>"><?php echo $row->name ?></a></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $categories[$row->category]->title ?></td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $users[$row->creator]->username ?> (<?php echo $users[$row->creator]->username ?>)</td>
				<td align="left" class="sectiontableentry<?php echo ($k+1) ?>"><?php echo $row->views ?></td>
		<?php
				$task = $row->published ? 'unpublishWriter' : 'publishWriter';
				$img = $row->published ? 'publish_g.png' : 'publish_x.png'; 
			?>
				<td width="10%" align="center"><a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
			</tr>
		<?php 
			$k = 1 - $k;
		} ?>
			<tr>
				<th align="center" colspan="7"><?php echo $pageNav->writePagesLinks(); ?></th>
			</tr>
			<tr>
				<td align="center" colspan="7"><?php echo $pageNav->writePagesCounter(); ?></td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="writerList" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
	<?php }

	function permissions( $option, $permissions ) { 
		?>
		<form action="index2.php" method="post" name="adminForm" >
		<table>
			<tr class="lightRow">
			  <td valign="top">
				<h3 style="margin-top:0px">Default User Permissions:</h3>
			  </td>
			  <td>
				<?php
				foreach($permissions as $permission){
					echo '
					<input name="default_permissions[]" type="checkbox" value="'.$permission["value"].'"'.$permission["default_selected"].' />'.$permission["name"].'<br />';
				}
				?>
			  </td>
			</tr>
			<tr class="darkRow">
			  <td valign="top">
				<h3 style="margin-top:0px">Visitor Permissions:</h3>
			  </td>
			  <td>
				<?php
				foreach($permissions as $permission){
					echo '
					<input name="visitor_permissions[]" type="checkbox" value="'.$permission["value"].'"'.$permission["visitor_selected"].' />'.$permission["name"].'<br />';
				}
				?>
			  </td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="savePermissions" />
		</form>


		<?php
	}

	function message( $message ) { 
		echo '<center>'.$message.'</center>';
	}

	function configuration( ) { 
		?>
		<center>Reserved for future use.</center>
		<?php
	}

	function showhelp( ) { 
		?>
		<center>For help visit <a href="http://www.chordbase.com" target="_blank">ChordBase.com</a></center>
		<?php
	}


} ?>