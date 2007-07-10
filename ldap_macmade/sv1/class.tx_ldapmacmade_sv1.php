<?php
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2004 Jean-David Gadina (info@macmade.net)
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
	 * @author		Jean-David Gadina <info@macmade.net>
	 * @version		2.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *		67:		function getUser
	 *		193:	function authUser(&$user)
	 * 
	 *				TOTAL FUNCTIONS: 2
	 */
	
	// OpenLDAP classes
	require_once(t3lib_extMgm::extPath('ldap_macmade') . 'class.tx_ldapmacmade_div.php');
	require_once(t3lib_extMgm::extPath('ldap_macmade') . 'class.tx_ldapmacmade_utils.php');
	
	// Backend class
	require_once (PATH_t3lib . 'class.t3lib_befunc.php');
	
	class tx_ldapmacmade_sv1 extends tx_sv_authbase {
		
		// Same as class name
		var $prefixId = 'tx_ldapmacmade_sv1';
		
		// Path to this script relative to the extension directory
		var $scriptRelPath = 'sv1/class.tx_ldapmacmade_sv1.php';
		
		// The extension key
		var $extKey = 'ldap_macmade';
		
		/**
		 * Get a LDAP user
		 * 
		 * This function gets a user through the enabled OpenLDAP servers.
		 * 
		 * @return		The Typo3 user row
		 */
		function getUser() {
			
			// Auth state
			$auth = 0;
			
			// User array
			$user = array('authenticated'=>false);
			
			// Extension configuration
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ldap_macmade']);
			
			// Check for a session
			if (!array_key_exists('userSession',$this->info) || !is_array($this->info['userSession']) || !array_key_exists('uid',$this->info['userSession'])) {
				
				// Check for a login
				if (!empty($this->login['uname'])) {
					
					// WHERE clause to select existing user
					$whereClause = $this->db_user['username_column'] . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($this->login['uname'],$this->db_user['table']);
					
					// Try to get an existing user
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($this->db_user['username_column'],$this->db_user['table'],$whereClause . ' ' . $this->db_user['enable_clause']);
					
					// Check for an existing user
					if (!$res || !is_array($GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
						
						// Login type
						$loginType = $this->authInfo['loginType'];
						
						// Additional MySQL WHERE clause for selecting records
						$addWhere = ($loginType == 'BE') ? 'typo3_autoimport=1 AND be_enable=1 AND be_auth=1 AND deleted=0 AND hidden=0' : 'typo3_autoimport=1 AND fe_enable=1 AND fe_auth=1 AND deleted=0 AND hidden=0';
						
						// Get OpenLDAP servers used for authentification
						$ldap_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_ldapmacmade_server',$addWhere);
						
						// Check MySQL ressource
						if ($ldap_res) {
							
							// Try each server
							while($server = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($ldap_res)) {
								
								// Configuration array
								$conf = array(
									'host' => $server['address'],
									'port' => $server['port'],
									'version' => $server['version'],
									'baseDN' => $server['basedn'],
									'tls' => $server['tls'],
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
									
									// New helper class
									$utils = t3lib_div::makeInstance('tx_ldapmacmade_utils');
									
									// LDAP users
									$users = $utils->cleanEntries($results['entries']);
									
									// Check array
									if (is_array($users)) {
										
										// Check import type
										if ($loginType == 'BE') {
											
											// Import as backend user
											$uid = $utils->ldap2BE(array_shift($users),$server,'IMPORT');
											
										} else if ($loginType == 'FE') {
											
											// Import as frontend user
											$uid = $utils->ldap2FE(array_shift($users),$server,'IMPORT');
										}
										
										// Check if user has been inserted
										if (isset($uid)) {
											
											// Store user
											$auth = t3lib_BEfunc::getRecord($this->db_user['table'],$uid);
										}
									}
									
									// Exit loop
									break;
								}
							}
						}
					}
				}
			}
			
			// Return auth state
			return $auth;
		}
		
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
			$auth = 0;
			
			// Extension configuration
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ldap_macmade']);
			
			// Check login
			if (array_key_exists('uname',$this->login) && array_key_exists('uident_text',$this->login)) {
				
				// Login type
				$loginType = $this->authInfo['loginType'];
				
				// Additional MySQL WHERE clause for selecting records
				$addWhere = ($loginType == 'BE') ? 'be_enable=1 AND be_auth=1 AND deleted=0 AND hidden=0' : 'fe_enable=1 AND fe_auth=1 AND deleted=0 AND hidden=0';
				
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
							'tls' => $server['tls'],
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
						$ldap->init(1);
						
						// Results
						$results = array(
							'version' => $ldap->pver,
							'bind' => $ldap->r,
							'search' => $ldap->sr,
							'count' => $ldap->num,
							'entries' => $ldap->info,
							'dn' => $ldap->dn,
						);
						
						// Check results
						if (is_array($results['dn']) && count($results['dn'])) {
							
							// Get user DN
							$dn = array_shift($results['dn']);
							
							// Add user informations to LDAP configuration
							$conf['user'] = $dn;
							$conf['password'] = $this->login['uident_text'];
							
							// New LDAP class
							$connect = t3lib_div::makeInstance('tx_ldapmacmade_div');
							
							// Set LDAP class configuration array
							$connect->conf = $conf;
							
							// Initialization of the LDAP class
							$connect->init(0,1);
							
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
			
			// Check if other authentication services can be launched
			if ($auth != 200 && $extConf['stop_auth'] == 0) {
				
				// Pass authentication
				$auth = 100;
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
