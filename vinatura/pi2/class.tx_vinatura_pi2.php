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

class tx_vinatura_pi2 extends tslib_pibase
{
    // Same as class name
    var $prefixId           = 'tx_vinatura_pi2';
    
    // Path to this script relative to the extension dir.
    var $scriptRelPath      = 'pi2/class.tx_vinatura_pi2.php';
    
    // The extension key
    var $extKey             = 'vinatura';
    
    // Check plugin hash
    var $pi_checkCHash      = FALSE;
    
    // Upload directory
    var $uploadDir          = 'uploads/pics/';
    
    // Version of the Developer API required
    var $apimacmade_version = 2.8;
    
    // Database tables
    var $extTables          = array(
        'users'    => 'fe_users',
        'profiles' => 'tx_vinatura_profiles',
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
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Check for login
        if ( t3lib_div::_POST( 'logintype' ) || $GLOBALS[ 'TSFE' ]->loginUser ) {
            
            // Check for a redirection
            if ( $this->conf[ 'redirect' ] && $GLOBALS[ 'TSFE' ]->loginUser ) {
                
                // Redirect user
                header( 'Location: ' . t3lib_div::locationHeaderUrl( $this->pi_getPageLink( $this->conf[ 'redirect' ] ) ) );
                
            } else {
                
                // Login box
                $content = $this->api->fe_makeStyledContent(
                    'div', 
                    'login-title', 
                    $this->pi_getLL( 'connect' ) )
                  . $this->api->fe_makeStyledContent( 'div', 'login-error', $this->pi_getLL( 'connect.error' ) )
                  . $this->api->fe_makeStyledContent( 'div', 'lobinbox', $this->api->fe_buildLoginBox( $this->conf[ 'storagePid' ] )
                );
                
            }
        } elseif ( empty( $this->conf[ 'agree_text' ] ) || array_key_exists( 'agree', $this->piVars ) ) {
            
            // Check template file (TS or Flex)
            $templateFile = ( $this->pi_getFFvalue( $this->piFlexForm, 'template_file', 'sTMPL' ) == '' ) ? $this->conf[ 'templateFile' ] : $this->uploadDir . $this->conf[ 'templateFile' ];
            
            // Template load and init
            $this->api->fe_initTemplate( $templateFile );
            
            // Required fields
            $this->requiredFields = explode( ',', $this->conf[ 'requiredFields' ] );
            
            // Check for mandatory field 'username'
            if ( !in_array( 'username', $this->requiredFields ) ) {
                
                // Add field
                $this->requiredFields[] = 'username';
            }
            
            // Check for mandatory field 'password'
            if ( !in_array( 'password', $this->requiredFields ) ) {
                
                // Add field
                $this->requiredFields[] = 'password';
            }
            
            // Get POST data
            $this->fedata = t3lib_div::_POST( 'fedata' );
            
            // Check if form has been submitted
            if ( array_key_exists( 'submit', $this->piVars ) && is_array( $this->fedata ) ) {
                
                // Get files
                $this->getImageField();
                
                // Check errors
                $this->checkErrors();
            }
            
            // Content
            $content = ( !isset( $this->errors ) || count( $this->errors ) > 0) ? $this->buildForm() : $this->confirm();
            
        } else {
            
            // Show agreement
            $content = $this->showAgreement();
        }
        
        // Return content
        return $this->pi_wrapInBaseClass( $content );
    }
    
    /**
     * 
     */
    function showAgreement()
    {
        
        // Storage
        $htmlCode = array();
        
        // Title
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'agreement-title', $this->conf[ 'agree_title' ] );
        
        // Text
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'agreement-text', nl2br( $this->conf[ 'agree_text' ] ) );
        
        // Accept link
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'agreement-link', $this->pi_linkTP_keepPIvars( $this->pi_getLL( 'agree.accept' ), array( 'agree' => '1' ) ) );
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
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
            'storagePid'     => 'sDEF:pages',
            'usergroup'      => 'sDEF:usergroup',
            'members'        => 'sDEF:members',
            'membersAlt'     => 'sDEF:members_alt',
            'redirect'       => 'sDEF:redirect',
            'requiredFields' => 'sFIELDS:required',
            'templateFile'   => 'sTMPL:template_file',
            'agree_title'    => 'sAGREE:title',
            'agree_text'     => 'sAGREE:agreement',
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex( $flex2conf, $this->conf, $this->piFlexForm );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'Weleda Baby PI1: configuration array');
    }
    
    /**
     * 
     */
    function getImageField()
    {
        
        // Check for incoming picture
        if ( array_key_exists( 'fedata', $_FILES ) ) {
            
            // Get image data
            $tmp = array(
                
                // Name
                'name'     => $_FILES[ 'fedata' ][ 'name' ][ $this->prefixId ][ 'image' ],
                
                // Type
                'type'     => $_FILES[ 'fedata' ][ 'type' ][ $this->prefixId ][ 'image' ],
                
                // Temp name
                'tmp_name' => $_FILES[ 'fedata' ][ 'tmp_name' ][ $this->prefixId ][ 'image' ],
                
                // Size
                'size'     => $_FILES[ 'fedata' ][ 'size' ][ $this->prefixId ][ 'image' ],
            );
            
            // Check for valid image data
            if ( !empty( $tmp[ 'name' ] ) ) {
                
                // Set FE data
                $this->fedata[ $this->prefixId ][ 'image' ] = $tmp;
            }
        }
    }
    
    /**
     * 
     */
    function buildForm()
    {
        
        // Template markers
        $templateMarkers = array();
        
        // Field list
        $this->fields = explode( ',', $this->conf[ 'fieldList' ] );
        
        // Process each field
        foreach( $this->fields as $fieldName ) {
            
            // Overwriting template markers
            $templateMarkers[ '###' . strtoupper( $fieldName ) . '###' ] = $this->writeInput( $fieldName );
        }
        
        // Additionnal fields
        $templateMarkers[ '###FIRSTNAME###' ] = $this->writeInput( 'firstname' );
        $templateMarkers[ '###MEMBER###' ]    = $this->writeInput( 'member' );
        $templateMarkers[ '###DOMAIN###' ]    = $this->writeInput( 'domain' );
        $templateMarkers[ '###CELLULAR###' ]  = $this->writeInput( 'cellular' );
        
        // Submit
        $templateMarkers[ '###SUBMIT###' ] = $this->api->fe_makeStyledContent( 'div', 'field', '<input type="submit" id="submit" name="submit" value="' . $this->pi_getLL( 'submit' ) . '" />' );
        
        // Wrap all in a CSS element
        $content = $this->api->fe_makeStyledContent(
            'form',
            'inputForm',
            $this->api->fe_renderTemplate($templateMarkers,'###MAIN###'),
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
        return $content;
    }
    
    /**
     * 
     */
    function checkErrors()
    {
        
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
            if ( count( $pwd ) != 2 || empty( $pwd[ 0 ] ) || empty( $pwd[ 1 ] ) ) {
                
                // Error
                $this->errors[ 'password' ] = $this->pi_getLL( 'errors.missing' );
                
            } else {
                
                // Check for same passwords
                if ( $pwd[ 0 ] != $pwd[ 1 ] ) {
                    
                    // Error
                    $this->errors[ 'password' ] = $this->pi_getLL( 'errors.password' );
                }
            }
        }
        
        // Special check for username
        if ( !array_key_exists( 'username', $this->errors ) ) {
            
            // Username field
            $username = $this->fedata[ $this->prefixId ][ 'username' ];
            
            // MySQL WHERE clause
            $whereClause = array(
                
                // Username
                'username="' . $GLOBALS['TYPO3_DB']->quoteStr( $username, $this->extTables[ 'users' ] ) . '"',
                
                // Storage PID
                'pid=' . $this->conf[ 'storagePid' ],
                
                // Delete clause
                'deleted=0',
            );
            
            // Try to get user
            $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery( '*', $this->extTables[ 'users' ], implode( ' AND ', $whereClause ) );
            
            // Check for a real email address
            if ( !$res || $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) > 0) {
                
                // Error
                $this->errors[ 'username' ] = $this->pi_getLL( 'errors.username' );
            }
        }
        
        // Special check for picture
        if ( !array_key_exists( 'image', $this->errors ) && array_key_exists( 'image', $this->fedata[ $this->prefixId] ) ) {
            
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
            if ( !$ext || !in_array( $ext, $allowedExt ) || !t3lib_div::verifyFilenameAgainstDenyPattern( $pic ) ) {
                
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
    function writeInput( $fieldName )
    {
        
        // Input ID
        $id = 'fedata[' . $this->prefixId . '][' . $fieldName . ']';
        
        // Label
        $label = $this->getFieldLabel( $fieldName );
        
        // Get error if any
        $error = $this->getFieldError( $fieldName );
        
        // Value
        if ( isset( $this->fedata ) && array_key_exists( $this->prefixId, $this->fedata ) && array_key_exists( $fieldName, $this->fedata[ $this->prefixId ] ) ) {
            
            // Get value
            $value = $this->fedata[ $this->prefixId ][ $fieldName ];
            
        } else {
            
            // Empty value
            $value = '';
        }
        
        // Check field name
        switch( $fieldName ) {
            
            // Address
            case 'address':
                
                // Textarea
                $input = '<textarea id="'
                       . $id
                       . '" name="'
                       . $id
                       . '" rows="'
                       . $this->conf[ 'textareaRows' ]
                       . '" cols="'
                       . $this->conf[ 'textareaCols' ]
                       . '">'
                       . $value
                       . '</textarea>';
            break;
            
            // State
            case 'state':
                
                // Storage
                $select = array();
                
                // Start select
                $select[] = '<select id="' . $id . '" name="' . $id . '">';
                
                // Process states
                foreach( $this->states as $state ) {
                    
                    // Add option
                    $select[] = '<option value="' . $state . '">' . $this->pi_getLL( 'state.' . $state ) . '</option>';
                }
                
                // End select
                $select[] = '</select>';
                
                // Complete code
                $input = implode( chr( 10 ), $select );
                
            break;
            
            // Address
            case 'image':
                
                // Textarea
                $input = '<input type="file" id="'
                       . $id
                       . '" name="'
                       . $id
                       . '" />';
            break;
            
            // Password
            case 'password':
                
                // Password input
                $input = '<input type="password" id="'
                       . $id
                       . '[0]" name="'
                       . $id
                       . '[0]" size="'
                       . $this->conf[ 'inputSize' ]
                       . '" /><br /><input type="password" id="'
                       . $id
                       . '[1]" name="'
                       . $id
                       . '[1]" size="20" />';
            break;
            
            // Default
            default:
                
                // Text input
                $input = '<input type="text" id="'
                       . $id
                       . '" name="'
                       . $id
                       . '" value="'
                       . $value
                       . '" size="'
                       . $this->conf[ 'inputSize' ]
                       . '" />';
            break;
        }
        
        // Full field
        return $label . $error . $this->api->fe_makeStyledContent( 'div', 'field', $input );
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
    function getFieldLabel( $fieldName ) {
        
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
    function confirm() {
        
        // Insert record
        $uid = $this->insertUser();
        
        // Storage
        $htmlCode = array();
        
        // Get user
        $user = $this->pi_getRecord( $this->extTables[ 'users' ], $uid );
        
        // Title
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'confirm-title', sprintf( $this->pi_getLL( 'register.over' ), $user[ 'username' ] ) );
        
        // Login text
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'confirm-login', $this->pi_getLL( 'register.login' ), $user[ 'username' ] );
        
        // Login box
        $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'confirm-lobinbox', $this->api->fe_buildLoginBox( $this->conf[ 'storagePid' ] ) );
        
        // Return confirmation
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * 
     */
    function insertUser() {
        
        // Now
        $now = time();
        
        // FE data
        $data = $this->fedata[ $this->prefixId ];
        
        // Check for additionnal profile fields
        if ( array_key_exists( 'firstname', $data ) ) {
            
            // Remove birth from user data
            $firstName = $data[ 'firstname' ];
            unset( $data[ 'firstname' ] );
        }
        if ( array_key_exists( 'state', $data ) ) {
            
            // Remove birth from user data
            $state = $data[ 'state' ];
            unset( $data[ 'state' ] );
        }
        if ( array_key_exists( 'domain', $data ) ) {
            
            // Remove birth from user data
            $domain = $data[ 'domain' ];
            unset( $data[ 'domain' ] );
        }
        if ( array_key_exists( 'cellular', $data ) ) {
            
            // Remove birth from user data
            $cellular = $data[ 'cellular' ];
            unset( $data[ 'cellular' ] );
        }
        if ( array_key_exists( 'member', $data ) ) {
            
            // Remove birth from user data
            $member = $data[ 'member' ];
            unset( $data[ 'member' ] );
        }
        
        // Username
        $data[ 'username' ]  = strtolower( str_replace( ' ', '', $data[ 'username' ] ) );
        
        // Password
        $data['password']    = array_pop( $data[ 'password' ] );
        
        // PID
        $data[ 'pid' ]       = $this->conf[ 'storagePid' ];
        
        // Usergroup
        $data[ 'usergroup' ] = $this->conf[ 'usergroup' ];
        
        // Creation date
        $data[ 'crdate' ]    = $now;
        
        // Modification date
        $data[ 'tstamp' ]    = $now;
        
        // Process picture if any
        if ( array_key_exists( 'image', $data ) && is_array( $data[ 'image' ] ) ) {
            
            // Upload as temp file
            $tmp = t3lib_div::upload_to_tempfile( $data[ 'image' ][ 'tmp_name' ] );
            
            // Storage directory (absolute)
            $storage = t3lib_div::getFileAbsFileName( $this->uploadDir );
            
            // File extension
            $ext = ( strrpos( $data[ 'image' ][ 'name' ], '.' ) ) ? substr( $data[ 'image' ][ 'name' ], strrpos( $data[ 'image' ][ 'name' ], '.' ), strlen( $data[ 'image' ][ 'name' ] ) ) : false;
            
            // File name
            $fName = uniqid( rand() ) . $ext;
            
            // Move file to final destination
            t3lib_div::upload_copy_move( $tmp, $storage . $fName );
                
            // Set reference
            $data[ 'image' ] = $fName;
            
            // Delete temp file
            t3lib_div::unlink_tempfile( $tmp );
        }
        
        // Insert record
        $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( $this->extTables[ 'users' ], $data );
        
        // User ID
        $uid = $GLOBALS[ 'TYPO3_DB' ]->sql_insert_id();
        
        // Profile data
        $profile = array(
            'feuser' => $uid,
            'pid'    => $this->conf[ 'storagePid' ],
            'crdate' => $now,
            'tstamp' => $now,
        );
        
        // Add profile fields
        if ( isset( $firstName ) ) {
            
            // Add date
            $profile[ 'firstname' ] = $firstName;
        }
        if ( isset( $state ) ) {
            
            // Add date
            $profile[ 'state' ] = $state;
        }
        if ( isset( $member ) ) {
            
            // Add date
            $profile[ 'member' ] = $member;
        }
        if ( isset( $domain ) ) {
            
            // Add date
            $profile[ 'domain' ] = $domain;
        }
        if ( isset( $cellular ) ) {
            
            // Add date
            $profile[ 'cellular' ] = $cellular;
        }
        
        // Create profile
        $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery( $this->extTables[ 'profiles' ], $profile );
        
        // Clear members page cache
        $this->clearPageCache( $this->conf[ 'members' ] );
        $this->clearPageCache( $this->conf[ 'membersAlt' ] );
        
        // Return user ID
        return $uid;
    }
    
    /**
     * 
     */
    function clearPageCache( $pid ) {
        
        // Delete page cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pages', 'page_id=' . $pid );
        
        // Delete page section cache
        $GLOBALS[ 'TYPO3_DB' ]->exec_DELETEquery( 'cache_pagesection', 'page_id=' . $pid );
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vinatura/pi2/class.tx_vinatura_pi2.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vinatura/pi2/class.tx_vinatura_pi2.php']);
}
?>
