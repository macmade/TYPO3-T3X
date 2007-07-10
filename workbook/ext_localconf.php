<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add plugins
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_workbook_pi1.php','_pi1','list_type',1);
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_workbook_pi2.php','_pi2','list_type',1);
	
	// Save & New options
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_workbook_companies=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_workbook_services=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_workbook_emphasis=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_workbook_categories=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_workbook_references=1
	');
?>