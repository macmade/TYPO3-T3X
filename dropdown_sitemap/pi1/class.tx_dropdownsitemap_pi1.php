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
 * Plugin 'Drop-Down sitemap' for the 'dropdown_sitemap' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     2.0.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *      97:     function main($content,$conf)
 *     175:     function setConfig
 *     215:     function buildMenuConfArray
 *     354:     function buildImageTSConfig($expanded=false)
 *     403:     function buildJSCode
 * 
 *              TOTAL FUNCTIONS: 5
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_dropdownsitemap_pi1 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_dropdownsitemap_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_dropdownsitemap_pi1.php';
    
    // The extension key
    var $extKey             = 'dropdown_sitemap';
    
    // Version of the Developer API required
    var $apimacmade_version = 4.2;
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin "tx_dropdownsitemap_pi1", and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param       $content            The content object
     * @param       $conf               The TS setup
     * @return      The content of the plugin.
     * @see         buildJSCode
     */
    function main( $content, $conf )
    {
        // New instance of the macmade.net API
        $this->api  = new tx_apimacmade( $this );
        
        // Placing TS conf array in a class variable
        $this->conf =& $conf;
        
        // Load LOCAL_LANG values
        $this->pi_loadLL();
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Checks the configuration array
        if( !is_array( $this->conf ) || count( $this->conf ) <= 2 ) {
            
            // Static template not included
            return $this->api->fe_makeStyledContent(
                'strong',
                'error',
                $this->pi_getLL( 'error' )
            );
        }
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Checks for Scriptaculous
        if( isset( $this->conf[ 'scriptaculous.' ][ 'enable' ] )
            && $this->conf[ 'scriptaculous.' ][ 'enable' ]
        ) {
            
            // Includes Scriptaculous
            $this->api->fe_includeScriptaculousJs();
        }
        
        // Create the menu configuration array
        $mconf = $this->buildMenuConfArray();
        
        // Add JavaScrip Code
        $this->buildJSCode();
        
        // New instance of the tslib_tmenu class
        $menu              = t3lib_div::makeInstance( 'tslib_tmenu' );
        
        // Set some internal vars
        $menu->parent_cObj = $this;
        
        // Use starting point field
        // Thanks a lot to Steven Bagshaw for that piece of code
        $startingPoint     = $this->conf[ 'startingPoint' ];
        
        // Class constructor
        $menu->start(
            $GLOBALS[ 'TSFE' ]->tmpl,
            $GLOBALS[ 'TSFE' ]->sys_page,
            $startingPoint,
            $mconf,
            1
        );
        
        // Make the menu
        $menu->makeMenu();
        
        // Storage
        $content = array();
        
        // Display the expAll link
        if( $this->conf[ 'expAllLink' ] == 1 ) {
            
            // Picture TS configuration array
            $imgTSConfig                        = array();
            
            // File reference
            $imgTSConfig[ 'file' ]              = $this->conf[ 'picture.' ][ 'expOn' ];
            
            // File ressource array
            $imgTSConfig[ 'file.' ]             = array();
            
            // Width
            $imgTSConfig[ 'file.' ][ 'width' ]  = $this->conf[ 'picture.' ][ 'width' ];
            
            // Height
            $imgTSConfig[ 'file.' ][ 'height' ] = $this->conf[ 'picture.' ][ 'height' ];
            
            // HTML tag parameters
            $imgTSConfig[ 'params' ]            = $this->conf[ 'picture.' ][ 'params' ]
                                                . ' id="'
                                                . $this->prefixId
                                                . '_expImg"';
            
            $content[] = '<div class="expAll"><a href="javascript:'
                       . $this->prefixId
                       . '_expAll();">'
                       . $this->cObj->IMAGE( $imgTSConfig )
                       . $this->pi_getLL( 'expall' )
                       . '</a></div>';
        }
        
        // Write the full menu with sub-items
        $content[] = $menu->writeMenu();
        
        // Return the full menu
        return $this->pi_wrapInBaseClass( implode( chr( 10 ), $content ) );
    }
    
    /**
     * Set configuration array.
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
            'startingPoint'    => 'sDEF:pages',
            'excludeList'      => 'sPAGES:exclude_pages',
            'excludeDoktypes'  => 'sPAGES:exclude_doktypes',
            'includeNotInMenu' => 'sPAGES:include_not_in_menu',
            'showSpacers'      => 'sPAGES:show_spacers',
            'expAllLink'       => 'sOPTIONS:expall',
            'showLevels'       => 'sOPTIONS:show_levels',
            'expandLevels'     => 'sOPTIONS:expand_levels',
            'descriptionField' => 'sOPTIONS:description_field',
            'linkTarget'       => 'sADVANCED:link_target',
            'list.'            => array(
                'tag'  => 'sADVANCED:list_tag',
                'type' => 'sADVANCED:list_type'
            ),
            'scriptaculous.'   => array(
                'enable' => 'sEFFECTS:scriptaculous',
                'appear' => 'sEFFECTS:appear',
                'fade'   => 'sEFFECTS:fade'
            )
        );
        
        // Override TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug( $this->conf, 'Drop-Down Site Map: configuration array' );
    }
    
    /**
     * Create MENU object configuration
     * 
     * This function creates the configuration array of the sitemap,
     * which will be used by the start() method of the tslib_tmenu class.
     * 
     * @return      The configuration array of the menu
     */
    function buildMenuConfArray()
    {
        // PID list storage
        $pidList = array();
        
        // Menu configuration array
        $mconf   = array();
        
        // Exclude pages
        $mconf[ 'excludeUidList' ]   =& $this->conf[ 'excludeList' ];
        
        // Exclude page types
        $mconf[ 'excludeDoktypes' ]  =& $this->conf[ 'excludeDoktypes' ];
        
        // Include not in menu
        $mconf[ 'includeNotInMenu' ] =& $this->conf[ 'includeNotInMenu' ];
        
        // Include not in menu
        $mconf[ 'insertData' ]       =  '1';
        
        // Creating menu items configuration
        for( $i = 1; $i < ( $this->conf[ 'showLevels' ] + 1 ); $i++ ) {
            
            // Create image TS Config
            $imgTSConfig                                      = ( $i <= $this->conf[ 'expandLevels' ] ) ? $this->buildImageTSConfig( 1 ) : $this->buildImageTSConfig();
            
            // Checks for Scriptaculous
            if( isset( $this->conf[ 'scriptaculous.' ][ 'enable' ] )
                && $this->conf[ 'scriptaculous.' ][ 'enable' ]
            ) {
                
                // No CSS class
                $className = '';
                
                // CSS styles name for expand/collapse
                $styles    = ( $i - 1 <= $this->conf[ 'expandLevels' ] ) ? ' style="display: block;"' : ' style="display: none;"';
                
            } else {
                
                // CSS class name for expand/collapse
                $className = ( $i <= $this->conf[ 'expandLevels' ] )     ? ' class="open"'            : ' class="closed"';
                
                // No CSS styles
                $styles    = '';
            }
            // TMENU object
            $mconf[ $i ]                                      = 'TMENU';
            
            // Wrap in an HTML list element
            $mconf[ $i . '.' ][ 'wrap' ]                      = '<'
                                                              . $this->conf[ 'list.' ][ 'tag' ]
                                                              . ' type="'
                                                              . $this->conf[ 'list.' ][ 'type' ]
                                                              . '"'
                                                              . $styles
                                                              . '>|</'
                                                              . $this->conf[ 'list.' ][ 'tag' ]
                                                              . '>';
            
            // Expand all property
            $mconf[ $i . '.' ][ 'expAll' ]                    = '1';
            
            // Target for the links
            $mconf[ $i . '.' ][ 'target' ]                    = $this->conf[ 'linkTarget' ];
            
            // NO state configuration
            $mconf[ $i . '.' ][ 'NO.' ]                       = array();
            
            // Enable UID field substitution
            $mconf[ $i . '.' ][ 'NO.' ][ 'subst_elementUid' ] = '1';
            
            // End wrap
            $mconf[ $i . '.' ][ 'NO.' ][ 'wrapItemAndSub' ]   = '|</div></li>';
            
            // Start wrap
            $mconf[ $i . '.' ][ 'NO.' ][ 'allWrap' ]          = '<li class="closed"><div class="level_'
                                                              . $i
                                                              . '">'
                                                              . $this->cObj->IMAGE( $imgTSConfig[ 'NO' ] )
                                                              . '<span class="no">|</span>';
            
            // Check if A tag title must be added
            if( $this->conf[ 'titleFields' ] ) {
                
                // Add fields for A tag
                $mconf[ $i . '.' ][ 'NO.' ][ 'ATagTitle.' ][ 'field' ] = $this->conf[ 'titleFields' ];
            }
            
            // Check if a description must be added
            if( $this->conf[ 'descriptionField' ] && $this->conf[ 'descriptionField' ] != 'none' ) {
                
                // Add description
                $mconf[ $i . '.' ][ 'NO.' ][ 'after.' ][ 'dataWrap' ] = '|<span class="description">&nbsp;{field:'
                                                                      . $this->conf[ 'descriptionField' ]
                                                                      . '}</span>';
            }
            
            // Only check for subpages if sublevels must be shown
            if( $i < $this->conf[ 'showLevels' ] ) {
                
                // IFSUB state configuration
                $mconf[ $i . '.' ][ 'IFSUB.' ]                       = array();
                
                // Enable UID field substitution
                $mconf[ $i . '.' ][ 'IFSUB.' ][ 'subst_elementUid' ] = '1';
                
                // End wrap
                $mconf[ $i . '.' ][ 'IFSUB.' ][ 'wrapItemAndSub' ]   = '|</div></li>';
                
                // Start wrap
                $mconf[ $i . '.' ][ 'IFSUB.' ][ 'allWrap' ]          = '<li id="'
                                                                     . $this->prefixId
                                                                     . '_{elementUid}"'
                                                                     . $className
                                                                     . '><div class="level_'
                                                                     . $i
                                                                     . '"><a href="javascript:'
                                                                     . $this->prefixId
                                                                     . '_swapClasses({elementUid});">'
                                                                     . $this->cObj->IMAGE( $imgTSConfig[ 'IFSUB' ] )
                                                                     . '</a><span class="ifsub">|</span>';
                
                // IFSUB state activation
                $mconf[ $i . '.' ][ 'IFSUB' ]                        = '1';
                
                // Check if A tag title must be added
                if( $this->conf[ 'titleFields' ] ) {
                    
                    // Add fields for A tag
                    $mconf[ $i . '.' ][ 'IFSUB.' ][ 'ATagTitle.' ][ 'field' ] = $this->conf[ 'titleFields' ];
                }
                
                // Check if a description must be added
                if( $this->conf[ 'descriptionField' ] && $this->conf[ 'descriptionField' ] != 'none' ) {
                    
                    // Add description
                    $mconf[ $i . '.' ][ 'IFSUB.' ][ 'after.' ][ 'dataWrap' ] = '|<span class="description">&nbsp;{field:'
                                                                             . $this->conf[ 'descriptionField' ]
                                                                             . '}</span>';
                }
            }
            
            // Configuration for spacers
            if( $this->conf[ 'showSpacers' ] ) {
                
                // Activate spacers
                $mconf[ $i . '.' ][ 'SPC' ]                      = '1';
                
                // End wrap
                $mconf[ $i . '.' ][ 'SPC.' ][ 'wrapItemAndSub' ] = '|</div></li>';
                
                // Start wrap
                $mconf[ $i . '.' ][ 'SPC.' ][ 'allWrap' ]        = '<li class="closed"><div class="level_'
                                                                 . $i
                                                                 . '">'
                                                                 . $this->cObj->IMAGE( $imgTSConfig[ 'SPC' ] )
                                                                 . '<span class="spc">|</span>';
            }
        }
        
        // Return configuration array
        return $mconf;
    }
    
    /**
     * Create IMAGE object configuration
     * 
     * This function creates the configuration array for the expand and
     * collapse pictures. Used by the IMAGE method of the cObj class.
     * 
     * @param       $expanded           Boolean - True if the menu should be expanded
     * @return      The configuration arrays for the pictures.
     */
    function buildImageTSConfig( $expanded = false )
    {
        // Image TS Config array for NO state
        $imgTSConfigNo                        = array();
        
        // File reference
        $imgTSConfigNo[ 'file' ]              = $this->conf[ 'picture.' ][ 'page' ];
        
        // File ressource array
        $imgTSConfigNo[ 'file.' ]             = array();
        
        // Width
        $imgTSConfigNo[ 'file.' ][ 'width' ]  = $this->conf[ 'picture.' ][ 'width' ];
        
        // Height
        $imgTSConfigNo[ 'file.' ][ 'height' ] = $this->conf[ 'picture.' ][ 'height' ];
        
        // HTML tag parameters
        $imgTSConfigNo[ 'params' ]            = $this->conf[ 'picture.' ][ 'params' ];
        
        // Image TS Config array for SPC state
        $imgTSConfigSpc                       = $imgTSConfigNo;
        
        // File reference
        $imgTSConfigSpc[ 'file' ]             = $this->conf[ 'picture.' ][ 'spacer' ];
        
        // Image TS Config array for IFSUB state
        $imgTSConfigSub                       = $imgTSConfigNo;
        
        // File reference
        $imgTSConfigSub[ 'file' ]             = ( $expanded ) ? $this->conf[ 'picture.' ][ 'collapse' ] : $this->conf[ 'picture.' ][ 'expand' ];
        
        // HTML tag parameters
        $imgTSConfigSub[ 'params' ]           = $this->conf[ 'picture.' ][ 'params' ]
                                              . ' id="pic_{elementUid}"';
        
        // Final array
        $imgTSConfig = array(
            'NO'    => $imgTSConfigNo,
            'IFSUB' => $imgTSConfigSub,
            'SPC'   => $imgTSConfigSpc
        );
        
        // Return array
        return $imgTSConfig;
    }
    
    /**
     * Adds JavaScript Code.
     * 
     * This function adds the javascript code used to switch between
     * CSS classes and to expand/collapse all sections.
     * 
     * @return      Void.
     */
    function buildJSCode() 
    {
        // Storage
        $jsCode      = array();
        
        // Plus image URL
        $plusImgURL  = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName(
                $this->conf[ 'picture.' ][ 'expand' ]
            )
        );
        
        // Minus image URL
        $minusImgURL = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName(
                $this->conf[ 'picture.' ][ 'collapse' ]
            )
        );
        
        // Expand all image URL
        $expOn = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName(
                $this->conf[ 'picture.' ][ 'expOn' ]
            )
        );
        
        // Collapse all image URL
        $expOff = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName(
                $this->conf[ 'picture.' ][ 'expOff' ]
            )
        );
        
        // Checks for Scriptaculous
        if( isset( $this->conf[ 'scriptaculous.' ][ 'enable' ] )
            && $this->conf[ 'scriptaculous.' ][ 'enable' ]
        ) {
            
            // Effects
            $appear = $this->conf[ 'scriptaculous.' ][ 'appear' ];
            $fade   = $this->conf[ 'scriptaculous.' ][ 'fade' ];
            
            // Function for swapping element class
            $jsCode[] = 'function ' . $this->prefixId . '_swapClasses( element )';
            $jsCode[] = '{';
            $jsCode[] = '    var listItem    = document.getElementById( "' . $this->prefixId . '_" + element );';
            $jsCode[] = '    var descendants = listItem.firstDescendant().immediateDescendants();';
            $jsCode[] = '    var list        = descendants[ descendants.length - 1 ];';
            $jsCode[] = '    var picture     = "pic_" + element;';
            $jsCode[] = '    if( list.getStyle( "display" ) == "none" ) {';
            $jsCode[] = '        Effect.' . $appear . '( list );';
            $jsCode[] = '        document.getElementById( picture ).src = "' . $minusImgURL . '";';
            $jsCode[] = '    } else {';
            $jsCode[] = '        Effect.' . $fade . '( list );';
            $jsCode[] = '        document.getElementById( picture ).src = "' . $plusImgURL . '";';
            $jsCode[] = '    }';
            $jsCode[] = '}';
            
            // Function for expanding/collapsing all elements
            $jsCode[] = 'var ' . $this->prefixId . '_expanded = 0;';
            $jsCode[] = 'function ' . $this->prefixId . '_expAll()';
            $jsCode[] = '{';
            $jsCode[] = '    if( document.getElementsByTagName ) {';
            $jsCode[] = '        var style     = ( ' . $this->prefixId . '_expanded ) ? "none" : "block";';
            $jsCode[] = '        var listItems = document.getElementsByTagName( "li" );';
            $jsCode[] = '        for( i = 0; i < listItems.length; i++ ) {';
            $jsCode[] = '            if( listItems[ i ].id.indexOf( "' . $this->prefixId . '" ) != -1 ) {';
            $jsCode[] = '                var descendants = listItems[ i ].firstDescendant().immediateDescendants();';
            $jsCode[] = '                var list        = descendants[ descendants.length - 1 ];';
            $jsCode[] = '                var picture     = "pic_" + listItems[ i ].id.replace( "' . $this->prefixId . '_", "" );';
            $jsCode[] = '                if( ' . $this->prefixId . '_expanded && list.getStyle( "display" ) == "block" ) {';
            $jsCode[] = '                    Effect.' . $fade . '( list );';
            $jsCode[] = '                    document.getElementById( picture ).src = "' . $plusImgURL . '";';
            $jsCode[] = '                } else if( list.getStyle( "display" ) == "none" ) {';
            $jsCode[] = '                    Effect.' . $appear . '( list );';
            $jsCode[] = '                    document.getElementById( picture ).src = "' . $minusImgURL . '";';
            $jsCode[] = '                }';
            $jsCode[] = '            }';
            $jsCode[] = '        }';
            $jsCode[] = '        document.getElementById( "' . $this->prefixId . '_expImg" ).src = ( ' . $this->prefixId . '_expanded == 1 ) ? "' . $expOn . '" : "' . $expOff . '"';
            $jsCode[] = '        ' . $this->prefixId . '_expanded                                = ( ' . $this->prefixId . '_expanded == 1 ) ? 0 : 1;';
            $jsCode[] = '    }';
            $jsCode[] = '}';
            
        } else {
            
            // Function for swapping element class
            $jsCode[] = 'function ' . $this->prefixId . '_swapClasses( element )';
            $jsCode[] = '{';
            $jsCode[] = '    if( document.getElementById ) {';
            $jsCode[] = '        var liClass                                  = "' . $this->prefixId . '_" + element;';
            $jsCode[] = '        var picture                                  = "pic_" + element;';
            $jsCode[] = '        document.getElementById( liClass ).className = ( document.getElementById( liClass ).className == "open" ) ? "closed" : "open";';
            $jsCode[] = '        document.getElementById( picture ).src       = ( document.getElementById( liClass ).className == "open" ) ? "' . $minusImgURL . '" : "' . $plusImgURL . '";';
            $jsCode[] = '    }';
            $jsCode[] = '}';
            
            // Function for expanding/collapsing all elements
            $jsCode[] = 'var ' . $this->prefixId . '_expanded = 0;';
            $jsCode[] = 'function ' . $this->prefixId . '_expAll()';
            $jsCode[] = '{';
            $jsCode[] = '    if( document.getElementsByTagName ) {';
            $jsCode[] = '        var listItems = document.getElementsByTagName( "li" );';
            $jsCode[] = '        for( i = 0; i < listItems.length; i++ ) {';
            $jsCode[] = '            if( listItems[ i ].id.indexOf( "' . $this->prefixId . '" ) != -1 ) {';
            $jsCode[] = '                listItems[ i ].className               = ( ' . $this->prefixId . '_expanded ) ? "closed" : "open";';
            $jsCode[] = '                var picture                            = "pic_" + listItems[ i ].id.replace( "' . $this->prefixId . '_", "" );';
            $jsCode[] = '                listItems[ i ].className               = ( ' . $this->prefixId . '_expanded ) ? "closed" : "open"';
            $jsCode[] = '                document.getElementById( picture ).src = ( ' . $this->prefixId . '_expanded ) ? "' . $plusImgURL . '" : "' . $minusImgURL . '";';
            $jsCode[] = '            }';
            $jsCode[] = '        }';
            $jsCode[] = '        document.getElementById( "' . $this->prefixId . '_expImg" ).src = ( ' . $this->prefixId . '_expanded == 1 ) ? "' . $expOn . '" : "' . $expOff . '"';
            $jsCode[] = '        ' . $this->prefixId . '_expanded                                = ( ' . $this->prefixId . '_expanded == 1 ) ? 0 : 1;';
            $jsCode[] = '    }';
            $jsCode[] = '}';
        }
        
        // Adds JS code
        $GLOBALS[ 'TSFE' ]->setJS(
            $this->prefixId,
            implode( chr( 10 ), $jsCode )
        );
    }
}

/**
 * XCLASS inclusion
 */
if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dropdown_sitemap/pi1/class.tx_dropdownsitemap_pi1.php"]) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dropdown_sitemap/pi1/class.tx_dropdownsitemap_pi1.php"]);
}
?>
