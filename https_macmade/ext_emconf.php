<?php

########################################################################
# Extension Manager/Repository config file for ext: "https_macmade"
#
# Auto generated 14-08-2007 16:49
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'HTTPS Enforcer / macmade.net',
	'description' => 'A RealURL compatible HTTPS enforcer, for pages or site sections',
	'category' => 'fe',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'shy' => 0,
	'dependencies' => 'cms,lang',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'macmade.net',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.0-0.0.0',
			'php' => '4.1.0-0.0.0',
			'cms' => '',
			'lang' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"91eb";s:14:"ext_tables.php";s:4:"a081";s:14:"ext_tables.sql";s:4:"27b2";s:16:"locallang_db.xml";s:4:"99ff";s:14:"doc/manual.sxw";s:4:"289e";s:33:"pi1/class.tx_httpsmacmade_pi1.php";s:4:"7ea0";s:24:"pi1/static/constants.txt";s:4:"189a";s:20:"pi1/static/setup.txt";s:4:"03ff";}',
	'suggests' => array(
	),
);

?>