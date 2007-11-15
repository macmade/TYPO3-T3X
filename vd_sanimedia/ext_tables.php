<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}

// Includes the TCA helper class
require_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'class.tx_vdsanimedia_tca.php' );

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
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/ts/', 'VD - Sanimedia' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/css/', 'VD - Sanimedia - CSS Styles' );

// Backend options
if ( TYPO3_MODE == 'BE' ) {
    
    // Content wizard
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_vdsanimedia_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi1/class.tx_vdsanimedia_pi1_wizicon.php';
    
    
    // Module flag on pages
    $TCA[ 'pages' ][ 'columns' ][ 'module' ][ 'config' ][ 'items' ][] = array(
        'VD / Sanimedia',
        'sanimedia'
    );
    
    // Type icon
    $ICON_TYPES[ 'sanimedia' ] = array(
        'icon' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/sysfolder.gif'
    );
}

// Public table
$TCA[ 'tx_vdsanimedia_public' ] = array(

    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tx_vdsanimedia_public',
        
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
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_vdsanimedia_public.gif'
    ),
    
    // Frontend options
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);

// Themes table
$TCA[ 'tx_vdsanimedia_themes' ] = array(

    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tx_vdsanimedia_themes',
        
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
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_vdsanimedia_themes.gif'
    ),
    
    // Frontend options
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);

// Keywords table
$TCA[ 'tx_vdsanimedia_keywords' ] = array(

    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tx_vdsanimedia_keywords',
        
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
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_vdsanimedia_keywords.gif'
    ),
    
    // Frontend options
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);

// Storage pages for the sanimedia records
$sanimediaPidList = tx_vdsanimedia_tca::getStoragePid();

// Temp TCA
$tempColumns = array (
    'tx_vdsanimedia_enable' => array (
        'exclude' => 1,
        'label'   => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:pages.tx_vdsanimedia_enable',
        'config'  => array (
            'type' => 'check'
        )
    )
);

// Checks if a storage page has been found
if( $sanimediaPidList ) {

    // Public field
    $tempColumns[ 'tx_vdsanimedia_public' ] = array (
        'exclude'     => 1,
        'label'       => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:pages.tx_vdsanimedia_public',
        'displayCond' => 'FIELD:tx_vdsanimedia_enable:REQ:true',
        'config'      => array (
            'type'                => 'select',
            'foreign_table'       => 'tx_vdsanimedia_public',
            'foreign_table_where' => 'AND tx_vdsanimedia_public.pid IN (' . $sanimediaPidList . ') ORDER BY title',
            'size'                => 10,
            'minitems'            => 0,
            'maxitems'            => 100
        )
    );
    
    // Themes field
    $tempColumns[ 'tx_vdsanimedia_themes' ] = array (
        'exclude'     => 1,
        'label'       => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:pages.tx_vdsanimedia_themes',
        'displayCond' => 'FIELD:tx_vdsanimedia_enable:REQ:true',
        'config'      => array (
            'type'                => 'select',
            'foreign_table'       => 'tx_vdsanimedia_themes',
            'foreign_table_where' => 'AND tx_vdsanimedia_themes.pid IN (' . $sanimediaPidList . ') ORDER BY title',
            'size'                => 10,
            'minitems'            => 0,
            'maxitems'            => 100
        )
    );
    
    // Keywords field
    $tempColumns[ 'tx_vdsanimedia_keywords' ] = array (
        'exclude'     => 1,
        'label'       => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:pages.tx_vdsanimedia_keywords',
        'displayCond' => 'FIELD:tx_vdsanimedia_enable:REQ:true',
        'config'      => array (
            'type'                => 'select',
            'foreign_table'       => 'tx_vdsanimedia_keywords',
            'foreign_table_where' => 'AND tx_vdsanimedia_keywords.pid IN (' . $sanimediaPidList . ') ORDER BY keyword',
            'size'                => 10,
            'minitems'            => 0,
            'maxitems'            => 100
        )
    );
    
    // Field list
    $fieldList = 'tx_vdsanimedia_enable;;;;1-1-1,tx_vdsanimedia_public,tx_vdsanimedia_themes,tx_vdsanimedia_keywords';
    
} else {
    
    // Field list
    $fieldList = 'tx_vdsanimedia_enable;;;;1-1-1';
}

// Load pages TCA
t3lib_div::loadTCA( 'pages' );

// Checks the request update section
if( isset( $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] ) && $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] ) {
    
    // Adds the update action
    $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] .= ',tx_vdsanimedia_enable';
    
} else {
    
    // Sets the update action
    $TCA[ 'pages' ][ 'ctrl' ][ 'requestUpdate' ] = 'tx_vdsanimedia_enable';
}

// Add fields to the pages TCA
t3lib_extMgm::addTCAcolumns( 'pages', $tempColumns, 1 );
t3lib_extMgm::addToAllTCAtypes(
    'pages',
    $fieldList,
    '1,2,5',
    'after:subtitle'
);

// Cleanup
unset( $tempColumns );
unset( $sanimediaPidList );
unset( $fieldList );
?>
