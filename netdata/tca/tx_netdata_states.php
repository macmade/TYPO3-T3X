<?php

# $Id$

// Security check
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 is not running
    trigger_error(
        'This script cannot be used outside TYPO3',
        E_USER_ERROR
    );
}

$TCA[ 'tx_netdata_states' ] = array(
    'ctrl'        => $TCA[ 'tx_netdata_states' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_netdata_states' ][ 'feInterface' ],
    'interface'   => array(
        'showRecordFieldList' => 'fullname,shortname,country_code'
    ),
    'columns' => array(
        'fullname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:netdata/lang/tx_netdata_states.xml:fullname',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'shortname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:netdata/lang/tx_netdata_states.xml:shortname',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'country_code' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:netdata/lang/tx_netdata_states.xml:country_code',
            'config' => array(
                'type'          => 'select',
                'foreign_table' => 'static_countries',
                'maxitems'      => 1,
                'minitems'      => 1
            )
        )
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'fullname;;;;1-1-1,shortname,country_code;;;;1-1-1'
        ),
    ),
    'palettes' => array()
);

?>
