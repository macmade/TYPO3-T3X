<?php

########################################################################
# Extension Manager/Repository config file for ext: "tslib_patcher"
#
# Auto generated 12-12-2007 14:56
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'TSLib Patcher',
	'description' => 'A correction for some bugs in the TSLib core classes. Actually, it mainly correct bugs related to the cache hash (cHash) variable. The use of this extension should increase the overall performances of your TYPO3 website.',
	'category' => 'fe',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'macmade.net',
	'version' => '0.1.4',
	'constraints' => array(
		'depends' => array(
			'php' => '4.1.2-0.0.0',
			'typo3' => '3.8.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:8:{s:25:"class.tx_tslibpatcher.php";s:4:"2dba";s:23:"class.ux_tslib_cobj.php";s:4:"8126";s:23:"class.ux_tslib_menu.php";s:4:"b9df";s:23:"class.ux_tx_realurl.php";s:4:"8df1";s:21:"ext_conf_template.txt";s:4:"4e32";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"13cb";s:14:"doc/manual.sxw";s:4:"ea1e";}',
	'suggests' => array(
	),
);

?>