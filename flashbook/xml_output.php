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
	 * XML page generation script for the 'flashbook' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * Creates XML output for Flash.
	 * 
	 * This function creates an XML output wich can be used by a SWF movie.
	 * 
	 * @return		The XML content.
	 */
	function writeXML() {
		
		// Book ID
		$uid = t3lib_div::_GET('flashbook');
		
		// Select book
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_flashbook_books','deleted=0 AND hidden=0 AND uid=' . $uid);
		
		// Get book
		if ($res && $book = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			
			// XML Storage
			$xml = array();
			
			// Base parameters
			$xml['xml-attribs'] = array(
				'width' => $book['width'],
				'height' => $book['height'],
				'hcover' => ($book['hardcover'] == 1) ? 'true' : 'false',
				'transparency' => ($book['transparency'] == 1) ? 'true' : 'false',
			);
			
			// Get pages
			$pages = tx_apimacmade::div_xml2array($book['pages'],0);
			
			// Check XML hierarchy
			if (tx_apimacmade::div_checkArrayKeys($pages,'T3FlexForms,data,sDEF,lDEF,fields,el',0,'array')) {
				
				// Process pages
				foreach($pages['T3FlexForms']['data']['sDEF']['lDEF']['fields']['el'] as $page) {
					
					// Check page informations
					if (tx_apimacmade::div_checkArrayKeys($page,'page,el,file,vDEF',1) && tx_apimacmade::div_checkArrayKeys($page,'page,el,cantear,vDEF',1)) {
						
						// Source
						$src = 'uploads/tx_flashbook/' . $page['page']['el']['file']['vDEF'];
							
						// Check if file exists
						if (@is_file(t3lib_div::getFileAbsFileName($src))) {
							
							// Add page
							$xml[] = array(
								
								// Page attributes
								'xml-attribs' => array(
									'src' => $src,
									'canTear' => ($page['page']['el']['cantear']['vDEF'] == 1) ? 'true' : 'false',
								),
							);
						}
					}
				}
			}
			
			// Return XML code
			return tx_apimacmade::div_array2xml($xml,'content','','page');
		}
	}
	
	// Include Developer API class
	include_once(t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	// DEBUG ONLY - Show XML
	#echo('<pre>' . htmlspecialchars(writeXML()) . '</pre>');
	
	// Write XML
	echo(writeXML());
	
	// Exit
	exit();
?>
