<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Sportsmen TCA
	 */
	$TCA['tx_weledasport_sportsmen'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_weledasport_sportsmen']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,name'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_weledasport_sportsmen']['feInterface'],
		
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
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'cat' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.cat',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_weledasport_cat',
					'foreign_table_where' => 'AND tx_weledasport_cat.pid=###CURRENT_PID### ORDER BY tx_weledasport_cat.sorting',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'link' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.link',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'nospace',
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.description',
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
					)
				)
			),
			'palmares' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.palmares',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:weleda_sport/flexform_ds.xml',
					),
				)
			),
			'advice' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.advice',
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
					)
				)
			),
			'product' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.product',
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
					)
				)
			),
			'picture' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.picture',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'jpeg,jpg',
					'max_size' => 500,
					'uploadfolder' => 'uploads/tx_weledasport',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'thumbnail' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen.thumbnail',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'jpeg,jpg',
					'max_size' => 100,
					'uploadfolder' => 'uploads/tx_weledasport',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, name;;;;2-2-2, cat;;;;3-3-3, link;;;;3-3-3, description;;;richtext[cut|copy|paste|bold|italic|underline|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_weledasport/rte/];3-3-3, palmares;;;;3-3-3, advice;;;richtext[cut|copy|paste|bold|italic|underline|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_weledasport/rte/];3-3-3, product;;;richtext[cut|copy|paste|bold|italic|underline|unorderedlist|link]:rte_transform[mode=ts_css|imgpath=uploads/tx_weledasport/rte/];3-3-3, picture;;;;3-3-3, thumbnail;;;;3-3-3'),
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	/**
	 * Categories TCA
	 */
	$TCA['tx_weledasport_cat'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_weledasport_cat']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,name'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_weledasport_cat']['feInterface'],
		
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
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_cat.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'season' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_cat.season',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_cat.season.I.0', '0'),
						Array('LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_cat.season.I.1', '1'),
					),
					'size' => 1,
					'maxitems' => 1,
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, name;;;;2-2-2, season;;;;3-3-3'),
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
