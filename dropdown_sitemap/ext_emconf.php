<?php

########################################################################
# Extension Manager/Repository config file for ext: "dropdown_sitemap"
#
# Auto generated 29-11-2007 15:02
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Drop-Down Site Map',
	'description' => 'The Drop-Down sitemap plugin adds a new kind of menu/sitemap to the T3 content elements. It uses HTML list elements, CSS and JS (Scriptaculous or Mootools) to generate a drop-down map of the website, with the possibility to expand/collapse every section.',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => 'api_macmade',
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
	'version' => '2.1.1',
	'_md5_values_when_last_written' => 'a:21:{s:20:"class.ext_update.php";s:4:"6ed8";s:32:"class.tx_dropdownsitemap_tca.php";s:4:"420c";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"a2f3";s:14:"ext_tables.php";s:4:"65f4";s:19:"flexform_ds_pi1.xml";s:4:"374c";s:13:"locallang.xml";s:4:"3723";s:16:"locallang_db.xml";s:4:"974e";s:14:"doc/manual.sxw";s:4:"582b";s:14:"pi1/ce_wiz.gif";s:4:"1ea2";s:36:"pi1/class.tx_dropdownsitemap_pi1.php";s:4:"725f";s:44:"pi1/class.tx_dropdownsitemap_pi1_wizicon.php";s:4:"3f5b";s:17:"pi1/locallang.xml";s:4:"9150";s:15:"res/exp-off.gif";s:4:"8587";s:14:"res/exp-on.gif";s:4:"6def";s:18:"res/folder-off.gif";s:4:"8c47";s:17:"res/folder-on.gif";s:4:"4431";s:12:"res/page.gif";s:4:"a659";s:14:"res/spacer.gif";s:4:"2a7d";s:23:"static/ts/constants.txt";s:4:"c858";s:19:"static/ts/setup.txt";s:4:"0c84";}',
	'constraints' => array(
		'depends' => array(
			'api_macmade' => '0.4.3-',
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'tslib_patcher' => '0.1.3-',
		),
	),
	'suggests' => array(
		'tslib_patcher' => '0.1.3-',
	),
);

?>