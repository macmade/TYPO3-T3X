<?php

########################################################################
# Extension Manager/Repository config file for ext: "fri_headers"
#
# Auto generated 04-09-2006 19:02
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'FRI Headers',
	'description' => '',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => 'cms,lang,api_macmade',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => 0,
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@gadlab.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.0.0',
	'_md5_values_when_last_written' => 'a:7:{s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"5d01";s:14:"ext_tables.php";s:4:"32e0";s:14:"ext_tables.sql";s:4:"35a6";s:24:"ext_typoscript_setup.txt";s:4:"75f9";s:16:"locallang_db.php";s:4:"869e";s:31:"pi1/class.tx_friheaders_pi1.php";s:4:"e6b2";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lang' => '',
			'api_macmade' => '',
			'php' => '3.0.0-',
			'typo3' => '3.5.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>