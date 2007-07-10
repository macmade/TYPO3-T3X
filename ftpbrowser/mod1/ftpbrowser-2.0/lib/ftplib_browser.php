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
	 * Browser functions
	 * 
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		2.1
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      78:		setDir
	 *     103:		init
	 *     174:		browserTableConstruct
	 *     205:		browseServer($file)
	 * 
	 * 				TOTAL FUNCTIONS: 4
	 */
	
	// Security check to prevent use outside of TYPO3
	// @author      Macmade - 27.05.2007
	if( !isset( $GLOBALS[ 'BE_USER' ]->user[ 'admin' ] ) || $GLOBALS[ 'BE_USER' ]->user[ 'admin' ] != 1
	    || !isset( $_COOKIE[ $GLOBALS[ 'BE_USER' ]->user[ 'ses_name' ] ] )
	    || $_COOKIE[ $GLOBALS[ 'BE_USER' ]->user[ 'ses_name' ] ] != $GLOBALS[ 'BE_USER' ]->user[ 'ses_id' ] ) {
	    die( 'Access denied' );
	}
	
	
	
	
	
	/***************************************************************
	 * SECTION 1 - MAIN
	 *
	 * Main functions.
	 ***************************************************************/
	
	/**
	 * Records array.
	 */
	$DATA = array(
		'NAME' => array(),
		'TYPE' => array(),
		'ATIME' => array(),
		'CTIME' => array(),
		'PERMS' => array(),
		'SIZE' => array(),
		'OWNER' => array(),
		'GROUP' => array(),
		'READ' => array(),
		'WRITE' => array(),
	);
	
	/**
	 * Sets the working directory.
	 * 
	 * This function sets the working directory to the server's document
	 * root, and adds the user defined startup directory, if any.
	 * 
	 * @return		Nothing
	 * @see			checkTrailingSlash
	 */
	function setDir() {
		global $dir,$CONFIG;
		$dir = $_SERVER['DOCUMENT_ROOT'];
		
		// Trailing slash check
		$dir = checkTrailingSlash($dir);
		
		// Startup directory
		$dir .= $CONFIG['STARTUP_DIR'];
	}
	
	/**
	 * Initializes the browser.
	 * 
	 * This function is the core of the browser. It open the directory,
	 * after having checked the path, and launch the functions to
	 * display the browser.
	 * 
	 * @return		Nothing
	 * @see			setDir
	 * @see			checkTrailingSlash
	 * @see			removeSymlinks
	 * @see			browseServer
	 * @see			browserTableConstruct
	 */
	function init() {
		global $dir,$CONFIG;
		
		// No dir variable
		if (!isset($dir)) {
			setDir();
		}
		
		// Trailing slash check
		$dir = checkTrailingSlash($dir);
		
		// Path correction
		$dir = removeSymlinks($dir);
		
		// Directory not found - Reset browser
		if (@!chdir($dir)) {
			setDir();
			chdir($dir);
			$GLOBALS['result'] = getLang('result.dirNotFound');
		}
		
		if (@$open = opendir('.')) {
			
			// Open directory
			if ($CONFIG['SORT_FILES']) {
				
				// Sort files
				while ($file = readdir($open)) {
				
					// Directories
					if (is_dir($file)) {
						browseServer($file);
					}
				}
				$open = opendir('.');
				while ($file = readdir($open)) {
				
					// Files
					if (!is_dir($file)) {
						browseServer($file);
					}
				}
			} else {
				
				// Do not sort files
				while ($file = readdir($open)) {
					browseServer($file);
				}
			}
			
			// Browser table construction
			browserTableConstruct();
			
			// Close directory ressource
			closedir($open);
		} else {
		
			// Error reading directory
			$GLOBALS['result'] = getLang('result.dirError');
		}
	}
	
	/**
	 * Display the browser.
	 * 
	 * This function requires the files needed to display all of
	 * the browser parts.
	 * 
	 * @return		Nothing
	 * @see			displayFiles
	 */
	function browserTableConstruct() {
		global $dir,$CONFIG,$DATA;
		
		// Edition module
		if ($_GET['action'] == 'edit') {
			require($CONFIG['FTP_PATH'] . 'include/edit.php');
		}
		
		// Browser header
		require($CONFIG['FTP_PATH'] . 'include/browser_header.php');
		
		// Create browser rows
		displayFiles();
		
		// Browser footer
		require($CONFIG['FTP_PATH'] . 'include/browser_footer.php');
		
		// Options module
		if (is_writeable($dir)) {
			require($CONFIG['FTP_PATH'] . 'include/options.php');
		}
	}
	
	/**
	 * Analyzes a file ressource.
	 * 
	 * This function adds to the data array ($DATA) the
	 * poperties of a given file ressource.
	 *
	 * @param		$file				The file ressource
	 */
	function browseServer($file) {
		global $DATA;
		if (is_readable($file)) {
		
			// Readable file
			array_push($DATA['NAME'], $file);
			array_push($DATA['TYPE'], filetype($file));
			array_push($DATA['ATIME'], fileatime($file));
			array_push($DATA['CTIME'], filectime($file));
			array_push($DATA['PERMS'], fileperms($file));
			array_push($DATA['SIZE'], filesize($file));
			array_push($DATA['OWNER'], fileowner($file));
			array_push($DATA['GROUP'], filegroup($file));
		}
		else {
		
			// Unreadable file
			array_push($DATA['NAME'], $file);
			array_push($DATA['TYPE'], "-");
			array_push($DATA['ATIME'], "-");
			array_push($DATA['CTIME'], "-");
			array_push($DATA['PERMS'], "-");
			array_push($DATA['SIZE'], "-");
			array_push($DATA['OWNER'], "-");
			array_push($DATA['GROUP'], "-");
		}
		
		// Common values (read - write)
		array_push($DATA['READ'], is_readable($file));
		array_push($DATA['WRITE'], is_writeable($file));
	}
	
	/**
	 * Initialization.
	 */
	init();
?>
