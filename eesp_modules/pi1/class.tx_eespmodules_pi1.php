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
	 * Plugin 'EESP - Modules calendar' for the 'eesp_modules' extension.
	 *
	 * @author		Jean-David Gadina (info@macmade.net)
	 * @version		1.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *        :		function main($content,$conf)
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Typo3 FE plugin class
	require_once(PATH_tslib . 'class.tslib_pibase.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	class tx_eespmodules_pi1 extends tslib_pibase {
		
		
		
		
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_eespmodules_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_eespmodules_pi1.php';
		
		// The extension key
		var $extKey = 'eesp_modules';
		
		// Version of the Developer API required
		var $apimacmade_version = 1.4;
		
		// Extension tables
		var $extTables = array(
			
			// Graduate programs
			'programs' => 'tx_eespmodules_programs',
		);
		
		// Modules storage
		var $modules = array();
		
		// Files storage
		var $files = array();
		
		// Domain priorities
		var $priorities = array(
			'D1' => '6',
			'D2' => '5',
			'D3' => '4',
			'D4' => '3',
			'MEM' => '2',
			'D7' => '1',
			'OE' => '7',
			'VAC' => '0',
		);
		
		// Sections
		var $sections = array(
			'1' => 'asc',
			'2' => 'as',
			'3' => 'es',
			'4' => 'er',
		);
		
		// Days
		var $days = array('MO','TU','WE','TH','FR');
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin 'tx_formbuilder_pi1', and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin
		 */
		function main($content,$conf) {
			
			// Disable cache
			#$GLOBALS['TSFE']->set_no_cache();
			
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Set default plufin variables
			$this->pi_setPiVarDefaults();
			
			// Load LOCAL_LANG values
			$this->pi_loadLL();
			
			// Init flexform configuration of the plugin
			$this->pi_initPIflexForm();
			
			// Store flexform informations
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
			
			// Set final configuration (TS or FF)
			$this->setConfig();
			
			// Storage
			$htmlCode = array();
			
			// Check direct module access
			if ($code = $this->piVars['code']) {
				
				// Get year
				$year = '20' . substr($code,0,2);
				
				// Show XML flag
				$this->piVars['showXML'] = $year . '/' . $code . '.xml';
			}
			
			// Display mode
			if ($file = $this->piVars['showXML']) {
				
				// Show XML file
				$htmlCode[] = $this->api->fe_makeStyledContent('div','xmlModule',$this->showModule($file,$this->year));
				
			} else if ($this->piVars['startyear'] && $this->piVars['formation']) {
				
				// Initialization
				$this->init();
				
				// Valid calendar
				if (is_array($this->program) && $this->program['terms']) {
					
					// Build menu
					$htmlCode[] = $this->buildMenu();
					
					// Module search
					$htmlCode[] = $this->modSearch();
					
					// Read index file for the current year
					$this->readIndexFile();
					
					// vCal features
					if ($this->conf['vCalExport']) {
						
						// Export link
						$htmlCode[] = $this->api->fe_makeStyledContent('div','vcalExport',$this->pi_linkTP_keepPIvars($this->pi_getLL('vcalexport'),array('vcal'=>1)));
						
						// Export
						if ($this->piVars['vcal']) {
							
							// Output cal
							$this->api->div_output($this->vCalExport($this->year),'text/x-vCalendar','EESP-' . $this->year . '.ics');
							
							// DEBUG ONLY - Write cal
							#$htmlCode[] = '<pre>' . $this->vCalExport($year) . '</pre>';
						}
					}
					
					// Calendar view
					switch($this->piVars['view']) {
						
						// Classic view
						case 1:
							
							// Build view
							$htmlCode[] = $this->classicView();
							
						break;
						
						// Default (calendar) view
						default:
							
							// Build view
							$htmlCode[] = $this->calView();
							
							// Add javascript code for row higlight
							$this->buildJSCode();
							
						break;
					}
				}
			} else {
				
				// Build menu
				$htmlCode[] = $this->buildMenu();
				
				// Module search
				$htmlCode[] = $this->modSearch();
			}
			
			// Return content
			return $this->pi_wrapInBaseClass(implode(chr(10),$htmlCode));
		}
		
		/**
		 * Set plugin configuration
		 * 
		 * This function merges the TypoScript configuration of the plugin
		 * with flexform informations. The priority is given to the flexform data.
		 * 
		 * @return		Void
		 */
		function setConfig() {
			
			// Mapping array for PI flexform
			$flex2conf = array(
				'pid' => 'sDEF:pages',
				'vCalExport' => 'sDEF:vcal',
				'showYears' => 'sDEF:showyears',
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf);
		}
		
		/**
		 * Build the calendar menu.
		 * 
		 * This function build a menu with the available graduate programs.
		 * 
		 * @return		The calendar menu
		 */
		function buildMenu() {
			
			// Storage
			$htmlCode = array();
			
			// Anchor
			$htmlCode[] = '<a name="' . $this->prefixId . '-top"></a>';
			
			// Start form
			$htmlCode[] = '<form action="' . $this->api->fe_linkTP_unsetPIvars_url(array(),array('vcal','showXML','startyear','showterm','formation','view')) . '" method="post" enctype="' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'] . '" name="' . $this->prefixId . '-menu">';
			
			// Header
			$htmlCode[] = '<h2>' . $this->pi_getLL('menu.header') . '</h2>';
			
			// Year selector
			$htmlCode[] = $this->yearSelect();
			
			// Term selector
			$htmlCode[] = $this->termSelect();
			
			// Type selector
			$htmlCode[] = $this->typeSelect();
			
			// Section selector
			$htmlCode[] = $this->sectionSelect();
			
			// View selector
			$htmlCode[] = $this->viewSelect();
			
			// Submit
			$htmlCode[] = '<input type="submit" name="submit" value="' . $this->pi_getLL('menu.submit') . '">';
			
			// End form
			$htmlCode[] = '</form>';
			
			// Return content
			return $this->api->fe_makeStyledContent('div','menu',implode(chr(10),$htmlCode));
		}
		
		/**
		 * Module search
		 * 
		 * This function build a search form to access directly a module file.
		 * 
		 * @return		The module search
		 */
		function modSearch() {
			
			// Storage
			$htmlCode = array();
			
			// Start form
			$htmlCode[] = '<form action="' . $this->api->fe_linkTP_unsetPIvars_url(array(),array('vcal','showXML')) . '" method="post" enctype="' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['form_enctype'] . '" name="' . $this->prefixId . '-modsearch">';
			
			// Description
			$htmlCode[] = $this->pi_getLL('modsearch.description');
			
			// Input
			$htmlCode[] = '<input name="' . $this->prefixId . '[code]" type="text" size="6" maxlength="6">';
			
			// Submit
			$htmlCode[] = '<input type="submit" name="submit" value="' . $this->pi_getLL('modsearch.submit') . '">';
			
			// End form
			$htmlCode[] = '</form>';
			
			// Return content
			return $this->api->fe_makeStyledContent('div','modsearch',implode(chr(10),$htmlCode));
		}
		
		/**
		 * Year selector
		 * 
		 * This function build the select menu for the years.
		 * 
		 * @return		The year selector
		 */
		function yearSelect() {
			
			// Storage
			$htmlCode = array();
			
			// Start years select
			$htmlCode[] = '<select name="' . $this->prefixId . '[startyear]">';
			
			// MySQL WHERE clause
			$whereClause = 'pid IN (' . $this->conf['pid'] . ')' . $this->cObj->enableFields($this->extTables['programs']);
			
			// MySQL ORDER BY clause
			$orderBy = 'startyear';
			
			// MySQL ressource
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,startyear',$this->extTables['programs'],$whereClause,false,$orderBy);
			
			// Get years
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				
				// Selected option
				$selected = ($this->piVars['startyear'] == $row['uid']) ? ' selected' : '';
				
				// Start year
				$startYear = explode('_',$row['startyear']);
				
				// Label
				$label = (array_key_exists(1,$startYear)) ? $startYear[0] . ' (' . $startYear[1] . ')' : $startYear[0];
				
				// Option tag
				$htmlCode[] = '<option value="' . $row['uid'] . '"' . $selected . '>' . $label . '</option>';
			}
			
			// End years select
			$htmlCode[] = '</select>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Term selector
		 * 
		 * This function build the select menu for the terms.
		 * 
		 * @return		The term selector
		 */
		function termSelect() {
			
			// Storage
			$htmlCode = array();
			
			// Start term select
			$htmlCode[] = '<select name="' . $this->prefixId . '[showterm]">';
			
			// All terms
			$htmlCode[] = '<option value="0">' . $this->pi_getLL('term.all') . '</option>';
			
			// Process terms
			for ($i = 0; $i < 8; $i++) {
				
				// Selected option
				$selected = ($this->piVars['showterm'] == ($i + 1)) ? ' selected' : '';
				
				// Option tag
				$htmlCode[] = '<option value="' . ($i + 1) . '"' . $selected . '>' . $this->pi_getLL('term') . ' ' . ($i + 1) . '</option>';
			}
			
			// End term select
			$htmlCode[] = '</select>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Type selector
		 * 
		 * This function build the select menu for the types.
		 * 
		 * @return		The type selector
		 */
		function typeSelect() {
			
			// Storage
			$htmlCode = array();
			
			// Start type select
			$htmlCode[] = '<select name="' . $this->prefixId . '[formation]">';
			
			// Build types
			for ($i = 1; $i < 3; $i++) {
				
				// Selected option
				$selected = ($this->piVars['formation'] == $i) ? ' selected' : '';
				
				// Option tag
				$htmlCode[] = '<option value="' . $i . '"' . $selected . '>' . $this->pi_getLL('menu.type.I.' . $i) . '</option>';
			}
			
			// End type select
			$htmlCode[] = '</select>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Section selector
		 * 
		 * This function build the select menu for the sections.
		 * 
		 * @return		The section selector
		 */
		function sectionSelect() {
			
			// Storage
			$htmlCode = array();
			
			// Start type select
			$htmlCode[] = '<select name="' . $this->prefixId . '[section]">';
			
			// Build types
			for ($i = 0; $i < 1; $i++) {
				
				// Selected option
				$selected = ($this->piVars['section'] == $i) ? ' selected' : '';
				
				// Option tag
				$htmlCode[] = '<option value="' . $i . '"' . $selected . '>' . $this->pi_getLL('menu.section.I.' . $i) . '</option>';
			}
			
			// End type select
			$htmlCode[] = '</select>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * View selector
		 * 
		 * This function build the select menu for the views.
		 * 
		 * @return		The view selector
		 */
		function viewSelect() {
			
			// Storage
			$htmlCode = array();
			
			// Start view select
			$htmlCode[] = '<select name="' . $this->prefixId . '[view]">';
			
			// Build views
			for ($i = 0; $i < 2; $i++) {
				
				// Selected option
				$selected = ($this->piVars['view'] == $i) ? ' selected' : '';
				
				// Option tag
				$htmlCode[] = '<option value="' . $i . '"' . $selected . '>' . $this->pi_getLL('menu.view.I.' . $i) . '</option>';
			}
			
			// End view select
			$htmlCode[] = '</select>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Initialization
		 * 
		 * This function initialize the base variable that will be used by this plugin.
		 * It also creates the terms and the modules array.
		 * 
		 * @return		Void
		 */
		function init() {
			
			// Check current week
			$this->cWeek = date('W');
			
			// Check current day of the week
			$this->cDay = date('w') - 1;
			
			// Check current year
			$this->cYear = date('Y');
			
			// Get program
			$this->program = $this->pi_getRecord($this->extTables['programs'],$this->piVars['startyear']);
			
			// Year path
			$this->yearPath = $this->program['startyear'];
			
			// Year informations
			$year = explode('_',$this->yearPath);
			
			// Year suffix
			$this->yearSuffix = (array_key_exists(1,$year)) ? '_' . $year[1] : '';
			
			// Memorize program year
			$this->year = $year[0];
			
			// Academic year
			$this->academicYear = (isset($this->piVars['showterm']) && $this->piVars['showterm'] > 0 && is_int($this->piVars['showterm'] / 2)) ? $this->year - 1 : $this->year;
			
			// Build terms for the program
			$this->buildTerms($this->year);
			
			// Parse calendar
			$this->parseCal($this->program['terms']);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 2 - CALENDAR VIEW
		 *
		 * Functions for the construction of the calendar view.
		 ***************************************************************/
		
		/**
		 * Build the calendar tables.
		 * 
		 * This function create a calendar view for the 8 terms of a
		 * graduate program.
		 * 
		 * @return		The whole calendar for the given graduate program
		 */
		function calView() {
			
			// Storage
			$htmlCode = array();
			
			// Build a table for each term
			for ($i = 0; $i < 8; $i++) {
				
				// Build term menu?
				if (!$this->piVars['showterm'] || $this->piVars['showterm'] == 0) {
					
					// Anchor
					$htmlCode[] = '<a name="' . $this->prefixId . '-term' . $i . '"></a>';
					
					// Build term menu
					$htmlCode[] = $this->createTermMenu();
					
					// Check if term contains a new academic year
					if ($i == 2 || $i == 4 || $i == 6) {
						
						// Increase academic year
						$this->academicYear++;
					}
					
				} else {
					
					// Check if the term should be displayed
					if ($this->piVars['showterm'] != ($i + 1)) {
						
						// Check if term contains a new year
						if ($i == 0 || $i == 2 || $i == 4 || $i == 6) {
							
							// Increase year
							$this->year++;
							$this->academicYear++;
						}
						
						// Reset content and continue
						continue;
					}
				}
				
				// Get downloads
				#$htmlCode[] = $this->getDownloads($i);
				
				// Start table
				$htmlCode[] = $this->api->fe_makeStyledContent('table','termTable',0,1,0,1,array('id'=>$this->prefixId . '-table' . $i,'border'=>0,'width'=>'100%','cellspacing'=>'1','cellpadding'=>'2','align'=>'center'));
				
				// Build a row for each week
				for ($j = 0; $j < count($this->terms[$i]); $j++) {
					
					// New year
					if ($this->terms[$i][$j] == '1') {
						
						// Increase year
						$this->year++;
						
						// Separation
						if ($this->conf['showYears']) {
							
							// Start row
							$htmlCode[] = '<tr>';
							
							// Emtpy cell
							$htmlCode[] = '<td>&nbsp;</td>';
							
							// Year
							$htmlCode[] = $this->api->fe_makeStyledContent('td','yearDiv',$this->year,1,0,0,array('colspan'=>'14'));
							
							// End row
							$htmlCode[] = '</tr>';
						}
					}
					
					// Build week day table headers
					if ($j == 0) {
						
						// Start row
						$htmlCode[] = '<tr>';
						
						// Term number
						$htmlCode[] = $this->api->fe_makeStyledContent('td','termInfo','<h2>' . $this->pi_getLL('term') . ' ' . ($i + 1) . '</h2>');
						
						// Empty cell
						$htmlCode[] = '<td>&nbsp;</td>';
						
						// Build a header cell for each half week day
						for ($k = 0; $k < 5; $k++) {
							
							// Day of the week
							$weekDay = $this->api->fe_makeStyledContent('span','weekday',$this->pi_getLL('weekday.' . $k));
							
							// AM
							$am = $this->api->fe_makeStyledContent('span','meridiem',$this->pi_getLL('weekday.am'));
							
							// PM
							$pm = $this->api->fe_makeStyledContent('span','meridiem',$this->pi_getLL('weekday.pm'));
							
							// Header AM
							$htmlCode[] = $this->api->fe_makeStyledContent('td','weekDays',$weekDay . '<br />' . $am);
							
							// Header PM
							$htmlCode[] = $this->api->fe_makeStyledContent('td','weekDays',$weekDay . '<br />' . $pm);
						}
						
						// End row
						$htmlCode[] = '</tr>';
					}
					
					// Start row
					$htmlCode[] = '<tr>';
					
					// Build info box
					if ($j == 0) {
						
						// Rowspan for infoBox column
						$infoSpan = count($this->terms[$i]);
						
						// Additionnal rowspan for new year terms
						if (is_int($i / 2)) {
							$infoSpan++;
						}
						
						// Info column
						$htmlCode[] = $this->api->fe_makeStyledContent('td','infoBox',$this->createInfoBox($i),1,0,0,array('id'=>$this->prefixId . '-infoBox-' . $i,'width'=>'15%','rowspan'=>$infoSpan,'valign'=>'top'));
					}
					
					// Build a cell for each weekday
					for ($k = 0; $k < 10; $k++) {
						
						// Build headers
						if ($k == 0) {
							
							// Header
							$htmlCode[] = $this->api->fe_makeStyledContent('td','weeks',$this->terms[$i][$j]);
						}
						
						// Content
						$htmlCode[] = $this->getCalEvent($i,$j,$k,$this->year);
					}
					
					// End row
					$htmlCode[] = '</tr>';
				}
				
				// End table
				$htmlCode[] = '</table>';
			}
			
			// Return content
			return $this->api->fe_makeStyledContent('div','calView',implode(chr(10),$htmlCode));
		}
		
		/**
		 * Get events for a specific day.
		 * 
		 * This function creates a table cell for a specific day,
		 * with infos about the current module area if aplicable.
		 * 
		 * @param		$term				The current term
		 * @param		$week				The current week
		 * @param		$day				The current day
		 * @param		$year				The current year
		 * @return		A content cell, if applicable
		 */
		function getCalEvent($term,$week,$day,$year) {
			
			// Check current day
			if ($year == $this->cYear && $this->terms[$term][$week] == $this->cWeek && ($day == ($this->cDay * 2) || $day == ($this->cDay * 2) + 1)) {
				
				// Highlight cell
				return $this->api->fe_makeStyledContent('td','today',date('d.m.Y'),0);
				
			} else {
				
				// Current term sheet
				$sTERM = 'sTERM' . ($term + 1);
				
				// Modules storage
				$modules = array();
				
				// Check if current term contains data
				if (is_array($this->modules[$sTERM])) {
					
					// Process each domain
					foreach($this->modules[$sTERM] as $domain) {
						
						// Process each module
						foreach($domain as $key=>$mod) {
							
							// Check the formation and the period of the module
							if (($mod['formation'] == '3' || $mod['formation'] == $this->piVars['formation']) && $mod['startweek'] <= $week && $mod['endweek'] >= $week && $mod['startday'] <= $day && $mod['endday'] >= $day) {
								
								// Check section
								if (!$this->piVars['section'] || $this->piVars['section'] == 0 || in_array($this->sections[$this->piVars['section']],$mod['section'])) {
									
									// Get module key
									$mod['key'] = $key;
									
									// Memorize module
									$modules[] = $mod;
								}
							}
						}
					}
				}
				
				// Check for module(s)
				if (count($modules)) {
					
					// Get prior module
					$modContent = array_shift($modules);
					
					// Check if the module has already been started
					if ($modContent['display'] == 1) {
						
						// Empty cell
						$content = '&nbsp';
						
					} else {
						
						// Title
						$title = '<h3>' . $modContent['domain'] . '</h3>';
						
						// Type
						$type = ($modContent['type']) ? $this->pi_getLL('module.type.I.' . $modContent['type']) : '';
						
						// Files
						$files = explode(',',$modContent['files']);
						
						// Check for files
						if (is_array($files) && count($files)) {
							
							// Storage
							$modCodes = array();
							
							// Process files
							foreach($files as $file) {
								
								// Check for a file
								if ($file) {
									
									// Plugin variables
									$overrulePIvars = array('showXML'=>$this->academicYear . $this->yearSuffix . '/' . $file,'startyear'=>$this->piVars['startyear'],'term'=>$term,'formation'=>$this->piVars['formation']);
									
									// Module key
									$modKey = str_replace('.xml','',$file);
									
									// Title tag
									$aTagTitle = $this->modIndex['index'][$modKey]['title'] . (($this->modIndex['index'][$modKey]['credits']) ? ' (' . $this->modIndex['index'][$modKey]['credits'] . ' ECTS)': '');
									
									// Add file
									$modCodes[] = $this->api->fe_linkTP($modKey,array($this->prefixId => $overrulePIvars),1,0,array('ATagParams'=>'title="' . $aTagTitle . '"'));
								}
							}
						}
						
						// Create content
						$content = $title . $type . $this->api->fe_makeStyledContent('div','module-codes',implode(' ',$modCodes));
						
						// Set display flag
						$this->modules[$sTERM][$modContent['domKey']][$modContent['key']]['display'] = 1;
					}
					
					// Cell ID
					$id = $modContent['domKey'] . '.' . $modContent['key'] . '-' . $term . '.' . $week . '.' . $day;
						
					// Return content cell
					return $this->api->fe_makeStyledContent('td',$modContent['domain'],$content,0,0,0,array('id'=>$id,'align'=>'center','valign'=>'middle','onclick'=>$this->prefixId . '_showInfo(\'' . $term . '.' . $modContent['domKey'] . '.' . $modContent['key'] .  '\',\'' . $term . '\')','onmouseover'=>$this->prefixId . '_showCells(\'' . $this->prefixId . '-table' . $term . '\',\'' . $id . '\',\'' . $modContent['domain'] . '-over' . '\');','onmouseout'=>$this->prefixId . '_showCells(\'' . $this->prefixId . '-table' . $term . '\',\'' . $id . '\',\'' . $modContent['domain'] . '\');'));
					
				} else {
					
					// Empty cell
					return $this->api->fe_makeStyledContent('td','FP','&nbsp;',0);
				}
			}
		}
		
		/**
		 * Creates the info box
		 * 
		 * This function creates the information area for the requested term, with
		 * all the modules informations.
		 * 
		 * @param		$term				The TS setup
		 * @return		The info box for the requested term
		 */
		function createInfoBox($term) {
			
			// Storage
			$htmlCode = array();
			
			// Info div content
			$infoDivContent = '<h3>' . $this->pi_getLL('infobox.title') . '</h3>' . $this->pi_getLL('infobox.infos');
			
			// Info div
			$htmlCode[] = $this->api->fe_makeStyledContent('div','visible',$infoDivContent,0);
			
			// Current term sheet
			$sTERM = 'sTERM' . ($term + 1);
			
			if (is_array($this->modules[$sTERM])) {
				
				// Process each domain
				foreach($this->modules[$sTERM] as $domain) {
					
					// Create content for each module
					foreach($domain as $key=>$mod) {
						
						// Content
						$htmlCode[] = $this->api->fe_makeStyledContent('div','hidden',$this->createModuleContent($mod,$term),0,0,0,array('id' => $term . '.' . $mod['domKey'] . '.' . $key));
					}
				}
			}
			
			// Return content
			return implode(chr(10),$htmlCode);

		}
		
		/**
		 * Creates the content for a module
		 * 
		 * This function creates the content for a specific module.
		 * 
		 * @param		$modArray			The module array
		 * @return		The informations for the requested module
		 */
		function createModuleContent($modArray,$term) {
			
			// Storage
			$htmlCode = array();
			
			// Title
			$htmlCode[] = $this->api->fe_makeStyledContent('h3',$modArray['domain'],$modArray['domain'],0);
			
			// Type
			$htmlCode[] = ($modArray['type']) ? '<p><strong>' . $this->pi_getLL('module.type.I.' . $modArray['type']) . '</strong></p>' : '';
			
			// Description
			$htmlCode[] = ($modArray['comments']) ? '<p>' . nl2br($modArray['comments']) . '</p>' : '';
			
			// Check if files are available
			if ($modArray['files']) {
				
				// Files array
				$files = explode(',',$modArray['files']);
				
				// Process ach file
				foreach ($files as $xmlModule) {
					
					// Module key
					$modKey = str_replace('.xml','',$xmlModule);
					
					// Content with link
					$linkedContent = $this->modIndex['index'][$modKey]['title'];
					$linkedContent .= ' (' . $this->modIndex['index'][$modKey]['hes_code'] . '-' . $modKey . ')';
					
					// Content without link
					$content = '';
					
					// URL
					$linkURL = (t3lib_div::getIndpEnv('REMOTE_ADDR') == $this->conf['router']) ? $this->conf['internal'] : $this->conf['external'];
					
					// Link
					$content = '<br /><a href="' . $linkURL . '?w_titre=' . urlencode($this->modIndex['index'][$modKey]['title']) . '" title="' . $this->pi_getLL('infoLink') . '" target="_blank">' . $this->cObj->IMAGE(array('file' => $this->conf['infoPic'])) . '</a>';
					
					// Check for ECTS
					if ($this->modIndex['index'][$modKey]['credits']) {
						
						// Add ECTS
						$content .= '<br /><strong>' . $this->modIndex['index'][$modKey]['credits'] . ' ECTS</strong>';
					}
					
					// Check for responsabilities
					if (is_array($this->modIndex['index'][$modKey]['resp'])) {
						
						// Process responsabilities
						foreach($this->modIndex['index'][$modKey]['resp'] as $person) {
							
							// Check for valid informations
							if ($person['firstname'] && $person['lastname']) {
								
								// Add person
								$content .= '<br />' . $person['firstname'] . ' ' . $person['lastname'];
							}
						}
					}
					
					// Create link
					$overrulePIvars = array('showXML'=>$this->academicYear . $this->yearSuffix . '/' . $xmlModule,'startyear'=>$this->piVars['startyear'],'term'=>$term,'formation'=>$this->piVars['formation']);
					$htmlCode[] = '<p>' . $this->api->fe_linkTP($linkedContent,array($this->prefixId => $overrulePIvars),1) . $content . '</p>';
				}
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - CLASSIC VIEW
		 *
		 * Functions for the construction of the classic view.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function classicView() {
			
			// Storage
			$htmlCode = array();
			
			// Build a table for each term
			for ($i = 0; $i < 8; $i++) {
				
				// Build term menu?
				if (!$this->piVars['showterm'] || $this->piVars['showterm'] == 0) {
					
					// Anchor
					$htmlCode[] = '<a name="' . $this->prefixId . '-term' . $i . '"></a>';
					
					// Build term menu
					$htmlCode[] = $this->createTermMenu();
					
					// Check if term contains a new academic year
					if ($i == 2 || $i == 4 || $i == 6) {
						
						// Increase academic year
						$this->academicYear++;
					}
					
				} else {
					
					// Check if the term should be displayed
					if ($this->piVars['showterm'] != ($i + 1)) {
						
						// Check if term contains a new year
						if ($i == 0 || $i == 2 || $i == 4 || $i == 6) {
							
							// Increase year
							$this->year++;
							$this->academicYear++;
						}
						
						// Reset content and continue
						continue;
					}
				}
				
				if (is_array($this->modules['sTERM' . ($i + 1)]) && count($this->modules['sTERM' . ($i + 1)])) {
					
					// Get downloads
					#$htmlCode[] = $this->getDownloads($i);
					
					// Start table
					$htmlCode[] = $this->api->fe_makeStyledContent('table','termTable',0,1,0,1,array('id'=>$this->prefixId . '-table' . $i,'border'=>0,'width'=>'100%','cellspacing'=>'1','cellpadding'=>'2','align'=>'center'));
					
					// Start row
					$htmlCode[] = '<tr>';
					
					// Empty cell
					$htmlCode[] = '<td width="5%">&nbsp;</td>';
					
					// Create table headers
					for ($j = 0; $j < 8; $j++) {
						$htmlCode[] = $this->api->fe_makeStyledContent('td','headers',$this->pi_getLL('classic.headers.' . $j));
					}
					
					// End row
					$htmlCode[] = '</tr>';
					
					// Process each week
					for ($j = 0; $j < count($this->terms[$i]); $j++) {
						
						// New year
						if ($this->terms[$i][$j] == '1') {
							
							// Increase year
							$this->year++;
							
							// Separation
							if ($this->conf['showYears']) {
								
								// Start row
								$htmlCode[] = '<tr>';
								
								// Empty cell
								$htmlCode[] = '<td width="5%">&nbsp;</td>';
								
								// Year
								$htmlCode[] = $this->api->fe_makeStyledContent('td','yearDiv',$this->year,1,0,0,array('colspan'=>'8'));
								
								// End row
								$htmlCode[] = '</tr>';
							}
						}
						
						// Get event for the week
						$htmlCode[] = $this->getClassicEvent($i,$j);
					}
					
					// End table
					$htmlCode[] = '</table>';
					
				} else {
					
					// Check if term contains a new year
					if ($i == 0 || $i == 2 || $i == 4 || $i == 6) {
						
						// Increase year
						$this->year++;
					}
					
					// No data
					$htmlCode[] = $this->api->fe_makeStyledContent('div','classicView-noData',$this->pi_getLL('term') . ' ' . ($i + 1) . ': ' . $this->pi_getLL('nodata'));
					
				}
			}
			
			// Return content
			return $this->api->fe_makeStyledContent('div','classicView',implode(chr(10),$htmlCode));
		}
		
		/**
		 * 
		 */
		function getClassicEvent($term,$week) {
			
			// Storage
			$htmlCode = array();
			
			// Current term sheet
			$sTERM = 'sTERM' . ($term + 1);
			
			// Modules storage
			$modules = array();
			
			// Check if current term contains data
			if (is_array($this->modules[$sTERM])) {
				
				// Process each domain
				foreach($this->modules[$sTERM] as $domain) {
					
					// Process each module
					foreach($domain as $key=>$mod) {
						
						// Check the formation and the period of the module
						if (($mod['formation'] == '3' || $mod['formation'] == $this->piVars['formation']) && $mod['startweek'] == $week) {
							
							// Check section
							if (!$this->piVars['section'] || $this->piVars['section'] == 0 || in_array($this->sections[$this->piVars['section']],$mod['section'])) {
								
								// Create modules rows
								$modules[$mod['startweek'] . '-' . $mod['startday'] . '-' . uniqid(rand())] = $this->createClassicContentRow($term,$mod,$week);
							}
						}
					}
				}
				
				// Sort modules
				ksort($modules);
				
				// Add modules row
				$htmlCode[] = implode(chr(10),$modules);
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function createClassicContentRow($term,$mod,$week) {
			
			// Storage
			$htmlCode = array();
			
			// Get an array with files
			$files = explode(',',$mod['files']);
			
			// Process each file
			foreach($files as $module) {
				
				// Start row
				$htmlCode[] = '<tr>';
				
				// Week
				$htmlCode[] = $this->api->fe_makeStyledContent('td','weeks',$this->terms[$term][$mod['startweek']] . '-' . $this->terms[$term][$mod['endweek']],1,0,0,array('width'=>'5%'));
				
				// Dates
				$htmlCode[] = $this->api->fe_makeStyledContent('td',$mod['domain'],$this->createClassicContent($term,$mod,'dates'),0,0,0,array('width'=>'10%'));
				
				// Days
				$htmlCode[] = $this->api->fe_makeStyledContent('td',$mod['domain'],$this->createClassicContent($term,$mod,'days'),0,0,0,array('width'=>'10%'));
				
				// Titles
				$htmlCode[] = $this->api->fe_makeStyledContent('td',$mod['domain'],$this->createClassicContent($term,$module,'titles'),0,0,0,array('width'=>'20%'));
				
				// ECTS
				$htmlCode[] = $this->api->fe_makeStyledContent('td',$mod['domain'],$this->createClassicContent($term,$module,'ects'),0,0,0,array('width'=>'5%'));
				
				// Types
				$htmlCode[] = $this->api->fe_makeStyledContent('td',$mod['domain'],$this->createClassicContent($term,$mod,'types'),0,0,0,array('width'=>'10%'));
				
				// Responsabilities
				$htmlCode[] = $this->api->fe_makeStyledContent('td',$mod['domain'],$this->createClassicContent($term,$module,'resp'),0,0,0,array('width'=>'15%'));
				
				// Sections
				$htmlCode[] = $this->api->fe_makeStyledContent('td',$mod['domain'],$this->createClassicContent($term,$module,'sections'),0,0,0,array('width'=>'5%'));
				
				// Comments
				$htmlCode[] = $this->api->fe_makeStyledContent('td',$mod['domain'],$this->createClassicContent($term,$mod,'comments'),0,0,0,array('width'=>'20%'));
				
				// End row
				$htmlCode[] = '</tr>';
			}
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function createClassicContent($term,$mod,$key) {
			
			// Storage
			$htmlCode = array();
			
			// Content type
			switch($key) {
				
				// Dates
				case 'dates':
					
					// Start date
					$htmlCode[] = date('d.m.Y',$this->api->div_week2date(ceil(($mod['startday'] + 1) / 2),$this->terms[$term][$mod['startweek']],$this->year));
					
					// Compute end year
					$endYear = ($this->terms[$term][$mod['startweek']] > $this->terms[$term][$mod['endweek']]) ? $this->year + 1 : $this->year;
					
					// End date
					$htmlCode[] = date('d.m.Y',$this->api->div_week2date(ceil(($mod['endday'] + 1) / 2),$this->terms[$term][$mod['endweek']],$endYear));
				break;
				
				// Days
				case 'days':
					
					// Day of the week (start)
					$weekDay_start = $this->pi_getLL('weekday.' . floor($mod['startday'] / 2));
					
					// Meridiem (start)
					$meridiem_start = (is_int($mod['startday'] / 2)) ? $this->pi_getLL('weekday.am') : $this->pi_getLL('weekday.pm');
					
					// Day of the week (end)
					$weekDay_end = $this->pi_getLL('weekday.' . floor($mod['endday'] / 2));
					
					// Meridiem (end)
					$meridiem_end = (is_int($mod['endday'] / 2)) ? $this->pi_getLL('weekday.am') : $this->pi_getLL('weekday.pm');
					
					// Start day
					$htmlCode[] = $this->api->fe_makeStyledContent('span','weekday',$weekDay_start) . ' ' . $meridiem_start;
					
					// End day
					$htmlCode[] = $this->api->fe_makeStyledContent('span','weekday',$weekDay_end) . ' ' . $meridiem_end;
				break;
				
				// Titles
				case 'titles':
					
					// Check for module file
					if ($mod) {
						
						// Module key
						$modKey = str_replace('.xml','',$mod);
						
						// URL
						$linkURL = (t3lib_div::getIndpEnv('REMOTE_ADDR') == $this->conf['router']) ? $this->conf['internal'] : $this->conf['external'];
						
						// Link
						$htmlCode[] = '<a href="' . $linkURL . '?w_titre=' . urlencode($this->modIndex['index'][$modKey]['title']) . '" title="' . $this->pi_getLL('infoLink') . '" target="_blank">' . $this->cObj->IMAGE(array('file' => $this->conf['infoPic'])) . '</a>';
						
						$overrulePIvars = array('showXML'=>$this->academicYear . $this->yearSuffix . '/' . $mod,'startyear'=>$this->piVars['startyear'],'term'=>$term,'formation'=>$this->piVars['formation']);
						$htmlCode[] = $this->api->fe_linkTP($this->modIndex['index'][$modKey]['title'] . ' (' . $this->modIndex['index'][$modKey]['hes_code'] . '-' . $modKey . ')',array($this->prefixId => $overrulePIvars),1);
					}

				break;
				
				// ECTS
				case 'ects':
					
					// Check for module file
					if ($mod) {
						
						// Module key
						$modKey = str_replace('.xml','',$mod);
						
						$htmlCode[] = $this->modIndex['index'][$modKey]['credits'];
					}
				break;
				
				// ECTS
				case 'sections':
					
					// Check for module file
					if ($mod) {
						
						// Module key
						$modKey = str_replace('.xml','',$mod);
						
						// Select XML attributes
						$attribs = $this->modIndex['index'][$modKey]['sections']['xml-attribs'];
						
						// Storage
						$sections = array();
						
						// Check for an array
						if (is_array($attribs)) {
							
							// Process each attribute
							foreach($attribs as $key=>$value) {
								
								// Check key
								if ($key != 'type' && $value == 1) {
									
									// Add section
									$sections[] = strtoupper($key);
								}
							}
							
							$htmlCode[] = implode(', ',$sections);
						}
					}
				break;
				
				// Responsabilities
				case 'resp':
					
					// Check for module file
					if ($mod) {
						
						// Module key
						$modKey = str_replace('.xml','',$mod);
						
						// Responsabilities
						$resp = $this->modIndex['index'][$modKey]['resp'];
						
						// Storage
						$persons = array();
						
						// Check for an array
						if (is_array($resp)) {
							
							// Process each person
							foreach($resp as $key=>$value) {
								
								// Check array key
								if (is_int($key)) {
									
									// Add person
									$persons[] = $value['firstname'] . ' ' . $value['lastname'];
								}
							}
							
							$htmlCode[] = implode(', ',$persons);
						}
					}
				break;
				
				// Types
				case 'types':
					
					// Add type
					$htmlCode[] = ($mod['type']) ? '<p><strong>' . $this->pi_getLL('module.type.I.' . $mod['type']) . '</strong></p>' : '';
				break;
				
				// Default
				default:
					
					// Write required key
					$htmlCode[] = $mod[$key];
				break;
			}
			
			// Return content
			return $this->api->fe_makeStyledContent('div',$key,nl2br(implode(chr(10),$htmlCode)));
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 5 - MODULE
		 *
		 * Functions for the display of a module XML file.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function showModule($file,$year) {
			
			// Conf
			$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['eesp_modules']);
			
			// Year
			$year = '/' . $year;
			
			// Modules directory
			$fullPath = t3lib_div::fixWindowsFilePath($confArray['modulesDir']);
			
			// Path Values
			$readPath = t3lib_div::getFileAbsFileName($fullPath);
			
			// File path
			$filePath = $readPath . '/' . $this->piVars['showXML'];
			
			// Open file
			if ($xml = @fopen($filePath,'r')) {
				
				// Parse module
				$module = $this->api->div_xml2array(fread($xml,filesize($filePath)));
				
				if (is_array($module)) {
					
					$GLOBALS['TSFE']->indexedDocTitle = $module['module']['title'];
					
					// Srorage
					$htmlCode = array();
					
					// Start div
					$htmlCode[] = $this->api->fe_makeStyledContent('div','header',false,1,0,1);
					
					// Path informations
					$pathInfos = explode('/',$this->piVars['showXML']);
					
					// Academic year
					$academicYear = '20' . substr($pathInfos[1],0,2);
					
					// Title
					$htmlCode[] = $this->api->fe_makeStyledContent('h4','title',$this->pi_getLL('title') . ' ' . $academicYear .'-' . ($academicYear + 1) . ':');
					
					// URL
					$infoLinkURL = (t3lib_div::getIndpEnv('REMOTE_ADDR') == $this->conf['router']) ? $this->conf['internal'] : $this->conf['external'];
					
					// Link
					$infoLink = '<a href="' . $infoLinkURL . '?w_titre=' . urlencode($module['module']['title']) . '" title="' . $this->pi_getLL('infoLink') . '" target="_blank">' . $this->cObj->IMAGE(array('file' => $this->conf['infoPic'])) . '</a>';
					
					// Code
					$htmlCode[] = $this->api->fe_makeStyledContent('h3','code',$module['module']['xml-attribs']['hes_code'] . '-' . $module['module']['xml-attribs']['code'] . ' ' . $infoLink);
					
					// Title
					$htmlCode[] = $this->api->fe_makeStyledContent('h2','title',$module['module']['title']);
					
					// Separation
					$htmlCode[] = '<hr noshade size="1" width="100%" />';
					
					// Sections
					foreach($module['module']['sections']['xml-attribs'] as $key=>$value) {
						
						// Check for a section
						if ($value) {
							$htmlCode[] = $this->api->fe_makeStyledContent('p','section',$this->pi_getLL('sections.' . $key));
						}
					}
					
					// Separation
					$htmlCode[] = '<hr noshade size="1" width="100%" />';
					
					$htmlCode[] = $this->api->fe_makeStyledContent('h4','responsabilities-pedagogy',$this->pi_getLL('responsabilities.pedagogy'));
					
					// Responsabilities (Pedagogy)
					foreach($module['module']['responsabilities'] as $person) {
						
						// Check for a pedagogic responsability
						if ($person['xml-attribs']['resp'] == 'pedagogy') {
							$htmlCode[] = $this->api->fe_makeStyledContent('h3','person',$person['lastname'] . ' ' . $person['firstname']);
						}
					}
					
					// Separation
					$htmlCode[] = '<hr noshade size="1" width="100%" />';
					
					// Domain
					$htmlCode[] = $this->api->fe_makeStyledContent('span','hes-domain',$this->pi_getLL('domain') . ': ' . $module['module']['xml-attribs']['hes_domain']);
					
					// Line break
					$htmlCode[] = '<br />';
					
					// Credits
					$htmlCode[] = $this->api->fe_makeStyledContent('span','credits',$this->pi_getLL('credits') . ': ' . $module['module']['xml-attribs']['credits'] . ' ' . $this->pi_getLL('credits.ects'));
					
					// End div
					$htmlCode[] = '</div>';
					
					// Identification
					foreach($module['module']['identification'] as $key=>$value) {

						
						// Check for a value
						if ($value) {
							$htmlCode[] = $this->api->fe_makeStyledContent('h4','identification-' . $key,$this->pi_getLL('identification.' . $key));
							$htmlCode[] = $this->api->fe_makeStyledContent('p','identification-' . $key,nl2br($value));
						}
					}
					
					// Content
					$htmlCode[] = $this->api->fe_makeStyledContent('h4','content',$this->pi_getLL('content'));
					$htmlCode[] = $this->api->fe_makeStyledContent('p','content',nl2br($module['module']['content']));
					
					// Evaluation
					foreach($module['module']['evaluation'] as $key=>$value) {
						
						// Check for a value
						if ($value) {

							$htmlCode[] = $this->api->fe_makeStyledContent('h4','evaluation-' . $key,$this->pi_getLL('evaluation.' . $key));
							$htmlCode[] = $this->api->fe_makeStyledContent('p','evaluation-' . $key,nl2br($value));
						}
					}
					
					// Remediation
					foreach($module['module']['remediation'] as $key=>$value) {
						
						// Check for a value
						if ($value) {
							$htmlCode[] = $this->api->fe_makeStyledContent('h4','remediation-' . $key,$this->pi_getLL('remediation.' . $key));
							$htmlCode[] = $this->api->fe_makeStyledContent('p','remediation-' . $key,nl2br($value));
						}
					}
					
					// Storage
					$resp_module = array();
					
					// Responsabilities (module)
					foreach($module['module']['responsabilities'] as $key=>$value) {

						
						// Check for a value
						if ($value['xml-attribs']['resp'] == 'module') {
							$resp_module[] = $value['firstname'] . ' ' . $value['lastname'];
						}
					}
					
					$htmlCode[] = $this->api->fe_makeStyledContent('h4','responsabilities-module',$this->pi_getLL('responsabilities.module'));
					
					$htmlCode[] = $this->api->fe_makeStyledContent('p','responsabilities-module',implode(', ',$resp_module));
					
					// DEBUG ONLY - Show module array
					#$htmlCode[] = $this->api->viewArray($module);
					
					// Return module
					return implode(chr(10),$htmlCode);
					
				} else {
					
					// XML error
					return '<strong>' . $module . '</strong>';
				}
				
			} else {
				
				// Wrong file

				return '<strong>' . $this->pi_getLL('xml.nofile') . '</strong>';
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 6 - VCAL EXPORT
		 *
		 * Functions for the export of the calendar.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function vCalExport($year) {
			
			// Storage
			$vCal = array();
			
			// Begin Calendar
			$vCal[] = 'BEGIN:VCALENDAR';
			$vCal[] = 'VESRION:2.0';
			$vCal[] = 'X-WR-CALNAME:EESP - ' . $year;
			$vCal[] = 'PRODID:-//Gad Lab Bros. / macmade.net//EESP//FR';
			#$vCal[] = 'X-WR-RELCALID:';
			$vCal[] = 'X-WR-TIMEZONE:Europe/Zurich';
			$vCal[] = 'CALSCALE:GREGORIAN';
			
			// Process each term
			foreach($this->modules as $key=>$value) {
				
				// Current key in the terms class array
				$termKey = substr($key,5,1) - 1;
				
				// Process each module area
				foreach($value as $mods) {
					
					// Process each module
					foreach($mods as $mod) {
						
						// Formation
						if ($mod['formation'] == '3' || $mod['formation'] == $this->piVars['formation']) {
							
							// Storage
							$days = array();
							
							// Check start & end date
							if ($mod['startday'] < $mod['endday']) {
								
								// Get all event days
								for ($i = $mod['startday']; $i < $mod['endday'] + 1; $i++) {
									
									// Add day reference
									$days[] =  $this->days[floor($i / 2)];
								}
								
							} else if ($mod['startday'] > $mod['endday']) {
								
								// Get all event days
								for ($i = $mod['endday']; $i < $mod['startday'] + 1; $i++) {
									
									// Add day reference
									$days[] =  $this->days[floor($i / 2)];
								}
							} else {
								
								// Add day reference
								$days[] = $this->days[$mod['startday']];
							}
							
							// Keep only unique values
							$days = array_unique($days);
							
							// Check terms width a year change
							if (($termKey == 0 || $termKey == 2 || $termKey == 4 || $termKey == 6)) {
								
								// Start year
								$startYear = ($this->terms[$termKey][$mod['startweek']] < 43) ? $year + ($key / 2) + 1 : $year + ($key / 2);
								
								// End year
								$endYear = ($this->terms[$termKey][$mod['endweek']] < 43) ? $year + ($key / 2) + 1 : $year + ($key / 2);
								
							} else {
								
								// Start year
								$startYear = $year + ceil($key / 2);
								
								// End year
								$endYear = startYear;
							}
							
							// Module type
							$type = ($mod['type']) ? $this->pi_getLL('module.type.I.' . $mod['type']) . '\n' : '';
							
							// Module comments
							$comments = ($mod['comments']) ? str_replace(chr(10),'\n',$mod['comments']) : '';
							
							// Get start tstamp
							$tsStart = $this->api->div_week2date($mod['startday'] + 1,$this->terms[$termKey][$mod['startweek']],$startYear);
							
							// Get end tstamp
							$tsEnd = $this->api->div_week2date($mod['endday'] + 1,$this->terms[$termKey][$mod['endweek']],$endYear);
							
							// Start event
							$vCal[] = 'BEGIN:VEVENT';
							
							// Location
							$vCal[] = 'LOCATION:EESP - Lausanne';
							
							// DTSTAMP
							#$vCal[] = 'DTSTAMP:';
							
							// UID
							#$vCal[] = 'UID:';
							
							// Sequence
							#$vCal[] = 'SEQUENCE:0';
							
							// URL
							$vCal[] = 'URL;VALUE=URI:' . t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->pi_linkTP_keepPIvars_url(array('vcal'=>0));
							
							// Status
							$vCal[] = 'STATUS:CONFIRMED';
							
							// Start date
							$vCal[] = 'DTSTART;TZID=Europe/Zurich:' . date('Ymd',$tsStart) . 'T083000';
							
							// End date
							$vCal[] = 'DTEND;TZID=Europe/Zurich:' . date('Ymd',$tsStart) . 'T170000';
							
							// Summary
							$vCal[] = 'SUMMARY:' . $mod['domain'];
							
							// Description
							$vCal[] = 'DESCRIPTION:' . $type . $comments;
							
							// Recurrence rule
							$vCal[] = 'RRULE:FREQ=WEEKLY;INTERVAL=1;UNTIL=' . date('Ymd',$tsEnd) . 'T170000;BYDAY=' . implode(',',$days);
							
							// End event
							$vCal[] = 'END:VEVENT';
						}
					}
				}
			}
			
			// End calendar
			$vCal[] = 'END:VCALENDAR';
			
			// Return calendar
			return implode(chr(10),$vCal);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 7 - UTILITIES
		 *
		 * General purposes functions.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function buildTerms($year) {
			
			// Storage
			$terms = array();
			
			// Term 1 start week
			$term1Start = $this->program['term1_startweek'];
			
			// Term 2 start week
			$term2Start = $this->program['term2_startweek'];
			
			// Term counter
			$termCount = 0;
			
			// Process each years
			for ($i = 0; $i < 4; $i++) {
				
				// Increment year
				$year += $i;
				
				// Timestamp for the first january of the year
				$tstampStart = mktime(0,0,0,01,01,$year);
				
				// Day number
				$day = date('w',$tstampStart);
				
				if ($day == 4 && date('L',$tstampStart) == 0) {
					
					// 53 weeks in the year
					$weeks = 53;
					
				} else if (($day == 4 || $day == 4) && date('L',$tstampStart) == 1) {
					
					// 53 weeks in the year
					$weeks = 53;
					
				} else {
					
					// 52 weeks in the year
					$weeks = 52;
				}
				
				// Create terms array for the current year
				$terms[$termCount] = array();
				$terms[$termCount + 1] = array();
				
				// Counter
				$counter = $term1Start;
				
				// Process each week
				for ($j = 0; $j < $weeks; $j++) {
					
					// Check the current term
					if ($counter < $term2Start || $counter >= $term1Start) {
						
						// Term 1
						$terms[$termCount][] = $counter;
						
					} else {
						
						// Term 2
						$terms[$termCount + 1][] = $counter;
					}
					
					// Increase or reset counter
					$counter = ($counter == $weeks) ? 1 : $counter + 1;
				}
				
				// Increase term count
				$termCount += 2;
			}
			
			// Create class array
			$this->terms = $terms;
			
			// DEBUG ONLY - Show terms array
			#$this->api->debug($this->terms,'TERMS');
		}
		
		/**
		 * Parse an XML calendar.
		 * 
		 * This function parse the XML data of a graduate programs and
		 * stores each module area in a class array entry.
		 * 
		 * @param		$xmlData			The XML data of the calendat
		 * @return		Void
		 */
		function parseCal($xmlData='') {
			
			// Get XML field as an array
			$xmlds = $this->api->div_xml2array($xmlData,0);
			
			// Check array
			if (is_array($xmlds)) {
				
				// Process each term
				foreach($xmlds['T3FlexForms']['data'] as $termKey=>$termValue) {
					
					// Create term array
					$term = array();
					$termFiles = array(
						'0' => array(),
						'1' => array(),
					);
					
					// Check array
					if (is_array($termValue) && array_key_exists('lDEF',$termValue) && is_array($termValue['lDEF']) && array_key_exists('fields',$termValue['lDEF']) && is_array($termValue['lDEF']['fields']) && array_key_exists('el',$termValue['lDEF']['fields']) && is_array($termValue['lDEF']['fields']['el'])) {
						
						// Process each module area
						foreach($termValue['lDEF']['fields']['el'] as $modValue) {
							
							// Check the kind of information
							if (array_key_exists('modules',$modValue)) {
								
								// Domain
								$domain = $modValue['modules']['el']['domain']['vDEF'];
								
								// Array key
								$domKey = $this->priorities[$domain];
								
								// Create domain array if required
								if (!is_array($term[$domKey])) {
									$term[$domKey] = array();
								}
								
								// Create module array
								$term[$domKey][] = array(
									'formation' => $modValue['modules']['el']['formation']['vDEF'],
									'type' => $modValue['modules']['el']['type']['vDEF'],
									'domain' => $modValue['modules']['el']['domain']['vDEF'],
									'section' => explode(',',$modValue['modules']['el']['section']['vDEF']),
									'startweek' => intval($modValue['modules']['el']['startweek']['vDEF']),
									'endweek' => intval($modValue['modules']['el']['endweek']['vDEF']),
									'startday' => $modValue['modules']['el']['startday']['vDEF'],
									'endday' => $modValue['modules']['el']['endday']['vDEF'],
									'files' => $modValue['modules']['el']['files']['vDEF'],
									'comments' => $modValue['modules']['el']['comments']['vDEF'],
									'domKey' => $domKey,
									'display' => 0,
								);
								
							} else if (array_key_exists('pdf',$modValue)) {
								
								$termFiles[$modValue['pdf']['el']['formation']['vDEF']][] = array(
									'title' => $modValue['pdf']['el']['title']['vDEF'],
									'file' => $modValue['pdf']['el']['file']['vDEF'],
								);
								
							}
						}
					}
					
					// Terms array key
					$termsArrKey = substr($termKey,5) - 1;
					
					// Check if current term can contain a 53rd week but does not
					if (($termsArrKey == 0 || $termsArrKey == 2 || $termsArrKey == 4 || $termsArrKey == 6) && !in_array('53',$this->terms[$termsArrKey])) {
						
						// Process each domain
						foreach($term as $domain=>$modules) {
							
							// Process each module area
							foreach($modules as $key=>$value) {
								
								// Check end week
								if ($term[$domain][$key]['endweek'] >= 10) {
									$term[$domain][$key]['endweek']--;
								}
								
								// Check start week
								if ($term[$domain][$key]['startweek'] >= 10) {
									$term[$domain][$key]['startweek']--;
								}
							}
						}
					}
					
					// Sort modules
					ksort($term);
					
					// Memorize term in a class variable
					$this->modules[$termKey] = $term;
					
					// Memorize term files in a class variable
					$this->files[$termKey] = $termFiles;
				}
			}
			
			// DEBUG ONLY - Show modules array
			#$this->api->debug($this->modules,'MODULES');
			
			// DEBUG ONLY - Show files array
			#$this->api->debug($this->files,'FILES');
		}
		
		/**
		 * Adds JavaScript Code.
		 * 
		 * This function adds the javascript code used to switch between
		 * CSS classes and to expand/collapse all sections.
		 * 
		 * @return		Void.
		 */
		function buildJSCode() {
			
			// Storage
			$jsCode = array();
			
			// Function for higlighting cells
			$jsCode[] = 'function ' . $this->prefixId . '_showCells(table,element,className) {';
			$jsCode[] = '	if (document.getElementById && document.getElementsByTagName) {';
			$jsCode[] = '		termTable = document.getElementById(table);';
			$jsCode[] = '		termCells = termTable.getElementsByTagName("TD");';
			$jsCode[] = '		eventId = element.substring(0,3);';
			$jsCode[] = '		for (i = 0; i < termCells.length; i++) {';
			$jsCode[] = '			if (termCells[i].id.substring(0,3) == eventId) {';
			$jsCode[] = '				termCells[i].className = className;';
			$jsCode[] = '			}';
			$jsCode[] = '		}';
			$jsCode[] = '	}';
			$jsCode[] = '}';
			
			// Function for showing info divs
			$jsCode[] = 'function ' . $this->prefixId . '_showInfo(element,term) {';
			$jsCode[] = '	if (document.getElementById && document.getElementsByTagName) {';
			$jsCode[] = '		infoCell = document.getElementById("' . $this->prefixId . '-infoBox-" + term);';
			$jsCode[] = '		divs = infoCell.getElementsByTagName("DIV");';
			$jsCode[] = '		for (i = 0; i < divs.length; i++) {';
			$jsCode[] = '			divs[i].className = "hidden";';
			$jsCode[] = '		}';
			$jsCode[] = '		document.getElementById(element).className = "visible";';
			$jsCode[] = '	}';
			$jsCode[] = '}';
			
			// Adds JS code
			$GLOBALS['TSFE']->setJS($this->prefixId,implode(chr(10),$jsCode));
		}
		
		/**
		 * 
		 */
		function createTermMenu() {
			
			// Storage
			$htmlCode = array();
			
			// Start table
			$htmlCode[] = $this->api->fe_makeStyledContent('table','subMenuTable',0,1,0,1,array('border'=>0,'width'=>'100%','cellspacing'=>'1','cellpadding'=>'2','align'=>'center'));
			
			// Start row
			$htmlCode[] = '<tr>';
			
			// Build a link to each term table
			for($i = 0; $i < 8; $i++) {
				
				// Cell
				$htmlCode[] = '<td><a href="#' . $this->prefixId . '-term' . $i . '">' . $this->pi_getLL('term') . ' ' . ($i + 1) . '</a></td>';
			}
			
			// Top link
			$htmlCode[] = '<td><a href="#' . $this->prefixId . '-top">' . $this->pi_getLL('toplink') . '</a></td>';
			
			// End row
			$htmlCode[] = '</tr>';
			
			// End table
			$htmlCode[] = '</table>';
			
			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function getDownloads($term) {
			
			$htmlCode = array();
			
			$termKey = 'sTERM' . ($term + 1);
			$formKey = $this->piVars['formation'] - 1;
			
			if (is_array($this->files[$termKey]) && count($this->files[$termKey][$formKey])) {
				
				// Header
				$htmlCode[] = '<h2>' . $this->pi_getLL('downloads') . '</h2>';
				
				$htmlCode[] = '<ul>';
				foreach($this->files[$termKey][$formKey] as $file) {
					
					$htmlCode[] = '<li><a href="/uploads/tx_eespmodules/' . $file['file'] . '">' . $file['title'] . '</a></li>';
				}
				$htmlCode[] = '</ul>';
			}
			
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function readIndexFile() {
			
			// Storage
			$fullIndex = array();
			
			// Conf
			$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['eesp_modules']);
			
			// Year
			$year = $this->year;
			
			// Directory storage
			$dirs = array();
			
			// Process years
			for($i = $year; $i < $year + 4; $i++) {
				
				// Path
				$path = $confArray['modulesDir'] . '/' . $i . $this->yearSuffix;
				
				// Modules directory
				$dirs[] = t3lib_div::fixWindowsFilePath($path);
			}
			
			// Process directories
			foreach($dirs as $fullPath) {
				
				// Path Values
				$readPath = t3lib_div::getFileAbsFileName($fullPath);
				
				// Check directory
				if (@is_dir($readPath)) {
					
					// Check for an index file
					if (!@is_file($readPath . '/index.xml')) {
						
						// Create index file
						$this->createModuleIndex($readPath);
					}
					
					// Try to open index file
					if ($index = @fopen($readPath . '/index.xml','r')) {
						
						// Buffer
						$buffer = '';
						
						// Read file
						while (!feof($index)) {
							
							// Add current line
							$buffer .= fgets($index,4096);
						}
						
						// Close file
						fclose($index);
						
						// Store content
						$fullIndex = array_merge_recursive($fullIndex,$this->api->div_xml2array($buffer));
					}
				}
			}
			
			// Store index
			$this->modIndex = $fullIndex;
			
			// DEBUG ONLY - Show index array
			#t3lib_div::debug($fullIndex);
		}
		
		/**
		 * Create the module index.
		 * 
		 * This function creates an XML index file with specific module informations, that can be
		 * read for a quick access.
		 * 
		 * @param		$readPath			The path to the directory containing the modules XML files
		 * @return		Void
		 */
		function createModuleIndex($readPath) {
			
			// Storage
			$modules = array();
			
			// Get all files
			$files = t3lib_div::getFilesInDir($readPath,'xml');
			
			// Process files
			foreach($files as $path) {
				
				// Open file
				$fd = fopen($readPath . '/' . $path,'r');
				
				// Buffer
				$buffer = '';
				
				// Read file
				while (!feof($fd)) {
					
					// Add current line
					$buffer .= fgets($fd,4096);
				}
				
				// Close file
				fclose($fd);
				
				// Store content
				$modules[$path] = $buffer;
			}
			
			// Modules index
			$index = array();
			
			// Check modules array
			if (count($modules)) {
				
				// Process modules array
				foreach($modules as $key=>$value) {
					
					// Convert XML to PHP array
					$mod = $this->api->div_xml2array($value);
					
					// Module key
					$modKey = $mod['module']['xml-attribs']['code'];
					
					// Add index for current module
					$index[$modKey] = array(
						'file' => $key,
						'title' => $mod['module']['title'],
						'hes_code' => $mod['module']['xml-attribs']['hes_code'],
						'credits' => $mod['module']['xml-attribs']['credits'],
						'sections' => $mod['module']['sections'],
						'resp' => array(),
					);
					
					// Check responsabilities
					if (is_array($mod['module']['responsabilities'])) {
						
						// Process responsabilities
						foreach($mod['module']['responsabilities'] as $person) {
							
							// Check responsability type
							if ($person['xml-attribs']['resp'] == 'pedagogy') {
								
								// Add person
								$index[$modKey]['resp'][] = array(
									'firstname' => $person['firstname'],
									'lastname' => $person['lastname'],
								);
							}
						}
					}
				}
				
				// Open index file
				$xml = fopen($readPath . '/index.xml','w');
				
				// Write content
				fwrite($xml,$this->api->div_array2xml($index,'index'));
				
				// Close file
				fclose($xml);
			}

		}
	}
	
	// XClass inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_modules/pi1/class.tx_eespmodules_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_modules/pi1/class.tx_eespmodules_pi1.php']);
	}
?>
