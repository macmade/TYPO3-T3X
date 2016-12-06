<?php

########################################################################
# Extension Manager/Repository config file for ext: "twitter_login"
#
# Auto generated 16-10-2009 17:28
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Twitter Login',
	'description' => 'Allows frontend users to authenticate through the Twitter API.',
	'category' => 'services',
	'author' => 'Jean-David Gadina',
	'author_email' => 'macmade@netinfluence.ch',
	'shy' => 0,
	'dependencies' => 'oop',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
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
			'oop' => '0.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:25:{s:21:"ext_conf_template.txt";s:4:"3991";s:12:"ext_icon.gif";s:4:"8cd0";s:17:"ext_localconf.php";s:4:"4ded";s:14:"ext_tables.php";s:4:"9066";s:14:"ext_tables.sql";s:4:"6540";s:48:"classes/class.tx_twitterlogin_authentication.php";s:4:"ee41";s:45:"classes/class.tx_twitterlogin_pi1_wizicon.php";s:4:"4cde";s:39:"classes/class.tx_twitterlogin_utils.php";s:4:"4917";s:17:"res/img/error.png";s:4:"c847";s:17:"res/img/login.gif";s:4:"002f";s:33:"res/img/tx_twitterlogin_users.gif";s:4:"f67b";s:17:"res/html/pi1.html";s:4:"b9e2";s:29:"tca/tx_twitterlogin_users.php";s:4:"5208";s:17:"lang/flex_pi1.xml";s:4:"b8d2";s:12:"lang/pi1.xml";s:4:"7a18";s:19:"lang/tt_content.xml";s:4:"7852";s:30:"lang/tx_twitterlogin_users.xml";s:4:"1cf9";s:16:"lang/wiz-pi1.xml";s:4:"4f52";s:24:"static/pi1/constants.txt";s:4:"7012";s:20:"static/pi1/setup.txt";s:4:"cb2d";s:24:"static/sv1/constants.txt";s:4:"03b8";s:20:"static/sv1/setup.txt";s:4:"a428";s:33:"pi1/class.tx_twitterlogin_pi1.php";s:4:"7f33";s:12:"flex/pi1.xml";s:4:"ebde";s:33:"sv1/class.tx_twitterlogin_sv1.php";s:4:"87f4";}',
	'suggests' => array(
	),
);

?>