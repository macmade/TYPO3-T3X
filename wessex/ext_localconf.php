<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Save & New options
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_wessex_languages=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_wessex_countries=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_wessex_cities=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_wessex_coursetypes=1
	');
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_wessex_coursecategories=1
	');
	
	// RTE configuration
	t3lib_extMgm::addPageTSConfig('
		# ***************************************************************************************
		# CONFIGURATION of RTE in table \'tx_wessex_countries\', field \'informations\'
		# ***************************************************************************************
		RTE.config.tx_wessex_countries.informations {
			hidePStyleItems = H1, H4, H5, H6
			proc.exitHTMLparser_db = 1
			proc.exitHTMLparser_db {
				keepNonMatchedTags = 1
				tags.font.allowedAttribs = color
				tags.font.rmTagIfNoAttrib = 1
				tags.font.nesting = global
			}
		}
	');
	t3lib_extMgm::addPageTSConfig('
		# ***************************************************************************************
		# CONFIGURATION of RTE in table \'tx_wessex_cities\', field \'description\'
		# ***************************************************************************************
		RTE.config.tx_wessex_cities.description {
			hidePStyleItems = H1, H4, H5, H6
			proc.exitHTMLparser_db = 1
			proc.exitHTMLparser_db {
				keepNonMatchedTags = 1
				tags.font.allowedAttribs = color
				tags.font.rmTagIfNoAttrib = 1
				tags.font.nesting = global
			}
		}
	');
	t3lib_extMgm::addPageTSConfig('
		# ***************************************************************************************
		# CONFIGURATION of RTE in table \'tx_wessex_cities\', field \'info_tables\'
		# ***************************************************************************************
		RTE.config.tx_wessex_cities.info_tables {
			hidePStyleItems = H1, H4, H5, H6
			proc.exitHTMLparser_db = 1
			proc.exitHTMLparser_db {
				keepNonMatchedTags = 1
				tags.font.allowedAttribs = color
				tags.font.rmTagIfNoAttrib = 1
				tags.font.nesting = global
			}
		}
	');
	t3lib_extMgm::addPageTSConfig('
		# ***************************************************************************************
		# CONFIGURATION of RTE in table \'tx_wessex_cities\', field \'informations\'
		# ***************************************************************************************
		RTE.config.tx_wessex_cities.informations {
			hidePStyleItems = H1, H4, H5, H6
			proc.exitHTMLparser_db = 1
			proc.exitHTMLparser_db {
				keepNonMatchedTags = 1
				tags.font.allowedAttribs = color
				tags.font.rmTagIfNoAttrib = 1
				tags.font.nesting = global
			}
		}
	');
	t3lib_extMgm::addPageTSConfig('
		# ***************************************************************************************
		# CONFIGURATION of RTE in table \'tx_wessex_cities\', field \'infobox_1\'
		# ***************************************************************************************
		RTE.config.tx_wessex_cities.infobox_1 {
			hidePStyleItems = H1, H4, H5, H6
			proc.exitHTMLparser_db = 1
			proc.exitHTMLparser_db {
				keepNonMatchedTags = 1
				tags.font.allowedAttribs = color
				tags.font.rmTagIfNoAttrib = 1
				tags.font.nesting = global
			}
		}
	');
	t3lib_extMgm::addPageTSConfig('
		# ***************************************************************************************
		# CONFIGURATION of RTE in table \'tx_wessex_cities\', field \'infobox_2\'
		# ***************************************************************************************
		RTE.config.tx_wessex_cities.infobox_2 {
			hidePStyleItems = H1, H4, H5, H6
			proc.exitHTMLparser_db = 1
			proc.exitHTMLparser_db {
				keepNonMatchedTags = 1
				tags.font.allowedAttribs = color
				tags.font.rmTagIfNoAttrib = 1
				tags.font.nesting = global
			}
		}
	');
	
	// FE plugins
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_wessex_pi1.php','_pi1','list_type',1);
	t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_wessex_pi2.php','_pi2','list_type',1);
?>
