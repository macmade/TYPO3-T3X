<?php
    
    if( !defined ( 'TYPO3_MODE' ) ) {
        die( 'Access denied.' );
    }
    
    // Backend options
    if( TYPO3_MODE == 'BE' ) {
        
        // Backend module
        t3lib_extMgm::addModule( 'web', 'txcjfM1', '', t3lib_extMgm::extPath( $_EXTKEY ) . 'mod1/' );
        
        // Module flag on pages
        $TCA[ 'pages' ][ 'columns' ][ 'module' ][ 'config' ][ 'items' ][] = array( 'CJF', 'cjf' );
    }
    
    // Include the TCA helper class
    include_once( t3lib_extMgm::extPath( 'cjf' ) . 'class.tx_cjf_tca.php' );
    
    // Load content TCA
    t3lib_div::loadTCA( 'tt_content' );
    
    // Plugin options
    $TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $_EXTKEY . '_pi1' ] = 'layout,select_key,pages,recursive';
    
    // Add flexform field to plugin options
    $TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $_EXTKEY . '_pi1' ] = 'pi_flexform';
    
    // Add flexform DataStructure
    t3lib_extMgm::addPiFlexFormValue( $_EXTKEY . '_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml' );
    
    // Add FE plugin
    t3lib_extMgm::addPlugin( array( 'LLL:EXT:cjf/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY . '_pi1' ), 'list_type' );
    
    // Static TypoScript code
    t3lib_extMgm::addStaticFile( $_EXTKEY, 'pi1/static/', 'Cully Jazz Festival - Events' );
    
    // Events TCA
    $TCA[ 'tx_cjf_events' ] = array(
        
        // Control section
        'ctrl' => array(
            
            // Table title
            'title' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_events',
            
            // Label field
            'label' => 'title',
            
            // Modification date
            'tstamp' => 'tstamp',
            
            // Creation date
            'crdate' => 'crdate',
            
            // Creation user
            'cruser_id' => 'cruser_id',
            
            // Sorty by
            'default_sortby' => 'ORDER BY date DESC,hour DESC,title ASC',
            
            // Use tabs
            "dividers2tabs" => 1,
            
            // Delete flag
            'delete' => 'deleted',
            
            // Localization fields
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l18n_parent',
            'transOrigDiffSourceField' => 'l18n_diffsource',
            
            // Special fields
            'enablecolumns' => array(
                
                // Hide flag
                'disabled' => 'hidden',
            ),
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
            
            // Icon
            'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/icon_tx_cjf_events.gif',
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => 'hidden, title, groups, place, date, hour, price, tickets, soldout, tickets_sold, tickets_booked, comments, no_seats, seats',
        )
    );
    
    // Groups TCA
    $TCA[ 'tx_cjf_groups' ] = array(
        
        // Control section
        'ctrl' => array(
            
            // Table title
            'title' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_groups',
            
            // Label field
            'label' => 'name',
            
            // Thumbnail
            'thumbnail' => 'picture',
            
            // Modification date
            'tstamp' => 'tstamp',
            
            // Creation date
            'crdate' => 'crdate',
            
            // Creation user
            'cruser_id' => 'cruser_id',
            
            // Sorty by
            'default_sortby' => 'ORDER BY name',
            
            // Use tabs
            "dividers2tabs" => 1,
            
            // Delete flag
            'delete' => 'deleted',
            
            // Localization fields
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l18n_parent',
            'transOrigDiffSourceField' => 'l18n_diffsource',
            
            // Special fields
            'enablecolumns' => array(
                
                // Hide flag
                'disabled' => 'hidden',
            ),
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
            
            // Icon
            'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/icon_tx_cjf_groups.gif',
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => 'hidden, name, country, style, description, www, artists, picture',
        )
    );
    
    // Artists TCA
    $TCA[ 'tx_cjf_artists' ] = array(
        
        // Control section
        'ctrl' => array(
            
            // Table title
            'title' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_artists',
            
            // Label field
            'label' => 'name_last',
            
            // Alternative label field
            'label_alt' => 'name_first',
            
            // Force alternative label
            'label_alt_force' => true,
            
            // Thumbnail
            'thumbnail' => 'picture',
            
            // Modification date
            'tstamp' => 'tstamp',
            
            // Creation date
            'crdate' => 'crdate',
            
            // Creation user
            'cruser_id' => 'cruser_id',
            
            // Sorty by
            'default_sortby' => 'ORDER BY name_last',
            
            // Delete flag
            'delete' => 'deleted',
            
            // Special fields
            'enablecolumns' => array(
                
                // Hide flag
                'disabled' => 'hidden',
            ),
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
            
            // Icon
            'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/icon_tx_cjf_artists.gif',
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => 'hidden, name_last, name_first, description, www, label, picture',
        )
    );
    
    // Styles TCA
    $TCA[ 'tx_cjf_styles' ] = array(
        
        // Control section
        'ctrl' => array(
            
            // Table title
            'title' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_styles',
            
            // Label field
            'label' => 'style',
            
            // Modification date
            'tstamp' => 'tstamp',
            
            // Creation date
            'crdate' => 'crdate',
            
            // Creation user
            'cruser_id' => 'cruser_id',
            
            // Sorty by
            'default_sortby' => 'ORDER BY style',
            
            // Delete flag
            'delete' => 'deleted',
            
            // Localization fields
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l18n_parent',
            'transOrigDiffSourceField' => 'l18n_diffsource',
            
            // Special fields
            'enablecolumns' => array(
                
                // Hide flag
                'disabled' => 'hidden',
            ),
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
            
            // Icon
            'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/icon_tx_cjf_styles.gif',
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => 'hidden, style',
        )
    );
    
    // Places TCA
    $TCA[ 'tx_cjf_places' ] = array(
        
        // Control section
        'ctrl' => array(
            
            // Table title
            'title' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_places',
            
            // Label field
            'label' => 'place',
            
            // Modification date
            'tstamp' => 'tstamp',
            
            // Creation date
            'crdate' => 'crdate',
            
            // Creation user
            'cruser_id' => 'cruser_id',
            
            // Sorty by
            'default_sortby' => 'ORDER BY place',
            
            // Delete flag
            'delete' => 'deleted',
            
            // Sorting
            'sortby' => 'sorting',
            
            // Localization fields
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l18n_parent',
            'transOrigDiffSourceField' => 'l18n_diffsource',
            
            // Special fields
            'enablecolumns' => array(
                
                // Hide flag
                'disabled' => 'hidden',
            ),
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
            
            // Icon
            'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/icon_tx_cjf_places.gif',
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => 'hidden, place',
        )
    );
    
    // Clients TCA
    $TCA[ 'tx_cjf_clients' ] = array(
        
        // Control section
        'ctrl' => array(
            
            // Table title
            'title' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_clients',
            
            // Label field
            'label' => 'name_last',
            
            // Alternative label field
            'label_alt' => 'name_first',
            
            // Force alternative label
            'label_alt_force' => true,
            
            // Modification date
            'tstamp' => 'tstamp',
            
            // Creation date
            'crdate' => 'crdate',
            
            // Creation user
            'cruser_id' => 'cruser_id',
            
            // Record type
            'type' => 'type',
            
            // Sorty by
            'default_sortby' => 'ORDER BY name_last',
            
            // Delete flag
            'delete' => 'deleted',
            
            // Special fields
            'enablecolumns' => array(
                
                // Hide flag
                'disabled' => 'hidden',
            ),
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
            
            // Icon
            'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/icon_tx_cjf_clients.gif',
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => 'hidden, type, name_last, name_first, address, zip, city, country, email, phone_professionnal, phone_personnal, fax, ip, newsletter',
        )
    );
    
    // Orders TCA
    $TCA[ 'tx_cjf_orders' ] = array(
        
        // Control section
        'ctrl' => array(
            
            // Table title
            'title' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_orders',
            
            // Label field
            'label' => 'orderid',
            
            // Modification date
            'tstamp' => 'tstamp',
            
            // Creation date
            'crdate' => 'crdate',
            
            // Creation user
            'cruser_id' => 'cruser_id',
            
            // Sorty by
            'default_sortby' => 'ORDER BY uid',
            
            // Delete flag
            'delete' => 'deleted',
            
            // Special fields
            'enablecolumns' => array(
                
                // Hide flag
                'disabled' => 'hidden',
            ),
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
            
            // Icon
            'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/icon_tx_cjf_orders.gif',
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => 'hidden, id_client, id_event, quantity, price, total, confirmed, type, orderid, transaction_id, pdf, labelprinted, processed',
        )
    );
    
    // Orders TCA
    $TCA[ 'tx_cjf_bookings' ] = array(
        
        // Control section
        'ctrl' => array(
            
            // Table title
            'title' => 'LLL:EXT:cjf/locallang_db.xml:tx_cjf_bookings',
            
            // Label field
            'label' => 'uid',
            
            // Modification date
            'tstamp' => 'tstamp',
            
            // Creation date
            'crdate' => 'crdate',
            
            // Creation user
            'cruser_id' => 'cruser_id',
            
            // Sorty by
            'default_sortby' => 'ORDER BY uid',
            
            // Delete flag
            'delete' => 'deleted',
            
            // Special fields
            'enablecolumns' => array(
                
                // Hide flag
                'disabled' => 'hidden',
            ),
            
            // External configuration file
            'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
            
            // Icon
            'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'res/icon_tx_cjf_bookings.gif',
        ),
        
        // FE settings
        'feInterface' => array(
            
            // Available fields
            'fe_admin_fieldList' => 'hidden, id_client, id_event, tickets_booked',
        )
    );
?>
