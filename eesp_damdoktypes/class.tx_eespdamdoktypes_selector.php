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
	 * Additionnal DAM selection rule for the 'eesp_damdoktypes' extension.
	 * 
	 * This class is based on the extension 'dam_demo', created by RenŽ Fritz.
	 *
	 * @author		Jean-David Gadina <info@macmade.net>
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 *        :		tx_eespdamdoktypes_selector
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// DAM selector helper
	require_once(PATH_txdam.'lib/class.tx_dam_selprocbase.php');
	
	class tx_eespdamdoktypes_selector extends tx_dam_browseTree {
		
		/**
		 * Class constructor
		 * 
		 * @return		Void
		 */
		function tx_eespdamdoktypes_selector() {
			global $LANG;
			
			// Title
			$this->title = $LANG->sL('LLL:EXT:eesp_damdoktypes/locallang_db.xml:tx_dam.tx_eespdamdoktypes_doktype');
			
			// Tree name
			$this->treeName = 'txeespdamdoktypes';
			
			// DOM prefix ID
			$this->domIdPrefix = $this->treeName;
			
			// Icon file
			$this->iconName = 'icon_tx_eespdamdoktypes_types.gif';
			
			// Icon path
			$this->iconPath = t3lib_extMgm::extRelPath('eesp_damdoktypes');
			
			// Root icon
			$this->rootIcon = $this->iconPath . 'res/icon_cat.gif';
		}
		
		/**
		 * Gets the tree data
		 * 
		 * This function is used to initialize the tree data for the selector.
		 *
		 * @param		parentId			The ID of the parent item
 		 * @return		The tree data
		 */
		function getDataInit($parentId) {
			
			// Check for existing tree data
			if (!is_array($this->data)) {
				
				// Get selector tree
				$this->_data = $this->getTreeArray();
				
				// Set selector
				$this->setDataFromArray($this->_data);
			}
			
			// Return data
			return parent::getDataInit($parentId);
		}
		
		/**
		 * Get tree
		 * 
		 * This function gets all the available document types from the database
		 * for usage in a DAM selector.
		 * 
		 * @return		The tree array
		 */
		function getTreeArray() {
			
			// Select doktypes
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_eespdamdoktypes_types','deleted=0');
			
			// Storage
			$tree = array();
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Process records
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Add record to tree
					$tree[$row['uid']] = array('title'=>$row['type']);
				}
			}
			
			// Return tree
			return $tree;
		}
	
	
		/**
		 * Returns an item title
		 * 
		 * This function returns the title of an element of the DAM selector
		 * tree.
		 * 
		 * @param		$id					The ID of the tree element
		 * @return		The title of the tree item
		 * @see			getTreeArray
		 */
		function selection_getItemTitle($id) {
			
			// Get selector tree
			$tree = $this->getTreeArray();
			
			// Return element title
			return $tree[$id]['title'];
		}
	
		/**
		 * Query processing
		 * 
		 * This function is used to process the SQL query for the DAM selector.
		 * 
		 * @param		$queryType			The query type (AND, OR, etc)
		 * @param		$operator			The operator
		 * @param		$cat				The category (treename)
		 * @param		$id					The select value/id
		 * @param		$value				The select value (true, false, etc)
		 * @param		$damObj				Reference to the parent DAM object
		 * @return		An array with the query
		 */
		function selection_getQueryPart($queryType,$operator,$cat,$id,$value,&$damObj) {
			
			// Database field
			$query = 'tx_dam.tx_eespdamdoktypes_doktype';
			
			// Check for exclusion
			if($queryType == 'NOT') {
				
				// Add exclusion word
				$query .= ' NOT';
			}
			
			// Create query
			$query .= ' LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($id,'tx_dam');
			
			// Return query
			return array($queryType,$query);
		}
	
	}
	
	// XClass inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_damdoktypes/class.tx_eespdamdoktypes_selector.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_damdoktypes/class.tx_eespdamdoktypes_selector.php']);
	}
?>
