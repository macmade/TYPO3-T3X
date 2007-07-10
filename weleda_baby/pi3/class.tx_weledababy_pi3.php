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
	
	class tx_weledababy_pi3 extends tslib_pibase {
		// Same as class name
		var $prefixId = 'tx_weledababy_pi3';
		
		// Path to this script relative to the extension dir.
		var $scriptRelPath = 'pi3/class.tx_weledababy_pi3.php';
		
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
			'forums_posts' => 'tx_chcforum_post',
			'forums_conf' => 'tx_chcforum_conference',
			'forums_cat' => 'tx_chcforum_category',
		);
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_femp3player_pi3", and
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
			
			// Get FE user
			$this->getFeUser();
			
			// Check user
			if (isset($this->user)) {
				
				// Process confirmations for friends
				$this->processFriends();
				
				// Check template file (TS or Flex)
				$templateFile = ($this->pi_getFFvalue($this->piFlexForm,'template_file','sTMPL') == '') ? $this->conf['templateFile'] : $this->uploadDir . $this->conf['templateFile'];
				
				// Template load and init
				$this->api->fe_initTemplate($templateFile);
				
				// Content
				$content = $this->createHome();
				
				// Return content
				return $this->pi_wrapInBaseClass($content);
			}
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
				'usergroup' => 'sDEF:usergroup',
				'templateFile' => 'sTMPL:template_file',
				'links.' => array(
					'members' => 'sLINK:members',
					'profile' => 'sLINK:profile',
				),
				'chcForums.' => array(
					'storagePid' => 'sFORUMS:forum_storage',
					'displayPage' => 'sFORUMS:forum_display',
					'lastMessages' => 'sFORUMS:forum_lastmessages',
				),
				'lastMembers' => 'sMISC:lastmembers',
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'Weleda Baby PI1: configuration array');
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
			
			// Profile ID
			$id = $this->user['uid'];
			
			// Check processing
			if (array_key_exists('refuse',$this->piVars)) {
				
				// Fields to update
				$updateFields = array(
					'refused' => 1,
				);
				
				// Refuse friend
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['friends'],'uid_local=' . $this->piVars['refuse'] . ' AND uid_foreign=' . $id,$updateFields);
				
				// Update friends
				unset($this->friends[$this->piVars['refuse']]);
				
				// Unset variable
				unset($this->piVars['refuse']);
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
				
			} else if (array_key_exists('confirm',$this->piVars)) {
				
				// Fields to update
				$updateFields = array(
					'confirmed' => 1,
				);
				
				// Confirm friend
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['friends'],'uid_local=' . $this->piVars['confirm'] . ' AND uid_foreign=' . $id,$updateFields);
				
				// Friend profile
				$friendProfile = $this->pi_getRecord($this->extTables['profiles'],$this->piVars['confirm']);
				
				// Fields to insert
				$insertFields = array(
					'uid_local' => $this->profile['uid'],
					'uid_foreign' => $friendProfile['feuser'],
					'confirmed' => 1,
				);
				
				// Add friend
				$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->extTables['friends'],$insertFields);
				
				// Update friends
				$this->friends[$friendProfile['feuser']] = 1;
				
				// Unset variable
				unset($this->piVars['confirm']);
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
				
			} else if (array_key_exists('is-refused',$this->piVars)) {
				
				// Confirm friend
				$GLOBALS['TYPO3_DB']->exec_DELETEquery($this->extTables['friends'],'uid_local=' . $this->profile['uid'] . ' AND uid_foreign=' . $this->piVars['is-refused']);
				
				// Unset variable
				unset($this->piVars['is-refused']);
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
			}
		}
		
		/**
		 * 
		 */
		function createHome() {
			
			// Template markers
			$templateMarkers = array();
			
			// Replace markers
			$templateMarkers['###CONFIRMFRIENDS###'] = $this->confirmFriends();
			$templateMarkers['###INFOS###'] = $this->showInfos();
			$templateMarkers['###OPTIONS###'] = $this->userOptions();
			$templateMarkers['###FRIENDS###'] = $this->displayFriends($this->friends,$this->user);
			$templateMarkers['###ONLINE###'] = $this->showOnlineUsers();
			$templateMarkers['###LASTMEMBERS###'] = $this->showLastMembers();
			$templateMarkers['###BIRTHDAYS###'] = $this->getBirthdays();
			$templateMarkers['###FORUMS_MSG###'] = $this->showLastForumPosts();
			
			// Wrap all in a CSS element
			return $this->api->fe_renderTemplate($templateMarkers,'###HOME###');
		}
		
		/**
		 * 
		 */
		function showInfos() {
			
			// Storage
			$htmlCode = array();
			
			// Picture
			$htmlCode[] = $this->api->fe_makeStyledContent('div','picture',$this->api->fe_createImageObjects($this->user['image'],$this->conf['picture.'],'uploads/pics/'));
			
			// Username
			$htmlCode[] = $this->api->fe_makeStyledContent('div','username',sprintf($this->pi_getLL('infos.username'),$this->user['username']));
			
			// Last login
			$htmlCode[] = ($this->user['lastlogin']) ? $this->api->fe_makeStyledContent('div','connection',$this->pi_getLL('infos.connect') . ' ' . strftime($this->conf['dateHourFormat'],$this->user['lastlogin'])) : '';
			
			// Return infos
			return $this->api->fe_makeStyledContent('div','infos',implode(chr(10),$htmlCode));
		}
		
		/**
		 * 
		 */
		function userOptions() {
			
			// Storage
			$htmlCode = array();
			
			// Header
			$htmlCode[] = $this->api->fe_makeStyledContent('div','options-header',$this->pi_getLL('options.header'));
			
			// Show options
			$htmlCode[] = $this->showOptions();
			
			// Edit options
			$htmlCode[] = $this->editOptions();
			
			// Return options
			return $this->api->fe_makeStyledContent('div','options',implode(chr(10),$htmlCode));
		}
		
		/**
		 * 
		 */
		function showOptions() {
			
			// Storage
			$htmlCode = array();
			
			// Edit features
			$show = array('profile');
			
			// Select articles from member
			$res_articles = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['articles'],'deleted=0 AND feuser=' . $this->user['uid']);
			
			// Select pictures from member
			$res_gallery = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['pictures'],'deleted=0 AND feuser=' . $this->user['uid']);
			
			// Check articles ressource
			if ($res_articles && $GLOBALS['TYPO3_DB']->sql_num_rows($res_articles)) {
				
				// Add articles option
				$show[] = 'articles';
			}
			
			// Check pictures ressource
			if ($res_gallery && $GLOBALS['TYPO3_DB']->sql_num_rows($res_gallery)) {
				
				// Add gallery option
				$show[] = 'gallery';
			}
			
			// URL parameters
			$urlParams = array(
				
				// Profile
				'profile' => array('tx_weledababy_pi1[showUid]'=>$this->user['uid']),
				
				// Articles
				'articles' => array('tx_weledababy_pi1[showUid]'=>$this->user['uid'],'tx_weledababy_pi1[showarticles]'=>1),
				
				// Gallery
				'gallery' => array('tx_weledababy_pi1[showUid]'=>$this->user['uid'],'tx_weledababy_pi1[showgallery]'=>1),
			);
			
			// Page ID
			$pid = $this->conf['links.']['members'];
			
			// Process options
			foreach($show as $option) {
				
				// Create link
				$link = $this->pi_linkTP($this->pi_getLL('show.' . $option),$urlParams[$option],1,$pid);
				
				// Display link
				$htmlCode[] = $this->api->fe_makeStyledContent('div','show-' . $option,$link);
			}
			
			// Return options
			return $this->api->fe_makeStyledContent('div','options-show',implode(chr(10),$htmlCode));
		}
		
		/**
		 * 
		 */
		function editOptions() {
			
			// Storage
			$htmlCode = array();
			
			// Edit features
			$edit = array('profile','description','babies','articles','gallery');
			
			// URL parameters
			$urlParams = array(
				
				// Profile
				'profile' => array('tx_weledababy_pi4[edit]'=>'profile'),
				
				// Description
				'description' => array('tx_weledababy_pi4[edit]'=>'description'),
				
				// Babies
				'babies' => array('tx_weledababy_pi4[edit]'=>'babies'),
				
				// Articles
				'articles' => array('tx_weledababy_pi4[edit]'=>'articles'),
				
				// Gallery
				'gallery' => array('tx_weledababy_pi4[edit]'=>'gallery'),
			);
			
			// Page ID
			$pid = $this->conf['links.']['profile'];
			
			// Process options
			foreach($edit as $option) {
				
				// Create link
				$link = $this->pi_linkTP($this->pi_getLL('edit.' . $option),$urlParams[$option],1,$pid);
				
				// Display link
				$htmlCode[] = $this->api->fe_makeStyledContent('div','edit-' . $option,$link);
			}
			
			// Return options
			return $this->api->fe_makeStyledContent('div','options-edit',implode(chr(10),$htmlCode));
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
				$htmlCode[] = $this->api->fe_makeStyledContent('div','friends-header',$this->pi_getLL('friends'));
				
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
						$display = $this->api->fe_createImageObjects($friend['image'],$this->conf['usersPicture.'],'uploads/pics/') . $this->pi_linkTP($friend['username'],array('tx_weledababy_pi1[showUid]'=>$friend['uid']),1,$this->conf['links.']['members']);
						
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
		function showLastMembers() {
			
			// Storage
			$htmlCode = array();
			
			// Select members
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['users'],'deleted=0 AND usergroup=' . $this->conf['usergroup'],false,'crdate DESC',$this->conf['lastMembers']);
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Header
				$htmlCode[] = $this->api->fe_makeStyledContent('div','last-header',$this->pi_getLL('last'));
				
				// Process users
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// User
					$user = $this->api->fe_createImageObjects($row['image'],$this->conf['usersPicture.'],'uploads/pics/') . $this->pi_linkTP($row['username'],array('tx_weledababy_pi1[showUid]'=>$row['uid']),1,$this->conf['links.']['members']);
					
					// Class
					$class = ($row['uid'] == $this->user['uid'] || array_key_exists($row['uid'],$this->friends)) ? 'last-friend' : 'last-user';
					
					// Add user
					$htmlCode[] = $this->api->fe_makeStyledContent('div',$class,$user);
				}
				
				// Return user list
				return $this->api->fe_makeStyledContent('div','last-list',implode(chr(10),$htmlCode));
			}
		}
		
		/**
		 * 
		 */
		function showOnlineUsers() {
			
			// Storage
			$htmlCode = array();
			
			// Now
			$now = time();
			
			// Range
			$range = $now - $this->conf['timeout'];
			
			// Select online users
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['users'],'deleted=0 AND is_online<=' . $now . ' AND is_online>=' . $range . ' AND usergroup=' . $this->conf['usergroup'],false,'username');
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Header
				$htmlCode[] = $this->api->fe_makeStyledContent('div','online-header',$this->pi_getLL('online'));
				
				// Process users
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						
					// User
					$user = $this->api->fe_createImageObjects($row['image'],$this->conf['usersPicture.'],'uploads/pics/') . $this->pi_linkTP($row['username'],array('tx_weledababy_pi1[showUid]'=>$row['uid']),1,$this->conf['links.']['members']);
					
					// Class
					$class = ($row['uid'] == $this->user['uid'] || array_key_exists($row['uid'],$this->friends)) ? 'online-friend' : 'online-user';
					
					// Add user
					$htmlCode[] = $this->api->fe_makeStyledContent('div',$class,$user);
				}
				
				// Return user list
				return $this->api->fe_makeStyledContent('div','online',implode(chr(10),$htmlCode));
			}
		}
		
		/**
		 * 
		 */
		function getBirthdays() {
			
			// Storage
			$htmlCode = array();
			
			// Now
			$now = time();
			
			// Today
			$today = strtotime(date('m/d/Y',$now));
			
			// Range
			$range = $today + ($this->conf['birthdayDays'] * 24 * 60 * 60);
			
			// Check for friends
			if (isset($this->friends) && count($this->friends)) {
				
				// Additionnal MySQL WHERE clause
				$addWhere = array();
				
				// Create WHERE clause for friends
				foreach($this->friends as $key=>$value) {
					
					// Only process confiremd friends
					if ($value) {
						
						// Add clause for friend
						$addWhere[] = 'feuser=' . $key;
					}
				}
				
				// Select friend babies
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['babies'],'deleted=0 AND (' . implode(' OR ',$addWhere) . ')');
				
				// Check ressource
				if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
					
					// Storage
					$babies = array();
					
					// Check babies
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						
						// Timestamp for comparison
						$tstamp = strtotime(date('m/d/',$row['birth']) . date('Y',$now));
						
						// Check if birthday is soon
						if ($tstamp >= $today && $tstamp <= $range) {
							
							// Add baby
							$babies[date('d.m',$row['birth'])] = $row;
						}
					}
					
					// Sort babies
					ksort($babies);
					
					// Check for babies
					if (count($babies)) {
						
						// Header
						$htmlCode[] = $this->api->fe_makeStyledContent('div','birthdays-header',$this->pi_getLL('birthdays'));
						
						// Process babies
						foreach($babies as $row) {
							
							// Storage
							$baby = array();
							
							// Get user
							$user = $this->pi_getRecord($this->extTables['users'],$row['feuser']);
							
							// Check for a picture
							if (!empty($row['picture'])) {
								
								// Add picture
								$baby[] = $this->api->fe_makeStyledContent('div','picture',$this->api->fe_createImageObjects($row['picture'],$this->conf['babiesPicture.'],$this->uploadDir));
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
							$baby[] = $this->api->fe_makeStyledContent('div','sex',$this->pi_getLL('sex.' . $row['sex']) . ' (' . $this->pi_linkTP($user['username'],array('tx_weledababy_pi1[showUid]'=>$user['uid']),1,$this->conf['links.']['members']) . ')');
							
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
						
						// Return birthdays list
						return $this->api->fe_makeStyledContent('div','birthdays',implode(chr(10),$htmlCode));
					}
				}
			}
		}
		
		/**
		 * 
		 */
		function confirmFriends() {
			
			// Storage
			$htmlCode = array();
			
			// Select people waiting for confirmation
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['friends'],'uid_foreign=' . $this->user['uid'] . ' AND confirmed=0 AND NOT refused');
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Header
				$htmlCode[] = $this->api->fe_makeStyledContent('div','confirm-header',$this->pi_getLL('confirm'));
				
				// Process users
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Storage
					$confirm = array();
					
					// Get user profile
					$profile = $this->pi_getRecord($this->extTables['profiles'],$row['uid_local']);
					
					// Get user row
					$user = $this->pi_getRecord($this->extTables['users'],$profile['feuser']);
					
					// Username with link
					$username = $this->pi_linkTP($user['username'],array('tx_weledababy_pi1[showUid]'=>$user['uid']),1,$this->conf['links.']['members']);
					
					// Add text
					$confirm[] = $this->api->fe_makeStyledContent('div','confirm-text',sprintf($this->pi_getLL('confirm.message'),$username));
					
					// Add accept link
					$confirm[] = $this->api->fe_makeStyledContent('div','confirm-accept',$this->pi_linkTP_keepPIvars($this->pi_getLL('confirm.accept'),array('confirm'=>$row['uid_local']),1));
					
					// Add refuse link
					$confirm[] = $this->api->fe_makeStyledContent('div','confirm-refuse',$this->pi_linkTP_keepPIvars($this->pi_getLL('confirm.refuse'),array('refuse'=>$row['uid_local']),1));
					
					// Add confirmation
					$htmlCode[] = $this->api->fe_makeStyledContent('div','confirm-user',implode(chr(10),$confirm));
				}
			}
			
			// Select refused contacts
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['friends'],'uid_local=' . $this->profile['uid'] . ' AND refused=1');
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Header
				$htmlCode[] = $this->api->fe_makeStyledContent('div','refuse-header',$this->pi_getLL('refuse'));
				
				// Process users
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Storage
					$refuse = array();
					
					// Get user row
					$user = $this->pi_getRecord($this->extTables['users'],$row['uid_foreign']);
					
					// Username with link
					$username = $this->pi_linkTP($user['username'],array('tx_weledababy_pi1[showUid]'=>$user['uid']),1,$this->conf['links.']['members']);
					
					// Add text
					$refuse[] = $this->api->fe_makeStyledContent('div','refuse-text',sprintf($this->pi_getLL('refuse.message'),$username));
					
					// Add accept link
					$refuse[] = $this->api->fe_makeStyledContent('div','refuse-accept',$this->pi_linkTP_keepPIvars($this->pi_getLL('ok'),array('is-refused'=>$row['uid_foreign']),1));
					
					// Add confirmation
					$htmlCode[] = $this->api->fe_makeStyledContent('div','refuse-user',implode(chr(10),$refuse));
				}
			}
			
			// Check for data
			if (count($htmlCode)) {
				
				// Return confirmation list
				return $this->api->fe_makeStyledContent('div','confirm',implode(chr(10),$htmlCode));
			}
		}
		
		/**
		 * 
		 */
		function showLastForumPosts() {
			
			// Check for forum infos
			if ($this->conf['chcForums.']['storagePid'] && $this->conf['chcForums.']['displayPage']) {
				
				// Storage
				$htmlCode = array();
				
				// Select posts
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['forums_posts'],'deleted=0 AND pid=' . $this->conf['chcForums.']['storagePid'],false,'crdate DESC',$this->conf['chcForums.']['lastMessages']);
				
				// Check ressource
				if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
					
					// Header
					$htmlCode[] = $this->api->fe_makeStyledContent('div','forums-header',$this->pi_getLL('forums'));
					
					// Process posts
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						
						// Get user
						$user = $this->pi_getRecord($this->extTables['users'],$row['post_author']);
						
						// Storage
						$post = array();
						
						// Get conference
						$conf = $this->pi_getRecord($this->extTables['forums_conf'],$row['conference_id']);
						
						// Get category
						$cat = $this->pi_getRecord($this->extTables['forums_cat'],$conf['cat_id']);
						
						// Username
						$username = $this->api->fe_makeStyledContent('span','forums-author',' (' . $this->pi_getLL('by') . ' ' . $this->pi_linkTP($user['username'],array('tx_weledababy_pi1[showUid]'=>$user['uid']),1,$this->conf['links.']['members']) . ', ' . strftime($this->conf['dateHourFormat'],$row['crdate']) . ')');
						
						// Title
						$post[] = $this->api->fe_makeStyledContent('div','forums-subject',$this->pi_linkTP($row['post_subject'],array('view'=>'single_thread','cat_uid'=>$cat['uid'],'conf_uid'=>$conf['uid'],'thread_uid'=>$row['thread_id']),0,$this->conf['chcForums.']['displayPage']) . $username);
						
						// Text
						$post[] = $this->api->fe_makeStyledContent('div','forums-text',$this->api->div_crop($row['post_text'],$this->conf['chcForums.']['crop']));
						
						// Conference
						$post[] = $this->api->fe_makeStyledContent('div','forums-conf',$this->pi_getLL('forums.conference'). ' ' . $this->pi_linkTP($conf['conference_name'],array('view'=>'single_conf','cat_uid'=>$cat['uid'],'conf_uid'=>$conf['uid']),0,$this->conf['chcForums.']['displayPage']));
						
						// Add user
						$htmlCode[] = $this->api->fe_makeStyledContent('div','forums-post',implode(chr(10),$post));
					}
					
					// Return user list
					return $this->api->fe_makeStyledContent('div','forums',implode(chr(10),$htmlCode));
				}
			}
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
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weleda_baby/pi3/class.tx_weledababy_pi3.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weleda_baby/pi3/class.tx_weledababy_pi3.php']);
	}
?>
