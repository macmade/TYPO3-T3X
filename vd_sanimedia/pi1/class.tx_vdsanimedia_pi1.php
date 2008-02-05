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
 * Plugin 'VD / Sanimedia' for the 'vd_sanimedia' extension.
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

class tx_vdsanimedia_pi1 extends tslib_pibase
{
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_vdsanimedia_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_vdsanimedia_pi1.php';
    
    // The extension key
    var $extKey             = 'vd_sanimedia';
    
    // Version of the Developer API required
    var $apimacmade_version = 4.2;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Configuration array
    var $conf               = array();
    
    // Plugin variables
    var $piVars             = array();
    
    // Instance of the Developer API
    var $api                = NULL;
    
    // New line character
    var $NL                 = '';
    
    // Current URL
    var $url                = '';
    
    // Current date
    var $curDate            = '';
    
    // Tables used by the extension
    var $extTables          = array(
        'public'   => 'tx_vdsanimedia_public',
        'themes'   => 'tx_vdsanimedia_themes',
        'keywords' => 'tx_vdsanimedia_keywords',
        'pages'    => 'pages'
    );
    
    // Keywords
    var $public             = array();
    
    // Keywords
    var $themes             = array();
    
    // Keywords
    var $keywords           = array();
    
    // Internal data for tslib_pibase
    var $internal           = array();
    
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
     */
    function main( &$content, &$conf )
    {
        // New instance of the macmade.net API
        $this->api =& tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                &$this
            )
        );
        
        // Gets the current URL
        $this->url     = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        // Gets the current date
        $this->curDate = time();
        
        // Set default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Load locallang labels
        $this->pi_loadLL();
        
        // Sets the new line character
        $this->NL   = chr( 10 );
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Sets the internal variables for tslib_pibase
        $this->setInternalVars();
        
        // Checks the configuration
        if( isset( $this->conf[ 'templateFile' ] ) ) {
            
            // Initializes the template file
            $this->api->fe_initTemplate( $this->conf[ 'templateFile' ] );
            
            // Gets the data from the extension table
            $this->getTableData();
            
            // Storage for the template markers
            $markers = array();
            
            // Title
            $markers[ '###TITLE###' ]        = $this->pi_getLL( 'title' );
            
            // Search options
            $markers[ '###MAIN_OPTIONS###' ] = $this->searchOptions();
            
            // Search results
            $markers[ '###MAIN_RESULTS###' ] = ( isset( $this->piVars[ 'formSubmit' ] ) ) ? $this->searchResults() : '';
            
            // Builds the full view
            $content = $this->api->fe_renderTemplate( $markers, '###MAIN###' );
            
        } else {
            
            // TS configuration error
            $content = $this->api->fe_makeStyledContent(
                'div',
                'error-ts',
                $this->pi_getLL( 'error-ts' )
            );
        }
        
        // Returns the content
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
            'pidList'          => 'sDEF:pages',
            'sortBy'           => 'sDISPLAY:sortby',
            'maxRecords'       => 'sDISPLAY:maxitems',
            'maxPages'         => 'sDISPLAY:maxpages',
            'keywordSelection' => 'sDISPLAY:keyword_select',
            'showPublic'       => 'sDISPLAY:show_public',
            'showThemes'       => 'sDISPLAY:show_themes'
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug( $this->conf, 'VD / Sanimedia: configuration array' );
    }
    
    /**
     * Sets internals variables.
     * 
     * This function is used to set the internal variables array
     * ($this->internal) needed to execute a MySQL query.
     * 
     * @return		Nothing
     */
    function setInternalVars()
    {
        // Sort by fields
        $this->piVars[ 'sort' ] = $this->conf[ 'sortBy' ];
        
        // Sets the internal variables
        $this->api->fe_setInternalVars(
            $this->conf[ 'maxRecords' ],
            $this->conf[ 'maxPages' ],
            false,
            $this->conf[ 'sortBy' ]
        );
    }
    
    /**
     * 
     */
    function searchOptions()
    {
        // Storage for the template markers
        $markers = array();
        
        // Value for the search input
        $searchInputValue = ( isset( $this->piVars[ 'keyword' ] ) ) ? $this->piVars[ 'keyword' ] : '';
        
        // Adds the labels
        $markers[ '###OPTIONS_PUBLIC_LABEL###' ]   = '<label for="'
                                                   . $this->prefixId
                                                   . '_public">'
                                                   . $this->pi_getLL( 'label-public' )
                                                   . '</label>';
        $markers[ '###OPTIONS_THEMES_LABEL###' ]   = '<label for="'
                                                   . $this->prefixId
                                                   . '_themes">'
                                                   . $this->pi_getLL( 'label-themes' )
                                                   . '</label>';
        $markers[ '###OPTIONS_KEYWORDS_LABEL###' ] = '<label for="'
                                                   . $this->prefixId
                                                   . '_keyword">'
                                                   . $this->pi_getLL( 'label-keywords' )
                                                   . '</label>';
        
        // Adds the select menus
        $markers[ '###OPTIONS_PUBLIC###' ]         = $this->buildSelect( 'public' );
        $markers[ '###OPTIONS_THEMES###' ]         = $this->buildSelect( 'themes' );
        
        // Adds the search input
        $markers[ '###OPTIONS_KEYWORDS###' ]       = '<input name="'
                                                   . $this->prefixId
                                                   . '[keyword]'
                                                   . '" id="'
                                                   . $this->prefixId
                                                   . '_keyword'
                                                   . '" type="text" size="'
                                                   . $this->conf[ 'keywordInputSize' ]
                                                   . '" value="'
                                                   . $searchInputValue
                                                   . '" />';
        
        // Adds the submit input
        $markers[ '###OPTIONS_SUBMIT###' ]         = '<input name="'
                                                   . $this->prefixId
                                                   . '[submit]" type="submit" value="'
                                                   . $this->pi_getLL( 'label-submit' )
                                                   . '" id="'
                                                   . $this->prefixId
                                                   . '_submit'
                                                   . '" />'
                                                   . '<input name="'
                                                   . $this->prefixId
                                                   . '[formSubmit]" id="'
                                                   . $this->prefixId
                                                   . '_formSubmit" type="hidden" value="1" />';
        
        // Adds the reset input
        $markers[ '###OPTIONS_CANCEL###' ]         = '<input name="'
                                                   . $this->prefixId
                                                   . '[reset]" type="reset" value="'
                                                   . $this->pi_getLL( 'label-reset' )
                                                   . '" id="'
                                                   . $this->prefixId
                                                   . '_reset'
                                                   . '" />';
        
        // TypoLink configuration for the form action
        $typoLink   = array(
            'parameter'    => $GLOBALS[ 'TSFE' ]->id,
            'useCacheHash' => 1
        );
        
        // Gets the form action URL
        $formAction = $this->cObj->typoLink_URL( $typoLink );
        
        // Creates the form
        $form = $this->api->fe_makeStyledContent(
            'form',
            'form',
            $this->api->fe_renderTemplate( $markers, '###OPTIONS###' ),
            1,
            false,
            false,
            $params = array(
                'name'    => $this->prefixId . '_form',
                'action'  => $formAction,
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                'method'  => 'post'
            )
        );
        
        // Returns the search options
        return $this->api->fe_makeStyledContent(
            'div',
            'search-options',
            $form
        );
    }
    
    /**
     * 
     */
    function searchResults() 
    {
        // Storage for the template markers
        $markers = array();
        
        // Clears the content of the markers
        $markers[ '###RESULTS_COUNT###' ] = '';
        $markers[ '###RESULTS_ITEMS###' ] = '';
        $markers[ '###RESULTS_PAGES###' ] = '';
        $markers[ '###RESULTS_TITLE###' ] = '';
        
        // Sets the title
        $markers[ '###RESULTS_TITLE###' ] = $this->pi_getLL( 'title-results' );
        
        // Checks if the page title must be substituted
        if( $this->conf[ 'substitutePageTitle' ] ) {
            
            // Sets the page title
            $GLOBALS[ 'TSFE' ]->page[ 'title' ] = $this->pi_getLL( 'title-page' );
            $GLOBALS[ 'TSFE' ]->indexedDocTitle = $this->pi_getLL( 'title-page' );
            $GLOBALS[ 'TSFE' ]->altPageTitle    = $this->pi_getLL( 'title-page' );
        }
        
        // No active page
        if ( !isset( $this->piVars[ 'pointer' ] ) ) {
            $this->piVars[ 'pointer' ] = 0;
        }
        
        // Additionnal MySQL WHERE clause for filters
        $whereClause = $this->buildWhereClause();
        
        // Gets the records number
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            'uid',
            $this->extTables[ 'pages' ],
            $whereClause
        );
        
        // Checks the DB ressource
        if( $res ) {
            
            // Sets the records number
            $this->internal[ 'res_count' ] = $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res );
            
            // Checks the record number
            if( $this->internal[ 'res_count' ] > 0 ) {
                
                // Values for the LIMIT clause
                $limitStart = $this->piVars[ 'pointer' ] * $this->conf[ 'maxRecords' ];
                $limitEnd   = $limitStart + $this->conf[ 'maxRecords' ];
                
                // Makes the listing query
                $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                    'uid,title,description,tx_vdsanimedia_public,tx_vdsanimedia_themes',
                    $this->extTables[ 'pages' ],
                    $whereClause,
                    false,
                    $this->conf[ 'sortBy' ],
                    $limitStart . ',' . $limitEnd
                );
                
                // Sets the current working table
                $this->internal[ 'currentTable' ] = $this->extTables[ 'pages' ];
                
                // Adds the browse box
                $markers[ '###RESULTS_PAGES###' ] = $this->api->fe_buildBrowseBox();
                
                // Label for the result count
                $resCountLabel = ( $this->internal[ 'res_count' ] > 1 ) ? 'rescount' : 'rescount-single';
                
                // Adds the results count
                $markers[ '###RESULTS_COUNT###' ] = $this->api->fe_makeStyledContent(
                    'span',
                    'rescount',
                    sprintf(
                        $this->pi_getLL( $resCountLabel ),
                        $this->internal[ 'res_count' ]
                    )
                );
                
                // Adds the list
                $markers[ '###RESULTS_ITEMS###' ] = $this->buildList( $res );
                
            } else {
                
                // No result
                $markers[ '###RESULTS_COUNT###' ] = $this->api->fe_makeStyledContent(
                    'div',
                    'error',
                    $this->pi_getLL( 'error-noresult' )
                );
            }
        } else {
                
            // No result
            $markers[ '###RESULTS_COUNT###' ] = $this->api->fe_makeStyledContent(
                'div',
                'error',
                $this->pi_getLL( 'error-noresult' )
            );
        }
        
        // Returns the result views
        return $this->api->fe_makeStyledContent(
            'div',
            'search-results',
            $this->api->fe_renderTemplate( $markers, '###RESULTS###' )
        );
    }
    
    /**
     * 
     */
    function buildWhereClause()
    {
        // Storage
        $whereClause  = array();
        
        // Only selects the sanimedia enabled pages
        $whereClause[] = 'tx_vdsanimedia_enable=1';
        
        // Checks for a public
        if( isset( $this->piVars[ 'public' ] ) && $this->piVars[ 'public' ] ) {
            
            // Selects the public
            $whereClause[] = $GLOBALS[ 'TYPO3_DB' ]->listQuery(
                'tx_vdsanimedia_public',
                $this->piVars[ 'public' ],
                $this->extTables[ 'pages' ]
            );
        }
        
        // Checks for a theme
        if( isset( $this->piVars[ 'themes' ] ) && $this->piVars[ 'themes' ] ) {
            
            // Checks for a theme
            $whereClause[] = $GLOBALS[ 'TYPO3_DB' ]->listQuery(
                'tx_vdsanimedia_themes',
                $this->piVars[ 'themes' ],
                $this->extTables[ 'pages' ]
            );
        }
        
        // Checks for a keyword
        if( isset( $this->piVars[ 'keyword' ] ) && $this->piVars[ 'keyword' ] ) {
            
            // Storage
            $keySelect = array();
            
            // List of the user keywords
            $keywords = explode( ' ', trim( $this->piVars[ 'keyword' ] ) );
            
            // Process each submitted keyword
            foreach( $keywords as $word ) {
                
                // Clears whitespace, and converts to lowercase
                $word = strtolower( trim( $word ) );
                
                // Process each keyword
                foreach( $this->keywords as $keyword => $keywordId ) {
                    
                    // Checks the length
                    if( strlen( $word ) > strlen( $keyword ) ) {
                        
                        // No possible match - Process next keyword
                        continue;
                    }
                    
                    // Checks if keywords are matching
                    if( strstr( $keyword, $word ) ) {
                        
                        // Selects the keword
                        $keySelect[] =  $GLOBALS[ 'TYPO3_DB' ]->listQuery(
                            'tx_vdsanimedia_keywords',
                            $keywordId,
                            $this->extTables[ 'pages' ]
                        );
                    }
                }
            }
            
            // Selects the keywords
            $whereClause[] = '(' . implode( ' ' . $this->conf[ 'keywordSelection' ] . ' ', $keySelect ) . ')';
        }
        
        // DEBUG ONLY - Output the where array
        #$this->api->debug( $whereClause, 'VD / Sanimedia: WHERE clause' );
        
        // Returns the where clause
        return implode( ' AND ', $whereClause );
    }
    
    /**
     * 
     */
    function buildList( &$res )
    {
        // Storage
        $htmlCode = array();
        
        // Process each record
        while( $page = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
            
            // Storage for the template markers
            $markers = array();
            
            // TypoLink configuration for the page link
            $typoLink   = array(
                'parameter'    => $page[ 'uid' ],
                'useCacheHash' => 1
            );
            
            // Sets the markers
            $markers[ '###ITEM_DESCRIPTION###' ] = $page[ 'description' ];
            $markers[ '###ITEM_TITLELINK###' ]   = $this->cObj->typoLink( $page[ 'title' ], $typoLink );
            $markers[ '###ITEM_PUBLIC###' ]      = '';
            $markers[ '###ITEM_THEMES###' ]      = '';
            
            // Displays the public
            if( $this->conf[ 'showPublic' ] ) {
                
                // Adds the public
                $markers[ '###ITEM_PUBLIC###' ] = $this->pageInfos( 'public', $page );
            }
            
            // Displays the themes
            if( $this->conf[ 'showThemes' ] ) {
                
                // Adds the themes
                $markers[ '###ITEM_THEMES###' ] = $this->pageInfos( 'themes', $page );
            }
            
            // Adds the page
            $htmlCode[] = $this->api->fe_makeStyledContent(
                'div',
                'page',
                $this->api->fe_renderTemplate(
                    $markers,
                    '###ITEM###'
                )
            );
        }
        
        // Returns the list of pages
        return implode( $this->NL, $htmlCode );
    }
    
    /**
     * 
     */
    function pageInfos( $field, &$page )
    {
        // Label
        $label = $this->api->fe_makeStyledContent( 'strong', 'label', $this->pi_getLL( 'label-' . $field ) );
        
        // Data in the page array
        $pageData = explode( ',', $page[ 'tx_vdsanimedia_' . $field ] );
        
        // Corresponding records
        $data =& $this->$field;
        
        // Storage
        $content = array();
        
        // Process the page data
        foreach( $pageData as $uid ) {
            
            // Gets the corresponding record title
            $content[] = $data[ $uid ][ 'title' ];
        }
        
        // Returns the page info
        return $label . ' ' . implode( ' - ', $content );
    }
    
    /**
     * 
     */
    function getTableData()
    {
        // Tables to get
        $tables = array(
            'public',
            'themes',
            'keywords'
        );
        
        // Storage
        $keywords = array();
        
        // Where clause to select records
        $whereClause = '';
        
        // Checks for storage pages
        if( $this->conf[ 'pidList' ] ) {
            
            // Adds the pages IDs
            $whereClause = 'pid IN (' . $this->conf[ 'pidList' ] . ')';
        }
        
        // Process each table
        foreach( $tables as $tableName ) {
            
            // Selects the records
            $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                '*',
                $this->extTables[ $tableName ],
                $whereClause
            );
            
            // Check DB ressource
            if( $res ) {
                
                // Process each record
                while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                    
                    // Checks the table name
                    if( $tableName === 'keywords' ) {
                        
                        // Reference to the storage array
                        $storage =& $keywords;
                        
                    } else {
                        
                        // Reference to the storage array
                        $storage =& $this->$tableName;
                    }
                    
                    // Adds the record
                    $storage[ $row[ 'uid' ] ] = $row;
                }
            }
        }
        
        // Process each keyword
        foreach( $keywords as $key => $value ) {
            
            // Key is the keyword
            $realKey                    = strtolower( $value[ 'keyword' ] );
            
            // Keeps only the UID field
            $this->keywords[ $realKey ] = $value[ 'uid' ];
        }
        
        // DEBUG ONLY - Output the table data
        #$this->api->debug( $this->public,   'VD / Sanimedia: table data' );
        #$this->api->debug( $this->themes,   'VD / Sanimedia: table data' );
        #$this->api->debug( $this->keywords, 'VD / Sanimedia: table data' );
    }
    
    /**
     * 
     */
    function buildSelect( $name )
    {
        // Storage
        $htmlCode = array();
        
        // Starts the select
        $htmlCode[] = '<select id="'
                    . $this->prefixId
                    . '_'
                    . $name
                    . '" name="'
                    . $this->prefixId
                    . '['
                    . $name
                    . ']">';
        
        // Adds the default option
        $htmlCode[] = '<option value="">'
                    . $this->pi_getLL( 'select-all-' . $name )
                    . '</option>';
        
        $piVarValue = ( isset( $this->piVars[ $name ] ) ) ? $this->piVars[ $name ] : 0;
        
        // Process each option
        foreach( $this->$name as $key => $value ) {
            
            // Selected state
            $selected   = ( $value[ 'uid' ] == $piVarValue ) ? ' selected="selected"' : '';
            
            // Adds the options tag
            $htmlCode[] = '<option value="'
                        . $value[ 'uid' ]
                        . '"'
                        . $selected
                        . '>'
                        . $value[ 'title' ]
                        . '</option>';
        }
        
        // Ends the select
        $htmlCode[] = '</select>';
        
        // Returns the select
        return implode( $this->NL, $htmlCode );
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_sanimedia/pi1/class.tx_vdsanimedia_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_sanimedia/pi1/class.tx_vdsanimedia_pi1.php']);
}
?>
