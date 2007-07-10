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
	 * Core functions
	 * 
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		2.1
	 */
	
	/**
	 * [CLASS/FUNCTION INDEX OF SCRIPT]
	 * 
	 * SECTION:		1 - MAIN
	 *      62:		getLang($label)
	 *      76:		userConf
	 *     108:		userAuth
	 * 
	 * 				TOTAL FUNCTIONS: 3
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
	 * Returns a LANG string
	 * 
	 * This function first check if the string exists in the working
	 * language. If it doesn't, the function returns the string in the
	 * default language.
	 *
	 * @param		$label				The key of the LANG array
	 * @return		The requested string from the LANG array
	 */
	function getLang($label) {
		global $CONFIG,$LANG;
		return((isset($LANG[$CONFIG['LANG']][$label])) ? $LANG[$CONFIG['LANG']][$label] : $LANG[$CONFIG['EN']][$label]);
	}
	
	/**
	 * Checks if the user's configuration files exist.
	 * 
	 * This function first check if the file exists in the specified
	 * path ($CONFIG['USER_CONF']). If it does, the function require
	 * the file to add the values.
	 * 
	 * @return		Nothing
	 */
	function userConf() {
		global $CONFIG;
		
		// Optional user config file
		if (file_exists($CONFIG['FTP_PATH'] . $CONFIG['USER_CONF'] . 'ftpconfig')) {
			require($CONFIG['FTP_PATH'] . $CONFIG['USER_CONF'] . 'ftpconfig');
		}
		
		// Optional user color file
		if (file_exists($CONFIG['FTP_PATH'] . $CONFIG['USER_CONF'] . 'ftpcolors')) {
			require($CONFIG['FTP_PATH'] . $CONFIG['USER_CONF'] . 'ftpcolors');
		}
		
		// optional user language file
		if (file_exists($CONFIG['FTP_PATH'] . 'lang/' . $CONFIG['LANG'] . '.php')) {
			require($CONFIG['FTP_PATH'] . 'lang/' . $CONFIG['LANG'] . '.php');
		}
	}
	
	/**
	 * Checks the user authentification / Proccess login
	 * 
	 * This function checks if a authentification is needed
	 * ($CONFIG['AUTH']). If it is, and if the user isn't authentified,
	 * it redirects to the login panel. Otherwise, it grants access to
	 * the browser page.
	 *
	 * This function is also use to process the login, and to start the
	 * authentification session if the user is granted.
	 * 
	 * @return		Nothing
	 */
	function userAuth() {
		global $CONFIG;
		
		// Starting session
		session_name('FTPSESSIONID');
		session_start();
		
		// Login action
		if (isset($GLOBALS['action']) && $GLOBALS['action'] == 'login') {
			
			if ($GLOBALS['log'] == $CONFIG['LOGIN'] && $GLOBALS['pwd'] == $CONFIG['PASSWORD']) {
				
				// Access granted
				$_SESSION['LOGIN'] = 1;
			} else {
				
			// Authentification error
				$GLOBALS['result'] = getLang('login.error');
			}
		}
		
		// Checking authentification and path
		if ($CONFIG['AUTH'] == 0 || isset($_SESSION['LOGIN'])) {
			if ($_SERVER['SCRIPT_FILENAME'] == $CONFIG['FTP_PATH'] . 'index.php') {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/browser.php");
			}
		} else {
			if ($_SERVER['PATH_TRANSLATED'] != $CONFIG['FTP_PATH'] . 'index.php') {
				header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php");
			}
		}
	}
	
	/**
	 * Initiatization.
	 */
	userConf();
	userAuth();
?>
