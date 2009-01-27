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
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		3.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - DISPLAY
	 *      66:		function setRowColor(row,id,action,color1,color2,checkbox)
	 *      95:		function checkBoxList(field)
	 * 
	 * 				TOTAL FUNCTIONS: 2
	 */
	
	
	
	
	
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
	 * @param		checkbox			The row has a checkbox
	 * @return		Nothing
	 */
	var rows = new Array();
	function setRowColor(row,id,action,color1,color2,checkbox) {
		if (action == "click") {
			if (rows[id] == "enabled") {
				rows[id] = "disabled";
				row.bgColor = color2;
				if (checkbox == 1) {
					document.forms[0].list[id].checked = 0;
				}
			} else {
				rows[id] = "enabled";
				row.bgColor = color1;
				if (checkbox == 1) {
					document.forms[0].list[id].checked = 1;
				}
			}
		} else if (rows[id] != "enabled") {
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
	 * @return		Nothing
	 */
	var check = 0;
	function checkBoxList(field) {
		check = (check == 0) ? 1 : 0;
		for (i = 0; i < field.length; i++) {
			field[i].checked = check;
		}
	}
//-->
