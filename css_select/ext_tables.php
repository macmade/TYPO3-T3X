<?php

// Checks the TYPO3 context
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 context cannot be guessed
    die( 'Access denied.' );
}

// Checks if we are in a backend context
if( TYPO3_MODE == 'BE' ) {
    
    // Includes the PHP class to handle the CSS files
    include_once( t3lib_extMgm::extPath( 'css_select' ) . 'class.tx_cssselect_handlestylesheets.php' );
}

// Temporary TCA
$tempColumns = array(
    'tx_cssselect_stylesheets' => array(
        'exclude' => 1,
        'label'   => 'LLL:EXT:css_select/locallang_db.php:pages.tx_cssselect_stylesheets',
        'config'  => array(
            'type'          => 'select',
            'items'         => array(),
            'itemsProcFunc' => 'tx_cssselect_handleStylesheets->main',
            'size'          => 10,
            'maxitems'      => 10,
            'iconsInOptionTags' => true
        )
    ),
    'tx_cssselect_inheritance' => array(
        'exclude' => 1,
        'label'   => 'LLL:EXT:css_select/locallang_db.php:pages.tx_cssselect_inheritance',
        'config'  => array(
            'type'          => 'select',
            'items'         => array(
                array(
                    'LLL:EXT:css_select/locallang_db.php:pages.tx_cssselect_inheritance.I.0',
                    0
                ),
                array(
                    'LLL:EXT:css_select/locallang_db.php:pages.tx_cssselect_inheritance.I.1',
                    1
                ),
                array(
                    'LLL:EXT:css_select/locallang_db.php:pages.tx_cssselect_inheritance.I.2',
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
t3lib_extMgm::addToAllTCAtypes( 'pages', 'tx_cssselect_stylesheets;;;;1-1-1, tx_cssselect_inheritance' );

// Adds the static TS template
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/', 'Page StyleSheet Selector' );

// Unsets the temporary variables to clean up the global space
unset( $tempColumns );
?>
