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
     * Plugin 'VD / GeoMap' for the 'vd_geomap' extension.
     *
     * @author      Jean-David Gadina (info@macmade.net)
     * @version     1.0
     */
    
    /**
     * [CLASS/FUNCTION INDEX OF SCRIPT]
     * 
     * SECTION:     1 - MAIN
     *     125:     function main($content,$conf)
     *     200:     function setConfig
     *     227:     function displayMap
     *     302:     function getPicture
     *     335:     function buildScaleBar
     *     424:     function getIcons
     * 
     *              TOTAL FUNCTIONS: 6
     */
    
    // Typo3 FE plugin class
    require_once( PATH_tslib . 'class.tslib_pibase.php' );
    
    // Developer API class
    require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );
    
    // Xajax class
    require_once ( t3lib_extMgm::extPath( 'xajax' ) . 'class.tx_xajax.php' );
    
    class tx_vdgeomap_pi1 extends tslib_pibase
    {
        
        
        
        
        
        /***************************************************************
         * SECTION 0 - VARIABLES
         *
         * Class variables for the plugin.
         ***************************************************************/
        
        // Same as class name
        var $prefixId           = 'tx_vdgeomap_pi1';
        
        // Path to this script relative to the extension dir
        var $scriptRelPath      = 'pi1/class.tx_vdgeomap_pi1.php';
        
        // The extension key
        var $extKey             = 'vd_geomap';
        
        // Version of the Developer API required
        var $apimacmade_version = 2.8;
        
        // Check plugin hash
        var $pi_checkCHash      = true;
        
        // Icons storage
        var $icons              = array();
        
        // Xajax configuration
        var $xajax;
        
        // Page link
        var $pageLink;
        
        // Page ID
        var $pid;
        
        // Map properties
        var $xpos;
        var $ypos;
        var $scale;
        var $layers;
        var $move;
        
        // Availables scales
        var $availableScales    = array(
            850000,
            500000,
            200000,
            100000,
            50000,
            25000,
            10000,
            5000,
            3000
        );
        
        
        
        
        
        /***************************************************************
         * SECTION 1 - MAIN
         *
         * Functions for the initialization and the output of the plugin.
         ***************************************************************/
        
        /**
         * Returns the content object of the plugin.
         * 
         * This function initialises the plugin "tx_mozsearch_pi1", and
         * launches the needed functions to correctly display the plugin.
         * 
         * @param       $content            The content object
         * @param       $conf               The TS setup
         * @return      The content of the plugin.
         * @see         setConfig
         * @see         displayMap
         */
        function main( $content, $conf )
        {
            // Set page ID
            $this->pid = $GLOBALS[ 'TSFE' ]->id;
            
            // New instance of the macmade.net API
            $this->api = new tx_apimacmade( $this );
            
            // Set default plugin variables
            $this->pi_setPiVarDefaults();
            
            // Load locallang labels
            $this->pi_loadLL();
            
            // Set class confArray TS from the function
            $this->conf = $conf;
            
            // Init flexform configuration of the plugin
            $this->pi_initPIflexForm();
            
            // Store flexform informations
            $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
            
            // Set final configuration (TS or FF)
            $this->setConfig();
            
            // Check for Xajax
            if( $this->conf[ 'xajax' ] ) {
            
                // New instance of Xajax
                $this->xajax = t3lib_div::makeInstance( 'tx_xajax' );
                
                // Decode form vars from utf-8
                $this->xajax->decodeUTF8InputOn();
                
                // Encode of the response to utf-8
                $this->xajax->setCharEncoding( 'utf-8' );
                
                // Prepend the extension prefix
                $this->xajax->setWrapperPrefix( $this->prefixId );
                
                // Toggle messages in the status bar
                $this->xajax->statusMessagesOn();
                
                // Toggle debug
                #$this->xajax->debugOn();
                
                // Method to call through Xajax
                $this->xajax->registerFunction(
                    array(
                        'processXajax',
                        &$this,
                        'processXajax'
                    )
                );
                
                // Process requests
                $this->xajax->processRequests();
                
                // Add JavaScript code to the header
                $GLOBALS[ 'TSFE' ]->additionalHeaderData[ $this->prefixId ] = $this->xajax->getJavascript( t3lib_extMgm::siteRelPath( 'xajax' ) );
                
                // Set Xajax configuration
                $this->setXajaxConfig();
            }
            
            // Check for a map URL
            if( empty( $this->conf[ 'url' ] ) ) {
                
                $content = $this->api->fe_makeStyledContent( 'div', 'error', $this->pi_getLL( 'urlError' ) );
                
            } else {
                
                // Build plugin content
                $content = $this->buildContent();
            }
            
            // Return content
            return $this->pi_wrapInBaseClass( '<div id="' . $this->prefixId . '">' . $content . '</div>' );
        }
        
        /**
         * 
         */
        function processXajax( $data )
        {
            // Get Xajax data as an array
            $dataArray = explode( ',', $data );
            
            // Configuration
            $conf      = explode( ':', $dataArray[ 0 ] );
            
            // Plugin variables
            $piVars    = explode( ':', $dataArray[ 1 ] );
            
            // Page ID
            $pid       = explode( ':', $dataArray[ 2 ] );
            
            // Store configuration, plugin variables and page ID
            $this->conf   = unserialize( base64_decode( $conf[ 1 ] ) );
            $this->piVars = unserialize( base64_decode( $piVars[ 1 ] ) );
            $this->pid    = $pid[ 1 ];
            
            // Set Xajax configuration
            $this->setXajaxConfig();
            
            // Load locallang labels
            $this->pi_loadLL();
            
            // Refresh plugin
            $content = $this->buildContent();
            
            // New Xajax response object
            $objResponse = new tx_xajax_response();
            
            // Add the content to result div
            $objResponse->addAssign( $this->prefixId, 'innerHTML', $content );
            
            // Return XML response
            return $objResponse->getXML();
        }
        
        /**
         * Set configuration array
         * 
         * This function is used to set the final configuration array of the
         * plugin, by providing a mapping array between the TS & the flexform
         * configuration.
         * 
         * @return      Void
         */
        function setConfig()
        {
            // Mapping array for PI flexform
            $flex2conf = array(
                'xpos'    => 'sDEF:xpos',
                'ypos'    => 'sDEF:ypos',
                'scale'   => 'sDEF:uscale',     // Added by Dung Nguyen - 11.06.2007
                'showMap' => 'sDEF:showmap',
            );
            
            // Ovverride TS setup with flexform
            $this->conf = $this->api->fe_mergeTSconfFlex( $flex2conf, $this->conf, $this->piFlexForm );
            
            // DEBUG ONLY - Output configuration array
            #$this->api->debug($this->conf,'VD / Geomap: configuration array');
        }
        
        /**
         * 
         */
        function setXajaxConfig()
        {
            // Create configuration array for Xajax
            $xajaxConf = array(
                'url'            => $this->conf[ 'url' ],
                'scale'          => $this->conf[ 'scale' ],
                'imgWidth'       => $this->conf[ 'imgWidth' ],
                'imgHeight'      => $this->conf[ 'imgHeight' ],
                'urlParameters.' => $this->conf[ 'urlParameters.' ],
                'res.'           => $this->conf[ 'res.' ],
                'xpos'           => $this->conf[ 'xpos' ],
                'ypos'           => $this->conf[ 'ypos' ],
                'showMap'        => $this->conf[ 'showMap' ],
                'xajax'          => $this->conf[ 'xajax' ]
            );
            
            // Store configuration
            $this->xajaxConf = base64_encode( serialize( $xajaxConf ) );
        }
        
        /**
         * 
         */
        function buildContent()
        {
            
            // Page link
            $this->pageLink = $this->pi_getPageLink( $this->pid ) . '#' . $this->prefixId;
            
            // Storage
            $htmlCode = array();
            
            // Add anchor
            $htmlCode[] = '<a name="' . $this->prefixId . '"></a>';
            
            // Display mode
            $displayMode = ( isset( $this->piVars[ 'showMap' ] ) ) ? $this->piVars[ 'showMap' ] : $this->conf[ 'showMap' ];
            
            // Check if map must be displayed
            if ( $displayMode == 0 ) {
                
                // Show / hide text
                $displayText = $this->pi_getLL( 'show' );
                
                // Check for Xajax
                if( $this->conf[ 'xajax' ] == 0 ) {
                    
                    // Link to show / hide map
                    $link = $this->api->fe_linkTP_unsetPIvars_url(
                        array( 'showMap' => '1' ),
                        array( 'xpos', 'ypos', 'scale' )
                    );
                    
                    // Full link
                    $displayLink = $this->cObj->typoLink(
                        $displayText,
                        array(
                            'parameter' => $link,
                            'title'     => $this->pi_getLL( 'show-title' )
                        )
                    );
                    
                } else {
                    
                    // Plugin variables for Xajax
                    $xajaxPiVars = base64_encode( serialize( array( 'showMap' => '1' ) ) );
                    
                    // Ajax link
                    $displayLink = '<a href="' . $this->pageLink . '" title="' . $this->pi_getLL( 'show-title' ) . '" onclick="javascript:' . $this->prefixId . 'processXajax( \'conf:' . $this->xajaxConf . ',piVars:' . $xajaxPiVars . ',pid:' . $this->pid . '\' );">' . $displayText . '</a>';
                }
                
                // Add link
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'showHide', $displayLink );
                
            } else {
                
                // Show / hide text
                $displayText = $this->pi_getLL( 'hide' );
                
                // Check for Xajax
                if( $this->conf[ 'xajax' ] == 0 ) {
                
                    // Link to show / hide map
                    $link = $this->api->fe_linkTP_unsetPIvars_url(
                        array( 'showMap' => '0' ),
                        array( 'xpos', 'ypos', 'scale' )
                    );
                    
                    // Full link
                    $displayLink = $this->cObj->typoLink(
                        $displayText,
                        array(
                            'parameter' => $link,
                            'title'     => $this->pi_getLL( 'hide-title' )
                        )
                    );
                    
                } else {
                    
                    // Plugin variables for Xajax
                    $xajaxPiVars = base64_encode( serialize( array( 'showMap' => '0' ) ) );
                    
                    // Ajax link
                    $displayLink = '<a href="' . $this->pageLink . '" title="' . $this->pi_getLL( 'show-title' ) . '" onclick="javascript:' . $this->prefixId . 'processXajax( \'conf:' . $this->xajaxConf . ',piVars:' . $xajaxPiVars . ',pid:' . $this->pid . '\' );">' . $displayText . '</a>';
                    
                }
                
                // Add link
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'showHide', $displayLink );
                
                // Add map
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'map', $this->displayMap() );
            }
            
            // Return content
            return implode( chr( 10 ), $htmlCode );
        }
        
        /**
         * Display the map
         * 
         * This function is used to display the whole map area, with scale bar
         * and directionnal buttons.
         * 
         * @return      The map area
         * @see         getIcons
         * @see         getPicture
         * @see         buildScaleBar
         */
        function displayMap()
        {
            // Map variables
            $this->xpos  = ( isset( $this->piVars['xpos'] ) )  ? $this->piVars[ 'xpos' ]  : $this->conf[ 'xpos' ];
            $this->ypos  = ( isset( $this->piVars['ypos'] ) )  ? $this->piVars[ 'ypos' ]  : $this->conf[ 'ypos' ];
            $this->scale = ( isset( $this->piVars['scale'] ) ) ? $this->piVars[ 'scale' ] : $this->conf[ 'scale' ];
            
            // Layers to display (depends of scale)
            // CS = Road map
            // CN = National map
            $this->layers = ( $this->scale < 10000 ) ? 'cs' : 'cn';
            
            // Move relative to scale
            $this->move = $this->scale / 20;
            
            // Storage
            $htmlCode  = array();
            $mapTop    = array();
            $mapMiddle = array();
            $mapBottom = array();
            
            // Get icons
            $this->getIcons();
            
            // Add icons to map top
            $mapTop[] = $this->api->fe_makeStyledContent( 'div', 'left', $this->icons[ 'left-up' ] );
            $mapTop[] = $this->api->fe_makeStyledContent( 'div', 'up', $this->icons[ 'up' ] );
            $mapTop[] = $this->api->fe_makeStyledContent( 'div', 'right', $this->icons[ 'right-up' ] );
            
            // Add left icon
            $mapMiddle[] = $this->api->fe_makeStyledContent( 'div', 'right', $this->icons[ 'left' ] );
            
            // Add picture
            $mapMiddle[] = $this->api->fe_makeStyledContent( 'div', 'picture', $this->getPicture() );
            
            // Add right icon
            $mapMiddle[] = $this->api->fe_makeStyledContent( 'div', 'right', $this->icons[ 'right' ] );
            
            // Add icons to map bottom
            $mapBottom[] = $this->api->fe_makeStyledContent( 'div', 'left', $this->icons[ 'left-down' ] );
            $mapBottom[] = $this->api->fe_makeStyledContent( 'div', 'down', $this->icons[ 'down' ] );
            $mapBottom[] = $this->api->fe_makeStyledContent( 'div', 'right', $this->icons[ 'right-down' ] );
            
            // Add map areas
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'mapTop', implode( chr( 10 ), $mapTop ) );
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'mapMiddle', implode( chr( 10 ), $mapMiddle ) );
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'mapBottom', implode( chr( 10 ), $mapBottom ) );
            
            // Check if display has changed
            if ( $this->xpos != $this->conf[ 'xpos' ] || $this->ypos != $this->conf[ 'ypos' ] || $this->scale != $this->conf[ 'scale' ] ) {
                
                // Check for Xajax
                if( $this->conf[ 'xajax' ] == 0 ) {
                    
                    // Back link
                    $back = $this->pi_linkTP_keepPIvars_url(
                        array(
                            'xpos'  => $this->conf[ 'xpos' ],
                            'ypos'  => $this->conf[ 'ypos' ],
                            'scale' => $this->conf[ 'scale' ]
                        )
                    );
                    
                    // Full link to original
                    $backLink = $this->cObj->typoLink(
                        $this->pi_getLL( 'backLink' ),
                        array(
                            'parameter' => $back,
                            'title'     => $this->pi_getLL( 'backLink-title' )
                        )
                    );
                    
                } else {
                    
                    // Plugin variables for Xajax
                    $xajaxPiVars = base64_encode( serialize( array( 'xpos' => $this->conf[ 'xpos' ], 'ypos' => $this->conf[ 'ypos' ], 'scale' => $this->conf[ 'scale' ] ) ) );
                    
                    // Ajax link
                    $backLink = '<a href="' . $this->pageLink . '" title="' . $this->pi_getLL( 'backLink-title' ) . '" onclick="javascript:' . $this->prefixId . 'processXajax( \'conf:' . $this->xajaxConf . ',piVars:' . $xajaxPiVars . ',pid:' . $this->pid . '\' );">' . $this->pi_getLL( 'backLink' ) . '</a>';
                    
                }
                
                // Back to original
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'back', $backLink );
            }
            
            // Add scale bar
            $htmlCode[] = $this->buildScaleBar();
            
            // Return map
            return implode( chr( 10 ), $htmlCode );
        }
        
        /**
         * Build the map picture
         * 
         * This function is used to build the IMG tag of the map.
         * 
         * @return      The map IMG tag
         */
        function getPicture()
        {
            // Parameters definition
            $params =& $this->conf[ 'urlParameters.' ];
            
            // Map parameters
            $mapParams = array(
                $params[ 'xpos' ]   => $this->xpos,
                $params[ 'ypos' ]   => $this->ypos,
                $params[ 'scale' ]  => $this->scale,
                $params[ 'layers' ] => $this->layers,
                $params[ 'width' ]  => $this->conf[ 'imgWidth' ],
                $params[ 'height' ] => $this->conf[ 'imgHeight' ]
            );
            
            // Map image URL
            $mapUrl = $this->conf[ 'url' ] . '?' . t3lib_div::implodeArrayForUrl( '', $mapParams, '', 1 );
            
            // Formatted scale
            $scale = '1:' . number_format( $this->scale, '.', '', '\'' );
            
            // Alt & title text
            $alt = sprintf( $this->pi_getLL( 'imgAltText' ), $scale, $this->xpos, $this->ypos );
            
            // Return picture
            return '<img src="'
                 . $mapUrl
                 . '" alt="'
                 . $alt
                 . '" title="'
                 . $alt
                 . '" width="'
                 . $this->conf[ 'imgWidth' ]
                 . '" height="'
                 . $this->conf[ 'imgHeight' ]
                 . '">';
        }
        
        /**
         * Build the scale bar
         * 
         * This function is used to build the scale bar below the map, which
         * will allow users to zoom/dezoom on the map.
         * 
         * @return      The full scale bar
         */
        function buildScaleBar()
        {
            // Storage
            $scaleBar = array();
            
            // Check first scale
            if ( $this->scale == $this->availableScales[ 0 ] ) {
                
                // Dezoom deactivated
                $icon   = $this->icons[ 'minus-off' ];
                $dezoom = $this->api->fe_makeStyledContent( 'div', 'dezoom', $icon );
                
            } else {
                
                // New scale
                $newScale = array_search( $this->scale, $this->availableScales ) - 1;
                
                // Check for Xajax
                if( $this->conf[ 'xajax' ] == 0 ) {
                    
                    // Dezoom available
                    $icon = $this->pi_linkTP_keepPIvars(
                        $this->icons[ 'minus' ],
                        array(
                            'scale' => $this->availableScales[ $newScale ]
                        )
                    );
                    
                } else {
                        
                    // Create plugin variables array
                    $piVars = array(
                        'xpos'  => $this->xpos,
                        'ypos'  => $this->ypos,
                        'scale' => $this->availableScales[ $newScale ]
                    );
                    
                    // Plugin variables for Xajax
                    $xajaxPiVars = base64_encode( serialize( $piVars ) );
                    
                    // Ajax link
                    $icon = '<a href="' . $this->pageLink . '" onclick="javascript:' . $this->prefixId . 'processXajax( \'conf:' . $this->xajaxConf . ',piVars:' . $xajaxPiVars . ',pid:' . $this->pid . '\' );">' . $this->icons[ 'minus' ] . '</a>';
                }
                
                // Add dezoom link
                $dezoom = $this->api->fe_makeStyledContent( 'div', 'dezoom', $icon );
            }
            
            // Add dezoom
            $scaleBar[] = $dezoom;
            
            // Current scale flag
            $cur = false;
            
            // Process scales
            foreach( $this->availableScales as $value ) {
                
                // Formated scale
                $formatScale = '1:' . number_format( $value, '.', '', '\'' );
                
                // Check current scale
                if ( $value == $this->scale ) {
                    
                    // Current scale
                    $icon = sprintf( $this->icons[ 'zoom-current' ], $formatScale, $formatScale );
                    
                    // Set current flag
                    $cur = true;
                    
                } else if ( $cur ) {
                    
                    // Icon
                    $image = sprintf(
                                $this->icons[ 'zoom-decrease' ],
                                $formatScale,
                                $formatScale
                            );
                    
                    // Check for Xajax
                    if( $this->conf[ 'xajax' ] == 0 ) {
                        
                        // Scale link
                        $icon = $this->pi_linkTP_keepPIvars(
                            $image,
                            array( 'scale' => $value )
                        );
                        
                    } else {
                        
                        // Create plugin variables array
                        $piVars = array(
                            'xpos'  => $this->xpos,
                            'ypos'  => $this->ypos,
                            'scale' => $value
                        );
                    
                        // Plugin variables for Xajax
                        $xajaxPiVars = base64_encode( serialize( $piVars ) );
                        
                        // Ajax link
                        $icon = '<a href="' . $this->pageLink . '" onclick="javascript:' . $this->prefixId . 'processXajax( \'conf:' . $this->xajaxConf . ',piVars:' . $xajaxPiVars . ',pid:' . $this->pid . '\' );">' . $image . '</a>';
                    }
                    
                } else {
                    
                    // Icon
                    $image = sprintf(
                                $this->icons[ 'zoom-increase' ],
                                $formatScale,
                                $formatScale
                            );
                    
                    // Check for Xajax
                    if( $this->conf[ 'xajax' ] == 0 ) {
                    
                        // Scale link
                        $icon = $this->pi_linkTP_keepPIvars(
                            $image,
                            array( 'scale' => $value )
                        );
                        
                    } else {
                        
                        // Create plugin variables array
                        $piVars = array(
                            'xpos'  => $this->xpos,
                            'ypos'  => $this->ypos,
                            'scale' => $value
                        );
                    
                        // Plugin variables for Xajax
                        $xajaxPiVars = base64_encode( serialize( $piVars ) );
                        
                        // Ajax link
                        $icon = '<a href="' . $this->pageLink . '" onclick="javascript:' . $this->prefixId . 'processXajax( \'conf:' . $this->xajaxConf . ',piVars:' . $xajaxPiVars . ',pid:' . $this->pid . '\' );">' . $image . '</a>';
                    }
                }
                
                // Add scale
                $scaleBar[] = $this->api->fe_makeStyledContent( 'div', 'scale', $icon );
            }
            
            // Check first scale
            if ( $this->scale == $this->availableScales[ count( $this->availableScales ) - 1 ] ) {
                
                // Zoom deactivated
                $icon = $this->icons[ 'plus-off' ];
                $zoom = $this->api->fe_makeStyledContent( 'div', 'zoom', $icon );
                
            } else {
                
                // New scale
                $newScale = array_search( $this->scale, $this->availableScales ) + 1;
                
                // Check for Xajax
                if( $this->conf[ 'xajax' ] == 0 ) {
                    
                    // Zoom available
                    $icon = $this->pi_linkTP_keepPIvars(
                        $this->icons[ 'plus' ],
                        array( 'scale' => $this->availableScales[ $newScale ] )
                    );
                    
                } else {
                    
                    // Create plugin variables array
                    $piVars = array(
                        'xpos'  => $this->xpos,
                        'ypos'  => $this->ypos,
                        'scale' => $this->availableScales[ $newScale ]
                    );
                    
                    // Plugin variables for Xajax
                    $xajaxPiVars = base64_encode( serialize( $piVars ) );
                    
                    // Ajax link
                    $icon = '<a href="' . $this->pageLink . '" onclick="javascript:' . $this->prefixId . 'processXajax( \'conf:' . $this->xajaxConf . ',piVars:' . $xajaxPiVars . ',pid:' . $this->pid . '\' );">' . $this->icons[ 'plus' ] . '</a>';
                }
                
                // Add zoom link
                $zoom = $this->api->fe_makeStyledContent( 'div', 'zoom', $icon );
            }
            
            // Add dezoom
            $scaleBar[] = $zoom;
            
            // Return scale bar
            return $this->api->fe_makeStyledContent( 'div', 'scaleBar', implode( chr( 10 ), $scaleBar ) );
        }
        
        /**
         * Crete plugin icons
         * 
         * This function is used to create each icon used in the plugin.
         * Generated icons will be stored in the $this->icons array.
         * 
         * @return      Void
         */
        function getIcons()
        {
            // Process icons
            foreach( $this->conf[ 'res.' ] as $key => $val ) {
                
                // Real key
                $key = str_replace( '.', '', $key );
                
                // Alt text
                $alt = $this->pi_getLL( 'imgAlt_' . $key );
                
                // Add title & alt to pictures
                $val[ 'altText' ] = $alt;
                
                // Add link
                $link = 1;
                
                // Check key for linking
                switch( $key ) {
                    
                    // Left & Top
                    case 'left-up':
                        $x = $this->xpos - $this->move;
                        $y = $this->ypos + $this->move;
                    break;
                    
                    // Left & Top
                    case 'up':
                        $x = $this->xpos;
                        $y = $this->ypos + $this->move;
                    break;
                    
                    // Left & Top
                    case 'right-up':
                        $x = $this->xpos + $this->move;
                        $y = $this->ypos + $this->move;
                    break;
                    
                    // Left & Top
                    case 'left':
                        $x = $this->xpos - $this->move;
                        $y = $this->ypos;
                    break;
                    
                    // Left & Top
                    case 'right':
                        $x = $this->xpos + $this->move;
                        $y = $this->ypos;
                    break;
                    
                    // Left & Top
                    case 'left-down':
                        $x = $this->xpos - $this->move;
                        $y = $this->ypos - $this->move;
                    break;
                    
                    // Left & Top
                    case 'down':
                        $x = $this->xpos;
                        $y = $this->ypos - $this->move;
                    break;
                    
                    // Left & Top
                    case 'right-down':
                        $x = $this->xpos + $this->move;
                        $y = $this->ypos - $this->move;
                    break;
                    
                    // Default processing
                    default:
                        $link = 0;
                    break;
                }
                
                // Check if link must be added now
                if ( $link ) {
                    
                    // Check for Xajax
                    if( $this->conf[ 'xajax' ] == 0 ) {
                    
                        // Add generated icon with link
                        $this->icons[ $key ] = $this->pi_linkTP_keepPIvars(
                                                    $this->cObj->IMAGE( $val ),
                                                    array(
                                                        'xpos' => $x,
                                                        'ypos' => $y
                                                    )
                                               );
                        
                    } else {
                        
                        // Create plugin variables array
                        $piVars = array(
                            'xpos'  => $x,
                            'ypos'  => $y,
                            'scale' => $this->scale
                        );
                        
                        // Plugin variables for Xajax
                        $xajaxPiVars = base64_encode( serialize( $piVars ) );
                        
                        // Xajax link
                        $this->icons[ $key ] = '<a href="' . $this->pageLink . '" onclick="javascript:' . $this->prefixId . 'processXajax( \'conf:' . $this->xajaxConf . ',piVars:' . $xajaxPiVars . ',pid:' . $this->pid . '\' );">' . $this->cObj->IMAGE( $val ) . '</a>';
                    }
                    
                } else {
                    
                    // Add generated icon
                    $this->icons[ $key ] = $this->cObj->IMAGE( $val );
                }
            }
        }
    }
    
    /**
     * XCLASS inclusion
     */
    if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_geomap/pi1/class.tx_vdgeomap_pi1.php']) {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_geomap/pi1/class.tx_vdgeomap_pi1.php']);
    }
?>
