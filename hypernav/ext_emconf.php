<?php

########################################################################
# Extension Manager/Repository config file for ext: "hypernav"
#
# Auto generated 31-01-2008 21:18
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Hyper Navigation System',
	'description' => 'The Hyper Navigation System plugin is a rootline type menu, where each section is a link with a drop-down menu displaying the sub pages. It can display as many sub levels as you want. This way, with a simple rootline, every single page of your website is accessible from anywhere.',
	'category' => 'fe',
	'shy' => 0,
	'dependencies' => 'cms,lang',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@gadlab.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.1.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.5.0-0.0.0',
			'php' => '3.0.0-0.0.0',
			'cms' => '',
			'lang' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"499b";s:24:"ext_typoscript_setup.txt";s:4:"3329";s:14:"doc/manual.sxw";s:4:"0ba7";s:29:"pi1/class.tx_hypernav_pi1.php";s:4:"7498";}',
	'suggests' => array(
	),
);

?>