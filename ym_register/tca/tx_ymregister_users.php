<?php

# $Id$

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_ymregister_users' ] = array(
    'ctrl'        => $TCA[ 'tx_ymregister_users' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_ymregister_users' ][ 'feInterface' ],
    'interface'   => array(
        'showRecordFieldList' => 'hidden,id_fe_users,id_tx_facebookconnect_users,lastname,firstname,nickname,age,address,zip,id_tx_netdata_states,id_tx_netdata_cities,phone,confirm_hash'
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'id_fe_users' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:id_fe_users',
            'config' => array(
                'type'          => 'inline',
                'foreign_table' => 'fe_users',
                'maxitems'      => 1,
                'appearance'    => array(
                    'expandSingle' => true
                )
            )
        ),
        'id_tx_facebookconnect_users' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:id_tx_facebookconnect_users',
            'config' => array(
                'type'          => 'inline',
                'foreign_table' => 'tx_facebookconnect_users',
                'maxitems'      => 1,
                'appearance'    => array(
                    'expandSingle' => true
                )
            )
        ),
        'lastname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:lastname',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'firstname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:firstname',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'nickname' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:nickname',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'phone' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:phone',
            'config'  => array(
                'type' => 'input',
                'size' => '30'
            )
        ),
        'age' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:age',
            'config'  => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'int'
            )
        ),
        'address' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:address',
            'config'  => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '2'
            )
        ),
        'zip' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:zip',
            'config'  => array(
                'type' => 'input',
                'size' => '5'
            )
        ),
        'id_tx_netdata_states' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:id_tx_netdata_states',
            'config' => array(
                'type'          => 'select',
                'foreign_table' => 'tx_netdata_states',
                'maxitems'      => 1,
                'items'         => array(
                    array( '', '' )
                )
            )
        ),
        'id_tx_netdata_cities' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:id_tx_netdata_cities',
            'config' => array(
                'type'          => 'select',
                'foreign_table' => 'tx_netdata_cities',
                'maxitems'      => 1,
                'items'         => array(
                    array( '', '' )
                )
            )
        ),
        'confirm_hash' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:ym_register/lang/tx_ymregister_users.xml:confirm_hash',
            'config'  => array(
                'type' => 'none',
            )
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'hidden;;;;1-1-1,lastname;;;;1-1-1,firstname,nickname;;;;1-1-1,age,address;;;;1-1-1,zip,id_tx_netdata_states,id_tx_netdata_cities,phone;;;;1-1-1,id_fe_users;;;;1-1-1,id_tx_facebookconnect_users;;;;1-1-1,confirm_hash;;;;1-1-1'
        ),
    ),
    'palettes' => array()
);

?>
