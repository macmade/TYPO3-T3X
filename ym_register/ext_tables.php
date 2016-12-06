<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Configures the frontend plugins
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 1, true );
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 2, true );
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 3, false );
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 4, false );

// Adds the frontend plugin wizard icon
tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 1 );
tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 2 );

// Adds the static TS templates
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi1/', 'yourmove.ch - Registration' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi2/', 'yourmove.ch - Profile' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi3/', 'yourmove.ch - Movie publish confirmation' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi4/', 'yourmove.ch - Image publish confirmation' );

// Adds the save & new button
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'tx_ymregister_users' );

// Twitter users table
$TCA[ 'tx_ymregister_users' ] = array(
    'ctrl' => array (
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_ymregister_users.xml:tx_ymregister_users',
        'label'             => 'lastname',
        'label_alt'         => 'firstname',
        'label_alt_force'   => true,
        'tstamp'            => 'tstamp',
        'crdate'            => 'crdate',
        'cruser_id'         => 'cruser_id',
        'default_sortby'    => 'ORDER BY lastname,firstname',
        'delete'            => 'deleted',
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_ymregister_users.php',
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_ymregister_users.gif',
        'dividers2tabs'     => 1,
        'enablecolumns'     => array (
            'disabled' => 'hidden'
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden,id_fe_users,id_tx_facebookconnect_users,lastname,firstname,age,nickname,address,zip,id_tx_netdata_states,id_tx_netdata_cities,phone,confirm_hash'
    )
);

?>
