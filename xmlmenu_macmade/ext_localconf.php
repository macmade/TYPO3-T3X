<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Get extension configuration
$xmlmenu_macmade_extconf = unserialize( $_EXTCONF );

// Add constants
t3lib_extMgm::addTypoScriptConstants(
    'extension.xmlmenu_macmade.typeNum = '
  . $xmlmenu_macmade_extconf[ 'typeNum' ]
);
?>
