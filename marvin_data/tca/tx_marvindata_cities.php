<?php

# $Id: tx_marvindata_cities.php 144 2009-12-09 13:18:27Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_marvindata_cities' ] = array(
    'ctrl'        => $TCA[ 'tx_marvindata_cities' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_marvindata_cities' ][ 'feInterface' ],
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
                'foreign_table' => 'tx_marvindata_cities',
                'foreign_table_where' => 'AND tx_marvindata_cities.pid=###CURRENT_PID### AND tx_marvindata_cities.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'fullname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_cities.xml:fullname',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'id_country' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_cities.xml:id_country',
            'config' => array(
                'type'                => 'select',
                'size'                => '1',
                'foreign_table'       => 'tx_marvindata_countries',
                'foreign_table_where' => 'AND sys_language_uid = 0 AND tx_marvindata_countries.pid=###CURRENT_PID### ORDER BY tx_marvindata_countries.fullname',
                'maxitems'            => 1
            ),
        ),
        'id_country' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_cities.xml:id_country',
            'config' => array(
                'type'                => 'select',
                'size'                => '1',
                'foreign_table'       => 'tx_marvindata_countries',
                'foreign_table_where' => 'AND sys_language_uid = 0 AND tx_marvindata_countries.pid=###CURRENT_PID### ORDER BY tx_marvindata_countries.fullname',
                'maxitems'            => 1
            ),
        ),
        'coord_x' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_cities.xml:coord_x',
            'config' => array(
                'type' => 'input',
                'size' => '5',
                'eval' => 'int'
            ),
        ),
        'coord_y' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_cities.xml:coord_y',
            'config' => array(
                'type' => 'input',
                'size' => '5',
                'eval' => 'int'
            ),
        ),
        'map_help' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_data/lang/tx_marvindata_cities.xml:map_help',
            'config' => array(
                'type'     => 'user',
                'userFunc' => 'tx_marvindata_Tca_Helper->mapHelpImage'
            ),
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => '--div--;LLL:EXT:marvin_data/lang/tx_marvindata_cities.xml:tabs.general,hidden;;;;1-1-1,fullname;;;;1-1-1,id_country,--div--;LLL:EXT:marvin_data/lang/tx_marvindata_cities.xml:tabs.coords,coord_x;;;;1-1-1,coord_y,map_help;;;;1-1-1'
        ),
    ),
    'palettes' => array()
);

?>
