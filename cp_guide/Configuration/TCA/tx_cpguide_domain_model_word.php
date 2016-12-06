<?php

# $Id: tx_cpguide_domain_model_word.php 7 2010-10-18 13:46:27Z jean $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_cpguide_domain_model_word' ] = array(
    'ctrl'      => $TCA[ 'tx_cpguide_domain_model_word' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'word,articles' ),
    'types'     => array( '1' => array( 'showitem' => 'word,articles' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'cp_guide', 'word' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'cp_guide', 'word' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'cp_guide', 'word' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'word', 'word' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'word', 'articles', 'tx_cpguide_domain_model_article', 999999 )

?>
