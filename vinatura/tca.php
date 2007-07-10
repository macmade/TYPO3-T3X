<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Pictures TCA
	$TCA['tx_vinatura_winetypes'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_vinatura_winetypes']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			
			// Available fields
			'showRecordFieldList' => 'hidden,title'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_vinatura_pictures']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_winetypes.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'type' => Array (
				'exclude' => 1,
				'l10n_mode' => 'exclude',
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_winetypes.type',
				'config' => Array (
					'type' => 'radio',
					'items' => array(
						array('LLL:EXT:vinatura/locallang_db.php:tx_vinatura_winetypes.type.I.0','0'),
						array('LLL:EXT:vinatura/locallang_db.php:tx_vinatura_winetypes.type.I.1','1'),
					),
				)
			),
			'sys_language_uid' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
				'config' => array(
					'type' => 'select',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => array(
						array( 'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1 ),
						array( 'LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0 )
					)
				)
			),
			'l18n_parent' => array(
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
				'config' => array(
					'type' => 'select',
					'items' => array(
						array( '', 0 ),
					),
					'foreign_table' => 'tx_cjf_events',
					'foreign_table_where' => 'AND tx_vinatura_winetypes.pid=###CURRENT_PID### AND tx_vinatura_winetypes.sys_language_uid IN (-1,0)',
				)
			),
			'l18n_diffsource' => array(
				'config' => array(
					'type' => 'passthrough'
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, title;;;;2-2-2, type')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	// Articles TCA
	$TCA['tx_vinatura_wines'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_vinatura_wines']['ctrl'],
		
		// Frontend interface
		'interface' => Array (
			
			// Available fields
			'showRecordFieldList' => 'hidden,feuser,title,description'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_vinatura_articles']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'feuser' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_wines.feuser',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'fe_users',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_wines.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'type' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_wines.type',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_vinatura_winetypes',
					'foreign_table_where' => 'AND tx_vinatura_winetypes.pid=###CURRENT_PID### ORDER BY tx_vinatura_winetypes.title',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_wines.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, feuser, title;;;;2-2-2, type, description;;;;3-3-3')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	// Profiles TCA
	$TCA['tx_vinatura_profiles'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_vinatura_profiles']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			
			// Available fields
			'showRecordFieldList' => 'hidden,feuser,firstname,state,description,prices,redwines,whitewines'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_vinatura_profiles']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'feuser' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.feuser',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'fe_users',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'domain' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.domain',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'firstname' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.firstname',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'cellular' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.cellular',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'surface' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.surface',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'state' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state',
				'config' => Array (
					'type' => 'select',
					'items' => array(
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.AG', 'AG' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.AI', 'AI' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.AR', 'AR' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.BE', 'BE' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.BL', 'BL' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.BS', 'BS' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.FR', 'FR' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.GE', 'GE' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.GL', 'GL' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.GR', 'GR' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.JU', 'JU' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.LU', 'LU' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.NE', 'NE' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.NW', 'NW' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.OW', 'OW' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.SG', 'SG' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.SH', 'SH' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.SO', 'SO' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.SZ', 'SZ' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.TG', 'TG' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.TI', 'TI' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.UR', 'UR' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.VD', 'VD' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.VS', 'VS' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.ZG', 'ZG' ),
						array( 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.state.I.ZH', 'ZH' ),
					),
					'size' => '1',
					'maxitems' => '1',
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'prices' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.prices',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
				)
			),
			'whitewines' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.whitewines',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_vinatura_winetypes',
					'foreign_table_where' => 'AND tx_vinatura_winetypes.pid=###CURRENT_PID### AND tx_vinatura_winetypes.type=0 ORDER BY tx_vinatura_winetypes.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 5,
				)
			),
			'redwines' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.redwines',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_vinatura_winetypes',
					'foreign_table_where' => 'AND tx_vinatura_winetypes.pid=###CURRENT_PID### AND tx_vinatura_winetypes.type=1 ORDER BY tx_vinatura_winetypes.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 5,
				)
			),
			'distribution' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.distribution',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'restaurants' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.restaurants',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'events' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.events',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'member' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:vinatura/locallang_db.php:tx_vinatura_profiles.member',
				'config' => Array (
					'type' => 'input',
					'size' => '10',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;;;1-1-1, feuser, domain;;;;1-1-1, firstname, member, cellular;;;;2-2-2, surface, state, description;;;;3-3-3, prices, whitewines;;;;4-4-4, redwines, distribution;;;;4-4-4, restaurants, events')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
