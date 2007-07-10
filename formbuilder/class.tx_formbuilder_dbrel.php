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
	 * Class/Function which manipulates the item-array for the
	 * 'database' flexform field of the 'xmlds' field.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      54:		function main(&$params,&$pObj)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
	class tx_formbuilder_dbRel {
	
		/**
		 * List DB tables
		 * 
		 * This function adds all the available tables to selector
		 * box items arrays.
		 *
		 * @param		array		Array of items passed by reference.
		 * @param		object		The parent object (t3lib_TCEforms / t3lib_transferData depending on context)
		 * @return		void
		 */
		function main(&$params,&$pObj) {
			global $TCA,$LANG;
			
			// Traverse TCA array
			while (list($table) = each($TCA)) {
				
				// Load TCA for current table
				t3lib_div::loadTCA($table);
				
				// Table configuration
				$config = $TCA[$table];
				
				// Label to display in the select
				$label = $LANG->sL($config['ctrl']['title']) . ' (' . $table . ')';
				
				// Add select item
				$params['items'][] = array($label,$table);
			}
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formbuilder/class.tx_formbuilder_dbrel.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formbuilder/class.tx_formbuilder_dbrel.php']);
	}
?>
