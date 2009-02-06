<?php

# $Id$

// Security check
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 is not running
    trigger_error(
        'This script cannot be used outside TYPO3',
        E_USER_ERROR
    );
}

// Temporary TCA
$TMP_TCA = array(
    'tx_httpsmacmade_enforcemode' => array(
        'exclude' => 1,
        'label'   => 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode',
        'config'  => array(
            'type'  => 'select',
            'items' => array(
                array( 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.0', '0' ),
                array( 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.1', '1' ),
                array( 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.2', '2' )
            ),
            'size'     => 1,
            'maxitems' => 1
        )
    )
);

// Loads the 'pages' TCA and adds the fields
t3lib_div::loadTCA( 'pages' );
t3lib_extMgm::addTCAcolumns( 'pages', $TMP_TCA, 1 );
t3lib_extMgm::addToAllTCAtypes( 'pages', 'tx_httpsmacmade_enforcemode;;;;1-1-1' );

// Adds the static TS template
t3lib_extMgm::addStaticFile(
    $_EXTKEY,
    'static/pi1/',
    'HTTPS Enforcer / macmade.net'
);

// Cleans up global variables
unset( $TMP_TCA )
