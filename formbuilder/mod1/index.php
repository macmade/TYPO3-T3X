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
	 * Module 'Forms' for the 'formbuilder' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
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
	$LANG->includeLLFile('EXT:formbuilder/mod1/locallang.php');
	require_once (PATH_t3lib.'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_formbuilder_module1 extends t3lib_SCbase {
		
		var $pageinfo;
		
		// Version of the Developer API required
		var $apimacmade_version = 1.5;
		
		
		
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
				$this->doc->form = '<form action="" method="POST">';
				
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
				$this->content .= $this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				$this->content .= $this->doc->divider(5);
				
				// Render content
				$this->moduleContent();
				
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
			
			// Storage
			$htmlCode = array();
			
			// Start section
			$htmlCode[] = $this->doc->sectionBegin();
			
			// Development notice
			$htmlCode[] =  '<div style="text-align: center; padding: 5px; border-style: solid; border-width: 1px; border-left-color:#FFFFFF; border-top-color:#FFFFFF; border-right-color:#BFBC99; border-bottom-color:#BFBC99; background: #FFFBCC;">';
			$htmlCode[] = $this->doc->sectionHeader('Important note');
			$htmlCode[] = '<p>This module will allow you to display, edit and manage the data submissions of the forms you have access to.</p>';
			$htmlCode[] = '<p><strong>The module is under development. Only a few features are available for now.</strong></p>';
			$htmlCode[] =  '</div style="">';
			
			// End section
			$htmlCode[] = $this->doc->sectionEnd();
			$htmlCode[] = $this->doc->spacer(5);
			
			// Functions
			switch($this->MOD_SETTINGS['function']) {
				
				// Forms overview
				case '0':
					$htmlCode[] = $this->buildFormList();
				break;
			}
			
			// Place content
			$this->content .= implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - OVERVIEW
		 *
		 * Overview functions.
		 ***************************************************************/
		
		/**
		 * Creates a form list.
		 * 
		 * This function creates a list of all the forms available
		 * on the current page.
		 * 
		 * @return		Void
		 */
		function buildFormList() {
			global $LANG;
			
			// Database tabe
			$table = 'tx_formbuilder_datastructure';
			
			// Counters
			$rowcount = 0;
			$colorcount = 0;
			
			// Storage
			$htmlCode = array();
			
			// Show XML DS?
			if ($showXML = t3lib_div::_GP('showXML')) {
				
				// Get XML DS
				$row = t3lib_BEfunc::getRecord($table,$showXML);
				
				// XML -> PHP array
				$dsArray = $this->api->div_xml2array($row['xmlds'],0);
				
				// Start section
				$htmlCode[] = $this->doc->sectionBegin();
				
				// Title
				$htmlCode[] = $this->doc->sectionHeader('XML Datastructure (#' . $row['uid'] . ' - ' . $row['title'] . ')');
				
				// Display DS
				$htmlCode[] = $this->showDS($dsArray);
				
				// End section
				$htmlCode[] = $this->doc->sectionEnd();
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(5);
			}
			
			// Start section
			$htmlCode[] = $this->doc->sectionBegin();
			
			// Title
			$htmlCode[] = $this->doc->sectionHeader('Available forms');
			
			// Get records
			$records = t3lib_BEfunc::getRecordsByField($table,'pid',$this->id);
			
			// Check for records
			if (is_array($records)) {
				
				// Start table
				$htmlCode[] = '<table id="recList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
				$htmlCode[] = '<tr>';
				$htmlCode[] = '<td width="15%" align="left" valign="middle"><strong>Actions</strong></td>';
				$htmlCode[] = '<td width="10%" align="left" valign="middle"><strong>UID</strong></td>';
				$htmlCode[] = '<td width="50%" align="left" valign="middle"><strong>Title</strong></td>';
				$htmlCode[] = '<td width="10%" align="left" valign="middle"><strong>Submissions</strong></td>';
				$htmlCode[] = '<td width="10%" align="left" valign="middle"><strong>New submissions</strong></td>';
				$htmlCode[] = '<td width="10%" align="left" valign="middle"><strong>Structure</strong></td>';
				$htmlCode[] = '</tr>';
				
				// Build a row for each record
				foreach($records as $rec) {
					
					// Color parameters
					$colorcount = ($colorcount == 1) ? 0: 1;
					$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
					$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
					
					// New line
					$htmlCode[] = '<tr' . $tr_params . '>';
					
					// Columns
					$htmlCode[] = '<td width="10%" align="left" valign="middle">' . $this->api->be_buildRecordIcons('show,edit,delete',$table,$rec['uid']) . '</td>';
					$htmlCode[] = '<td width="10%" align="left" valign="middle">' . $rec['uid'] . '</td>';
					$htmlCode[] = '<td width="50%" align="left" valign="middle"><b>' . $rec['title'] . '</b></td>';
					$htmlCode[] = '<td width="10%" align="left" valign="middle">N/A</td>';
					$htmlCode[] = '<td width="10%" align="left" valign="middle">N/A</td>';
					$htmlCode[] = '<td width="10%" align="left" valign="middle"><a href="' . t3lib_div::linkThisScript(array('showXML'=>$rec['uid'])) . '">Show XML DS</a></td>';
					
					// End line
					$htmlCode[] = '</tr>';
					
					// Increase counter
					$rowcount++;
				}
				
				// End table
				$htmlCode[] = '</table>';
				
			} else {
				
				// No form in the page
				$htmlCode[] = '<strong>There is no form to display in the current page</strong>';
				
			}
			
			// Return the full code
			return implode(chr(10),$htmlCode);
			
			// End section
			$htmlCode[] = $this->doc->sectionEnd();
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Prints a form data structure.
		 * 
		 * This function prints the datastructure array of a form.
		 * 
		 * @param		$dsArray			The DS array of the form
		 * @return		Void
		 */
		function showDS($dsArray) {
			
			// Array keys to exclude
			$excludeKeys = array('T3FlexForms','data','sDEF','lDEF','vDEF','el','fields');
			
			// Storage
			$result = array();
			
			// Check for an array
			if (is_array($dsArray)) {
				
				// Number of elements in the array
				$endrow = count($dsArray);
				
				// Couters
				$count = 0;
				$table = 0;
				
				// Display content
				foreach ($dsArray as $key=>$val) {
					
					// Increase counter
					$count++;
					
					// Check current key
					if(in_array($key,$excludeKeys)) {
						
						// Don't dipsplay - 
						$result[] = $this->showDS($val);
						
					} else {
						
						// Start <table> tag?
						if (!$table) {
							
							$result[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
							$table = 1;
						}
						
						// Start row
						$result[] = '<tr><td align="left" valign="middle" bgcolor="' . $this->doc->bgColor5 . '">' . $key . '</td><td align="left" valign="middle" bgcolor="' . $this->doc->bgColor4 . '">';
						
						// Check array value
						if (is_array($val)) {
							
							// Result is array
							$result[] =  $this->showDS($val);
						
						} else {
						
							// Result can be displayed directly
							$result[] = (strlen($val) > 50) ? htmlentities(substr($val,0,50)) . $this->writeHTML(' [...]','span',array('color: green','font-weight: bold')) : htmlentities($val);
						}
						
						// End row
						$result[] = '</td></tr>';
						
						// End <table> tag?
						if($count == $endrow) {
							$result[] = '</table>';
						}
					}
				}
			} else {
				
				// Value only
				$result[] = $dsArray;
			}
			
			// Return content
			return implode(chr(10),$result);
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
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formbuilder/mod1/index.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formbuilder/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_formbuilder_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);
	
	$SOBE->main();
	$SOBE->printContent();
?>
