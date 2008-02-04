<?php
	/***************************************************************
	 * Copyright notice
	 *
	 * (c) 2005 macmade.net
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
	 * Plugin 'Hyper Navigation System' for the 'hypernav' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      91:		function main($content,$conf)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
	// Typo3 menu classes
	require_once(PATH_tslib.'media/scripts/tmenu_layers.php');
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	class tx_hypernav_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_hypernav_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_hypernav_pi1.php';
		
		// The extension key
		var $extKey = 'hypernav';
		
		// Check CHash
		var $pi_checkCHash = TRUE;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_dropdownsitemap_pi1", and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 */
		function main($content,$conf) {
			
			// Storage
			$htmlCode = array();
			
			// New instance of the tslib_tmenu class
			$menu = t3lib_div::makeInstance('tslib_tmenu_layers');
			
			// Menu options
			$GLOBALS['TSFE']->register['count_HMENU']++;
			$GLOBALS['TSFE']->register['count_HMENU_MENUOBJ'] = 0;
			$GLOBALS['TSFE']->applicationData['GMENU_LAYERS']['WMid'] = array();
			$GLOBALS['TSFE']->applicationData['GMENU_LAYERS']['WMparentId'] = array();
			
			// Set some internal vars
			$menu->parent_cObj = $this;
			
			// Menu configuration
			$mconf = array(
				
				// Special property
				'special' => 'rootline',
				
				// Add first level
				'1' => 'TMENU_LAYERS',
				'1.' => $conf['firstLevel.'],
			);
			
			// Checks for a range
			if( $this->conf[ 'firstLevel.' ][ 'range' ] ) {
				
				// Adds the range
				$mconf[ 'special.' ] = array(
					'range' => $conf[ 'firstLevel.' ][ 'range' ]
				);
			}
			
			// Force expAll property
			$mconf['1.']['expAll'] = 1;
			
			// Build each level
			for ($i = 0; $i < $conf['showLevels']; $i++) {
				
				// New menu object
				$mconf[2 + $i] = 'TMENU_LAYERS';
				
				// Menu configuration
				$mconf[(2 + $i) . '.'] = $conf['subLevels.'];
				
				// Force expAll property
				$mconf[(2 + $i) . '.']['expAll'] = 1;
			}
			
			// DEBUG ONLY - Show complete menu configuration
			#t3lib_div::debug($mconf);
			
			// Class constructor
			$menu->start($GLOBALS['TSFE']->tmpl,$GLOBALS['TSFE']->sys_page,0,$mconf,1);
				
			// Make the menu
			$menu->makeMenu();
			
			// Write the full menu with sub-items
			$htmlCode[] = $menu->writeMenu();
			
			// Return content
			return $this->pi_wrapInBaseClass(implode(chr(10),$htmlCode));
		}
	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/hypernav/pi1/class.tx_hypernav_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/hypernav/pi1/class.tx_hypernav_pi1.php']);
	}
?>
