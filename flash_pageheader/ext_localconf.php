<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// FE plugins
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_flashpageheader_pi1.php','_pi1','',0);
	
	// Get extension configuration
	$flash_pageheader_extConf = unserialize($_EXTCONF);
	
	// Add constants
	t3lib_extMgm::addTypoScriptConstants('extension.flash_pageheader.typeNum = ' . $flash_pageheader_extConf['typeNum']);
	
	// Add Flash file & replacement picture to root line
	$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= ',tx_flashpageheader_swf,tx_flashpageheader_picture';
?>
