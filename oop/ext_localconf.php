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
    t3lib_extMgm::extPath( $_EXTKEY )
  . DIRECTORY_SEPARATOR
  . 'classes'
  . DIRECTORY_SEPARATOR
  . 'tx'
  . DIRECTORY_SEPARATOR
  . 'oop'
  . DIRECTORY_SEPARATOR
  . 'Core'
  . DIRECTORY_SEPARATOR
  . 'ClassManager.class.php'
);

// Registers an SPL autoload method to use to load the classes form this package
spl_autoload_register( array( 'tx_oop_Core_ClassManager', 'autoLoad' ) );

// Registers an SPL autoload method to use to load the classes form TYPO3 (t3lib or tslib)
spl_autoload_register( array( 'tx_oop_Typo3_ClassManager', 'autoLoad' ) );

// Checks for the extension configuration
if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] ) ) {
    
    // Gets the extension configuration
    $OOP_EXT_CONF = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] );
    
    // Checks if the ouput from the XHTML classes must be formatted
    if( isset( $OOP_EXT_CONF[ 'htmlFormat' ] ) ) {
        
        // Sets the HTML formatting option
        tx_oop_Xhtml_Tag::useFormattedOutput( ( boolean )$OOP_EXT_CONF[ 'htmlFormat' ] );
    }
    
    // Checks if the T3Lib classes must be auto-loaded
    if( isset( $OOP_EXT_CONF[ 't3libAutoLoad' ] ) ) {
        
        // Auto-loads the T3Lib classes
        tx_oop_Typo3_ClassManager::getInstance()->registerClassDir(
            't3lib_',
            PATH_t3lib
        );
    }
    
    // Checks if the TSLib classes must be auto-loaded
    if( isset( $OOP_EXT_CONF[ 'tslibAutoLoad' ] ) ) {
        
        // Auto-loads the TSLib classes
        tx_oop_Typo3_ClassManager::getInstance()->registerClassDir(
            'tslib_',
            t3lib_extMgm::extPath( 'cms' ) . 'tslib' . DIRECTORY_SEPARATOR
        );
    }
    
    // Cleans up global variables
    unset( $OOP_EXT_CONF );
}
