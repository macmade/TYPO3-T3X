<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add fields options to TCA
	$TCA['tx_classifiedsmacmade_categories'] = Array (
		
		// CTRL section
		'ctrl' => $TCA['tx_classifiedsmacmade_categories']['ctrl'],
		
		'interface' => Array (
			// Field list in the BE-interface
			'showRecordFieldList' => 'hidden,starttime,endtime,title,parent,validation,description,icon'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_classifiedsmacmade_categories']['feInterface'],
		
		// Backend fields configuration
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
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			/*'parent' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.parent',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.parent.I.0', '0'),
					),
					'itemsProcFunc' => 'tx_classifiedsmacmade_handleCategories->main',
					'size' => 1,
					'maxitems' => 1,
				)
			),*/
			'parent' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.parent',
				'config' => Array (
					'type' => 'select',
					'form_type' => 'user',
					'userFunc' => 'tx_classifiedsmacmade_treeview->displayCategoryTree',
					'treeView' => 1,
					'foreign_table' => 'tx_classifiedsmacmade_categories',
					'size' => 3,
					'autoSizeMax' => 25,
					'minitems' => 0,
					'maxitems' => 500,
					'MM' => 'tx_classifiedsmacmade_categories_mm',
			)
		),
			'validation' => Array (
				"exclude" => 1,
				"label" => "LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.validation",
				"config" => Array (
					"type" => "check",
					"default" => 1,
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.description',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'icon' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.icon',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'gif,png,jpeg,jpg',
					'max_size' => 100,
					'uploadfolder' => 'uploads/tx_classifiedsmacmade',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.s_main, title;;;;1-1-1, parent, validation, --div--;LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.s_infos , description;;;;1-1-1, icon, --div--;LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories.s_special, hidden;;1;;1-1-1')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime')
		)
	);
	
	// Add fields options to TCA
	$TCA['tx_classifiedsmacmade_ads'] = Array (
		
		// CTRL section
		'ctrl' => $TCA['tx_classifiedsmacmade_ads']['ctrl'],
		
		'interface' => Array (
			
			// Field list in the BE-interface
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_cruser_id,adtype,title,subtitle,category,description,price,currency,price_best,price_undefined,pictures, views'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_classifiedsmacmade_ads']['feInterface'],
		
		// Backend fields configuration
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
			'fe_cruser_id' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.fe_cruser_id',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'fe_users',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'adtype' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.adtype',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.adtype.I.0', '0'),
						Array('LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.adtype.I.1', '0'),
					),
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.title',
				'config' => Array (
					'type' => 'input',

					'size' => '30',
					'eval' => 'required',
				)
			),
			'subtitle' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.subtitle',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'category' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.category',
				'config' => Array (
					'type' => 'select',
					'items' => Array (),
					'itemsProcFunc' => 'tx_classifiedsmacmade_handleCategories->main',
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'price' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.price',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'currency' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.currency',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'static_currencies',
					'foreign_table_where' => 'ORDER BY static_currencies.cu_name_en',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'price_best' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.price_best',
				'config' => Array (
					'type' => 'check',
				)
			),
			'price_undefined' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.price_undefined',
				'config' => Array (
					'type' => 'check',
				)
			),
			'pictures' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.pictures',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
					'max_size' => 500,
					'uploadfolder' => 'uploads/tx_classifiedsmacmade',
					'show_thumbs' => 1,
					'size' => 3,
					'minitems' => 0,
					'maxitems' => 3,
				)
			),
			'views' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.views',
				'config' => Array (
					'type' => 'input',
					'size' => '4',
					'max' => '4',
					'eval' => 'int',
					'checkbox' => '0',
					'range' => Array (
						'upper' => '1000000',
						'lower' => '0'
					),
					'default' => 0
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.s_main, adtype, title, subtitle, category, fe_cruser_id, --div--;LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.s_infos, description, pictures, --div--;LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.s_price, price, currency, price_best, price_undefined, --div--;LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads.s_special, hidden;;1;;1-1-1, views')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime')
		)
	);
?>
