<?php
	
	// Module path
	define('TYPO3_MOD_PATH', '../typo3conf/ext/dbsync/mod1/');
	
	// Back path to typo3
	$BACK_PATH='../../../../typo3/';
	
	// Module name
	$MCONF['name']='tools_txdbsyncM1';
	
	// Access
	$MCONF['access']='admin';
	
	// Script file
	$MCONF['script']='index.php';
	
	// Module icon
	$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
	
	// Language labels
	$MLANG['default']['ll_ref']='LLL:EXT:dbsync/mod1/locallang_mod.php';
?>
