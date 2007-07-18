<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}

// Static templates
t3lib_extMgm::addStaticFile(
    $_EXTKEY,
    'static/',
    'XML Menu / macmade.net'
);
?>
