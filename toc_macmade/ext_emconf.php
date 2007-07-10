<?php

########################################################################
# Extension Manager/Repository config file for ext: "toc_macmade"
#
# Auto generated 19-12-2006 13:18
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Table of contents',
	'description' => 'An automatic table of contents for content records on a page.',
	'category' => 'plugin',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'shy' => '',
	'dependencies' => 'cms,lang,api_macmade,templavoila',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'macmade.net',
	'version' => '0.1.1',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lang' => '',
			'api_macmade' => '0.2.8-',
			'templavoila' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:14:{s:27:"class.tx_tocmacmade_tca.php";s:4:"51d3";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"e5c0";s:14:"ext_tables.php";s:4:"170f";s:24:"ext_typoscript_setup.txt";s:4:"72fc";s:19:"flexform_ds_pi1.xml";s:4:"30da";s:13:"locallang.xml";s:4:"39fe";s:16:"locallang_db.xml";s:4:"4fa7";s:14:"doc/manual.sxw";s:4:"06d7";s:14:"pi1/ce_wiz.gif";s:4:"f801";s:31:"pi1/class.tx_tocmacmade_pi1.php";s:4:"ad8c";s:39:"pi1/class.tx_tocmacmade_pi1_wizicon.php";s:4:"a0b2";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"4bbb";}',
	'suggests' => array(
	),
);

?>