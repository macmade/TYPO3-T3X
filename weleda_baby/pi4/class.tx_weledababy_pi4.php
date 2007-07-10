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
	
	class tx_weledababy_pi4 extends tslib_pibase {
		// Same as class name
		var $prefixId = 'tx_weledababy_pi4';
		
		// Path to this script relative to the extension dir.
		var $scriptRelPath = 'pi4/class.tx_weledababy_pi4.php';
		
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
		 * This function initialises the plugin "tx_femp3player_pi4", and
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
				
			// Get POST data
			$this->fedata = t3lib_div::_POST('fedata');
			
			// Check user
			if (isset($this->user)) {
				
				// Check template file (TS or Flex)
				$templateFile = ($this->pi_getFFvalue($this->piFlexForm,'template_file','sTMPL') == '') ? $this->conf['templateFile'] : $this->uploadDir . $this->conf['templateFile'];
				
				// Template load and init
				$this->api->fe_initTemplate($templateFile);
				
				// Content
				$content = $this->editProfile();
				
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
				'storagePid' => 'sDEF:pages',
				'usergroup' => 'sDEF:usergroup',
				'templateFile' => 'sTMPL:template_file',
				'links.' => array(
					'members' => 'sLINK:members',
				),
				'profile.' => array(
					'requiredFields' => 'sFIELDS:required',
				)
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
		function editProfile() {
			
			// Template markers
			$templateMarkers = array();
			
			// Check for plugin variables
			if (array_key_exists('edit',$this->piVars) && $this->piVars['edit'] != 'profile') {
				
				// Check edit variable
				switch($this->piVars['edit']) {
					
					// Description
					case 'description':
						$edit = $this->sectionDescription();
					break;
					
					// Gallery
					case 'gallery':
						$edit = $this->sectionGallery();
					break;
					
					// Babies
					case 'babies':
						$edit = $this->sectionBabies();
					break;
					
					// Articles
					case 'articles':
						$edit = $this->sectionArticles();
					break;
				}
				
			} else {
				
				// Default edit form
				$edit = $this->sectionProfile();
			}
			
			// Replace markers
			$templateMarkers['###OPTIONS###'] = $this->userOptions();
			$templateMarkers['###EDIT###'] = $edit;
			
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
			$pid = $GLOBALS['TSFE']->id;
			
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
		function sectionProfile() {
			
			// Field list
			$fieldTypes = array('password'=>'password','name'=>'input','image'=>'image','address'=>'text','zip'=>'input','city'=>'input','country'=>'input','email'=>'input','www'=>'input','telephone'=>'input','fax'=>'input','title'=>'input','company'=>'input');
			
			$fields = explode(',',$this->conf['profile.']['fieldList']);
			
			// Required fields
			$this->requiredFields = explode(',',$this->conf['profile.']['requiredFields']);
			
			// Check for mandatory field 'password'
			if (!in_array('password',$this->requiredFields)) {
				
				// Add field
				$this->requiredFields[] = 'password';
			}
			
			// Template markers
			$templateMarkers = array();
			
			// Empty confirmation
			$templateMarkers['###CONFIRM###'] = '';
			
			// Check for FE data
			if (array_key_exists('submit',$this->piVars) && count($this->fedata[$this->prefixId])) {
				
				// Get image
				$this->getImageField('image');
				
				// Get errors
				$this->checkProfileErrors();
				
				// Check for errors
				if (!count($this->errors)) {
					
					// Data array
					$data = array(
						'name' => $this->fedata[$this->prefixId]['name'],
						'address' => $this->fedata[$this->prefixId]['address'],
						'zip' => $this->fedata[$this->prefixId]['zip'],
						'city' => $this->fedata[$this->prefixId]['city'],
						'country' => $this->fedata[$this->prefixId]['country'],
						'email' => $this->fedata[$this->prefixId]['email'],
						'www' => $this->fedata[$this->prefixId]['www'],
						'telephone' => $this->fedata[$this->prefixId]['telephone'],
						'fax' => $this->fedata[$this->prefixId]['fax'],
						'title' => $this->fedata[$this->prefixId]['title'],
						'company' => $this->fedata[$this->prefixId]['company'],
						'tstamp' => time(),
					);
					
					// Check for a new password
					if (!empty($this->fedata[$this->prefixId]['password'][0])) {
						
						// Add password
						$data['password'] = $this->fedata[$this->prefixId]['password'][0];
					}
					
					// Check for a picture
					if (array_key_exists('image',$this->fedata[$this->prefixId])) {
						
						// Upload as temp file
						$tmp = t3lib_div::upload_to_tempfile($this->fedata[$this->prefixId]['image']['tmp_name']);
						
						// Storage directory (absolute)
						$storage = t3lib_div::getFileAbsFileName('uploads/pics/');
						
						// File extension
						$ext = (strrpos($this->fedata[$this->prefixId]['image']['name'],'.')) ? substr($this->fedata[$this->prefixId]['image']['name'],strrpos($this->fedata[$this->prefixId]['image']['name'],'.'),strlen($this->fedata[$this->prefixId]['image']['name'])) : false;
						
						// File name
						$fName = uniqid(rand()) . $ext;
						
						// Move file to final destination
						t3lib_div::upload_copy_move($tmp,$storage . $fName);
							
						// Set reference
						$data['image'] = $fName;
						
						// Delete temp file
						t3lib_div::unlink_tempfile($tmp);
						
						// Delete old file
						@unlink(t3lib_div::getFileAbsFileName('uploads/pics/' . $this->user['image']));
					}
					
					// Update user
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['users'],'uid=' . $this->user['uid'],$data);
					
					// Birth
					$birth = strtotime($this->fedata[$this->prefixId]['birth']);
					
					// Update profile
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['profiles'],'uid=' . $this->profile['uid'],array('birth'=>$birth));
					
					// Clear members page cache
					$this->clearPageCache($this->conf['links.']['members']);
					
					// Get user
					$user = $this->pi_getRecord($this->extTables['users'],$this->user['uid']);
					
					// Update user
					$this->user = $user;
					
					// Update profile
					$this->profile['birth'] = $birth;
					
					// Confirmation
					$templateMarkers['###CONFIRM###'] = $this->api->fe_makeStyledContent('div','confirm',$this->pi_getLL('confirm'));
				}
			}
			
			// Replace markers
			$templateMarkers['###HEADER###'] = $this->api->fe_makeStyledContent('div','section-header',$this->pi_getLL('section.profile'));
			
			// Process each field
			foreach($fields as $value) {
				
				// Overwriting template markers
				$templateMarkers['###' . strtoupper($value) . '###'] = $this->writeInput($value,$this->user[$value],$this->conf['profile.'],$fieldTypes[$value]);
			}
			
			// Birth date
			$templateMarkers['###BIRTH###'] = $this->writeInput('birth',$this->profile['birth'],$this->conf['profile.'],'date');
			
			// Submit
			$templateMarkers['###SUBMIT###'] = $this->api->fe_makeStyledContent('div','field','<input type="submit" id="submit" name="submit" value="' . $this->pi_getLL('submit.modify') . '" />');
			
			// Wrap all in a form
			$content = $this->api->fe_makeStyledContent('form','inputForm',$this->api->fe_renderTemplate($templateMarkers,'###PROFILE###'),1,0,0,array('action'=>$this->pi_linkTP_keepPIvars_url(array('submit'=>'1')),'enctype'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],'method'=>'post'));
			
			// Return content wrapped in a CSS element
			return $this->api->fe_makeStyledContent('div','profile',$content);
		}
		
		/**
		 * 
		 */
		function checkProfileErrors() {
			
			// Storage
			$this->errors = array();
			
			// Check required fields
			foreach($this->requiredFields as $req) {
				
				// Check FE data
				if ($req && (!array_key_exists($req,$this->fedata[$this->prefixId]) || empty($this->fedata[$this->prefixId][$req]))) {
					
					// Error
					$this->errors[$req] = $this->pi_getLL('errors.missing');
				}
			}
			
			// Special check for email
			if (!array_key_exists('email',$this->errors)) {
				
				// Check for a real email address
				if (!t3lib_div::validEmail($this->fedata[$this->prefixId]['email'])) {
					
					// Error
					$this->errors['email'] = $this->pi_getLL('errors.email');
				}
			}
			
			// Special check for password
			if (!array_key_exists('password',$this->errors)) {
				
				// Password field
				$pwd = $this->fedata[$this->prefixId]['password'];
				
				// Check for same passwords
				if ((!empty($pwd[0]) && !empty($pwd[1])) && ($pwd[0] != $pwd[1])) {
					
					// Error
					$this->errors['password'] = $this->pi_getLL('errors.password');
				}
			}
			
			// Special check for picture
			if (!array_key_exists('image',$this->errors) && array_key_exists('image',$this->fedata[$this->prefixId])) {
				
				// Load TCA for users
				t3lib_div::loadTCA($this->extTables['users']);
				
				// Field settings
				$conf = $GLOBALS['TCA']['fe_users']['columns']['image'];
				
				// Image name
				$pic = $this->fedata[$this->prefixId]['image']['name'];
				
				// File extension
				$ext = (strrpos($pic,'.')) ? substr($pic,strrpos($pic,'.') + 1,strlen($pic)) : false;
				
				// Allowed extensions
				$allowedExt = explode(',',$conf['config']['allowed']);
				
				// Maximum size
				$maxSize = $conf['config']['max_size'];
				
				// Check image
				if (!$ext || !in_array($ext,$allowedExt) || !t3lib_div::verifyFilenameAgainstDenyPattern($pic)) {
					
					// Wrong type
					$this->errors['image'] = $this->pi_getLL('errors.image.type');
					
				} else if (($this->fedata[$this->prefixId]['image']['size'] / 1024) > $maxSize) {
					
					// Wrong size
					$this->errors['image'] = $this->pi_getLL('errors.image.size');
				}
			}
		}
		
		/**
		 * 
		 */
		function sectionDescription() {
			
			// Required fields
			$this->requiredFields = explode(',',$this->conf['description.']['requiredFields']);
			
			// Template markers
			$templateMarkers = array();
			
			// Empty confirmation
			$templateMarkers['###CONFIRM###'] = '';
			
			// Check for FE data
			if (array_key_exists('submit',$this->piVars) && count($this->fedata[$this->prefixId])) {
				
				// Storage
				$this->errors = array();
				
				// Check required fields
				foreach($this->requiredFields as $req) {
					
					// Check FE data
					if ($req && (!array_key_exists($req,$this->fedata[$this->prefixId]) || empty($this->fedata[$this->prefixId][$req]))) {
						
						// Error
						$this->errors[$req] = $this->pi_getLL('errors.missing');
					}
				}
				
				// Check for errors
				if (!count($this->errors)) {
					
					// Data
					$data = array(
						'description' => $this->fedata[$this->prefixId]['description'],
						'tstamp' => time(),
					);
					
					// Update description
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['profiles'],'uid=' . $this->profile['uid'],$data);
					
					// Update profile array
					$this->profile['description'] = $data['description'];
					
					// Clear members page cache
					$this->clearPageCache($this->conf['links.']['members']);
					
					// Confirmation
					$templateMarkers['###CONFIRM###'] = $this->api->fe_makeStyledContent('div','confirm',$this->pi_getLL('confirm'));
				}
			}
			
			// Replace markers
			$templateMarkers['###HEADER###'] = $this->api->fe_makeStyledContent('div','section-header',$this->pi_getLL('section.description'));
			$templateMarkers['###TEXT###'] = $this->writeInput('description',$this->profile['description'],$this->conf['description.'],'text');
			$templateMarkers['###SUBMIT###'] = $this->api->fe_makeStyledContent('div','field','<input type="submit" id="submit" name="submit" value="' . $this->pi_getLL('submit.modify') . '" />');
			
			// Wrap all in a form
			$content = $this->api->fe_makeStyledContent('form','inputForm',$this->api->fe_renderTemplate($templateMarkers,'###DESCRIPTION###'),1,0,0,array('action'=>$this->pi_linkTP_keepPIvars_url(array('submit'=>'1')),'enctype'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],'method'=>'post'));
			
			// Return content wrapped in a CSS element
			return $this->api->fe_makeStyledContent('div','description',$content);
		}
		
		/**
		 * 
		 */
		function sectionGallery() {
			
			// Required fields
			$this->requiredFields = explode(',',$this->conf['gallery.']['requiredFields']);
			
			// Check for mandatory field 'title'
			if (!in_array('title',$this->requiredFields)) {
				
				// Add field
				$this->requiredFields[] = 'title';
			}
			
			// Check for mandatory field 'picture'
			if (!in_array('picture',$this->requiredFields) && array_key_exists('new',$this->piVars)) {
				
				// Add field
				$this->requiredFields[] = 'picture';
			}
			
			// Template markers
			$templateMarkers = array();
			
			// Empty form
			$form = '';
			
			// Delete picture
			if (array_key_exists('delete',$this->piVars)) {
				
				// Set delete flag
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['pictures'],'feuser=' . $this->user['uid'] . ' AND uid=' . $this->piVars['delete'],array('deleted'=>1,'tstamp'=>time()));
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
				
				// Confirmation
				$form = $this->api->fe_makeStyledContent('div','confirm',$this->pi_getLL('confirm'));
				
			} else if (array_key_exists('modify',$this->piVars) || array_key_exists('new',$this->piVars)) {
				
				// Form
				$form = $this->formGallery();
			}
			// Replace markers
			$templateMarkers['###HEADER###'] = $this->api->fe_makeStyledContent('div','section-header',$this->pi_getLL('section.gallery'));
			$templateMarkers['###LIST###'] = $this->listPictures();
			$templateMarkers['###NEW###'] = $this->api->fe_makeStyledContent('div','new',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('new.picture'),array('new'=>1),array('modify','delete','submit'),1));
			$templateMarkers['###FORM###'] = $form;
			
			// Wrap all in a form
			$content = $this->api->fe_makeStyledContent('form','inputForm',$this->api->fe_renderTemplate($templateMarkers,'###GALLERY###'),1,0,0,array('action'=>$this->pi_linkTP_keepPIvars_url(array('submit'=>'1')),'enctype'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],'method'=>'post'));
			
			// Return content wrapped in a CSS element
			return $this->api->fe_makeStyledContent('div','gallery',$content);
		}
		
		/**
		 * 
		 */
		function formGallery() {
			
			// Storage
			$this->errors = array();
			
			// Check for FE data
			if (array_key_exists('submit',$this->piVars) && count($this->fedata[$this->prefixId])) {
				
				// Get image
				$this->getImageField('picture');
				
				// Get errors
				$this->checkGalleryErrors();
			}
				
			// Empty array
			$values = array('title'=>'','description'=>'','picture'=>'');
			
			// Check for edit flag
			if (array_key_exists('modify',$this->piVars)) {
				
				// Select record
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['pictures'],'uid=' . $this->piVars['modify'] . ' AND feuser=' . $this->user['uid']);
				
				// Get record
				$pic = ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) ? $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) : false;
				
				// Check for article
				if (is_array($pic)) {
					
					// Set values
					$values['title'] = $pic['title'];
					$values['description'] = $pic['description'];
					$values['picture'] = $pic['picture'];
				}
			}
			
			// Check for errors
			if (array_key_exists('submit',$this->piVars) && !count($this->errors)) {
				
				// Now
				$now = time();
				
				// Data array
				$data = array(
					'title' => $this->fedata[$this->prefixId]['title'],
					'description' => $this->fedata[$this->prefixId]['description'],
					'tstamp' => $now,
				);
				
				// Check for a picture
				if (array_key_exists('picture',$this->fedata[$this->prefixId]) && count($this->fedata[$this->prefixId]['picture'])) {
					
					// Upload as temp file
					$tmp = t3lib_div::upload_to_tempfile($this->fedata[$this->prefixId]['picture']['tmp_name']);
					
					// Storage directory (absolute)
					$storage = t3lib_div::getFileAbsFileName($this->uploadDir);
					
					// File extension
					$ext = (strrpos($this->fedata[$this->prefixId]['picture']['name'],'.')) ? substr($this->fedata[$this->prefixId]['picture']['name'],strrpos($this->fedata[$this->prefixId]['picture']['name'],'.'),strlen($this->fedata[$this->prefixId]['picture']['name'])) : false;
					
					// File name
					$fName = uniqid(rand()) . $ext;
					
					// Move file to final destination
					t3lib_div::upload_copy_move($tmp,$storage . $fName);
						
					// Set reference
					$data['picture'] = $fName;
					
					// Delete temp file
					t3lib_div::unlink_tempfile($tmp);
					
					// Delete old file
					@unlink(t3lib_div::getFileAbsFileName($this->uploadDir . $pic['picture']));
				}
				
				// Check for action
				if (array_key_exists('modify',$this->piVars)) {
					
					// Update record
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['pictures'],'feuser=' . $this->user['uid'] . ' AND uid=' . $this->piVars['modify'],$data);
					
				} else if (array_key_exists('new',$this->piVars)) {
					
					// Add special fields
					$data['crdate'] = $now;
					$data['pid'] = $this->conf['storagePid'];
					$data['feuser'] = $this->user['uid'];
					
					// New record
					$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->extTables['pictures'],$data);
				}
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
				
				// Confirmation
				return $this->api->fe_makeStyledContent('div','confirm',$this->pi_getLL('confirm'));
				
			} else {
				
				// Submit label
				$submitLabel = (array_key_exists('modify',$this->piVars)) ? $this->pi_getLL('submit.modify') : $this->pi_getLL('submit.new');
				
				// Fields
				$fields = array('title'=>'input','description'=>'text','picture'=>'image');
				
				// Template markers
				$templateMarkers = array();
				
				// Process each field
				foreach($fields as $key=>$value) {
					
					// Overwriting template markers
					$templateMarkers['###' . strtoupper($key) . '###'] = $this->writeInput($key,$values[$key],$this->conf['gallery.'],$value);
				}
				
				// Submit
				$templateMarkers['###SUBMIT###'] = $this->api->fe_makeStyledContent('div','field','<input type="submit" id="submit" name="submit" value="' . $submitLabel. '" />');
				
				// Wrap all in a form
				$content = $this->api->fe_makeStyledContent('form','inputForm',$this->api->fe_renderTemplate($templateMarkers,'###GALLERY_FORM###'),1,0,0,array('action'=>$this->pi_linkTP_keepPIvars_url(array('submit'=>'1')),'enctype'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],'method'=>'post'));
				
				// Return content
				return $this->api->fe_makeStyledContent('div','gallery-form',$content);
			}
		}
		
		/**
		 * 
		 */
		function checkGalleryErrors() {
			
			// Check required fields
			foreach($this->requiredFields as $req) {
				
				// Check FE data
				if ($req && (!array_key_exists($req,$this->fedata[$this->prefixId]) || empty($this->fedata[$this->prefixId][$req]))) {
					
					// Error
					$this->errors[$req] = $this->pi_getLL('errors.missing');
				}
			}
			
			// Special check for picture
			if (!array_key_exists('picture',$this->errors) && array_key_exists('picture',$this->fedata[$this->prefixId])) {
				
				// Load TCA for users
				t3lib_div::loadTCA($this->extTables['pictures']);
				
				// Field settings
				$conf = $GLOBALS['TCA'][$this->extTables['pictures']]['columns']['picture'];
				
				// Image name
				$pic = $this->fedata[$this->prefixId]['picture']['name'];
				
				// File extension
				$ext = (strrpos($pic,'.')) ? substr($pic,strrpos($pic,'.') + 1,strlen($pic)) : false;
				
				// Allowed extensions
				$allowedExt = explode(',',$conf['config']['allowed']);
				
				// Maximum size
				$maxSize = $conf['config']['max_size'];
				
				// Check image
				if (!$ext || !in_array($ext,$allowedExt) || !t3lib_div::verifyFilenameAgainstDenyPattern($pic)) {
					
					// Wrong type
					$this->errors['picture'] = $this->pi_getLL('errors.image.type');
					
				} else if (($this->fedata[$this->prefixId]['image']['size'] / 1024) > $maxSize) {
					
					// Wrong size
					$this->errors['picture'] = $this->pi_getLL('errors.image.size');
				}
			}
		}
		
		/**
		 * 
		 */
		function listPictures() {
			
			// Select pictures from member
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['pictures'],'deleted=0 AND feuser=' . $this->user['uid'],false,'crdate DESC');
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Storage
				$htmlCode = array();
				
				// Get articles
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Date
					$date = $this->api->fe_makeStyledContent('div','date',strftime($this->conf['dateFormat'],$row['crdate']));
					
					// Title
					$title = $this->api->fe_makeStyledContent('div','title',$row['title']);
					
					// Thumbnail
					$thumb = $this->api->fe_makeStyledContent('div','thumb',$this->api->fe_createImageObjects($row['picture'],$this->conf['gallery.']['imgConf.'],$this->uploadDir));
					
					// Options
					$options = array(
						
						// Edit
						$this->api->fe_makeStyledContent('span','modify',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('edit'),array('modify'=>$row['uid']),array('new','delete','submit'),1)),
						
						// Delete
						$this->api->fe_makeStyledContent('span','delete',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('delete'),array('delete'=>$row['uid']),array('new','modify','submit'),1)),
					);
					
					// Display article
					$htmlCode[] = $this->api->fe_makeStyledContent('div','picture',$date . $title . $thumb . $this->api->fe_makeStyledContent('div','options',implode(chr(10),$options)));
				}
				
				// Return list
				return $this->api->fe_makeStyledContent('div','gallery-list',implode(chr(10),$htmlCode));
			}
		}
		
		/**
		 * 
		 */
		function sectionArticles() {
			
			// Required fields
			$this->requiredFields = explode(',',$this->conf['articles.']['requiredFields']);
			
			// Check for mandatory field 'title'
			if (!in_array('title',$this->requiredFields)) {
				
				// Add field
				$this->requiredFields[] = 'title';
			}
			
			// Template markers
			$templateMarkers = array();
			
			// Empty form
			$form = '';
			
			// Delete article
			if (array_key_exists('delete',$this->piVars)) {
				
				// Set delete flag
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['articles'],'feuser=' . $this->user['uid'] . ' AND uid=' . $this->piVars['delete'],array('deleted'=>1,'tstamp'=>time()));
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
				
				// Confirmation
				$form = $this->api->fe_makeStyledContent('div','confirm',$this->pi_getLL('confirm'));
				
			} else if (array_key_exists('modify',$this->piVars) || array_key_exists('new',$this->piVars)) {
				
				// Form
				$form = $this->formArticles();
			}
			
			// Replace markers
			$templateMarkers['###HEADER###'] = $this->api->fe_makeStyledContent('div','section-header',$this->pi_getLL('section.articles'));
			$templateMarkers['###LIST###'] = $this->listArticles();
			$templateMarkers['###NEW###'] = $this->api->fe_makeStyledContent('div','new',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('new.article'),array('new'=>1),array('modify','delete','submit'),1));
			$templateMarkers['###FORM###'] = $form;
			
			// Wrap all in a form
			$content = $this->api->fe_makeStyledContent('form','inputForm',$this->api->fe_renderTemplate($templateMarkers,'###ARTICLES###'),1,0,0,array('action'=>$this->pi_linkTP_keepPIvars_url(array('submit'=>'1')),'enctype'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],'method'=>'post'));
			
			// Return content wrapped in a CSS element
			return $this->api->fe_makeStyledContent('div','articles',$content);
		}
		
		/**
		 * 
		 */
		function formArticles() {
			
			// Storage
			$this->errors = array();
			
			// Check for FE data
			if (array_key_exists('submit',$this->piVars) && count($this->fedata[$this->prefixId])) {
				
				// Get errors
				$this->checkArticlesErrors();
			}
			
			// Check for errors
			if (array_key_exists('submit',$this->piVars) && !count($this->errors)) {
				
				// Now
				$now = time();
				
				// Data array
				$data = array(
					'title' => $this->fedata[$this->prefixId]['title'],
					'article' => $this->fedata[$this->prefixId]['article'],
					'tstamp' => $now,
				);
				
				// Check for action
				if (array_key_exists('modify',$this->piVars)) {
					
					// Update record
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['articles'],'feuser=' . $this->user['uid'] . ' AND uid=' . $this->piVars['modify'],$data);
					
				} else if (array_key_exists('new',$this->piVars)) {
					
					// Add special fields
					$data['crdate'] = $now;
					$data['pid'] = $this->conf['storagePid'];
					$data['feuser'] = $this->user['uid'];
					
					// New record
					$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->extTables['articles'],$data);
				}
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
				
				// Confirmation
				return $this->api->fe_makeStyledContent('div','confirm',$this->pi_getLL('confirm'));
				
			} else {
				
				// Empty array
				$values = array('title'=>'','article'=>'');
				
				// Check for edit flag
				if (array_key_exists('modify',$this->piVars)) {
					
					// Select record
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['articles'],'uid=' . $this->piVars['modify'] . ' AND feuser=' . $this->user['uid']);
					
					// Get record
					$article = ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) ? $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) : false;
					
					// Check for article
					if (is_array($article)) {
						
						// Set values
						$values['title'] = $article['title'];
						$values['article'] = $article['article'];
					}
				}
				
				// Submit label
				$submitLabel = (array_key_exists('modify',$this->piVars)) ? $this->pi_getLL('submit.modify') : $this->pi_getLL('submit.new');
				
				// Fields
				$fields = array('title'=>'input','article'=>'text');
				
				// Template markers
				$templateMarkers = array();
				
				// Process each field
				foreach($fields as $key=>$value) {
					
					// Overwriting template markers
					$templateMarkers['###' . strtoupper($key) . '###'] = $this->writeInput($key,$values[$key],$this->conf['articles.'],$value);
				}
				
				// Submit
				$templateMarkers['###SUBMIT###'] = $this->api->fe_makeStyledContent('div','field','<input type="submit" id="submit" name="submit" value="' . $submitLabel. '" />');
				
				// Wrap all in a form
				$content = $this->api->fe_makeStyledContent('form','inputForm',$this->api->fe_renderTemplate($templateMarkers,'###ARTICLES_FORM###'),1,0,0,array('action'=>$this->pi_linkTP_keepPIvars_url(array('submit'=>'1')),'enctype'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],'method'=>'post'));
				
				// Return content
				return $this->api->fe_makeStyledContent('div','article-form',$content);
			}
		}
		
		/**
		 * 
		 */
		function checkArticlesErrors() {
			
			// Check required fields
			foreach($this->requiredFields as $req) {
				
				// Check FE data
				if ($req && (!array_key_exists($req,$this->fedata[$this->prefixId]) || empty($this->fedata[$this->prefixId][$req]))) {
					
					// Error
					$this->errors[$req] = $this->pi_getLL('errors.missing');
				}
			}
		}
		
		/**
		 * 
		 */
		function listArticles() {
			
			// Select articles from member
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['articles'],'deleted=0 AND feuser=' . $this->user['uid'],false,'crdate DESC');
			
			// Check ressource
			if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				
				// Storage
				$htmlCode = array();
				
				// Get articles
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					// Date
					$date = $this->api->fe_makeStyledContent('div','date',strftime($this->conf['dateFormat'],$row['crdate']));
					
					// Title
					$title = $this->api->fe_makeStyledContent('div','title',$row['title']);
					
					// Resume
					$resume = $this->api->fe_makeStyledContent('div','resume',$this->api->div_crop($row['article'],$this->conf['articles.']['crop']));
					
					// Options
					$options = array(
						
						// Edit
						$this->api->fe_makeStyledContent('span','modify',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('edit'),array('modify'=>$row['uid']),array('new','delete','submit'),1)),
						
						// Delete
						$this->api->fe_makeStyledContent('span','delete',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('delete'),array('delete'=>$row['uid']),array('new','modify','submit'),1)),
					);
					
					// Display article
					$htmlCode[] = $this->api->fe_makeStyledContent('div','article',$date . $title . $resume . $this->api->fe_makeStyledContent('div','options',implode(chr(10),$options)));
				}
				
				// Return list
				return $this->api->fe_makeStyledContent('div','articles-list',implode(chr(10),$htmlCode));
			}
		}
		
		/**
		 * 
		 */
		function sectionBabies() {
			
			// Required fields
			$this->requiredFields = explode(',',$this->conf['babies.']['requiredFields']);
			
			// Check for mandatory field 'title'
			if (!in_array('name',$this->requiredFields)) {
				
				// Add field
				$this->requiredFields[] = 'name';
			}
			
			// Check for mandatory field 'title'
			if (!in_array('sex',$this->requiredFields)) {
				
				// Add field
				$this->requiredFields[] = 'sex';
			}
			
			// Check for mandatory field 'title'
			if (!in_array('birth',$this->requiredFields)) {
				
				// Add field
				$this->requiredFields[] = 'birth';
			}
			
			// Template markers
			$templateMarkers = array();
			
			// Empty form
			$form = '';
			
			// Delete picture
			if (array_key_exists('delete',$this->piVars)) {
				
				// Set delete flag
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['babies'],'feuser=' . $this->user['uid'] . ' AND uid=' . $this->piVars['delete'],array('deleted'=>1,'tstamp'=>time()));
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
				
				// Confirmation
				$form = $this->api->fe_makeStyledContent('div','confirm',$this->pi_getLL('confirm'));
				
			} else if (array_key_exists('modify',$this->piVars) || array_key_exists('new',$this->piVars)) {
				
				// Add form
				$form = $this->formBabies();
			}
			
			// Replace markers
			$templateMarkers['###HEADER###'] = $this->api->fe_makeStyledContent('div','section-header',$this->pi_getLL('section.babies'));
			$templateMarkers['###WARNING###'] = $this->api->fe_makeStyledContent('div','section-warning',$this->pi_getLL('babies.warning'));
			$templateMarkers['###LIST###'] = $this->listBabies();
			$templateMarkers['###NEW###'] = $this->api->fe_makeStyledContent('div','new',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('new.baby'),array('new'=>1),array('modify','delete','submit'),1));
			$templateMarkers['###FORM###'] = $form;
			
			// Wrap all in a form
			$content = $this->api->fe_makeStyledContent('form','inputForm',$this->api->fe_renderTemplate($templateMarkers,'###BABIES###'),1,0,0,array('action'=>$this->pi_linkTP_keepPIvars_url(array('submit'=>'1')),'enctype'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],'method'=>'post'));
			
			// Return content wrapped in a CSS element
			return $this->api->fe_makeStyledContent('div','babies',$content);
		}
		
		/**
		 * 
		 */
		function formBabies() {
			
			// Storage
			$this->errors = array();
			
			// Check for FE data
			if (array_key_exists('submit',$this->piVars) && count($this->fedata[$this->prefixId])) {
				
				// Get image
				$this->getImageField('picture');
				
				// Get errors
				$this->checkBabiesErrors();
			}
				
			// Empty array
			$values = array('name'=>'','sex'=>'','birth'=>'','picture'=>'');
			
			// Check for edit flag
			if (array_key_exists('modify',$this->piVars)) {
				
				// Select record
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['babies'],'uid=' . $this->piVars['modify'] . ' AND feuser=' . $this->user['uid']);
				
				// Get record
				$baby = ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) ? $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) : false;
				
				// Check for article
				if (is_array($baby)) {
					
					// Set values
					$values['name'] = $baby['name'];
					$values['sex'] = $baby['sex'];
					$values['birth'] = $baby['birth'];
					$values['picture'] = $baby['picture'];
				}
			}
			
			// Check for errors
			if (array_key_exists('submit',$this->piVars) && !count($this->errors)) {
				
				// Now
				$now = time();
				
				// Data array
				$data = array(
					'name' => $this->fedata[$this->prefixId]['name'],
					'sex' => $this->fedata[$this->prefixId]['sex'],
					'birth' => strtotime($this->fedata[$this->prefixId]['birth']),
					'tstamp' => $now,
				);
				
				// Check for a picture
				if (array_key_exists('picture',$this->fedata[$this->prefixId]) && count($this->fedata[$this->prefixId]['picture'])) {
					
					// Upload as temp file
					$tmp = t3lib_div::upload_to_tempfile($this->fedata[$this->prefixId]['picture']['tmp_name']);
					
					// Storage directory (absolute)
					$storage = t3lib_div::getFileAbsFileName($this->uploadDir);
					
					// File extension
					$ext = (strrpos($this->fedata[$this->prefixId]['picture']['name'],'.')) ? substr($this->fedata[$this->prefixId]['picture']['name'],strrpos($this->fedata[$this->prefixId]['picture']['name'],'.'),strlen($this->fedata[$this->prefixId]['picture']['name'])) : false;
					
					// File name
					$fName = uniqid(rand()) . $ext;
					
					// Move file to final destination
					t3lib_div::upload_copy_move($tmp,$storage . $fName);
						
					// Set reference
					$data['picture'] = $fName;
					
					// Delete temp file
					t3lib_div::unlink_tempfile($tmp);
					
					// Delete old file
					@unlink(t3lib_div::getFileAbsFileName($this->uploadDir . $baby['picture']));
				}
				
				// Check for action
				if (array_key_exists('modify',$this->piVars)) {
					
					// Update record
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->extTables['babies'],'feuser=' . $this->user['uid'] . ' AND uid=' . $this->piVars['modify'],$data);
					
				} else if (array_key_exists('new',$this->piVars)) {
					
					// Add special fields
					$data['crdate'] = $now;
					$data['pid'] = $this->conf['storagePid'];
					$data['feuser'] = $this->user['uid'];
					
					// New record
					$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->extTables['babies'],$data);
				}
				
				// Clear members page cache
				$this->clearPageCache($this->conf['links.']['members']);
				
				// Confirmation
				return $this->api->fe_makeStyledContent('div','confirm',$this->pi_getLL('confirm'));
				
			} else {
				
				// Submit label
				$submitLabel = (array_key_exists('modify',$this->piVars)) ? $this->pi_getLL('submit.modify') : $this->pi_getLL('submit.new');
				
				// Fields
				$fields = array('name'=>'input','birth'=>'date','picture'=>'image','sex'=>'radio:2');
				
				// Template markers
				$templateMarkers = array();
				
				// Process each field
				foreach($fields as $key=>$value) {
					
					// Overwriting template markers
					$templateMarkers['###' . strtoupper($key) . '###'] = $this->writeInput($key,$values[$key],$this->conf['babies.'],$value);
				}
				
				// Submit
				$templateMarkers['###SUBMIT###'] = $this->api->fe_makeStyledContent('div','field','<input type="submit" id="submit" name="submit" value="' . $submitLabel. '" />');
				
				// Wrap all in a form
				$content = $this->api->fe_makeStyledContent('form','inputForm',$this->api->fe_renderTemplate($templateMarkers,'###BABIES_FORM###'),1,0,0,array('action'=>$this->pi_linkTP_keepPIvars_url(array('submit'=>'1')),'enctype'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'],'method'=>'post'));
				
				// Return content
				return $this->api->fe_makeStyledContent('div','baby-form',$content);
			}
		}
		
		/**
		 * 
		 */
		function checkBabiesErrors() {
			
			// Check required fields
			foreach($this->requiredFields as $req) {
				
				// Check FE data
				if ($req && (!array_key_exists($req,$this->fedata[$this->prefixId]) || (empty($this->fedata[$this->prefixId][$req]) && $this->fedata[$this->prefixId][$req] !== '0'))) {
					
					// Error
					$this->errors[$req] = $this->pi_getLL('errors.missing');
				}
			}
			
			// Special check for picture
			if (!array_key_exists('picture',$this->errors) && array_key_exists('picture',$this->fedata[$this->prefixId])) {
				
				// Load TCA for users
				t3lib_div::loadTCA($this->extTables['babies']);
				
				// Field settings
				$conf = $GLOBALS['TCA'][$this->extTables['babies']]['columns']['picture'];
				
				// Image name
				$pic = $this->fedata[$this->prefixId]['picture']['name'];
				
				// File extension
				$ext = (strrpos($pic,'.')) ? substr($pic,strrpos($pic,'.') + 1,strlen($pic)) : false;
				
				// Allowed extensions
				$allowedExt = explode(',',$conf['config']['allowed']);
				
				// Maximum size
				$maxSize = $conf['config']['max_size'];
				
				// Check image
				if (!$ext || !in_array($ext,$allowedExt) || !t3lib_div::verifyFilenameAgainstDenyPattern($pic)) {
					
					// Wrong type
					$this->errors['picture'] = $this->pi_getLL('errors.image.type');
					
				} else if (($this->fedata[$this->prefixId]['image']['size'] / 1024) > $maxSize) {
					
					// Wrong size
					$this->errors['picture'] = $this->pi_getLL('errors.image.size');
				}
			}
		}
		
		/**
		 * 
		 */
		function listBabies() {
			
			// Get babies
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->extTables['babies'],'deleted=0 AND feuser=' . $this->user['uid']);
			
			// Check ressource
			if ($res) {
				
				// Check for babies
				if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
					
					// Storage
					$htmlCode = array();
					
					// Process babies
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						
						// Storage
						$baby = array();
						
						// Check for a picture
						if (!empty($row['picture'])) {
							
							// Add picture
							$baby[] = $this->api->fe_makeStyledContent('div','picture',$this->api->fe_createImageObjects($row['picture'],$this->conf['babies.']['imgConf.'],$this->uploadDir));
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
						
						// Options
						$options = array(
							
							// Edit
							$this->api->fe_makeStyledContent('span','modify',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('edit'),array('modify'=>$row['uid']),array('new','delete','submit'),1)),
							
							// Delete
							$this->api->fe_makeStyledContent('span','delete',$this->api->fe_linkTP_unsetPIvars($this->pi_getLL('delete'),array('delete'=>$row['uid']),array('new','modify','submit'),1)),
						);
						
						// Add options
						$baby[] = $this->api->fe_makeStyledContent('div','options',implode(chr(10),$options));
						
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
		function writeInput($fieldName,$value,$conf,$type=false) {
			
			// Input ID
			$id = 'fedata[' . $this->prefixId . '][' . $fieldName . ']';
			
			// Label
			$label = $this->getFieldLabel($fieldName);
			
			// Get error if any
			$error = $this->getFieldError($fieldName);
			
			// Field type
			$fieldType = explode(':',$type);
			
			// Value
			if ($fieldType[0] != 'image' && isset($this->fedata) && array_key_exists($this->prefixId,$this->fedata) && array_key_exists($fieldName,$this->fedata[$this->prefixId])) {
				
				// Get value
				$value = ($type == 'date') ? strtotime($this->fedata[$this->prefixId][$fieldName]) : $this->fedata[$this->prefixId][$fieldName];
			}
			
			// Check field name
			switch($fieldType[0]) {
				
				// Address
				case 'text':
					
					// Textarea
					$input = $this->api->fe_makeStyledContent('div','field','<textarea id="' . $id . '" name="' . $id . '" rows="' . $conf['textareaRows'] . '" cols="' . $conf['textareaCols'] . '">' . $value . '</textarea>');
				break;
				
				// Date
				case 'date':
					
					// Date
					$date = (empty($value)) ? date('m/d/Y',time()) : date('m/d/Y',$value);
					
					// Text input
					$input = $this->api->fe_makeStyledContent('div','field','<input type="text" id="' . $id . '" name="' . $id . '" value="' . $date . '" size="' . $conf['inputSize'] . '" />');
				break;
				
				// Radio
				case 'radio':
					
					// Storage
					$radios = array();
					
					// Create radios
					for($i = 0; $i < $fieldType[1]; $i++) {
						
						// Selected state
						$selected = ($value == $i) ? 'checked="checked"' : '';
						
						// Radio input
						$radios[] = $this->api->fe_makeStyledContent('div','field','<input type="radio" ' . $selected . ' id="' . $id . '" name="' . $id . '" value="' . $i . '" size="' . $conf['inputSize'] . '" /> ' . $this->pi_getLL('headers.' . $fieldName . '.I.' . $i));
					}
					
					// Add radios
					$input = implode(chr(10),$radios);
				break;
				
				// Address
				case 'image':
					
					// Upload directory
					$uploadDir = ($fieldName == 'image') ? 'uploads/pics/' : $this->uploadDir;
					
					// Create thumbnail
					$img = (!empty($value)) ? $this->api->fe_makeStyledContent('div','picture',$this->api->fe_createImageObjects($value,$conf['imgConf.'],$uploadDir)) : '';
					
					// File
					$input = $this->api->fe_makeStyledContent('div','field',$img . '<input type="file" id="' . $id . '" name="' . $id . '" />');
				break;
				
				// Password
				case 'password':
					
					// Password input
					$input = $this->api->fe_makeStyledContent('div','field','<input type="password" id="' . $id . '[0]" name="' . $id . '[0]" size="' . $conf['inputSize'] . '" /><br /><input type="password" id="' . $id . '[1]" name="' . $id . '[1]" size="' . $conf['inputSize'] . '" />');
				break;
				
				// Default
				default:
					
					// Text input
					$input = $this->api->fe_makeStyledContent('div','field','<input type="text" id="' . $id . '" name="' . $id . '" value="' . $value . '" size="' . $conf['inputSize'] . '" />');
				break;
			}
			
			// Full field
			return $label . $error . $input;
		}
		
		/**
		 * 
		 */
		function getFieldLabel($fieldName) {
			
			// Storage
			$htmlCode = array();
			
			// Label
			$htmlCode[] = '<strong>' . $this->pi_getLL('headers.' . $fieldName) . '</strong>';
			
			// Check if field is required
			if (in_array($fieldName,$this->requiredFields)) {
				
				// Add marker
				$htmlCode[] = '<span>*</span>';
				
				// Label class
				$class = 'label-required';
				
			} else {
				
				// Label class
				$class = 'label';
			}
			
			// Return label
			return $this->api->fe_makeStyledContent('div',$class,implode(chr(10),$htmlCode));
		}
		
		/**
		 * 
		 */
		function getFieldError($fieldName) {
			
			if (is_array($this->errors) && array_key_exists($fieldName,$this->errors)) {
				
				// Label
				$msg = '<strong>' . $this->errors[$fieldName] . '</strong>';
				
				// Return label
				return $this->api->fe_makeStyledContent('div','error',$msg);
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
		function getImageField($name) {
			
			// Check for incoming picture
			if (array_key_exists('fedata',$_FILES)) {
				
				// Get image data
				$tmp = array(
					
					// Name
					'name' => $_FILES['fedata']['name'][$this->prefixId][$name],
					
					// Type
					'type' => $_FILES['fedata']['type'][$this->prefixId][$name],
					
					// Temp name
					'tmp_name' => $_FILES['fedata']['tmp_name'][$this->prefixId][$name],
					
					// Size
					'size' => $_FILES['fedata']['size'][$this->prefixId][$name],
				);
				
				// Check for valid image data
				if (!empty($tmp['name'])) {
					
					// Set FE data
					$this->fedata[$this->prefixId][$name] = $tmp;
				}
			}
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
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weleda_baby/pi4/class.tx_weledababy_pi4.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/weleda_baby/pi4/class.tx_weledababy_pi4.php']);
	}
?>
