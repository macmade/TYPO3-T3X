<?php

if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Get extension configuration
$extConf = unserialize( $_EXTCONF );

// Check if skin should be used by default
if( isset( $extConf[ 'useAsDefaultSkin' ] ) ) {
    
    // Add skin
    t3lib_extMgm::addPageTSConfig( '
        RTE.default.skin = EXT:'    . $_EXTKEY . '/ooo/htmlarea.css
        RTE.default.FE.skin = EXT:' . $_EXTKEY . '/ooo/htmlarea.css
    ' );
}

?>
