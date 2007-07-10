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
	 *      57:		function main(&$backRef,$menuItems,$table,$uid)
	 *     240:		includeLL
	 * 
	 *				TOTAL FUNCTIONS: 2
	 */
	
	class tx_setpagetype_cm1 {
		
		/**
		 * Add items to the clickmenu
		 * 
		 * This function adds an item to the Typo3 click menu, with a sublevel
		 * to directly define a page doktype.
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
			
			// CSM items storage
			$localItems = Array();
			
			// Sublevel name
			$subname = t3lib_div::_GP('subname');
			
			// Detect menu level
			if (!$backRef->cmLevel) {
				
				// Item is a page and is editable
				if ($backRef->editOK && $table == 'pages') {
					
					// Language file
					$LL = $this->includeLL();
					
					// Add spacer
					$localItems[] = 'spacer';
					
					// Add container
					$localItems['tx_setpagetype_cm1'] = $backRef->linkItem(
						$LANG->getLLL('cm1_title',$LL),
						$backRef->excludeIcon(''),
						'top.loadTopMenu(\'' . t3lib_div::linkThisScript() . '&cmLevel=1&subname=tx_setpagetype_cm1\'); return false;',
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
						
						// Find "cut" item
						if (!strcmp($k,"cut")) {
							
							// Exit
							break;
						}
					}
					
					// Insert menu
					array_splice($menuItems,$c,0,$localItems);
					
					// Return menu items
					return $menuItems;
				}
			} else if ($subname == 'tx_setpagetype_cm1') {
				
				// Second level
				if ($backRef->editOK && $table == 'pages') {
					
					// Page types
					$doktypes = array(
						
						// Standard
						'1' => array(
							'type' => 'standard',
							'icon' => 'gfx/i/pages.gif',
							'label' => 'LLL:EXT:lang/locallang_tca.php:doktype.I.0',
						),
						
						// Advanced
						'2' => array(
							'type' => 'advanced',
							'icon' => 'gfx/i/pages.gif',
							'label' => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.0',
						),
						
						// External URL
						'3' => array(
							'type' => 'link',
							'icon' => 'gfx/i/pages_link.gif',
							'label' => 'LLL:EXT:lang/locallang_general.php:LGL.external',
						),
						
						// Shortcut
						'4' => array(
							'type' => 'shortcut',
							'icon' => 'gfx/i/pages_shortcut.gif',
							'label' => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.2',
						),
						
						// Not in menu
						'5' => array(
							'type' => 'notinmenu',
							'icon' => 'gfx/i/pages_notinmenu.gif',
							'label' => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.3',
						),
						
						// Backend user section
						'6' => array(
							'type' => 'backendusersection',
							'icon' => 'gfx/i/be_users_section.gif',
							'label' => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.4',
						),
						
						// Mount point
						'7' => array(
							'type' => 'mountpoint',
							'icon' => 'gfx/i/pages_mountpoint.gif',
							'label' => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.5',
						),
						
						// Spacer
						'199' => array(
							'type' => 'spacer',
							'icon' => 'gfx/i/spacer_icon.gif',
							'label' => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.7',
						),
						
						// SysFolder
						'254' => array(
							'type' => 'sysfolder',
							'icon' => 'gfx/i/sysf.gif',
							'label' => 'LLL:EXT:lang/locallang_tca.php:doktype.I.1',
						),
						
						// Recycler
						'255' => array(
							'type' => 'recycler',
							'icon' => 'gfx/i/recycler.gif',
							'label' => 'LLL:EXT:lang/locallang_tca.php:doktype.I.2',
						),
					);
					
					// Create a menu item for each page type
					foreach($doktypes as $key=>$value) {
						
						// Check user permissions
						if ($GLOBALS['BE_USER']->isAdmin() || t3lib_div::inList($GLOBALS['BE_USER']->groupData['pagetypes_select'],$key)) {
							
							// On click parameter
							$editOnClick = array();
							
							// Location
							$loc = 'top.content' . (($backRef->listFrame && !$backRef->alwaysContentFrame) ? '.list_frame' : '');
							
							// Create on click parameter
							$editOnClick[] = 'if (' . $loc . ') {';
							$editOnClick[] = $loc . '.document.location=top.TS.PATH_typo3+\'tce_db.php?';
							$editOnClick[] = 'redirect=\'+top.rawurlencode(' . $backRef->frameLocation($loc . '.document') . ')+';
							$editOnClick[] = '\'&data[pages]['.$uid.'][doktype]='. $key;
							$editOnClick[] = '&prErr=1';
							$editOnClick[] = '&vC=' . $GLOBALS['BE_USER']->veriCode() . '\';';
							$editOnClick[] = 'hideCM();';
							$editOnClick[] = '}';
							
							// Add menu item
							$localItems[$value['type']] = $backRef->linkItem(
								$LANG->sL($value['label']),
								$backRef->excludeIcon('<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],$value['icon'],'') . ' alt="" hspace="0" vspace="0" border="0" align="middle">'),
								implode('',$editOnClick) . ' return false;',
								0
							);
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
			include(t3lib_extMgm::extPath('setpagetype') . 'locallang.php');
			
			// Return array
			return $LOCAL_LANG;
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/setpagetype/class.tx_setpagetype_cm1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/setpagetype/class.tx_setpagetype_cm1.php']);
	}
?>
