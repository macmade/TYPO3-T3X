<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	if (TYPO3_MODE == 'BE') {
		
		// Add CSM item
		$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
			'name' => 'tx_newrecordwiz_cm1',
			'path' => t3lib_extMgm::extPath($_EXTKEY) . 'class.tx_newrecordwiz_cm1.php'
		);
	}
?>
