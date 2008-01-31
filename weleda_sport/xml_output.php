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
	 * XML page generation script for the 'weleda_sport' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		2.0
	 */
	
	/**
	 * Creates XML output for Flash.
	 * 
	 * This function creates an XML output wich can be used by a SWF movie.
	 * 
	 * @return		The XML content.
	 */
	function writeXML() {
		
		// XML Storage
		$xml = array();
		
		// Menu sections
		$sections = array('Et');
		
		// Start tag
		$xml[] = '<weleda_sport>';
		
		// Check for a requested record
		if ($uid = t3lib_div::_GP('uid')) {
			
			// Select record
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_weledasport_sportsmen','tx_weledasport_sportsmen.uid=' . $uid . ' AND NOT deleted');
			
			// Get record
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			
			// Sportsman XML
			$xml[] = getSportsman($row);
			
		} else {
			
			// Start tag
			$xml[] = '<menu>';
			
			// Storage PID
			$page = t3lib_div::_GP('pages');
			
			// Create menu items
			 foreach($sections as $key=>$value) { 
				
				// Start tag
				$xml[] = '<section id="' . $key . '" label="' . $value . '">'; 
				
				// Select winter categories
				$cat_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_weledasport_cat','NOT deleted',false,'name');  /* tx_weledasport_cat.season=' . $key . ' AND tx_weledasport_cat.pid=' . $page . ' AND */
				
				// Get categories
				while($cat = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($cat_res)) {
					
					// Start tag
					$xml[] = '<cat label="' . $cat['name'] . '">';
					
					// Select sportsmen
					$sport_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_weledasport_sportsmen','tx_weledasport_sportsmen.deleted=0 AND tx_weledasport_sportsmen.cat=' . $cat['uid'] . ' AND tx_weledasport_sportsmen.pid=' . $page . ' AND NOT deleted',false,'name');
					
					// Get sportsmen
					while($sport = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($sport_res)) {
						
						// Start tag
						$xml[] = '<item url="' . t3lib_div::getIndpEnv('REQUEST_URI') . '&uid=' . $sport['uid'] . '" label="' . $sport['name'] . '"/>';
					}
					
					// End tag
					 $xml[] = '</cat>';
				 }
				
				// End tag
				$xml[] = '</section>';  
			}
			
			// End tag
			$xml[] = '</menu>';
			
			// Select random record
			$random_res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_weledasport_sportsmen','tx_weledasport_sportsmen.pid=' . $page . '  AND NOT deleted',false,'rand()');
			
			// Get random record
			$random_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($random_res);
			
			// Sportsman XML
			$xml[] = getSportsman($random_row);
		}
		
		// End tag
		$xml[] = '</weleda_sport>';
		
		// Return XML code
		return implode(chr(10),$xml);
	}
	
	/**
	 * Creates XML output for a sportsman.
	 * 
	 * This function creates an XML output for a sportsman.
	 * 
	 * @param		$row				The sportsman row
	 * @return		The XML content.
	 */
	function getSportsman($row) {
		
		// Upload folder
		$uploadFolder = str_replace(PATH_site,'',t3lib_div::getFileAbsFileName('uploads/tx_weledasport'));;
		
		// XML Storage
		$xml = array();
		
		// Palmares array
		$palmares = tx_apimacmade::div_xml2array($row['palmares']);
		
		// Start tag
		$xml[] = '<record uid="' . $row['uid'] . '">';
		
		// Name
		$xml[] = '<name><![CDATA[' . $row['name'] . ']]></name>';
		
		// Link
		$xml[] = '<link><![CDATA[' . $row['link'] . ']]></link>';
		
		// Description
		$xml[] = '<description><![CDATA[' . str_replace('&nbsp;',' ',$row['description']) . ']]></description>';
		
		// Palmares
		if (is_array($palmares['T3FlexForms']['data']['sDEF']['lDEF']['fields']['el'])) {
			
			// Start tag
			$xml[] = '<prizes>';
			
			// Process palmares
			foreach($palmares['T3FlexForms']['data']['sDEF']['lDEF']['fields']['el'] as $key=>$value) {
				
				// Check for valid data
				if (is_int($key)) {
					
					// Write palmares
					$xml[] = '<prize><![CDATA[' . $value['palmares']['el']['text']['vDEF'] . ']]></prize>';
				}
			}
			
			// End tag
			$xml[] = '</prizes>';
		}
		
		// Advice
		$xml[] = '<advice><![CDATA[' . str_replace('&nbsp;',' ',$row['advice']) . ']]></advice>';
		
		// Product
		$xml[] = '<product><![CDATA[' . str_replace('&nbsp;',' ',$row['product']) . ']]></product>';
		
		// Picture
		$xml[] = '<picture><![CDATA[/' . $uploadFolder . '/' . $row['picture'] . ']]></picture>';
		
		// Thumbnail
		$xml[] = '<thumb><![CDATA[/' . $uploadFolder . '/' . $row['thumbnail'] . ']]></thumb>';
		
		// End tag
		$xml[] = '</record>';
		
		// Return XML code
		return implode(chr(10),$xml);
	}
	
	// Include Developer API class
	include_once(t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	// Write XML
	echo(writeXML());
	
	// Exit script
	die();
?>
