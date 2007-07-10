<?php

########################################################################
# Extension Manager/Repository config file for ext: "ldap_macmade"
#
# Auto generated 10-07-2007 14:58
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'OpenLDAP / macmade.net',
	'description' => 'This extension allows you to import and authenticate Typo3 users (frontend & backend) from OpenLDAP servers. You can also synchronize the LDAP users with any other database table. All the mapping process is done visually, through flexforms.',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'alpha',
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
			'sv' => '',
			'api_macmade' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:23:{s:28:"class.tx_ldapmacmade_div.php";s:4:"fb85";s:28:"class.tx_ldapmacmade_tca.php";s:4:"a87c";s:21:"ext_conf_template.txt";s:4:"9784";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"d2db";s:14:"ext_tables.php";s:4:"8721";s:14:"ext_tables.sql";s:4:"e6f5";s:23:"flexform_ds_mapping.xml";s:4:"82f8";s:39:"flexform_ds_mapping_external_fields.xml";s:4:"d3cc";s:30:"icon_tx_ldapmacmade_server.gif";s:4:"a618";s:16:"locallang_db.php";s:4:"306a";s:7:"tca.php";s:4:"4a92";s:14:"doc/manual.sxw";s:4:"7ab0";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"8925";s:14:"mod1/index.php";s:4:"2728";s:18:"mod1/locallang.php";s:4:"3e11";s:22:"mod1/locallang_mod.php";s:4:"c9a3";s:19:"mod1/moduleicon.gif";s:4:"a7d5";s:15:"mod1/scripts.js";s:4:"43ff";s:34:"res/icon_tx_ldapmacmade_server.gif";s:4:"a618";s:37:"res/icon_tx_ldapmacmade_server__h.gif";s:4:"91ef";s:32:"sv1/class.tx_ldapmacmade_sv1.php";s:4:"5d16";}',
);

?>