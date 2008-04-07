<?php

########################################################################
# Extension Manager/Repository config file for ext: "js_select"
#
# Auto generated 07-04-2008 16:13
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
	'author_email' => 'info@macmade.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:13:{s:20:"class.ext_update.php";s:4:"aad2";s:39:"class.tx_jsselect_handlejavascripts.php";s:4:"18aa";s:21:"ext_conf_template.txt";s:4:"41c4";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"4983";s:14:"ext_tables.php";s:4:"4c35";s:14:"ext_tables.sql";s:4:"bce4";s:16:"locallang_db.xml";s:4:"8173";s:14:"doc/manual.sxw";s:4:"8662";s:29:"pi1/class.tx_jsselect_pi1.php";s:4:"517b";s:10:"res/js.gif";s:4:"f60b";s:20:"static/constants.txt";s:4:"ba0d";s:16:"static/setup.txt";s:4:"d46e";}',
	'suggests' => array(
	),
);

?>