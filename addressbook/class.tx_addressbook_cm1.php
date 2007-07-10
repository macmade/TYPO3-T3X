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
	 * @author		Jean-David Gadina (info@macmade.net)
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
	
	class tx_addressbook_cm1 {
		
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
			
			// Detect menu level
			if (!$backRef->cmLevel) {
				
				// Item is a page and is editable
				if ($backRef->editOK && ($table == 'tx_addressbook_groups' || $table == 'tx_addressbook_addresses')) {
					
					// Language file
					$LL = $this->includeLL();
					
					// Add spacer
					$localItems[] = 'spacer';
					
					// URL
					$url = t3lib_extMgm::extRelPath('addressbook') . 'mod1/index.php?action=export&table=' . $table . '&uid=' . $uid;
					
					// Add container
					$localItems['tx_setpagetype_cm1'] = $backRef->linkItem(
						$LANG->getLLL('cm1_title',$LL),
						$backRef->excludeIcon('<img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],t3lib_extMgm::extRelPath('addressbook') . 'res/icon_cm1.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle">'),
						$backRef->urlRefForCM($url,'returnUrl'),
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
			include(t3lib_extMgm::extPath('addressbook') . 'locallang.php');
			
			// Return array
			return $LOCAL_LANG;
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/addressbook/class.tx_addressbook_cm1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/addressbook/class.tx_addressbook_cm1.php']);
	}
?>
