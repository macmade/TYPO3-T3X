<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Pictures TCA
	$TCA['tx_weledababy_pictures'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_weledababy_pictures']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			
			// Available fields
			'showRecordFieldList' => 'hidden,feuser,title,picture,description'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_weledababy_pictures']['feInterface'],
		
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
			'feuser' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_pictures.feuser',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'fe_users',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_pictures.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'picture' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_pictures.picture',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'gif,png,jpeg,jpg',
					'max_size' => 500,
					'uploadfolder' => 'uploads/tx_weledababy',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_pictures.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, feuser, title;;;;2-2-2, picture;;;;3-3-3, description')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	// Articles TCA
	$TCA['tx_weledababy_articles'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_weledababy_articles']['ctrl'],
		
		// Frontend interface
		'interface' => Array (
			
			// Available fields
			'showRecordFieldList' => 'hidden,feuser,title,article'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_weledababy_articles']['feInterface'],
		
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
			'feuser' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_articles.feuser',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'fe_users',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_articles.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'article' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_articles.article',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, feuser, title;;;;2-2-2, article;;;;3-3-3')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	// Profiles TCA
	$TCA['tx_weledababy_profiles'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_weledababy_profiles']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			
			// Available fields
			'showRecordFieldList' => 'hidden,feuser,friends'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_weledababy_profiles']['feInterface'],
		
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
			'feuser' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_profiles.feuser',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'fe_users',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'birth' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_profiles.birth',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'checkbox' => '0',
					'default' => '0'
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_profiles.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'friends' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_profiles.friends',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'fe_users',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 50,
					'MM' => 'tx_weledababy_profiles_friends_mm',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, feuser, birth;;;;1-1-1, description;;;;1-1-1, friends;;;;1-1-1')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	// Babies TCA
	$TCA['tx_weledababy_babies'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_weledababy_babies']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			
			// Available fields
			'showRecordFieldList' => 'hidden,feuser,name,sex,birth,picture'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_weledababy_babies']['feInterface'],
		
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
			'feuser' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_babies.feuser',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'fe_users',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_babies.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'sex' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_babies.sex',
				'config' => Array (
					'type' => 'radio',
					'items' => Array (
						Array('LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_babies.sex.I.0', '0'),
						Array('LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_babies.sex.I.1', '1'),
					),
				)
			),
			'birth' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_babies.birth',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'checkbox' => '0',
					'default' => '0'
				)
			),
			'picture' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_babies.picture',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'gif,png,jpeg,jpg',
					'max_size' => 500,
					'uploadfolder' => 'uploads/tx_weledababy',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, feuser, name, sex, birth, picture')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
