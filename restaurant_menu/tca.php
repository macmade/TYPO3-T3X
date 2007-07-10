<?php
if( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Categories TCA
$TCA[ 'tx_restaurantmenu_categories' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_restaurantmenu_categories' ][ 'ctrl' ],
    
    // Backend options
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,name,description'
    ),
    
    // Frontend options
    'feInterface' => $TCA[ 'tx_restaurantmenu_categories' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_categories.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,uniqueInPid',
            )
        ),
        'description' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_categories.description',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            )
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'hidden;;;;1-1-1, name, description' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Vine types TCA
$TCA[ 'tx_restaurantmenu_vinetypes' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_restaurantmenu_vinetypes' ][ 'ctrl' ],
    
    // Backend options
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,name'
    ),
    
    // Frontend options
    'feInterface' => $TCA[ 'tx_restaurantmenu_vinetypes' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vinetypes.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,uniqueInPid',
            )
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'hidden;;;;1-1-1, name' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Vines TCA
$TCA[ 'tx_restaurantmenu_vines' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_restaurantmenu_vines' ][ 'ctrl' ],
    
    // Backend options
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,name,description,picture,year,region,country,price,vinetype'
    ),
    
    // Frontend options
    'feInterface' => $TCA[ 'tx_restaurantmenu_vines' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            )
        ),
        'description' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines.description',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            )
        ),
        'picture' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines.picture',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'max_size' => 500,
                'uploadfolder' => 'uploads/tx_restaurantmenu',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'year' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines.year',
            'config' => array(
                'type' => 'input',
                'size' => '5',
                'eval' => 'required,year',
            )
        ),
        'region' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines.region',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        ),
        'country' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines.country',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        ),
        'price' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines.price',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'eval' => 'required,double2',
            )
        ),
        'vinetype' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines.vinetype',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'tx_restaurantmenu_vinetypes',
                'foreign_table_where' => 'AND tx_restaurantmenu_vinetypes.pid=###CURRENT_PID### ORDER BY tx_restaurantmenu_vinetypes.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'wizards' => Array(
                    '_PADDING' => 2,
                    '_VERTICAL' => 1,
                    'add' => Array(
                        'type' => 'script',
                        'title' => 'Create new record',
                        'icon' => 'add.gif',
                        'params' => Array(
                            'table'=>'tx_restaurantmenu_vinetypes',
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
            )
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'hidden;;;;1-1-1, name, description, picture, year, region, country, price, vinetype' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Ingredients TCA
$TCA[ 'tx_restaurantmenu_ingredients' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_restaurantmenu_ingredients' ][ 'ctrl' ],
    
    // Backend options
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,name'
    ),
    
    // Frontend options
    'feInterface' => $TCA[ 'tx_restaurantmenu_ingredients' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_ingredients.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,uniqueInPid',
            )
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'hidden;;;;1-1-1, name' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Menus TCA
$TCA[ 'tx_restaurantmenu_menus' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_restaurantmenu_menus' ][ 'ctrl' ],
    
    // Backend options
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,name,sections'
    ),
    
    // Frontend options
    'feInterface' => $TCA[ 'tx_restaurantmenu_menus' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_menus.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            )
        ),
        'sections' => array(
            'config' => array(
                'type' => 'passthrough',
            )
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'hidden;;;;1-1-1, name, sections' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);

// Courses TCA
$TCA[ 'tx_restaurantmenu_courses' ] = array(
    
    // Control section
    'ctrl' => $TCA[ 'tx_restaurantmenu_courses' ][ 'ctrl' ],
    
    // Backend options
    'interface' => array(
        
        // Available fields
        'showRecordFieldList' => 'hidden,name,picture,ingredients,price_big,price_small,categories,vines,takeaway'
    ),
    
    // Frontend options
    'feInterface' => $TCA[ 'tx_restaurantmenu_courses' ][ 'feInterface' ],
    
    // Fields configuration
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses.name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required',
            )
        ),
        'picture' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses.picture',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'gif,png,jpeg,jpg',
                'max_size' => 500,
                'uploadfolder' => 'uploads/tx_restaurantmenu',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'ingredients' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses.ingredients',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'tx_restaurantmenu_ingredients',
                'foreign_table_where' => 'AND tx_restaurantmenu_ingredients.pid=###CURRENT_PID### ORDER BY tx_restaurantmenu_ingredients.uid',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 100,
                'wizards' => Array(
                    '_PADDING' => 2,
                    '_VERTICAL' => 1,
                    'add' => Array(
                        'type' => 'script',
                        'title' => 'Create new record',
                        'icon' => 'add.gif',
                        'params' => Array(
                            'table'=>'tx_restaurantmenu_ingredients',
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
            )
        ),
        'price_big' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses.price_big',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'eval' => 'double2',
            )
        ),
        'price_small' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses.price_small',
            'config' => array(
                'type' => 'input',
                'size' => '10',
                'eval' => 'double2',
            )
        ),
        'categories' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses.categories',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'tx_restaurantmenu_categories',
                'foreign_table_where' => 'AND tx_restaurantmenu_categories.pid=###CURRENT_PID### ORDER BY tx_restaurantmenu_categories.uid',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 10,
                'wizards' => Array(
                    '_PADDING' => 2,
                    '_VERTICAL' => 1,
                    'add' => Array(
                        'type' => 'script',
                        'title' => 'Create new record',
                        'icon' => 'add.gif',
                        'params' => Array(
                            'table'=>'tx_restaurantmenu_categories',
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
            )
        ),
        'vines' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses.vines',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'tx_restaurantmenu_vines',
                'foreign_table_where' => 'AND tx_restaurantmenu_vines.pid=###CURRENT_PID### ORDER BY tx_restaurantmenu_vines.uid',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 10,
                'wizards' => Array(
                    '_PADDING' => 2,
                    '_VERTICAL' => 1,
                    'add' => Array(
                        'type' => 'script',
                        'title' => 'Create new record',
                        'icon' => 'add.gif',
                        'params' => Array(
                            'table'=>'tx_restaurantmenu_vines',
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
            )
        ),
        'takeaway' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses.takeaway',
            'config' => array(
                'type' => 'check',
            )
        ),
    ),
    
    // Types configuration
    'types' => array(
        '0' => array( 'showitem' => 'hidden;;;;1-1-1, name, picture, ingredients, price_big, price_small, categories, vines, takeaway' )
    ),
    
    // Palettes configuration
    'palettes' => array(
        '1' => array( 'showitem' => '' )
    )
);
?>
