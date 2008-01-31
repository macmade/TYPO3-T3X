<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Sportsmen TCA
	 */
	$TCA['tx_weledasport_sportsmen'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_sportsmen',
			
			// Label
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Backend user
			'cruser_id' => 'cruser_id',
			
			// Sort by field
			'sortby' => 'sorting',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Type field
			'type' => 'type',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_weledasport_sportsmen.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, name',
		)
	);
	
	/**
	 * Categories TCA
	 */
	$TCA['tx_weledasport_cat'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:weleda_sport/locallang_db.php:tx_weledasport_cat',
			
			// Label
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Backend user
			'cruser_id' => 'cruser_id',
			
			// Sort by field
			'sortby' => 'sorting',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Type field
			'type' => 'type',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_weledasport_cat.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, name',
		)
	);
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	
	// Add plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:weleda_sport/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Wizard icon
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_weledasport_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_weledasport_pi1_wizicon.php';
	}
?>
