<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Categories TCA
$TCA[ 'tx_vdmunicipalitiessearch_institutions' ] = array(
    
    // Control section
    'ctrl'      => $TCA[ 'tx_vdmunicipalitiessearch_institutions' ][ 'ctrl' ],
    
    // Backend interface
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'name'
    ),
    
    // Frontend interface
    'feInterface' => $TCA[ 'tx_vdmunicipalitiessearch_institutions' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'name' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities_search/locallang_db.xml:tx_vdmunicipalitiessearch_institutions.name',
            'config'  => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'required'
            )
        ),
    ),
    
    // Types configurations
    'types' => array(
        '0' => array( 'showitem' => 'name;;;;1-1-1' )
    ),
    
    // Palettes configurations
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);
?>
