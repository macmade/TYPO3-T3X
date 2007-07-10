<?php
	
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add FE plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_eespbasedocs_pi1.php','_pi1','list_type',1);
	
	// Save & New options
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_eespbasedocs_categories = 1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_eespbasedocs_instances = 1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_eespbasedocs_activity = 1
	');
	
	// RTE configuration
	$rteConf = '
		
		# ***************************************************************************************
		# CONFIGURATION of RTE for extension eesp_basedocs
		# ***************************************************************************************
		RTE.config.database.field {
			
			disableColorPicker = 1
			hideButtons = *
			showButtons = bold,italic,underline,orderedlist,unorderedlist,link,findreplace,spellcheck,removeformat,copy,cut,paste,undo,redo,showhelp,about,chMode
			proc.exitHTMLparser_db = 1
			proc.exitHTMLparser_db {
				allowTags = b, strong, i, em, u, ol, li, ul, a
				tags.div.remap = P
			}
		}
	';
	
	// Add RTE configuration
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_instances.operation',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_instances.responsabilities',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_instances.goals',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_activity.description',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_activity.reference',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_activity.public',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_activity.goals',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_activity.domains',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_activity.collab_internal',$rteConf));
	t3lib_extMgm::addPageTSConfig(str_replace('database.field','tx_eespbasedocs_activity.collab_external',$rteConf));
?>
