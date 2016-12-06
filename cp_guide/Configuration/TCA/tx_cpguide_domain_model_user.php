<?php

# $Id: tx_cpguide_domain_model_user.php 7 2010-10-18 13:46:27Z jean $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_cpguide_domain_model_user' ] = array(
    'ctrl'      => $TCA[ 'tx_cpguide_domain_model_user' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'fe_user,prefix,company,firstname,lastname,email,account_type,address1,address2,city,zip,country,telephone,fax,mobile,tags,comments,notes,favorites,confirm_hash' ),
    'types'     => array( '1' => array( 'showitem' => 'fe_user,prefix,company,firstname,lastname,email,account_type,address1,address2,city,zip,country,telephone,fax,mobile,confirm_hash,--div--;LLL:EXT:cp_guide/Resources/Private/Language/tx_cpguide_domain_model_article.xml:tx_cpguide_domain_model_article.tab.relations,tags,comments,notes,favorites' ) ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'cp_guide', 'user' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'cp_guide', 'user' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'cp_guide', 'user' );
Tx_NetExtbase_Utility_Tca::addRelation( 'cp_guide', 'user', 'fe_user', 'fe_users' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'firstname' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'lastname' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'email' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'company' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'address1' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'address2' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'city' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'zip' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'country' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'telephone' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'fax' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'user', 'mobile' );
Tx_NetExtbase_Utility_Tca::addNone( 'cp_guide', 'user', 'confirm_hash' );
Tx_NetExtbase_Utility_Tca::addInlineRelation( 'cp_guide', 'user', 'tags', 'tx_cpguide_domain_model_tag', 'author' );
Tx_NetExtbase_Utility_Tca::addInlineRelation( 'cp_guide', 'user', 'comments', 'tx_cpguide_domain_model_comment', 'author' );
Tx_NetExtbase_Utility_Tca::addInlineRelation( 'cp_guide', 'user', 'notes', 'tx_cpguide_domain_model_note', 'author' );
Tx_NetExtbase_Utility_Tca::addInlineRelation( 'cp_guide', 'user', 'favorites', 'tx_cpguide_domain_model_favorite', 'author' );
Tx_NetExtbase_Utility_Tca::addSingleSelect( 'cp_guide', 'user', 'prefix', 3 );
Tx_NetExtbase_Utility_Tca::addSingleSelect( 'cp_guide', 'user', 'account_type', 2 );

?>
