<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	// Save & New options
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_weblinks_categories=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_weblinks_links=1
	');
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_weblinks_pi1.php','_pi1','list_type',1);
?>