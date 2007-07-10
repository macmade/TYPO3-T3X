<?php

########################################################################
# Extension Manager/Repository config file for ext: "formbuilder"
#
# Auto generated 10-07-2007 15:12
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Form Builder',
	'description' => 'A tool designed to help you create any kind of forms in a frontend context, associated with a backend module, for back-office purposes.',
	'category' => 'plugin',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'experimental',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_formbuilder/rte/',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
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
			'api_macmade' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:37:{s:30:"class.tx_formbuilder_dbrel.php";s:4:"1cfe";s:38:"class.tx_formbuilder_previewimages.php";s:4:"8fe7";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"4091";s:14:"ext_tables.php";s:4:"ad0f";s:14:"ext_tables.sql";s:4:"e4f9";s:28:"ext_typoscript_constants.txt";s:4:"b047";s:24:"ext_typoscript_setup.txt";s:4:"a457";s:15:"flexform_ds.xml";s:4:"6a92";s:19:"flexform_ds_pi1.xml";s:4:"f181";s:37:"icon_tx_formbuilder_datastructure.gif";s:4:"157d";s:32:"icon_tx_formbuilder_formdata.gif";s:4:"721f";s:13:"locallang.php";s:4:"3471";s:31:"locallang_csh_datastructure.php";s:4:"5b89";s:16:"locallang_db.php";s:4:"7c1e";s:7:"tca.php";s:4:"0b6e";s:18:"csh/checkboxes.gif";s:4:"3e2b";s:25:"csh/database_relation.gif";s:4:"4ecf";s:13:"csh/files.gif";s:4:"ddfd";s:16:"csh/password.gif";s:4:"6f64";s:21:"csh/radio_buttons.gif";s:4:"d361";s:14:"csh/select.gif";s:4:"8a47";s:18:"csh/text_input.gif";s:4:"ee72";s:16:"csh/textarea.gif";s:4:"61d6";s:14:"doc/manual.sxw";s:4:"41c9";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"fe2b";s:14:"mod1/index.php";s:4:"7766";s:18:"mod1/locallang.php";s:4:"34fc";s:22:"mod1/locallang_mod.php";s:4:"290f";s:19:"mod1/moduleicon.gif";s:4:"15d2";s:15:"mod1/scripts.js";s:4:"f137";s:14:"pi1/ce_wiz.gif";s:4:"59db";s:32:"pi1/class.tx_formbuilder_pi1.php";s:4:"5984";s:40:"pi1/class.tx_formbuilder_pi1_wizicon.php";s:4:"fc6c";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.php";s:4:"96a3";}',
);

?>