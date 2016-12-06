<?php

# $Id: tx_cpguide_domain_model_favorite.php 2 2010-06-21 07:43:04Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_cpguide_domain_model_favorite' ] = array(
    'ctrl'      => $TCA[ 'tx_cpguide_domain_model_favorite' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'article,author' ),
    'types'     => array( '1' => array( 'showitem' => 'article,author' ) ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'cp_guide', 'favorite' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'cp_guide', 'favorite' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'cp_guide', 'favorite' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'favorite', 'article', 'tx_cpguide_domain_model_article' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'favorite', 'author', 'tx_cpguide_domain_model_user' );

?>
