<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Get extension configuration from localconf.php
	$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['workbook']);
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';
	
	// Add flexform fields to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	
	// Add flexform DataStructures
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	
	// Add plugins
	t3lib_extMgm::addPlugin(Array('LLL:EXT:workbook/locallang_db.php:tt_content.list_type_pi1',$_EXTKEY.'_pi1'),'list_type');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:workbook/locallang_db.php:tt_content.list_type_pi2',$_EXTKEY.'_pi2'),'list_type');
	
	// Wizard icons
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_workbook_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_workbook_pi1_wizicon.php';
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_workbook_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_workbook_pi2_wizicon.php';
	}
	
	/**
	 * Companies TCA
	 */
	$TCA['tx_workbook_companies'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Table title
			'title' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies',
			
			// Record label
			'label' => 'title',
			
			// Thumbnail
			'thumbnail' => ($confArray['showThumbs'] == 1) ? 'logo' : false,
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL default SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Use tabs
			'dividers2tabs' => 1,
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
				
				// Start time
				'starttime' => 'starttime',
				
				// End time
				'endtime' => 'endtime',
			),
			
			// Fields configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_workbook_companies.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, title, description, logo, priority, services, www, contact_person, contact_email, address, zip, city, country, phone, fax',
		)
	);
	
	/**
	 * Services TCA
	 */
	$TCA['tx_workbook_services'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Table title
			'title' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_services',
			
			// Record label
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL default SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
			),
			
			// Fields configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_workbook_services.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, title',
		)
	);
	
	/**
	 * Emphasis TCA
	 */
	$TCA['tx_workbook_emphasis'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Table title
			'title' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_emphasis',
			
			// Record label
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL default SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
			),
			
			// Fields configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_workbook_emphasis.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, title',
		)
	);
	
	/**
	 * Categories TCA
	 */
	$TCA['tx_workbook_categories'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Table title
			'title' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_categories',
			
			// Record label
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL default SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
			),
			
			// Fields configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_workbook_categories.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, title',
		)
	);
	
	/**
	 * References TCA
	 */
	$TCA['tx_workbook_references'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Table title
			'title' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references',
			
			// Record label
			'label' => 'title',
			
			// Thumbnail
			'thumbnail' => ($confArray['showThumbs'] == 1) ? 'screenshots' : false,
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL default SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Use tabs
			'dividers2tabs' => 1,
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
				
				// Start time
				'starttime' => 'starttime',
				
				// End time
				'endtime' => 'endtime',
			),
			
			// Fields configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_workbook_references.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, title, description, priority, launch, www, status, screenshots, casestory, emphasis, categories, manager, developers',
		)
	);
?>
