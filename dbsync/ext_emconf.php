<?php

########################################################################
# Extension Manager/Repository config file for ext: "dbsync"
#
# Auto generated 10-07-2007 14:57
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'DB Sync',
	'description' => 'A backend module which allows you to synchronize your Typo3 database with an external Typo3 database. It also allows you to synchronize two different table inside the same database.',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'beta',
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
			'api_macmade' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:12:"ext_icon.gif";s:4:"c839";s:14:"ext_tables.php";s:4:"c3f8";s:14:"doc/manual.sxw";s:4:"1a0f";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"8ed1";s:14:"mod1/index.php";s:4:"51ab";s:18:"mod1/locallang.php";s:4:"bc7f";s:22:"mod1/locallang_mod.php";s:4:"697c";s:19:"mod1/moduleicon.gif";s:4:"2f43";s:15:"mod1/scripts.js";s:4:"3c4b";}',
);

?>