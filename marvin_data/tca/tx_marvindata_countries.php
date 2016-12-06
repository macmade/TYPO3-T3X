<?php

# $Id: tx_marvindata_countries.php 181 2010-01-04 12:33:58Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_marvindata_countries' ] = array(
    'ctrl'        => $TCA[ 'tx_marvindata_countries' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_marvindata_countries' ][ 'feInterface' ],
    'interface'   => array(
        'showRecordFieldList' => 'hidden,fullname'
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1 ),
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0 )
                )
            )
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l10n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array( '', 0 ),
                ),
                'foreign_table' => 'tx_marvindata_countries',
                'foreign_table_where' => 'AND tx_marvindata_countries.pid=###CURRENT_PID### AND tx_marvindata_countries.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'fullname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_countries.xml:fullname',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'region' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_countries.xml:region',
            'l10n_mode' => 'exclude',
            'config' => array(
                'type' => 'select',
                'size' => '1',
                'maxItems' => '1',
                'minItems' => '1',
                'items' => array(
                    array( 'LLL:EXT:marvin_data/lang/tx_marvindata_countries.xml:region.0', 0 ),
                    array( 'LLL:EXT:marvin_data/lang/tx_marvindata_countries.xml:region.1', 1 ),
                    array( 'LLL:EXT:marvin_data/lang/tx_marvindata_countries.xml:region.2', 2 ),
                    array( 'LLL:EXT:marvin_data/lang/tx_marvindata_countries.xml:region.3', 3 ),
                )
            ),
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'hidden;;;;1-1-1,fullname;;;;1-1-1,region;;;;1-1-1'
        ),
    ),
    'palettes' => array()
);

?>
