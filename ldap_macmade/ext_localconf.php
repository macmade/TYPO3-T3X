<?php
	if (!defined ("TYPO3_MODE")) {
		die ("Access denied.");
	}
	
	// Save & New options
	t3lib_extMgm::addUserTSConfig('
		options.saveDocNew.tx_ldapmacmade_server=1
	');
	
	// Extension configuration
	$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ldap_macmade']);
	
	// Storage
	$authTypes = array();
	
	// Check backend authentification
	if ($extConf['be_auth']) {
		
		// BE auth enabled
		$authTypes[] = 'getUserBE,authUserBE';
		
		// Send clear-text passwords
		$TYPO3_CONF_VARS['BE']['loginSecurityLevel'] = 'normal';
	}
	
	// Check frontend authentification
	if ($extConf['fe_auth']) {
		
		// FE auth enabled
		$authTypes[] = 'getUserFE,authUserFE';
	}
	
	// Authentification check
	if ($extConf['be_auth'] || $extConf['fe_auth']) {
		
		// Service sub types
		$subTypes = implode(',',$authTypes);
		
		// Register authentification service
		t3lib_extMgm::addService(
			
			// Extension key
			$_EXTKEY,
			
			// Service type
			'auth',
			
			// Service name
			'tx_ldapmacmade_sv1',
			
			// Service description
			array(
				'title' => 'OpenLDAP authentification',
				'description' => 'This service allows backend users to be authenticated through an OpenLDAP server.',
				'subtype' => $subTypes,
				'available' => 1,
				'priority' => 100,
				'quality' => 50,
				'os' => '',
				'exec' => '',
				'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sv1/class.tx_ldapmacmade_sv1.php',
				'className' => 'tx_ldapmacmade_sv1',
			)
		);
	}
?>
