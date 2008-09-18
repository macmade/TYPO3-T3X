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
            'showitem' => 'type;;;;1-1-1,'
                       .  'subject;;;;1-1-1,'
                       .  'from_email;;;;1-1-1,'
                       .  'from_name;;;;1-1-1,'
                       .  'reply_to,'
                       .  'message;;;;1-1-1'
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
        
        'type' => array(
            'config'  => array(
                'type'  => 'select',
                'items' => array(
                    array( $tempLangPath . 'type.0', 0 ),
                    array( $tempLangPath . 'type.1', 1 )
                )
            )
        ),
        
        'subject' => array(
            'config'  => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'from_email' => array(
            'config'  => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'from_mail' => array(
            'config'  => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'reply_to' => array(
            'config'  => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'required'
            )
        ),
        
        'message' => array(
            'config'  => array(
                'type' => 'text',
                'rows' => '5',
                'cols' => '30',
                'eval' => 'required'
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
unset( $tempLglPath );
unset( $tempFieldList );
unset( $tempFieldName );
unset( $tempField );
?>
