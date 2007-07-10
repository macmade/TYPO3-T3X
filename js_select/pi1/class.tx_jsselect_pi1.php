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
	 * Plugin 'JavaScript Selector' for the 'js_select' extension.
	 *
	 * @author		Jean-David Gadina <macmade@gadlab.net>
	 * @version		2.2
	 */
	
	 /**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      74:		function main($content,$conf)
	 *     121:		function buildIndex($jsArray)
	 *     142:		function buildScripts($jsArray,$confArray)
	 *     169;		function getJSFiles
	 * 
	 *				TOTAL FUNCTIONS: 4
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	class tx_jsselect_pi1 extends tslib_pibase {
		
		// Same as class name
		var $prefixId = 'tx_jsselect_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_jsselect_pi1.php';
		
		// The extension key
		var $extKey = 'js_select';
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Base functions.
		 ***************************************************************/
		
		/**
		 * Adds a <script> tag to the page header.
		 * 
		 * This function includes an additionnal page header with a <script>
		 * tag for each JavaScript included in the page.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		Header data
		 */
		function main($content,$conf) {
			
			// Store TS configuration array
			$this->conf = $conf;
			
			// Reads extension configuration
			$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['js_select']);
			
			// JS files
			$jsArray = $this->getJSFiles();
			
			// Check for a valid JS array
			if (is_array($jsArray) && count($jsArray)) {
				
				// Storage
				$content = array();
				
				// Checks if there is one or more JavaScript(s) to include
				if (is_array($jsArray) && count($jsArray)) {
					
					// Build comments if required
					if ($conf['jsComments']) {
						$content[] = '<!--';
						$content[] = '/***************************************************************';
						$content[] = ' * Scripts added by plugin "tx_jsselect_pi1"';
						$content[] = ' * ';
						$content[] = ' * Index:';
						$content[] = $this->buildIndex($jsArray);
						$content[] = ' ***************************************************************/';
						$content[] = '//-->';
					}
					
					// Build <script> tags
					$content[] = $this->buildScripts($jsArray,$confArray);
					
					// Returns header data
					return implode(chr(10),$content) . chr(10);
				}
			}
		}
		
		/**
		 * Build the index of the page JavaScripts.
		 * 
		 * @param		$jsArray			The page Javascript(s)
		 * @return		An index of the JavaScript(s)
		 */
		function buildIndex($jsArray) {
			
			// Init
			$index = array();
			$i = 1;
			
			// Builds comments
			foreach($jsArray as $javascript) {
				$index[] = ' * ' . $i . ') ' . $javascript;
				$i++;
			}
			
			// Returns the index
			return implode(chr(10),$index);
		}
		
		/**
		 * Build <script> tags.
		 * 
		 * @param		$jsArray			The page JavaScript(s)
		 * @param		$confArray			The configuration of the extension
		 * @return		A <script> tag for each JavaScript
		 */
		function buildScripts($jsArray,$confArray) {
			
			// Init
			$scripts = array();
			
			// Defer option
			$defer = ($this->conf['defer']) ? ' defer' : '';
			
			// Builds script tags
			foreach($jsArray as $javascript) {
				$scripts[] = '<script src="' . $confArray['JSDIR'] . $javascript . '" type="' . $this->conf['type'] . '" language="' . $this->conf['language'] . '" charset="' . $this->conf['charset'] . '"' . $defer . '></script>';
			}
			
			// Returns the <script> tags
			return implode(chr(10),$scripts);
		}
		
		/**
		 * Return all JS file
		 * 
		 * This function returns the specified JS files for the current page. It also
		 * checks, if needed, for javascripts on the top paes.
		 * 
		 * @return		An array with the javascripts to load
		 */
		function getJSFiles() {
			
			// Checking if the recursive option si set
			if ($this->conf['recursive'] == 1) {
				
				// Storage
				$jsArray = array();
				
				// Check each top page
				foreach($GLOBALS['TSFE']->config['rootLine'] as $topPage) {
					
					// Check if a javascript is specified
					// Thanx to Wolfgang Klinger for the debug
					if ($topPage['tx_jsselect_javascripts']) {
						
						// Add JS files
						$jsArray = array_merge($jsArray,explode(',',$topPage['tx_jsselect_javascripts']));
					}
				}
			} else if (!empty($GLOBALS['TSFE']->page['tx_jsselect_javascripts'])){
				
				// Get page only javascripts
				$jsArray = explode(',',$GLOBALS['TSFE']->page['tx_jsselect_javascripts']);
			}
			
			// Return JS files
			return $jsArray;
		}
	}
	
	/** 
	 * XClass inclusion.
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/js_select/pi1/class.tx_jsselect_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/js_select/pi1/class.tx_jsselect_pi1.php']);
	}
?>
