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
 * Plugin 'VD / GeoMap - Prototype JS version' for the 'vd_geomap_prototype' extension.
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

class tx_vdgeomapprototype_pi1 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_vdgeomapprototype_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_vdgeomapprototype_pi1.php';
    
    // The extension key
    var $extKey             = 'vd_geomap_prototype';
    
    // Version of the Developer API required
    var $apimacmade_version = 3.0;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Icons storage
    var $icons              = array();
    
    // Page link
    var $pageLink;
    
    // Page ID
    var $pid;
    
    // Server name
    var $serverName         = '';
    
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
        
        // Check for Ajax
        if( $this->conf[ 'ajax' ] ) {
            
            // Include Prototype JS framework
            $this->api->fe_includePrototypeJs();
            
            // Add JavaScript functions
            $this->addJsFunctions();
        }
        
        // Check for a map URL
        if( empty( $this->conf[ 'url' ] ) ) {
            
            $content = $this->api->fe_makeStyledContent(
                'div',
                'error',
                $this->pi_getLL( 'urlError' )
            );
            
        } else {
            
            // Build plugin content
            $content = $this->buildContent();
        }
        
        // Checks for an Ajax call
        if( isset( $this->piVars[ 'ajaxCall' ] ) ) {
            
            print $content;
            exit();
            
        } else {
            
            // Return content
            return $this->pi_wrapInBaseClass(
                '<div id="'
              . $this->prefixId
              . '">'
              . $content
              . '</div>'
            );
        }
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
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // HTTP scheme
        $httpScheme = ( t3lib_div::getIndpEnv( 'TYPO3_SSL' ) ) ? 'https://' : 'http';
        
        // Server name
        if( isset( $this->conf[ 'serverName' ] ) && $this->conf[ 'serverName' ] && isset( $_SERVER[ $this->conf[ 'serverName' ] ] ) ) {
            
            $this->serverName = $httpScheme . $_SERVER[ $this->conf[ 'serverName' ] ];
            
        } else {
            
            $this->serverName = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_HOST' );
        }
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'VD / Geomap: configuration array');
    }
    
    /**
     * 
     */
    function addJsFunctions()
    {
        // New line character
        $nl = chr( 10 );
        
        // JavaScript code
        $code = 'function ' . $this->prefixId . '_ajaxRequest( url )' . $nl
              . '{' . $nl
              . 'new Ajax.Updater( \''
              . $this->prefixId
              .  '\', '
              . 'url, '
              .  '{ method: \'get\' }'
              . ' );' . $nl
              . '}';
        
        // Add JavaScript code
        $GLOBALS[ 'TSFE' ]->additionalJavaScript[ $this->prefixId ] = $code;
    }
    
    /**
     * 
     */
    function buildContent()
    {
        // Page link
        $this->pageLink = $this->pi_getPageLink( $this->pid )
                        . '#'
                        . $this->prefixId;
        
        // Storage
        $htmlCode = array();
        
        // Add anchor
        $htmlCode[] = '<a name="'
                    . $this->prefixId
                    . '"></a>';
        
        // Display mode
        $displayMode = ( isset( $this->piVars[ 'showMap' ] ) ) ? $this->piVars[ 'showMap' ] : $this->conf[ 'showMap' ];
        
        // Check if map must be displayed
        if ( $displayMode == 0 ) {
            
            // Show / hide text
            $displayText = $this->pi_getLL( 'show' );
            
            // Check for Ajax
            if( $this->conf[ 'ajax' ] == 0 ) {
                
                // Link to show / hide map
                $link = $this->api->fe_linkTP_unsetPIvars_url(
                    array(
                        'showMap' => '1'
                    ),
                    array(
                        'xpos',
                        'ypos',
                        'scale'
                    )
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
                
                // Link to show / hide map
                $link = $this->api->fe_linkTP_unsetPIvars_url(
                    array(
                        'showMap'  => '1',
                        'ajaxCall' => '1'
                    ),
                    array(
                        'xpos',
                        'ypos',
                        'scale'
                    )
                );
                
                // Full link
                $displayLink = $this->cObj->typoLink(
                    $displayText,
                    array(
                        'parameter' => 'javascript:'
                                    .  $this->prefixId
                                    .  '_ajaxRequest(\''
                                    .  $this->serverName . '/' . $link
                                    .  '\');',
                        'title'     => $this->pi_getLL( 'show-title' )
                    )
                );
            }
            
            // Add link
            $htmlCode[] = $this->api->fe_makeStyledContent(
                'div',
                'showHide',
                $displayLink
            );
            
        } else {
            
            // Show / hide text
            $displayText = $this->pi_getLL( 'hide' );
            
            // Check for Ajax
            if( $this->conf[ 'ajax' ] == 0 ) {
            
                // Link to show / hide map
                $link = $this->api->fe_linkTP_unsetPIvars_url(
                    array(
                        'showMap' => '0'
                    ),
                    array(
                        'xpos',
                        'ypos',
                        'scale'
                    )
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
                
                // Link to show / hide map
                $link = $this->api->fe_linkTP_unsetPIvars_url(
                    array(
                        'showMap'  => '0',
                        'ajaxCall' => '1'
                    ),
                    array(
                        'xpos',
                        'ypos',
                        'scale'
                    )
                );
                
                // Full link
                $displayLink = $this->cObj->typoLink(
                    $displayText,
                    array(
                        'parameter' => 'javascript:'
                                    .  $this->prefixId
                                    .  '_ajaxRequest(\''
                                    .  $this->serverName . '/' . $link
                                    .  '\');',
                        'title'     => $this->pi_getLL( 'hide-title' )
                    )
                );
            }
            
            // Add link
            $htmlCode[] = $this->api->fe_makeStyledContent(
                'div',
                'showHide',
                $displayLink
            );
            
            // Add map
            $htmlCode[] = $this->api->fe_makeStyledContent(
                'div',
                'map',
                $this->displayMap()
            );
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
        $mapTop[] = $this->api->fe_makeStyledContent(
            'div',
            'left',
            $this->icons[ 'left-up' ]
        );
        $mapTop[] = $this->api->fe_makeStyledContent(
            'div',
            'up',
            $this->icons[ 'up' ]
        );
        $mapTop[] = $this->api->fe_makeStyledContent(
            'div',
            'right',
            $this->icons[ 'right-up' ]
        );
        
        // Add left icon
        $mapMiddle[] = $this->api->fe_makeStyledContent(
            'div',
            'right',
            $this->icons[ 'left' ]
        );
        
        // Add picture
        $mapMiddle[] = $this->api->fe_makeStyledContent(
            'div',
            'picture',
            $this->getPicture()
        );
        
        // Add right icon
        $mapMiddle[] = $this->api->fe_makeStyledContent(
            'div',
            'right',
            $this->icons[ 'right' ]
        );
        
        // Add icons to map bottom
        $mapBottom[] = $this->api->fe_makeStyledContent(
            'div',
            'left',
            $this->icons[ 'left-down' ]
        );
        $mapBottom[] = $this->api->fe_makeStyledContent(
            'div',
            'down',
            $this->icons[ 'down' ]
        );
        $mapBottom[] = $this->api->fe_makeStyledContent(
            'div',
            'right',
            $this->icons[ 'right-down' ]
        );
        
        // Add map areas
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'div',
            'mapTop',
            implode( chr( 10 ), $mapTop )
        );
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'div',
            'mapMiddle',
            implode( chr( 10 ), $mapMiddle )
        );
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'div',
            'mapBottom',
            implode( chr( 10 ), $mapBottom )
        );
        
        // Check if display has changed
        if ( $this->xpos != $this->conf[ 'xpos' ] ||
             $this->ypos != $this->conf[ 'ypos' ] ||
             $this->scale != $this->conf[ 'scale' ] ) {
            
            // Check for Ajax
            if( $this->conf[ 'ajax' ] == 0 ) {
                
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
                        'parameter' => 'javascript:'
                                    .  $this->prefixId
                                    .  '_ajaxRequest(\''
                                    .  $this->serverName . '/' . $back
                                    .  '\');',
                        'title'     => $this->pi_getLL( 'backLink-title' )
                    )
                );
            }
            
            // Back to original
            $htmlCode[] = $this->api->fe_makeStyledContent(
                'div',
                'back',
                $backLink
            );
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
        $mapUrl = $this->conf[ 'url' ]
                . '?'
                . t3lib_div::implodeArrayForUrl( '', $mapParams, '', 1 );
        
        // Formatted scale
        $scale = '1:' . number_format( $this->scale, '.', '', '\'' );
        
        // Alt & title text
        $alt = sprintf(
            $this->pi_getLL( 'imgAltText' ),
            $scale,
            $this->xpos,
            $this->ypos
        );
        
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
            $dezoom = $this->api->fe_makeStyledContent(
                'div',
                'dezoom',
                $icon
            );
            
        } else {
            
            // New scale
            $newScale = array_search(
                $this->scale,
                $this->availableScales ) - 1;
            
            // Check for Ajax
            if( $this->conf[ 'ajax' ] == 0 ) {
                
                // Dezoom available
                $icon = $this->pi_linkTP_keepPIvars(
                    $this->icons[ 'minus' ],
                    array(
                        'scale' => $this->availableScales[ $newScale ]
                    )
                );
                
            } else {
                
                // Dezoom available
                $link = $this->pi_linkTP_keepPIvars_url(
                    array(
                        'scale'    => $this->availableScales[ $newScale ],
                        'ajaxCall' => 1
                    )
                );
                
                // Full link
                $icon = $this->cObj->typoLink(
                    $this->icons[ 'minus' ],
                    array(
                        'parameter' => 'javascript:'
                                    .  $this->prefixId
                                    .  '_ajaxRequest(\''
                                    .  $this->serverName . '/' . $link
                                    .  '\');'
                    )
                );
            }
            
            // Add dezoom link
            $dezoom = $this->api->fe_makeStyledContent(
                'div',
                'dezoom',
                $icon
            );
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
                $icon = sprintf(
                    $this->icons[ 'zoom-current' ],
                    $formatScale,
                    $formatScale
                );
                
                // Set current flag
                $cur = true;
                
            } else if ( $cur ) {
                
                // Icon
                $image = sprintf(
                    $this->icons[ 'zoom-decrease' ],
                    $formatScale,
                    $formatScale
                );
                
                // Check for Ajax
                if( $this->conf[ 'ajax' ] == 0 ) {
                    
                    // Scale link
                    $icon = $this->pi_linkTP_keepPIvars(
                        $image,
                        array(
                            'scale' => $value
                        )
                    );
                    
                } else {
                    
                    // Scale link
                    $link = $this->pi_linkTP_keepPIvars_url(
                        array(
                            'scale'    => $value,
                            'ajaxCall' => 1
                        )
                    );
                    
                    // Full link
                    $icon = $this->cObj->typoLink(
                        $image,
                        array(
                            'parameter' => 'javascript:'
                                        .  $this->prefixId
                                        .  '_ajaxRequest(\''
                                        .  $this->serverName . '/' . $link
                                        .  '\');'
                        )
                    );
                }
                
            } else {
                
                // Icon
                $image = sprintf(
                    $this->icons[ 'zoom-increase' ],
                    $formatScale,
                    $formatScale
                );
                
                // Check for Ajax
                if( $this->conf[ 'ajax' ] == 0 ) {
                
                    // Scale link
                    $icon = $this->pi_linkTP_keepPIvars(
                        $image,
                        array(
                            'scale' => $value
                        )
                    );
                    
                } else {
                    
                    // Scale link
                    $link = $this->pi_linkTP_keepPIvars_url(
                        array(
                            'scale'    => $value,
                            'ajaxCall' => 1
                        )
                    );
                    
                    // Full link
                    $icon = $this->cObj->typoLink(
                        $image,
                        array(
                            'parameter' => 'javascript:'
                                        .  $this->prefixId
                                        .  '_ajaxRequest(\''
                                        .  $this->serverName . '/' . $link
                                        .  '\');'
                        )
                    );
                }
            }
            
            // Add scale
            $scaleBar[] = $this->api->fe_makeStyledContent(
                'div',
                'scale',
                $icon
            );
        }
        
        // Check first scale
        if ( $this->scale == $this->availableScales[ count( $this->availableScales ) - 1 ] ) {
            
            // Zoom deactivated
            $icon = $this->icons[ 'plus-off' ];
            $zoom = $this->api->fe_makeStyledContent(
                'div',
                'zoom',
                $icon
            );
            
        } else {
            
            // New scale
            $newScale = array_search(
                $this->scale,
                $this->availableScales ) + 1;
            
            // Check for Ajax
            if( $this->conf[ 'ajax' ] == 0 ) {
                
                // Zoom available
                $icon = $this->pi_linkTP_keepPIvars(
                    $this->icons[ 'plus' ],
                    array(
                        'scale' => $this->availableScales[ $newScale ]
                    )
                );
                
            } else {
                
                // Zoom available
                $link = $this->pi_linkTP_keepPIvars_url(
                    array(
                        'scale'    => $this->availableScales[ $newScale ],
                        'ajaxCall' => 1
                    )
                );
                
                // Full link
                $icon = $this->cObj->typoLink(
                    $this->icons[ 'plus' ],
                    array(
                        'parameter' => 'javascript:'
                                    .  $this->prefixId
                                    .  '_ajaxRequest(\''
                                    .  $this->serverName . '/' . $link
                                    .  '\');'
                    )
                );
            }
            
            // Add zoom link
            $zoom = $this->api->fe_makeStyledContent(
                'div',
                'zoom',
                $icon
            );
        }
        
        // Add dezoom
        $scaleBar[] = $zoom;
        
        // Return scale bar
        return $this->api->fe_makeStyledContent(
            'div',
            'scaleBar',
            implode( chr( 10 ), $scaleBar )
        );
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
                
                // Check for Ajax
                if( $this->conf[ 'ajax' ] == 0 ) {
                
                    // Add generated icon with link
                    $this->icons[ $key ] = $this->pi_linkTP_keepPIvars(
                        $this->cObj->IMAGE( $val ),
                        array(
                            'xpos' => $x,
                            'ypos' => $y
                        )
                    );
                    
                } else {
                    
                    // Ajax link
                    $link = $this->pi_linkTP_keepPIvars_url(
                        array(
                            'xpos'     => $x,
                            'ypos'     => $y,
                            'ajaxCall' => 1
                        )
                    );
                    
                    // Add generated icon with link
                    $this->icons[ $key ] = $this->pi_linkTP_keepPIvars(
                        $this->cObj->IMAGE( $val ),
                        array(
                            'xpos' => $x,
                            'ypos' => $y
                        )
                    );
                    $this->icons[ $key ] = $this->cObj->typoLink(
                        $this->cObj->IMAGE( $val ),
                        array(
                            'parameter' => 'javascript:'
                                        .  $this->prefixId
                                        .  '_ajaxRequest(\''
                                        .  $this->serverName . '/' . $link
                                        .  '\');'
                        )
                    );
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
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_geomap_prototype/pi1/class.tx_vdgeomapprototype_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_geomap_prototype/pi1/class.tx_vdgeomapprototype_pi1.php']);
}
?>
