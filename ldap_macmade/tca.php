<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add fields options to TCA
	$TCA['tx_ldapmacmade_server'] = Array (
		'ctrl' => $TCA['tx_ldapmacmade_server']['ctrl'],
		'interface' => Array (
			
			// Field list in the BE-interface
			'showRecordFieldList' => 'hidden,title,address,port,version,user,password,basedn,filter,fegroup,fegroup_select,fe_username,fe_name,fe_firstname,fe_title,fe_company,fe_address,fe_zipcode,fe_city,fe_country,fe_phone,fe_fax,fe_email,fe_www,fe_departmentnumber'
		),
		'feInterface' => $TCA['tx_ldapmacmade_server']['feInterface'],
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
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.title',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'address' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.address',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,nospace',
				)
			),
			'port' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.port',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'max' => '5',
					'eval' => 'required,int,nospace',
					'default' => '389',
				)
			),
			'version' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.version',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.version.I.0', '0'),
						Array('LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.version.I.1', '1'),
						Array('LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.version.I.2', '2'),
						Array('LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.version.I.3', '3'),
					),
					'size' => 1,
					'maxitems' => 1,
				)
			),
			'tls' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.tls',
				'config' => Array (
					'type' => 'check',
				)
			),
			'user' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.user',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
					'default' => 'cn=admin,dc=localdomain',
				)
			),
			'password' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.password',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'checkbox' => '',
					'eval' => 'nospace,password',
				)
			),
			'basedn' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.basedn',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
					'default' => 'dc=localdomain',
				)
			),
			'filter' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.filter',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
					'default' => '(objectClass=*)',
				)
			),
			'group_class' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.group_class',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,nospace',
					'default' => 'posixGroup',
				)
			),
			'group_member' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.group_member',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required,nospace',
					'default' => 'memberUid',
				)
			),
			'typo3_autoimport' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.typo3_autoimport',
				'config' => Array (
					'type' => 'check',
				)
			),
			'be_enable' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.be_enable',
				'config' => Array (
					'type' => 'check',
				)
			),
			'be_auth' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.be_auth',
				'displayCond' => 'FIELD:be_enable:REQ:true',
				'config' => Array (
					'type' => 'user',
					'userFunc' => 'tx_ldapmacmade_tca->beAuth',
				)
			),
			'be_pwdrule' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.be_pwdrule',
				'displayCond' => 'FIELD:be_enable:REQ:true',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'nospace',
					'default' => 'password_[LDAP:sn]',
				)
			),
			'be_groups_import' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.be_groups_import',
				'displayCond' => 'FIELD:be_enable:REQ:true',
				'config' => Array (
					'type' => 'check',
				)
			),
			'be_groups_fixed' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.be_groups_fixed',
				'displayCond' => 'FIELD:be_enable:REQ:true',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'be_groups',
					'foreign_table_where' => 'ORDER BY be_groups.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 25,
				)
			),
			'be_lang' => Array (
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.be_lang',
				'displayCond' => 'FIELD:be_enable:REQ:true',
				'config' => Array (
					'type' => 'select',
					'items' => Array (
						Array('English',''),
						Array('Arabic','ar'),
						Array('Bahasa Malaysia','my'),
						Array('Basque','eu'),
						Array('Bosnian','ba'),
						Array('Brazilian Portuguese','br'),
						Array('Bulgarian','bg'),
						Array('Catalan','ca'),
						Array('Chinese (Simpl)','ch'),
						Array('Chinese (Trad)','hk'),
						Array('Croatian','hr'),
						Array('Czech','cz'),
						Array('Danish','dk'),
						Array('Dutch','nl'),
						Array('Estonian','et'),
						Array('Esperanto','eo'),
						Array('Finnish','fi'),
						Array('French','fr'),
						Array('German','de'),
						Array('Greek','gr'),
						Array('Greenlandic','gl'),
						Array('Hebrew','he'),
						Array('Hindi','hi'),
						Array('Hungarian','hu'),
						Array('Icelandic','is'),
						Array('Italian','it'),
						Array('Japanese','jp'),
						Array('Korean','kr'),
						Array('Latvian','lv'),
						Array('Lithuanian','lt'),
						Array('Norwegian','no'),
						Array('Polish','pl'),
						Array('Portuguese','pt'),
						Array('Romanian','ro'),
						Array('Russian','ru'),
						Array('Slovak','sk'),
						Array('Slovenian','si'),
						Array('Spanish','es'),
						Array('Swedish','se'),
						Array('Thai','th'),
						Array('Turkish','tr'),
						Array('Ukrainian','ua'),
						Array('Vietnamese','vn'),
					)
				)
			),
			'be_tsconf' => Array (
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.be_tsconf',
				'displayCond' => 'FIELD:be_enable:REQ:true',
				'config' => Array (
					'type' => 'text',
					'cols' => '40',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 4,
						'0' => Array(
							'type' => t3lib_extMgm::isLoaded('tsconfig_help') ? 'popup' : '',
							'title' => 'TSconfig QuickReference',
							'script' => 'wizard_tsconfig.php?mode=beuser',
							'icon' => 'wizard_tsconfig.gif',
							'JSopenParams' => 'height=500,width=780,status=0,menubar=0,scrollbars=1',
						)
					),
					'softref' => 'TSconfig'
				),
				'defaultExtras' => 'fixed-font : enable-tab',
			),
			'fe_enable' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.fe_enable',
				'config' => Array (
					'type' => 'check',
				)
			),
			'fe_auth' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.fe_auth',
				'displayCond' => 'FIELD:fe_enable:REQ:true',
				'config' => Array (
					'type' => 'user',
					'userFunc' => 'tx_ldapmacmade_tca->feAuth',
				)
			),
			'fe_pwdrule' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.fe_pwdrule',
				'displayCond' => 'FIELD:fe_enable:REQ:true',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'nospace',
					'default' => 'password_[LDAP:sn]',
				)
			),
			'fe_groups_import' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.fe_groups_import',
				'displayCond' => 'FIELD:fe_enable:REQ:true',
				'config' => Array (
					'type' => 'check',
				)
			),
			'fe_groups_fixed' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.fe_groups_fixed',
				'displayCond' => 'FIELD:fe_enable:REQ:true',
				'config' => Array (
					'type' => 'select',
					'foreign_table' => 'fe_groups',
					'foreign_table_where' => 'ORDER BY fe_groups.title',
					'size' => 5,
					'minitems' => 0,
					'maxitems' => 25,
				)
			),
			'fe_lock' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.fe_lock',
				'displayCond' => 'FIELD:fe_enable:REQ:true',
				'config' => Array (
					'type' => 'input',
					'size' => '20',
					'eval' => 'trim',
					'max' => '50',
					'checkbox' => '',
					'softref' => 'substitute'
				)
			),
			'fe_tsconf' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.fe_tsconf',
				'displayCond' => 'FIELD:fe_enable:REQ:true',
				'config' => Array (
					'type' => 'text',
					'cols' => '40',
					'rows' => '5',
					'wizards' => Array(
						'_PADDING' => 4,
						'0' => Array(
							'type' => t3lib_extMgm::isLoaded('tsconfig_help') ? 'popup' : '',
							'title' => 'TSconfig QuickReference',
							'script' => 'wizard_tsconfig.php?mode=fe_users',
							'icon' => 'wizard_tsconfig.gif',
							'JSopenParams' => 'height=500,width=780,status=0,menubar=0,scrollbars=1',
						)
					),
					'softref' => 'TSconfig'
				),
				'defaultExtras' => 'fixed-font : enable-tab',
			),
			'mapping_username' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.mapping_username',
				'config' => Array (
					'type' => 'input',
					'size' => '30',
					'eval' => 'required',
				)
			),
			'mapping' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.mapping',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:ldap_macmade/flexform_ds_mapping.xml',
					),
				)
			),
			'mapping_external' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.mapping_external',
				'config' => Array (
					'type' => 'select',
					'items' => Array(
						Array('','')
					),
					'special' => 'tables',
					'suppress_icons' => 1,
					'iconsInOptionTags' => 1,
				)
			),
			'mapping_external_fields' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.mapping_external_fields',
				'displayCond' => 'FIELD:mapping_external:REQ:true',
				'config' => Array (
					'type' => 'flex',
					'ds' => Array(
						'default' => 'FILE:EXT:ldap_macmade/flexform_ds_mapping_external_fields.xml',
					),
				)
			),
			'mapping_external_pid' => Array (
				'exclude' => 1,
				'label' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.mapping_external_pid',
				'displayCond' => 'FIELD:mapping_external:REQ:true',
				'config' => Array (
					'type' => 'group',
					'internal_type' => 'db',
					'allowed' => 'pages',
					'size' => 1,
					'minitems' => 0,
					'maxitems' => 1,
				)
			),
		),
		
		// Types configuration
		'types' => Array (
			'0' => Array('showitem' => '--div--;LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.s_basic, hidden;;;;1-1-0, title;;;;--0, --div--;LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.s_server, address;;;;1-1-0, port, version, tls, user;;;;--0, password, --div--;LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.s_ldap, basedn;;;;1-1-0, filter, group_class;;;;1-1-0, group_member, typo3_autoimport;;;;1-1-0, --div--;LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.s_backend, be_enable;;;;1-1-0, be_auth;;;;--0, be_pwdrule, be_groups_import;;;;--0, be_groups_fixed, be_lang;;;;--0, be_tsconf, --div--;LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.s_frontend, fe_enable;;;;1-1-0, fe_auth;;;;--0, fe_pwdrule, fe_groups_import;;;;1-1-0, fe_groups_fixed, fe_lock;;;;--0, fe_tsconf, --div--;LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server.s_mapping, mapping_username;;;;1-1-0, mapping;;;;--0, mapping_external;;;;--0, mapping_external_fields, mapping_external_pid')
		),
		
		// Palettes configuration
		'palettes' => Array (
			'1' => Array('showitem' => '')
		)
	);
?>
