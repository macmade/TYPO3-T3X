<?php

# $Id: tx_marvinaddresses_addresses.php 127 2009-12-03 15:17:33Z macmade $

// Security check
tx_oop_Typo3_Utils::typo3ModeCheck();

// Twitter users table
$TCA[ 'tx_marvinaddresses_addresses' ] = array(
    'ctrl'        => $TCA[ 'tx_marvinaddresses_addresses' ][ 'ctrl' ],
    'feInterface' => $TCA[ 'tx_marvinaddresses_addresses' ][ 'feInterface' ],
    'interface'   => array(
        'showRecordFieldList' => 'hidden,address_type,sav,fullname,address,address_number,zip,id_city,phone,phone2,fax,email,email2,url,infos,remarks'
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
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l10n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array( '', 0 ),
                ),
                'foreign_table' => 'tx_marvinaddresses_addresses',
                'foreign_table_where' => 'AND tx_marvinaddresses_addresses.pid=###CURRENT_PID### AND tx_marvinaddresses_addresses.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'address_type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:address_type',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array( 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:address_type.0', 0 ),
                    array( 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:address_type.1', 1 ),
                    array( 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:address_type.2', 2 )
                )
            )
        ),
        'sav' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:sav',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'fullname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:fullname',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'address' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:address',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'address_number' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:address_number',
            'config' => array(
                'type' => 'input',
                'size' => '5'
            ),
        ),
        'zip' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:zip',
            'config' => array(
                'type' => 'input',
                'size' => '5'
            ),
        ),
        'id_city' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:id_city',
            'config' => array(
                'type'          => 'select',
                'itemsProcFunc' => 'tx_marvindata_Tca_Helper->getCities'
            ),
        ),
        'phone' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:phone',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'phone2' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:phone2',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'fax' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:fax',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'email' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:email',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'email2' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:email2',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'url' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:url',
            'config' => array(
                'type' => 'input',
                'size' => '30'
            ),
        ),
        'infos' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:infos',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            ),
        ),
        'remarks' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:marvin_addresses/lang/tx_marvinaddresses_addresses.xml:remarks',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            ),
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'hidden;;;;1-1-1,address_type;;;;1-1-1,sav,fullname;;;;1-1-1,address;;;;1-1-1,address_number,zip,id_city,phone;;;;1-1-1,phone2,fax,email;;;;1-1-1,email2,url,infos;;;;1-1-1,remarks'
        ),
    ),
    'palettes' => array()
);

?>
