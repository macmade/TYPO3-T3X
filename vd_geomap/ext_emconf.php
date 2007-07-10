<?php

########################################################################
# Extension Manager/Repository config file for ext: "vd_geomap"
#
# Auto generated 03-07-2007 22:27
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'VD / Geomap',
	'description' => 'A plugin to display swiss maps from Geoplanet.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.3.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lang' => '',
			'api_macmade' => '0.2.8-',
			'xajax' => '0.2.4-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:31:{s:25:"class.tx_vdgeomap_tca.php";s:4:"d824";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"54c6";s:14:"ext_tables.php";s:4:"5627";s:28:"ext_typoscript_constants.txt";s:4:"5057";s:24:"ext_typoscript_setup.txt";s:4:"9d7d";s:19:"flexform_ds_pi1.xml";s:4:"5e72";s:13:"locallang.xml";s:4:"b5a9";s:16:"locallang_db.xml";s:4:"2a0e";s:14:"doc/manual.sxw";s:4:"0f95";s:14:"pi1/ce_wiz.gif";s:4:"87fb";s:29:"pi1/class.tx_vdgeomap_pi1.php";s:4:"0b4d";s:37:"pi1/class.tx_vdgeomap_pi1_wizicon.php";s:4:"622d";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"c11b";s:12:"res/down.gif";s:4:"0b7a";s:12:"res/help.jpg";s:4:"4e2d";s:17:"res/left-down.gif";s:4:"0385";s:15:"res/left-up.gif";s:4:"0e4b";s:12:"res/left.gif";s:4:"8e36";s:17:"res/minus-off.gif";s:4:"89cb";s:13:"res/minus.gif";s:4:"61d5";s:16:"res/plus-off.gif";s:4:"0d6d";s:12:"res/plus.gif";s:4:"6de1";s:18:"res/right-down.gif";s:4:"10fa";s:16:"res/right-up.gif";s:4:"4c66";s:13:"res/right.gif";s:4:"a4f3";s:10:"res/up.gif";s:4:"742a";s:20:"res/zoom-current.gif";s:4:"8090";s:21:"res/zoom-decrease.gif";s:4:"d160";s:21:"res/zoom-increase.gif";s:4:"a20c";}',
);

?>