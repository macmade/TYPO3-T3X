<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Address groups TCA
	 */
	$TCA['tx_addressbook_groups'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_groups',
			
			// Label
			'label' => 'title',
			
			// Use tabs as dividers
			'dividers2tabs' => 1,
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Backend user
			'cruser_id' => 'cruser_id',
			
			// MySQL ORDER BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_addressbook_groups.gif',
		),
		
		// FrontEnd interface
		#'feInterface' => Array (
		#	'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, title',
		#)
	);
	
	/**
	 * Addresses TCA
	 */
	$TCA['tx_addressbook_addresses'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses',
			
			// Label
			'label' => 'lastname',
			
			// Alternative labels
			'label_alt' => 'firstname,company',
			
			// Force alternative labels
			'label_alt_force' => 1,
			
			// Use tabs as dividers
			'dividers2tabs' => 1,
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Backend user
			'cruser_id' => 'cruser_id',
			
			// Type field
			'type' => 'type',
			
			// MySQL ORDER BY instruction
			'default_sortby' => 'ORDER BY lastname',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				'disabled' => 'hidden',
				'starttime' => 'starttime',
				'endtime' => 'endtime',
				'fe_group' => 'fe_group',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_addressbook_people.gif',
			
			// Type icon field
			'typeicon_column' => 'type',
			
			// Type icons
			'typeicons' => Array(
				'0' => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_addressbook_people.gif',
				'1' => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_addressbook_company.gif',
			),
		),
		
		// FE interface
		#'feInterface' => Array (
		#	'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, firstname, lastname, nickname, jobtitle, department, company, type, picture, homepage, birthday, groups',
		#)
	);
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
	
	// Add FE plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:addressbook/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Backend features
	if (TYPO3_MODE=='BE') {
		
		// Content wizard
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_addressbook_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_addressbook_pi1_wizicon.php';
		
		// Add CSM items
		$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
			'name' => 'tx_addressbook_cm1',
			'path' => t3lib_extMgm::extPath($_EXTKEY) . 'class.tx_addressbook_cm1.php'
		);
		
		// Add backend module
		t3lib_extMgm::addModule('web','txaddressbookM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
?>
