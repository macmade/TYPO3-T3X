<?php

# $Id: ext_tables.php 7 2010-10-18 13:46:27Z jean $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

Tx_Extbase_Utility_Extension::registerPlugin(
    $_EXTKEY,
    'Pi1',
    'CP / Guide'
);

t3lib_extMgm::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'CP / Guide'
);

Tx_NetExtbase_Utility_Extension::addFlexForm( $_EXTKEY, 1 );

Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'article', 'title', true );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'comment', 'content', true );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'note', 'title', true );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'tag', 'title', true );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'favorite', array( 'article', 'author' ), true );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'user', array( 'lastname', 'firstname' ), true );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'word', 'word', false );
Tx_NetExtbase_Utility_Extension::addTable( $_EXTKEY, 'allowedword', 'word', false );

Tx_NetExtbase_Utility_Tca::setTypeField( $_EXTKEY, 'article', 'article_type' );

if( TYPO3_MODE === 'BE' )
{
    t3lib_extMgm::addModule
    (
        'web',
        'txcpguideM1',
        '',
        t3lib_extMgm::extPath( $_EXTKEY ) . 'mod1/'
    );
}

?>
