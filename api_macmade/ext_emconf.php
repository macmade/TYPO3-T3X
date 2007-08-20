<?php

########################################################################
# Extension Manager/Repository config file for ext: "api_macmade"
#
# Auto generated 21-08-2007 00:59
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Developer API',
	'description' => 'This extension provides an API to help developing Typo3 extensions. It includes helpful functions, for frontend, backend, databases and miscellaneous development. Please take a look at the manual for a complete description of this API.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '0.3.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:17:{s:9:"ChangeLog";s:4:"1467";s:7:"GPL.txt";s:4:"a073";s:23:"class.tx_apimacmade.php";s:4:"37a0";s:12:"ext_icon.gif";s:4:"c839";s:14:"doc/manual.sxw";s:4:"198f";s:29:"res/js/prototype/prototype.js";s:4:"3766";s:30:"res/js/scriptaculous/CHANGELOG";s:4:"3406";s:32:"res/js/scriptaculous/MIT-LICENSE";s:4:"9f3d";s:27:"res/js/scriptaculous/README";s:4:"5c5d";s:35:"res/js/scriptaculous/src/builder.js";s:4:"f2ab";s:36:"res/js/scriptaculous/src/controls.js";s:4:"6e5f";s:36:"res/js/scriptaculous/src/dragdrop.js";s:4:"7f11";s:35:"res/js/scriptaculous/src/effects.js";s:4:"ab48";s:41:"res/js/scriptaculous/src/scriptaculous.js";s:4:"783b";s:34:"res/js/scriptaculous/src/slider.js";s:4:"8baa";s:33:"res/js/scriptaculous/src/sound.js";s:4:"5a58";s:36:"res/js/scriptaculous/src/unittest.js";s:4:"da7d";}',
	'suggests' => array(
		'tslib_patcher' => '0.1.1-',
	),
);

?>