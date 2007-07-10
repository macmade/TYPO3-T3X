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
	 * Actions functions
	 * 
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		2.0
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      76:		processActions
	 * 
	 * SECTION:		2 - GET
	 *     132:		logOut
	 *     146:		compressFile
	 *     181:		eraseFile
	 * 
	 * SECTION:		3 - POST
	 *     225:		makeDir
	 *     268:		uploadFile
	 *     314:		newFile
	 *     365:		editFile
	 * 
	 * 				TOTAL FUNCTIONS: 8
	 */
	
	
	
	
	
	/***************************************************************
	 * SECTION 1 - MAIN
	 *
	 * Main functions.
	 ***************************************************************/
	
	/**
	 * Process actions.
	 * 
	 * This function detect if an action has benn requested, and launch
	 * the correct function to process it.
	 * 
	 * @return		Noting
	 * @see			logOut
	 * @see			compressFile
	 * @see			makeDir
	 * @see			uploadFile
	 * @see			newFile
	 * @see			eraseFile
	 * @see			editFile
	 */
	function processActions() {
		if (isset($_GET['action'])) {
			
			// GET actions
			switch($_GET['action']) {
				case 'disconnect':
					$result = logOut();
				break;
				case 'compress':
					$result = compressFile();
				break;
				case 'erase':
					$result = eraseFile();
				break;
			}
		} else if (isset($_POST['action'])) {
			
			// POST actions
			switch($_POST['action']) {
				case 'mkdir':
					$result = makeDir();
				break;
				case 'upload':
					$result = uploadFile();
				break;
				case 'newfile':
					$result = newFile();
				break;
				case 'editfile':
					$result = editFile();
				break;
			}
		}
		
		// Display result
		$GLOBALS['result'] = $result;
	}
	
	
	
	
	
	/***************************************************************
	 * SECTION 2 - GET
	 *
	 * Functions for actions passed through GET.
	 ***************************************************************/
	
	/**
	 * Process the 'diconnect' action.
	 * 
	 * This function destroys the authentification session, and redirects
	 * to the login page.
	 * 
	 * @return		Nothing
	 */
	function logOut() {
		unset($_SESSION['LOGIN']);
		header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
	}
	
	/**
	 * Process the 'compress' action.
	 * 
	 * This function checks if the gunZip function can be launched.
	 * 
	 * @return		The result of the action
	 * @see			gunZip
	 * @see			getLang
	 */
	function compressFile() {
		global $dir;
		
		// Get GET variables
		$id = $_GET['id'];
		$compress = $_GET['compress'];
		$extension = '.gz';
		if (file_exists($dir . $compress . $extension)) {
			
			// File already exists
			$result = getLang('action.compress.exist');
		} else {
			if (is_writeable($dir) && is_file($dir . $compress) && is_readable($dir . $compress)) {
				
				// Can GZ
				gunZip($dir . $compress, $extension);
				$result = getLang('action.compress.success');
				$GLOBALS['highlight'] = $compress . $extension;
			} else {
				
				// Can not GZ
				$result = getLang('action.compress.error');
			}
		}
		return $result;
	}
	
	/**
	 * Process the 'erase' action.
	 * 
	 * This function erase a file or a directory, if it's empty.
	 * 
	 * @return		The result of the action
	 * @see			getLang
	 */
	function eraseFile() {
		global $dir;
		
		// Get GET variables
		$erase = $_GET['erase'];
		if (is_dir($dir . $erase)) {
			
			// Directory
			if (@rmdir($dir . $erase)) {
				$result = getLang('action.erase.dir.success');
			} else {
				$result = getLang('action.erase.dir.error');
			}
		} else {
			
			// File
			if (@unlink($dir . $erase)) {
				$result = getLang('action.erase.file.success');
			} else {
				$result = getLang('action.erase.file.error');
			}
		}
		return $result;
	}
	
	
	
	
	
	/***************************************************************
	 * SECTION 1 - POST
	 *
	 * Functions for actions passed through POST.
	 ***************************************************************/
	
	/**
	 * Process the 'mkdir' action.
	 * 
	 * This function create a new directory.
	 * 
	 * @return		The result of the action
	 * @see			setChmod
	 * @see			getLang
	 */
	function makeDir() {
		global $dir;
		
		// Get POST variables
		$name = $_POST['name'];
		$ur = $_POST['ur'];
		$uw = $_POST['uw'];
		$ux = $_POST['ux'];
		$gr = $_POST['gr'];
		$gw = $_POST['gw'];
		$gx = $_POST['gx'];
		$wr = $_POST['wr'];
		$ww = $_POST['ww'];
		$wx = $_POST['wx'];
		if (file_exists($dir . $name)) {
			
			// Directory exists
			$result = getLang('action.mkdir.exist');
		} else {
			if (@mkdir($dir . $name)) {
				
				// Directory created
				setChmod($dir . $name,($ur + $uw + $ux),($gr + $gw + $gx),($wr + $ww + $wx));
				$result = getLang('action.mkdir.success');
				$GLOBALS['highlight'] = $name;
			} else {
				
				// Error
				$result = getLang('action.mkdir.error');
			}
		}
		return $result;
	}
	
	/**
	 * Process the 'upload' action.
	 * 
	 * This function upload a file.
	 * 
	 * @return		The result of the action
	 * @see			setChmod
	 * @see			getLang
	 */
	function uploadFile() {
		global $dir;
		
		// Get GLOBALS variables
		$upload = $GLOBALS['upload'];
		$upload_name = $GLOBALS['upload_name'];
		
		// Get POST variables
		$ur = $_POST['ur'];
		$uw = $_POST['uw'];
		$ux = $_POST['ux'];
		$gr = $_POST['gr'];
		$gw = $_POST['gw'];
		$gx = $_POST['gx'];
		$wr = $_POST['wr'];
		$ww = $_POST['ww'];
		$wx = $_POST['wx'];
		if (file_exists($dir . $upload_name)) {
			// File exists
			$result = getLang('action.upload.exist');
		} else {
			if (copy($upload, $dir . $upload_name)) {
				
				// Upload successful
				setChmod($dir . $upload_name,($ur + $uw + $ux),($gr + $gw + $gx),($wr + $ww + $wx));
				unlink($upload);
				$result = getLang('action.upload.success');
				$GLOBALS['highlight'] = $upload_name;
			} else {
				
				// Error
				$result = getLang('action.upload.error');
			}
		}
		return $result;
	}
	
	/**
	 * Process the 'newfile' action.
	 * 
	 * This function create a new file, with a new content.
	 * 
	 * @return		The result of the action
	 * @see			setChmod
	 * @see			getLang
	 */
	function newFile() {
		global $dir;
		
		// Get POST variables
		$extension = $_POST['extension'];
		$name = $_POST['name'];
		$content = stripslashes($_POST['content']);
		$ur = $_POST['ur'];
		$uw = $_POST['uw'];
		$ux = $_POST['ux'];
		$gr = $_POST['gr'];
		$gw = $_POST['gw'];
		$gx = $_POST['gx'];
		$wr = $_POST['wr'];
		$ww = $_POST['ww'];
		$wx = $_POST['wx'];
		if ($extension == 'none') {
			$extension = '';
		}
		if (file_exists($dir . $name . $extension)) {
			
			// File exists
			$result = getLang('action.newfile.exist');
		} else {
			if (@$fileopen = fopen($dir . $name . $extension, 'w')) {
				
				// File created
				setChmod($dir . $name . $extension,($ur + $uw + $ux),($gr + $gw + $gx),($wr + $ww + $wx));
				fputs($fileopen, $content);
				$result = getLang('action.newfile.success');
				fclose($fileopen);
				$GLOBALS['highlight'] = $name . $extension;
			} else {
				
				// Error
				$result = getLang('action.newfile.error');
			}
		}
		return $result;
	}
	
	/**
	 * Process the 'edifile' action.
	 * 
	 * This function edit a file (content includes), or
	 * a directory.
	 * 
	 * @return		The result of the action
	 * @see			setChmod
	 * @see			getLang
	 */
	function editFile() {
		global $dir;
		$edit = $_POST['edit'];
		$name = $_POST['name'];
		$content = stripslashes($_POST['content']);
		$ur = $_POST['ur'];
		$uw = $_POST['uw'];
		$ux = $_POST['ux'];
		$gr = $_POST['gr'];
		$gw = $_POST['gw'];
		$gx = $_POST['gx'];
		$wr = $_POST['wr'];
		$ww = $_POST['ww'];
		$wx = $_POST['wx'];
		if (is_dir($dir . $edit)) {
		
			// Directory exists
			if (file_exists($dir . $name) && $name != $edit) {
				$result = getLang('action.editfile.dir.exist');
			} else {
			
				// Edit directory
				rename($dir . $edit, $dir . $name);
				$chmodResult = setChmod($dir . $name,($ur + $uw + $ux),($gr + $gw + $gx),($wr + $ww + $wx));
				$result = getLang('action.editfile.dir.success') . '<br>' . $chmodResult;
				$GLOBALS['highlight'] = $name;
			}
		}
		else {
			
			// File exists
			if (file_exists($dir . $name) && $name != $edit) {
				$result = getLang('action.editfile.file.exist');
			} else {
				
				// Edit file
				$fileopen = fopen($dir . $edit, 'w');
				fputs($fileopen, $content,strlen($content));
				fclose($fileopen);
				rename($dir . $edit, $dir . $name);
				$chmodResult = setChmod($dir . $name,($ur + $uw + $ux),($gr + $gw + $gx),($wr + $ww + $wx));
				$result = getLang('action.editfile.file.success') . '<br>' . $chmodResult;
				$GLOBALS['highlight'] = $name;
			}
		}
		return $result;
	}
	
	/**
	 * Initialization.
	 */
	processActions();
?>
