<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Backend module
	if (TYPO3_MODE=='BE') {
		t3lib_extMgm::addModule('web','txldapmacmadeM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
	
	// Include the class for listing available tables fields in the 'mapping_external_fields' field of the 'tx_ldapmacmade_server' table
	include_once(t3lib_extMgm::extPath('ldap_macmade') . 'class.tx_ldapmacmade_tca.php');
	
	/**
	 * Server TCA.
	 */
	$TCA['tx_ldapmacmade_server'] = Array (
		'ctrl' => Array (
			
			// Record type title
			'title' => 'LLL:EXT:ldap_macmade/locallang_db.php:tx_ldapmacmade_server',
			
			// Field used as record title
			'label' => 'title',
			
			// Alternative label
			'label_alt' => 'address',
			
			// Force alternative label
			'label_alt_force' => 1,
			
			// Field used as modification date
			'tstamp' => 'tstamp',
			
			// Field used as creation date
			'crdate' => 'crdate',
			
			// Field used as BE-user
			'cruser_id' => 'cruser_id',
			
			// Field used as default sort command
			'default_sortby' => 'ORDER BY title',
			
			// Use tabs
			'dividers2tabs' => 1,
			
			// Field used as delete flag
			'delete' => 'deleted',
			'enablecolumns' => Array (
				
				// Field used as hidden flag
				'disabled' => 'hidden',
			),
			
			// External config file
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			
			// List of fields that need a reload
			'requestUpdate' => 'be_enable,fe_enable,mapping_external',
			
			// Records icon file
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'res/icon_tx_ldapmacmade_server.gif',
		),
		
		// Fields allowed in the FE-interface
		'feInterface' => Array (
			'fe_admin_fieldList' => 'hidden, title, address, port, version, user, password, basedn, filter',
		)
	);
	
	// Adding context sensitive help (CSH)
	t3lib_extMgm::addLLrefForTCAdescr('tx_ldapmacmade_server','EXT:ldap_macmade/locallang_csh_server.php');
?>
