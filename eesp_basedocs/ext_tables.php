<?php
	
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	
	// Add FE plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:eesp_basedocs/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Static template
	t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','EESP - Base Documents');
	
	// Wizard icons
	if (TYPO3_MODE == 'BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_eespbasedocs_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_eespbasedocs_pi1_wizicon.php';
	}
	
	/**
	 * Categories TCA
	 */
	$TCA['tx_eespbasedocs_categories'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Table title
			'title' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_categories',
			
			// Label field
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Sorting field
			'sortby' => 'sorting',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_eespbasedocs_categories.gif',
		),
		
		// FE interface
		'feInterface' => Array (
			
			// Available FE fields
			'fe_admin_fieldList' => 'hidden, name',
		)
	);
	
	/**
	 * Instances TCA
	 */
	$TCA['tx_eespbasedocs_instances'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Table title
			'title' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_instances',
			
			// Label field
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Default order
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_eespbasedocs_instances.gif',
		),
		
		// FE interface
		'feInterface' => Array (
			
			// Available FE fields
			'fe_admin_fieldList' => 'hidden, title, operation, members, responsabilities, goals, communications, activities',
		)
	);
	
	/**
	 * Activities TCA
	 */
	$TCA['tx_eespbasedocs_activity'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Table title
			'title' => 'LLL:EXT:eesp_basedocs/locallang_db.php:tx_eespbasedocs_activity',
			
			// Label field
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Modification date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Sorting field
			'sortby' => 'sorting',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Table icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_eespbasedocs_activity.gif',
		),
		
		// FE interface
		'feInterface' => Array (
			
			// Available FE fields
			'fe_admin_fieldList' => 'hidden, title, cat, description, incharge, secretariat, reference, public, goals, domains, collab_internal, collab_external, documents, instances',
		)
	);
?>
