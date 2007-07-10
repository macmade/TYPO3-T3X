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
	 **************************************************************/
	
	/*
	 * Module 'FTP Browser' for the 'ftpbrowser' extension
	 *
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		1.1
	 */
	
	// Default initialization
	unset($MCONF);
	require ("conf.php");
	require ($BACK_PATH . "init.php");
	require ($BACK_PATH . "template.php");
	$LANG->includeLLFile("EXT:ftpbrowser/mod1/locallang.php");
	require_once (PATH_t3lib . "class.t3lib_scbase.php");
	
	// Access check
	$BE_USER->modAccess($MCONF,1);
	
	class tx_ftpbrowser_module1 extends t3lib_SCbase {
		
		function main()	{
			global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $HTTP_GET_VARS, $HTTP_POST_VARS, $CLIENT, $TYPO3_CONF_VARS;
			$this->MCONF = $GLOBALS['MCONF'];
			if ($this->MCONF['ftpBrowserSubDir'] && @is_dir($this->MCONF['ftpBrowserSubDir'])) {
				
				// Valid installation / Redirection
				$redirect = $this->MCONF['ftpBrowserSubDir'];
				header('Location: '. $redirect);
			} else {
				// Corrupt installation / Error
				$this->doc = t3lib_div::makeInstance("mediumDoc");
				$this->doc->backPath = $BACK_PATH;
				$this->content .= $this->doc->startPage($LANG->getLL("title"));
				$this->content .= $this->doc->header($LANG->getLL("title"));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $LANG->getLL("error");
			}
		}
	
		function printContent()	{
			echo $this->content;
		}
	}
	
	if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/ftpbrowser/mod1/index.php"]) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/ftpbrowser/mod1/index.php"]);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance("tx_ftpbrowser_module1");
	$SOBE->main();
	$SOBE->printContent();
?>
