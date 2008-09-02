<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Checks the TYPO3 mode
if( TYPO3_MODE === 'BE' ) {
    
    // Adds the backend module
    t3lib_extMgm::addModule(
        'web',
        'txadlercontestM1',
        '',
        t3lib_extMgm::extPath( $_EXTKEY ) . 'mod1/'
    );
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
    ),
    
    // Votes
    'tx_adlercontest_emails' => array(
        
        // Deleted field enabled
        'deletedField'   => true,
        
        // Hidden field enabled
        'hiddenField'    => false,
        
        // Start time field enabled
        'startTimeField' => false,
        
        // End time field enabled
        'endTimeField'   => false,
        
        // Default ORDER BY instruction
        'orderBy'        => 'subject',
        
        // Label
        'label'          => 'subject'
    )
);

// Load content TCA
t3lib_div::loadTCA( 'tt_content' );

// Process each FE plugin
foreach( $tempFePlugins as $tempPiNum => $tempPiOptions ) {
    
    // Plugin options
    $TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_' . $tempPiNum ] = 'layout,select_key,pages,recursive';
    
    // Checks if the plugin has a flexform
    if( $tempPiOptions[ 'flex' ] === true ) {
        
        // Adds the flexform field to plugin options
        $TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_' . $tempPiNum ] = 'pi_flexform';
        
        // Add flexform DataStructure
        t3lib_extMgm::addPiFlexFormValue(
            $_EXTKEY . '_' . $tempPiNum,
            'FILE:EXT:' . $_EXTKEY . '/flex/' . $tempPiNum . '.xml'
        );
    }
    
    // Adds the plugin
    t3lib_extMgm::addPlugin(
        array(
            'LLL:EXT:' . $_EXTKEY . '/lang/wiz_'. $tempPiNum . '.xml:pi_title',
            $_EXTKEY . '_' . $tempPiNum
        ),
        'list_type'
    );
    
    // Checks if the plugin has a static TS setup
    if( $tempPiOptions[ 'staticTs' ] === true ) {
        
        // TS setup for the frontend plugins
        t3lib_extMgm::addStaticFile(
            $_EXTKEY,
            'static/ts/' . $tempPiNum . '/',
            'TS setup - ' . strtoupper( $tempPiNum )
        );
    }
    
    // Checks if the plugin has static CSS styles
    if( $tempPiOptions[ 'staticCss' ] === true ) {
        
        // CSS styles for the frontend plugins
        t3lib_extMgm::addStaticFile(
            $_EXTKEY,
            'static/css/' . $tempPiNum . '/',
            'CSS styles - ' . strtoupper( $tempPiNum ) 
        );
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
            'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/img/' . $tempTableName . '.png',
            
            // Special fields
            'enablecolumns'   => array()
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => ''
        )
    );
    
    // Checks if the delete field is available
    if( $tempTableOptions[ 'deletedField' ] ) {
        
        // Delete field available
        $TCA[ $tempTableName ][ 'ctrl' ][ 'delete' ] = 'deleted';
    }
    
    // Checks if the hidden field is available
    if( $tempTableOptions[ 'hiddenField' ] ) {
        
        // Hidden field available
        $TCA[ $tempTableName ][ 'ctrl' ][ 'enablecolumns' ][ 'disabled' ] = 'hidden';
    }
    
    // Checks if the start time field is available
    if( $tempTableOptions[ 'startTimeField' ] ) {
        
        // Start time field available
        $TCA[ $tempTableName ][ 'ctrl' ][ 'enablecolumns' ][ 'starttime' ]  = 'starttime';
    }
    
    // Checks if the end time field is available
    if( $tempTableOptions[ 'endTimeField' ] ) {
        
        // End time field available
        $TCA[ $tempTableName ][ 'ctrl' ][ 'enablecolumns' ][ 'endttime' ]  = 'endttime';
    }
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
