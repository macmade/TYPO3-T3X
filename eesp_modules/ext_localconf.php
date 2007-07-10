<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Save & New options
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_eespmodules_programs=1
	');
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_eespmodules_pi1.php','_pi1','list_type',1);
	
	// Check TYPO3 mode
	if (TYPO3_MODE == 'BE') {
		
		// Extends TCEForms
		#$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_tceforms.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'class.ux_t3lib_tceforms.php';
	}
?>
