<?php
	// Check for external module configuration
	if (!isset($MCONF['extModInclude'])) {
		$MCONF['extModInclude'] = 0;
	}
	
	// External module directory
	$MCONF['mantisSubDir'] = 'mantis-0.19.3/';
	
	/**
	 * Usual TYPO3_MOD_PATH deactivated for the external module
	 * 
	define('TYPO3_MOD_PATH', '../typo3conf/ext/mantis_bugtracker/mod1/');
	 */
	
	// Basic setup
	$BACK_PATH = '../../../../' . ($MCONF['extModInclude'] ? '../' : '') . 'typo3/';
	$MCONF['name'] = 'user_txmantisbugtrackerM1';
	
	// Check installation type
	if (substr_count($_SERVER['SCRIPT_FILENAME'], 'typo3conf') > 0) {
		
		// Local install
		define('TYPO3_MOD_PATH', '../typo3conf/ext/mantis_bugtracker/mod1/' . ( $MCONF['extModInclude'] ? $MCONF['mantisSubDir'] : '' ));
		
	} else {
		
		// Global install
		define('TYPO3_MOD_PATH', 'ext/mantis_bugtracker/modsub/' . ($MCONF['extModInclude'] ? $MCONF['mantisSubDir'] : ''));
		$BACK_PATH='../../../' . ($MCONF['extModInclude'] ? '../' : '');
	}
	
	// Module configuration
	$MCONF['access'] = 'user,group';
	$MCONF['script'] = 'index.php';
	$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
	$MLANG['default']['ll_ref'] = 'LLL:EXT:mantis_bugtracker/mod1/locallang_mod.php';
?>
