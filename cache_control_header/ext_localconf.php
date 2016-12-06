<?php

# $Id: ext_localconf.php 36 2010-05-20 13:28:41Z macmade $

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

require_once( t3lib_extMgm::extPath( 'cache_control_header' ) . 'Classes/class.tx_cachecontrolheader_controller.php' );

$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SC_OPTIONS' ][ 'tslib/class.tslib_fe.php' ][ 'isOutputting' ][] = 'tx_cachecontrolheader_controller->processDirective';

?>
