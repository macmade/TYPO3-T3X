<?php

########################################################################
# Extension Manager/Repository config file for ext: "vd_geomap_prototype"
#
# Auto generated 22-08-2007 18:53
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'VD / Geomap (Prototype JS version)',
	'description' => 'A plugin to display swiss maps from Geoplanet.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.1.1',
	'dependencies' => 'cms,lang,api_macmade',
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
			'api_macmade' => '0.3.2-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'tslib_patcher' => '0.1.3-',
		),
	),
	'_md5_values_when_last_written' => 'a:33:{s:20:"class.ext_update.php";s:4:"deb5";s:34:"class.tx_vdgeomapprototype_tca.php";s:4:"7d44";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"c482";s:14:"ext_tables.php";s:4:"8b8e";s:28:"ext_typoscript_constants.txt";s:4:"322b";s:24:"ext_typoscript_setup.txt";s:4:"0d9f";s:19:"flexform_ds_pi1.xml";s:4:"d3be";s:13:"locallang.xml";s:4:"6798";s:16:"locallang_db.xml";s:4:"4c76";s:14:"doc/manual.sxw";s:4:"d1a2";s:14:"pi1/ce_wiz.gif";s:4:"87fb";s:38:"pi1/class.tx_vdgeomapprototype_pi1.php";s:4:"19b4";s:46:"pi1/class.tx_vdgeomapprototype_pi1_wizicon.php";s:4:"9ab7";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"9b67";s:12:"res/down.gif";s:4:"0b7a";s:12:"res/help.jpg";s:4:"4e2d";s:17:"res/left-down.gif";s:4:"0385";s:15:"res/left-up.gif";s:4:"0e4b";s:12:"res/left.gif";s:4:"8e36";s:17:"res/minus-off.gif";s:4:"89cb";s:13:"res/minus.gif";s:4:"61d5";s:16:"res/plus-off.gif";s:4:"0d6d";s:12:"res/plus.gif";s:4:"6de1";s:16:"res/prototype.js";s:4:"3766";s:18:"res/right-down.gif";s:4:"10fa";s:16:"res/right-up.gif";s:4:"4c66";s:13:"res/right.gif";s:4:"a4f3";s:10:"res/up.gif";s:4:"742a";s:20:"res/zoom-current.gif";s:4:"8090";s:21:"res/zoom-decrease.gif";s:4:"d160";s:21:"res/zoom-increase.gif";s:4:"a20c";}',
	'suggests' => array(
	),
);

?>