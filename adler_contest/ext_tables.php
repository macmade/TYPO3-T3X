<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}
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
