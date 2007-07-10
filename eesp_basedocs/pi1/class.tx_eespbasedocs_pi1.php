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
	 * Plugin 'Classified ads' for the 'classifieds_macmade' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 * 				function main($content,$conf)
	 * 				function initTemplate
	 * 
	 * SECTION:		2 - CAT MENU
	 * 				function buildCatMenu
	 * 
	 * SECTION:		3 - MISC / UTILS
	 * 				function renderTemplate($templateContent,$templateMarkers,$templateSection)
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// macmade.net API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_eespbasedocs_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_eespbasedocs_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_eespbasedocs_pi1.php';
		
		// The extension key
		var $extKey = 'eesp_basedocs';
		
		// Database tables
		var $extTables = array(
			
			// Activities
			'activities' => 'tx_eespbasedocs_activity',
			
			// Instances
			'instances' => 'tx_eespbasedocs_instances',
			
			// Categories
			'categories' => 'tx_eespbasedocs_categories',
			
			// Relation table
			'mm' => 'tx_eespbasedocs_activity_instances_mm',
		);
		
		// Upload folder
		var $uploadFolder = 'uploads/tx_eespbasedocs/';
		
		// Version of the macmade.net API required
		var $apimacmade_version = 2.2;
		
		// Check cHash
		var $pi_checkCHash = true;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin 'tx_ergotheca_pi1', and
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
			
			// Check for required values
			if ($this->conf['pidList']) {
				
				// Get an comma list of all the pages from where to select records
				$this->startingPoints = $this->pi_getPidList($this->conf['pidList'],$this->conf['recursive']);
				
				// Build menu
				$content = $this->buildMenu();
				
			} else {
				
				// No starting point defined
				$content = '<strong>' . $this->pi_getLL('pi_error_conf') . '</strong>';
			}
			
			// DEBUG ONLY - Output plugin variables
			#$this->api->debug($this->piVars);
			
			// Return content
			return $this->pi_wrapInBaseClass($content);
		}
		
		/**
		 * 
		 */
		function setConfig() {
			
			// Mapping array for PI flexform
			$flex2conf = array(
				'pidList' => 'sDEF:pages',
				'recursive' => 'sDEF:recursive',
				'catMenu.' => array(
					'expandLevels' => 'sCAT:expand_levels',
				),
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 2 - CAT MENU
		 *
		 * Construction of the categories menu.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function buildMenu() {
			
			// Add JavaScript Code
			$this->buildJSCode();
			
			// Storage
			$catMenu = array();
			
			// MySQL WHERE clause
			$whereClause = 'pid IN (' . $this->startingPoints . ')' . $this->cObj->enableFields($this->extTables['categories']);
			
			// MySQL ORDER BY clause
			$orderBy = 'sorting';
			
			// Select categories
			$res =  $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['categories'],$whereClause,false,$orderBy);
			
			// Check MySQL ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Start list
				$catMenu[] = '<ul>';
				
				// Process categories
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Level
					$level = 0;
					
					// CSS class for list items
					$listClass = ($this->conf['catMenu.']['expandLevels'] > $level) ? 'open' : 'closed';
					
					// ID of the list item
					$listId = $this->prefixId . '_level' . $level . '_' . $row['uid'];
					
					// List picture
					$listImage = ($this->conf['catMenu.']['expandLevels'] > $level) ? $this->conf['catMenu.']['closeImage'] : $this->conf['catMenu.']['openImage'];
					
					// Start list tag
					$catMenu[] = $this->api->fe_makeStyledContent('li',$listClass,0,0,0,1,array('id'=>$listId));
					
					// Try to get childs
					$childs = $this->getActivities($row['uid']);
					
					// Link to use for the category
					if ($childs) {
						
						// Category link
						$open = $this->api->fe_makeSwapClassesJSLink($listId,$this->cObj->fileResource($listImage,'id="' . $listId . '_pic"'),0,0,array('title'=>$row['name']));
						
						// Category with activities
						$content = $open . $row['name'] . $childs;
						
					} else {
						
						// Category name
						$content = $row['name'];
					}
					
					// Start div tag
					$catMenu[] = $this->api->fe_makeStyledContent('div','level' . $level,$content);
				
					// End list tag
					$catMenu[] = '</li>';
				}
				
				// End list
				$catMenu[] = '</ul>';
				
				// Free MySQL ressource
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
			}
			
			// Return menu
			return $this->api->fe_makeStyledContent('div','catMenu',implode(chr(10),$catMenu));
		}
		
		/**
		 * 
		 */
		function getActivities($cat) {
			
			// Storage
			$catMenu = array();
			
			// MySQL WHERE clause
			$whereClause = 'cat=' . $cat . ' AND pid IN (' . $this->startingPoints . ')' . $this->cObj->enableFields($this->extTables['activities']);
			
			// MySQL ORDER BY clause
			$orderBy = 'sorting';
			
			// Select categories
			$res =  $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['activities'],$whereClause,false,$orderBy);
			
			// Check MySQL ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Start list
				$catMenu[] = '<ul>';
				
				// Process categories
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Level
					$level = 1;
					
					// CSS class for list items
					$listClass = ($this->conf['catMenu.']['expandLevels'] > $level) ? 'open' : 'closed';
					
					// ID of the list item
					$listId = $this->prefixId . '_level' . $level . '_' . $row['uid'];
					
					// List picture
					$listImage = ($this->conf['catMenu.']['expandLevels'] > $level) ? $this->conf['catMenu.']['closeImage'] : $this->conf['catMenu.']['openImage'];
					
					// Start list tag
					$catMenu[] = $this->api->fe_makeStyledContent('li',$listClass,0,0,0,1,array('id'=>$listId));
					
					// Try to get childs
					$childs = $this->getInstances($row['uid']);
					
					// Link to use for the category
					if ($childs) {
						
						// Activity link
						$open = $this->api->fe_makeSwapClassesJSLink($listId,$this->cObj->fileResource($listImage,'id="' . $listId . '_pic"'),0,0,array('title'=>$row['title']));
						
						// Category with activities
						$content = $open . $this->pi_list_linkSingle($row['title'],$row['uid'],1,array('table'=>'activities')) . $childs;
						
					} else {
						
						// Category name
						$content = $row['title'];
					}
					
					// Start div tag
					$catMenu[] = $this->api->fe_makeStyledContent('div','level' . $level,$content);
					
					// End list tag
					$catMenu[] = '</li>';
				}
				
				// End list
				$catMenu[] = '</ul>';
				
				// Free MySQL ressource
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
			}
			
			// Check for items
			if (count($catMenu)) {
				
				// Return menu
				return implode(chr(10),$catMenu);
			}
		}
		
		/**
		 * 
		 */
		function getInstances($activity) {
			
			// Storage
			$catMenu = array();
			
			// Fields to get
			$selectFields = $this->extTables['instances'] . '.*';
			
			// MySQL WHERE clause
			$whereClause = ' AND ' . $this->extTables['activities'] . '.uid=' . $activity . ' AND ' . $this->extTables['instances'] . '.pid IN (' . $this->startingPoints . ')' . $this->cObj->enableFields($this->extTables['instances']);
			
			// MySQL ORDER BY clause
			$orderBy = $this->extTables['mm'] . '.sorting';
			
			// Select categories
			$res =  $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($selectFields,$this->extTables['activities'],$this->extTables['mm'],$this->extTables['instances'],$whereClause,false,$orderBy);
			
			// Check MySQL ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Start list
				$catMenu[] = '<ul>';
				
				// Process categories
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Level
					$level = 2;
					
					// CSS class for list items
					$listClass = ($this->conf['catMenu.']['expandLevels'] > $level) ? 'open' : 'closed';
					
					// ID of the list item
					$listId = $this->prefixId . '_level' . $level . '_' . $row['uid'];
					
					// Start list tag
					$catMenu[] = $this->api->fe_makeStyledContent('li',$listClass,0,0,0,1,array('id'=>$listId));
					
					// Start div tag
					$catMenu[] = $this->api->fe_makeStyledContent('div','level' . $level,$this->pi_list_linkSingle($row['title'],$row['uid'],1,array('table'=>'instances')));
					
					// End list tag
					$catMenu[] = '</li>';
				}
				
				// End list
				$catMenu[] = '</ul>';
				
				// Free MySQL ressource
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
			}
			
			// Check for items
			if (count($catMenu)) {
				
				// Return menu
				return implode(chr(10),$catMenu);
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 7 - SINGLE
		 *
		 * Construction of the single view
		 ***************************************************************/
		
		/**
		 * 
		 */
		function showAd() {
			
			// Get current record
			$this->internal['currentTable'] = $this->extTables['activities'];
			$this->internal['currentRow'] = $this->pi_getRecord($this->extTables['activities'],$this->piVars['showUid']);
			
			// Set page title (used by the indexed search engine)
			if ($this->internal['currentRow']['title']) {
				$GLOBALS['TSFE']->indexedDocTitle = $this->internal['currentRow']['title'];
			}
			
			// Template markers

			$templateMarkers = array();
			
			// Get an array with the fields to render
			$fieldList = explode(',',$this->conf['single.']['displayFields']);
			
			// Process each field
			foreach($fieldList as $field) {
				
				// Markers substitution
				$templateMarkers['###' . strtoupper($field) . '###'] = $this->getFieldContent($field,'single');
			}
			
			// Add edit panel
			$templateMarkers['###EDITPANEL###'] = $this->pi_getEditPanel();
			
			// Add back link
			$templateMarkers['###BACKLINK###'] = $this->api->fe_makeStyledContent('div','backLink',$this->pi_list_linkSingle($this->pi_getLL('pi_single_backlink','Back'),0));
			
			// Wrap all in a CSS element
			$content = $this->api->fe_makeStyledContent('div','single',$this->api->fe_renderTemplate($templateMarkers,'###SINGLE###'));
			
			// Update the view counter
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['activities'],'uid=' . $this->internal['currentRow']['uid'],array('views'=>$this->internal['currentRow']['views'] + 1));
			
			// Return content
			return $content;
			
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 6 - MISCELLANEOUS
		 *
		 * General purpose functions
		 ***************************************************************/
		
		/**
		 * Fields processing
		 * 
		 * This function outputs the specified field, which is processed
		 * with the correct function
		 * 
		 * @param		$fieldName			The field to output
		 * @param		$view				The kind of view
		 * @return		The processed field
		 */
		function getFieldContent($fieldName,$view) {
			
			// Get field content and check if HTML is allowed for content
			$field = ($fieldName == 'description' && $this->conf['allowHTML']) ? $this->internal['currentRow'][$fieldName] : htmlspecialchars($this->internal['currentRow'][$fieldName]);
			
			// Process fields
			switch($fieldName) {
				
				// Title
				case 'title':
				
					// Check view and add link if necessary
					$content = ($view == 'single') ? $field : $this->pi_list_linkSingle($field,$this->internal['currentRow']['uid'],1);
				break;
				
				// Description
				case 'description':
				
					// Convert new lines into <br> tag
					$content = ($this->conf['allowHTML']) ? $field : nl2br($field);
				break;
				
				// Pictures
				case 'pictures':
				
					// Display images
					$content = $this->api->fe_createImageObjects($field,$this->conf[$view . '.']['pictures.'],$this->uploadFolder);;
				break;
				
				// Views
				case 'views':
					$content = str_replace('###NUMBER###',$this->api->fe_makeStyledContent('span','viewsNumber',$field),$this->pi_getLL('pi_field_views'));
				break;
				
				// Ad type
				case 'adtype':
					$content = $this->pi_getLL('pi_field_adtype') . ' ' . $this->api->fe_makeStyledContent('span','adType',$this->pi_getLL('pi_field_adtype.' . $field));
				break;
				
				// Ad type
				case 'adtype':
					$content = $this->pi_getLL('pi_field_adtype') . ' ' . $this->api->fe_makeStyledContent('span','adType',$this->pi_getLL('pi_field_adtype.' . $field));
				break;
				
				// Best price
				case 'price_best':
					$content = ($field) ? $this->pi_getLL('pi_field_price_best') : false;
				break;
				
				// Price to define
				case 'price_undefined':
					$content = ($field) ? $this->pi_getLL('pi_field_price_undefined') : false;
				break;
				
				// Category
				case 'category':
					
					// Get category row
					$row = $this->pi_getRecord($this->extTables['categories'],$field);
					
					// Check for a category image
					if (!empty($row['icon'])) {
						
						// Add category image
						$icon = $this->api->fe_createImageObjects($row['icon'],$this->conf['catImage.'],$this->uploadFolder);
					}
					
					// Complete category name
					$content = $this->pi_getLL('pi_field_category') . '&nbsp;' . $icon . $this->api->fe_linkTP_unsetPIvars($row['title'],array('showCat'=>$row['uid']),array('sword','pointer','showUid','action'));
				break;
				
				// Creation date
				case 'crdate':
					// Format date
					$date = strftime($this->conf['dateFormat'],$field);
					
					// Convert date
					if ($this->conf['isoDate']) {
						$date = $this->api->div_utf8ToIso($date);
					}
					
					// Add age
					$content = $this->pi_getLL('pi_field_crdate') . ' ' . $date . ' ' . str_replace('###AGE###',$this->api->div_getAge($field),$this->pi_getLL('pi_field_age'));
				break;
				
				// Price
				case 'price':
					$content = (!empty($field)) ? $this->getCurrencySymbol($this->internal['currentRow']['currency'],$field) : false;
				break;
				
				// Default
				default:
				
					// Convert new lines into <br> tag
					$content = nl2br($field);
				break;
			}
			
			// Return processed field with wrap
			if (!empty($content)) {
				return $this->cObj->wrap($content,$this->conf[$view . '.']['wrapFields.'][$fieldName]);
			}
		}
		
		/**
		 * Adds JavaScript Code.
		 * 
		 * This function adds the javascript code used to switch between
		 * CSS classes and to expand/collapse all sections.
		 * 
		 * @return		Void.
		 */
		function buildJSCode() {
			
			// Storage
			$jsCode = array();
			
			// Plus image URL
			$plusImgURL = str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($this->conf['catMenu.']['openImage']));
			
			// Minus image URL
			$minusImgURL = str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($this->conf['catMenu.']['closeImage']));
			
			// Function for swappingelement class
			$jsCode[] = 'function ' . $this->prefixId . '_swapClasses(element) {';
			$jsCode[] = '	if (document.getElementById) {';
			$jsCode[] = '		var liClass = element;';
			$jsCode[] = '		var picture = element + "_pic";';
			$jsCode[] = '		document.getElementById(liClass).className = (document.getElementById(liClass).className == "open") ? "closed" : "open";';
			$jsCode[] = '		document.getElementById(picture).src = (document.getElementById(liClass).className == "open") ? "' . $minusImgURL . '" : "' . $plusImgURL . '";';
			$jsCode[] = '	}';
			$jsCode[] = '}';
			
			// Function for expanding/collapsing all elements
			$jsCode[] = 'var expanded = 0;';
			$jsCode[] = 'function ' . $this->prefixId . '_expAll() {';
			$jsCode[] = '	if (document.getElementsByTagName) {';
			$jsCode[] = '		var listItems = document.getElementsByTagName("li");';
			$jsCode[] = '		for (i = 0; i < listItems.length; i++) {';
			$jsCode[] = '			if (listItems[i].id.indexOf("' . $this->prefixId . '") != -1) {';
			$jsCode[] = '				listItems[i].className = (expanded) ? "closed" : "open";';
			$jsCode[] = '				var picture = "pic_" + listItems[i].id.replace("' . $this->prefixId . '_","");';
			$jsCode[] = '				listItems[i].className = (expanded) ? "closed" : "open"';
			$jsCode[] = '				document.getElementById(picture).src = (expanded) ? "' . $plusImgURL . '" : "' . $minusImgURL . '";';
			$jsCode[] = '			}';
			$jsCode[] = '		}';
			$jsCode[] = '	expanded = (expanded == 1) ? 0 : 1;';
			$jsCode[] = '	}';
			$jsCode[] = '}';
			
			// Adds JS code
			$GLOBALS["TSFE"]->setJS($this->prefixId,implode(chr(10),$jsCode));
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_basedocs/pi1/class.tx_eespbasedocs_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_basedocs/pi1/class.tx_eespbasedocs_pi1.php']);
	}
?>
