<?php

########################################################################
# Extension Manager/Repository config file for ext: "https_macmade"
#
# Auto generated 11-07-2006 11:02
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
			'typo3' => '4.0-',
			'php' => '4.1.0-',
			'cms' => '',
			'lang' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:6:{s:9:"ChangeLog";s:4:"3ce4";s:10:"README.txt";s:4:"9fa9";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"36a5";s:14:"ext_tables.sql";s:4:"27b2";s:16:"locallang_db.xml";s:4:"2339";}',
);

?>
