<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Include helper classes
	if (TYPO3_MODE=='BE') {
		include_once(t3lib_extMgm::extPath('eesp_modules').'class.tx_eespmodules_helpers.php');
	}
	
	/**
	 * Graduate programs TCA
	 */
	$TCA['tx_eespmodules_programs'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:eesp_modules/locallang_db.php:tx_eespmodules_programs',
			
			// Label
			'label' => 'startyear',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL SORTBY instruction
			'default_sortby' => 'ORDER BY startyear',
			
			// Deleted
			'delete' => 'deleted',
			
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
				
				// Start date
				'starttime' => 'starttime',
				
				// End date
				'endtime' => 'endtime',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_eespmodules_programs.gif',
		),
		
		// FE interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, startyear, term1_startweek, term2_startweek, terms',
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
	t3lib_extMgm::addPlugin(Array('LLL:EXT:eesp_modules/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Wizard icons
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_eespmodules_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'pi1/class.tx_eespmodules_pi1_wizicon.php';
	}
?>
