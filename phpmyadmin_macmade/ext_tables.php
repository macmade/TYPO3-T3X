<?php
    if( !defined( 'TYPO3_MODE' ) ) {
        die( 'Access denied.' );
    }
    
    // Backend options
    if( TYPO3_MODE === 'BE' ) {
        
        // Adds the backend module
        t3lib_extMgm::addModule(
            'tools',
            'txphpmyadminmacmadeM1',
            '',
            t3lib_extMgm::extPath( $_EXTKEY ) . 'mod1/'
        );
    }
?>
