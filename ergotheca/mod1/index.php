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
	 * Module 'OASIS validation' for the 'ergotheca' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *        :		function init
	 *        :		function main
	 * 
	 * SECTION:		2 - MAIN
	 *        :		function menuConfig
	 *        :		function moduleContent
	 *        :		function makeList($records)
	 *        :		function searchBiblio($uid)
	 *        :		function listBiblio($records)
	 *        :		function addBiblio($uid,$biblioList)
	 *        :		function buildBiblioFieldSelect
	 * 
	 * SECTION:		3 - UTILITIES
	 *        :		function printContent
	 *        :		function buildRecordIcons($uid)
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require('conf.php');
	require($BACK_PATH . 'init.php');
	require($BACK_PATH . 'template.php');
	$LANG->includeLLFile('EXT:ergotheca/mod1/locallang.php');
	require_once(PATH_t3lib . 'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	// API class
	require_once(t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	class tx_ergotheca_module1 extends t3lib_SCbase {
		
		// Class variables
		var $pageinfo;
		
		// API version
		var $apimacmade_version = 1.1;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - INIT
		 *
		 * Base module functions.
		 ***************************************************************/
		
		/**
		 * Initialization of the class.
		 * 
		 * @return		Nothing
		 */
		function init() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			
			// New instance of the API
			$this->api = new tx_apimacmade($this);
			
			// Init
			parent::init();
		}
		
		/**
		 * Creates the page.
		 * 
		 * This function creates the basic page in which the module will
		 * take place.
		 * 
		 * @return		Nothing
		 */
		function main() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
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
				$this->doc->postCode='
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
				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']) . '<br>' . $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path') . ': ' . t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				
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
				
				$this->content .= $this->doc->spacer(10);
			} else {
				
				// No access
				$this->doc = t3lib_div::makeInstance('smallDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $LANG->getLL('noaccess');
				$this->content .= $this->doc->spacer(10);
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Main module functions.
		 ***************************************************************/
		
		/**
		 * Creates the module's menu.
		 * 
		 * This function creates the module's menu.
		 * 
		 * @return		Nothing
		 */
		function menuConfig() {
			global $LANG;
			
			// Menu items
			$this->MOD_MENU = Array (
				'function' => Array (
					'1' => $LANG->getLL('modmenu_func1'),
					'2' => $LANG->getLL('modmenu_func2'),
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
		 * @return		Nothing
		 */
		function moduleContent() {
			global $LANG;
			
			// Working table
			$this->extTable = 'tx_ergotheca_tools';
			
			// Check current menu function ans set variables
			switch ($this->MOD_SETTINGS['function']) {
				
				// Hidden records
				case 1:
					
					// Additionnal MySQL WHERE clause
					$addWhere = ' AND hidden=1';
					
					// Icons to display
					$this->iconList = array('show','unhide','edit','delete');
				break;
				
				// Validated records
				case 2:
					
					// Try to get biblio var
					$biblio = t3lib_div::_GP('biblio');
					
					// Additionnal MySQL WHERE clause
					$addWhere = ($biblio) ? ' AND uid=' . $biblio : '';
					
					// Icons to display
					$this->iconList = array('show','edit','biblio','delete');
				break;
			}
			
			// Get records
			$records = t3lib_BEfunc::getRecordsByField($this->extTable,'pid',$this->id,$addWhere,'','name');
			
			// List records
			if (is_array($records)) {
				$this->content .= $this->makeList($records);
				
				// Add biblio module
				if ($biblio) {
					$this->content .= $this->searchBiblio($biblio);
				}
			}
			else {
				$this->content .= $LANG->getLL('norecord');
			}
		}
		
		/**
		 * Makes a list of records.
		 * 
		 * This function creates a table containing all the requested
		 * records.
		 * 
		 * @param		$records			An array of records
		 * @return		A table with all the records
		 */
		function makeList($records) {
			global $LANG;
			$rowcount = 0;
			$colorcount = 0;
			
			// Gets Typo3 settings for dates
			$ddmmyy = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'];
			$hhmm = $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'];
			
			// HTML code storage
			$htmlCode = array();
			
			// Start table
			$htmlCode[] = '<table id="recList" border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			
			// Table headers
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td align="left" valign="top"></td>';
			$htmlCode[] = '<td width="30%" align="left" valign="top"><strong>' . $LANG->getLL('header_name') . '</strong></td>';
			$htmlCode[] = '<td width="30%" align="left" valign="top"><strong>' . $LANG->getLL('header_authors') . '</strong></td>';
			$htmlCode[] = '<td width="20%" align="left" valign="top"><strong>' . $LANG->getLL('header_crdate') . '</strong></td>';
			$htmlCode[] = '</tr>';
			
			// Build a row for each record
			foreach($records as $rec) {
				
				// TR & color parameters
				$colorcount = ($colorcount == 1) ? 0: 1;
				$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
				$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\',\'0\');" bgcolor="' . $color . '"';
				
				$htmlCode[] = '<tr' . $tr_params . '>';
				
				// Icons
				$htmlCode[] = '<td align="left" valign="top">' . $this->buildRecordIcons($rec['uid']) . '</td>';
				
				// Name
				$htmlCode[] = '<td width="30%" align="left" valign="top">'. $this->api->div_str2list($rec['name'],chr(10),1,'ol') .'</td>';
				
				// Authors
				$htmlCode[] = '<td width="30%" align="left" valign="top">' . $this->api->div_str2list($rec['authors'],chr(10),1,'ol') .'</a></td>';
				
				// Creation date
				$htmlCode[] = '<td width="20%" align="left" valign="top">'. date($ddmmyy,$rec['crdate']) . ' - ' . date($hhmm,$rec['crdate']) .'</td>';
				
				
				$htmlCode[] = '</tr>';
				$rowcount++;
			}
			
			// End table
			$htmlCode[] = '</table>';
			
			// Return the full code
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function searchBiblio($uid) {
			global $LANG;
			
			// HTML code storage
			$htmlCode = array();
			
			// Divider
			$htmlCode[] = $this->doc->divider(5);
			
			// Start fieldset
			$htmlCode[] = '<fieldset>';
			
			// Legend
			$htmlCode[] = '<legend align="top">' . $LANG->getLL('biblio_search') . '</legend>';
			
			// Search
			$htmlCode[] = $LANG->getLL('biblio_search_word') . ' <input type="text" name="sword" size="20" value="' . t3lib_div::_GP('sword') . '"> ';
			$htmlCode[] = $LANG->getLL('biblio_search_field') . ' ' . $this->buildBiblioFieldSelect() . ' ';
			$htmlCode[] = $LANG->getLL('biblio_search_pid') . ' <input type="text" name="pid" size="4" value="' . t3lib_div::_GP('pid') . '">';
			
			// Spacer
			$htmlCode[] .= $this->doc->spacer(10);
			
			// Submit
			$htmlCode[] = '<input type="submit" name="submit_search" value="' . $LANG->getLL('biblio_search_submit') . '">';
			
			// End fieldset
			$htmlCode[] = '</fieldset>';
			
			// Search or add action
			if (t3lib_div::_GP('submit_add') && is_array(t3lib_div::_GP('uidList'))) {
				
				// Spacer
				$htmlCode[] .= $this->doc->spacer(10);
				
				// Add selected values
				$htmlCode[] = $this->addBiblio($uid,t3lib_div::_GP('uidList'));
				
			} else if (t3lib_div::_GP('submit_search') && t3lib_div::_GP('sword') && t3lib_div::_GP('pid')) {
				
				// Spacer
				$htmlCode[] .= $this->doc->spacer(10);
				
				// Bibliography table
				$biblioTable = 'tx_endnote_db';
				
				// Search query
				$addWhere = ' AND ' . t3lib_div::_GP('field') . ' LIKE "%' . t3lib_div::_GP('sword') . '%"';
				
				// Get records
				$records = t3lib_BEfunc::getRecordsByField($biblioTable,'pid',t3lib_div::_GP('pid'),$addWhere,'','title');
				
				if (is_array($records)) {
					
					// Create list
					$htmlCode[] = $this->listBiblio($records);
					
				} else {
					
					// No records
					$htmlCode[] = $LANG->getLL('biblio_search_empty');
				}
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function listBiblio($records) {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Select all
			$htmlCode[] = '<input type="button" value="' . $LANG->getLL('biblio_add_select') . '" onclick="checkBoxList(document.forms[0].list)">';
			
			// Submit
			$htmlCode[] = '<input type="submit" name="submit_add" value="' . $LANG->getLL('biblio_add_submit') . '">';
			
			// Spacer
			$htmlCode[] .= $this->doc->spacer(10);
			
			$rowcount = 0;
			$colorcount = 0;
			
			// Start table
			$htmlCode[] = '<table id="biblioList" border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			
			// Table headers
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td align="left" valign="top"></td>';
			$htmlCode[] = '<td width="50%" align="left" valign="top"><strong>' . $LANG->getLL('biblio_header_title') . '</strong></td>';
			$htmlCode[] = '<td width="45%" align="left" valign="top"><strong>' . $LANG->getLL('biblio_header_custom1') . '</strong></td>';
			$htmlCode[] = '</tr>';
			
			// Build a row for each record
			foreach($records as $rec) {
				
				// TR & color parameters
				$colorcount = ($colorcount == 1) ? 0: 1;
				$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
				$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\',\'1\');" bgcolor="' . $color . '"';
				
				$htmlCode[] = '<tr' . $tr_params . '>';
				
				// Icons
				$htmlCode[] = '<td align="left" valign="top"><input type="checkbox" id="list" name="uidList[]" value="' . $rec['uid'] . '"></td>';
				
				// Name
				$htmlCode[] = '<td width="50%" align="left" valign="top">'. $rec['title'] .'</td>';
				
				// Creation date
				$htmlCode[] = '<td width="45%" align="left" valign="top">'. $this->api->div_str2list($rec['custom1'],';',1,'ol') .'</td>';
				
				
				$htmlCode[] = '</tr>';
				$rowcount++;
			}
			
			// End table
			$htmlCode[] = '</table>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function addBiblio($uid,$biblioList) {
			global $LANG;
			
			// Get tool
			$tool = t3lib_BEfunc::getRecord($this->extTable,$uid,'bibliography');
			
			// Existing bibliographic references
			$references = explode(',',$tool['bibliography']);
			
			// Merge arrays
			$updateArray = array_unique(array_merge($references,$biblioList));
			
			// Update record
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTable,'uid=' . $uid,array('bibliography'=>implode(',',$updateArray)));
			
			// Return confirmation
			return $LANG->getLL('biblio_add_success');
		}
		
		/**
		 * 
		 */
		function buildBiblioFieldSelect() {
			
			// Storage
			$htmlCode = array();
			
			// Start select
			$htmlCode[] = '<select name="field">';
			
			// Fields
			$fields = $GLOBALS['TYPO3_DB']->admin_get_fields('tx_endnote_db');
			
			// Existing variable
			$fieldVar = t3lib_div::_GP('field');
			
			// Exclude fields
			$excludeFields = array('uid','pid','tstamp','crdate','cruser_id','deleted','hidden','fe_group','type');
			
			// Process each field
			foreach($fields  as $key=>$value) {
				
				// Check exclude fields
				if (!in_array($key,$excludeFields)) {
					
					// Selected value
					$selected = ($key == $fieldVar) ? ' selected' : '';
					
					// Option
					$htmlCode[] = '<option value="' . $key . '"' . $selected . '>' . strtoupper($key) . '</option>';
				}
			}
			
			// End select
			$htmlCode[] = '</select>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - UTILITIES
		 *
		 * General purpose functions.
		 ***************************************************************/
		
		/**
		 * Prints the page.
		 * 
		 * This function closes the page, and writes the final
		 * rendered content.
		 * 
		 * @return		Nothing
		 */
		function printContent() {
			$this->content .= $this->doc->endPage();
			echo $this->content;
		}
		
		/**
		 * Build action icons.
		 * 
		 * This function creates the icons to show, unhide, edit
		 * and delete a record.
		 * 
		 * @param		$uid				The record uid
		 * @return		The record icons
		 */
		function buildRecordIcons($uid) {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Link parameters
			$editLinkParams = '&edit[' . $this->extTable . '][' . $uid . ']=edit';
			$validateLinkParams = '&data[' . $this->extTable . ']['. $uid .'][hidden]=0';
			$deleteLinkParams = '&cmd[' . $this->extTable . ']['. $uid .'][delete]=1';
			$biblioLinkParams = '&id=' . $this->id . '&biblio=' . $uid;
			
			// Icons & links definitions
			$icons = array(
				'show' => array(
					'file' => '../gfx/icon_tx_ergotheca_mod1_show.gif',
					'link' => '<a href="#" title="' . $LANG->getLL('title_show') . '" onclick="top.launchView(\'' . $this->extTable . '\', \'' . $uid . '\'); return false;">',
				),
				'unhide' => array(
					'file' => '../gfx/icon_tx_ergotheca_mod1_unhide.gif',
					'link' => '<a href="' . htmlspecialchars($GLOBALS['SOBE']->doc->issueCommand($validateLinkParams)) . '" title="' . $LANG->getLL('title_unhide') . '" onclick="' . htmlspecialchars('return confirm(\'' . $GLOBALS['LANG']->getLL('confirm_validate') . '\');') . '">',
				),
				'edit' => array(
					'file' => '../gfx/icon_tx_ergotheca_mod1_edit.gif',
					'link' => '<a href="#" title="' . $LANG->getLL('title_edit') . '" onclick="javascript:' . htmlspecialchars(t3lib_BEfunc::editOnClick($editLinkParams,$GLOBALS['BACK_PATH'])) . '">',
				),
				'biblio' => array(
					'file' => '../gfx/icon_tx_ergotheca_mod1_biblio.gif',
					'link' => '<a href="index.php?' . $biblioLinkParams . '" title="' . $LANG->getLL('title_biblio') . '" >',
				),
				'delete' => array(
					'file' => '../gfx/icon_tx_ergotheca_mod1_delete.gif',
					'link' => '<a href="' . htmlspecialchars($GLOBALS['SOBE']->doc->issueCommand($deleteLinkParams)) . '" title="' . $LANG->getLL('title_delete') . '" onclick="' . htmlspecialchars('return confirm(\'' . $GLOBALS['LANG']->getLL('confirm_delete') . '\');') . '">',
				),
			);
			
			// Build icons
			foreach($icons as $key=>$icon) {
				
				// Icon must be displayed
				if (in_array($key,$this->iconList)) {
					$htmlCode[] =  $icon['link'] . '<img src="' . $icon['file'] . '" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></a>';
				}
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ergotheca/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ergotheca/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_ergotheca_module1');
	$SOBE->init();

	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	$SOBE->main();
	$SOBE->printContent();
?>
