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

// Save & New options for the database tables
t3lib_extMgm::addUserTSConfig( 'options.saveDocNew.tx_netdata_states=1' );
t3lib_extMgm::addUserTSConfig( 'options.saveDocNew.tx_netdata_cities=1' );
?>
