<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Languages TCA
	 */
	$TCA['tx_wessex_languages'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_wessex_languages']['ctrl'],
		
		// BE interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,sys_language_uid,l18n_parent,name,color'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_wessex_languages']['feInterface'],
		
		/**
		 * Fields configuration
		 */
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_languages.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'color' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_languages.color',
				'config' => Array (
					'type' => 'input',
					'size' => '10',
					'wizards' => Array(
						'_PADDING' => 2,
						'color' => Array(
							'title' => 'Color:',
							'type' => 'colorbox',
							'dim' => '12x12',
							'tableStyle' => 'border:solid 1px black;',
							'script' => 'wizard_colorpicker.php',
							'JSopenParams' => 'height=300,width=250,status=0,menubar=0,scrollbars=1',
						),
					),
					'eval' => 'required',
				)
			),
			'sys_language_uid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => Array(
						Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
						Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
					)
				)
			),
			'l18n_parent' => Array (
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
					),
					'foreign_table' => 'tx_wessex_languages',
					'foreign_table_where' => 'AND tx_wessex_languages.pid=###CURRENT_PID### AND tx_wessex_languages.sys_language_uid IN (-1,0)',
				)
			),
			'l18n_diffsource' => Array(
				'config' => array(
					'type'=>'passthrough'
				)
			),
		),
		
		// Types
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, sys_language_uid, l18n_parent, name;;;;--1, color;;;;--1')
		),
		
		// Palettes
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	/**
	 * Countries TCA
	 */
	$TCA['tx_wessex_countries'] = Array (
		
		// Cotrol section
		'ctrl' => $TCA['tx_wessex_countries']['ctrl'],
		
		// BE interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,sys_language_uid,l18n_parent,name,id_language,informations'
		),
		
		// FE interafce
		'feInterface' => $TCA['tx_wessex_countries']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_countries.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'id_language' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_countries.id_language',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_wessex_languages',
					'foreign_table_where' => 'AND tx_wessex_languages.pid=###CURRENT_PID### AND tx_wessex_languages.sys_language_uid IN (-1,0) ORDER BY tx_wessex_languages.name',
					'size' => 1,
					'minitems' => 1,
					'maxitems' => 1,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_wessex_languages',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'list' => Array(
							'type' => 'script',
							'title' => 'List',
							'icon' => 'list.gif',
							'params' => Array(
								'table'=>'tx_wessex_languages',
								'pid' => '###CURRENT_PID###',
							),
							'script' => 'wizard_list.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
			'informations' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_countries.informations',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'sys_language_uid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => Array(
						Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
						Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
					)
				)
			),
			'l18n_parent' => Array (
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
					),
					'foreign_table' => 'tx_wessex_countries',
					'foreign_table_where' => 'AND tx_wessex_countries.pid=###CURRENT_PID### AND tx_wessex_countries.sys_language_uid IN (-1,0)',
				)
			),
			'l18n_diffsource' => Array(
				'config' => array(
					'type'=>'passthrough'
				)
			),
		),
		
		// Types
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:wessex/locallang_db.php:tx_wessex_countries.s_main, hidden;;1;;1-1-1, sys_language_uid, l18n_parent, name;;;;--1, id_language;;;;--1, --div--;LLL:EXT:wessex/locallang_db.php:tx_wessex_countries.s_infos, informations;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_wessex/rte/];--1')
		),
		
		// Palettes
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	/**
	 * Cities TCA
	 */
	$TCA['tx_wessex_cities'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_wessex_cities']['ctrl'],
		
		// BE interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,sys_language_uid,l18n_parent,starttime,endtime,name,id_country,id_coursetypes,description,informations,infobox_1,infobox_2,front_picture, credit, credit_url,map,pictures,info_tables'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_wessex_cities']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'starttime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'default' => '0',
					'checkbox' => '0'
				)
			),
			'endtime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'checkbox' => '0',
					'default' => '0',
					'range' => Array (
						'upper' => mktime(0,0,0,12,31,2020),
						'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
					)
				)
			),
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'id_country' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.id_country',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_wessex_countries',
					'foreign_table_where' => 'AND tx_wessex_countries.pid=###CURRENT_PID### AND tx_wessex_countries.sys_language_uid IN (-1,0) ORDER BY tx_wessex_countries.name',
					'size' => 1,
					'minitems' => 1,
					'maxitems' => 1,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_wessex_countries',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'list' => Array(
							'type' => 'script',
							'title' => 'List',
							'icon' => 'list.gif',
							'params' => Array(
								'table'=>'tx_wessex_countries',
								'pid' => '###CURRENT_PID###',
							),
							'script' => 'wizard_list.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
			'id_coursetypes' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.id_coursetypes',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_wessex_coursetypes',
					'foreign_table_where' => 'AND tx_wessex_coursetypes.pid=###CURRENT_PID### AND tx_wessex_coursetypes.sys_language_uid IN (-1,0) ORDER BY tx_wessex_coursetypes.name',
					'size' => 10,
					'minitems' => 0,
					'maxitems' => 10,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_wessex_coursetypes',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'list' => Array(
							'type' => 'script',
							'title' => 'List',
							'icon' => 'list.gif',
							'params' => Array(
								'table'=>'tx_wessex_coursetypes',
								'pid' => '###CURRENT_PID###',
							),
							'script' => 'wizard_list.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'informations' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.informations',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'infobox_1' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.infobox_1',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'infobox_2' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.infobox_2',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'front_picture' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.front_picture',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
					'max_size' => 100,
					'uploadfolder' => 'uploads/tx_wessex',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),


			'credit' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.credit',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			"credit_url" => Array (
				"exclude" => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.credit_url',
				"config" => Array (
					"type" => "input",
					"size" => "30",
					"checkbox" => "",
					"wizards" => Array(
						"_PADDING" => 2,
						"link" => Array(
							"type" => "popup",
							"title" => "Link",
							"icon" => "link_popup.gif",
							"script" => "browse_links.php?mode=wizard",
							"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
						),
					),
					"eval" => "nospace",
				)
			),
			'map' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.map',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
					'max_size' => 100,
					'uploadfolder' => 'uploads/tx_wessex',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'pictures' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.pictures',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
					'max_size' => 100,
					'uploadfolder' => 'uploads/tx_wessex',
					'show_thumbs' => 1,
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 5,
				)
			),
			'info_tables' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.info_tables',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 2,
						'RTE' => Array(
							'notNewRecords' => 1,
							'RTEonly' => 1,
							'type' => 'script',
							'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
							'icon' => 'wizard_rte2.gif',
							'script' => 'wizard_rte.php',
						),
					),
				)
			),
			'sys_language_uid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',

				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => Array(
						Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
						Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
					)
				)
			),

			'l18n_parent' => Array (
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
					),
					'foreign_table' => 'tx_wessex_cities',
					'foreign_table_where' => 'AND tx_wessex_cities.pid=###CURRENT_PID### AND tx_wessex_cities.sys_language_uid IN (-1,0)',
				)
			),
			'l18n_diffsource' => Array(
				'config' => array(
					'type'=>'passthrough'
				)
			),
		),
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.s_main, hidden;;1;;1-1-1, sys_language_uid, l18n_parent, name;;;;--1, id_country;;;;--1, id_coursetypes, --div--;LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.s_infos, description;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_wessex/rte/];--1, informations;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_wessex/rte/];--1, infobox_1;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_wessex/rte/];--1, infobox_2;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_wessex/rte/];--1, --div--;LLL:EXT:wessex/locallang_db.php:tx_wessex_cities.s_files, front_picture;;;;--1, credit, credit_url, map;;;;--1, pictures, info_tables;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_wessex/rte/];--1')
		),
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime')
		)
	);
	
	/**
	 * Course types TCA
	 */
	$TCA['tx_wessex_coursetypes'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_wessex_coursetypes']['ctrl'],
		
		// BE interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,sys_language_uid,l18n_parent,name,id_categories'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_wessex_coursetypes']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_coursetypes.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'id_categories' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_coursetypes.id_categories',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_wessex_coursecategories',
					'foreign_table_where' => 'AND tx_wessex_coursecategories.pid=###CURRENT_PID### AND tx_wessex_coursecategories.sys_language_uid IN (-1,0) ORDER BY tx_wessex_coursecategories.name',
					'size' => 5,
					'minitems' => 1,
					'maxitems' => 10,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_wessex_coursecategories',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'list' => Array(
							'type' => 'script',
							'title' => 'List',
							'icon' => 'list.gif',
							'params' => Array(
								'table'=>'tx_wessex_coursecategories',
								'pid' => '###CURRENT_PID###',
							),
							'script' => 'wizard_list.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
			'sys_language_uid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => Array(
						Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
						Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
					)
				)
			),
			'l18n_parent' => Array (
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
					),
					'foreign_table' => 'tx_wessex_coursetypes',
					'foreign_table_where' => 'AND tx_wessex_coursetypes.pid=###CURRENT_PID### AND tx_wessex_coursetypes.sys_language_uid IN (-1,0)',
				)
			),
			'l18n_diffsource' => Array(
				'config' => array(
					'type'=>'passthrough'
				)
			),
		),
		
		// Types
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, sys_language_uid, l18n_parent, name;;;;--1, id_categories;;;;--1')
		),
		
		// Palettes
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	/**
	 * Course categories TCA
	 */
	$TCA['tx_wessex_coursecategories'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_wessex_coursecategories']['ctrl'],
		
		// BE interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,sys_language_uid,l18n_parent,name'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_wessex_coursecategories']['feInterface'],
		
		/**
		 * Fields configuration
		 */
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_coursecategories.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'sys_language_uid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
				'config' => Array (

					'type' => 'select',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => Array(
						Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
						Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
					)
				)
			),
			'l18n_parent' => Array (
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
					),
					'foreign_table' => 'tx_wessex_coursecategories',
					'foreign_table_where' => 'AND tx_wessex_coursecategories.pid=###CURRENT_PID### AND tx_wessex_coursecategories.sys_language_uid IN (-1,0)',
				)
			),
			'l18n_diffsource' => Array(
				'config' => array(
					'type'=>'passthrough'
				)
			),
		),
		
		// Types
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, sys_language_uid, l18n_parent, name;;;;--1')
		),
		
		// Palettes
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	?>
