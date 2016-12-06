<?php

# $Id: ext_localconf.php 25 2010-06-21 08:05:58Z macmade $

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

$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'FE' ][ 'eID_include' ][ 'tx_alcoquizz_eid' ] = 'EXT:alcoquizz/scripts/eid.inc.php';

?>
