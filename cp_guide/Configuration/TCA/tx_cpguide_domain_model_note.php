<?php

# $Id: tx_cpguide_domain_model_note.php 2 2010-06-21 07:43:04Z macmade $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_cpguide_domain_model_note' ] = array(
    'ctrl'      => $TCA[ 'tx_cpguide_domain_model_note' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'title,content,article,author' ),
    'types'     => array( '1' => array( 'showitem' => 'title,content,article,author' ) ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'cp_guide', 'note' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'cp_guide', 'note' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'cp_guide', 'note' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'note', 'title' );
Tx_NetExtbase_Utility_Tca::addText( 'cp_guide', 'note', 'content' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'note', 'article', 'tx_cpguide_domain_model_article' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'note', 'author', 'tx_cpguide_domain_model_user' );

?>
