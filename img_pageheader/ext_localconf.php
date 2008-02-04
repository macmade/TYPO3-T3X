<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// FE plugins
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_imgpageheader_pi1.php','_pi1','',0);
		
	// Add Flash file & replacement picture to root line
	$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= ',tx_imgpageheader_picture';
?>
