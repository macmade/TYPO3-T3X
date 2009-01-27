<?php
	if (!defined ("TYPO3_MODE")) {
		die ("Access denied.");
	}
	
	// Backend module
	if (TYPO3_MODE == "BE") {
		t3lib_extMgm::addModule("web","txergothecaM1","",t3lib_extMgm::extPath($_EXTKEY) . "mod1/");
	}
	
	/**
	 * Tools TCA.
	 */
	 
	$TCA["tx_ergotheca_tools"] = Array (
		"ctrl" => Array (
		
			// Record type title
			"title" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools",
			
			// Field used as record title
			"label" => "name",
			
			// Field used as modification date
			"tstamp" => "tstamp",
			
			// Field used as creation date
			"crdate" => "crdate",
			
			// Field used as BE-user
			"cruser_id" => "cruser_id",
			
			// Field used as FE-user
			'fe_cruser_id' => 'fe_cruser_id',
			
			// Field used as default sort command
			"default_sortby" => "ORDER BY name",
			
			// Field used as delete flag
			"delete" => "deleted",
			"enablecolumns" => Array (
				
				// Field used as hidden flag
				"disabled" => "hidden",
			),
			
			// Use tabs
			"dividers2tabs" => 1,
			
			// External config file
			"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY) . "tca.php",
			
			// Records icon file (disabled record suffix = __h)
			"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY) . "gfx/icon_tx_ergotheca_tools.gif",
		),
		
		// Fields allowed in the FE-interface
		"feInterface" => Array (
			"fe_admin_fieldList" => "hidden, name, authors, testyear, evalfield, formalization, opencontent, evalobject, practicemodel, targetpublic_age, targetpublic_alt, bibliography, keywords, passation_method, passation_description, passation_procedure, passation_material, passation_setpos, passation_quotint, comments, sources, links, language, language_alt, traduction, traduction_alt, traduction_standard, traduction_standard_alt, eesp, eesp_testyear, usecond, remarks, cost, pictures, files",
		)
	);
	
	/**
	 * Keywords TCA.
	 */
	
	$TCA["tx_ergotheca_keywords"] = Array (
		"ctrl" => Array (
			
			// Record type title
			"title" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_keywords",
			
			// Field used as record title
			"label" => "keyword",
			
			// Field used as modification date
			"tstamp" => "tstamp",
			
			// Field used as creation date
			"crdate" => "crdate",
			
			// Field used as BE-user
			"cruser_id" => "cruser_id",
			
			// Field used as default sort command
			"default_sortby" => "ORDER BY keyword",
			
			// Field Used As Delete Flag
			"delete" => "deleted",
			"enablecolumns" => Array (
				
				// Field used as hidden flag
				"disabled" => "hidden",
			),
			
			// External config file
			"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY) . "tca.php",
			
			// Records icon file (disabled record suffix = __h)
			"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY) . "gfx/icon_tx_ergotheca_keywords.gif",
		),
		// Fields allowed in the FE-interface
		"feInterface" => Array (
			"fe_admin_fieldList" => "hidden, keyword",
		)
	);
	
	/**
	 * Reaserches TCA.
	 */
	
	$TCA["tx_ergotheca_researches"] = Array (
		"ctrl" => Array (
			
			// Record type title
			"title" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_researches",
			
			// Field used as record title
			"label" => "title",
			
			// Field used as modification date
			"tstamp" => "tstamp",
			
			// Field used as FE-user
			"crdate" => "crdate",
			
			// Field used as BE-user
			"cruser_id" => "cruser_id",
			
			// Field used as FE-user
			"fe_cruser_id" => "fe_cruser_id",
			
			// Field used as default sort command
			"default_sortby" => "ORDER BY title",
			
			// Field Used As Delete Flag
			"delete" => "deleted",
			"enablecolumns" => Array (
				
				// Field used as hidden flag
				"disabled" => "hidden",
			),
			
			// External config file
			"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY) . "tca.php",
			
			// Records icon file (disabled record suffix = __h)
			"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY) . "gfx/icon_tx_ergotheca_researches.gif",
		),
		
		// Fields allowed in the FE-interface
		"feInterface" => Array (
			"fe_admin_fieldList" => "hidden, title, description, date_start, date_end, tool",
		)
	);
	
	// Load tt_content TCA
	t3lib_div::loadTCA("tt_content");
	
	// Content subtypes exclude list
	$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"] = "layout,select_key";
	$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi2"] = "layout,select_key";
	$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi3"] = "layout,select_key";
	
	// Add plugin
	t3lib_extMgm::addPlugin(Array("LLL:EXT:ergotheca/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY . "_pi1"),"list_type");
	t3lib_extMgm::addPlugin(Array("LLL:EXT:ergotheca/locallang_db.php:tt_content.list_type_pi2", $_EXTKEY . "_pi2"),"list_type");
	t3lib_extMgm::addPlugin(Array("LLL:EXT:ergotheca/locallang_db.php:tt_content.list_type_pi3", $_EXTKEY . "_pi3"),"list_type");
	
	// Wizard icons
	if (TYPO3_MODE=="BE") {
		$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_ergotheca_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY) . "pi1/class.tx_ergotheca_pi1_wizicon.php";
		$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_ergotheca_pi2_wizicon"] = t3lib_extMgm::extPath($_EXTKEY) . "pi2/class.tx_ergotheca_pi2_wizicon.php";
		$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_ergotheca_pi3_wizicon"] = t3lib_extMgm::extPath($_EXTKEY) . "pi3/class.tx_ergotheca_pi3_wizicon.php";
	}
?>
