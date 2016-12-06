<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Checks for the extension's configuration
if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] ) ) {
    
    // Gets the extension configuration
    $EXT_CONF = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $_EXTKEY ] );
    
    // Checks if the authentication service must be activated
    if( isset( $EXT_CONF[ 'service' ] ) && $EXT_CONF[ 'service' ] ) {
        
        // Adds the static TS templates
        t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/sv1/', 'Twitter TYPO3 Authentication Service' );
    }
    
    // Checks if the frontend plugin for OAuth must be activated
    if( isset( $EXT_CONF[ 'oauth' ] ) && $EXT_CONF[ 'oauth' ] ) {
        
        // Configures the frontend plugin
        tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 1, true );
        
        // Adds the frontend plugin wizard icon
        tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 1 );
        
        t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi1/', 'Twitter OAuth Service' );
    }
    
    // Cleaning
    unset( $EXT_CONF );
}

// Adds the save & new button
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'tx_twitterlogin_users' );

// Twitter users table
$TCA[ 'tx_twitterlogin_users' ] = array(
    'ctrl' => array (
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_twitterlogin_users.xml:tx_twitterlogin_users',
        'label'             => 'screen_name',
        'label_alt'         => 'fullname',
        'label_alt_force'   => true,
        'tstamp'            => 'tstamp',
        'crdate'            => 'crdate',
        'cruser_id'         => 'cruser_id',
        'default_sortby'    => 'ORDER BY screen_name',
        'delete'            => 'deleted',
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_twitterlogin_users.php',
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_twitterlogin_users.gif',
        'dividers2tabs'     => 1,
        'enablecolumns'     => array (
            'disabled' => 'hidden',
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'id_fe_users,twitter_id,fullname,screen_name,location,description,profile_image_url,url,protected,followers_count,profile_background_color,profile_text_color,profile_link_color,profile_sidebar_fill_color,profile_sidebar_border_color,friends_count,created_at,favourites_count,utc_offset,time_zone,profile_background_image_url,profile_background_tile,statuses_count,notifications,verified,following,status_created_at,status_id,status_text,status_source,status_truncated,status_in_reply_to_status_id,status_in_reply_to_user_id,status_favorited,status_in_reply_to_screen_name'
    )
);

?>
