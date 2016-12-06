<?php

# $Id: ext_tables.php 198 2010-02-11 10:30:06Z root $

// Security check
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 is not running
    trigger_error(
        'This script cannot be used outside TYPO3',
        E_USER_ERROR
    );
}

// Loads the 'tt_content' TCA
t3lib_div::loadTCA( 'tt_content' );

// Plugin options
$GLOBALS[ 'TCA' ][ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi1' ] = 'layout,select_key,pages,recursive';

// Add flexform field to plugin options
$GLOBALS[ 'TCA' ][ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi1' ] = 'pi_flexform';

// Adds the flexform data structure
t3lib_extMgm::addPiFlexFormValue(
    $_EXTKEY . '_pi1',
    'FILE:EXT:' . $_EXTKEY . '/flex/pi1.xml'
);

// Adds the frontend plugin
t3lib_extMgm::addPlugin(
    array(
        'LLL:EXT:' . $_EXTKEY . '/lang/tt_content.xml:list_type.pi1',
        $_EXTKEY . '_pi1'
    ),
    'list_type'
);

?>
