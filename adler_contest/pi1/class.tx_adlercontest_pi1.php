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
 * Plugin 'Registration' for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the TYPO3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Includes the method provider class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_methodprovider.php' );

// Includes the macmade.net API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_adlercontest_pi1 extends tslib_pibase
{
    /**
     * The database object
     */
    protected static $_db                 = NULL;
    
    /**
     * The method provider object
     */
    protected static $_mp                 = NULL;
    
    /**
     * Database tables
     */
    protected static $_dbTables           = array(
        'users'     => 'fe_users',
        'profiles'  => 'tx_adlercontest_users',
    );
    
    /**
     * Form fields for the registration
     */
    protected static $_registrationFields = array(
        'lastname'   => array( 'type' => 'text', ),
        'firstname'  => array( 'type' => 'text' ),
        'email'      => array( 'type' => 'text' ),
        'username'   => array( 'type' => 'text' ),
        'password'   => array( 'type' => 'password' ),
        'password2'  => array( 'type' => 'password' ),
        'agree'      => array( 'type' => 'checkbox' )
    );
    
    /**
     * Form fields for the profile
     */
    protected static $_profileFields      = array(
        'gender'         => array( 'type' => 'radio', 'items' => array( 'f', 'm' ) ),
        'address'        => array( 'type' => 'text' ),
        'address2'       => array( 'type' => 'text' ),
        'country'        => array( 'type' => 'country' ),
        'nationality'    => array( 'type' => 'text' ),
        'birthdate'      => array( 'type' => 'date' ),
        'school_name'    => array( 'type' => 'text' ),
        'school_address' => array( 'type' => 'text' ),
        'school_country' => array( 'type' => 'country' )
    );
    
    /**
     * Form fields for the profile
     */
    protected static $_uploadFields       = array(
        'age_proof'    => array( 'type' => 'file', 'ext' => 'pdf', 'size' => 2048 ),
        'school_proof' => array( 'type' => 'file', 'ext' => 'pdf', 'size' => 2048 ),
        'later'        => array( 'type' => 'checkbox', 'optionnal' => true )
    );
    
    /**
     * The new line character
     */
    protected static $_NL                 = '';
    
    /**
     * The TYPO3 site URL
     */
    protected static $_typo3Url           = '';
    
    /**
     * The instance of the Developer API
     */
    protected $_api                       = NULL;
    
    /**
     * The TypoScript configuration array
     */
    protected $_conf                      = array();
    
    /**
     * The user row
     */
    protected $_user                      = array();
    
    /**
     * The profile row
     */
    protected $_profile                   = array();
    
    /**
     * Storage for the form errors
     */
    protected $_errors                    = array();
    
    /**
     * The flexform data
     */
    protected $_piFlexForm                = '';
    
    /**
     * The upload directory
     */
    protected $_uploadDirectory           = '';
    
    /**
     * Current URL
     */
    protected $_url                       = '';
    
    /**
     * The current date
     */
    protected $_currentDate               = '';
    
    /**
     * The class name
     */
    public $prefixId                      = 'tx_adlercontest_pi1';
    
    /**
     * The path to this script relative to the extension directory
     */
    public $scriptRelPath                 = 'pi1/class.tx_adlercontest_pi1.php';
    
    /**
     * The extension key
     */
    public $extKey                        = 'adler_contest';
    
    /**
     * Wether to check plugin hash
     */
    public $pi_checkCHash                 = true;
    
    /**
     * The required version of the macmade.net API
     */
    public $apimacmade_version            = 4.5;
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_tscobj_pi1', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param   string  $content    The plugin content
     * @param   array   $conf       The TS setup
     * @return  string  The content of the plugin
     * @see     _userProfile
     * @see     _uploadDocuments
     * @see     _registrationForm
     */
    public function main( $content, array $conf )
    {
        // Checks if the new line character is already set
        if( !self::$_NL ) {
            
            // Sets the new line character
            self::$_NL = chr( 10 );
        }
        
        // Checks if the site URL is already set
        if( !self::$_typo3Url ) {
            
            // Sets the site URL
            self::$_typo3Url = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' );
        }
        
        // Checks if the DB object already exists
        if( !is_object( self::$_db ) ) {
            
            // Gets a reference to the database object
            self::$_db = $GLOBALS[ 'TYPO3_DB' ];
        }
        
        // Checks if the DB object already exists
        if( !is_object( self::$_mp ) ) {
            
            // Gets a reference to the database object
            self::$_mp = tx_adlercontest_methodProvider::getInstance();
        }
        
        // Stores the TypoScript configuration
        $this->_conf            = $conf;
        
        // Gets the current URL
        $this->_url             = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        // Gets the current date
        $this->_currentDate     = time();
        
        // Sets the upload directory
        $this->_uploadDirectory = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->extKey ) )
        );
        
        // Gets a new instance of the macmade.net API
        $this->_api             = tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                $this
            )
        );
        
        // Sets the default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Loads the LOCAL_LANG values
        $this->pi_loadLL();
        
        // Initialize the flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Stores the flexform informations
        $this->_piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Sets the final configuration (TS or FF)
        $this->_setConfig();
        
        // Initialize the template object
        $this->_api->fe_initTemplate( $this->_conf[ 'templateFile' ] );
        
        // Checks the view type
        if( isset( $this->piVars[ 'confirm' ] ) && $this->piVars[ 'confirm' ] ) {
            
            // Confirm user
            return $this->pi_wrapInBaseClass( $this->_userProfile() );
            
        } elseif( isset( $this->piVars[ 'upload' ] ) && $this->piVars[ 'upload' ] ) {
            
            // Upload documents
            return $this->pi_wrapInBaseClass( $this->_uploadDocuments() );
            
        } else {
            
            // Return the form
            return $this->pi_wrapInBaseClass( $this->_registrationForm() );
        }
    }
    
    /**
     * Sets the configuration array
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return  NULL
     */
    protected function _setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array(
            'pid'           => 'sDEF:pages',
            'userGroup'     => 'sDEF:group',
            'infoPage'      => 'sDEF:info_page',
            'registration.' => array(
                'header'       => 'sREGISTER:header',
                'description'  => 'sREGISTER:description',
                'conditions'   => 'sREGISTER:conditions',
                'confirmation' => 'sREGISTER:confirmation'
            ),
            'mailer.'       => array(
                'replyTo'  => 'sMAILER:reply_email',
                'from'     => 'sMAILER:from_email',
                'fromName' => 'sMAILER:from_name',
                'subject'  => 'sMAILER:subject',
                'message'  => 'sMAILER:message'
            ),
            'profile.' => array(
                'header'       => 'sPROFILE:header',
                'description'  => 'sPROFILE:description'
            ),
            'upload.' => array(
                'header'       => 'sUPLOAD:header',
                'description'  => 'sUPLOAD:description'
            ),
        );
        
        // Ovverride TS setup with flexform
        $this->_conf = $this->_api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->_conf,
            $this->_piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->_api->debug( $this->_conf, $this->prefixId . ': configuration array' );
    }
    
    /**
     * Creates an HTML form tag
     * 
     * @param   string  $content    The content to put in the form tag (the fields)
     * @param   array   $keepPiVars An array with the name of the plugin variables to keep
     * @param   boolean $cache      If sets, the action URL will be cached
     * @param   string  $name       The name parameter of the form
     * @param   string  $class      The CSS class of the form
     * @return  string  The form tag
     */
    protected function _formTag( $content, array $keepPiVars = array(), $cache = false, $name = 'form', $class = 'form' )
    {
        // Storage for additional parameters
        $addParams = array();
        
        // Checks for plugin variables to keep
        if( count( $keepPiVars ) ) {
            
            // Process each variable
            foreach( $keepPiVars as $piVar ) {
                
                // Checks for a value
                if( !isset( $this->piVars[ $piVar ] ) ) {
                    
                    // No value
                    continue;
                }
                
                // Adds the variable to the additionnal parameters
                $addParams[] = '&' . $this->prefixId . '[' . $piVar . ']=' . $this->piVars[ $piVar ];
            }
        }
        
        // TypoLink parameters for the form action
        $typoLink = array(
            'parameter'        => $GLOBALS[ 'TSFE' ]->id,
            'useCacheHash'     => 1,
            'additionalParams' => implode( '', $addParams )
        );
        
        // Checks if the form must be uncached
        if( !$cache ) {
            
            // No cache
            $typoLink[ 'useCacheHash' ] = 0;
            $typoLink[ 'no_cache' ]     = 1;
        }
        
        // Returns the form tag
        return $this->_api->fe_makeStyledContent(
            'form',
            $class,
            $content,
            true,
            false,
            false,
            array(
                'method'  => $this->_conf[ 'formMethod' ],
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                'id'      => $this->prefixId . '_' . $name,
                'name'    => $this->prefixId . '_' . $name,
                'action'  => $this->cObj->typoLink_URL( $typoLink )
            )
        );
    }
    
    /**
     * Creates form fields
     * 
     * @param   array   $fields             An array with the fields to create
     * @param   string  $templateSection    The template section to render
     * @return  string  The form fields
     */
    protected function _formFields( array $fields, $templateSection )
    {
        // Template markers
        $markers = array();
        
        // Process each field
        foreach( $fields as $fieldName => $fieldOptions ) {
            
            // Error message, if any
            $error              = ( isset( $this->_errors[ $fieldName ] ) ) ? $this->_api->fe_makeStyledContent( 'div', 'form-error', $this->_errors[ $fieldName ] ) : '';
            
            // Marker for the template
            $marker             = '###' . strtoupper( $fieldName ) . '###';
            
            // ID of the input
            $inputId            = $this->prefixId . '_' . $fieldName;
            
            // Field label
            $label              = $this->_api->fe_makeStyledContent(
                'div',
                'label',
                '<label for="' . $inputId . '">' . $this->pi_getLL( 'label-' . $fieldName ) . '</label>'
            );
            
            // Checks the input type
            switch( $fieldOptions[ 'type' ] ) {
                
                // Country select
                case 'country':
                    
                    // Input tag
                    $input = $this->_api->fe_makeStyledContent(
                        'div',
                        'input',
                        self::$_mp->countrySelect( $this->prefixId . '[' . $fieldName . ']' )
                    );
                    break;
                
                // Date select
                case 'date':
                    
                    // Input tag
                    $input = $this->_api->fe_makeStyledContent(
                        'div',
                        'input',
                        self::$_mp->dateSelect( $this->prefixId . '[' . $fieldName . ']' )
                    );
                    break;
                
                // Radio buttons
                case 'radio':
                    
                    // Storage
                    $radios = array();
                    
                    // Checks for items
                    if( isset( $fieldOptions[ 'items' ] ) && is_array( $fieldOptions[ 'items' ] ) ) {
                        
                        // Process each item
                        foreach( $fieldOptions[ 'items' ] as $radioValue ) {
                            
                            // Label for the radio item
                            $radioLabel = $this->_api->fe_makeStyledContent(
                                'span',
                                'radio-label',
                                $this->pi_getLL( 'label-' . $fieldName . '-' . $radioValue, $radioValue )
                            );
                            
                            // Radio item
                            $radioItem  = $this->_api->fe_makeStyledContent(
                                'span',
                                'radio-item',
                                '<input type="radio" name="'
                              . $this->prefixId . '[' . $fieldName . ']'
                              . '" value="'
                              . $radioValue
                              . '" id="'
                              . $inputId
                              . '" />'
                            );
                            
                            // Adds the full radio item
                            $radios[]   = $radioLabel . ' ' . $radioItem;
                        }
                    }
                    
                    // Input tag
                    $input = $this->_api->fe_makeStyledContent(
                        'div',
                        'input',
                        implode( self::$_NL, $radios )
                    );
                    break;
                
                // Default processing
                default:
                    
                    // Input value
                    $value = ( isset( $this->piVars[ $fieldName ] ) && $fieldOptions[ 'type' ] === 'text' ) ? ' value="' . $this->piVars[ $fieldName ] . '"' : '';
                    
                    // Input tag
                    $input = $this->_api->fe_makeStyledContent(
                        'div',
                        'input',
                        '<input type="'
                      . $fieldOptions[ 'type' ]
                      . '" name="'
                      . $this->prefixId . '[' . $fieldName . ']'
                      . '" id="'
                      . $inputId
                      . '"'
                      . ( ( $fieldOptions[ 'type' ] === 'text' || $fieldOptions[ 'type' ] === 'password' ) ? ' size="' . $this->_conf[ 'inputSize' ] . '"' : '' )
                      . $value
                      . ' />'
                    );
                    break;
            }
            
            // Field with label
            $field              = $this->_api->fe_makeStyledContent(
                'div',
                'field',
                $label . $input
            );
            
            // Sets the template marker
            $markers[ $marker ] = $error . $field;
        }
        
        // Renders the template markers and returns the result
        return $this->_api->fe_renderTemplate( $markers, $templateSection );
    }
    
    /**
     * Validates a form
     * 
     * @param   array   $fields     The fields to validate
     * @param   array   $callbacks  An array with the callbacks for specific field checks
     * @return  boolean True if the form is valid, otherwise false
     */
    protected function _formValid( array $fields, array $callbacks = array() )
    {
        // Checks if the form has been submitted
        if( !isset( $this->piVars[ 'submit' ] ) ) {
            
            // Form has not been submitted
            return false;
        }
        
        // Default error
        $defaultError = $this->pi_getLL( 'error-required' );
        
        // Process each field
        foreach( $fields as $fieldName => $fieldOptions ) {
            
            // Checks if the field is optionnal
            if( isset( $fieldOptions[ 'optionnal' ] ) && $fieldOptions[ 'optionnal' ] ) {
                
                // Next field
                continue;
            }
            
            // Field specific error
            $fieldError = $this->pi_getLL( 'error-' . $fieldName );
            
            // Error message for the current field
            $error      = ( $fieldError ) ? $fieldError : $defaultError;
            
            // Special processing for the file inputs
            if( $fieldOptions[ 'type' ] === 'file' ) {
                
                // Checks if the field is empty
                if( !isset( $_FILES[ $this->prefixId ][ 'name' ][ $fieldName ] ) || empty( $_FILES[ $this->prefixId ][ 'name' ][ $fieldName ] ) ) {
                    
                    // Stores the error message
                    $this->_errors[ $fieldName ] = $error;
                }
                
            } else {
                
                // Checks if the field is empty
                if( !isset( $this->piVars[ $fieldName ] ) || empty( $this->piVars[ $fieldName ] ) ) {
                    
                    // Stores the error message
                    $this->_errors[ $fieldName ] = $error;
                }
            }
                
            // Checks if an error is already set
            if( isset( $this->_errors[ $fieldName ] ) ) {
                
                // Next field
                continue;
            }
            
            // Checks for a specific callback
            if( isset( $callbacks[ $fieldName ] ) ) {
                
                // Calls the callback function
                if( $error = $this->$callbacks[ $fieldName ]( $fieldName, $fieldOptions ) ) {
                    
                    // Stores the error
                    $this->_errors[ $fieldName ] = $error;
                }
            }
        }
        
        // Returns the check state
        return ( count( $this->_errors ) ) ? false : true;
    }
    
    ############################################################################
    # Registration
    ############################################################################
    
    /**
     * Creates the registration form
     * 
     * @return  string  The registration form
     * @see _checkPassword
     * @see _checkPassword2
     * @see _checkEmail
     * @see _checkUsername
     * @see _formValid
     * @see _registerUser
     * @see _sendConfirmation
     * @see _formTag
     */
    protected function _registrationForm()
    {
        // Validation callbacks
        $validCallbacks = array(
            'password'  => '_checkPassword',
            'password2' => '_checkPassword2',
            'email'     => '_checkEmail',
            'username'  => '_checkUsername',
        );
        
        // Checks the submission, if any
        if( $this->_formValid( self::$_registrationFields, $validCallbacks ) ) {
            
            // Register the user
            $this->_registerUser();
            
            // Sends the confirmation email
            $this->_sendConfirmation();
            
            // Template markers
            $markers = array();
            
            // Sets the header
            $markers[ '###HEADER###' ]       = $this->_api->fe_makeStyledContent(
                'h2',
                'header',
                $this->pi_RTEcssText( $this->_conf[ 'registration.' ][ 'header' ] )
            );
            
            // Sets the confirmation
            $markers[ '###CONFIRMATION###' ]  = $this->_api->fe_makeStyledContent(
                'div',
                'confirmation',
                $this->pi_RTEcssText( $this->_conf[ 'registration.' ][ 'confirmation' ] )
            );
            
            
            
            // Returns the confirmation section
            return $this->_api->fe_renderTemplate( $markers, '###REGISTER_CONFIRM###' );
        }
        
        // Template markers
        $markers                         = array();
        
        // Sets the header
        $markers[ '###HEADER###' ]       = $this->_api->fe_makeStyledContent(
            'h2',
            'header',
            $this->pi_RTEcssText( $this->_conf[ 'registration.' ][ 'header' ] )
        );
        
        // Sets the description
        $markers[ '###DESCRIPTION###' ]  = $this->_api->fe_makeStyledContent(
            'div',
            'description',
            $this->pi_RTEcssText( $this->_conf[ 'registration.' ][ 'description' ] )
        );
        
        // Sets the conditions link
        $markers[ '###CONDITIONS###' ]   = $this->_api->fe_makeStyledContent(
            'div',
            'conditions-link',
            $this->cObj->typoLink(
                $this->pi_getLL( 'conditions-link' ),
                array(
                    'parameter'    => $this->_uploadDirectory . '/' . $this->_conf[ 'registration.' ][ 'conditions' ],
                    'useCacheHash' => 0,
                    'title'        => $title
                )
            )
        );
        
        // Sets the field infos
        $markers[ '###FIELDS_INFOS###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'field-infos',
            $this->pi_getLL( 'field-infos' )
        );
        
        // Creates the fields
        $markers[ '###FIELDS###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'fields',
            $this->_formFields( self::$_registrationFields, '###REGISTER_FIELDS###' )
        );
        
        // Sets the submit button
        $markers[ '###SUBMIT###' ]       = $this->_api->fe_makeStyledContent(
            'div',
            'submit',
            '<input name="'
          . $this->prefixId
          . '[submit]" id="'
          . $this->prefixId
          . '_submit" type="submit" value="'
          . $this->pi_getLL( 'submit' )
          . '" />'
        );
        
        // Full form
        $form                            = $this->_formTag( $this->_api->fe_renderTemplate( $markers, '###REGISTER_MAIN###' ) );
        
        // Returns the form
        return $form;
    }
    
    /**
     * 
     */
    protected function _checkPassword( $fieldName, array $fieldOptions )
    {
        // Checks the length
        if( strlen( $this->piVars[ $fieldName ] ) < $this->_conf[ 'passMinLength' ] ) {
            
            // Password too short
            return sprintf( $this->pi_getLL( 'error-password-length' ), $this->_conf[ 'passMinLength' ] );
        }
        
        return false;
    }
    /**
     * 
     */
    protected function _checkPassword2( $fieldName, array $fieldOptions )
    {
        // Checks the two passwords
        if( $this->piVars[ 'password' ] !== $this->piVars[ $fieldName ] ) {
            
            // Password does not match
            return $this->pi_getLL( 'error-password2-nomatch' );
        }
        
        return false;
    }
    
    /**
     * 
     */
    protected function _checkEmail( $fieldName, array $fieldOptions )
    {
        // Checks for a valid email
        if( !t3lib_div::validEmail( $this->piVars[ $fieldName ] ) ) {
            
            // Invalid email
            return $this->pi_getLL( 'error-email' );
        }
        
        // Checks that the email is unique
        if( !$this->_isUnique( $this->piVars[ $fieldName ], 'email', self::$_dbTables[ 'users' ], $this->_conf[ 'pid' ] ) ) {
            
            // Email is not unique
            return $this->pi_getLL( 'error-email-exists' );
        }
        
        return false;
    }
    
    /**
     * 
     */
    protected function _checkUsername( $fieldName, array $fieldOptions )
    {
        // Checks that the username is unique
        if( !$this->_isUnique( $this->piVars[ $fieldName ], 'username', self::$_dbTables[ 'users' ], $this->_conf[ 'pid' ] ) ) {
            
            // Username is not unique
            return $this->pi_getLL( 'error-username-exists' );
        }
        
        return false;
    }
    
    /**
     * 
     */
    protected function _isUnique( $value, $fieldName, $tableName, $pid, $checkHidden = true )
    {
        // Where clause
        $where = 'pid='
               . $pid
               . ' AND '
               . $fieldName
               . '='
               . self::$_db->fullQuoteStr( $value, $tableName )
               . $this->cObj->enableFields( $tableName, 1 );
        
        // Record selection
        $res = self::$_db->exec_SELECTquery( $fieldName, $tableName, $where );
        
        // Checks the result
        if( $res && self::$_db->sql_num_rows( $res ) ) {
            
            // Field is not unique
            return false;
        }
        
        // Field is unique
        return true;
    }
    
    /**
     * 
     */
    protected function _registerUser()
    {
        // Storage for the database
        $user                = array();
        $profile             = array();
        
        // Current time
        $time                = time();
        
        // Sets the user fields
        $user[ 'pid' ]       = $this->_conf[ 'pid' ];
        $user[ 'crdate' ]    = $time;
        $user[ 'tstamp' ]    = $time;
        $user[ 'disable' ]   = 1;
        $user[ 'username' ]  = $this->piVars[ 'username' ];
        $user[ 'password' ]  = trim( $this->piVars[ 'password' ] );
        $user[ 'email' ]     = trim( $this->piVars[ 'email' ] );
        $user[ 'usergroup' ] = $this->_conf[ 'userGroup' ];
        
        // Inserts the user
        self::$_db->exec_INSERTquery( self::$_dbTables[ 'users' ], $user );
        
        // Gets the user ID
        $userId                     = self::$_db->sql_insert_id();
        
        // Sets the profile fields
        $profile[ 'pid' ]           = $this->_conf[ 'pid' ];
        $profile[ 'crdate' ]        = $time;
        $profile[ 'tstamp' ]        = $time;
        $profile[ 'id_fe_users' ]   = $userId;
        $profile[ 'firstname' ]     = $this->piVars[ 'firstname' ];
        $profile[ 'lastname' ]      = $this->piVars[ 'lastname' ];
        
        // Confirmation token
        $profile[ 'confirm_token' ] = md5( uniqid( rand(), true) );
        
        // Inserts the profile
        self::$_db->exec_INSERTquery( self::$_dbTables[ 'profiles' ], $profile );
        
        // Gets the profile ID
        $profileId      = self::$_db->sql_insert_id();
        
        // Gets and stores the user (with t3lib_db as the record is hidden)
        $this->_user    = self::$_db->sql_fetch_assoc(
            self::$_db->exec_SELECTquery(
                '*',
                self::$_dbTables[ 'users' ],
                'uid=' . $userId
            )
        );
        
        // Gets and stores the user
        $this->_profile = $this->pi_getRecord( self::$_dbTables[ 'profiles' ], $profileId );
        
        return true;
    }
    
    /**
     * 
     */
    protected function _sendConfirmation()
    {
        // Confirmation link
        $confirmLink = self::$_typo3Url . $this->cObj->typoLink_URL(
            array(
                'parameter'        => $GLOBALS[ 'TSFE' ]->id,
                'useCacheHash'     => 0,
                'no_cache'         => 1,
                'additionalParams' => $this->_api->fe_typoLinkParams(
                    array(
                        'confirm' => $this->_profile[ 'confirm_token' ]
                    ),
                    false
                )
            )
        );
        
        // Final mail message, with tags replaced
        $message = preg_replace(
            array(
                '/\${firstname}/',
                '/\${lastname}/',
                '/\${username}/',
                '/\${password}/',
                '/\${confirmLink}/'
            ),
            array(
                $this->_profile[ 'firstname' ],
                $this->_profile[ 'lastname' ],
                $this->_user[ 'username' ],
                $this->_user[ 'password' ],
                $confirmLink
            ),
            $this->_conf[ 'mailer.' ][ 'message' ]
        );
        
        // Sends the confirmation email
        $this->cObj->sendNotifyEmail(
            $message,
            $this->_user[ 'email' ],
            '',
            $this->_conf[ 'mailer.' ][ 'from' ],
            $this->_conf[ 'mailer.' ][ 'fromMail' ],
            $this->_conf[ 'mailer.' ][ 'replyTo' ]
        );
        
        return true;
    }
    
    ############################################################################
    # Profile
    ############################################################################
    
    /**
     * 
     */
    protected function _userProfile()
    {
        // Where clause
        $where = 'pid='
               . $this->_conf[ 'pid' ]
               . ' AND confirm_token='
               . self::$_db->fullQuoteStr( $this->piVars[ 'confirm' ], self::$_dbTables[ 'profiles' ] )
               . $this->cObj->enableFields( self::$_dbTables[ 'profiles' ], true );
        
        // Try to select the user
        $res = self::$_db->exec_SELECTquery( '*', self::$_dbTables[ 'profiles' ], $where );
        
        // Checks the token
        if( $res && $profile = self::$_db->sql_fetch_assoc( $res ) ) {
            
            // Stores the profile
            $this->_profile = $profile;
            
            // Gets and stores the user
            $this->_user = self::$_db->sql_fetch_assoc(
                self::$_db->exec_SELECTquery(
                    '*',
                    self::$_dbTables[ 'users' ],
                    'uid=' . $this->_profile[ 'id_fe_users' ]
                )
            );
            
            // Checks the submission, if any
            if( $this->_formValid( self::$_profileFields ) ) {
                
                // Updates the profile
                $this->_updateProfile();
                
                // Next step URL
                $nextLink = self::$_typo3Url . $this->cObj->typoLink_URL(
                    array(
                        'parameter'        => $GLOBALS[ 'TSFE' ]->id,
                        'useCacheHash'     => 0,
                        'no_cache'         => 1,
                        'additionalParams' => $this->_api->fe_typoLinkParams(
                            array(
                                'upload' => $this->_profile[ 'confirm_token' ],
                            ),
                            false
                        )
                    )
                );
                
                // Go to the next step
                header( 'Location: ' . $nextLink );
            }
            
            // Template markers
            $markers                         = array();
            
            // Sets the header
            $markers[ '###HEADER###' ]       = $this->_api->fe_makeStyledContent(
                'h2',
                'header',
                $this->pi_RTEcssText( $this->_conf[ 'profile.' ][ 'header' ] )
            );
            
            // Sets the description
            $markers[ '###DESCRIPTION###' ]  = $this->_api->fe_makeStyledContent(
                'div',
                'description',
                $this->pi_RTEcssText( $this->_conf[ 'profile.' ][ 'description' ] )
            );
            
            // Sets the field infos
            $markers[ '###FIELDS_INFOS###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'field-infos',
                $this->pi_getLL( 'field-infos' )
            );
            
            // Sets the user name
            $markers[ '###USER_FULLNAME###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'user-fullname',
                $this->_profile[ 'firstname' ] . ' ' . $this->_profile[ 'lastname' ]
            );
            
            // Sets the user email
            $markers[ '###USER_EMAIL###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'user-email',
                $this->_user[ 'email' ]
            );
            
            // Creates the fields
            $markers[ '###FIELDS###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'fields',
                $this->_formFields( self::$_profileFields, '###PROFILE_FIELDS###' )
            );
            
            // Sets the submit button
            $markers[ '###SUBMIT###' ]       = $this->_api->fe_makeStyledContent(
                'div',
                'submit',
                '<input name="'
              . $this->prefixId
              . '[submit]" id="'
              . $this->prefixId
              . '_submit" type="submit" value="'
              . $this->pi_getLL( 'submit' )
              . '" />'
            );
            
            // Full form
            $form                            = $this->_formTag( $this->_api->fe_renderTemplate( $markers, '###PROFILE_MAIN###' ), array( 'confirm' ) );
            
            // Returns the form
            return $form;
        }
        
        // Invalid token
        return $this->_api->fe_makeStyledContent(
            'div',
            'error',
            $this->pi_getLL( 'confirm-error' )
        );
    }
    
    /**
     * 
     */
    protected function _updateProfile()
    {
        // Storage for the database
        $profile                     = array();
        
        // Current time
        $time                        = time();
        
        // Birthdate
        $birthdate                   = strtotime(
            $this->piVars[ 'birthdate' ][ 'year' ]
          . '-'
          . $this->piVars[ 'birthdate' ][ 'month' ]
          . '-'
          . $this->piVars[ 'birthdate' ][ 'day' ]
        );
        
        // Sets the profile fields
        $profile[ 'tstamp' ]         = $time;
        $profile[ 'gender' ]         = $this->piVars[ 'gender' ];
        $profile[ 'address' ]        = $this->piVars[ 'address' ];
        $profile[ 'address2' ]       = $this->piVars[ 'address2' ];
        $profile[ 'country' ]        = $this->piVars[ 'country' ];
        $profile[ 'nationality' ]    = $this->piVars[ 'nationality' ];
        $profile[ 'birthdate' ]      = $birthdate;
        $profile[ 'school_name' ]    = $this->piVars[ 'school_name' ];
        $profile[ 'school_address' ] = $this->piVars[ 'school_address' ];
        $profile[ 'school_country' ] = $this->piVars[ 'school_country' ];
        
        // Inserts the user
        self::$_db->exec_UPDATEquery( self::$_dbTables[ 'profiles' ], $this->_profile[ 'uid' ], $profile );
        
        return true;
    }
    
    ############################################################################
    # Upload
    ############################################################################
    
    protected function _uploadDocuments()
    {
        // Where clause
        $where = 'pid='
               . $this->_conf[ 'pid' ]
               . ' AND confirm_token='
               . self::$_db->fullQuoteStr( $this->piVars[ 'upload' ], self::$_dbTables[ 'profiles' ] )
               . $this->cObj->enableFields( self::$_dbTables[ 'profiles' ], true );
        
        // Try to select the user
        $res = self::$_db->exec_SELECTquery( '*', self::$_dbTables[ 'profiles' ], $where );
        
        // Checks the token
        if( $res && $profile = self::$_db->sql_fetch_assoc( $res ) ) {
            
            // Stores the profile
            $this->_profile = $profile;
            
            // Gets and stores the user
            $this->_user = self::$_db->sql_fetch_assoc(
                self::$_db->exec_SELECTquery(
                    '*',
                    self::$_dbTables[ 'users' ],
                    'uid=' . $this->_profile[ 'id_fe_users' ]
                )
            );
            
            // Checks if the documents will be uploaded later
            if( isset( $this->piVars[ 'submit' ] ) && isset( $this->piVars[ 'later' ] ) && $this->piVars[ 'later' ] ) {
                
                // Activates the user
                $this->_activateUser( $this->_user['uid' ], $this->_profile['uid' ] );
                
                // Connects the user
                self::$_mp->feLogin( $this->_user );
            
                // Next step URL
                $nextLink = self::$_typo3Url . $this->cObj->typoLink_URL(
                    array(
                        'parameter'        => $this->_conf[ 'infoPage' ],
                        'useCacheHash'     => 1
                    )
                );
                
                // Go to the next step
                header( 'Location: ' . $nextLink );
            }
            
            // Validation callbacks
            $validCallbacks = array(
                'age_proof'    => '_checkPdfUpload',
                'school_proof' => '_checkPdfUpload'
            );
            
            // Checks the submission, if any
            if( $this->_formValid( self::$_uploadFields, $validCallbacks ) ) {
                
                // Activates the user
                $this->_activateUser( $this->_user['uid' ], $this->_profile['uid' ] );
                
                // Connect the user
                $this->_feLogin();
                
                // Process the files
                $this->_processFiles();
            
                // Next step URL
                $nextLink = self::$_typo3Url . $this->cObj->typoLink_URL(
                    array(
                        'parameter'        => $this->_conf[ 'infoPage' ],
                        'useCacheHash'     => 1
                    )
                );
                
                // Go to the next step
                header( 'Location: ' . $nextLink );
            }
            
            // Template markers
            $markers                         = array();
            
            // Sets the header
            $markers[ '###HEADER###' ]       = $this->_api->fe_makeStyledContent(
                'h2',
                'header',
                $this->pi_RTEcssText( $this->_conf[ 'upload.' ][ 'header' ] )
            );
            
            // Sets the description
            $markers[ '###DESCRIPTION###' ]  = $this->_api->fe_makeStyledContent(
                'div',
                'description',
                $this->pi_RTEcssText( $this->_conf[ 'upload.' ][ 'description' ] )
            );
            
            // Creates the fields
            $markers[ '###FIELDS###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'fields',
                $this->_formFields( self::$_uploadFields, '###UPLOAD_FIELDS###' )
            );
            
            // Sets the submit button
            $markers[ '###SUBMIT###' ]       = $this->_api->fe_makeStyledContent(
                'div',
                'submit',
                '<input name="'
              . $this->prefixId
              . '[submit]" id="'
              . $this->prefixId
              . '_submit" type="submit" value="'
              . $this->pi_getLL( 'submit' )
              . '" />'
            );
            
            // Full form
            $form                            = $this->_formTag( $this->_api->fe_renderTemplate( $markers, '###UPLOAD_MAIN###' ), array( 'upload' ) );
            
            // Returns the form
            return $form;
        }
        
        // Invalid token
        return $this->_api->fe_makeStyledContent(
            'div',
            'error',
            $this->pi_getLL( 'confirm-error' )
        );
    }
    
    /**
     * 
     */
    protected function _checkPdfUpload( $fieldName, array $fieldOptions )
    {
        // Checks the file extension
        if( !strstr( $_FILES[ $this->prefixId ][ 'name' ][ $fieldName ], '.' . $fieldOptions[ 'ext' ] ) ) {
            
            // Wrong extension
            return $this->pi_getLL( 'error-file-ext' );
        }
        
        // Checks the file size
        if( $_FILES[ $this->prefixId ][ 'size' ][ $fieldName ] > ( $fieldOptions[ 'size' ] * 1024 ) ) {
            
            // File to large
            return $this->pi_getLL( 'error-file-size' );
        }
        
        return false;
    }
    
    /**
     * 
     */
    protected function _processFiles()
    {
        // Absolute path to the upload directory
        $uploadDir  = t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->extKey ) );
        
        // Prefix for the files
        $filePrefix = md5( uniqid( rand(), true) );
        
        // Move the files to the upload directory
        move_uploaded_file( $_FILES[ $this->prefixId ][ 'tmp_name' ][ 'age_proof' ],    $uploadDir . DIRECTORY_SEPARATOR . $filePrefix . '-age.pdf' );
        move_uploaded_file( $_FILES[ $this->prefixId ][ 'tmp_name' ][ 'school_proof' ], $uploadDir . DIRECTORY_SEPARATOR . $filePrefix . '-school.pdf' );
        
        // Updates the profile
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'profiles' ],
            'uid=' . $this->_profile[ 'uid' ],
            array(
                'age_proof'    => $filePrefix . '-age.pdf',
                'school_proof' => $filePrefix . '-school.pdf'
            )
        );
    }
    
    /**
     * 
     */
    protected function _activateUser( $userId, $profileId )
    {
        // Activates the user
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'users' ],
            'uid=' . $userId,
            array(
                'disable' => 0
            )
        );
        
        // Removes the token
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'profiles' ],
            'uid=' . $profileId,
            array(
                'confirm_token' => ''
            )
        );
        
        return true;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi1/class.tx_adlercontest_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi1/class.tx_adlercontest_pi1.php']);
}
