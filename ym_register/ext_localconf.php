<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Adds the frontend plugins
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 1, false );
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 2, false );
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 3, false );
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 4, false );

// Registers the classes directory
tx_oop_Typo3_ClassManager::getInstance()->getInstance()->registerClassDir(
    'tx_ymregister_',
    t3lib_extMgm::extPath( 'ym_register' ) . 'classes' . DIRECTORY_SEPARATOR
);

// Registers the EID script
$TYPO3_CONF_VARS[ 'FE' ][ 'eID_include' ][ $_EXTKEY . '_getCities' ] = 'EXT:' . $_EXTKEY . '/scripts/getCities.inc.php';

?>
