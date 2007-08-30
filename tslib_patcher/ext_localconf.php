<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// XClass fo tslib_content
if( TYPO3_MODE == 'FE' ) {
    
    // Storage place for the application data
    $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $_EXTKEY ] = array();
    
    // Registers the _GET array
    $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $_EXTKEY ][ 'getVars' ] = t3lib_div::_GET();
    
    // Gets the extension configuration
    $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] );
    
    // Regiters the extension configuration
    $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $_EXTKEY ][ 'config' ] = $extConf;
    
    // Checks the configuration
    if( is_array( $extConf ) ) {
        
        // Checks for the lightbox compatibility mode
        if( isset( $extConf[ 'lightBoxCompat' ] ) && $extConf[ 'lightBoxCompat' ] && t3lib_extMgm::isLoaded( 'kj_imagelightbox2' ) ) {
            
            // Lightbox compatibility mode is possible
            $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $_EXTKEY ][ 'lightBoxCompat' ] = true;
            
        } else {
            
            // No lightbox compatibility
            $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $_EXTKEY ][ 'lightBoxCompat' ] = false;
        }
        
        // Checks for the cHash checker
        if( isset( $extConf[ 'cHashForce' ] ) && $extConf[ 'cHashForce' ] ) {
            
            // Registers the cHash checker method
            $TYPO3_CONF_VARS[ 'SC_OPTIONS' ][ 'tslib/class.tslib_fe.php' ][ 'contentPostProc-all' ][] = 'EXT:'
                                                                                                      . $_EXTKEY
                                                                                                      . '/class.tx_tslibpatcher.php:tx_tslibpatcher->cHashCheck';
        }
        
        // Checks for RealURL
        if( isset( $extConf[ 'realurl' ] ) && $extConf[ 'realurl' ] && t3lib_extMgm::isLoaded( 'realurl' ) ) {
            
            // XClass
            $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/realurl/class.tx_realurl.php' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'class.ux_tx_realurl.php';
        }
        
        // Class name mapping
        $classMap = array(
            'tslib_content' => 'ux_tslib_cobj',
            'tslib_menu'    => 'ux_tslib_menu'
        );
        
        // Process each class
        foreach( $extConf as $className => $enable ) {
            
            // Do not process RealURL here
            if( $className == 'realurl' || $className == 'cHashForce' || $className == 'lighBoxCompat' ) {
                
                continue;
            }
            
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
