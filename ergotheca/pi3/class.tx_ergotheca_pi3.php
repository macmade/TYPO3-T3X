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
	 * Plugin 'Ergotheca researches' for the 'ergotheca' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *        :		function main($content,$conf)
	 *        :		function setDefaultVars($conf)
	 *        :		function initTemplate
	 * 
	 * SECTION:		2 - LIST VIEW
	 *        :		function listView($content,$conf,$templateContent)
	 *        :		function setInternalVars
	 *        :		function makeList($res,$templateContent)
	 *        :		function makeListItem($templateContent)
	 *        :		function makeSignalIcons
	 * 
	 * SECTION:		3 - SINGLE VIEW
	 *        :		
	 * 
	 * SECTION:		4 - MISC / UTILS
	 *        :		function renderTemplate($templateContent,$templateMarkers,$templateSection)
	 *        :		function makeContent($fieldName,$view,$function=false)
	 *        :		getFieldContent($fieldName)
	 *        :		getExplodedField($field,$delimiter,$before,$sep)
	 *        :		getSpecialField($field,$type,$num)
	 * 
	 *				TOTAL FUNCTIONS: 37
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib."class.tslib_pibase.php");
	
	class tx_ergotheca_pi3 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = "tx_ergotheca_pi3";
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = "pi3/class.tx_ergotheca_pi3.php";
		
		// The extension key
		var $extKey = "ergotheca";
		
		// SQL caching for external tables
		var $sqlCache = array();
		
		// Plugin variables storage
		var $pluginVars = array();
		
		// DB table
		var $extTable = 'tx_ergotheca_researches';
		

		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_ergotheca_pi1", and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.-
		 * @see			setDefaultVars
		 * @see			initTemplate
		 * @see			singleView
		 * @see			listView
		 * @see			renderTemplate
		 */
		function main($content,$conf) {
			
			// TS variables storage
			$this->storeTSVars($conf);
			
			// Template load and init.
			$templateContent = $this->initTemplate();
			$templateMarkers = array();
			
			// Overwriting template markers
			$templateMarkers['###MAIN_SINGLEVIEW###'] = '';
			$templateMarkers['###MAIN_LISTVIEW###'] = '';
			
			if (isset($this->piVars['showUid'])) {
				
				// Single view
				$templateMarkers['###MAIN_SINGLEVIEW###'] = $this->singleView($content,$conf,$templateContent);
			} else {
				
				// List view
				$templateMarkers['###MAIN_LISTVIEW###'] = $this->listView($content,$conf,$templateContent);
			}
			
			// Template rendering
			$content = $this->renderTemplate($templateContent,$templateMarkers,'###MAIN###');
			
			// Return content
			return $this->pi_wrapInBaseClass($content);
		}
		
		/**
		 * Strore the plugin variables.
		 * 
		 * Stores the plugin TS variables in an array to allow class functions
		 * to access the plugin configuration.
		 * 
		 * @param		$conf				The TS setup
		 * @return		Nothing
		 */
		function storeTSVars($conf) {
			
			// Store TS Variavles
			$this->pluginVars['templateFile'] = $conf['templateFile'];
			$this->pluginVars['emptyField'] = $conf['emptyField'];
			$this->pluginVars['dateFormat'] = $conf['dateFormat'];
			$this->pluginVars['listView.defaultMode'] = $conf['listView.']['defaultMode'];
			$this->pluginVars['listView.sortBy'] = $conf['listView.']['sortBy'];
			$this->pluginVars['listView.maxRecords'] = $conf['listView.']['maxRecords'];
			$this->pluginVars['listView.maxPages'] = $conf['listView.']['maxPages'];
			$this->pluginVars['listView.displayFields'] = $conf['listView.']['displayFields'];
		
			// PiVars check
			if (!isset($this->piVars['mode'])) {
				$this->piVars['mode'] = $this->pluginVars['listView.defaultMode'];
			}
			if (!isset($this->piVars['sort'])) {
				$this->piVars['sort'] = $this->pluginVars['listView.sortBy'];
			}
		}
		
		/**
		 * Loads the template file.
		 * 
		 * This function reads the template file and returns it as
		 * a C-Object.
		 * 
		 * @return		The C-Object of the template file
		 */
		function initTemplate() {
			
			// Template load
			$templateContent = $this->cObj->fileResource($this->pluginVars['templateFile']);
			
			// Return template C-Object
			return $templateContent;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 2 - LIST VIEW
		 *
		 * Functions for the initialization and the output of the list view.
		 ***************************************************************/
		
		/**
		 * Returns the list view section.
		 * 
		 * This function initialises the list view, and launches the
		 * needed functions to correctly display the view.
		 * 
		 * @param		$content			The content setup
		 * @param		$conf				The TS setup
		 * @param		$templateContent	The template C-Object
		 * @return		The template section for the list view
		 * @see			setInternalVars
		 * @see			makeList
		 * @see			renderTemplate
		 */
		function listView($content,$conf,$templateContent) {
			
			// Get general records
			if (strstr($this->cObj->currentRecord,'tt_content')) {
				$conf['pidList'] = $this->cObj->data['pages'];
				$conf['recursive'] = $this->cObj->data['recursive'];
			}
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			$this->pi_setPiVarDefaults();
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// No active page
			if (!isset($this->piVars['pointer'])) {
				$this->piVars['pointer'] = 0;
			}
			
			// MySQL query init
			$this->setInternalVars();
			
			// Get modmenu WHERE clause
			$whereClause = ($this->piVars['mode']) ? $this->makeListModQuery($this->piVars['mode']) : '';
			
			// Get records number
			$res = $this->pi_exec_query($this->extTable,1,$whereClause);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			// Make listing query - Pass query to MySQL database
			$res = $this->pi_exec_query($this->extTable,0,$whereClause);
			$this->internal['currentTable'] = $this->extTable;
			
			// DEBUG ONLY - Output plugin variables
			// $content .= t3lib_div::view_array($this->piVars);
			
			// Adds the whole list table
			$content .= $this->makeList($res,$templateContent);
			
			// Return content
			return $content;
		}
		
		/**
		 * 
		 */
		function makeListModQuery($mode) {
			
			// Process modes
			switch($mode) {
				
				// Ongoing
				case '1':
					$query = 'AND date_start<' . time() . ' AND date_end>' . time();
				break;
				
				// Future
				case '2':
					$query = 'AND date_start>' . time();
				break;
				
				// Ended
				case '3':
					$query = 'AND date_end<' . time();
				break;
			}
			
			// Return query
			return $query ;
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
			
			// Query parameters:
			list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
			
			// Number of results to show in a listing
			$this->internal['results_at_a_time'] = t3lib_div::intInRange($this->pluginVars['listView.maxRecords'],0);
			
			// Maximum number of pages
			$this->internal['maxPages'] = t3lib_div::intInRange($this->pluginVars['listView.maxPages'],0);
			
			// Search fields
			$this->internal['searchFieldList'] = 'title,description,date_start,date_end';
			
			// ORDER BY list
			$this->internal['orderByList'] = 'uid,crdate,title,date_start,date_end';
		}
		
		/**
		 * Returns a menu.
		 * 
		 * This function is used to output a menu, based on $this->piVars['mod'].
		 * 
		 * @return		The modmenu
		 */
		function makeListMenu() {
			
			// Menu items
			$listMenu = array(
				'0' => $this->pi_getLL('pi_list_modmenu_1','Mode 1'),
				'1' => $this->pi_getLL('pi_list_modmenu_2','Mode 2'),
				'2' => $this->pi_getLL('pi_list_modmenu_3','Mode 3'),
				'3' => $this->pi_getLL('pi_list_modmenu_4','Mode 4'),
			);
			
			// Create menu
			return $this->pi_list_modeSelector($listMenu);
		}
		
		/**
		 * Returns the complete list view.
		 * 
		 * This function creates a the full list view section, with the
		 * searchbox, the modmenu, the table of records and the browsebox.
		 * 
		 * @param		$res				The MySQL ressource
		 * @param		$templateContent	The template C-Object
		 * @return		The list view
		 * @see			makeListMenu
		 * @see			makeStyledContent
		 * @see			makeListTable
		 * @see			renderTemplate
		 */
		function makeList($res,$templateContent) {
			
			// Add searchbox
			$templateMarkers['###LISTVIEW_SEARCHFORM###'] = $this->pi_list_searchBox();
			
			// Add menu
			$templateMarkers['###LISTVIEW_MODMENU###'] = $this->makeListMenu();
			
			// Add list items
			$templateMarkers['###LISTVIEW_TABLE###'] = $this->makeStyledContent('div','listTable',$this->makeListTable($res,$templateContent));
			
			// Add browse box
			$templateMarkers['###LISTVIEW_BROWSEBOX###'] = $this->pi_list_browseresults();
			
			// Wrap all in a CSS element
			$content = $this->makeStyledContent('div','listView',$this->renderTemplate($templateContent,$templateMarkers,'###LISTVIEW###'));
			
			// Return content
			return $content;
		}
		
		/**
		 * Returns the list-table.
		 * 
		 * This function table of the records, with headers.
		 * 
		 * @param		$res				The MySQL ressource
		 * @param		$templateContent	The template C-Object
		 * @return		The list-table
		 * @see			makeListHeaders
		 * @see			makeListItem
		 * @see			renderTemplate
		 */
		function makeListTable($res,$templateContent) {
			
			// Items rows storage
			$items = Array();
			
			// Get items to list
			while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Build item
				$items[] = $this->makeListItem($templateContent);
			}
			
			// Add headers
			$templateMarkers['###LISTVIEW_HEADERS###'] = $this->makeListHeaders($templateContent);
			
			// Add rows
			$templateMarkers['###LISTVIEW_ITEMS###'] = implode(chr(10),$items);
			
			// Template rendering
			$content = $this->renderTemplate($templateContent,$templateMarkers,'###LISTVIEW_TABLE###');
			
			// Return content
			return $content;
		}
		
		/**
		 * Returns the headers.
		 * 
		 * This function creates the list-table headers.
		 * 
		 * @param		$templateContent	The template C-Object
		 * @return		The list-table headers
		 * @see			makeStyledContent
		 * @see			getFieldHeader
		 * @see			renderTemplate
		 */
		function makeListHeaders($templateContent) {
			
			// Get list-fields
			$fields = explode(',',$this->pluginVars['listView.displayFields']);
			
			// Get sort variable
			$sort = explode(':',$this->piVars['sort']);
			$selected = array();
			
			// CSS selected flag
			foreach($fields as $field) {
				$selected[] = ($sort[0] == $field) ? '-SCell' : '';
			}
			
			// Markers substitution
			$templateMarkers['###LISTVIEW_HEADER_0###'] = $this->makeStyledContent('h1','listrowHeader' . $selected[0],$this->getFieldHeader_sortLink($fields[0]));
			$templateMarkers['###LISTVIEW_HEADER_1###'] = $this->makeStyledContent('h1','listrowHeader' . $selected[1],$this->getFieldHeader_sortLink($fields[1]));
			$templateMarkers['###LISTVIEW_HEADER_2###'] = $this->makeStyledContent('h1','listrowHeader' . $selected[2],$this->getFieldHeader_sortLink($fields[2]));
			
			// Template rendering
			$content = $this->renderTemplate($templateContent,$templateMarkers,'###LISTVIEW_HEADERS###');
			
			// Return content
			return $content;
		}
		
		/**
		 * Returns a row.
		 * 
		 * This function creates a list-table row for the requested record.
		 * 
		 * @param		$templateContent	The template C-Object
		 * @return		The record's row
		 * @see			makeContent
		 * @see			makeSignalIcons
		 * @see			renderTemplate
		 */
		function makeListItem($templateContent) {
			
			// Get list-fields
			$fields = explode(',',$this->pluginVars['listView.displayFields']);
			
			// Markers substitution
			$templateMarkers['###LISTVIEW_FIELD_0###'] = $this->makeContent($fields[0],'list');
			$templateMarkers['###LISTVIEW_FIELD_1###'] = $this->makeContent($fields[1],'list');
			$templateMarkers['###LISTVIEW_FIELD_2###'] = $this->makeContent($fields[2],'list');
			
			// Template rendering
			$content = $this->renderTemplate($templateContent,$templateMarkers,'###LISTVIEW_ITEMS###');
			
			// Return content
			return $content;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - SINGLE VIEW
		 *
		 * Functions for the initialization and the output of the single view.
		 ***************************************************************/
		
		/**
		 * Single View
		 */
		function singleView($content,$conf,$templateContent) {
			
			// Get current record
			$this->internal['currentTable'] = $this->extTable;
			$this->internal['currentRow'] = $this->pi_getRecord($this->extTable,$this->piVars['showUid']);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			$this->pi_setPiVarDefaults();
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// Set page title (used by the indexed search engine)
			if ($this->internal['currentRow']['title']) {
				$GLOBALS['TSFE']->indexedDocTitle = $this->internal['currentRow']['title'];
			}
			
			// Markers substitution
			$templateMarkers['###SINGLEVIEW_TITLE###'] = $this->makeContent('title','single');
			$templateMarkers['###SINGLEVIEW_DESCRIPTION###'] = $this->makeContent('description','single');
			$templateMarkers['###SINGLEVIEW_DATE_START###'] = $this->makeContent('date_start','single');
			$templateMarkers['###SINGLEVIEW_DATE_END###'] = $this->makeContent('date_end','single');
			$templateMarkers['###SINGLEVIEW_TOOL###'] = $this->makeContent('tool','single');
			$templateMarkers['###SINGLEVIEW_BACKLINK###'] = '<p>' . $this->pi_list_linkSingle($this->pi_getLL('pi_single_back','Back'),0) . '</p></div>';
			
			// Template rendering
			$content = $this->makeStyledContent('div','singleView',$this->renderTemplate($templateContent,$templateMarkers,'###SINGLEVIEW_TEMPLATE###'));
			
			// Return content
			return $content;
		}
		
		
		
		
		
		 /***************************************************************
		 * SECTION 4 - MISC / UTILS
		 *
		 * General functions for the plugin.
		 ***************************************************************/
		
		/**
		 * Template rendering
		 * 
		 * This function analyzes the template C-Object and substitute
		 * the specified section with the specified subsections.
		 * 
		 * @param		$templateContent	The template C-Object
		 * @param		$templateMarkers	The markers array
		 * @param		$templateSection	The section to substitute
		 * @return		The processed template section
		 */
		function renderTemplate($templateContent,$templateMarkers,$templateSection) {
			
			// Get template subparts
			$subpart = $this->cObj->getSubpart($templateContent,$templateSection);
			
			// Return substituted section
			return $this->cObj->substituteMarkerArrayCached($subpart,array(),$templateMarkers,array());
		}
		
		/**
		 * Content rendering
		 * 
		 * This function outputs the specified field, formatted with a CSS class.
		 * 
		 * @param		$fieldName			The field to output
		 * @param		$view				The kind of view (used for CSS-Styling)
		 * @return		The rendered field
		 * @see			getFieldContent
		 */
		function makeContent($fieldName,$view) {
			
			// Get field content
			$fieldContent = $this->getFieldContent($fieldName,$view);
			
			// Detect the kind of view (for CSS marking)
			switch($view) {
				case 'single':
					$content = ($fieldName == 'title') ? $this->makeStyledContent('h1','singleViewField-' . $fieldName,$fieldContent) : $this->makeStyledContent('h1','singleViewHeader-' . $fieldName,$this->getFieldHeader($fieldName)) . $this->makeStyledContent('p','singleViewField-' . $fieldName,$fieldContent);
				break;
				case 'list':
					$content = $this->makeStyledContent('div','listrowField-' . $fieldName,$fieldContent);
				break;
			}
			
			// Return content
			return $content;
		}
		
		/**
		 * Fields processing
		 * 
		 * This function outputs the specified field, which is processed
		 * with the correct function
		 * 
		 * @param		$fieldName			The field to output
		 * @param		$view				The kind of view
		 * @see			getExplodedField
		 * @see			getExplodedLinkField
		 * @see			getSpecialField
		 * @see			getDocsField
		 * @return		The processed field
		 */
		function getFieldContent($fieldName,$view) {
			
			// Get field content
			$field = $this->internal['currentRow'][$fieldName];
			
			// Field is empty, and not a special field
			if (empty($field) && $fieldName != 'formalization' && $fieldName != 'language'&& $fieldName != 'eesp') {
				$content = $this->pluginVars['emptyField'];
			} else {
				
				// Process special fields
				switch($fieldName) {
					
					// UID -> Link
					case 'uid':
					
						// 1 = single items caching (0 = disable)
						$content = ($view == 'single') ? $field : $this->pi_list_linkSingle($field,$this->internal['currentRow']['uid'],1);
					break;
					
					// Name -> List
					case 'title':
						if ($view == 'single') {
							
							// Single view
							$content = $field;
						} else {
							
							// List view
							$content = $this->pi_list_linkSingle($field,$this->internal['currentRow']['uid'],1);
						}
					break;
					
					case 'date_start':
						$content = date($this->pluginVars['dateFormat'],$field);
					break;
					
					case 'date_end':
						$content = date($this->pluginVars['dateFormat'],$field);
					break;
					
					case 'tool':
						$content = $this->getRelatedTool($field,$view);
					break;
					
					// Normal fields
					default:
						$content = nl2br($field);
					break;
				}
			}
			
			// Return content
			return $content;
		}
		
		/**
		 * Returns separated values.
		 * 
		 * This function is used to output a field containing multiple
		 * values, using a separator.
		 * 
		 * @param		$field				The field to process
		 * @param		$delimiter			The delimiter used in the field
		 * @param		$before				A string to put before each value
		 * @param		$after				A string to put after each value
		 * @param		$sep				The separator between each value
		 * @return		The separated values
		 */
		function getExplodedField($field,$delimiter,$before,$after,$sep) {
			
			// Get each value
			$fieldArray = explode($delimiter,$field);
			
			// Process values
			for ($i = 0; $i < count($fieldArray); $i++) {
				$fieldArray[$i] = $before . $fieldArray[$i] . $after;
			}
			
			// Make content
			$content = implode($sep,$fieldArray);
			
			// Return content
			return $content;
		}
		
		/**
		 * Returns the content with CSS.
		 * 
		 * This function is used to output the requested content
		 * wraped in an HTML element, containing a CSS class.
		 * 
		 * @param		$element			The HTML element to procuce
		 * @param		$className			The CSS class name to link
		 * @param		$content			The content to wrap
		 * @return		The CSS styled content
		 */
		function makeStyledContent($element, $className, $content) {
			
			// Wrap content
			$styledContent = '<' . $element . ' ' . $this->pi_classParam($className) .'>' . $content . '</' . $element . '>';
			
			// Return content
			return $styledContent;
			
		}
		
		/**
		 * Returns the field header.
		 * 
		 * This function is used to output a header for the requested
		 * field. Values are taken from the plugin's locallang file.
		 * 
		 * @param		$fieldname			The field to display the header
		 * @return		The field header
		 */
		function getFieldHeader($fieldName) {
		
			// Return label
			return $this->pi_getLL('pi_common_field_header_' . $fieldName,'[' . $fieldName . ']');
		}
		
		/**
		 * Returns the field header with a link.
		 * 
		 * This function is used to output a header for the requested
		 * field. Values are taken from the plugin's locallang file. The
		 * header is wraped into a link, which allow the sorting of the
		 * records ($this->piVars['sort']).
		 * 
		 * @param		$fieldname			The field to display the header
		 * @return		The linked field header
		 * @see			getFieldHeader
		 */
		function getFieldHeader_sortLink($fieldName) {
			return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fieldName),array('sort'=>$fieldName . ':' . ($this->internal['descFlag'] ? 0 : 1)));
		}
		
		function getRelatedTool($uid,$view) {
			
			// Memorise tools table
			if (!array_key_exists('tx_ergotheca_tools',$this->sqlCache)) {
				$this->sqlCache['tx_ergotheca_tools'] = $this->pi_getCategoryTableContents('tx_ergotheca_tools',43);
			}
			
			// Storage
			$content = array();
			
			// Get an array with requested tools
			$tools = explode(',',$uid);
			
			// Process ech tool
			foreach($tools as $toolId) {
			
				// Get related tool's name
				$name = $this->sqlCache['tx_ergotheca_tools'][$toolId]['name'];
				
				if ($view == 'list') {
					
					// Single view
					$field = $this->getExplodedField($name,';','<li>','</li>',chr(10));
					$content[] = '<ul>' . $field . '</ul>';
				} else {
					
					// List view
					$field = $this->getExplodedField($name,';','<li>','</li>',chr(10));
					
					$content[] = '<ul>' . $this->pi_linkTP($field,$urlParameters=array('tx_ergotheca_pi1[showUid]'=>$toolId),1,25) . '</ul>';
				}
			}
			
			// Return content
			return implode(chr(10),$content);
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/ergotheca/pi3/class.tx_ergotheca_pi3.php"]) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/ergotheca/pi3/class.tx_ergotheca_pi3.php"]);
	}
?>
