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
	 * Plugin 'Web links' for the 'weblinks' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *        :		function main($content,$conf)
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_weblinks_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_weblinks_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_weblinks_pi1.php';
		
		// The extension key
		var $extKey = 'weblinks';
		
		// Database tables
		var $extTables = array(
			
			// Ads
			'links' => 'tx_weblinks_links',
			
			// Categories
			'categories' => 'tx_weblinks_categories',
		);
		
		// Upload folder
		var $uploadFolder = 'uploads/tx_weblinks/';
		
		// Internal variables
		var $searchFields = 'title,url,description';
		var $orderByFields = 'title';
		
		// Version of the Developer API required
		var $apimacmade_version = 1.6;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin 'tx_formbuilder_pi1', and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin
		 */
		function main($content,$conf) {
			
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Set default plufin variables
			$this->pi_setPiVarDefaults();
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// Init flexform configuration of the plugin
			$this->pi_initPIflexForm();
			
			// Store flexform informations
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
			
			// Set final configuration (TS or FF)
			$this->setConfig();
			
			// Storage
			$htmlCode = array();
			
			// Check for required values
			if ($this->conf['pidList']) {
				
				// Get an comma list of all the pages from where to select records
				$this->startingPoints = $this->pi_getPidList($this->conf['pidList'],$this->conf['recursive']);
				
				// MySQL query init
				$this->setInternalVars();
				
				// Check template file (TS or Flex)
				$templateFile = ($this->pi_getFFvalue($this->piFlexForm,'template_file','sTMPL') == '') ? $this->conf['templateFile'] : $this->uploadFolder . $this->conf['templateFile'];
				
				// Template load and init
				$this->api->fe_initTemplate($templateFile);
				
				// List view
				$code = $this->buildList();
				
			} else {
				
				// No starting point defined
				$code = '<strong>' . $this->pi_getLL('error.conf') . '</strong>';
			}
			
			// Return content
			return $this->pi_wrapInBaseClass($code);
		}
		
		/**
		 * Set configuration array.
		 * 
		 * This function is used to set the final configuration array of the
		 * plugin, by providing a mapping array between the TS & the flexform
		 * configuration.
		 * 
		 * @return		Void
		 */
		function setConfig() {
			
			// Mapping array for PI flexform
			$flex2conf = array(
				'pidList' => 'sDEF:pages',
				'recursive' => 'sDEF:recursive',
				'catMenu.' => array(
					'expandLevels' => 'sCAT:expand_levels',
				),
				'list.' => array(
					'sortBy' => 'sLIST:sort_by',
					'maxRecords' => 'sLIST:max_records',
					'maxPages' => 'sLIST:max_pages',
				),
				'templateFile' => 'sTMPL:template_file',
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'Web Links: configuration array');
		}
		
		/**
		 * Sets internals variables.
		 * 
		 * This function is used to set the internal variables array
		 * ($this->internal) needed to execute a MySQL query.
		 * 
		 * @return		Nothing
		 */
		function setInternalVars() {
			
			// SORT BY
			$this->piVars['sort'] = $this->conf['list.']['sortBy'];
			
			// Set general internal variables
			$this->api->fe_setInternalVars($this->conf['list.']['maxRecords'],$this->conf['list.']['maxPages'],$this->searchFields,$this->orderByFields);
		}
		
		
		/**
		 * 
		 */
		function buildList() {
			
			// No active page
			if (!isset($this->piVars['pointer'])) {
				$this->piVars['pointer'] = 0;
			}
			
			// Additionnal MySQL WHERE clause for filters
			$whereClause = '';
			
			// Categories selection
			$whereClause .= ($this->piVars['showCat']) ? ' AND' . $GLOBALS['TYPO3_DB']->listQuery('category',$this->piVars['showCat'],$this->extTables['links']) : '';
			
			// Get records number
			$res = $this->pi_exec_query($this->extTables['links'],1,$whereClause);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			// Make listing query - Pass query to MySQL database
			$res = $this->pi_exec_query($this->extTables['links'],0,$whereClause);
			$this->internal['currentTable'] = $this->extTables['links'];
			
			// Template markers
			$templateMarkers = array();
			
			// Overwriting template markers
			$templateMarkers['###CATMENU###'] = $this->buildCatMenu();
			$templateMarkers['###SEARCHBOX###'] = $this->pi_list_searchBox();
			$templateMarkers['###BROWSEBOX###'] = $this->pi_list_browseresults();
			$templateMarkers['###LIST###'] = $this->makeList($res);
			
			// Wrap all in a CSS element
			$content = $this->api->fe_renderTemplate($templateMarkers,'###MAIN###');
			
			// Return content
			return $content;
		}
		
		
		/***************************************************************
		 * SECTION 2 - CAT MENU
		 *
		 * Construction of the categories menu.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function buildCatMenu() {
			
			// Store sword
			$this->tempSword = $this->piVars['sword'];
			
			// Temporary unset sword to avoid problems with the category menu
			unset($this->piVars['sword']);
			
			// Add JavaScript Code
			$this->api->fe_buildSwapClassesJSCode('open','closed');
			
			// Storage
			$catMenu = array();
			$this->totalItemsNum = array();
			
			// Add header
			$catMenu[] = $this->api->fe_makeStyledContent('h2','header',$this->pi_getLL('catmenu.header'));
			
			// Build menu
			$catMenu[] = $this->getCategories(0,0);
			
			// Restore sword
			$this->piVars['sword'] = $this->tempSword;
			
			// Return menu
			return $this->api->fe_makeStyledContent('div','catMenu',implode(chr(10),$catMenu));
		}
		
		/**
		 * 
		 */
		function getCategories($parent,$level) {
			
			// Storage
			$catMenu = array();
			
			// MySQL WHERE clause
			$whereClause = 'parent=' . $parent . ' AND pid IN (' . $this->startingPoints . ')' . $this->cObj->enableFields($this->extTables['categories']);
			
			// MySQL ORDER BY clause
			$orderBy = 'title';
			
			// MySQL ressource
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['categories'],$whereClause,false,$orderBy);
			
			// CSS class for list items
			$listClass = ($this->conf['catMenu.']['expandLevels'] > $level) ? 'open' : 'closed';
			
			// Get categories
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// ID of the list item
				$listId = $this->prefixId . '_' . $row['uid'];
				
				// Start list tag
				$catMenu[] = $this->api->fe_makeStyledContent('li',$listClass,0,0,0,1,array('id'=>$listId));
				
				// Start div tag
				$catMenu[] = $this->api->fe_makeStyledContent('div','level' . $level,0,1,0,1);
				
				// Try to get subcategories
				$childs = $this->getCategories($row['uid'],$level + 1);
				
				// Link to use for the category
				if ($childs) {
					
					// Display subcategories
					$link = $this->api->fe_makeSwapClassesJSLink($listId,$row['title']);
					
					// Add number of links for parent category
					$this->totalItemsNum[$parent] += $this->totalItemsNum[$row['uid']];
					
				} else {
					
					// Display this category
					$link = $this->api->fe_linkTP_unsetPIvars($row['title'],array('showCat'=>$row['uid'],'sword'=>$this->tempSword),array('pointer','showUid'));
					
					// Get number of links
					$resNum = $this->pi_exec_query($this->extTables['links'],1,'AND ' . $GLOBALS['TYPO3_DB']->listQuery('category',$row['uid'],$this->extTables['links']));
					list($localItemsNum) = $GLOBALS['TYPO3_DB']->sql_fetch_row($resNum);
					
					// Add number of links for parent category
					$this->totalItemsNum[$parent] += $localItemsNum;
				}
				
				// Set links number
				$itemsNum = ($childs) ? $this->totalItemsNum[$row['uid']] : $localItemsNum;
				
				// Category item w/ wrap
				$catItem = $this->cObj->wrap($link . $this->api->fe_makeStyledContent('span','itemsNum',' (' . $itemsNum . ')'),$this->conf['catMenu.']['itemWrap']);
				
				// Add category and subcategories if any
				$catMenu[] = $catItem . $childs;
				
				// End list tag
				$catMenu[] = '</div></li>';
			}
			
			// Free MySQL ressource
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			
			// Check for entries in menu
			if (count($catMenu)) {
				
				// Full menu for the current level
				$levelMenu = '<ul>' . implode(chr(10),$catMenu) . '</ul>';
				
				// Return menu
				return $levelMenu;
			}
		}
		
		/***************************************************************
		 * SECTION 3 - LIST VIEW
		 *
		 * Construction of the list view.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function makeList($res) {
			
			// Items storage
			$items = array();
			
			// Get items to list
			while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Template markers
				$templateMarkers = array();
				
				// Override template markers
				$templateMarkers['###TITLE###'] = $this->getFieldContent('title');
				$templateMarkers['###PICTURE###'] = $this->getFieldContent('picture');
				$templateMarkers['###DESCRIPTION###'] = $this->getFieldContent('description');
				$templateMarkers['###LINK###'] = $this->getFieldContent('url');
				$templateMarkers['###DEADLINK###'] = $this->pi_getLL('buglink');
				
				// Build item
				$items[] = $this->api->fe_renderTemplate($templateMarkers,'###ITEM###');
			}
			
			// Return list
			return $this->api->fe_makeStyledContent('div','list',implode(chr(10),$items));
		}
		
		/**
		 * 
		 */
		function getFieldContent($fieldName) {
			
			// Field content
			$field = $this->internal['currentRow'][$fieldName];
			
			// Check field name
			switch($fieldName) {
				
				// Title
				case 'title':
					$content = '<a href="' . $this->internal['currentRow']['url'] . '" target="' . $this->internal['currentRow']['target'] . '">' . $field . '</a>';
				break;
				
				// Title
				case 'picture':
					$content = '<a href="' . $this->internal['currentRow']['url'] . '" target="' . $this->internal['currentRow']['target'] . '"' . $this->api->fe_createImageObjects($field,$this->conf['imgConf.'],$this->uploadFolder) . '</a>';
				break;
				
				// Title
				case 'description':
					$content = nl2br($field);
				break;
				
				// Title
				case 'url':
					$content = '<a href="' . $field . '" target="' . $this->internal['currentRow']['target'] . '">' . $field . '</a>';
				break;
			}
			
			// Return content
			return $content;
		}
	}
	
	// XClass inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weblinks/pi1/class.tx_weblinks_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weblinks/pi1/class.tx_weblinks_pi1.php']);
	}
?>
