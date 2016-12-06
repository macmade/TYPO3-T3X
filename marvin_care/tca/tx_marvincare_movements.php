<?php

# $Id: tx_marvincare_movements.php 150 2009-12-10 10:29:12Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_marvincare_movements' ] = array(
    'ctrl'        => $TCA[ 'tx_marvincare_movements' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_marvincare_movements' ][ 'feInterface' ],
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
        'fullname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_care/lang/tx_marvincare_movements.xml:fullname',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'movement_type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_care/lang/tx_marvincare_movements.xml:movement_type',
            'config' => array(
                'type'     => 'select',
                'size'     => '1',
                'maxItems' => '1',
                'minItems' => '1',
                'items'    => array(
                    array( 'LLL:EXT:marvin_care/lang/tx_marvincare_movements.xml:movement_type.0', 0 ),
                    array( 'LLL:EXT:marvin_care/lang/tx_marvincare_movements.xml:movement_type.1', 1 ),
                )
            ),
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'hidden;;;;1-1-1,fullname;;;;1-1-1,movement_type;;;1-1-1'
        ),
    ),
    'palettes' => array()
);

?>
