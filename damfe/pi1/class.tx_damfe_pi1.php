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
 * Plugin 'DAM / Frontend Integration' for the 'damfe' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *        :     function main($content,$conf)
 *        :     function setConfig
 * 
 *              TOTAL FUNCTIONS: 
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

// DAM DB class
require_once( t3lib_extMgm::extPath( 'dam' ) . 'lib/class.tx_dam_db.php' );

class tx_damfe_pi1 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_damfe_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_damfe_pi1.php';
    
    // The extension key
    var $extKey             = 'damfe';
    
    // Version of the Developer API required
    var $apimacmade_version = 2.8;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Extension tables
    var $extTables          = array(
        'mediaTypes' => 'tx_dam_metypes_avail',
        'cat'        => 'tx_dam_cat',
        'media'      => 'tx_dam',
    );
    
    // Internal variables
    var $searchFields       = 'title,keywords,description,abstract,search_content,publisher,copyright,instructions,loc_desc,loc_country,loc_city';
    var $orderByFields      = 'title';
    
    // Developer API
    var $api                = NULL;
    
    // Database object
    var $db                 = NULL;
    
    // Flexform data
    var $piFlexForm         = '';
    
    // Flexform data
    var $damPid             = false;
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin "tx_damfe_pi1", and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param       $content            The content object
     * @param       $conf               The TS setup
     * @return      The content of the plugin.
     */
    function main( $content, $conf )
    {
        
        // New instance of the macmade.net API
        $this->api = new tx_apimacmade( $this );
        
        // Reference to the database object
        $this->db =& $GLOBALS[ 'TYPO3_DB' ];
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Set default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Load locallang labels
        $this->pi_loadLL();
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm =& $this->cObj->data[ 'pi_flexform' ];
        
        // DAM storage PID
        $this->damPid = tx_dam_db::getPid();
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Set internal variables
        $this->setInternalVars();
        
        // Add JavaScript code
        $this->api->fe_buildSwapClassesJSCode( 'open', 'closed', array(
            'plus'  => $this->conf[ 'icons.' ][ 'plus' ],
            'minus' => $this->conf[ 'icons.' ][ 'minus' ]
        ) );
        
        // Init template
        $this->api->fe_initTemplate( $this->conf[ 'templateFile' ] );
        
        // Storage
        $templateMarkers = array();
        
        // Substitute template markers
        $templateMarkers[ '###CATMENU###' ]   = $this->catMenu();
        $templateMarkers[ '###TYPEMENU###' ]  = $this->mediaTypesMenu();
        $templateMarkers[ '###SELECTION###' ] = $this->showSelection();
        $templateMarkers[ '###SEARCHBOX###' ] = $this->api->fe_buildSearchBox();
        $templateMarkers[ '###LIST###' ]      = $this->showList();
        $templateMarkers[ '###BROWSEBOX###' ] = $this->api->fe_buildBrowseBox();
        
        // Render template
        $template = $this->api->fe_renderTemplate( $templateMarkers, '###MAIN###' );
        
        // Return content
        return $this->pi_wrapInBaseClass( $template );
    }
    
    /**
     * Set configuration array.
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return		Void
     */
    function setConfig()
    {
        
        // Mapping array for PI flexform
        $flex2conf = array(
            
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex( $flex2conf, $this->conf, $this->piFlexForm );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'DAM FE: configuration array');
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
        
        // SORT BY
        $this->piVars[ 'sort' ] = 'title';
        
        // PID for selecting records
        $this->conf[ 'pidList' ] = $this->damPid;
        
        // Set general internal variables
        $this->api->fe_setInternalVars(
            $this->conf[ 'list.' ][ 'maxResults' ],
            $this->conf[ 'list.' ][ 'maxPages' ],
            $this->searchFields,
            $this->orderByFields
        );
    }
    
    /**
     * 
     */
    function catMenu( $parent = 0 )
    {
        
        // Storage
        $htmlCode = array();
        
        // Header
        $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'cat-header', $this->pi_getLL( 'cat' ) );
        
        // Get categories
        $htmlCode[] = $this->getCategories();
        
        // Return code
        return  $this->api->fe_makeStyledContent( 'div', 'cat', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function getCategories( $parent = 0, $level = 0 )
    {
        
        // Storage
        $htmlCode = array();
        
        // Where clause for selecting categories
        $whereClause = 'pid=' . $this->damPid . ' AND parent_id=' . $parent;
            
        // Order by clause for selecting categories
        $orderBy = 'sorting';
        
        // Get DAM categories
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            '*',
            $this->extTables[ 'cat' ],
            $whereClause, 
            '',
            $orderBy
        );
        
        // Check DB result
        if ( $res && $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) ) {
            
            // Process categories
            while( $cat = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                
                // Title
                $title = ( empty( $cat[ 'nav_title' ] ) ) ? $cat[ 'title' ] : $cat[ 'nav_title' ];
                
                // List ID
                $listId = $this->prefixId . '_cat_' . $cat[ 'uid' ];
                
                // Link
                $link = $this->pi_linkTP_keepPIvars( $title, array( 'sel' => array( 'cat' => $cat[ 'uid' ] ) ) );
                
                // Check sub categories
                if ( $subItems = $this->getCategories( $cat[ 'uid' ], $level + 1 ) ) {
                    
                    // Icon
                    $icon = $this->cObj->fileResource( $this->conf[ 'icons.' ][ 'plus' ], 'id="' . $listId . '_pic"' );
                    
                    // Icon link
                    $iconLink = $this->api->fe_makeSwapClassesJSLink( $listId, $icon, 0, 0, array( 'title' => $title ) );
                    
                    // Full link with sub items
                    $link = $iconLink . $link . $subItems;
                    
                    // List content
                    $listContent = $this->api->fe_makeStyledContent( 'div', 'category', $link );
                    
                } else {
                    
                    // List content
                    $listContent = $this->api->fe_makeStyledContent( 'div', 'category-nochild', $link );
                }
                
                // Add list item
                $htmlCode[] = $this->api->fe_makeStyledContent( 'li', 'closed', $listContent, 0, 0, 0, array( 'id' => $listId ) );
            }
            
            // Return list
            return $this->api->fe_makeStyledContent( 'ul', 'cat-level-' . $level, implode( chr( 10 ), $htmlCode ) );
        }
    }
    
    /**
     * 
     */
    function mediaTypesMenu()
    {
        
        // Storage
        $htmlCode  = array();
        $mediaList = array();
        
        // Where clause for selecting media types
        $whereClause = 'pid=' . $this->damPid;
        
        // Order by clause for selecting media types
        $orderBy = 'type DESC,sorting,title DESC';
        
        // Get DAM media types
        $res = $this->db->exec_SELECTquery(
            '*',
            $this->extTables[ 'mediaTypes' ],
            $whereClause,
            '',
            $orderBy
        );
        
        // Check DB result
        if ( $res ) {
            
            // Header
            $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'mediatypes-header', $this->pi_getLL( 'mediatypes' ) );
            
            // Buffer for media subtypes
            $subTypes = array();
            
            // Process types
            while( $type = $this->db->sql_fetch_assoc( $res ) ) {
                
                // Check root types
                if ( $type[ 'parent_id' ] == 0 ) {
                    
                    // Title
                    $title = ucfirst( $type[ 'title' ] );
                    
                    // List ID
                    $listId = $this->prefixId . '_medialist_' . $type[ 'uid' ];
                    
                    // Icon
                    $icon = $this->cObj->fileResource( $this->conf[ 'icons.' ][ 'plus' ], 'id="' . $listId . '_pic"' );
                    
                    // Icon link
                    $iconLink = $this->api->fe_makeSwapClassesJSLink( $listId, $icon, 0, 0, array( 'title' => $title ) );
                    
                    // Link
                    $link = $this->pi_linkTP_keepPIvars( $title, array( 'sel' => array( 'mediatype' => $type[ 'type' ] ) ) );
                    
                    // Fine selectors
                    $selectors = $this->api->fe_makeStyledContent( 'div', 'selectors', '+ -' );
                    
                    // Check for media subtypes
                    if ( count( $subTypes ) ) {
                        
                        // Sub items
                        $subItems = $this->api->fe_makeStyledContent( 'ul', 'medialist-subtypes', implode( chr( 10 ), array_reverse( $subTypes ) ) );
                        
                        // Full link with sub items
                        $fullLink = $this->api->fe_makeStyledContent( 'div', 'link', $iconLink . $link ) . $selectors . $subItems;
                        
                        // Reset subtypes buffer
                        $subTypes = array();
                        
                    } else {
                        
                        // Full link
                        $fullLink = $this->api->fe_makeStyledContent( 'div', 'link', $link ) . $selectors;
                    }
                    
                    // Add list item
                    $mediaList[] = $this->api->fe_makeStyledContent( 'li', 'closed', $fullLink, 0, 0, 0, array( 'id' => $listId ) );
                    
                } else {
                    
                    // Title
                    $title = $type[ 'title' ];
                    
                    // Link
                    $link = $this->pi_linkTP_keepPIvars( $title, array( 'sel' => array( 'mediasubtype' => $type[ 'title' ] ) ) );
                    
                    // Fine selectors
                    $selectors = $this->api->fe_makeStyledContent( 'div', 'selectors', '+ -' );
                    
                    // Ful link
                    $fullLink = $this->api->fe_makeStyledContent( 'div', 'link', $link ) . $selectors;
                    
                    // Add media subtype to buffer
                    $subTypes[] = $this->api->fe_makeStyledContent( 'li', 'meidalist-subtype-' . $title,$fullLink );
                }
            }
            
            // Add full list
            $htmlCode[] = $this->api->fe_makeStyledContent( 'ul', 'medialist', implode( chr( 10 ), array_reverse( $mediaList ) ) );
        }
        
        // Return code
        return $this->api->fe_makeStyledContent( 'div', 'mediatypes', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function showSelection()
    {
        
        // Storage
        $htmlCode = array();
        
        // Header
        $htmlCode[] = $this->api->fe_makeStyledContent( 'h2', 'selection-header', $this->pi_getLL( 'selection' ) );
        
        // Return code
        return  $this->api->fe_makeStyledContent( 'div', 'selection', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function showList()
    {
        
        // No active page
        if ( !isset( $this->piVars[ 'pointer' ] ) ) {
            $this->piVars[ 'pointer' ] = 0;
        }
        
        // Build DB WHERE clause
        $whereClause = $this->whereClause();
        
        // Get records number
        $res = $this->pi_exec_query( $this->extTables[ 'media' ], 1, $whereClause );
        list( $this->internal[ 'res_count' ] ) = $this->db->sql_fetch_row( $res );
        
        // Make listing query - Pass query to DB
        $res = $this->pi_exec_query( $this->extTables[ 'media' ], 0, $whereClause );
        $this->internal[ 'currentTable' ] = $this->extTables[ 'media' ];
        
        // Check DB ressource
        if ( $res ) {
            
            // Storage
            $htmlCode = array();
            $headers  = array();
            
            // Counter
            $counter = 0;
            
            // Headers
            $headers[] = $this->api->fe_makeStyledContent( 'div', 'header-title', '<h3>' . $this->pi_getLL( 'title' ) . '</h3>' );
            $headers[] = $this->api->fe_makeStyledContent( 'div', 'header-mtime', '<h3>' . $this->pi_getLL( 'date' ) . '</h3>' );
            $headers[] = $this->api->fe_makeStyledContent( 'div', 'header-size', '<h3>' . $this->pi_getLL( 'size' ) . '</h3>' );
            $headers[] = $this->api->fe_makeStyledContent( 'div', 'header-mimetype', '<h3>' . $this->pi_getLL( 'type' ) . '</h3>' );
            
            // Add headers
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'list-header', $this->api->fe_makeStyledContent( 'div', 'row', implode( chr( 10 ), $headers ) ) );
            
            // Get items to list
            while( $this->internal[ 'currentRow' ] = $this->db->sql_fetch_assoc( $res ) ) {
                
                // Row class
                $rowClass = ( $counter == 0 ) ? 'row' : 'row-alt';
                
                // Add item
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'list-item', $this->showItem( $this->internal[ 'currentRow' ], $rowClass ) );
                
                // Modify counter
                $counter = ( $counter == 0 ) ? 1 : 0;
            }
            
            // Return items
            return $this->api->fe_makeStyledContent( 'div', 'list', implode( chr( 10 ), $htmlCode ) );
        }
    }
    
    /**
     * 
     */
    function whereClause()
    {
        
        // Base WHERE clause
        $whereClause = ' AND pid=' . $this->damPid;
        
        // Check filters
        if ( array_key_exists( 'sel', $this->piVars ) ) {
            
            // Store filters
            $sel =& $this->piVars[ 'sel' ];
            
            // Media type
            if ( array_key_exists( 'mediatype', $sel ) ) {
                
                // Add filter
                $whereClause .= ' AND media_type IN (' . $sel[ 'mediatype' ] . ')';
            }
            
            // Media sub type
            if ( array_key_exists( 'mediasubtype', $sel ) ) {
                
                // Get types
                $types = explode( ',', $sel[ 'mediasubtype' ] );
                
                // Process file types
                foreach( $types as $key => $value ) {
                    
                    // Add quotes
                    $types[ $key ] = '"' . $value . '"';
                }
                
                // Start filter
                $whereClause .= ' AND file_type LIKE (' . implode( ',', $types ) . ')';
            }
        }
        
        // DEBUG ONLY - Show query
        #$this->api->debug($whereClause);
        
        // Return full WHERE clause
        return $whereClause;
    }
    
    /**
     * 
     */
    function showItem( $row, $rowClass )
    {
        
        // Storage
        $htmlCode = array();
        
        // Title
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'list-title', $row[ 'title' ] );
        
        // Modification time
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'list-mtime', date( $this->conf[ 'dateFormat' ], $row[ 'file_mtime' ] ) );
        
        // File size
        $size = $row[ 'file_size' ];
        
        // Size in kilobytes
        $size = $size / 1024;
        
        // Suffix
        $sizeSuffix = $this->pi_getLL( 'kilobyte' );
        
        // Check size
        if ( $size >= 1000 ) {
            
            // Size in megabytes
            $size = $size / 1024;
            
            // Suffix
            $sizeSuffix = $this->pi_getLL( 'megabyte' );
        }
        
        // Size
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'list-size', round( $size, 2 ) . ' ' . $sizeSuffix );
        
        // Mime type
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'list-mimetype', $row[ 'file_mime_type' ] . '/' . $row[ 'file_mime_subtype' ] );
        
        // Return code
        return $this->api->fe_makeStyledContent( 'div', $rowClass, implode( chr( 10 ), $htmlCode ) );
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damfe/pi1/class.tx_damfe_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damfe/pi1/class.tx_damfe_pi1.php']);
}
?>
