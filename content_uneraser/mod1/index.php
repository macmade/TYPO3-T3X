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
	 * Module 'Content UnEraser' for the 'content_uneraser' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		2.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *      92:		function init
	 *     113:		function main
	 * 
	 * SECTION:		2 - MAIN
	 *     205:		function menuConfig
	 *     233:		function moduleContent
	 *     264:		function tablesOverview
	 *     432:		function showTable($table)
	 * 
	 * SECTION:		3 - UTILITIES
	 *     664:		function printContent
	 *     678:		function getTables
	 *     728:		function flushTables($table)
	 *     763:		function recoverRecord($table,$record,$hidden='leave')
	 *     824:		function deleteRecord($table,$record)
	 *     866:		function showRecord($table,$uid)
	 * 
	 *				TOTAL FUNCTIONS: 12
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require('conf.php');
	require($BACK_PATH . 'init.php');
	require($BACK_PATH . 'template.php');
	$LANG->includeLLFile('EXT:content_uneraser/mod1/locallang.php');
	require_once(PATH_t3lib . 'class.t3lib_scbase.php');
	
	// Check user permissions
	$BE_USER->modAccess($MCONF,1);
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	class tx_contentuneraser_module1 extends t3lib_SCbase {
		
		var $pageinfo;
		
		// Version of the Developer API required
		var $apimacmade_version = 1.6;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - INIT
		 *
		 * Base module functions.
		 ***************************************************************/
		
		/**
		 * Initialization of the class
		 * 
		 * @return		Void
		 */
		function init() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Get available tables
			$this->getTables();
			
			// Initialization
			parent::init();
		}
		
		/**
		 * Creates the page
		 * 
		 * This function creates the basic page in which the module will
		 * take place.
		 * 
		 * @return		Void
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
				
				// Add javascript for row highlighting
				$this->doc->JScode .= chr(10) . '<script src="scripts.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>';
				
				$this->doc->postCode = '
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
				$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo['_thePath']) . '<br>' . $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path') . ': ' . t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				
				// Start page content
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				$this->content .= $this->doc->divider(5);
				
				
				// Render content:
				$this->moduleContent();
				
				
				// Adds shortcut
				if ($BE_USER->mayMakeShortcut()) {
					$this->content .= $this->doc->spacer(20) . $this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}
				
				$this->content .= $this->doc->spacer(10);
				
			} else {
				
				// No access
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
		 * Creates the module's menu
		 * 
		 * This function creates the module's menu.
		 * 
		 * @return		Void
		 */
		function menuConfig() {
			global $LANG;
			
			// Add static functions
			$this->MOD_MENU = Array (
				'function' => Array (
					'1' => $LANG->getLL('overview'),
				)
			);
			
			// Creates dynamic menu items
			foreach($this->tables as $key=>$value) {
				
				// Add table to menu
				$this->MOD_MENU['function'][$key] = '- ' . $value['title'];
			}
			
			// Creates menu
			parent::menuConfig();
		}
		
		/**
		 * Creates the module's content
		 * 
		 * This function creates the module's content.
		 * 
		 * @return		Void
		 */
		function moduleContent() {
			global $LANG;
			
			// Start section
			$this->content .= $this->doc->sectionBegin();
			
			// Check function
			switch($this->MOD_SETTINGS['function']) {
				
				// Overview
				case '1':
					$this->content .= $this->tablesOverview();
				break;
				
				default:
					$this->content .= $this->showTable($this->MOD_SETTINGS['function']);
				break;
			}
			
			// End section
			$this->content .= $this->doc->sectionEnd();
		}
		
		/**
		 * Creates a list of tables
		 * 
		 * This function creates an overview of the available Typo3 tables,
		 * with the functions to flush or show them.
		 * 
		 * @return		A list of tables
		 */
		function tablesOverview() {
			global $LANG;
			
			// Counters
			$rowcount = 0;
			$colorcount = 0;
			
			// Storage
			$htmlCode = array();
			
			// Title
			$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('overview'));
			
			// Check for selected table(s)
			if($tableList = t3lib_div::GPvar('tableList')) {
				
				// Try to flush tables
				if ($this->flushTables($tableList)) {
					
					// Divider
					$htmlCode[] = $this->doc->divider(5);
					
					// Write confirmation
					$htmlCode[] = '<strong>' . $LANG->getLL('flush_tables.success') . '</strong>';
					
					// Start list
					$htmlCode[] = '<ol>';
					
					// Write flushed tables
					foreach($tableList as $tableName) {
						
						// Add table
						$htmlCode[] = '<li>' . t3lib_iconWorks::getIconImage($tableName,array(),$GLOBALS['BACK_PATH']) . '&nbsp;<strong>' . $this->tables[$tableName]['title'] . '</strong> (' . $tableName . ')' . '</li>';
					}
					
					// End list
					$htmlCode[] = '</ol>';
					
					// Divider
					$htmlCode[] = $this->doc->divider(5);
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(5);
				}
			}
			
			// Check for direct flush
			if ($tableFlush = t3lib_div::GPvar('tableFlush')) {
				
				// Try to flush tables
				if ($this->flushTables($tableFlush)) {
					
					// Divider
					$htmlCode[] = $this->doc->divider(5);
					
					// Write confirmation
					$htmlCode[] = '<strong>' . $LANG->getLL('flush_tables.success') . '</strong>';
					
					// Start list
					$htmlCode[] = '<ol>';
					
					// Add table
					$htmlCode[] = '<li>' . t3lib_iconWorks::getIconImage($tableFlush,array(),$GLOBALS['BACK_PATH']) . '&nbsp;<strong>' . $this->tables[$tableFlush]['title'] . '</strong> (' . $tableFlush . ')' . '</li>';
					
					// End list
					$htmlCode[] = '</ol>';
					
					// Divider
					$htmlCode[] = $this->doc->divider(5);
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(5);
				}
			}
			
			// Check / Unckeck all
			$htmlCode[] = '<input type="button" value="' . $LANG->getLL('check_all') . '" onclick="checkBoxList(document.forms[0].list)">';
			
			// Flush tables
			$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('flush_tables') . '">';
			
			// Unset single flush if present
			$htmlCode[] = '<input type="hidden" name="tableFlush" value="0">';
			
			// Spacer
			$htmlCode[] = $this->doc->spacer(5);
			
			// Start table
			$htmlCode[] = '<table id="tableList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td align="left" valign="middle"></td>';
			$htmlCode[] = '<td align="left" valign="middle"></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.tables.title') . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.tables.name') . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.tables.records.total') . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.tables.records.deleted') . '</strong></td>';
			$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.tables.actions') . '</strong></td>';
			$htmlCode[] = '</tr>';
			
			// Build a row for each record
			foreach($this->tables as $key=>$value) {
				
				// Total number of records
				$totalRecords = $GLOBALS['TYPO3_DB']->sql_fetch_row($GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)',$key,''));
				
				// Number of deleted records
				$deletedRecords = $GLOBALS['TYPO3_DB']->sql_fetch_row($GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)',$key,$value['delete']));
				
				// Change row color
				$colorcount = ($colorcount == 1) ? 0: 1;
				$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
				
				// Actions storage
				$actions = array();
				
				// Erase
				$actions[] = '<a href="index.php?tableFlush=' . $key . '" title="' . $LANG->getLL('table.erase') . '"><img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/ol/minusbullet.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle"></a>';
				
				// Build row parameters
				$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
				
				// Start row
				$htmlCode[] = '<tr' . $tr_params . '>';
				
				// CheckBox
				$htmlCode[] = '<td width="20" align="left" valign="middle"><input type="checkbox" name="tableList[]" id="list" value="' . $key . '"></td>';
				
				// Table icon
				$htmlCode[] = '<td align="left" valign="middle">' . t3lib_iconWorks::getIconImage($key,array(),$GLOBALS['BACK_PATH']) . '</td>';
				
				// Table label w/ link
				$htmlCode[] = '<td align="left" valign="middle"><a href="index.php?SET[function]=' . $key . '">' . $value['title'] . '</a></td>';
				
				// Table name
				$htmlCode[] = '<td align="left" valign="middle">' . $key . '</td>';
				
				// Total records
				$htmlCode[] = '<td align="left" valign="middle">' . $totalRecords[0] . '</td>';
				
				// Deleted records
				$htmlCode[] = '<td align="left" valign="middle">' . $deletedRecords[0] . '</td>';
				
				// Actions
				$htmlCode[] = '<td align="left" valign="middle">' . implode(chr(10),$actions) . '</td>';
				
				// End row
				$htmlCode[] = '</tr>';
				
				// Increase row count
				$rowcount++;
			}
			
			// End table
			$htmlCode[] = '</table>';
			
			// Add content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Creates a list of records.
		 * 
		 * This function creates a list of the deleted records on
		 * a specific table, with the function to recover or delete them.
		 * 
		 * @param		$table				The name of the table to show
		 * @return		A list of records
		 */
		function showTable($table) {
			global $LANG;
			
			// Counters
			$rowcount = 0;
			$colorcount = 0;
			
			// Storage
			$htmlCode = array();
			
			// Title
			$htmlCode[] = $this->doc->sectionHeader($this->tables[$table]['title'] . ' (' . $table . ')');
			
			// Recover record
			if ($recover = t3lib_div::GPvar('recover')) {
				$htmlCode[] = $this->recoverRecord($table,$recover);
			}
			
			// Delete record
			if ($delete = t3lib_div::GPvar('delete')) {
				$htmlCode[] = $this->deleteRecord($table,$delete);
			}
			
			// Show record
			if ($show = t3lib_div::GPvar('show')) {
				$htmlCode[] = $this->showRecord($table,$show);
			}
			
			// Process multiple records
			if ($recList = t3lib_div::GPvar('recList')) {
				
				// Get options
				$action = t3lib_div::GPvar('action');
				$hidden = t3lib_div::GPvar('hidden');
				
				// Check action
				if ($action == 'recover') {
					
					// Recover records
					$htmlCode[] = $this->recoverRecord($table,$recList,$hidden);
					
				} else if ($action == 'delete') {
					
					// Delete records
					$htmlCode[] = $this->deleteRecord($table,$recList);
				}
			}
			
			// Select deleted records
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,$this->tables[$table]['delete'] . '=1');
			
			// Number of deleted records
			$deletedRecords = $GLOBALS['TYPO3_DB']->sql_fetch_row($GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)',$table,$this->tables[$table]['delete']));
			
			// Check for deleted records
			if ($deletedRecords[0]) {
				
				// Start fieldset
				$htmlCode[] = '<fieldset>';
				$htmlCode[] = '<legend align="top">' . $LANG->getLL('options') . '</legend>';
				
				// Options
				$htmlCode[] = '<input type="radio" name="hidden" value="hide" checked> ' . $LANG->getLL('options.hide') . '<br />';
				$htmlCode[] = '<input type="radio" name="hidden" value="show"> ' . $LANG->getLL('options.show') . '<br />';
				$htmlCode[] = '<input type="radio" name="hidden" value="leave"> ' . $LANG->getLL('options.leave') . '<br /><br />';
				$htmlCode[] = '<input type="radio" name="action" value="recover" checked> ' . $LANG->getLL('options.recover') . '<br />';
				$htmlCode[] = '<input type="radio" name="action" value="delete"> ' . $LANG->getLL('options.delete') . '<br /><br />';
				
				// Check / Unckeck all
				$htmlCode[] = '<input type="button" value="' . $LANG->getLL('check_all') . '" onclick="checkBoxList(document.forms[0].list)">';
				
				// Submit
				$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('process_records') . '">';
				
				// Unset single processing if present
				$htmlCode[] = '<input type="hidden" name="recover" value="0">';
				$htmlCode[] = '<input type="hidden" name="delete" value="0">';
				$htmlCode[] = '<input type="hidden" name="show" value="0">';
				
				// End fieldset
				$htmlCode[] = '</fieldset>';
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(5);
				
				// Start table
				$htmlCode[] = '<table id="recList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
				$htmlCode[] = '<tr>';
				$htmlCode[] = '<td align="left" valign="middle"></td>';
				$htmlCode[] = '<td align="left" valign="middle"></td>';
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.records.uid') . '</strong></td>';
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.records.label') . '</strong></td>';
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.records.pid') . '</strong></td>';
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.records.path') . '</strong></td>';
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.records.crdate') . '</strong></td>';
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.records.tstamp') . '</strong></td>';
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.records.cruser_id') . '</strong></td>';
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $LANG->getLL('headers.records.actions') . '</strong></td>';
				$htmlCode[] = '</tr>';
				
				// Build a row for each record
				while($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Set deleted flag to 0 in record array to get the correct icon
					$rec[$this->tables[$table]['delete']] = 0;
					
					// Record icon
					$icon = t3lib_iconWorks::getIconImage($table,$rec,$GLOBALS['BACK_PATH']);
					
					// Creation date
					$crdate = ($rec[$this->tables[$table]['crdate']]) ? date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$rec[$this->tables[$table]['crdate']]) : '-';
					
					// Modification date
					$tstamp = ($rec[$this->tables[$table]['tstamp']]) ? date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$rec[$this->tables[$table]['tstamp']]) : '-';
					
					// Check for a user field
					if ($this->tables[$table]['cruser_id']) {
						
						// User
						$user = t3lib_BEfunc::getRecord('be_users',$rec[$this->tables[$table]['cruser_id']]);
						
						// User name
						$username = $user['username'];
						
						// Check for a real name
						if ($user['realName']) {
							
							// Add realname
							$username = $user['realName'] . ' (' . $username . ')';
						}
					}
					
					// Check for a valid user
					if (!$username) {
						
						// No user
						$username = '-';
					}
					
					// Label
					$label = $this->api->div_crop(t3lib_BEfunc::getRecordTitle($table,$rec),30);
					
					// Actions storage
					$actions = array();
					
					// Recover
					$actions[] = '<a href="index.php?SET[function]=' . $table . '&amp;recover=' . $rec['uid'] . '" title="' . $LANG->getLL('record.recover') . '"><img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/ol/plusbullet.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle"></a>';
					
					// Erase
					$actions[] = '<a href="index.php?SET[function]=' . $table . '&amp;delete=' . $rec['uid'] . '" title="' . $LANG->getLL('record.erase') . '"><img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/ol/minusbullet.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle"></a>';
					
					// Show
					$actions[] = '<a href="index.php?SET[function]=' . $table . '&amp;show=' . $rec['uid'] . '" title="' . $LANG->getLL('record.show') . '"><img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/zoom2.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle"></a>';
					
					// Change row color
					$colorcount = ($colorcount == 1) ? 0: 1;
					$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
					
					// Build row parameters
					$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
					
					// Start row
					$htmlCode[] = '<tr' . $tr_params . '>';
					
					// CheckBox
					$htmlCode[] = '<td width="20" align="left" valign="middle"><input type="checkbox" name="recList[]" id="list" value="' . $rec['uid'] . '"></td>';
					
					// Table icon
					$htmlCode[] = '<td align="left" valign="middle">' . $icon . '</td>';
					
					// UID
					$htmlCode[] = '<td align="left" valign="middle">' . $rec['uid'] . '</td>';
					
					// Label
					$htmlCode[] = '<td align="left" valign="middle"><strong>' . $label . '</strong></td>';
					
					// Path
					$htmlCode[] = '<td align="left" valign="middle">' . $rec['pid'] . '</td>';
					
					// Path
					$htmlCode[] = '<td align="left" valign="middle">' . t3lib_BEfunc::getRecordPath($rec['pid'],'',0,0) . '</td>';
					
					// Date created
					$htmlCode[] = '<td align="left" valign="middle">' . $crdate . '</td>';
					
					// Date modified
					$htmlCode[] = '<td align="left" valign="middle">' . $tstamp . '</td>';
					
					// User
					$htmlCode[] = '<td align="left" valign="middle">' . $username . '</td>';
					
					// Actions
					$htmlCode[] = '<td align="left" valign="middle">' . implode(chr(10),$actions) . '</td>';
					
					// End row
					$htmlCode[] = '</tr>';
					
					// Increase row count
					$rowcount++;
				}
				
				// End table
				$htmlCode[] = '</table>';
			
			} else {
				
				// No deleted records
				$htmlCode[] = $LANG->getLL('norecords');
			}
			
			// Add content
			return implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 8 - UTILITIES
		 *
		 * General purpose functions.
		 ***************************************************************/
		
		/**
		 * Prints the page
		 * 
		 * This function closes the page, and writes the final
		 * rendered content.
		 * 
		 * @return		Void
		 */
		function printContent() {
			$this->content .= $this->doc->endPage();
			echo($this->content);
		}
		
		/**
		 * Gets available table
		 * 
		 * This function is used to store the available table descriptions
		 * in a class array ($this->tables). It memorize the table name as
		 * the array key, and some of the TCA properties in a subarray.
		 * 
		 * @return		Void
		 */
		function getTables() {
			global $LANG,$TCA;
			
			// Class array for table storage
			$this->tables = array();
			
			// Traverse TCA array
			while (list($table) = each($TCA)) {
				
				// Load TCA for current table
				t3lib_div::loadTCA($table);
				
				// Table configuration
				$config = $TCA[$table];
				
				// Check if table has a delete flag
				if ($config['ctrl']['delete']) {
					
					// Memorize table informations
					$this->tables[$table] = array(
						'title' => $LANG->sL($config['ctrl']['title']),
						'delete' => $config['ctrl']['delete'],
						'label' => $config['ctrl']['label'],
						'label_alt' => $config['ctrl']['label_alt'],
						'label_alt_force' => $config['ctrl']['label_alt_force'],
						'tstamp' => $config['ctrl']['tstamp'],
						'crdate' => $config['ctrl']['crdate'],
						'cruser_id' => $config['ctrl']['cruser_id'],
						'default_sortby' => $config['ctrl']['default_sortby'],
						'disabled' => $config['ctrl']['enablecolumns']['disabled'],
						'showRecordFieldList' => $config['interface']['showRecordFieldList'],
					);
				}
			}
			
			// Sort tables array
			asort($this->tables);
			
			// DEBUG ONLY - Show tables
			#$this->api->debug($this->tables,'TABLES');
		}
		
		/**
		 * Flush table(s)
		 * 
		 * This function erases all the deleted records on the given tables.
		 * 
		 * @param		$table				Either a tablename or an array containing tablenames
		 * @return		True if the flush was successful
		 */
		function flushTables($table) {
			
			// Check for multiple tables
			if (is_array($table)) {
				
				// Process tables
				foreach($table as $tableName) {
					
					// Erase deleted records
					$GLOBALS['TYPO3_DB']->exec_DELETEquery($tableName,$this->tables[$tableName]['delete'] . '=1');
				}
				
				// Confirm flush
				return true;
				
			} else {
				
				// Erase deleted records
				$GLOBALS['TYPO3_DB']->exec_DELETEquery($table,$this->tables[$table]['delete'] . '=1');
				
				// Confirm flush
				return true;
			}
		}
		
		/**
		 * Recover record(s)
		 * 
		 * This function is used to recover records by resetting their delete flag.
		 * 
		 * @param		$table				The table name
		 * @param		$record			Either a record UID or an array containing record UIDs
		 * @param		$hidden				Also set the hidden flag if available (can be 'leave', 'show' or 'hide')
		 * @return		A confirmation message
		 */
		function recoverRecord($table,$record,$hidden='leave') {
			global $LANG;
			
			// Reset delete flag
			$update = array($this->tables[$table]['delete']=>0);
			
			// Check for multiple records
			if (is_array($record)) {
				
				// Check hidden flag
				if ($this->tables[$table]['disabled']) {
					
					// Set hidden flag
					switch($hidden) {
						
						// Show
						case 'show':
							$update[$this->tables[$table]['disabled']] = 0;
						break;
						
						// Hide
						case 'hide':
							$update[$this->tables[$table]['disabled']] = 1;
						break;
					}
				}
				
				// Process records
				foreach($record as $uid) {
					
					// Recover record
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid=' . $uid,$update);
				}
			} else {
				
				// Recover record
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,'uid=' . $record,$update);
			}
			
			// Storage
			$htmlCode = array();
			
			// Confirmation
			$htmlCode[] = '<strong>' . $LANG->getLL('recover.success') . '</strong>';
			
			// Spacer
			$htmlCode[] = $this->doc->spacer(5);
			
			// Return comfirmation
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Erase record(s)
		 * 
		 * This function is used to erase records from a table.
		 * 
		 * @param		$table				The table name
		 * @param		$record			Either a record UID or an array containing record UIDs
		 * @return		A confirmation message
		 */
		function deleteRecord($table,$record) {
			global $LANG;
			
			// Check for multiple records
			if (is_array($record)) {
				
				// Process records
				foreach($record as $uid) {
					
					// Delete record
					$GLOBALS['TYPO3_DB']->exec_DELETEquery($table,'uid=' . $uid);
				}
			} else {
				
				// Delete record
				$GLOBALS['TYPO3_DB']->exec_DELETEquery($table,'uid=' . $record);
			}
			
			// Storage
			$htmlCode = array();
			
			// Confirmation
			$htmlCode[] = '<strong>' . $LANG->getLL('delete.success') . '</strong>';
			
			// Spacer
			$htmlCode[] = $this->doc->spacer(5);
			
			// Return comfirmation
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Show a record
		 * 
		 * This function is used to show a record. Only the fields configured to be shown
		 * in the backend interface are displayed. The values are also processed, to be
		 * human-readable.
		 * 
		 * @param		$table				The table name
		 * @param		$uid				The record UID
		 * @return		An HTML table containing informations about the record
		 */
		function showRecord($table,$uid) {
			global $LANG,$TCA;
			
			// Storage
			$htmlCode = array();
			
			// Divider
			$htmlCode[] = $this->doc->divider(5);
			
			// Select record
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'uid=' . $uid);
			
			// Get record
			$rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			
			// Set deleted flag to 0 in record array to get the correct icon
			$rec[$this->tables[$table]['delete']] = 0;
			
			// Record icon
			$icon = t3lib_iconWorks::getIconImage($table,$rec,$GLOBALS['BACK_PATH']);
			
			// Label
			$label = $this->api->div_crop(t3lib_BEfunc::getRecordTitle($table,$rec),30);
			
			// Start table
			$htmlCode[] = '<table id="showRec" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			
			// Record title
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td colspan="2" align="left" valign="middle">' . $icon . '&nbsp;<strong>' . $label . ' (' . $uid . ')</strong></td>';
			$htmlCode[] = '</tr>';
			
			// Get display fields
			$showRecordFieldList = explode(',',$this->tables[$table]['showRecordFieldList']);
			
			// Process each field
			foreach($showRecordFieldList as $field) {
				
				// Field value
				$value = t3lib_BEfunc::getProcessedValue($table,$field,$rec[$field]);
				
				// Field name
				$fieldName = $LANG->sL($TCA[$table]['columns'][$field]['label']);
				
				// Start row
				$htmlCode[] = '<tr>';
				
				// Field name
				$htmlCode[] = '<td bgcolor="' . $this->doc->bgColor5 . '" width="20%" align="left" valign="middle"><strong>' . $fieldName . '</strong></td>';
				
				// Field value
				$htmlCode[] = '<td bgcolor="' . $this->doc->bgColor4 . '" width="80%" align="left" valign="middle">' . $value . '</td>';
				
				// End row
				$htmlCode[] = '</tr>';
			}
			
			// End table
			$htmlCode[] = '</table>';
			
			// Divider
			$htmlCode[] = $this->doc->divider(5);
			
			// Spacer
			$htmlCode[] = $this->doc->spacer(5);
			
			// Return record view
			return implode(chr(10),$htmlCode);
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/content_uneraser/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/content_uneraser/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_contentuneraser_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	// Start module
	$SOBE->main();
	$SOBE->printContent();
?>
