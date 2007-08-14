<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Temp TCA
$tempColumns = array (
    'tx_httpsmacmade_enforcemode' => array (
        'exclude' => 1,
        'label'   => 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode',
        'config'  => array (
            'type'  => 'select',
            'items' => array (
                array( 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.0', '0' ),
                array( 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.1', '1' ),
                array( 'LLL:EXT:https_macmade/locallang_db.xml:pages.tx_httpsmacmade_enforcemode.I.2', '2' )
            ),
            'size'     => 1,
            'maxitems' => 1
        )
    )
);

// Load pages TCA and add fields
t3lib_div::loadTCA( 'pages' );
t3lib_extMgm::addTCAcolumns( 'pages', $tempColumns, 1 );
t3lib_extMgm::addToAllTCAtypes( 'pages', 'tx_httpsmacmade_enforcemode;;;;1-1-1' );

// Static template
t3lib_extMgm::addStaticFile(
    $_EXTKEY,
    'pi1/static/',
    'HTTPS Enforcer / macmade.net'
);
?>
