<?php
if( !defined( 'TYPO3_MODE' ) ) {
    
    die( 'Access denied.' );
}

// Checks the TYPO3 mode
if( TYPO3_MODE == 'BE' ) {
    
    // Adds the CSM item
    $GLOBALS[ 'TBE_MODULES_EXT' ][ 'xMOD_alt_clickmenu' ][ 'extendCMclasses' ][] = array(
        'name' => 'tx_setpagetype_cm1',
        'path' => t3lib_extMgm::extPath( $_EXTKEY ) . 'class.tx_setpagetype_cm1.php'
    );
}
?>
