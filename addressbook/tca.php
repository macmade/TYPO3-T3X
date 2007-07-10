<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	/**
	 * Address groups TCA
	 */
	$TCA['tx_addressbook_groups'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_addressbook_groups']['ctrl'],
		
		// BE interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_addressbook_groups']['feInterface'],
		
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
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_groups.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,uniqueInPid',
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_main, title;;;;2-2-2, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_visibility, hidden;;;;5-5-5, starttime, endtime, fe_group')
		),
	);
	
	/**
	 * Addresses TCA
	 */
	$TCA['tx_addressbook_addresses'] = Array (
		
		// Control section
		'ctrl' => $TCA['tx_addressbook_addresses']['ctrl'],
		
		// BE interface
		'interface' => Array (
			'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,firstname,lastname,nickname,jobtitle,department,company,type,picture,homepage,birthday,groups'
		),
		
		// FE interface
		'feInterface' => $TCA['tx_addressbook_addresses']['feInterface'],
		
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
			'firstname' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.firstname',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'lastname' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.lastname',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'nickname' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.nickname',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'jobtitle' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.jobtitle',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'department' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.department',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'company' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.company',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'type' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.type',
				'config' => Array (
					'type' => 'check',
				)
			),
			'picture' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.picture',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'file',
					'allowed' => 'gif,png,jpeg,jpg',
					'max_size' => 500,
					'uploadfolder' => 'uploads/tx_addressbook',
					'show_thumbs' => 1,
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
			'homepage' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.homepage',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
				)
			),
			'birthday' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.birthday',
				'config' => Array (
					'type' => 'input',
					'size' => '8',
					'max' => '20',
					'eval' => 'date',
					'checkbox' => '0',
					'default' => '0'
				)
			),
			'groups' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.groups',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'tx_addressbook_groups',
					'foreign_table_where' => 'AND tx_addressbook_groups.pid=###CURRENT_PID### ORDER BY tx_addressbook_groups.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 50,
					'MM' => 'tx_addressbook_addresses_groups_mm',
					'iconsInOptionTags' => 1,
					'foreign_table_loadIcons' => 1,
					'renderMode' => 'checkbox',
				)
			),
			'notes' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.notes',
				'config' => Array (
					'type' => 'text',
					'cols' => '30',
					'rows' => '5',
				)
			),
			'phone' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.phone',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:addressbook/flexform_ds_phone.xml',
					),
				)
			),
			'email' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.email',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:addressbook/flexform_ds_email.xml',
					),
				)
			),
			'messenger' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.messenger',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:addressbook/flexform_ds_messenger.xml',
					),
				)
			),
			'address' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:addressbook/locallang_db.php:tx_addressbook_addresses.address',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:addressbook/flexform_ds_address.xml',
					),
					
				)
			),
		),
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_main, firstname;;;;1-1-1, lastname, nickname, jobtitle;;;;3-3-3, department, company, type, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_address, address, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_phone, phone, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_email, email, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_messenger, messenger, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_relations, groups;;;;-3-3-3, picture, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_extras, homepage;;;;4-4-4, birthday, notes;;;;4-4-4, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_visibility, hidden;;;;5-5-5, starttime, endtime, fe_group'),
			'1' => Array('showitem' => '--div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_main, company;;;;1-1-1, type, firstname;;;;3-3-3, lastname, nickname, jobtitle;;;;-3-3-3, department, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_address, address, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_phone, phone, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_email, email, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_messenger, messenger, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_relations, groups;;;;-3-3-3, picture, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_extras, homepage;;;;4-4-4, birthday, notes;;;;4-4-4, --div--;LLL:EXT:addressbook/locallang_db.php:tabs.s_visibility, hidden;;;;5-5-5, starttime, endtime, fe_group'),
		),
	);
?>
