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
	 * Plugin 'Ergotheca tools library' for the 'ergotheca' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *        :		
	 * 
	 * SECTION:		2 - LIST VIEW
	 *        :		
	 * 
	 * SECTION:		3 - SINGLE VIEW
	 *        :		
	 * 
	 * SECTION:		4 - MISC / UTILS
	 *        :		
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib."class.tslib_pibase.php");
	
	class tx_ergotheca_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = "tx_ergotheca_pi1";
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = "pi1/class.tx_ergotheca_pi1.php";
		
		// The extension key
		var $extKey = "ergotheca";
		
		// SQL caching for external tables
		var $sqlCache = array();
		
		// Plugin variables storage
		var $pluginVars = array();
		
		// DB table
		var $extTable = 'tx_ergotheca_tools';
		
		
		
		
		
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
		 * @return		The content of the plugin
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
			$this->pluginVars['iconsDir'] = str_replace(PATH_site,'/',t3lib_div::getFileAbsFileName($conf['iconsDir']));
			$this->pluginVars['iconsFields'] = $conf['iconsFields'];
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
		 * Functions for the initialization and the output of the list view
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
		 * @see			makeListModQuery
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
			$whereClause = ($this->piVars['mode']) ? $this->makeListModQuery('evalfield',$this->piVars['mode'],3) : '';
			
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
		 * Sets internals variables.
		 * 
		 * This function is used to set the internal variables array
		 * ($this->internal) needed to execute a MySQL query.
		 * 
		 * @return		Nothing
		 */
		function setInternalVars() {
			
			// Unsetting mode variable for searching
			if (isset($this->piVars['sword'])) {
				unset($this->piVars['mode']);
			}
			
			// Query parameters:
			list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
			
			// Number of results to show in a listing
			$this->internal['results_at_a_time'] = t3lib_div::intInRange($this->pluginVars['listView.maxRecords'],0);
			
			// Maximum number of pages
			$this->internal['maxPages'] = t3lib_div::intInRange($this->pluginVars['listView.maxPages'],0);
			
			// Search fields
			$this->internal['searchFieldList'] = 'name,authors,evalobject,practicemodel,passation_description,passation_procedure,passation_material,passation_setpos,passation_quotint,comments,usecond,remarks';
			
			// ORDER BY list
			$this->internal['orderByList'] = 'uid,crdate,name,authors,testyear,evalfield,formalization,evalobject,practicemodel,targetpublic_age,targetpublic_alt,keywords,passation_method,passation_description,passation_procedure,passation_material,passation_setpos,passation_quotint,comments,sources,language,traduction,traduction_standard,eesp,eesp_testyear,usecond,remarks,cost,pictures,files';
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
				'4' => $this->pi_getLL('pi_list_modmenu_4','Mode 4'),
			);
			
			// Create menu
			return $this->pi_list_modeSelector($listMenu);
		}
		
		/**
		 * Returns the complete list view
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
		 * Returns the list-table
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
		 * Returns the headers
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
			$templateMarkers['###LISTVIEW_ICONS###'] = $this->makeSignalIcons('list');
			
			// Template rendering
			$content = $this->renderTemplate($templateContent,$templateMarkers,'###LISTVIEW_ITEMS###');
			
			// Return content
			return $content;
		}
		
		/**
		 * Returns a MySQL WHERE clause.
		 * 
		 * This function create a MySQL WHERE clause for a checkbox type field
		 * used by the modmenu of the plugin.
		 * 
		 * @param		$field				The field to use
		 * @param		$value				The value of the field to check for
		 * @param		$num				The number of possibilities in the field
		 * @return		The MySQL WHERE clause.
		 */
		function makeListModQuery($field,$value,$num) {
			$table = $this->extTable;
			$fields = array($field);
			
			// Storage
			$values = array();
			$queryParts = array();
			
			// Maximum possible value for the field
			$max = pow(2,$num);
			
			// Get possible values
			for($i = 0; $i < $max; $i++) {
				if ($value & $i) {
					$values[] = $i;
				}
			}
			
			// Create MySQL LIKE instructions
			foreach($values as $sw)	{
				$like = ' LIKE "%' . $sw .'%"';
				$queryParts[] = $this->extTable . '.' . $field . $like;
			}
			
			// Create query
			$query = 'AND (' . implode(') OR (',$queryParts) . ')';
			
			// Return query
			return $query ;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - SINGLE VIEW
		 *
		 * Functions for the initialization and the output of the single view.
		 ***************************************************************/
		
		/**
		 * Returns the complete single view
		 * 
		 * This function creates a the full single view section.
		 * 
		 * @param		$content			The content setup
		 * @param		$conf				The TS setup
		 * @param		$templateContent	The template C-Object
		 * @return		The single view
		 * @see			addVote
		 * @see			makeContent
		 * @see			makeSignalIcons
		 * @see			renderTemplate
		 */
		function singleView($content,$conf,$templateContent) {
			
			// Check vote action
			if (t3lib_div::GPvar('action') == 'vote') {
				$this->addVote();
			}
			
			// Get current record
			$this->internal['currentTable'] = $this->extTable;
			$this->internal['currentRow'] = $this->pi_getRecord($this->extTable,$this->piVars['showUid']);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			$this->pi_setPiVarDefaults();
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// Set page title (used by the indexed search engine)
			if ($this->internal['currentRow']['name']) {
				$GLOBALS['TSFE']->indexedDocTitle = $this->internal['currentRow']['name'];
			}
			
			// Markers substitution
			$templateMarkers['###SINGLEVIEW_NAME###'] = $this->makeContent('name','single');
			$templateMarkers['###SINGLEVIEW_ICONS###'] = $this->makeSignalIcons('single');
			$templateMarkers['###SINGLEVIEW_VOTE###'] = $this->makeVote();
			$templateMarkers['###SINGLEVIEW_AUTHORS###'] = $this->makeContent('authors','single');
			$templateMarkers['###SINGLEVIEW_TESTYEAR###'] = $this->makeContent('testyear','single');
			$templateMarkers['###SINGLEVIEW_EVALFIELD###'] = $this->makeContent('evalfield','single');
			$templateMarkers['###SINGLEVIEW_FORMALIZATION###'] = $this->makeContent('formalization','single');
			$templateMarkers['###SINGLEVIEW_EVALOBJECT###'] = $this->makeContent('evalobject','single');
			$templateMarkers['###SINGLEVIEW_PRACTICEMODEL###'] = $this->makeContent('practicemodel','single');
			$templateMarkers['###SINGLEVIEW_TARGETPUBLIC_AGE###'] = $this->makeContent('targetpublic_age','single');
			$templateMarkers['###SINGLEVIEW_PASSATION_METHOD###'] = $this->makeContent('passation_method','single');
			$templateMarkers['###SINGLEVIEW_PASSATION_DESCRIPTION###'] = $this->makeContent('passation_description','single');
			$templateMarkers['###SINGLEVIEW_PASSATION_PROCEDURE###'] = $this->makeContent('passation_procedure','single');
			$templateMarkers['###SINGLEVIEW_PASSATION_MATERIAL###'] = $this->makeContent('passation_material','single');
			$templateMarkers['###SINGLEVIEW_PASSATION_SETPOS###'] = $this->makeContent('passation_setpos','single');
			$templateMarkers['###SINGLEVIEW_PASSATION_QUOTINT###'] = $this->makeContent('passation_quotint','single');
			$templateMarkers['###SINGLEVIEW_COMMENTS###'] = $this->makeContent('comments','single');
			$templateMarkers['###SINGLEVIEW_LINKS###'] = $this->makeContent('links','single');
			$templateMarkers['###SINGLEVIEW_SOURCES###'] = $this->makeContent('sources','single');
			$templateMarkers['###SINGLEVIEW_LANGUAGE###'] = $this->makeContent('language','single');
			$templateMarkers['###SINGLEVIEW_TRADUCTION###'] = $this->makeContent('traduction','single');
			$templateMarkers['###SINGLEVIEW_TRADUCTION_STANDARD###'] = $this->makeContent('traduction_standard','single');
			$templateMarkers['###SINGLEVIEW_EESP###'] = $this->makeContent('eesp','single');
			$templateMarkers['###SINGLEVIEW_EESP_TESTYEAR###'] = $this->makeContent('eesp_testyear','single');
			$templateMarkers['###SINGLEVIEW_USECOND###'] = $this->makeContent('usecond','single');
			$templateMarkers['###SINGLEVIEW_REMARKS###'] = $this->makeContent('remarks','single');
			$templateMarkers['###SINGLEVIEW_COST###'] = $this->makeContent('cost','single');
			$templateMarkers['###SINGLEVIEW_PICTURES###'] = '<a name="pictures"></a>' . $this->makeContent('pictures','single');
			$templateMarkers['###SINGLEVIEW_FILES###'] = '<a name="files"></a>' . $this->makeContent('files','single');
			$templateMarkers['###SINGLEVIEW_BIBLIOGRAPHY###'] = '<a name="bibliography"></a>' . $this->makeContent('bibliography','single');
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
		 * This function outputs the specified field, formatted with a CSS class
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
					$content = ($fieldName == 'name') ? $this->makeStyledContent('h1','singleViewField-' . $fieldName,$fieldContent) : $this->makeStyledContent('h1','singleViewHeader-' . $fieldName,$this->getFieldHeader($fieldName)) . $this->makeStyledContent('p','singleViewField-' . $fieldName,$fieldContent);
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
					case 'name':
						if ($view == 'single') {
							
							// Single view
							$field = $this->getExplodedField($field,chr(10),'','','<br />');
							$content = $field;
						} else {
							
							// List view
							$field = $this->getExplodedField($field,chr(10),'<li>','</li>',chr(10));
							$content = '<ul>' . $this->pi_list_linkSingle($field,$this->internal['currentRow']['uid'],1) . '</ul>';
						}
					break;
					
					// Authors -> List
					case 'authors':
						$field = $this->getExplodedField($field,chr(10),'<li>','</li>',chr(10));
						$content = '<ul>' . $field . '</ul>';
					break;
					
					// Evaluation field -> Checkbox
					case 'evalfield':
						$field = $this->getSpecialField($field,$fieldName,'checkbox',3);
						$content = '<ul>' . $field . '</ul>';
					break;
					
					// Formalization -> Radio
					case 'formalization':
						$rank = (empty($field)) ? 'none' : $field;
						$field = $this->getSpecialField($field,$fieldName,'radio');
						$content = '<img src="' . $this->pluginVars['iconsDir'] . 'rank_' . $rank . '.gif" alt="" width="69" height="15" hspace="0" vspace="0" border="0" align="middle"> (' . $field . ')';
					break;
					
					// Target public age field -> Checkbox
					case 'targetpublic_age':
						$field = $this->getSpecialField($field,$fieldName,'checkbox',4);
						$content = '<ul>' . $field . '</ul>';
					break;
					
					// Passation method -> Checkbox
					case 'passation_method':
						$field = $this->getSpecialField($field,$fieldName,'checkbox',2);
						$content = '<ul>' . $field . '</ul>';
					break;
					
					// Language -> Select
					case 'links':
						$field = $this->getExplodedLinkField($field,chr(10),'|','<li>','</li>',chr(10));
						$content = '<ul>' . $field . '</ul>';
					break;
					
					// Language -> Select
					case 'language':
						$field = $this->getSpecialField($field,$fieldName,'select');
						$content = $field;
					break;
					
					// Traduction -> Checkbox
					case 'traduction':
						$field = $this->getSpecialField($field,$fieldName,'checkbox',6);
						$content = '<ul>' . $field . '</ul>';
					break;
					
					// Standard traduction -> Checkbox
					case 'traduction_standard':
						$field = $this->getSpecialField($field,$fieldName,'checkbox',6);
						$content = '<ul>' . $field . '</ul>';
					break;
					
					// EESP Possesion -> Checkbox
					case 'eesp':
						$field = $this->getSpecialField($field,$fieldName,'checkbox',1);
						$content =  $field;
					break;
					
					// Pictures -> Preview
					case 'pictures':
						$field = $this->getDocsField($field,$fieldName);
						$content =  $field;
					break;
					
					// Files -> Preview
					case 'files':
						$field = $this->getDocsField($field,$fieldName);
						$content =  $field;
					break;
					
					// Files -> Preview
					case 'bibliography':
						$field = $this->getBiblioField($field,$fieldName);
						$content =  $field;
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
		 * 
		 */
		function getBiblioField($field,$fieldName) {
			
			$records = explode(',',$field);
			
			$content = array();
			
			foreach($records as $biblio) {
				$rec = $this->pi_getRecord('tx_endnote_db',$biblio);
				$content[] = $this->pi_linkTP($rec['title'],$urlParameters=array('tx_endnote_pi1[showUid]'=>$rec['uid']),1,37);
			}
			
			return implode('<br /><br />',$content);
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
		 * Returns separated values with links.
		 * 
		 * This function is used to output a field containing multiple
		 * values with links, using separators.
		 * 
		 * @param		$field				The field to process
		 * @param		$delimiter			The delimiter used in the field
		 * @param		$subDelimiter		The sub delimiter used in the field
		 * @param		$before				A string to put before each value
		 * @param		$after				A string to put after each value
		 * @param		$sep				The separator between each value
		 * @return		The separated values
		 */
		function getExplodedLinkField($field,$delimiter,$subDelimiter,$before,$after,$sep) {
			
			// Storage
			$linkArray = array();
			
			// Get each value
			$fieldArray = explode($delimiter,$field);
			
			// Process values
			for ($i = 0; $i < count($fieldArray); $i++) {
				
				// Get each subvalue
				if (strstr($fieldArray[$i],$subDelimiter)) {
					
					// Link with title
					$fieldArray[$i] = explode($subDelimiter,$fieldArray[$i]);
					
					// Process subvalues
					$linkArray[] = $before . '<a href="' . $fieldArray[$i][1] . '" target="_blank">' . $fieldArray[$i][0] . '</a>' . $after;
					
				} else {
					
					// Process subvalues
					$linkArray[] = $before . '<a href="' . $fieldArray[$i] . '" target="_blank">' . $fieldArray[$i] . '</a>' . $after;
					
				}
			}
			
			// Make content
			$content = implode($sep,$linkArray);
			
			// Return content
			return $content;
		}
		
		/**
		 * Returns the processed values.
		 * 
		 * This function is used to output a special field, like checkboxes
		 * or radio, into a human readable format.
		 * 
		 * @param		$field				The field to process
		 * @param		$fieldName			The name of the field
		 * @param		$type				The type of the field
		 * @param		$before				The max number of possible entry (for checkboxes)
		 * @view		getExplodedField
		 * @return		The separated values
		 */
		function getSpecialField($field,$fieldName,$type,$num=0) {
			
			// Field type detection
			switch($type) {
			
				// Process a checkbox field
				case 'checkbox':
					
					if ($num > 1) {
						
						// Multiple checkboxes
						$values = array();
						$readableValues = array();
						
						// Bits comparison
						for($i = 0; $i < $num; $i++) {
							if ($field & pow(2,$i)) {
								$values[] = $i;
							}
						}
						
						// Get labels for values
						foreach($values as $value) {
							$readableValues[] = $this->pi_getLL('pi_common_field_value_' . $fieldName . '.I.' . $value);
						}
						
						// Traduction / Standard Traduction fields
						if ($fieldName == 'traduction' || $fieldName == 'traduction_standard') {
							$altField = explode(';',$this->internal['currentRow'][$fieldName . '_alt']);
							foreach($altField as $alt) {
								$readableValues[] = $alt;
							}
						}
						
						// Target public field
						if ($fieldName == 'targetpublic_age') {
							$readableValues[] = $this->internal['currentRow']['targetpublic_alt'];
						}
						
						// Return content
						return $this->getExplodedField(implode(';',$readableValues),';','<li>','</li>',chr(10));
					} else {
						
						// Single checkbox
						return $this->pi_getLL('pi_common_field_value_' . $fieldName . '.I.' . $field);
					}
				break;
				
				// Process a radio field
				case 'radio':
					return $this->pi_getLL('pi_common_field_value_' . $fieldName . '.I.' . $field);
				break;
				
				// Process a radio field
				case 'select':
					$img = '<img src="' . $this->pluginVars['iconsDir'] . $fieldName . '_' . $field . '.gif" alt="" width="18" height="12" hspace="5" vspace="0" border="0" align="middle">';
					if ($fieldName == 'language' && $field == 5) {
						
						// Language field
						return $this->internal['currentRow'][$fieldName . '_alt'];
					} else {
						
						// Normal case
						return $img . $this->pi_getLL('pi_common_field_value_' . $fieldName . '.I.' . $field);
					}
				break;
			}
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
		
		/**
		 * Returns the icons col.
		 * 
		 * This function check the icons fields and display an icon
		 * for each field that indicates if the field is empty or not.
		 * 
		 * @param		$view				The kind of view to use
		 * @return		An image object for each field
		 */
		function makeSignalIcons($view) {
			
			// Get icon-fields
			$fields = explode(',',$this->pluginVars['iconsFields']);
			
			// Icons torage
			$iconArray = array();
			
			// Create IMG elements
			$i = 0;
			foreach ($fields as $field) {
				$row = $this->internal['currentRow'][$field];
				$icon = (empty($row)) ? $this->pluginVars['iconsDir'] . $field . '_h.gif' : $this->pluginVars['iconsDir'] . $field . '.gif';
				$img = '<img src="' . $icon . '" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle">';
				
				// Creating icons and links
				if ($view == 'single') {
					
					// Single view
					$iconArray[] = (empty($row)) ? $img : '<a href="#' . $fields[$i] . '">' . $img . '</a>';
				} else {
					
					// List view
					$url = $this->pi_list_linkSingle('',$this->internal['currentRow']['uid'],1,0,1);
					$iconArray[] = (empty($row)) ? $img : '<a href="' . $url . '#' . $fields[$i] . '">' . $img . '</a>';
				}
				$i++;
			}
			
			// Open Content icon
			if ($this->internal['currentRow']['opencontent'] == 1) {
				$opencontent = '<br /><img src="' . $this->pluginVars['iconsDir'] . 'opencontent.gif" alt="" width="66" height="20" hspace="0" vspace="0" border="0" align="middle">';
			}
			
			// Return icons
			return '<nobr>' . implode(chr(10),$iconArray) . '</nobr>' . $opencontent;
		}
		
		/**
		 * Create the vote section.
		 * 
		 * This function creates the vote section for a tool. It includes the display
		 * of the current note, and, if the user is authentified and has never rated the
		 * current tool, a vote form.
		 * 
		 * @return		The vote section
		 */
		function makeVote() {
			
			// Get vote results
			$votes = explode(',',$this->internal['currentRow']['vote_results']);
			
			// Get vote users
			$users = explode(',',$this->internal['currentRow']['vote_users']);
			
			// Round cote result
			$result = round(array_sum($votes) / count($votes));
			
			// Result icon
			$icon = $this->pluginVars['iconsDir'] . 'vote_' . $result . '.gif';
			
			// Storage
			$htmlCode = array();
			
			// Add icon
			$htmlCode[] = '<p><img src="' . $icon . '" alt="" width="90" height="18" hspace="0" vspace="0" border="0" align="middle"></p>';
			
			// User can vote
			if ($GLOBALS["TSFE"]->loginUser && !in_array($GLOBALS['TSFE']->fe_user->user['uid'],$users)) {
				
				// Add cote form
				$htmlCode[] = '<form method="post" id="vote">';
				$htmlCode[] = '<input type="hidden" name="action" value="vote">';
				$htmlCode[] = '<select name="note">';
				$htmlCode[] = '<option value="1">1</option>';
				$htmlCode[] = '<option value="2">2</option>';
				$htmlCode[] = '<option value="3">3</option>';
				$htmlCode[] = '<option value="4">4</option>';
				$htmlCode[] = '<option value="5">5</option>';
				$htmlCode[] = '</select>';
				$htmlCode[] = '<input type="submit" name="submit" value="' . $this->pi_getLL('pi_single_vote') . '">';
				$htmlCode[] = '</form>';
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Adds a vote.
		 * 
		 * This function makes the MySQL queries to add a vote to a tool.
		 * 
		 * @return		Nothing
		 */
		function addVote() {
			
			$note = t3lib_div::GPvar('note');
			
			// Check
			if (!empty($note) && $GLOBALS["TSFE"]->loginUser) {
				
				// Get current values
				$row = $this->pi_getRecord($this->extTable,$this->piVars['showUid']);
				
				// Current Notes
				$results = (empty($row['vote_results'])) ? array() : explode(',',$row['vote_results']);
				
				// Current Users
				$users = (empty($row['vote_users'])) ? array() : explode(',',$row['vote_users']);
				
				// Add new values
				$results[] = $note;
				$users[] = $GLOBALS['TSFE']->fe_user->user['uid'];
				
				// Query values
				$values = array();
				$values['vote_results'] = implode(',',$results);
				$values['vote_users'] = implode(',',$users);
				
				// Make MySQL UPDATE query
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTable,'uid=' . $this->piVars['showUid'],$values);
			}
		}
		
		/**
		 * Creates associated docs section.
		 * 
		 * This function creates a preview for each document present in the field,
		 * and makes a link to that document.
		 * 
		 * @return		
		 * @param		$field				The content of the field to render
		 * @param		$fieldName			The name of the field to render
		 */
		function getDocsField($field,$fieldName) {
			if (!empty($field)) {

				
				// Get separated values
				$row = explode(',',$field);
				
				// Storage
				$content = array();
				
				// Table start
				$content[] = '<table border="0" width="100%" cellspacing="5" cellpadding="0" align="center">';
				
				// Rows
				foreach ($row as $document) {
					
					// Crop long document names
					if (strlen($document) > $this->conf['docMaxLength']) {
						$documentShortName = substr($document,0,$this->conf['docMaxLength']) . ' [...]';
					} else {
						$documentShortName = $document;
					}
					
					if ($this->conf['thumbCObject.']) {
						// Thumbnail
						$imgTSConfig = $this->conf['thumbCObject.'];
						$imgTSConfig['file'] = 'uploads/tx_ergotheca/' . $document;
						
						// Link
						$file = PATH_site . 'uploads/tx_ergotheca/' . $document;
						
						// File size
						$size = number_format(@filesize($file) / 1024,0) . "kb";
						
						// File creation date
						$time = date($this->pluginVars['dateFormat'],@filectime($file));
						
						// URL
						$url = 'uploads/tx_ergotheca/' . $document;
						
						// Row construction
						$content[] = '<tr><td width="50%" align="center" valign="middle">' . $this->cObj->IMAGE($imgTSConfig) . '</td><td width="50%" align="center" valign="middle"><a href="' . $url . '" target="_blank">' . $documentShortName . '</a><br /><br />' . $size . '<br />' . $time . '</td></tr>';
					} else {
						$content[] = '<tr><td width="50%" align="center" valign="middle"></td><td width="50%" align="center" valign="middle"><a href="' . $url . '" target="_blank">' . $documentShortName . '</a><br /><br />' . $size . '<br />' . $time . '</td></tr>';
					}
				}
				
				// Table end
				$content[] = '</table>';
				
				// Return table
				return implode(chr(10),$content);
			}
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/ergotheca/pi1/class.tx_ergotheca_pi1.php"]) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/ergotheca/pi1/class.tx_ergotheca_pi1.php"]);
	}
?>
