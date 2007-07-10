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
	 * Module 'DB Sync' for the 'dbsync' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *     103:		function init
	 *     124:		function main
	 * 
	 * SECTION:		2 - MAIN
	 *     215:		function menuConfig
	 *     237:		function moduleContent
	 * 
	 * SECTION:		3 - TABLES
	 *     281:		function t3TablesSync
	 *     339:		function t3TablesList
	 *     499:		function t3TablesShowFields($src,$dest)
	 *     646:		function t3TablesExecSync
	 * 
	 * SECTION:		4 - DB
	 *     757:		function t3DBSync
	 *     807:		function t3DBDestroySessionData
	 *     827:		function t3DBGetSessionData
	 *     846:		function t3DBConnectStatus
	 *     904:		function t3DBListTables
	 *    1075:		function t3DBExecQueries($database)
	 *    1140:		function t3DBConnect
	 * 
	 * SECTION:		5 - UTILITIES
	 *    1237:		function printContent
	 *    1251:		function getTables
	 *    1287:		function getTableRecords($table)
	 * 
	 *				TOTAL FUNCTIONS: 18
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require('conf.php');
	require($BACK_PATH . 'init.php');
	require($BACK_PATH . 'template.php');
	$LANG->includeLLFile('EXT:dbsync/mod1/locallang.php');
	require_once(PATH_t3lib . 'class.t3lib_scbase.php');
	require_once(PATH_t3lib . 'class.t3lib_pagetree.php');
	
	// Check user permissions
	$BE_USER->modAccess($MCONF,1);
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	class tx_dbsync_module1 extends t3lib_SCbase {
		
		var $pageinfo;
		
		// Version of the Developer API required
		var $apimacmade_version = 1.7;
		
		
		
		
		
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
				$this->doc = t3lib_div::makeInstance('mediumDoc');
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
				';
				
				// Add javascripts for row highlighting
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
				$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo['_thePath']) . '<br />' . $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path') . ': ' . t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				
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
					'1' => $LANG->getLL('sync.t3tables'),
					'2' => $LANG->getLL('sync.t3db'),
				)
			);
			
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
				
				// Sync databases
				case '2':
					$this->content .= $this->t3DBSync();
				break;
				
				// Sync tables
				default:
					$this->content .= $this->t3TablesSync();
				break;
			}
			
			// End section
			$this->content .= $this->doc->sectionEnd();
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - TABLES
		 *
		 * Typo3 tables synchronisation functions.
		 ***************************************************************/
		
		/**
		 * Table synchronisation
		 * 
		 * This is the main function for the synchronisation between local
		 * tables.
		 * 
		 * @return		The complete module's function
		 * @see			t3TablesList
		 * @see			t3TablesShowFields
		 */
		function t3TablesSync() {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Title
			$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('sync.t3tables'));
			
			// Check action
			if (t3lib_div::_GP('action') == 't3tables.map' || t3lib_div::_GP('action') == 't3tables.sync') {
				
				// POST variables
				$tables = array(
					'source' => t3lib_div::_GP('source'),
					'destination' => t3lib_div::_GP('destination'),
				);
				
				// Check variables
				if (empty($tables['source']) || empty($tables['destination'])) {
					
					// Missing table
					$htmlCode[] = '<strong>' . $LANG->getLL('errors.t3tables.missing') . '</strong>';
					$htmlCode[] = $this->doc->divider(5);
					$htmlCode[] = $this->t3TablesList();
					
				} else if ($tables['source'] == $tables['destination']) {
					
					// Same table
					$htmlCode[] = '<strong>' . $LANG->getLL('errors.t3tables.same') . '</strong>';
					$htmlCode[] = $this->doc->divider(5);
					$htmlCode[] = $this->t3TablesList();
					
				} else {
					
					// Show fields
					$htmlCode[] = $this->doc->divider(5);
					$htmlCode[] = $this->t3TablesShowFields($tables['source'],$tables['destination']);
				}
			} else {
				
				// No action
				$htmlCode[] = $this->doc->divider(5);
				$htmlCode[] = $this->t3TablesList();
			}
			
			// Add content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * List local tables
		 * 
		 * This function creates a list of all the available local tables,
		 * with the option to synchronize them.
		 * 
		 * @return		A list of the local tables
		 */
		function t3TablesList() {
			global $LANG;
			
			// Counters
			$rowcount = 0;
			$colorcount = 0;
			
			// Storage
			$htmlCode = array();
			
			// Start DIV
			$htmlCode[] = '<div align="center">';
			
			// Submit
			$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('submit.t3tables.map') . '">';
			
			// Action
			$htmlCode[] = '<input type="hidden" value="t3tables.map" name="action">';
			
			// End DIV
			$htmlCode[] = '</div>';
			
			// Spacer
			$htmlCode[] = $this->doc->spacer(5);
			
			// Start container table
			$htmlCode[] = '<table border="0" width="100%" cellspacing="5" cellpadding="0" align="center">';
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td width="50%" align="left" valign="top">';
			
			// Start table
			$htmlCode[] = '<table id="tableListSource" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td align="center" valign="middle" colspan="3"><strong>' . $LANG->getLL('headers.t3tables.source') . '</strong></td>';
			$htmlCode[] = '</tr>';
			
			
			// POST variables
			$tables = array(
				'source' => t3lib_div::_GP('source'),
				'destination' => t3lib_div::_GP('destination'),
			);
			
			// Build a row for each record
			foreach($this->tables as $key=>$value) {
				
				// Checked parameter
				$checked = ($tables['source'] == $key) ? ' checked' : '';
				
				// Change row color
				$colorcount = ($colorcount == 1) ? 0: 1;
				$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
				
				// Build row parameters
				$tr_params = ' onmouseover="setRowColor(this,\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $color . '\');" bgcolor="' . $color . '" onclick="enableRadio(\'source_' . $rowcount . '\');"';
				
				// Start row
				$htmlCode[] = '<tr' . $tr_params . '>';
				
				// Table label and title
				$htmlCode[] = '<td align="right" valign="middle"><strong>' . $value['title'] . '</strong><br />(' . $key . ')</td>';
				
				// Table icon
				$htmlCode[] = '<td align="center" valign="middle">' . t3lib_iconWorks::getIconImage($key,array(),$GLOBALS['BACK_PATH']) . '</td>';
				
				// Radio
				$htmlCode[] = '<td width="20" align="left" valign="middle"><input type="radio" name="source" id="source_' .  $rowcount. '" value="' . $key . '"' . $checked . '></td>';
				
				// End row
				$htmlCode[] = '</tr>';
				
				// Increase row count
				$rowcount++;
			}
			
			// End table
			$htmlCode[] = '</table>';
			
			// Separation
			$htmlCode[] = '</td>';
			$htmlCode[] = '<td width="50%" align="left" valign="top">';
			
			// Counters
			$rowcount = 0;
			$colorcount = 0;
			
			// Start table
			$htmlCode[] = '<table id="tableListDestination" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td align="center" valign="middle" colspan="3"><strong>' . $LANG->getLL('headers.t3tables.destination') . '</strong></td>';
			$htmlCode[] = '</tr>';
			
			// Build a row for each record
			foreach($this->tables as $key=>$value) {
				
				// Checked parameter
				$checked = ($tables['destination'] == $key) ? ' checked' : '';
				
				// Change row color
				$colorcount = ($colorcount == 1) ? 0: 1;
				$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
				
				// Build row parameters
				$tr_params = ' onmouseover="setRowColor(this,\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $color . '\');" bgcolor="' . $color . '" onclick="enableRadio(\'destination_' . $rowcount . '\');"';
				
				// Start row
				$htmlCode[] = '<tr' . $tr_params . '>';
				
				// Radio
				$htmlCode[] = '<td width="20" align="left" valign="middle"><input type="radio" name="destination" id="destination_' .  $rowcount. '" value="' . $key . '"' . $checked . '></td>';
				
				// Table icon
				$htmlCode[] = '<td align="center" valign="middle">' . t3lib_iconWorks::getIconImage($key,array(),$GLOBALS['BACK_PATH']) . '</td>';
				
				// Table label and title
				$htmlCode[] = '<td align="left" valign="middle"><strong>' . $value['title'] . '</strong><br />(' . $key . ')</td>';
				
				// End row
				$htmlCode[] = '</tr>';
				
				// Increase row count
				$rowcount++;
			}
				
			// End table
			$htmlCode[] = '</table>';
			
			// End container table
			$htmlCode[] = '</td>';
			$htmlCode[] = '</tr>';
			$htmlCode[] = '</table>';
			
			// Spacer
			$htmlCode[] = $this->doc->spacer(5);
			
			// Start DIV
			$htmlCode[] = '<div align="center">';
			
			// Submit
			$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('submit.t3tables.map') . '">';
			
			// End DIV
			$htmlCode[] = '</div>';
			
			// Add content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Map table fields
		 * 
		 * This function creates a mapping interface to help synchronizing two
		 * Typo3 tables.
		 * 
		 * @param		$src		The source table
		 * @param		$dest		The destination table
		 * @return		The mapping view between two tables
		 * @see			t3TablesExecSync
		 * @see			getTableRecords
		 */
		function t3TablesShowFields($src,$dest) {
			global $LANG, $TCA;
			
			// Storage
			$htmlCode = array();
			
			// Check action
			if (t3lib_div::_GP('action') == 't3tables.sync') {
				
				// Try to sync
				$result = $this->t3TablesExecSync();
				
				// Check result
				if ($result > 0) {
					
					// Success
					$htmlCode[] = '<strong>' . $LANG->getLL('LGL.insert') . '</strong>: ' . $result;
					
				} else {
					
					// Error
					$htmlCode[] = '<strong>' . $LANG->getLL('errors.t3tables.nosync') . '</strong>';
				}
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(5);
			}
			
			// Start fieldset
			$htmlCode[] = '<fieldset>';
			
			// Title
			$htmlCode[] = '<legend>' . $LANG->getLL('LGL.options') . '</legend>';
			
			// Add records
			$htmlCode[] = '<input name="method" type="radio" value="0" checked> ' . $LANG->getLL('options.t3tables.method.0') . '<br />';
			
			// Empty destination table
			$htmlCode[] = '<input name="method" type="radio" value="1"> ' . $LANG->getLL('options.t3tables.method.1') . '<br /><br />';
			
			// Store in page
			$htmlCode[] = $LANG->getLL('options.t3tables.storage') . ':<br />' . $this->api->be_buildPageTreeSelect('pid') . '<br /><br />';
			
			// Only include selected records
			$htmlCode[] = $LANG->getLL('options.t3tables.selected') . ':<br />' . $this->getTableRecords($src) . '<br /><br />';
			
			// Do not include deleted records
			$htmlCode[] = '<input name="deleted" type="checkbox" value="1" checked> ' . $LANG->getLL('options.t3tables.deleted') . '<br /><br />';
			
			// Submit
			$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('submit.t3tables.sync') . '">';
			
			// Hidden inputs
			$htmlCode[] = '<input type="hidden" value="t3tables.sync" name="action">';
			$htmlCode[] = '<input type="hidden" value="' . $src . '" name="source">';
			$htmlCode[] = '<input type="hidden" value="' . $dest . '" name="destination">';
			
			// End fieldset
			$htmlCode[] = '</fieldset>';
			
			// Spacer
			$htmlCode[] = $this->doc->spacer(5);
			
			// Start table
			$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			$htmlCode[] = '<tr>';
			$htmlCode[] = '<td width="50%" align="center" valign="middle"><strong>' . $LANG->getLL('headers.t3tables.destination') . '</strong><br />' . t3lib_iconWorks::getIconImage($dest,array(),$GLOBALS['BACK_PATH']) . ' ' . $this->tables[$dest]['title'] . ' (' . $dest . ')</td>';
			$htmlCode[] = '<td width="50%" align="center" valign="middle"><strong>' . $LANG->getLL('headers.t3tables.source') . '</strong><br />' . t3lib_iconWorks::getIconImage($src,array(),$GLOBALS['BACK_PATH']) . ' ' . $this->tables[$src]['title'] . ' (' . $src . ')</td>';
			$htmlCode[] = '</tr>';
			
			// Process each destination field
			foreach($TCA[$dest]['columns'] as $key=>$value) {
				
				// Label
				$label = $LANG->sL($value['label']);
				
				// Field name
				$fieldName = ($label) ? $label : '[' . $key . ']';
				
				// Storage
				$select = array();
				
				// Start mapping select
				$select[] = '<select size="1" name="' . $key . '">';
				$select[] = '<option value="0">' . $LANG->getLL('LGL.none') . '</option>';
				
				// Build mapping select
				foreach($TCA[$src]['columns'] as $srcKey=>$srcValue) {
					
					// Label
					$srcLabel = $LANG->sL($srcValue['label']);
					
					// Field name
					$srcFieldName = ($srcLabel) ? $srcLabel : '[' . $srcKey . ']';
					
					// Try to map fields
					if ($key == $srcKey) {
						
						// Same field name
						$selected = ' selected';
						
					} else if ($fieldName == $srcFieldName) {
						
						// Same field label
						$selected = ' selected';
						
					} else {
						
						// Not selected
						$selected = '';
					}
					
					// Option tag
					$select[] = '<option value="' . $srcKey . '"' . $selected . '>' . $srcFieldName . ' (' . $LANG->getLL('types.' . $srcValue['config']['type']) . ')</option>';
				}
				
				// End mapping select
				$select[] = '</select>';
				
				// Start row
				$htmlCode[] = '<tr>';
				
				// Field name & type
				$htmlCode[] = '<td width="50%" align="left" valign="middle" bgcolor="' . $this->doc->bgColor5 . '"><strong>' . $fieldName . '</strong><br />' . $LANG->getLL('types.' . $value['config']['type']) . '</td>';
				
				// Select
				$htmlCode[] = '<td width="50%" align="left" valign="middle" bgcolor="' . $this->doc->bgColor4 . '">' . implode(chr(10),$select) . '</td>';
				
				// Start row
				$htmlCode[] = '</tr>';
			}
			
			// End table
			$htmlCode[] = '</table>';
			
			// Add content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Synchronise two tables
		 * 
		 * This function synchronize a local tables with another one, by mapping
		 * each field.
		 * 
		 * @return		The number of inserted row
		 */
		function t3TablesExecSync() {
			global $TCA;
			
			// Queries storage
			$sql = array();
			
			// POST variables
			$POST = array(
				'method' => t3lib_div::_GP('method'),				// Synchronisation methods
				'pid' => t3lib_div::_GP('pid'),						// Destination PID
				'uidList' => t3lib_div::_GP('uidList'),				// List of records
				'deleted' => t3lib_div::_GP('deleted'),				// Do not include deleted records
				'source' => t3lib_div::_GP('source'),				// Source table
				'destination' => t3lib_div::_GP('destination'),		// Destination table
				'fields' => array(),								// Fields mapping
			);
			
			// Get mapping for each field
			foreach($TCA[$POST['destination']]['columns'] as $key=>$value) {
				
				// Get mapping
				$map = t3lib_div::_GP($key);
				
				// Check for mapping
				if ($map) {
					
					// Add mapping
					$POST['fields'][$key] = $map;
				}
			}
			
			// Delete clause for source records if specified
			$deleteClause = ($POST['deleted']) ? t3lib_BEfunc::deleteClause($POST['source']) : '';
			
			// Check for selected records
			if (is_array($POST['uidList']) && count($POST['uidList'])) {
				
				
				// WHERE clause for source records
				$whereClause = 'uid IN (' . implode(',',$POST['uidList']) . ')' . $deleteClause;
				
			} else {
				
				// WHERE clause with no selected records
				$whereClause = str_replace('AND ','',$deleteClause);
			}
			
			// Select source records
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$POST['source'],$whereClause);
			
			// Check method
			if ($POST['method'] == 1) {
				
				// Empty destination table before adding new records
				$GLOBALS['TYPO3_DB']->sql_query('TRUNCATE TABLE ' . $POST['destination']);
			}
			
			// Status
			$status = 0;
			
			// Process each source records
			while($row = mysql_fetch_assoc($res)) {
				
				// Storage
				$newRow = array();
				
				// Map each field
				foreach($POST['fields'] as $key=>$value) {
					
					// Add field content
					$newRow[$key] = $row[$value];
				}
				
				// Add PID field
				$newRow['pid'] = $POST['pid'];
				
				// Insert new row
				if ($GLOBALS['TYPO3_DB']->exec_INSERTquery($POST['destination'],$newRow)) {
					
					// Increase record count for current table
					$status++;
				}
			}
			
			// Return number of inserted records
			return $status;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 4 - DB
		 *
		 * Typo3 databases synchronisation functions.
		 ***************************************************************/
		
		/**
		 * Database synchronisation
		 * 
		 * This is the main function for the synchronisation with an external
		 * Typo3 database.
		 * 
		 * @return		The complete module's function
		 * @see			t3DBDestroySessionData
		 * @see			t3DBGetSessionData
		 * @see			t3DBConnectStatus
		 * @see			t3DBListTables
		 * @see			t3DBConnect
		 */
		function t3DBSync() {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Title
			$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('sync.t3db'));
			
			// New connection?
			if (t3lib_div::_GP('disconnect')) {
				
				// Erase connection parameters
				$this->t3DBDestroySessionData();
			}
			
			// Try to connect using session data
			if ($this->connection = $this->t3DBGetSessionData()) {
				
				// Connection status
				$htmlCode[] = $this->t3DBConnectStatus();
				
				// List tables
				$htmlCode[] = $this->t3DBListTables();
				
			} else {
				
				// Connection parameters
				$htmlCode[] = $this->t3DBConnect();
			}
			
			// Check for a connection
			if ($this->connection) {
				
				// Close connection
				mysql_close($this->connection);
			}
			
			// Add content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Destroy user session data
		 * 
		 * This function destroys the connection parameters saved in
		 * a user's session.
		 * 
		 * @return		Void
		 */
		function t3DBDestroySessionData() {
			
			// Get session data
			$data = $GLOBALS['BE_USER']->getModuleData($GLOBALS['MCONF']['name']);
			
			// Unset parameters
			unset($data['connection']);
			
			// Store module data
			$GLOBALS['BE_USER']->pushModuleData($GLOBALS['MCONF']['name'],$data);
		}
		
		/**
		 * Get user session data
		 * 
		 * This function gets the connection parameters saved in a user's session,
		 * and try to connect with those informations.
		 * 
		 * @return		A MySQL link identifier, or false
		 */
		function t3DBGetSessionData() {
			
			// Get session data
			$data = $GLOBALS['BE_USER']->getModuleData($GLOBALS['MCONF']['name']);
			
			// Unserialize data
			$this->dbConf = unserialize($data['connection']);
			
			// Check for memorized informations
			if ($this->dbConf) {
				
				// Try to connect
				return @mysql_connect($this->dbConf['host'],$this->dbConf['username'],$this->dbConf['password']);
			}
		}
		
		/**
		 * Show database connection status
		 * 
		 * This function show the current connection with an external database.
		 * 
		 * @return		The connection status
		 */
		function t3DBConnectStatus() {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Start fieldset
			$htmlCode[] = '<fieldset>';
			
			// Title
			$htmlCode[] = '<legend>' . $LANG->getLL('LGL.connection.saved') . '</legend>';
			
			// User
			$htmlCode[] = $LANG->getLL('LGL.username') . ': <strong>' . $this->dbConf['username'] . '</strong><br />';
			
			// Host
			$htmlCode[] = $LANG->getLL('LGL.host') . ': <strong>' . $this->dbConf['host'] . '</strong><br /><br />';
			
			// Start select
			$htmlCode[] = $LANG->getLL('LGL.database') . ':<br /><select name="database" size="1">';
			
			// Get DB list
			$dbList = mysql_list_dbs($this->connection);
			// Process each database
			while($db = mysql_fetch_object($dbList)) {
				
				// Selected parameter
				$selected = ($db->Database == t3lib_div::_GP('database')) ? ' selected' : '';
				
				// Write option
				$htmlCode[] = '<option value="' . $db->Database . '"' . $selected . '>' . $db->Database . '</option>';
			}
			
			// End select
			$htmlCode[] = '</select><br /><br />';
			
			// Choose database
			$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('submit.t3db.choose') . '" name="choose">';
			
			// Disconnect
			$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('submit.t3db.disconnect') . '" name="disconnect">';
			
			// End fieldset
			$htmlCode[] = '</fieldset>';
			
			// Add content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * List tables
		 * 
		 * This function creates a list of the local tables ready to be synchronized
		 * with the corresponding tables in an external database.
		 * 
		 * @return		A list of the tables available to sync
		 * @see			t3DBExecQueries
		 */
		function t3DBListTables() {
			global $LANG;
			
			// Check for a database
			if ($database = t3lib_div::_GP('database')) {
				
				// Storage
				$htmlCode = array();
				
				// Check action
				if (t3lib_div::_GP('sync')) {
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(5);
					
					// Try to exec queries
					if ($result = $this->t3DBExecQueries($database)) {
						
						// Check result
						if (count($result)) {
							
							// Status
							$htmlCode[] = '<strong>' . $LANG->getLL('LGL.insert') . ':</strong>';
							
							// Start list
							$htmlCode[] = '<ul>';
							
							// Show each table
							foreach($result as $key=>$value) {
								
								// Label
								$label = (array_key_exists($key,$this->tables)) ? '<strong>' . $this->tables[$key]['title'] . '</strong> (' . $key . ')' : '<strong>' . $key . '</strong>';
								
								// Add list item
								$htmlCode[] = '<li>' . $label . ': ' . $value . '</li>';
							}
							
							// End list
							$htmlCode[] = '</ul>';
							
						} else {
							
							// Error
							$htmlCode[] = '<strong>' .  $LANG->getLL('errors.t3db.nosync') . '</strong>';
						}
						
					} else {
						
						// Error
						$htmlCode[] = '<strong>' .  $LANG->getLL('errors.t3db.missing') . '</strong>';
					}
				}
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(5);
				
				// Get local tables
				$localTables = $GLOBALS['TYPO3_DB']->admin_get_tables();
				
				// Get tables
				$tableList = mysql_list_tables($database,$this->connection);
				
				// Tables available for sync
				$sync = array();
				
				// Check each external table
				while($extTable = mysql_fetch_row($tableList)) {
					
					// Check for a corresponding table
					if (array_key_exists($extTable[0],$localTables)) {
						
						// Add external table name
						$sync[] = $extTable[0];
					}
				}
				
				// Check sync array
				if (count($sync)) {
					
					// Counters
					$rowcount = 0;
					$colorcount = 0;
					
					// Start fieldset
					$htmlCode[] = '<fieldset>';
					
					// Title
					$htmlCode[] = '<legend>' . $LANG->getLL('LGL.options') . '</legend>';
					
					// Add records
					$htmlCode[] = '<input name="method" type="radio" value="0"> ' . $LANG->getLL('options.t3db.method.1') . '<br />';
					
					// Empty destination table
					$htmlCode[] = '<input name="method" type="radio" value="1" checked> ' . $LANG->getLL('options.t3db.method.0') . '<br /><br />';
					
					// Check / Unckeck all
					$htmlCode[] = '<input type="button" value="' . $LANG->getLL('LGL.checkall') . '" onclick="checkBoxList(document.forms[0].list)">';
					
					// Disconnect
					$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('submit.t3db.sync') . '" name="sync">';
					
					// End fieldset
					$htmlCode[] = '</fieldset>';
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(5);
					
					// Start table
					$htmlCode[] = '<table id="tableList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
					$htmlCode[] = '<tr>';
					$htmlCode[] = '<td align="center" valign="middle" colspan="3"><strong>' . $LANG->getLL('headers.t3db.tables') . '</strong></td>';
					$htmlCode[] = '</tr>';
					
					// Build a row for each record
					foreach($sync as $table) {
						
						// Change row color
						$colorcount = ($colorcount == 1) ? 0: 1;
						$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
						
						// Build row parameters
						$tr_params = ' onmouseover="setRowColorCheck(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColorCheck(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColorCheck(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
						
						// Start row
						$htmlCode[] = '<tr' . $tr_params . '>';
						
						// Checkbox
						$htmlCode[] = '<td width="20" align="left" valign="middle"><input type="checkbox" name="tables[]" id="list" value="' . $table . '"></td>';
						
						// Icon
						$icon = (array_key_exists($table,$this->tables)) ? t3lib_iconWorks::getIconImage($table,array(),$GLOBALS['BACK_PATH']) : '';
						
						// Label
						$label = (array_key_exists($table,$this->tables)) ? '<strong>' . $this->tables[$table]['title'] . '</strong> (' . $table . ')' : '<strong>' . $table . '</strong>';
						
						// Table icon
						$htmlCode[] = '<td align="center" valign="middle">' . $icon . '</td>';
						
						// Table label
						$htmlCode[] = '<td align="left" valign="middle">' . $label . '</td>';
						
						// End row
						$htmlCode[] = '</tr>';
						
						// Increase row count
						$rowcount++;
					}
					
					// End table
					$htmlCode[] = '</table>';
					
				} else {
					
					// No available tables
					$htmlCode[] = '<strong>' . $LANG->getLL('errors.t3db.notables') . '</strong>';
				}
				
				// Add content
				return implode(chr(10),$htmlCode);
			}
		}
		
		/**
		 * Synchronise tables
		 * 
		 * This function synchronize tables from an external database with the
		 * corresponding local ones.
		 * 
		 * @param		$database	The external database
		 * @return		An array contaning the number of inserted records for each table
		 */
		function t3DBExecQueries($database) {
			
			// Tables
			$tables = t3lib_div::_GP('tables');
			
			// Method
			$method = t3lib_div::_GP('method');
			
			// Check for tables
			if (is_array($tables) && count($tables)) {
				
				// Status
				$status = array();
				
				// Process each tables
				foreach($tables as $table) {
					
					// Add table to status
					$status[$table] = 0;
					
					// Check method
					if ($method == 1) {
						
						// Empty local table before adding new records
						$GLOBALS['TYPO3_DB']->sql_query('TRUNCATE TABLE ' . $table);
					}
					
					// Select all records from external table
					$res = mysql_db_query($database,'SELECT * FROM ' . $table,$this->connection);
					
					// Process each record
					while($row = mysql_fetch_assoc($res)) {
						
						// Check method
						if ($method == 0) {
							
							// Unset UID to force external records to be added
							unset($row['uid']);
						}
						
						// Insert rows
						if ($GLOBALS['TYPO3_DB']->exec_INSERTquery($table,$row)) {
							
							// Increase record count for current table
							$status[$table]++;
						}
					}
				}
				
				// Return status array
				return $status;
			}
		}
		
		/**
		 * Connect to an external DB
		 * 
		 * This function contains all the necessary commands to connect to
		 * an external Typo3 database. The connection parameters are kept in
		 * the user session.
		 * 
		 * @return		A DB connect box
		 * @see			t3DBConnectStatus
		 * @see			t3DBListTables
		 */
		function t3DBConnect() {
			global $LANG;
			
			// Check action
			if (t3lib_div::_GP('action') == 't3db.connect') {
				
				// POST variables
				$conf = array(
					'username' => t3lib_div::_GP('username'),
					'password' => t3lib_div::_GP('password'),
					'host' => t3lib_div::_GP('host'),
				);
				
				// Check informations
				if ($conf['username'] && $conf['password'] && $conf['host']) {
					
					// Try to connect
					$this->connection = @mysql_connect($conf['host'],$conf['username'],$conf['password']);
				}
			}
			
			// Storage
			$htmlCode = array();
			
			// Check for a valid connection
			if ($this->connection) {
				
				// Get module data
				$data = $GLOBALS['BE_USER']->getModuleData($GLOBALS['MCONF']['name']);
				
				// Add configuration array to module data
				$data['connection'] = serialize($conf);
				
				// Store module data
				$GLOBALS['BE_USER']->pushModuleData($GLOBALS['MCONF']['name'],$data);
				
				// Store configuration array in a class variable
				$this->dbConf = $conf;
				
				// Connection status
				$htmlCode[] = $this->t3DBConnectStatus();
				
				// List tables
				$htmlCode[] = $this->t3DBListTables();
				
			} else {
				
				// Start fieldset
				$htmlCode[] = '<fieldset>';
				
				// Title
				$htmlCode[] = '<legend>' . $LANG->getLL('LGL.connection.params') . '</legend>';
				
				// Connection error
				if (t3lib_div::_GP('action') == 't3db.connect') {
					
					// Display warning
					$htmlCode[] = '<strong>' . $LANG->getLL('errors.t3db.connect') . '</strong><br /><br />';
				}
				
				// DB user
				$htmlCode[] = $LANG->getLL('LGL.username') . ':<br /><input name="username" type="text" value=""size="30"><br />';
				
				// DB password
				$htmlCode[] = $LANG->getLL('LGL.password') . ':<br /><input name="password" type="password" value=""size="30"><br />';
				
				// DB host
				$htmlCode[] = $LANG->getLL('LGL.host') . ':<br /><input name="host" type="text" value="localhost"size="30"><br /><br />';
				
				// Submit
				$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('submit.t3db.connect') . '">';
				
				// Hidden inputs
				$htmlCode[] = '<input type="hidden" value="t3db.connect" name="action">';
			}
			
			// End fieldset
			$htmlCode[] = '</fieldset>';
			
			// Add content
			return implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 5 - UTILITIES
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
				
				$this->tables[$table] = array(
					'title' => $LANG->sL($config['ctrl']['title']),
				);
			}
			
			// Sort tables array
			asort($this->tables);
			
			// DEBUG ONLY - Show tables
			#$this->api->debug($this->tables,'TABLES');
		}
		
		/**
		 * Get records for a table
		 * 
		 * This function creates a select menu with all the available records
		 * from a specified table.
		 * 
		 * @param		$table		The database table
		 * @return		A select menu with the records
		 */
		function getTableRecords($table) {
			global $LANG, $TCA;
			
			// Label field
			$label = $TCA[$table]['ctrl']['label'];
			
			// Storage
			$select = array();
			
			// Start select
			$select[] = '<select name="uidList[]" size="5" multiple>';
			
			// Select records
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'');
			
			// Process each record
			while($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Add option
				$select[] = '<option style="' . $this->api->be_getSelectStyleRecordIcon($table,$rec,$GLOBALS['BACK_PATH']) . '" value="' . $rec['uid'] . '">' . $rec[$label] . '</option>';
			}
			
			// End select
			$select[] = '</select>';
			
			// Return select
			return implode(chr(10),$select);
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dbsync/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dbsync/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_dbsync_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	// Start module
	$SOBE->main();
	$SOBE->printContent();
?>
