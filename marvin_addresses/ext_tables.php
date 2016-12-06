<?php

# $Id: ext_tables.php 146 2009-12-09 13:20:17Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Configures the frontend plugins
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 1, true );

// Adds the frontend plugin wizard icons
tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 1 );

// Adds the static TS templates
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi1/', 'Marvin - Addresses' );

$TCA[ 'tx_marvinaddresses_addresses' ] = array(
    'ctrl' => array (
        'title'                     => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_marvinaddresses_addresses.xml:tx_marvinaddresses_addresses',
        'label'                     => 'fullname',
        'tstamp'                    => 'tstamp',
        'crdate'                    => 'crdate',
        'cruser_id'                 => 'cruser_id',
        'default_sortby'            => 'ORDER BY fullname',
        'delete'                    => 'deleted',
        'dynamicConfigFile'         => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_marvinaddresses_addresses.php',
        'iconfile'                  => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_marvinaddresses_addresses.gif',
        'dividers2tabs'             => 1,
        'languageField'             => 'sys_language_uid',
        'transOrigPointerField'     => 'l10n_parent',
        'transOrigDiffSourceField'  => 'l10n_diffsource',
        'enablecolumns'             => array (
            'disabled' => 'hidden'
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden,address_type,sav,fullname,address,address_number,zip,id_city,phone,phone2,fax,email,email2,url,infos,remarks'
    )
);

?>
