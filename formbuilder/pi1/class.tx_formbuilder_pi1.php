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
	 * Plugin 'Form displayer' for the 'formbuilder' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *     104:		function main($content,$conf)
	 *     177:		function setConfig
	 *     203:		function buildIndex($forms)
	 *     252:		function displayForm($uid)
	 * 
	 *				TOTAL FUNCTIONS: 4
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_formbuilder_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_formbuilder_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_formbuilder_pi1.php';
		
		// The extension key
		var $extKey = 'formbuilder';
		
		// Extension tables
		var $extTables = array(
			
			// Form datastructure
			'ds' => 'tx_formbuilder_datastructure',
			
			// Form data
			'data' => 'tx_formbuilder_formdata',
		);
		
		// Version of the Developer API required
		var $apimacmade_version = 1.5;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin 'tx_formbuilder_pi1', and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin
		 */
		function main($content,$conf) {
			
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Set default plufin variables
			$this->pi_setPiVarDefaults();
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// Init flexform configuration of the plugin
			$this->pi_initPIflexForm();
			
			// Store flexform informations
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
			
			// Set final configuration (TS or FF)
			$this->setConfig();
			
			// New instance of cObj
			$this->cObj = t3lib_div::makeInstance('tslib_cObj');
			
			// Storage
			$htmlCode = array();
			
			// Development notice
			$htmlCode[] =  '<div style="text-align: center; padding: 5px; border-style: solid; border-width: 1px; border-left-color:#FFFFFF; border-top-color:#FFFFFF; border-right-color:#BFBC99; border-bottom-color:#BFBC99; background: #FFFBCC;">';
			$htmlCode[] = '<h2>Important note</h2>';
			$htmlCode[] = '<p><strong>This plugin is still under heavy development.</strong></p>';
			$htmlCode[] = '<p>No form objects are rendered yet.</p>';
			$htmlCode[] =  '</div style="">';
			
			// Check for forms
			if ($this->conf['pidList']) {
				
				// Get an array with forms to display
				$forms = explode(',',$this->conf['pidList']);
				
				// Display mode
				if ($this->piVars['showForm'] && in_array($this->piVars['showForm'],$forms)) {
					
					// Single form
					$htmlCode[] = $this->displayForm($this->piVars['showForm']);
					
				} else if (count($forms) > 1) {
					
					// Multiple forms
					$htmlCode[] = $this->buildIndex($forms);
					
				} else {
					
					// Single form
					$htmlCode[] = $this->displayForm($forms[0]);
				}
			}
			
			// Return content
			return $this->pi_wrapInBaseClass(implode(chr(10),$htmlCode));
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
				'pidList' => 'sDEF:pages',
				'index.' => array(
					'showDescr' => 'sINDEX:show_descr',
					'cropDescr' => 'sINDEX:crop_descr',
				),
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf);
		}
		
		/**
		 * Returns an index.
		 * 
		 * This function creates an index of the forms to display.
		 * 
		 * @param		$forms				The forms to display
		 * @return		An index of the available forms
		 */
		function buildIndex($forms) {
			
			// Storage
			$htmlCode = array();
			
			// Number of forms
			$htmlCode[] = $this->api->fe_makeStyledContent('div','index-number',str_replace('%s',count($forms),$this->pi_getLL('index.number')));
			
			// Process forms
			foreach($forms as $uid) {
				
				// Start div
				$htmlCode[] = $this->api->fe_makeStyledContent('div','index-form',false,1,0,1);
				
				// Get record
				$rec = $this->pi_getRecord($this->extTables['ds'],$uid);
				
				// Title
				$htmlCode[] = $this->api->fe_makeStyledContent('h2','index-form-title',$rec['title']);
				
				// Description?
				if ($rec['description'] && $this->conf['index.']['showDescr']) {
					
					// Crop?
					$descr = ($this->conf['index.']['cropDescr']) ? $this->api->div_crop($rec['description'],$this->conf['index.']['cropDescr']) : $rec['description'];
					
					// Display description
					$htmlCode[] = $this->pi_RTEcssText($descr);
				}
				
				// Access link
				$htmlCode[] = $this->api->fe_makeStyledContent('div','index-form-link',$this->pi_linkTP_keepPIvars($this->pi_getLL('index.link'),array('showForm' => $rec['uid'])));
				
				// End div
				$htmlCode[] = '</div>';
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Render a form.
		 * 
		 * This function creates a full frontend form.
		 * 
		 * @param		$forms				The UID of the form
		 * @return		The complete form
		 */
		function displayForm($uid) {
			
			// Storage
			$htmlCode = array();
			
			// Start div
			$htmlCode[] = $this->api->fe_makeStyledContent('div','single-form',false,1,0,1);
			
			// Get record
			$rec = $this->pi_getRecord($this->extTables['ds'],$uid);
			
			// Title
			$htmlCode[] = $this->api->fe_makeStyledContent('h2','index-form-title',$rec['title']);
			
			// Description?
			if ($rec['description']) {
				
				// Display description
				$htmlCode[] = $this->pi_RTEcssText($rec['description']);
			}
			
			// Get form DS
			$ds = $this->api->div_xml2array($rec['xmlds'],0);
			
			// Check for a valid form
			if (is_array($ds['T3FlexForms']['data']['sDEF']['lDEF']['fields']['el'])) {
				
				// Start form
				$htmlCode[] = '';
				
				// Start table
				$htmlCode[] = $this->api->fe_makeStyledContent('table','fields',false,1,0,1,array('border'=>'0','width'=>'100%','cellspacing'=>'0','cellpadding'=>'5','align'=>'center'));
				
				// Process each field
				foreach($ds['T3FlexForms']['data']['sDEF']['lDEF']['fields']['el'] as $field) {
					
					// Field type
					$type = key($field);
					
					// Start row
					$htmlCode[] = '<tr>';
					
					// Label
					$htmlCode[] = $this->api->fe_makeStyledContent('td','label',$field[$type]['el']['label']['vDEF'],1,0,0,array('width'=>'25%','align'=>'left','valign'=>'middle'));
					
					// Field
					$htmlCode[] = $this->api->fe_makeStyledContent('td','field','[' . strtoupper($type) . ']',1,0,0,array('width'=>'75%','align'=>'left','valign'=>'middle'));
					
					// End row
					$htmlCode[] = '</tr>';
				}
				
				// End table
				$htmlCode[] = '</table>';
				
				// Submit
				$htmlCode[] = '<input type="submit" name="submit" value="' . $rec['submit'] . '">';
				
				// DEBUG ONLY - Display form DS
				$htmlCode[] = $this->api->viewArray($ds['T3FlexForms']['data']['sDEF']['lDEF']['fields']['el']);
			}
			
			// End div
			$htmlCode[] = '</div>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
	}
	
	// XClass inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formbuilder/pi1/class.tx_formbuilder_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formbuilder/pi1/class.tx_formbuilder_pi1.php']);
	}
?>
