<?php

# $Id: tx_cpguide_domain_model_article.php 7 2010-10-18 13:46:27Z jean $

Tx_NetExtbase_Utility_Extension::typo3ModeCheck();

$TCA[ 'tx_cpguide_domain_model_article' ] = array(
    'ctrl'      => $TCA[ 'tx_cpguide_domain_model_article' ][ 'ctrl' ],
    'interface' => array( 'showRecordFieldList' => 'article_type,title,pagenum,content,change_date,change_description,tags,comments,notes,favorites,views' ),
    'types'     => array
    (
        '0' => array( 'showitem' => 'article_type,title,pagenum,content;;;richtext,change_date,change_description,views,--div--;LLL:EXT:cp_guide/Resources/Private/Language/tx_cpguide_domain_model_article.xml:tx_cpguide_domain_model_article.tab.relations,tags,comments,notes,favorites' ),
        '1' => array( 'showitem' => 'article_type,title' ),
        '2' => array( 'showitem' => 'article_type,content' )
    ),
    'palettes'  => array( '1' => array( 'showitem' => '' ) )
);

Tx_NetExtbase_Utility_Tca::enableLocalisation( 'cp_guide', 'article' );
Tx_NetExtbase_Utility_Tca::enableVersioning( 'cp_guide', 'article' );
Tx_NetExtbase_Utility_Tca::addHiddenField( 'cp_guide', 'article' );
Tx_NetExtbase_Utility_Tca::addSingleSelect( 'cp_guide', 'article', 'article_type', 3, true );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'article', 'title', 'trim,required' );
Tx_NetExtbase_Utility_Tca::addTextInput( 'cp_guide', 'article', 'pagenum', 'trim' );
Tx_NetExtbase_Utility_Tca::addText( 'cp_guide', 'article', 'content' );
Tx_NetExtbase_Utility_Tca::addDate( 'cp_guide', 'article', 'change_date' );
Tx_NetExtbase_Utility_Tca::addText( 'cp_guide', 'article', 'change_description' );
Tx_NetExtbase_Utility_Tca::addInlineRelation( 'cp_guide', 'article', 'tags', 'tx_cpguide_domain_model_tag', 'article' );
Tx_NetExtbase_Utility_Tca::addInlineRelation( 'cp_guide', 'article', 'comments', 'tx_cpguide_domain_model_comment', 'article' );
Tx_NetExtbase_Utility_Tca::addInlineRelation( 'cp_guide', 'article', 'notes', 'tx_cpguide_domain_model_note', 'article' );
Tx_NetExtbase_Utility_Tca::addInlineRelation( 'cp_guide', 'article', 'favorites', 'tx_cpguide_domain_model_favorite', 'article' );
Tx_NetExtbase_Utility_Tca::addNone( 'cp_guide', 'article', 'views' );

?>
