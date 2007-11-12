<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}

// Load content TCA
t3lib_div::loadTCA( 'tt_content' );

// Plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi1' ] = 'layout,select_key,pages,recursive';

// Add flexform field to plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi1' ] = 'pi_flexform';

// Add flexform DataStructure
t3lib_extMgm::addPiFlexFormValue(
    $_EXTKEY . '_pi1',
    'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml'
);

// Add FE plugin
t3lib_extMgm::addPlugin(
    array(
        'LLL:EXT:vd_municipalities_search/locallang_db.xml:tt_content.list_type_pi1',
        $_EXTKEY . '_pi1'
    ),
    'list_type'
);

// Content wizard
if ( TYPO3_MODE == 'BE' ) {
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_vdmunicipalitiessearch_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi1/class.tx_vdmunicipalitiessearch_pi1_wizicon.php';
}

// TCA helper class
require_once( t3lib_extMgm::extPath( 'vd_municipalities_search' ) . 'class.tx_vdmunicipalitiessearch_tca.php' );

// Institutions table
$TCA[ 'tx_vdmunicipalitiessearch_institutions' ] = array(

// Control section
'ctrl' => array(
    
    // Table title
    'title'             => 'LLL:EXT:vd_municipalities_search/locallang_db.xml:tx_vdmunicipalitiessearch_institutions',
    
    // Table label field
    'label'             => 'name',
    
    // Modification date
    'tstamp'            => 'tstamp',
    
    // Creation date
    'crdate'            => 'crdate',
    
    // Creation user
    'cruser_id'         => 'cruser_id',
    
    // Sorty by field
    'default_sortby'    => 'ORDER BY name',
    
    // Delete flag
    'delete'            => 'deleted',
    
    // Only admin users can view records
    'adminOnly'         => 1,
    
    // Records are stored on site container
    'rootLevel'         => 1,
    
    // Records are 'static'
    'is_static'         => 1,
    
    // External configuration file
    'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
    
    // Table icon
    'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_vdmunicipalitiessearch_institutions.gif'
),

// Frontend options
'feInterface' => array(
    
    // Available fields
    'fe_admin_fieldList' => ''
)
);
// Temp TCA
$tempColumns = array (
    'tx_vdmunicipalitiessearch_institution' => array (
        'exclude' => 1,
        'label'   => 'LLL:EXT:vd_municipalities_search/locallang_db.php:pages.tx_vdmunicipalitiessearch_institution',
        'config'  => array (
            'type'                => 'select',
            'items'               => array(
                array( '', 0 ),
            ),
            'foreign_table'       => 'tx_vdmunicipalitiessearch_institutions',
            'foreign_table_where' => 'ORDER BY name',
            'size'                => 1,
            'minitems'            => 0,
            'maxitems'            => 1
        )
    ),
    'tx_vdmunicipalitiessearch_municipalities' => array (
        'exclude'     => 1,
        'label'       => 'LLL:EXT:vd_municipalities_search/locallang_db.php:pages.tx_vdmunicipalitiessearch_municipalities',
        'displayCond' => 'FIELD:tx_vdmunicipalitiessearch_institution:>:0',
        'config'      => array (
            'type'          => 'select',
            'items'         => array(),
            'itemsProcFunc' => 'tx_vdmunicipalitiessearch_tca->getMunicipalities',
            'size'          => 10,
            'minitems'      => 0,
            'maxitems'      => 50
        )
    ),
);

// Load pages TCA
t3lib_div::loadTCA( 'pages' );

if( isset( $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] ) && $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] ) {
    
    // Add update action
    $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] .= ',tx_vdmunicipalitiessearch_institution';
    
} else {
    
    // Add update action
    $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] = 'tx_vdmunicipalitiessearch_institution';
}

// Add fields to the pages TCA
t3lib_extMgm::addTCAcolumns( 'pages', $tempColumns, 1 );
t3lib_extMgm::addToAllTCAtypes(
    'pages',
    'tx_vdmunicipalitiessearch_institution;;;;1-1-1, tx_vdmunicipalitiessearch_municipalities'
);
?>

