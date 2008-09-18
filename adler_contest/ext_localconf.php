<?php

# $Id$

if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// FE plugins
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_adlercontest_pi1.php',
    '_pi1',
    'list_type',
    1
);
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi2/class.tx_adlercontest_pi2.php',
    '_pi2',
    'list_type',
    1
);
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi3/class.tx_adlercontest_pi3.php',
    '_pi3',
    'list_type',
    1
);

?>
