<?php

# $Id: ext_localconf.php 104 2009-12-01 13:55:17Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Registers the classes directory
tx_oop_Typo3_ClassManager::getInstance()->registerClassDir(
    'tx_marvindata_',
    t3lib_extMgm::extPath( $_EXTKEY ) . 'classes' . DIRECTORY_SEPARATOR,
    true
);

// Adds the save & new buttons
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'countries' );
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'cities' );
tx_oop_Typo3_Utils::addSaveAndNewButton( $_EXTKEY, 'languages' );

?>
