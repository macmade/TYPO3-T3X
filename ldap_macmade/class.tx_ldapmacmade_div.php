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
	 * Class 'tx_ldapmacmade_div' for the 'ldap_macmade' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *      95:		function init($try)
	 *     128:		function setOptions
	 *     150:		function createBind
	 *     168:		function searchServer
	 *     186:		function countEntries
	 *     204:		function getEntries
	 * 
	 *				TOTAL FUNCTIONS: 6
	 */
	
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
		function init($try=false) {
			
			// Connection
			$this->ds = @ldap_connect($this->conf['host'],$this->conf['port']);
			
			// Set LDAP options
			$this->setOptions();
			
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
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/class.tx_ldapmacmade_div.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/class.tx_ldapmacmade_div.php']);
	}
?>
