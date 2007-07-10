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
	 * Plugin 'Typo3 Code Snippets' for the 'snippetsmacmade' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      159:		function main($content,$conf)
	 *     195:		function setConfig
	 *     219:		function showCode
	 * 
	 *				TOTAL FUNCTIONS: 3
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	// Typo3 BE class
	require_once(PATH_t3lib.'class.t3lib_befunc.php');
	
	class tx_snippetsmacmade_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_snippetsmacmade_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_snippetsmacmade_pi1.php';
		
		// The extension key
		var $extKey = 'snippets_macmade';
		
		// Version of the Developer API required
		var $apimacmade_version = 2.4;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_snippetsmacmade_pi1", and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 * @see			setConfig
		 * @see			showCode
		 */
		function main($content,$conf) {
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Init flexform configuration of the plugin
			$this->pi_initPIflexForm();
			
			// Store flexform informations
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
			
			// Set final configuration (TS or FF)
			$this->setConfig();
			
			// Show code
			$content = $this->showCode();
			
			// Return content
			return $this->pi_wrapInBaseClass($content);
		}
		
		/**
		 * Set configuration array.
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
				'file' => 'sDEF:file',
				'rec' => 'sREC:rec',
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'Configuration array');
		}
		
		/**
		 * Show code
		 * 
		 * This function is used to get the code, and to parse it through
		 * GeSHi.
		 * 
		 * @return		The parsed code
		 */
		function showCode() {
			
			// Storage
			$htmlCode = array();
			
			// Check for a file
			if (!empty($this->conf['file'])) {
				
				// File path
				$path = (substr($this->conf['file'],0,1) == '/') ? $this->conf['file'] : t3lib_div::getFileAbsFileName($this->conf['file']);
				
				// Check if file exists and is readable
				if (@file_exists($path) && @is_readable($path)) {
					
					// Get code
					$code = file_get_contents($path);
					
					// Check for lines
					if (!empty($code)) {
						
						// Unify line breaks
						$code = $this->api->div_convertLineBreaks($code,0);
						
						// Get lines
						$lines = explode(chr(10),$code);
						
						// Start list
						$htmlCode[] = $this->api->fe_makeStyledContent('ol','file',false,1,0,1);
						
						// Process lines
						foreach($lines as $key=>$value) {
							
							// Add line
							$htmlCode[] = $this->api->fe_makeStyledContent('li','line-' . $key,htmlspecialchars($value));
						}
						
						// End list
						$htmlCode[] = '</ol>';
					}
				}
			} else {
				
				// Record to show
				$rec = t3lib_BEfunc::splitTable_Uid($this->conf['rec']);
				
				// Get record
				$row = $this->pi_getRecord($rec[0],$rec[1]);
				
				// Check row
				if ($row) {
					
					// Check record type
					switch($rec[0]) {
						
						// TemplaVoila
						case 'tx_templavoila_datastructure':
							
							// Get code
							$code = array($row['dataprot']);
							
						break;
						
						// TemplaVoila
						case 'sys_template':
							
							// Get code
							$code = array('constants'=>$row['constants'],'setup'=>$row['config']);
							
						break;
						
						// Default - TSConfig
						default:
							
							// Get code
							$code = array($row['TSconfig']);
							
						break;
					}
					
					// Process code
					foreach($code as $key=>$value) {
						
						// Unify line breaks
						$value = $this->api->div_convertLineBreaks($value,0);
						
						// Don't proces empty values
						if (!empty($value)) {
							
							// Get lines
							$lines = explode(chr(10),$value);
							
							// Check for a header
							if (is_string($key)) {
								
								// Add header
								$htmlCode[] = $this->api->fe_makeStyledContent('h2',$key,$this->pi_getLL($key));
							}
							
							// Start list
							$htmlCode[] = $this->api->fe_makeStyledContent('ol','file',false,1,0,1);
							
							// Process lines
							foreach($lines as $key=>$value) {
								
								// Add line
								$htmlCode[] = $this->api->fe_makeStyledContent('li','line-' . $key,htmlspecialchars($value));
							}
							
							// End list
							$htmlCode[] = '</ol>';
						}
					}
				}
			}
			
			// Return code
			return implode(chr(10),$htmlCode);
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/snippets_macmade/pi1/class.tx_snippetsmacmade_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/snippets_macmade/pi1/class.tx_snippetsmacmade_pi1.php']);
	}
?>
