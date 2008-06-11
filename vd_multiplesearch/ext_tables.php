<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}

// Includes the TCA helper class
require_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'class.tx_vdmultiplesearch_tca.php' );

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
        'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_content.list_type_pi1',
        $_EXTKEY . '_pi1'
    ),
    'list_type'
);

// Static templates
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/ts/', 'VD - Multiple Search' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/css/', 'VD - Multiple Search - CSS Styles' );

// Backend options
if ( TYPO3_MODE == 'BE' ) {
    
    // Content wizard
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_vdmultiplesearch_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi1/class.tx_vdmultiplesearch_pi1_wizicon.php';
}

// Public table
$TCA[ 'tx_vdmultiplesearch_public' ] = array(

    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tx_vdmultiplesearch_public',
        
        // Table label field
        'label'             => 'title',
        
        // Modification date
        'tstamp'            => 'tstamp',
        
        // Creation date
        'crdate'            => 'crdate',
        
        // Creation user
        'cruser_id'         => 'cruser_id',
        
        // Sorty by field
        'default_sortby'    => 'ORDER BY title',
        
        // Delete flag
        'delete'            => 'deleted',
        
        // Only admin users can view records
        #'adminOnly'         => 1,
        
        // Records are stored on site container
        #'rootLevel'         => 1,
        
        // External configuration file
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_vdmultiplesearch_public.gif'
    ),
    
    // Frontend options
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);

// Themes table
$TCA[ 'tx_vdmultiplesearch_themes' ] = array(

    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tx_vdmultiplesearch_themes',
        
        // Table label field
        'label'             => 'title',
        
        // Modification date
        'tstamp'            => 'tstamp',
        
        // Creation date
        'crdate'            => 'crdate',
        
        // Creation user
        'cruser_id'         => 'cruser_id',
        
        // Sorty by field
        'default_sortby'    => 'ORDER BY title',
        
        // Delete flag
        'delete'            => 'deleted',
        
        // Only admin users can view records
        #'adminOnly'         => 1,
        
        // Records are stored on site container
        #'rootLevel'         => 1,
        
        // External configuration file
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_vdmultiplesearch_themes.gif'
    ),
    
    // Frontend options
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);

// Keywords table
$TCA[ 'tx_vdmultiplesearch_keywords' ] = array(

    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tx_vdmultiplesearch_keywords',
        
        // Table label field
        'label'             => 'keyword',
        
        // Modification date
        'tstamp'            => 'tstamp',
        
        // Creation date
        'crdate'            => 'crdate',
        
        // Creation user
        'cruser_id'         => 'cruser_id',
        
        // Sorty by field
        'default_sortby'    => 'ORDER BY keyword',
        
        // Delete flag
        'delete'            => 'deleted',
        
        // Only admin users can view records
        #'adminOnly'         => 1,
        
        // Records are stored on site container
        #'rootLevel'         => 1,
        
        // External configuration file
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_vdmultiplesearch_keywords.gif'
    ),
    
    // Frontend options
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);

// Temp TCA
$tempColumns = array (
    'tx_vdmultiplesearch_enable' => array (
        'exclude' => 1,
        'label'   => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:pages.tx_vdmultiplesearch_enable',
        'config'  => array (
            'type' => 'check'
        )
    )
);

// Public field
$tempColumns[ 'tx_vdmultiplesearch_public' ] = array (
    'exclude'     => 1,
    'label'       => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:pages.tx_vdmultiplesearch_public',
    'displayCond' => 'FIELD:tx_vdmultiplesearch_enable:REQ:true',
    'config'      => array (
        'type'          => 'select',
        'items'         => array(),
        'itemsProcFunc' => 'tx_vdmultiplesearch_tca->getRecords',
        'size'          => 10,
        'minitems'      => 0,
        'maxitems'      => 100
    )
);

// Themes field
$tempColumns[ 'tx_vdmultiplesearch_themes' ] = array (
    'exclude'     => 1,
    'label'       => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:pages.tx_vdmultiplesearch_themes',
    'displayCond' => 'FIELD:tx_vdmultiplesearch_enable:REQ:true',
    'config'      => array (
        'type'          => 'select',
        'items'         => array(),
        'itemsProcFunc' => 'tx_vdmultiplesearch_tca->getRecords',
        'size'          => 10,
        'minitems'      => 0,
        'maxitems'      => 100
    )
);

// Keywords field
$tempColumns[ 'tx_vdmultiplesearch_keywords' ] = array (
    'exclude'     => 1,
    'label'       => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:pages.tx_vdmultiplesearch_keywords',
    'displayCond' => 'FIELD:tx_vdmultiplesearch_enable:REQ:true',
    'config'      => array (
        'type'          => 'select',
        'items'         => array(),
        'itemsProcFunc' => 'tx_vdmultiplesearch_tca->getRecords',
        'size'          => 10,
        'minitems'      => 0,
        'maxitems'      => 100
    )
);

// Field list
$fieldList = 'tx_vdmultiplesearch_enable;;;;1-1-1,tx_vdmultiplesearch_public,tx_vdmultiplesearch_themes,tx_vdmultiplesearch_keywords';

// Load pages TCA
t3lib_div::loadTCA( 'pages' );

// Checks the request update section
if( isset( $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] ) && $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] ) {
    
    // Adds the update action
    $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] .= ',tx_vdmultiplesearch_enable';
    
} else {
    
    // Sets the update action
    $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] = 'tx_vdmultiplesearch_enable';
}

// Add fields to the pages TCA
t3lib_extMgm::addTCAcolumns( 'pages', $tempColumns, 1 );
t3lib_extMgm::addToAllTCAtypes(
    'pages',
    $fieldList,
    '1,2,4,5',
    'after:subtitle'
);

// Cleanup
unset( $tempColumns );
unset( $fieldList );
?>
