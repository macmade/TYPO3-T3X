<?php

########################################################################
# Extension Manager/Repository config file for ext: "css_select"
#
# Auto generated 10-07-2007 14:58
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Page StyleSheet Selector',
	'description' => 'Select one or more stylesheet(s) for each page of the tree... The goal of this extension is to drastically reduce the weight of the pages generated by TYPO3, by including only the styles needed in the page.',
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
	'version' => '0.5.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:11:{s:40:"class.tx_cssselect_handlestylesheets.php";s:4:"dbc3";s:21:"ext_conf_template.txt";s:4:"6510";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"8b03";s:14:"ext_tables.php";s:4:"9059";s:14:"ext_tables.sql";s:4:"5b95";s:28:"ext_typoscript_constants.txt";s:4:"14b8";s:24:"ext_typoscript_setup.txt";s:4:"8324";s:16:"locallang_db.php";s:4:"9cf0";s:14:"doc/manual.sxw";s:4:"3a83";s:30:"pi1/class.tx_cssselect_pi1.php";s:4:"555d";}',
);

?>
