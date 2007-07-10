<?php

########################################################################
# Extension Manager/Repository config file for ext: "content_uneraser"
#
# Auto generated 10-07-2007 14:54
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Content UnEraser',
	'description' => 'Allows backend users to retrieve erased content from all the available tables present in the Typo3 database.',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
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
	'version' => '0.2.0',
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
	'_md5_values_when_last_written' => 'a:10:{s:12:"ext_icon.gif";s:4:"c839";s:14:"ext_tables.php";s:4:"c373";s:14:"doc/manual.sxw";s:4:"4bbe";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"64b3";s:14:"mod1/index.php";s:4:"51ee";s:18:"mod1/locallang.php";s:4:"f50d";s:22:"mod1/locallang_mod.php";s:4:"6275";s:19:"mod1/moduleicon.gif";s:4:"247a";s:15:"mod1/scripts.js";s:4:"058c";}',
);

?>