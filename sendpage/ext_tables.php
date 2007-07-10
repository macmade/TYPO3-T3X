<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Include the class for handling BE-Users
	include_once(t3lib_extMgm::extPath('sendpage') . 'class.tx_sendpage_handlebeusers.php');
	
	/**
	 * Pages temp TCA.
	 */
	$tempColumns = Array (
		'tx_sendpage_incharge' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:sendpage/locallang_db.php:pages.tx_sendpage_incharge',
			'config' => Array (
				'type' => 'select',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 5,
				'iconsInOptionTags' => 1,
				'renderMode' => 'checkbox',
				'items' => Array(),
				'itemsProcFunc' => 'tx_sendpage_handleBeUsers->main',
			)
		),
	);
	
	// Load pages TCA
	t3lib_div::loadTCA('pages');
	
	// Add field(s)
	t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
	t3lib_extMgm::addToAllTCAtypes('pages','tx_sendpage_incharge;;;;1-1-1');
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	
	// Add plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:sendpage/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Add wizard icon
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_sendpage_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_sendpage_pi1_wizicon.php';
	}
?>
