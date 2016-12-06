<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Registers the classes directory
tx_oop_Typo3_ClassManager::getInstance()->getInstance()->registerClassDir(
    'tx_facebookconnect_',
    t3lib_extMgm::extPath( 'facebook_connect' ) . 'classes' . DIRECTORY_SEPARATOR
);

// Adds the frontend plugin
tx_oop_Typo3_Utils::addFrontendPlugin( $_EXTKEY, 1, false );

?>
