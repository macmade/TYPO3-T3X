<?php

// Checks the global objects
if( !isset( $GLOBALS[ 'TSFE' ] )
    || !is_object( $GLOBALS[ 'TSFE' ] )
    || !isset( $GLOBALS[ 'TT' ] )
    || !is_object( $GLOBALS[ 'TT' ] )
) {
    
    // Access denied
    die( 'You cannot execute this file directly. It\'s meant to be included from index_ts.php' );
}

// Checks if TSpagegen has been included
if( !class_exists( 'TSpagegen' ) ) {
    
    // Includes TSpagegen
    require_once( PATH_tslib . 'class.tslib_pagegen.php' );
}

// Checks if the 'pagegen_macmade' extension si loaded
$pageGenMacmadeLoaded = ( t3lib_extMgm::isLoaded( 'pagegen_macmade' ) ) ? true : false;

// Checks the load status
if( $pageGenMacmadeLoaded ) {
    
    // Includes the helper class
    require_once( t3lib_extMgm::extPath( 'pagegen_macmade' ) . 'class.ux_tspagegen.php' );
    
    // Creates a new instance
    $pageGenMacmade = t3lib_div::makeInstance( 'ux_TSpagegen' );
}

// Init message
$GLOBALS[ 'TT' ]->push( 'pagegen.php, initialize' );

// Initialization of TSpagegen
TSpagegen::pagegenInit();

// Creates an instance of tslib_cObj
$GLOBALS[ 'TSFE' ]->newCObj();

// Gets the files to include
$tempIncludeFiles = TSpagegen::getIncFiles();

// Checks for files to include
if( is_array( $tempIncludeFiles ) ) {
    
    // Resets the array pointer
    reset( $tempIncludeFiles );
    
    // Process each file
    foreach( $tempIncludeFiles as $key => $val ) {
        
        // Includes the file
        include_once( './' . $val );
    }
}

// Message
$GLOBALS[ 'TT' ]->pull();

// Checks if some INT scripts must be included
if ( !$GLOBALS['TSFE']->isINTincScript() ) {
    
    // Message
    $GLOBALS[ 'TT' ]->push( 'pagegen.php, render' );
    
    // Checks if the extension is loaded
    if( $pageGenMacmadeLoaded ) {
        
        // Renders the page content
        $pageGenMacmade->renderContent();
        
    } else {
        
        // Renders the page content
        TSpagegen::renderContent();
    }
    
    // Subsititute paths
    $GLOBALS[ 'TSFE' ]->setAbsRefPrefix();
    
    // Message
    $GLOBALS[ 'TT' ]->pull();
}

// Cleans up the global variables that are not needed anymore
unset( $pageGenMacmadeLoaded );
unset( $pageGenMacmade );
unset( $tempIncludeFiles );
unset( $key );
unset( $val );
