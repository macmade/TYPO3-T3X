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
	 * Addition of an item to the clickmenu
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      59:		function main(&$backRef,$menuItems,$table,$uid)
	 *     300:		function includeLL
	 *     317:		function isTableAllowedForThisPage($pid_row, $checkTable)
	 *     369:		function showNewRecLink($table,$allowedNewTables='')
	 * 
	 *				TOTAL FUNCTIONS: 4
	 */
	
	class tx_newrecordwiz_cm1 {
		
		/**
		 * Add items to the clickmenu
		 * 
		 * This function adds an item to the Typo3 click menu, with a sublevel
		 * to directly create a content element.
		 * 
		 * @param		$backRef	The source table
		 * @param		$menuItems	The destination table
		 * @param		$table		The table of the click item
		 * @param		$uid		The UID of the click item
		 * @return		An array with menu items
		 * @see			includeLL
		 */
		function main(&$backRef,$menuItems,$table,$uid) {
			global $BE_USER,$TCA,$LANG;
			
			// Extension configuration
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['newrecordwiz']);
			
			// CSM items storage
			$localItems = Array();
			
			// Sublevel name
			$subname = t3lib_div::_GP('subname');
			
			// Detect menu level
			if (!$backRef->cmLevel) {
				
				// Item is a page and is editable
				if ($table == 'pages') {
					
					// Language file
					$LL = $this->includeLL();
					
					// Add container
					$localItems['tx_newrecordwiz_cm1'] = $backRef->linkItem(
						$LANG->getLLL('cm1_title',$LL),
						$backRef->excludeIcon('<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_el.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle">'),
						'top.loadTopMenu(\'' . t3lib_div::linkThisScript() . '&cmLevel=1&subname=tx_newrecordwiz_cm1\'); return false;',
						0,
						1
					);
					
					// Reset items array
					reset($menuItems);
					
					// Counter
					$c = 0;
					
					// Traverse items array
					while(list($k) = each($menuItems)) {
						
						// Increase counter
						$c++;
						
						// Find "new" item
						if (!strcmp($k,'new')) {
							
							// Exit
							break;
						}
					}
					
					// Remove obsolete "New" CSM item?
					if ($extConf['remove']) {
						
						// Unset 
						unset($menuItems['new']);
						
						// Decrease menu position
						$c--;
					}
					
					// Insert menu
					array_splice($menuItems,$c,0,$localItems);
					
					// Return menu items
					return $menuItems;
				}
			} else if ($subname == 'tx_newrecordwiz_cm1') {
				
				// Second level
				if ($table == 'pages') {
					
					// Permissions storage
					$this->permissions = array(
						'record' => 0,		// User can create table records
						'pageInside' => 0,	// User can create a page in the clicked page
						'pageAfter' => 0,	// User can create a page after the clicked page
					);
					
					// Permission clause for BE user
					$this->permsClause = $BE_USER->getPagePermsClause(1);
					
					// Check ID
					if ($uid > 0) {
						
						// Get permissions for page records
						$this->pageinfo = t3lib_BEfunc::readPageAccess($uid,$this->perms_clause);
					}
					
					// Check access
					if ($this->pageinfo['uid']) {
						
						// List module TS config
						$this->listTSconfig = t3lib_BEfunc::getModTSconfig($this->pageinfo['uid'],'mod.web_list');
						
						// Database elements which can be created
						$this->createNew = t3lib_div::trimExplode(',',$this->listTSconfig['properties']['allowedNewTables'],1);
						
						// Get record for parent page
						$this->pidInfo = t3lib_BEfunc::getRecord('pages',$this->pageinfo['pid']);
						
						// Check permissions for new content into
						if ($BE_USER->doesUserHaveAccess($this->pageinfo,16)) {
							
							// User can create content into
							$this->permissions['pagerecord'] = 1;
						}
						
						// Check permissions for new pages into
						if ($BE_USER->doesUserHaveAccess($this->pageinfo,8)) {
							
							// User can create pages into
							$this->permissions['pageInside'] = 1;
						}
						
						// Check permissions for new pages after
						if (($BE_USER->isAdmin() || is_array($this->pidInfo)) && $BE_USER->doesUserHaveAccess($this->pidInfo,8)) {
							
							// User can create pages after
							$this->permissions['pageAfter'] = 1;
						}
						
					} else if ($BE_USER->isAdmin()) {
						
						// Admin specific rights for site root
						$this->permissions['pagerecord'] = 1;
						$this->permissions['pageInside'] = 1;
						$this->permissions['pageAfter'] = 0;
					
					} else {
						
						// No permission
						$this->permissions['pagerecord'] = 0;
						$this->permissions['pageInside'] = 0;
						$this->permissions['pageAfter'] = 0;
					}
					
					// New page inside the clicked page
					if ($this->permissions['pageInside'] && $this->isTableAllowedForThisPage($this->pageinfo,'pages') && $BE_USER->check('tables_modify','pages')) {
						
						// URL for page creation
						$url = 'alt_doc.php?edit[pages][' . $uid . ']=new';
						
						// Add menu item
						$localItems['pagesInside'] = $backRef->linkItem(
							$LANG->sL($TCA['pages']['ctrl']['title']) . ' (' . $LANG->sL('LLL:EXT:lang/locallang_core.php:db_new.php.inside') . ')',
							$backRef->excludeIcon('<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_page.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle">'),
							$backRef->urlRefForCM($url),
							0
						);
						
						// Url for page wizard
						$url = 'db_new.php?pagesOnly=1&id=' . $uid;
						
						// Add wizard item
						$localItems['pagesInsideWiz'] = $backRef->linkItem(
							$LANG->sL('LLL:EXT:lang/locallang_misc.php:clickForWizard'),
							$backRef->excludeIcon('<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_page.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle">'),
							$backRef->urlRefForCM($url),
							0
						);
					}
					
					// New page after the clicked page
					if ($this->permissions['pageAfter'] && $this->isTableAllowedForThisPage($this->pidInfo,'pages') && $BE_USER->check('tables_modify','pages')) {
						
						// URL for page creation
						$url = 'alt_doc.php?edit[pages][' . (-$uid) . ']=new';
						
						// Add menu item
						$localItems['pagesAfter'] = $backRef->linkItem(
							$LANG->sL($TCA['pages']['ctrl']['title']) . ' (' . $LANG->sL('LLL:EXT:lang/locallang_core.php:db_new.php.after') . ')',
							$backRef->excludeIcon('<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_page.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle">'),
							$backRef->urlRefForCM($url),
							0
						);
					}
					
					// New table record inside the clicked page
					if ($this->permissions['pagerecord']) {
						
						// Spacer
						$localItems[] = 'spacer';
						
						// Check for TCA
						if (is_array($TCA)) {
							
							// Process each TCA entry
							foreach($TCA as $key => $value) {
								
								// Check if item can be created
								if ($key != 'pages' && $this->showNewRecLink($key) && $this->isTableAllowedForThisPage($this->pageinfo,$key) && $BE_USER->check('tables_modify',$key) && (($value['ctrl']['rootLevel'] xor $uid) || $value['ctrl']['rootLevel'] == -1)) {
									
									// Link to wizard for content table
									if ($key =='tt_content') {
										
										// Check if default wizard must be overridden by an extension (like TV)
										$overrideExt = $this->listTSconfig['properties']['newContentWiz.']['overrideWithExtension'];
										
										// Path to the wizard
										$pathToWizard = (t3lib_extMgm::isLoaded($overrideExt)) ? (t3lib_extMgm::extRelPath($overrideExt) . 'mod1/db_new_content_el.php') : 'sysext/cms/layout/db_new_content_el.php';
										
										// URL
										$url = $pathToWizard.'?id=' . $uid;
										
									} else {
										
										// Usual database records
										$url = 'alt_doc.php?edit[' . $key . '][' . $uid . ']=new';
									}
									// Add menu item
									$localItems[$key] = $backRef->linkItem(
										$LANG->sL($value['ctrl']['title']),
										t3lib_iconWorks::getIconImage($key,array(),$GLOBALS['BACK_PATH']),
										$backRef->urlRefForCM($url),
										0
									);
								}
							}
						}
					}
					
					// Overwrite menu items
					$menuItems = $localItems;
					
					// Return menu items
					return $menuItems;
				}
			}
			
			// Return menu items
			return $menuItems;
		}
		
		/**
		 * Includes the locallang file for the extension.
		 * 
		 * This function includes the locallang.php file of this extension, and
		 * returns the $LOCAL_LANG array.
		 * 
		 * @return		The locallang array
		 */
		function includeLL() {
			
			// Include locallang file
			include(t3lib_extMgm::extPath('newrecordwiz') . 'locallang.php');
			
			// Return array
			return $LOCAL_LANG;
		}
		
		/**
		 * Returns true if the tablename $checkTable is allowed to be created on the page with record $pid_row
		 *
		 * @param		$pid_row				Record for parent page.
		 * @param		$checkTable				Table name to check
		 * @return		Returns true if the tablename $checkTable is allowed to be created on the page with record $pid_row
		 * @author		Kasper Skaarhoj (kasperYYYY@typo3.com)
		 */
		function isTableAllowedForThisPage($pid_row, $checkTable) {
			global $TCA, $PAGES_TYPES;
			
			// Check record
			if (!is_array($pid_row)) {
				
				// Check admin rights
				if ($GLOBALS['BE_USER']->user['admin']) {
					
					// Access
					return true;
					
				} else {
					
					// No access
					return false;
				}
			}
			
			// BE users and BE groups may not be created anywhere but in the root
			if ($checkTable == 'be_users' || $checkTable == 'be_groups') {
				
				// No access
				return false;
			}
			
			// Check doktype
			$doktype = intval($pid_row['doktype']);
			
			// Check allowed tables for the current doktype
			if (!$allowedTableList = $PAGES_TYPES[$doktype]['allowedTables']) {
				
				// Get default available tables
				$allowedTableList = $PAGES_TYPES['default']['allowedTables'];
			}
			
			// Check permissions
			if (strstr($allowedTableList,'*') || t3lib_div::inList($allowedTableList,$checkTable)) {
				
				// Access
				return true;
			}
		}
		
		/**
		 * Returns true if the $table tablename is found in $allowedNewTables (or if $allowedNewTables is empty)
		 *
		 * @param		$table					Table name to test if in allowedTables
		 * @param		$allowedNewTables		Array of new tables that are allowed
		 * @return		Returns true if the $table tablename is found in $allowedNewTables (or if $allowedNewTables is empty)
		 * @author		Kasper Skaarhoj (kasperYYYY@typo3.com)
		 */
		function showNewRecLink($table,$allowedNewTables='') {
			
			// Allowe tables
			$allowedNewTables = is_array($allowedNewTables) ? $allowedNewTables : $this->allowedNewTables;
			
			// Return access
			return !count($allowedNewTables) || in_array($table,$allowedNewTables);
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/newrecordwiz/class.tx_newrecordwiz_cm1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/newrecordwiz/class.tx_newrecordwiz_cm1.php']);
	}
?>
