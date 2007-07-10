<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Include the class to handle JPG files
	if (TYPO3_MODE=='BE') {
		include_once(t3lib_extMgm::extPath('slideshow') . 'class.tx_slideshow_handlejpgfiles.php');
	}
	
	/**
	 * SlideShows TCA
	 */
	$TCA['tx_slideshow_slideshows'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:slideshow/locallang_db.php:tx_slideshow_slideshows',
			
			// Label
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Backend user
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
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
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_slideshow_slideshows.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, title',
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
	t3lib_extMgm::addPlugin(Array('LLL:EXT:slideshow/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Wizard icon
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_slideshow_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_slideshow_pi1_wizicon.php';
	}
?>
