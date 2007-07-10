<?php
	
	// Module path
	define('TYPO3_MOD_PATH', '../typo3conf/ext/addressbook/mod1/');
	
	// Back path
	$BACK_PATH='../../../../typo3/';
	
	// Module name
	$MCONF['name']='web_txaddressbookM1';
	
	// Access
	$MCONF['access']='user,group';
	
	// Module script
	$MCONF['script']='index.php';
	
	// Module icon
	$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
	
	// Locallang file
	$MLANG['default']['ll_ref']='LLL:EXT:addressbook/mod1/locallang_mod.php';
?>
