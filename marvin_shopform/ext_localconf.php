<?php

# $Id: ext_localconf.php 198 2010-02-11 10:30:06Z root $

// Security check
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 is not running
    trigger_error(
        'This script cannot be used outside TYPO3',
        E_USER_ERROR
    );
}

// Adds the frontend plugin
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_marvinshopform_pi1.php',
    '_pi1',
    'list_type',
    false
);

?>
