<?php

# $Id: ext_tables.php 36 2010-05-20 13:28:41Z macmade $

// Security check
if( !defined( 'TYPO3_MODE' ) )
{
    // TYPO3 is not running
    trigger_error
    (
        'TYPO3 does not seem to be running. This script can only be used with TYPO3.',
        E_USER_ERROR
    );
}

// Temporary TCA
$TMP_TCA = array
(
    'tx_cachecontrolheader_directive' => array
    (
        'exclude'   => 1,
        'label'     => 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive',
        'config'    => array
        (
            'type'      => 'select',
            'size'      => 1,
            'maxitems'  => 1,
            'items'     => array
            (
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.0', 0 ),
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.1', 1 ),
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.2', 2 ),
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.3', 3 ),
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.4', 4 ),
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.5', 5 ),
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.6', 6 ),
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.7', 7 ),
                array( 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive.I.8', 8 )
            )
        )
    )
);

// Second field
$TMP_TCA[ 'tx_cachecontrolheader_directive_feuser' ]            = $TMP_TCA[ 'tx_cachecontrolheader_directive' ];
$TMP_TCA[ 'tx_cachecontrolheader_directive_feuser' ][ 'label' ] = 'LLL:EXT:cache_control_header/locallang_db.php:pages.tx_cachecontrolheader_directive_feuser';

// Loads the TCA for the 'pages' table
t3lib_div::loadTCA( 'pages' );

// Adds the fields to the 'pages' TCA
t3lib_extMgm::addTCAcolumns( 'pages', $TMP_TCA, 1 );

// Adds the fields to all types of the 'pages' table
t3lib_extMgm::addToAllTCAtypes( 'pages', 'tx_cachecontrolheader_directive;;;;1-1-1,tx_cachecontrolheader_directive_feuser' );

// Unsets the temporary variables to clean up the global scope
unset( $TMP_TCA );

?>
