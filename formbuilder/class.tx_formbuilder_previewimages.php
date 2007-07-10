<?php
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2004 Jean-David Gadina (macmade@gadlab.net)
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
	 * Class/Function which show preview images in the 'xmlds' flex field
	 * of table 'tx_formbuilder_datastructure'.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      73:		function textInput($PA,$fObj)
	 *      93:		function textarea($PA,$fObj)
	 *     113:		function checkboxes($PA,$fObj)
	 *     133:		function radioButtons($PA,$fObj)
	 *     153:		function selectMenu($PA,$fObj)
	 *     173:		function databaseRelation($PA,$fObj)
	 *     193:		function files($PA,$fObj)
	 *     213:		function password($PA,$fObj)
	 *     230:		function createImage($imgPath)
	 * 
	 *				TOTAL FUNCTIONS: 9
	 */
	
	class tx_formbuilder_previewImages {
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - Main
		 *
		 * Base functions.
		 ***************************************************************/
		
		/**
		 * Returns a picture
		 * 
		 * This function returns the preview picture for a text input field.
		 * 
		 * @param		$PA					The current rendered field confuguration
		 * @param		$fObj				The parent object (t3lib_TCEforms)
		 * @return		An <img> tag
		 * @see			createImage
		 * 
		 */
		function textInput($PA,$fObj) {
			
			// Create image path
			$imgPath = t3lib_extMgm::extRelPath('formbuilder') . 'csh/text_input.gif';
			
			// Return image tag
			return $this->createImage($imgPath);
		}
		
		/**
		 * Returns a picture
		 * 
		 * This function returns the preview picture for a text input field.
		 * 
		 * @param		$PA					The current rendered field confuguration
		 * @param		$fObj				The parent object (t3lib_TCEforms)
		 * @return		An <img> tag
		 * @see			createImage
		 * 
		 */
		function textarea($PA,$fObj) {
			
			// Create image path
			$imgPath = t3lib_extMgm::extRelPath('formbuilder') . 'csh/textarea.gif';
			
			// Return image tag
			return $this->createImage($imgPath);
		}
		
		/**
		 * Returns a picture
		 * 
		 * This function returns the preview picture for a text input field.
		 * 
		 * @param		$PA					The current rendered field confuguration
		 * @param		$fObj				The parent object (t3lib_TCEforms)
		 * @return		An <img> tag
		 * @see			createImage
		 * 
		 */
		function checkboxes($PA,$fObj) {
			
			// Create image path
			$imgPath = t3lib_extMgm::extRelPath('formbuilder') . 'csh/checkboxes.gif';
			
			// Return image tag
			return $this->createImage($imgPath);
		}
		
		/**
		 * Returns a picture
		 * 
		 * This function returns the preview picture for a text input field.
		 * 
		 * @param		$PA					The current rendered field confuguration
		 * @param		$fObj				The parent object (t3lib_TCEforms)
		 * @return		An <img> tag
		 * @see			createImage
		 * 
		 */
		function radioButtons($PA,$fObj) {
			
			// Create image path
			$imgPath = t3lib_extMgm::extRelPath('formbuilder') . 'csh/radio_buttons.gif';
			
			// Return image tag
			return $this->createImage($imgPath);
		}
		
		/**
		 * Returns a picture
		 * 
		 * This function returns the preview picture for a text input field.
		 * 
		 * @param		$PA					The current rendered field confuguration
		 * @param		$fObj				The parent object (t3lib_TCEforms)
		 * @return		An <img> tag
		 * @see			createImage
		 * 
		 */
		function selectMenu($PA,$fObj) {
			
			// Create image path
			$imgPath = t3lib_extMgm::extRelPath('formbuilder') . 'csh/select.gif';
			
			// Return image tag
			return $this->createImage($imgPath);
		}
		
		/**
		 * Returns a picture
		 * 
		 * This function returns the preview picture for a text input field.
		 * 
		 * @param		$PA					The current rendered field confuguration
		 * @param		$fObj				The parent object (t3lib_TCEforms)
		 * @return		An <img> tag
		 * @see			createImage
		 * 
		 */
		function databaseRelation($PA,$fObj) {
			
			// Create image path
			$imgPath = t3lib_extMgm::extRelPath('formbuilder') . 'csh/database_relation.gif';
			
			// Return image tag
			return $this->createImage($imgPath);
		}
		
		/**
		 * Returns a picture
		 * 
		 * This function returns the preview picture for a text input field.
		 * 
		 * @param		$PA					The current rendered field confuguration
		 * @param		$fObj				The parent object (t3lib_TCEforms)
		 * @return		An <img> tag
		 * @see			createImage
		 * 
		 */
		function files($PA,$fObj) {
			
			// Create image path
			$imgPath = t3lib_extMgm::extRelPath('formbuilder') . 'csh/files.gif';
			
			// Return image tag
			return $this->createImage($imgPath);
		}
		
		/**
		 * Returns a picture
		 * 
		 * This function returns the preview picture for a text input field.
		 * 
		 * @param		$PA					The current rendered field confuguration
		 * @param		$fObj				The parent object (t3lib_TCEforms)
		 * @return		An <img> tag
		 * @see			createImage
		 * 
		 */
		function password($PA,$fObj) {
			
			// Create image path
			$imgPath = t3lib_extMgm::extRelPath('formbuilder') . 'csh/password.gif';
			
			// Return image tag
			return $this->createImage($imgPath);
		}
		
		/**
		 * Creates an <img> tag
		 * 
		 * This function creates an HTML <img> tag, with a specified path.
		 * 
		 * @param		$imgPath			The path of the image
		 * @return		The HTML <img> tag
		 */
		function createImage($imgPath) {
			
			// Return <img> tag
			return '<img src="' .$imgPath  . '" alt="" hspace="5" vspace="5" border="0" align="middle">';
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formbuilder/class.tx_formbuilder_previewimages.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/formbuilder/class.tx_formbuilder_previewimages.php']);
	}
?>
