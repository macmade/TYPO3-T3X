<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add backend module
	if (TYPO3_MODE == 'BE') {
		t3lib_extMgm::addModule('tools','txcontentuneraserM1','',t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	}
?>
