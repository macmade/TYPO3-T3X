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
	 * File 'browser.php' / Browser page.
	 * 
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		2.1
	 */
	
	/**
	 * Config file.
	 */
	require('./conf/config.php');
	
	/**
	 * Typo3 specific variables.
	 */
	require('../extModInclude.php');
	
	// Security check to prevent use outside of TYPO3
	// @author      Macmade - 27.05.2007
	if( !isset( $GLOBALS[ 'BE_USER' ]->user[ 'admin' ] ) || $GLOBALS[ 'BE_USER' ]->user[ 'admin' ] != 1
	    || !isset( $_COOKIE[ $GLOBALS[ 'BE_USER' ]->user[ 'ses_name' ] ] )
	    || $_COOKIE[ $GLOBALS[ 'BE_USER' ]->user[ 'ses_name' ] ] != $GLOBALS[ 'BE_USER' ]->user[ 'ses_id' ] ) {
	    die( 'Access denied' );
	}
	
	/**
	 * Default language file.
	 */
	require_once($CONFIG['FTP_PATH'] . 'lang/' . $CONFIG['LANG'] . '.php');
	
	/**
	 * Filetypes file.
	 */
	require($CONFIG['FTP_PATH'] . 'conf/files.php');
	
	/**
	 * Core functions.
	 */
	require($CONFIG['FTP_PATH'] . 'lib/ftplib_core.php');
	
	/**
	 * Utilities functions.
	 */
	require($CONFIG['FTP_PATH'] . 'lib/ftplib_utils.php');
	
	/**
	 * Actions functions.
	 */
	require($CONFIG['FTP_PATH'] . 'lib/ftplib_actions.php');
	
	/**
	 * Header.
	 */
	require($CONFIG['FTP_PATH'] . 'include/header.php');
	
	/**
	 * Browser functions.
	 */
	require($CONFIG['FTP_PATH'] . 'lib/ftplib_browser.php');
	
	/**
	 * Footer.
	 */
	require($CONFIG['FTP_PATH'] . 'include/footer.php');
?>
