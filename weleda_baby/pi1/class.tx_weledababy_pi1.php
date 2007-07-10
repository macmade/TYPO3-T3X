<?php
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2005 Jean-David Gadina (info@macmade.net)
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
	 * Plugin 'User registration' for the 'weleda_baby' extension.
	 *
	 * @author	Jean-David Gadina <info@macmade.net>
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
	
	class tx_weledababy_pi1 extends tslib_pibase {
		// Same as class name
		var $prefixId = 'tx_weledababy_pi1';
		
		// Path to this script relative to the extension dir.
		var $scriptRelPath = 'pi1/class.tx_weledababy_pi1.php';
		
		// The extension key
		var $extKey = 'weleda_baby';
		
		// Check plugin hash
		var $pi_checkCHash = FALSE;
		
		// Upload directory
		var $uploadDir = 'uploads/tx_weledababy/';
		
		// Version of the Developer API required
		var $apimacmade_version = 2.3;
		
		// Internal variables
		var $searchFields = 'username,name,city,country';
		var $orderByFields = 'username';
		
		// Database tables
		var $extTables = array(
			'users' => 'fe_users',
			'babies' => 'tx_weledababy_babies',
			'profiles' => 'tx_weledababy_profiles',
			'friends' => 'tx_weledababy_profiles_friends_mm',
			'pictures' => 'tx_weledababy_pictures',
			'articles' => 'tx_weledababy_articles',
		);
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_femp3player_pi1", and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 */
		function main($content,$conf) {
			
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Set default plugin variables
			$this->pi_setPiVarDefaults();
			
			// Load locallang labels
			$this->pi_loadLL();
			
			// Init flexform configuration of the plugin
			$this->pi_initPIflexForm();
			
			// Store flexform informations
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
			
			// Set final configuration (TS or FF)
			$this->setConfig();
			
			// MySQL query init
			$this->setInternalVars();
			
			// Get FE user
			$this->getFeUser();
			
			// Add or remove friend
			$this->processFriends();
			
			// Check template file (TS or Flex)
			$templateFile = ($this->pi_getFFvalue($this->piFlexForm,'template_file','sTMPL') == '') ? $this->conf['templateFile'] : $this->uploadDir . $this->conf['templateFile'];
			
			// Template load and init
			$this->api->fe_initTemplate($templateFile);
			
			// Content
			$content = ($this->piVars['showUid']) ? $this->showUser() : $this->buildList();
			
			// Return content
			return $this->pi_wrapInBaseClass($content);
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
				'usergroup' => 'sDEF:usergroup',
				'templateFile' => 'sTMPL:template_file',
				'list.' => array(
					'showFields' => 'sLIST:list_showfields',
					'maxRecords' => 'sLIST:list_maxrecords',
					'maxPages' => 'sLIST:list_maxpages',
				),
				'single.' => array(
					'showFields' => 'sSINGLE:single_showfields',
					'showArticles' => 'sSINGLE:lastarticles',
				),
				'articles.' => array(
					'list.' => array(
						'maxRecords' => 'sARTICLES:articles_maxrecords',
						'maxPages' => 'sARTICLES:articles_maxpages',
					),
				),
				'gallery.' => array(
					'list.' => array(
						'maxRecords' => 'sGALLERY:gallery_maxrecords',
						'maxPages' => 'sGALLERY:gallery_maxpages',
					),
				),
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'Weleda Baby PI1: configuration array');
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
		function getFeUser() {
			
			// Check for a login
			if ($GLOBALS['TSFE']->loginUser) {
				
				// Check group
				if ($GLOBALS['TSFE']->fe_user->user['usergroup'] == $this->conf['usergroup']) {
					
					// Store FE user
					$this->user = $GLOBALS['TSFE']->fe_user->user;
					
					// Get profile
					$this->profile = $this->getProfile($this->user['uid']);
					
					// Get friends
					$this->friends = $this->getFriends($this->profile['uid']);
				}
			}
		}
		
		/**
		 * 
		 */
		function getProfile($uid) {
			
			// Select profile
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['profiles'],'deleted=0 AND feuser=' . $uid);
			
			// Check ressource
			if ($res) {
				
				// Get profile
				return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			}
		}
		
		/**
		 * 
		 */
		function getFriends($uid) {
			
			// Select friends
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['friends'],'uid_local=' . $uid . ' AND NOT refused');
			
			// Storage
			$friends = array();
			
			// Check ressource
			if ($res) {
				
				// Process friends
				while($friend = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Add friend
					$friends[$friend['uid_foreign']] = $friend['confirmed'];
				}
			}
			
			// Return friends
			return $friends;
		}
		
		/**
		 * 
		 */
		function processFriends() {
			
			// Check login
			if (isset($this->user)) {
					
				// Profile ID
				$id = $this->profile['uid'];
				
				// Check processing
				if (array_key_exists('remove',$this->piVars) && array_key_exists($this->piVars['remove'],$this->friends)) {
					
					// Get friend profile
					$friendProfile = $this->getProfile($this->piVars['remove']);
					
					// Delete friend
					$GLOBALS['TYPO3_DB']->exec_DELETEquery($this->extTables['friends'],'uid_local=' . $id . ' AND uid_foreign=' . $this->piVars['remove']);
					$GLOBALS['TYPO3_DB']->exec_DELETEquery($this->extTables['friends'],'uid_local=' . $friendProfile['uid'] . ' AND uid_foreign=' . $this->user['uid']);
					
					// Update friends
					unset($this->friends[$this->piVars['remove']]);
					
					// Unset variable
					unset($this->piVars['remove']);
					
					// Clear members page cache
					$this->clearPageCache($GLOBALS['TSFE']->id);
					
				} else if (array_key_exists('add',$this->piVars) && !array_key_exists($this->piVars['add'],$this->friends)) {
					
					// Fields to insert
					$insertFields = array(
						'uid_local' => $id,
						'uid_foreign' => $this->piVars['add'],
					);
					
					// Add friend
					$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->extTables['friends'],$insertFields);
					
					// Update friends
					$this->friends[$this->piVars['add']] = 0;
					
					// Unset variable
					unset($this->piVars['add']);
					
					// Clear members page cache
					$this->clearPageCache($GLOBALS['TSFE']->id);
				}
			}
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
			
			// Regular expression for navig
			if (array_key_exists('letter',$this->piVars)) {
				
				// Letter to select
				$letter = $this->piVars['letter'];
				
				// Check letter
				if ($letter == '0-9') {
					
					// Regular expression
					$regExp = '^[' . $letter . ']';
					
				} else {
					
					// Regular expression
					$regExp = '^' . $letter . '';
				}
				
				// Set WHERE clause
				$whereClause = 'AND username REGEXP \'' . $regExp . '\'';
			}
			
			// Get records number
			$res = $this->pi_exec_query($this->extTables['users'],1,$whereClause);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			// Make listing query - Pass query to MySQL database
			$res = $this->pi_exec_query($this->extTables['users'],0,$whereClause);
			$this->internal['currentTable'] = $this->extTables['users'];
			
			// Template markers
			$templateMarkers = array();
			
			// Replace markers
			$templateMarkers['###SEARCH###'] = $this->api->fe_makeStyledContent('div','search-header',$this->pi_getLL('headers.search.members')) . $this->pi_list_searchBox();
			$templateMarkers['###NAVIG###'] = $this->buildNavig();
			$templateMarkers['###BACKLIST###'] = (array_key_exists('letter',$this->piVars) || array_key_exists('sword',$this->piVars)) ? $this->api->fe_makeStyledContent('div','back-list',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('backlist'),array(),array('sword','letter'))) : '';
			$templateMarkers['###MEMBERS###'] = ($this->internal['res_count']) ? $this->makeList($res) : '';
			$templateMarkers['###BROWSE###'] = $this->pi_list_browseresults();
			
			// Wrap all in a CSS element
			return $this->api->fe_renderTemplate($templateMarkers,'###LIST###');
		}
		
		/**
		 * 
		 */
		function buildNavig() {
			
			// Letters
			$letters = array('0-9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
			
			// Storage
			$htmlCode = array();
			
			// Process each letter
			foreach($letters as $value) {
				
				// CSS class
				$class = (array_key_exists('letter',$this->piVars) && $this->piVars['letter'] == $value) ? 'letter-selected' : 'letter';
				
				// Letter
				$letter = (array_key_exists('letter',$this->piVars) && $this->piVars['letter'] == $value) ? $value : $this->pi_linkTP_keepPIvars($value,array('letter'=>$value));
				
				// Add letter
				$htmlCode[] = $this->api->fe_makeStyledContent('span',$class,$letter);
			}
			
			// Return navig
			return $this->api->fe_makeStyledContent('div','navig',implode(chr(10),$htmlCode));
		}
		
		/**
		 * 
		 */
		function makeList($res) {
			
			// Items storage
			$htmlCode = array();
			
			// Fields to display
			$showFields = explode(',',$this->conf['list.']['showFields']);
			
			// Start table
			$htmlCode[] = $this->api->fe_makeStyledContent('table','list-table',false,1,0,1);
			
			// Start headers row
			$htmlCode[] = '<tr>';
			
			// Picture header if applicable
			if (in_array('image',$showFields)) {
				
				// Header
				$htmlCode[] = '<th>' . $this->pi_getLL('headers.image') . '</th>';
			}
			
			// Fixed headers
			$htmlCode[] = '<th>' . $this->pi_getLL('headers.infos') . '</th>';
			$htmlCode[] = '<th>' . $this->pi_getLL('headers.options') . '</th>';
			
			// Start headers row
			$htmlCode[] = '</tr>';
			// Counter
			$count = 0;
			
			// Get items to list
			while($this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Class name
				$class= ($count == 0) ?  'row' : 'row-alt';
				
				// Logged user
				if (isset($this->user) && $this->user['uid'] == $this->internal['currentRow']['uid']) {
					
					// Set class
					$class = 'login';
				}
				
				// Friend
				if (isset($this->friends) && array_key_exists($this->internal['currentRow']['uid'],$this->friends)) {
					
					// Set class
					$class = 'friend';
				}
				
				// User storage
				$user = array();
				$options = array();
				
				// Start row
				$user[] = $this->api->fe_makeStyledContent('tr',$class,false,1,0,1);
				
				// Picture column if applicable
				if (in_array('image',$showFields)) {
					
					// Picture
					$user[] = $this->api->fe_makeStyledContent('td','image',$this->getFieldContent('image','list'));
				}
				
				// Start infos
				$user[] = $this->api->fe_makeStyledContent('td','infos',false,1,0,1);
				
				// Process fields
				foreach($showFields as $field) {
					
					// Do not display picture in this field
					if ($field == 'image') {
						
						// Next field
						continue;
					}
					
					// Get field content
					$user[] = $this->api->fe_makeStyledContent('div',$field,$this->getFieldContent($field,'list'));
				}
				
				// Get number of babies
				$babies_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['babies'],'deleted=0 AND feuser=' . $this->internal['currentRow']['uid']);
				
				// Number of babies
				$babies_num = $GLOBALS['TYPO3_DB']->sql_num_rows($babies_res);
				
				// Check for babies
				if ($babies_num > 0) {
					
					// One or many
					$babies = ($babies_num > 1) ? $this->pi_getLL('babies') : $this->pi_getLL('baby');
					
					// Babies
					$user[] = $this->api->fe_makeStyledContent('div','babies',sprintf($this->pi_getLL('mother'),$babies_num,$babies));
				}
				
				// End infos
				$user[] = '</td>';
				
				// Profile string
				$profile = ($class == 'login') ? sprintf($this->pi_getLL('showprofile'),$this->pi_getLL('my')) : sprintf($this->pi_getLL('showprofile'),$this->pi_getLL('her'));
				
				// Show profile
				$options[] = $this->api->fe_makeStyledContent('div','showprofile',$this->pi_list_linkSingle($profile,$this->internal['currentRow']['uid'],1));
				
				// Check if user is logged
				if (isset($this->user) && $this->user['uid'] != $this->internal['currentRow']['uid']) {
					
					// Friend string
					if ($class == 'friend' && $this->friends[$this->internal['currentRow']['uid']]) {
						
						
						// Remove friend link
						$friend = $this->pi_linkTP_keepPIvars($this->pi_getLL('friend.remove'),array('remove'=>$this->internal['currentRow']['uid']));
						
					} else if ($class == 'friend') {
						
						// Waiting for confirmation
						$friend = $this->pi_getLL('friend.confirm');
						
					} else {
						
						// Add friend link
						$friend = $this->pi_linkTP_keepPIvars($this->pi_getLL('friend.add'),array('add'=>$this->internal['currentRow']['uid']));
					}
					
					// Friend options
					$options[] = $this->api->fe_makeStyledContent('div','friend',$friend);
				}
				
				// Options
				$user[] = $this->api->fe_makeStyledContent('td','options',implode(chr(10),$options));
				
				// End row
				$user[] = '</tr>';
				
				// Build item
				$htmlCode[] = $this->api->fe_makeStyledContent('div','user',implode(chr(10),$user));
				
				// Set counter
				$count = ($count == 0) ? 1 : 0;
			}
			
			// Start table
			$htmlCode[] = '</table>';
			
			// Return list
			return $this->api->fe_makeStyledContent('div','list',implode(chr(10),$htmlCode));
		}
		
		/**
		 * 
		 */
		function showUser() {
			
			// Get user
			$user = $this->pi_getRecord($this->extTables['users'],$this->piVars['showUid']);
			
			// Check plugin variables
			if (array_key_exists('showarticles',$this->piVars) || array_key_exists('article',$this->piVars)) {
				
				// Show all articles
				return $this->showArticles($user);
				
			} else if (array_key_exists('showgallery',$this->piVars)) {
				
				// Show gallery
				return $this->showGallery($user);
				
			} else {
				
				// Check for a search word
				if (array_key_exists('sword',$this->piVars)) {
					
					// Unset search word
					unset($this->piVars['sword']);
				}
				
				// Get profile
				$profile = $this->getProfile($user['uid']);
				
				// Get friends
				$friends = $this->getFriends($profile['uid']);
				
				// Template markers
				$templateMarkers = array();
				
				// Check for a description
				$description = (!empty($profile['description'])) ? $this->api->fe_makeStyledContent('div','description-text',nl2br(htmlspecialchars($profile['description']))) : '';
				
				// Options
				$options = array();
				
				// Check if user is logged
				if (isset($this->user) && $this->user['uid'] != $user['uid']) {
					
					// Mail option
					$options[] = $this->api->fe_makeStyledContent('div','mail',$this->cObj->typoLink(sprintf($this->pi_getLL('sendmail'),$user['username']),array('parameter'=>$user['email'])));
					
					// Friend string
					if (array_key_exists($user['uid'],$this->friends) && $this->friends[$user['uid']]) {
						
						// Remove friend link
						$friend = $this->pi_linkTP_keepPIvars($this->pi_getLL('friend.remove'),array('remove'=>$user['uid']));
						
					} else if (array_key_exists($user['uid'],$this->friends)) {
						
						// Waiting for confirmation
						$friend = $this->pi_getLL('friend.confirm');
						
					} else {
						
						// Add friend link
						$friend = $this->pi_linkTP_keepPIvars($this->pi_getLL('friend.add'),array('add'=>$user['uid']));
					}
					
					// Friend options
					$options[] = $this->api->fe_makeStyledContent('div','friend',$friend);
				}
				
				// Replace markers
				$templateMarkers['###BABIES###'] = $this->displayBabies($user);
				$templateMarkers['###OPTIONS###'] = (count($options)) ? $this->api->fe_makeStyledContent('div','options',implode(chr(10),$options)) : '';
				$templateMarkers['###SHOWGALLERY###'] = $this->galleryLink($user);
				$templateMarkers['###ARTICLES###'] = $this->displayArticles($user);
				$templateMarkers['###FRIENDS###'] = $this->displayFriends($friends,$user);
				$templateMarkers['###DESCRIPTION###'] = $this->api->fe_makeStyledContent('div','description',$description);
				$templateMarkers['###INFOS###'] = $this->showUserInfos($user);
				$templateMarkers['###BACK###'] = $this->backLink(array('showUid'));
				
				// Wrap all in a CSS element
				return $this->api->fe_renderTemplate($templateMarkers,'###PROFILE###');
			}
		}
		
		/**
		 * 
		 */
		function galleryLink($user) {
			
			// Select pictures from member
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['pictures'],'deleted=0 AND feuser=' . $user['uid']);
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Text
				$text = (isset($this->user) && $user['uid'] == $this->user['uid']) ? $this->pi_getLL('showgallery.connected') : sprintf($this->pi_getLL('showgallery'),$user['username']);
				
				// Link
				$link = $this->pi_linkTP_keepPIvars($text,array('showgallery'=>1),1);
				
				// Return link
				return $this->api->fe_makeStyledContent('div','gallery-link',$link);
			}
		}
		
		/**
		 * 
		 */
		function showUserInfos($user) {
			
			// Storage
			$htmlCode = array();
			
			// Fields to display
			$showFields = explode(',',$this->conf['single.']['showFields']);
			
			// Process fields
			foreach($showFields as $field) {
				
				// Get field content
				$htmlCode[] = $this->api->fe_makeStyledContent('div',$field,$this->getFieldContent($field,'single',$user));
			}
			
			// Return content
			return $this->api->fe_makeStyledContent('div','infos',implode(chr(10),$htmlCode));
		}
		
		/**
		 * 
		 */
		function displayFriends($uidArray,$user) {
			
			// Check input array
			if (is_array($uidArray) && count($uidArray)) {
				
				// Storage
				$htmlCode = array();
				$friends = array();
				
				// Header
				$htmlCode[] = (isset($this->user) && $user['uid'] == $this->user['uid']) ? $this->api->fe_makeStyledContent('div','friends-header',sprintf($this->pi_getLL('friends.list.connected'),$user['username'])) : $this->api->fe_makeStyledContent('div','friends-header',sprintf($this->pi_getLL('friends.list'),$user['username']));
				
				// Process uids
				foreach($uidArray as $key=>$value) {
					
					// Check if friend is confirmed
					if ($value == 1) {
						
						// Get record
						$row = $this->pi_getRecord($this->extTables['users'],$key);
						
						// Store record
						$friends[$row['username']] = $row;
					}
				}
				
				// Check for confirmed friends
				if (count($friends)) {
					
					// Sort array
					ksort($friends);
					
					// Process friends
					foreach($friends as $friend) {
						
						// Display
						$display = $this->api->fe_createImageObjects($friend['image'],$this->conf['single.']['friendsPicture.'],'uploads/pics/') . $this->pi_list_linkSingle($friend['username'],$friend['uid'],1);
						
						// Display username
						$htmlCode[] = $this->api->fe_makeStyledContent('div','friend',$display);
					}
					
					// Return list
					return $this->api->fe_makeStyledContent('div','friends-list',implode(chr(10),$htmlCode));
				}
			}
		}
		
		/**
		 * 
		 */
		function displayArticles($user) {
			
			// Select articles from member
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['articles'],'deleted=0 AND feuser=' . $user['uid'],false,'crdate DESC',$this->conf['single.']['showArticles']);
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Storage
				$htmlCode = array();
				
				// Header
				$htmlCode[] = (isset($this->user) && $user['uid'] == $this->user['uid']) ? $this->api->fe_makeStyledContent('div','friends-header',$this->pi_getLL('messages.list.connected')) : $this->api->fe_makeStyledContent('div','friends-header',sprintf($this->pi_getLL('messages.list'),$user['username']));
				
				// Get articles
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Date
					$date = $this->api->fe_makeStyledContent('div','date',strftime($this->conf['dateFormat'],$row['crdate']));
					
					// Title
					$title = $this->api->fe_makeStyledContent('div','title',$this->pi_linkTP_keepPIvars($row['title'],array('article'=>$row['uid']),1));
					
					// Resume
					$resume = $this->api->fe_makeStyledContent('div','resume',$this->api->div_crop($row['article'],$this->conf['single.']['cropArticles']));
					
					// Display article
					$htmlCode[] = $this->api->fe_makeStyledContent('div','article',$date . $title . $resume);
				}
				
				// All articles text
				$text = (isset($this->user) && $user['uid'] == $this->user['uid']) ? $this->pi_getLL('showarticles.connected') : sprintf($this->pi_getLL('showarticles'),$user['username']);
				
				// All articles link
				$link = $this->pi_linkTP_keepPIvars($text,array('showarticles'=>1),1);
				
				// Display link
				$htmlCode[] = $this->api->fe_makeStyledContent('div','articles-link',$link);
				
				// Return list
				return $this->api->fe_makeStyledContent('div','articles-list',implode(chr(10),$htmlCode));
			}
		}
		
		/**
		 * 
		 */
		function displayBabies($user) {
			
			// Get babies
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['babies'],'deleted=0 AND feuser=' . $user['uid']);
			
			// Check ressource
			if ($res) {
				
				// Check for babies
				if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
					
					// Storage
					$htmlCode = array();
					
					// Header
					$htmlCode[] = (isset($this->user) && $user['uid'] == $this->user['uid']) ? $this->api->fe_makeStyledContent('div','babies-header',$this->pi_getLL('babies.list.connected')) : $this->api->fe_makeStyledContent('div','babies-header',$this->pi_getLL('babies.list'));
					
					// Process babies
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						
						// Storage
						$baby = array();
						
						// Check for a picture
						if (!empty($row['picture'])) {
							
							// Add picture
							$baby[] = $this->api->fe_makeStyledContent('div','picture',$this->api->fe_createImageObjects($row['picture'],$this->conf['single.']['babiesPicture.'],$this->uploadDir));
						}
						
						// Age
						$age = $this->getAge($row['birth']);
						
						// Age string
						$ageString = ($age > 0) ? $age . ' ' . $this->pi_getLL('years') : $age . ' ' . $this->pi_getLL('year');
						
						// Start DIV
						$baby[] = $this->api->fe_makeStyledContent('div','infos',false,1,0,1);
						
						// Name
						$baby[] = $this->api->fe_makeStyledContent('div','name',$row['name']);
						
						// Sex
						$baby[] = $this->api->fe_makeStyledContent('div','sex',$this->pi_getLL('sex.' . $row['sex']));
						
						// Birth
						$baby[] = $this->api->fe_makeStyledContent('div','birth',$this->pi_getLL('birth') . ':<br />' . strftime($this->conf['dateFormat'],$row['birth']));
						
						// Age
						$baby[] = $this->api->fe_makeStyledContent('div','age',$this->pi_getLL('age') . ': ' . $ageString);
						
						// End div
						$baby[] = '</div>';
						
						// Baby class
						$class = ($row['sex']) ? 'baby-boy' : 'baby-girl';
						
						// Add baby
						$htmlCode[] = $this->api->fe_makeStyledContent('div',$class,implode(chr(10),$baby));
					}
					
					// Return list
					return $this->api->fe_makeStyledContent('div','babies-list',implode(chr(10),$htmlCode));
				}
			}
		}
		
		/**
		 * 
		 */
		function displayArticle($user) {
			
			// Get article
			$article = $this->pi_getRecord($this->extTables['articles'],$this->piVars['article']);
			
			// Check article
			if (is_array($article)) {
				
				// Template markers
				$templateMarkers = array();
				
				// Replace markers
				$templateMarkers['###TITLE###'] = $this->api->fe_makeStyledContent('div','article-title',$article['title']);
				$templateMarkers['###DATE###'] = $this->api->fe_makeStyledContent('div','article-date',$this->pi_getLL('date') . ' ' . strftime($this->conf['dateFormat'],$article['crdate']));
				$templateMarkers['###TEXT###'] = $this->api->fe_makeStyledContent('div','article-text',nl2br(htmlspecialchars($article['article'])));
				$templateMarkers['###BACK###'] = $this->backLink(array('showarticles','article'));
				
				// Wrap all in a CSS element
				return $this->api->fe_makeStyledContent('div','article',$this->api->fe_renderTemplate($templateMarkers,'###ARTICLES_SINGLE###'));
			}
		}
		
		/**
		 * 
		 */
		function showArticles($user) {
			
			// Internal variables
			$searchFields = 'title,article';
			$orderByFields = 'crdate';
			
			// SORT BY
			$this->piVars['sort'] = $this->conf['articles.']['list.']['sortBy'] . ':1';
			
			// Set general internal variables
			$this->api->fe_setInternalVars($this->conf['articles.']['list.']['maxRecords'],$this->conf['articles.']['list.']['maxPages'],$searchFields,$orderByFields);
			
			// No active page
			if (!isset($this->piVars['pointer'])) {
				$this->piVars['pointer'] = 0;
			}
			
			// Additionnal MySQL WHERE clause for filters
			$whereClause = ' AND feuser=' . $user['uid'];
			
			// Get records number
			$res = $this->pi_exec_query($this->extTables['articles'],1,$whereClause);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			// Make listing query - Pass query to MySQL database
			$res = $this->pi_exec_query($this->extTables['articles'],0,$whereClause);
			$this->internal['currentTable'] = $this->extTables['articles'];
			
			// Check ressource
			if ($res) {
				
				// Storage
				$htmlCode = array();
				
				// Get articles
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Title
					$title = $this->api->fe_makeStyledContent('div','title',$this->pi_linkTP_keepPIvars($row['title'],array('article'=>$row['uid']),1));
					
					// Date
					$date = $this->api->fe_makeStyledContent('div','date',strftime($this->conf['dateFormat'],$row['crdate']));
					
					// Resume
					$resume = $this->api->fe_makeStyledContent('div','resume',$this->api->div_crop($row['article'],$this->conf['articles.']['list.']['crop']));
					
					// Read link
					$readLink = $this->api->fe_makeStyledContent('div','read-link',$this->pi_linkTP_keepPIvars($this->pi_getLL('articles.next'),array('article'=>$row['uid']),1));
					
					// Display article
					$htmlCode[] = $this->api->fe_makeStyledContent('div','article',$date . $title . $resume . $readLink);
				}
				
				// Template markers
				$templateMarkers = array();
				
				// Section header
				$header = (isset($this->user) && $user['uid'] == $this->user['uid']) ? $this->pi_getLL('headers.messages.connected') : sprintf($this->pi_getLL('headers.messages'),$user['username']);
				
				// Replace markers
				$templateMarkers['###SINGLE###'] = (array_key_exists('article',$this->piVars)) ? $this->displayArticle($user) : '';
				$templateMarkers['###HEADER###'] = $this->api->fe_makeStyledContent('div','header',$header);
				$templateMarkers['###SEARCH###'] = $this->api->fe_makeStyledContent('div','search-header',$this->pi_getLL('headers.search.articles')) . $this->pi_list_searchBox();
				$templateMarkers['###LIST###'] = $this->api->fe_makeStyledContent('div','list',implode(chr(10),$htmlCode));
				$templateMarkers['###BROWSE###'] = $this->pi_list_browseresults();
				$templateMarkers['###BACK###'] = $this->backLink(array('showarticles','article'));
				$templateMarkers['###BACKLIST###'] = (array_key_exists('sword',$this->piVars)) ? $this->api->fe_makeStyledContent('div','back-list',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('backlist'),array(),array('sword'))) : '';
				
				// Wrap all in a CSS element
				return $this->api->fe_makeStyledContent('div','articles-list',$this->api->fe_renderTemplate($templateMarkers,'###ARTICLES_LIST###'));
			}
		}
		
		/**
		 * 
		 */
		function showGallery($user) {
			
			// Internal variables
			$searchFields = 'title,description';
			$orderByFields = 'crdate';
			
			// SORT BY
			$this->piVars['sort'] = $this->conf['gallery.']['list.']['sortBy'] . ':1';
			
			// Set general internal variables
			$this->api->fe_setInternalVars($this->conf['gallery.']['list.']['maxRecords'],$this->conf['gallery.']['list.']['maxPages'],$searchFields,$orderByFields);
			
			// No active page
			if (!isset($this->piVars['pointer'])) {
				$this->piVars['pointer'] = 0;
			}
			
			// Additionnal MySQL WHERE clause for filters
			$whereClause = ' AND feuser=' . $user['uid'];
			
			// Get records number
			$res = $this->pi_exec_query($this->extTables['pictures'],1,$whereClause);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			
			// Make listing query - Pass query to MySQL database
			$res = $this->pi_exec_query($this->extTables['pictures'],0,$whereClause);
			$this->internal['currentTable'] = $this->extTables['pictures'];
			
			// Check ressource
			if ($res) {
				
				// Storage
				$htmlCode = array();
				
				// Get articles
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Create picture
					$pic = $this->api->fe_createImageObjects($row['picture'],$this->conf['gallery.']['list.']['picture.'],$this->uploadDir);
					
					// Image DIV
					$htmlCode[] = $this->api->fe_makeStyledContent('div','image',$this->pi_linkTP_keepPIvars($pic,array('image'=>$row['uid'])));
				}
				
				// Template markers
				$templateMarkers = array();
				
				// Section header
				$header = (isset($this->user) && $user['uid'] == $this->user['uid']) ? $this->pi_getLL('headers.gallery.connected') : sprintf($this->pi_getLL('headers.gallery'),$user['username']);
				
				// Replace markers
				$templateMarkers['###SINGLE###'] = (array_key_exists('image',$this->piVars)) ? $this->displayImage($user) : '';
				$templateMarkers['###HEADER###'] = $this->api->fe_makeStyledContent('div','header',$header);
				$templateMarkers['###SEARCH###'] = $this->api->fe_makeStyledContent('div','search-header',$this->pi_getLL('headers.search.gallery')) . $this->pi_list_searchBox();
				$templateMarkers['###LIST###'] = $this->api->fe_makeStyledContent('div','list',implode(chr(10),$htmlCode));
				$templateMarkers['###BROWSE###'] = $this->pi_list_browseresults();
				$templateMarkers['###BACK###'] = $this->backLink(array('showgallery','image'));
				$templateMarkers['###BACKLIST###'] = (array_key_exists('sword',$this->piVars)) ? $this->api->fe_makeStyledContent('div','back-list',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('backlist'),array(),array('sword'))) : '';
				
				// Wrap all in a CSS element
				return $this->api->fe_makeStyledContent('div','gallery-list',$this->api->fe_renderTemplate($templateMarkers,'###GALLERY_LIST###'));
			}
		}
		
		/**
		 * 
		 */
		function displayImage($user) {
			
			// Get image
			$image = $this->pi_getRecord($this->extTables['pictures'],$this->piVars['image']);
			
			// Check article
			if (is_array($image)) {
				
				// Template markers
				$templateMarkers = array();
				
				// Create picture
				$pic = $this->api->fe_createImageObjects($image['picture'],$this->conf['gallery.']['single.']['picture.'],$this->uploadDir);
				
				// Get previous picture if any
				$previous = ($res_previous = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['pictures'],'crdate < ' . $image['crdate'] . ' AND deleted=0 AND feuser=' . $user['uid'])) ? $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res_previous) : false;
				
				// Get next picture if any
				$next = ($res_next = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['pictures'],'crdate > ' . $image['crdate'] . ' AND deleted=0 AND feuser=' . $user['uid'])) ? $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res_next) : false;
				
				// Navig
				$navig = array(
					
					// Previous
					'previous' => (is_array($previous)) ? $this->api->fe_makeStyledContent('span','image-previous',$this->pi_linkTP_keepPivars($this->pi_getLL('gallery.previous'),array('image'=>$previous['uid']),1)) : '',
					
					// Next
					'next' => (is_array($next)) ? $this->api->fe_makeStyledContent('span','image-next',$this->pi_linkTP_keepPivars($this->pi_getLL('gallery.next'),array('image'=>$next['uid']),1)) : '',
				);
				
				// Infos
				$infos = array(
					
					// Date
					'date' => $this->api->fe_makeStyledContent('div','image-date',$this->pi_getLL('date') . ' ' . strftime($this->conf['dateFormat'],$image['crdate'])),
					
					// Navig
					'navig' => $this->api->fe_makeStyledContent('div','image-navig',implode(chr(10),$navig)),
				);
				
				// Replace markers
				$templateMarkers['###TITLE###'] = $this->api->fe_makeStyledContent('div','image-title',$image['title']);
				$templateMarkers['###IMAGE###'] = $this->api->fe_makeStyledContent('div','image-image',$pic);
				$templateMarkers['###TEXT###'] = $this->api->fe_makeStyledContent('div','image-text-header',$this->pi_getLL('headers.gallery.about')) . $this->api->fe_makeStyledContent('div','image-text',nl2br(htmlspecialchars($image['description'])));
				$templateMarkers['###INFOS###'] = $this->api->fe_makeStyledContent('div','infos',$this->api->fe_makeStyledContent('div','image-infos',implode(chr(10),$infos)));
				$templateMarkers['###BACK###'] = $this->backLink(array('image','showgallery'));
				
				// Wrap all in a CSS element
				return $this->api->fe_makeStyledContent('div','article',$this->api->fe_renderTemplate($templateMarkers,'###GALLERY_SINGLE###'));
			}
		}
		
		/**
		 * 
		 */
		function getFieldContent($fieldName,$view,$row=false) {
			
			// Field content
			$field = ($row) ? $row[$fieldName] : $this->internal['currentRow'][$fieldName];
			
			// Check field name
			switch($fieldName) {
				
				// Picture
				case 'image':
					
					// Check for a picture
					if (!empty($field)) {
						
						// Picture configuration
						$conf = $this->conf[$view . '.']['picture.'];
						
						// Get picture
						$content = ($view == 'list') ? $this->pi_list_linkSingle($this->api->fe_createImageObjects($field,$conf,'uploads/pics/'),$this->internal['currentRow']['uid'],1) : $this->api->fe_createImageObjects($field,$conf,'uploads/pics/');
					}
					
				break;
				
				// Email
				case 'email':
					
					// Field content
					$content = $this->cObj->typoLink($field,array('parameter'=>$field));
					
				break;
				
				// Email
				case 'www':
					
					// Field content
					$content = $this->cObj->typoLink($field,array('parameter'=>$field));
					
				break;
				
				// Default
				default:
					
					// Field content
					$content = $field;
					
				break;
			}
			
			// Return content
			return $content;
		}
		
		/**
		 * 
		 */
		function backLink($unsetPIvars) {
			
			// Return link
			return $this->api->fe_makeStyledContent('div','back',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('back'),array(),$unsetPIvars,1));
		}
		
		/**
		 * 
		 */
		function getAge($tstamp) {
			
			// Date
			$date1 = date('d.m.Y',$tstamp);
			
			// Now
			$date2 = date('d.m.Y',time());
			
			// First date parts
			$parts_1 = explode('.',$date1);
			
			// Second date parts
			$parts_2 = explode('.',$date2);
			
			// Check month
			if ($parts_1[1] > $parts_2[1]) {
				
				// Birthday already passed
				$age = $parts_2[2] - $parts_1[2];
				
			} else if ($parts_1[1] < $parts_2[1]) {
				
				// Birthday not passed
				$age = $parts_2[2] - $parts_1[2] - 1;
				
			} else {
				
				// Same month - Check day
				if ($parts_1[0] > $parts_2[0] || $parts_1[0] == $parts_2[0]) {
					
					// Birthday already passed or now
					$age = $parts_2[2] - $parts_1[2];
					
				} else {
					
					// Birthday not passed
					$age = $parts_2[2] - $parts_1[2] - 1;
				}
			}
			
			// Return age
			return $age;
		}
		
		/**
		 * 
		 */
		function clearPageCache($pid) {
			
			// Delete page cache
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_pages','page_id=' . $pid);
			
			// Delete page section cache
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('cache_pagesection','page_id=' . $pid);
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weleda_baby/pi1/class.tx_weledababy_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weleda_baby/pi1/class.tx_weledababy_pi1.php']);
	}
?>
