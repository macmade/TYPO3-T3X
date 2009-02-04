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
    
    // Includes the class that will add the OOP documentation menu item
    require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'class.tx_oop_doxygenMenuItem.php');

    // Register the class as toolbar item
    $GLOBALS[ 'TYPO3backend' ]->addToolbarItem(
        'TYPO3 OOP Doxygen',
        'tx_oop_doxygenMenuItem'
    );

}
