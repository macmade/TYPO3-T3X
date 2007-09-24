<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Storage place for the application data
$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $_EXTKEY ] = array();

// Hooks
$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $_EXTKEY ][ 'afterTitleHook' ] = array();

// Gets the extension configuration
$extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] );

// Regiters the extension configuration
$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $_EXTKEY ][ 'config' ] = $extConf;

// Cleans up the global variables that are not needed anymore
unset( $extConf );
?>
