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
	 * Module 'Ergotheca Library' for the 'ergotheca' extension.
	 *
	 * @author		Jean-David Gadina <info@macmade.net>
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *      80:		function init
	 *      93:		function main
	 * 
	 * SECTION:		2 - MAIN
	 *     177:		function menuConfig
	 *     207:		function moduleContent
	 *     268:		function makeList($records,$tableName)
	 * 
	 * SECTION:		3 - UTILITIES
	 *     364:		function printContent
	 *     378:		function checkTCA($tableName)
	 *     393:		function getDeletedRecords($tableName)
	 *     426:		function processMultiple
	 *     507:		function processSingle
	 * 
	 *				TOTAL FUNCTIONS: 10
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require ('conf.php');
	require ($BACK_PATH . 'init.php');
	require ($BACK_PATH . 'template.php');
	$LANG->includeLLFile('EXT:ergotheca/mod1/locallang.php');
	require_once (PATH_t3lib . 'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	class tx_ergotheca_module1 extends t3lib_SCbase {
		var $pageinfo;
		
		
		
		
		
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
						function jumpToUrl(URL)	{
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
							top.fsMod.recentIds["web"] = ' . intval($this->id).';
						}
						//-->
					</script>
				';
				
				// Build current path
				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']) . '<br>' . $LANG->sL("LLL:EXT:lang/locallang_core.php:labels.path") . ": " . t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				
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
					$this->content .= $this->doc->spacer(20) . $this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}
				$this->content .= $this->doc->spacer(10);
			} else {
				$this->doc = t3lib_div::makeInstance('mediumDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->content .=$this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
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
		 * This function creates a menu with all the tables present in
		 * the Typo3 database.
		 * 
		 * @return		Nothing
		 */
		function menuConfig() {
			global $LANG;
			
			// Gets an array of all tables
			$result = mysql_list_tables(TYPO3_db);
			
			// Intialize menu array and adds title
			$this->MOD_MENU = Array (
				'function' => Array (
					'1' => $LANG->getLL('menu_func_1'),
					'2' => $LANG->getLL('menu_func_2'),
				),
			);
			
			// Creates menu
			parent::menuConfig();
		}
		
		/**
		 * Creates the module's menu.
		 * 
		 * This function creates a menu with all the tables present in
		 * the Typo3 database.
		 * 
		 * @return		Nothing
		 */
		function moduleContent() {
			global $LANG;
			
			// Variables definitions
			$table = 'tx_ergotheca_tools';
			$hidden = ($this->MOD_SETTINGS['function'] == 1) ? 1 : 0;
			
			// Get records
			$recordsArray = t3lib_BEfunc::getRecordsByField($table,'hidden',$hidden);
			
			// Add content
			if (is_array($recordsArray)) {
				$this->content .= $this->makeList($recordsArray,$table);
			} else {
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
		function makeList($records,$tableName) {
			global $LANG;
			$rowcount = 0;
			$colorcount = 0;
			
			// HTML code storage
			$htmlCode = array();
			
			// Start table
			$htmlCode[] = '<table id="recList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td align="left" valign="middle"></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('header_uid') . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('header_name') . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('header_authors') . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('header_testyear') . '</strong></td>';
			$htmlCode[] = '</tr>';
			
			// Build a row for each record
			foreach($records as $rec) {
				$colorcount = ($colorcount == 1) ? 0: 1;
				$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
				$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
				
				$htmlCode[] = '<tr' . $tr_params . '>';
				
				// Link parameters
				$editLinkParams = '&edit[' . $tableName . '][' . $rec['uid'] . ']=edit';
				$validateLinkparams = '&data[' . $tableName . ']['.$rec['uid'].'][hidden]=0';
				$deleteLinkParams = '&cmd[' . $tableName . ']['.$rec['uid'].'][delete]=1';


				// Links
				$htmlCode[] = '<td width="20" align="left" valign="middle">
					<a href="' . htmlspecialchars($GLOBALS['SOBE']->doc->issueCommand($validateLinkparams)) . '" onclick="' . htmlspecialchars("return confirm('" . $LANG->getLL('confirm_validate') . "');") . '"><img src="../gfx/icon_tx_ergotheca_mod1_unhide.gif" alt="" width="15" height="15" hspace="0" vspace="0" border="0" align="middle"></a>
					<br><br><a href="#" onclick="javascript:' . htmlspecialchars(t3lib_BEfunc::editOnClick($editLinkParams,$GLOBALS['BACK_PATH'])) . '"><img src="../gfx/icon_tx_ergotheca_mod1_edit.gif" alt="" width="15" height="15" hspace="0" vspace="0" border="0" align="middle"></a>
					<br><br><a href="' . htmlspecialchars($GLOBALS['SOBE']->doc->issueCommand($deleteLinkParams)) . '" onclick="' . htmlspecialchars("return confirm('" . $LANG->getLL('confirm_delete') . "');") . '"><img src="../gfx/icon_tx_ergotheca_mod1_delete.gif" alt="" width="15" height="15" hspace="0" vspace="0" border="0" align="middle"></a>
					</td>';
				
				// UID
				$htmlCode[] = '<td align="left" valign="middle">' . $rec['uid'] . '</td>';
				
				// Name
				$htmlCode[] = '<td align="left" valign="middle">' . implode('<br>',explode(';',nl2br($rec['name']))) . '</td>';
				
				// Authors
				$htmlCode[] = '<td align="left" valign="middle">' . implode('<br>',explode(';',nl2br($rec['authors']))) . '</td>';
				
				// Year
				$htmlCode[] = '<td align="left" valign="middle">' . $rec['testyear'] . '</td>';
				
				$htmlCode[] = '</tr>';
				$rowcount++;
			}
			
			// End table
			$htmlCode[] = '</table><br />';
			
			// Return the full code
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
