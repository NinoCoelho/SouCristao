<?php
// *****************************************************************************************
// Title          PrayerCenter Latest Prayer Module for Joomla 1.5
// Author         Mike Leeper
// Version        1.5.0
// License        This is free software and you may redistribute it under the GPL.
//                PrayerCenter Latest Prayer comes with absolutely no warranty. For details,
//                see the license at http://www.gnu.org/licenses/gpl.txt
//                YOU ARE NOT REQUIRED TO KEEP COPYRIGHT NOTICES IN
//                THE HTML OUTPUT OF THIS SCRIPT. YOU ARE NOT ALLOWED
//                TO REMOVE COPYRIGHT NOTICES FROM THE SOURCE CODE.
//
// ******************************************************************************************

defined( '_JEXEC' ) or die( 'Restricted access' );// no direct access

  global $mainframe;

  $livesite = JURI::base();
  $lang =& Jfactory::getLanguage();
  $lang->load( 'com_prayercenter', JPATH_SITE);

  $count 		= $params->get( 'count' );
  $rssurl	= $params->get( 'rss url' ); // $livesite.'/index.php?option=com_prayercenter&task=rss';

  $options = array();
  $options['rssUrl'] = $rssurl;
	if ($params->get('cache')) {
		$options['cache_time'] = $params->get('cache_time', 15) ;
		$options['cache_time'] *= 60;
	} else {
		$options['cache_time'] = null;
	}

	$modRssDoc =& JFactory::getXMLparser('RSS', $options);
	$feed = new stdclass();

	if ($modRssDoc != false)
	{
	$feed->title = $modRssDoc->get_title();
	$feed->description = $modRssDoc->get_description();
	$items = $modRssDoc->get_items();
	$feed->items = array_slice($items, 0, $params->get('count'));
	$actualItems = count( $feed->items );
	$setItems    = $params->get('count');
	if ($setItems > $actualItems) {
		$totalItems = $actualItems;
	} else {
		$totalItems = $setItems;
	}
	?>
  <style>
    table.moduletable ul.pcl {
    	margin-left: 1px;
    }
  </style>
	<table cellpadding="0" cellspacing="0" class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
	<tr>
		<td>
			<ul class="pcl<?php echo $params->get( 'moduleclass_sfx'); ?>"  >
			<?php
			for ($j = 0; $j < $totalItems; $j ++)
			{
				$currItem = & $feed->items[$j];
				?>
				<p class="newsfeed_item<?php echo $params->get( 'moduleclass_sfx'); ?>"  >
					<div>"<?php echo $currItem->get_description(); ?>"</div>
					<div align=right><strong><i><?php echo $currItem->get_title() ?></i></strong></div>
				</p>
				<?php
			}
			?>
			</ul>
		</td>
		</tr>
	</table>
	<?php
  } else {
  	echo 'Sem versículos hoje';
  	return;
  }
?>
