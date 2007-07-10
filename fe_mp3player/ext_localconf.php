<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_femp3player_pi1.php','_pi1','list_type',0);
	
	// Get extension configuration
	$fe_mp3player_extConf = unserialize($_EXTCONF);
	
	// Add constants
	t3lib_extMgm::addTypoScriptConstants('extension.fe_mp3player.typeNum = ' . $fe_mp3player_extConf['typeNum']);
	
	// Save & New button
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_femp3player_playlists=1
	');
?>
