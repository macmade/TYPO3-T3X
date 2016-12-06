<?php

########################################################################
# Extension Manager/Repository config file for ext: "netdata"
#
# Auto generated 16-10-2009 14:21
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'netinfluence / Static data',
	'description' => 'General data used by the extensions by netinfluence.',
	'category' => 'misc',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@netinfluence.ch',
	'shy' => 0,
	'dependencies' => 'static_info_tables',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'netinfluence',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'static_info_tables' => '0.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:11:{s:12:"ext_icon.gif";s:4:"8cd0";s:17:"ext_localconf.php";s:4:"0841";s:14:"ext_tables.php";s:4:"970c";s:14:"ext_tables.sql";s:4:"d2d1";s:25:"ext_tables_static+adt.sql";s:4:"cd19";s:29:"res/img/tx_netdata_cities.gif";s:4:"d47c";s:29:"res/img/tx_netdata_states.gif";s:4:"84b5";s:25:"tca/tx_netdata_cities.php";s:4:"2407";s:25:"tca/tx_netdata_states.php";s:4:"bb0c";s:26:"lang/tx_netdata_cities.xml";s:4:"5840";s:26:"lang/tx_netdata_states.xml";s:4:"f2e9";}',
	'suggests' => array(
	),
);

?>