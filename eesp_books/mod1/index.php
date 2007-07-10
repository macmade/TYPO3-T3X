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
	 * Module 'EESP Books' for the 'eesp_books' extension.
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
	 * SECTION:		3 - OVERVIEW
	 *     318:		function overView
	 * 
	 * SECTION:		8 - UTILITIES
	 *    1161:		function printContent
	 *    1176:		function writeHTML($text,$tag='p',$style=false)
	 * 
	 *				TOTAL FUNCTIONS: 
	 */
	
	// Default initialization of the module
	unset($MCONF);
	require ('conf.php');
	require ($BACK_PATH . 'init.php');
	require ($BACK_PATH . 'template.php');
	$LANG->includeLLFile('EXT:eesp_books/mod1/locallang.php');
	require_once (PATH_t3lib . 'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF,1);
	
	// Developer API class
	require_once (t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');
	
	// TCE class
	require_once (PATH_t3lib . 'class.t3lib_tcemain.php');
	
	class tx_eespbooks_module1 extends t3lib_SCbase {
		
		var $pageinfo;
		var $apimacmade_version = 2.6;
		
		
		
		
		
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
			
			// New instance of the Developer API
			$this->api = new tx_apimacmade($this);
			
			// Access check
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;
			
			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {
				
				// Draw the header
				$this->doc = t3lib_div::makeInstance('bigDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->doc->form = '<form action="" method="POST">';
				
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
					<script src="scripts.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
				';
				
				$this->doc->postCode = '
					<script type="text/javascript" language="Javascript" charset="iso-8859-1">
						<!--
						script_ended = 1;
						if (top.fsMod) {
							top.fsMod.recentIds["web"] = ' . intval($this->id).';
						}
						//-->
					</script>
				';
				
				// Init CSM menu
				$this->api->be_initCSM();
				
				// Build current path
				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']) . '<br>' . $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path') . ': ' . t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
				
				// Start page content
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				$this->content .= $this->doc->divider(5);
				
				
				// Render content:
				$this->moduleContent();
				
				// Adds shortcut
				if ($BE_USER->mayMakeShortcut())	{
					$this->content .= $this->doc->spacer(20) . $this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
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
		 * SECTION 2 - MAIN
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
			
			// Check data submission
			if (t3lib_div::_POST('submit')) {
				
				// Process data
				$this->processData(t3lib_div::_POST('data'));
			}
			
			// Get monthes
			$this->getMonthes();
			
			// Menu items
			$this->MOD_MENU = Array (
				'function' => Array (
					$LANG->getLL('menu.func.1'),
				)
			);
			
			// Check for monthes
			if (is_array($this->monthes)) {
				
				// Add monthes
				foreach($this->monthes as $key=>$value) {
					
					// Add month to menu
					$this->MOD_MENU['function'][$value['uid']] = $LANG->getLL('monthes.' . $value['stock_month']) . ' ' . $value['stock_year'];
				}
			}
			
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
			global $LANG;
			
			// Check for records
			if (is_array($this->monthes)) {
				
				// Server(s) found
				switch ($this->MOD_SETTINGS['function']) {
					
					// Overview
					case '0':
						$this->content .= $this->overView();
					break;
					
					// Month view
					default:
						$this->content .= $this->showMonth($this->MOD_SETTINGS['function']);
					break;
				}
			} else {
				
				// No server found
				$this->content .= $this->createMonth();
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - OVERVIEW
		 *
		 * Basic view.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function overView() {
			global $LANG;
			
			// Storage
			$htmlCode = array();
			
			// First month
			$firstMonth = $this->monthes[0];
			
			// Current time
			$time = time();
			
			// Current month
			$month = date('m',$time);
			
			// Current year
			$year = date('Y',$time);
			
			// Check first month
			if ($firstMonth['stock_month'] < $month && $firstMonth['stock_year'] <= $year) {
				
				// Create current month
				$htmlCode[] = $this->createMonth();
			}
			
			// Start section
			$htmlCode[] = $this->doc->sectionBegin();
			
			// Title
			$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('overview'));
			
			// Start list
			$htmlCode[] = '<ul>';
			
			// Process monthes
			foreach($this->monthes as $key=>$value) {
				
				// Text to display
				$text = $LANG->getLL('monthes.' . $value['stock_month']) . ' ' . $value['stock_year'];
				
				// Link for the month view
				$href = t3lib_div::linkThisScript(array('SET[function]'=>$value['uid']));
				
				// Add list item
				$htmlCode[] = '<li><a href="' . $href . '">' . $text . '</a></li>';
			}
			
			// End list
			$htmlCode[] = '</ul>';
			
			// End section
			$htmlCode[] = $this->doc->sectionEnd();
			
			// Return HTML code
			return implode(chr(10),$htmlCode);
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 3 - OVERVIEW
		 *
		 * Basic view.
		 ***************************************************************/
		
		/**
		 * 
		 */
		function showMonth($monthID) {
			global $LANG;
			
			// Get month
			$month = t3lib_BEfunc::getRecord('tx_eespbooks_monthes',$monthID);
			
			// Working table
			$table = 'tx_eespbooks_books';
			
			// Additional MySQL WHERE clause for selecting records
			$addWhere = ' AND copies!=0';
			
			// Get available books from PID
			$this->books = t3lib_BEfunc::getRecordsByField($table,'pid',$this->id,$addWhere,false,'bookid DESC');
			
			// Check books
			if (is_array($this->books)) {
				
				// Storage
				$htmlCode = array();
				
				// Module GET variables
				$getVars = t3lib_div::_GET($GLOBALS['MCONF']['name']);
				
				// Check edition
				if (is_array($getVars) && array_key_exists('editStock',$getVars)) {
					
					// Create edit form
					$htmlCode[] = $this->editStock($getVars['editStock']);
				}
				
				// Begin section
				$htmlCode[] = $this->doc->sectionBegin();
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(10);
				
				// Title
				$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('monthes.' . $month['stock_month']) . ' ' . $month['stock_year']);
				
				// Spacer
				$htmlCode[] = $this->doc->spacer(10);
				
				// Counters
				$colorcount = 0;
				$rowcount = 0;
				
				// Start table
				$htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
				
				// Headers style
				$headerStyle = array('font-size: 9px','text-align: center');
				
				// Table headers
				$htmlCode[] = '<tr>';
				$htmlCode[] = $this->writeHTML('','td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('book.id'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('book.title'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.sales_reception'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.sales_bill'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.sales_free'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.students_distribution'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.students_exchange'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.events_out'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.events_back'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.send_albert'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.send_cid'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.back_students'),'td',$headerStyle);
				$htmlCode[] = $this->writeHTML($LANG->getLL('stock.back_cid'),'td',$headerStyle);
				$htmlCode[] = '</tr>';
				
				// Process books
				foreach($this->books as $key=>$value) {
					
					// Working table
					$stock_table = 'tx_eespbooks_stock';
					
					// Additional MySQL WHERE clause for selecting records
					$stock_addWhere = ' AND rel_book=' . $value['uid'] . ' AND rel_month=' . $monthID;
					
					// Get stock
					$stockRows = t3lib_BEfunc::getRecordsByField($stock_table,'pid',$this->id,$stock_addWhere);
					
					// Check stock
					if (!is_array($stockRows)) {
						
						// Stock data
						$data = array(
							$stock_table => array(
								uniqid('NEW') => array(
									'pid' => $this->id,
									'rel_book' => $value['uid'],
									'rel_month' => $monthID,
									'stock.sales_reception' => '0',
									'stock.sales_bill' => '0',
									'stock.sales_free' => '0',
									'stock.students_distribution' => '0',
									'stock.students_exchange' => '0',
									'stock.events_out' => '0',
									'stock.events_back' => '0',
									'stock.send_albert' => '0',
									'stock.send_cid' => '0',
									'stock.back_students' => '0',
									'stock.back_cid' => '0',
								),
							),
						);
						
						// Create stock
						$this->processData($data);
						
						// Get stock
						$stockRows = t3lib_BEfunc::getRecordsByField($stock_table,'pid',$this->id,$stock_addWhere);
					}
					
					// Stock infos
					$stock = array_shift($stockRows);
					
					// Color
					$colorcount = ($colorcount == 1) ? 0: 1;
					$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
					
					// TR parameters
					$tr_params = ' bgcolor="' . $color . '"';
					
					// Storage
					$row = array();
					
					// Icon
					$row[] = $this->writeHTML($this->api->be_getRecordCSMIcon($table,$value,$GLOBALS['BACK_PATH']),'td');
					
					// ID
					$row[] = $this->writeHTML($value['bookid'],'td');
					
					// Stock edit link
					$editStock = t3lib_div::linkThisScript(array($GLOBALS['MCONF']['name'] . '[editStock]'=>$stock['uid']));
					
					// Title
					$row[] = $this->writeHTML('<a href="' . $editStock . '">' . $value['title'] . '</a>','td');
					
					// Stock informations
					$row[] = $this->writeHTML($stock['sales_reception'],'td');
					$row[] = $this->writeHTML($stock['sales_bill'],'td');
					$row[] = $this->writeHTML($stock['sales_free'],'td');
					$row[] = $this->writeHTML($stock['students_distribution'],'td');
					$row[] = $this->writeHTML($stock['students_exchange'],'td');
					$row[] = $this->writeHTML($stock['events_out'],'td');
					$row[] = $this->writeHTML($stock['events_back'],'td');
					$row[] = $this->writeHTML($stock['send_albert'],'td');
					$row[] = $this->writeHTML($stock['send_cid'],'td');
					$row[] = $this->writeHTML($stock['back_students'],'td');
					$row[] = $this->writeHTML($stock['back_cid'],'td');
					
					// Add row
					$htmlCode[] = '<tr ' . $tr_params . '>' . implode(chr(10),$row) . '</tr>';
					
					// Increase row count
					$rowcount++;
				}
				
				// End table
				$htmlCode[] = '</table>';
				
				// Divider
				$htmlCode[] = $this->doc->divider(5);
				
				// End section
				$htmlCode[] = $this->doc->sectionEnd();
				
			} else {
				
				// Start section
				$htmlCode[] = $this->doc->sectionBegin();
				
				// End section
				$htmlCode[] = $this->doc->sectionEnd();
			}
			
			// Return code
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * 
		 */
		function editStock($uid) {
			global $LANG;
			
			// Tables
			$table = 'tx_eespbooks_stock';
			
			// Get stock
			$stock = t3lib_BEfunc::getRecord($table,$uid);
			
			// Check stock
			if (is_array($stock)) {
				
				// Get book
				$book = t3lib_BEfunc::getRecord('tx_eespbooks_books',$stock['rel_book']);
				
				// Check book
				if (is_array($book)) {
					
					// Storage
					$htmlCode = array();
					
					// Begin section
					$htmlCode[] = $this->doc->sectionBegin();
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Spacer
					$htmlCode[] = $this->doc->spacer(10);
					
					// Start table
					$htmlCode[] = '<table border="0" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
					
					// Start row
					$htmlCode[] = '<tr>';
					
					// Title
					$htmlCode[] = $this->writeHTML($book['title'],'td',array('font-weight: bold','text-align: center'),array('colspan'=>'2'));
					
					// End row
					$htmlCode[] = '</tr>';
					
					// Counters
					$colorcount = 0;
					$rowcount = 0;
					
					// Fields to display
					$fields = array('sales_reception','sales_bill','sales_free','students_distribution','students_exchange','events_out','events_back','send_albert','send_cid','back_students','back_cid');
					
					foreach($fields as $field) {
						
						// Color
						$colorcount = ($colorcount == 1) ? 0: 1;
						$color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
						
						// TR parameters
						$tr_params = ' bgcolor="' . $color . '"';
						
						// Storage
						$row = array();
						
						// Label
						$row[] = $this->writeHTML($LANG->getLL('stock.' . $field),'td');
						
						// Input
						$row[] = $this->writeHTML('<input name="data[' . $table . '][' . $uid . '][' . $field . ']" type="text" value="' . $stock[$field] . '" size="5">','td');
						
						
					
						// Add row
						$htmlCode[] = '<tr ' . $tr_params . '>' . implode(chr(10),$row) . '</tr>';
						
						// Increase row count
						$rowcount++;
					}
					
					// Start row
					$htmlCode[] = '<tr>';
					
					// Title
					$htmlCode[] = $this->writeHTML('<input name="submit[month]" type="submit" value="' . $LANG->getLL('submit.stock') . '">','td',array('text-align: center'),array('colspan'=>'2'));
					
					// End row
					$htmlCode[] = '</tr>';
					
					// End table
					$htmlCode[] = '</table>';
					
					// End section
					$htmlCode[] = $this->doc->sectionEnd();
					
					// Return code
					return implode(chr(10),$htmlCode);
				}
			}
		}
		
		
		
		
		
		/***************************************************************
		 * SECTION 8 - UTILITIES
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
		
		/**
		 * Writes some code.
		 * 
		 * This function writes a text wrapped inside an HTML tag.
		 * 
		 * @param		$text				The text to write
		 * @param		$tag				The HTML tag to write
		 * @param		$style				An array containing the CSS styles for the tag
		 * @param		$style				An array containing the tag parameters
		 * @return		The text wrapped in the HTML tag
		 */
		function writeHTML($text,$tag='p',$style=false,$params=false) {
			
			// Create param tags
			if (is_array($params)) {
				$paramTag = ' ' . $this->api->div_writeTagParams($params);
			}
			
			// Create style tag
			if (is_array($style)) {
				$styleTag = ' style="' . implode('; ',$style) . '"';
			}
			
			// Return HTML text
			return '<' . $tag . $styleTag . $paramTag . '>' . $text . '</' . $tag . '>';
		}
		
		/**
		 * 
		 */
		function processData($data) {
			
			// Check data
			if (is_array($data)) {
				
				// Check for existing TCE object
				if (!isset($this->TCE)) {
					
					// New TCE object
					$this->TCE = t3lib_div::makeInstance('t3lib_TCEmain');
				}
				
				// Stripslashes option
				$this->TCE->stripslashes_values = 0;
				
				// Add data
				$this->TCE->start($data,array());
				
				// Process data
				$this->TCE->process_datamap('all');
			}
		}
		
		/**
		 * 
		 */
		function getMonthes() {
			
			// Working table
			$table = 'tx_eespbooks_monthes';
			
			// Additional MySQL WHERE clause for selecting records
			$addWhere = '';
			
			// Get monthes from PID
			$this->monthes = t3lib_BEfunc::getRecordsByField($table,'pid',$this->id,$addWhere,false,'stock_year,stock_month DESC');
		}
		
		/**
		 * 
		 */
		function createMonth() {
			global $LANG;
			
			// Current time
			$time = time();
			
			// Storage
			$htmlCode = array();
			
			// Start section
			$htmlCode[] = $this->doc->sectionBegin();
			
			// Title
			$htmlCode[] = $this->doc->sectionHeader($LANG->getLL('month.new'));
			
			// New flag
			$prefix = '[tx_eespbooks_monthes][' . uniqid('NEW') . ']';
			
			// Start DIV
			$htmlCode[] = '<div style="border: dashed 1px #666666; background-color: ' . $this->doc->bgColor4 . '; padding: 5px;">';
			
			// Month creation
			$htmlCode[] = $this->dateSelect($prefix,date('Y',$time),date('Y',$time),date('m/d/Y',$time));
			
			// Submit
			$htmlCode[] = '<input name="submit[month]" type="submit" value="' . $LANG->getLL('submit.month') . '">';
			
			// Hidden fields
			$htmlCode[] = '<input type="hidden" name="data' . $prefix . '[pid]" value="' . $this->id . '">';
			
			// End DIV
			$htmlCode[] = '</div>';
			
			// End section
			$htmlCode[] = $this->doc->sectionEnd();
			
			// Return HTML code
			return implode(chr(10),$htmlCode);
		}
		
		/**
		 * Create a date selector
		 * 
		 * This function is used to round a number to either 0 or 50.
		 * 
		 * @param		$prefix		The prefix to use for the select tags
		 * @param		$yearStart	The starting year
		 * @param		$yearStop	The ending year
		 * @return		The date selector
		 */
		function dateSelect($prefix,$yearStart,$yearStop,$selected=false) {
			global $LANG;
			
			// Check for a selected item
			if ($selected) {
				
				// Timestamp
				$ts = strtotime($selected);
				
				// Month
				$month = date('m',$ts);
				
				// Year
				$year = date('Y',$ts);
			}
			
			// Select names
			$monthSelect = 'data' . $prefix . '[stock_month]';
			$yearSelect = 'data' . $prefix . '[stock_year]';
			
			// Storage
			$monthItems = array();
			$yearItems = array();
			
			// Create monthes
			for ($i = 1; $i < 13; $i++) {
				
				// Parameters
				$params = array('value'=>$i);
				
				// Check selected state
				if (isset($month) && $month == $i) {
					
					// Add selected parameter
					$params['selected'] = 'selected';
				}
				
				// Create option tag
				$monthItems[] = $this->writeHTML($LANG->getLL('monthes.' . $i),'option',false,$params);
			}
			
			// Create years
			for ($i = $yearStart; $i < ($yearStop + 1); $i++) {
				
				// Parameters
				$params = array('value'=>$i);
				
				// Check selected state
				if (isset($year) && $year == $i) {
					
					// Add selected parameter
					$params['selected'] = 'selected';
				}
				
				// Create option tag
				$yearItems[] = $this->writeHTML($i,'option',false,$params);
			}
			
			// Full selects
			$monthes = $this->writeHTML(implode(chr(10),$monthItems),'select',false,array('name'=>$monthSelect));
			$years = $this->writeHTML(implode(chr(10),$yearItems),'select',false,array('name'=>$yearSelect));
			
			// Return selects
			return $monthes . $years;
		}
	}
	
	// XCLASS inclusion
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_books/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_books/mod1/index.php']);
	}
	
	// Make instance
	$SOBE = t3lib_div::makeInstance('tx_eespbooks_module1');
	$SOBE->init();
	
	// Include files
	foreach($SOBE->include_once as $INC_FILE) {
		include_once($INC_FILE);
	}
	
	$SOBE->main();
	$SOBE->printContent();
?>
