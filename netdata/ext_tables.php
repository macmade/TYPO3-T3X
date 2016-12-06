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
    'feInterface' => array( 'fe_admin_fieldList' => '' ),
    'ctrl'        => array(
        'title'             => 'LLL:EXT:netdata/lang/tx_netdata_states.xml:tx_netdata_states',
        'label'             => 'fullname',
        'label_alt'         => 'shortname,country_code',
        'label_alt_force'   => true,
        'readOnly'          => true,
        'adminOnly'         => true,
        'is_static'         => true,
        'rootLevel'         => 1,
        'tstamp'            => 'tstamp',
        'crdate'            => 'crdate',
        'cruser_id'         => 'cruser_id',
        'default_sortby'    => 'ORDER BY fullname',
        'delete'            => 'deleted',
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_netdata_states.php',
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_netdata_states.gif',
        'enablecolumns'     => array()
    )
);

$TCA[ 'tx_netdata_cities' ] = array(
    'feInterface' => array( 'fe_admin_fieldList' => '' ),
    'ctrl'        => array(
        'title'             => 'LLL:EXT:netdata/lang/tx_netdata_cities.xml:tx_netdata_cities',
        'label'             => 'fullname',
        'label_alt'         => 'district_name',
        'label_alt_force'   => true,
        'readOnly'          => true,
        'adminOnly'         => true,
        'is_static'         => true,
        'rootLevel'         => 1,
        'tstamp'            => 'tstamp',
        'crdate'            => 'crdate',
        'cruser_id'         => 'cruser_id',
        'default_sortby'    => 'ORDER BY fullname',
        'delete'            => 'deleted',
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_netdata_cities.php',
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/tx_netdata_cities.gif',
        'enablecolumns'     => array()
    )
);

?>

