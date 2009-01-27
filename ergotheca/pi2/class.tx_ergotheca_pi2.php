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
	 * Plugin 'OASIS speakers registration' for the 'eesp_oasis' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *     104:		function main($content,$conf)
	 *     145:		function initTemplate
	 *     164:		function setFeAdminTemplate()
	 * 
	 * SECTION:		4 - MISC / UTILS
	 *     235:		function renderTemplate($templateContent,$templateMarkers,$templateSection)
	 *     259:		function createInput($type,$name,$number=1,$value=false,$label=false,$checked=false)
	 *     352:		function createTextArea($name)
	 *     385:		function createSelectFromTable($table,$labelField,$valueField,$name,$title=false)
	 *     436:		function createSelect($name,$options,$title=false)
	 *     476:		function buildFormElementHeader($label)
	 *     513:		function updateArray($content,$conf)
	 * 
	 *				TOTAL FUNCTIONS: 10
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib . 'class.tslib_pibase.php');
	
	class tx_ergotheca_pi2 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_ergotheca_pi2';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi2/class.tx_ergotheca_pi2.php';
		
		// The extension key
		var $extKey = 'ergotheca';
		
		// SQL caching for external tables
		var $sqlCache = array();
		
		// DB table
		var $extTable = 'tx_ergotheca_tools';
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin 'tx_ergotheca_pi1', and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 * @see			initTemplate
		 * @see			setFeAdminTemplate
		 */
		function main($content,$conf) {
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Set default plufin variables
			$this->pi_setPiVarDefaults();
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// Disable caching
			$GLOBALS['TSFE']->set_no_cache();
			
			// Template file initialization
			$templateContent = $this->initTemplate();
			
			// Creates fe_adminLib template file
			$feAdmin = $this->setFeAdminTemplate($templateContent);
			
			// Render content object
			$content = $this->cObj->cObjGetSingle($this->conf['fe_adminLib'],$feAdmin);
			
			if (!is_int($conf['fe_adminLib.']['pid'])) {
				
				// Return content
				return $this->pi_wrapInBaseClass($content);
			} else {
				
				// No storage pid
				return $this->pi_getLL('pi_error_pid');
			}
		}
		
		/**
		 * Loads the template file.
		 * 
		 * This function reads the template file and returns it as
		 * a C-Object.
		 * 
		 * @return		The C-Object of the template file
		 */
		function initTemplate() {
			
			// Template load
			$templateContent = $this->cObj->fileResource($this->conf['templateFile']);
			
			// Return template C-Object
			return $templateContent;
		}
		
		/**
		 * Creates a template.
		 * 
		 * This function creates the template file for the fe_adminLib
		 * script, with all the parts necessary for the fe_adminLib class.
		 * 
		 * @return		The template content
		 * @see			renderTemplate
		 */
		function setFeAdminTemplate($templateContent) {
			
			// Get fe_adminLib TS parameters
			$feAdmin = $this->conf['fe_adminLib.'];
			
			// Template markers storage
			$templateMarkers = array();
			
			// Markers substitution
			$templateMarkers['###CREATE_FORM_NAME###'] = $this->prefixID . '_feAdmin';
			$templateMarkers['###CREATE_FORM_ENCTYPE###'] = $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'];
			$templateMarkers['###CREATE_FIELD_NAME###'] = $this->createTextArea('name');
			$templateMarkers['###CREATE_FIELD_OPENCONTENT###'] = $this->createInput('radio','opencontent',2,1,1);
			$templateMarkers['###CREATE_FIELD_AUTHORS###'] = $this->createTextArea('authors');
			$templateMarkers['###CREATE_FIELD_TESTYEAR###'] = $this->createInput('text','testyear');
			$templateMarkers['###CREATE_FIELD_EVALFIELD###'] = $this->createInput('checkbox','evalfield',3,'unix',1);
			$templateMarkers['###CREATE_FIELD_FORMALIZATION###'] = $this->createInput('radio','formalization',5,1,1);
			$templateMarkers['###CREATE_FIELD_EVALOBJECT###'] = $this->createTextArea('evalobject');
			$templateMarkers['###CREATE_FIELD_PRACTICEMODEL###'] = $this->createTextArea('practicemodel');
			$templateMarkers['###CREATE_FIELD_TARGETPUBLIC_AGE###'] = $this->createInput('checkbox','targetpublic_age',4,'unix',1);
			$templateMarkers['###CREATE_FIELD_TARGETPUBLIC_ALT###'] = $this->createInput('text','targetpublic_alt');
			$templateMarkers['###CREATE_FIELD_PASSATION_METHOD###'] = $this->createInput('checkbox','passation_method',2,'unix',1);
			$templateMarkers['###CREATE_FIELD_PASSATION_DESCRIPTION###'] = $this->createTextArea('passation_description');
			$templateMarkers['###CREATE_FIELD_PASSATION_PROCEDURE###'] = $this->createTextArea('passation_procedure');
			$templateMarkers['###CREATE_FIELD_PASSATION_MATERIAL###'] = $this->createTextArea('passation_material');
			$templateMarkers['###CREATE_FIELD_PASSATION_SETPOS###'] = $this->createTextArea('passation_setpos');
			$templateMarkers['###CREATE_FIELD_PASSATION_QUOTINT###'] = $this->createTextArea('passation_quotint');
			$templateMarkers['###CREATE_FIELD_COMMENTS###'] = $this->createTextArea('comments');
			$templateMarkers['###CREATE_FIELD_SOURCES###'] = $this->createTextArea('sources');
			$templateMarkers['###CREATE_FIELD_LINKS###'] = $this->createTextArea('links');
			$templateMarkers['###CREATE_FIELD_LANGUAGE###'] = $this->createSelect('language',5);
			$templateMarkers['###CREATE_FIELD_LANGUAGE_ALT###'] = $this->createInput('text','language_alt');
			$templateMarkers['###CREATE_FIELD_TRADUCTION###'] = $this->createInput('checkbox','traduction',5,'unix',1);
			$templateMarkers['###CREATE_FIELD_TRADUCTION_ALT###'] = $this->createInput('text','traduction_alt');
			$templateMarkers['###CREATE_FIELD_TRADUCTION_STANDARD###'] = $this->createInput('checkbox','traduction_standard',5,'unix',1);
			$templateMarkers['###CREATE_FIELD_TRADUCTION_STANDARD_ALT###'] = $this->createInput('text','traduction_standard_alt');
			$templateMarkers['###CREATE_FIELD_USECOND###'] = $this->createTextArea('usecond');
			$templateMarkers['###CREATE_FIELD_REMARKS###'] = $this->createTextArea('remarks');
			$templateMarkers['###CREATE_FIELD_COST###'] = $this->createInput('text','cost');
			$templateMarkers['###CREATE_FIELD_FILES###'] = $this->createInput('file','files',3);
			$templateMarkers['###CREATE_FIELD_PICTURES###'] = $this->createInput('file','pictures',3);
			$templateMarkers['###CREATE_SUBMIT###'] = $this->createInput('submit','submit',1,$this->pi_getLL('pi_create_submit'));
			$templateMarkers['###CREATE_SAVED_TITLE###'] = $this->pi_getLL('pi_create_saved_title');
			$templateMarkers['###CREATE_SAVED_MESSAGE###'] =  $this->pi_getLL('pi_create_saved_message');
			
			// Template rendering
			$feAdmin['templateContent'] = $this->renderTemplate($templateContent,$templateMarkers,'###FE_ADMINLIB_TEMPLATE###');
			
			// Return template content
			return $feAdmin;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 4 - MISC / UTILS
		 *
		 * General functions for the plugin.
		 ***************************************************************/
		
		/**
		 * Template rendering
		 * 
		 * This function analyzes the template C-Object and substitute
		 * the specified section with the specified subsections.
		 * 
		 * @param		$templateContent	The template C-Object
		 * @param		$templateMarkers	The markers array
		 * @param		$templateSection	The section to substitute
		 * @return		The processed template section
		 */
		function renderTemplate($templateContent,$templateMarkers,$templateSection) {
			
			// Get template subparts
			$subpart = $this->cObj->getSubpart($templateContent,$templateSection);
			
			// Return substituted section
			return $this->cObj->substituteMarkerArrayCached($subpart,array(),$templateMarkers,array());
		}
		
		/**
		 * Creates a form input.
		 * 
		 * This function creates one or many HTML <input> tag for use with the
		 * fe_adminLib script.
		 * 
		 * @param		$type				The input type
		 * @param		$name				The name of the field
		 * @param		$number				The number of input to create
		 * @param		$value				The default value of the input
		 * @param		$label				An additional label found in the locallang file
		 * @param		$checked			For checkboxes or radios, checked by default
		 * @return		The complete input zone.
		 * @see			buildFormElementHeader
		 */
		function createInput($type,$name,$number=1,$value=false,$label=false,$checked=false) {
			
			$FEvars = t3lib_div::_POST('FE');
			if (is_array($FEvars) && !$value) {
				
					$value = $FEvars[$this->extTable][$name];
				
			} else if (is_array($FEvars) && $type == 'checkbox') {
					
					$checked = $FEvars[$this->extTable][$name];
					
				} else if (is_array($FEvars) && $type == 'radio') {
					
					$checked = array($FEvars[$this->extTable][$name] + 1);
				}
			
			// Optional parameters storage
			$params = array();
			
			// Creates size parameter for text inputs
			if ($type == 'text') {
				$params['size'] = ' size="' . $this->conf['textInputSize'] . '"';
			}
			
			// Output storage
			$code = array();
			
			// Create fe_adminLib input name
			if ($type == 'submit') {
				$feAdminName = $name;
			} else {
				$feAdminName = 'FE[' . $this->extTable . '][' . $name . ']';
				
				// Build header
				$code[] = $this->buildFormElementHeader($name);
			}
			
			// Updating name for multiple values inputs
			if ($type == 'file' || $type == 'checkbox') {
				$feAdminName .= '[]';
			}
			
			// Single or multiple inputs
			if ($number == 1) {
				// Detect if a checkbox or a radio must be checked by default
				if (($type == 'checkbox' || $type == 'radio') && $checked) {
					$params['checked'] = ' checked';
				}
				
				$code[] = '<input type="' . $type . '" name="' . $feAdminName . '" value="' . $value . '" ' . implode('',$params) . '>';
			} else {
				
				for ($i = 0; $i < $number; $i++) {
					
					// Detect if a checkbox or a radio must be checked by default
					if (($type == 'checkbox' || $type == 'radio') && is_array($checked)) {
						if (in_array($i + 1,$checked)) {
							
							// Checked
							$params['checked'] = ' checked';
						} else {
							
							// Not checked
							unset($params['checked']);
						}
					}
					
					// Check value type
					if (is_array($value)) {
					
						// Custom values
						$currentValue = $value[$i];
					} else if ($value == 'unix') {
						
						// Unix like values
						$currentValue = pow(2,$i);
					} else if ($value) {
						
						// Normal values
						$currentValue = $i;
					} else {
						
						// No value
						$currentValue == '';
					}
					
					$code[] = '<input type="' . $type . '" name="' . $feAdminName . '" value="' . $currentValue . '" ' . implode('',$params) . '>';
					
					// Add label if required
					$code[] = ($label) ? $this->pi_getLL('pi_field_' . $name . '.I.' . $i) . '<br />' : '<br />';
				}
			}
			
			// Return code
			return implode(chr(10),$code);
		}
		
		/**
		 * Creates a text area.
		 * 
		 * This function creates an HTML <textarea> tag for use with the
		 * fe_adminLib script.
		 * 
		 * @param		$name				The name of the field
		 * @return		The complete textarea zone.
		 * @see			buildFormElementHeader
		 */
		function createTextArea($name) {
			
			$FEvars = t3lib_div::_POST('FE');
			if (is_array($FEvars)) {
				$value = $FEvars[$this->extTable][$name];
			}
			
			// Output storage
			$code = array();
			
			// Build header
			$code[] = $this->buildFormElementHeader($name);
			
			// Create fe_adminLib input name
			$feAdminName = 'FE[' . $this->extTable . '][' . $name . ']';
			
			// Build input
			$code[] = '<textarea name="' . $feAdminName . '" rows="' . $this->conf['textAreaRows'] . '" cols="' . $this->conf['textAreaCols'] . '">' . $value . '</textarea>';
			
			// Return code
			return implode(chr(10),$code);
		}
		
		/**
		 * Creates a select.
		 * 
		 * This function creates an HTML <select> tag for use with the
		 * fe_adminLib script. Select options are taken from an external
		 * table.
		 * 
		 * @param		$table				The external table
		 * @param		$labelField			The field of the table to use as the option label
		 * @param		$valueField			The field of the table to use as the option value
		 * @param		$name				The name of the field
		 * @param		$title				An optional option with a title for the select
		 * @return		The complete select zone.
		 * @see			buildFormElementHeader
		 */
		function createSelectFromTable($table,$labelField,$valueField,$name,$title=false) {
			
			// Output storage
			$code = array();
			
			// Build header
			$code[] = $this->buildFormElementHeader($name);
			
			// Create fe_adminLib input name
			$feAdminName = 'FE[' . $this->extTable . '][' . $name . ']';
			
			// UID of the page from where to get the records
			$pid = $this->conf[$table . '_pid'];
			
			// Memorise external table
			if (!array_key_exists($table,$this->sqlCache)) {
				$this->sqlCache[$table] = $this->pi_getCategoryTableContents($table,$pid);
			}
			
			// Select start
			$code[] = '<select name="' . $feAdminName . '">';
			
			// Empty option for title
			if ($title) {
				$code[] = '<option value="" selected>' . $title . '</option>';
			}
			
			// Create select options
			foreach($this->sqlCache[$table] as $row) {
				$code[] = '<option value="' . $row[$valueField] . '">' . $row[$labelField] . '</option>';
			}
			
			// Select end
			$code[] = '</select>';
			
			// Return code
			return implode(chr(10),$code);
		}
		
		/**
		 * Creates a select.
		 * 
		 * This function creates an HTML <select> tag for use with the
		 * fe_adminLib script.
		 * 
		 * @param		$name				The name of the field
		 * @param		$options			The number of options to create
		 * @param		$title				An optional option with a title for the select
		 * @return		The complete select zone.
		 * @see			buildFormElementHeader
		 */
		function createSelect($name,$options,$title=false) {
			
			// Output storage
			$code = array();
			
			// Build header
			$code[] = $this->buildFormElementHeader($name);
			
			// Create fe_adminLib input name
			$feAdminName = 'FE[' . $this->extTable . '][' . $name . ']';
			
			$code[] = '<select name="' . $feAdminName . '">';
			
			// Empty option for title
			if ($title) {
				$code[] = '<option value="" selected>' . $title . '</option>';
			}
			
			// Create select options
			for($i = 0; $i < $options; $i++) {
				$code[] = '<option value="' . $i . '">' . $this->pi_getLL('pi_field_' . $name . '.I.' . $i) . '</option>';
			}
			
			// Select end
			$code[] = '</select>';
			
			// Return code
			return implode(chr(10),$code);
		}
		
		/**
		 * Returns a form element header.
		 * 
		 * This function creates the header of a form element for use with
		 * the fe_adminLib script. It also checks if the field is required in
		 * the plugin configuration array, and adds warning markers.
		 * 
		 * @param		$label				The name of the field
		 * @return		The header zone
		 */
		function buildFormElementHeader($label) {
			
			// Field is required
			$required = (strpos($this->conf['fe_adminLib.']['create.']['required'],$label) === false) ? false : ' <strong>*</strong>';
			
			// Header storage
			$header = array();
			
			// Main header
			$header[] = '<h3>' . $this->pi_getLL('pi_field_' . $label) . $required . '</h3>';
			
			// Required field alert
			if ($required) {
				$header[] = '<!--###SUB_REQUIRED_FIELD_' . $label . '###-->';
				$header[] = '<p><strong>' . $this->pi_getLL('pi_required_alert') . '</strong></p>';
				$header[] = '<!--###SUB_REQUIRED_FIELD_' . $label . '###-->';
			}
			
			// Return header
			return implode(chr(10),$header);
		}
		
		/**
		 * Returns processed value array.
		 * 
		 * This function process the values submited through fe_adminLib,
		 * 
		 * @param		$content			The value array
		 * @param		$conf				The TS setup
		 * @return		The value array.
		 */
		function updateArray($content,$conf) {
			
			// Checkbox type fields
			$checkBoxfields = array('evalfield','targetpublic_age','passation_method','traduction','traduction_standard');
			
			// Process checkbox type fields
			foreach($checkBoxfields as $field) {
				
				// Reset temp value
				$finalValue = 0;
				
				// Process separate values
				if (isset($content[$field])) {
					foreach($content[$field] as $value) {
						$finalValue += $value;
					}
				}
				
				// Unset field
				unset($content[$field]);
				
				// New value
				$content[$field] = $finalValue;
			}
			
			// Return the value array
			return $content;
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ergotheca/pi2/class.tx_ergotheca_pi2.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ergotheca/pi2/class.tx_ergotheca_pi2.php']);
	}
?>
