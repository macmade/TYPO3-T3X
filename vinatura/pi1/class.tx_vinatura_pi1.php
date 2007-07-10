<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2005 Jean-David Gadina (info@macmade.net)
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
 * Plugin 'User registration' for the 'vinatura' extension.
 *
 * @author	Jean-David Gadina <info@macmade.net>
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:		1 - MAIN
 *        :		function main($content,$conf)
 * 
 *				TOTAL FUNCTIONS: 
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_vinatura_pi1 extends tslib_pibase
{
    // Same as class name
    var $prefixId           = 'tx_vinatura_pi1';
    
    // Path to this script relative to the extension dir.
    var $scriptRelPath      = 'pi1/class.tx_vinatura_pi1.php';
    
    // The extension key
    var $extKey             = 'vinatura';
    
    // Check plugin hash
    var $pi_checkCHash      = FALSE;
    
    // Upload directory
    var $uploadDir          = 'uploads/tx_vinatura/';
    
    // Version of the Developer API required
    var $apimacmade_version = 2.8;
    
    // Internal variables
    var $searchFields       = 'name,city,country';
    var $orderByFields      = 'name';
    
    // Database tables
    var $extTables          = array(
        'users'     => 'fe_users',
        'profiles'  => 'tx_vinatura_profiles',
        'wines'     => 'tx_vinatura_wines',
        'winetypes' => 'tx_vinatura_winetypes',
    );
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin "tx_femp3player_pi1", and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param		$content			The content object
     * @param		$conf				The TS setup
     * @return		The content of the plugin.
     */
    function main( $content, $conf )
    {
        
        // New instance of the macmade.net API
        $this->api = new tx_apimacmade( $this );
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Set default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Load locallang labels
        $this->pi_loadLL();
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Store FE language
        $this->lang = $GLOBALS[ 'TSFE' ]->sys_page->sys_language_uid;
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // MySQL query init
        $this->setInternalVars();
        
        // Check template file (TS or Flex)
        $templateFile = ( $this->pi_getFFvalue( $this->piFlexForm, 'template_file' , 'sTMPL' ) == '' ) ? $this->conf[ 'templateFile' ] : $this->uploadDir . $this->conf[ 'templateFile' ];
        
        // Template load and init
        $this->api->fe_initTemplate($templateFile);
        
        // Content
        $content = $this->buildList();
        
        // Return content
        return $this->pi_wrapInBaseClass( $content );
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
            'pidList'      => 'sDEF:pages',
            'recursive'    => 'sDEF:recursive',
            'usergroup'    => 'sDEF:usergroup',
            'templateFile' => 'sTMPL:template_file',
            'list.'        => array(
                'maxRecords' => 'sLIST:list_maxrecords',
                'maxPages'   => 'sLIST:list_maxpages',
            ),
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'Weleda Baby PI1: configuration array');
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
        $this->piVars[ 'sort' ] = $this->conf[ 'list.' ][ 'sortBy' ];
        
        // Set general internal variables
        $this->api->fe_setInternalVars(
            $this->conf[ 'list.' ][ 'maxRecords' ],
            $this->conf[ 'list.' ][ 'maxPages' ],
            $this->searchFields,
            $this->orderByFields
        );
    }
    
    /**
     * 
     */
    function buildList()
    {
        
        // No active page
        if ( !isset( $this->piVars[ 'pointer' ] ) ) {
            $this->piVars[ 'pointer' ] = 0;
        }
        
        // Additionnal MySQL WHERE clause for filters
        $whereClause = '';
        
        // Regular expression for navig
        if ( array_key_exists( 'letter', $this->piVars ) ) {
            
            // Letter to select
            $letter = $this->piVars[ 'letter' ];
            
            // Regular expression
            $regExp = '^' . $letter . '';
            
            // Set WHERE clause
            $whereClause = 'AND name REGEXP \'' . $regExp . '\'';
        }
        
        // Get records number
        $res = $this->pi_exec_query( $this->extTables[ 'users' ], 1, $whereClause );
        list( $this->internal[ 'res_count' ] ) = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_row( $res );
        
        // Make listing query - Pass query to MySQL database
        $res = $this->pi_exec_query( $this->extTables[ 'users' ], 0, $whereClause );
        $this->internal[ 'currentTable' ] = $this->extTables[ 'users' ];
        
        // Template markers
        $templateMarkers = array();
        
        // Replace markers
        $templateMarkers[ '###SEARCH###' ]   = $this->api->fe_buildSearchBox();
        $templateMarkers[ '###NAVIG###' ]    = $this->buildNavig();
        $templateMarkers[ '###BACKLIST###' ] = ( array_key_exists( 'letter', $this->piVars ) || array_key_exists( 'sword', $this->piVars ) ) ? $this->api->fe_makeStyledContent( 'div', 'back-list', $this->api->fe_linkTP_unsetPIvars( $this->pi_getLL( 'backlist' ), array(), array( 'sword', 'letter' ), 1 ) ) : '';
        $templateMarkers[ '###MEMBERS###' ]  = ( $this->internal[ 'res_count' ] ) ? $this->makeList( $res ) : '';
        $templateMarkers[ '###BROWSE###' ]   = $this->api->fe_buildBrowseBox();
        $templateMarkers[ '###USER###' ]     = ( isset( $this->piVars[ 'showUid' ] ) ) ? $this->showUser() : '';
        
        // Wrap all in a CSS element
        return $this->api->fe_renderTemplate( $templateMarkers, '###LIST###' );
    }
    
    /**
     * 
     */
    function buildNavig()
    {
        
        // Letters
        $letters = array(
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z'
        );
        
        // Storage
        $htmlCode = array();
        
        // Process each letter
        foreach( $letters as $value ) {
            
            // CSS class
            $class = ( array_key_exists( 'letter', $this->piVars ) && $this->piVars[ 'letter' ] == $value ) ? 'letter-selected' : 'letter';
            
            // Letter
            $letter = ( array_key_exists( 'letter', $this->piVars ) && $this->piVars[ 'letter' ] == $value ) ? $value : $this->pi_linkTP_keepPIvars( $value, array( 'letter' => $value ), 1 );
            
            // Add letter
            $htmlCode[] = $this->api->fe_makeStyledContent( 'span', $class, $letter );
        }
        
        // Return navig
        return $this->api->fe_makeStyledContent( 'div', 'navig', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function makeList( $res )
    {
        
        // Items storage
        $htmlCode = array();
        
        // Process each member
        while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
            
            // User profile
            $profile = $this->getProfile( $row[ 'uid' ] );
            
            // User name
            $userName = $row[ 'name' ] . ' ' . $profile[ 'firstname' ];
            
            // Link
            $link = $this->pi_linkTP_keepPIvars( $userName, array( 'showUid' => $row[ 'uid' ] ), 1 );
            
            // CSS class
            $class = ( isset( $this->piVars[ 'showUid' ] ) && $this->piVars[ 'showUid' ] == $row[ 'uid' ] ) ? 'list-member-act' : 'list-member';
            
            // Add username
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', $class, $link );
        }
        
        // Return list
        return $this->api->fe_makeStyledContent( 'div', 'list', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function showUser()
    {
        // Get user
        $user = $this->pi_getRecord( $this->extTables['users'] , $this->piVars['showUid'] );
        
        // Check user
        if( is_array( $user ) ) {
            
            // Get profile
            $profile = $this->getProfile( $user[ 'uid' ] );
            
            // Template markers
            $templateMarkers = array();
            
            // Picture
            $picture = ( !empty( $user[ 'image' ] ) ) ? $this->api->fe_createImageObjects( $user[ 'image' ], $this->conf[ 'single.' ][ 'picture.' ], 'uploads/pics/' ) : '';
            
            // Name
            $fullName = $user[ 'name' ] . ' ' . $profile[ 'firstname' ];
            
            // Title
            $title = ( !empty( $user[ 'title' ] ) ) ? $user[ 'title' ] : '';
            
            // State
            $state = $this->pi_getLL( 'state' ) . ' <span>' . $this->pi_getLL( 'state.' . $profile[ 'state' ] ) . '</span>';
            
            // Prices
            $prices = $this->pi_getLL( 'prices' ) . ' <span>' . htmlspecialchars( $profile[ 'prices' ] ) . '</span>';
            
            // White wines
            $whiteWines = $this->pi_getLL( 'whitewines' ) . ' <span>' . $this->getWineTypes( $profile, 0 ) . '</span>';
            
            // Red wines
            $redWines = $this->pi_getLL( 'redwines' ) . ' <span>' . $this->getWineTypes( $profile, 1 ) . '</span>';
            
            // Override template markers
            $templateMarkers[ '###IMAGE###' ]       = $this->api->fe_makeStyledContent( 'div', 'image', $picture );
            $templateMarkers[ '###NAME###' ]        = $this->api->fe_makeStyledContent( 'div', 'name', $fullName );
            $templateMarkers[ '###DOMAIN###' ]      = $this->api->fe_makeStyledContent( 'div', 'domain', $profile[ 'domain' ] );
            $templateMarkers[ '###TITLE###' ]       = $this->api->fe_makeStyledContent( 'div', 'title', $title );
            $templateMarkers[ '###INFOS###' ]       = $this->api->fe_makeStyledContent( 'div', 'infos', $this->getUserInfos( $user, $profile ) );
            $templateMarkers[ '###STATE###' ]       = $this->api->fe_makeStyledContent( 'div', 'state', $state );
            $templateMarkers[ '###DESCRIPTION###' ] = $this->api->fe_makeStyledContent( 'div', 'description', nl2br( htmlspecialchars( $profile[ 'description' ] ) ) );
            $templateMarkers[ '###PRICES###' ]      = $this->api->fe_makeStyledContent( 'div', 'prices', $prices );
            $templateMarkers[ '###WHITEWINES###' ]  = $this->api->fe_makeStyledContent( 'div', 'whitewines', $whiteWines );
            $templateMarkers[ '###REDWINES###' ]    = $this->api->fe_makeStyledContent( 'div', 'redwines', $redWines );
            $templateMarkers[ '###WINELIST###' ]    = $this->api->fe_makeStyledContent( 'div', 'winelist', $this->getUserWines( $user[ 'uid' ] ) );
            
            // Wrap all in a CSS element
            $templateCode = $this->api->fe_renderTemplate( $templateMarkers, '###PROFILE###');
            return $this->api->fe_makeStyledContent( 'div', 'user', $templateCode );
        }
    }
    
    /**
     * 
     */
    function getUserInfos( &$user, &$profile )
    {
        // Storage
        $infos = array();
        
        // Address
        $infos[] = $this->api->fe_makeStyledContent( 'div', 'address', nl2br( htmlspecialchars( $user[ 'address' ] ) ) );
        
        // City
        $infos[] = $this->api->fe_makeStyledContent( 'div', 'city', $user[ 'zip' ] . ' ' .$user[ 'city' ] . ' / ' . $profile[ 'state' ] );
        
        // Telephone
        if( !empty( $user[ 'telephone' ] ) ) {
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'telephone', $user[ 'telephone' ] );
        }
        
        // Cellular
        if( !empty( $profile[ 'cellular' ] ) ) {
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'cellular', $profile[ 'cellular' ] );
        }
        
        // Fax
        if( !empty( $user[ 'fax' ] ) ) {
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'fax', $user[ 'fax' ] );
        }
        
        // Email
        $infos[] = $this->api->fe_makeStyledContent( 'div', 'email', $this->cObj->typoLink( $user[ 'email' ], array( 'parameter' => $user[ 'email' ] ) ) );
        
        // Website
        if( !empty( $user[ 'www' ] ) ) {
            $infos[] = $this->api->fe_makeStyledContent( 'div', 'www', $this->cObj->typoLink( $user[ 'www' ], array( 'parameter' => $user[ 'www' ], 'extTarget' => '_blank' ) ) );
        }
        
        // Return infos
        return implode( chr( 10 ), $infos );
    }
    
    /**
     * 
     */
    function getWineTypes( &$profile, $type )
    {
        // Get wine types
        $types = ( $type == 0 ) ? explode( ',', $profile[ 'whitewines' ] ) : explode( ',', $profile[ 'redwines' ] );
        
        // Check types
        if( count( $types ) ) {
            
            // Storage
            $result = array();
            
            // Process types
            foreach( $types as $uid ) {
                
                // Get record
                $row =  $this->pi_getRecord( $this->extTables[ 'winetypes' ], $uid );
                $row =& $this->recordL10N( $this->extTables[ 'winetypes' ], $row );
                
                // Add title
                $result[] = $row[ 'title' ];
            }
            
            // Return types
            return implode( ', ', $result );
        }
    }
    
    /**
     * 
     */
    function getUserWines( $uid )
    {
        // Select wines
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( '*', $this->extTables[ 'wines' ], 'feuser=' . $uid . $this->cObj->enableFields( $this->extTables[ 'wines' ] ) );
        
        // Check for wines
        if( $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) ) {
            
            // Storage
            $htmlCode = array();
            
            // Header
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'header', $this->pi_getLL( 'winelist' ) );
            
            // Process wines
            while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                
                // Storage
                $wine = array();
                
                // Wine type
                $type =  $this->pi_getRecord( $this->extTables[ 'winetypes' ], $row[ 'type' ] );
                $type =& $this->recordL10N( $this->extTables[ 'winetypes' ], $type );
                
                // Add wine name
                $wine[] = $this->api->fe_makeStyledContent( 'div', 'wine-name', '<span>' . $row[ 'title' ] . '</span> (' . $type[ 'title' ] . ')' );
                
                // Check for a description
                if( !empty( $row[ 'description' ] ) ) {
                    
                    // Add description
                    $wine[] = $this->api->fe_makeStyledContent( 'div', 'wine-description', $row[ 'description' ] );
                }
                
                // Add wine
                $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'wine', implode( chr( 10 ), $wine ) );
            }
            
            // Return content
            return implode( chr( 10 ), $htmlCode );
        }
    }
    
    /**
     * 
     */
    function getProfile( $uid )
    {
        $profileRes = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( '*', $this->extTables[ 'profiles' ], 'feuser=' . $uid );
        $profile    = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $profileRes );
        return $profile;
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
    function clearPageCache( $pid )
    {
        
        // Delete page cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pages', 'page_id=' . $pid );
        
        // Delete page section cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pagesection', 'page_id=' . $pid );
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vinatura/pi1/class.tx_vinatura_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vinatura/pi1/class.tx_vinatura_pi1.php']);
}
?>
