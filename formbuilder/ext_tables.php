<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	if (TYPO3_MODE=='BE') {
		
		// Backend module
		t3lib_extMgm::addModule('web','txformbuilderM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
	
	// Include the class for showing preview pictures in the 'xmlds' field of the 'tx_formbuilder_datastructure' table
	include_once(t3lib_extMgm::extPath('formbuilder') . 'class.tx_formbuilder_previewimages.php');
	
	// Include the class for listing available tables in the 'xmlds' field of the 'tx_formbuilder_datastructure' table
	include_once(t3lib_extMgm::extPath('formbuilder') . 'class.tx_formbuilder_dbrel.php');
	
	// Allow records from the extension table on standard pages
	t3lib_extMgm::allowTableOnStandardPages('tx_formbuilder_datastructure');
	t3lib_extMgm::allowTableOnStandardPages('tx_formbuilder_formdata');
	
	/**
	 * Form datastructure TCA
	 */
	$TCA['tx_formbuilder_datastructure'] = Array (
		
		/**
		 * Control section
		 */
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure',
			
			// Label
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// BE-User
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Deleted
			'delete' => 'deleted',
			
			// Use tabs
			'dividers2tabs' => 1,
			
			// Always show secondary option palette
			'canNotCollapse' => 1,
			
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
				
				// Start date
				'starttime' => 'starttime',
				
				// End date
				'endtime' => 'endtime',
				
				// FE-Group
				'fe_group' => 'fe_group',
			),
			
			// External configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_formbuilder_datastructure.gif',
		),
		
		/**
		 * FE interface
		 */
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, title, description, post_message, be_groups, recipients, datasend, submit, preview, destination, redirect, redirect_time, xmlds',
		)
	);
	
	/**
	 * Form data TCA
	 */
	$TCA['tx_formbuilder_formdata'] = Array (
		
		/**
		 * Control section
		 */
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_formdata',
			
			// Label
			'label' => 'uid',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// BE-User
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY crdate',
			
			// Deleted
			'delete' => 'deleted',
			
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
			),
			
			// External configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_formbuilder_formdata.gif',
		),
		
		/**
		 * FE interface
		 */
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, datastructure, xmldata',
		)
	);
	
	// Adding context sensitive help (CSH)
	t3lib_extMgm::addLLrefForTCAdescr('tx_formbuilder_datastructure','EXT:formbuilder/locallang_csh_datastructure.php');
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	
	// Add plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:formbuilder/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Wizard icons
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_formbuilder_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_formbuilder_pi1_wizicon.php';
	}
?>
