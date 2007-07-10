<?php

########################################################################
# Extension Manager/Repository config file for ext: "vd_municipalities"
#
# Auto generated 03-07-2007 10:48
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'VD / Municipalities',
	'description' => 'An extension that imports informations about swiss municipalities from a web service. This extension only works with PHP5 as it used SimpleXML.',
	'category' => 'module',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'shy' => '',
	'dependencies' => 'cms,lang',
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'macmade.net',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lang' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:16:{s:36:"class.tx_vdmunicipalities_import.php";s:4:"d516";s:21:"ext_conf_template.txt";s:4:"828c";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"cf6c";s:14:"ext_tables.php";s:4:"8f26";s:14:"ext_tables.sql";s:4:"9990";s:43:"icon_tx_vdmunicipalities_municipalities.gif";s:4:"c890";s:16:"locallang_db.xml";s:4:"9dca";s:7:"tca.php";s:4:"0e1f";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"a496";s:14:"mod1/index.php";s:4:"85fd";s:18:"mod1/locallang.xml";s:4:"0b0e";s:22:"mod1/locallang_mod.xml";s:4:"8101";s:19:"mod1/moduleicon.gif";s:4:"3fcb";s:20:"mod1/res/extconf.gif";s:4:"62a3";}',
	'suggests' => array(
	),
);

?>