<?php

# $Id: tx_marvindata_languages.php 107 2009-12-01 16:22:32Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_marvindata_languages' ] = array(
    'ctrl'        => $TCA[ 'tx_marvindata_languages' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_marvindata_languages' ][ 'feInterface' ],
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
                'foreign_table' => 'tx_marvindata_languages',
                'foreign_table_where' => 'AND tx_marvindata_languages.pid=###CURRENT_PID### AND tx_marvindata_languages.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'fullname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_languages.xml:fullname',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'hidden;;;;1-1-1,fullname;;;;1-1-1'
        ),
    ),
    'palettes' => array()
);

?>
