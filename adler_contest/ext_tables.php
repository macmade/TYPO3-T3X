<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Prefix for the frontend plugins
$tempPiPrefix = str_replace( '_', '', $_EXTKEY );

// Frontend plugins
$tempFePlugins = array(
    'pi1' => array(
        'flex'      => true,
        'wiz'       => true,
        'cached'    => true,
        'staticTs'  => true,
        'staticCss' => true
    ),
    'pi2' => array(
        'flex'      => true,
        'wiz'       => true,
        'staticTs'  => true,
        'staticCss' => true
    ),
    'pi3' => array(
        'flex'      => true,
        'wiz'       => true,
        'staticTs'  => true,
        'staticCss' => true
    )
);

// Tables used in this extension
$tempExtTables = array(
    
    // Users profiles
    'tx_adlercontest_users' => array(
        
        // Localization enabled
        'l10n'           => false,
        
        // Versionning enabled
        'versionning'    => false,
        
        // Deleted field enabled
        'deletedField'   => true,
        
        // Hidden field enabled
        'hiddenField'    => true,
        
        // Start time field enabled
        'startTimeField' => false,
        
        // End time field enabled
        'endTimeField'   => false,
        
        // Default ORDER BY instruction
        'orderBy'        => 'lastname,firstname',
        
        // Label
        'label'          => 'lastname'
    ),
    
    // Votes
    'tx_adlercontest_votes' => array(
        
        // Localization enabled
        'l10n'           => false,
        
        // Versionning enabled
        'versionning'    => false,
        
        // Deleted field enabled
        'deletedField'   => true,
        
        // Hidden field enabled
        'hiddenField'    => false,
        
        // Start time field enabled
        'startTimeField' => false,
        
        // End time field enabled
        'endTimeField'   => false,
        
        // Default ORDER BY instruction
        'orderBy'        => 'note,uid',
        
        // Label
        'label'          => 'note'
    )
);

// Load content TCA
t3lib_div::loadTCA( 'tt_content' );

// Process each FE plugin
foreach( $tempFePlugins as $tempPiNum => $tempPiOptions ) {
    
    // Plugin options
    $TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . $tempPiNum ] = 'layout,select_key,pages,recursive';
    
    // Checks if the plugin has a flexform
    if( $tempPiOptions[ 'flex' ] === true ) {
        
        // Adds the flexform field to plugin options
        $TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . $tempPiNum ] = 'pi_flexform';
        
        // Add flexform DataStructure
        t3lib_extMgm::addPiFlexFormValue(
            $_EXTKEY . $tempPiNum,
            'FILE:EXT:' . $_EXTKEY . '/flex/' . $tempPiNum . '.xml'
        );
    }
    
    // Adds the plugin
    t3lib_extMgm::addPlugin(
        array(
            'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:tt_content.list_type_' . $tempPiNum,
            $_EXTKEY . $tempPiNum
        ),
        'list_type'
    );
    
    // Checks if the plugin has a static TS setup
    if( $tempPiOptions[ 'staticTs' ] === true ) {
        
        // TS setup for the frontend plugins
        t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/ts/' . $tempPiNum . '/', 'LLL:EXT:' . $_EXTKEY . '/lang/' . $tempTableName . '.xml:typo3-staticTs' );
    }
    
    // Checks if the plugin has static CSS styles
    if( $tempPiOptions[ 'staticCss' ] === true ) {
        
        // CSS styles for the frontend plugins
        t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/css/' . $tempPiNum . '/', 'LLL:EXT:' . $_EXTKEY . '/lang/' . $tempTableName . '.xml:typo3-staticCss' );
    }
    
    // Checks if the plugin has a wizard
    if( $tempPiOptions[ 'wiz' ] === true && TYPO3_MODE === 'BE' ) {
        
        $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_' . $tempPiPrefix . '_' . $tempPiNum . '_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . $tempPiNum . '/class.tx_' . $tempPiPrefix . '_' . $tempPiNum . '_wizicon.php';
    }
}

// Process each table
foreach( $tempExtTables as $tempTableName => $tempTableOptions ) {
   
    // Creates the default TCA entry
    $TCA[ $tempTableName ] = array(
            
        // Control section
        'ctrl' => array(
            
            // Table title
            'title'             => 'LLL:EXT:' . $_EXTKEY . '/lang/' . $tempTableName . '.xml:typo3-tableName',
            
            // Label field
            'label'             => $tempTableOptions[ 'label' ],
            
            // Modification date
            'tstamp'            => 'tstamp',
            
            // Creation date
            'crdate'            => 'crdate',
            
            // Creation user
            'cruser_id'         => 'cruser_id',
            
            // Sorty by
            'default_sortby'    => 'ORDER BY ' . $tempTableOptions[ 'orderBy' ],
            
            // Use tabs
            "dividers2tabs"     => 1,
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/' . $tempTableName . '.php',
            
            // Icon
            'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/' . $tempTableName . '.gif',
            
            // Special fields
            'enablecolumns'   => array()
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => ''
        )
    );
}

// TCA specific options - Users
$TCA[ 'tx_adlercontest_users' ][ 'ctrl' ][ 'label_alt' ]       = 'firstname';
$TCA[ 'tx_adlercontest_users' ][ 'ctrl' ][ 'label_alt_force' ] = true;

// Cleanup - Unset unused variables
unset( $tempPiPrefix );
unset( $tempPiNum );
unset( $tempPiOptions );
unset( $tempExtTables );
unset( $tempTableName );
unset( $tempTableOptions );
?>
