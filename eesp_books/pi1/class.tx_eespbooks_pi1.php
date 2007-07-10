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
	 * Plugin 'EESP book list' for the 'eesp_books' extension.
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
	
	class tx_eespbooks_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = "tx_eespbooks_pi1";
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = "pi1/class.tx_eespbooks_pi1.php";
		
		// The extension key
		var $extKey = "eesp_books";
		
		// DB table
		var $extTable = 'tx_eespbooks_db';
		
		// Upload folder
		var $uploadDir = 'uploads/tx_eespbooks/';
		
		
		
		
		
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
			
			// Template load and init.
			$templateContent = $this->initTemplate($conf);
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
		 * Loads the template file.
		 * 
		 * This function reads the template file and returns it as
		 * a C-Object.
		 * 
		 * @param		$conf				The TS setup
		 * 
		 * @return		The C-Object of the template file
		 */
		function initTemplate($conf) {
			
			// Template load
			$templateContent = $this->cObj->fileResource($conf['templateFile']);
			
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
			
			// Exclude reeditions from list
			$whereClause = ' AND reedition=0';
			
			// Get modmenu WHERE clause
			$whereClause .= (isset($this->piVars['mode'])) ? $this->makeListModQuery('collection',$this->piVars['mode']) : '';
			
			// Get records number
			$res = $this->pi_exec_query($this->extTable,1,$whereClause);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			// Make listing query - Pass query to MySQL database
			$res = $this->pi_exec_query($this->extTable,0,$whereClause);
			$this->internal['currentTable'] = $this->extTable;
			
			// DEBUG ONLY - Output plugin variables
			//$content .= t3lib_div::view_array($this->piVars);
			
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
			
			// PiVars check
			if (!isset($this->piVars['mode'])) {
				$this->piVars['mode'] = $this->conf['listView.']['defaultMode'];
			}
			if (empty($this->piVars['sort'])) {
				$this->piVars['sort'] = $this->conf['listView.']['sortBy'] . ':1';
			}
			
			// Unsetting mode variable for searching
			if (isset($this->piVars['sword'])) {
				unset($this->piVars['mode']);
			}
			
			// Query parameters:
			list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
			
			// Number of results to show in a listing
			$this->internal['results_at_a_time'] = t3lib_div::intInRange($this->conf['listView.']['maxRecords'],0);
			
			// Maximum number of pages
			$this->internal['maxPages'] = t3lib_div::intInRange($this->conf['listView.']['maxPages'],0);
			
			// Search fields
			$this->internal['searchFieldList'] = 'bookid,pubyear,title,subtitle,authors,abstract,analysis,bibliography';
			
			// ORDER BY list
			$this->internal['orderByList'] = 'uid,bookid,isbn,pubyear,price_chf,price_eur,pages,title,subtitle,authors';
		}
		
		/**
		 * Returns a MySQL WHERE clause.
		 * 
		 * This function create a MySQL WHERE clause for a radio type field
		 * used by the modmenu of the plugin.
		 * 
		 * @param		$field				The field to use
		 * @param		$value				The value of the field to check for
		 * @return		The MySQL WHERE clause.
		 */
		function makeListModQuery($field,$value) {
			
			// Create query
			$query = ' AND ' . $field . '=' . $value;
			
			// Return query
			return $query;
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
			
			// Add rows
			$templateMarkers['###LISTVIEW_ITEMS###'] = implode(chr(10),$items);
			
			// Template rendering
			$content = $this->renderTemplate($templateContent,$templateMarkers,'###LISTVIEW_TABLE###');
			
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
		 * @see			renderTemplate
		 */
		function makeListItem($templateContent) {
			
			// Markers substitution
			$templateMarkers['###LISTVIEW_COVER###'] = $this->makeContent('cover','list');
			$templateMarkers['###LISTVIEW_TITLE###'] = $this->makeContent('title','list');
			$templateMarkers['###LISTVIEW_BOOKID###'] = $this->makeContent('bookid','list');
			$templateMarkers['###LISTVIEW_SUBTITLE###'] = $this->makeContent('subtitle','list');
			$templateMarkers['###LISTVIEW_AUTHORS###'] = $this->makeContent('authors','list');
			$templateMarkers['###LISTVIEW_EDITPANEL###'] = $this->pi_getEditPanel();
			
			// Template rendering
			$content = $this->renderTemplate($templateContent,$templateMarkers,'###LISTVIEW_ITEMS###');
			
			// Return content
			return $content;
		}
		
		/**
		 * Returns a menu.
		 * 
		 * This function is used to output a menu, based on $this->piVars['mod'].
		 * 
		 * @return		The modmenu
		 */
		function makeListMenu() {
			
			// Unset sword
			unset($this->piVars['sword']);
			
			// Menu items
			$listMenu = array(
				'0' => $this->pi_getLL('pi_list_modmenu_0','Mode 0'),
				'1' => $this->pi_getLL('pi_list_modmenu_1','Mode 1'),
				'2' => $this->pi_getLL('pi_list_modmenu_2','Mode 2'),
			);
			
			// Create menu
			return $this->pi_list_modeSelector($listMenu);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - SINGLE VIEW
		 *
		 * Functions for the initialization and the output of the single view.
		 ***************************************************************/
		
		/**
		 * Returns the single view section.
		 * 
		 * This function creates the single view, and launches the
		 * needed functions to correctly display the view.
		 * 
		 * @param		$content			The content setup
		 * @param		$conf				The TS setup
		 * @param		$templateContent	The template C-Object
		 * @return		The template section for the single view
		 * @see			makeContent
		 * @see			makeStyledContent
		 * @see			renderTemplate
		 */
		function singleView($content,$conf,$templateContent) {
			
			// Get current record
			$this->internal['currentTable'] = $this->extTable;
			$this->internal['currentRow'] = $this->pi_getRecord($this->extTable,$this->piVars['showUid']);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Set default plufin variables
			$this->pi_setPiVarDefaults();
			

			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// Set page title (used by the indexed search engine)
			if ($this->internal['currentRow']['name']) {
				$GLOBALS['TSFE']->indexedDocTitle = $this->internal['currentRow']['title'];
			}
			
			// Markers substitution
			$templateMarkers['###SINGLEVIEW_TITLE###'] = $this->makeContent('title','single');
			$templateMarkers['###SINGLEVIEW_SUBTITLE###'] = $this->makeContent('subtitle','single');
			$templateMarkers['###SINGLEVIEW_AUTHORS###'] = $this->makeContent('authors','single');
			$templateMarkers['###SINGLEVIEW_BOOKID###'] = $this->makeContent('bookid','single');
			$templateMarkers['###SINGLEVIEW_ABSTRACT###'] = $this->makeContent('abstract','single');
			$templateMarkers['###SINGLEVIEW_COVER###'] = $this->makeContent('cover','single');
			$templateMarkers['###SINGLEVIEW_COLLECTION###'] = $this->makeContent('collection','single');
			$templateMarkers['###SINGLEVIEW_INFOS###'] = $this->getInfoFields($this->internal['currentRow']['uid']);
			$templateMarkers['###SINGLEVIEW_REEDITIONS###'] = $this->getReEditions($this->internal['currentRow']['uid']);
			$templateMarkers['###SINGLEVIEW_EDITPANEL###'] = $this->pi_getEditPanel();
			$templateMarkers['###SINGLEVIEW_BACKLINK###'] = $this->pi_list_linkSingle($this->pi_getLL('back','Back'),0);
			
			// Template rendering
			$content = $this->makeStyledContent('div','singleView',$this->renderTemplate($templateContent,$templateMarkers,'###SINGLEVIEW_TEMPLATE###'));
			
			// Return content
			return $content;
		}
		
		/**
		 * 
		 */
		function getInfoFields($uid) {
			
			// Storage
			$content = array();
			
			// Editions
			$this->editions = array();
			
			// Additionnal MySQL WHERE clause
			$whereClause = 'uid=' . $uid . ' OR original=' . $uid;
			
			// Make listing query - Pass query to MySQL database
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($this->conf['singleView.']['infoFields'],$this->extTable,$whereClause,false,'pubyear DESC,reedition DESC');
			
			// Get each edition
			while ($edition = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->editions[] = $this->makeInfoFields($edition);
			}
			
			// Return info fields
			return array_shift($this->editions);
		}
		
		/**
		 * 
		 */
		function makeInfoFields($fields) {
			
			// Storage
			$content = array();
			
			// Process each field
			foreach ($fields as $key=>$value) {
				
				// Get field
				$fieldContent = $this->getFieldContent($key,'single',$value);
				
				// Memorize field
				if (!empty($fieldContent)) {
					$content[] = $fieldContent;
				}
			}
			
			// Edition row
			$row = implode(', ',$content);
			
			// Return content
			return $this->makeStyledContent('div','edition',$row);
		}
		
		/**
		 * 
		 */
		function getReEditions($uid) {
			
			// Check for other editions
			if (count($this->editions)) {
				// Storage
				$content = array();
				
				// Separator
				$content[] = '<hr>';
				
				// Header
				$content[] = $this->makeStyledContent('h3','reeditions',$this->pi_getLL('pi_single_reeditions'));
				
				// Process each edition
				foreach ($this->editions as $edition) {
					$content[] = $edition;
				}
				
				// Return editions
				return implode(chr(10),$content);
			}
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
					$content = ($fieldName == 'title') ? $this->makeStyledContent('h2','singleViewField-' . $fieldName,$fieldContent) : $this->makeStyledContent('p','singleViewField-' . $fieldName,$fieldContent);
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
		 * @see			getPictureField
		 * @see			makeHrefTag
		 * @see			getExternalTableField
		 * @return		The processed field
		 */
		function getFieldContent($fieldName,$view,$content=false) {
			
			// Get field content
			$field = ($content) ? $content : $this->internal['currentRow'][$fieldName];
			
			// List of the special fields to display even if the value is false
			$specialFields = array('collection','reedition');
			
			// Field is empty
			if (!empty($field) || in_array($fieldName,$specialFields)) {
				
				// Process special fields
				switch($fieldName) {
					
					// UID -> Link
					case 'uid':
					
						// 1 = single items caching (0 = disable)
						$content = ($view == 'single') ? $field : $this->pi_list_linkSingle($field,$this->internal['currentRow']['uid'],1);
					break;
					
					// Title -> List
					case 'title':
						if ($view == 'single') {
							
							// Single view
							$content = $field;
						} else {
							
							// List view
							$content = $this->pi_list_linkSingle($field,$this->internal['currentRow']['uid'],1);
						}
					break;
					
					// Picture -> Thumb
					case 'cover':
						$content = ($view == 'single') ? $this->getPictureField($field,$view) : $this->pi_list_linkSingle($this->getPictureField($field,$view),$this->internal['currentRow']['uid'],1);
					break;
					
					// Authors -> List
					case 'authors':
						if ($view == 'single') {
							
							// Single view
							$content = $this->makeListFromField($field,chr(10));
						} else {
							
							// List view
							$content = $this->makeCommalistFromField($field,chr(10));
						}
						
					break;
					
					// Book ID
					case 'bookid':
						$content = $this->pi_getLL('number') . ': ' . $field;
					break;
					
					// ISBN
					case 'isbn':
						$content = $this->pi_getLL('pi_single_isbn') . ': ' . $field;
					break;
					
					// Pages
					case 'pages':
						$content = $field . ' ' . $this->pi_getLL('pi_single_pages');
					break;
					
					// Price / CHF
					case 'price_chf':
						$after = (strstr($field,'.')) ? '' : '.-';
						$content = $this->pi_getLL('pi_single_price_chf') . ' ' . $field . $after;
					break;
					
					// Price / Eur
					case 'price_eur':
						$after = (strstr($field,'.')) ? '' : '.-';
						$content = $this->pi_getLL('pi_single_price_eur') . ' ' . $field . $after;
					break;
					
					// Collection
					case 'collection':
						$content = $this->pi_getLL('pi_single_collection') . ': ' .  $this->pi_getLL('pi_single_collection.I.' . $field);
					break;
					
					// Reedition
					case 'reedition':
						$content = ($field) ? $this->pi_getLL('pi_single_reedition') : $this->pi_getLL('pi_single_original');
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
		 * Return one or more pictures.
		 * 
		 * This function creates a thumbnails for picture ressources contained in
		 * a database field (comma list value), and launch the needed functions
		 * to display the full-size image.
		 * 
		 * @param		$field				The field to return the image(s)
		 * @param		$sep				The separator to use if value is comma list
		 * @return		Picture thumbnail(s)
		 */
		function getPictureField($field,$view,$sep='<br />') {
			
			// Storage
			$content = array();
			
			// Pictures to get
			$pictures = explode(',',$field);
			
			// Process each picture
			foreach($pictures as $pic) {
				
				// Path
				$path = $this->uploadDir . $pic;
				
				// Thumbnail
				$imgTSConfig = $this->conf[$view . 'View.']['thumbCObject.'];
				$imgTSConfig['file'] = $path;
				
				// Picture
				$content[] = $this->cObj->IMAGE($imgTSConfig);
			}
			
			// Return content
			return implode($sep,$content);
		}
		
		/**
		 * 
		 */
		function makeListFromField($content,$separator) {
			
			// Storage
			$listCode = array();
			
			// Array of field values
			$fieldArray = explode($separator,$content);
			
			// Start list
			$listCode[] = '<ul>';
			
			// Process each value
			foreach($fieldArray as $val) {
				$listCode[] = '<li>' . $val . '</li>';
			}
			
			// End list
			$listCode[] = '</ul>';
			
			// Return list
			return implode(chr(10),$listCode);
		}
		
		/**
		 * 
		 */
		function makeCommalistFromField($content,$separator) {
			
			// Array of field values
			$fieldArray = explode($separator,$content);
			
			// Return list
			return implode(', ',$fieldArray);
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
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/eesp_books/pi1/class.tx_eespbooks_pi1.php"])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/eesp_books/pi1/class.tx_eespbooks_pi1.php"]);
	}
?>
