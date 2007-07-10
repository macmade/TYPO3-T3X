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
	 * Plugin 'MP3 Player' for the 'fe_mp3player' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		2.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *     100:		function main($content,$conf)
	 *     133:		function setConfig
	 *     176:		function buildFlashCode
	 *     256:		function writeFlashObjectParams
	 *     278:		function createLinks
	 * 
	 *				TOTAL FUNCTIONS: 5
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_femp3player_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_femp3player_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_femp3player_pi1.php';
		
		// The extension key
		var $extKey = 'fe_mp3player';
		
		// Upload directory
		var $uploadDir = 'uploads/tx_femp3player/';
		
		// Version of the Developer API required
		var $apimacmade_version = 2.3;
		
		
		
		
		
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
		 * @see			setConfig
		 * @see			buildFlashCode
		 */
		function main($content,$conf) {
			
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Init flexform configuration of the plugin
			$this->pi_initPIflexForm();
			
			// Store flexform informations
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
			
			// Set final configuration (TS or FF)
			$this->setConfig();
			
			// Build content
			$content = $this->buildFlashCode();
			
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
				'playlist' => 'sDEF:playlist',
				'swfParams.' => array(
					'loop' => 'sFLASH:loop',
					'menu' => 'sFLASH:menu',
					'quality' => 'sFLASH:quality',
					'scale' => 'sFLASH:scale',
					'bgcolor' => 'sFLASH:bgcolor',
					'swliveconnect' => 'sFLASH:swliveconnect',
					'wmode' => 'sFLASH:wmode',
					'salign' => 'sFLASH:salign',
				),
				'playerParams.' => array(
					'useSkin' => 'sPLAYER:skin',
					'gskinColor' => 'sPLAYER:gskin_color',
					'autoStart' => 'sPLAYER:autostart',
					'showDisplay' => 'sPLAYER:showdisplay',
					'showPlaylist' => 'sPLAYER:showplaylist',
				),
				'width' => 'sFLASH:width',
				'height' => 'sFLASH:height',
				'version' => 'sFLASH:version',
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'MP3 Player: configuration array');
		}
		
		/**
		 * Returns the code for the flash file.
		 * 
		 * This function creates a JavaScript and VBScript detection for the Macromedia Flash Plugin.
		 * 
		 * @return		The complete HTML and JavaScript code used to display the flash file.
		 * @see			createLinks
		 * @see			writeFlashObjectParams
		 */
		function buildFlashCode() {
			
			// Get playlist record
			$this->playlist = $this->pi_getRecord('tx_femp3player_playlists',$this->conf['playlist']);
			
			// Check playlist
			if (is_array($this->playlist)) {
				
				// Get player path
				$player = $this->conf['skins.'][$this->conf['playerParams.']['useSkin']];
				
				// Creating valid pathes for the MP3 player
				$swfPath = str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($player));
				
				// Create XML file location
				$xmlFile = 'index.php?id=' . $GLOBALS['TSFE']->id . '&type=' . $this->conf['xmlPageId'] . '&playlist=' . $this->conf['playlist'] . '&autoStart=' . $this->conf['playerParams.']['autoStart'] . '&showPlaylist=' . $this->conf['playerParams.']['showPlaylist'] . '&showDisplay=' . $this->conf['playerParams.']['showDisplay'] . '&gskinColor=' . $this->conf['playerParams.']['gskinColor'];
				
				// Add FlashVars param to TS
				$this->conf['swfParams.']['FlashVars'] = 'playlist=' . urlencode($xmlFile);
				
				// Add movie param to TS
				$this->conf['swfParams.']['movie'] = $swfPath;
				
				// Storage
				$htmlCode = array();
				
				// Replacement code
				$noFlash = $this->createLinks();
				
				// Flash code
				$htmlCode[] = '
					<!-- URL\'s used in the movie-->
					<!-- text used in the movie-->
					<script type="text/javascript" language="Javascript" charset="iso-8859-1">
						<!--
						var MM_contentVersion = ' . $this->conf['version'] . ';
						var plugin = (navigator.mimeTypes && navigator.mimeTypes[\'application/x-shockwave-flash\']) ? navigator.mimeTypes[\'application/x-shockwave-flash\'].enabledPlugin : 0;
						if (plugin) {
							var words = navigator.plugins[\'Shockwave Flash\'].description.split(\' \');
							for (i = 0; i < words.length; i++) {
								if (isNaN(parseInt(words[i]))) {
									continue;
								}
								var MM_PluginVersion = words[i]; 
							}
							var MM_FlashCanPlay = MM_PluginVersion >= MM_contentVersion;
						}
						else if (navigator.userAgent && navigator.userAgent.indexOf(\'MSIE\') >=0 && (navigator.appVersion.indexOf(\'Win\') != -1)) {
							document.write(\'<script type="text\/vbscript" language="VBScript" charset="iso-8859-1">\n\');
							document.write(\'on error resume next \n\');
							document.write(\'MM_FlashCanPlay = (IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash." & MM_contentVersion)))\n\');
							document.write(\'<\/script>\n\');
						}
						if (MM_FlashCanPlay) {
							document.write(\'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http:\/\/download.macromedia.com\/pub\/shockwave\/cabs\/flash\/swflash.cab#version=' . $this->conf['version'] . ',0,0,0" id="' . $this->prefixId . '" width="' . $this->conf['width'] . '" height="' . $this->conf['height'] . '" align="center">\n\');
							' . $this->writeFlashObjectParams() . '
							document.write(\'<embed src="' . $swfPath . '" FlashVars="' . $this->conf['swfParams.']['FlashVars'] . '" swliveconnect="' . $this->conf['swfParams.']['swliveconnect'] . '" loop="' . $this->conf['swfParams.']['loop'] . '" menu="' . $this->conf['swfParams.']['menu'] . '" quality="' . $this->conf['swfParams.']['quality'] . '" scale="' . $this->conf['swfParams.']['scale'] . '" salign="' . $this->conf['swfParams.']['salign'] . '" wmode="' . $this->conf['swfParams.']['wmode'] . '" bgcolor="' . $this->conf['swfParams.']['bgcolor'] . '" width="' . $this->conf['width'] . '" height="' . $this->conf['height'] . '" name="' . $this->prefixId . '" align="top" type="application\/x-shockwave-flash" pluginspage="http:\/\/www.macromedia.com\/go\/getflashplayer">\n\');
							document.write(\'<\/embed>\n\');
							document.write(\'<\/object>\n\');
						}
						else {
							document.write(\'' . $noFlash . '\');
						}
					//-->
					</script>';
				
				// Return content
				return implode(chr(10),$htmlCode);
			}
		}
		
		/**
		 * Returns param tags
		 * 
		 * This function creates a param tag for each parameter specified in the
		 * setup field, wrapped in a JavaScript write() instruction, for use with
		 * the flash detection code.
		 * 
		 * @return		A param tag for each parameter.
		 */
		function writeFlashObjectParams() {
			
			// Storage
			$params = array();
			
			// Build HTML <param> tags from TS setup
			foreach($this->conf['swfParams.'] as $name => $value) {
				$params[] = 'document.write(\'<param name="' . $name . '" value="' . $value . '">\');';
			}
			
			// Return tags
			return implode(chr(10),$params);
		}
		
		/**
		 * Creates a list of MP3 files
		 * 
		 * This function creates a list of the MP3 files, with links and filesizes.
		 * This function is used if the Flash plugin is not found.
		 * 
		 * @return		Void
		 */
		function createLinks() {
			
			// HTML code storage
			$htmlCode = array();
			
			// Check type
			if ($this->playlist['type'] == 2) {
				
				// Get PodCast XML content
				$podCastXML = @file_get_contents($this->playlist['podcast_url']);
				
				// Check XML content
				if ($podCastXML) {
					
					// Convert XML to a PHP array
					$podCast = $this->api->div_xml2array($podCastXML);
					
					// Check XML structure
					if ($this->api->div_checkArrayKeys($podCast,'rss,channel')) {
						
						// Start table
						$htmlCode[] = '<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">';
						
						// Process PodCast items
						foreach($podCast['rss']['channel'] as $key=>$value) {
							
							// Check if XML node contains a link section
							if (substr($key,0,4) == 'item' && $this->api->div_checkArrayKeys($value,'link')) {
								
								// Podcast file
								$podCastFile = $value['link'];
								
								// Check if file has .mp3 extension
								if (strrpos($podCastFile,'.mp3') == (strlen($podCastFile) - 4)) {
									
									// check for a title
									if (array_key_exists('title',$value) && $value['title'] != '') {
										
										// MP3 title
										$mp3_title = $value['title'];
										
									} else {
										
										// Explode path segments
										$path_info = explode('/',$podCastFile);
										
										// MP3 title
										$mp3_title = array_pop($path_info);
									}
									
									// Start line
									$htmlCode[] = '<tr>';
									
									// Add title
									$htmlCode[] = '<td width="100%" align="left" valign="top"><a href="' . $podCastFile . '" target="_blank">' . $mp3_title . '</a></td>';
									
									// End line
									$htmlCode[] = '</tr>';
								}
							}
						}
						
						// End table
						$htmlCode[] = '</table>';
					}
				}
				
			} else if ($this->playlist['type'] == 1) {
				
				// Get directory path
				$uploadDir = str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($this->playlist['dir_path']));
				
				// Check for a trailing slash
				if (strrpos($uploadDir,'/') != strlen($uploadDir) - 1) {
					
					// Add trailing slash
					$uploadDir .= '/';
				}
				
				// Get an array with songs
				$songsArray = explode(',',$this->playlist['dir_songs']);
				
				// Unify linebreaks for titles
				$titles = tx_apimacmade::div_convertLineBreaks($this->playlist['dir_titles']);
				
				// Get an array with titles
				$titlesArray = explode(chr(10),$titles);
				
				// Check for files
				if (count($songsArray)) {
					
					// Start table
					$htmlCode[] = '<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">';
					
					// Process each MP3 file
					foreach($songsArray as $key=>$mp3File) {
						
						// MP3 path
						$mp3_path = $uploadDir . $mp3File;
						
						// Get raw title
						$mp3_title = (is_array($titlesArray) && array_key_exists($key,$titlesArray) && $titlesArray[$key] != '') ? $titlesArray[$key] : $mp3File;
						
						// Check if file exists
						if (@is_file(t3lib_div::getFileAbsFileName($mp3_path))) {
							
							// Start line
							$htmlCode[] = '<tr>';
							
							// Add title
							$htmlCode[] = '<td width="90%" align="left" valign="top"><a href="' . $mp3_path . '" target="_blank">' . $mp3_title . '</a></td>';
							
							// Compute file size
							$fileSize = ((@filesize(t3lib_div::getFileAbsFileName($mp3_path)) / 1024) / 1024);
							
							// Add size
							$htmlCode[] = '<td width="10%" align="left" valign="top">' . round($fileSize,2) . ' MB</td>';
							
							// End line
							$htmlCode[] = '</tr>';
						}
					}
					
					// End table
					$htmlCode[] = '</table>';
				}
				
			} else {
				
				// Get an array with songs
				$songsArray = $this->api->div_xml2array($this->playlist['playlist']);
				
				// Check array
				if ($this->api->div_checkArrayKeys($songsArray,'T3FlexForms,data,sDEF,lDEF,fields,el')) {
					
					// Start table
					$htmlCode[] = '<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">';
					
					// Process each songs
					foreach($songsArray['T3FlexForms']['data']['sDEF']['lDEF']['fields']['el'] as $mp3File) {
						
						// Check element type
						if ($this->api->div_checkArrayKeys($mp3File,'mp3,el,file,vDEF')) {
							
							// Absoulte path of mp3 file
							$filePath = t3lib_div::getFileAbsFileName($this->uploadDir . $mp3File['mp3']['el']['file']['vDEF']);
							
							if (@is_file($filePath)) {
								
								// MP3 title
								$mp3_title = ($this->api->div_checkArrayKeys($mp3File,'mp3,el,title,vDEF')) ? $mp3File['mp3']['el']['title']['vDEF'] : $mp3File['mp3']['el']['file']['vDEF'];
								
								// Start line
								$htmlCode[] = '<tr>';
								
								// Add title
								$htmlCode[] = '<td width="90%" align="left" valign="top"><a href="' . $this->uploadDir . $mp3File['mp3']['el']['file']['vDEF'] . '" target="_blank">' . $mp3_title . '</a></td>';
								
								// Compute file size
								$fileSize = ((@filesize($filePath) / 1024) / 1024);
								
								// Add size
								$htmlCode[] = '<td width="10%" align="left" valign="top">' . round($fileSize,2) . ' MB</td>';
								
								// End line
								$htmlCode[] = '</tr>';
							}
						} else if ($this->api->div_checkArrayKeys($mp3File,'path,el,path,vDEF')) {
							
							// Absoulte path of mp3 file
							$filePath = t3lib_div::getFileAbsFileName($mp3File['path']['el']['path']['vDEF']);
							
							if (@is_file($filePath)) {
								
								// Check for a title
								if ($this->api->div_checkArrayKeys($mp3File,'path,el,title,vDEF')) {
									
									// MP3 title
									$mp3_title = $mp3File['path']['el']['title']['vDEF'];
									
								} else {
									
									// Explode path segments
									$path_info = explode('/',$mp3File['path']['el']['path']['vDEF']);
									
									// MP3 title
									$mp3_title = array_pop($path_info);
								}
								
								// Start line
								$htmlCode[] = '<tr>';
								
								// Add title
								$htmlCode[] = '<td width="90%" align="left" valign="top"><a href="' . $mp3File['path']['el']['path']['vDEF'] . '" target="_blank">' . $mp3_title . '</a></td>';
							
								// Compute file size
								$fileSize = ((@filesize($filePath) / 1024) / 1024);
								
								// Add size
								$htmlCode[] = '<td width="10%" align="left" valign="top">' . round($fileSize,2) . ' MB</td>';
								
								// End line
								$htmlCode[] = '</tr>';
							}
						}
					}
				}
				
				// End table
				$htmlCode[] = '</table>';
			}
			
			// Return HTML code
			return addslashes(implode('',$htmlCode));
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fe_mp3player/pi1/class.tx_femp3player_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fe_mp3player/pi1/class.tx_femp3player_pi1.php']);
	}
?>
