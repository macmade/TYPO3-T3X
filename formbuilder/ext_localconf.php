<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// RTE configuration
	t3lib_extMgm::addPageTSConfig('
		# ***************************************************************************************
		# CONFIGURATION of RTE in table "tx_formbuilder_datastructure", field "description"
		# ***************************************************************************************
		RTE.config.tx_formbuilder_datastructure.description {
			hidePStyleItems = H1, H4, H5, H6
			proc.exitHTMLparser_db=1
			proc.exitHTMLparser_db {
				keepNonMatchedTags=1
				tags.font.allowedAttribs= color
				tags.font.rmTagIfNoAttrib = 1
				tags.font.nesting = global
			}
		}
	');
	
	// Add plugin
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_formbuilder_pi1.php','_pi1','list_type',1);
?>
