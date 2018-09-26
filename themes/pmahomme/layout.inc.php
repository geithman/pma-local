<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * configures general layout
 * for detailed layout configuration please refer to the css files
 *
 * @package    PhpMyAdmin-theme
 * @subpackage PMAHomme
 */

/**
 * navi frame
 */
// navi frame width
$GLOBALS['cfg']['NaviWidth']                = 240;

// foreground (text) color for the navi frame
$GLOBALS['cfg']['NaviColor']                = '#000';

// background for the navi frame
$GLOBALS['cfg']['NaviBackground']           = '#f3f3f3';

// foreground (text) color of the pointer in navi frame
$GLOBALS['cfg']['NaviPointerColor']         = '#000';

// background of the pointer in navi frame
$GLOBALS['cfg']['NaviPointerBackground']    = '#ddd';

/**
 * main frame
 */
// foreground (text) color for the main frame
$GLOBALS['cfg']['MainColor']                = '#444';

// background for the main frame
// $GLOBALS['cfg']['MainBackground']           = '#fff';
if (@file_exists('/srv/hadata/cluster/webdata/mpoadmin/')) {  // Cluster ?? ffcce0
	$GLOBALS['cfg']['MainBackground']           = '#ffcce0';
} elseif (substr_compare($_SERVER['SERVER_NAME'], 'geithspace.de',-13,13,TRUE)===0) { // Geithspace.de ???
	$GLOBALS['cfg']['MainBackground']           = '#ffe6f3';
} elseif (substr_compare($_SERVER['SERVER_NAME'], '.dev',-4,4,TRUE)===0 || substr_compare($_SERVER['SERVER_NAME'], '.local',-6,6,TRUE)===0) { // Vagrant dev/test-Server ? e6ffe6
	$GLOBALS['cfg']['MainBackground']           = '#e6ffe6';
} elseif (substr_compare($_SERVER['SERVER_NAME'], 'orga.cpl-web.de',-15,15,TRUE)===0) { // Orga ???
	$GLOBALS['cfg']['MainBackground']           = '#ffeecc';
} elseif (substr_compare($_SERVER['SERVER_NAME'], 'geithwork.de',-12,12,TRUE)===0) { // Geithwork.de ???
	$GLOBALS['cfg']['MainBackground']           = '#f2ffcc';
} else {
	$GLOBALS['cfg']['MainBackground']           = '#ffffff';
}

// foreground (text) color of the pointer in browse mode
$GLOBALS['cfg']['BrowsePointerColor']       = '#000';

// background of the pointer in browse mode
$GLOBALS['cfg']['BrowsePointerBackground']  = '#cfc';

// foreground (text) color of the marker (visually marks row by clicking on it)
// in browse mode
$GLOBALS['cfg']['BrowseMarkerColor']        = '#000';

// background of the marker (visually marks row by clicking on it) in browse mode
$GLOBALS['cfg']['BrowseMarkerBackground']   = '#fc9';

/**
 * fonts
 */
/**
 * the font family as a valid css font family value,
 * if not set the browser default will be used
 * (depending on browser, DTD and system settings)
 */
$GLOBALS['cfg']['FontFamily']           = 'sans-serif';
/**
 * fixed width font family, used in textarea
 */
$GLOBALS['cfg']['FontFamilyFixed']      = 'monospace';

/**
 * tables
 */
// border
$GLOBALS['cfg']['Border']               = 0;
// table header and footer color
$GLOBALS['cfg']['ThBackground']         = '#D3DCE3';
// table header and footer background
$GLOBALS['cfg']['ThColor']              = '#000';
// table data row background
$GLOBALS['cfg']['BgOne']                = '#E5E5E5';
// table data row background, alternate
$GLOBALS['cfg']['BgTwo']                = '#D5D5D5';
