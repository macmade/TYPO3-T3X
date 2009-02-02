<?php

// Security check
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 is not running
    trigger_error(
        'This script cannot be used outside TYPO3',
        E_USER_ERROR
    );
}

// Backend options
if( TYPO3_MODE === 'BE' ) {
    
    // Adds the backend module
    t3lib_extMgm::addModule(
        'web',
        'txooptestM1',
        '',
        t3lib_extMgm::extPath( $_EXTKEY ) . 'mod1/'
    );
}
