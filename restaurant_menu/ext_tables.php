<?php
if( !defined ( 'TYPO3_MODE' ) ) {
    die ( 'Access denied.' );
}

// Load content TCA
t3lib_div::loadTCA( 'tt_content' );

// Plugin options
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][$_EXTKEY.'_pi1' ]='layout,select_key';

// Add FE plugin
t3lib_extMgm::addPlugin(
    array(
        'LLL:EXT:restaurant_menu/locallang_db.xml:tt_content.list_type_pi1',
        $_EXTKEY . '_pi1'
    ),
    'list_type'
);

// Static template
t3lib_extMgm::addStaticFile( $_EXTKEY, 'pi1/static/', 'Restaurant - Menu display' );

// Add wizard icon for backend
if(TYPO3_MODE == 'BE' )	{
    $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'tx_restaurantmenu_pi1_wizicon' ] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi1/class.tx_restaurantmenu_pi1_wizicon.php';
}

// Categories TCA
$TCA[ 'tx_restaurantmenu_categories' ] = array(
    
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'              => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_categories',
        
        // Records label
        'label'              => 'name',
        
        // Modification date
        'tstamp'             => 'tstamp',
        
        // Creation date
        'crdate'             => 'crdate',
        
        // Creation user
        'cruser_id'          => 'cruser_id',
        
        // Default sorting field
        'default_sortby'     => 'ORDER BY name',
        
        // Delete flag
        'delete'             => 'deleted',
        
        // Special fields
        'enablecolumns'      => array(
            
            // Hidden flag
            'disabled' => 'hidden'
        ),
        
        // Fields configuration
        'dynamicConfigFile'  => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'           => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_restaurantmenu_categories.gif'
    ),
    
    // Frontend interface
    'feInterface' => array(
        
        // Available fields for FE admin
        'fe_admin_fieldList' => 'hidden, name, description'
    )
);

// Vine types TCA
$TCA[ 'tx_restaurantmenu_vinetypes' ] = array(
    
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'              => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vinetypes',
        
        // Records label
        'label'              => 'name',
        
        // Modification date
        'tstamp'             => 'tstamp',
        
        // Creation date
        'crdate'             => 'crdate',
        
        // Creation user
        'cruser_id'          => 'cruser_id',
        
        // Default sorting field
        'sortby'             => 'sorting',
        
        // Delete flag
        'delete'             => 'deleted',
        
        // Special fields
        'enablecolumns'      => array(
            
            // Hidden flag
            'disabled' => 'hidden'
        ),
        
        // Fields configuration
        'dynamicConfigFile'  => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        
        // Table icon
        'iconfile'           => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_restaurantmenu_vinetypes.gif'
    ),
    
    // Frontend interface
    'feInterface' => array(
        
        // Available fields for FE admin
        'fe_admin_fieldList' => 'hidden, name'
    )
);

// Vines TCA
$TCA[ 'tx_restaurantmenu_vines' ] = array(
    
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'              => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_vines',
        
        // Records label
        'label'              => 'name',
        
        // Modification date
        'tstamp'             => 'tstamp',
        
        // Creation date
        'crdate'             => 'crdate',
        
        // Creation user
        'cruser_id'          => 'cruser_id',
        
        // Default sorting field
        'default_sortby'     => 'ORDER BY name',
        
        // Delete flag
        'delete'             => 'deleted',
        
        // Special fields
        'enablecolumns'      => array(
            
            // Hidden flag
            'disabled' => 'hidden'
        ),
        
        // Fields configuration
        'dynamicConfigFile'  => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'           => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_restaurantmenu_vines.gif'
    ),
    
    // Frontend interface
    'feInterface' => array(
        
        // Available fields for FE admin
        'fe_admin_fieldList' => 'hidden, name, description, picture, year, region, country, price, vinetype'
    )
);

// Ingredients TCA
$TCA[ 'tx_restaurantmenu_ingredients' ] = array(
    
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'              => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_ingredients',
        
        // Records label
        'label'              => 'name',
        
        // Modification date
        'tstamp'             => 'tstamp',
        
        // Creation date
        'crdate'             => 'crdate',
        
        // Creation user
        'cruser_id'          => 'cruser_id',
        
        // Default sorting field
        'default_sortby'     => 'ORDER BY name',
        
        // Delete flag
        'delete'             => 'deleted',
        
        // Special fields
        'enablecolumns'      => array(
            
            // Hidden flag
            'disabled' => 'hidden'
        ),
        
        // Fields configuration
        'dynamicConfigFile'  => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'           => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_restaurantmenu_ingredients.gif'
    ),
    
    // Frontend interface
    'feInterface' => array(
        
        // Available fields for FE admin
        'fe_admin_fieldList' => 'hidden, name'
    )
);

// Menus TCA
$TCA[ 'tx_restaurantmenu_menus' ] = array(
    
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'              => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_menus',
        
        // Records label
        'label'              => 'name',
        
        // Modification date
        'tstamp'             => 'tstamp',
        
        // Creation date
        'crdate'             => 'crdate',
        
        // Creation user
        'cruser_id'          => 'cruser_id',
        
        // Default sorting field
        'default_sortby'     => 'ORDER BY name',
        
        // Delete flag
        'delete'             => 'deleted',
        
        // Special fields
        'enablecolumns'      => array(
            
            // Hidden flag
            'disabled' => 'hidden'
        ),
        
        // Fields configuration
        'dynamicConfigFile'  => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'           => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_restaurantmenu_menus.gif'
    ),
    
    // Frontend interface
    'feInterface' => array(
        
        // Available fields for FE admin
        'fe_admin_fieldList' => 'hidden, name, sections'
    )
);

// Courses TCA
$TCA[ 'tx_restaurantmenu_courses' ] = array(
    
    // Control section
    'ctrl' => array(
        
        // Table title
        'title'              => 'LLL:EXT:restaurant_menu/locallang_db.xml:tx_restaurantmenu_courses',
        
        // Records label
        'label'              => 'name',
        
        // Modification date
        'tstamp'             => 'tstamp',
        
        // Creation date
        'crdate'             => 'crdate',
        
        // Creation user
        'cruser_id'          => 'cruser_id',
        
        // Default sorting field
        'default_sortby'     => 'ORDER BY name',
        
        // Delete flag
        'delete'             => 'deleted',
        
        // Special fields
        'enablecolumns'      => array(
            
            // Hidden flag
            'disabled' => 'hidden'
        ),
        
        // Fields configuration
        'dynamicConfigFile'  => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
        
        // Table icon
        'iconfile'           => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_restaurantmenu_courses.gif'
    ),
    
    // Frontend interface
    'feInterface' => array(
        
        // Available fields for FE admin
        'fe_admin_fieldList' => 'hidden, name, picture, ingredients, price_big, price_small, categories, vines, takeaway'
    )
);
?>
