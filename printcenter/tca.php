<?php
	
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Service TCA
	$TCA['tx_printcenter_service'] = Array (
		
		// COntrol section
		'ctrl' => $TCA['tx_printcenter_service']['ctrl'],
		
		// Backend section
		'interface' => Array (
			
			// Allowed fields
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,damcat'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_printcenter_service']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'starttime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
					),
					'foreign_table' => 'fe_groups'
				)
			),
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_service.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'damcat' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_service.damcat',
				'config' => Array (
					'type' => 'none',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, title;;;;1-1-1, damcat')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime, fe_group')
		)
	);
	
	// Color TCA
	$TCA['tx_printcenter_color'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_printcenter_color']['ctrl'],
		
		// Backend section
		'interface' => Array (
			
			// Allowed fields
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,namer,preview'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_printcenter_color']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'starttime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
					),
					'foreign_table' => 'fe_groups'
				)
			),
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_color.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'preview' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_color.preview',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'wizards' => Array(
						'_PADDING' => 2,
						'color' => Array(
							'title' => 'Color:',
							'type' => 'colorbox',
							'dim' => '12x12',
							'tableStyle' => 'border:solid 1px black;',
							'script' => 'wizard_colorpicker.php',
							'JSopenParams' => 'height=300,width=250,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, name;;;;1-1-1, preview')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime, fe_group')
		)
	);
	
	// Weight TCA
	$TCA['tx_printcenter_weight'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_printcenter_weight']['ctrl'],
		
		// Backend section
		'interface' => Array (
			
			// Allowed fields
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,weight'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_printcenter_weight']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'starttime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
					),
					'foreign_table' => 'fe_groups'
				)
			),
			'weight' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_weight.weight',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'eval' => 'required,double2,uniqueInPid',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, weight;;;;1-1-1')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime, fe_group')
		)
	);
	
	// Size TCA
	$TCA['tx_printcenter_size'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_printcenter_size']['ctrl'],
		
		// Backend section
		'interface' => Array (
			
			// Allowed fields
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,size'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_printcenter_size']['feInterface'],
		
		// Columns
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'starttime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
					),
					'foreign_table' => 'fe_groups'
				)
			),
			'size' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_size.size',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, size;;;;1-1-1')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime, fe_group')
		)
	);
	
	// Paper TCA
	$TCA['tx_printcenter_paper'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_printcenter_paper']['ctrl'],
		
		// Backend section
		'interface' => Array (
			
			// Allowed fields
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,name,color,weight,size'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_printcenter_paper']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'starttime' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
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
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('', 0),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
						Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
					),
					'foreign_table' => 'fe_groups'
				)
			),
			'name' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_paper.name',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'color' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_paper.color',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_printcenter_color',
					'foreign_table_where' => 'AND tx_printcenter_color.pid=###CURRENT_PID### ORDER BY tx_printcenter_color.uid',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 50,
					'MM' => 'tx_printcenter_paper_color_mm',
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_printcenter_color',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
					),
				)
			),
			'weight' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_paper.weight',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_printcenter_weight',
					'foreign_table_where' => 'AND tx_printcenter_weight.pid=###CURRENT_PID### ORDER BY tx_printcenter_weight.uid',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 50,
					'MM' => 'tx_printcenter_paper_weight_mm',
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_printcenter_weight',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
					),
				)
			),
			'size' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_paper.size',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_printcenter_size',
					'foreign_table_where' => 'AND tx_printcenter_size.pid=###CURRENT_PID### ORDER BY tx_printcenter_size.uid',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 50,
					'MM' => 'tx_printcenter_paper_size_mm',
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_printcenter_size',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
					),
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'hidden;;1;;1-1-1, name;;;;1-1-1, color;;;;1-1-1, weight, size')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime, fe_group')
		)
	);
	
	// Job TCA
	$TCA['tx_printcenter_job'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_printcenter_job']['ctrl'],
		
		// Backend section
		'interface' => Array (
			
			// Allowed fields
			'showRecordFieldList' => 'hidden,service,module,name_last,name_first,office,phone,original_pages,original_type,original_orientation,original_size,original_customsize,print_copies,print_colors,print_twosided'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_printcenter_job']['feInterface'],
		
		// Fields configuration
		'columns' => Array (
			'hidden' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
				'config' => Array (
					'type' => 'check',
					'default' => '0'
				)
			),
			'service' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.service',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_printcenter_service',
					'foreign_table_where' => 'AND tx_printcenter_service.pid=###CURRENT_PID### ORDER BY tx_printcenter_service.uid',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'module' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.module',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'name_last' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.name_last',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'name_first' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.name_first',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'office' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.office',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'eval' => 'required',
				)
			),
			'phone' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.phone',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'original_pages' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.original_pages',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'eval' => 'required,int',
				)
			),
			'original_type' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.original_type',
				'config' => Array (
					'type' => 'radio',
					'items' => Array (
						Array('LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.original_type.I.0', '0'),
						Array('LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.original_type.I.1', '1'),
					),
				)
			),
			'original_orientation' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.original_orientation',
				'config' => Array (
					'type' => 'radio',
					'items' => Array (
						Array('LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.original_orientation.I.0', '0'),
						Array('LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.original_orientation.I.1', '1'),
					),
				)
			),
			'original_size' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.original_size',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
					),
					'foreign_table' => 'tx_printcenter_size',
					'foreign_table_where' => 'AND tx_printcenter_size.pid=###CURRENT_PID### ORDER BY tx_printcenter_size.uid',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'print_copies' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.print_copies',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'eval' => 'required',
				)
			),
			'print_colors' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.print_colors',
				'config' => Array (
					'type' => 'radio',
					'items' => Array (
						Array('LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.print_colors.I.0', '0'),
						Array('LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.print_colors.I.1', '1'),
					),
				)
			),
			'print_twosided' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:printcenter/locallang_db.xml:tx_printcenter_job.print_twosided',
				'config' => Array (
					'type' => 'check',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--, idden;;1;;1-1-1, service, module, name_last, name_first, office, phone, --div--, original_pages, original_type, original_orientation, original_size, --div--, print_copies, print_colors, print_twosided')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
