<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	if (TYPO3_MODE=='BE') {
		
		// Backend module
		#t3lib_extMgm::addModule('web','txclassifiedsmacmadeM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
		
		// Include the class for handling categories
		include_once(t3lib_extMgm::extPath('classifieds_macmade') . 'class.tx_classifiedsmacmade_handlecategories.php');
		include_once(t3lib_extMgm::extPath('classifieds_macmade') . 'class.tx_classifiedsmacmade_treeview.php');
	}
	
	/**
	 * Categories TCA
	 */
	$TCA['tx_classifiedsmacmade_categories'] = Array (
		
		// CTRL section
		'ctrl' => Array (
			
			// Record type title
			'title' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_categories',
			
			// Field used as record title
			'label' => 'title',
			
			// Thumbnail
			'thumbnail' => 'icon',
			
			// Field used as modification date
			'tstamp' => 'tstamp',
			
			// Field used as creation date
			'crdate' => 'crdate',
			
			// Field used as BE-user
			'cruser_id' => 'cruser_id',
			
			// Field used as default sort command
			'default_sortby' => 'ORDER BY title',
			
			// Field used as delete flag
			'delete' => 'deleted',
			
			// Use tabs
			"dividers2tabs" => 1,
			
			// Special options
			'enablecolumns' => Array (
				
				// Field used as hidden flag
				'disabled' => 'hidden',
				
				// Field used as start date
				'starttime' => 'starttime',
				
				// Field used as end date
				'endtime' => 'endtime',
			),
			
			// External config file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Records icon file (disabled record suffix = __h)
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_classifiedsmacmade_categories.gif',
		),
		
		// Fields allowed in the FE-interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, title, parent, validation, description, icon',
		)
	);
	
	/**
	 * Ads TCA
	 */
	$TCA['tx_classifiedsmacmade_ads'] = Array (
		
		// CTRL section
		'ctrl' => Array (
			
			// Record type title
			'title' => 'LLL:EXT:classifieds_macmade/locallang_db.php:tx_classifiedsmacmade_ads',
			
			// Field used as record title
			'label' => 'title',
			
			// Field used as modification date
			'tstamp' => 'tstamp',
			
			// Field used as creation date
			'crdate' => 'crdate',
			
			// Field used as BE-user
			'cruser_id' => 'cruser_id',
			
			// Field used as FE-user
			'fe_cruser_id' => 'fe_cruser_id',
			
			// Field used as default sort command
			'default_sortby' => 'ORDER BY crdate',
			
			// Field used as delete flag
			'delete' => 'deleted',
			
			// Use tabs
			"dividers2tabs" => 1,
			
			// Special options
			'enablecolumns' => Array (

				
				// Field used as hidden flag
				'disabled' => 'hidden',
				
				// Field used as start date
				'starttime' => 'starttime',


				
				// Field used as end date
				'endtime' => 'endtime',
			),
			
			// External config file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Records icon file (disabled record suffix = __h)
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_classifiedsmacmade_ads.gif',
		),
		
		// Fields allowed in the FE-interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_cruser_id, adtype, title, subtitle, category, description, price, currency, price_best, price_undefined, pictures, views',
		)
	);
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds.xml');
	
	// Add plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:classifieds_macmade/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Add wizard icons
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_classifiedsmacmade_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_classifiedsmacmade_pi1_wizicon.php';
	}
?>
