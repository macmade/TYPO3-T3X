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
	 * XML page generation script for the 'slideshow' extension.
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
		
		// XML parameters storage
		$params = array();
		
		// Timer
		$params['rotatetime'] = t3lib_div::_GP('timer');
		
		// Random play
		$params['randomplay'] = (t3lib_div::_GP('random')) ? 'true' : 'false';
		
		// Transition type
		$params['shownavigation'] = (t3lib_div::_GP('navigation')) ? 'true' : 'false';
		
		// Transition type
		$params['transition'] = t3lib_div::_GP('transition');
		
		// slideshow UID
		$slideshowID = t3lib_div::_GP('slideshow');
		
		// Select requested slideshow
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_slideshow_slideshows','tx_slideshow_slideshows.uid=' . $slideshowID);
		
		// Get slideshow
		$slideshow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		// Check MySQL ressource
		if ($slideshow) {
			
			// XML declaration
			$xml[] = '<?xml version="1.0" encoding="utf-8"?' . '>';
			
			// XML root start
			$xml[] = '<jpgrotator>';
			
			// Parameters container start
			$xml[] = '<parameters>';
			
			// Process each parameter
			foreach($params as $key=>$value) {
				
				// Add parameter
				$xml[] = '<' . $key . '>' . $value . '</' . $key . '>';
			}
			
			// Parameters container end
			$xml[] = '</parameters>';
			
			// Pictures container start
			$xml[] = '<photos>';
			
			// Get document root
			$docRoot = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT');
			
			// Check type
			if ($slideshow['type'] == 1) {
				
				// Get directory path
				$uploadDir = str_replace($docRoot,'',t3lib_div::getFileAbsFileName($slideshow['dir_path']));
				
				// Check for a trailing slash
				if (strrpos($uploadDir,'/') != strlen($uploadDir) - 1) {
					
					// Add trailing slash
					$uploadDir .= '/';
				}
				
				// Get an array with pictures
				$picsArray = explode(',',$slideshow['dir_pictures']);
				
				// Process each JPG file
				foreach($picsArray as $jpgFile) {
					
					// Link
					$link = $slideshow['dir_url'];
					
					// Add picture tag
					$xml[] = '<photo path="' . $uploadDir . $jpgFile . '" link="' . $link . '" />';
				}
				
			} else {
				
				// Fix path to upload directory
				$uploadDir = str_replace($docRoot,'',t3lib_div::getFileAbsFileName('uploads/tx_slideshow/'));
				
				// Get an array with pictures
				$picsArray = tx_apimacmade::div_xml2array($slideshow['pictures'],0);
				
				// Process each JPG file
				foreach($picsArray['T3FlexForms']['data']['sDEF']['lDEF']['fields']['el'] as $jpgFile) {
					
					// Check type (uploaded file or path)
					if ($jpgFile['jpg']) {
						
						// Check link
						switch($jpgFile['jpg']['el']['link']['vDEF']) {
							
							// None
							case '0':
								$link = '';
							break;
							
							// External URL
							case '1':
								$link = $jpgFile['jpg']['el']['url']['vDEF'];
							break;
						}
						
						// Add picture tag
						$xml[] = '<photo path="' . $uploadDir . $jpgFile['jpg']['el']['file']['vDEF'] . '" link="' . $link . '" />';
						
					} else if ($jpgFile['path']) {
						
						// Get abolute path of the picture
						$absPath = t3lib_div::getFileAbsFileName($jpgFile['path']['el']['path']['vDEF']);
						
						// Check if file exists
						if (@is_file($absPath)) {
							
							// Check link
							switch($jpgFile['path']['el']['link']['vDEF']) {
								
								// None
								case '0':
									$link = '';
								break;
								
								// External URL
								case '1':
									$link = $jpgFile['path']['el']['url']['vDEF'];
								break;
							}
							
							// Add picture tag
							$xml[] = '<photo path="' . str_replace($docRoot,'',$absPath) . '" link="' . $link . '" />';
						}
					}
				}
			}
			
			// Pictures container end
			$xml[] = '</photos>';
			
			// XML root end
			$xml[] = '</jpgrotator>';
		}
		
		// Return XML code
		return implode(chr(10),$xml);
	}
	
	// Include Developer API class
	include_once(t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	// Write XML
	echo writeXML();
	
	// Exit
	exit();
?>
