<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_loginboxmacmade_pi1.php','_pi1','list_type',0);
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_loginboxmacmade_pi2.php','_pi2','',0);
?>
