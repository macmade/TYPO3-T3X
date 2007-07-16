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

class tx_vinatura_pi3 extends tslib_pibase
{
    // Same as class name
    var $prefixId           = 'tx_vinatura_pi3';
    
    // Path to this script relative to the extension dir.
    var $scriptRelPath      = 'pi3/class.tx_vinatura_pi3.php';
    
    // The extension key
    var $extKey             = 'vinatura';
    
    // Check plugin hash
    var $pi_checkCHash      = FALSE;
    
    // Upload directory
    var $uploadDir          = 'uploads/tx_vinatura/';
    
    // Version of the Developer API required
    var $apimacmade_version = 2.8;
    
    // Database tables
    var $extTables = array(
        'users'       => 'fe_users',
        'wines'       => 'tx_vinatura_wines',
        'winetypes'   => 'tx_vinatura_winetypes',
        'profiles'    => 'tx_vinatura_profiles',
    );
    
    // Swiss states
    var $states = array(
        'AG',
        'AI',
        'AR',
        'BE',
        'BL',
        'BS',
        'FR',
        'GE',
        'GL',
        'GR',
        'JU',
        'LU',
        'NE',
        'NW',
        'OW',
        'SG',
        'SH',
        'SO',
        'SZ',
        'TG',
        'TI',
        'UR',
        'VD',
        'VS',
        'ZG',
        'ZH'
    );
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin "tx_femp3player_pi3", and
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
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Get FE user
        $this->getFeUser();
            
        // Get POST data
        $this->fedata = t3lib_div::_POST( 'fedata' );
        
        // Check user
        if ( isset( $this->user ) ) {
            
            // Check template file (TS or Flex)
            $templateFile = ( $this->pi_getFFvalue( $this->piFlexForm, 'template_file', 'sTMPL' ) == '' ) ? $this->conf[ 'templateFile' ] : $this->uploadDir . $this->conf[ 'templateFile' ];
            
            // Template load and init
            $this->api->fe_initTemplate( $templateFile );
            
            // Content
            $content = $this->editProfile();
            
            // Return content
            return $this->pi_wrapInBaseClass( $content );
        }
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
            'storagePid'   => 'sDEF:pages',
            'usergroup'    => 'sDEF:usergroup',
            'templateFile' => 'sTMPL:template_file',
            'links.'       => array(
                'members'    => 'sLINK:members',
                'membersAlt' => 'sLINK:members_alt',
                'login' => 'sLINK:login',
            ),
            'profile.'     => array(
                'requiredFields' => 'sFIELDS:required',
            )
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex( $flex2conf, $this->conf, $this->piFlexForm );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'Weleda Baby PI1: configuration array');
    }
    
    /**
     * 
     */
    function getFeUser()
    {
        
        // Check for a login
        if ( $GLOBALS[ 'TSFE' ]->loginUser ) {
            
            // Check group
            if ( $GLOBALS[ 'TSFE' ]->fe_user->user[ 'usergroup' ] == $this->conf[ 'usergroup' ] ) {
                
                // Store FE user
                $this->user = $GLOBALS[ 'TSFE' ]->fe_user->user;
                
                // Get profile
                $this->profile = $this->getProfile( $this->user[ 'uid' ] );
            }
        }
    }
    
    /**
     * 
     */
    function getProfile( $uid )
    {
        
        // Select profile
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( '*', $this->extTables[ 'profiles' ], 'deleted=0 AND feuser=' . $uid );
        
        // Check ressource
        if ( $res ) {
            
            // Get profile
            return $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res );
        }
    }
    
    /**
     * 
     */
    function editProfile()
    {
        
        // Template markers
        $templateMarkers = array();
        
        // Check for plugin variables
        if( array_key_exists( 'edit', $this->piVars ) && $this->piVars[ 'edit' ] != 'profile' ) {
            
            // Check edit variable
            switch( $this->piVars[ 'edit' ] ) {
                
                // Description
                case 'description':
                    $edit = $this->sectionDescription();
                break;
                
                // Babies
                case 'wines':
                    $edit = $this->sectionWines();
                break;
            }
            
        } else {
            
            // Default edit form
            $edit = $this->sectionProfile();
        }
        
        // Replace markers
        $templateMarkers[ '###OPTIONS###' ] = $this->userOptions();
        $templateMarkers[ '###EDIT###' ]    = $edit;
        
        // Wrap all in a CSS element
        return $this->api->fe_renderTemplate( $templateMarkers, '###HOME###' );
    }
    
    /**
     * 
     */
    function userOptions()
    {
        
        // Storage
        $htmlCode = array();
        
        // Header
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'options-header', $this->pi_getLL( 'options.header' ) );
        
        // Show options
        $htmlCode[] = $this->showOptions();
        
        // Edit options
        $htmlCode[] = $this->editOptions();
        
        // Return options
        return $this->api->fe_makeStyledContent( 'div', 'options', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function showOptions()
    {
        
        // Storage
        $htmlCode = array();
        
        // Show features
        $show = array( 'profile' );
        
        // URL parameters
        $urlParams = array(
            
            // Profile
            'profile' => array( 'tx_vinatura_pi1[showUid]' => $this->user[ 'uid' ] ),
        );
        
        // Page ID
        $pid = $this->conf[ 'links.' ][ 'members' ];
        
        // Process options
        foreach( $show as $option ) {
            
            // Create link
            $link = $this->pi_linkTP( $this->pi_getLL( 'show.' . $option ), $urlParams[ $option ], 1, $pid );
            
            // Display link
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'show-' . $option, $link );
        }
        
        // Return options
        return $this->api->fe_makeStyledContent( 'div', 'options-show', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function editOptions()
    {
        
        // Storage
        $htmlCode = array();
        
        // Edit features
        $edit = array( 'profile', 'description' );
        
        // Add wines list if enabled
        if( isset( $this->conf[ 'enableWineList' ] ) && $this->conf[ 'enableWineList' ] == 1 ) {
            
            $edit[] = 'wines';
        }
        
        // URL parameters
        $urlParams = array(
            
            // Profile
            'profile'     => array( 'tx_vinatura_pi3[edit]' => 'profile' ),
            
            // Description
            'description' => array( 'tx_vinatura_pi3[edit]' => 'description' ),
            
            // Babies
            'wines'      => array( 'tx_vinatura_pi3[edit]' => 'wines' ),
        );
        
        // Page ID
        $pid = $GLOBALS[ 'TSFE' ]->id;
        
        // Process options
        foreach( $edit as $option ) {
            
            // Create link
            $link = $this->pi_linkTP( $this->pi_getLL( 'edit.' . $option ), $urlParams[ $option ], 1, $pid );
            
            // Display link
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'edit-' . $option, $link );
        }
        
        // Logout
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'edit-logout', $this->pi_linkTP( $this->pi_getLL( 'edit.logout' ), array( 'logintype' => 'logout' ), 0, $this->conf[ 'links.' ][ 'login' ] ) );
        
        // Return options
        return $this->api->fe_makeStyledContent( 'div', 'options-edit', implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function sectionProfile() {
        
        // Field list
        $fieldTypes = array(
            'password'  => 'password',
            'name'      => 'input',
            'firstname' => 'input',
            'image'     => 'image',
            'address'   => 'text',
            'zip'       => 'input',
            'city'      => 'input',
            'country'   => 'input',
            'email'     => 'input',
            'www'       => 'input',
            'telephone' => 'input',
            'fax'       => 'input',
            'title'     => 'input',
            'state'     => 'select-states',
            'domain'    => 'input',
            'cellular'  => 'input',
            'member'    => 'input',
        );
        
        $fields = explode( ',', $this->conf[ 'profile.' ][ 'fieldList' ] );
        
        // Required fields
        $this->requiredFields = explode( ',', $this->conf[ 'profile.' ][ 'requiredFields' ] );
        
        // Check for mandatory field 'password'
        if ( !in_array( 'password', $this->requiredFields ) ) {
            
            // Add field
            $this->requiredFields[] = 'password';
        }
        
        // Template markers
        $templateMarkers = array();
        
        // Empty confirmation
        $templateMarkers[ '###CONFIRM###' ] = '';
        
        // Check for FE data
        if ( array_key_exists( 'submit', $this->piVars ) && count( $this->fedata[ $this->prefixId ] ) ) {
            
            // Get image
            $this->getImageField( 'image' );
            
            // Get errors
            $this->checkProfileErrors();
            
            // Check for errors
            if ( !count( $this->errors) ) {
                
                // Data array
                $data = array(
                    'name'      => $this->fedata[ $this->prefixId ][ 'name' ],
                    'address'   => $this->fedata[ $this->prefixId ][ 'address' ],
                    'zip'       => $this->fedata[ $this->prefixId ][ 'zip' ],
                    'city'      => $this->fedata[ $this->prefixId ][ 'city' ],
                    'email'     => $this->fedata[ $this->prefixId ][ 'email' ],
                    'www'       => $this->fedata[ $this->prefixId ][ 'www' ],
                    'telephone' => $this->fedata[ $this->prefixId ][ 'telephone' ],
                    'fax'       => $this->fedata[ $this->prefixId ][ 'fax' ],
                    'title'     => $this->fedata[ $this->prefixId ][ 'title' ],
                    'tstamp'    => time(),
                );
                
                // Check for a new password
                if ( !empty( $this->fedata[ $this->prefixId ][ 'password' ][ 0 ] ) ) {
                    
                    // Add password
                    $data[ 'password' ] = $this->fedata[ $this->prefixId ][ 'password' ][ 0 ];
                }
                
                // Check for a picture
                if ( array_key_exists( 'image', $this->fedata[ $this->prefixId ] ) ) {
                    
                    // Upload as temp file
                    $tmp = t3lib_div::upload_to_tempfile( $this->fedata[ $this->prefixId ][ 'image' ][ 'tmp_name' ] );
                    
                    // Storage directory (absolute)
                    $storage = t3lib_div::getFileAbsFileName( 'uploads/pics/' );
                    
                    // File extension
                    $ext = ( strrpos( $this->fedata[ $this->prefixId ][ 'image' ][ 'name' ], '.' ) ) ? substr( $this->fedata[ $this->prefixId ][ 'image' ][ 'name' ], strrpos( $this->fedata[ $this->prefixId ][ 'image' ][ 'name' ], '.' ), strlen( $this->fedata[ $this->prefixId ][ 'image' ][ 'name' ] ) ) : false;
                    
                    // File name
                    $fName = uniqid( rand() ) . $ext;
                    
                    // Move file to final destination
                    t3lib_div::upload_copy_move( $tmp, $storage . $fName );
                        
                    // Set reference
                    $data[ 'image' ] = $fName;
                    
                    // Delete temp file
                    t3lib_div::unlink_tempfile( $tmp );
                    
                    // Delete old file
                    @unlink( t3lib_div::getFileAbsFileName( 'uploads/pics/' . $this->user[ 'image' ] ) );
                }
                
                // Update user
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'users' ],'uid=' . $this->user[ 'uid' ], $data );
                
                // Profile
                $profile = array(
                    'firstname' => $this->fedata[ $this->prefixId ][ 'firstname' ],
                    'state'     => $this->fedata[ $this->prefixId ][ 'state' ],
                    'domain'    => $this->fedata[ $this->prefixId ][ 'domain' ],
                    'member'    => $this->fedata[ $this->prefixId ][ 'member' ],
                    'cellular'  => $this->fedata[ $this->prefixId ][ 'cellular' ]
                );
                
                // Update profile
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'profiles' ], 'uid=' . $this->profile[ 'uid' ], $profile );
                
                // Notify
                $this->emailNotify();
                
                // Clear members page cache
                $this->clearPageCache( $this->conf[ 'links.' ][ 'members' ] );
                $this->clearPageCache( $this->conf[ 'links.' ][ 'membersAlt' ] );
                
                // Get user
                $user = $this->pi_getRecord( $this->extTables[ 'users' ], $this->user[ 'uid' ] );
                
                // Update user
                $this->user = $user;
                
                // Update profile
                $this->profile[ 'state' ]     = $this->fedata[ $this->prefixId ][ 'state' ];
                $this->profile[ 'firstname' ] = $this->fedata[ $this->prefixId ][ 'firstname' ];
                $this->profile[ 'domain' ]    = $this->fedata[ $this->prefixId ][ 'domain' ];
                $this->profile[ 'cellular' ]  = $this->fedata[ $this->prefixId ][ 'cellular' ];
                $this->profile[ 'member' ]    = $this->fedata[ $this->prefixId ][ 'member' ];
                
                // Confirmation
                $templateMarkers[ '###CONFIRM###' ] = $this->api->fe_makeStyledContent( 'div', 'confirm', $this->pi_getLL( 'confirm' ) );
            }
        }
        
        // Replace markers
        $templateMarkers[ '###HEADER###' ] = $this->api->fe_makeStyledContent( 'div', 'section-header', $this->pi_getLL( 'section.profile' ) );
        
        // Process each field
        foreach( $fields as $value ) {
            
            // Field value
            $fieldValue = ( $value == 'state' || $value == 'firstname' || $value == 'domain' || $value == 'member' || $value == 'cellular' ) ? $this->profile[ $value ] : $this->user[ $value ];
            
            // Overwriting template markers
            $templateMarkers[ '###' . strtoupper( $value ) . '###' ] = $this->writeInput( $value, $fieldValue, $this->conf[ 'profile.' ], $fieldTypes[ $value ] );
        }
        
        // Submit
        $templateMarkers[ '###SUBMIT###' ] = $this->api->fe_makeStyledContent( 'div', 'field', '<input type="submit" id="submit" name="submit" value="' . $this->pi_getLL( 'submit.modify' ) . '" />' );
        
        // Wrap all in a form
        $content = $this->api->fe_makeStyledContent(
            'form',
            'inputForm',
            $this->api->fe_renderTemplate( $templateMarkers, '###PROFILE###' ),
            1,
            0,
            0,
            array(
                'action'  => $this->pi_linkTP_keepPIvars_url( array( 'submit' => '1' ) ),
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                'method'=>'post'
            )
        );
        
        // Return content wrapped in a CSS element
        return $this->api->fe_makeStyledContent( 'div', 'profile', $content );
    }
    
    /**
     * 
     */
    function checkProfileErrors()
    {
        
        // Storage
        $this->errors = array();
        
        // Check required fields
        foreach( $this->requiredFields as $req ) {
            
            // Check FE data
            if ( $req && ( !array_key_exists( $req, $this->fedata[ $this->prefixId ] ) || empty( $this->fedata[ $this->prefixId ][ $req ] ) ) ) {
                
                // Error
                $this->errors[ $req ] = $this->pi_getLL( 'errors.missing' );
            }
        }
        
        // Special check for email
        if ( !array_key_exists( 'email', $this->errors ) && !empty( $this->fedata[ $this->prefixId ][ 'email' ] ) ) {
            
            // Check for a real email address
            if ( !t3lib_div::validEmail( $this->fedata[ $this->prefixId ][ 'email' ] ) ) {
                
                // Error
                $this->errors[ 'email' ] = $this->pi_getLL( 'errors.email' );
            }
        }
        
        // Special check for password
        if ( !array_key_exists( 'password', $this->errors ) ) {
            
            // Password field
            $pwd = $this->fedata[ $this->prefixId ][ 'password' ];
            
            // Check for same passwords
            if ( ( !empty( $pwd[ 0 ] ) && !empty( $pwd[ 1 ] ) ) && ( $pwd[ 0 ] != $pwd[ 1 ] ) ) {
                
                // Error
                $this->errors[ 'password' ] = $this->pi_getLL( 'errors.password' );
            }
        }
        
        // Special check for picture
        if ( !array_key_exists( 'image', $this->errors ) && array_key_exists( 'image', $this->fedata[ $this->prefixId ] ) ) {
            
            // Load TCA for users
            t3lib_div::loadTCA( $this->extTables[ 'users' ] );
            
            // Field settings
            $conf = $GLOBALS[ 'TCA' ][ 'fe_users' ][ 'columns' ][ 'image' ];
            
            // Image name
            $pic = $this->fedata[ $this->prefixId ][ 'image' ][ 'name' ];
            
            // File extension
            $ext = ( strrpos( $pic, '.' ) ) ? substr( $pic, strrpos( $pic, '.' ) + 1, strlen( $pic ) ) : false;
            
            // Allowed extensions
            $allowedExt = explode( ',', $conf[ 'config' ][ 'allowed' ] );
            
            // Maximum size
            $maxSize = $conf[ 'config' ][ 'max_size' ];
            
            // Check image
            if ( !$ext || !in_array( strtolower( $ext ), $allowedExt ) || !t3lib_div::verifyFilenameAgainstDenyPattern( $pic ) ) {
                
                // Wrong type
                $this->errors[ 'image' ] = $this->pi_getLL( 'errors.image.type' );
                
            } else if ( ( $this->fedata[ $this->prefixId ][ 'image' ][ 'size' ] / 1024) > $maxSize ) {
                
                // Wrong size
                $this->errors[ 'image' ] = $this->pi_getLL( 'errors.image.size' );
            }
        }
    }
    
    /**
     * 
     */
    function sectionDescription()
    {
        
        // Required fields
        $this->requiredFields = explode( ',', $this->conf[ 'description.' ][ 'requiredFields' ] );
        
        // Template markers
        $templateMarkers = array();
        
        // Empty confirmation
        $templateMarkers[ '###CONFIRM###' ] = '';
        
        // Check for FE data
        if ( array_key_exists( 'submit', $this->piVars ) && count( $this->fedata[ $this->prefixId ] ) ) {
            
            // Storage
            $this->errors = array();
            
            // Check required fields
            foreach( $this->requiredFields as $req ) {
                
                // Check FE data
                if ( $req && ( !array_key_exists( $req, $this->fedata[ $this->prefixId ]) || empty( $this->fedata[ $this->prefixId ][ $req ] ) ) ) {
                    
                    // Error
                    $this->errors[ $req ] = $this->pi_getLL( 'errors.missing' );
                }
            }
            
            // Check for errors
            if ( !count( $this->errors ) ) {
                
                // Wines
                $redWines   = ( is_array( $this->fedata[ $this->prefixId ][ 'redwines' ] ) )   ? implode( ',', $this->fedata[ $this->prefixId ][ 'redwines' ] )   : '';
                $whiteWines = ( is_array( $this->fedata[ $this->prefixId ][ 'whitewines' ] ) ) ? implode( ',', $this->fedata[ $this->prefixId ][ 'whitewines' ] ) : '';
                
                // Data
                $data = array(
                    'description'  => $this->fedata[ $this->prefixId ][ 'description' ],
                    'prices'       => $this->fedata[ $this->prefixId ][ 'prices' ],
                    'surface'      => $this->fedata[ $this->prefixId ][ 'surface' ],
                    'distribution' => $this->fedata[ $this->prefixId ][ 'distribution' ],
                    'restaurants'  => $this->fedata[ $this->prefixId ][ 'restaurants' ],
                    'events'       => $this->fedata[ $this->prefixId ][ 'events' ],
                    'redwines'     => $redWines,
                    'whitewines'   => $whiteWines,
                    'tstamp'       => time(),
                );
                
                // Update description
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->extTables[ 'profiles' ], 'uid=' . $this->profile[ 'uid' ], $data );
                
                // Notify
                $this->emailNotify();
                
                // Update profile array
                $this->profile[ 'description' ]  = $data[ 'description' ];
                $this->profile[ 'redwines' ]     = $data[ 'redwines' ];
                $this->profile[ 'whitewines' ]   = $data[ 'whitewines' ];
                $this->profile[ 'prices' ]       = $data[ 'prices' ];
                $this->profile[ 'surface' ]      = $data[ 'surface' ];
                $this->profile[ 'distribution' ] = $data[ 'distribution' ];
                $this->profile[ 'restaurants' ]  = $data[ 'restaurants' ];
                $this->profile[ 'events' ]       = $data[ 'events' ];
                
                // Clear members page cache
                $this->clearPageCache( $this->conf[ 'links.' ][ 'members' ] );
                $this->clearPageCache( $this->conf[ 'links.' ][ 'membersAlt' ] );
                
                // Confirmation
                $templateMarkers[ '###CONFIRM###' ] = $this->api->fe_makeStyledContent( 'div', 'confirm', $this->pi_getLL( 'confirm' ) );
            }
        }
        
        // Replace markers
        $templateMarkers[ '###HEADER###' ]       = $this->api->fe_makeStyledContent( 'div', 'section-header', $this->pi_getLL( 'section.description' ) );
        $templateMarkers[ '###TEXT###' ]         = $this->writeInput( 'description', $this->profile[ 'description' ], $this->conf[ 'description.' ], 'text' );
        $templateMarkers[ '###RESTAURANTS###' ]  = $this->writeInput( 'restaurants', $this->profile[ 'restaurants' ], $this->conf[ 'description.' ], 'text' );
        $templateMarkers[ '###EVENTS###' ]       = $this->writeInput( 'events', $this->profile[ 'events' ], $this->conf[ 'description.' ], 'text' );
        $templateMarkers[ '###DISTRIBUTION###' ] = $this->writeInput( 'distribution', $this->profile[ 'distribution' ], $this->conf[ 'description.' ], 'text' );
        $templateMarkers[ '###PRICES###' ]       = $this->writeInput( 'prices', $this->profile[ 'prices' ], $this->conf[ 'description.' ], 'input' );
        $templateMarkers[ '###SURFACE###' ]      = $this->writeInput( 'surface', $this->profile[ 'surface' ], $this->conf[ 'description.' ], 'input' );
        $templateMarkers[ '###REDWINES###' ]     = $this->writeInput( 'redwines', $this->profile[ 'redwines' ], $this->conf[ 'description.' ], 'select-winetypes-red-multiple' );
        $templateMarkers[ '###WHITEWINES###' ]   = $this->writeInput( 'whitewines', $this->profile[ 'whitewines' ], $this->conf[ 'description.' ], 'select-winetypes-white-multiple' );
        $templateMarkers[ '###SUBMIT###' ]       = $this->api->fe_makeStyledContent( 'div', 'field', '<input type="submit" id="submit" name="submit" value="' . $this->pi_getLL( 'submit.modify' ) . '" />' );
        
        // Wrap all in a form
        $content = $this->api->fe_makeStyledContent(
            'form',
            'inputForm',
            $this->api->fe_renderTemplate( $templateMarkers, '###DESCRIPTION###' ),
            1,
            0,
            0,
            array(
                'action'  => $this->pi_linkTP_keepPIvars_url( array( 'submit' => '1' ) ),
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                'method'  => 'post'
            )
        );
        
        // Return content wrapped in a CSS element
        return $this->api->fe_makeStyledContent( 'div', 'description', $content );
    }
    
    /**
     * 
     */
    function sectionWines()
    {
        // Required fields
        $this->requiredFields = explode( ',', $this->conf[ 'wines.' ][ 'requiredFields' ] );
        
        // Check for mandatory field 'title'
        if ( !in_array( 'title', $this->requiredFields ) ) {
            
            // Add field
            $this->requiredFields[] = 'title';
        }
        
        // Template markers
        $templateMarkers = array();
        
        // Empty form
        $form = '';
        
        // Delete picture
        if ( array_key_exists( 'delete', $this->piVars ) ) {
            
            // Set delete flag
            $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                $this->extTables[ 'wines' ],
                'feuser=' . $this->user[ 'uid' ] . ' AND uid=' . $this->piVars[ 'delete' ],
                array(
                    'deleted' => 1,
                    'tstamp'  => time()
                )
            );
            
            // Notify
            $this->emailNotify();
            
            // Clear members page cache
            $this->clearPageCache($this->conf[ 'links.' ][ 'members' ] );
            $this->clearPageCache($this->conf[ 'links.' ][ 'membersAlt' ] );
            
            // Confirmation
            $form = $this->api->fe_makeStyledContent( 'div', 'confirm', $this->pi_getLL( 'confirm' ) );
            
        } elseif ( array_key_exists( 'modify', $this->piVars ) || array_key_exists( 'new', $this->piVars ) ) {
            
            // Add form
            $form = $this->formWines();
        }
        
        // Replace markers
        $templateMarkers[ '###HEADER###' ]  = $this->api->fe_makeStyledContent( 'div', 'section-header', $this->pi_getLL( 'section.wines' ) );
        $templateMarkers[ '###WARNING###' ] = $this->api->fe_makeStyledContent( 'div', 'section-warning', $this->pi_getLL( 'wines.warning' ) );
        $templateMarkers[ '###LIST###' ]    = $this->listWines();
        $templateMarkers[ '###NEW###' ]     = $this->api->fe_makeStyledContent( 'div', 'new', $this->api->fe_linkTP_unsetPIvars( $this->pi_getLL( 'new.wine' ), array( 'new' => 1 ), array( 'modify', 'delete', 'submit' ), 1 ) );
        $templateMarkers[ '###FORM###' ]    = $form;
        
        // Wrap all in a form
        $content = $this->api->fe_makeStyledContent(
            'form',
            'inputForm',
            $this->api->fe_renderTemplate( $templateMarkers, '###WINES###' ),
            1,
            0,
            0,
            array(
                'action'  => $this->pi_linkTP_keepPIvars_url( array( 'submit' => '1' ) ),
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                'method'  => 'post'
            )
        );
        
        // Return content wrapped in a CSS element
        return $this->api->fe_makeStyledContent( 'div', 'wines', $content );
    }
    
    /**
     * 
     */
    function formWines() {
        
        // Storage
        $this->errors = array();
        
        // Check for FE data
        if ( array_key_exists( 'submit', $this->piVars ) && count( $this->fedata[ $this->prefixId ] ) ) {
            
            // Get errors
            $this->checkWinesErrors();
        }
            
        // Empty array
        $values = array(
            'title'       => '',
            'type'        => '',
            'description' => ''
        );
        
        // Check for edit flag
        if ( array_key_exists( 'modify', $this->piVars ) ) {
            
            // Select record
            $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                '*',
                $this->extTables[ 'wines' ],
                'uid=' . $this->piVars[ 'modify' ] . ' AND feuser=' . $this->user[ 'uid' ]
            );
            
            // Get record
            $wine = ( $res && $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) ) ? $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) : false;
            
            // Check for article
            if ( is_array( $wine ) ) {
                
                // Set values
                $values[ 'title' ]       = $wine[ 'title' ];
                $values[ 'type' ]        = $wine[ 'type' ];
                $values[ 'description' ] = $wine[ 'description' ];
            }
        }
        
        // Check for errors
        if ( array_key_exists( 'submit', $this->piVars ) && !count( $this->errors ) ) {
            
            // Now
            $now = time();
            
            // Data array
            $data = array(
                'title'       => $this->fedata[ $this->prefixId ][ 'title' ],
                'type'        => $this->fedata[ $this->prefixId ][ 'type' ],
                'description' => $this->fedata[ $this->prefixId ][ 'description' ],
                'tstamp'      => $now,
            );
            
            // Check for action
            if ( array_key_exists( 'modify', $this->piVars ) ) {
                
                // Update record
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                    $this->extTables[ 'wines' ],
                    'feuser=' . $this->user[ 'uid' ] . ' AND uid=' . $this->piVars[ 'modify' ],
                    $data
                );
                
            } elseif( array_key_exists( 'new', $this->piVars ) ) {
                
                // Add special fields
                $data[ 'crdate' ] = $now;
                $data[ 'pid' ]    = $this->conf[ 'storagePid' ];
                $data[ 'feuser' ] = $this->user[ 'uid' ];
                
                // New record
                $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( $this->extTables[ 'wines' ], $data );
            }
            
            // Notify
            $this->emailNotify();
            
            // Clear members page cache
            $this->clearPageCache( $this->conf[ 'links.' ][ 'members' ] );
            $this->clearPageCache( $this->conf[ 'links.' ][ 'membersAlt' ] );
            
            // Confirmation
            return $this->api->fe_makeStyledContent( 'div', 'confirm', $this->pi_getLL( 'confirm' ) );
            
        } else {
            
            // Submit label
            $submitLabel = ( array_key_exists( 'modify', $this->piVars ) ) ? $this->pi_getLL( 'submit.modify' ) : $this->pi_getLL( 'submit.new' );
            
            // Fields
            $fields = array(
                'title'       => 'input',
                'type'        => 'select-winetype-single',
                'description' => 'text',
            );
            
            // Template markers
            $templateMarkers = array();
            
            // Process each field
            foreach( $fields as $key => $value ) {
                
                // Overwriting template markers
                $templateMarkers[ '###' . strtoupper( $key ) . '###' ] = $this->writeInput( $key, $values[ $key ], $this->conf[ 'wines.' ], $value );
            }
            
            // Submit
            $templateMarkers[ '###SUBMIT###' ] = $this->api->fe_makeStyledContent( 'div', 'field', '<input type="submit" id="submit" name="submit" value="' . $submitLabel. '" />');
            
            // Wrap all in a form
            $content = $this->api->fe_makeStyledContent(
                'form',
                'inputForm',
                $this->api->fe_renderTemplate( $templateMarkers, '###WINES_FORM###' ),
                1,
                0,
                0,
                array(
                    'action'  => $this->pi_linkTP_keepPIvars_url( array( 'submit' => '1' ) ),
                    'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                    'method'  => 'post'
                )
            );
            
            // Return content
            return $this->api->fe_makeStyledContent( 'div', 'wine-form', $content );
        }
    }
    
    /**
     * 
     */
    function checkWinesErrors()
    {
        
        // Check required fields
        foreach( $this->requiredFields as $req ) {
            
            // Check FE data
            if ( $req && ( !array_key_exists( $req, $this->fedata[ $this->prefixId ] ) || ( empty( $this->fedata[ $this->prefixId ][ $req ] ) && $this->fedata[ $this->prefixId ][ $req ] !== '0' ) ) ) {
                
                // Error
                $this->errors[ $req ] = $this->pi_getLL( 'errors.missing' );
            }
        }
    }
    
    /**
     * 
     */
    function listWines()
    {
        
        // Get wines
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( '*', $this->extTables[ 'wines' ], 'deleted=0 AND feuser=' . $this->user[ 'uid' ] );
        
        // Check ressource
        if( $res ) {
            
            // Check for wines
            if( $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) ) {
                
                // Storage
                $htmlCode = array();
                
                // Process wines
                while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                    
                    // Storage
                    $wine = array();
                    
                    // Start DIV
                    $wine[] = $this->api->fe_makeStyledContent(
                        'div',
                        'infos',
                        false,
                        1,
                        0,
                        1
                     );
                    
                    // Wine type
                    $type =  $this->pi_getRecord( $this->extTables[ 'winetypes' ], $row[ 'type' ] );
                    $type =& $this->recordL10N( $this->extTables[ 'winetypes' ], $type );
                    
                    // Title
                    $wine[] = $this->api->fe_makeStyledContent(
                        'div',
                        'title',
                        $row[ 'title' ] . ' (' . $type[ 'title' ] . ')'
                    );
                    
                    // Options
                    $options = array(
                        
                        // Edit
                        $this->api->fe_makeStyledContent(
                            'span',
                            'modify',
                            $this->api->fe_linkTP_unsetPIvars(
                                $this->pi_getLL( 'edit' ),
                                array( 'modify' => $row[ 'uid' ] ),
                                array( 'new', 'delete', 'submit' ),
                                1
                            )
                        ),
                        
                        // Delete
                        $this->api->fe_makeStyledContent(
                            'span',
                            'delete',
                            $this->api->fe_linkTP_unsetPIvars(
                                $this->pi_getLL( 'delete' ),
                                array( 'delete' => $row['uid'] ),
                                array( 'new', 'modify', 'submit' ),
                                1
                            )
                        ),
                    );
                    
                    // Add options
                    $wine[] = $this->api->fe_makeStyledContent( 'div', 'options', implode( chr( 10 ), $options ) );
                    
                    // End div
                    $wine[] = '</div>';
                    
                    // Add wine
                    $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'wine', implode( chr( 10 ), $wine ) );
                }
                
                // Return list
                return $this->api->fe_makeStyledContent( 'div', 'wines-list', implode( chr( 10 ), $htmlCode ) );
            }
        }
    }
    
    /**
     * 
     */
    function writeInput( $fieldName, $value, $conf, $type = false )
    {
        // Input ID
        $id = 'fedata[' . $this->prefixId . '][' . $fieldName . ']';
        
        // Label
        $label = $this->getFieldLabel( $fieldName );
        
        // Get error if any
        $error = $this->getFieldError( $fieldName );
        
        // Field type
        $fieldType = explode( ':', $type );
        
        // Value
        if ( $fieldType[ 0 ] != 'image' && isset( $this->fedata ) && array_key_exists( $this->prefixId, $this->fedata ) && array_key_exists( $fieldName, $this->fedata[ $this->prefixId ] ) ) {
            
            // Get value
            $value = $this->fedata[ $this->prefixId ][ $fieldName ];
        }
        
        // Check field name
        switch( $fieldType[ 0 ] ) {
            
            // Address
            case 'text':
                
                // Textarea
                $input = $this->api->fe_makeStyledContent(
                    'div',
                    'field',
                    '<textarea id="'
                  . $id
                  . '" name="'
                  . $id
                  . '" rows="'
                  . $conf[ 'textareaRows' ]
                  . '" cols="'
                  . $conf[ 'textareaCols' ]
                  . '">'
                  . $value
                  . '</textarea>'
                );
            break;
            
            // Address
            case 'image':
                
                // Upload directory
                $uploadDir = ( $fieldName == 'image' ) ? 'uploads/pics/' : $this->uploadDir;
                
                // Create thumbnail
                $img = ( !empty( $value ) ) ? $this->api->fe_makeStyledContent( 'div', 'picture', $this->api->fe_createImageObjects( $value, $conf[ 'imgConf.' ], $uploadDir ) ) : '';
                
                // File
                $input = $this->api->fe_makeStyledContent(
                    'div',
                    'field',
                    $img
                  . '<input type="file" id="'
                  . $id
                  . '" name="'
                  . $id
                  . '" />'
                );
            break;
            
            // Password
            case 'password':
                
                // Password input
                $input = $this->api->fe_makeStyledContent(
                    'div',
                    'field',
                    '<input type="password" id="'
                  . $id
                  . '[0]" name="'
                  . $id . '[0]" size="'
                  . $conf[ 'inputSize' ]
                  . '" /><br /><input type="password" id="'
                  . $id
                  . '[1]" name="'
                  . $id
                  . '[1]" size="'
                  . $conf[ 'inputSize' ]
                  . '" />'
                );
            break;
            
            // States
            case 'select-states':
                
                // Storage
                $select = array();
                
                // Start select
                $select[] = '<select id="' . $id . '" name="' . $id . '">';
                
                // Process states
                foreach( $this->states as $state ) {
                    
                    // Selected state
                    $selected = ( $value == $state ) ? ' selected' : '';
                    
                    // Add option
                    $select[] = '<option value="' . $state . '"' . $selected . '>' . $this->pi_getLL( 'state.' . $state ) . '</option>';
                }
                
                // End select
                $select[] = '</select>';
                
                // Complete code
                $input = implode( chr( 10 ), $select );
            break;
            
            // Multiple winetypes
            case 'select-winetypes-red-multiple':
            case 'select-winetypes-white-multiple':
                
                // Multiple value
                $id .= '[]';
                
                // Storage
                $select = array();
                
                // Start select
                $select[] = '<select id="' . $id . '" name="' . $id . '" multiple size="5">';
                
                $type = ( $fieldType[ 0 ] == 'select-winetypes-white-multiple' ) ? 0 : 1;
                
                // Selected items
                $selectedItems = ( is_array( $value ) ) ? $value : explode( ',', $value );
                
                // Select wine types
                $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( '*', $this->extTables[ 'winetypes' ], 'type=' . $type . ' AND sys_language_uid=0 AND pid=' . $this->conf[ 'storagePid' ] . $this->cObj->enableFields( $this->extTables[ 'winetypes' ] ) );
                
                // Process states
                while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                    
                    // Selected state
                    $selected = ( in_array( $row[ 'uid' ], $selectedItems ) ) ? ' selected' : '';
                    
                    // Get localized row
                    $row =& $this->recordL10N( $this->extTables[ 'winetypes' ], $row );
                    
                    // Add option
                    $select[] = '<option value="' . $row[ 'uid' ] . '"' . $selected . '>' . $row[ 'title' ] . '</option>';
                }
                
                // End select
                $select[] = '</select>';
                
                // Complete code
                $input = implode( chr( 10 ), $select );
            break;
            
            // Single winetype
            case 'select-winetype-single':
                
                // Storage
                $select = array();
                
                // Start select
                $select[] = '<select id="' . $id . '" name="' . $id . '">';
                
                // Select wine types
                $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                    '*',
                    $this->extTables[ 'winetypes' ],
                    'sys_language_uid=0 AND pid=' . $this->conf[ 'storagePid' ] . $this->cObj->enableFields( $this->extTables[ 'winetypes' ] ),
                    '',
                    'type'
                );
                
                // Process states
                while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                    
                    // Selected state
                    $selected = ( $value == $row[ 'uid' ] ) ? ' selected' : '';
                    
                    // Get localized row
                    $row =& $this->recordL10N( $this->extTables[ 'winetypes' ], $row );
                    
                    // Add option
                    $select[] = '<option value="' . $row[ 'uid' ] . '"' . $selected . '>' . $row[ 'title' ] . ' (' . $this->pi_getLL( 'winetype.' . $row[ 'type' ] ) . ')' . '</option>';
                }
                
                // End select
                $select[] = '</select>';
                
                // Complete code
                $input = implode( chr( 10 ), $select );
                
            break;
            
            // Default
            default:
                
                // Text input
                $input = $this->api->fe_makeStyledContent(
                    'div',
                    'field',
                    '<input type="text" id="'
                  . $id
                  . '" name="'
                  . $id
                  . '" value="'
                  . $value
                  . '" size="'
                  . $conf[ 'inputSize' ]
                  . '" />'
                );
            break;
        }
        
        // Full field
        return $label . $error . $input;
    }
    
    /**
     * 
     */
    function getFieldLabel( $fieldName )
    {
        // Storage
        $htmlCode = array();
        
        // Label
        $htmlCode[] = '<strong>' . $this->pi_getLL( 'headers.' . $fieldName ) . '</strong>';
        
        // Check if field is required
        if ( in_array( $fieldName, $this->requiredFields ) ) {
            
            // Add marker
            $htmlCode[] = '<span>*</span>';
            
            // Label class
            $class = 'label-required';
            
        } else {
            
            // Label class
            $class = 'label';
        }
        
        // Return label
        return $this->api->fe_makeStyledContent( 'div', $class, implode( chr( 10 ), $htmlCode ) );
    }
    
    /**
     * 
     */
    function getFieldError( $fieldName )
    {    
        if ( is_array( $this->errors ) && array_key_exists( $fieldName, $this->errors ) ) {
            
            // Label
            $msg = '<strong>' . $this->errors[ $fieldName ] . '</strong>';
            
            // Return label
            return $this->api->fe_makeStyledContent( 'div', 'error', $msg );
        }
    }
    
    /**
     * 
     */
    function getImageField( $name )
    {
        
        // Check for incoming picture
        if ( array_key_exists( 'fedata', $_FILES ) ) {
            
            // Get image data
            $tmp = array(
                
                // Name
                'name'     => $_FILES[ 'fedata' ][ 'name' ][ $this->prefixId ][ $name ],
                
                // Type
                'type'     => $_FILES[ 'fedata' ][ 'type' ][ $this->prefixId ][ $name ],
                
                // Temp name
                'tmp_name' => $_FILES[ 'fedata' ][ 'tmp_name' ][ $this->prefixId ][ $name ],
                
                // Size
                'size'     => $_FILES[ 'fedata' ][ 'size' ][ $this->prefixId ][ $name ],
            );
            
            // Check for valid image data
            if ( !empty( $tmp[ 'name' ] ) ) {
                
                // Set FE data
                $this->fedata[ $this->prefixId ][ $name ] = $tmp;
            }
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
    function clearPageCache( $pid )
    {
        
        // Delete page cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pages', 'page_id=' . $pid );
        
        // Delete page section cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pagesection', 'page_id=' . $pid );
    }
    
    /**
     * 
     */
    function emailNotify()
    {
        
        if( isset( $this->conf[ 'notificationEmail' ] ) && isset( $this->conf[ 'notificationEmailFrom' ] ) && $this->conf[ 'notificationEmail' ] && $this->conf[ 'notificationEmailFrom' ] ) {
            
            // Profile URL
            $url = $this->pi_getPageLink(
                $this->conf[ 'links.' ][ 'members' ],
                '',
                array(
                    'tx_vinatura_pi1[showUid]' => $this->user[ 'uid' ]
                )
            );
            
            // Message
            $message = sprintf(
                $this->pi_getLL( 'notifyEmail' ),
                $this->user[ 'username' ],
                t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . $url
            );
            
            // Send email
            $this->cObj->sendNotifyEmail(
                $message,
                $this->conf[ 'notificationEmail' ],
                '',
                $this->conf[ 'notificationEmailFrom' ],
                $this->conf[ 'notificationEmailFrom' ]
            );
        }
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vinatura/pi3/class.tx_vinatura_pi3.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vinatura/pi3/class.tx_vinatura_pi3.php']);
}
?>
