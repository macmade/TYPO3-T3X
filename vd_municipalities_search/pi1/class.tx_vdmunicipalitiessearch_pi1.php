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
 * Plugin 'VD / Municipalites Search' for the 'vd_municipalities_search' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.1
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *     117:     function main( $content,$conf )
 *     165:     function setConfig
 *     193:     function searchView
 *     300:     function searchResults
 *     432:     function noResult
 *     453:     function showMunicipalities( $res )
 *     584:     function singleMunicipality( $mid )
 * 
 *              TOTAL FUNCTIONS: 7
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_vdmunicipalitiessearch_pi1 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_vdmunicipalitiessearch_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_vdmunicipalitiessearch_pi1.php';
    
    // The extension key
    var $extKey             = 'vd_municipalities_search';
    
    // Version of the Developer API required
    var $apimacmade_version = 3.3;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Extension tables
    var $extTables          = array(
        'municipalities'  => 'tx_vdmunicipalities_municipalities',
        'institutions'    => 'tx_vdmunicipalitiessearch_institutions'
    );
    
    // Configuration array
    var $conf               = array();
    
    // Plugin variables
    var $piVars             = array();
    
    // Instance of the Developer API
    var $api                = NULL;
    
    
    
    
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
        
        // Check view type
        if( isset( $this->conf[ 'municipality' ] ) && $this->conf[ 'municipality' ] ) {
            
            // Fixed municipality
            $content = $this->singleMunicipality( $this->conf[ 'municipality' ] );
            
        } else {
            
            // Search
            $content = $this->searchView();
        }
        
        // Return content
        return $this->pi_wrapInBaseClass( $content );
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
            'institutions' => 'sDEF:institutions',
            'municipality' => 'sDEF:municipality',
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex( $flex2conf, $this->conf, $this->piFlexForm );
        
        // Checks the value of institutionLinkText
        if( $this->conf[ 'institutionLinkText' ] != 'page' && $this->conf[ 'institutionLinkText' ] != 'institution' ) {
            
            // Defaults to 'page'
            $this->conf[ 'institutionLinkText' ] = 'page';
        }
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'VD / Municipalities Search: configuration array');
    }
    
    /**
     * Builds the search view
     * 
     * @return  string  The complete search view
     * @see     searchResults
     */
    function searchView()
    {
        // Storage
        $htmlCode  = array();
        $searchBox = array();
        
        // Start form
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'form',
            'form',
            false,
            1,
            false,
            true,
            array(
                'action'  => $this->cObj->typolink_URL(
                    array(
                        'parameter'    => $GLOBALS[ 'TSFE' ]->id,
                        'useCacheHash' => 1
                    )
                ),
                'method'  => 'post',
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ]
            )
        );
        
        // On focus action
        $onFocus = 'this.value=(this.value==\'' . $this->pi_getLL( 'searchBox-sword-default' ) . '\') ? \'\' : this.value;';
        
        // On blur action
        $onBlur = 'this.value=(this.value.length > 0) ? this.value : \'' . $this->pi_getLL( 'searchBox-sword-default' ) . '\';';
        
        // Search input
        $searchBox[] = $this->api->fe_makeStyledContent(
            'span',
            'sword',
            '<input name="'
                . $this->prefixId
                . '[sword]" id="'
                . $this->prefixId
                . '-sword" type="text" value="'
                . $this->pi_getLL( 'searchBox-sword-default' )
                . '" size="25" onfocus="'
                . $onFocus
                .  '" onblur="'
                . $onBlur
                . '" title="'
                . $this->pi_getLL( 'sword-title' )
                . '" />'
        );
        
        // Submit
        $searchBox[] = $this->api->fe_makeStyledContent(
            'span',
            'submit',
            '<input name="'
                . $this->prefixId
                . '[submit]" id="'
                . $this->prefixId
                . '-submit" type="submit" value="'
                . $this->pi_getLL( 'searchBox-submit' )
                . '" title="'
                . $this->pi_getLL( 'submit-title' )
                . '" />'
        );
        
        // Add search box
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'div',
            'searchbox',
            implode( chr( 10 ), $searchBox )
        );
        
        // End form
        $htmlCode[] = '</form>';
        
        // Checks for a search word
        if( isset( $this->piVars[ 'sword' ] ) ) {
            
            // Remove spaces before and after search word
            $this->piVars[ 'sword' ] = trim( $this->piVars[ 'sword' ] );
            
            // Search results
            $htmlCode[] = ( isset( $this->piVars[ 'submit' ] ) && $this->piVars[ 'sword' ] ) ? $this->searchResults() : '';
        }
        
        // Return content
        return $this->api->fe_makeStyledContent(
            'div',
            'search',
            implode( chr( 10 ), $htmlCode )
        );
    }
    
    /**
     * Builds the search results
     *
     * This function is used to display the search results. If only one
     * If only one municipality is found, it will display diirectly the list
     * of the related pages. In any case, only municipalities associated with
     * TYPO3 pages will be displayed.
     * 
     * @return  string  The search results view
     * @see     singleMunicipality
     * @see     showMunicipalities
     * @see     noResult
     */
    function searchResults()
    {
        // Storage
        $htmlCode = array();
        
        // Access to the list of the institutions
        if( isset( $this->piVars[ 'mid' ] ) ) {
            
            // Display institutions
            $htmlCode[] = $this->singleMunicipality( $this->piVars[ 'mid' ] );
            
        } else {
            
            // Search word
            $sword = strtolower( $this->piVars[ 'sword' ] );
            
            // Check for 'fatal' exceptions
            if( isset( $this->conf[ 'fatalExceptions.' ] ) && is_array( $this->conf[ 'fatalExceptions.' ] ) ) {
                
                // Process 'fatal' exceptions
                foreach( $this->conf[ 'fatalExceptions.' ] as $fatal ) {
                    
                    // Checks search word
                    if( $sword == strtolower( $fatal[ 'string' ] ) ) {
                        
                        // Display error message and return content
                        $htmlCode[] = $this->api->fe_makeStyledContent(
                            'div',
                            'error',
                            $this->cObj->HTML( array( 'value' => $fatal[ 'message' ] ) )
                        );
                        
                        // Return content
                        return $this->api->fe_makeStyledContent(
                            'div',
                            'searchResults',
                            implode( chr( 10 ), $htmlCode )
                        );
                    }
                }
            }
            
            // Check for exceptions
            if( isset( $this->conf[ 'exceptions.' ] ) && is_array( $this->conf[ 'exceptions.' ] ) ) {
                
                // Process exceptions
                foreach( $this->conf[ 'exceptions.' ] as $exception ) {
                    
                    // Checks search word
                    if( $sword == strtolower( $exception[ 'string' ] ) ) {
                        
                        // Set municipality ID
                        $mid = $exception[ 'municipalityId' ];
                    }
                }
            }
            
            // No direct municipality ID
            if( !$mid ) {
                
                // Replace dashes by spaces
                $sword      = str_replace( '-', ' ', $sword );
                
                // Get each word in an array
                $swordParts = explode( ' ', $sword );
                
                // Build search query
                $where      = $GLOBALS[ 'TYPO3_DB' ]->searchQuery(
                    $swordParts,
                    array( 'name_lower', 'name_lower15' ),
                    $this->extTables[ 'municipalities' ]
                );
                
                // Select municipalities
                $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                    '*',
                    $this->extTables[ 'municipalities' ],
                    $where,
                    '',
                    'name_lower'
                );
                
                // Checks query
                if( $res ) {
                    
                    // Number of results
                    $numRows = $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res );
                    
                    if( $numRows > 1 ) {
                        
                        $htmlCode[] = $this->showMunicipalities( $res );
                        
                    } elseif( $numRows == 1 ) {
                        
                        // Municipality row
                        $municipality = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res );
                        
                        // Display institutions
                        $htmlCode[] = $this->singleMunicipality( $municipality[ 'id_municipality' ] );
                        
                    } else {
                        
                        // No results
                        $htmlCode[] = $this->noResult();
                    }
                    
                } else {
                    
                    // No results
                    $htmlCode[] = $this->noResult();
                }
                
            } else {
                
                // Display institutions
                $htmlCode[] = $this->singleMunicipality( $mid );
            }
        }
        
        // Return content
        return $this->api->fe_makeStyledContent(
            'div',
            'searchResults',
            implode( chr( 10 ), $htmlCode )
        );
    }
    
    /**
     * Displays a no result message
     * 
     * @return  string  The no result message 
     */
    function noResult()
    {
        // Return error
        return $this->api->fe_makeStyledContent(
            'div',
            'noResult',
            $this->pi_getLL( 'noResult' )
        );
    }
    
    /**
     * Shows the list of the found municipalities
     * 
     * Only municipalities associated with TYPO3 pages will be shown. If only
     * one is found. it will swap to the single view.
     * 
     * @param   ressource   $res    The MySQL query ressource
     * @return  string      The list of the municipalities
     * @see     singleMunicipality
     * @see     noResult
     */
    function showMunicipalities( $res )
    {
        // Storage
        $htmlCode = array();
        
        // Title
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'h2',
            'municipality',
            $this->pi_getLL( 'results-header' )
        );
        
        // Start list
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'ul',
            'municipalitiesList',
            false,
            1,
            false,
            true
        );
        
        // Storage for municipalities
        $municipalities = array();
        
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
            
            // WHERE clause for selecting pages based on the municipality
            $whereClause = $GLOBALS[ 'TYPO3_DB' ]->listQuery(
                'tx_vdmunicipalitiessearch_municipalities',
                $row[ 'id_municipality' ],
                'pages'
            );
            
            // Check if institutions are selected
            if( isset( $this->conf[ 'institutions' ] ) && $this->conf[ 'institutions' ] ) {
                
                // Storage
                $addWhere         = array();
                
                // Selected institutions
                $institutionsList = explode( ',', $this->conf[ 'institutions' ] );
                
                // Process each institution
                foreach( $institutionsList as $instId ) {
                    
                    // Add additionnal where clause
                    $addWhere[] = $GLOBALS[ 'TYPO3_DB' ]->listQuery(
                        'tx_vdmunicipalitiessearch_institution',
                        $instId,
                        'pages'
                    );
                }
                
                // Add full additionnal where clause
                $whereClause .= ' AND (' . implode( ' OR ', $addWhere ) . ')';
            }
            
            // Select pages
            $pagesRes = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                '*',
                'pages',
                $whereClause
            );
            
            // Checks the result from the pages table
            if( $pagesRes && $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $pagesRes ) ) {
                
                // Stores the municipality ID
                $mid = $row[ 'id_municipality' ];
                
                // TypoLink configuration
                $typoLink = $this->conf[ 'pagesTypoLink.' ];
                
                // Add parameters
                $typoLink[ 'title' ]            = ( isset( $typoLink[ 'title' ] ) ) ? $typoLink[ 'title' ] : $row[ 'name_lower' ];
                $typoLink[ 'parameter' ]        = $GLOBALS[ 'TSFE' ]->id;
                $typoLink[ 'additionalParams' ] = $this->api->fe_typoLinkParams(
                    array(
                        'mid' => $row[ 'id_municipality' ]
                    ),
                    true
                );
                
                // Get page link
                $link = $this->cObj->typoLink( $row[ 'name_lower' ], $typoLink );
                
                // Add municipality name
                $municipalities[] = $this->api->fe_makeStyledContent(
                    'li',
                    'municipality',
                    $link
                );
            }
        }
        
        // Checks for municipalities
        if( count( $municipalities ) > 1 ) {
            
            // Shows the municipalities
            $htmlCode[] = implode( chr( 10 ), $municipalities );
            
        } elseif( count( $municipalities ) == 1 ) {
                
                // Only one municipality is available. Displays the single view
                return $this->singleMunicipality( $mid );
                
        } else {
            
            // No results
            $htmlCode[] = $this->noResult();
        }
        
        // End list
        $htmlCode[] = '</ul>';
        
        // Return content
        return $this->api->fe_makeStyledContent(
            'div',
            'searchResults',
            implode( chr( 10 ), $htmlCode )
        );
    }
    
    /**
     * Display the pages associated with a municipality
     * 
     * @param   int     $mid    The municipality ID (not the uid field)
     * @return  The list of the associated pages
     * @see     noResult
     */
    function singleMunicipality( $mid )
    {
        // Storage
        $htmlCode = array();
        
        // Select the municipality
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            '*',
            $this->extTables[ 'municipalities' ],
            'id_municipality=' . $mid
        );
        
        // Try to select the municipality
        if( $res && $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) >= 1 ) {
            
            // Get the municipality
            $municipality = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res );
            
            // WHERE clause for selecting pages based on the municipality
            $whereClause = $GLOBALS[ 'TYPO3_DB' ]->listQuery(
                'tx_vdmunicipalitiessearch_municipalities',
                $mid,
                'pages'
            );
            
            // Check if institutions are selected
            if( isset( $this->conf[ 'institutions' ] ) && $this->conf[ 'institutions' ] ) {
                
                // Storage
                $addWhere         = array();
                
                // Selected institutions
                $institutionsList = explode( ',', $this->conf[ 'institutions' ] );
                
                // Process each institution
                foreach( $institutionsList as $instId ) {
                    
                    // Add additionnal where clause
                    $addWhere[] = $GLOBALS[ 'TYPO3_DB' ]->listQuery(
                        'tx_vdmunicipalitiessearch_institution',
                        $instId,
                        'pages'
                    );
                }
                
                // Add full additionnal where clause
                $whereClause .= ' AND (' . implode( ' OR ', $addWhere ) . ')';
            }
            
            // Select pages
            $pagesRes = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                '*',
                'pages',
                $whereClause
            );
            
            // Check query result
            if( $pagesRes ) {
                
                // Number of result
                $resNum = $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $pagesRes );
                
                // Checks for more than one result
                if( $resNum > 1 ) {
                                    
                    // Add header with municipality name
                    $htmlCode[] = $this->api->fe_makeStyledContent(
                        'h2',
                        'municipality',
                        $municipality[ 'name_lower' ]
                    );
                    
                    // Storage
                    $pagesItems = array();
                    
                    // Process each page
                    while( $page = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $pagesRes ) ) {
                        
                        // Check if link text must be the institution name
                        if( $this->conf[ 'institutionLinkText' ] == 'institution' ) {
                            
                            // Gets the institution
                            $institution = $this->pi_getRecord(
                                $this->extTables[ 'institutions' ],
                                $page[ 'tx_vdmunicipalitiessearch_institution' ]
                            );
                            
                            // Display text
                            $text = ( is_array( $institution ) ) ? $institution[ 'name' ] : $page[ 'title' ];
                            
                        } else {
                            
                            // Display page name
                            $text = $page[ 'title' ];
                        }
                        
                        // TypoLink configuration
                        $typoLink = $this->conf[ 'pagesTypoLink.' ];
                        
                        // Add parameters
                        $typoLink[ 'parameter' ] = $page[ 'uid' ];
                        $typoLink[ 'title' ]     = ( isset( $typoLink[ 'title' ] ) ) ? $typoLink[ 'title' ] : $page[ 'title' ];
                        
                        // Get page link
                        $link = $this->cObj->typoLink( $text, $typoLink );
                        
                        // Add page link
                        $pagesItems[ $page[ 'title' ] ] = $this->api->fe_makeStyledContent(
                            'li',
                            'page',
                            $link
                        );
                    }
                    
                    // Sort pages
                    ksort( $pagesItems );
                    
                    // Add page list
                    $htmlCode[] = $this->api->fe_makeStyledContent(
                        'ul',
                        'pageList',
                        implode( chr( 10 ), $pagesItems )
                    );
                    
                } elseif( $resNum == 1 ) {
                    
                    // Page row
                    $page = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $pagesRes );
                    
                    // URL to the matching page
                    $url  = $this->pi_getPageLink( $page[ 'uid' ] );
                    
                    // Redirection
                    header( 'Location: ' . t3lib_div::locationHeaderUrl( $url ) );
                    
                } else {
                    
                    // No results
                    $htmlCode[] = $this->noResult();
                }
                
            } else {
                
                // No results
                $htmlCode[] = $this->noResult();
            }
            
        } else {
            
            // No results
            $htmlCode[] = $this->noResult();
        }
        
        // Return content
        return $this->api->fe_makeStyledContent(
            'div',
            'municipality-fixed',
            implode( chr( 10 ), $htmlCode )
        );
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_municipalities_search/pi1/class.tx_vdmunicipalitiessearch_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_municipalities_search/pi1/class.tx_vdmunicipalitiessearch_pi1.php']);
}
?>
