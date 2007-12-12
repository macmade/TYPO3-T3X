<?php

########################################################################
# Extension Manager/Repository config file for ext: "terminal"
#
# Auto generated 12-12-2007 15:55
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Terminal (PHP Shell)',
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
	'version' => '0.3.1',
	'constraints' => array(
		'depends' => array(
			'api_macmade' => '0.3.0-',
			'php' => '4.1.2-0.0.0',
			'typo3' => '3.8.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:27:{s:21:"ext_conf_template.txt";s:4:"8e59";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"de54";s:14:"ext_tables.php";s:4:"b228";s:14:"doc/manual.sxw";s:4:"a161";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"8e83";s:14:"mod1/index.php";s:4:"6d57";s:18:"mod1/locallang.xml";s:4:"de5c";s:22:"mod1/locallang_mod.xml";s:4:"fba0";s:19:"mod1/moduleicon.gif";s:4:"c0c0";s:15:"mod1/scripts.js";s:4:"ac11";s:14:"res/custom.png";s:4:"a28a";s:12:"res/date.png";s:4:"b139";s:10:"res/df.png";s:4:"8996";s:17:"res/fileadmin.png";s:4:"0601";s:16:"res/ifconfig.png";s:4:"33e0";s:10:"res/ls.png";s:4:"f6a8";s:11:"res/pwd.png";s:4:"3750";s:12:"res/site.png";s:4:"99be";s:13:"res/t3lib.png";s:4:"cf44";s:11:"res/top.png";s:4:"5f72";s:13:"res/typo3.png";s:4:"4866";s:17:"res/typo3conf.png";s:4:"f39d";s:17:"res/typo3temp.png";s:4:"9a18";s:15:"res/uploads.png";s:4:"b286";s:14:"res/whoami.png";s:4:"4fb4";}',
	'suggests' => array(
	),
);

?>