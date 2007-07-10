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
	 * Class/Function which manipulates the item-array for field nbo_podcast
	 * of table tx_femp3player_playlists.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      59:		function main(&$params,&$pObj)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
	class tx_femp3player_handlePodCastFiles {
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Base functions.
		 ***************************************************************/
		
		/**
		 * Adds items to the stylesheet selector.
		 * 
		 * This function reads all the PodCast files in the defined
		 * directory, and adds the references to the selector.
		 * 
		 * @param		&$params			The parameters of the form
		 * @param		&$pObj				Reference to the parent object
		 * @return		Void
		 */
		function main(&$params,&$pObj) {
			
			// Get storage directory
			$readPath = t3lib_div::getFileAbsFileName('nbo_podcast');
			
			if (@is_dir($readPath)) {
				
				// Gets all MP3 files
				$xmlFiles = t3lib_div::getFilesInDir($readPath,'',1,1);
				
				foreach($xmlFiles as $xml) {
					
					// Do not include index.html
					if (basename($xml) != 'index.html') {
						
						// Reset
						$selectorBoxItem_title = '';
						
						// Adds items
						$selectorBoxItem_title = basename($xml);
						$params['items'][] = array(
							$selectorBoxItem_title,
							basename($xml)
						);
					}
				}
			}
		}
		
		/**
		 * Error message
		 * 
		 * This function returns an error message.
		 * 
		 * @param		&$params			The parameters of the form
		 * @param		&$pObj				Reference to the parent object
		 * @return		An error message
		 */
		function warning(&$params,&$pObj) {
			global $LANG;
			
			// Include locallang file
			$LANG->includeLLFile('EXT:fe_mp3player/locallang_db.php');
			
			// Error message
			return '<span class="typo3-red">' . $LANG->getLL('tx_femp3player_playlists.nbo_podcast_false.message') . '</span>';
		}
	}
	
	/**
	 * XClass inclusion.
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fe_mp3player/class.tx_femp3player_handlepodcastfiles.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fe_mp3player/class.tx_femp3player_handlepodcastfiles.php']);
	}

?>
