<?php

# $Id: ext_tables.php 194 2010-01-27 08:55:55Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Configures the frontend plugins
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 1, true );
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 2, false );
tx_oop_Typo3_Utils::configureFrontendPlugin( $_EXTKEY, 3, false );

// Adds the frontend plugin wizard icons
tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 1 );
tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 2 );
tx_oop_Typo3_Utils::addPluginWizardIcon( $_EXTKEY, 3 );

// Adds the static TS templates
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi1/', 'Marvin - Catalog' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/pi3/', 'Marvin - Catalog / iPhone' );

?>
