<?php

# $Id: ext_localconf.php 37 2010-06-02 08:40:53Z macmade $

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

if( TYPO3_MODE === 'FE' )
{
    require_once( t3lib_extMgm::extPath( 'netanalytics' ) . 'Classes' . DIRECTORY_SEPARATOR . 'class.tx_netanalytics_tracker.php' );
    
    $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SC_OPTIONS' ][ 't3lib/class.t3lib_pagerenderer.php' ][ 'render-preProcess' ][] = 'tx_netanalytics_tracker->addTrackerCode';
}

?>
