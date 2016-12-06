<?php

# $Id: ext_localconf.php 24 2010-06-21 07:53:48Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();
Tx_NetExtbase_Utility_Extension::configurePlugin( $_EXTKEY, 'Pi1' );
Tx_NetExtbase_Utility_Extension::configurePluginForRealUrl(
    $_EXTKEY,
    'Pi1',
    array(
        'article'  => 'tx_alcoopedia_domain_model_article.title',
        'category' => 'tx_alcoopedia_domain_model_category.title'
    )
);

?>
