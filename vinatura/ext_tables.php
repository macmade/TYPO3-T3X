<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Pictures TCA
	$TCA['tx_vinatura_winetypes'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_winetypes',
			
			// Label field
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// Localization fields
			'languageField' => 'sys_language_uid',
			'transOrigPointerField' => 'l18n_parent',
			'transOrigDiffSourceField' => 'l18n_diffsource',
			
			// External configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_vinatura_winetypes.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			
			// Fields available
			'fe_admin_fieldList' => 'hidden, title, type',
		)
	);
	
	// Articles TCA
	$TCA['tx_vinatura_wines'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_wines',
			
			// Label field
			'label' => 'title',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY title',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// External configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_vinatura_wines.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			
			// Fields available
			'fe_admin_fieldList' => 'hidden, feuser, title, type, description',
		)
	);
	
	// Profiles TCA
	$TCA['tx_vinatura_profiles'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles',
			
			// Label field
			'label' => 'domain',
			
			// Alternatve label
			'label_alt' => 'feuser',
			
			// Force display of alternatve label
			'label_alt_force' => 1,
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY crdate',
			
			// Delete flag
			'delete' => 'deleted',
			
			// Special fields
			'enablecolumns' => Array (
				
				// Hidden flag
				'disabled' => 'hidden',
			),
			
			// External configuration
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_vinatura_profiles.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			
			// Fields available
			'fe_admin_fieldList' => 'hidden, feuser, domain, firstname, cellular, surface, state, description, prices, redwines, whitewines, distribution, events, restaurants, member',
		)
	);
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi3']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi2.xml');
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi3', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi3.xml');
	
	// FE plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:vinatura/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:vinatura/locallang_db.php:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:vinatura/locallang_db.php:tt_content.list_type_pi3', $_EXTKEY.'_pi3'),'list_type');
	
	// Static templates
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts/','Vinatura');
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/css/','Vinatura (CSS Styles)');
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
?>
