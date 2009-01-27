<?php
	if (!defined ("TYPO3_MODE")) {
		die ("Access denied.");
	}
	
	// Add fields options to TCA
	$TCA["tx_ergotheca_tools"] = Array (
		
		// Control section
		"ctrl" => $TCA["tx_ergotheca_tools"]["ctrl"],
		
		"interface" => Array (
			
			// Field list in the BE-interface
			"showRecordFieldList" => "fe_cruser_id,hidden,name,authors,testyear,evalfield,formalization,opencontent,evalobject,practicemodel,targetpublic_age,targetpublic_alt,bibliography,keywords,passation_method,passation_description,passation_procedure,passation_material,passation_setpos,passation_quotint,comments,sources,links,language,language_alt,traduction,traduction_alt,traduction_standard,traduction_standard_alt,eesp,eesp_testyear,usecond,remarks,cost,files,pictures"
		),
		
		// Frontend interface
		"feInterface" => $TCA["tx_ergotheca_tools"]["feInterface"],
		
		// Backend fields configuration
		"columns" => Array (
			"fe_cruser_id" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.fe_cruser_id",
				"config" => Array (
					"type" => "group",
					"internal_type" => "db",
					"allowed" => "fe_users",
					"size" => 1,
					"minitems" => 0,
					"maxitems" => 1,
				)
			),
			"hidden" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
				"config" => Array (
					"type" => "check",
					"default" => "0"
				)
			),
			"name" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.name",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "2",
				)
			),
			"authors" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.authors",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "2",
				)
			),
			"testyear" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.testyear",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "2",
				)
			),
			"evalfield" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.evalfield",
				"config" => Array (
					"type" => "check",
					"cols" => 4,
					"items" => Array (
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.evalfield.I.0", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.evalfield.I.1", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.evalfield.I.2", ""),
					),
				)
			),
			"formalization" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.formalization",
				"config" => Array (
					"type" => "radio",
					"items" => Array (
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.formalization.I.0", "0"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.formalization.I.1", "1"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.formalization.I.2", "2"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.formalization.I.3", "3"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.formalization.I.4", "4"),
					),
				)
			),
			"opencontent" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.opencontent",
				"config" => Array (
					"type" => "check",
					"default" => "0"
				)
			),
			"evalobject" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.evalobject",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"practicemodel" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.practicemodel",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"targetpublic_age" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.targetpublic_age",
				"config" => Array (
					"type" => "check",
					"cols" => 4,
					"items" => Array (
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.targetpublic_age.I.0", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.targetpublic_age.I.1", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.targetpublic_age.I.2", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.targetpublic_age.I.3", ""),
					),
				)
			),
			"targetpublic_alt" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.targetpublic_alt",
				"config" => Array (
					"type" => "input",
					"size" => "25",
					"checkbox" => "",
					"eval" => "trim",
				)
			),
			"bibliography" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.bibliography",
				"config" => Array (
					"type" => "group",
					"internal_type" => "db",
					"allowed" => "tx_endnote_db",
					"size" => 10,
					"minitems" => 0,
					"maxitems" => 50,
				)
			),
			"keywords" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.keywords",
				"config" => Array (
					"type" => "select",
					"foreign_table" => "tx_ergotheca_keywords",
					"foreign_table_where" => "ORDER BY tx_ergotheca_keywords.uid",
					"size" => 10,
					"minitems" => 0,
					"maxitems" => 10,
				)
			),
			"passation_method" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.passation_method",
				"config" => Array (
					"type" => "check",
					"cols" => 4,
					"items" => Array (
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.passation_method.I.0", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.passation_method.I.1", ""),
					),
				)
			),
			"passation_description" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.passation_description",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"passation_procedure" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.passation_procedure",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"passation_material" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.passation_material",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"passation_setpos" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.passation_setpos",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"passation_quotint" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.passation_quotint",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"comments" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.comments",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"sources" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.sources",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"links" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.links",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"language" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.language",
				"config" => Array (
					"type" => "select",
					"items" => Array (
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.language.I.0", "0", t3lib_extMgm::extRelPath("ergotheca")."gfx/selicon_tx_ergotheca_tools_language_0.gif"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.language.I.1", "1", t3lib_extMgm::extRelPath("ergotheca")."gfx/selicon_tx_ergotheca_tools_language_1.gif"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.language.I.2", "2", t3lib_extMgm::extRelPath("ergotheca")."gfx/selicon_tx_ergotheca_tools_language_2.gif"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.language.I.3", "3", t3lib_extMgm::extRelPath("ergotheca")."gfx/selicon_tx_ergotheca_tools_language_3.gif"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.language.I.4", "4", t3lib_extMgm::extRelPath("ergotheca")."gfx/selicon_tx_ergotheca_tools_language_4.gif"),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.language.I.5", "5", t3lib_extMgm::extRelPath("ergotheca")."gfx/selicon_tx_ergotheca_tools_language_5.gif"),
					),
					"size" => 1,
					"maxitems" => 1,
				)
			),
			"language_alt" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.language_alt",
				"config" => Array (
					"type" => "input",
					"size" => "25",
					"checkbox" => "",
					"eval" => "trim",
				)
			),
			"traduction" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction",
				"config" => Array (
					"type" => "check",
					"cols" => 4,
					"items" => Array (
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction.I.0", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction.I.1", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction.I.2", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction.I.3", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction.I.4", ""),
					),
				)
			),
			"traduction_alt" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction_alt",
				"config" => Array (
					"type" => "input",
					"size" => "25",
					"checkbox" => "",
					"eval" => "trim",
				)
			),
			"traduction_standard" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction_standard",
				"config" => Array (
					"type" => "check",
					"cols" => 4,
					"items" => Array (
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction_standard.I.0", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction_standard.I.1", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction_standard.I.2", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction_standard.I.3", ""),
						Array("LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction_standard.I.4", ""),
					),
				)
			),
			"traduction_standard_alt" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.traduction_standard_alt",
				"config" => Array (
					"type" => "input",
					"size" => "25",
					"checkbox" => "",
					"eval" => "trim",
				)
			),
			"eesp" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.eesp",
				"config" => Array (
					"type" => "check",
				)
			),
			"eesp_testyear" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.testyear",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "2",
				)
			),
			"usecond" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.usecond",
				"config" => Array (
					"type" => "text",
					"cols" => "25",
					"rows" => "10",
				)
			),
			"remarks" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.remarks",
				"config" => Array (
					"type" => "text",
					"cols" => "30",
					"rows" => "5",
				)
			),
			"cost" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.cost",
				"config" => Array (
					"type" => "input",
					"size" => "10",
				)
			),
			"files" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.files",
				"config" => Array (
					"type" => "group",
					"internal_type" => "file",
					"allowed" => "pdf,txt,doc,xls,ppt,sxw,sxc,sxi",
					"max_size" => 3000,
					"uploadfolder" => "uploads/tx_ergotheca",
					"size" => 5,
					"minitems" => 0,
					"maxitems" => 5,
				)
			),
			"pictures" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.pictures",
				"config" => Array (
					"type" => "group",
					"internal_type" => "file",
					"allowed" => "gif,png,jpeg,jpg",
					"max_size" => 500,
					"uploadfolder" => "uploads/tx_ergotheca",
					"show_thumbs" => 1,
					"size" => 5,
					"minitems" => 0,
					"maxitems" => 5,
				)
			),
		),
		
		// Types configuration
		"types" => Array (
			"0" => Array("showitem" => "--div--;Application,fe_cruser_id;;1;;1-1-0, hidden, name;;;;--0, authors, testyear, evalfield;;;;--0, formalization, opencontent;;;;--0, evalobject;;;;--0, practicemodel, targetpublic_age;;;;--0, targetpublic_alt, bibliography;;;;--0, keywords,--div--;Passation, passation_method;;;;--0, passation_description, passation_procedure, passation_material, passation_setpos, passation_quotint, comments;;;;--0, sources,--div--;Infos, links, language;;;;--0, language_alt, traduction, traduction_alt, traduction_standard, traduction_standard_alt, eesp;;;;--0, eesp_testyear, usecond;;;;--0, remarks, cost, files;;;;--0, pictures")
		),
		
		// Palettes configuration
		"palettes" => Array (
			"1" => Array("showitem" => "")
		)
	);
	
	// Add fields options to TCA
	$TCA["tx_ergotheca_keywords"] = Array (
		
		// Control section
		"ctrl" => $TCA["tx_ergotheca_keywords"]["ctrl"],
		
		"interface" => Array (
			
			// Field list in the BE-interface
			"showRecordFieldList" => "hidden,keyword"
		),
		
		// Frontend interface
		"feInterface" => $TCA["tx_ergotheca_keywords"]["feInterface"],
		
		// Backend fields configuration
		"columns" => Array (
			"hidden" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
				"config" => Array (
					"type" => "check",
					"default" => "0"
				)
			),
			"keyword" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_keywords.keyword",
				"config" => Array (
					"type" => "input",
					"size" => "25",

					"eval" => "required,trim,unique",
				)
			),
		),
		
		// Types configuration
		"types" => Array (
			"0" => Array("showitem" => "hidden;;1;;1-1-1, keyword")
		),
		
		// Palettes configuration
		"palettes" => Array (
			"1" => Array("showitem" => "")
		)
	);
	
	// Add fields options to TCA
	$TCA["tx_ergotheca_researches"] = Array (
		
		// Control section
		"ctrl" => $TCA["tx_ergotheca_researches"]["ctrl"],
		
		"interface" => Array (
			
			// Field list in the BE-interface
			"showRecordFieldList" => "hidden,title,description,date_start,date_end,tool"
		),
		
		// Frontend interface
		"feInterface" => $TCA["tx_ergotheca_researches"]["feInterface"],
		
		// Backend fields configuration
		"columns" => Array (
			"fe_cruser_id" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_tools.fe_cruser_id",
				"config" => Array (
					"type" => "group",
					"internal_type" => "db",
					"allowed" => "fe_users",
					"size" => 1,
					"minitems" => 0,
					"maxitems" => 1,
				)
			),
			"hidden" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
				"config" => Array (
					"type" => "check",
					"default" => "0"
				)
			),
			"title" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_researches.title",
				"config" => Array (
					"type" => "input",
					"size" => "30",
					"eval" => "required",
				)
			),
			"description" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_researches.description",
				"config" => Array (
					"type" => "text",
					"cols" => "30",
					"rows" => "5",
				)
			),
			"date_start" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_researches.date_start",
				"config" => Array (
					"type" => "input",
					"size" => "8",
					"max" => "20",
					"eval" => "date",
					"checkbox" => "0",
					"default" => "0"
				)
			),
			"date_end" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_researches.date_end",
				"config" => Array (
					"type" => "input",
					"size" => "8",
					"max" => "20",
					"eval" => "date",
					"checkbox" => "0",
					"default" => "0"
				)
			),
			"tool" => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:ergotheca/locallang_db.php:tx_ergotheca_researches.tool",
				"config" => Array (
					"type" => "select",
					"items" => Array (),
					"foreign_table" => "tx_ergotheca_tools",
					"foreign_table_where" => "ORDER BY tx_ergotheca_tools.name",
					"size" => 5,
					"minitems" => 0,
					"maxitems" => 5,
				)
			),
		),
		
		// Types configuration
		"types" => Array (
			"0" => Array("showitem" => "fe_cruser_id;;1;;1-1-0, hidden, title;;;;--0, description;;;;--0, date_start;;;;--0, date_end, tool;;;;--0")
		),
		
		// Palettes configuration
		"palettes" => Array (
			"1" => Array("showitem" => "")
		)
	);
?>
