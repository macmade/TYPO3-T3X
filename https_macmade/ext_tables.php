<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Temp TCA
	$tempColumns = Array (
		'tx_httpsmacmade_enforcemode' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.0', '0'),
					Array('LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.1', '1'),
					Array('LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.2', '2'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
	);
	
	// Load pages TCA and add fields
	t3lib_div::loadTCA('pages');
	t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('pages','tx_httpsmacmade_enforcemode;;;;1-1-1');
	
	// Static template
	t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','HTTPS Enforcer / macmade.net');
?>
