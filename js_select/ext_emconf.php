<?php

########################################################################
# Extension Manager/Repository config file for ext: "js_select"
#
# Auto generated 10-07-2007 14:58
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Page JavaScript Selector',
	'description' => 'Select one or more JavaScript file(s) for each page of the tree...',
	'category' => 'be',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@gadlab.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.3.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.5.0-0.0.0',
			'php' => '3.0.0-0.0.0',
			'cms' => '',
			'lang' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:11:{s:39:"class.tx_jsselect_handlejavascripts.php";s:4:"8c51";s:21:"ext_conf_template.txt";s:4:"64aa";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"5e0a";s:14:"ext_tables.php";s:4:"c945";s:14:"ext_tables.sql";s:4:"8678";s:28:"ext_typoscript_constants.txt";s:4:"a937";s:24:"ext_typoscript_setup.txt";s:4:"a9c1";s:16:"locallang_db.php";s:4:"9bdd";s:14:"doc/manual.sxw";s:4:"0b54";s:29:"pi1/class.tx_jsselect_pi1.php";s:4:"8814";}',
);

?>