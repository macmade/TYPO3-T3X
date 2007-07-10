<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Save & New options
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_eespdamdoktypes_types=1
	');
	
	// Register custom DAM selector
	tx_dam::register_selection('txeespdamdoktypes','EXT:eesp_damdoktypes/class.tx_eespdamdoktypes_selector.php:&tx_eespdamdoktypes_selector','before:txdamMedia');
?>
