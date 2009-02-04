<?php

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Checks if TYPO3 is running
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 is not running
    trigger_error(
        'TYPO3 does not seem to be running. This script can only be used with TYPO3.',
        E_USER_ERROR
    );
}

// Backend specific options
if( TYPO3_MODE === 'BE' ) {
    
    // Adds the top menu item for the OOP documentation
    $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'typo3/backend.php' ][ 'additionalBackendItems' ][] = t3lib_extMgm::extPath( $_EXTKEY ) . 'docmenu/doxygenMenuItem.inc.php';
}
