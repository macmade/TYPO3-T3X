<?php

# $Id: ext_tables.php 150 2009-12-10 10:29:12Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Configures the frontend plugins
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 1, true );

// Adds the frontend plugin wizard icons
tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 1 );

$TCA[ 'tx_marvincare_manuals' ] = array(
    'ctrl' => array (
        'title'                     => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_marvincare_manuals.xml:tx_marvincare_manuals',
        'label'                     => 'fullname',
        'tstamp'                    => 'tstamp',
        'crdate'                    => 'crdate',
        'cruser_id'                 => 'cruser_id',
        'default_sortby'            => 'ORDER BY fullname',
        'delete'                    => 'deleted',
        'dynamicConfigFile'         => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_marvincare_manuals.php',
        'iconfile'                  => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_marvincare_manuals.gif',
        'dividers2tabs'             => 1,
        'languageField'             => 'sys_language_uid',
        'transOrigPointerField'     => 'l10n_parent',
        'transOrigDiffSourceField'  => 'l10n_diffsource',
        'enablecolumns'             => array (
            'disabled' => 'hidden'
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden,fullname,id_movement'
    )
);

$TCA[ 'tx_marvincare_movements' ] = array(
    'ctrl' => array (
        'title'                     => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_marvincare_movements.xml:tx_marvincare_movements',
        'label'                     => 'fullname',
        'tstamp'                    => 'tstamp',
        'crdate'                    => 'crdate',
        'cruser_id'                 => 'cruser_id',
        'default_sortby'            => 'ORDER BY fullname',
        'delete'                    => 'deleted',
        'dynamicConfigFile'         => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_marvincare_movements.php',
        'iconfile'                  => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_marvincare_movements.gif',
        'dividers2tabs'             => 1,
        'enablecolumns'             => array (
            'disabled' => 'hidden'
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden,fullname,movement_type'
    )
);

?>
