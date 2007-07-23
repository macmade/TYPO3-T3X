<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Languages TCA
	 */
	$TCA['tx_wessex_languages'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_languages',
			
			// Label
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// SORT BY field
			'sortby' => 'sorting',
			
			// Deleted
			'delete' => 'deleted',
			
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
			),
			
			// Use tabs
			"dividers2tabs" => 1,
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'gfx/icon_tx_wessex_languages.gif',
			
			// Copying
			'hideAtCopy' => 1,
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'copyAfterDuplFields' => 'sys_language_uid',
			
			// Default values
			'useColumnsForDefaultValues' => 'sys_language_uid',
			
			// Localization
			'languageField' => 'sys_language_uid',
			'transOrigPointerField' => 'l18n_parent',
			'transOrigDiffSourceField' => 'l18n_diffsource',
		),
		
		// FE interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, name, color',
		)
	);
	
	/**
	 * Countries TCA
	 */
	$TCA['tx_wessex_countries'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_countries',
			
			// Label
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// SORT BY field
			'sortby' => 'sorting',
			
			// Deleted
			'delete' => 'deleted',
			
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
			),
			
			// Use tabs
			"dividers2tabs" => 1,
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'gfx/icon_tx_wessex_countries.gif',
			
			// Copying
			'hideAtCopy' => 1,
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'copyAfterDuplFields' => 'sys_language_uid',
			
			// Default values
			'useColumnsForDefaultValues' => 'sys_language_uid',
			
			// Localization
			'languageField' => 'sys_language_uid',
			'transOrigPointerField' => 'l18n_parent',
			'transOrigDiffSourceField' => 'l18n_diffsource',
		),
		
		// FE interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, name, id_language, informations',
		)
	);
	
	/**
	 * Cities TCA
	 */
	$TCA['tx_wessex_cities'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_cities',
			
			// label
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// SORT BY field
			'sortby' => 'sorting',
			
			// Thumbnail
			'thumbnail' => 'front_picture',
			
			// Deleted
			'delete' => 'deleted',
			
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
				
				// Start
				'starttime' => 'starttime',
				
				// Stop
				'endtime' => 'endtime',
			),
			
			// Use tabs
			"dividers2tabs" => 1,
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'gfx/icon_tx_wessex_cities.gif',
			
			// Copying
			'hideAtCopy' => 1,
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'copyAfterDuplFields' => 'sys_language_uid',
			
			// Default values
			'useColumnsForDefaultValues' => 'sys_language_uid',
			
			// Localization
			'languageField' => 'sys_language_uid',
			'transOrigPointerField' => 'l18n_parent',
			'transOrigDiffSourceField' => 'l18n_diffsource',
		),
		
		// FE interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, starttime, endtime, name, id_country, id_coursetypes, description, informations, infobox_1, infobox_2, front_picture, credit, credit_url, map, pictures, info_tables',
		)
	);
	
	/**
	 * Course types TCA
	 */
	$TCA['tx_wessex_coursetypes'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_coursetypes',
			
			// Label
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// SORT BY instruction
			'default_sortby' => 'ORDER BY name',
			
			// Deleted
			'delete' => 'deleted',
			
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
			),
			
			// Use tabs
			"dividers2tabs" => 1,
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'gfx/icon_tx_wessex_coursetypes.gif',
			
			// Copying
			'hideAtCopy' => 1,
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'copyAfterDuplFields' => 'sys_language_uid',
			
			// Default values
			'useColumnsForDefaultValues' => 'sys_language_uid',
			
			// Localization
			'languageField' => 'sys_language_uid',
			'transOrigPointerField' => 'l18n_parent',
			'transOrigDiffSourceField' => 'l18n_diffsource',
		),
		
		// FE interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, name, id_categories',
		)
	);
	
	/**
	 * Course categories TCA
	 */
	$TCA['tx_wessex_coursecategories'] = Array (
		
		// COntrol section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:wessex/locallang_db.php:tx_wessex_coursecategories',
			
			// Label
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// SORT BY instruction
			'default_sortby' => 'ORDER BY name',
			
			// Deleted
			'delete' => 'deleted',
			
			'enablecolumns' => Array (
				
				// Hidden
				'disabled' => 'hidden',
			),
			
			// Use tabs
			"dividers2tabs" => 1,
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'gfx/icon_tx_wessex_coursecategories.gif',
			
			// Copying
			'hideAtCopy' => 1,
			'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
			'copyAfterDuplFields' => 'sys_language_uid',
			
			// Default values
			'useColumnsForDefaultValues' => 'sys_language_uid',
			
			// Localization
			'languageField' => 'sys_language_uid',
			'transOrigPointerField' => 'l18n_parent',
			'transOrigDiffSourceField' => 'l18n_diffsource',
		),
		
		// FE interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, name',
		)
	);

	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	
	// Add plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:wessex/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:wessex/locallang_db.php:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');
?>
