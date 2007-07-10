<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Include the class to handle JavaScript files
	if (TYPO3_MODE=='BE') {
		include_once(t3lib_extMgm::extPath('js_select') . 'class.tx_jsselect_handlejavascripts.php');
	}
	
	// Temp TCA
	$tempColumns = Array (
		'tx_jsselect_javascripts' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:js_select/locallang_db.php:pages.tx_jsselect_javascripts',
			'config' => Array (
				'type' => 'select',
				'items' => Array (),
				'itemsProcFunc' => 'tx_jsselect_handleJavascripts->main',
				'size' => 10,
				'maxitems' => 10,
			)
		),
	);
	
	// Load pages TCA and add fields
	t3lib_div::loadTCA('pages');
	t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('pages','tx_jsselect_javascripts;;;;1-1-1');
?>
