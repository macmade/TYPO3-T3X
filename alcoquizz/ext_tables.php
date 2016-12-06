<?php

# $Id: ext_tables.php 25 2010-06-21 08:05:58Z macmade $

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

if( TYPO3_MODE == 'BE' )
{
    // Adds the BE module
    t3lib_extMgm::addModule
    (
        'web',
        'txalcoquizzM1',
        '',
        t3lib_extMgm::extPath( $_EXTKEY ) . 'mod1/'
    );
}

?>
