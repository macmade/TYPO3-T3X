<?php

# $Id: tx_alcoopedia_domain_model_article.php 24 2010-06-21 07:53:48Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_alcoopedia_domain_model_article' ] = array(
    'ctrl'      => $TCA[ 'tx_alcoopedia_domain_model_article' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'title,content,views' ),
    'types'     => array
    (
        '0' => array( 'showitem' => 'title,content;;;richtext,views' ),
    ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'alcoopedia', 'article' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'alcoopedia', 'article' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'alcoopedia', 'article' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'alcoopedia', 'article', 'title', 'trim,required' );
Tx_NetExtbase_Utility_Tca::addText( 'alcoopedia', 'article', 'content' );
Tx_NetExtbase_Utility_Tca::addNone( 'alcoopedia', 'article', 'views' );

?>
