<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// TCA - Users profiles
$TCA[ 'tx_adlercontest_users' ] = array(
        
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:adler_contest/lang/tx_adlercontest_users.xml:tx_adlercontest_users',
        
        // Label field
        'label'             => 'lastname',
        
        // Alternative label field
        'label_alt'         => 'firstname',
        
        // Label field
        'label_alt_force'   => true,
        
        // Modification date
        'tstamp'            => 'tstamp',
        
        // Creation date
        'crdate'            => 'crdate',
        
        // Creation user
        'cruser_id'         => 'cruser_id',
        
        // Sorty by
        'default_sortby'    => 'ORDER BY lastname,firstname',
        
        // Use tabs
        "dividers2tabs"     => 1,
        
        // Delete flag
        'delete'            => 'deleted',
        
        // External configuration file
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_adlercontest_users.php',
        
        // Icon
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/tx_adlercontest_users.gif',
        
        // Special fields
        'enablecolumns'   => array(
            
            // Hide flag
            'disabled' => 'hidden'
        )
    ),
    
    // FE settings
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);

// TCA - Votes
$TCA[ 'tx_adlercontest_votes' ] = array(
        
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:adler_contest/lang/tx_adlercontest_votes.xml:tx_adlercontest_votes',
        
        // Label field
        'label'             => 'note',
        
        // Modification date
        'tstamp'            => 'tstamp',
        
        // Creation date
        'crdate'            => 'crdate',
        
        // Creation user
        'cruser_id'         => 'cruser_id',
        
        // Sorty by
        'default_sortby'    => 'ORDER BY note,uid',
        
        // Use tabs
        "dividers2tabs"     => 1,
        
        // Delete flag
        'delete'            => 'deleted',
        
        // External configuration file
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca/tx_adlercontest_votes.php',
        
        // Icon
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/tx_adlercontest_votes.gif'
    ),
    
    // FE settings
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);

// Load content TCA
t3lib_div::loadTCA( 'tt_content' );

// Plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi1' ] = 'layout,select_key,pages,recursive';
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi2' ] = 'layout,select_key,pages,recursive';
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi3' ] = 'layout,select_key,pages,recursive';

// Add flexform field to plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi1' ]     = 'pi_flexform';
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi2' ]     = 'pi_flexform';
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi3' ]     = 'pi_flexform';

// Add flexform DataStructure
t3lib_extMgm::addPiFlexFormValue(
    $_EXTKEY . '_pi1',
    'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml'
);
t3lib_extMgm::addPiFlexFormValue(
    $_EXTKEY . '_pi2',
    'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi2.xml'
);
t3lib_extMgm::addPiFlexFormValue(
    $_EXTKEY . '_pi3',
    'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi3.xml'
);

// Add plugins
t3lib_extMgm::addPlugin(
    array(
        'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:tt_content.list_type_pi1',
        $_EXTKEY . '_pi1'
    ),
    'list_type'
);
t3lib_extMgm::addPlugin(
    array(
        'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:tt_content.list_type_pi2',
        $_EXTKEY . '_pi2'
    ),
    'list_type'
);
t3lib_extMgm::addPlugin(
    array(
        'LLL:EXT:' . $_EXTKEY . '/locallang_db.php:tt_content.list_type_pi3',
        $_EXTKEY . '_pi3'
    ),
    'list_type'
);

// Static templates
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/ts/', 'Adler / Contest' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/css/', 'Adler / Contest - CSS Styles' );

// Wizard icons
if( TYPO3_MODE == 'BE' ) {
    
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_adlercontest_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi1/class.tx_adlercontest_pi1_wizicon.php';
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_adlercontest_pi2_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi2/class.tx_adlercontest_pi2_wizicon.php';
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_adlercontest_pi3_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi3/class.tx_adlercontest_pi3_wizicon.php';
}
?>
