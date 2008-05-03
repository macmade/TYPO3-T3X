<?php

########################################################################
# Extension Manager/Repository config file for ext: "eesp_ws_modules"
#
# Auto generated 03-05-2008 17:40
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'EESP / Modules Calendar (WS Version)',
	'description' => 'A calendar of the courses modules for the EESP school.',
	'category' => 'plugin',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'shy' => '',
	'dependencies' => 'cms,lang,api_macmade',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 1,
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
			'api_macmade' => '0.3.4',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'tslib_patcher' => '0.1.3-',
		),
	),
	'_md5_values_when_last_written' => 'a:24:{s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"b4db";s:14:"ext_tables.php";s:4:"4f9c";s:19:"flexform_ds_pi1.xml";s:4:"57d2";s:13:"locallang.xml";s:4:"6581";s:16:"locallang_db.xml";s:4:"3359";s:45:"classes/class.tx_eespwsmodules_listgetter.php";s:4:"4559";s:47:"classes/class.tx_eespwsmodules_peoplegetter.php";s:4:"6171";s:47:"classes/class.tx_eespwsmodules_singlegetter.php";s:4:"19dc";s:38:"classes/class.tx_eespwsmodules_tca.php";s:4:"b665";s:14:"pi1/ce_wiz.gif";s:4:"380f";s:34:"pi1/class.tx_eespwsmodules_pi1.php";s:4:"e0cb";s:42:"pi1/class.tx_eespwsmodules_pi1_wizicon.php";s:4:"67e5";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"a211";s:18:"res/classrooms.png";s:4:"e20b";s:20:"res/collaborator.png";s:4:"4fb4";s:18:"res/headers-bg.gif";s:4:"a67e";s:19:"res/information.png";s:4:"3750";s:19:"res/modinfos-bg.gif";s:4:"713a";s:11:"res/pdf.png";s:4:"5ee1";s:15:"res/student.png";s:4:"8d72";s:21:"res/template_pi1.html";s:4:"a05d";s:19:"static/ts/setup.txt";s:4:"da67";}',
	'suggests' => array(
	),
);

?>