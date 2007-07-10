<!--
	/***************************************************************
	 * Copyright notice
	 * 
	 * (c) 2004 macmade.net
	 * All rights reserved
	 * 
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 2
	 * of the License, or (at your option) any later version.
	 * 
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html
	 * 
	 * This copyright notice MUST APPEAR in all copies of the script!
	 **************************************************************/
	
	/**
	 * JavaScript functions.
	 * 
	 * @author		Jean-David Gadina <info@macmade.net>
	 * @version		3.1
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      77:		function setRowColor(row,id,action,color1,color2)
	 *     137:		function checkBoxList(field)
	 * 
	 * 				TOTAL FUNCTIONS: 2
	 */
	
	
	
	
	
	/***************************************************************
	 * SECTION 0 - VARIABLES
	 *
	 * Internal variables.
	 ***************************************************************/
	 
	// Row storage
	var rows = new Array();
	
	// Check all flag
	var check = 0;
	
	
	/***************************************************************
	 * SECTION 1 - MAIN
	 *
	 * Main functions.
	 ***************************************************************/
	
	/**
	 * Change row bgColor.
	 * 
	 * This function change the background color for the specified row.
	 * The color will depend of the given action (rollover or click).
	 * 
	 * @param		row					The row to process
	 * @param		id					The row id
	 * @param		action				The action to process
	 * @param		color1				The new color
	 * @param		color2				The old color
	 * @return		Void
	 */
	function setRowColor(row,id,checkId,action,color1,color2) {
		
		// Check action
		if (action == "click") {
			
			// Check row state
			if (rows[id] == "enabled") {
				
				// Disable row
				rows[id] = "disabled";
				
				// Change color
				row.bgColor = color2;
				
				// Checkboxes
				checkbox = eval("document.forms[0]." + checkId);
				
				// Check for multiple checkboxes
				if (checkbox.length) {
					
					// Disable checkbox
					checkbox[id].checked = 0;
					
				} else {
					
					// Disable checkbox
					checkbox.checked = 0;
				}
			} else {
				
				// Enable row
				rows[id] = "enabled";
				
				// Change color
				row.bgColor = color1;
				
				// Checkboxes
				checkbox = eval("document.forms[0]." + checkId);
				
				// Check for multiple checkboxes
				if (checkbox.length) {
					
					// Enable checkbox
					checkbox[id].checked = 1;
					
				} else {
					
					// Enable checkbox
					checkbox.checked = 1;
				}
			}
		} else if (rows[id] != "enabled") {
			
			// Change color
			row.bgColor = color1;
		}
	}
	/**
	 * Select / Unselect all checkboxes.
	 * 
	 * This function select or unselect all checkboxes in the
	 * specified group.
	 * 
	 * @param		field				The field group to process
	 * @return		Void
	 */
	function checkBoxList(field) {
		
		// Update check state
		check = (check == 0) ? 1 : 0;
		
		// Check for multiple checkboxes
		if (field.length) {
			
			// Process each instance of the field
			for (i = 0; i < field.length; i++) {
				
				// Update checkbox
				field[i].checked = check;
			}
		} else {
			
			// Update checkbox
			field.checked = check;
		}
	}
//-->
