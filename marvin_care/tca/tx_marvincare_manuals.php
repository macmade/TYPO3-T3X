<?php

# $Id: tx_marvincare_manuals.php 109 2009-12-01 16:23:04Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_marvincare_manuals' ] = array(
    'ctrl'        => $TCA[ 'tx_marvincare_manuals' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_marvincare_manuals' ][ 'feInterface' ],
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
                'foreign_table' => 'tx_marvincare_manuals',
                'foreign_table_where' => 'AND tx_marvincare_manuals.pid=###CURRENT_PID### AND tx_marvincare_manuals.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'fullname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_care/lang/tx_marvincare_manuals.xml:fullname',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'id_movement' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_care/lang/tx_marvincare_manuals.xml:id_movement',
            'config' => array(
                'type'                => 'select',
                'size'                => '1',
                'foreign_table'       => 'tx_marvincare_movements',
                'foreign_table_where' => 'AND tx_marvincare_movements.pid=###CURRENT_PID### ORDER BY tx_marvincare_movements.fullname',
                'maxitems'            => 1
            ),
        ),
        'files' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_care/lang/tx_marvincare_manuals.xml:files',
            'config' => array(
                'type' => 'flex',
                'ds'   => Array(
                    'default' => 'FILE:EXT:marvin_care/flex/tx_marvincare_manuals.files.xml',
                ),
            ),
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'hidden;;;;1-1-1,fullname;;;;1-1-1,id_movement;;;;1-1-1,files;;;;1-1-1'
        ),
    ),
    'palettes' => array()
);

?>
