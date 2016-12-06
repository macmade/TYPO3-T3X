<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Registers the classes directory
tx_oop_Typo3_ClassManager::getInstance()->getInstance()->registerClassDir(
    'tx_twitterlogin_',
    t3lib_extMgm::extPath( 'twitter_login' ) . 'classes' . DIRECTORY_SEPARATOR
);

// Checks for the extension's configuration
if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] ) ) {
    
    // Gets the extension configuration
    $EXT_CONF = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] );
    
    // Checks if the authentication service must be activated
    if( isset( $EXT_CONF[ 'service' ] ) && $EXT_CONF[ 'service' ] ) {
        
        // Properties of the authentication service
        $SV_PRIORITY = ( isset( $EXT_CONF[ 'priority' ] ) ) ? ( int )$EXT_CONF[ 'priority' ] : 50;
        $SV_QUALITY  = ( isset( $EXT_CONF[ 'priority' ] ) ) ? ( int )$EXT_CONF[ 'quality' ]  : 50;
        
        // Registers the authentication service
        t3lib_extMgm::addService(
            
            // Extension key
            $_EXTKEY,
            
            // Service type
            'auth',
            
            // Service name
            'tx_twitterlogin_sv1',
            
            // Service description
            array(
                'title'       => 'Twitter Login',
                'description' => 'Allows frontend users to authenticate through the Twitter API.',
                'subtype'     => 'getUserFE,authUserFE',
                'available'   => 1,
                'priority'    => $SV_PRIORITY,
                'quality'     => $SV_QUALITY,
                'os'          => '',
                'exec'        => '',
                'classFile'   => t3lib_extMgm::extPath( $_EXTKEY ) . 'sv1/class.tx_twitterlogin_sv1.php',
                'className'   => 'tx_twitterlogin_sv1',
            )
        );
        
        // Cleaning
        unset( $SV_PRIORITY );
        unset( $SV_QUALITY );
    }
    
    // Checks if the frontend plugin for OAuth must be activated
    if( isset( $EXT_CONF[ 'oauth' ] ) && $EXT_CONF[ 'oauth' ] ) {
        
        // Adds the frontend plugin
        tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 1, false );
    }
    
    // Cleaning
    unset( $EXT_CONF );
}

?>
