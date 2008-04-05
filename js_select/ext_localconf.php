<?php

// Checks the TYPO3 context
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 context cannot be guessed
    die( 'Access denied.' );
}

// Adds the frontend plugin
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_jsselect_pi1.php',
    '_pi1',
    '',
    1
);

// Add the JS field to the rootline
$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'FE' ][ 'addRootLineFields' ] .= ',tx_jsselect_javascripts,tx_jsselect_inheritance';
?>
