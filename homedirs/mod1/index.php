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
	 * Module 'Home directories' for the 'homedirs' extension.
	 *
	 * @author		Jean-David Gadina <macmade@gadlab.net>
	 * @version		1.1
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *      90:		function init
	 *     108:		function main
	 * 
	 * SECTION:		2 - MAIN
	 *     203:		function menuConfig
	 *     225:		function moduleContent
	 *     286:		function showDirContent
	 *     413:		function createDirs($section)
	 *     594:		function deleteDirs($section)
	 *     809:		function function showDirs($section)
	 * 
	 * SECTION:		5 - UTILITIES
	 *    1004:		function printContent
	 *    1019:		function writeHTML($text,$tag='div',$class=false,$style=false)
	 *    1044:		function buildSelector($table)
	 *    1096:		function processActions($section)
	 *    1184:		function checkSettings($type)
	 *    1268:		function excludeDeleted($var)
	 * 
	 *				TOTAL FUNCTIONS: 14
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require ('conf.php');
	require ($BACK_PATH . 'init.php');
	require ($BACK_PATH . 'template.php');
	$LANG->includeLLFile('EXT:homedirs/mod1/locallang.php');
	require_once (PATH_t3lib . 'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	// Developer API class
	require_once (t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	class tx_homedirs_module1 extends t3lib_SCbase {
		
		var $pageinfo;
		var $apimacmade_version = 2.3;
		
		
		
		
		
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
			
			// GET variables
			$this->getVars = t3lib_div::_GET($GLOBALS['MCONF']['name']);
			
			// Init
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
				
				// Render content
				$this->content .= $this->moduleContent();
				
				// Adds shortcut
				if ($BE_USER->mayMakeShortcut())	{
					$this->content .= $this->doc->spacer(20) . $this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}
				
				// Spacer
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
		 * @return		The modules's content
		 */
		function moduleContent() {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// Section
			$section = ($this->MOD_SETTINGS['function'] == 1) ? 'users' : 'groups';
			
			// Check settings
			if ($error = $this->checkSettings($section)) {
				
				// Display error
				$htmlCode[] = $error;
				
			} else {
				
				// Check for POST variables
				if (t3lib_div::_POST('action')) {
					
					// Process actions
					$this->processActions($section);
				}
				
				// Get directories
				$this->homeDirs = t3lib_div::get_dirs($this->directory);
				
				// Check for a real array
				if (is_array($this->homeDirs) && count($this->homeDirs)) {
					
					// Exclude directories marked as deleted
					$this->homeDirs = array_filter($this->homeDirs,array($this,'excludeDeleted'));
				}
				
				// Form action
				$htmlCode[] = '<input name="action" id="action" type="hidden" value="">';
				
				// View directory content
				$htmlCode[] = $this->showDirContent();
				
				// Show users with no home
				$htmlCode[] = $this->createDirs($section);
				
				// Show homes with no user
				$htmlCode[] = $this->deleteDirs($section);
				
				// Show homes with valid user
				$htmlCode[] = $this->showDirs($section);
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Show a directory's content.
		 * 
		 * This function is used to display the files contained in a directory.
		 * 
		 * @return		The directory's content
		 */
		function showDirContent() {
			global $LANG;
			
			// Check variables
			if (is_array($this->getVars) && array_key_exists('showDir',$this->getVars) && is_array($this->homeDirs) && in_array($this->getVars['showDir'],$this->homeDirs)) {
				
				// Try to get content
				$sub = t3lib_div::getAllFilesAndFoldersInPath(array(),$this->directory . $this->getVars['showDir'] . '/');
				
				// Check for files
				if (is_array($sub) && count($sub)) {
					
					// Storage
					$htmlCode = array();
					
					// Begin section
					$htmlCode[] = $this->doc->sectionBegin();
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Title
					$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('section.listing') . ' (' . $this->directory . $this->getVars['showDir'] . '/' . ')');
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Counters
					$colorcount = 0;
					$rowcount = 0;
					
					// Start table
					$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
					
					// Headers style
					$headerStyle = array('font-weight: bold');
					
					// Table headers
					$htmlCode[] = '<tr>';
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.name'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.size'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.path'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('modification'),'td',false,$headerStyle);
					$htmlCode[] = '</tr>';
					
					// Process files
					foreach($sub as $key=>$value) {
						
						// Color
						$colorcount = ($colorcount == 1) ? 0: 1;
						$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
						
						// Storage
						$dir = array();
						
						// Size
						$size = filesize($value);
						
						// Size in kilobytes
						$size = $size / 1024;
						
						// Suffix
						$sizeSuffix = $LANG->getLL('kilobyte');
						
						// Check size
						if ($size >= 1000) {
							
							// Size in megabytes
							$size = $size / 1024;
							
							// Suffix
							$sizeSuffix = $LANG->getLL('megabyte');
						}
						
						// TR parameters
						$tr_params = ' bgcolor="' . $color . '"';
						
						// Path info
						$pathInfo = explode('/',$value);
						
						// View href
						$viewHref = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . str_replace(PATH_site,'',$value);
						
						// View icon with link
						$viewIcon = '<a href="' . $viewHref . '" target="_blank"><img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/zoom.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle"></a>';
						
						// View
						$dir[] = $this->writeHTML($viewIcon,'td');
						
						// File name
						$dir[] = $this->writeHTML(array_pop($pathInfo),'td');
						
						// Size
						$dir[] = $this->writeHTML(round($size,2) . ' ' . $sizeSuffix,'td');
						
						// Path
						$dir[] = $this->writeHTML(str_replace($this->directory . $this->getVars['showDir'],'',implode('/',$pathInfo)) . '/','td');
						
						// Date format
						$dateFormat = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] . ' / ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'];
						
						// Modification time
						$dir[] = $this->writeHTML(date($dateFormat,filemtime($value)),'td');
						
						// Add row
						$htmlCode[] = '<tr ' . $tr_params . '>' . implode(chr(10),$dir) . '</tr>';
					}
					
					// End table
					$htmlCode[] = '</table>';
					
					// Return code
					return implode(chr(10),$htmlCode);
				}
			}
		}
		
		/**
		 * Create missing directories
		 * 
		 * This function is used to display the list of the users/groups with no
		 * home directories.
		 * 
		 * @param		$section			The type of view (users or groups)
		 * @return		The list of the users/groups
		 */
		function createDirs($section) {
			global $LANG;
			
			// Database table to use
			$table = ($section == 'users') ? 'be_users' : 'be_groups';
			
			// Storage
			$htmlCode = array();
			
			// Check for directories
			if (is_array($this->homeDirs) && count($this->homeDirs)) {
				
				// MySQL WHERE clause
				$addWhere = array();
				
				// Process directories
				foreach($this->homeDirs as $uid) {
					
					// Add clause for directory
					$addWhere[] = 'uid!=' . $uid;
				}
				
				// Select users with no home
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'deleted=0 AND ' . implode(' AND ',$addWhere));
				
			} else {
				
				// Select users with no home
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'deleted=0');
			}
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Begin section
				$htmlCode[] = $this->doc->sectionBegin();
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(10);
				
				// Title
				$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('section.' . $section . '.nodir'));
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(10);
				
				// Counters
				$colorcount = 0;
				$rowcount = 0;
				
				// Storage
				$btns = array();
				
				// Label
				$btns[] = $this->writeHTML($LANG->getLL('options'),'legend');
				
				// Create  
				$btns[] = '<input type="submit" value="' . $LANG->getLL('create.' . $section) . '" onclick="document.forms[0].action.value=\'create\'">';
				
				// Check / Unckeck all
				$btns[] = '<input type="button" value="' . $LANG->getLL('btn.checkall') . '" onclick="checkBoxList(document.forms[0].list_db)">';
				
				// Add buttons
				$htmlCode[] = $this->writeHTML(implode(chr(10),$btns),'fieldset');
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(10);
				
				// Start table
				$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
				
				// Headers style
				$headerStyle = array('font-weight: bold');
				
				// Check section
				if ($section == 'users') {
					
					// Table headers
					$htmlCode[] = '<tr>';
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('be_users.uid'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('be_users.username'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('be_users.realName'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('be_users.crdate'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('be_users.lastlogin'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = '</tr>';
					
				} else {
					
					// Table headers
					$htmlCode[] = '<tr>';
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('be_groups.uid'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('be_groups.title'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('be_groups.crdate'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = '</tr>';
				}
				
				// Process users with no home
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Color
					$colorcount = ($colorcount == 1) ? 0: 1;
					$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
					
					// TR parameters
					$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
					
					// Storage
					$rec = array();
					
					// Checkbox
					$rec[] = $this->writeHTML('<input type="checkbox" name="' . $GLOBALS['MCONF']['name'] . '[create][]" id="list_db" value="' . $row['uid'] . '">','td');
					
					// Icon
					$rec[] = $this->writeHTML($this->api->be_getRecordCSMIcon($table,$row,$GLOBALS['BACK_PATH']),'td');
					
					// UID
					$rec[] = $this->writeHTML($row['uid'],'td');
					
					// Check section
					if ($section == 'users') {
						
						// Username
						$rec[] = $this->writeHTML($row['username'],'td',false,array('font-weight: bold'));
						
						// Real name
						$rec[] = $this->writeHTML($row['realName'],'td');
						
						// Creation date
						$rec[] = $this->writeHTML(date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$row['crdate']),'td');
						
						// Last login
						$rec[] = $this->writeHTML(date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$row['lastlogin']),'td');
						
					} else {
						
						// Title
						$rec[] = $this->writeHTML($row['title'],'td',false,array('font-weight: bold'));
						
						// Creation date
						$rec[] = $this->writeHTML(date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],$row['crdate']),'td');
					}
					
					// Infos
					$rec[] = $this->writeHTML($this->api->be_buildRecordIcons('show,edit,delete',$table,$row['uid']),'td');
					
					// Add row
					$htmlCode[] = '<tr ' . $tr_params . '>' . implode(chr(10),$rec) . '</tr>';
					
					// Increase row count
					$rowcount++;
				}
				
				// End table
				$htmlCode[] = '</table>';
				
				// Divider
				$htmlCode[] = $this->doc->divider(5);
				
				// End section
				$htmlCode[] = $this->doc->sectionEnd();
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Delete obsolete directories
		 * 
		 * This function is used to display the list of the directories with no
		 * associated users/groups.
		 * 
		 * @param		$section			The type of view (users or groups)
		 * @return		The list of the directories
		 */
		function deleteDirs($section) {
			global $LANG;
			
			// Database table to use
			$table = ($section == 'users') ? 'be_users' : 'be_groups';
			
			// Storage
			$htmlCode = array();
			
			// Check for existing home directories
			if (is_array($this->homeDirs) && count($this->homeDirs)) {
				
				// Storage
				$orphaned = array();
				
				// Process existing directories
				foreach($this->homeDirs as $id) {
					
					// Check for an associated BE user
					if (!t3lib_BEfunc::getRecord($table,$id)) {
						
						// User does not exists - Directory is orphaned
						$orphaned[] = $id;
					}
				}
				
				// Check for orphaned directories
				if (count($orphaned)) {
					
					// Sort directories
					sort($orphaned);
					
					// Begin section
					$htmlCode[] = $this->doc->sectionBegin();
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Title
					$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('section.' . $section . '.norel'));
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Counters
					$colorcount = 0;
					$rowcount = 0;
					
					// Storage
					$btns = array();
					
					// Label
					$btns[] = $this->writeHTML($LANG->getLL('options'),'legend');
					
					// Rename
					$btns[] = $this->writeHTML('<input type="radio" name="' . $GLOBALS['MCONF']['name'] . '[process]" value="0" checked>&nbsp;' . $LANG->getLL('move.' . $section));
					
					// Spacer
					$btns[] = $this->doc->spacer(5);
					
					// Delete
					$btns[] = $this->writeHTML('<input type="radio" name="' . $GLOBALS['MCONF']['name'] . '[process]" value="1">&nbsp;' . $LANG->getLL('delete.' . $section));
					
					// Create select with BE users
					$select = $this->buildSelector($table);
					
					// Check selector
					if ($select) {
						
						// Spacer
						$btns[] = $this->doc->spacer(5);
						
						// Assign
						$btns[] = $this->writeHTML('<input type="radio" name="' . $GLOBALS['MCONF']['name'] . '[process]" value="2">&nbsp;' . $LANG->getLL('assign.' . $section) . '&nbsp;' . $select);
					}
					
					// Spacer
					$btns[] = $this->doc->spacer(10);
					
					// Process
					$btns[] = '<input type="submit" value="' . $LANG->getLL('process.' . $section) . '" onclick="document.forms[0].action.value=\'delete\'">';
					
					// Check / Unckeck all
					$btns[] = '<input type="button" value="' . $LANG->getLL('btn.checkall') . '" onclick="checkBoxList(document.forms[0].list_dir)">';
					
					// Add buttons
					$htmlCode[] = $this->writeHTML(implode(chr(10),$btns),'fieldset');
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Start table
					$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
					
					// Headers style
					$headerStyle = array('font-weight: bold');
					
					// Table headers
					$htmlCode[] = '<tr>';
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.name'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.files'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.size'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.path'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = '</tr>';
					
					// Process orphaned directories
					foreach($orphaned as $directory) {
						
						// Get content
						$sub = t3lib_div::getAllFilesAndFoldersInPath(array(),$this->directory . $directory . '/');
						
						// Size counter
						$size = 0;
						
						// Check array
						if (is_array($sub)) {
							
							// Process each subfile
							foreach($sub as $subFile) {
								
								// Add size
								$size += filesize($subFile);
							}
						}
						
						// Color
						$colorcount = ($colorcount == 1) ? 0: 1;
						$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
						
						// TR parameters
						$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'list_dir\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'list_dir\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'list_dir\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
						
						// Storage
						$dir = array();
						
						// Checkbox
						$dir[] = $this->writeHTML('<input type="checkbox" name="' . $GLOBALS['MCONF']['name'] . '[delete][]" id="list_dir" value="' . $directory . '">','td');
						
						// Icon
						$dir[] = $this->writeHTML('<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/i/sysf.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle">','td');
						
						// Dir name
						$dir[] = $this->writeHTML($directory,'td',false,array('font-weight: bold'));
						
						// Number of files
						$dir[] = $this->writeHTML(count($sub),'td');
						
						// Size in kilobytes
						$size = $size / 1024;
						
						// Suffix
						$sizeSuffix = $LANG->getLL('kilobyte');
						
						// Check size
						if ($size >= 1000) {
							
							// Size in megabytes
							$size = $size / 1024;
							
							// Suffix
							$sizeSuffix = $LANG->getLL('megabyte');
						}
						
						// Size
						$dir[] = $this->writeHTML(round($size,2) . ' ' . $sizeSuffix,'td');
						
						// Path
						$dir[] = $this->writeHTML($this->directory,'td');
						
						// View href
						$viewHref = t3lib_div::linkThisScript(array($GLOBALS['MCONF']['name'] . '[showDir]'=>$directory));
						
						// View icon with link
						$viewIcon = (is_array($sub) && count($sub)) ? '<a href="' . $viewHref . '"><img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/zoom.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle"></a>' : '';
						
						// View column
						$dir[] = $this->writeHTML($viewIcon,'td');
						
						// Add row
						$htmlCode[] = '<tr ' . $tr_params . '>' . implode(chr(10),$dir) . '</tr>';
						
						// Increase row count
						$rowcount++;
						
						// Cleat stats
						clearstatcache();
					}
					
					// End table
					$htmlCode[] = '</table>';
					
					// Divider
					$htmlCode[] = $this->doc->divider(5);
					
					// End section
					$htmlCode[] = $this->doc->sectionEnd();
				}
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Show valid directories
		 * 
		 * This function is used to display the list of the directories with
		 * valid users/groups associated.
		 * 
		 * @param		$section			The type of view (users or groups)
		 * @return		The list of the directories
		 */
		function showDirs($section) {
			global $LANG;
			
			// Database table to use
			$table = ($section == 'users') ? 'be_users' : 'be_groups';
			
			// Storage
			$htmlCode = array();
			
			// Check home directories
			if (is_array($this->homeDirs) && count($this->homeDirs)) {
				
				// Order by field
				$orderBy = ($section == 'users') ? 'username' : 'title';
				
				// Select backend users
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'deleted=0 AND uid IN (' . implode(',',$this->homeDirs) . ')',false,$orderBy);
				
				// Check ressource
				if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
					
					// Begin section
					$htmlCode[] = $this->doc->sectionBegin();
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Title
					$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('section.' . $section . '.show'));
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Counters
					$colorcount = 0;
					$rowcount = 0;
					
					// Start table
					$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
					
					// Headers style
					$headerStyle = array('font-weight: bold');
					
					// Table headers
					$htmlCode[] = '<tr>';
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.name'),'td',false,$headerStyle);
					
					// Check section
					if ($section == 'users') {
						
						// User
						$htmlCode[] = $this->writeHTML($LANG->getLL('user'),'td',false,$headerStyle);
						
					} else {
						
						// Group
						$htmlCode[] = $this->writeHTML($LANG->getLL('group'),'td',false,$headerStyle);
					}
					
					// Table headers
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.files'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.size'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML($LANG->getLL('dir.path'),'td',false,$headerStyle);
					$htmlCode[] = $this->writeHTML('','td',false,$headerStyle);
					$htmlCode[] = '</tr>';
					
					// Process users homes
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						
						// Get content
						$sub = t3lib_div::getAllFilesAndFoldersInPath(array(),$this->directory . $row['uid'] . '/');
						
						// Size counter
						$size = 0;
						
						// Check array
						if (is_array($sub)) {
							
							// Process each subfile
							foreach($sub as $subFile) {
								
								// Add size
								$size += filesize($subFile);
							}
						}
						
						// Color
						$colorcount = ($colorcount == 1) ? 0: 1;
						$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
						
						// TR parameters
						$tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'list_show\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'list_show\',\'out\',\'' . $color . '\');" bgcolor="' . $color . '"';
						
						// Storage
						$dir = array();
						
						// Icon
						$dir[] = $this->writeHTML('<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/i/sysf.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle">','td');
						
						// Dir name
						$dir[] = $this->writeHTML($row['uid'],'td',false,array('font-weight: bold'));
						
						// Check section
						if ($section == 'users') {
							
							// Label
							$label = (empty($row['realName'])) ? $row['username'] : $row['username'] . ' (' . $row['realName'] . ')';
							
							// User
							$dir[] = $this->writeHTML($label,'td');
							
						} else {
							
							// Group
							$dir[] = $this->writeHTML($row['title'],'td');
						}
						
						// Number of files
						$dir[] = $this->writeHTML(count($sub),'td');
						
						// Size in kilobytes
						$size = $size / 1024;
						
						// Suffix
						$sizeSuffix = $LANG->getLL('kilobyte');
						
						// Check size
						if ($size >= 1000) {
							
							// Size in megabytes
							$size = $size / 1024;
							
							// Suffix
							$sizeSuffix = $LANG->getLL('megabyte');
						}
						
						// Size
						$dir[] = $this->writeHTML(round($size,2) . ' ' . $sizeSuffix,'td');
						
						// Path
						$dir[] = $this->writeHTML($this->directory,'td');
						
						// View href
						$viewHref = t3lib_div::linkThisScript(array($GLOBALS['MCONF']['name'] . '[showDir]'=>$row['uid']));
						
						// View icon with link
						$viewIcon = (is_array($sub) && count($sub)) ? '<a href="' . $viewHref . '"><img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/zoom.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle"></a>' : '';
						
						// View column
						$dir[] = $this->writeHTML($viewIcon,'td');
						
						// Add row
						$htmlCode[] = '<tr ' . $tr_params . '>' . implode(chr(10),$dir) . '</tr>';
						
						// Increase row count
						$rowcount++;
						
						// Cleat stats
						clearstatcache();
					}
					
					// End table
					$htmlCode[] = '</table>';
					
					// Divider
					$htmlCode[] = $this->doc->divider(5);
					
					// End section
					$htmlCode[] = $this->doc->sectionEnd();
				}
			}
			
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
		function writeHTML($text,$tag='div',$class=false,$style=false) {
			
			// Create style tag
			if (is_array($style)) {
				$styleTag = ' style="' . implode('; ',$style) . '"';
			}
			
			// Create class tag
			if (isset($class)) {
				$classTag = ' class="' . $class . '"';
			}
			
			// Return HTML text
			return '<' . $tag . $classTag . $styleTag . '>' . $text . '</' . $tag . '>';
		}
		
		/**
		 * Create selector
		 * 
		 * This function creates a selector with BE users or BE groups with
		 * valid home directories.
		 * 
		 * @param		$table				The database table
		 * @return		The selector
		 */
		function buildSelector($table) {
			
			// Order by field
			$orderBy = ($table == 'be_users') ? 'username' : 'title';
			
			// Select BE users with home directories
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'uid IN (' . implode(',',$this->homeDirs) . ')',false,$orderBy);
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Storage
				$select = array();
				
				// Start select
				$select[] = '<select name="' . $GLOBALS['MCONF']['name'] . '[' . $table . ']">';
				
				// Process users
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Check table
					if ($table == 'be_users') {
						
						// Label
						$label = (empty($row['realName'])) ? $row['username'] : $row['username'] . ' (' . $row['realName'] . ')';
						
					} else {
						
						// Label
						$label = $row['title'];
					}
					
					// Create option
					$select[] = '<option value="' . $row['uid'] . '">' . $label . '</option>';
				}
				
				// End select
				$select[] = '</select>';
				
				// Return select
				return implode(chr(10),$select);
			}
		}
		
		/**
		 * Process actions
		 * 
		 * This function process action after the form has been submitted.
		 * 
		 * @param		$section			The type of view (users or groups)
		 * @return		Void
		 */
		function processActions($section) {
			
			// Database table to use
			$table = ($section == 'users') ? 'be_users' : 'be_groups';
			
			// Action
			$action = t3lib_div::_POST('action');
			
			// Section
			$section = $this->MOD_SETTINGS['function'];
			
			// POST variable for the module
			$POST = t3lib_div::_POST($GLOBALS['MCONF']['name']);
			
			// Check action
			switch($action) {
				
				// Create
				case 'create':
					
					// Elements to process
					$el = $POST['create'];
					
					// Process users
					foreach($el as $id) {
						
						// Create directory
						t3lib_div::mkdir($this->directory . $id);
					}
					
				break;
				
				// Move
				case 'delete':
					
					// Elements to process
					$el = $POST['delete'];
					
					// Check processing type
					switch($POST['process']) {
						
						// Move
						case '0':
							
							// Process directories
							foreach($el as $dir) {
								
								// Move directory
								rename($this->directory . $dir,$this->directory . 'zzz_deleted_' . $dir);
							}
						break;
						
						// Delete
						case '1':
							
							// Process directories
							foreach($el as $dir) {
								
								// Delete directory
								$this->api->div_rmdir($this->directory . $dir . '/');
							}
						break;
						
						// Assign
						case '2':
							
							// Process directories
							foreach($el as $dir) {
								
								// Move directory
								rename($this->directory . $dir,$this->directory . $POST[$table] . '/' . 'backup_' . $dir);
							}
						break;
					}
					
				break;
			}
		}
		
		/**
		 * Check settings
		 * 
		 * This function checks the basic settings for the module. It checks if
		 * the base directory for users or groups exists and is writeable.
		 * 
		 * @param		$tyoe				The type of view (users or groups)
		 * @return		Error if any
		 */
		function checkSettings($type) {
			global $LANG;
			
			// Section in Typo3 conf
			$section = ($type == 'users') ? 'userHomePath' : 'groupHomePath';
			
			// Storage
			$htmlCode = array();
			
			// Check settings
			if (empty($GLOBALS['TYPO3_CONF_VARS']['BE'][$section])) {
				
				// No configuration
				$htmlCode[] =  $this->writeHTML(sprintf($LANG->getLL('error.conf'),$section),'div','typo3-red',array('font-weight: bold'));
				
			} else {
				
				// Set working directory
				$this->directory = t3lib_div::getFileAbsFileName($GLOBALS['TYPO3_CONF_VARS']['BE'][$section]);
				
				// Get parent directory
				$parentSegments = t3lib_div::removeArrayEntryByValue(explode('/',$this->directory),'');
				
				// Relative directory
				$relDir = array_pop($parentSegments);
				
				// Parent directory
				$parentDir = '/' . implode('/',$parentSegments);
				
				// Check if a directory must be created
				if ((empty($this->directory) || !@file_exists($this->directory)) && is_array($this->getVars) && array_key_exists('createDef',$this->getVars)) {
					
					// Create new directory
					t3lib_div::mkdir($this->directory);
				}
				
				// Check if directory exists
				if (empty($this->directory) || !@file_exists($this->directory)) {
					
					// No directory
					$htmlCode[] =  $this->writeHTML(sprintf($LANG->getLL('error.nodir'),$this->directory),'div','typo3-red',array('font-weight: bold'));
					
					// Check if parent directory is writeable
					if (@is_writeable($parentDir)) {
						
						// Link HREF
						$href =  t3lib_div::linkThisScript(array($GLOBALS['MCONF']['name'] . '[createDef]'=>1));
						
						// Create directory
						$htmlCode[] =  $this->writeHTML('<a href="' . $href . '">' . $LANG->getLL('create.auto') . '</a>','div');
						
					} else {
						
						// Directory must be created manually
						$htmlCode[] =  $this->writeHTML($LANG->getLL('create.manual'),'div');
					}
				} else {
					
					// Check if directory is writeable
					if (!@is_writeable($this->directory)) {
						
						// Directory is not writeable
						$htmlCode[] =  $this->writeHTML(sprintf($LANG->getLL('error.nowrite'),$this->directory),'div','typo3-red',array('font-weight: bold'));
					}
				}
			}
			
			// Check for errors
			if (count($htmlCode)) {
				
				// Return errors
				return implode(chr(10),$htmlCode);
			}
		}
		
		/**
		 * Exculude invalid directories
		 * 
		 * This function is called by the PHP array_filter function. It's used
		 * to validate the home directories by returning only valid directories.
		 * 
		 * @param		$var				The directory name
		 * @return		True if the name is valid (integer), false otherwise
		 */
		function excludeDeleted($var) {
			
			// Variable as integer
			$id = (int) $var;
			
			// Return id 
			return $id;
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/homedirs/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/homedirs/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_homedirs_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	$SOBE->main();
	$SOBE->printContent();
?>
