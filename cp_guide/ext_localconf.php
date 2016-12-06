<?php

# $Id: ext_localconf.php 2 2010-06-21 07:43:04Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();
Tx_NetExtbase_Utility_Extension::configurePlugin( $_EXTKEY, 'Pi1' );
Tx_NetExtbase_Utility_Extension::configurePluginForRealUrl(
    $_EXTKEY,
    'Pi1',
    array(
        'article'     => 'tx_cpguide_domain_model_article.title',
        'category'    => 'pages.title',
        'subCategory' => 'pages.title'
    )
);

Tx_NetExtbase_Utility_Extension::addSchedulerTask( $_EXTKEY, 'Indexing', true );
Tx_NetExtbase_Utility_Extension::addEidScript( $_EXTKEY, 'AutoComplete' );

?>
