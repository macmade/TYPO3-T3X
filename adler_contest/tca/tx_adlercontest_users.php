<?php

# $Id$

if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Table name
$tempTableName   = str_replace( '.php', '', basename( __FILE__ ) );

// Gets the current path
$tempExtPathInfo = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );

// Removes the TCA directory
array_pop( $tempExtPathInfo );

// Gets the extension key
$tempExtKey      = array_pop( $tempExtPathInfo );

// LLL path to the language file for the current table
$tempLangPath    = 'LLL:EXT:' . $tempExtKey . '/lang/' . $tempTableName . '.xml:';

// LLL path to the general language file
$tempLglPath     = 'LLL:EXT:' . $tempExtKey . '/lang/lgl.xml:';

// Field list
$tempFieldList   = array();

// TCA
$TCA[ $tempTableName ] = array(
    
    // Control section from the ext_tables.php file
    'ctrl'        => $TCA[ $tempTableName ][ 'ctrl' ],
    
    // FE settings from the ext_tables.php file
    'feInterface' => $TCA[ $tempTableName ][ 'feInterface' ],
    
    // BE settings
    'interface'   => array(
        
        // Available fields
        'showRecordFieldList' => ''
    ),
    
    // Types configuration
    'types'       => array(
        '0' => array(
            'showitem' => '--div--;' . $tempLangPath . 'tab-profile,'
                       .  'lastname;;;;1-1-1,'
                       .  'firstname,'
                       .  'gender;;;;1-1-1,'
                       .  'address;;;;1-1-1,'
                       .  'address2,'
                       .  'city,'
                       .  'zip,'
                       .  'country,'
                       .  'phone;;;;1-1-1,'
                       .  'nationality;;;;1-1-1,'
                       .  'birthdate;;;;1-1-1,'
                       .  'school_name;;;;1-1-1,'
                       .  'school_address,'
                       .  'school_country,'
                       .  '--div--;' . $tempLangPath . 'tab-relations,'
                       .  'id_fe_users;;;;1-1-1,'
                       .  'votes;;;;1-1-1,'
                       .  '--div--;' . $tempLangPath . 'tab-documents,'
                       .  'age_proof;;;;1-1-1,'
                       .  'school_proof,'
                       .  'project;;;;1-1-1,'
                       .  '--div--;' . $tempLangPath . 'tab-misc,'
                       .  'hidden;;;;1-1-1,'
                       .  'sworn;;;;1-1-1,'
                       .  'validated;;;;1-1-1,'
                       .  'confirm_token;;;;1-1-1'
        )
    ),
    
    // Palettes configuration
    'palettes'    => array(
        '1' => array( 'showitem' => '' )
    ),
    
    // Fields configuration
    'columns' => array(
        
        'hidden' => array(
            'config'  => array(
                'type' => 'check',
            )
        ),
        
        'id_fe_users' => array(
            'config' => array(
                'type'          => 'group',
                'internal_type' => 'db',
                'allowed'       => 'fe_users',
                'minitems'      => 1,
                'maxitems'      => 1,
                'size'          => 1,
                'show_thumbs'   => true,
                'wizards'       => array(
                    '_PADDING'  => 2,
                    '_VERTICAL' => 1,
                    'add'       => array(
                        'type'   => 'script',
                        'title'  => $tempLglPath . 'wizard-add',
                        'icon'   => 'add.gif',
                        'script' => 'wizard_add.php',
                        'params' => array(
                            'table'    => 'pages',
                            'pid'      => '###CURRENT_PID###',
                            'setValue' => 'prepend'
                        )
                    ),
                    'edit'      => array(
                        'type'                     => 'popup',
                        'title'                    => $tempLglPath . 'wizard-add',
                        'script'                   => 'wizard_edit.php',
                        'popup_onlyOpenIfSelected' => 1,
                        'icon'                     => 'edit2.gif',
                        'JSopenParams'             => 'height=500,width=600,status=0,menubar=0,scrollbars=1'
                    )
                )
            )
        ),
        
        'votes' => array(
            'config' => array(
                'type'          => 'inline',
                'foreign_table' => 'tx_adlercontest_votes',
                'foreign_field' => 'id_tx_adlercontest_users',
                'appearance'    => array(
                    'collapseAll'           => true,
                    'expandSingle'          => true,
                    'newRecordLinkAddTitle' => true,
                    'newRecordLinkPosition' => 'top',
                    'useCombination'        => false,
                    'useSortable'           => false
                ),
            )
        ),
        
        'lastname' => array(
            'config'  => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'firstname' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'gender' => array(
            'config' => array(
                'type'  => 'radio',
                'items' => array(
                    array( $tempLangPath . 'gender.f', 'f' ),
                    array( $tempLangPath . 'gender.m', 'm' ),
                )
            )
        ),
    
        'address' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'address2' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
            )
        ),
    
        'city' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
    
        'zip' => array(
            'config' => array(
                'type' => 'input',
                'size' => '5',
                'eval' => 'required'
            )
        ),
        
        'country' => array(
            'config' => array(
                'type'                => 'select',
                'foreign_table'       => 'static_countries',
                'foreign_table_where' => 'ORDER BY static_countries.cn_short_en'
            )
        ),
    
        'phone' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
            )
        ),
        
        'nationality' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'birthdate' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'date'
            )
        ),
    
        'school_name' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
    
        'school_address' => array(
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'school_country' => array(
            'config' => array(
                'type'                => 'select',
                'foreign_table'       => 'static_countries',
                'foreign_table_where' => 'ORDER BY static_countries.cn_short_en'
            )
        ),
        
        'age_proof' => array(
            'config' => array(
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => 'jpg,jpeg',
                'max_size'      => 2048,
                'uploadfolder'  => 'uploads/tx_adlercontest',
                'show_thumbs'   => 1,
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1
            )
        ),
        
        'school_proof' => array(
            'config' => array(
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => 'jpg,jpeg',
                'max_size'      => 2048,
                'uploadfolder'  => 'uploads/tx_adlercontest',
                'show_thumbs'   => 1,
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1
            )
        ),
        
        'project' => array(
            'config' => array(
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => 'jpg,jpeg',
                'max_size'      => 2048,
                'uploadfolder'  => 'uploads/tx_adlercontest',
                'show_thumbs'   => 1,
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1
            )
        ),
        
        'sworn' => array(
            'config'  => array(
                'type' => 'check',
            )
        ),
        
        'validated' => array(
            'config'  => array(
                'type' => 'check',
            )
        ),
        
        'confirm_token' => array(
            'config' => array(
                'type' => 'none'
            )
        )
    )
);

// Process each field
foreach( $TCA[ $tempTableName ][ 'columns' ] as $tempFieldName => &$tempField ) {
    
    // Sets the exclude flag
    $tempField[ 'exclude' ] = true;
    
    // Adds the label
    $tempField[ 'label' ]   = $tempLangPath . $tempFieldName;
    
    // Adds the field to the field list
    $tempFieldList[]        = $tempFieldName;
}

// Sets the BE fields list
$TCA[ $tempTableName ][ 'interface' ][ 'showRecordFieldList' ] = implode( ',', $tempFieldList );

// Cleanup - Unset unused variables
unset( $tempTableName );
unset( $tempExtPathInfo );
unset( $tempExtKey );
unset( $tempLangPath );
unset( $tempLglPath );
unset( $tempFieldList );
unset( $tempFieldName );
unset( $tempField );
?>
