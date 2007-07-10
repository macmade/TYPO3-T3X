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
	 * @version		2.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - DISPLAY
	 *      59:		preloadPictures
	 *      97:		setPerms(form)
	 *     137:		setRowColor(row,id,action,color1,color2)
	 * 
	 * 				TOTAL FUNCTIONS: 3
	 */
	
	
	
	
	
	/***************************************************************
	 * SECTION 1 - MAIN
	 *
	 * Main functions.
	 ***************************************************************/
	
	/**
	 * Preload pictures.
	 * 
	 * This function preloads all the pictures used in roll-over links.
	 * 
	 * @return		Nothing
	 */
	function preloadPictures() {
		var action_compress_over = new Image();
		action_compress_over.src = "/ftp/gfx/icons/action_compress_over.gif";
		var action_edit_over = new Image();
		action_edit_over.src = "/ftp/gfx/icons/action_edit_over.gif";
		var action_erase_over = new Image();
		action_erase_over.src = "/ftp/gfx/icons/action_erase_over.gif";
		var file_audio_over = new Image();
		file_audio_over.src = "/ftp/gfx/icons/file_audio_over.gif";
		var file_code_over = new Image();
		file_code_over.src = "/ftp/gfx/icons/file_code_over.gif";
		var file_compress_over = new Image();
		file_compress_over.src = "/ftp/gfx/icons/file_compress_over.gif";
		var file_directory_over = new Image();
		file_directory_over.src = "/ftp/gfx/icons/file_directory_over.gif";
		var file_picture_over = new Image();
		file_picture_over.src = "/ftp/gfx/icons/file_picture_over.gif";
		var file_text_over = new Image();
		file_text_over.src = "/ftp/gfx/icons/file_text_over.gif";
		var file_unknown_over = new Image();
		file_unknown_over.src = "/ftp/gfx/icons/file_unknown_over.gif";
		var file_video_over = new Image();
		file_video_over.src = "/ftp/gfx/icons/file_video_over.gif";
		var btn_configure_over = new Image();
		btn_configure_over.src = "/ftp/gfx/btn_configure_over.gif";
		var btn_disconnect_over = new Image();
		btn_disconnect_over.src = "/ftp/gfx/btn_disconnect_over.gif";
	}
	
	/**
	 * Gets CHMOD from input.
	 * 
	 * This function gets the chmod from checkboxes and writes it into
	 * a text input.
	 * 
	 * @param		form				The form to use
	 * @return		Nothing
	 */
	function setPerms(form) {
		var user_perms = 0;
		var group_perms = 0;
		var world_perms = 0;
		var perms = 000;
		user_perms += (eval("document." + form + ".ur.checked") == true) ? 4 : 0;
		user_perms += (eval("document." + form + ".uw.checked") == true) ? 2 : 0;
		user_perms += (eval("document." + form + ".ux.checked") == true) ? 1 : 0;
		group_perms += (eval("document." + form + ".gr.checked") == true) ? 4 : 0;
		group_perms += (eval("document." + form + ".gw.checked") == true) ? 2 : 0;
		group_perms += (eval("document." + form + ".gx.checked") == true) ? 1 : 0;
		world_perms += (eval("document." + form + ".wr.checked") == true) ? 4 : 0;
		world_perms += (eval("document." + form + ".ww.checked") == true) ? 2 : 0;
		world_perms += (eval("document." + form + ".wx.checked") == true) ? 1 : 0;
		perms = user_perms.toString() + group_perms.toString() + world_perms.toString();
		if (form == "mkdir") {
			document.mkdir.mod.value = perms;
		} else if (form == "upload") {
			document.upload.mod.value = perms;
		} else if (form == "newfile") {
			document.newfile.mod.value = perms;
		} else if (form == "edit") {
			document.edit.mod.value = perms;
		}
	}
	
	/**
	 * Row highlighting.
	 * 
	 * This function highlight a row on mouseover, and sets a color
	 * when the row is clicked.
	 * 
	 * @param		row					The form to use
	 * @param		id					The form to use
	 * @param		action				The form to use
	 * @param		color1				The form to use
	 * @param		color2				The form to use
	 * @return		Nothing
	 */
	var rows = new Array();
	function setRowColor(row,id,action,color1,color2) {
		if (action == "click") {
			if (rows[id] == "enabled") {
				rows[id] = "disabled";
				row.bgColor = color2;
			} else {
				rows[id] = "enabled";
				row.bgColor = color1;
			}
		} else if (rows[id] != "enabled") {
			row.bgColor = color1;
		}
	}
//-->
