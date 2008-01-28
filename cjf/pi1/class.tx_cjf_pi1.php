<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 macmade.net
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
 * Plugin 'CJF - Events' for the 'cjf' extension.
 * 
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *        :     function main( $content, $conf )
 * 
 *              TOTAL FUNCTIONS: 
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

// Shopping cart class
require_once( t3lib_extMgm::extPath( 'cjf' ) . 'class.tx_cjf_shoppingcart.php' );

// PDF class
require_once( t3lib_extMgm::extPath( 'cjf' ) . 'class.tx_cjf_pdf.php' );

class tx_cjf_pi1 extends tslib_pibase
{
    
    // Same as class name
    var $prefixId           = 'tx_cjf_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_cjf_pi1.php';
    
    // The extension key
    var $extKey             = 'cjf';
    
    // Version of the Developer API required
    var $apimacmade_version = 2.8;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Plugin configuration array
    var $conf               = array();
    
    // Developer API instance
    var $api                = NULL;
    
    // Shopping cart instance
    var $cart               = NULL;
    
    // FE user instance (reference to TSFE)
    var $user               = NULL;
    
    // PDF class
    var $pdf                = NULL;
    
    // Current language
    var $lang               = 0;
    
    // Upload directory
    var $uploadDir          = 'uploads/tx_cjf/';
    
    // Form errors
    var $cartErrors         = array();
    
    // Form errors
    var $formErrors         = array();
    
    // Submitted form data
    var $formData           = array();
    
    // Extension tables
    var $extTables          = array(
        'events'   => 'tx_cjf_events',
        'groups'   => 'tx_cjf_groups',
        'artists'  => 'tx_cjf_artists',
        'places'   => 'tx_cjf_places',
        'styles'   => 'tx_cjf_styles',
        'clients'  => 'tx_cjf_clients',
        'orders'   => 'tx_cjf_orders',
        'bookings' => 'tx_cjf_bookings',
    );
    
    // Partners storage
    var $partners           = array();
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin "tx_mozsearch_pi1", and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param       string      $content            The content object
     * @param       array       $conf               The TS setup
     * @return      string      The content of the plugin
     * @see         setConfig
     */
    function main( $content, $conf)
    {
        
        // New instance of the macmade.net API
        $this->api = new tx_apimacmade( $this );
        
        // New instance of the shopping cart
        $this->cart = t3lib_div::makeInstance( 'tx_cjf_shoppingCart' );
        
        // Load locallang file
        $this->pi_loadLL();
        
        // Set default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Store FE language
        $this->lang = $GLOBALS[ 'TSFE' ]->sys_page->sys_language_uid;
        
        // Storage
        $content = array();
        
        // DEBUG - For macmade only - Rebuild PDF for a specific client
        if( isset( $this->piVars[ 'debugMacmade' ] ) ) {
            $this->createOrderPDF( '2007-4602595c7cc3b' );
        }
        
        // Check returns from payement system
        if( $payReturn = $this->checkPayReturn() ) {
            
            // Write pay return
            $content[] = $payReturn;
            
        } else {
            
            // Cart processing
            $content[] = $this->processCart();
            
            // Cart status
            $content[] = $this->cartStatus();
            
            // Check display mode
            if( isset( $this->piVars[ 'checkOut' ] ) ) {
                
                // Show cart items
                $content[] = $this->checkOut();
                
            } elseif( isset( $this->piVars[ 'showCart' ] ) ) {
                
                // Checkout order
                $content[] = $this->showCart();
                
            } elseif( isset( $this->piVars[ 'showArtist' ] ) ) {
                
                // Show artist
                $content[] = $this->showArtist( $this->piVars[ 'showArtist' ] );
                
            } elseif( isset( $this->piVars[ 'showGroup' ] ) ) {
                
                // Show group
                $content[] = $this->showGroup( $this->piVars[ 'showGroup' ] );
                
            } else {
                
                // Show events
                $content[] = $this->getEvents();
            }
        }
        
        // Return content
        return $this->pi_wrapInBaseClass( implode( chr( 10 ), $content ) );
    }
    
    /**
     * Set configuration array
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     */
    function setConfig()
    {
        
        // Mapping array for PI flexform
        $flex2conf = array(
            'pid'            => 'sDEF:pages',
            'disableCart'    => 'sDEF:disable_cart',
            'hideInfos'      => 'sDEF:hide_infos',
            'edition'        => 'sDEF:edition',
            'postalDelivery' => 'sDEF:postal_delivery',
            'currency'       => 'sPAY:currency',
            'taxesPay'       => 'sPAY:taxes_pay',
            'taxesPostal'    => 'sPAY:taxes_postal',
            'shopId'         => 'sPAY:shopid',
            'shopIdFull'     => 'sPAY:shopid_full',
            'langId'         => 'sPAY:langid',
            'hashSeed'       => 'sPAY:hash_seed',
            'payTest'        => 'sPAY:pay_test',
            'pdfTitle'       => 'sPDF:pdf_title',
            'pdfStorage'     => 'sPDF:pdf_store',
            'email'          => 'sMAIL:email',
            'emailTitle'     => 'sMAIL:mail_title',
            'emailBody'      => 'sMAIL:mail_body',
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug( $this->conf, 'CJF - Events: configuration array' );
    }
    
    /**
     * 
     */
    function getEvents()
    {
        // Where clase for selecting events
        $where = 'pid='
               . $this->conf[ 'pid' ]
               . ' AND sys_language_uid=0'
               . $this->cObj->enableFields( $this->extTables[ 'events' ] );
        
        // Order by clause for events
        $orderBy = 'date ASC,hour ASC,title ASC';
        
        // Select events
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            '*',
            $this->extTables[ 'events' ],
            $where,
            false,
            $orderBy
        );
        
        // Check MySQL result
        if( $res ) {
        
            // Storage
            $htmlCode    = array();
            $lastHeader  = false;
            $events      = array();
            $anchorsMenu = array();
            
            // Add events menu
            $htmlCode[] = $this->eventsMenu();
            
            // Process events
            while( $tempRow = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                
                if( isset( $this->piVars[ 'sortBy' ] ) && $this->piVars[ 'sortBy' ] == 1 ) {
                    
                    // Get place
                    $tempPlace = $this->pi_getRecord( $this->extTables[ 'places' ], $tempRow[ 'place' ] );
                    
                    // Check if index exists
                    if( !isset( $events[ $tempPlace[ 'sorting' ] ] ) ) {
                        
                        // Sorting field
                        $sorting = $tempPlace[ 'sorting' ];
                        
                        // Create index
                        $events[ $sorting ] = array();
                        
                        // Get localized place
                        $tempPlace =& $this->recordL10N( $this->extTables[ 'places' ], $tempPlace );
                        
                        // Add anchors menu entry
                        $anchorsMenu[ $sorting ] = $tempPlace[ 'place' ];
                    }
                    
                    // Store row
                    $events[ $tempPlace[ 'sorting' ] ][] = $tempRow;
                    
                } else {
                    
                    // Store row
                    $events[ $tempRow[ 'date' ] ][] = $tempRow;
                    
                    // Add anchors menu entry
                    $anchorsMenu[ $tempRow[ 'date' ] ] = $this->cObj->stdWrap( $tempRow[ 'date' ], array( 'strftime' => $this->conf[ 'dateFormat' ] ) );
                }
            }
            
            // Sort events and anchors menu
            ksort( $events );
            ksort( $anchorsMenu );
            
            // Add anchors menu
            $htmlCode[] = $this->anchorsMenu( $anchorsMenu );
            
            // Process events group
            foreach( $events as $anchorIndex => $rowGroup ) {
                
                // Process events
                foreach( $rowGroup as $row ) {
                    
                    // Store event ID
                    $eventId = $row[ 'uid' ];
                    
                    // Get localized row
                    $row =& $this->recordL10N( $this->extTables[ 'events' ], $row );
                    
                    // Storage
                    $event  = array();
                    $infos  = array();
                    $groups = array();
                    
                    // Select groups
                    $groupsRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
                        $this->extTables['groups'] . '.*',
                        $this->extTables['events'],
                        'tx_cjf_events_groups_mm',
                        $this->extTables['groups'],
                        'AND tx_cjf_events_groups_mm.uid_local=' . $eventId
                        . $this->cObj->enableFields( $this->extTables['groups'] )
                    );
                    
                    // Check MySQL result
                    if( $res ) {
                        
                        // Process groups
                        while( $groupRow = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $groupsRes ) ) {
                            
                            // Group ID (not localized)
                            $groupId = $groupRow[ 'uid' ];
                            
                            // Get localized row
                            $groupRow =& $this->recordL10N( $this->extTables[ 'groups' ], $groupRow );
                            
                            // Storage
                            $group = array();
                            
                            // Group link
                            $groupLink = $this->pi_linkTP_keepPIvars( $groupRow[ 'name' ] , array( 'showGroup' => $groupId ) );
                            
                            // Group name
                            $group[] = $this->api->fe_makeStyledContent( 'div', 'event-group-name', $groupLink );
                            
                            // Add group
                            $groups[] = $this->api->fe_makeStyledContent( 'div', 'event-group', implode( chr( 10 ), $group ) );
                        }
                    }
                    
                    // Select place
                    $place = $this->pi_getRecord( $this->extTables[ 'places' ], $row[ 'place' ] );
                    
                    // Get localized place
                    $place =& $this->recordL10N( $this->extTables[ 'places' ], $place );
                    
                    // Formatted event date
                    $displayDate = $this->cObj->stdWrap( $row[ 'date' ], array( 'strftime' => $this->conf[ 'dateFormat' ] ) );
                    
                    // Display mode
                    if( isset( $this->piVars[ 'sortBy' ] ) && $this->piVars[ 'sortBy' ] == 1 ) {
                        
                        // Header & subheader
                        $header    = $place[ 'place' ];
                        $subheader = $displayDate;
                        
                    } else {
                        
                        // Header & subheader
                        $header    = $displayDate;
                        $subheader = $place[ 'place' ];
                    }
                    
                    // Check if header is already displayed
                    if ( $header != $lastHeader ) {
                        
                        // Store current header
                        $lastHeader = $header;
                        
                        // Event header
                        $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'event-header', '<a name="' . $anchorIndex . '"></a>' . $header );
                    }
                    
                    // Event subheader
                    $event[] = $this->api->fe_makeStyledContent( 'h3', 'event-subheader', $subheader );
                    
                    // Event title
                    $event[] = $this->api->fe_makeStyledContent( 'h4', 'event-title', $row[ 'title' ] );
                    
                    // Number of tickets still available
                    $ticketsAvailableNum = ( $row[ 'tickets' ] - $row[ 'tickets_sold' ] ) - $row[ 'tickets_booked' ];
                    
                    // Tickets available
                    $ticketsAvailable = ( $ticketsAvailableNum == 0 || $row[ 'soldout' ] == 1 ) ? $this->pi_getLL( 'soldout' ) : $ticketsAvailableNum;
                    
                    // Event price
                    $price = ( !empty( $row[ 'price' ] ) ) ? $this->conf[ 'currency' ] . ' ' . $this->numberFormat( $row[ 'price' ], 2 ) : $price = $this->pi_getLL( 'free' );
                    
                    // Add event groups
                    $event[] = ( count( $groups ) ) ? $this->api->fe_makeStyledContent( 'div', 'event-groups', implode( chr( 10 ), $groups ) ) : '';
                    
                    // Event comments
                    $event[] = $this->api->fe_makeStyledContent( 'div', 'event-comments', $row[ 'comments' ] );
                    
                    // Check if tickets are available
                    if( !empty( $row[ 'price' ] ) && $ticketsAvailable > 0 && $row[ 'soldout' ] != 1 && $this->conf[ 'disableCart' ] != 1 ) {
                        
                        // Cart link
                        $cartLink = $this->pi_linkTP_keepPIvars( $this->pi_getLL( 'cartAdd' ), array( 'addToCart' => $eventId ) );
                        
                        // Add to cart
                        $event[] = $this->api->fe_makeStyledContent( 'div', 'event-add-to-cart', $cartLink );
                    }
                    
                    // Checks the soldout state
                    if( $row[ 'soldout' ] ) {
                        
                        // Adds soldout message
                        $event[] = $this->api->fe_makeStyledContent( 'div', 'event-soldout', $this->pi_getLL( 'soldout' ) );
                    }
                    
                    // Event hour
                    $infos[] = $this->api->fe_makeStyledContent( 'div', 'event-hour', $this->pi_getLL( 'hour' ) . ' ' . $this->cObj->stdWrap( $row[ 'hour' ] + ( 3600 * $this->conf[ 'hourCorrection' ] ), array( 'strftime' => $this->conf[ 'hourFormat' ] ) ) );
                    
                    // Seats availables
                    $seats = ( $row[ 'seats' ] == 1 ) ? $this->pi_getLL( 'yes' ) : $this->pi_getLL( 'no' );
                    
                    // Seats availables
                    $standing = ( $row[ 'no_seats' ] == 1 ) ? $this->pi_getLL( 'yes' ) : $this->pi_getLL( 'no' );
                    
                    // Check if infos must be displayed
                    if( !$this->conf[ 'hideInfos' ] ) {
                        
                        // Event price
                        $infos[] = $this->api->fe_makeStyledContent( 'div', 'event-price', $this->pi_getLL( 'price' ) . ' ' . $price );
                        
                        // Seats
                        $infos[] = $this->api->fe_makeStyledContent( 'div', 'event-seats', $this->pi_getLL( 'seats' ) . ' ' . $seats );
                        
                        // Standing places
                        $infos[] = $this->api->fe_makeStyledContent( 'div', 'event-noseats', $this->pi_getLL( 'no_seats' ) . ' ' . $standing );
                        
                        // Check for a specific number of tickets
                        #if( $row[ 'tickets' ] ) {
                        #    
                        #    // Add number of tickets available
                        #    $infos[] = $this->api->fe_makeStyledContent( 'div', 'event-tickets', $this->pi_getLL( 'tickets' ) . ' ' . $ticketsAvailable );
                        #}
                        
                        // Sell places
                        #if( $sellPlaces = $this->additionnalSellPlaces( $eventId ) ) {
                            
                            // Add sell places
                        #    $infos[] = $this->api->fe_makeStyledContent( 'div', 'event-price', $this->pi_getLL( 'sellPlaces' ) . ' ' . $sellPlaces );
                        #}
                        
                        // Add event informations
                        $event[] = $this->api->fe_makeStyledContent( 'div', 'event-infos', implode( chr( 10 ), $infos ) );
                    }
                    
                    // Add event to content
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'event', implode( chr( 10 ), $event ) );
                }
            }
            
            // Return events
            return implode( chr( 10 ), $htmlCode );
        }
    }
    
    /**
     * 
     */
    function anchorsMenu( $menu )
    {
        // Storage
        $htmlCode = array();
        
        // Start list
        $htmlCode[] = '<ul>';
        
        // Process menu items
        foreach( $menu as $key => $value ) {
            
            // Add item
            $htmlCode[] = '<li><a href="#' . $key . '">' . $value . '</a></li>';
        }
        
        // End list
        $htmlCode[] = '</ul>';
        
        // Return menu
        return $this->api->fe_makeStyledContent( 'div', 'anchorsMenu', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function additionnalSellPlaces( $eventId )
    {
        // Select bookings
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            '*',
            $this->extTables[ 'bookings' ],
            'pid=' . $this->conf[ 'pid' ] . ' AND id_event=' . $eventId . $this->cObj->enableFields( $this->extTables[ 'bookings' ] )
        );
        
        // Check MySQL result
        if( $res )
        {
            
            // Storage
            $clients  = array();
            $htmlCode = array();
            
            // Process bookings
            while( $booking = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                
                // Check if client has already been stored
                if( !isset( $this->partners[ $booking[ 'id_client' ] ] ) ) {
                    
                    // Get client
                    $clientRow = $this->pi_getRecord( $this->extTables[ 'clients' ], $booking[ 'id_client' ] );
                    
                    // Store client name
                    $this->partners[ $booking[ 'id_client' ] ] = $clientRow[ 'name_last' ];
                }
                
                // Reference to client
                $clients[] =& $this->partners[ $booking[ 'id_client' ] ];
            }
            
            // Check partners
            if( count( $clients ) ) {
                
                // Return client list
                return implode( ', ', $clients );
            }
        }
    }
    
    function eventsMenu()
    {
        
        // Storage
        $htmlCode = array();
        
        // Links
        $datesLink  = $this->api->fe_makeStyledContent(
            'div',
            'event-menu-dates',
            $this->api->fe_linkTP_unsetPIvars(
                $this->pi_getLL( 'menuDates' ),
                array(
                    'sortBy' => '0'
                ),
                array()
            )
        );
        $placesLink = $this->api->fe_makeStyledContent(
            'div',
            'event-menu-places',
            $this->api->fe_linkTP_unsetPIvars(
                $this->pi_getLL( 'menuPlaces' ),
                array(
                    'sortBy' => '1'
                ),
                array()
            )
        );
        
        // CSS classes
        $datesClass  = ( $this->piVars[ 'sortBy' ] ) ? 'item' : 'item-act';
        $placesClass = ( $this->piVars[ 'sortBy' ] ) ? 'item-act' : 'item';
        
        // Select by dates
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', $datesClass, $datesLink );
        
        // Select by places
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', $placesClass, $placesLink );
        
        // Return menu
        return $this->api->fe_makeStyledContent( 'div', 'event-menu', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function showGroup( $uid )
    {
        
        // Storage
        $htmlCode = array();
        $infos    = array();
        $artists  = array();
        
        // Try to get group
        $row = $this->pi_getRecord( $this->extTables[ 'groups' ], $uid );
        
        // Check MySQL result
        if( $row ) {
            
            // Group style
            $style = $this->pi_getRecord( $this->extTables[ 'styles' ], $row[ 'style' ] );
            
            // Localized style
            $style =& $this->recordL10N( $this->extTables[ 'styles' ], $style );
            
            // Get localized record
            $row =& $this->recordL10N( $this->extTables[ 'groups' ], $row );
            
            // Group name
            $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'group-name', $row[ 'name' ] );
            
            // Check for a picture
            if( !empty( $row[ 'picture' ] ) ) {
                
                // Picture TS object
                $picture = $this->api->fe_createImageObjects( $row[ 'picture' ], $this->conf[ 'pictures.' ][ 'groups.' ], $this->uploadDir );
                
                // Add picture
                $infos[] = $this->api->fe_makeStyledContent( 'div', 'group-picture', $picture );
            }
            
            // Country
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'group-country', $this->pi_getLL( 'country' ) . ' ' . $row[ 'country' ] );
            
            // Style
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'group-style', $this->pi_getLL( 'style' ) . ' ' . $style[ 'style' ] );
            
            // Website
            $website = ( !empty( $row[ 'www' ] ) ) ? $this->cObj->typoLink( $row[ 'www' ], array( 'parameter' => $row[ 'www' ], 'extTarget' => $this->conf[ 'extLinkTarget' ] ) ) : '';
            
            // Website
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'group-www', $this->pi_getLL( 'www' ) . ' ' . $website );
            
            // Add group informations
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'group-infos', implode( chr( 10 ), $infos ) );
            
            // Check for artists
            if( !empty( $row[ 'artists' ] ) ) {
                
                // Convert XML data
                $data = $this->api->div_xml2array( $row[ 'artists' ], 0 );
                
                // Check datastructure
                if( isset( $data[ 'T3FlexForms' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'fields' ][ 'el' ] ) ) {
                    
                    // Artists data
                    $artistsData = $data[ 'T3FlexForms' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'fields' ][ 'el' ];
                    
                    // Process artists
                    foreach( $artistsData as $artist ) {
                        
                        // Artist ID
                        $artistId = $artist[ 'artists' ][ 'el' ][ 'artist' ][ 'vDEF' ];
                        
                        // Role
                        $role = $artist[ 'artists' ][ 'el' ][ 'role' ][ 'vDEF' ];
                        
                        // Get artist row
                        $artistRow = $this->pi_getRecord( $this->extTables[ 'artists' ], $artistId );
                        
                        // Check for artists infos
                        if( !empty( $artistRow[ 'label' ] ) || !empty( $artistRow[ 'picture' ] ) || !empty( $artistRow[ 'www' ] ) || !empty( $artistRow[ 'description' ] ) ) {
                            
                            // Artist name with link
                            $artistLink = $this->pi_linkTP_keepPIvars( $artistRow[ 'name_first' ] . ' ' . $artistRow[ 'name_last' ], array( 'showArtist' => $artistId ) );
                            
                        } else {
                            
                            // Artist name without link
                            $artistLink = $artistRow[ 'name_first' ] . ' ' . $artistRow[ 'name_last' ];
                        }
                        
                        // Add artist
                        $artists[] = $this->api->fe_makeStyledContent( 'div', 'artist', $artistLink . ' (' . $role . ')');
                    }
                    
                    // Add artists informations
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'group-artists', implode( chr( 10 ), $artists ) );
                }
            }
            
            // Group description
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'group-description', $this->pi_RTEcssText( $row[ 'description' ] ) );
            
            // Back link
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'back', $this->api->fe_linkTP_unsetPIvars( $this->pi_getLL( 'back' ), array(), array( 'showGroup' ) ) );
        }
        
        // Return content
        return $this->api->fe_makeStyledContent( 'div', 'group', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function showArtist( $uid )
    {
        
        // Storage
        $htmlCode = array();
        $infos    = array();
        
        // Try to get group
        $row = $this->pi_getRecord( $this->extTables[ 'artists' ], $uid );
        
        // Check MySQL result
        if( $row ) {
            
            // Get localized record
            $row =& $this->recordL10N( $this->extTables[ 'artists' ], $row );
            
            // Group name
            $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'artist-name', $row[ 'name_first' ] . ' ' . $row[ 'name_last' ] );
            
            // Check for a picture
            if( !empty( $row[ 'picture' ] ) ) {
                
                // Picture TS object
                $picture = $this->api->fe_createImageObjects( $row[ 'picture' ], $this->conf[ 'pictures.' ][ 'artists.' ], $this->uploadDir );
                
                // Add picture
                $infos[] = $this->api->fe_makeStyledContent( 'div', 'artist-picture', $picture );
            }
            
            // Label
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'artist-label', $this->pi_getLL( 'label' ) . ' ' . $row[ 'label' ] );
            
            // Website
            $website = ( !empty( $row[ 'www' ] ) ) ? $this->cObj->typoLink( $row[ 'www' ], array( 'parameter' => $row[ 'www' ], 'extTarget' => $this->conf[ 'extLinkTarget' ] ) ) : '';
            
            // Website
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'artist-www', $this->pi_getLL( 'www' ) . ' ' . $website );
            
            // Add group informations
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'artist-infos', implode( chr( 10 ), $infos ) );
            
            // Artist description
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'artist-description', $this->pi_RTEcssText( $row[ 'description' ] ) );
            
            // Back link
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'back', $this->api->fe_linkTP_unsetPIvars( $this->pi_getLL( 'back' ), array(), array( 'showArtist' ) ) );
            
            // Return content
            return $this->api->fe_makeStyledContent( 'div', 'artist', implode( chr( 10 ), $htmlCode ) );
        }
    }
    
    /**
     * 
     */
    function &recordL10N( $tableName, &$row )
    {
        
        // Check for a alternative language
        if( $this->lang > 0 ) {
            
            // Select localized record
            $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                '*',
                $tableName,
                $tableName . '.l18n_parent=' . $row[ 'uid' ]
                . ' AND ' . $tableName .  '.sys_language_uid=' . $this->lang
                . $this->cObj->enableFields( $tableName )
            );
            
            // Try to get row
            if( $res && $localized = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                
                // Process localized data
                foreach( $localized as $key => $value ) {
                    
                    // Do not include empty rows
                    if ( !empty( $value ) ) {
                        
                        // Add localized value to row
                        $row[ $key ] = $value;
                    }
                }
            }
        }
        
        // Return localized row
        return $row;
    }
    
    /**
     * 
     */
    function processCart()
    {
        
        // Add items?
        if( isset( $this->piVars[ 'addToCart' ] ) && $this->conf[ 'disableCart' ] != 1 ) {
            
            // Get row
            $row = $this->pi_getRecord( $this->extTables[ 'events' ], $this->piVars[ 'addToCart' ] );
            
            // Available tickets
            $ticketsAvailable = ( $row[ 'tickets' ] - $row[ 'tickets_sold' ] ) - $row[ 'tickets_booked' ];
            
            // Check if item is already in the cart
            if( $cartItem = $this->cart->getItem( $this->piVars[ 'addToCart' ] ) ) {
                
                // Update available tickets number
                $ticketsAvailable -= $cartItem[ 'quantity' ];
            }
                
            // Unset plugin variable
            unset( $this->piVars[ 'addToCart' ] );
            
            // Check for row
            if( is_array( $row ) && !empty( $row[ 'price' ] ) && $ticketsAvailable > 0 ) {
                
                // Add item
                $this->cart->addItem( $this->extTables[ 'events' ], $row[ 'uid' ], $row[ 'price' ] );
                
            } else {
                
                // Return error
                return $this->api->fe_makeStyledContent( 'div', 'error', $this->pi_getLL( 'cartItem-notEnough' ) );
            }
        }
    }
    
    /**
     * 
     */
    function cartStatus()
    {
        
        // Check for actions
        if ( !isset( $this->piVars[ 'checkOut' ] ) && !isset( $this->piVars[ 'showCart' ] ) && $this->conf[ 'disableCart' ] != 1 ) {
            
            // Count cart items
            if( $items = $this->cart->countItems() ) {
                
                // Storage
                $htmlCode = array();
                $actions  = array();
                
                // Number of items
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'cart-status-items', sprintf( $this->pi_getLL( 'cartItemsNum' ), $items ) );
                
                // View cart
                $actions[] = $this->api->fe_makeStyledContent(
                    'div',
                    'view-cart',
                    $this->api->fe_linkTP_unsetPIvars(
                        $this->pi_getLL( 'cartShow' ),
                        array(
                            'showCart' => '1'
                        )
                    )
                );
                
                // Checkout
                $actions[] = $this->api->fe_makeStyledContent(
                    'div',
                    'checkout',
                    $this->api->fe_linkTP_unsetPIvars(
                        $this->pi_getLL( 'cartCheckOut' ),
                        array(
                            'checkOut' => '1'
                        )
                    )
                );
                
                // Add actions
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'cart-status-actions', implode( chr( 10 ), $actions ) );
                
                // Return status
                return $this->api->fe_makeStyledContent( 'div', 'cart-status', implode( chr( 10 ), $htmlCode ) );
            }
        }
    }
    
    /**
     * 
     */
    function showCart()
    {
        // Check cart availability
        if( $this->conf[ 'disableCart' ] != 1 ) {
            
            // Process cart items to verify availability of tickets
            foreach( $this->cart->data as $key => $value ) {
                
                // Get record
                $row = $this->pi_getRecord( $value[ 'table' ], $key );
                
                // Available tickets
                $ticketsAvailable = ( $row[ 'tickets' ] - $row[ 'tickets_sold' ] )  - $row[ 'tickets_booked' ];
                
                // Tickets quantity requested
                $quantity = ( ( $this->piVars[ 'cart_item' ][ $key ] ) ) ? $this->piVars[ 'cart_item' ][ $key ] : $value[ 'quantity' ];
                
                // Check number of available tickets
                if( $ticketsAvailable < 1 ) {
                    
                    // No more tickets
                    $this->cartErrors[ $key ] = $this->pi_getLL( 'cartItem-notAvailable' );
                    
                } elseif( $quantity > $ticketsAvailable ) {
                    
                    // Not enough tickets
                    $this->cartErrors[ $key ] = sprintf( $this->pi_getLL( 'cartItem-quantityTooBig' ), $ticketsAvailable );
                }
            }
            
            // Check if cart has been updated
            if( isset( $this->piVars[ 'cart_update' ] ) ) {
                
                if( !count( $this->cartErrors ) ) {
                    
                    // Process items
                    foreach( $this->piVars[ 'cart_item' ] as $id => $quantity ) {
                            
                        // Update quantity
                        $this->cart->updateItemQuantity( $id, $quantity );
                    }
                }
                
                // Unset plugin variables
                unset( $this->piVars[ 'cart_update' ] );
                unset( $this->piVars[ 'cart_item' ] );
            }
            
            // Check if an item must be removed
            if( isset( $this->piVars[ 'removeItem' ] ) ) {
                
                // Remove item
                $this->cart->removeItem( $this->piVars[ 'removeItem' ] );
                
                // Unset plugin variable
                unset( $this->piVars[ 'removeItem' ] );
            }
            
            // Storage
            $htmlCode = array();
            
            // Header
            $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'cart-details-header', $this->pi_getLL( 'cartDetailsHeader' ) );
            
            // Cart items details
            $htmlCode[] = $this->cartItems( true );
            
            // Check for items in cart
            if( count( $this->cart->data ) ) {
                
                // Checkout link
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'cart-checkout', $this->api->fe_linkTP_unsetPIvars( $this->pi_getLL( 'cartCheckOut' ), array( 'checkOut' => 1 ), array() ) );
            }
            
            // Back link
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'back', $this->api->fe_linkTP_unsetPIvars( $this->pi_getLL( 'back' ), array(), array( 'showCart' ) ) );
            
            // Return code
            return implode( chr( 10 ), $htmlCode );
        }
    }
    
    /**
     * 
     */
    function cartItems( $editable = false )
    {
        
        // Storage
        $htmlCode = array();
        
        // Cart items
        $cart =& $this->cart->data;
        
        if( count( $cart ) ) {
            
            // Storage
            $headers = array();
            $total   = 0;
            
            // Cart headers
            $headers[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-headers-name', $this->pi_getLL( 'cartLabelName' ) );
            $headers[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-headers-price', $this->pi_getLL( 'cartLabelPrice' ) );
            $headers[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-headers-quantity', $this->pi_getLL( 'cartLabelQuantity' ) );
            $headers[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-headers-total', $this->pi_getLL( 'cartLabelTotal' ) );
            
            // Add headers
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-headers', implode( chr( 10 ), $headers ) );
            
            // Process cart items
            foreach( $cart as $key => $value ) {
                
                // Get record
                $row = $this->pi_getRecord( $value[ 'table' ], $key );
                
                // Check MySQL result
                if( $row ) {
                
                    // Storage
                    $item = array();
                    
                    // Get localized record
                    $row =& $this->recordL10N( $this->extTables[ 'events' ], $row );
                    
                    // Check for errors
                    if( isset( $this->cartErrors[ $key ] ) ) {
                        
                        // Add error
                        $item[] = $this->api->fe_makeStyledContent( 'div', 'error', $this->cartErrors[ $key ] );
                    }
                    
                    // Item name
                    $item[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-item-name', $row[ 'title' ] );
                    
                    // Item price
                    $item[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-item-price', $this->conf[ 'currency' ] . ' ' . $this->numberFormat( $value[ 'price' ], 2 ) );
                    
                    // Check render mode
                    if( $editable ) {
                        
                        // Quantity input
                        $quantity = $this->api->fe_makeStyledContent(
                            'input',
                            'cart-item-input',
                            false,
                            1,
                            false,
                            false,
                            array(
                                'type' => 'text',
                                'size' => '5',
                                'value' => $value[ 'quantity' ],
                                'name' => $this->prefixId . '[cart_item][' . $key . ']'
                            )
                        );
                    } else {
                        
                        // Quantity as text only
                        $quantity = $value[ 'quantity' ];
                    }
                    
                    // Item quantity
                    $item[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-item-quantity', $quantity );
                    
                    // Total price for item
                    $price = $value[ 'price' ] * $value[ 'quantity' ];
                    
                    // Increase general total
                    $total += $price;
                    
                    // Add item total
                    $item[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-item-total', $this->conf[ 'currency' ] . ' ' . $this->numberFormat( $price, 2 ) );
                    
                    // Check render mode
                    if( $editable ) {
                        
                        // Remove item
                        $item[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-item-remove', $this->pi_linkTP_keepPIvars( $this->pi_getLL( 'cartRemove' ), array( 'removeItem' => $key ) ) );
                    }
                    
                    // Add item
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-item', implode( chr( 10 ), $item ) );
                }
            }
            
            // Taxes total
            $payTaxes = $this->conf[ 'taxesPay' ] * $this->cart->countItems();
            
            // Taxes
            $taxes = array();
            
            // Name
            $taxes[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-taxes-name', $this->pi_getLL( 'taxesPay' ) );
            
            // Price for payement taxes
            $taxes[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-taxes-price', $this->conf[ 'currency' ] . ' ' . $this->numberFormat( $payTaxes, 2 ) );
            
            // Add taxes
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-taxes', implode( chr( 10 ), $taxes ) );
            
            // Add total
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'cart-details-total', $this->pi_getLL( 'cartLabelTotal' ) . ' ' . $this->conf[ 'currency' ] . ' ' . $this->numberFormat( $total + $payTaxes, 2 ) );
            
            // Check render mode
            if( $editable ) {
                
                // Update cart
                $update = $this->api->fe_makeStyledContent(
                    'input',
                    'submit',
                    false,
                    1,
                    false,
                    false,
                    array(
                        'type' => 'submit',
                        'value' => $this->pi_getLL( 'cartUpdate' ),
                        'name' => $this->prefixId . '[cart_update]'
                    )
                );
                
                // Add update cart input
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'cart-update', $update );
            }
            
        } else {
            
            // Empty cart
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'empty', $this->pi_getLL( 'cartEmpty' ) );
        }
        
        // Cart details
        $details = $this->api->fe_makeStyledContent( 'div', 'cart-details', implode( chr( 10 ), $htmlCode ) );
        
        // Check render mode
        if( $editable ) {
            
            // Form parameters
            $formParams = array(
                'name' => $this->prefixId . '-shoppingCart',
                'action' => $this->pi_linkTP_keepPIvars_url(),
                'method' => 'post',
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
            );
            
            // Return form
            return $this->api->fe_makeStyledContent( 'form', 'cart-form', $details, 1, false, false, $formParams );
            
        } else {
            
            // Return cart details
            return $details;
        }
    }
    
    /**
     * 
     */
    function checkOut()
    {
        
        // Check for items in cart
        if( count( $this->cart->data ) && $this->conf[ 'disableCart' ] != 1 ) {
            
            // Check for POST data
            if ( isset( $this->piVars[ 'checkoutInfos' ] ) ) {
                
                // Store form data
                $this->formData[ 'client' ] = $this->piVars[ 'client' ];
                
                // Verify form
                $clientComplete = $this->verifyForm( $this->conf[ 'clientFields.' ], 'client' );
                
                // Unset plugin variables
                unset( $this->piVars[ 'checkoutInfos' ] );
                unset( $this->piVars[ 'client' ] );
            }
            
            // Storage
            $htmlCode = array();
            $options  = array();
            
            // Check if form was completed
            if( $clientComplete ) {
                
                // Save order informations
                $this->saveOrder();
                
                // Header
                $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'pay-header', $this->pi_getLL( 'payementHeader' ) );
                
                // Logo
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'pay-picture', $this->cObj->IMAGE( $this->conf[ 'payLogo.' ] ) );
                
                // Informations
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'pay-infos', $this->pi_getLL( 'payementInfos' ) );
                
                // Payement form
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'pay-form', $this->payForm() );
                
            } else {
                
                // Header
                $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'checkout-header-order', $this->pi_getLL( 'checkoutOrder' ) );
                
                if( isset( $this->formErrors[ 'client' ][ 'cart' ] ) ) {
                    
                    // Error in the cart
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'error', $this->formErrors[ 'client' ][ 'cart' ] );
                }
                
                // Show cart details
                $htmlCode[] = $this->cartItems();
                
                // Edit cart
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'checkout-editcart', $this->api->fe_linkTP_unsetPIvars( $this->pi_getLL( 'cartUpdate' ), array( 'showCart' => 11 ), array( 'checkOut' ) ) );
                
                // Infos header
                $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'checkout-header-clientInfos', $this->pi_getLL( 'checkoutClientInfos' ) );
                
                // Build client form
                $htmlCode[] = $this->clientForm();
            
                // Back link
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'back', $this->api->fe_linkTP_unsetPIvars( $this->pi_getLL( 'back' ), array(), array( 'checkOut' ) ) );
            }
            
            // Return code
            return implode( chr( 10 ), $htmlCode );
        }
    }
    
    /**
     * 
     */
    function payForm()
    {
        // Storage
        $htmlCode = array();
        
        // Payement URL
        $payURL = ( $this->conf[ 'payTest' ] == 1 ) ? $this->conf[ 'payUrl.' ][ 'test' ] : $this->conf[ 'payUrl.' ][ 'real' ];
        
        // Form parameters
        $formParams = array(
            'name' => $this->prefixId . '-payInfos',
            'action' => $payURL,
            'method' => 'post',
            'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
        );
        
        // Number of items in the cart
        $itemsCount = $this->cart->countItems();
        
        // Cart total
        $cartTotal = $this->cart->getTotal();
        
        // Payement taxes
        $taxesPay = $this->conf[ 'taxesPay' ] * $itemsCount;
        
        // Delivery taxes, if necessary
        $taxesDelivery = ( $this->formData[ 'client' ][ 'payement' ] == 0 ) ? $this->conf[ 'taxesPostal' ] : 0;
        
        // Payement total
        $txtOrderTotal = number_format( $cartTotal + $taxesPay + $taxesDelivery, 2, '.', '' );
        
        // Hash
        $txtHash = $this->conf[ 'shopId' ] . $this->conf[ 'currency' ] . $txtOrderTotal . $this->conf[ 'hashSeed' ];
        
        // Order ID
        $txtOrderIdShop = $this->formData[ 'client' ][ 'orderid' ];
        
        // Hidden inputs
        $htmlCode[] = '<input type="hidden" name="txtShopId" value="' . $this->conf[ 'shopIdFull' ] . '" />';
        $htmlCode[] = '<input type="hidden" name="txtLangVersion" value="' . $this->conf[ 'langId' ] . '" />';
        $htmlCode[] = '<input type="hidden" name="txtOrderIDShop" value="' . $txtOrderIdShop . '" />';
        $htmlCode[] = '<input type="hidden" name="txtOrderTotal" value="' . $txtOrderTotal . '" />';
        $htmlCode[] = '<input type="hidden" name="txtArtCurrency" value="' . $this->conf[ 'currency' ] . '" />';
        $htmlCode[] = '<input type="hidden" name="txtHash" value="' . md5( $txtHash ) . '" />';
        $htmlCode[] = '<input type="hidden" name="txtBLastName" value="' . $this->formData[ 'client' ][ 'name_last' ] . '" />';
        $htmlCode[] = '<input type="hidden" name="txtBFirstName" value="' . $this->formData[ 'client' ][ 'name_first' ] . '" />';
        $htmlCode[] = '<input type="hidden" name="txtBAddr1" value="' . $this->formData[ 'client' ][ 'address' ] . '" />';
        $htmlCode[] = '<input type="hidden" name="txtBZipCode" value="' . $this->formData[ 'client' ][ 'zip' ] . '" />';
        $htmlCode[] = '<input type="hidden" name="txtBCity" value="' . $this->formData[ 'client' ][ 'city' ] . '" />';
        $htmlCode[] = '<input type="hidden" name="txtBEmail" value="' . $this->formData[ 'client' ][ 'email' ] . '" />';
        
        // Submit
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'pay-form-submit', '<input name="submit" type="submit" value="' . $this->pi_getLL( 'payementSubmit' ) .  '" />' );
        
        // Return form        
        return $this->api->fe_makeStyledContent( 'form', 'pay-form', implode( chr( 10 ), $htmlCode ), 1, false, false, $formParams );

    }
    
    /**
     * 
     */
    function clientForm()
    {
        
        // Storage
        $htmlCode = array();
        
        // Build fields
        $htmlCode[] = $this->buildFields( $this->conf[ 'clientFields.' ], 'client' );
        
        // Submit
        $submit = $this->api->fe_makeStyledContent(
            'input',
            'submit',
            false,
            1,
            false,
            false,
            array(
                'type' => 'submit',
                'value' => $this->pi_getLL( 'checkoutInfosSubmit' ),
                'name' => $this->prefixId . '[checkoutInfos]'
            )
        );
        
        // Add submit
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'checkout-submit', $submit );
        
        // Form parameters
        $formParams = array(
            'name' => $this->prefixId . '-clientInfos',
            'action' => $this->pi_linkTP_keepPIvars_url(),
            'method' => 'post',
            'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
        );
        
        // Order ID
        $htmlCode[] = '<input name="' . $this->prefixId . '[client][orderid]' . '" type="hidden" value="' . uniqid( $this->conf[ 'edition' ] . '-' ) . '" />';
        
        // Form
        $form = $this->api->fe_makeStyledContent( 'form', 'client-form', implode( chr( 10 ), $htmlCode ), 1, false, false, $formParams );
        
        // Return form
        return $this->api->fe_makeStyledContent( 'div', 'checkout-clientForm', $form );
    }
    
    /**
     * 
     */
    function buildFields( $conf, $piVarsSection )
    {
        
        // Storage
        $htmlCode = array();
        $options  = array();
        
        // Label for payement options
        $labelOptions = $this->api->fe_makeStyledContent( 'div', 'label', $this->api->fe_makeStyledContent( 'span', 'fieldLabel', $this->pi_getLL( 'checkoutPayOptions' ) ) );
        
        // Start select
        $options[] = '<select name="' . $this->prefixId . '[' . $piVarsSection . '][payement]' . '">';
        
        // Process payement options
        for( $i = 0; $i < 2; $i++ ) {
            
            // Checks for postal delivery
            if( $i == 0 && $this->conf[ 'postalDelivery' ] != 1 ) {
                
                // Not available
                continue;
            }
            
            // Taxes
            $taxes = ( $i == 0 ) ? ' ( + ' . $this->conf[ 'currency' ] . ' ' . number_format( $this->conf[ 'taxesPostal' ], 2, '.', '\'' ) . ' )' : '';
            
            // Add option tag
            $options[] = '<option value="' . $i .  '">' . $this->pi_getLL( 'checkoutPayOptions.I.' . $i ) . $taxes . '</option>';
        }
        
        // End select
        $options[] = '</select>';
        
        // Payement options field
        $fieldOptions = $this->api->fe_makeStyledContent( 'div', 'field', implode( chr( 10 ), $options ) );
        
        // Add Gt options
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'field-payOptions', $labelOptions . $fieldOptions );
        
        // Process each field
        foreach( $conf as $key => $value ) {
            
            // Storage
            $fieldCode = array();
            
            // Field name
            $fieldName = substr( $key, 0, strlen( $key ) - 1 );
            
            // Required state
            $required = ( isset( $value[ 'required' ] ) && $value[ 'required' ] == 1 ) ? 1 : 0;
            
            // Label
            $label = $this->api->fe_makeStyledContent( 'span', 'fieldLabel', $this->pi_getLL( 'clientFields-' . $fieldName ) );
            
            // Check if field is required
            if( $required ) {
                
                // Add required wildcard
                $label .= ' ' . $this->api->fe_makeStyledContent( 'span', 'required', '*' );
            }
            
            // Add label
            $fieldCode[] = $this->api->fe_makeStyledContent( 'div', 'label', $label );
            
            // Check for an error message
            if ( isset( $this->formErrors[ $piVarsSection ][ $fieldName ] ) ) {
                
                // Add error message
                $fieldCode[] = $this->api->fe_makeStyledContent( 'div', 'error', $this->formErrors[ $piVarsSection ][ $fieldName ] );
            }
            
            // Field value if any
            $fieldValue = ( isset( $this->formData[ $piVarsSection ][ $fieldName ] ) ) ? $this->formData[ $piVarsSection ][ $fieldName ] : '';
            
            // Check field type
            if( $value[ 'type' ] == 'input' ) {
                
                // Input parameters
                $inputParams = array(
                    'type'  => 'text',
                    'size'  => $value[ 'size' ],
                    'value' => $fieldValue,
                    'name'  => $this->prefixId . '[' . $piVarsSection . '][' . $fieldName . ']'
                );
                
                // Checks for a maximum length
                if( isset( $value[ 'max' ] ) && $value[ 'max' ] ) {
                    
                    // Adds the max length
                    $inputParams[ 'maxlength' ] = $value[ 'max' ];
                }
                
                // Add field
                $field = $this->api->fe_makeStyledContent(
                    'input',
                    $piVarsSection . '-' . $fieldName,
                    false,
                    1,
                    false,
                    false,
                    $inputParams
                );
                
            } elseif ( $value[ 'type' ] == 'textarea' ) {
                
                // Add field
                $field = $this->api->fe_makeStyledContent(
                    'textarea',
                    $piVarsSection . '-' . $fieldName,
                    false,
                    1,
                    false,
                    true,
                    array(
                        'cols' => $value[ 'cols' ],
                        'rows' => $value[ 'rows' ],
                        'name' => $this->prefixId . '[' . $piVarsSection . '][' . $fieldName . ']'
                    )
                );
                
                // Add value and close tag
                $field .= $fieldValue;
                $field .= '</textarea>';
                
            }
            
            // Add field
            $fieldCode[] = $this->api->fe_makeStyledContent( 'div', 'field', $field );
            
            // Add full field with label and error
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'field-' . $fieldName, implode( chr( 10 ), $fieldCode ) );
        }
        
        // Label for newsletter
        $newsLabel = $this->api->fe_makeStyledContent( 'div', 'label', $this->api->fe_makeStyledContent( 'span', 'fieldLabel', $this->pi_getLL( 'checkoutNewsletter' ) ) );
        
        // Start select
        $newsletter = '<input type="checkbox" value="1" name="' . $this->prefixId . '[' . $piVarsSection . '][newsletter]' . '" checked />';
        
        // Payement options field
        $fieldNews = $this->api->fe_makeStyledContent( 'div', 'field', $newsletter );
        
        // Add payement options
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'field-newsletter', $newsLabel . $fieldNews );
        
        // Return fields
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * 
     */
    function verifyForm( $conf, $piVarsSection )
    {
        
        // Form data
        $data =& $this->formData[ $piVarsSection ];
        
        // Create error container
        $this->formErrors[ $piVarsSection ] = array();
        
        // Process each field
        foreach( $conf as $key => $value ) {
            
            // Field name
            $fieldName = substr( $key, 0, strlen( $key ) - 1 );
            
            // Verify required fields
            if( isset( $value[ 'required' ] ) && $value[ 'required' ] == 1 && empty( $data[ $fieldName ] ) ) {
                
                // Error message
                $this->formErrors[ $piVarsSection ][ $fieldName ] = $this->pi_getLL( 'formError' ) . ' ' . $this->pi_getLL( 'formError-required' );
                
                // Next fields
                continue;
            }
            
            // Verify email fields
            if( isset( $value[ 'eval' ] ) && $value[ 'eval' ] == 'email' && !t3lib_div::validEmail( $data[ $fieldName ] )) {
                
                // Error message
                $this->formErrors[ $piVarsSection ][ $fieldName ] = $this->pi_getLL( 'formError' ) . ' ' . $this->pi_getLL( 'formError-email' );
                
                // Next fields
                continue;
            }
        }
        
        // Process cart items
        foreach( $this->cart->data as $key => $value ) {
            
            // Get event row
            $row = $this->pi_getRecord( $value[ 'table' ], $key );
            
            // Tickets available
            $ticketsAvailable = ( ( $row[ 'tickets' ] - $row[ 'tickets_sold' ] )  - $row[ 'tickets_booked' ] ) - $value[ 'quantity' ];
            
            // Check number of remaining tickets
            if( $ticketsAvailable < 0 ) {
                
                // Error
                $this->formErrors[ $piVarsSection ][ 'cart' ] = $this->pi_getLL( 'checkoutCartError' );
                break;
            }
        }
        
        // Check for errors
        $result = ( count( $this->formErrors[ $piVarsSection ] ) ) ? false : true;
        
        // Return validation state
        return $result;
    }
    
    /**
     * 
     */
    function saveOrder()
    {
        // Order ID in session data
        $sessionOrderId = $GLOBALS[ 'TSFE' ]->fe_user->getKey( 'ses', 'tx_cjf_orderId' );
        
        // Check if same order ID has already been processed (back button)
        if( !$sessionOrderId || $sessionOrderId != $this->formData[ 'client' ][ 'orderid' ] ) {
        
            // Shopping cart
            $cart =& $this->cart->data;
            
            // Current time
            $time = time();
            
            // Storage page
            $pid = $this->conf[ 'pid' ];
            
            // Client row
            $client = array(
                
                // TYPO3 specific fields
                'crdate'  => $time,
                'tstamp'  => $time,
                'hidden'  => 0,
                'deleted' => 0,
                'pid'     => $pid,
                'type'    => 1,
                
                // Data fields
                'name_first'          => $this->formData[ 'client' ][ 'name_first' ],
                'name_last'           => $this->formData[ 'client' ][ 'name_last' ],
                'address'             => $this->formData[ 'client' ][ 'address' ],
                'zip'                 => $this->formData[ 'client' ][ 'zip' ],
                'city'                => $this->formData[ 'client' ][ 'city' ],
                'country'             => $this->formData[ 'client' ][ 'country' ],
                'email'               => $this->formData[ 'client' ][ 'email' ],
                'phone_professionnal' => $this->formData[ 'client' ][ 'phone_professionnal' ],
                'phone_personnal'     => $this->formData[ 'client' ][ 'phone_personnal' ],
                'fax'                 => $this->formData[ 'client' ][ 'fax' ],
                'ip'                  => t3lib_div::getIndpEnv( 'REMOTE_ADDR' ),
                'newsletter'          => $this->formData[ 'client' ][ 'newsletter' ]
            );
            
            // Insert client in database
            $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( $this->extTables[ 'clients' ], $client );
            
            // Client ID
            $clientId = $GLOBALS[ 'TYPO3_DB' ]->sql_insert_id();
            
            // Process cart items
            foreach( $cart as $key => $value ) {
                
                // Order row
                $order = array(
                    
                    // TYPO3 specific fields
                    'crdate'  => $time,
                    'tstamp'  => $time,
                    'hidden'  => 0,
                    'deleted' => 0,
                    'pid'     => $pid,
                    
                    // Data fields
                    'id_client' => $clientId,
                    'id_event'  => $key,
                    'quantity'  => $value[ 'quantity' ],
                    'price'     => $value[ 'price' ],
                    'total'     => $value[ 'price' ] * $value[ 'quantity' ],
                    'confirmed' => 0,
                    'type'      => $this->formData[ 'client' ][ 'payement' ],
                    'orderid'   => $this->formData[ 'client' ][ 'orderid' ],
                );
                
                // Insert client in database
                $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( $this->extTables[ 'orders' ], $order );
            
                // Get event row
                $row = $this->pi_getRecord( $value[ 'table' ], $key );
                
                // Number of tickets solds
                $event = array(
                    'tickets_sold' => $row[ 'tickets_sold' ] + $value[ 'quantity' ],
                    'tstamp'       => $time
                );
                
                // Update event
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $value[ 'table' ], 'uid=' . $key, $event );
            }
            
            // Store order ID in session data
            $GLOBALS[ 'TSFE' ]->fe_user->setKey( 'ses', 'tx_cjf_orderId', $this->formData[ 'client' ][ 'orderid' ] );
            
            // Create PDF
            $this->createOrderPDF( $this->formData[ 'client' ][ 'orderid' ] );
            
            // Clear page cache
            $this->clearPageCache( $GLOBALS[ 'TSFE' ]->id );
        }
        
        // Data was saved
        return true;
    }
    
    /**
     * 
     */
    function checkPayReturn()
    {
        // GET variables (used if piVars are not working for an unknown reason)
        $GET  = t3lib_div::_GET();
        
        // Storage
        $htmlCode = array();
        
        // Check for order confirmation
        if( isset( $this->piVars[ 'validorder' ] ) || isset( $GET[ 'tx_cjf_pi1' ][ 'validorder' ] ) ) {
            
            // Order ID
            $orderId = t3lib_div::_POST( 'txtOrderIDShop' );
            #$orderId = '2007-45ad61ce2b564';
            
            // Check order ID
            if( !empty( $orderId ) ) {
                    
                // Transaction ID
                $transactionId = t3lib_div::_POST( 'txtTransactionID' );
                
                // Orders fields
                $orderFields = array(
                    'confirmed'      => 1,
                    'transaction_id' => $transactionId
                );
                
                // Validate orders
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                    $this->extTables[ 'orders' ],
                    'orderid=' . $GLOBALS[ 'TYPO3_DB' ]->fullQuoteStr( $orderId, $this->extTables[ 'orders' ] ) . $this->cObj->enableFields( $this->extTables[ 'orders' ] ),
                    $orderFields
                );
                
                // Send confirmation mail
                $this->confirmationMail( $orderId );
            }
            
            // HTTP 200 code
            header( 'HTTP/1.1 200 OK' );
            
            // Exit script
            exit();
        }
        
        // Check for return
        if( isset( $this->piVars[ 'postfinance' ] ) || isset( $GET[ 'tx_cjf_pi1' ][ 'postfinance' ] ) ) {
            
            // Return value
            $return = ( isset( $this->piVars[ 'postfinance' ] ) ) ? $this->piVars[ 'postfinance' ] : $GET[ 'tx_cjf_pi1' ][ 'postfinance' ];
            
            switch( $return ) {
                
                // Success
                case '1':
                    
                    // Empty cart
                    $this->cart->emptyCart();
                    
                    // Check for an order ID
                    if( $orderId = $GLOBALS[ 'TSFE' ]->fe_user->getKey( 'ses', 'tx_cjf_orderId' ) ) {
                        
                        // Select orders
                        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                            '*',
                            $this->extTables[ 'orders' ],
                            'orderid=' . $GLOBALS[ 'TYPO3_DB' ]->fullQuoteStr( $orderId, $this->extTables[ 'orders' ] ) . $this->cObj->enableFields( $this->extTables[ 'orders' ] )
                        );
                        
                        // Check MySQL result
                        if( $res ) {
                            
                            // Get order
                            $order = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res );
                        }
                    }
                    if( isset( $this->piVars[ 'download' ] ) && is_array( $order ) && $order[ 'type' ] == 1 ) {
                            
                        // Check for a PDF
                        if( !empty( $order[ 'pdf' ] ) ) {
                            
                            // PDF file name
                            $fileInfos = explode( '/', $order[ 'pdf' ] );
                            $fileName = array_pop( $fileInfos );
                            
                            // Read PDF file
                            $handle = fopen( $order[ 'pdf' ], 'r' );
                            $pdf = fread( $handle, filesize( $order[ 'pdf' ] ) );
                            fclose( $handle );
                            
                            // Delete order ID
                            $GLOBALS[ 'TSFE' ]->fe_user->setKey( 'ses', 'tx_cjf_orderId', false );
                            
                            // Output PDF
                            $this->api->div_output( $pdf, 'application', $fileName );
                            exit();
                        }
                    }
                    
                    // Header
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'payReturn-header', $this->pi_getLL( 'payReturn-successHeader' ) );
                    
                    // Message
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-message', $this->pi_getLL( 'payReturn-successMessage' ) );
                    
                    // Note for coupons
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-message-voucher', $this->pi_getLL( 'payReturn-successMessage-voucher' ) );
                    
                    // Download link
                    if( is_array( $order ) && $order[ 'type' ] == 1 ) {
                        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-message-download', $this->pi_linkTP_keepPIvars( $this->pi_getLL( 'payReturn-successMessage-download' ), array( 'download' => 1 ) ) );
                    }
                    
                    // Back to the events list
                    $links[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-links-link', $this->pi_linkTP( $this->pi_getLL( 'payReturn-backToList' ) ) );
                    
                    // Add links
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-links', implode( chr( 10 ), $links ) );
                    break;
                
                // Failed
                case '2':
                    
                    // Header
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'payReturn-header', $this->pi_getLL( 'payReturn-failedHeader' ) );
                    
                    // Message
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-message', $this->pi_getLL( 'payReturn-failedMessage' ) );
                    
                    // Back to the events list
                    $links[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-links-link', $this->pi_linkTP( $this->pi_getLL( 'payReturn-backToList' ) ) );
                    
                    // Show cart
                    $links[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-links-link', $this->pi_linkTP( $this->pi_getLL( 'payReturn-showCart' ), array( 'tx_cjf_pi1[showCart]' => 1 ) ) );
                    
                    // Add links
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-links', implode( chr( 10 ), $links ) );
                    
                    // Unset order ID
                    $GLOBALS[ 'TSFE' ]->fe_user->setKey( 'ses', 'tx_cjf_orderId', false );
                    break;
                
                // Abort
                case '3':
                    
                    // Header
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'payReturn-header', $this->pi_getLL( 'payReturn-abortHeader' ) );
                    
                    // Message
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-message', $this->pi_getLL( 'payReturn-abortMessage' ) );
                    
                    // Back to the events list
                    $links[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-links-link', $this->pi_linkTP( $this->pi_getLL( 'payReturn-backToList' ) ) );
                    
                    // Show cart
                    $links[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-links-link', $this->pi_linkTP( $this->pi_getLL( 'payReturn-showCart' ), array( 'tx_cjf_pi1[showCart]' => 1 ) ) );
                    
                    // Add links
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'payReturn-links', implode( chr( 10 ), $links ) );
                    
                    // Unset order ID
                    $GLOBALS[ 'TSFE' ]->fe_user->setKey( 'ses', 'tx_cjf_orderId', false );
                    break;
            }
            
            // Return content
            return implode( chr( 10 ), $htmlCode );
        }
    }
    
    /**
     * 
     */
    function createOrderPDF( $orderId )
    {
        
        // PDF configuration
        $conf =& $this->conf[ 'pdf.' ];
        
        // PDF path
        $path = t3lib_div::getFileAbsFileName( $this->conf[ 'pdfStorage' ] . $orderId . '.pdf' );
        
        // Select orders
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            '*',
            $this->extTables[ 'orders' ],
            'orderid=' . $GLOBALS[ 'TYPO3_DB' ]->fullQuoteStr( $orderId, $this->extTables[ 'orders' ] ) . $this->cObj->enableFields( $this->extTables[ 'orders' ] )
        );
        
        // Storage
        $orders = array();
        $events = array();
        
        // Get each order
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
            
            // Store order
            $orders[] = $row;
            
            // Get related event
            $events[ $row[ 'id_event' ] ] = $this->pi_getRecord( $this->extTables[ 'events' ], $row[ 'id_event' ] );
        }
        
        // Check orders
        if( count( $orders ) ) {
            
            // Client ID
            $clientId = $orders[ 0 ][ 'id_client' ];
            
            // Order type
            $orderType = $orders[ 0 ][ 'type' ];
            
            // Get client row
            $client = $this->pi_getRecord( $this->extTables[ 'clients' ], $clientId );
            
            // New PDF
            $pdf = new tx_cjf_pdf( $conf[ 'orientation' ], $conf[ 'unit' ], $conf[ 'format' ] );
            
            // Store variables
            $pdf->client      =& $client;
            $pdf->title       =  $this->conf[ 'pdfTitle' ];
            $pdf->pObj        =& $this;
            $pdf->orderId     =  $orderId;
            $pdf->date        =  strftime( $this->conf[ 'dateFormat' ] . ' / ' . $this->conf[ 'hourFormat' ], $orders[ 0 ][ 'crdate' ] );
            $pdf->orders      =& $orders;
            $pdf->events      =& $events;
            $pdf->ticketTaxes = $this->conf[ 'taxesPay' ];
            $pdf->postalTaxes = $this->conf[ 'taxesPostal' ];
            $pdf->currency    = $this->conf[ 'currency' ];
            $pdf->orderType   = $orderType;
            
            // New page
            $pdf->AddPage();
            
            // Add order details
            $pdf->orderInfos();
            
            // Output PDF
            $pdf->Output( $path );
            
            // Store PDF path in orders
            $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                $this->extTables[ 'orders' ],
                'orderid=' . $GLOBALS[ 'TYPO3_DB' ]->fullQuoteStr( $orderId, $this->extTables[ 'orders' ] ) . $this->cObj->enableFields( $this->extTables[ 'orders' ] ),
                array( 'pdf' => $path )
            );
        }
    }
    
    /**
     * 
     */
    function confirmationMail( $orderId )
    {
        // Select orders
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            '*',
            $this->extTables[ 'orders' ],
            'orderid=' . $GLOBALS[ 'TYPO3_DB' ]->fullQuoteStr( $orderId, $this->extTables[ 'orders' ] ) . $this->cObj->enableFields( $this->extTables[ 'orders' ] )
        );
        
        // Storage
        $orders = array();
        
        // Get each order
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
            
            // Store order
            $orders[] = $row;
        }
        
        // Check orders
        if( count( $orders ) ) {
            
            // Client ID
            $clientId = $orders[ 0 ][ 'id_client' ];
            
            // Order type
            $orderType = $orders[ 0 ][ 'type' ];
            
            // PDF path
            $path = $orders[ 0 ][ 'pdf' ];
            
            // Get client row
            $client = $this->pi_getRecord( $this->extTables[ 'clients' ], $clientId );
            
            // Mail informations
            $to      = $client[ 'email' ];
            $subject = $this->conf[ 'emailTitle' ];
            $message = $this->conf[ 'emailBody' ];
            $replyTo = $this->conf[ 'email' ];
            $from    = $this->conf[ 'email' ];
            
            // Storage
            $mime      = array();
            $multipart = array();
            
            // Message parts
            $parts = array(
                
                // Message
                array(
                    'ctype'   => 'text/plain',
                    'message' => $message,
                    'name'    => '',
                )
            );
            
            // Check for a file to send
            if( $path && $orderType == 1 ) {
                
                // File name
                $fileInfos = explode( '/', $path );
                $fileName  = array_pop( $fileInfos );
                
                // Read PDF
                 $handle     = fopen( $path, 'r' );
                 $pdfContent = fread( $handle, filesize( $path ) );
                 fclose( $handle );
                
                // Add attachement
                $parts[] = array(
                    'ctype'   => 'application/pdf',
                    'message' => $pdfContent,
                    'name'    => $fileName,
                );
            }
            
            // Add base infos
            $mime[] = 'From: ' . $from;
            $mime[] = 'To: ' . $to;
            $mime[] = 'Subject: ' . $subject;
            
            // MIME version
            $mime[] = 'MIME-Version: 1.0';
            
            // Message boundary
            $boundary = 'b' . md5( uniqid( time() ) );
            
            // Multipart flag
            $multipart = array(
                'Content-Type: multipart/mixed; boundary = ' . $boundary . chr( 10 ) . chr( 10 ) . 'This is a MIME encoded message.' . chr( 10 ) . chr( 10 ) . '--' . $boundary,
            );
            
            // Process parts
            foreach( $parts as $value ) {
                
                // Get and encode message
                $encodedMessage = chunk_split( base64_encode( $value[ 'message' ] ) );
                
                // Encoding type
                $encoding =  'base64';
                
                // Full message
                $fullMessage = 'Content-Type: ' . $value[ 'ctype' ] . ( ( $value[ 'name' ] ) ? '; name = \'' . $value[ 'name' ] . '\'' : '') . chr( 10 ) . 'Content-Transfer-Encoding:' . $encoding . chr( 10 ) . chr( 10 ) . $encodedMessage . chr( 10 );
                
                // Add part
                $multipart[] = $fullMessage . '--' . $boundary;
            }
            
            // Add all parts
            $mime[] = implode( chr( 10 ), $multipart ) . '--' . chr(10) . chr(10);
            
            // Send mail
            mail( $to, $subject, '', implode( chr( 10 ), $mime ) );
        }
    }
    
    /**
     * 
     */
    function clearPageCache($pid)
    {
        
        // Delete page cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pages', 'page_id=' . $pid );
        
        // Delete page section cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pagesection', 'page_id=' . $pid );
    }
    
    function numberFormat( $number, $decimals = 0 )
    {
        
        // Return formatted number
        return number_format( $number, $decimals, '.', '\'' );
    }
}

 // XCLASS inclusion
if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/cjf/pi1/class.tx_cjf_pi1.php' ] ) {
    include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/cjf/pi1/class.tx_cjf_pi1.php' ] );
}
