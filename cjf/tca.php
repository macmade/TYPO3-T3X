<?php

if ( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Events TCA
$TCA[ 'tx_cjf_events' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_cjf_events' ][ 'ctrl' ],
    
    // BE settings
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,title,groups,place,date,hour,price,tickets,soldout,tickets_sold,tickets_booked,comments,no_seats,seats'
    ),
    
    // FE settings
    'feInterface' => $TCA[ 'tx_cjf_events' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1 ),
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0 )
                )
            )
        ),
        'l18n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array( '', 0 ),
                ),
                'foreign_table' => 'tx_cjf_events',
                'foreign_table_where' => 'AND tx_cjf_events.pid=###CURRENT_PID### AND tx_cjf_events.sys_language_uid IN (-1,0)',
            )
        ),
        'l18n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'title' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.title',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'groups' => Array (
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.groups',
            'config' => Array (
                'type' => 'select',
                'foreign_table' => 'tx_cjf_groups',
                'foreign_table_where' => 'AND tx_cjf_groups.pid=###CURRENT_PID### AND tx_cjf_groups.sys_language_uid=0 ORDER BY tx_cjf_groups.name',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 10,
                'MM' => 'tx_cjf_events_groups_mm',
                'wizards' => Array(
                    '_PADDING' => 2,
                    '_VERTICAL' => 1,
                    'add' => Array(
                        'type' => 'script',
                        'title' => 'Create new record',
                        'icon' => 'add.gif',
                        'params' => Array(
                            'table'=>'pages',
                            'pid' => '###CURRENT_PID###',
                            'setValue' => 'prepend'
                        ),
                        'script' => 'wizard_add.php',
                    ),
                    'edit' => Array(
                        'type' => 'popup',
                        'title' => 'Edit',
                        'script' => 'wizard_edit.php',
                        'popup_onlyOpenIfSelected' => 1,
                        'icon' => 'edit2.gif',
                        'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ),
                ),
            ),
        ),
        'place' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.place',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'tx_cjf_places',
                'foreign_table_where' => 'AND tx_cjf_places.pid=###CURRENT_PID### AND tx_cjf_places.sys_language_uid=0 ORDER BY tx_cjf_places.place',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'date' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.date',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'eval' => 'date',
            ),
        ),
        'hour' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.hour',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'eval' => 'time',
            ),
        ),
        'price' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.price',
            'config' => array(
                'type' => 'input',
                'size' => '10',
            ),
        ),
        'tickets' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.tickets',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'eval' => 'int',
                'range' => array(
                    'upper' => '10000',
                    'lower' => '0',
                ),
                'default' => 0,
            ),
        ),
        'soldout' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.soldout',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'tickets_sold' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.tickets_sold',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'tickets_booked' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.tickets_booked',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'comments' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.comments',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ),
        ),
        'no_seats' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.no_seats',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'seats' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events.seats',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'title;;;;1-1-1, place;;;;1-1-1, groups;;;;1-1-1, date;;;;1-1-1, hour, price, tickets, soldout, tickets_sold, tickets_booked, comments;;;;1-1-1, no_seats;;;;1-1-1, seats, hidden;;;;1-1-1, ' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Groups TCA
$TCA[ 'tx_cjf_groups' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_cjf_groups' ][ 'ctrl' ],
    
    // BE settings
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,name,country,style,description,www,artists,picture'
    ),
    
    // FE settings
    'feInterface' => $TCA[ 'tx_cjf_groups' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1 ),
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0 )
                )
            )
        ),
        'l18n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array( '', 0 ),
                ),
                'foreign_table' => 'tx_cjf_groups',
                'foreign_table_where' => 'AND tx_cjf_groups.pid=###CURRENT_PID### AND tx_cjf_groups.sys_language_uid IN (-1,0)',
            )
        ),
        'l18n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_groups.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'country' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_groups.country',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'style' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_groups.style',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'tx_cjf_styles',
                'foreign_table_where' => 'AND tx_cjf_styles.pid=###CURRENT_PID### AND tx_cjf_styles.sys_language_uid=0 ORDER BY tx_cjf_styles.style',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'description' => array(
            'exclude' => 1,
            'l10n_display' => 'hideDiff',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_groups.description',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => array(
                    '_PADDING' => 2,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif',
                        'script' => 'wizard_rte.php',
                    ),
                ),
            ),
        ),
        'picture' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_groups.picture',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'max_size' => 5000,
                'uploadfolder' => 'uploads/tx_cjf',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'www' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_groups.www',
            'config' => array(
                'type' => 'input',
                'size' => '15',
                'max' => '255',
                'checkbox' => '',
                'eval' => 'trim',
            ),
        ),
        'artists' => array(
            'exclude' => 1,
            'l10n_display' => 'hideDiff',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_groups.artists',
            'config' => array(
                'type' => 'flex',
                'ds' => array(
                    'default' => 'FILE:EXT:cjf/flexform_ds_groups.xml',
                ),
            ),
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => '--div--;LLL:EXT:cjf/locallang_db.php:tx_cjf_groups.s_main, name;;;;1-1-1, country;;;;1-1-1, style, description;;;richtext[cut|copy|paste|formatblock|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cjf/rte/];1-1-1, picture;;;;1-1-1, www;;;;1-1-1, hidden;;;;1-1-1, --div--;LLL:EXT:cjf/locallang_db.php:tx_cjf_groups.s_artists, artists;;;;1-1-1' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Artists TCA
$TCA[ 'tx_cjf_artists' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_cjf_artists' ][ 'ctrl' ],
    
    // BE settings
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,name_last,name_first,www,label,picture'
    ),
    
    // FE settings
    'feInterface' => $TCA[ 'tx_cjf_artists' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'name_last' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_artists.name_last',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'name_first' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_artists.name_first',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            ),
        ),
        'description' => array(
            'exclude' => 1,
            'l10n_display' => 'hideDiff',
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_artists.description',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => array(
                    '_PADDING' => 2,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif',
                        'script' => 'wizard_rte.php',
                    ),
                ),
            ),
        ),
        'picture' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_artists.picture',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'max_size' => 5000,
                'uploadfolder' => 'uploads/tx_cjf',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'www' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_artists.www',
            'config' => array(
                'type' => 'input',
                'size' => '15',
                'max' => '255',
                'checkbox' => '',
                'eval' => 'trim',
            ),
        ),
        'label' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_artists.label',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            ),
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'name_last;;;;1-1-1, name_first, description;;;richtext[cut|copy|paste|formatblock|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_cjf/rte/];1-1-1, picture;;;;1-1-1, www;;;;1-1-1, label;;;;1-1-1, hidden;;;;1-1-1' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Styles TCA
$TCA[ 'tx_cjf_styles' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_cjf_styles' ][ 'ctrl' ],
    
    // BE settings
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,style'
    ),
    
    // FE settings
    'feInterface' => $TCA[ 'tx_cjf_styles' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1 ),
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0 )
                )
            )
        ),
        'l18n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array( '', 0 ),
                ),
                'foreign_table' => 'tx_cjf_styles',
                'foreign_table_where' => 'AND tx_cjf_styles.pid=###CURRENT_PID### AND tx_cjf_styles.sys_language_uid IN (-1,0)',
            )
        ),
        'l18n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'style' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_styles.style',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'style;;;;1-1-1, hidden;;;;1-1-1' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Places TCA
$TCA[ 'tx_cjf_places' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_cjf_places' ][ 'ctrl' ],
    
    // BE settings
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,place'
    ),
    
    // FE settings
    'feInterface' => $TCA[ 'tx_cjf_places' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1 ),
                    array( 'LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0 )
                )
            )
        ),
        'l18n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array( '', 0 ),
                ),
                'foreign_table' => 'tx_cjf_places',
                'foreign_table_where' => 'AND tx_cjf_places.pid=###CURRENT_PID### AND tx_cjf_places.sys_language_uid IN (-1,0)',
            )
        ),
        'l18n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'place' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_places.place',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'place;;;;1-1-1, hidden;;;;1-1-1' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Places TCA
$TCA[ 'tx_cjf_clients' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_cjf_clients' ][ 'ctrl' ],
    
    // BE settings
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,type,name_last,name_first,address,zip,city,country,email,phone_professionnal,phone_personnal,fax,ip,newsletter'
    ),
    
    // FE settings
    'feInterface' => $TCA[ 'tx_cjf_clients' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'type' => array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.type',
            'config' => array (
                'type' => 'select',
                'items' => Array (
                    array( 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.type.I.0', '0' ),
                    array( 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.type.I.1', '1' ),
                ),
                'size' => 1,
                'maxitems' => 1,
            )
        ),
        'name_last' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.name_last',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'name_first' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.name_first',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'address' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.address',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ),
        ),
        'zip' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.zip',
            'config' => array(
                'type' => 'input',
                'size' => '5',
                'eval' => 'required',
            ),
        ),
        'city' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.city',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'country' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.country',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'email' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.email',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            ),
        ),
        'phone_professionnal' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.phone_professionnal',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            ),
        ),
        'phone_personnal' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.phone_personnal',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            ),
        ),
        'fax' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.fax',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            ),
        ),
        'ip' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.ip',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'newsletter' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients.newsletter',
            'config' => array(
                'type' => 'check',
            ),
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'type;;;;1-1-1, name_last;;;;1-1-1, hidden;;;;1-1-1' ),
        '1' => array( 'showitem' => 'type;;;;1-1-1, name_last;;;;1-1-1, name_first, address;;;;1-1-1, zip;;;;1-1-1, city, country, email;;;;1-1-1, phone_professionnal;;;;1-1-1, phone_personnal, fax, newsletter;;;;1-1-1, ip;;;;1-1-1, hidden;;;;1-1-1' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Orders TCA
$TCA[ 'tx_cjf_orders' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_cjf_orders' ][ 'ctrl' ],
    
    // BE settings
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,id_client,id_event,quantity,price,total,confirmed,type,orderid,transaction_id,pdf,labelprinted,processed'
    ),
    
    // FE settings
    'feInterface' => $TCA[ 'tx_cjf_orders' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'id_client' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.id_client',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'id_event' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.id_event',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'quantity' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.quantity',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'price' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.price',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'total' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.total',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'type' => array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.type',
            'config' => array (
                'type' => 'none',
            )
        ),
        'orderid' => array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.orderid',
            'config' => array (
                'type' => 'none',
            )
        ),
        'transaction_id' => array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.transaction_id',
            'config' => array (
                'type' => 'none',
            )
        ),
        'pdf' => array (
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.pdf',
            'config' => array (
                'type' => 'none',
            )
        ),
        'confirmed' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.confirmed',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'processed' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.processed',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'labelprinted' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders.labelprinted',
            'config' => array(
                'type' => 'none',
            ),
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'id_client;;;;1-1-1, id_event;;;;1-1-1, quantity;;;;1-1-1, price, total, total;;;;1-1-1, confirmed;;;;1-1-1, type, orderid;;;;1-1-1, processed, transaction_id, pdf, labelprinted;;;;1-1-1, hidden;;;;1-1-1' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Orders TCA
$TCA[ 'tx_cjf_bookings' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_cjf_bookings' ][ 'ctrl' ],
    
    // BE settings
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,id_client,id_event,tickets_booked'
    ),
    
    // FE settings
    'feInterface' => $TCA[ 'tx_cjf_bookings' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'id_client' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_bookings.id_client',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'id_event' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_bookings.id_event',
            'config' => array(
                'type' => 'none',
            ),
        ),
        'tickets_booked' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_bookings.tickets_booked',
            'config' => array(
                'type' => 'none',
            ),
        ),
        #'tickets_sold' => array(
        #    'exclude' => 1,
        #    'label' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_bookings.tickets_sold',
        #    'config' => array(
        #        'type' => 'user',
        #        'userFunc' => 'tx_cjf_tca->numberInput',
        #        'eval' => 'int',
        #    ),
        #),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'id_client;;;;1-1-1, id_event;;;;1-1-1, tickets_booked;;;;1-1-1, hidden;;;;1-1-1' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);
?>
