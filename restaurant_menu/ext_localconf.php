<?php
if ( !defined( 'TYPO3_MODE' ) ) {
    die( 'Access denied.' );
}

// Add FE plugin
t3lib_extMgm::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_restaurantmenu_pi1.php',
    '_pi1',
    'list_type',
    1
);

// Save & New options
t3lib_extMgm::addUserTSConfig('
    options.saveDocNew.tx_restaurantmenu_categories=1
    options.saveDocNew.tx_restaurantmenu_vinetypes=1
    options.saveDocNew.tx_restaurantmenu_vines=1
    options.saveDocNew.tx_restaurantmenu_ingredients=1
    options.saveDocNew.tx_restaurantmenu_menus=1
    options.saveDocNew.tx_restaurantmenu_courses=1
');
?>
