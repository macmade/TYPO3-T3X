<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_weledasport_pi1.php','_pi1','list_type',0);
	
	// Get extension configuration
	$ws_extConf = unserialize($_EXTCONF);
	
	// Add constants
	t3lib_extMgm::addTypoScriptConstants('extension.weledasport.typeNum = ' . $ws_extConf['typeNum']);
	
	// Save & New button
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_weledasport_sportsmen
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_weledasport_cat
	');
	
	// RTE configuration
	$rteConf = '
		
		# ***************************************************************************************
		# CONFIGURATION of RTE for extension weleda_sport
		# ***************************************************************************************
		RTE.config.database.field {
			
			disableColorPicker = 1
			hideButtons = *
			showButtons = formatblock,bold,italic,underline,unorderedlist,link,findreplace,spellcheck,removeformat,copy,cut,paste,undo,redo,showhelp,about,chMode
			hidePStyleItems = h3, h4, h5, h6, pre, address
		}
	';
	
	// Add RTE configuration
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_weledasport_sportsmen.description',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_weledasport_sportsmen.advice',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_weledasport_sportsmen.product',$rteConf));
?>
