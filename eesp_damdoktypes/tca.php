<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Document types TCA
	 */
	$TCA['tx_eespdamdoktypes_types'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_eespdamdoktypes_types']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			
			// Available fields
			'showRecordFieldList' => 'type'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_eespdamdoktypes_types']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'type' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:eesp_damdoktypes/locallang_db.xml:tx_eespdamdoktypes_types.type',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'type;;;;1-1-1')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
