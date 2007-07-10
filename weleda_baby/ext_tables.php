<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Pictures TCA
	$TCA['tx_weledababy_pictures'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_pictures',
			
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
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_weledababy_pictures.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			
			// Fields available
			'fe_admin_fieldList' => 'hidden, feuser, title, picture, description',
		)
	);
	
	// Articles TCA
	$TCA['tx_weledababy_articles'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_articles',
			
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
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_weledababy_articles.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			
			// Fields available
			'fe_admin_fieldList' => 'hidden, feuser, title, article',
		)
	);
	
	// Profiles TCA
	$TCA['tx_weledababy_profiles'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_profiles',
			
			// Label field
			'label' => 'feuser',
			
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
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_weledababy_profiles.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			
			// Fields available
			'fe_admin_fieldList' => 'hidden, feuser, birth, description, friends',
		)
	);
	
	// Babies TCA
	$TCA['tx_weledababy_babies'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:weleda_baby/locallang_db.php:tx_weledababy_babies',
			
			// Label field
			'label' => 'name',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY name',
			
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
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_weledababy_babies.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			
			// Fields available
			'fe_admin_fieldList' => 'hidden, feuser, name, sex, birth, picture',
		)
	);
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
	
	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key,pages,recursive';
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi4']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi3']='pi_flexform';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi4']='pi_flexform';
	
	// Add flexform DataStructure
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi2.xml');
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi3', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi3.xml');
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi4', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi4.xml');
	
	// FE plugin
	t3lib_extMgm::addPlugin(Array('LLL:EXT:weleda_baby/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:weleda_baby/locallang_db.php:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:weleda_baby/locallang_db.php:tt_content.list_type_pi3', $_EXTKEY.'_pi3'),'list_type');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:weleda_baby/locallang_db.php:tt_content.list_type_pi4', $_EXTKEY.'_pi4'),'list_type');
	
	// Static templates
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts/','Weleda Baby');
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/css/','Weleda Baby (CSS Styles)');
	
	// Load content TCA
	t3lib_div::loadTCA('tt_content');
?>
