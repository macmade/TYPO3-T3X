<?php

########################################################################
# Extension Manager/Repository config file for ext: "countdown_macmade"
#
# Auto generated 31-01-2008 17:41
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'CountDown / macmade.net',
	'description' => 'A simple coutdown.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.0.0',
	'dependencies' => 'cms,lang,api_macmade',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
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
			'api_macmade' => '0.4.4-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"cc62";s:14:"ext_tables.php";s:4:"582f";s:24:"ext_typoscript_setup.txt";s:4:"400f";s:19:"flexform_ds_pi1.xml";s:4:"130c";s:16:"locallang_db.xml";s:4:"6bb5";s:37:"pi1/class.tx_countdownmacmade_pi1.php";s:4:"0c35";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"da6d";s:14:"res/counter.js";s:4:"e7e9";}',
	'suggests' => array(
	),
);

?>