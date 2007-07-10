<?php

########################################################################
# Extension Manager/Repository config file for ext: "loginbox_macmade"
#
# Auto generated 10-10-2006 13:21
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'LoginBox / macmade.net',
	'description' => 'A simple login box which allows you to define a different starting point for each instance of the plugin.',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => 'cms,lang,api_macmade',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.3.4',
	'_md5_values_when_last_written' => 'a:22:{s:9:".DS_Store";s:4:"1efb";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"b986";s:14:"ext_tables.php";s:4:"1937";s:28:"ext_typoscript_constants.txt";s:4:"b621";s:24:"ext_typoscript_setup.txt";s:4:"4ded";s:19:"flexform_ds_pi1.xml";s:4:"2e34";s:13:"locallang.xml";s:4:"1195";s:16:"locallang_db.xml";s:4:"d060";s:13:"doc/.DS_Store";s:4:"82ad";s:14:"doc/manual.sxw";s:4:"06d7";s:14:"pi1/ce_wiz.gif";s:4:"6e35";s:36:"pi1/class.tx_loginboxmacmade_pi1.php";s:4:"d83f";s:44:"pi1/class.tx_loginboxmacmade_pi1_wizicon.php";s:4:"3408";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.php";s:4:"bb42";s:17:"pi1/locallang.xml";s:4:"cbc9";s:36:"pi2/class.tx_loginboxmacmade_pi2.php";s:4:"2da7";s:13:"pi2/clear.gif";s:4:"cc11";s:13:"res/login.gif";s:4:"5192";s:14:"res/logout.gif";s:4:"4034";s:17:"res/template.html";s:4:"c794";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lang' => '',
			'api_macmade' => '0.2.8-',
			'php' => '3.0.0-',
			'typo3' => '3.5.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'core_permalogin' => '',
		),
	),
	'suggests' => array(
	),
);

?>