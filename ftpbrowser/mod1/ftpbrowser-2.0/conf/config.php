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
	 * File 'config/config.php' / General configuration file.
	 * 
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		2.1
	 */
	
	/**
	 * Core setup.
	 */
	 
	// Path to the FTP directory
	$CONFIG['FTP_PATH'] = '';
	
	// Path to the optional users conf files
	$CONFIG['USER_CONF'] = '';
	
	/**
	 * Default setup.
	 */
	
	// Display header and logo
	$CONFIG['DATE_FORMAT'] = 'D; d.m.Y / H:i';
	
	// Date & time format
	$CONFIG['DATE_FORMAT'] = 'D; d.m.Y / H:i';
	
	// Language
	$CONFIG['LANG'] = 'EN';
	
	// Ask for a login & password
	$CONFIG['AUTH'] = 1;
	
	// Login
	$CONFIG['LOGIN'] = 'admin';
	
	// Password
	$CONFIG['PASSWORD'] = 'password';
	
	// Default startup directory
	$CONFIG['STARTUP_DIR'] = '/';
	
	// Display directories before files
	$CONFIG['SORT_FILES'] = 1;
	
	/**
	 * Default favorites.
	 */
	$CONFIG['FAVORITES'] = array(
	
		// Syntax = FavName:FavPath
		'Root Directory:/',
	);
	
	/**
	 * Default colors setup.
	 */
	$CONFIG['COLOR_ROWBG_1'] = '#EFEFEF';		// Row color
	$CONFIG['COLOR_ROWBG_2'] = '#CCCCCC';		// Row alt color
	$CONFIG['COLOR_ROWBG_ACT'] = '#FFAF7F';		// Active row color
	$CONFIG['COLOR_ROWBG_OVER'] = '#84DAFF';	// Row over color
	$CONFIG['COLOR_ROWBG_SEL'] = '#8CE500';		// Selected row color
	$CONFIG['COLOR_TEXT'] = '#4D4D4D';			// Text color
	$CONFIG['COLOR_INPUT'] = '#4D4D4D';			// Inputs color
	$CONFIG['COLOR_LINK'] = '#4D4D4D';			// Links color
	$CONFIG['COLOR_LINKHOVER'] = '#FF3600';		// Links over color
	$CONFIG['COLOR_LINKACTIVE'] = '#8CE500';	// Active links color
	$CONFIG['COLOR_LINKVISITED'] = '#4D4D4D';	// Visited links color
	$CONFIG['COLOR_INFOS'] = '#4D4D4D';			// Server infos color
	$CONFIG['COLOR_LEGEND'] = '#FF3600';		// Legends color
	$CONFIG['COLOR_RESULT'] = '#FF3600';		// Alerts color
	$CONFIG['COLOR_PROPERTIES'] = '#273D70';	// Properties color
	$CONFIG['COLOR_FILENAME'] = '#4D4D4D';		// Filename column color
	$CONFIG['COLOR_TYPE'] = '#273D70';			// Type column color
	$CONFIG['COLOR_SIZE'] = '#3B6000';			// Charset column color
	$CONFIG['COLOR_PERMS'] = '#FF3600';			// Perms column color
	$CONFIG['COLOR_MOD'] = '#FF3600';			// Mod column color
	$CONFIG['COLOR_READ'] = '#FF0080';			// Read column color
	$CONFIG['COLOR_WRITE'] = '#FF0080';			// Write column color
	$CONFIG['COLOR_UID'] = '#00B2FF';			// UID column color
	$CONFIG['COLOR_GID'] = '#00B2FF';			// GID column color
	$CONFIG['COLOR_ATIME'] = '#732518';			// CTime column color
	$CONFIG['COLOR_CTIME'] = '#732518';			// ATime column color
?>
