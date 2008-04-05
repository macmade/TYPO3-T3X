<?php

// Checks the TYPO3 context
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 context cannot be guessed
    die( 'Access denied.' );
}

// Checks if we are in a backend context
if( TYPO3_MODE == 'BE' ) {
    
    // Includes the PHP class to handle the JS files
    include_once( t3lib_extMgm::extPath( 'js_select' ) . 'class.tx_jsselect_handlejavascripts.php' );
}

// Temporary TCA
$tempColumns = array(
    'tx_jsselect_javascripts' => array(
        'exclude' => 1,
        'label'   => 'LLL:EXT:js_select/locallang_db.php:pages.tx_jsselect_javascripts',
        'config'  => array(
            'type'          => 'select',
            'items'         => array(),
            'itemsProcFunc' => 'tx_jsselect_handleJavascripts->main',
            'size'          => 10,
            'maxitems'      => 10,
            'iconsInOptionTags' => true
        )
    ),
    'tx_jsselect_inheritance' => array(
        'exclude' => 1,
        'label'   => 'LLL:EXT:js_select/locallang_db.php:pages.tx_jsselect_inheritance',
        'config'  => array(
            'type'          => 'select',
            'items'         => array(
                array(
                    'LLL:EXT:js_select/locallang_db.php:pages.tx_jsselect_inheritance.I.0',
                    0
                ),
                array(
                    'LLL:EXT:js_select/locallang_db.php:pages.tx_jsselect_inheritance.I.1',
                    1
                ),
                array(
                    'LLL:EXT:js_select/locallang_db.php:pages.tx_jsselect_inheritance.I.2',
                    2
                )
            ),
            'size'          => 1,
            'maxitems'      => 1
        )
    )
);

// Load the TCA for the 'pages' table
t3lib_div::loadTCA( 'pages' );

// Adds the fields to the 'pages' TCA
t3lib_extMgm::addTCAcolumns( 'pages', $tempColumns, 1 );

// Adds the fields to all types of the 'pages' table
t3lib_extMgm::addToAllTCAtypes( 'pages', 'tx_jsselect_javascripts;;;;1-1-1, tx_jsselect_inheritance' );

// Adds the static TS template
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/', 'Page JavaScript Selector' );

// Unsets the temporary variables to clean up the global space
unset( $tempColumns );
?>
