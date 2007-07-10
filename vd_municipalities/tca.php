<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Categories TCA
$TCA[ 'tx_vdmunicipalities_municipalities' ] = array(
    
    // Control section
    'ctrl'      => $TCA[ 'tx_vdmunicipalities_municipalities' ][ 'ctrl' ],
    
    // Backend interface
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'name_lower,name_lower15,name_upper,name_upper15,id_state,id_district,id_municipality,surface,objectid,idex2000'
    ),
    
    // Frontend interface
    'feInterface' => $TCA[ 'tx_vdmunicipalities_municipalities' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'name_lower' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.name_lower',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'name_lower15' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.name_lower15',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'name_upper' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.name_upper',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'name_upper15' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.name_upper15',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'id_state' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.id_state',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'id_district' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.id_district',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'id_municipality' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.id_municipality',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'surface' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.surface',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'objectid' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.objectid',
            'config'  => array(
                'type' => 'none',
            )
        ),
        'idex2000' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities.idex2000',
            'config'  => array(
                'type' => 'none',
            )
        )
    ),
    
    // Types configurations
    'types' => array(
        '0' => array( 'showitem' => 'name_lower;;;;1-1-1,name_lower15,name_upper,name_upper15,id_state,id_district,id_municipality,surface,objectid,idex2000' )
    ),
    
    // Palettes configurations
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);
?>
