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

// LLL path to the language file
$tempLangPath    = 'LLL:EXT:' . $tempExtKey . '/lang/' . $tempTableName . '.xml:';

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
            'showitem' => 'note;;;;1-1-1,'
                       .  'criteria_1;;;;1-1-1,'
                       .  'criteria_2,'
                       .  'criteria_3,'
                       .  'criteria_4,'
                       .  'criteria_5,'
                       .  'id_fe_users;;;;1-1-1'
        )
    ),
    
    // Palettes configuration
    'palettes'    => array(
        '1' => array( 'showitem' => '' )
    ),
    
    // Fields configuration
    'columns' => array(
        
        'criteria_1' => array(
            'config' => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        
        'criteria_2' => array(
            'config' => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        
        'criteria_3' => array(
            'config'  => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        
        'criteria_4' => array(
            'config' => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        
        'criteria_5' => array(
            'config'  => array(
                'type' => 'input',
                'size' => '10'
            )
        ),
        
        'note' => array(
            'config' => array(
                'type' => 'input',
                'size' => '10'
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
unset( $tempFieldList );
unset( $tempFieldName );
unset( $tempField );
?>
