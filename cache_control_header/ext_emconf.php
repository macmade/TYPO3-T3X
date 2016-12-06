<?php

########################################################################
# Extension Manager/Repository config file for ext "cache_control_header".
#
# Auto generated 20-05-2010 15:22
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Cache Control Header',
	'description' => 'This extension allows a fine tuning of the Cache-Control HTTP header, in order to solve problems when accessing the TYPO3 frontend from behind an HTTP proxy.',
	'category' => 'fe',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@netinfluence.com',
	'author_company' => 'netinfluence',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'version' => '0.1.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:9:{s:12:"ext_icon.gif";s:4:"8cd0";s:17:"ext_localconf.php";s:4:"5f1f";s:14:"ext_tables.php";s:4:"919f";s:14:"ext_tables.sql";s:4:"c5b2";s:28:"ext_typoscript_constants.txt";s:4:"3555";s:24:"ext_typoscript_setup.txt";s:4:"5c88";s:16:"locallang_db.xml";s:4:"a45b";s:50:"Classes/class.tx_cachecontrolheader_controller.php";s:4:"3682";s:14:"doc/manual.sxw";s:4:"7a5b";}',
);

?>