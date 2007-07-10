<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Backend module
	if (TYPO3_MODE=='BE') {
		t3lib_extMgm::addModule('web','txeespbooksM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
	
	/**
	 * Books TCA.
	 */
	$TCA['tx_eespbooks_books'] = Array (
		'ctrl' => Array (
			
			// Record type title
			'title' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_books',
			
			// Field used as record title
			'label' => 'title',
			
			// Field used as modification date
			'tstamp' => 'tstamp',
			
			// Field used as creation date
			'crdate' => 'crdate',
			
			// Field used as BE-user
			'cruser_id' => 'cruser_id',
			
			// Field used as type
			'type' => 'reedition',
			
			// Field used as default sort command
			'default_sortby' => 'ORDER BY bookid',
			
			// Field used as delete flag
			'delete' => 'deleted',
			
			// Use tabs
			'dividers2tabs' => 1,
			
			'enablecolumns' => Array (
				
				// Field used as hidden flag
				'disabled' => 'hidden',
				
				// Field used as start time
				'starttime' => 'starttime',
				
				// Field used as end time
				'endtime' => 'endtime',
			),
			
			// External config file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Records icon file (disabled record suffix = __h)
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_eespbooks_books.gif',
		),
		
		// Fields allowed in the FE-interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, bookid, title',
		)
	);
	
	/**
	 * Monthes TCA.
	 */
	$TCA['tx_eespbooks_monthes'] = Array (
		'ctrl' => Array (
			
			// Record type title
			'title' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_monthes',
			
			// Field used as record title
			'label' => 'stock_month',
			
			// Alternative record title
			'label_alt' => 'stock_year',
			
			// Force alternative record title
			'label_alt_force' => 1,
			
			// Field used as modification date
			'tstamp' => 'tstamp',
			
			// Field used as creation date
			'crdate' => 'crdate',
			
			// Field used as BE-user
			'cruser_id' => 'cruser_id',
			
			// Field used as default sort command
			'default_sortby' => 'ORDER BY crdate',
			
			// Field used as delete flag
			'delete' => 'deleted',
			
			// External config file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Records icon file (disabled record suffix = __h)
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_eespbooks_monthes.gif',
		),
		
		// Fields allowed in the FE-interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'stock_month, stock_year',
		)
	);
	
	/**
	 * Stock TCA.
	 */
	$TCA['tx_eespbooks_stock'] = Array (
		'ctrl' => Array (
			
			// Record type title
			'title' => 'LLL:EXT:eesp_books/locallang_db.php:tx_eespbooks_stock',
			
			// Field used as record title
			'label' => 'uid',
			
			// Field used as modification date
			'tstamp' => 'tstamp',
			
			// Field used as creation date
			'crdate' => 'crdate',
			
			// Field used as BE-user
			'cruser_id' => 'cruser_id',
			
			// Field used as default sort command
			'default_sortby' => 'ORDER BY uid',
			
			// Field used as delete flag
			'delete' => 'deleted',
			
			// Use tabs
			'dividers2tabs' => 1,
			
			// External config file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Records icon file (disabled record suffix = __h)
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_eespbooks_stock.gif',
		),
		
		// Fields allowed in the FE-interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'rel_month, rel_book',
		)
	);
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
	
	// Add plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:eesp_books/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Add wizard icon
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_eespbooks_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_eespbooks_pi1_wizicon.php';
	}
?>
