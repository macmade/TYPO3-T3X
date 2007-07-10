<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Additionnal fields for DAM TCA
	 */
	$tempColumns = Array (
		'tx_eespdamdoktypes_doktype' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:eesp_damdoktypes/locallang_db.xml:tx_dam.tx_eespdamdoktypes_doktype',
			'config' => Array (
				'type' => 'select',
				'items' => Array(
					Array('',0),
				),
				'foreign_table' => 'tx_eespdamdoktypes_types',
				'foreign_table_where' => 'AND tx_eespdamdoktypes_types.deleted=0 AND tx_eespdamdoktypes_types.pid=###SITEROOT### ORDER BY tx_eespdamdoktypes_types.uid',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	);
	
	// Load DAM TCA
	t3lib_div::loadTCA('tx_dam');
	
	// Add columns
	t3lib_extMgm::addTCAcolumns('tx_dam',$tempColumns);
	
	// Add fields to TCA types
	t3lib_extMgm::addToAllTCAtypes('tx_dam','--div--;LLL:EXT:eesp_damdoktypes/locallang_db.xml:tx_dam.div_eesp,tx_eespdamdoktypes_doktype;;;;1-1-1');
	
	/**
	 * Document types TCA
	 */
	$TCA['tx_eespdamdoktypes_types'] = Array (
		
		// Control section
		'ctrl' => Array (
			
			// Title
			'title' => 'LLL:EXT:eesp_damdoktypes/locallang_db.xml:tx_eespdamdoktypes_types',
			
			// Label
			'label' => 'type',
			
			// Modification date
			'tstamp' => 'tstamp',
			
			// Creation date
			'crdate' => 'crdate',
			
			// Creation user
			'cruser_id' => 'cruser_id',
			
			// MySQL SORT BY instruction
			'default_sortby' => 'ORDER BY type',
			
			// Only for admins
			'adminOnly' => 1,
			
			// Only on site root
			'rootLevel' => 1,
			
			// Delete flag
			'delete' => 'deleted',
			
			// External configuration file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// Icon file
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_eespdamdoktypes_types.gif',
		),
		
		// Frontend interface
		'feInterface' => Array (
			
			// Fields available
			'fe_admin_fieldList' => 'type',
		)
	);
?>
