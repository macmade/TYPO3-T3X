<?php

# $Id: ext_tables.php 24 2010-06-21 07:53:48Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

Tx_Extbase_Utility_Extension::registerPlugin(
    $_EXTKEY,
    'Pi1',
    'CHUV / Alcoopedia'
);

t3lib_extMgm::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'CHUV / Alcoopedia'
);

Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'category', 'title', true );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'article', 'title', true );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'word', 'word', true );

?>
