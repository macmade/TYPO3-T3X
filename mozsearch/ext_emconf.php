<?php

########################################################################
# Extension Manager/Repository config file for ext: "mozsearch"
#
# Auto generated 10-07-2007 14:58
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Mozilla/Firefox Search Plugin Generator',
	'description' => 'This extension provides a dynamic search plugin generator for Mozilla/Firefox browsers. It will allow your visitors to search on your website directly from the toolbar of their browser.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.1.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@gadlab.net',
	'author_company' => 'macmade.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.6.0-0.0.0',
			'php' => '4.1.0-0.0.0',
			'cms' => '',
			'lang' => '',
			'api_macmade' => '0.2.6-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:16:{s:21:"ext_conf_template.txt";s:4:"8ac7";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"04ed";s:14:"ext_tables.php";s:4:"0056";s:28:"ext_typoscript_constants.txt";s:4:"4a5c";s:24:"ext_typoscript_setup.txt";s:4:"add4";s:19:"flexform_ds_pi1.xml";s:4:"f2f4";s:13:"locallang.xml";s:4:"291e";s:16:"locallang_db.xml";s:4:"2203";s:14:"xml_output.php";s:4:"d894";s:14:"doc/manual.sxw";s:4:"6c3c";s:14:"pi1/ce_wiz.gif";s:4:"a7cd";s:30:"pi1/class.tx_mozsearch_pi1.php";s:4:"bc99";s:38:"pi1/class.tx_mozsearch_pi1_wizicon.php";s:4:"05fa";s:13:"pi1/clear.gif";s:4:"cc11";s:12:"res/icon.png";s:4:"2265";}',
);

?>