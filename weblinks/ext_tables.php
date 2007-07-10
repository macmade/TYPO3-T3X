<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Include the class for handling BE-Users
	include_once(t3lib_extMgm::extPath('weblinks') . 'class.tx_weblinks_handlebeusers.php');
	
	// Include the class for handling categories
	include_once(t3lib_extMgm::extPath('weblinks') . 'class.tx_weblinks_handlecategories.php');
	
	/**
	 * Categories TCA.
	 */
	$TCA['tx_weblinks_categories'] = Array (
		'ctrl' => Array (
			
			// Record type title
			'title' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_categories',
			
			// Field used as record title
			'label' => 'title',
			
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
			
			'enablecolumns' => Array (
				
				// Field used as hidden flag
				'disabled' => 'hidden',
			),
			
			// External config file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Records icon file (disabled record suffix = __h)
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_weblinks_categories.gif',
		),
		
		// Fields allowed in the FE-interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, title, parent, incharge',
		)
	);
	
	/**
	 * Links TCA.
	 */
	$TCA['tx_weblinks_links'] = Array (
		'ctrl' => Array (
			
			// Record type title
			'title' => 'LLL:EXT:weblinks/locallang_db.php:tx_weblinks_links',
			
			// Field used as record title
			'label' => 'title',
			
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
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_weblinks_links.gif',
		),
		
		// Fields allowed in the FE-interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, title, category, url, bug, target, description, picture',
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
	t3lib_extMgm::addPlugin(Array('LLL:EXT:weblinks/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Add static templates
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/css/','CSS Styles');
	
	// Wizard icons
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_weblinks_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_weblinks_pi1_wizicon.php';
	}
?>
