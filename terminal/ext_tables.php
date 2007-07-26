<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}
// Backend options
if ( TYPO3_MODE == 'BE' ) {
    
    // Add BE module
    t3lib_extMgm::addModule(
        'tools',
        'txterminalM1',
        '',
        t3lib_extMgm::extPath( $_EXTKEY ) . 'mod1/'
    );
}
?>
