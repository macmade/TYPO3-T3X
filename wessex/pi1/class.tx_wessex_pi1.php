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
	 * Plugin 'Wessex - Linguistic stays' for the 'wessex' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 * 				function main($content,$conf)
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// macmade.net API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_wessex_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_wessex_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_wessex_pi1.php';
		
		// The extension key
		var $extKey = 'wessex';
		
		// Database tables
		var $extTables = array(
			
			// Languages
			'languages' => 'tx_wessex_languages',
			
			// Countries
			'countries' => 'tx_wessex_countries',
			
			// Cities
			'cities' => 'tx_wessex_cities',
			
			// Course categories
			'categories' => 'tx_wessex_coursecategories',
			
			// Course types
			'types' => 'tx_wessex_coursetypes',
		);
		
		// Upload folder
		var $uploadFolder = 'uploads/tx_wessex/';
		
		// SQL caching for external databases
		var $sqlCache = array();
		
		// Version of the macmade.net API required
		var $apimacmade_version = 1.2;
		
		
		
		
		
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
		 * @see			initTemplate
		 * @see			buildCatMenu
		 * @see			renderTemplate
		 */
		function main($content,$conf) {
			
			// New instance of the Developer API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Set default plufin variables
			$this->pi_setPiVarDefaults();
			
			// Init flexform configuration of the plugin
			$this->pi_initPIflexForm();
			
			// Store flexform informations
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
			
			// Set final configuration (TS or FF)
			$this->setConfig();
			
			// Check for a single record
			if ($this->piVars['showUid']) {
				
				// Try to get a single record
				$singleRecord = $this->pi_getRecord($this->extTables['cities'],$this->piVars['showUid']);
			}
			
			// Display mode
			if (is_array($singleRecord)) {
				
				// Template load and init
				$this->api->fe_initTemplate($this->conf['templateFile']);
				
				// Display single record
				$content = $this->viewCity($singleRecord);
				
			} else {
				
				// Add JavaScript Code
				$this->api->fe_buildSwapClassesJSCode('open','closed');
				
				// List stays
				$content = $this->getStays($this->extTables['languages']);
			}
			
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
				'imgConf.' => array(
					'front_picture.' => array(
						'file.' => array(
							'maxW' => 'sDEF:front_picture_maxw',
							'maxH' => 'sDEF:front_picture_maxh',
						),
					),
					'map.' => array(
						'file.' => array(
							'maxW' => 'sDEF:map_maxw',
							'maxH' => 'sDEF:map_maxh',
						),
					),
					'pictures.' => array(
						'file.' => array(
							'maxW' => 'sDEF:pictures_maxw',
							'maxH' => 'sDEF:pictures_maxh',
						),
					),
				),
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 5 - LIST
		 *
		 * Construction of the list view
		 ***************************************************************/
		
		/**
		 * List linguistic stays
		 * 
		 * This function produces a drop-down list of the linguistic stays.
		 * The fisrt level is the languages, the second the countries, and the
		 * third the cities.
		 * 
		 * @param		$table				The database table to list
		 * @param		$pVal				The foreign key of the parent table
		 * @return		A drop-down list of the linguistic stays
		 */
		function getStays($table,$pVal=false) {
			
			// Child tables
			switch($table) {
				
				// First level
				case 'tx_wessex_languages':
					$childTable = $this->extTables['countries'];
					$pKey = false;
				break;
				
				// Second level
				case 'tx_wessex_countries':
					$childTable = $this->extTables['cities'];
					$pKey = 'id_language';
				break;
				
				// Third level
				default:
					$childTable = false;
					$pKey = 'id_country';
				break;
			}
			
			// Storage
			$menu = array();
			
			// Additionnal MySQL WHERE clause
			$addWhere = ($pKey) ? ' AND ' . $pKey . '=' . $pVal : '';
			
			// MySQL WHERE clause
			$whereClause = 'sys_language_uid=0 AND pid IN (' . $this->conf[ 'pidList' ] . ')' . $addWhere . $this->cObj->enableFields($table);
			
			// MySQL ORDER BY clause
			$orderBy = 'sorting';
			
			// MySQL ressource
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,$whereClause,false,$orderBy);
			
			// CSS class for list items
			$listClass = 'closed';
			
			// Get categories
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Localization support
				if ($lang = t3lib_div::_GP('L')) {
					
					// MySQL WHERE clause
					$langClause = 'sys_language_uid=' . $lang . ' AND l18n_parent=' . $row['uid'];
					
					// MySQL ressource
					$langRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,$langClause);
					
					// Try to get localized record
					if ($langRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($langRes)) {
						
						// Translation available
						$rec = $langRow;
					} else {
						
						// No translation available
						$rec = $row;
					}
					
					// Free MySQL ressource
					$GLOBALS['TYPO3_DB']->sql_free_result($langRes);
					
				} else {
					
					// Default language
					$rec = $row;
	
				}

				// Additionnal params for first level DIV tag
				// Modified by keizo
				$imageLink = 'fileadmin/templates/fonds_sejours/'.substr($rec['name'], 0, 2).'.jpg';
				//$addParams = ($table == $this->extTables['languages']) ? array('style'=>'background-image: url('.$imageLink.'); background-color: ' . $rec['color'] ) : array('id'=>$table.'_'.substr($rec['name'], 0, 2));
				$addParams = array('id'=>$table.'_'.substr($rec['name'], 0, 2));
				
				// ID of the list item
				$listId = $table . '_' . $rec['uid'];
				//$listId = $table . '_' . substr($rec['name'], 0, 2);
				
				
				// Start list tag
				$menu[] = $this->api->fe_makeStyledContent('li',$listClass,0,0,0,1,array('id'=>$listId));
				
				// Start div tag
				$menu[] = $this->api->fe_makeStyledContent('div',$table,0,1,0,1,$addParams);
				/*
				print_r($addParams);
				echo('<br />');
				*/
				// Try to get subcategories
				$childs = ($childTable) ? $this->getStays($childTable,$row['uid']) : false;
				
				// Link to use for the category
				if ($childs) {
					
					// Display subcategories
					$link = $this->api->fe_makeSwapClassesJSLink($listId,$rec['name'],0,0);
					

				} else if ($table == $this->extTables['cities']) {
					
					// Show stay
					$link = $this->pi_list_linkSingle($rec['name'],$row['uid']);
					
		
				} else {
					
					// Display this category
					$link = $rec['name'];
				}
				
				// Add category and subcategories if any
				$menu[] = $link . $childs;
				
				// End list tag
				$menu[] = '</div></li>';
			}
			
			// Free MySQL ressource
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			
			// Check for entries in menu
			if (count($menu)) {
				
				// Full menu for the current level
				$levelMenu = '<ul>' . implode(chr(10),$menu) . '</ul>';
				
				// Return menu
				return $levelMenu;
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
		function viewCity($row) {
			
			// Get parent records
			$this->country = $this->pi_getRecord($this->extTables['countries'],$row['id_country']);
			$this->language = $this->pi_getRecord($this->extTables['languages'],$this->country['id_language']);
			
			// Localization support
			if ($lang = t3lib_div::_GP('L')) {
				
				// MySQL WHERE clause
				$langClause = 'sys_language_uid=' . $lang . ' AND l18n_parent=' . $row['uid'];
				
				// MySQL ressource
				$langRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['cities'],$langClause);
				
				// Try to get localized record
				if ($langRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($langRes)) {
					
					// Translation available
					$this->rec = $langRow;
				} else {
					
					// No translation available
					$this->rec = $row;
				}
				
				// Free MySQL ressource
				$GLOBALS['TYPO3_DB']->sql_free_result($langRes);
				
			} else {
				
				// Default language
				$this->rec = $row;
			}
			
			// Set page title (used by the indexed search engine)
			$GLOBALS['TSFE']->indexedDocTitle = $rec['name'];
			
			// Template markers
			$templateMarkers = array();
			
			// Overriding template markers
			$templateMarkers['###LANG_NAME###'] = $this->getFieldContent('lang_name');
			$templateMarkers['###LANG_COLOR###'] = $this->getFieldContent('lang_color');
			$templateMarkers['###FULLNAME###'] = $this->getFieldContent('fullname');
			$templateMarkers['###NAME###'] = $this->getFieldContent('name');
			$templateMarkers['###FRONT_PICTURE###'] = $this->getFieldContent('front_picture');
			$templateMarkers['###CREDIT###'] = $this->getFieldContent('credit');
			$templateMarkers['###MAP###'] = $this->getFieldContent('map');
			$templateMarkers['###COUNTRY_NAME###'] = $this->getFieldContent('country_name');
			$templateMarkers['###COUNTRY_INFOS###'] = $this->getFieldContent('country_infos');
			$templateMarkers['###DESCRIPTION###'] = $this->getFieldContent('description');
			$templateMarkers['###INFORMATIONS###'] = $this->getFieldContent('informations');
			$templateMarkers['###INFOBOX_1###'] = $this->getFieldContent('infobox_1');
			$templateMarkers['###INFOBOX_2###'] = $this->getFieldContent('infobox_2');
			$templateMarkers['###INFO_TABLES###'] = $this->getFieldContent('info_tables');
			$templateMarkers['###PICTURES###'] = $this->getFieldContent('pictures');
			
			// Wrap all in a CSS element
			$content = $this->api->fe_makeStyledContent('div','single',$this->api->fe_renderTemplate($templateMarkers,'###SINGLE###'));
			
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
		function getFieldContent($fieldName) {
			
			// Get field
			$field = $this->rec[$fieldName];
			
			// Process fields
			switch($fieldName) {
				
				// Country name
				case 'fullname':
					$content = $this->rec['name']. ' - ' . $this->getCountry('name');
				break;
				
				// Description
				case 'description':
				
					// RTE field
					$content = $this->pi_RTEcssText($field);
				break;
				
				// Tables
				case 'info_tables':
				
					// RTE field
					$content = $this->pi_RTEcssText($field);
				break;
				
				// Front picture
				case 'front_picture':
				
					// Display images
					$content = $this->api->fe_createImageObjects($field,$this->conf['imgConf.']['front_picture.'],$this->uploadFolder);
				break;
				
				// Map
				case 'map':
				
					// Display images
					$content = $this->api->fe_createImageObjects($field,$this->conf['imgConf.']['map.'],$this->uploadFolder);
				break;
				
				// Pictures
				case 'pictures':
				
					// Display images
					$content = $this->api->fe_createImageObjects($field,$this->conf['imgConf.']['pictures.'],$this->uploadFolder);
				break;
				
				// Country name
				case 'country_name':
					$content = $this->getCountry('name');
				break;
				
				// Country informations
				case 'country_infos':
					$content = $this->pi_RTEcssText($this->getCountry('informations'));
				break;
				
				// Country informations
				case 'credit':
					$content = ($link = $this->rec['credit_url']) ? '<a href="' . $link . '" target="_blank">' . $field . '</a>' : $field;
				break;
				
				// Country name
				case 'lang_name':
					$content = $this->getLang('name');
				break;
				
				// Country name
				case 'lang_color':
					$content = $this->getLang('color');
				break;
				
				// Infos
				case 'informations':
					$content = $this->pi_RTEcssText($field);
				break;
				
				// Info box 1
				case 'infobox_1':
					$content = $this->pi_RTEcssText($field);
				break;
				
				// Info box 2
				case 'infobox_2':
					$content = $this->pi_RTEcssText($field);
				break;
				
				// Default
				default:
				
					// Convert new lines into <br> tag
					$content = nl2br($field);
				break;
			}
			
			// Return content
			// Initial was just : return $this->api->fe_makeStyledContent('div',$fieldName,$content);
			// modified by keizo on 20.04.05 for just having the colorCode without the <div> formating
			if($fieldName == 'lang_color' || $fieldName == 'lang_name' ){
			    return $content;
            }else{
                return $this->api->fe_makeStyledContent('div',$fieldName,$content);
            }
		}
		
		/**
		 * 
		 */
		function getCountry($field) {
			
			// Get country
			$row = $this->country;
			
			// Localization support
			if ($lang = t3lib_div::_GP('L')) {
				
				// MySQL WHERE clause
				$langClause = 'sys_language_uid=' . $lang . ' AND l18n_parent=' . $row['uid'];
				
				// MySQL ressource
				$langRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['countries'],$langClause);
				
				// Try to get localized record
				if ($langRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($langRes)) {
					
					// Translation available
					$country = $langRow;
				} else {
					
					// No translation available
					$country = $row;
				}
				
				// Free MySQL ressource
				$GLOBALS['TYPO3_DB']->sql_free_result($langRes);
				
			} else {
				
				// Default language
				$country = $row;
			}
			
			// Return content
			return $country[$field];
		}
		
		/**
		 * 
		 */
		function getLang($field) {
			
			// Get language
			$row = $this->language;
			
			// Localization support
			if ($lang = t3lib_div::_GP('L')) {
				
				// MySQL WHERE clause
				$langClause = 'sys_language_uid=' . $lang . ' AND l18n_parent=' . $row['uid'];
				
				// MySQL ressource
				$langRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['languages'],$langClause);
				
				// Try to get localized record
				if ($langRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($langRes)) {
					
					// Translation available
					$language = $langRow;
				} else {
					
					// No translation available
					$language = $row;
				}
				
				// Free MySQL ressource
				$GLOBALS['TYPO3_DB']->sql_free_result($langRes);
				
			} else {
				
				// Default language
				$language = $row;
			}
			
			// Return content
			return $language[$field];
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wessex/pi1/class.tx_wessex_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wessex/pi1/class.tx_wessex_pi1.php']);
	}
?>
