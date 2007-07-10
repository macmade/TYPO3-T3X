<?php
	
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_printcenter_pi1.php','_pi1','list_type',1);
	
	// Save & new options
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_printcenter_service=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_printcenter_color=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_printcenter_weight=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_printcenter_paper=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_printcenter_size=1
	');
?>
