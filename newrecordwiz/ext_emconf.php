<?php

########################################################################
# Extension Manager/Repository config file for ext: "newrecordwiz"
#
# Auto generated 10-07-2007 14:58
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'New Record Wizard CSM',
	'description' => 'This extension adds an item in the Typo3 context sensitive menu which allows you to create pages, content, and database records directly from the menu. This way, you get exactly the same functionalities as in the wizard, without having to load it each time you want to create a new element.',
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
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@gadlab.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.1.0',
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
	'_md5_values_when_last_written' => 'a:6:{s:29:"class.tx_newrecordwiz_cm1.php";s:4:"e9e3";s:21:"ext_conf_template.txt";s:4:"71a6";s:12:"ext_icon.gif";s:4:"c839";s:14:"ext_tables.php";s:4:"d390";s:13:"locallang.php";s:4:"d29c";s:14:"doc/manual.sxw";s:4:"06c1";}',
);

?>