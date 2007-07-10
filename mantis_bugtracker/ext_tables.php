<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Backend module
	if (TYPO3_MODE=='BE') {
		t3lib_extMgm::addModule('user','txmantisbugtrackerM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
?>
