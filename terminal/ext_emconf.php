<?php

########################################################################
# Extension Manager/Repository config file for ext: "terminal"
#
# Auto generated 09-07-2007 09:52
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Terminal',
	'description' => 'Yet another PHP shell extension. This extension allows you to run shell commands on the server from the TYPO3 backend. This may be useful for changing files permissions, creating symbolic links, uncompressing archives, etc.',
	'category' => 'module',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'shy' => '',
	'dependencies' => 'api_macmade',
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
			'api_macmade' => '0.3.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:16:{s:36:"class.tx_vdmunicipalities_import.php";s:4:"7c93";s:21:"ext_conf_template.txt";s:4:"a23f";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"cf6c";s:14:"ext_tables.php";s:4:"8f26";s:14:"ext_tables.sql";s:4:"9990";s:43:"icon_tx_vdmunicipalities_municipalities.gif";s:4:"c890";s:16:"locallang_db.xml";s:4:"9dca";s:7:"tca.php";s:4:"0e1f";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"a496";s:14:"mod1/index.php";s:4:"f608";s:18:"mod1/locallang.xml";s:4:"0b0e";s:22:"mod1/locallang_mod.xml";s:4:"8101";s:19:"mod1/moduleicon.gif";s:4:"3fcb";s:20:"mod1/res/extconf.gif";s:4:"62a3";}',
	'suggests' => array(
	),
);

?>
