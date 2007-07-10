<?php

########################################################################
# Extension Manager/Repository config file for ext: "flash_pageheader"
#
# Auto generated 10-07-2007 14:54
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Flash Page Header',
	'description' => 'This plugin lets you choose dynamic Flash headers for your pages. Informations are passed to the animation through an XML file, ready to be processed. It includes a default SWF header, as an example, with the Flash source (.fla).',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@gadlab.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.0.0',
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
	'_md5_values_when_last_written' => 'a:15:{s:21:"ext_conf_template.txt";s:4:"b198";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"2102";s:14:"ext_tables.php";s:4:"4b93";s:14:"ext_tables.sql";s:4:"e7dc";s:28:"ext_typoscript_constants.txt";s:4:"ab5f";s:24:"ext_typoscript_setup.txt";s:4:"cef2";s:16:"locallang_db.php";s:4:"b628";s:14:"xml_output.php";s:4:"b719";s:14:"doc/manual.sxw";s:4:"67c2";s:36:"pi1/class.tx_flashpageheader_pi1.php";s:4:"cabe";s:19:"pi1/default.fla.zip";s:4:"676f";s:15:"pi1/default.png";s:4:"915e";s:15:"pi1/default.swf";s:4:"1321";s:17:"pi1/locallang.php";s:4:"2ad3";}',
);

?>