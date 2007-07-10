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
	 * Class/Function which manipulates the item-array for table/field be_users.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.1
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      51:		function main(&$params,&$pObj)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
	class tx_weblinks_handleBeUsers {
	
		/**
		 * Adds BE-Users to selector box items arrays.
		 * Adds ALL available BE-Users, depending of the group(s) of teh current user.
		 *
		 * @param	$params				Array of items passed by reference.
		 * @param	$pObj				The parent object (t3lib_TCEforms / t3lib_transferData depending on context)
		 * @return	Void
		 */
		function main(&$params,&$pObj) {
			
			// Additionnal MySQL WHERE clause for selecting BE-Users
			$whereClause = array();
			
			// Checking admin privileges
			if (!$GLOBALS['BE_USER']->isAdmin()) {
				
				// Get group ID(s) from current user
				$usergroup = explode(',',$GLOBALS['BE_USER']->user['usergroup']);
				
				// Process each group
				foreach($usergroup as $gid) {
					$whereClause[] = $GLOBALS['TYPO3_DB']->listQuery('usergroup',$gid,'be_users');
				}
				
				// Create the complete WHERE clause
				$addWhere = ' AND ' . implode(' OR ',$whereClause);
			}
			
			// Get available BE-Users
			$be_users = t3lib_BEfunc::getRecordsByField('be_users','deleted','0',$addWhere);
			
			// Add BE-Users to selector
			foreach($be_users as $user) {
				
				// Only select users with email addresses
				if ($user['email']) {
					
					// Label
					$label = $user['username'];
					
					// Complete label if possible
					if ($user['realName']) {
						
						// Add real name
						$label = $user['realName'] . ' (' . $user['username'] . ')';
					}
					
					// Get user icon
					$icon = t3lib_iconWorks::getIcon('be_users',$user);
					
					// Skinning support
					$skinIcon = t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],$icon,'',1);
					
					// Suppress gfx/ prefix if original icon
					$optionIcon = ($icon == $skinIcon) ? str_replace('gfx/','',$icon) : $skinIcon;
					
					// Complete label if possible
					$params['items'][] = array($label,$user['uid'],$optionIcon);
				}
			}
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weblinks/class.tx_weblinks_handlebeusers.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weblinks/class.tx_weblinks_handlebeusers.php']);
	}
?>
