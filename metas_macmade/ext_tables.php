<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Temporary TCA
$tempColumns = array (
    'tx_metasmacmade_metas' => array(
        'exclude' => 1,
        'label'   => 'LLL:EXT:metas_macmade/locallang_db.php:pages.tx_metasmacmade_metas',
        'config'  => array (
            'type' => 'flex',
            'ds'   => array(
                'default' => 'FILE:EXT:metas_macmade/flexform_ds.xml'
            )
        )
    )
);

// Loads the pages TCA
t3lib_div::loadTCA( 'pages' );

// Adds the metas field to the pages TCA
t3lib_extMgm::addTCAcolumns( 'pages', $tempColumns, 1 );

// Layout for the metas field
t3lib_extMgm::addToAllTCAtypes(
    'pages',
    'tx_metasmacmade_metas',
    '1,2,5',
    'after:subtitle'
);

// Cleans up the global variables that are not needed anymore
unset( $tempColumns );
?>
