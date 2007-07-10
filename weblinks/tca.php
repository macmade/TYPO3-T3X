<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add fields options to TCA
	$TCA['tx_weblinks_categories'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_weblinks_categories']['ctrl'],
		
		'interface' => Array (
			
			// Field list in the BE-interface
			'showRecordFieldList' => 'hidden,title,parent'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_weblinks_categories']['feInterface'],
		
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
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_categories.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
					'eval' => 'required,uniqueInPid',
				)
			),
			'parent' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_categories.parent',
				'config' => Array (
					'type' => 'select',
					'iconsInOptionTags' => 1,
					'items' => Array (
						Array('LLL:EXT:weblinks/locallang_db.php:tx_weblinks_categories.parent.I.0', '0'),
					),
					'itemsProcFunc' => 'tx_weblinks_handleCategories->main',
					'size' => 5,
					'maxitems' => 1,
				)
			),
			'incharge' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_categories.incharge',
				'config' => Array (
					'type' => 'select',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 25,
					'iconsInOptionTags' => 1,
					'renderMode' => 'checkbox',
					'items' => Array(),
					'itemsProcFunc' => 'tx_weblinks_handleBeUsers->main',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;4-1-0, title;;;;1--0, parent;;;;3--0, incharge;;;;5--0')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	// Add fields options to TCA
	$TCA['tx_weblinks_links'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_weblinks_links']['ctrl'],
		
		'interface' => Array (
			
			// Field list in the BE-interface
			'showRecordFieldList' => 'hidden,starttime,endtime,title,category,url,bug,target,description,picture'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_weblinks_links']['feInterface'],
		
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
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
					'eval' => 'required,uniqueInPid',
				)
			),
			'category' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.category',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('DUMMY ITEM','0'),
					),
					'itemsProcFunc' => 'tx_weblinks_handleCategories->main',
					'size' => 5,
					'minitems' => 1,
					'maxitems' => 5,
					'iconsInOptionTags' => 1,
					'renderMode' => 'checkbox',
				)
			),
			'url' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.url',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'wizards' => Array(
						'_PADDING' => 2,
						'link' => Array(
							'type' => 'popup',
							'title' => 'Link',
							'icon' => 'link_popup.gif',
							'script' => 'browse_links.php?mode=wizard',
							'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
						),
					),
					'eval' => 'required,nospace,uniqueInPid',
				)
			),
			'bug' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.bug',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'target' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.target',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.target.I.0', '0'),
						Array('LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.target.I.1', '1'),
						Array('LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.target.I.2', '2'),
						Array('LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.target.I.3', '3'),
					),
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'picture' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.picture',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'gif,png,jpeg,jpg',
					'max_size' => 100,
					'uploadfolder' => 'uploads/tx_weblinks',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.s_link, title;;;;1-1-0, url;;;;3--0, target, category;;;;3--0, --div--;LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.s_infos, description;;;;3-1-0, picture, --div--;LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links.s_special, hidden;;1;;4-1-0, bug;;;;5--0')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime')
		)
	);
?>
