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
	 * Module 'Mantis' for the 'mantis_bugtracker' extension.
	 *
	 * @author		Jean-David Gadina <info@macmade.net>
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - INIT
	 *     116:		function init
	 *     129:		function main
	 * 
	 * SECTION:		2 - MAIN
	 *     224:		function menuConfig
	 *     249:		function moduleContent
	 * 
	 * SECTION:		3 - STATUS
	 *     318:		function showSettings($records)
	 *     358:		function buildSettingsTable($server)
	 *     412:		function tryLDAP($server)
	 * 
	 * SECTION:		4 - SHOW
	 *     486:		function showEntries($records)
	 *     546:		function buildEntriesTable($entries)
	 * 
	 * SECTION:		5 - IMPORT
	 *     612:		function importEntries($records)
	 *     776:		function importGroups($user,$server,$type)
	 *     882:		function importEXT($user,$server)
	 * 
	 * SECTION:		6 - MERGE
	 *     965:		function mergeEntries($records)
	 * 
	 * SECTION:		7 - DELETE
	 *    1130:		function deleteEntries($records)
	 * 
	 * SECTION:		8 - UTILITIES
	 *    1161:		function printContent
	 *    1176:		function writeHTML($text,$tag='p',$style=false)
	 *    1195:		function createLDAP($server)
	 *    1239:		function cleanEntries($entries)
	 *    1290:		function sortEntries($entries,$sortKey)
	 *    1332:		function buildLDAPUserCols($user,$server,$mode)
	 *    1425:		function substituteLDAPValue($ldapField,$ldapValue)
	 *    1453:		function checkLDAPField($field)
	 *    1476:		function getExistingUsers
	 *    1517:		function getLDAPUsers($records,$mode)
	 *    1637:		function ldap2BE($user,$server,$mode)
	 *    1735:		function ldap2BE($user,$server,$mode)
	 *    1833:		function mapFields($fields,$xmlds,$user)
	 * 
	 *				TOTAL FUNCTIONS: 27
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require ('conf.php');
	require ($BACK_PATH . 'init.php');
	require ($BACK_PATH . 'template.php');
	$LANG->includeLLFile('EXT:mantis_bugtracker/mod1/locallang.php');
	require_once (PATH_t3lib . 'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	class tx_mantisbugtracker_module1 extends t3lib_SCbase {
		
		
		
		
		
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
		 * Creates the page.
		 * 
		 * This function creates the basic page in which the module will
		 * take place.
		 * 
		 * @return		Void
		 */
		function main() {
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
			
			// Access check
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;
			
			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {
				
				// Module configuration
				$this->MCONF = $GLOBALS['MCONF'];
				
				// Check for a valid Mantis installation
				if ($this->MCONF['mantisSubDir'] && @is_dir($this->MCONF['mantisSubDir'])) {
					
					// Redirect URL
					$redirect = $this->MCONF['mantisSubDir'];
					
					// Redirect
					header('Location: ' . $redirect);
					
				} else {
					
					// No access
					$this->doc = t3lib_div::makeInstance('bigDoc');
					$this->doc->backPath = $BACK_PATH;
					$this->content .= $this->doc->startPage($LANG->getLL('title'));
					$this->content .= $this->doc->header($LANG->getLL('title'));
					$this->content .= $this->doc->spacer(5);
					$this->content .= $LANG->getLL('noinstall');
					$this->content .= $this->doc->spacer(10);
					
					// Print content
					$this->printContent();
				}
				
			} else {
				
				// No access
				$this->doc = t3lib_div::makeInstance('bigDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $LANG->getLL('noaccess');
				$this->content .= $this->doc->spacer(10);
				
				// Print content
				$this->printContent();
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 2 - UTILITIES
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
			$this->content .= $this->doc->endPage();
			echo($this->content);
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_bugtracker/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mantis_bugtracker/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_mantisbugtracker_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	$SOBE->main();
?>


