<?php

# $Id: tx_alcoopedia_domain_model_category.php 24 2010-06-21 07:53:48Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_alcoopedia_domain_model_category' ] = array(
    'ctrl'      => $TCA[ 'tx_alcoopedia_domain_model_category' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'title,articles' ),
    'types'     => array
    (
        '0' => array( 'showitem' => 'title,articles' ),
    ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'alcoopedia', 'category' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'alcoopedia', 'category' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'alcoopedia', 'category' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'alcoopedia', 'category', 'title', 'trim,required' );
Tx_NetExtbase_Utility_Tca::addInlineMMRelation( 'alcoopedia', 'category', 'articles', 'tx_alcoopedia_domain_model_article', 'tx_alcoopedia_category_article_mm' );

?>
