<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}

// Add FE plugin
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_vdmultiplesearch_pi1.php',
    '_pi1',
    'list_type',
    1
);

// Save & new options
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_vdmultiplesearch_public=1
	options.saveDocNew.tx_vdmultiplesearch_themes=1
	options.saveDocNew.tx_vdmultiplesearch_keywords=1
');
?>
