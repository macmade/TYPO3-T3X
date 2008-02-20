<?php

########################################################################
# Extension Manager/Repository config file for ext: "vd_multiplesearch"
#
# Auto generated 05-02-2008 18:53
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
	'_md5_values_when_last_written' => 'a:23:{s:28:"class.tx_vdmultiplesearch_tca.php";s:4:"067b";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"8a34";s:14:"ext_tables.php";s:4:"d955";s:14:"ext_tables.sql";s:4:"2305";s:19:"flexform_ds_pi1.xml";s:4:"11b8";s:32:"icon_tx_vdmultiplesearch_keywords.gif";s:4:"7363";s:30:"icon_tx_vdmultiplesearch_public.gif";s:4:"0b72";s:30:"icon_tx_vdmultiplesearch_themes.gif";s:4:"0b72";s:13:"locallang.xml";s:4:"da20";s:16:"locallang_db.xml";s:4:"1e84";s:7:"tca.php";s:4:"746f";s:14:"pi1/ce_wiz.gif";s:4:"e7c0";s:32:"pi1/class.tx_vdmultiplesearch_pi1.php";s:4:"5866";s:40:"pi1/class.tx_vdmultiplesearch_pi1_wizicon.php";s:4:"a317";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"b322";s:14:"res/cancel.gif";s:4:"0673";s:17:"res/magnifier.gif";s:4:"b5b4";s:21:"res/template_pi1.html";s:4:"4321";s:20:"static/css/setup.txt";s:4:"fd91";s:23:"static/ts/constants.txt";s:4:"691f";s:19:"static/ts/setup.txt";s:4:"c8dc";}',
	'suggests' => array(
	),
);

?>
