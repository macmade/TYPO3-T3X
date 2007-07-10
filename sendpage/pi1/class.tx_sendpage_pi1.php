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
	 * Plugin 'Drop-Down sitemap' for the 'dropdown_sitemap' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *        :		function main($content,$conf)
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_sendpage_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_sendpage_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_sendpage_pi1.php';
		
		// The extension key
		var $extKey = 'sendpage';
		
		// Upload directory
		var $uploadDir = 'uploads/tx_sendpage/';
		
		// Version of the Developer API required
		var $apimacmade_version = 2.2;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin 'tx_dropdownsitemap_pi1', and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 */
		function main($content,$conf) {
			
			// Disable cache
			$GLOBALS['TSFE']->set_no_cache();
			
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
			
			// Set default plugin variables
			$this->pi_setPiVarDefaults();
			
			// Load locallang file
			$this->pi_loadLL();
			
			// Storage
			$content = array();
			
			// No URL -> Get current URL
			if (!array_key_exists('url',$this->piVars)) {
				$this->piVars['url'] = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
			}
			
			// No function -> Send
			if (!array_key_exists('function',$this->piVars)) {
				$this->piVars['function'] = 'send';
			}
			
			// Check TS setup
			if (empty($this->conf['pid'])) {
				
				// TS not configured
				$content[] = '<strong>' . $this->pi_getLL('noconfig') . '</strong>';
				
			} else {
				
				// TS configured - Begin form
				$content[] = '<form action="' . t3lib_div::getIndpEnv('REQUEST_URI') . '" name="' . $this->prefixId . '-form" enctype="' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'] . '" method="post">';
				
				// Add content
				$content[] = $this->makeForm();
				
				// End form
				$content[] = '</form>';
			}
			
			// Return content
			return $this->pi_wrapInBaseClass(implode(chr(10),$content));
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
				'templateFile' => 'sTMPL:template_file',
				'textAreaRows' => 'sTMPL:text_rows',
				'textAreaCols' => 'sTMPL:text_cols',
				'textInputSize' => 'sTMPL:input_size',
				'cropUrl' => 'sTMPL:crop_url',
				'webmasterName' => 'sMAILER:webmaster_name',
				'webmasterEmail' => 'sMAILER:webmaster_email',
				'bugReports' => 'sMAILER:bug_reports',
			);
			
			// Override TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'SendPage: configuration array');
		}
		
		/**
		 * Create form.
		 * 
		 * This function is used to create the sendpage form.
		 * 
		 * @return		The complete form
		 */
		function makeForm() {
			
			// Check template file (TS or Flex)
			$templateFile = ($this->pi_getFFvalue($this->piFlexForm,'template_file','sTMPL') == '') ? $this->conf['templateFile'] : $this->uploadFolder . $this->conf['templateFile'];
			
			// Template load and init.
			$this->api->fe_initTemplate($templateFile);
			$templateMarkers = array();
			
			// Checking for POST vars
			$postVars = (array_key_exists('submit',$this->piVars)) ? $this->sendMail() : '';
			
			// Overwriting template markers
			$templateMarkers['###HEADER###'] = '<h2>' . $this->pi_getLL('url_func_' . $this->piVars['function']) . '</h2>';
			$templateMarkers['###URL###'] = $this->api->fe_makeStyledContent('div','url',$this->api->div_crop($this->piVars['url'],$this->conf['cropUrl']));
			$templateMarkers['###SUBMIT_CONFIRM###'] = $this->api->fe_makeStyledContent('div','submitConfirm',$postVars);
			
			// Detect function
			if ($this->piVars['function'] == 'send') {
				
				// Send page
				$templateMarkers['###TO_NAME###'] = $this->createField('text','to_name',$this->pi_getLL('to_name'),$this->piVars['to_name']);
				$templateMarkers['###TO_EMAIL###'] = $this->createField('text','to_email',$this->pi_getLL('to_email'),$this->piVars['to_email']);
			
			} else {
				
				// Get user in charge
				$user = $this->getUserInCharge();
				
				// Bug report
				$templateMarkers['###TO_NAME###'] = $this->createField('hidden','to_name',$this->pi_getLL('to_name'),$user['name'],$user['name']);
				$templateMarkers['###TO_EMAIL###'] = $this->createField('hidden','to_email',$this->pi_getLL('to_email'),$user['email'],$this->cObj->typoLink($user['email'],array('parameter'=>$user['email'])));
				
			}
			
			// Common fields
			$templateMarkers['###FROM_NAME###'] = $this->createField('text','from_name',$this->pi_getLL('from_name'),$this->piVars['from_name']);
			$templateMarkers['###FROM_EMAIL###'] = $this->createField('text','from_email',$this->pi_getLL('from_email'),$this->piVars['from_email']);
			$templateMarkers['###MESSAGE###'] = $this->createField('textarea','message',$this->pi_getLL('message'),$this->piVars['message']);
			$templateMarkers['###SUBMIT###'] = '<input type="submit" name="' . $this->prefixId . '[submit]" value="' . $this->pi_getLL('send') . '" />';
			
			// Template rendering
			$content = $this->api->fe_renderTemplate($templateMarkers,'###MAIN###');
			
			// Return content
			return $content;
		}
		
		/**
		 * 
		 */
		function getUserInCharge() {
			
			// Storage
			$user = array();
			
			// Page creator ID
			$cruser_id = $GLOBALS['TSFE']->page['cruser_id'];
			
			// Get BE user row
			$be_user = $this->pi_getRecord('be_users',$cruser_id);
			
			// Check user informations
			if (empty($be_user['email'])) {
				
				// Send to webmaster
				$this->conf['bugReports'] = 2;
			}
			
			// Check plugin configuration
			switch($this->conf['bugReports']) {
				
				// Webmaster
				case '2':
					
					// Set fields
					$user['name'] = $this->conf['webmasterName'];
					$user['email'] = $this->conf['webmasterEmail'];
				break;
				
				// Webmaster & page creator
				case '3':
					
					// Set fields
					$user['name'] = ($be_user['realName']) ? $be_user['realName'] : $be_user['username'];
					$user['email'] = $be_user['email'];
				break;
				
				// Page creator
				default:
					
					// Set fields
					$user['name'] = ($be_user['realName']) ? $be_user['realName'] : $be_user['username'];
					$user['email'] = $be_user['email'];
				break;
			}
			
			// Return user
			return $user;
		}
		
		/**
		 * 
		 */
		function sendMail() {
			
			// Check fields
			if ($this->checkFields()) {
				
				// Plain mail content
				$content = $this->createMailContent();
				
				// Send the mail
				$this->cObj->sendNotifyEmail($content, $this->piVars['to_email'], '', $this->piVars['from_email'],  $this->piVars['from_name']);
				
				// Return confirmation
				return '<strong>' . str_replace('###RECIPIENT###',$this->piVars['to_name'],$this->pi_getLL('message_sent')) . '</strong>';
				
			} else {
				
				// Some fields are missing
				return '<strong>' . $this->pi_getLL('fields_error') . '</strong>';
			}
		}
		
		/**
		 * 
		 */
		function createMailContent() {
			
			// Template load and init.
			$templateContent = $this->initTemplate();
			$templateMarkers = array();
			
			// Overwriting template markers
			$templateMarkers['###TO###'] = $this->piVars['to_name'];
			$templateMarkers['###FROM###'] = $this->piVars['from_name'];
			$templateMarkers['###MESSAGE###'] = $this->piVars['message'];
			$templateMarkers['###URL###'] = $this->piVars['url'];
			
			// Template to use
			$template = ($this->piVars['function'] == 'send') ? '###MAIL_SEND###' : '###MAIL_BUG###';
			
			// Template rendering
			$content = $this->renderTemplate($templateContent,$templateMarkers,$template);
			
			// Return content
			return $content;
		}
		
		/**
		 * 
		 */
		function checkFields() {
			
			// Fields to check for
			$fieldList = 'to_name,to_email,from_name,from_email,message';
			
			// Array
			$fieldArray = explode(',',$fieldList);
			
			// Check each field for empty value
			foreach($fieldArray as $field) {
				
				if (empty($this->piVars[$field])) {
					
					// Field is missing
					$status = false;
					
					// Exit
					break;
					
				} else {
					
					// Field is ok
					$status = true;
				}
			}
			
			// Check valid emails
			if (!t3lib_div::validEmail($this->piVars['to_email']) || !t3lib_div::validEmail($this->piVars['from_email'])) {
				
				// Invalid email
				$status = false;
			}
			
			// Return fields status
			return $status;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 2 - MISC / UTILS
		 *
		 * General functions for the plugin.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function createField($type,$name,$label,$value=false,$hiddenLabel=false) {
			
			// Storage
			$htmlCode = array();
			
			// Label
			$htmlCode[] = $label . '<br />';
			
			// Field
			switch ($type) {
				
				// Text Area
				case 'textarea':
					$htmlCode[] = '<textarea name="' . $this->prefixId . '[' . $name . ']" rows="' . $this->conf['textAreaRows'] . '" cols="' . $this->conf['textAreaCols'] . '">' . $value . '</textarea>';
				break;
				
				// Text
				case 'text':
					$htmlCode[] = '<input type="text" name="' . $this->prefixId . '[' . $name . ']" value="' . $value . '" size="' . $this->conf['textInputSize'] . '" />';
				break;
				
				// Hidden
				case 'hidden':
					$htmlCode[] = $hiddenLabel . '<input type="hidden" name="' . $this->prefixId . '[' . $name . ']" value="' . $value . '" />';
				break;
			}
			
			// Return code
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function makeLink($content,$conf) {
			
			if (empty($conf['pid'])) {
				
				// Load locallang file
				$this->pi_loadLL();
				
				// TS not configured
				$link = 'javascript:window.alert(\'' . $this->pi_getLL('noconfig') . '\');';
				
			} else {
				
				// TS configured - Common link parameters
				$params = array(
					$this->prefixId . '[function]' => $conf['function'],
					$this->prefixId . '[url]' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),
					$this->prefixId . '[pid]' => $GLOBALS['TSFE']->id,
				);
				
				// Create link
				$link = $this->pi_getPageLink($conf['pid'],'',$params);
			}
			
			// Return link
			return $link;
		}
		
		/**
		 * 
		 */
		function getRootlineField($field) {
			
			// Check current page settings
			if (empty($GLOBALS['TSFE']->page[$field])) {
				
				// No user defined -> Try to get a recursive user
				foreach($GLOBALS['TSFE']->config['rootLine'] as $topPage) {
					
					// Recursive user found
					if (!empty($topPage[$field])) {
						$userid = $topPage[$field];
					}
				}
			} else {
				
				// Page specific user
				$userid = $GLOBALS['TSFE']->page[$field];
			}
			
			// Return field
			return $userid;
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sendpage/pi1/class.tx_sendpage_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sendpage/pi1/class.tx_sendpage_pi1.php']);
	}
?>
