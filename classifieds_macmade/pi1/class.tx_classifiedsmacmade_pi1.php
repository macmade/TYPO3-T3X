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
	
	class tx_classifiedsmacmade_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_classifiedsmacmade_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_classifiedsmacmade_pi1.php';
		
		// The extension key
		var $extKey = 'classifieds_macmade';
		
		// Database tables
		var $extTables = array(
			
			// Ads
			'ads' => 'tx_classifiedsmacmade_ads',
			
			// Categories
			'categories' => 'tx_classifiedsmacmade_categories',
			
			// Users
			'users' => 'fe_users',
		);
		
		// Upload folder
		var $uploadFolder = 'uploads/tx_classifiedsmacmade/';
		
		// Internal variables
		var $searchFields = 'title,subtitle,description,price';
		var $orderByFields = 'crdate,views';
		
		// SQL caching for external databases
		var $sqlCache = array();
		
		// Storage for fe_adminLib input
		var $feAdminInput = array();
		
		// Version of the macmade.net API required
		var $apimacmade_version = 1.0;
		
		
		
		
		
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
			if ($this->conf['pidList'] && $this->conf['users.']['storagePid'] && $this->conf['users.']['defaultGroup']) {
				
				// Get an comma list of all the pages from where to select records
				$this->startingPoints = $this->pi_getPidList($this->conf['pidList'],$this->conf['recursive']);
				
				// MySQL query init
				$this->setInternalVars();
				
				// Check template path and add upload directory if necessary
				$templateFile = (strstr($this->conf['templateFile'],'/')) ? $this->conf['templateFile'] : $this->uploadFolder . $this->conf['templateFile'];
				
				// Template load and init.
				$this->api->fe_initTemplate($templateFile);
				
				// Get incoming fe_adminLib POST variables
				if ($GPvars = t3lib_div::_POST('FE')) {
					$this->feAdminInput = $GPvars;
				}
				
				// Template markers storage
				$templateMarkers = array();
				
				// Overwriting template markers
				$templateMarkers['###TOOLS###'] = $this->buildToolbar();
				$templateMarkers['###CATMENU###'] = $this->buildCatMenu();
				
				// Check view type
				if ($this->piVars['showUid']) {
					
					// SIngle view
					$templateMarkers['###ITEMS###'] = $this->showAd();
					
				} else if ($this->piVars['action'] == 'register' || array_key_exists('fe_users',$this->feAdminInput)) {
					
					// Register
					$templateMarkers['###ITEMS###'] = $this->registerUser();
					
				} else if ($this->piVars['action'] == 'connect') {
					
					// Login
					$templateMarkers['###ITEMS###'] = $this->login();

					
				}else {
					
					// List view
					$templateMarkers['###ITEMS###'] = $this->buildList();
				}
				
				// Template rendering
				$content = $this->api->fe_renderTemplate($templateMarkers,'###MAIN###');
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
				'allowHTML' => 'sDEF:allow_html',
				'templateFile' => 'sTEMPLATE:template_file',
				'dateFormat' => 'sDEF:date_format',
				'list.' => array(
					'defaultMode' => 'sLIST:default_mode',
					'maxRecords' => 'sLIST:max_records',
					'maxPages' => 'sLIST:max_pages',
					'displayFields' => 'sLIST:display_fields',
					'pictures.' => array(
						'file.' => array(
							'maxW' => 'sLIST:image_maxw',
							'maxH' => 'sLIST:image_maxh',
						),
						'params' => 'sLIST:image_params',
					),
				),
				'single.' => array(
					'displayFields' => 'sSINGLE:display_fields',
					'pictures.' => array(
						'file.' => array(
							'maxW' => 'sSINGLE:image_maxw',
							'maxH' => 'sSINGLE:image_maxh',
						),
						'params' => 'sSINGLE:image_params',
					),
				),
				'users.' => array(
					'storagePid' => 'sUSER:storage',
					'defaultGroup' => 'sUSER:group',
				),
				'catImage.' => array(
					'file.' => array(
						'maxW' => 'sCAT:image_maxw',
						'maxH' => 'sCAT:image_maxh',
					),
					'params' => 'sCAT:image_params',
				),
				'catMenu.' => array(
					'expandLevels' => 'sCAT:expand_levels',
				),
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf);
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
			
			// Default mode
			if (!isset($this->piVars['mode'])) {
				$this->piVars['mode'] = $this->conf['list.']['defaultMode'];
			}
			
			// SORT BY
			$this->piVars['sort'] = ($this->piVars['mode'] == 0) ? 'crdate:1' : 'views:1';
			
			// Unsetting mode variable for searching
			if (isset($this->piVars['sword'])) {
				unset($this->piVars['mode']);
			}
			
			// Set general internal variables
			$this->api->fe_setInternalVars($this->conf['list.']['maxRecords'],$this->conf['list.']['maxPages'],$this->searchFields,$this->orderByFields);
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
			$sword = $this->piVars['sword'];
			
			// Temporary unset sword to avoid problems with the category menu
			unset($this->piVars['sword']);
			
			// Add JavaScript Code
			$this->api->fe_buildSwapClassesJSCode('open','closed');
			
			// Storage
			$catMenu = array();
			$this->totalAdsNum = array();
			
			// Add show all link
			$catMenu[] = $this->api->fe_makeStyledContent('div','catMenu-showAll',$this->pi_linkTP($this->pi_getLL('pi_catmenu_showall')));
			
			// Build menu
			$catMenu[] = $this->api->fe_makeStyledContent('div','catMenu',$this->getCategories(0,0));
			
			// Restore sword
			$this->piVars['sword'] = $sword;
			
			// Return menu
			return implode(chr(10),$catMenu);
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
				
				// Check for a category image
				if (!empty($row['icon'])) {
					
					// Add category image
					$catMenu[] = $this->api->fe_createImageObjects($row['icon'],$this->conf['catImage.'],$this->uploadFolder);
				}
				
				// Try to get subcategories
				$childs = $this->getCategories($row['uid'],$level + 1);
				
				// Link to use for the category
				if ($childs) {
					
					// Display subcategories
					$link = $this->api->fe_makeSwapClassesJSLink($listId,$row['title'],0,0,array('title'=>$row['description']));
					
					// Add number of ads for parent category
					$this->totalAdsNum[$parent] += $this->totalAdsNum[$row['uid']];
					
				} else {
					
					// Display this category
					$link = $this->api->fe_linkTP_unsetPIvars($row['title'],array('showCat'=>$row['uid']),array('sword','pointer','showUid','action'));
					
					// Get number of ads
					$resNum = $this->pi_exec_query($this->extTables['ads'],1,'AND category=' . $row['uid']);
					list($localAdsNum) = $GLOBALS['TYPO3_DB']->sql_fetch_row($resNum);
					
					// Add number of ads for parent category
					$this->totalAdsNum[$parent] += $localAdsNum;
				}
				
				// Set ads number
				$adsNum = ($childs) ? $this->totalAdsNum[$row['uid']] : $localAdsNum;
				
				// Category item w/ wrap
				$catItem = $this->cObj->wrap($link . $this->api->fe_makeStyledContent('span','adsNum',' (' . $adsNum . ')'),$this->conf['catMenu.']['itemWrap']);
				
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
		 * SECTION 3 - LIST
		 *
		 * Construction of the list view
		 ***************************************************************/
		
		/**
		 *
		 */
		function buildList() {
			
			// No active page
			if (!isset($this->piVars['pointer'])) {
				$this->piVars['pointer'] = 0;
			}
			
			// Additionnal MySQL WHERE clause
			$whereClause = ($this->piVars['showCat']) ? ' AND category=' . $this->piVars['showCat'] : '';
			
			// Get records number
			$res = $this->pi_exec_query($this->extTables['ads'],1,$whereClause);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			// Make listing query - Pass query to MySQL database
			$res = $this->pi_exec_query($this->extTables['ads'],0,$whereClause);
			$this->internal['currentTable'] = $this->extTables['ads'];
			
			// Template markers
			$templateMarkers = array();
			
			// Add searchbox
			$templateMarkers['###SEARCH###'] = $this->pi_list_searchBox();
			
			// Add menu
			$templateMarkers['###MODMENU###'] = $this->makeListMenu();
			
			// Add list table
			$templateMarkers['###LIST_TABLE###'] =$this->makeListTable($res);
			
			// Add browse box
			$templateMarkers['###BROWSE###'] = $this->pi_list_browseresults();
			
			// Wrap all in a CSS element
			$content = $this->api->fe_makeStyledContent('div','list',$this->api->fe_renderTemplate($templateMarkers,'###LIST###'));
			
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
			
			// Store sword
			$sword = $this->piVars['sword'];
			
			// Temporary unset sword to avoid problems with the category menu
			unset($this->piVars['sword']);
			
			// Menu items
			$listMenu = array(
				'0' => $this->pi_getLL('pi_list_modmenu_0','Mode 0'),
				'1' => $this->pi_getLL('pi_list_modmenu_1','Mode 1'),
			);
			
			// Create menu
			$modMenu = $this->pi_list_modeSelector($listMenu);
			
			// Restore sword
			$this->piVars['sword'] = $sword;
			
			// Return menu
			return $modMenu;
		}
		
		/**
		 * 
		 */
		function makeListTable($res) {
			
			// Template markers
			$templateMarkers = array();
			
			// Items storage
			$items = array();
			
			// Get items to list
			while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Build item
				$items[] = $this->makeListItem();
			}
			
			// Add rows
			$templateMarkers['###LIST_ITEM###'] = implode(chr(10),$items);
			
			// Wrap all in a CSS element
			$content = $this->api->fe_makeStyledContent('div','listTable',$this->api->fe_renderTemplate($templateMarkers,'###LIST_TABLE###'));
			
			// Return content
			return $content;
		}
		
		/**
		 * 
		 */
		function makeListItem() {
			
			// Template markers
			$templateMarkers = array();
			
			// Get an array with the fields to render
			$fieldList = explode(',',$this->conf['list.']['displayFields']);
			
			// Process each field
			foreach($fieldList as $field) {
				
				// Markers substitution
				$templateMarkers['###' . strtoupper($field) . '###'] = $this->getFieldContent($field,'list');
			}
			
			// Add edit panel
			$templateMarkers['###EDITPANEL###'] = $this->pi_getEditPanel();
			
			// Wrap all in a CSS element
			$content = $this->api->fe_renderTemplate($templateMarkers,'###LIST_ITEM###');
			
			// Return content
			return $content;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 4 - TOOLBAR
		 *
		 * Construction of the toolbar
		 ***************************************************************/
		
		/**
		 * 
		 */
		function buildToolbar() {
			
			// Template markers
			$templateMarkers = array();
			
			// CSS class names
			$classNames = array(
				'connection' => ($this->piVars['action'] == 'connect') ? 'connection-act' : 'connection',
				'profile_register' => ($this->piVars['action'] == 'register' || array_key_exists('fe_users',$this->feAdminInput)) ? 'profile_register-act' : 'profile_register',
			);
			
			// Check for a user conenction
			if ($GLOBALS['TSFE']->loginUser) {
				
				// User is logged
				$templateMarkers['###PROFILE_REGISTER###'] = $this->api->fe_makeStyledContent('div',$classNames['profile_register'],$this->pi_linkTP_keepPiVars($this->pi_getLL('pi_toolbar_profile'),array('action'=>'edituser'),0,1));
			
				
			} else {
				
				// User is not logged
				$templateMarkers['###PROFILE_REGISTER###'] = $this->api->fe_makeStyledContent('div',$classNames['profile_register'],$this->pi_linkTP_keepPiVars($this->pi_getLL('pi_toolbar_register'),array('action'=>'register'),0,1));
			
			}
			
			// Common markers
			$templateMarkers['###NEWAD###'] = $this->pi_getLL('pi_toolbar_newad');
			$templateMarkers['###MYADS###'] = $this->pi_getLL('pi_toolbar_myads');
			$templateMarkers['###CONNECTION###'] = $this->api->fe_makeStyledContent('div',$classNames['connection'],$this->pi_linkTP_keepPiVars($this->pi_getLL('pi_toolbar_connection'),array('action'=>'connect'),0,1));
				
			
			// Wrap all in a CSS element
			$content = $this->api->fe_makeStyledContent('div','toolbar',$this->api->fe_renderTemplate($templateMarkers,'###TOOLBAR###'));
			
			// Return content
			return $content;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 5 - LOGIN
		 *
		 * Construction of the user login form
		 ***************************************************************/
		
		/**
		 * 
		 */
		function login() {
			
			// Template markers
			$templateMarkers = array();
			
			// Override template markers
			$templateMarkers['###HEADER###'] = $this->pi_getLL('pi_login_header');
			$templateMarkers['###LOGINBOX###'] = $this->api->fe_buildLoginBox($this->conf['users.']['storagePid']);
			
			// Wrap all in a CSS element
			$content = $this->api->fe_makeStyledContent('div','login',$this->api->fe_renderTemplate($templateMarkers,'###LOGIN###'));
			
			return $content;
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 6 - REGISTER
		 *
		 * Construction of the user registration form
		 ***************************************************************/
		
		/**
		 * 
		 */
		function registerUser() {
			
			// Create feAdmin configuration
			$this->conf = $this->api->fe_initFeAdmin($this->conf,$this->extTables['users'],$this->conf['users.']['storagePid'],$this->conf['users.'],1,1,0,0,1,1,1);
			
			// Template markers
			$templateMarkers = array();
			
			// Add header
			$templateMarkers['###HEADER###'] = $this->pi_getLL('pi_register_header');
			
			// Add success header
			$templateMarkers['###HEADER_SAVED###'] = $this->pi_getLL('pi_register_success_header');
			
			// Add success message
			$templateMarkers['###MESSAGE_SAVED###'] = $this->pi_getLL('pi_register_success_text');
			
			// Add form enctype
			$templateMarkers['###CREATE_FORM_ENCTYPE###'] = $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'];
			
			// Get an array with all the fields to create
			$registerFields = explode(',',$this->conf['users.']['create.']['fields']);
			
			// Process each field to display
			foreach($registerFields as $formElement) {
				
				// Check for special fields
				switch($formElement) {
					
					// Address - Textarea
					case 'address':
						$formItem = $this->api->fe_createTextArea($formElement,$this->conf['fe_adminLib.'],'create',array('cols'=>$this->conf['forms.']['textAreaCols'],'rows'=>$this->conf['forms.']['textAreaRows']),0,1,'pi_register_');
					break;
					
					// Password - Password input
					case 'password':
						$formItem = $this->api->fe_createInput('password',$formElement,$this->conf['fe_adminLib.'],'create',1,array('size'=>$this->conf['forms.']['inputSize']),0,0,0,'pi_register_');
					break;
					
					// Default - Text input
					default:
						$formItem = $this->api->fe_createInput('text',$formElement,$this->conf['fe_adminLib.'],'create',1,array('size'=>$this->conf['forms.']['inputSize']),0,0,1,'pi_register_');
					break;
				}
				
				// Set template marker
				$templateMarkers['###' . strtoupper($formElement) . '###'] = $formItem;
			}
			
			// Add submit
			$templateMarkers['###SUBMIT###'] = $this->api->fe_createInput('submit',$formElement,$this->conf['fe_adminLib.'],'create',1,array(),$this->pi_getLL('pi_register_submit'),0);
			
			// Set default group
			$this->conf['fe_adminLib.']['create.']['overrideValues.'] = array(
				'usergroup' => $this->conf['users.']['defaultGroup'],
			);
			
			// Add usergroup to available fields
			$this->conf['fe_adminLib.']['create.']['fields'] .= ',usergroup';
			
			// Render template in fe_adminLib configuration array
			$this->conf['fe_adminLib.']['templateContent'] = $this->api->fe_renderTemplate($templateMarkers,'###REGISTER###');
			
			// Create fe_adminLib cObject
			$content = $this->cObj->cObjGetSingle($this->conf['fe_adminLib'],$this->conf['fe_adminLib.']);
			
			// Return content wrapped in a CSS element
			return $this->api->fe_makeStyledContent('div','register',$content);
			
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
			$this->internal['currentTable'] = $this->extTables['ads'];
			$this->internal['currentRow'] = $this->pi_getRecord($this->extTables['ads'],$this->piVars['showUid']);
			
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
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['ads'],'uid=' . $this->internal['currentRow']['uid'],array('views'=>$this->internal['currentRow']['views'] + 1));
			
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
		 * 
		 */
		function getCurrencySymbol($uid,$content) {
			
			// Check for SQL cache
			if (empty($this->sqlCache['static_currencies'])) {
				
				// Get database
				$this->sqlCache['static_currencies'] = $this->pi_getCategoryTableContents('static_currencies',0);
			}
			
			// Return price with currency
			return $this->sqlCache['static_currencies'][$uid]['cu_symbol_left'] . ' ' . $content . ' ' . $this->sqlCache['static_currencies'][$uid]['cu_symbol_right'];
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/classifieds_macmade/pi1/class.tx_classifiedsmacmade_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/classifieds_macmade/pi1/class.tx_classifiedsmacmade_pi1.php']);
	}
?>
