<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Form datastructure TCA
	 */
	$TCA['tx_formbuilder_datastructure'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_formbuilder_datastructure']['ctrl'],
		
		// Interface
		'interface' => Array (
			
			// Fields available
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,description,post_message,be_groups,recipients,datasend,submit,preview,destination,redirect,redirect_time,xmlds',
			
			// Always show field descriptions
			'always_description' => 1,
		),
		
		// Fe interface
		'feInterface' => $TCA['tx_formbuilder_datastructure']['feInterface'],
		
		// Fields
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'starttime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'default' => '0',
					'checkbox' => '0'
				)
			),
			'endtime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'checkbox' => '0',
					'default' => '0',
					'range' => Array (
						'upper' => mktime(0,0,0,12,31,2020),
						'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
					)
				)
			),
			'fe_group' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
						Array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
						Array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
						Array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
					),
					'foreign_table' => 'fe_groups'
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				),
				'defaultExtras' => 'richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_formbuilder/rte/]',
			),
			'post_message' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.post_message',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				),
			),
			'be_groups' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.be_groups',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'be_groups',
					'foreign_table_where' => 'ORDER BY be_groups.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 20,
					'renderMode' => 'singlebox',
					'iconsInOptionTags' => 1,
				)
			),
			'recipients' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.recipients',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'datasend' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.datasend',
				'config' => Array (
					'type' => 'check',
				)
			),
			'submit' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.submit',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'preview' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.preview',
				'config' => Array (
					'type' => 'check',
				)
			),
			'destination' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.destination',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'pages',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'redirect' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.redirect',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'pages',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'redirect_time' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.redirect_time',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'checkbox' => '',
					"range" => Array ("lower"=>0,"upper"=>30),
					'eval' => 'int',
				)
			),
			'xmlds' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.xmlds',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:formbuilder/flexform_ds.xml',
					),
				)
			),
		),
		
		// Types
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.s_main, title;;;;1-1-1, description;;;;3-1-1, post_message;;;;3-1-1, --div--;LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.s_access, be_groups;;;;1-1-1, --div--;LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.s_mailer, recipients;;;;1-1-1, datasend;;;;4-1-1, --div--;LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.s_fields, submit;;;;1-1-1, xmlds;;;;3-1-1, --div--;LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_datastructure.s_options, destination;;;;4-1-1, preview;;;;5-1-1, redirect;;;;4-1-1, redirect_time, hidden;;1;;5-1-1')
		),
		
		// Palettes
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime, fe_group')
		)
	);
	
	/**
	 * Form data TCA
	 */
	$TCA['tx_formbuilder_formdata'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_formbuilder_formdata']['ctrl'],
		
		// Interface
		'interface' => Array (
			
			// Fields available
			'showRecordFieldList' => 'hidden,datastructure,xmldata'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_formbuilder_formdata']['feInterface'],
		
		// Fields
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'datastructure' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_formdata.datastructure',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'pages',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'xmldata' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:formbuilder/locallang_db.php:tx_formbuilder_formdata.xmldata',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
		),
		
		// Types
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;5-1-1, datastructure;;;;1-1-1, xmldata;;;;3-1-1')
		),
		
		// Palettes
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
