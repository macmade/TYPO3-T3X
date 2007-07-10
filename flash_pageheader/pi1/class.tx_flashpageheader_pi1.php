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
	 * Plugin 'Flash header' for the 'flash_pageheader' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      93:		function main($content,$conf)
	 *     120:		function buildHeaderCode
	 *     195:		function getHeaderFile($field)
	 *     233:		function createImgFromTS($picturePath)
	 *     267:		function writeFlashObjectParams
	 * 
	 *				TOTAL FUNCTIONS: 5
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	class tx_flashpageheader_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_flashpageheader_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_flashpageheader_pi1.php';
		
		// The extension key
		var $extKey = 'flash_pageheader';
		
		// Upload directory
		var $uploadDir = 'uploads/tx_flashpageheader/';
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_flashpageheader_pi1", and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 * @see			buildHeaderCode
		 */
		function main($content,$conf) {
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// Build content
			$content = $this->buildHeaderCode();
			
			// Return content
			return $this->pi_wrapInBaseClass($content);
		}
		
		/**
		 * Returns the code for the flash file.
		 * 
		 * This function creates a JavaScript and VBScript detection for the Macromedia Flash Plugin.
		 * If a flash file is not specified for the current page, it display the default one. If the flash plugin
		 * is not found, it display a replacement picture, either the specified or the default one.
		 * 
		 * @return		The complete HTML and JavaScript code used to display the flash file.
		 * @see			getHeaderFile
		 * @see			createImgFromTS
		 * @see			writeFlashObjectParams
		 */
		function buildHeaderCode() {
			
			// Get header files
			$swf = $this->getHeaderFile('tx_flashpageheader_swf');
			$picture = $this->getHeaderFile('tx_flashpageheader_picture');
			
			// Creating valid pathes
			$swfPath = str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($swf));
			$picturePath = str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($picture));
			
			// Create an absolute path for the XML file
			$xmlPagePath = str_replace($_SERVER['DOCUMENT_ROOT'],'', PATH_site);
			
			// Add FlashVars param to TS
			$this->conf['swfParams.']['FlashVars'] = 'xmlPageId=' . $GLOBALS['TSFE']->id . '&xmlPageType=' . $this->conf['xmlPageId'] . '&xmlPagePath=' . $xmlPagePath;
			
			// Add movie param to TS
			$this->conf['swfParams.']['movie'] = $swfPath;
			
			// Storage
			$htmlCode = array();
			
			// Replacement image
			$imgTSConfig = $this->createImgFromTS($picturePath);
			
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
						document.write(\'<embed src="' . $swfPath . '" FlashVars="' . $this->conf['swfParams.']['FlashVars'] . '" swliveconnect="' . $this->conf['swfParams.']['swliveconnect'] . '" loop="' . $this->conf['swfParams.']['loop'] . '" menu="' . $this->conf['swfParams.']['menu'] . '" quality="' . $this->conf['swfParams.']['quality'] . '" scale="' . $this->conf['swfParams.']['scale'] . '" bgcolor="' . $this->conf['swfParams.']['bgcolor'] . '" width="' . $this->conf['width'] . '" height="' . $this->conf['height'] . '" name="' . $this->prefixId . '" align="top" type="application\/x-shockwave-flash" pluginspage="http:\/\/www.macromedia.com\/go\/getflashplayer">\n\');
						document.write(\'<\/embed>\n\');
						document.write(\'<\/object>\n\');
					}
					else {
						document.write(\'' . $this->cObj->IMAGE($imgTSConfig) . '\');
					}
				//-->
				</script>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Return the header file
		 * 
		 * This function checks if a header file is associated with the page. If not,
		 * depending of the plugin settings, it checks for a recursive header in the top
		 * pages, or returns the default header file.
		 * 
		 * @return		The header file path
		 */
		function getHeaderFile($field) {
			
			if (empty($GLOBALS['TSFE']->page[$field])) {
				
				// Default header
				$headerFile = ($field == 'tx_flashpageheader_swf') ? $this->conf['defaultSwf'] : $this->conf['defaultPicture'];
				
				// Checking for a recursive header
				if ($this->conf['recursive'] == 1) {
					
					foreach($GLOBALS['TSFE']->config['rootLine'] as $topPage) {
						
						// Recursive header found
						if (!empty($topPage[$field])) {
							$headerFile = $this->uploadDir . $topPage[$field];
						}
					}
				}
			} else {
				
				// Page specific header
				$headerFile = $this->uploadDir . $GLOBALS['TSFE']->page[$field];
			}
			
			// Return header file
			return $headerFile;
		}
		
		/**
		 * Returns an image ressource array.
		 * 
		 * This function creates the full CObject array for the replacement picture. It
		 * takes values from the constants and setup fields, and adds the specified
		 * picture field to the array.
		 * 
		 * @param		$picturePath			The path of the picture to create
		 * @return		The image ressource array
		 */
		function createImgFromTS($picturePath) {
			
			// Get image CObject form TS
			$imgTSConfig = $this->conf['noFlash.'];
			
			// Adding XY parameters
			$imgTSConfig['file.']['XY'] = $this->conf['width'] . ',' . $this->conf['height'];
			
			// Cheking for existing IMAGE object in <noFlash.10>
			if (array_key_exists('10',$imgTSConfig)) {
				
				// Delete existing object to avoid configuration problems
				unset($imgTSConfig['10']);
			}
			
			// Add IMAGE object
			$imgTSConfig['file.']['10'] = 'IMAGE';
			
			// Add IMAGE file
			$imgTSConfig['file.']['10.']['file'] = $picturePath;
			
			// Return IMAGE CObject
			return $imgTSConfig;
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
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flash_pageheader/pi1/class.tx_flashpageheader_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flash_pageheader/pi1/class.tx_flashpageheader_pi1.php']);
	}
?>
