<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// FE plugin
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_httpsmacmade_pi1.php',
    '_pi1',
    '',
    0
);
?>
