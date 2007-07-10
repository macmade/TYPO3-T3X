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
	 * Plugin 'Companies' for the 'workbook' extension.
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
	
	// Static info library helper class
	require_once(t3lib_extMgm::extPath('sr_static_info').'pi1/class.tx_srstaticinfo_pi1.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_workbook_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_workbook_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_workbook_pi1.php';
		
		// The extension key
		var $extKey = 'workbook';
		
		// Database tables
		var $extTables = array(
			
			// Ads
			'companies' => 'tx_workbook_companies',
			
			// Categories
			'services' => 'tx_workbook_services',
			
			// Categories
			'references' => 'tx_workbook_references',
		);
		
		// Upload folder
		var $uploadFolder = 'uploads/tx_workbook/';
		
		// Internal variables
		var $searchFields = 'title,description,www,contact_person,contact_email,address,city';
		var $orderByFields = 'priority,title';
		
		// Countries storage
		var $countries = array();
		
		// Services storage
		var $services = array();
		
		// Version of the Developer API required
		var $apimacmade_version = 1.9;
		
		
		
		
		
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
			
			// New instance of the static info library helper class & initialization
			$this->staticInfo = t3lib_div::makeInstance('tx_srstaticinfo_pi1');
			$this->staticInfo->init();
			
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
				'templateFile' => 'sTMPL:template_file',
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'Workbook: configuration array');
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
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - LIST VIEW
		 *
		 * Construction of the list view.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function buildList() {
			
			// Get all countries
			$this->getCountries();
			
			// Get all services
			$this->getServices();
			
			// No active page
			if (!isset($this->piVars['pointer'])) {
				$this->piVars['pointer'] = 0;
			}
			
			// Additionnal MySQL WHERE clause for filters
			$whereClause = '';
			
			// Filter countries
			$whereClause .= ($this->piVars['country']) ? ' AND country=' . $this->piVars['country'] : '';
			
			// Filter services
			$whereClause .= ($this->piVars['service']) ? ' AND ' . $GLOBALS['TYPO3_DB']->listQuery('services',$this->piVars['service'],$this->extTables['companies']) : '';
			
			// Get records number
			$res = $this->pi_exec_query($this->extTables['companies'],1,$whereClause);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			// Make listing query - Pass query to MySQL database
			$res = $this->pi_exec_query($this->extTables['companies'],0,$whereClause);
			$this->internal['currentTable'] = $this->extTables['companies'];
			
			// Template markers
			$templateMarkers = array();
			
			// Overwriting template markers
			$templateMarkers['###SEARCHBOX###'] = $this->pi_list_searchBox();
			$templateMarkers['###BROWSEBOX###'] = $this->pi_list_browseresults();
			$templateMarkers['###LIST###'] = $this->makeListItems($res);
			$templateMarkers['###SELECTORS###'] = $this->makeSelectors();
			$templateMarkers['###FILTERS###'] = $this->makeFilters();
			
			// Wrap all in a CSS element
			$content = $this->api->fe_renderTemplate($templateMarkers,'###MAIN###');
			
			// Return content
			return $content;
		}
		
		/**
		 * 
		 */
		function makeFilters() {
			
			// Check for filters
			if ($this->piVars['sword'] || $this->piVars['country'] || $this->piVars['service']) {
				
				// Storage
				$htmlCode = array();
				
				// Header
				$htmlCode[] = $this->api->fe_makeStyledContent('h2','header',$this->pi_getLL('filters.header'));
				
				// Description
				$htmlCode[] = $this->api->fe_makeStyledContent('p','description',$this->pi_getLL('filters.description'));
				
				// Start div
				$htmlCode[] = $this->api->fe_makeStyledContent('div','filters','',1,0,1);
				
				// Filters storage
				$filters = array();
				
				// Search word
				if ($this->piVars['sword']) {
					
					// Filter w/ link
					$sword = $this->piVars['sword'];
					
					// Add filter
					$filters[] = '<strong>' . $this->pi_getLL('filters.sword') . '</strong>: ' . $this->api->fe_linkTP_unsetPIvars($sword,array('pointer'=>0),array('sword'));
				}
				
				// Country
				if ($this->piVars['country']) {
					
					// Filter w/ link
					$country = $this->countries[$this->piVars['country']]['localName'];
					
					// Add filter
					$filters[] = '<strong>' . $this->pi_getLL('filters.country') . '</strong>: ' . $this->api->fe_linkTP_unsetPIvars($country,array('pointer'=>0),array('country'));
				}
				
				// Service
				if ($this->piVars['service']) {
					
					// Filter w/ link
					$service = $this->services[$this->piVars['service']]['title'];
					
					// Add filter
					$filters[] = '<strong>' . $this->pi_getLL('filters.service') . '</strong>: ' . $this->api->fe_linkTP_unsetPIvars($service,array('pointer'=>0),array('service'));
				}
				
				// Add available filters
				$htmlCode[] = implode('<br />',$filters);
				
				// End div
				$htmlCode[] = '</div>';
				
				// Remove all
				$htmlCode[] = $this->api->fe_makeStyledContent('p','remove',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('filters.remove'),array('pointer'=>0),array('sword','country','service')));
				
				// Return search filters
				return $this->api->fe_makeStyledContent('div','searchFilters',implode(chr(10),$htmlCode));
			}
		}
		
		/**
		 * 
		 */
		function makeListItems($res) {
			
			// Items storage
			$items = array();
			
			// Get items to list
			while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Template markers
				$templateMarkers = array();
				
				// Override template markers
				$templateMarkers['###TITLE###'] = $this->getFieldContent('title');
				$templateMarkers['###LOGO###'] = $this->getFieldContent('logo');
				$templateMarkers['###WWW###'] = $this->getFieldContent('www');
				$templateMarkers['###COUNTRY###'] = $this->getFieldContent('country');
				$templateMarkers['###SERVICES###'] = $this->getFieldContent('services');
				
				// Build item
				$items[] = $this->api->fe_renderTemplate($templateMarkers,'###ITEM###');
			}
			
			// Return list
			return $this->api->fe_makeStyledContent('div','list',implode(chr(10),$items));
		}
		
		/**
		 * 
		 */
		function makeSelectors() {
			
			// Storage
			$htmlCode = array();
			
			// Start form
			$htmlCode[] = '<form action="' . $this->cObj->lastTypoLinkUrl . '" method="post" name="' . $this->prefixId . '-selectors">';
			
			// Start countries select
			$htmlCode[] = '<select name="countries" onchange="document.location=this.options[this.selectedIndex].value;" selected>';
			
			// Label
			$htmlCode[] = '<option value="' . $this->cObj->lastTypoLinkUrl . '">' . $this->pi_getLL('select.countries') . ':</option>';
			
			// Build options
			foreach($this->countries as $key=>$value) {
				
				// Add service
				$htmlCode[] = '<option value="' . $this->api->fe_linkTP_unsetPIvars_url(array('country'=>$key),array('pointer')) . '">' . $value['localName'] . '</option>';
			}
			
			// End select
			$htmlCode[] = '</select>';
			
			// Start services select
			$htmlCode[] = '<select name="services" onchange="document.location=this.options[this.selectedIndex].value;" selected>';
			
			// Label
			$htmlCode[] = '<option value="' . $this->cObj->lastTypoLinkUrl . '">' . $this->pi_getLL('select.services') . ':</option>';
			
			// Build options
			foreach($this->services as $key=>$value) {
				
				// Add service
				$htmlCode[] = '<option value="' . $this->api->fe_linkTP_unsetPIvars_url(array('service'=>$key),array('pointer')) . '">' . $value['title'] . '</option>';
			}
			
			// End select
			$htmlCode[] = '</select>';
			
			// End form
			$htmlCode[] = '</form>';
			
			// Return select
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function getCountries() {
			
			// MySQL query parts storage
			$query = array(
			
				// Field selection
				'SELECT static_countries.uid,static_countries.cn_iso_3',
				
				// FROM instruction
				'FROM static_countries,' . $this->extTables['companies'],
				
				// Where clause
				'WHERE ' . $this->extTables['companies'] . '.country=static_countries.uid AND ' . $this->extTables['companies'] . '.pid IN (' . $this->startingPoints . ')',
				
				// Special options
				$this->cObj->enableFields($this->extTables['companies']),
				
				// GROUP BY instruction
				'GROUP BY ' . $this->extTables['companies'] . '.country',
				
				// ORDER BY instruction
				'ORDER BY static_countries.cn_short_en',
			);
			
			// Select available countries
			$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db,implode(' ',$query));
			
			// Process each row
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Get and store localized country name
				$row['localName'] = $this->staticInfo->getStaticInfoName('COUNTRIES',$row['cn_iso_3']);
				
				// Memorize row
				$this->countries[$row['uid']] = $row;
			}
			
			// DEBUG ONLY - Show countries
			#$this->api->debug($this->countries,'Workbook: countries');
		}
		
		/**
		 * 
		 */
		function getServices() {
			
			// Select available services
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['services'],'pid=' . $this->startingPoints,'','title');
			
			// Process each row
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Memorize row
				$this->services[$row['uid']] = $row;
			}
			
			// DEBUG ONLY - Show services
			#$this->api->debug($this->services,'Workbook: services');
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
					$content = $field;
				break;
				
				// Logo
				case 'logo':
					
					$content = $this->api->fe_createImageObjects($field,$this->conf['logoCObject.'],$this->uploadFolder);
				break;
				
				// Website
				case 'www':
					$content = '<a href="' . $field . '" target="_blank">' . $field . '</a>';
				break;
				
				// Country
				case 'country':
					$content = $this->countries[$field]['localName'];
				break;
				
				// Country
				case 'services':
					$content = $this->showServices($field);
				break;
			}
			
			// Field without header
			if ($fieldName == 'title' || $fieldName == 'logo') {
				
				// Return content
				return $content;
				
			} else {
				
				// Return content with header
				return '<strong>' . $this->pi_getLL('labels.' . $fieldName) . '</strong>: ' . $content;
			}
		}
		
		/**
		 * 
		 */
		function showServices($uidList) {
			
			// Serviced UIDs
			$keys = explode(',',$uidList);
			
			// Storage
			$services = array();
			
			// Get services
			foreach($keys as $uid) {
				
				// Add service
				$services[] = $this->services[$uid]['title'];
			}
			
			// Return list of services
			return implode(', ',$services);
		}
	}
	
	// XClass inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/workbook/pi1/class.tx_workbook_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/workbook/pi1/class.tx_workbook_pi1.php']);
	}
?>
