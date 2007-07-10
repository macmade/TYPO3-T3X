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
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	/**
	 * Creates XML output for Firefox.
	 * 
	 * This function creates an XML output wich can be as a Firefox search plugin.
	 * 
	 * @return		The XML content.
	 */
	function writeXML() {
		
		// Get plugin configuration
		$conf = unserialize($GLOBALS['TSFE']->fe_user->getKey('ses','tx_mozsearch_pi1'));
		
		// GET variables
		$get = t3lib_div::_GET('tx_mozsearch_pi1');
		
		// Check configuration
		if (is_array($conf) && is_array($get)) {
			
			// Generation informations
			$infos = explode('/',$get['file']);
			
			// Generation type (icon or plugin)
			if (is_array($infos) && $infos[0] == 'icon') {
				
				// Check if icon file exists
				if (@is_file($conf['iconAbsPath'])) {
					
					// Content type
					header('Content-type: image/png');
					
					// Return icon code
					return t3lib_div::getURL($conf['iconAbsPath']);
				}
				
			} else {
				
				// XML Storage
				$xml = array();
				
				// Plugin header
				$xml[] = '#----------------------------------------------------------------------';
				$xml[] = '# Made with Typo3 extension Firefox search plugin generator (mozsearch)';
				$xml[] = '# Copyright (C) 2004 macmade.net';
				$xml[] = '# http://www.macmade.net/';
				$xml[] = '#----------------------------------------------------------------------';
				
				// Plugin code
				$xml[] = '<search';
				$xml[] = '	version="' . $conf['version'] . '"';
				$xml[] = '	name="' . $conf['name'] . '"';
				$xml[] = '	description="' . $conf['description'] . '"';
				$xml[] = '	action="' . $conf['searchPage'] . '"';
				$xml[] = '	searchForm="' . $conf['searchPage'] . '"';
				$xml[] = '	method="' . $conf['method'] . '"';
				$xml[] = '	queryCharset="' . $conf['queryCharset'] . '"';
				$xml[] = '>';
				$xml[] = '<input name="sourceid" value="' . $conf['sourceid'] . '">';
				$xml[] = '<input name="' . $conf['sword'] . '" user>';
				$xml[] = '</search>';
				
				// Return XML code
				return implode(chr(10),$xml);
			}
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
