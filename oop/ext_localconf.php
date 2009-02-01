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

// Checks the PHP version
if( ( double )PHP_VERSION < 5 ) {
    
    // We are running PHP4
    trigger_error(
        'PHP version 5 is required to use this script (actual version is ' . PHP_VERSION . ')',
        E_USER_ERROR
    );
}

// Checks for the SPL
if( !function_exists( 'spl_autoload_register' ) ) {
    
    // The SPL is unavailable
    throw new Exception( 'The SPL (Standard PHP Library) is required to use this script' );
}

// Checks for the SimpleXmlElement class
if( !class_exists( 'SimpleXmlElement' ) ) {
    
    // SimpleXml is unavailable
    throw new Exception( 'The SimpleXmlElement class is required to use this script' );
}

// Includes the class manager
require_once(
    t3lib_extMgm::extPath( 'oop' )
  . DIRECTORY_SEPARATOR
  . 'classes'
  . DIRECTORY_SEPARATOR
  . 'class.tx_oop_classmanager.php'
);

// Registers an SPL autoload method to use to load the classes form this package
spl_autoload_register( array( 'tx_oop_classManager', 'autoLoad' ) );