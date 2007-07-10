<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_slideshow_pi1.php','_pi1','list_type',0);
	
	// Get extension configuration
	$slideshow_extConf = unserialize($_EXTCONF);
	
	// Add constants
	t3lib_extMgm::addTypoScriptConstants('extension.slideshow.typeNum = ' . $slideshow_extConf['typeNum']);
	
	// Save & New button
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_slideshow_slideshows=1
	');
?>
