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
	 * Class/Function which manipulates the item-array for field dir_songs
	 * of table tx_femp3player_playlists.
	 *
	 * @author		Jean-David Gadina (info@macmade.net
	 * @version		1.1
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      60:		function main(&$params,&$pObj)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
	class tx_femp3player_handleMP3Files {
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Base functions.
		 ***************************************************************/
		
		/**
		 * Adds items to the stylesheet selector.
		 * 
		 * This function reads all the MP3 files in the defined
		 * directory, and adds the references to the selector.
		 * 
		 * @param		&$params			The parameters of the form
		 * @param		&$pObj				Reference to the parent object
		 * @return		Nothing
		 */
		function main(&$params,&$pObj) {
			
			// Get storage directory
			$readPath = t3lib_div::getFileAbsFileName($params['row']['dir_path']);
			
			// Check for a real directory
			if (@is_dir($readPath)) {
				
				// Gets all MP3 files
				$mp3Files = t3lib_div::getFilesInDir($readPath,'mp3',1,1);
				
				// Check array
				if (is_array($mp3Files)) {
					
					// Process files
					foreach($mp3Files as $mp3) {
						
						// Reset
						$selectorBoxItem_title = '';
						
						// Adds items
						$selectorBoxItem_title = basename($mp3);
						$params['items'][] = array(
							$selectorBoxItem_title,
							basename($mp3)
						);
					}
				}
			}
		}
	}
	
	/**
	 * XClass inclusion.
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fe_mp3player/class.tx_femp3player_handlemp3files.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fe_mp3player/class.tx_femp3player_handlemp3files.php']);
	}

?>
