<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}
// Backend options
if ( TYPO3_MODE == 'BE' ) {
    
    // Add BE module
    t3lib_extMgm::addModule(
        'tools',
        'txvdmunicipalitiesM1',
        '',
        t3lib_extMgm::extPath( $_EXTKEY ) . 'mod1/'
    );
}

// Categories table
$TCA[ 'tx_vdmunicipalities_municipalities' ] = array(
    
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'             => 'LLL:EXT:vd_municipalities/locallang_db.xml:tx_vdmunicipalities_municipalities',
        
        // Table label field
        'label'             => 'name_lower',
        
        // Alternative label
        'label_alt'         => 'id_municipality',
        
        // Force display of alternative label
        'label_alt_force'   => 1,
        
        // Modification date
        'tstamp'            => 'tstamp',
        
        // Creation date
        'crdate'            => 'crdate',
        
        // Creation user
        'cruser_id'         => 'cruser_id',
        
        // Sorty by field
        'default_sortby'    => 'ORDER BY name_lower',
        
        // Delete flag
        'delete'            => 'deleted',
        
        // Table is only available for reading
        'readOnly'          => 1,
        
        // Only admin users can view records
		'adminOnly'         => 1,
		
		// Records are stored on site container
		'rootLevel'         => 1,
		
		// Records are 'static'
		'is_static'         => 1,
        
        // External configuration file
        'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'          => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_vdmunicipalities_municipalities.gif'
    ),
    
    // Frontend options
    'feInterface' => array(
        
        // Available fields
        'fe_admin_fieldList' => ''
    )
);
?>
