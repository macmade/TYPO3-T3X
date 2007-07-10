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
	
	class tx_friheaders_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_friheaders_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_ffriheadersr_pi1.php';
		
		// The extension key
		var $extKey = 'fri_headers';
		
		// Upload directory
		var $uploadDir = 'uploads/tx_friheaders/';
		
		
		
		
		
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
			
			// Build content
			$content = $this->buildHeaderCode();
			
			// Return content
			return $this->pi_wrapInBaseClass($content);
		}
		
		/**
		 * 
		 */
		function buildHeaderCode() {
			
			// Storage
			$htmlCode = array();
			
			// Get random pictures
			$randomPics = t3lib_div::getFilesInDir(PATH_site . $this->conf['randomDir'],'jpg,jpeg,gif,png');
			
			// Try to get fixed picture
			$fixed = $this->getHeaderFile('tx_friheaders_picture');
			
			// Header pictures
			$pics = array();
			
			// Check if a fixed picture is present
			if ($fixed) {
				
				// Get 2 random pictures
				$randIndex = array_rand($randomPics,2);
				
				// Add fixed picture
				$pics[] = $this->uploadDir . $fixed;
				
			} else {
				
				// Get 3 random pictures
				$randIndex = array_rand($randomPics,3);
			}
			
			// Add random pics
			foreach($randIndex as $index) {
				
				// Add picture
				$pics[] = $this->conf['randomDir'] . $randomPics[$index];
			}
			
			// Shuffle pictures
			$header = shuffle($pics);
			
			// Process header pictures
			foreach($pics as $headerPic) {
				
				// Build picture
				$htmlCode[] = $this->buildPicture($headerPic);
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function getHeaderFile($field) {
			
			// Check if page has a header
			if (empty($GLOBALS['TSFE']->page[$field])) {
				
				// Get recursive header
				foreach($GLOBALS['TSFE']->config['rootLine'] as $topPage) {
					
					// Recursive header found
					if (!empty($topPage[$field])) {
						$headerFile = $topPage[$field];
					}
				}
			} else {
				
				// Page specific header
				$headerFile = $GLOBALS['TSFE']->page[$field];
			}
			
			// Return header file
			return $headerFile;
		}
		
		/**
		 * 
		 */
		function buildPicture($src) {
			
			// Configuration array
			$conf = array(
				
				// Source
				'file' => $src,
				
				// Parameters
				'file.' => $this->conf['picture.'],
			);
			
			// Return picture
			return $this->cObj->IMAGE($conf);
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fri_headers/pi1/class.tx_friheaders_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fri_headers/pi1/class.tx_friheaders_pi1.php']);
	}
?>
