<?php
	/***************************************************************
	 * Copyright notice
	 *
	 * (c) 2004 macmade.net
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
	 **************************************************************/
	
	/*
	 * Module 'FTP Browser' for the 'ftpbrowser' extension
	 *
	 * @author		Jean-David Gadina / macmade.net (info@macmade.net)
	 * @version		1.0
	 */
	
	unset($MCONF);
	$MCONF['extModInclude'] = 1;
	include('../conf.php');
	
	if ($MCONF['ftpBrowserSubDir']) {
		require($BACK_PATH . 'init.php');
		require($BACK_PATH . 'template.php');
		$BE_USER->modAccess($MCONF, 1);
		
		// Default variables
		$CONFIG['FTP_PATH'] = ereg_replace("/typo3/../","/",PATH_typo3 . TYPO3_MOD_PATH);
		
		// Get User Defined Variables
		$confArray = unserialize($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["ftpbrowser"]);
		
		// Mapping LANG array
		$langMap = array(
			'fr' => 'FR',
		);
		
		// Overwrite variables with localconf values
		$CONFIG['HEADER'] = $confArray['HEADER'];
		$CONFIG['DATE_FORMAT'] = $confArray['DATE_FORMAT'];
		$CONFIG['LANG'] = ($langMap[$LANG->lang]) ? $langMap[$LANG->lang] : 'EN' ;
		$CONFIG['SORT_FILES'] = $confArray['SORT_FILES'];
		$CONFIG['STARTUP_DIR'] = $confArray['STARTUP_DIR'];
		$CONFIG['FAVORITES'] = explode(',',$confArray['FAVORITES']);
		$CONFIG['AUTH'] = $confArray['AUTH'];
		$CONFIG['LOGIN'] = $confArray['LOGIN'];
		$CONFIG['PASSWORD'] = $confArray['PASSWORD'];
		$CONFIG['COLOR_ROWBG_1'] = $confArray['COLOR_ROWBG_1'];
		$CONFIG['COLOR_ROWBG_2'] = $confArray['COLOR_ROWBG_2'];
		$CONFIG['COLOR_ROWBG_ACT'] = $confArray['COLOR_ROWBG_ACT'];
		$CONFIG['COLOR_ROWBG_OVER'] = $confArray['COLOR_ROWBG_OVER'];
		$CONFIG['COLOR_ROWBG_SEL'] = $confArray['COLOR_ROWBG_SEL'];
		$CONFIG['COLOR_TEXT'] = $confArray['COLOR_TEXT'];
		$CONFIG['COLOR_INPUT'] = $confArray['COLOR_INPUT'];
		$CONFIG['COLOR_LINK'] = $confArray['COLOR_LINK'];
		$CONFIG['COLOR_LINKHOVER'] = $confArray['COLOR_LINKHOVER'];
		$CONFIG['COLOR_LINKACTIVE'] = $confArray['COLOR_LINKACTIVE'];
		$CONFIG['COLOR_LINKVISITED'] = $confArray['COLOR_LINKVISITED'];
		$CONFIG['COLOR_INFOS'] = $confArray['COLOR_INFOS'];
		$CONFIG['COLOR_LEGEND'] = $confArray['COLOR_LEGEND'];
		$CONFIG['COLOR_RESULT'] = $confArray['COLOR_RESULT'];
		$CONFIG['COLOR_PROPERTIES'] = $confArray['COLOR_PROPERTIES'];
		$CONFIG['COLOR_FILENAME'] = $confArray['COLOR_FILENAME'];
		$CONFIG['COLOR_TYPE'] = $confArray['COLOR_TYPE'];
		$CONFIG['COLOR_SIZE'] = $confArray['COLOR_SIZE'];
		$CONFIG['COLOR_PERMS'] = $confArray['COLOR_PERMS'];
		$CONFIG['COLOR_MOD'] = $confArray['COLOR_MOD'];
		$CONFIG['COLOR_READ'] = $confArray['COLOR_READ'];
		$CONFIG['COLOR_WRITE'] = $confArray['COLOR_WRITE'];
		$CONFIG['COLOR_UID'] = $confArray['COLOR_UID'];
		$CONFIG['COLOR_GID'] = $confArray['COLOR_GID'];
		$CONFIG['COLOR_CTIME'] = $confArray['COLOR_CTIME'];
		$CONFIG['COLOR_ATIME'] = $confArray['COLOR_ATIME'];
	}
	else {
		die('Not Configured');
	}
?>
