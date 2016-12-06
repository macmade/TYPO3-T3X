<?php

# $Id: tx_alcoopedia_domain_model_word.php 24 2010-06-21 07:53:48Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_alcoopedia_domain_model_word' ] = array(
    'ctrl'      => $TCA[ 'tx_alcoopedia_domain_model_word' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'word,count' ),
    'types'     => array
    (
        '0' => array( 'showitem' => 'word,count' ),
    ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'alcoopedia', 'category' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'alcoopedia', 'category' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'alcoopedia', 'category' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'alcoopedia', 'word', 'word', 'trim,required' );
Tx_NetExtbase_Utility_Tca::addNone( 'alcoopedia', 'word', 'count' );

?>
