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
	 * XML page generation script for the 'flash_pageheader' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net) / macmade.net
	 * @version		1.0
	 */
	
	/**
	 * Creates XML output for Flash.
	 * 
	 * This function creates an XML output wich can be used by a SWF movie.
	 * The XML will contain the fields from the table "pages" specified in
	 * the extension configuration.
	 * 
	 * @return		The XML content.
	 */
	function writeXML() {
		
		// Extension configuration
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['flash_pageheader']);
		//debug($GLOBALS['TSFE']['t3lib_tstemplate']);
		
		// Get the fields to output
		$xmlNodes = explode(',',$extConf['fields']);
		
		// XML Storage
		$xml = array();
		
		// XML declaration
		$xml[] = '<?xml version="1.0" encoding="utf-8"?>';
		
		// XML root start
		$xml[] = '<T3PageHeader>';
		
		// Checking XML nodes
		foreach($GLOBALS['TSFE']->page as $nodeName => $nodeValue) {
			
			// Checking fields to output
			if (in_array($nodeName,$xmlNodes)) {
				
				// Flash replacement picture
				if ($nodeName == 'tx_flashpageheader_picture') {
					if (empty($nodeValue)) {
						
						// Default picture
						$relPath = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_flashpageheader_pi1.']['defaultPicture'];
						
						// Checking for a recursive picture
						if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_flashpageheader_pi1.']['recursive'] == 1) {
							
							foreach($GLOBALS['TSFE']->config['rootLine'] as $topPage) {
								
								// Recursive picture found
								if (!empty($topPage[$tx_flashpageheader_picture])) {
									$relPath = $this->uploadDir . $topPage[$field];
								}
							}
						}
						$absPath = t3lib_div::getFileAbsFileName($relPath);
						$nodeValue = str_replace($_SERVER['DOCUMENT_ROOT'],'',$absPath);
					} else {
						
						// User defined picture
						$nodeValue = 'uploads/tx_flashpageheader/' . $nodeValue;
					}
				}
				
				// Build XML node
				$xml[] = chr(9) . '<' . $nodeName . '>' . rawurlencode($nodeValue) . '</' . $nodeName . '>';
			}
		}
		
		// XML root end
		$xml[] = '</T3PageHeader>';
		
		// Return XML code
		return implode(chr(10),$xml);
	}
	
	// Write XML
	echo writeXML();
?>
