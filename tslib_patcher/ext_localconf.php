<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// XClass fo tslib_content
if( TYPO3_MODE == 'FE' ) {
    
    // Gets the extension configuration
    $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ]['EXT']['extConf']['tslib_patcher'] );
    
    // Checks the configuration
    if( is_array( $extConf ) ) {
        
        // Class name mapping
        $classMap = array(
            'tslib_content' => 'ux_tslib_cobj',
            'tslib_menu'    => 'ux_tslib_menu'
        );
        
        // Process each class
        foreach( $extConf as $className => $enable ) {
            
            // Checks if the patch is enabled for the current class
            if( $enable ) {
                
                // XClass
                $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'tslib/class.' . $className . '.php' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'class.' . $classMap[ $className ] . '.php';
            }
        }
    }
    
    // Cleans up global variables
    unset( $extConf );
    unset( $classMap );
    unset( $className );
    unset( $enable );
}
?>
