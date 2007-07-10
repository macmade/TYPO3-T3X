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
	 * Class 'tx_ldapmacmade_div' for the 'ldap_macmade' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *      98:		function init($try)
	 *     145:		function setOptions
	 *     167:		function createBind
	 *     185:		function searchServer
	 *     203:		function countEntries
	 *     221:		function getEntries
	 *     239:		function getEntriesDN
	 * 
	 *				TOTAL FUNCTIONS: 7
	 */
	
	// Developer API class
	require_once (t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	class tx_ldapmacmade_div {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables.
		 ***************************************************************/
		
		// Configuration array
		var $conf = array(
			'host' => '',
			'port' => '',
			'version' => '',
			'tls' => 0,
			'user' => '',
			'password' => '',
			'baseDN' => '',
			'filter' => '',
		);
		
		// LDAP ressources
		var $ds;
		var $pver;
		var $r;
		var $sr;
		var $num;
		var $info;
		var $dn = array();
		
		// LDAP errors
		var $errors = array();
		
		
		
		
		/***************************************************************
		 * SECTION 1 - INIT
		 *
		 * Base module functions.
		 ***************************************************************/
		
		/**
		 * Initialization of the class.
		 * 
		 * @param		$try		If this is set, this class only tries to connect to the LDAP server
		 * @return		Void
		 */
		function init($dn=false,$try=false) {
			
			// Hook for LDAP connection and queries
			if (tx_apimacmade::div_checkArrayKeys($GLOBALS['TYPO3_CONF_VARS'],'EXTCONF,ldap_macmade,ldap_base',0,'array')) {
				
				// Process hook calls
				foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ldap_macmade']['ldap_base'] as $_hookCall) {
					
					// Parameters
					$params = array('dn'=>$dn,'try'=>$try);
					
					// Call user function
					t3lib_div::callUserFunction($_hookCall,$params,$this);
				}
				
			} else {
				
				// Connection
				$this->ds = @ldap_connect($this->conf['host'],$this->conf['port']);
				
				// Set LDAP options
				$this->setOptions();
				
				// Start LDAP over TLS
				if ($this->conf['tls']) {
					
					// Use TLS
					@ldap_start_tls($this->ds);
				}
				
				// Bind with server
				$this->createBind();
				
				// Complete processing?
				if (!$try) {
					
					// Search the server
					$this->searchServer();
					
					// Get entries count
					$this->countEntries();
					
					// Get entries
					$this->getEntries();
					
					// Also get entries DN?
					if ($dn) {
						
						// Get DN
						$this->getEntriesDN();
					}
				}
				
				// Disconnect
				@ldap_close($this->ds);
			}
		}
		
		/**
		 * Set LDAP options.
		 * 
		 * @return		Void
		 */
		function setOptions() {
			
			// Testing connection ressource
			if ($this->ds) {
				
				// Check version parameter
				if ($this->conf['version']) {
					
					// Protocol version
					$this->pver = @ldap_set_option($this->ds,LDAP_OPT_PROTOCOL_VERSION,$this->conf['version']);
					
					// Error message
					$this->errors[] = array('code'=>ldap_errno($this->ds),'message'=>ldap_error($this->ds));
				}
			}
		}
		
		/**
		 * Bind with server.
		 * 
		 * @return		Void
		 */
		function createBind() {
			
			// Testing connection ressource
			if ($this->ds) {
				
				// Bind
				$this->r = @ldap_bind($this->ds,$this->conf['user'],$this->conf['password']);
				
				// Error message
				$this->errors[] = array('code'=>ldap_errno($this->ds),'message'=>ldap_error($this->ds));
			}
		}
		
		/**
		 * Search the server.
		 * 
		 * @return		Void
		 */
		function searchServer() {
			
			// Testing bind
			if ($this->r) {
				
				// Search
				$this->sr = @ldap_search($this->ds,$this->conf['baseDN'],$this->conf['filter']);
				
				// Error message
				$this->errors[] = array('code'=>ldap_errno($this->ds),'message'=>ldap_error($this->ds));
			}
		}
		
		/**
		 * Count the number of entries.
		 * 
		 * @return		Void
		 */
		function countEntries() {
			
			// Testing seach
			if ($this->sr) {
				
				// Get count
				$this->num =  @ldap_count_entries($this->ds,$this->sr);
				
				// Error message
				$this->errors[] = array('code'=>ldap_errno($this->ds),'message'=>ldap_error($this->ds));
			}
		}
		
		/**
		 * Get each entry.
		 * 
		 * @return		Void
		 */
		function getEntries() {
			
			// Testing bind
			if ($this->num) {
				
				// Get entries
				$this->info = @ldap_get_entries($this->ds,$this->sr);
				
				// Error message
				$this->errors[] = array('code'=>ldap_errno($this->ds),'message'=>ldap_error($this->ds));
			}
		}
		
		/**
		 * Get entries DN.
		 * 
		 * @return		Void
		 */
		function getEntriesDN() {
			
			// Testing bind
			if ($this->num) {
				
				// Get first entry
				$entry = ldap_first_entry($this->ds,$this->sr);
				
				// Process entries
				while($entry) {
					
					// Get and store entry DN
					$this->dn[] = ldap_get_dn($this->ds,$entry);
					
					// Next entry
					$entry = ldap_next_entry($this->ds,$entry);
				}
				
				// Error message
				$this->errors[] = array('code'=>ldap_errno($this->ds),'message'=>ldap_error($this->ds));
			}
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/class.tx_ldapmacmade_div.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/class.tx_ldapmacmade_div.php']);
	}
?>
