<?php

# $Id: tx_cpguide_domain_model_tag.php 2 2010-06-21 07:43:04Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_cpguide_domain_model_tag' ] = array(
    'ctrl'      => $TCA[ 'tx_cpguide_domain_model_tag' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'title,article,author' ),
    'types'     => array( '1' => array( 'showitem' => 'title,article,author' ) ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'cp_guide', 'tag' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'cp_guide', 'tag' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'cp_guide', 'tag' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'tag', 'title' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'tag', 'article', 'tx_cpguide_domain_model_article' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'tag', 'author', 'tx_cpguide_domain_model_user' );

?>
