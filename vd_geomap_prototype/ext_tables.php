<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Include the TCA helper class
include_once(
    t3lib_extMgm::extPath( 'vd_geomap_prototype' )
  . 'class.tx_vdgeomapprototype_tca.php'
);

// Load content TCA
t3lib_div::loadTCA( 'tt_content' );

// Plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi1' ] = 'layout,select_key,pages,recursive';

// Add flexform field to plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi1' ]     = 'pi_flexform';

// Add flexform DataStructure
t3lib_extMgm::addPiFlexFormValue(
    $_EXTKEY . '_pi1',
    'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml'
);

// Add FE plugin
t3lib_extMgm::addPlugin(
    array(
        'LLL:EXT:vd_geomap_prototype/locallang_db.xml:tt_content.list_type_pi1',
        $_EXTKEY . '_pi1'
    ),
    'list_type'
);

// Content wizard
if ( TYPO3_MODE == 'BE' ) {
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_vdgeomapprototype_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY )
                                                                                                         . 'pi1/class.tx_vdgeomapprototype_pi1_wizicon.php';
}
?>
