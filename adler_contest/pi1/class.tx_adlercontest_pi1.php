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

// TYPO3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Method provider
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_methodprovider.php' );

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
        'lastname'   => 'text',
        'firstname'  => 'text',
        'email'      => 'text',
        'username'   => 'text',
        'password'   => 'password',
        'password2'  => 'password',
        'agree'      => 'checkbox'
    );
    
    /**
     * The new line character
     */
    protected static $_NL                 = '';
    
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
     */
    public function main( $content, array $conf )
    {
        // Checks if the new line character is already set
        if( !self::$_NL ) {
            
            // Sets the new line character
            self::$_NL = chr( 10 );
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
        
        // Checks for a confirmation
        if( isset( $this->piVars[ 'confirm' ] ) && $this->piVars[ 'confirm' ] ) {
            
            return $this->_userConfirmation();
            
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
            )
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
    
    ############################################################################
    # Registration
    ############################################################################
    
    /**
     * 
     */
    protected function _registrationForm()
    {
        // Checks the submission, if any
        if( $this->_checkRegistration() ) {
            
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
            $this->_registrationFields()
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
        $form                            = $this->_api->fe_makeStyledContent(
            'form',
            'form',
            $this->_api->fe_renderTemplate( $markers, '###REGISTER_MAIN###' ),
            true,
            false,
            false,
            array(
                'method'  => $this->_conf[ 'formMethod' ],
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                'id'      => $this->prefixId . '_form',
                'name'    => $this->prefixId . '_form',
                'action'  => $this->cObj->typoLink_URL(
                    array(
                        'parameter'    => $GLOBALS[ 'TSFE' ]->id,
                        'useCacheHash' => 1
                    )
                )
            )
        );
        
        // Returns the form
        return $form;
    }
    
    /**
     * 
     */
    protected function _checkRegistration()
    {
        // Checks if the form has been submitted
        if( !isset( $this->piVars[ 'submit' ] ) ) {
            
            // Form has not been submitted
            return false;
        }
        
        // Default error
        $defaultError = $this->pi_getLL( 'error-required' );
        
        // Process each field
        foreach( self::$_registrationFields as $fieldName => $fieldType ) {
            
            // Do not check password confirmation if errors were found in first password
            if( $fieldName === 'password2' && isset( $this->_errors[ 'password' ] ) ) {
                
                // Next field
                continue;
            }
            
            // Field specific error
            $fieldError   = $this->pi_getLL( 'error-' . $fieldName );
            
            // Error message for the current field
            $error = ( $fieldError ) ? $fieldError : $defaultError;
            
            // Checks if the field is empty
            if( !isset( $this->piVars[ $fieldName ] ) || empty( $this->piVars[ $fieldName ] ) ) {
                
                // Stores the error message
                $this->_errors[ $fieldName ] = $error;
            }
            
            // Checks if an error is already set
            if( isset( $this->_errors[ $fieldName ] ) ) {
                
                // Next field
                continue;
            }
            
            // Password specific check
            if( $fieldName === 'password' ) {
                
                // Checks the length
                if( strlen( $this->piVars[ $fieldName ] ) < $this->_conf[ 'passMinLength' ] ) {
                    
                    // Password too short
                    $this->_errors[ $fieldName ] = sprintf( $this->pi_getLL( 'error-password-length' ), $this->_conf[ 'passMinLength' ] );
                }
            }
            
            // Password confirmation specific check
            if( $fieldName === 'password2' ) {
                
                // Checks the two passwords
                if( $this->piVars[ 'password' ] !== $this->piVars[ $fieldName ] ) {
                    
                    // Password does not match
                    $this->_errors[ $fieldName ] = $this->pi_getLL( 'error-password2-nomatch' );
                }
            }
            
            // Email specific check
            if( $fieldName === 'email' ) {
                
                // Checks for a valid email
                if( !t3lib_div::validEmail( $this->piVars[ $fieldName ] ) ) {
                    
                    // Invalid email
                    $this->_errors[ $fieldName ] = $error;
                }
                
                // Checks that the email is unique
                if( !$this->_isUnique( $this->piVars[ $fieldName ], 'email', self::$_dbTables[ 'users' ], $this->_conf[ 'pid' ] ) ) {
                    
                    // Email is not unique
                    $this->_errors[ $fieldName ] = $this->pi_getLL( 'error-email-exists' );
                }
            }
            
            // Login specific check
            if( $fieldName === 'username' ) {
                
                // Checks that the email is unique
                if( !$this->_isUnique( $this->piVars[ $fieldName ], 'username', self::$_dbTables[ 'users' ], $this->_conf[ 'pid' ] ) ) {
                    
                    // Email is not unique
                    $this->_errors[ $fieldName ] = $this->pi_getLL( 'error-username-exists' );
                }
            }
        }
        
        // Returns the check state
        return ( count( $this->_errors ) ) ? false : true;
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
               . $this->cObj->enableFields( $tableName, $checkHidden );
        
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
    protected function _registrationFields()
    {
        // Template markers
        $markers = array();
        
        // Process each field
        foreach( self::$_registrationFields as $fieldName => $fieldType ) {
            
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
            
            // Input value
            $value              = ( isset( $this->piVars[ $fieldName ] ) && $fieldType === 'text' ) ? ' value="' . $this->piVars[ $fieldName ] . '"' : '';
            
            // Iput tag
            $input              = $this->_api->fe_makeStyledContent(
                'div',
                'input',
                '<input type="'
              . $fieldType
              . '" name="'
              . $this->prefixId . '[' . $fieldName . ']'
              . '" id="'
              . $inputId
              . '"'
              . ( ( $fieldType === 'text' || $fieldType === 'password' ) ? ' size="' . $this->_conf[ 'inputSize' ] . '"' : '' )
              . $value
              . ' />'
            );
            
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
        return $this->_api->fe_renderTemplate( $markers, '###REGISTER_FIELDS###' );
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
        $user[ 'password' ]  = $this->piVars[ 'password' ];
        $user[ 'email' ]     = $this->piVars[ 'email' ];
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
        // TYPO3 site URL
        $typo3Url    = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' );
        
        // Confirmation link
        $confirmLink = $typo3Url . $this->cObj->typoLink_URL(
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
    # Confirmation
    ############################################################################
    
    /**
     * 
     */
    protected function _userConfirmation()
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
                $this->_registrationFields()
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
            $form                            = $this->_api->fe_makeStyledContent(
                'form',
                'form',
                $this->_api->fe_renderTemplate( $markers, '###PROFILE_MAIN###' ),
                true,
                false,
                false,
                array(
                    'method'  => $this->_conf[ 'formMethod' ],
                    'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                    'id'      => $this->prefixId . '_form',
                    'name'    => $this->prefixId . '_form',
                    'action'  => $this->cObj->typoLink_URL(
                        array(
                            'parameter'    => $GLOBALS[ 'TSFE' ]->id,
                            'useCacheHash' => 1
                        )
                    )
                )
            );
            
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
    protected function _activateUser( $userId, $profileId )
    {
        // Activates the user
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'users' ],
            'uid=' . $id,
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
