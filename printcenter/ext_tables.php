<?php
	
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
	
	// Add plugin
	t3lib_extMgm::addPlugin(array('LLL:EXT:printcenter/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	
	// Add static template
	t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','Print center');
	
	// Backend mode
	if (TYPO3_MODE=='BE') {
		
		// Add wizard icon
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_printcenter_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'pi1/class.tx_printcenter_pi1_wizicon.php';
	}
	
	/**
	 * Service TCA
	 */
	$TCA['tx_printcenter_service'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_service',
			
			// Label
			'label' => 'title',
			
			// Modification time
			'tstamp' => 'tstamp',
			
			// Creation time,
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Sorting
			'sortby' => 'sorting',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
				
				// Start time
				'starttime' => 'starttime',
				
				// End time
				'endtime' => 'endtime',
				
				// Frontend group
				'fe_group' => 'fe_group',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_printcenter_service.gif',
		),
		
		// Frontend section
		'feInterface' => Array (
			
			// Allowed fields
			'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, title, damcat',
		)
	);
	
	/**
	 * Color TCA
	 */
	$TCA['tx_printcenter_color'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_color',
			
			// Label
			'label' => 'name',
			
			// Modification time
			'tstamp' => 'tstamp',
			
			// Creation time,
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Sorting
			'sortby' => 'sorting',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
				
				// Start time
				'starttime' => 'starttime',
				
				// End time
				'endtime' => 'endtime',
				
				// Frontend group
				'fe_group' => 'fe_group',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_printcenter_color.gif',
		),
		
		// Frontend section
		'feInterface' => Array (
			
			// Allowed fields
			'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, namer, preview',
		)
	);
	
	/**
	 * Weight TCA
	 */
	$TCA['tx_printcenter_weight'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_weight',
			
			// Label
			'label' => 'weight',
			
			// Modification time
			'tstamp' => 'tstamp',
			
			// Creation time,
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Default sorting
			'default_sortby' => 'ORDER BY weight',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
				
				// Start time
				'starttime' => 'starttime',
				
				// End time
				'endtime' => 'endtime',
				
				// Frontend group
				'fe_group' => 'fe_group',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_printcenter_weight.gif',
		),
		
		// Frontend section
		'feInterface' => Array (
			
			// Allowed fields
			'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, weight',
		)
	);
	
	/**
	 * Paper TCA
	 */
	$TCA['tx_printcenter_paper'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_paper',
			
			// Label
			'label' => 'name',
			
			// Modification time
			'tstamp' => 'tstamp',
			
			// Creation time,
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Sorting
			'sortby' => 'sorting',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
				
				// Start time
				'starttime' => 'starttime',
				
				// End time
				'endtime' => 'endtime',
				
				// Frontend group
				'fe_group' => 'fe_group',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_printcenter_paper.gif',
		),
		
		// Frontend section
		'feInterface' => Array (
			
			// Allowed fields
			'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, name, color, weight, size',
		)
	);
	
	/**
	 * Size TCA
	 */
	$TCA['tx_printcenter_size'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_size',
			
			// Label
			'label' => 'size',
			
			// Modification time
			'tstamp' => 'tstamp',
			
			// Creation time,
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Default sorting
			'default_sortby' => 'ORDER BY size',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
				
				// Start time
				'starttime' => 'starttime',
				
				// End time
				'endtime' => 'endtime',
				
				// Frontend group
				'fe_group' => 'fe_group',
			),
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_printcenter_size.gif',
		),
		
		// Frontend section
		'feInterface' => Array (
			
			// Allowed fields
			'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, size',
		)
	);
	
	/**
	 * Job TCA
	 */
	$TCA['tx_printcenter_job'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job',
			
			// Label
			'label' => 'uid',
			
			// Modification time
			'tstamp' => 'tstamp',
			
			// Creation time,
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// Default sorting
			'default_sortby' => 'ORDER BY crdate DESC',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// Use tabs
			'dividers2tabs' => true,
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_printcenter_job.gif',
		),
		
		// Frontend section
		'feInterface' => Array (
			
			// Allowed fields
			'fe_admin_fieldList' => 'hidden, service, module, name_last, name_first, office, phone, original_pages, original_type, original_orientation, original_size, print_copies, print_colors, print_twosided, --div--, --div--',
		)
	);
?>
