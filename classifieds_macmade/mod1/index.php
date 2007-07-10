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
	 * Module 'Classified ads' for the 'classifieds_macmade' extension.
	 *
	 * @author		Jean-David Gadina <info@macmade.net>
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *        :		
	 * 
	 * SECTION:		2 - MAIN
	 *        :		
	 * 
	 * SECTION:		3 - UTILITIES
	 *        :		
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require ('conf.php');
	require ($BACK_PATH.'init.php');
	require ($BACK_PATH.'template.php');
	$LANG->includeLLFile('EXT:classifieds_macmade/mod1/locallang.php');
	require_once (PATH_t3lib.'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	class tx_classifiedsmacmade_module1 extends t3lib_SCbase {
		
		var $pageinfo;
		
		/***************************************************************
		 * SECTION 1 - INIT
		 *
		 * Base module functions.
		 ***************************************************************/
		
		/**
		 * Initialization of the class.
		 * 
		 * @return		Void
		 */
		
		function init() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			parent::init();
		}
		
		/**
		 * Main function of the module. Write the content to $this->content
		 */
		function main() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			
			// Access check
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;
			
			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {
				
				// Draw the header.
				$this->doc = t3lib_div::makeInstance('bigDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->doc->form='<form action="" method="POST">';
				
				// JavaScript
				$this->doc->JScode = '
					<script type="text/javascript" language="Javascript" charset="iso-8859-1">
						<!--
						script_ended = 0;
						function jumpToUrl(URL) {
							document.location = URL;
						}
						//-->
					</script>
				';
				$this->doc->postCode='
					<script type="text/javascript" language="Javascript" charset="iso-8859-1">
						<!--
						script_ended = 1;
						if (top.fsMod) {
							top.fsMod.recentIds["web"] = '.intval($this->id).';
						}
						//-->
					</script>
				';
				
				// Build current path
				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				
				// Start page content
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				$this->content .= $this->doc->divider(5);
				
				// Render content:
				$this->moduleContent();
				
				// Adds shortcut
				if ($BE_USER->mayMakeShortcut()) {
					$this->content .= $this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}
				
				$this->content .= $this->doc->spacer(10);
				
			} else {
				
				// No access
				$this->doc = t3lib_div::makeInstance('bigDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $LANG->getLL('noaccess');
				$this->content .= $this->doc->spacer(10);
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Main module functions.
		 ***************************************************************/
		
		/**
		 * Creates the module's menu.
		 * 
		 * This function creates the module's menu.
		 * 
		 * @return		Void
		 */
		function menuConfig() {
			global $LANG;
			
			// Menu items
			$this->MOD_MENU = Array (
				'function' => Array (
					'1' => $LANG->getLL('function1'),
				)
			);
			
			// Creates menu
			parent::menuConfig();
		}
		
		/**
		 * Creates the module's content.
		 * 
		 * This function creates the module's content.
		 * 
		 * @return		Void
		 */
		function moduleContent() {
			
			// Storage
			$htmlCode = array();
			
			// Start section
			$htmlCode[] = $this->doc->sectionBegin();
			
			// Title
			$htmlCode[] = $this->doc->sectionHeader('Classified ads administration module');
			
			// Dummy content
			$htmlCode[] = '<strong>Not started yet</strong>';
			
			// Divider
			$htmlCode[] = $this->doc->divider(5);
			
			// End section
			$htmlCode[] = $this->doc->sectionEnd();
			
			// Set content
			$this->content .= implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - UTILITIES
		 *
		 * General purpose functions.
		 ***************************************************************/
		
		/**
		 * Prints the page.
		 * 
		 * This function closes the page, and writes the final
		 * rendered content.
		 * 
		 * @return		Void
		 */
		function printContent() {
			
			// End document
			$this->content .= $this->doc->endPage();
			
			// Output content
			echo($this->content);
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/classifieds_macmade/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/classifieds_macmade/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_classifiedsmacmade_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	$SOBE->main();
	$SOBE->printContent();
?>
