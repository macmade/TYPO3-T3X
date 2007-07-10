<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Playlists TCA
	 */
	$TCA['tx_flashbook_books'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_flashbook_books']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,title'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_flashbook_books']['feInterface'],
		
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
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:flashbook/locallang_db.php:tx_flashbook_books.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'width' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:flashbook/locallang_db.php:tx_flashbook_books.width',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'eval' => 'required,int',
				)
			),
			'height' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:flashbook/locallang_db.php:tx_flashbook_books.height',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'eval' => 'required,int',
				)
			),
			'hardcover' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:flashbook/locallang_db.php:tx_flashbook_books.hardcover',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'transparency' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:flashbook/locallang_db.php:tx_flashbook_books.transparency',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'pages' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:flashbook/locallang_db.php:tx_flashbook_books.pages',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:flashbook/flexform_ds.xml',
					),
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, width;;;;3-3-3, height, hardcover, transparency, pages;;;;3-3-3'),
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
