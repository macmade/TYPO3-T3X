<?php
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
	 * Utilities functions
	 * 
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		2.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - DISPLAY
	 *      73:		favSelect
	 *      96:		pathSelect
	 *     129:		fileTypeSelect
	 *     152:		displayFiles
	 *     261:		sepRow
	 *     276:		sepCol
	 *
	 * SECTION:		2 - MISC
	 *     299:		writeHTML($code)
	 *     312:		checkTrailingSlash($path)
	 *     328:		removeSymlinks($path)
	 *     356:		getUnixperms($mode)
	 *     378:		checkType($filename)
	 *     398:		gunZip($file, $extension)
	 *     420:		setChmod($path,$user,$group,$world)
	 * 
	 * 				TOTAL FUNCTIONS: 13
	 */
	
	
	
	
	
	/***************************************************************
	 * SECTION 1 - DISPLAY
	 *
	 * Display functions.
	 ***************************************************************/
	
	/**
	 * Writes the options for the favorites select.
	 * 
	 * This function is used to parse the favorites array
	 * ($CONFIG['FAVORITES']), and to write a select option for each entry.
	 * 
	 * @return		Nothing
	 * @see			writeHTML
	 */
	function favSelect() {
		global $CONFIG;
		for ($i = 0; $i < count($CONFIG['FAVORITES']); $i++) {
			$fav = explode(':', $CONFIG['FAVORITES'][$i]);
			if (substr($_SERVER['DOCUMENT_ROOT'],strlen($_SERVER['DOCUMENT_ROOT']) - 1,1) != '/' && substr($fav[1],0,1) != '/') {
				$fav[1] = '/' . $fav[1];
			}
			if (substr($_SERVER['DOCUMENT_ROOT'],strlen($_SERVER['DOCUMENT_ROOT']) - 1,1) == '/' && substr($fav[1],0,1) == '/') {
				$fav[1] = substr($fav[1],1,strlen($fav[1]) - 1);
			}
			writeHTML('<option value="' . $_SERVER['DOCUMENT_ROOT'] . $fav[1] . '">' . $fav[1] . ' (' . $fav[0] . ')</option>');
		}
	}
	
	/**
	 * Writes the options for the path select.
	 * 
	 * This function is used to parse the current working path, and to
	 * write a select option for directory.
	 * 
	 * @return		Nothing
	 * @see			writeHTML
	 */
	function pathSelect() {
		global $dir;
		$label = array();
		array_push($label, '/');
		$dir_path = ereg_replace($_SERVER['DOCUMENT_ROOT'],'',$dir);
		while (ereg('/[^/]*/', $dir_path)) {
			$rep = ereg_replace('/([^/]*)/.*', '\\1', $dir_path);
			$dir_path = ereg_replace('/[^/]*(/.*)', '\\1', $dir_path);
			array_push($label, $rep);
		}
		for ($i = count($label); $i > 0; $i--) {
			$path = '';
			for ($j = 0; $j < $i; $j++) {
				$path .= $label[$j];
				if ($path != '/') {
					$path .= '/';
				}
			}
			writeHTML('<option value="'. $_SERVER['DOCUMENT_ROOT'] . $path . '">' . $label[$i - 1] . '</option>');
		}
	}
	
	/**
	 * Writes the options for the filetypes select.
	 * 
	 * This function is used to parse the filetypes array ($FILETYPES),
	 * and to write a select option for each entry, if the edit flag
	 * is found.
	 * 
	 * @return		Nothing
	 * @see			writeHTML
	 * @see			getLang
	 */
	function fileTypeSelect() {
		global $FILETYPES;
		foreach($FILETYPES as $type) {
			if ($type['edit']) {
				writeHTML('<option value="' . $type['ext'] . '">' . $type["ext"] . ' (' . getLang('files' . $type["ext"]) . ')</option>');
			}
		}
	}
	
	/**
	 * Writes the browser's table.
	 * 
	 * This function is used to generate the complete row for each file
	 * of the working directory.
	 * 
	 * @return		Nothing
	 * @see			checkType
	 * @see			getLang
	 * @see			getUnixperms
	 * @see			sepCol
	 * @see			sepRow
	 * @see			writeHTML
	 */
	function displayFiles() {
		global $FILETYPES, $dir, $DATA, $CONFIG, $LANG, $highlight;
		$color_row = $CONFIG['COLOR_ROWBG_1'];
		$number = 0;
		for ($i = 0; $i < count($DATA['NAME']); $i++) {
			$type_id = (is_dir($dir . $DATA['NAME'][$i])) ? count($FILETYPES) - 2 : checkType($DATA['NAME'][$i]);
			
			// Icon file
			$icon = $FILETYPES[$type_id]['icon'];
			
			// Type column label
			$type = getLang('files' . $FILETYPES[$type_id]['ext']);
			
			if ($DATA['READ'][$i] == '1') {
				
				// Add link	
				$viewlink = ereg_replace($_SERVER['DOCUMENT_ROOT'],'',$dir . $DATA['NAME'][$i]);
				$href = (is_dir($dir . $DATA['NAME'][$i])) ? '<a href="' . $_SERVER['PHP_SELF'] . '?dir=' . $dir . $DATA['NAME'][$i] . '/" onmouseover="javascript:document.icon_' . $number . '.src=\'gfx/icons/file_' . $icon . '_over.gif\';" onmouseout="javascript:document.icon_' . $number . '.src=\'gfx/icons/file_' . $icon . '.gif\';" title="' . getLang('href_title.opendir') . '">' : '<a href="' . $viewlink . '" target="_blank" onmouseover="javascript:document.icon_' . $number . '.src=\'gfx/icons/file_' . $icon . '_over.gif\';" onmouseout="javascript:document.icon_' . $number . '.src=\'gfx/icons/file_' . $icon . '.gif\';" title="' . getLang('href_title.viewfile') . '">';
				$file = $href . $DATA['NAME'][$i] . '</a>';
			} else {
				
				// No link
				$file = $DATA['NAME'][$i];
			}
			
			// Get owner name (POSIX)
			$owner_infos = ($DATA['READ'][$i] == "1") ? posix_getpwuid($DATA['OWNER'][$i]) : "-";
			
			// Get group name (POSIX)
			$group_infos = ($DATA['READ'][$i] == "1") ? posix_getgrgid($DATA['GROUP'][$i]) : "-";
			
			// Format file size
			$size = ($DATA['TYPE'][$i] != "file") ? "-" : number_format($DATA['SIZE'][$i] / 1024,2) . "kb";
			
			// Format file mod
			$mod = ($DATA['READ'][$i] == "1") ? sprintf("%o", $DATA['PERMS'][$i]) : "-";
			
			// Read column label
			$read = ($DATA['READ'][$i] == "1") ? getLang('global.yes') : getLang('global.no');
			
			// Write column label
			$write = ($DATA['WRITE'][$i] == "1") ? getLang('global.yes') : getLang('global.no');
			
			// Edit icon
			$editable = (is_writeable(".") && $DATA['READ'][$i] == "1" && $DATA['WRITE'][$i] == "1" && $DATA['NAME'][$i] != "." && $DATA['NAME'][$i] != "..") ? '<a href="' . $_SERVER["PHP_SELF"] . '?dir=' . $dir .'&action=edit&edit=' . $DATA['NAME'][$i] . '&id=' . $i . '" onmouseover="javascript:document.edit_' . $number . '.src=\'gfx/icons/action_edit_over.gif\';" onmouseout="javascript:document.edit_' . $number . '.src=\'gfx/icons/action_edit.gif\';" title="' . getLang('href_title.edit') . '"><img src="gfx/icons/action_edit.gif" name="edit_' . $number .'" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></a>' : '<img src="gfx/icons/action_edit_off.gif" name="edit_' . $number .'" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle">';
			
			// Compress icon
			$compress = (is_writeable(".") && $DATA['TYPE'][$i] == "file" && $DATA['READ'][$i] == "1" && $DATA['NAME'][$i] != "." && $DATA['NAME'][$i] != "..") ? '<a href="' . $_SERVER["PHP_SELF"] . '?dir=' . $dir .'&action=compress&compress=' . $DATA['NAME'][$i] . '" onmouseover="javascript:document.compress_' . $number . '.src=\'gfx/icons/action_compress_over.gif\';" onmouseout="javascript:document.compress_' . $number . '.src=\'gfx/icons/action_compress.gif\';" title="' . getLang('href_title.compress') . '"><img src="gfx/icons/action_compress.gif" name="compress_' . $number .'" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></a>' : '<img src="gfx/icons/action_compress_off.gif" name="compress_' . $number .'" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle">';
			
			// Erase icon
			$erase = (is_writeable(".")  && $DATA['WRITE'][$i] == "1" && $DATA['NAME'][$i] != "." && $DATA['NAME'][$i] != "..") ? '<a href="' . $_SERVER["PHP_SELF"] . '?dir=' . $dir .'&action=erase&erase=' . $DATA['NAME'][$i] . '" onmouseover="javascript:document.erase_' . $number . '.src=\'gfx/icons/action_erase_over.gif\';" onmouseout="javascript:document.erase_' . $number . '.src=\'gfx/icons/action_erase.gif\';" title="' . getLang('href_title.erase') . '"><img src="gfx/icons/action_erase.gif" name="erase_' . $number .'" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></a>' : '<img src="gfx/icons/action_erase_off.gif" name="erase_' . $number .'" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle">';
			
			// Active row check
			$color = ($highlight == $DATA['NAME'][$i] || $_GET['edit'] == $DATA['NAME'][$i]) ? $CONFIG['COLOR_ROWBG_ACT'] : $color_row;
			
			// TR parameters for mouseover and mouseout
			$tr_params = ' onmouseover="setRowColor(this,\'' . $i . '\',\'over\',\'' . $CONFIG['COLOR_ROWBG_OVER'] . '\');" onmouseout="setRowColor(this,\'' . $i . '\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $i . '\',\'click\',\'' . $CONFIG['COLOR_ROWBG_SEL'] . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
			
			// Table construction
			writeHTML('<tr ' . $tr_params . '>');
			sepCol();
			writeHTML('<td width="20" height="20" align="left" valign="middle"><img src="gfx/icons/file_' . $icon . '.gif" name="icon_' . $number .'" alt="" width="20" height="20" hspace="0" vspace="0" border="0" align="middle"></td>');
			sepCol();
			writeHTML('<td height="20" align="left" valign="middle" class="filename">&nbsp;' . $file . '</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="type">' . $type . '</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="size">' . $size . '</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="perms">' . getUnixPerms($DATA['PERMS'][$i]) . '</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="mod">' . substr($mod, strlen($mod) - 3, 3) . '</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="read">' . $read . '</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="write">' . $write . '</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="uid">' . $DATA['OWNER'][$i] . ' (' . $owner_infos["name"] . ')</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="gid">' . $DATA['GROUP'][$i] . ' (' . $group_infos["name"] . ')</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="ctime">' . date($CONFIG['DATE_FORMAT'], $DATA['CTIME'][$i]) . '</td>');
			sepCol();
			writeHTML('<td height="20" align="center" valign="middle" class="atime">' . date($CONFIG['DATE_FORMAT'], $DATA['ATIME'][$i]) . '</td>');
			sepCol();
			writeHTML('<td width="20" height="20" align="left" valign="middle">' . $editable . '</td>');
			sepCol();
			writeHTML('<td width="20" height="20" align="left" valign="middle">' . $compress . '</td>');
			sepCol();
			writeHTML('<td width="20" height="20" align="left" valign="middle">' . $erase . '</td>');
			sepCol();
			writeHTML('</tr>');
			sepRow();
			
			// Change row color
			$color_row = ($color_row == $CONFIG['COLOR_ROWBG_1']) ? $CONFIG['COLOR_ROWBG_2'] : $CONFIG['COLOR_ROWBG_1'];
			$number++;
		}
	}
	
	/**
	 * Writes a row.
	 * 
	 * This function is used to write a separation row, placed
	 * after each file's row.
	 * 
	 * @return		Nothing
	 * @see			writeHTML
	 */
	function sepRow() {
		writeHTML('<tr>');
		writeHTML('<td colspan="31" height="1" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="10" height="1" hspace="0" vspace="0" border="0" align="middle"></td>');
		writeHTML('</tr>');
	}
	
	/**
	 * Writes a column.
	 * 
	 * This function is used to write a separation column, placed
	 * after each file's column.
	 * 
	 * @return		Nothing
	 * @see			writeHTML
	 */
	function sepCol() {
		writeHTML('<td width="1" height="10" align="left" valign="top" bgcolor="#4D4D4D"><img src="gfx/spacer.gif" alt="" width="1" height="10" hspace="0" vspace="0" border="0" align="middle"></td>');
	}
	
	
	
	
	
	/***************************************************************
	 * SECTION 2 - MISC
	 *
	 * Miscellaneous functions.
	 ***************************************************************/
	
	/**
	 * Writes a string and a line break.
	 * 
	 * This function is used to avoid the page's code to be on a single
	 * line.
	 * 
	 * @param		$code				The string to write
	 * @return		Nothing
	 */
	function writeHTML($code) {
		echo($code . "\n");
	}
	
	/**
	 * Returns a corrected path.
	 * 
	 * This function is used to check a given path, and to add, if
	 * needed, a trailing slash.
	 * 
	 * @param		$path				The path to correct
	 * @return		The corrected path
	 */
	function checkTrailingSlash($path) {
		if (substr($path, strlen($path) - 1, 1) != '/') {
			$path .= '/';
		}
		return $path;
	}
	
	/**
	 * Returns a corrected path.
	 * 
	 * This function is used to check a given path, and to remove all
	 * path abberation, like <code>../</code>.
	 * 
	 * @param		$path				The path to correct
	 * @param		The corrected path
	 */
	function removeSymlinks($path) {
		while (ereg('//', $path)) {
			$path = ereg_replace('//','/', $path);
		}
		if (ereg('/[^/]*/\.\./', $path)) {
			$path = ereg_replace('/[^/]*/\.\./', '/', $path);
		}
		if (ereg('/[^/]*/\./', $path)) {
			$path = ereg_replace('(/[^/]*/)\./', '\\1', $path);
		}
		if (ereg('/\.\./', $path)) {
			$path = ereg_replace('/\.\./', '/', $path);
		}
		if (ereg('/\./', $path)) {
			$path = ereg_replace('/\./', '/', $path);
		}
		return $path;
	}
	
	/**
	 * Returns readable permissions.
	 * 
	 * This function is used to convert unix type permissions into a
	 * readable string, like rwx-rwx-rwx.
	 * 
	 * @param		$mode				The file's permissions
	 * @return		Formatted permissions 
	 */
	function getUnixperms($mode) {
		$owner['read'] = ($mode & 00400) ? 'r' : '-';
		$owner['write'] = ($mode & 00200) ? 'w' : '-';
		$owner['execute'] = ($mode & 00100) ? 'x' : '-';
		$group['read'] = ($mode & 00040) ? 'r' : '-';
		$group['write'] = ($mode & 00020) ? 'w' : '-';
		$group['execute'] = ($mode & 00010) ? 'x' : '-';
		$world['read'] = ($mode & 00004) ? 'r' : '-';
		$world['write'] = ($mode & 00002) ? 'w' : '-';
		$world['execute'] = ($mode & 00001) ? 'x' : '-';
		return($owner['read'] . $owner['write'] . $owner['execute'] . ' ' . $group['read'] . $group['write'] . $group['execute'] . ' ' . $world['read'] . $world['write'] . $world[execute]);
	}
	
	/**
	 * Returns the correct key of the filetypes array ($FILETYPES).
	 * 
	 * This function is used to search for a filetype, from a given
	 * filename.
	 * 
	 * @param		$filename			The file to identify
	 * @return		The ID of the correct filetype
	 */
	function checkType($filename) {
		global $FILETYPES;
		for ($i = 0; $i < count($FILETYPES); $i++) {
			$type_id = $i;
			if (ereg('.+\.' . $FILETYPES[$i]['regexp'] . '$',$filename)) {
				break;
			}
		}
		return $type_id;
	}
	
	/**
	 * Compress a file.
	 * 
	 * This function is used to encode a given file in the GunZip Format.
	 * 
	 * @param		$filename	The file to compress
	 * @param		$extension	The extension to add
	 * @return		Nothing
	 */
	function gunZip($file, $extension) {
		$DATA = implode('', file($file));
		$gzdata = gzencode($DATA, 9);
		$archive = fopen($file . $extension, 'w');
		fwrite($archive, $gzdata);
		fclose($archive);
	}
	
	/**
	 * Chmod a file.
	 * 
	 * This function is used to change the permissions of a given file.
	 * The permissions are passed as integers, in example 777. The function
	 * te converts the number as an octal number in order to chmod.
	 * 
	 * @param		$path				The path of the file to chmod
	 * @param		$user				The permissions for the user (int)
	 * @param		$group				The permissions for the group (int)
	 * @param		$world				The permissions for the world (int)
	 * @return		Nothing if success, error from LANG otherwise
	 * @see			getLang
	 */
	function setChmod($path,$user,$group,$world) {
		$number = '0';
		$number .= $user;
		$number .= $group;
		$number .= $world;
		$number = octdec($number);
		if (@chmod($path, $number)) {
			return false;
		} else {
			return getLang('chmod.error');
		}
	}
?>
