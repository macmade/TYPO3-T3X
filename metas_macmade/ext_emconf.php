<?php

########################################################################
# Extension Manager/Repository config file for ext: "metas_macmade"
#
# Auto generated 24-09-2007 19:29
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Meta tags / macmade.net',
	'description' => 'Adds the possibility to define meta tags for each TYPO3 page.',
	'category' => 'fe',
	'author' => 'Jean-David Gadina',
	'author_email' => 'info@macmade.net',
	'shy' => 1,
	'dependencies' => 'pagegen_macmade',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'experimental',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'macmade.net',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'php' => '4.1.2-0.0.0',
			'typo3' => '3.8.0-0.0.0',
			'pagegen_macmade' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:8:{s:35:"class.tx_metasmacmade_generator.php";s:4:"ce30";s:21:"ext_conf_template.txt";s:4:"7e0a";s:12:"ext_icon.gif";s:4:"c839";s:17:"ext_localconf.php";s:4:"f45a";s:14:"ext_tables.php";s:4:"b4c8";s:14:"ext_tables.sql";s:4:"1ee1";s:15:"flexform_ds.xml";s:4:"119a";s:16:"locallang_db.xml";s:4:"ee82";}',
	'suggests' => array(
	),
);

?>