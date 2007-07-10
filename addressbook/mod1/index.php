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
	 * Module 'Address Book' for the 'addressbook' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *      85:		function init
	 *      99:		function main
	 * 
	 * SECTION:		2 - MAIN
	 *     190:		function menuConfig
	 *     211:		function moduleContent
	 * 
	 * SECTION:		3 - OVERVIEW
	 *     261:		function buildFormList
	 *     376:		function showDS($dsArray)
	 * 
	 * SECTION:		4 - UTILITIES
	 *     467:		function printContent
	 * 
	 *				TOTAL FUNCTIONS: 7
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require ('conf.php');
	require ($BACK_PATH.'init.php');
	require ($BACK_PATH.'template.php');
	$LANG->includeLLFile('EXT:addressbook/mod1/locallang.php');
	require_once (PATH_t3lib.'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	// vCard export class
	require_once(t3lib_extMgm::extPath('addressbook').'class.tx_addressbook_vcardexport.php');
	
	class tx_addressbook_module1 extends t3lib_SCbase {
		
		var $pageinfo;
		
		// Version of the Developer API required
		var $apimacmade_version = 2.1;
		
		// Extension tables
		var $extTables = array(
			'groups' => 'tx_addressbook_groups',
			'addresses' => 'tx_addressbook_addresses',
		);
		
		
		
		/***************************************************************
		 * SECTION 1 - INIT
		 *
		 * Base module functions.
		 ***************************************************************/
		
		/**
		 * Initialization of the class.
		 * 
		 * @return		Void
		 */
		function init() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			
			parent::init();
		}
		
		/**
		 * Creates the page.
		 * 
		 * This function creates the basic page in which the module will
		 * take place.
		 * 
		 * @return		Void
		 */
		function main() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			
			// New instance of the Developer API
			$this->api = new tx_apimacmade($this);
			
			// Access check
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;
			
			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {
				
				// Draw the header
				$this->doc = t3lib_div::makeInstance('bigDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->doc->form = '<form action="" method="POST" enctype="' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'] . '">';
				
				// JavaScript
				$this->doc->JScode = '
					<script type="text/javascript" language="Javascript" charset="iso-8859-1">
						<!--
						script_ended = 0;
						function jumpToUrl(URL) {
							document.location = URL;
						}
						//-->
					</script>
					<script src="scripts.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
				';
				
				$this->doc->postCode = '
					<script type="text/javascript" language="Javascript" charset="iso-8859-1">
						<!--
						script_ended = 1;
						if (top.fsMod) {
							top.fsMod.recentIds["web"] = ' . intval($this->id) . ';
						}
						//-->
					</script>
				';
				
				// Build current path
				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				
				// Start page content
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				#$this->content .= $this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				$this->content .= $this->doc->section('',$this->doc->funcMenu($headerSection,''));
				$this->content .= $this->doc->divider(5);
				
				if (t3lib_div::_GP('action') == 'export') {
					
					// Export contacts
					$this->exportContacts();
					
				} else {
					
					// Render content
					$this->moduleContent();
				}
				// Adds shortcut
				if ($BE_USER->mayMakeShortcut()) {
					$this->content .= $this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}
				
				// End spacer
				$this->content .= $this->doc->spacer(10);
				
			} else {
				
				// No access or no ID
				$this->doc = t3lib_div::makeInstance('bigDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $this->doc->spacer(10);
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 2 - MAIN
		 *
		 * Main module functions.
		 ***************************************************************/
		
		/**
		 * Creates the module's menu.
		 * 
		 * This function creates the module's menu.
		 * 
		 * @return		Void
		 */
		function menuConfig() {
			global $LANG;
			
			// Menu items
			$this->MOD_MENU = Array (
				'function' => Array (
					'0' => $LANG->getLL('funcmenu.I.0'),
				)
			);
			
			// Creates menu
			parent::menuConfig();
		}
		
		/**
		 * Creates the module's content.
		 * 
		 * This function creates the module's content.
		 * 
		 * @return		Void
		 */
		function moduleContent() {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Check page ID
			if ($this->id) {
				
				// File upload
				$htmlCode[] = $this->buildImportForm();
				
			} else {
				
				// No page selected
				$htmlCode[] = $LANG->getLL('error.nopid');
			}
			
			// Place content
			$this->content .= implode(chr(10),$htmlCode);
		}
		
		
		
		
		

		/***************************************************************
		 * SECTION 3 - EXPORT
		 *
		 * EXPORT functions.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function exportContacts() {
			
			// Incoming variables
			$table = t3lib_div::_GP('table');
			$uid = t3lib_div::_GP('uid');
			$returnUrl = t3lib_div::_GP('returnUrl');
			
			// New instance of vCard helper class
			$vCard = t3lib_div::makeInstance('tx_addressbook_vCardExport');
				
			// Force BE charset
			$forceCharset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];
			
			// Charset to use
			$charset = ($forceCharset) ? $forceCharset : 'iso-8859-1';
			
			// Check table
			if ($table == $this->extTables['groups']) {
				
				
				
			} else if ($table == $this->extTables['addresses']) {
				
				// Get user record
				$rec = t3lib_BEfunc::getRecord($table,$uid);
				
				// Convert user array
				$user = $vCard->mapUserArray($rec);
				
				// Create vCard
				$vCard = $this->api->div_vCardCreate($user,'3.0',$charset);
				
				// Export vCard
				$this->api->div_output($vCard,'text/x-vCard','t3_vcard_export.vcf','attachment',$charset);
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 4 - IMPORT
		 *
		 * IMPORT functions.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function buildImportForm() {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Start section
			$htmlCode[] = $this->doc->sectionBegin();
			
			if ($_FILES['importFile']['tmp_name']) {
				
				// Temporary file
				$uploadedTempFile = t3lib_div::upload_to_tempfile($_FILES['importFile']['tmp_name']);
				
				// Parse file
				$vCard = $this->api->div_vCardFileParse($uploadedTempFile);
				
				// Delete temporary file
				t3lib_div::unlink_tempfile($uploadedTempFile);
				
				// Check vCard array
				if (is_array($vCard) && count($vCard)) {
					
					// Process vCard array
					$this->importCards($vCard);
					
				} else {
					
					// Import error
					$htmlCode[] = '<strong><span class="typo3-red">' . $LANG->getLL('error.import') . '</span></strong>';
				}
			}
			
			// Header
			$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('headers.import'));
			
			// Start div
			$htmlCode[] = '<div style="border: dashed 1px #666666; background-color: ' . $this->doc->bgColor4 . '; padding: 5px;">';
			
			// Title
			$htmlCode[] = $LANG->getLL('import.file') . '&nbsp;';
			
			// File input
			$htmlCode[] = '<input name="importFile" type="file" />&nbsp;&nbsp;';
			
			// Title
			$htmlCode[] = $LANG->getLL('import.group') . '&nbsp;';
			
			// Group select
			$htmlCode[] = $this->buildGroupSelect() . '&nbsp;&nbsp;';
			
			// Submit
			$htmlCode[] = '<input name="import" type="submit" value="Import" />';
			
			// End div
			$htmlCode[] = '</div>';
			
			// End section
			$htmlCode[] = $this->doc->sectionEnd();
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function buildGroupSelect() {
			
			// Storage
			$htmlCode = array();
			
			// Get available groups
			$groups = t3lib_BEfunc::getRecordsByField($this->extTables['groups'],'pid',$this->id);
			
			// Check array
			if (is_array($groups)) {
				
				// Start select
				$htmlCode[] = '<select name="importGroup">';
				
				// Process each group
				foreach($groups as $row) {
					
					// Build option tag
					$htmlCode[] = '<option value="' . $row['uid'] . '">' . $row['title'] . '</option>';
				}
				
				// End select
				$htmlCode[] = '</select>';
			}
			
			// Return select
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function importCards($vCard) {
			
			// Counter
			$count = 0;
			
			// Process vCards
			foreach($vCard as $card) {
				
				// Storage
				$data = array();
				
				// File extension for pictures
				$fileExt = '.jpg';
				
				// Path to the upload directory
				$uploadDir = PATH_site . 'uploads/tx_addressbook/';
				
				// PID
				$data['pid'] = $this->id;
				
				// Type
				$data['type'] = ($card['iscompany']) ? 1 : 0;
				
				// Add normal fields from vCard
				$data['firstname'] = $card['firstname'];
				$data['lastname'] = $card['name'];
				$data['nickname'] = $card['username'];
				$data['jobtitle'] = $card['title'];
				$data['department'] = $card['department'];
				$data['company'] = $card['company'];
				$data['homepage'] = $card['www'];
				$data['birthday'] = $card['birthday'];
				
				// Notes
				$data['notes'] = str_replace('\n',chr(10),$card['note']);
				
				// Check for a picture
				if ($card['image'] && @is_file($card['image'])) {
					
					// Filename
					$fileName = uniqid(rand());
					
					// Move file
					t3lib_div::upload_copy_move($card['image'],$uploadDir . $fileName . $fileExt);
					
					// Add file reference
					$data['picture'] = $fileName . $fileExt;
				}
				
				// Check for email
				if (is_array($card['email']) && count($card['email'])) {
					
					// Add email flex
					$data['email'] = $this->sectionDS($card['email'],'fields_email','email');
				}
				
				// Check for phone
				if (is_array($card['phone']) && count($card['phone'])) {
					
					// Add email flex
					$data['phone'] = $this->sectionDS($card['phone'],'fields_phone','phone');
				}
				
				// Check for messenger
				if (is_array($card['messenger']) && count($card['messenger'])) {
					
					// Add email flex
					$data['messenger'] = $this->sectionDS($card['messenger'],'fields_messenger','messenger');
				}
				
				// Check for address
				if (is_array($card['address']) && count($card['address'])) {
					
					// Add email flex
					$data['address'] = $this->sectionDS($card['address'],'fields_address','address');
				}
				
				// Insert data
				if ($GLOBALS['TYPO3_DB']->exec_INSERTquery($this->extTables['addresses'],$data)) {
					
					// Increase row counter
					$count++;
				}
			}
			
			// Return number of inserted rows
			return $count;
		}
		
		/**
		 * 
		 */
		function sectionDS($input,$fieldKey,$sectionKey) {
			
			// Empty flexform array
			$flex = array('data' => array('sDEF' => array('lDEF' => array($fieldKey => array('el' => array())))));
			
			// Counter for items
			$i = 1;
			
			// Process input array
			foreach($input as $key=>$value) {
				
				// Temporary array
				$section = array($sectionKey => array('el' => array()));
				
				// Process value array
				foreach($value as $k => $v) {
					
					// Temporary array
					$tmp = array('vDEF' => $v);
					
					// Add fields to section
					$section[$sectionKey]['el'][$k] = $tmp;
				}
				
				// Add section to flex
				$flex['data']['sDEF']['lDEF'][$fieldKey]['el'][] = $section;
				
				// Increase counter
				$i++;
			}
			
			// Return XML content
			return t3lib_div::array2xml($flex,'',0,'T3FlexForms');
		}
		
		
		
		
		/***************************************************************
		 * SECTION 4 - UTILITIES
		 *
		 * General purpose functions.
		 ***************************************************************/
		
		/**
		 * Prints the page.
		 * 
		 * This function closes the page, and writes the final
		 * rendered content.
		 * 
		 * @return		Void
		 */
		function printContent() {
			$this->content .= $this->doc->endPage();
			echo $this->content;
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/addressbook/mod1/index.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/addressbook/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_addressbook_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	$SOBE->main();
	$SOBE->printContent();
?>
