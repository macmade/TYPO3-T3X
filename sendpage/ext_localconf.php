<?php
	if (!defined ('TYPO3_MODE'))  {
		die ('Access denied.');
	}
	
	// FE plugins
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_sendpage_pi1.php','_pi1','list_type',0);
	
	// Add person in charge to root line
	$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= ',tx_sendpage_incharge';
?>
