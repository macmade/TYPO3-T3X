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
	 * Class/Function which manipulates the item-array for table/field
	 * tx_classifiedsmacmade_categories.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      52:		function main(&$params,&$pObj)
	 *     104:		function getChildCategories($parent,$title,&$params)
	 * 
	 *				TOTAL FUNCTIONS: 2
	 */
	
	class tx_classifiedsmacmade_handleCategories {
		
		/**
		 * Adds categories to selector box items arrays.
		 * 
		 * @param	$params		Array of items passed by reference.
		 * @param	$pObj		The parent object (t3lib_TCEforms / t3lib_transferData depending on context)
		 * @return	void
		 */
		function main(&$params,&$pObj) {
			
			// Parent object TS config
			$pObjTSConfig = $pObj->cachedTSconfig;
			
			// DB table
			$this->table = 'tx_classifiedsmacmade_categories';
			
			// Get infos about active record
			$this->activeRecord = explode(':',array_shift(array_keys($pObjTSConfig)));
			
			// UID of the current record
			$this->recUid = $this->activeRecord[1];
			
			// Delete clause
			$this->deleteClause = t3lib_BEfunc::deleteClause($table);
			
			// Add WHERE clause to avoid getting current category
			if ($this->activeRecord[0] == $this->table && intval($this->recUid)) {
				$this->deleteClause .= ' AND ' . $this->table . '.uid!=' . $this->recUid;
				
			}
			// Get root categories
			$categories = t3lib_BEfunc::getRecordsByField($this->table,'parent','0',$this->deleteClause,false,'title');
			
			// Root categories found
			if (is_array($categories)) {
				
				// Process each category
				foreach($categories as $cat) {
					
					// Get child categories
					$childs = $this->getChildCategories($cat['uid'],$cat['title'],$params);
					
					// Check if current record is a category
					if ($this->activeRecord[0] == $this->table || !$childs) {
						
						// Add root category
						$params["items"][] = array($cat['title'], $cat['uid']);
					}
				}
			}
		}
		
		/**
		 * Adds child categories to selector box items arrays.
		 * 
		 * @param	$parent		The UID of the parent category
		 * @param	$title		The title of the parent category
		 * @param	$params		Array of items passed by reference.
		 * @return	void
		 */
		function getChildCategories($parent,$title,&$params) {
			
			// Get child categories
			$categories = t3lib_BEfunc::getRecordsByField($this->table,'parent',$parent,$this->deleteClause,false,'title');
			
			// Category has sub-categories
			if (is_array($categories)) {
				
				// Process each category
				foreach($categories as $cat) {
					
					// Complete title (with parent category)
					$fullTitle = $title . ' > ' . $cat['title'];
					
					// Get child categories
					$childs = $this->getChildCategories($cat['uid'],$fullTitle,$params);
					
					// Check if current record is a category
					if ($this->activeRecord[0] == $this->table || !$childs) {
						
						// Add child category
						$params["items"][] = array($fullTitle, $cat['uid']);
					}
				}
				
				// Return true
				return true;
			}
		}
	}
	
	// XCLASS inclusion
	if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/classifieds_macmade/class.tx_classifiedsmacmade_tx_classifiedsmacmade_ads_category.php"])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/classifieds_macmade/class.tx_classifiedsmacmade_tx_classifiedsmacmade_ads_category.php"]);
	}
?>
