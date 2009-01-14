<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (info@macmade.net)
 * All rights reserved
 * 
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * 
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Module 'CJF - Tickets' for the 'cjf' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - INIT
 *        :     function init
 *        : function main
 * 
 * SECTION:     2 - MAIN
 *        :     function menuConfig
 *        :     function moduleContent
 * 
 *              TOTAL FUNCTIONS: 
 */

// Default initialization of the module
unset( $MCONF );
require( 'conf.php' );
require( $BACK_PATH . 'init.php' );
require( $BACK_PATH . 'template.php' );
$LANG->includeLLFile( 'EXT:cjf/mod1/locallang.php' );
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
$BE_USER->modAccess( $MCONF, 1 );

// Developer API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

// FPDF class
require_once ( t3lib_extMgm::extPath( 'fpdf' ) . 'class.tx_fpdf.php' );

// Extension configuration
$extConf = unserialize( $TYPO3_CONF_VARS[ 'EXT' ][ 'extConf' ][ 'cjf' ] );

// PDF label class
require_once ( t3lib_div::getFileAbsFileName( $extConf[ 'pdfLabelClass' ] ) );

class tx_cjf_module1 extends t3lib_SCbase
{
    // Page informations
    var $pageinfo;
    
    // Developer API version
    var $apimacmade_version = 2.8;
    
    // Extension tables
    var $extTables          = array(
        'events'         => 'tx_cjf_events',
        'clients'        => 'tx_cjf_clients',
        'orders'         => 'tx_cjf_orders',
        'bookings'       => 'tx_cjf_bookings',
        'bookings_sales' => 'tx_cjf_bookings_sales'
    );
    
    // Index of tables
    var $indexes            = array(
        'events'   => array(),
        'clients'  => array(),
        'orders'   => array(
            'events'  => array(),
            'clients' => array(),
            'id' => array()
        ),
        'bookings' => array(
            'events'  => array(),
            'clients' => array()
        )
    );
   
   // Compiled informations by client
   var $clientsInfos        = array();
    
    // Selected records
    var $records            = array(
        'events'   => array(),
        'clients'  => array(),
        'orders'   => array(),
        'bookings' => array()
    );
    
    // Date format
    var $dateFormat;
    
    // TS Config
    var $tsConfig = false;
    
    // Show only stats
    var $onlyStats = 0;
    
    /**
     * Initialization of the class.
     * 
     * @return      Void
     */
    function init()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        
        // Set date format
        $this->dateFormat = $TYPO3_CONF_VARS[ 'SYS' ][ 'ddmmyy' ] . ' / ' . $TYPO3_CONF_VARS[ 'SYS' ][ 'hhmm' ];
        
        // Store TS Config
        $this->tsConfig = t3lib_BEfunc::getModTSconfig( $this->id, 'mod.' . $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        // Check only stats flag
        if( is_array( $this->tsConfig ) && isset( $this->tsConfig[ 'properties' ][ 'onlyStats' ] ) && $this->tsConfig[ 'properties' ][ 'onlyStats' ] == 1 ) {
            
            // Show only stats
            $this->onlyStats = 1;
        }
        
        // Init
        parent::init();
    }
    
    /**
     * Creates the page.
     * 
     * This function creates the basic page in which the module will
     * take place.
     * 
     * @return      Void
     */
    function main()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        
        // New instance of the Developer API
        $this->api = new tx_apimacmade( $this );
        
        // Access check
        $this->pageinfo = t3lib_BEfunc::readPageAccess( $this->id, $this->perms_clause );
        $access = is_array( $this->pageinfo ) ? 1 : 0;
        
        if( ( $this->id && $access ) || ( $BE_USER->user[ 'admin' ] && !$this->id ) ) {
            
            // Draw the header
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = $BACK_PATH;
            $this->doc->form     = '<form action="" method="POST">';
            
            // JavaScript
            $this->doc->JScode = '
                <script type="text/javascript" language="Javascript" charset="iso-8859-1">
                    <!--
                    script_ended = 0;
                    function jumpToUrl(URL) {
                        document.location = URL;
                    }
                    //-->
                </script>
                <script src="scripts.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
                <script src="scripts.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
                <script src="../res/calendar/calendar.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
                <script src="../res/calendar/calendar-en.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
                <script src="../res/calendar/calendar-setup.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
                <link rel="stylesheet" href="../res/calendar/calendar.css" type="text/css" media="all" charset="utf-8" />
            ';
            $this->doc->postCode = '
                <script type="text/javascript" language="Javascript" charset="iso-8859-1">
                    <!--
                    script_ended = 1;
                    if(top.fsMod) {
                        top.fsMod.recentIds["web"] = ' . intval( $this->id ) . ';
                    }
                    //-->
                </script>
            ';
            
            // Init CSM menu
            $this->api->be_initCSM();
            
            // Build current path
            $headerSection = $this->doc->getHeader(
                'pages',
                $this->pageinfo,
                $this->pageinfo[ '_thePath' ]
            )
            . '<br>'
            . $LANG->sL( 'LLL:EXT:lang/locallang_core.php:labels.path' )
            . ': '
            . t3lib_div::fixed_lgd_pre( $this->pageinfo[ '_thePath' ], 50 );
            
            // Start page content
            $this->content .= $this->doc->startPage( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->header( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->spacer( 5 );
            $this->content .= $this->doc->section( '',
                                                   $this->doc->funcMenu( $headerSection,
                                                                         t3lib_BEfunc::getFuncMenu( $this->id,
                                                                                                    'SET[function]',
                                                                                                    $this->MOD_SETTINGS[ 'function' ],
                                                                                                    $this->MOD_MENU[ 'function' ]
                                                                         )
                                                   )
                              );
            $this->content .= $this->doc->divider( 5 );
            
            // Render content
            $this->moduleContent();
            
            // Adds shortcut
            if( $BE_USER->mayMakeShortcut() ) {
                $this->content .= $this->doc->spacer( 20 )
                                . $this->doc->section( '',
                                                       $this->doc->makeShortcutIcon( 'id',
                                                                                     implode( ',', array_keys( $this->MOD_MENU ) ),
                                                                                     $this->MCONF[ 'name' ]
                                                       )
                                  );
            }
            
            $this->content .= $this->doc->spacer(10);
            
        } else {
            
            // No access
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = $BACK_PATH;
            $this->content      .= $this->doc->startPage( $LANG->getLL( 'title' ) );
            $this->content      .= $this->doc->header( $LANG->getLL ( 'title' ) );
            $this->content      .= $this->doc->spacer( 5 );
            $this->content      .= $LANG->getLL( 'noaccess' );
            $this->content      .= $this->doc->spacer( 10 );
        }
    }
    
    /**
     * Creates the module's menu.
     * 
     * This function creates the module's menu.
     * 
     * @return		Void
     */
    function menuConfig()
    {
        global $LANG;
        
        // Check TS Config
        if( $this->onlyStats == 1 ) {
        
            // Only statistics
            $this->MOD_MENU = array (
                'function' => array (
                    '5' => $LANG->getLL( 'menu.func.5' ),
                )
            );
            
            // Force view
            $this->MOD_SETTINGS[ 'function' ] = 4;
        
        } else {
            
            // Menu items
            $this->MOD_MENU = array (
                'function' => array (
                    '1' => $LANG->getLL( 'menu.func.1' ),
                    '2' => $LANG->getLL( 'menu.func.2' ),
                    '3' => $LANG->getLL( 'menu.func.3' ),
                    '4' => $LANG->getLL( 'menu.func.4' ),
                    '5' => $LANG->getLL( 'menu.func.5' ),
                    '6' => $LANG->getLL( 'menu.func.6' ),
                    '7' => $LANG->getLL( 'menu.func.7' ),
                    '8' => $LANG->getLL( 'menu.func.8' ),
                )
            );
        }
        
        // Creates menu
        parent::menuConfig();
    }
    
    /**
     * Prints the page.
     * 
     * This function closes the page, and writes the final
     * rendered content.
     * 
     * @return      Void
     */
    function printContent()
    {
        $this->content .= $this->doc->endPage();
        echo( $this->content );
    }
    
    /**
     * Creates the module's content.
     * 
     * This function creates the module's content.
     * 
     * @return		Void
     */
    function moduleContent() {
        global $LANG;
        
        // GET data
        $GET = t3lib_div::_GET( $GLOBALS['MCONF']['name'] );
        
        // Check for a PDF output
        if( isset( $GET[ 'downloadPdf' ] ) ) {
            
            // File name
            $fileInfos = explode( '/', $GET[ 'downloadPdf' ] );
            $fileName  = array_pop( $fileInfos );
            
            // Check if file exists
            if( @is_file( $GET[ 'downloadPdf' ] ) ) {
                
                // Read PDF
                $handle = fopen( $GET[ 'downloadPdf' ], 'r' );
                $pdf = fread( $handle, filesize( $GET[ 'downloadPdf' ] ) );
                fclose( $handle );
                
                // Output PDF
                $this->api->div_output( $pdf, 'application/pdf', $fileName );
            }
        }
        
        // Storage
        $htmlCode = array();
        
        // Menu section
        $section = $this->MOD_SETTINGS[ 'function' ];
        
        // Check menu section
        if( $section == 8 ) {
            
            // Get clients by name
            $this->selectRecords( 'clients', 'type=1' );
            
            // Get events
            $this->selectRecords( 'events', 'sys_language_uid=0' );
            
        } elseif( $section == 7 ) {
            
            // Get clients by name
            $this->selectRecords( 'clients' );
            
            // Get orders
            $this->selectRecords( 'orders', 'type=0 AND confirmed=1', 'crdate DESC' );
            
            // Get events
            $this->selectRecords( 'events', 'sys_language_uid=0 AND price AND tickets' );
            
        } elseif( $section == 6 ) {
            
            // Get clients by name
            $this->selectRecords( 'clients' );
            
            // Get orders
            $this->selectRecords( 'orders' );
            
            // Get events
            $this->selectRecords( 'events', 'sys_language_uid=0 AND price AND tickets' );
            
        } elseif( $section == 5 ) {
            
            // Get clients by name
            $this->selectRecords( 'clients' );
            
            // Get bookings
            $this->selectRecords( 'bookings' );
            
            // Get events
            $this->selectRecords( 'events', 'sys_language_uid=0 AND price AND tickets', 'date,title' );
            
            // Get orders
            $this->selectRecords( 'orders' );
            
        } elseif( $section == 3 ) {
            
            // Get clients by name
            $this->selectRecords( 'clients', 'type=0', 'name_last' );
            
            // Get bookings
            $this->selectRecords( 'bookings' );
            
            // Get events
            $this->selectRecords( 'events', 'sys_language_uid=0' );
            
        } elseif( $section == 2 ) {
            
            // Get clients by date
            $this->selectRecords( 'clients', 'type=1', 'name_last,name_first' );
            
            // Get orders
            $this->selectRecords( 'orders' );
            
            // Get events
            $this->selectRecords( 'events', 'sys_language_uid=0' );
            
        } else {
            
            // Get clients by date
            $this->selectRecords( 'clients', 'type=1', 'crdate DESC' );
            
            // Get orders
            $this->selectRecords( 'orders' );
            
            // Get events
            $this->selectRecords( 'events', 'sys_language_uid=0' );
        }
        
        // Check data
        if( count( $this->records[ 'events' ] ) && count( $this->records[ 'clients' ] ) ) {
            
            // Check for POST variables
            if ($action = t3lib_div::_POST('action')) {
                
                // Process actions
                $this->processActions( $action );
            }
            
            // Check display mode
            switch( $section ) {
                
                // Show distributors
                case '3':
                    
                    // Show clients
                    $htmlCode[] = $this->showDistributors();
                    break;
                
                // Show distributors
                case '4':
                    
                    // Show clients
                    $htmlCode[] = 'Coming very soon... : )';
                    break;
                
                // Show distributors
                case '5':
                    
                    // Show clients
                    $htmlCode[] = $this->showStats();
                    break;
                
                // Show distributors
                case '6':
                    
                    // Show clients
                    $htmlCode[] = $this->findOrders();
                    break;
                
                // Print labels
                case '7':
                    
                    // Print labels
                    $htmlCode[] = $this->printLabels();
                    break;
                
                // Export emails
                case '8':
                    
                    // Print labels
                    $htmlCode[] = $this->exportEmails();
                    break;
                
                // Show clients
                default:
                    
                    // Show clients
                    $htmlCode[] = $this->showClients();
                    break;
            }
            
        } else {
            
            $res = $GLOBALS[ 'TYPO3_DB' ]->sql_query(
                'SELECT DISTINCT pid FROM ' . $this->extTables[ 'events' ]
            );
            
            if( $res && $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) ) {
                
                $htmlCode[] .= $LANG->getLL( 'norecord.list' );
                $htmlCode[] .= '<ol>';
                
                while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                    
                    $page = t3lib_BEfunc::getRecord( 'pages', $row[ 'pid' ] );
                    
                    $htmlCode[] .= '<li>';
                    $htmlCode[] .= $this->api->be_getRecordCSMIcon( 'pages', $page, $GLOBALS[ 'BACK_PATH' ] );
                    $htmlCode[] .= '<a href="' . t3lib_div::linkThisScript( array( 'id' => $page[ 'uid' ] ) ) . '" title="' . $page[ 'uid' ] . '">';
                    $htmlCode[] .= $page[ 'title' ];
                    $htmlCode[] .= '</a>';
                    $htmlCode[] .= '</li>';
                }
                
                $htmlCode[] .= '</ol>';
                
            } else {
                
                // No record found in page
                $htmlCode[] .= $LANG->getLL( 'norecord' );
            }
        }
        
        // Add content
        $this->content .= implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * 
     */
    function processActions( $action )
    {
        // Check action
        switch( $action ) {
            
            // Validate orders
            case 'validate':
                $this->validateOrders( 1 );
                break;
            
            // Unvalidate orders
            case 'unvalidate':
                $this->validateOrders( 0 );
                break;
            
            // Delete orders
            case 'delete':
                $this->deleteOrders();
                break;
            
            // Process orders
            case 'process':
                $this->processOrders();
                break;
        }
    }
    
    /**
     * 
     */
    function validateOrders( $value )
    {
        // POST data
        $POST = t3lib_div::_POST( $GLOBALS['MCONF']['name'] );
        
        // Check data
        if( is_array( $POST ) && isset( $POST[ 'list' ] ) ) {
            
            // Process orders
            foreach( $POST[ 'list' ] as $list ) {
                
                // List parts
                $listParts = explode( ':', $list );
                
                // Orders
                $orders = explode( ',', $listParts[ 1 ] );
                
                // Process each order
                foreach( $orders as $orderId ) {
                    
                    // Update order
                    $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'orders' ], 'uid=' . $orderId, array( 'confirmed' => $value ) );
                    $this->records[ 'orders' ][ $orderId ][ 'confirmed' ] = $value;
                }
            }
        }
    }
    
    /**
     * 
     */
    function processOrders()
    {
        // POST data
        $POST = t3lib_div::_POST( $GLOBALS['MCONF']['name'] );
        
        // Check data
        if( is_array( $POST ) && isset( $POST[ 'list' ] ) ) {
            
            // Process orders
            foreach( $POST[ 'list' ] as $list ) {
                
                // List parts
                $listParts = explode( ':', $list );
                
                // Orders
                $orders = explode( ',', $listParts[ 1 ] );
                
                // Process each order
                foreach( $orders as $orderId ) {
                    
                    // Update order
                    $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'orders' ], 'uid=' . $orderId, array( 'processed' => 1 ) );
                    $this->records[ 'orders' ][ $orderId ][ 'processed' ] = 1;
                }
            }
        }
    }
    
    /**
     * 
     */
    function deleteOrders()
    {
        // POST data
        $POST = t3lib_div::_POST( $GLOBALS['MCONF']['name'] );
        
        // Check data
        if( is_array( $POST ) && isset( $POST[ 'list' ] ) ) {
            
            // Process orders
            foreach( $POST[ 'list' ] as $list ) {
                
                // List parts
                $listParts = explode( ':', $list );
                
                // Orders
                $orders = explode( ',', $listParts[ 1 ] );
                
                // Process each order
                foreach( $orders as $orderId ) {
                    
                    // Get order
                    #$orderRow = t3lib_BEfunc::getRecord( $this->extTables[ 'orders' ], $orderId );
                    $orderRow =& $this->records[ 'orders' ][ $orderId ];
                    
                    // Get associated event
                    #$eventRow = t3lib_BEfunc::getRecord( $this->extTables[ 'events' ], $orderRow[ 'id_event' ] );
                    $eventRow =& $this->records[ 'events' ][ $orderRow[ 'id_event' ] ];
                    
                    // New number of sold tickets
                    $ticketsSold = $eventRow[ 'tickets_sold' ] - $orderRow[ 'quantity' ];
                    
                    // Update number of sold tickets on event
                    $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'events' ], 'uid=' . $eventRow[ 'uid' ], array( 'tickets_sold' => $ticketsSold ) );
                    $this->records[ 'events' ][ $orderRow[ 'id_event' ] ][ 'tickets_sold' ] = $ticketsSold;
                    
                    // Delete order
                    $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'orders' ], 'uid=' . $orderId, array( 'deleted' => 1 ) );
                    unset( $this->records[ 'orders' ][ $orderId ] );
                }
                // Delete client
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'clients' ], 'uid=' . $listParts[ 0 ], array( 'deleted' => 1 ) );
                unset( $this->records[ 'clients' ][ $listParts[ 0 ] ] );
            }
            
            // Clear cache of pages containing the FE plugin
            $this->clearPi1Pages();
        }
    }
    
    /**
     * 
     */
    function clearPi1Pages()
    {
        // Select pages containing PI1
        $pages = t3lib_BEfunc::getRecordsByField( 'pages', 'module', 'cjf' );
        
        // Check MySQL result
        if( is_array( $pages ) ) {
            
            // Process pages
            foreach( $pages as $row ) {
                
                // Clear cache for page
                $this->clearPageCache( $row[ 'uid' ] );
            }
        }
    }
    
    /**
     * 
     */
    function clearPageCache( $pid ) {
        
        // Delete page cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pages', 'page_id=' . $pid );
        
        // Delete page section cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pagesection', 'page_id=' . $pid );
    }
    
    /**
     * 
     */
    function selectRecords( $table, $addWhere = false, $orderBy = false )
    {
        // Where clause
        $where = 'pid=' . $this->id . t3lib_BEfunc::deleteClause( $this->extTables[ $table ] );
        
        // Add additionnal where clause if any
        $where .= ( !empty( $addWhere ) ) ? ' AND ' . $addWhere : ''; 
        
        // Get records
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( '*', $this->extTables[ $table ], $where, false, $orderBy );
        
        // Check MySQL result
        if( $res ) {
            
            // Process records
            while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                
                // Store record
                $this->records[ $table ][ $row[ 'uid' ] ] = $row;
                
                // Special processing for orders
                if( $table == 'orders' ) {
                    
                    // Check if index already exist
                    if( !isset( $this->indexes[ 'orders' ][ 'clients' ][ $row[ 'id_client' ] ] ) ) {
                        
                        // Create index
                        $this->indexes[ 'orders' ][ 'clients' ][ $row[ 'id_client' ] ] = array();
                    }
                    
                    // Check if index already exist
                    if( !isset( $this->indexes[ 'orders' ][ 'events' ][ $row[ 'id_event' ] ] ) ) {
                        
                        // Create index
                        $this->indexes[ 'orders' ][ 'events' ][ $row[ 'id_event' ] ] = array();
                    }
                    
                    // Check if index already exist
                    if( !isset( $this->indexes[ 'orders' ][ 'id' ][ $row[ 'orderid' ] ] ) ) {
                        
                        // Create index
                        $this->indexes[ 'orders' ][ 'id' ][ $row[ 'orderid' ] ] = array();
                    }
                    
                    // References to record
                    $this->indexes[ 'orders' ][ 'clients' ][ $row[ 'id_client' ] ][] = $row[ 'uid' ];
                    $this->indexes[ 'orders' ][ 'events' ][ $row[ 'id_event' ] ][] = $row[ 'uid' ];
                    $this->indexes[ 'orders' ][ 'id' ][ $row[ 'orderid' ] ][] = $row[ 'uid' ];
                }
                
                // Special processing for bookings
                if( $table == 'bookings' ) {
                    
                    // Check if index already exist
                    if( !isset( $this->indexes[ 'bookings' ][ 'clients' ][ $row[ 'id_client' ] ] ) ) {
                        
                        // Create index
                        $this->indexes[ 'bookings' ][ 'clients' ][ $row[ 'id_client' ] ] = array();
                    }
                    
                    // Check if index already exist
                    if( !isset( $this->indexes[ 'bookings' ][ 'events' ][ $row[ 'id_event' ] ] ) ) {
                        
                        // Create index
                        $this->indexes[ 'bookings' ][ 'events' ][ $row[ 'id_event' ] ] = array();
                    }
                    
                    // References to record
                    $this->indexes[ 'bookings' ][ 'clients' ][ $row[ 'id_client' ] ][] = $row[ 'uid' ];
                    $this->indexes[ 'bookings' ][ 'events' ][ $row[ 'id_event' ] ][] = $row[ 'uid' ];
                    $this->indexes[ 'bookings' ][ 'clients-events' ][ $row[ 'id_client' ] ][] = $row[ 'id_event' ];
                }
                
                // Special processing for events
                if( $table == 'events' ) {
                    
                    // Check if index already exist
                    if( !isset( $this->indexes[ 'events' ][ $row[ 'date' ] ] ) ) {
                        
                        // Create index
                        $this->indexes[ 'events' ][ $row[ 'date' ] ] = array();
                    }
                    
                    // Reference to record
                    $this->indexes[ 'events' ][ $row[ 'date' ] ][] = $row[ 'uid' ];
                }
            }
        }
    }
    
    /**
     * 
     */
    function showClients()
    {
        global $LANG;
        
        // Storage
        $htmlCode = array();
        
        // Start section
        $htmlCode[] = $this->doc->sectionBegin();
        
        // Title
        $htmlCode[] = $this->doc->sectionHeader( $LANG->getLL( 'section.clients' ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Counters
        $colorcount = 0;
        $rowcount   = 0;
        
        // Storage
        $btns = array();
        
        // Label
        $btns[] = $this->writeHTML( $LANG->getLL( 'actions' ), 'legend' );
        
        // GET data
        $GET = t3lib_div::_GET( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        // Icons
        $iconOk     = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_ok2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        $iconError  = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_fatalerror.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        $pdfIcon    = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/fileicons/pdf.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        $unhideIcon = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/button_unhide.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        $hideIcon   = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/button_hide.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        
        // Options
        $btns[] = $this->writeHTML( '<input type="radio" name="action" value="validate" checked>&nbsp;' . $LANG->getLL( 'action.validate' ) );
        $btns[] = $this->doc->spacer( 10 );
        $btns[] = $this->writeHTML( '<input type="radio" name="action" value="unvalidate">&nbsp;' . $LANG->getLL( 'action.unvalidate' ) );
        $btns[] = $this->doc->spacer( 10 );
        $btns[] = $this->writeHTML( '<input type="radio" name="action" value="delete">&nbsp;' . $LANG->getLL( 'action.delete' ), 'div', 'typo3-red' );
        $btns[] = $this->doc->spacer( 10 );
        $btns[] = $this->writeHTML( '<input type="radio" name="action" value="process">&nbsp;' . $LANG->getLL( 'action.process' ), 'div', false, array('color: #006600') );
        
        // Spacer
        $btns[] = $this->doc->spacer( 10 );
        
        // Submit
        $btns[] = '<input type="submit" value="' . $LANG->getLL('btn.submit') . '">';
        
        // Check / Unckeck all
        $btns[] = '<input type="button" value="' . $LANG->getLL('btn.checkall') . '" onclick="checkBoxList(document.forms[0].list_db)">';
        
        // Add buttons
        $htmlCode[] = $this->writeHTML(implode(chr(10),$btns),'fieldset');
        
        // Spacer
        $htmlCode[] = $this->doc->spacer(10);
        
        // Check processed view
        if( !isset( $GET[ 'showProcessed' ] ) || $GET[ 'showProcessed' ] == 0 ) {
            
            // Link to show processed orders
            $processedLink = t3lib_div::linkThisScript( array( $GLOBALS[ 'MCONF' ][ 'name' ] . '[showProcessed]' => 1 ) );
            
            // Add processed link
            $htmlCode[] = $this->writeHTML( $unhideIcon . '<a href="' . $processedLink . '">' . $LANG->getLL( 'showProcessed' ) . '</a>' );
            
        } else {
            
            // Link to show processed orders
            $processedLink = t3lib_div::linkThisScript( array( $GLOBALS[ 'MCONF' ][ 'name' ] . '[showProcessed]' => 0 ) );
            
            // Add processed link
            $htmlCode[] = $this->writeHTML( $hideIcon . '<a href="' . $processedLink . '">' . $LANG->getLL( 'showUnprocessed' ) . '</a>' );
        }
        
        // Spacer
        $htmlCode[] = $this->doc->spacer(10);
        
        // Start table
        $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
        
        // Headers style
        $headerStyle = array( 'font-weight: bold' );
        
        // Table headers
        $htmlCode[] = '<tr>';
        $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.name_last' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.name_first' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.crdate' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.orders' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.total' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.confirmed' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.type' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.orderid' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
        $htmlCode[] = '</tr>';
        
        // Process records
        foreach( $this->records[ 'clients' ] as $row ) {
            
            // Storage
            $orders = array();
            
            // Compile client informations
            $this->compileClientsInfos( $row[ 'uid' ] );
            
            // Processed state check
            $processCheck = ( isset( $GET[ 'showProcessed' ] ) && $GET[ 'showProcessed' ] ) ? $this->clientsInfos[ $row['uid'] ][ 'processed' ] == 1 : $this->clientsInfos[ $row['uid'] ][ 'processed' ] == 0;
            
            // Check if order is processed
            if( $processCheck ) {
                
                // Checkbox value
                $checkboxValue = $row[ 'uid' ] . ':' . implode( ',', $this->indexes[ 'orders' ][ 'clients' ][ $row[ 'uid' ] ] );
                
                // Storage
                $client = array();
                
                // Color
                $colorcount = ( $colorcount == 1 ) ? 0 : 1;
                $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                
                // TR parameters
                $tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
                
                // Checkbox
                $client[] = $this->writeHTML('<input type="checkbox" name="' . $GLOBALS['MCONF']['name'] . '[list][]" id="list_db" value="' . $checkboxValue . '">','td');
                
                // Client name
                $client[] = $this->writeHTML( $row[ 'name_last' ], 'td' );
                $client[] = $this->writeHTML( $row[ 'name_first' ], 'td' );
                
                // Order date
                $client[] = $this->writeHTML( date( $this->dateFormat, $row[ 'crdate' ] ), 'td' );
                
                // Process orders
                foreach( $this->clientsInfos[ $row['uid'] ][ 'orders' ] as $ordersInfos ) {
                    
                    // Storage
                    $order = array();
                    
                    // Order actions
                    $order[] = $this->writeHTML( $this->api->be_buildRecordIcons( 'show', $this->extTables[ 'events' ], $ordersInfos[ 'event' ][ 'uid' ] ), 'span' );
                    
                    // Add event title
                    $order[] = $this->writeHTML( $ordersInfos[ 'event' ][ 'title' ], 'span' );
                    
                    // Add quantity
                    $order[] = $this->writeHTML( '(' . $this->numberFormat( $ordersInfos[ 'order' ][ 'quantity' ] ) . ')', 'span', 'typo3-red' );
                    
                    // Add full order
                    $orders[] = $this->writeHTML( implode( chr( 10 ), $order ) );
                }
                
                // Add orders
                $client[] = $this->writeHTML( implode( chr( 10 ), $orders ), 'td' );
                
                // Order total
                $client[] = $this->writeHTML( $this->numberFormat( $this->clientsInfos[ $row['uid'] ][ 'total' ], 2 ), 'td' );
                
                // Confirmation state
                $confirmed = ( $this->clientsInfos[ $row['uid'] ][ 'confirmed' ] == 1 ) ? $iconOk : $iconError;
                
                // Payement confirmed
                $client[] = $this->writeHTML( $confirmed, 'td' );
                
                // Type
                $client[] = $this->writeHTML( $LANG->getLL( 'client.type.I.' . $this->clientsInfos[ $row['uid'] ][ 'orderType' ] ), 'td' );
                
                // Add order ID
                $client[] = $this->writeHTML( $this->clientsInfos[ $row['uid'] ][ 'orderId' ], 'td' );
                
                // PDF link
                $pdfLink = ( !empty( $this->clientsInfos[ $row['uid'] ][ 'pdf' ] ) ) ? '<a href="' . t3lib_div::linkThisScript( array( $GLOBALS[ 'MCONF' ][ 'name' ] . '[downloadPdf]' => $this->clientsInfos[ $row['uid'] ][ 'pdf' ] ) ) . '">' . $pdfIcon . '</a>' : '';
                
                // Actions
                $client[] = $this->writeHTML( $this->api->be_buildRecordIcons( 'show,edit', $this->extTables[ 'clients' ], $row[ 'uid' ] ) . $pdfLink, 'td' );
                
                // Add row
                $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $client ) . '</tr>';
                
                // Increase row count
                $rowcount++;
            }
        }
        
        // End table
        $htmlCode[] = '</table>';
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // End section
        $htmlCode[] = $this->doc->sectionEnd();
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * 
     */
    function showDistributors() {
        global $LANG;
        
        // Storage
        $htmlCode = array();
        
        // Start section
        $htmlCode[] = $this->doc->sectionBegin();
        
        // Title
        $htmlCode[] = $this->doc->sectionHeader( $LANG->getLL( 'section.distributors' ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // GET data
        $GET = t3lib_div::_GET( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        // Check post data
        if( isset( $GET[ 'editDistributor' ] ) && $GET[ 'editDistributor' ] > 0 ) {
            
            // Edit distributor
            $htmlCode[] = $this->editDistributor( $GET[ 'editDistributor' ] );
        }
        
        // Check post data
        if( isset( $GET[ 'editBooking' ] ) && $GET[ 'editBooking' ] > 0 ) {
            
            // Edit booking
            $htmlCode[] = $this->editBooking( $GET[ 'editBooking' ] );
        }
        
        // Check post data
        if( isset( $GET[ 'showBookingSales' ] ) && $GET[ 'showBookingSales' ] > 0 ) {
            
            // Edit booking
            $htmlCode[] = $this->showBookingSales( $GET[ 'showBookingSales' ] );
        }
        
        // Counters
        $colorcount = 0;
        $rowcount   = 0;
        
        // Start table
        $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
        
        // Headers style
        $headerStyle = array( 'font-weight: bold' );
        
        // Table headers
        $htmlCode[] = '<tr>';
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'distributor.name_last' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'distributor.tickets' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
        $htmlCode[] = '</tr>';
        
        // Process records
        foreach( $this->records[ 'clients' ] as $row ) {
            
            // Storage
            $client = array();
            
            // Color
            $colorcount = ( $colorcount == 1 ) ? 0 : 1;
            $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
            
            // TR parameters
            $tr_params = ' bgcolor="' . $color . '"';
            
            // Distributor name
            $client[] = $this->writeHTML( $row[ 'name_last' ], 'td' );
            
            // Bookings
            $client[] = $this->writeHTML( $this->showBookings( $row[ 'uid' ] ), 'td' );
            
            // Tickets icon
            $addTicketsIcon = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/options.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
            
            // Tickets link
            $addTicketsLink = t3lib_div::linkThisScript(
                array(
                    $GLOBALS[ 'MCONF' ][ 'name' ] . '[editDistributor]'   => $row[ 'uid' ],
                    $GLOBALS[ 'MCONF' ][ 'name' ] . '[editBooking]'       => 0,
                    $GLOBALS[ 'MCONF' ][ 'name' ] . '[showBookingSales]'  => 0,
                    $GLOBALS[ 'MCONF' ][ 'name' ] . '[deleteBookingSale]' => 0
                )
            );
            
            // Actions
            $client[] = $this->writeHTML( $this->api->be_buildRecordIcons( 'edit', $this->extTables[ 'clients' ], $row[ 'uid' ] ) . '<a href="' . $addTicketsLink . '">' . $addTicketsIcon . '</a>', 'td' );
            
            // Add row
            $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $client ) . '</tr>';
            
            // Increase row count
            $rowcount++;
        }
        
        // End table
        $htmlCode[] = '</table>';
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // End section
        $htmlCode[] = $this->doc->sectionEnd();
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * 
     */
    function editDistributor( $uid )
    {
        global $LANG;
        
        // Action
        $action = $this->bookTickets( $uid );
        
        // Get events
        #$events = t3lib_BEfunc::getRecordsByField( $this->extTables[ 'events' ], 'pid', $this->id, ' AND sys_language_uid=0', '', 'title' );
        $events =& $this->records[ 'events' ];
        
        // Distributor row
        $distributor =& $this->records[ 'clients' ][ $uid ];
        
        // Check for events
        if( is_array( $events ) ) {
            
            // Storage
            $htmlCode      = array();
            $eventsSelect  = array();
            
            // Title
            $htmlCode[] = $this->writeHTML( '<strong>' . $distributor[ 'name_last' ] . '</strong>', $eventsSelect );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Action
            $htmlCode[] = '<input name="action" type="hidden" value="booking">';
            
            // Process actions
            $htmlCode[] = $action;
            
            // Number inout
            #$numberInput = '<input name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[quantity]" type="text" size="5">';
            
            // Start select
            $eventsSelect[] = '<select name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[event]">';
            
            // POST data, if any
            $POST = t3lib_div::_POST( $GLOBALS[ 'MCONF' ][ 'name' ] );
            
            // Selected id, if available
            $selectedId = ( isset( $POST[ 'event' ] ) ) ? $POST[ 'event' ] : 0;
            
            // Process events
            foreach( $events as $event ) {
                
                // Check if tickets can be booked for this event
                if( !empty( $event[ 'price' ] ) && ( ( $event[ 'tickets' ] - $event[ 'tickets_sold' ] ) - $event[ 'tickets_booked' ] ) > 0 ) {
                    
                    // Check if a booking alrady exists
                    if( !isset( $this->indexes[ 'bookings' ][ 'clients-events' ][ $uid ] ) || !in_array( $event[ 'uid' ], $this->indexes[ 'bookings' ][ 'clients-events' ][ $uid ] ) ) {
                        
                        // Selected state
                        $selected = ( $selectedId == $event[ 'uid' ] ) ? ' selected' : '';
                        
                        // Add option
                        $eventsSelect[] = '<option value="' . $event[ 'uid' ] . '"' . $selected . '>' . $event[ 'title' ] . ' ' . date( 'd.m.Y', $event[ 'date' ] ) . '</option>';
                    }
                }
            }
            
            // End select
            $eventsSelect[] = '</select>';
            
            // Submit
            $submit = ' <input name="submit" type="submit" value="' . $LANG->getLL( 'ok' ) . '">';
            
            // Add select tag for events
            $htmlCode[] = $this->writeHTML( implode( chr( 10 ), $eventsSelect ) . $submit );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Divider
            $htmlCode[] = $this->doc->divider( 5 );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Return content
            return implode( chr( 10 ), $htmlCode );
        }
    }
    
    /**
     * 
     */
    function editBooking( $uid )
    {
        global $LANG;
        
        // Action
        $action = $this->unbookTickets( $uid );
        
        // Get records
        #$booking = t3lib_BEfunc::getRecord( $this->extTables[ 'bookings' ], $uid );
        #$distributor = t3lib_BEfunc::getRecord( $this->extTables[ 'clients' ], $booking[ 'id_client' ] );
        #$event   = t3lib_BEfunc::getRecord( $this->extTables[ 'events' ], $booking[ 'id_event' ] );
        $booking =& $this->records[ 'bookings' ][ $uid ];
        $distributor =& $this->records[ 'clients' ][ $booking[ 'id_client' ] ];
        $event   =& $this->records[ 'events' ][ $booking[ 'id_event' ] ];
        
        // Storage
        $htmlCode = array();
        
        // Tickets that can be unbooked
        #$freeTickets = $booking[ 'tickets_booked' ] - $booking[ 'tickets_sold' ];
        
        // Title
        $htmlCode[] = $this->writeHTML( '<strong>' . $distributor[ 'name_last' ] . ': ' . $event[ 'title' ] .'</strong>' );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Action
        $htmlCode[] = '<input name="action" type="hidden" value="unbooking">';
        
        // Process actions
        $htmlCode[] = $action;
        
        // Add label
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'distributor.ticketsSold' ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 5 );
        
        // Inputs storage
        $inputs = array();
        
        // Input IDs
        $idSold = uniqid( 'INPUT' );
        $idAdd  = uniqid( 'INPUT' );
        $idDate = uniqid( 'INPUT' );
        $idCal  = uniqid( 'INPUT' );
        
        // Add icon
        $iconAdd  = '<img '
                  . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/goback.gif', '' )
                  . ' alt="" hspace="0" vspace="0" border="0" align="top">';
        
        // Calendar icon size
        $calSize  = getimagesize( t3lib_extMgm::extPath( 'cjf' ) . '/res/date.png' );
        
        // Calendar icon
        $iconCal  = '<img src="../res/date.png" '
                    . $calSize[ 3 ]
                    . ' alt="" hspace="0" vspace="0" border="0" align="top" id="' . $idCal . '" />';
        
        // Tickets sold
        #$inputs[] = '<input name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[ticketsBooked]" readonly="readonly" id="' . $idSold . '" type="text" value="' . $booking[ 'tickets_booked' ] . '" size="10" />';
        
        // Image
        #$inputs[] = '<a href="#" onclick="javascript:addNumberToInput(\'' . $idSold . '\',\'' . $idAdd . '\');">' . $iconAdd . '</a>';
        
        // Add tickets
        $inputs[] = '<input name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[addTickets]" id="' . $idAdd . '" type="text" value="0" size="10" />';
        
        // Date
        $inputs[] = '<input name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[date]" readonly="readonly" id="' . $idDate . '" type="text" value="' . date( 'm/d/y' ) . '" size="10" />' . $iconCal;
        
        // Calendar setup
        $inputs[] = '<script type="text/javascript" charset="utf-8">'
	              . chr( 10 )
	              . '// <![CDATA['
	              . chr( 10 )
	              . 'Calendar.setup('
	              . chr( 10 )
	              . '{'
	              . chr( 10 )
	              . 'inputField  : "'
	              . $idDate
	              . '",'
	              . chr( 10 )
	              . 'ifFormat    : "%m/%e/%y",'
	              . chr( 10 )
	              . 'button      : "'
	              . $idCal
	              . '",'
	              . chr( 10 )
	              . 'align       : "Br",'
	              . chr( 10 )
	              . 'singleClick : true'
	              . chr( 10 )
	              . '}'
	              . chr( 10 )
	              . ');'
	              . chr( 10 )
	              . '// ]]>'
	              . chr( 10 )
	              . '</script>';
        
        // Add inputs
        $htmlCode[] = $this->writeHTML( implode( chr( 10 ), $inputs ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 5 );
        
        // Submit
        $htmlCode[] = $this->writeHTML( '<input name="submit" type="submit" value="' . $LANG->getLL( 'ok' ) . '">' );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Divider
        $htmlCode[] = $this->doc->divider( 5 );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Show booking sales
        $htmlCode[] = $this->showBookingSales( $uid );
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * 
     */
    function unbookTickets()
    {
        global $LANG;
        
        // Check action
        if( t3lib_div::_POST( 'action' ) == 'unbooking' ) {
            
            // Data
            $POST = t3lib_div::_POST( $GLOBALS[ 'MCONF' ][ 'name' ] );
            $GET  = t3lib_div::_GET( $GLOBALS[ 'MCONF' ][ 'name' ] );
            
            // Check quantity
            if( is_numeric( $POST[ 'addTickets' ] ) && $POST[ 'addTickets' ] > 0) {
            
                $time = time();
                
                // Get booking
                #$booking = t3lib_BEfunc::getRecord( $this->extTables[ 'bookings' ], $GET[ 'editBooking' ] );
                $booking =& $this->records[ 'bookings' ][ $GET[ 'editBooking' ] ];
                
                // Get event
                #$event = t3lib_BEfunc::getRecord( $this->extTables[ 'events' ], $booking[ 'id_event' ] );
                $event =& $this->records[ 'events' ][ $booking[ 'id_event' ] ];
                
                // Tickets that can be unbooked
                #$freeTickets = $booking[ 'tickets_booked' ] - $booking[ 'tickets_sold' ];
                
                // Fields to update in event
                $eventFields = array(
                    'tstamp'         => $time,
                    'tickets_booked' => $event[ 'tickets_booked' ] + $POST[ 'addTickets' ]
                );
                
                // Fields to update in booking
                $bookingFields = array(
                    'tstamp'         => $time,
                    'tickets_booked' => $booking[ 'tickets_booked' ] + $POST[ 'addTickets' ]
                );
                
                // Fields to insert in bookings sales
                $bookingsSalesFields = array(
                    'pid'          => $this->id,
                    'crdate'       => $time,
                    'tstamp'       => $time,
                    'cruser_id'    => $GLOBALS[ 'BE_USER' ]->user[ 'uid' ],
                    'id_booking'   => $booking[ 'uid' ],
                    'sale_date'    => strtotime( $POST[ 'date' ] ),
                    'tickets_sold' => $POST[ 'addTickets' ]
                );
                
                // Update records
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'events' ], 'uid=' . $event[ 'uid' ], $eventFields );
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'bookings' ], 'uid=' . $booking[ 'uid' ], $bookingFields );
                $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( $this->extTables[ 'bookings_sales' ], $bookingsSalesFields );
                $this->records[ 'events' ][ $event[ 'uid' ] ][ 'tickets_booked' ] = $eventsFields[ 'tickets_booked' ];
                $this->records[ 'bookings' ][ $booking[ 'uid' ] ][ 'tickets_booked' ] = $bookingFields[ 'tickets_booked' ];
                
            } else {
                
                // Error
                return $this->writeHTML( $LANG->getLL( 'quantityNotNumeric' ), 'div', 'typo3-red' );
            }
        }
    }
    
    /**
     * 
     */
    function bookTickets( $distributorId )
    {
        global $LANG;
        
        // Check action
        if( t3lib_div::_POST( 'action' ) == 'booking' ) {
            
            // Post data
            $POST = t3lib_div::_POST( $GLOBALS[ 'MCONF' ][ 'name' ] );
            
            // Check for an event
            if( !empty( $POST[ 'event' ] ) ) {
                
                // Get event
                #$event = t3lib_BEfunc::getRecord( $this->extTables[ 'events' ], $POST[ 'event' ] );
                $event =& $this->records[ 'events' ][ $POST[ 'event' ] ];
                
                // Current time
                $time = time();
                
                // Fields to update in booking
                $fieldsBooking = array(
                    'crdate'         => $time,
                    'cruser_id'      => $GLOBALS[ 'BE_USER' ]->user[ 'uid' ],
                    'tstamp'         => $time,
                    'hidden'         => 0,
                    'deleted'        => 0,
                    'pid'            => $this->id,
                    'id_client'      => $distributorId,
                    'id_event'       => $event[ 'uid' ],
                    'tickets_booked' => 0,
                );
                
                // Insert booking
                $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( $this->extTables[ 'bookings' ], $fieldsBooking );
                
                // Inserted booking id
                $newBookId = $GLOBALS[ 'TYPO3_DB' ]->sql_insert_id();
                
                // Add record to internal storage
                $this->records[ 'bookings' ][ $newBookId ] = t3lib_BEfunc::getRecord( $this->extTables[ 'bookings' ], $newBookId );
                
                // Check bookings index
                if( !isset( $this->indexes[ 'bookings' ][ 'clients' ][ $distributorId ] ) ) {
                    
                    // Create index for distributor
                    $this->indexes[ 'bookings' ][ 'clients' ][ $distributorId ] = array();
                }
                
                // Check bookings index
                if( !isset( $this->indexes[ 'bookings' ][ 'clients-events' ][ $distributorId ] ) ) {
                    
                    // Create index for distributor
                    $this->indexes[ 'bookings' ][ 'clients' ][ $distributorId ] = array();
                }
                
                // Add index
                $this->indexes[ 'bookings' ][ 'clients' ][ $distributorId ][]        = $newBookId;
                $this->indexes[ 'bookings' ][ 'clients-events' ][ $distributorId ][] = $event[ 'uid' ];
                
                // Clear plugins pages
                $this->clearPi1Pages();
            }
        }
    }
    
    /**
     * 
     */
    function showBookings( $distributorId )
    {
        // Check bookings
        if( isset( $this->indexes[ 'bookings' ][ 'clients' ][ $distributorId ] ) ) {
            
            // Reference to booking index
            $bookings =& $this->indexes[ 'bookings' ][ 'clients' ][ $distributorId ];
            
            // Storage
            $htmlCode = array();
            
            // Process each booking
            foreach( $bookings as $id ) {
                
                // Booking row
                $row =& $this->records[ 'bookings' ][ $id ];
                
                // Storage
                $book = array();
                
                // Get associated event
                $event = $this->records[ 'events' ][ $row[ 'id_event' ] ];
                
                $showBookingSalesIcon = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/zoom2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
                
                $showBookingSalesLink = t3lib_div::linkThisScript(
                    array(
                        $GLOBALS[ 'MCONF' ][ 'name' ] . '[showBookingSales]'  => $row[ 'uid' ],
                        $GLOBALS[ 'MCONF' ][ 'name' ] . '[editDistributor]'   => 0,
                        $GLOBALS[ 'MCONF' ][ 'name' ] . '[editBooking]'       => 0,
                        $GLOBALS[ 'MCONF' ][ 'name' ] . '[deleteBookingSale]' => 0
                    )
                );
                
                $book[] = $this->writeHTML( '<a href="' . $showBookingSalesLink . '">' . $showBookingSalesIcon . '</a>', 'span' );
                
                // Add edit icons
                #$book[] = $this->writeHTML( $this->api->be_buildRecordIcons( 'show', $this->extTables[ 'events' ], $event[ 'uid' ] ), 'span' );
                
                // Add event tile
                $book[] = $this->writeHTML( date( 'd.m.Y', $event[ 'date' ] ) . ' - ' . $event[ 'title' ], 'span' );
                
                // Add quantity
                $book[] = $this->writeHTML( '(' . $this->numberFormat( $row[ 'tickets_booked' ] ) . ')', 'span', 'typo3-red' );
                
                // Tickets icon
                $addTicketsIcon = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/options.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
                
                // Tickets link
                $addTicketsLink = t3lib_div::linkThisScript( array( $GLOBALS[ 'MCONF' ][ 'name' ] . '[editBooking]' => $row[ 'uid' ], $GLOBALS[ 'MCONF' ][ 'name' ] . '[editDistributor]' => 0, $GLOBALS[ 'MCONF' ][ 'name' ] . '[showBookingSales]' => 0 ) );
                
                // Actions
                $book[] = $this->writeHTML( '<a href="' . $addTicketsLink . '">' . $addTicketsIcon . '</a>', 'span' );
                
                // Array key
                $key = $event[ 'date' ] . '-' . $event[ 'title' ];
                
                // Add booking
                $htmlCode[ $key ] = $this->writeHTML( implode( chr( 10 ), $book ) );
            }
            
            // Sort the bookings by event date
            ksort( $htmlCode );
            
            // Return code
            return implode( chr( 10 ), $htmlCode );
        }
    }
    
    /**
     * 
     */
    function showBookingSales( $bookingId )
    {
        global $LANG;
        
        $GET = t3lib_div::_GET( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        if( $GET[ 'deleteBookingSale' ] ) {
            
            $deleteRow = t3lib_BEfunc::getRecord(
                $this->extTables[ 'bookings_sales' ],
                $GET[ 'deleteBookingSale' ]
            );
            
            if( is_array( $deleteRow ) ) {
                
                $time = time();
                
                $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery(
                    $this->extTables[ 'bookings_sales' ],
                    'uid=' . $GET[ 'deleteBookingSale' ]
                );

                $booking = $this->records[ 'bookings' ][ $bookingId ];
                $event   = $this->records[ 'events' ][ $booking[ 'id_event' ] ];
                
                $eventFields = array(
                    'tstamp'         => $time,
                    'tickets_booked' => $event[ 'tickets_booked' ] - $deleteRow[ 'tickets_sold' ]
                );
                
                // Fields to update in booking
                $bookingFields = array(
                    'tstamp'         => $time,
                    'tickets_booked' => $booking[ 'tickets_booked' ] - $deleteRow[ 'tickets_sold' ]
                );
                
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                    $this->extTables[ 'events' ],
                    'uid=' . $event[ 'uid' ],
                    $eventFields
                );
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                    $this->extTables[ 'bookings' ],
                    'uid=' . $booking[ 'uid' ],
                    $bookingFields
                );
                
                $this->records[ 'events' ][ $event[ 'uid' ] ][ 'tickets_booked' ] = $eventsFields[ 'tickets_booked' ];
                $this->records[ 'bookings' ][ $booking[ 'uid' ] ][ 'tickets_booked' ] = $bookingFields[ 'tickets_booked' ];
            }
        }
        
        // Get booking sales
        $sales = t3lib_BEfunc::getRecordsByField(
            $this->extTables[ 'bookings_sales' ],
            'id_booking',
            $bookingId,
            '',
            '',
            'sale_date DESC'
        );
        
        // Check for events
        if( is_array( $sales ) ) {
        
            // Storage
            $htmlCode = array();
            
            // Counters
            $colorcount = 0;
            $rowcount   = 0;
            
            // Start table
            $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
            
            // Headers style
            $headerStyle = array( 'font-weight: bold' );
            
            // Table headers
            $htmlCode[] = '<tr>';
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'bookings_sales.sale_date' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'bookings_sales.tickets_sold' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
            $htmlCode[] = '</tr>';
            
            // Process records
            foreach( $sales as $sale ) {
                
                // Storage
                $row = array();
                
                // Color
                $colorcount = ( $colorcount == 1 ) ? 0 : 1;
                $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                
                // TR parameters
                $tr_params = ' bgcolor="' . $color . '"';
                
                $iconTrash = '<img '
                           . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/delete_record.gif', '' )
                           . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
                
                $trashLink = t3lib_div::linkThisScript(
                    array(
                        $GLOBALS[ 'MCONF' ][ 'name' ] . '[editBooking]'       => $GET[ 'editBooking' ],
                        $GLOBALS[ 'MCONF' ][ 'name' ] . '[editDistributor]'   => 0,
                        $GLOBALS[ 'MCONF' ][ 'name' ] . '[showBookingSales]'  => $GET[ 'showBookingSales' ],
                        $GLOBALS[ 'MCONF' ][ 'name' ] . '[deleteBookingSale]' => $sale[ 'uid' ]
                    )
                );
                
                // Distributor name
                $row[] = $this->writeHTML( date( 'd.m.Y', $sale[ 'sale_date' ] ), 'td' );
                
                // Bookings
                $row[] = $this->writeHTML( $sale[ 'tickets_sold' ], 'td' );
                
                // Delete
                $row[] = $this->writeHTML( $this->writeHTML( '<a href="' . $trashLink . '">' . $iconTrash . '</a>', 'span' ), 'td' );
                
                // Add row
                $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $row ) . '</tr>';
                
                // Increase row count
                $rowcount++;
            }
            
            // End table
            $htmlCode[] = '</table>';
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Divider
            $htmlCode[] = $this->doc->divider( 5 );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Return content
            return implode( chr( 10 ), $htmlCode );
        }
    }
    
    /**
     * 
     */
    function compileClientsInfos( $uid )
    {
        // Storage
        $infos = array();
        
        // Client row
        $infos[ 'client' ] =& $this->records[ 'clients' ][ $uid ];
        
        // Check orders index
        if( isset( $this->indexes[ 'orders' ][ 'clients' ][ $uid ] ) ) {
            
            // Orders total
            $total = 0;
            
            // Orders confirmation
            $confirmed = 0;
            
            // Orders storage
            $orders = array();
            
            // Process orders
            foreach( $this->indexes[ 'orders' ][ 'clients' ][ $uid ] as $orderId ) {
                
                // Reference to order
                $orderRow =& $this->records[ 'orders' ][ $orderId ];
                
                // Reference to event
                $eventRow =& $this->records[ 'events' ][ $orderRow[ 'id_event' ] ];
                
                // Add confirmation state for the current order
                $confirmed = $orderRow[ 'confirmed' ];
                
                // PDF file
                $pdf = $orderRow[ 'pdf' ];
                
                // Add order & event informations
                $orders[] = array(
                    'order' => $orderRow,
                    'event' => $eventRow
                );
                
                // Confirmation
                $confirmed = $orderRow[ 'confirmed' ];
                
                // Type
                $type = $orderRow[ 'type' ];
                
                // Processed state
                $processed = $orderRow[ 'processed' ];
                
                // Order ID
                $orderId = $orderRow[ 'orderid' ];
                
                // Increase total
                $total += $orderRow[ 'total' ];
            }
            
            // Add order informations
            $infos[ 'orders' ] = $orders;
        }
        
        // Add PDF file
        $infos[ 'pdf' ] = $pdf;
        
        // Add total
        $infos[ 'total' ] = $total;
        
        // Add confirmation state
        $infos[ 'confirmed' ] = $confirmed;
        
        // Add order type
        $infos[ 'orderType' ] = $type;
        
        // Add processed state
        $infos[ 'processed' ] = $processed;
        
        // Add order ID
        $infos[ 'orderId' ] = $orderId;
        
        // Store compiled informations
        $this->clientsInfos[ $uid ] = $infos;
    }
    
    /**
     * 
     */
    function csvExport( $eventId )
    {
        global $LANG;
        
        $orders = $this->indexes[ 'orders' ][ 'events' ][ $eventId ];
        $event  = $this->records[ 'events' ][ $eventId ];
        $csv    = array();
        
        $headers = array(
            $LANG->getLL( 'client.name_last' ),
            $LANG->getLL( 'client.name_first' ),
            $LANG->getLL( 'client.email' ),
            $LANG->getLL( 'client.confirmed' ),
            $LANG->getLL( 'stats.type' ),
            $LANG->getLL( 'stats.quantity' ),
            $LANG->getLL( 'stats.total' ),
            $LANG->getLL( 'client.crdate' )
        );
        
        $csv[] = $event[ 'title' ] . ' - ' . date( $this->dateFormat, $event[ 'date' ] );
        $csv[] = implode( chr( 9 ), $headers );
        
        foreach( $orders as $orderId ) {
            
            $row    = $this->records[ 'orders' ][ $orderId ];
            $client = $this->records[ 'clients' ][ $row[ 'id_client' ] ];
            $data   = array();
            
            $data[] = $client[ 'name_first' ];
            $data[] = $client[ 'name_last' ];
            $data[] = $client[ 'email' ];
            $data[] = ( $row[ 'confirmed' ] ) ? $LANG->getLL( 'yes' ) : $LANG->getLL( 'no' );
            $data[] = $LANG->getLL( 'stats.type.I.' . $row[ 'type' ] );
            $data[] = $this->numberFormat( $row[ 'quantity' ] );
            $data[] = $this->numberFormat( $row[ 'total' ], 2 );
            $data[] = date( $this->dateFormat, $row[ 'crdate' ] );
            
            $csv[]  = implode( chr( 9 ), $data );
        }
        
        tx_apimacmade::div_output(
            implode( chr( 10 ), $csv ),
            'text/csv',
            'export.csv'
        );
    }
    
    /**
     * 
     */
    function showStats()
    {
        global $LANG;
        
        // Post data
        $POST = t3lib_div::_POST( $GLOBALS['MCONF']['name'] );
        
        // Post data
        $GET  = t3lib_div::_GET( $GLOBALS['MCONF']['name'] );
        
        // Checks for a CSV export
        if( isset( $GET[ 'csvExport' ] ) && isset( $this->indexes[ 'orders' ][ 'events' ][ $GET[ 'csvExport' ] ] ) ) {
            
            // Export event as CSV
            $this->csvExport( $GET[ 'csvExport' ] );
        }
        
        // Storage
        $htmlCode = array();
        
        // Start section
        $htmlCode[] = $this->doc->sectionBegin();
        
        // Title
        $htmlCode[] = $this->doc->sectionHeader( $LANG->getLL( 'section.stats' ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Note
        $htmlCode[] = $this->writeHTML( $this->writeHTML( $LANG->getLL( 'stats.note' ), 'strong' ), 'div', 'typo3-red' );
        $htmlCode[] = $this->writeHTML( $this->writeHTML( $LANG->getLL( 'stats.note.text' ), 'strong' ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Counters
        $reallySold = array(
            'distributors' => 0,
            'online'   => 0,
            'total'    => 0
        );
        
        // Get number of tickets really sold online
        foreach( $this->records[ 'orders' ] as $key => $value ) {
            
            // Check confirmation
            if( $value[ 'confirmed' ] == 1 ) {
                
                // Add order
                $reallySold[ 'online' ] += $value[ 'quantity' ];
                $reallySold[ 'total' ]  += $value[ 'quantity' ];
            }
        }
        
        // Get number of tickets really sold by distributors
        foreach( $this->records[ 'bookings' ] as $key => $value ) {
            
            // Add order
            $reallySold[ 'distributors' ] += $value[ 'tickets_booked' ];
            $reallySold[ 'total' ]    += $value[ 'tickets_booked' ];
        }
        
        // Total number of tickets sold (really)
        $htmlCode[] = $this->writeHTML( $this->writeHTML( sprintf( $LANG->getLL( 'stats.reallySold' ), $reallySold[ 'total' ], $reallySold[ 'online' ], $reallySold[ 'distributors' ] ), 'strong' ), 'div', 'typo3-red' );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Storage
        $btns = array();
        
        // Label
        $btns[] = $this->writeHTML( $LANG->getLL( 'actions' ), 'legend' );
        
        // Icons
        $iconOk    = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_ok2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        $iconError = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_fatalerror.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        $iconCsv   = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/fileicons/csv.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        
        // Options
        $btns[] = $this->writeHTML( '<input type="checkbox" name="' . $GLOBALS['MCONF']['name'] . '[details][]" value="bookings">&nbsp;' . $LANG->getLL( 'stats.showBookings' ) );
        $btns[] = $this->doc->spacer( 10 );
        $btns[] = $this->writeHTML( '<input type="checkbox" name="' . $GLOBALS['MCONF']['name'] . '[details][]" value="orders">&nbsp;' . $LANG->getLL( 'stats.showOrders' ) );
        
        // Spacer
        $btns[] = $this->doc->spacer( 10 );
        
        // Submit
        $btns[] = '<input type="submit" value="' . $LANG->getLL('stats.submit') . '">';
        
        // Check / Unckeck all
        $btns[] = '<input type="button" value="' . $LANG->getLL('btn.checkall') . '" onclick="checkBoxList(document.forms[0].list_db)">';
        
        // Add buttons
        $htmlCode[] = $this->writeHTML(implode(chr(10),$btns),'fieldset');
        
        // Spacer
        $htmlCode[] = $this->doc->spacer(10);
        
        // Process dates
        foreach( $this->indexes[ 'events' ] as $key => $value ) {
        
            // Counters
            $colorcount = 0;
            $rowcount   = 0;
            
            // Add date
            $htmlCode[] = $this->writeHTML( $this->writeHTML( date( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'ddmmyy' ], $key ), 'strong' ) );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Start table
            $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
            
            // Headers style
            $headerStyle = array( 'font-weight: bold' );
            
            // Storage
            $headers = array();
            
            // Table headers
            $headers[] = '<tr>';
            $headers[] = $this->writeHTML( '', 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( $LANG->getLL( 'stats.event' ), 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( $LANG->getLL( 'stats.tickets' ), 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( $LANG->getLL( 'stats.price' ), 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( $LANG->getLL( 'stats.total' ), 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( $LANG->getLL( 'stats.sold' ), 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( $LANG->getLL( 'stats.booked' ), 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( $LANG->getLL( 'stats.totalSold' ), 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( $LANG->getLL( 'stats.available' ), 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( '', 'td', false, $headerStyle );
            $headers[] = $this->writeHTML( '', 'td', false, $headerStyle );
            $headers[] = '</tr>';
            
            // Full headers
            $fullHeaders = implode( chr( 10 ), $headers );
            
            // Add headers
            $htmlCode[] = $fullHeaders;
            
            // Number of events
            $eventsCount = count( $value );
            
            // Process events
            foreach( $value as $eventId ) {
                
                // Event row
                $row = $this->records[ 'events' ][ $eventId ];
                
                // CSV link
                $csvLink = '<a href="'
                         . t3lib_div::linkThisScript(
                            array(
                                $GLOBALS[ 'MCONF' ][ 'name' ] . '[csvExport]' => $row[ 'uid' ]
                            )
                         )
                         . '">'
                         . $iconCsv
                         . '</a>';
                
                // Storage
                $event = array();
                
                // Color
                $colorcount = ( $colorcount == 1 ) ? 0 : 1;
                $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                
                // TR parameters
                $tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
                
                // Checkbox
                $event[] = $this->writeHTML('<input type="checkbox" name="' . $GLOBALS['MCONF']['name'] . '[list][]" id="list_db" value="' . $row[ 'uid' ] . '">','td');
                
                // Event title
                $event[] = $this->writeHTML( $row[ 'title' ], 'td' );
                
                // Number of tickets
                $event[] = $this->writeHTML( $this->numberFormat( $row[ 'tickets' ] ), 'td' );
                
                // Price
                $event[] = $this->writeHTML( $this->numberFormat( $row[ 'price' ], 2 ), 'td' );
                
                // Total price
                $event[] = $this->writeHTML( $this->numberFormat( $row[ 'tickets' ] * $row[ 'price' ], 2 ), 'td' );
                
                // Tickets sold
                $event[] = $this->writeHTML( $this->numberFormat( $row[ 'tickets_sold' ] ), 'td' );
                
                // Tickets booked
                $event[] = $this->writeHTML( $this->numberFormat( $row[ 'tickets_booked' ] ), 'td' );
                
                // Total tickets sold
                $event[] = $this->writeHTML( $this->numberFormat( $row[ 'tickets_sold' ] + $row[ 'tickets_booked' ] ), 'td' );
                
                // Available tickets
                $event[] = $this->writeHTML( $this->numberFormat( ( $row[ 'tickets' ] - $row[ 'tickets_sold' ] ) - $row[ 'tickets_booked' ] ), 'td' );
                
                // Actions
                $editActions = ( $this->onlyStats == 1 ) ? '' : $this->api->be_buildRecordIcons( 'edit', $this->extTables[ 'events' ], $row[ 'uid' ] );
                
                // Event actions
                $event[] = $this->writeHTML( $editActions, 'td' );
                
                // CSV export
                $event[] = $this->writeHTML( $csvLink, 'td' );
                
                // Add row
                $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $event ) . '</tr>';
                
                // Check POST data
                if( isset( $POST[ 'details' ] ) && count( $POST[ 'details' ] ) && isset( $POST[ 'list' ] ) && in_array( $row[ 'uid' ], $POST[ 'list' ] ) ) {
                    
                    // Add details
                    $htmlCode[] = $this->showStatsDetails( $row[ 'uid' ], $color );
                    
                    // Repeat headers if necessary
                    if( $rowcount < $eventsCount - 1 ) {
                        $htmlCode[] = $fullHeaders;
                    }
                }
                
                // Increase row count
                $rowcount++;
            }
            
            // End table
            $htmlCode[] = '</table>';
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
        }
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * 
     */
    function showStatsDetails( $eventId, $color )
    {
        // Storage
        $htmlCode = array();
        
        // Post data
        $POST = t3lib_div::_POST( $GLOBALS['MCONF']['name'] );
        
        // Empty row
        $htmlCode[] = $this->writeHTML( '', 'td', false, false, array( 'colspan' => '2' ) );
        
        // Colspan
        $colspan = ( count( $POST[ 'details' ] ) == 2 ) ? '3' : '6';
        
        // Display booking details?
        if( in_array( 'bookings', $POST[ 'details' ] ) ) {
            
            // Build booking details
            $booking = $this->bookingsStatsDetails( $eventId );
            
            // Add details
            $htmlCode[] = $this->writeHTML( $booking, 'td', false, false, array( 'colspan' => $colspan, 'valign' => 'top' ) );
        }
        
        // Display orders details?
        if( in_array( 'orders', $POST[ 'details' ] ) ) {
            
            // Build booking details
            $orders = $this->ordersStatsDetails( $eventId );
            
            // Add details
            $htmlCode[] = $this->writeHTML( $orders, 'td', false, false, array( 'colspan' => $colspan, 'valign' => 'top' ) );
        }
        
        // Empty rows
        $htmlCode[] = $this->writeHTML( '', 'td' );
        $htmlCode[] = $this->writeHTML( '', 'td' );
        $htmlCode[] = $this->writeHTML( '', 'td' );
        
        // Return details
        return '<tr bgcolor="' . $color . '">' . implode ( chr( 10 ), $htmlCode ) . '</tr>';
    }
    
    /**
     * 
     */
    function bookingsStatsDetails( $eventId ) {
        global $LANG;
        
        // Check for details
        if( count( $this->indexes[ 'bookings' ][ 'events' ][ $eventId ] ) ) {
            
            // Storage
            $htmlCode = array();
            
            // Event row
            $eventRow = $this->records[ 'events' ][ $eventId ];
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Title
            $htmlCode[] = $this->writeHTML( $this->writeHTML( $LANG->getLL( 'stats.bookingDetails' ) ), 'strong' );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Counters
            $colorcount = 0;
            $rowcount   = 0;
            
            // Start table
            $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
            
            // Headers style
            $headerStyle = array( 'font-weight: bold' );
            
            // Table headers
            $htmlCode[] = '<tr>';
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'stats.distributor' ), 'td', false, $headerStyle );

            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'stats.distributor.sold' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'stats.totalSold' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
            $htmlCode[] = '</tr>';
            
            // Process bookings
            foreach( $this->indexes[ 'bookings' ][ 'events' ][ $eventId ] as $bookingId ) {
                
                // Event row
                $row = $this->records[ 'bookings' ][ $bookingId ];
                
                // Storage
                $booking = array();
                
                // Color
                $colorcount = ( $colorcount == 1 ) ? 0 : 1;
                $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                
                // TR parameters
                $tr_params = 'bgcolor="' . $color . '"';
                
                // Client
                $client = $this->records[ 'clients' ][ $row[ 'id_client' ] ];
                
                // Client name
                $booking[] = $this->writeHTML( $this->records[ 'clients' ][ $row[ 'id_client' ] ][ 'name_last' ], 'td' );
                
                // Booked tickets
                $booking[] = $this->writeHTML( $this->numberFormat( $row[ 'tickets_booked' ] ), 'td' );
                
                // Booked tickets total
                $booking[] = $this->writeHTML( $this->numberFormat( $row[ 'tickets_booked' ] * $eventRow[ 'price' ], 2 ), 'td' );
                
                // Actions
                $booking[] = $this->writeHTML( $this->api->be_buildRecordIcons( 'show', $this->extTables[ 'clients' ], $client[ 'uid' ] ), 'td' );
                
                // Add row
                $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $booking ) . '</tr>';
                
                // Increase row count
                $rowcount++;
            }
            
            // End table
            $htmlCode[] = '</table>';
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Return content
            return implode( chr( 10 ), $htmlCode );
            
        } else {
            
            // No details
            return $this->writeHTML( $LANG->getLL( 'nodetails' ) , 'div', 'typo3-red' );
        }
    }
    
    /**
     * 
     */
    function ordersStatsDetails( $eventId ) {
        global $LANG;
        
        // Check for details
        if( count( $this->indexes[ 'orders' ][ 'events' ][ $eventId ] ) ) {
        
            // Storage
            $htmlCode = array();
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Title
            $htmlCode[] = $this->writeHTML( $this->writeHTML( $LANG->getLL( 'stats.ordersDetails' ) ), 'strong' );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Counters
            $colorcount = 0;
            $rowcount   = 0;
            
            // Start table
            $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
            
            // Headers style
            $headerStyle = array( 'font-weight: bold' );
            
            // Table headers
            $htmlCode[] = '<tr>';
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'stats.client' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'stats.quantity' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'stats.total' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'stats.status' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'stats.type' ), 'td', false, $headerStyle );
            $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
            $htmlCode[] = '</tr>';
            
            // Icons
            $iconOk    = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_ok2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
            $iconError = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_fatalerror.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
            
            // Process bookings
            foreach( $this->indexes[ 'orders' ][ 'events' ][ $eventId ] as $bookingId ) {
                
                // Event row
                $row = $this->records[ 'orders' ][ $bookingId ];
                
                // Storage
                $booking = array();
                
                // Color
                $colorcount = ( $colorcount == 1 ) ? 0 : 1;
                $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                
                // TR parameters
                $tr_params = 'bgcolor="' . $color . '"';
                
                // Client
                $client = $this->records[ 'clients' ][ $row[ 'id_client' ] ];
                
                // Full name
                $clientFullName = $client[ 'name_first' ] . ' ' . $client[ 'name_last' ];
                
                // Confirmation state
                $confirmed = ( $row[ 'confirmed' ] == 1 ) ? $iconOk : $iconError;
                
                // Client name
                $booking[] = $this->writeHTML( $clientFullName, 'td' );
                
                // Quantity
                $booking[] = $this->writeHTML( $this->numberFormat( $row[ 'quantity' ] ), 'td' );
                
                // Total
                $booking[] = $this->writeHTML( $this->numberFormat( $row[ 'total' ], 2 ), 'td' );
                
                // Confirmation
                $booking[] = $this->writeHTML( $confirmed, 'td' );
                
                // Confirmation
                $booking[] = $this->writeHTML( $LANG->getLL( 'stats.type.I.' . $row[ 'type' ] ), 'td' );
                
                // Actions
                $booking[] = $this->writeHTML( $this->api->be_buildRecordIcons( 'show', $this->extTables[ 'clients' ], $client[ 'uid' ] ), 'td' );
                
                // Add row
                $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $booking ) . '</tr>';
                
                // Increase row count
                $rowcount++;
            }
            
            // End table
            $htmlCode[] = '</table>';
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Return content
            return implode( chr( 10 ), $htmlCode );
            
        } else {
            
            // No details
            return $this->writeHTML( $LANG->getLL( 'nodetails' ) , 'div', 'typo3-red' );
        }
    }
    
    /**
     * 
     */
    function findOrders()
    {
        global $LANG;
        
        // POST data
        $POST = t3lib_div::_POST( $GLOBALS['MCONF']['name'] );
        
        // Check actions
        if( $action = t3lib_div::_POST( 'action' ) ) {
            
            // Check order
            if( isset( $this->indexes[ 'orders' ][ 'id' ][ $POST[ 'orderid' ] ] ) ) {
            
                // Get orders
                $ordersList = $this->indexes[ 'orders' ][ 'id' ][ $POST[ 'orderid' ] ];
                
                // Check action
                if( $action == 'validate' ) {
                    
                    // Process orders
                    foreach( $ordersList as $orderId ) {
                        
                        // Update database
                        $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'orders' ], 'uid=' . $orderId, array( 'confirmed' => 1 ) );
                        
                        // Modify record in storage
                        $this->records[ 'orders' ][ $orderId ][ 'confirmed' ] = 1;
                    }
                    
                } elseif( $action == 'unvalidate' ) {
                    
                    // Process orders
                    foreach( $ordersList as $orderId ) {
                        
                        // Update database
                        $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'orders' ], 'uid=' . $orderId, array( 'confirmed' => 0 ) );
                        
                        // Modify record in storage
                        $this->records[ 'orders' ][ $orderId ][ 'confirmed' ] = 0;
                    }
                    
                } elseif( $action == 'delete' ) {
                    
                    // Process orders
                    foreach( $ordersList as $orderId ) {
                        
                        // Get order
                        $orderRow =& $this->records[ 'orders' ][ $orderId ];
                        
                        // Get associated event
                        $eventRow =& $this->records[ 'events' ][ $orderRow[ 'id_event' ] ];
                        
                        // New number of sold tickets
                        $ticketsSold = $eventRow[ 'tickets_sold' ] - $orderRow[ 'quantity' ];
                        
                        // Update number of sold tickets on event
                        $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'events' ], 'uid=' . $eventRow[ 'uid' ], array( 'tickets_sold' => $ticketsSold ) );
                        
                        // Delete order
                        $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'orders' ], 'uid=' . $orderId, array( 'deleted' => 1 ) );
                        unset( $this->records[ 'orders' ][ $orderId ] );
                    }
                    
                    // Unset index
                    unset( $this->indexes[ 'orders' ][ 'id' ][ $POST[ 'orderid' ] ] );
                }
            }
        }
        
        // Storage
        $htmlCode = array();
        
        // Start section
        $htmlCode[] = $this->doc->sectionBegin();
        
        // Title
        $htmlCode[] = $this->doc->sectionHeader( $LANG->getLL( 'section.find' ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Label
        $htmlCode[] = $this->writeHTML( $this->writeHTML( $LANG->getLL( 'search.label' ), 'strong' ) );
        
        // Input value
        $inputValue = ( isset( $POST[ 'orderid' ] ) ) ? $POST[ 'orderid' ] : '';
        
        // Search field
        $searchField = $this->writeHTML( '<input value="' . $inputValue . '" name="' . $GLOBALS['MCONF']['name'] . '[orderid]" type="text" value="" size="25" />', 'span' );
        
        // Submit
        $submit = $this->writeHTML( '<input name="submit" type="submit" value="' . $LANG->getLL( 'search.submit' ) . '" />', 'span' );
        
        // Add search and submit
        $htmlCode[] = $this->writeHTML( $searchField . ' ' . $submit );
        
        // Check post data
        if( isset( $POST[ 'orderid' ] ) && !empty( $POST[ 'orderid' ] ) ) {
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Try to find order
            if( isset( $this->indexes[ 'orders' ][ 'id' ][ $POST[ 'orderid' ] ] ) ) {
                
                // ID of first order
                $firstOrderId = $this->indexes[ 'orders' ][ 'id' ][ $POST[ 'orderid' ] ][ 0 ];
                
                // Client ID
                $clientId = $this->records[ 'orders' ][ $firstOrderId ][ 'id_client' ];
                
                // Client row
                $client = $this->records[ 'clients' ][ $clientId ];
                        
                // Options
                $btns[] = $this->writeHTML( '<input type="radio" name="action" value="validate" checked>&nbsp;' . $LANG->getLL( 'search.validate' ) );
                $btns[] = $this->doc->spacer( 10 );
                $btns[] = $this->writeHTML( '<input type="radio" name="action" value="unvalidate">&nbsp;' . $LANG->getLL( 'search.unvalidate' ) );
                $btns[] = $this->doc->spacer( 10 );
                $btns[] = $this->writeHTML( '<input type="radio" name="action" value="delete">&nbsp;' . $LANG->getLL( 'search.delete' ), 'div', 'typo3-red' );
                
                // Spacer
                $btns[] = $this->doc->spacer( 10 );
                
                // Submit
                $btns[] = '<input type="submit" value="' . $LANG->getLL('search.action.submit') . '">';
                
                // Add buttons
                $htmlCode[] = $this->writeHTML( implode( chr( 10 ), $btns ), 'fieldset' );
                
                // Spacer
                $htmlCode[] = $this->doc->spacer( 10 );
                
                // Client actions
                $clientActions = $this->api->be_buildRecordIcons( 'edit', $this->extTables[ 'clients' ], $clientId );
                
                // Client infos header
                $htmlCode[] = $this->writeHTML( $this->writeHTML( $LANG->getLL( 'search.client' ), 'strong' ) . ' ' . $clientActions );
                
                // Spacer
                $htmlCode[] = $this->doc->spacer( 10 );
                
                // Start client table
                $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
                
                $clientDisplayFields = array(
                    'name_first',
                    'name_last',
                    'address',
                    'zip',
                    'city',
                    'country',
                    'email',
                    'phone_professionnal',
                    'phone_personnal',
                    'fax',
                    'ip',
                    'crdate'
                );
                
                // Counters
                $colorcount = 0;
                $rowcount   = 0;
                
                // 
                foreach( $clientDisplayFields as $field ) {
                    
                    // Storage
                    $tableRow = array();
                    
                    // Color
                    $colorcount = ( $colorcount == 1 ) ? 0 : 1;
                    $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                    
                    // TR parameters
                    $tr_params = 'bgcolor="' . $color . '"';
                    
                    // Add label
                    $tableRow[] = $this->writeHTML( $LANG->getLL( 'search.client.' . $field ), 'td' );
                    
                    // Check field
                    if( $field == 'crdate' ) {
                        
                        // Date and time
                        $fieldValue = date( $this->dateFormat, $client[ $field ] );
                        
                    } else {
                        
                        // Unprocessed value
                        $fieldValue = $client[ $field ];
                    }
                    
                    // Add field
                    $tableRow[] = $this->writeHTML( $this->writeHTML( $fieldValue, 'strong' ), 'td' );
                    
                    // Add row
                    $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $tableRow ) . '</tr>';
                    
                    // Increase row count
                    $rowcount++;
                }
                
                // End client table
                $htmlCode[] = '</table>';
                
                // Spacer
                $htmlCode[] = $this->doc->spacer( 10 );
                
                // Orders infos header
                $htmlCode[] = $this->writeHTML( $this->writeHTML( $LANG->getLL( 'search.orders' ), 'strong' ) );
                
                // Spacer
                $htmlCode[] = $this->doc->spacer( 10 );
                
                // Start orders table
                $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
                
                // Headers style
                $headerStyle = array( 'font-weight: bold' );
                
                // Table headers
                $htmlCode[] = '<tr>';
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'search.orders.event' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'search.orders.price' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'search.orders.quantity' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'search.orders.total' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'search.orders.confirmed' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'search.orders.type' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
                $htmlCode[] = '</tr>';
                
                // Counters
                $colorcount = 0;
                $rowcount   = 0;
                
                // Icons
                $iconOk    = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_ok2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
                $iconError = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_fatalerror.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
                
                // Process orders
                foreach(  $this->indexes[ 'orders' ][ 'id' ][ $POST[ 'orderid' ] ] as $id ) {
                    
                    // Storage
                    $tableRow = array();
                    
                    // Order row
                    $row = $this->records[ 'orders' ][ $id ];
                    
                    // Event
                    $event = $this->records[ 'events' ][ $row[ 'id_event' ] ];
                    
                    // Color
                    $colorcount = ( $colorcount == 1 ) ? 0 : 1;
                    $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                    
                    // TR parameters
                    $tr_params = 'bgcolor="' . $color . '"';
                    
                    // Confirmation state
                    $confirmed = ( $row[ 'confirmed' ] == 1 ) ? $iconOk : $iconError;
                    
                    // Event name
                    $tableRow[] = $this->writeHTML( $event[ 'title' ], 'td' );
                    
                    // Price
                    $tableRow[] = $this->writeHTML( $this->numberFormat( $row[ 'price' ], 2 ), 'td' );
                    
                    // Quantity
                    $tableRow[] = $this->writeHTML( $this->numberFormat( $row[ 'quantity' ] ), 'td' );
                    
                    // Total
                    $tableRow[] = $this->writeHTML( $this->numberFormat( $row[ 'total' ], 2 ), 'td' );
                    
                    // Confirmation state
                    $tableRow[] = $this->writeHTML( $confirmed, 'td' );
                    
                    // Type
                    $tableRow[] = $this->writeHTML( $LANG->getLL( 'search.orders.type.I.' . $row[ 'type' ] ), 'td' );
                    
                    // Actions
                    $tableRow[] = $this->writeHTML( $this->api->be_buildRecordIcons( 'show,edit', $this->extTables[ 'events' ], $event[ 'uid' ] ), 'td' );
                    
                    // Add row
                    $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $tableRow ) . '</tr>';
                    
                    // Increase row count
                    $rowcount++;
                }
                
                // End client table
                $htmlCode[] = '</table>';
                
            } else {
                
                // Order not found
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'search.notfound' ), 'div', 'typo3-red' );
            }
        }
        
        // End section
        $htmlCode[] = $this->doc->sectionEnd();
        
        // Return code
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Writes some code.
     * 
     * This function writes a text wrapped inside an HTML tag.
     * @param       string      $text               The text to write
     * @param       string      $tag                The HTML tag to write
     * @param       array       $style              An array containing the CSS styles for the tag
     * @return      string      The text wrapped in the HTML tag
     */
    function writeHTML( $text, $tag = 'div', $class = false, $style = false, $params = array() )
    {
        // Create style tag
        if ( is_array( $style ) ) {
            $styleTag = ' style="' . implode( '; ', $style ) . '"';
        }
        
        // Create class tag
        if ( isset( $class ) ) {
            $classTag = ' class="' . $class . '"';
        }
        
        // Tag parameters
        if( count( $params ) ) {
            $paramsTags = ' ' . $this->api->div_writeTagParams( $params );
        }
        
        // Return HTML text
        return '<' . $tag . $classTag . $styleTag . $paramsTags . '>' . $text . '</' . $tag . '>';
    }
    
    function numberFormat( $number, $decimals = 0 )
    {
        // Return formatted number
        return number_format( $number, $decimals, '.', '\'' );
    }
    
    function printLabels()
    {
        global $LANG;
        
        // Check for an action
        if( t3lib_div::_POST( 'action' ) ) {
            
            // Create labels
            $this->createLabels();
        }
        
        // Storage
        $htmlCode = array();
        
        // Start section
        $htmlCode[] = $this->doc->sectionBegin();
        
        // Title
        $htmlCode[] = $this->doc->sectionHeader( $LANG->getLL( 'section.labels' ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Counters
        $colorcount = 0;
        $rowcount   = 0;
        
        // Storage
        $btns = array();
        
        // Label
        $btns[] = $this->writeHTML( $LANG->getLL( 'actions' ), 'legend' );
        
        // Icons
        $iconOk    = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_ok2.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        $iconError = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_fatalerror.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
        
        // Submit
        $btns[] = '<input type="submit" value="' . $LANG->getLL('btn.submit') . '">';
        
        // Check / Unckeck all
        $btns[] = '<input type="button" value="' . $LANG->getLL('btn.checkall') . '" onclick="checkBoxList(document.forms[0].list_db)">';
        
        // Add buttons
        $htmlCode[] = $this->writeHTML(implode(chr(10),$btns),'fieldset');
        
        // Spacer
        $htmlCode[] = $this->doc->spacer(10);
        
        // Action
        $htmlCode[] = '<input name="action" type="hidden" value="printLabel">';
        
        // Start table
        $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
        
        // Headers style
        $headerStyle = array( 'font-weight: bold' );
        
        // Table headers
        $htmlCode[] = '<tr>';
        $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.name_last' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.name_first' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.crdate' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.orderid' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.confirmed' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'client.labelprinted' ), 'td', false, $headerStyle );
        $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
        $htmlCode[] = '</tr>';
        
        // Process records
        foreach( $this->indexes[ 'orders' ][ 'clients' ] as $clientId => $ordersList ) {
            
            // Checkbox value
            $checkboxValue = $clientId;
            
            // Client row
            $row = $this->records[ 'clients' ][ $clientId ];
            
            // Order
            $order = $this->records[ 'orders' ][ $ordersList[ 0 ] ];
            
            if( $order[ 'labelprinted' ] == 0 ) {
                
                // Confirmed state
                $confirmed = ( $order[ 'confirmed' ] == 1 ) ? $iconOk : $iconError;
                
                // Storage
                $client = array();
                
                // Color
                $colorcount = ( $colorcount == 1 ) ? 0 : 1;
                $color      = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                
                // TR parameters
                $tr_params = ' onmouseover="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'over\',\'' . $this->doc->bgColor3 . '\');" onmouseout="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'out\',\'' . $color . '\');" onclick="setRowColor(this,\'' . $rowcount . '\',\'list_db\',\'click\',\'' . $this->doc->bgColor6 . '\',\'' . $color . '\');" bgcolor="' . $color . '"';
                
                // Checkbox
                $client[] = $this->writeHTML( '<input type="checkbox" name="' . $GLOBALS['MCONF']['name'] . '[list][]" id="list_db" value="' . $checkboxValue . '">', 'td' );
                
                // Last name
                $client[] = $this->writeHTML( $row[ 'name_last' ], 'td' );
                
                // First name
                $client[] = $this->writeHTML( $row[ 'name_first' ], 'td' );
                
                // Order date
                $client[] = $this->writeHTML( date( $this->dateFormat, $row[ 'crdate' ] ), 'td' );
                
                // Order ID
                $client[] = $this->writeHTML( $order[ 'orderid' ], 'td' );
                
                // Confirmation
                $client[] = $this->writeHTML( $confirmed, 'td' );
                
                // Printed state
                $printed = ( $order[ 'labelprinted' ] ) ? 1 : 0;
                
                // Already printed
                $client[] = $this->writeHTML( $LANG->getLL( 'client.labelprinted.I.' . $printed ), 'td' );
                
                // Actions
                $client[] = $this->writeHTML( $this->api->be_buildRecordIcons( 'show,edit', $this->extTables[ 'clients' ], $clientId ), 'td' );
                
                // Add row
                $htmlCode[] = '<tr ' . $tr_params . '>' . implode ( chr( 10 ), $client ) . '</tr>';
                
                // Increase row count
                $rowcount++;
            }
        }
        
        // End table
        $htmlCode[] = '</table>';
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // End section
        $htmlCode[] = $this->doc->sectionEnd();
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * 
     */
    function createLabels()
    {
        // POST data
        $POST = t3lib_div::_POST( $GLOBALS['MCONF']['name'] );
        
        // Check for data
        if( isset( $POST[ 'list' ] ) ) {
            
            // New instance of PDF label class
            $pdf = new PDF_Label(
                array(
                    'name'       => 'Her4464',
                    'paper-size' => 'A4',
                    'marginLeft' => 3,
                    'marginTop'  => 3,
                    'NX'         => 3,
                    'NY'         => 8,
                    'SpaceX'     => 0,
                    'SpaceY'     => 0,
                    'width'      => '70',
                    'height'     => '37',
                    'metric'     => 'mm',
                    'font-size'  => 10
                ),
                1,
                1
            );
            
            // Process records
            foreach( $POST[ 'list' ] as $clientId ) {
                
                // Orders
                $ordersList = $this->indexes[ 'orders' ][ 'clients' ][ $clientId ];
                
                // Process orders
                foreach( $ordersList as $orderId ) {
                    
                    // Update order row
                    $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'orders' ], 'uid=' . $orderId, array( 'labelprinted' => 1 ) );
                }
                
                // Client row
                $row = $this->records[ 'clients' ][ $clientId ];
                
                // Client infos
                #$fullName = utf8_decode( $row[ 'name_first' ] ) . ' ' . utf8_decode( $row[ 'name_last' ] );
                #$address  = utf8_decode( $row[ 'address' ] );
                #$city     = utf8_decode( $row[ 'zip' ] . ' - ' . $row[ 'city' ] );
                $fullName = $row[ 'name_first' ] . ' ' . $row[ 'name_last' ];
                $address  = $row[ 'address' ];
                $city     = $row[ 'zip' ] . ' - ' . $row[ 'city' ];
                
                // Add label
                $pdf->Add_PDF_Label( sprintf( '%s' . chr(10) . '%s' . chr(10) . '%s', $fullName, $address, $city ) );
            }
            
            // Create and output PDF
            $pdf->Open();
            $pdf->AddPage();
            $pdf->Output();
        }
    }
    
    /**
     * 
     */
    function exportEmails()
    {
        global $LANG;
        
        // GET data
        $GET = t3lib_div::_GET( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        // Check GET data
        if( isset( $GET[ 'exportEmails' ] ) ) {
            
            // Storage
            $clients = array();
            
            // Process clients
            foreach( $this->records[ 'clients' ] as $row ) {
                
                // Check email
                if( !array_key_exists( $client[ 'email' ], $row ) ) {
                    
                    // Client data
                    $client = array(
                        $row[ 'name_first' ],
                        $row[ 'name_last' ],
                        $row[ 'email' ],
                    );
                    
                    // Add client
                    $clients[ $row[ 'email' ] ] = implode( chr( 9 ), $client );
                }
            }
            
            // Output file
            $this->api->div_output( implode( chr( 10 ), $clients ), 'text/plain', 'clients.txt' );
            
        } else {
            
            // Storage
            $htmlCode = array();
            
            // Start section
            $htmlCode[] = $this->doc->sectionBegin();
            
            // Title
            $htmlCode[] = $this->doc->sectionHeader( $LANG->getLL( 'section.export' ) );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Link
            $link = '<a href="' . t3lib_div::linkThisScript( array( $GLOBALS[ 'MCONF' ][ 'name' ] . '[exportEmails]' => 1 ) ) . '">' . $LANG->getLL( 'export.link' ) . '</a>';
            
            // Add link
            $htmlCode[] = $this->writeHTML( $link );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // End section
            $htmlCode[] = $this->doc->sectionEnd();
            
            // Return content
            return implode( chr( 10 ), $htmlCode );
        }
    }
}

// XCLASS inclusion
if( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/ldap_macmade/mod1/index.php' ] ) {
    include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/ldap_macmade/mod1/index.php' ] );
}

// Make instance
$SOBE = t3lib_div::makeInstance( 'tx_cjf_module1' );
$SOBE->init();

// Include files
foreach( $SOBE->include_once as $INC_FILE ) {
    include_once( $INC_FILE );
}

// Output module
$SOBE->main();
$SOBE->printContent();
