<?php

# $Id: tx_cpguide_domain_model_comment.php 2 2010-06-21 07:43:04Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_cpguide_domain_model_comment' ] = array(
    'ctrl'      => $TCA[ 'tx_cpguide_domain_model_comment' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'content,article,author' ),
    'types'     => array( '1' => array( 'showitem' => 'content,article,author' ) ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'cp_guide', 'comment' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'cp_guide', 'comment' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'cp_guide', 'comment' );
Tx_NetExtbase_Utility_Tca::addText( 'cp_guide', 'comment', 'content' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'comment', 'article', 'tx_cpguide_domain_model_article' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'comment', 'author', 'tx_cpguide_domain_model_user' );

?>
