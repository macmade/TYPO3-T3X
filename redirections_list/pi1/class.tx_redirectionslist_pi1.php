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
 * Plugin 'RealURL redirections list' for the 'redirections_list' extension.
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
 * 
 *              TOTAL FUNCTIONS: 
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_redirectionslist_pi1 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_redirectionslist_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_redirectionslist_pi1.php';
    
    // The extension key
    var $extKey             = 'redirections_list';
    
    // Instance of the developer API
    var $api                = NULL;
    
    // Version of the Developer API required
    var $apimacmade_version = 3.0;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Plugin configuration array
    var $conf               = array();
    
    // Flexform data
    var $pi_flexform        = '';
    
    // Table to use
    var $extTables          = array(
        'redirects' => 'tx_realurl_redirects',
        'cache'     => 'tx_realurl_urlencodecache',
        'pages'     => 'pages',
        'pagesLL'   => 'pages_language_overlay'
    );
    
    // Current lang
    var $lang               = 0;
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin, and launches the needed functions
     * to correctly display it.
     * 
     * @param       $content            The content object
     * @param       $conf               The TS setup
     * @return      The content of the plugin.
     * @see         setConfig
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
        
        // Current lang ID
        $this->lang = $GLOBALS[ 'TSFE' ]->sys_language_uid;
        
        // Return content
        return $this->pi_wrapInBaseClass( $this->showRedirections() );
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
            'selection'    => 'sDEF:selection',
            'redirections' => 'sDEF:redirections',
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // Converts selected redirections to an array
        $this->conf[ 'redirections' ] = explode( ',', $this->conf[ 'redirections' ] );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'RealURL redirections list: configuration array');
    }
    
    function showRedirections()
    {
        // Storage
        $htmlCode = array();
        
        // Checks the selection mode
        if( $this->conf[ 'selection' ] == 0 ) {
            
            // Selects all RealURL redirections
            $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                '*',
                $this->extTables[ 'redirects' ],
                'url_hash'
            );
            
            // Checks the DB resource
            if( $res ) {
                
                // Process each redirection
                while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                    
                    // Empty page array
                    $page = array();
                    
                    // Only process redirections that are not selected
                    if( !in_array( $row[ 'url_hash' ], $this->conf[ 'redirections' ] ) ) {
                        
                        // Add trailing slash to destination if necessary
                        $destination = ( substr( $row[ 'destination' ], -1 ) == '/' ) ? $row[ 'destination' ] : $row[ 'destination' ] . '/';
                        
                        // Add trailing slash to source if necessary
                        $url = ( substr( $row[ 'url' ], -1 ) == '/' ) ? $row[ 'url' ] : $row[ 'url' ] . '/';
                        
                        // Tries to select page from the URL cache
                        $cacheRes = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                            '*',
                            $this->extTables[ 'cache' ],
                            'content="' . $GLOBALS[ 'TYPO3_DB' ]->quoteStr( $destination, $this->extTables[ 'cache' ] ) . '"'
                        );
                        
                        if( $cacheRes && $cacheRow = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $cacheRes ) ) {
                            
                            // Page ID
                            $pid     = $cacheRow[ 'page_id' ];
                            
                            // Gets the page row
                            $page    = $this->pi_getRecord(
                                $this->extTables[ 'pages' ],
                                $pid
                            );
                            
                            // Checks for an alternative language
                            if( $this->lang != 0 ) {
                                
                                // Select a page localization
                                $pageRes = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                                    '*',
                                    $this->extTables[ 'pagesLL' ],
                                    'sys_language_uid=' . $this->lang . ' AND pid=' . $pid
                                );
                                
                                // Gets the localized row
                                if( $pageRes && $pageLL = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $pageRes ) ) {
                                    
                                    // Sets the page title
                                    $page[ 'title' ] = $pageLL[ 'title' ];
                                }
                            }                          
                        }
                        
                        // Page title
                        $title = ( isset( $page[ 'title' ] ) ) ? $page[ 'title' ] : '';
                        
                        // URL div
                        $urlDiv = $this->api->fe_makeStyledContent(
                            'div',
                            'url',
                            $this->cObj->typoLink(
                                t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . $url,
                                array(
                                    'parameter' => t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . $url
                                )
                            )
                            
                        );
                        
                        // Destination prefix
                        $destinationPrefix = ( substr( $destination, 0, 4 ) == 'http' || substr( $destination, 0, 4 ) == 'www.' ) ? '' : t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' );
                        
                        // Destination div
                        $destinationDiv = $this->api->fe_makeStyledContent(
                            'div',
                            'destination',
                            $this->cObj->typoLink(
                                $destinationPrefix . $destination,
                                array(
                                    'parameter' => $destinationPrefix . $destination
                                )
                            )
                        );
                        
                        // Title div
                        $titleDiv = $this->api->fe_makeStyledContent(
                            'div',
                            'title',
                            $title
                        );
                        
                        // Redirection div
                        $htmlCode[] = $this->api->fe_makeStyledContent(
                            'div',
                            'redirection',
                            $urlDiv . $destinationDiv . $titleDiv
                        );
                    }
                }
            }
            
        } else {
            
        }
        
        // Returns code
        return implode( chr( 10 ), $htmlCode );
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/redirections_list/pi1/class.tx_redirectionslist_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/redirections_list/pi1/class.tx_redirectionslist_pi1.php']);
}
?>
