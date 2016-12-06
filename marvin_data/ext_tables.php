<?php

# $Id: ext_tables.php 181 2010-01-04 12:33:58Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

$TCA[ 'tx_marvindata_countries' ] = array(
    'ctrl' => array (
        'title'                     => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_marvindata_countries.xml:tx_marvindata_countries',
        'label'                     => 'fullname',
        'tstamp'                    => 'tstamp',
        'crdate'                    => 'crdate',
        'cruser_id'                 => 'cruser_id',
        'default_sortby'            => 'ORDER BY fullname',
        'delete'                    => 'deleted',
        'dynamicConfigFile'         => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_marvindata_countries.php',
        'iconfile'                  => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_marvindata_countries.gif',
        'dividers2tabs'             => 1,
        'languageField'             => 'sys_language_uid',
        'transOrigPointerField'     => 'l10n_parent',
        'transOrigDiffSourceField'  => 'l10n_diffsource',
        'enablecolumns'             => array (
            'disabled' => 'hidden'
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden,fullname,region'
    )
);

$TCA[ 'tx_marvindata_cities' ] = array(
    'ctrl' => array (
        'title'                     => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_marvindata_cities.xml:tx_marvindata_cities',
        'label'                     => 'fullname',
        'tstamp'                    => 'tstamp',
        'crdate'                    => 'crdate',
        'cruser_id'                 => 'cruser_id',
        'default_sortby'            => 'ORDER BY fullname',
        'delete'                    => 'deleted',
        'dynamicConfigFile'         => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_marvindata_cities.php',
        'iconfile'                  => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_marvindata_cities.gif',
        'dividers2tabs'             => 1,
        'languageField'             => 'sys_language_uid',
        'transOrigPointerField'     => 'l10n_parent',
        'transOrigDiffSourceField'  => 'l10n_diffsource',
        'enablecolumns'             => array (
            'disabled' => 'hidden'
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden,fullname,id_country'
    )
);

$TCA[ 'tx_marvindata_languages' ] = array(
    'ctrl' => array (
        'title'                     => 'LLL:EXT:' . $_EXTKEY . '/lang/tx_marvindata_languages.xml:tx_marvindata_languages',
        'label'                     => 'fullname',
        'tstamp'                    => 'tstamp',
        'crdate'                    => 'crdate',
        'cruser_id'                 => 'cruser_id',
        'default_sortby'            => 'ORDER BY fullname',
        'delete'                    => 'deleted',
        'dynamicConfigFile'         => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_marvindata_languages.php',
        'iconfile'                  => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_marvindata_languages.gif',
        'dividers2tabs'             => 1,
        'languageField'             => 'sys_language_uid',
        'transOrigPointerField'     => 'l10n_parent',
        'transOrigDiffSourceField'  => 'l10n_diffsource',
        'enablecolumns'             => array (
            'disabled' => 'hidden'
        )
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden,fullname,coord_x,coord_y'
    )
);

?>
