<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_mozsearch_pi1.php','_pi1','list_type',0);
	
	// Get extension configuration
	$mozsearch_extConf = unserialize($_EXTCONF);
	
	// Add constants
	t3lib_extMgm::addTypoScriptConstants('extension.mozsearch.typeNum = ' . $mozsearch_extConf['typeNum']);
?>
