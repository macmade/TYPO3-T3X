<?php

########################################################################
# Extension Manager/Repository config file for ext: "vd_municipalities_search"
#
# Auto generated 14-11-2007 15:23
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'VD / Municipalities Search',
	'description' => 'A plugin to search specific municipalities pages.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.1.1',
	'dependencies' => 'cms,lang,api_macmade,vd_municipalities',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lang' => '',
			'api_macmade' => '0.2.8-',
			'vd_municipalities' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:39:"class.tx_vdmunicipalitiessearch_tca.php";s:4:"f9c0";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"5b6f";s:14:"ext_tables.php";s:4:"599c";s:14:"ext_tables.sql";s:4:"4222";s:24:"ext_typoscript_setup.txt";s:4:"2797";s:19:"flexform_ds_pi1.xml";s:4:"2357";s:47:"icon_tx_vdmunicipalitiessearch_institutions.gif";s:4:"2603";s:13:"locallang.xml";s:4:"1e93";s:16:"locallang_db.xml";s:4:"e939";s:7:"tca.php";s:4:"ac1b";s:14:"doc/manual.sxw";s:4:"721b";s:14:"pi1/ce_wiz.gif";s:4:"69e7";s:43:"pi1/class.tx_vdmunicipalitiessearch_pi1.php";s:4:"6261";s:51:"pi1/class.tx_vdmunicipalitiessearch_pi1_wizicon.php";s:4:"9fbb";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"7a48";s:14:"res/bullet.gif";s:4:"a884";s:17:"res/magnifier.gif";s:4:"b5b4";}',
	'suggests' => array(
	),
);

?>