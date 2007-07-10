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
	 * Class/Function to help in the vCard export process
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      59:		function mapUserArray($user)
	 * 
	 *				TOTAL FUNCTIONS: 1
	 */
	
	class tx_addressbook_vCardExport {
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Base functions.
		 ***************************************************************/
		
		/**
		 * Convert an array for vCards.
		 * 
		 * This function converts an user array, taken from the tx_addressbook_addresses
		 * table, and converts it for use in the div_vCardCreate function of the Developer API
		 * (api_macmade).
		 * 
		 * @param		$user				The user array to convert
		 * @return		An array with the user informations, ready for a vCard conversion
		 */
		function mapUserArray($user) {
			
			// Base array
			$vCard = array(
				'firstname' => $user['firstname'],		// First name
				'name' => $user['lastname'],			// Last name
				'username' => $user['nickname'],		// Nick name
				'company' => $user['company'],			// Company
				'department'  => $user['department'],	// Department
				'title' => $user['jobtitle'],			// Job title
				'www' => $user['homepage'],				// Home page
				'note' => $user['notes'],				// Notes
				'birthday' => $user['birthday'],		// Birtday
				'iscompany' => $user['type']			// Define as company
			);
			
			// Check for user picture
			if ($user['picture']) {
				
				// Add picture
				$vCard['image'] = PATH_site . 'uploads/tx_addressbook/' . $user['picture'];
			}
			
			// Return final array
			return $vCard;
				/*'email' => array(
					array(
						mail => string		// Email address
						type => string		// Type (WORK - HOME - Other)
					)
				)
				'phone' => array(
					array(
						number => string	// Number
						type => string		// Type (WORK - HOME - CELL - MAIN - HOMEFAX - WORKFAX - Other)
					)
				)
				'messenger' => array(
					array(
						name =>				// Account name
						service => string	// Service (AIM - JABBER - MSN - YAHOO - ICQ)
						type => string		// Type (WORK - HOME - Other)
					)
				)
				'address' => array(
					array(
						street => string	// Street
						city => string		// City
						state => string		// State
						zip => string		// ZIP code
						country => string	// Country
						type => string		// Type (WORK - HOME - Other)
					)
				)*/
		}
	}
	
	/**
	 * XClass inclusion.
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/addressbook/class.tx_addressbook_vcardexport.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/addressbook/class.tx_addressbook_vcardexport.php']);
	}

?>
