<?php
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2004 Jean-David Gadina (macmade@gadlab.net)
	 * All rights reserved
	 * 
	 * This script is part of the TYPO3 project. The TYPO3 project is 
	 * free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 * 
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html.
	 * 
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/
	
	/**
	 * Service 'OpenLDAP authentification' for the 'ldap_macmade' extension.
	 *
	 * @author		Jean-David Gadina <macmade@gadlab.net>
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *		63:		function authUser(&$user)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
	// OpenLDAP class
	require_once(t3lib_extMgm::extPath('ldap_macmade') . 'class.tx_ldapmacmade_div.php');
	
	class tx_ldapmacmade_sv1 extends tx_sv_authbase {
		
		// Same as class name
		var $prefixId = 'tx_ldapmacmade_sv1';
		
		// Path to this script relative to the extension directory
		var $scriptRelPath = 'sv1/class.tx_ldapmacmade_sv1.php';
		
		// The extension key
		var $extKey = 'ldap_macmade';
		
		/**
		 * User authentification
		 * 
		 * This function authentify a user through the enabled OpenLDAP servers.
		 * 
		 * @param		$user		The user array, passed by reference
		 * @return		The autentification code
		 */
		function authUser(&$user) {
			
			// Auth state
			$auth = 100;
			
			// Check login
			if ($this->login['uname'] && $this->login['uident_text']) {
				
				// Additional MySQL WHERE clause for selecting records
				$addWhere = 'deleted=0 AND be_auth=1';
				
				// Get OpenLDAP servers used for authentification
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_ldapmacmade_server',$addWhere);
				
				// Check MySQL ressource
				if ($res) {
					
					// Try each server
					while($server = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						
						// Configuration array
						$conf = array(
							'host' => $server['address'],
							'port' => $server['port'],
							'version' => $server['version'],
							'baseDN' => $server['basedn'],
							'user' => $server['user'],
							'password' => $server['password'],
						);
						
						// LDAP search filter
						$conf['filter'] = '(' . $server['mapping_username'] . '=' . $this->login['uname'] . ')';
						
						// New LDAP class
						$ldap = t3lib_div::makeInstance('tx_ldapmacmade_div');
						
						// Set LDAP class configuration array
						$ldap->conf = $conf;
						
						// Initialization of the LDAP class
						$ldap->init();
						
						// Results
						$results = array(
							'version' => $ldap->pver,
							'bind' => $ldap->r,
							'search' => $ldap->sr,
							'count' => $ldap->num,
							'entries' => $ldap->info,
						);
						
						// Check results
						if (is_array($results['entries']) && count($results['entries'])) {
							
							// Get user DN
							$dn = $results['entries'][0]['dn'];
							
							// Add user informations to LDAP configuration
							$conf['user'] = $dn;
							$conf['password'] = $this->login['uident_text'];
							
							// New LDAP class
							$connect = t3lib_div::makeInstance('tx_ldapmacmade_div');
							
							// Set LDAP class configuration array
							$connect->conf = $conf;
							
							// Initialization of the LDAP class
							$connect->init(1);
							
							// Results
							$results = array(
								'version' => $connect->pver,
								'bind' => $connect->r,
							);
							
							// Check LDAP bind
							if ($results['bind']) {
								
								// User is authorized
								$auth = 200;
								
								// Exit
								break;
							}
						}
					}
				}
			}
			
			// Return auth state
			return $auth;
		}
	}
	
	// XClass inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/sv1/class.tx_ldapmacmade_sv1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/sv1/class.tx_ldapmacmade_sv1.php']);
	}
?>
