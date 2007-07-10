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
	 * Module 'OpenLDAP' for the 'ldap_macmade' extension.
	 *
	 * @author		Jean-David Gadina <info@macmade.net>
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *     116:		function init
	 *     129:		function main
	 * 
	 * SECTION:		2 - MAIN
	 *     224:		function menuConfig
	 *     249:		function moduleContent
	 * 
	 * SECTION:		3 - STATUS
	 *     318:		function showSettings($records)
	 *     358:		function buildSettingsTable($server)
	 *     412:		function tryLDAP($server)
	 * 
	 * SECTION:		4 - SHOW
	 *     486:		function showEntries($records)
	 *     546:		function buildEntriesTable($entries)
	 * 
	 * SECTION:		5 - IMPORT
	 *     612:		function importEntries($records)
	 *     882:		function importEXT($user,$server)
	 * 
	 * SECTION:		6 - MERGE
	 *     965:		function mergeEntries($records)
	 * 
	 * SECTION:		7 - DELETE
	 *    1130:		function deleteEntries($records)
	 * 
	 * SECTION:		8 - UTILITIES
	 *    1161:		function printContent
	 *    1176:		function writeHTML($text,$tag='p',$style=false)
	 *    1332:		function buildLDAPUserCols($user,$server,$mode)
	 *    1476:		function getExistingUsers
	 *    1517:		function getLDAPUsers($records,$mode)
	 * 
	 *				TOTAL FUNCTIONS: 27
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require ('conf.php');
	require ($BACK_PATH . 'init.php');
	require ($BACK_PATH . 'template.php');
	$LANG->includeLLFile('EXT:ldap_macmade/mod1/locallang.php');
	require_once (PATH_t3lib . 'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	// LDAP helper class
	require_once (t3lib_extMgm::extPath('ldap_macmade') . 'class.tx_ldapmacmade_utils.php');
	
	// Developer API class
	require_once (t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	class tx_ldapmacmade_module1 extends t3lib_SCbase {
		
		var $pageinfo;
		var $apimacmade_version = 2.2;
		
		
		
		
		
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
			
			// New instance of the LDAP helper class
			$this->utils = t3lib_div::makeInstance('tx_ldapmacmade_utils');
			
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
							top.fsMod.recentIds["web"] = ' . intval($this->id).';
						}
						//-->
					</script>
				';
				
				// Init CSM menu
				$this->api->be_initCSM();
				
				// Build current path
				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']) . '<br>' . $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path') . ': ' . t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				
				// Start page content
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				$this->content .= $this->doc->divider(5);
				
				
				// Render content:
				$this->moduleContent();
				
				// Adds shortcut
				if ($BE_USER->mayMakeShortcut())	{
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
				$this->content .= $LANG->getLL('noaccess');
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
					'1' => $LANG->getLL('menu.func.1'),
					'2' => $LANG->getLL('menu.func.2'),
					'3' => $LANG->getLL('menu.func.3'),
					'4' => $LANG->getLL('menu.func.4'),
					'5' => $LANG->getLL('menu.func.5'),
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
			
			// Working table
			$this->extTable = 'tx_ldapmacmade_server';
			
			// Additional MySQL WHERE clause for selecting records
			$addWhere = ' AND hidden=0';
			
			// Get LDAP servers form PID
			$records = t3lib_BEfunc::getRecordsByField($this->extTable,'pid',$this->id,$addWhere);
				
				// Check for records
				if (is_array($records)) {
					
					// Server(s) found
					switch ($this->MOD_SETTINGS['function']) {
					
					// Settings view
					case '1':
						$this->content .= $this->showSettings($records);
					break;
					
					// Entries view
					case '2':
						$this->content .= $this->showEntries($records);
					break;
					
					// Import
					case '3':
						$this->content .= $this->importEntries($records);
					break;
					
					// Merge
					case '4':
						$this->content .= $this->mergeEntries($records);
					break;
					
					// Delete
					case '5':
						$this->content .= $this->deleteEntries($records);
					break;
				}
			} else {
				
				// No server found
				$this->content .= $LANG->getLL('noserver') . '<br />' . $LANG->getLL('addserver');
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - STATUS
		 *
		 * Status view.
		 ***************************************************************/
		
		/**
		 * Show status & settings.
		 * 
		 * This function displays the settings and the status of the OpenLDAP
		 * servers found in the current page.
		 * 
		 * @param		$records			The database records
		 * @return		The settings view
		 */
		function showSettings($records) {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Process each server
			foreach ($records as $server) {
				
				// Start section
				$htmlCode[] = $this->doc->sectionBegin();
				
				// Title
				$htmlCode[] = $this->doc->sectionHeader($this->api->be_getRecordCSMIcon($this->extTable,$server,$GLOBALS['BACK_PATH']) . $server['title']);
				
				// Build settings table
				$htmlCode[] = $this->buildSettingsTable($server);
				
				// Attempt to connect
				$htmlCode[] = $this->tryLDAP($server);
				
				// Divider
				$htmlCode[] = $this->doc->divider(5);
				
				// End section
				$htmlCode[] = $this->doc->sectionEnd();
			}
			
			// Return HTML code
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Create settings table.
		 * 
		 * This function creates the settings table for an OpenLDAP server.
		 * 
		 * @param		$server				An OpenLDAP server record
		 * @return		The settings table
		 */
		function buildSettingsTable($server) {
			global $LANG;
			
			// Fields to display
			$showFields = array('address','port','version','user','basedn','filter','typo3_autoimport','be_enable','be_groups_import','be_auth','fe_enable','fe_groups_import','fe_auth','mapping_external');
			
			// Enable features - Human readable
			$server['typo3_autoimport'] = ($server['typo3_autoimport'] == 1) ? $this->writeHTML($LANG->getLL('LGL.enabled'),'span',array('color: green;')) : $this->writeHTML($LANG->getLL('LGL.disabled'),'span',array('color: red;'));
			$server['be_enable'] = ($server['be_enable'] == 1) ? $this->writeHTML($LANG->getLL('LGL.enabled'),'span',array('color: green;')) : $this->writeHTML($LANG->getLL('LGL.disabled'),'span',array('color: red;'));
			$server['be_groups_import'] = ($server['be_groups_import'] == 1) ? $this->writeHTML($LANG->getLL('LGL.enabled'),'span',array('color: green;')) : $this->writeHTML($LANG->getLL('LGL.disabled'),'span',array('color: red;'));
			$server['be_auth'] = ($server['be_auth'] == 1) ? $this->writeHTML($LANG->getLL('LGL.enabled'),'span',array('color: green;')) : $this->writeHTML($LANG->getLL('LGL.disabled'),'span',array('color: red;'));
			$server['fe_enable'] = ($server['fe_enable'] == 1) ? $this->writeHTML($LANG->getLL('LGL.enabled'),'span',array('color: green;')) : $this->writeHTML($LANG->getLL('LGL.disabled'),'span',array('color: red;'));
			$server['fe_groups_import'] = ($server['fe_groups_import'] == 1) ? $this->writeHTML($LANG->getLL('LGL.enabled'),'span',array('color: green;')) : $this->writeHTML($LANG->getLL('LGL.disabled'),'span',array('color: red;'));
			$server['fe_auth'] = ($server['fe_auth'] == 1) ? $this->writeHTML($LANG->getLL('LGL.enabled'),'span',array('color: green;')) : $this->writeHTML($LANG->getLL('LGL.disabled'),'span',array('color: red;'));
			$server['mapping_external'] = ($server['mapping_external']) ? $server['mapping_external'] : $this->writeHTML($LANG->getLL('LGL.disabled'),'span',array('color: red;'));
			
			// Storage
			$htmlCode = array();
			
			// Start table
			$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
			
			// Process each field
			foreach ($showFields as $field) {
				
				if (array_key_exists($field,$server)) {
					
					// Start row
					$htmlCode[] = '<tr>';
					
					// Key
					$htmlCode[] = '<td width="25%" align="left" valign="center" bgcolor="' . $this->doc->bgColor4 . '">' . $LANG->getLL($field) . '</td>';
					
					// Value
					$htmlCode[] = '<td width="75%" align="left" valign="center" bgcolor="' . $this->doc->bgColor3 . '">' . $this->writeHTML($server[$field],'strong') . '</td>';
					
					// End $row
					$htmlCode[] = '</tr>';
				}
			}
			
			// End table
			$htmlCode[] = '</table>';
			
			// Return HTML code
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Try an LDAP server.
		 * 
		 * This function tries to connect to an OpenLDAP server, and displays
		 * informations about the connection status.
		 * 
		 * @param		$server				An OpenLDAP server record
		 * @return		The status of the connection
		 */
		function tryLDAP($server) {
			global $LANG;
			
			// LDAP
			$ldap = $this->utils->createLDAP($server);
			
			// Spacer
			$htmlCode[] = $this->doc->spacer(5);
			
			// Error count
			$errorNumber = 0;
			
			// Create status
			foreach($ldap[0] as $key=>$value) {
				
				// Style tag
				$color = ($value) ? 'green' : 'red' ;
				
				// Status
				$status = ($value) ? '[' . $LANG->getLL('status.success') . ']' : '[' . $LANG->getLL('status.failure') . ']' ;
				
				// Check for an error
				if (!$value) {
					
					// Add error number & description
					$status .= ' - ' . $ldap[1][$errorNumber]['code'] . ' (' . $ldap[1][$errorNumber]['message'] . ')';
				}
				
				// Paragraph elements
				$legend = $this->writeHTML($LANG->getLL('status.' . $key) . ': ','strong');
				$result = $this->writeHTML($status,'span',array('color: ' . $color . ''));
				
				// Paragraph style
				$pStyle = array(
					'border: solid 1px ' . $this->doc->bgColor2,
					'background-color: ' . $this->doc->bgColor4,
					'margin-bottom: 1px',
					'padding: 2px',
				);
				
				// Paragraph
				$htmlCode[] = $this->writeHTML($legend . $result,'p',$pStyle);
				
				// Increase error counter
				$errorNumber++;
				
				// Break if error
				if (!$value) {
					break;
				}
			}
			
			// Return status
			return implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 4 - SHOW
		 *
		 * Show view.
		 ***************************************************************/
		
		/**
		 * Show OpenLDAP entries.
		 * 
		 * This function displays the entries found on the OpenLDAP servers.
		 * 
		 * @param		$records			The database records
		 * @return		The OpenLDAP entries view
		 */
		function showEntries($records) {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Process each server
			foreach ($records as $server) {
				
				// Start section
				$htmlCode[] = $this->doc->sectionBegin();
				
				// Title
				$htmlCode[] = $this->doc->sectionHeader($this->api->be_getRecordCSMIcon($this->extTable,$server,$GLOBALS['BACK_PATH']) . $server['title']);
				
				// LDAP
				$ldap = $this->utils->createLDAP($server);
				
				if (is_array($ldap[0]['entries'])) {
					
					// Clean LDAP entries array
					$entries = $this->utils->cleanEntries($ldap[0]['entries']);
					
					// Sort entries
					$entries = $this->utils->sortEntries($entries,$server['mapping_username']);
					
					// Entries count
					$entriesCountStr = str_replace('###NUMBER###',$ldap[0]['count'],$LANG->getLL('entries.count.show'));
					$htmlCode[] = $this->writeHTML($entriesCountStr,'p',array('color: green','font-weight: bold'));
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(5);
					
					// Can display entries
					$htmlCode[] = $this->buildEntriesTable($entries);
				} else {
					
					// LDAP error
					$htmlCode[] = $this->writeHTML($LANG->getLL('ldap.error'),'p',array('color: red','font-weight: bold'));
				}
				
				// Divider
				$htmlCode[] = $this->doc->divider(5);
				
				// End section
				$htmlCode[] = $this->doc->sectionEnd();
			}
			
			// Return HTML code
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Build entries table.
		 * 
		 * This function creates a table with the entries found on a OpenLDAP server.
		 * 
		 * @param		$entries			The OpenDAP entries
		 * @return		The OpenLDAP entries table
		 */
		function buildEntriesTable($entries) {
			
			// Storage
			$result = array();
			
			if (is_array($entries)) {
				
				// Start table
				$result[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
				
				// Counting array
				if (count($entries)) {
					
					// Display content
					foreach ($entries as $key=>$val) {
						
						// Start row
						$result[] = '<tr><td align="left" valign="middle" bgcolor="' . $this->doc->bgColor4 . '"><strong>' . $key . '</strong></td><td align="left" valign="middle" bgcolor="' . $this->doc->bgColor3 . '">';
						
						if (is_array($val)) {
								
								// Result is array
								$result[] =  $this->buildEntriesTable($val);
							
						} else {
							
							// Check field type
							switch($key) {
								
								// JPEG picture -> special processing for base64 chunks
								/*case 'jpegphoto':
									$result[] = 'OK';
								break;*/
								
								// Result can be displayed directly
								default:
									$result[] = (strlen($val) > 50) ? htmlentities(substr($val,0,50)) . $this->writeHTML(' [...]','span',array('color: green','font-weight: bold')) : htmlentities($val);
								break;
							}
						}
						
						// End row
						$result[] = '</td></tr>';
					}
					
				} else {
					
					// No content to display
					$result[] = '<tr><td align="left" valign="middle" bgcolor="' . $this->doc->bgColor3. '">' . $this->writeHTML('[EMPTY]','span',array('color: red','font-weight: bold')) . '</td></tr>';
				}
				
				// End table
				$result[] = '</table>';
				
				// Return table
				return implode(chr(10),$result);
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 5 - IMPORT
		 *
		 * Import view.
		 ***************************************************************/
		
		/**
		 * Import LDAP users.
		 * 
		 * This function displays the import form for the LDAP users.
		 * 
		 * @param		$records			The database records
		 * @return		The import view
		 */
		function importEntries($records) {
			global $LANG;
			
			// Counters
			$rowcount = 0;
			$colorcount = 0;
			
			// Storage
			$htmlCode = array();
			
			// Import users
			if (t3lib_div::_POST('action')) {
				$htmlCode[] = $this->getLDAPUsers($records,'IMPORT');
			}
			
			// Server uid
			$htmlCode[] = '<input type="hidden" value="" name="server">';
			
			// Process each server
			foreach ($records as $server) {
				
				// Update existing users
				$this->getExistingUsers($server);
				
				// Start section
				$htmlCode[] = $this->doc->sectionBegin();
				
				// Title
				$htmlCode[] = $this->doc->sectionHeader($this->api->be_getRecordCSMIcon($this->extTable,$server,$GLOBALS['BACK_PATH']) . $server['title']);
				
				if ($server['be_enable'] || $server['fe_enable'] || $server['mapping_external']) {
					
					// LDAP
					$ldap = $this->utils->createLDAP($server);
					
					// Check LDAP entries
					if (is_array($ldap[0]['entries'])) {
						
						// Clean LDAP entries array
						$entries = $this->utils->cleanEntries($ldap[0]['entries']);
						
						// Sort entries
						$entries = $this->utils->sortEntries($entries,$server['mapping_username']);
						
						// Entries count
						$htmlCode['count'] = '';
						
						// Storage
						$ldapImport = array();
						
						// Action flag
						$htmlCode[] = '<input type="hidden" value="import" name="action">';
						
						// Check / Unckeck all
						$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('btn.import') . '" style="margin: 5 5 5 0; border: solid 1px #666666; background-color: #FFFFFF; color: #666666" onclick="document.forms[0].server.value=\'' . $server['uid'] . '\';">';
						
						// Check / Unckeck all
						$htmlCode[] = '<input type="button" value="' . $LANG->getLL('btn.check') . '" style="margin: 5 5 5 0; border: solid 1px #666666; background-color: #FFFFFF; color: #666666" onclick="checkBoxList(document.forms[0].list)">';
						
						// Start table
						$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
						
						// Start table headers
						$htmlCode[] = '<tr>';
						$htmlCode[] = '<td align="center" valign="middle"></td>';
						
						// Username header
						$htmlCode[] = '<td align="center" valign="middle"><strong>' . $LANG->getLL('headers.username') . '</strong></td>';
						
						// Get mapping informations
						$map = t3lib_div::xml2array($server['mapping']);
						
						// Check result
						if (is_array($map['data']['sDEF']['lDEF']['fields']['el']) && count($map['data']['sDEF']['lDEF']['fields']['el'])) {
							
							// Add headers
							foreach($map['data']['sDEF']['lDEF']['fields']['el'] as $field) {
								
								// Type field to get
								$type = $field['field']['el']['type']['vDEF'];
								
								// Value
								$value = $LANG->getLL('headers.' . $type);
								
								// Add header
								$htmlCode[] = '<td align="center" valign="middle"><strong>' . $value . '</strong></td>';
							}
						}
						
						// Import header
						$htmlCode[] = '<td align="center" valign="middle"><strong>' . $LANG->getLL('headers.import') . '</strong></td>';
						
						// End table headers
						$htmlCode[] = '</tr>';
						
						// Check each entry
						foreach ($entries as $user) {
							
							// LDAP username
							$ldapUser = $this->utils->checkLDAPField($user,$server['mapping_username']);
							
							// LDAP user does not exist in FE users or BE users
							if (!in_array($ldapUser,$this->users['FE']) || !in_array($ldapUser,$this->users['BE'])) {
								
								// Color
								$colorcount = ($colorcount == 1) ? 0: 1;
								$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
								
								// TR parameters
								$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
								
								// Start row
								$htmlCode[] = '<tr' . $tr_params . '>';
								
								// Build import form
								$htmlCode[] = $this->buildLDAPUserCols($user,$server,'IMPORT');
								
								// End row
								$htmlCode[] = '</tr>';
								
								// Increase row counter
								$rowcount++;
							}
						}
						
						// Update number of users
						$entriesCountStr = str_replace('###NUMBER###',$rowcount,$LANG->getLL('entries.count.import'));
						$htmlCode['count'] = $this->writeHTML($entriesCountStr,'p',array('color: green','font-weight: bold'));
						
						// End table
						$htmlCode[] = '</table>';
						
					} else {
						
						// LDAP error
						$htmlCode[] = $this->writeHTML($LANG->getLL('ldap.error'),'p',array('color: red','font-weight: bold'));
					}
				} else {
					
					// No import
					$htmlCode[] = $LANG->getLL('noimport');
				}
				
				// Divider
				$htmlCode[] = $this->doc->divider(5);
				
				// End section
				$htmlCode[] = $this->doc->sectionEnd();
			}
			
			// Return HTML code
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Import a record in an external table.
		 * 
		 * This function import a user from the LDAP server to an external
		 * table.
		 * 
		 * @param		$user				The LDAP user
		 * @param		$server				The LDAP server
		 * @return		Void
		 */
		function importEXT($user,$server) {
			global $TCA;
			
			// Database table
			$table = $server['mapping_external'];
			
			// Storage
			$insertFields = array();
			
			// Load TCA
			t3lib_div::loadTCA($table);
			
			// TCA informations
			$tstamp = $TCA[$table]['ctrl']['tstamp'];
			$crdate = $TCA[$table]['ctrl']['crdate'];
			$cruser_id = $TCA[$table]['ctrl']['cruser_id'];
			
			// PID
			$insertFields['pid'] = ($server['mapping_external_pid']) ? $server['mapping_external_pid'] : $this->id;
			
			// Modification date
			if ($tstamp) {
				
				// Add time
				$insertFields[$tstamp] = time();
			}
			
			// Creation date
			if ($crdate) {
				
				// Add time
				$insertFields[$crdate] = time();
			}
			
			// Creation user
			if ($cruser_id) {
				
				// Add user
				$insertFields[$cruser_id] = $GLOBALS['BE_USER']->user['uid'];
			}
			
			// Mapping infos
			$ds = t3lib_div::xml2array($server['mapping_external_fields']);
			
			// Check mapping
			if (is_array($ds['data']['sDEF']['lDEF']['fields_external']['el']) && count($ds['data']['sDEF']['lDEF']['fields_external']['el'])) {
				
				// Process each fields
				foreach($ds['data']['sDEF']['lDEF']['fields_external']['el'] as $map) {
					
					// Field name
					$field = $map['field']['el']['field']['vDEF'];
					
					// Field value
					$value = $map['field']['el']['ldap']['vDEF'];
					
					// Add field
					$insertFields[$field] = $this->utils->substituteLDAPValue($value,$user);
				}
			}
			
			// MySQL INSERT query
			$GLOBALS['TYPO3_DB']->exec_INSERTquery($table,$insertFields);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 6 - MERGE
		 *
		 * Merge view.
		 ***************************************************************/
		
		/**
		 * Update users.
		 * 
		 * This function displays the update form for the Typo3 users.
		 * 
		 * @param		$records			The database records
		 * @return		The update view
		 */
		function mergeEntries($records) {
			global $LANG;
			
			// Counters
			$rowcount = 0;
			$colorcount = 0;
			
			// Storage
			$htmlCode = array();
			
			// Update users
			if (t3lib_div::_POST('action')) {
				$htmlCode[] = $this->getLDAPUsers($records,'UPDATE');
			}
			
			// Server uid
			$htmlCode[] = '<input type="hidden" value="" name="server">';
			
			// Process each server
			foreach ($records as $server) {
				
				// Update existing users
				$this->getExistingUsers($server);
				
				// Start section
				$htmlCode[] = $this->doc->sectionBegin();
				
				// Title
				$htmlCode[] = $this->doc->sectionHeader($this->api->be_getRecordCSMIcon($this->extTable,$server,$GLOBALS['BACK_PATH']) . $server['title']);
				
				if ($server['be_enable'] || $server['fe_enable']) {
					
					// LDAP
					$ldap = $this->utils->createLDAP($server);
					
					// Check LDAP entries
					if (is_array($ldap[0]['entries'])) {
						
						// Clean LDAP entries array
						$entries = $this->utils->cleanEntries($ldap[0]['entries']);
						
						// Sort entries
						$entries = $this->utils->sortEntries($entries,$server['mapping_username']);
						
						// Entries count
						$htmlCode['count'] = '';
						
						// Storage
						$ldapImport = array();
						
						// Action flag
						$htmlCode[] = '<input type="hidden" value="update" name="action">';
						
						// Check / Unckeck all
						$htmlCode[] = '<input type="submit" value="' . $LANG->getLL('btn.update') . '" style="margin: 5 5 5 0; border: solid 1px #666666; background-color: #FFFFFF; color: #666666" onclick="document.forms[0].server.value=\'' . $server['uid'] . '\';">';
						
						// Check / Unckeck all
						$htmlCode[] = '<input type="button" value="' . $LANG->getLL('btn.check') . '" style="margin: 5 5 5 0; border: solid 1px #666666; background-color: #FFFFFF; color: #666666" onclick="checkBoxList(document.forms[0].list)">';
						
						// Start table
						$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
						
						// Start table headers
						$htmlCode[] = '<tr>';
						$htmlCode[] = '<td align="center" valign="middle"></td>';
						
						// Username header
						$htmlCode[] = '<td align="center" valign="middle"><strong>' . $LANG->getLL('headers.username') . '</strong></td>';
						
						// Get mapping informations
						$map = t3lib_div::xml2array($server['mapping']);
						
						// Check result
						if (is_array($map['data']['sDEF']['lDEF']['fields']['el']) && count($map['data']['sDEF']['lDEF']['fields']['el'])) {
							
							// Add headers
							foreach($map['data']['sDEF']['lDEF']['fields']['el'] as $field) {
								
								// Type field to get
								$type = $field['field']['el']['type']['vDEF'];
								
								// Value
								$value = $LANG->getLL('headers.' . $type);
								
								// Add header
								$htmlCode[] = '<td align="center" valign="middle"><strong>' . $value . '</strong></td>';
							}
						}
						
						// Update header
						$htmlCode[] = '<td align="center" valign="middle"><strong>' . $LANG->getLL('headers.update') . '</strong></td>';
						
						// End table headers
						$htmlCode[] = '</tr>';
						
						// Check each entry
						foreach ($entries as $user) {
							
							// LDAP username
							$ldapUser = $this->utils->checkLDAPField($user,$server['mapping_username']);
							
							// LDAP user does not exist in FE users or BE users
							if (in_array($ldapUser,$this->users['FE']) || in_array($ldapUser,$this->users['BE'])) {
								
								// Color
								$colorcount = ($colorcount == 1) ? 0: 1;
								$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
								
								// TR parameters
								$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
								
								// Start row
								$htmlCode[] = '<tr' . $tr_params . '>';
								
								// Build import form
								$htmlCode[] = $this->buildLDAPUserCols($user,$server,'UPDATE');
								
								// End row
								$htmlCode[] = '</tr>';
								
								// Increase row counter
								$rowcount++;
							}
						}
						
						// Update number of users
						$entriesCountStr = str_replace('###NUMBER###',$rowcount,$LANG->getLL('entries.count.update'));
						$htmlCode['count'] = $this->writeHTML($entriesCountStr,'p',array('color: green','font-weight: bold'));
						
						// End table
						$htmlCode[] = '</table>';
						
					} else {
						
						// LDAP error
						$htmlCode[] = $this->writeHTML($LANG->getLL('ldap.error'),'p',array('color: red','font-weight: bold'));
					}
				} else {
					
					// No import
					$htmlCode[] = $LANG->getLL('noimport');
				}
				
				// Divider
				$htmlCode[] = $this->doc->divider(5);
				
				// End section
				$htmlCode[] = $this->doc->sectionEnd();
			}
			
			// Return HTML code
			return implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 7 - DELETE
		 *
		 * Delete view.
		 ***************************************************************/
		
		/**
		 * Delete users.
		 * 
		 * This function displays the delete form for the Typo3 users.
		 * 
		 * @param		$records			The database records
		 * @return		The delete view
		 */
		function deleteEntries($records) {
			
			// Not yet implemented
			return '<strong>Not yet implemented</strong><br />Error 500: Macmade\'s brain overload...';
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 8 - UTILITIES
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
			echo($this->content);
		}
		
		/**
		 * Writes some code.
		 * 
		 * This function writes a text wrapped inside an HTML tag.
		 * 
		 * @param		$text				The text to write
		 * @param		$tag				The HTML tag to write
		 * @param		$style				An array containing the CSS styles for the tag
		 * @return		The text wrapped in the HTML tag
		 */
		function writeHTML($text,$tag='p',$style=false) {
			
			// Create style tag
			if (is_array($style)) {
				$styleTag = ' style="' . implode('; ',$style) . '"';
			}
			
			// Return HTML text
			return '<' . $tag . $styleTag . '>' . $text . '</' . $tag . '>';
		}
		
		/**
		 * Build LDAP user informations
		 * 
		 * This function creates a table row with the needed informations
		 * about a LDAP user.
		 * 
		 * @param		$user				An array with the LDAP user
		 * @param		$server				The LDAP server
		 * @param		$mode				The mode (IMPORT or UPDATE)
		 * @return		The user's informations row
		 */
		function buildLDAPUserCols($user,$server,$mode) {
			
			// Storage
			$htmlCode = array();
			
			// Get username
			$username = utf8_decode($this->utils->checkLDAPField($user,$server['mapping_username']));
			
			// Checkbox
			$htmlCode[] = '<td width="5%" align="center" valign="middle">' . '<input type="checkbox" name="ldap_action[]" id="list" value="' . $username . '"></td>';
			
			// Username
			$htmlCode[] = '<td align="left" valign="middle">' . $username . '</td>';
			
			// Get mapping informations
			$map = t3lib_div::xml2array($server['mapping']);
			
			// Check result
			if (is_array($map['data']['sDEF']['lDEF']['fields']['el']) && count($map['data']['sDEF']['lDEF']['fields']['el'])) {
				
				// Add mapping infos
				foreach($map['data']['sDEF']['lDEF']['fields']['el'] as $field) {
					
					// LDAP field to get
					$ldap = $field['field']['el']['ldap']['vDEF'];
					
					// Value
					$value = $this->utils->substituteLDAPValue($ldap,$user);
					
					// Add field
					$htmlCode[] = '<td align="left" valign="middle">' . utf8_decode($value) . '</td>';
				}
			}
			
			// Types
			$types = array();
			
			// Check BE features
			if ($server['be_enable']) {
				
				// Check mode
				if ($mode == 'IMPORT' && !in_array($username,$this->users['BE'])) {
					
					// BE import available
					$types[] = 'BE';
					
				} else if ($mode == 'UPDATE' && in_array($username,$this->users['BE'])) {
					
					// BE update available
					$types[] = 'BE';
				}
			}
			
			// Check FE features
			if ($server['fe_enable']) {
				
				// Check mode
				if ($mode == 'IMPORT' && !in_array($username,$this->users['FE'])) {
					
					// BE import available
					$types[] = 'FE';
					
				} else if ($mode == 'UPDATE' && in_array($username,$this->users['FE'])) {
					
					// BE update available
					$types[] = 'FE';
				}
			}
			
			// Check external import
			if ($server['mapping_external'] && $mode == 'IMPORT') {
				
				// External import available
				$types[] = 'EXT';
			}
			
			// Type
			$htmlCode[] = '<td align="center" valign="middle" class="typo3-red"><strong>' . implode('-',$types) . '</strong></td>';
			
			// Return the row
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Get Typo3 users
		 * 
		 * This function creates a class array ($this->users) containing all
		 * the existing BE and FE users.
		 * 
		 * @return		Void
		 */
		function getExistingUsers($server) {
			
			// Existing users storage
			$fe_users = array();
			$be_users = array();
			$external = array();
			
			// Delete clause
			$whereClause = 'deleted=0';
			
			// MySQL queries
			$res_be = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username','be_users',$whereClause);
			$res_fe = $GLOBALS['TYPO3_DB']->exec_SELECTquery('username','fe_users',$whereClause . ' AND pid=' . $this->id);
			
			// Get FE username field
			while($row_fe = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res_fe)) {
				$fe_users[] = $row_fe['username'];
			}
			
			// Get BE username field
			while($row_be = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res_be)) {
				$be_users[] = $row_be['username'];
			}
			
			// Free MySQL results
			$GLOBALS['TYPO3_DB']->sql_free_result($res_fe);
			$GLOBALS['TYPO3_DB']->sql_free_result($res_be);
			
			// Set users as class variables
			$this->users = array('BE'=>$be_users,'FE'=>$fe_users);
		}
		
		/**
		 * Get selected users
		 * 
		 * This function launch the LDAP users' import or udpate process.
		 * 
		 * @param		$records			The database records
		 * @param		$mode				The mode (IMPORT or UPDATE)
		 * @return		A confirmation message
		 */
		function getLDAPUsers($records,$mode) {
			global $LANG;
			
			// Users to import
			$users = t3lib_div::_POST('ldap_action');
			
			// Server to use
			$server_uid = t3lib_div::_POST('server');
			
			// Check server from PID
			foreach($records as $server) {
				
				// Check server to use
				if ($server['uid'] == $server_uid) {
					
					// Set settings
					$settings = $server;
					
					// Exit loop
					break;
				}
			}
			
			// Update existing users
			$this->getExistingUsers($settings);
			
			// Server is a valid record
			if (isset($settings) && is_array($settings) && is_array($users)) {
				
				// LDAP
				$ldap = $this->utils->createLDAP($settings);
				
				// Check LDAP entries
				if (is_array($ldap[0]['entries'])) {
					
					// Clean LDAP entries array
					$entries = $this->utils->cleanEntries($ldap[0]['entries']);
					
					// Process LDAP entries
					foreach ($entries as $user) {
						
						// Get username
						$username = $this->utils->checkLDAPField($user,$settings['mapping_username']);
						
						// Import or update user
						if (in_array($username,$users)) {
							
							// Check BE features
							if ($server['be_enable']) {
								
								// Check mode
								if ($mode == 'IMPORT' && !in_array($username,$this->users['BE'])) {
									
									// Import
									$this->utils->ldap2BE($user,$server,$mode);
									
								} else if ($mode == 'UPDATE' && in_array($username,$this->users['BE'])) {
									
									// Update
									$this->utils->ldap2BE($user,$server,$mode);
								}
							}
							
							// Check FE features
							if ($server['fe_enable']) {
								
								// Check mode
								if ($mode == 'IMPORT' && !in_array($username,$this->users['FE'])) {
									
									// Import
									$this->utils->ldap2FE($user,$server,$mode);
									
								} else if ($mode == 'UPDATE' && in_array($username,$this->users['FE'])) {
									
									// Update
									$this->utils->ldap2FE($user,$server,$mode);
								}
							}
							
							// Import as external table?
							if ($server['mapping_external']) {
								
								// Import
								$this->importEXT($user,$server);
							}
						}
					}
					
					// Import successful
					$import = true;
					
				} else {
					
					// Can't import
					$import = false;
				}
			} else {
				
				// Can't import
				$import = false;
			}
			
			// Result
			$result = ($import) ? $LANG->getLL(strtolower($mode) . '.ok') : $LANG->getLL(strtolower($mode) . '.error');
			
			// Return result
			return $this->writeHTML($result,'p',array('color: red','font-weight: bold'));
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ldap_macmade/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_ldapmacmade_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	$SOBE->main();
	$SOBE->printContent();
?>
