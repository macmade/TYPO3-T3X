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

$TCA[ 'tx_netdata_cities' ] = array(
    'ctrl'        => $TCA[ 'tx_netdata_cities' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_netdata_cities' ][ 'feInterface' ],
    'interface'   => array(
        'showRecordFieldList' => 'id_tx_netdata_states,fullname,shortname,district_name,district_number'
    ),
    'columns' => array(
        'fullname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:netdata/lang/tx_netdata_cities.xml:fullname',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'shortname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:netdata/lang/tx_netdata_cities.xml:shortname',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'district_name' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:netdata/lang/tx_netdata_cities.xml:district_name',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'district_number' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:netdata/lang/tx_netdata_cities.xml:district_number',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
                'eval' => 'int'
            )
        ),
        'id_tx_netdata_states' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:netdata/lang/tx_netdata_cities.xml:id_tx_netdata_states',
            'config' => array(
                'type'          => 'select',
                'foreign_table' => 'tx_netdata_states',
                'maxitems'      => 1
            )
        )
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'fullname;;;;1-1-1,shortname,district_name;;;;1-1-1,district_number,id_tx_netdata_states;;;;1-1-1'
        ),
    ),
    'palettes' => array()
);

?>
