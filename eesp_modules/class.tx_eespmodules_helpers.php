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
	 * Class/Function which manipulates the item-array for flex:files field
	 * of table tx_eespmodules_programs.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      62:		function handleYears(&$params,&$pObj)
	 *     112:		function handleWeeks(&$params,&$pObj)
	 *     148:		function handleFiles(&$params,&$pObj)
	 * 
	 *				TOTAL FUNCTIONS: 3
	 */
	
	class tx_eespmodules_helpers {
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Base functions.
		 ***************************************************************/
		
		/**
		 * Adds items to the year selector.
		 * 
		 * This function reads all the years directories in the defined
		 * modules directory, and adds the references to the selector.
		 * 
		 * @param		&$params			The parameters of the form
		 * @param		&$pObj				Reference to the parent object
		 * @return		Void
		 */
		function handleYears(&$params,&$pObj) {
			
			// Conf
			$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['eesp_modules']);
			
			// Path Values
			$readPath = t3lib_div::getFileAbsFileName($confArray['modulesDir']);
			
			if (@is_dir($readPath)) {
				
				// Gets all directories
				$yearsDir = t3lib_div::get_dirs($readPath);
				
				// Sort directories
				sort($yearsDir);
				
				// Check for years
				if (count($yearsDir)) {
					
					// Get first year
					$start = explode('_',$yearsDir[0]);
					
					// Get last year
					$end = explode('_',$yearsDir[count($yearsDir) - 1]);
				}
				
				// Add necessary previous years
				for($i = 1; $i < 4; $i++) {
					
					// Add year
					$yearDirs[] = $start[0] - $i;
				}
				
				// Sort directories
				sort($yearsDir);
				
				// Process years
				foreach($yearsDir as $val) {
					
					// Year
					$year = explode('_',$val);
					
					// Label
					$label = (array_key_exists(1,$year)) ? $year[0] . ' (' . $year[1] . ')' : $year[0];
					
					// Adds items
					$params['items'][] = array($label,$val);
				}
			}
		}
		
		/**
		 * Adds items to the week selector.
		 * 
		 * This function add items to the week selector. The order of the
		 * weeks depends from the starting week selected in the current record.
		 * 
		 * @param		&$params			The parameters of the form
		 * @param		&$pObj				Reference to the parent object
		 * @return		Void
		 */
		function handleWeeks(&$params,&$pObj) {
			
			// Storage
			$params['items'] = array();
			
			// Start week
			$start = $params['row']['term1_startweek'];
			
			// Process weeks
			for ($i == 0; $i < 53; $i++) {
				
				// Add week to selector
				$params['items'][] = array($start,$i);
				
				// Increase start
				$start += 1;
				
				// Check start
				if ($start > 53) {
					
					// Reset start
					$start = 1;
				}
			}
		}
		
		/**
		 * Adds items to the module selector.
		 * 
		 * This function reads all the files in the defined modules
		 * directory, and adds the references to the selector.
		 * 
		 * @param		&$params			The parameters of the form
		 * @param		&$pObj				Reference to the parent object
		 * @return		Void
		 */
		function handleFiles(&$params,&$pObj) {
			
			// Storage
			$params['items'] = array();
			
			// Conf
			$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['eesp_modules']);
			
			// Year
			$year = explode('_',$params['row']['startyear']);
			
			// Directory storage
			$dirs = array();
			
			// Process years
			for($i = $year[0]; $i < $year[0] + 4; $i++) {
				
				// Year path
				$yearPath = ($year[1]) ? $i . '_' . $year[1] : $i;
				
				// Modules directory
				$dirs[] = t3lib_div::fixWindowsFilePath($confArray['modulesDir'] . '/' . $yearPath);
			}
			
			// Process directories
			foreach($dirs as $fullPath) {
				
				// Path Values
				$readPath = t3lib_div::getFileAbsFileName($fullPath);
				
				if (@is_dir($readPath)) {
					
					// Gets all XML files
					$files = t3lib_div::getFilesInDir($readPath,'xml');
					
					// Process files
					foreach($files as $file) {
						
						// Do not include index files
						if ($file != 'index.xml') {
							
							// Adds items
							$params['items'][] = array(str_replace('.xml','',$file),$file);
						}
					}
				}
			}
		}
	}
	
	/**
	 * XClass inclusion.
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_modules/class.tx_eespmodules_helpers.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_modules/class.tx_eespmodules_helpers.php']);
	}
?>
