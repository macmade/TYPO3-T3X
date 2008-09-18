<?php

# $Id$

// Loads the module configuration
unset( $MCONF );
require( 'conf.php' );

// Includes the TYPO3 initialization files
require( $BACK_PATH . 'init.php' );
require( $BACK_PATH . 'template.php' );

// Includes the TYPO3 module classes
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
require_once( PATH_t3lib . 'class.t3lib_tcemain.php' );

// Includes the language labels
$LANG->includeLLFile( 'EXT:adler_contest/lang/mod1.xml' );

// Checks the access to the module
$BE_USER->modAccess( $MCONF, 1 );

// Includes the backend module class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'mod1/class.tx_adlercontest_module1.php' );

// Creates the module instance
$SOBE = t3lib_div::makeInstance( 'tx_adlercontest_module1' );

// Initializes the module
$SOBE->init();

// Process each include file
foreach( $SOBE->include_once as $includeFile ) {
    
    // Includes the file
    include_once( $includeFile );
}

// Launches the module main method
$SOBE->main();

// Writes the module content
print $SOBE;
