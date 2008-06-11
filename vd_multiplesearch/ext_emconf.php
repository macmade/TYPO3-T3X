<?php

########################################################################
# Extension Manager/Repository config file for ext: "vd_multiplesearch"
#
# Auto generated 11-06-2008 11:52
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'VD / Multiple Search',
	'description' => 'A plugin to search specific TYPO3 pages.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.0.0',
	'dependencies' => 'cms,lang,api_macmade',
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
			'api_macmade' => '0.4.2-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:23:{s:33:"class.tx_vdmultiplesearch_tca.php";s:4:"5875";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"b0e0";s:14:"ext_tables.php";s:4:"8c84";s:14:"ext_tables.sql";s:4:"d88f";s:19:"flexform_ds_pi1.xml";s:4:"2db5";s:37:"icon_tx_vdmultiplesearch_keywords.gif";s:4:"7363";s:35:"icon_tx_vdmultiplesearch_public.gif";s:4:"0b72";s:35:"icon_tx_vdmultiplesearch_themes.gif";s:4:"0b72";s:13:"locallang.xml";s:4:"78c1";s:16:"locallang_db.xml";s:4:"6897";s:7:"tca.php";s:4:"4a71";s:14:"pi1/ce_wiz.gif";s:4:"e7c0";s:37:"pi1/class.tx_vdmultiplesearch_pi1.php";s:4:"6844";s:45:"pi1/class.tx_vdmultiplesearch_pi1_wizicon.php";s:4:"83de";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"34e8";s:14:"res/cancel.gif";s:4:"0673";s:17:"res/magnifier.gif";s:4:"b5b4";s:21:"res/template_pi1.html";s:4:"5b50";s:20:"static/css/setup.txt";s:4:"246b";s:23:"static/ts/constants.txt";s:4:"0832";s:19:"static/ts/setup.txt";s:4:"743e";}',
	'suggests' => array(
	),
);

?>