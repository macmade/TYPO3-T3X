<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Includes the metas generator class
require_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'class.tx_metasmacmade_generator.php' );

// Use a hook in the page generation script to place the meta tags
$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'pagegen_macmade' ][ 'afterTitleHook' ][] = 'tx_metasmacmade_generator';

// Adds the meta tags in the rootline fields
$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'FE' ][ 'addRootLineFields' ] .= ',tx_metasmacmade_metas';
?>
