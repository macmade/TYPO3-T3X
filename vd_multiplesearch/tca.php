<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Public TCA
$TCA[ 'tx_vdmultiplesearch_public' ] = array(
    
    // Control section
    'ctrl'      => $TCA[ 'tx_vdmultiplesearch_public' ][ 'ctrl' ],
    
    // Backend interface
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'title'
    ),
    
    // Frontend interface
    'feInterface' => $TCA[ 'tx_vdmultiplesearch_public' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'title' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_multiplesearch/locallang_db.xml:tx_vdmultiplesearch_public.title',
            'config'  => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'required'
            )
        ),
    ),
    
    // Types configurations
    'types' => array(
        '0' => array( 'showitem' => 'title;;;;1-1-1' )
    ),
    
    // Palettes configurations
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Themes TCA
$TCA[ 'tx_vdmultiplesearch_themes' ] = array(
    
    // Control section
    'ctrl'      => $TCA[ 'tx_vdmultiplesearch_themes' ][ 'ctrl' ],
    
    // Backend interface
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'title'
    ),
    
    // Frontend interface
    'feInterface' => $TCA[ 'tx_vdmultiplesearch_themes' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'title' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_multiplesearch/locallang_db.xml:tx_vdmultiplesearch_themes.title',
            'config'  => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'required'
            )
        ),
    ),
    
    // Types configurations
    'types' => array(
        '0' => array( 'showitem' => 'title;;;;1-1-1' )
    ),
    
    // Palettes configurations
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Keywords TCA
$TCA[ 'tx_vdmultiplesearch_keywords' ] = array(
    
    // Control section
    'ctrl'      => $TCA[ 'tx_vdmultiplesearch_keywords' ][ 'ctrl' ],
    
    // Backend interface
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'keyword'
    ),
    
    // Frontend interface
    'feInterface' => $TCA[ 'tx_vdmultiplesearch_keywords' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'keyword' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_multiplesearch/locallang_db.xml:tx_vdmultiplesearch_keywords.keyword',
            'config'  => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'required'
            )
        ),
    ),
    
    // Types configurations
    'types' => array(
        '0' => array( 'showitem' => 'keyword;;;;1-1-1' )
    ),
    
    // Palettes configurations
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);
?>
