<?php

########################################################################
# Extension Manager/Repository config file for ext: "https_macmade"
#
# Auto generated 06-02-2009 16:11
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
	'dependencies' => '',
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
	'version' => '0.2.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.2.0-0.0.0',
			'php' => '5.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"ee70";s:14:"ext_tables.php";s:4:"cf50";s:14:"ext_tables.sql";s:4:"3b48";s:16:"locallang_db.xml";s:4:"e68f";s:14:"doc/manual.sxw";s:4:"0bff";s:33:"pi1/class.tx_httpsmacmade_pi1.php";s:4:"82b8";s:24:"pi1/static/constants.txt";s:4:"8c33";s:20:"pi1/static/setup.txt";s:4:"a52d";}',
	'suggests' => array(
	),
);

?>