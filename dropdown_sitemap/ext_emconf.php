<?php

########################################################################
# Extension Manager/Repository config file for ext: "dropdown_sitemap"
#
# Auto generated 30-05-2007 14:23
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Drop-Down Site Map',
	'description' => 'The Drop-Down sitemap plugin adds a new kind of menu/sitemap to the Typo3 content elements. It uses HTML list elements, CSS and JavaScript to generate a drop-down map of the website, with the possibility to expand / collapse every section.',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => 'cms,lang,api_macmade',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.3.0',
	'_md5_values_when_last_written' => 'a:17:{s:20:"class.ext_update.php";s:4:"73d3";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"caf8";s:14:"ext_tables.php";s:4:"efdc";s:28:"ext_typoscript_constants.txt";s:4:"1493";s:24:"ext_typoscript_setup.txt";s:4:"499b";s:19:"flexform_ds_pi1.xml";s:4:"7d70";s:13:"locallang.php";s:4:"aa32";s:16:"locallang_db.php";s:4:"211b";s:14:"doc/manual.sxw";s:4:"1b5e";s:14:"pi1/ce_wiz.gif";s:4:"1ea2";s:36:"pi1/class.tx_dropdownsitemap_pi1.php";s:4:"4454";s:44:"pi1/class.tx_dropdownsitemap_pi1_wizicon.php";s:4:"0d4d";s:17:"pi1/locallang.php";s:4:"ddb3";s:13:"pi1/minus.gif";s:4:"833d";s:12:"pi1/plus.gif";s:4:"3c64";s:14:"pi1/spacer.gif";s:4:"3254";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lang' => '',
			'api_macmade' => '',
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
);

?>