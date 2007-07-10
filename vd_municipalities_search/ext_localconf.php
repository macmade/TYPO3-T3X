<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}

// Add FE plugin
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_vdmunicipalitiessearch_pi1.php',
    '_pi1',
    'list_type',
    0
);

// Save & new options
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vdmunicipalitiessearch_institutions=1
');
?>
