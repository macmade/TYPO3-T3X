<?php

# $Id$

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_cpguide_domain_model_allowedword' ] = array(
    'ctrl'      => $TCA[ 'tx_cpguide_domain_model_allowedword' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'word' ),
    'types'     => array( '1' => array( 'showitem' => 'word' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'cp_guide', 'allowedword' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'cp_guide', 'allowedword' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'cp_guide', 'allowedword' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'allowedword', 'word' );

?>
