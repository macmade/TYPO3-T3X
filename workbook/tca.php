	<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Companies TCA
	 */
	$TCA['tx_workbook_companies'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_workbook_companies']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,starttime,endtime,title,description,logo,priority,services,www,contact_person,contact_email,address,zip,city,country,phone,fax'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_workbook_companies']['feInterface'],
		
		/**
		 * Fields configuration
		 */
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
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'logo' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.logo',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'gif,png,jpeg,jpg',
					'max_size' => 200,
					'uploadfolder' => 'uploads/tx_workbook',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'priority' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.priority',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.priority.I.0', '0'),
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.priority.I.1', '1'),
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.priority.I.2', '2'),
					),
					'size' => 1,
					'maxitems' => 1,
					'default' => 1,
				)
			),
			'services' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.services',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_workbook_services',
					'foreign_table_where' => 'AND tx_workbook_services.pid=###CURRENT_PID### ORDER BY tx_workbook_services.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 25,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_workbook_services',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
			'www' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.www',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
					'wizards' => Array(
						'_PADDING' => 2,
						'link' => Array(
							'type' => 'popup',
							'title' => 'Link',
							'icon' => 'link_popup.gif',
							'script' => 'browse_links.php?mode=wizard',
							'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
						),
					),
					'eval' => 'nospace',
				)
			),
			'contact_person' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.contact_person',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'contact_email' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.contact_email',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
					'eval' => 'nospace',
				)
			),
			'address' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.address',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'zip' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.zip',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'checkbox' => '',
				)
			),
			'city' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.city',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'country' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.country',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'static_countries',
					'foreign_table_where' => 'ORDER BY static_countries.cn_short_en',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'phone' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.phone',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'fax' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_companies.fax',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;Main, title;;;;2-2-2, www;;;;1-1-1, contact_person;;;;3-3-3, contact_email, description;;;;3-3-3, --div--;Infos, address;;;;3-3-3, zip, city, country, phone;;;;4-4-4, fax, --div--;Relations, services;;;;-5-5-5, logo;;;;--5, --div--;Extras, priority;;;;4-4-4, hidden;;;;--4')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime')
		)
	);
	
	/**
	 * Services TCA
	 */
	$TCA['tx_workbook_services'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_workbook_services']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,title'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_workbook_services']['feInterface'],
		
		/**
		 * Fields configuration
		 */
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
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_services.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'title;;;;2-2-2, hidden;;1;;4-4-4')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	/**
	 * Emphasis TCA
	 */
	$TCA['tx_workbook_emphasis'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_workbook_emphasis']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,title'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_workbook_emphasis']['feInterface'],
		
		/**
		 * Fields configuration
		 */
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
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_emphasis.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'title;;;;2-2-2, hidden;;1;;4-4-4')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	/**
	 * Categories TCA
	 */
	$TCA['tx_workbook_categories'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_workbook_categories']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,title'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_workbook_categories']['feInterface'],
		
		/**
		 * Fields configuration
		 */
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
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_categories.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => 'title;;;;2-2-2, hidden;;1;;4-4-4')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
	
	/**
	 * References TCA
	 */
	$TCA['tx_workbook_references'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_workbook_references']['ctrl'],
		
		// Backend interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,starttime,endtime,title,description,priority,launch,www,status,screenshots,casestory,emphasis,categories,manager,developers'
		),
		
		// Frontend interface
		'feInterface' => $TCA['tx_workbook_references']['feInterface'],
		
		/**
		 * Fields configuration
		 */
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
			'title' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'description' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.description',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'priority' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.priority',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_references.priority.I.0', '0'),
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_references.priority.I.1', '1'),
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_references.priority.I.2', '2'),
					),
					'size' => 1,
					'maxitems' => 1,
					'default' => 1,
				)
			),
			'launch' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.launch',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '0',
					'eval' => 'date',
				)
			),
			'www' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.www',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'wizards' => Array(
						'_PADDING' => 2,
						'link' => Array(
							'type' => 'popup',
							'title' => 'Link',
							'icon' => 'link_popup.gif',
							'script' => 'browse_links.php?mode=wizard',
							'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
						),
					),
					'eval' => 'required',
				)
			),
			'status' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.status',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_references.status.I.0', '0'),
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_references.status.I.1', '1'),
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_references.status.I.2', '2'),
						Array('LLL:EXT:workbook/locallang_db.php:tx_workbook_references.status.I.3', '3'),
					),
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'screenshots' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.screenshots',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'gif,png,jpeg,jpg',
					'max_size' => 200,
					'uploadfolder' => 'uploads/tx_workbook',
					'show_thumbs' => 1,
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 5,
				)
			),
			'casestory' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.casestory',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'wizards' => Array(
						'_PADDING' => 2,
						'link' => Array(
							'type' => 'popup',
							'title' => 'Link',
							'icon' => 'link_popup.gif',
							'script' => 'browse_links.php?mode=wizard',
							'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
						),
					),
				)
			),
			'emphasis' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.emphasis',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_workbook_emphasis',
					'foreign_table_where' => 'AND tx_workbook_emphasis.pid=###CURRENT_PID### ORDER BY tx_workbook_emphasis.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 25,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_workbook_emphasis',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
			'categories' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.categories',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_workbook_categories',
					'foreign_table_where' => 'AND tx_workbook_categories.pid=###CURRENT_PID### ORDER BY tx_workbook_categories.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 25,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_workbook_categories',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
			'manager' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.manager',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_workbook_companies',
					'foreign_table_where' => 'AND tx_workbook_companies.pid=###CURRENT_PID### ORDER BY tx_workbook_companies.title',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_workbook_companies',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
			'developers' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:workbook/locallang_db.php:tx_workbook_references.developers',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_workbook_companies',
					'foreign_table_where' => 'AND tx_workbook_companies.pid=###CURRENT_PID### ORDER BY tx_workbook_companies.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 25,
					'wizards' => Array(
						'_PADDING' => 2,
						'_VERTICAL' => 1,
						'add' => Array(
							'type' => 'script',
							'title' => 'Create new record',
							'icon' => 'add.gif',
							'params' => Array(
								'table'=>'tx_workbook_companies',
								'pid' => '###CURRENT_PID###',
								'setValue' => 'prepend'
							),
							'script' => 'wizard_add.php',
						),
						'edit' => Array(
							'type' => 'popup',
							'title' => 'Edit',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
						),
					),
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;Main, title;;;;2-2-2, www;;;;1-1-1, description;;;;3-3-3, screenshots;;;;--3, --div--;Infos, status;;;;3-3-3, launch;;;;--3, casestory;;;;--3, --div--;Relations;;;3-3-3, emphasis;;;--3, categories;;;--3, manager;;;--3, developers;;;--3, --div--;Extras, priority;;;;4-4-4, hidden;;;;--4')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => 'starttime, endtime')
		)
	);
?>
