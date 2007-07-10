<?php
	/***************************************************************
	 * Copyright notice
	 *
	 * (c) 2004 macmade.net
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
	 * Plugin 'Mozilla/Firefox search plugin' for the 'mozsearch' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *     98:		function main($content,$conf)
	 *     165:		function setConfig
	 *     198:		function setJS
	 * 
	 *				TOTAL FUNCTIONS: 3
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_mozsearch_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_mozsearch_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_mozsearch_pi1.php';
		
		// The extension key
		var $extKey = 'mozsearch';
		
		// Upload directory
		var $uploadDir = 'uploads/tx_mozsearch/';
		
		// Version of the Developer API required
		var $apimacmade_version = 2.6;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_mozsearch_pi1", and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 * @see			setConfig
		 * @see			setJS
		 */
		function main($content,$conf) {
			
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Current record infos
			$infos = explode(':',$this->cObj->currentRecord);
			
			// Check if current record is content
			if ($infos[0] == $GLOBALS['TYPO3_CONF_VARS']['SYS']['contentTable']) {
				
				// Init flexform configuration of the plugin
				$this->pi_initPIflexForm();
				
				// Store flexform informations
				$this->piFlexForm = $this->cObj->data['pi_flexform'];
				
				// Set final configuration (TS or FF)
				$this->setConfig();
			}
			
			// Set JavaScript code used by the plugin
			$this->setJS();
			
			// Site URL
			$siteUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
			
			// Icon source
			$iconSource = ($this->pi_getFFvalue($this->piFlexForm,'icon','sDEF') == '') ? $this->conf['icon'] : $this->uploadDir . $this->conf['icon'];
			
			// Add icon absolute path to configuration
			$this->conf['iconAbsPath'] = t3lib_div::getFileAbsFileName($iconSource);
			
			// Add search page to configuration
			$this->conf['searchPage'] = $siteUrl . $this->pi_getPageLink($this->conf['searchPid']);
			
			// Plugin URL
			$pluginFile = $siteUrl. 'index.php?&id=' . $GLOBALS['TSFE']->id . '&type=' . $this->conf['xmlPageId'] . '&' . $this->prefixId . '[file]=plugin/' . $this->conf['pluginFileName'] . '.src';
			
			// Icon URL
			$pluginIcon = $siteUrl. 'index.php?&id=' . $GLOBALS['TSFE']->id . '&type=' . $this->conf['xmlPageId'] . '&' . $this->prefixId . '[file]=icon/' . $this->conf['pluginFileName'] . '.png';
			
			// JavaScript function call
			$jsCall = $this->prefixId . '_addSearchEngine(\'' . $pluginFile . '\',\'' . $pluginIcon . '\',\'' . $this->conf['name'] . '\',\'' . $this->conf['cat'] . '\')';
			
			// Store configuration
			$GLOBALS['TSFE']->fe_user->setKey('ses',$this->prefixId,serialize($this->conf));
			
			// Content
			$content = '<a href="javascript:' . $jsCall . '">' . nl2br($this->conf['linkText']) . '</a>';
			
			// Return content
			return $this->pi_wrapInBaseClass($content);
		}
		
		/**
		 * Set configuration array
		 * 
		 * This function is used to set the final configuration array of the
		 * plugin, by providing a mapping array between the TS & the flexform
		 * configuration.
		 * 
		 * @return		Void
		 */
		function setConfig() {
			
			// Mapping array for PI flexform
			$flex2conf = array(
				'version' => 'sOPT:version',
				'name' => 'sDEF:name',
				'description' => 'sDEF:description',
				'searchPid' => 'sDEF:searchpid',
				'method' => 'sOPT:method',
				'sourceid' => 'sOPT:sourceid',
				'sword' => 'sOPT:sword',
				'linkText' => 'sDEF:linktext',
				'icon' => 'sDEF:icon',
				'cat' => 'sOPT:cat',
				'pluginFileName' => 'sOPT:plugin_filename',
				'queryCharset' => 'sOPT:query_charset',
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'MozSearch: configuration array');
		}
		
		/**
		 * Add JavaScript code
		 * 
		 * This function creates the JavaScript code used to add a custom
		 * search engin in Mozilla browsers.
		 * 
		 * @return		Void
		 */
		function setJS() {
			
			// Storage
			$jsCode = array();
			
			// Function to add search engines
			$jsCode[] = 'function ' . $this->prefixId . '_addSearchEngine(plugin,icon,name,cat) {';
			$jsCode[] = '	if ((typeof window.sidebar == "object") && (typeof window.sidebar.addSearchEngine == "function")) {';
			$jsCode[] = '		window.sidebar.addSearchEngine(plugin,icon,name,cat);';
			$jsCode[] = '	}';
			$jsCode[] = '}';
			
			// Adds JS code
			$GLOBALS['TSFE']->setJS($this->prefixId,implode(chr(10),$jsCode));
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mozsearch/pi1/class.tx_mozsearch_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mozsearch/pi1/class.tx_mozsearch_pi1.php']);
	}
?>
