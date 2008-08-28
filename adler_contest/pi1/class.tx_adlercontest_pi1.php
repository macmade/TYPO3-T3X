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

// Includes the frontend plugin base class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_pibase.php' );

class tx_adlercontest_pi1 extends tx_adlercontest_piBase
{
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
     * Form fields for the proof documents
     */
    protected static $_proofFields       = array(
        'age_proof'    => array( 'type' => 'file', 'ext' => 'jpg,jpeg', 'size' => 2048 ),
        'school_proof' => array( 'type' => 'file', 'ext' => 'jpg,jpeg', 'size' => 2048 ),
        'later'        => array( 'type' => 'checkbox', 'optionnal' => true )
    );
    
    /**
     * The user row
     */
    protected $_user                      = array();
    
    /**
     * The profile row
     */
    protected $_profile                   = array();
    
    /**
     * Configuration mapping array (between TS and Flex)
     */
    protected $_configMap                 = array(
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
        'proof.' => array(
            'header'       => 'sPROOF:header',
            'description'  => 'sPROOF:description'
        )
    );
    
    /**
     * 
     */
    protected function _getContent()
    {
        // Checks the view type
        if( isset( $this->piVars[ 'redirect' ] ) && $this->piVars[ 'redirect' ] ) {
            
            // Next step URL
            // Double redirection to handle the frontend user login
            $nextLink = self::$_typo3Url . $this->cObj->typoLink_URL(
                array(
                    'parameter'        => $this->_conf[ 'infoPage' ],
                    'useCacheHash'     => 1
                )
            );
            
            // Go to the next step
            header( 'Location: ' . $nextLink );
            exit();
            
        } elseif( isset( $this->piVars[ 'confirm' ] ) && $this->piVars[ 'confirm' ] ) {
            
            // Confirm user
            return $this->_userProfile();
            
        } elseif( isset( $this->piVars[ 'proof' ] ) && $this->piVars[ 'proof' ] ) {
            
            // Upload proof documents
            return $this->_proofDocuments();
            
        } else {
            
            // Return the form
            return $this->_registrationForm();
        }
    }
    
    ############################################################################
    # Registration
    ############################################################################
    
    /**
     * Creates the registration form
     * 
     * @return  string  The registration form
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
                $this->_conf[ 'registration.' ][ 'header' ]
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
            $this->_conf[ 'registration.' ][ 'header' ]
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
                    'parameter'    => self::$_uploadDirectory . $this->_conf[ 'registration.' ][ 'conditions' ],
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
        $markers[ '###SUBMIT###' ]       = $this->_submitButton();
        
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
                'parameter'        => self::$_tsfe->id,
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
                        'parameter'        => self::$_tsfe->id,
                        'useCacheHash'     => 0,
                        'no_cache'         => 1,
                        'additionalParams' => $this->_api->fe_typoLinkParams(
                            array(
                                'proof' => $this->_profile[ 'confirm_token' ],
                            ),
                            false
                        )
                    )
                );
                
                // Go to the next step
                header( 'Location: ' . $nextLink );
                exit();
            }
            
            // Template markers
            $markers                         = array();
            
            // Sets the header
            $markers[ '###HEADER###' ]       = $this->_api->fe_makeStyledContent(
                'h2',
                'header',
                $this->_conf[ 'profile.' ][ 'header' ]
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
            $markers[ '###SUBMIT###' ]       = $this->_submitButton();
            
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
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'profiles' ],
            'uid=' . $this->_profile[ 'uid' ],
            $profile
        );
        
        return true;
    }
    
    ############################################################################
    # Proof documents
    ############################################################################
    
    protected function _proofDocuments()
    {
        // Where clause
        $where = 'pid='
               . $this->_conf[ 'pid' ]
               . ' AND confirm_token='
               . self::$_db->fullQuoteStr( $this->piVars[ 'proof' ], self::$_dbTables[ 'profiles' ] )
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
                        'parameter'        => self::$_tsfe->id,
                        'useCacheHash'     => 0,
                        'no_cache'         => 1,
                        'additionalParams' => $this->_api->fe_typoLinkParams(
                            array(
                                'redirect' => 1,
                            ),
                            false
                        )
                    )
                );
                
                // Go to the next step
                header( 'Location: ' . $nextLink );
                exit();
            }
            
            // Validation callbacks
            $validCallbacks = array(
                'age_proof'    => '_checkUploadType',
                'school_proof' => '_checkUploadType'
            );
            
            // Checks the submission, if any
            if( $this->_formValid( self::$_proofFields, $validCallbacks ) ) {
                
                // Activates the user
                $this->_activateUser( $this->_user['uid' ], $this->_profile['uid' ] );
                
                // Connect the user
                self::$_mp->feLogin( $this->_user );
                
                // Process the files
                $this->_processProofFiles();
            
                // Next step URL
                $nextLink = self::$_typo3Url . $this->cObj->typoLink_URL(
                    array(
                        'parameter'        => self::$_tsfe->id,
                        'useCacheHash'     => 0,
                        'no_cache'         => 1,
                        'additionalParams' => $this->_api->fe_typoLinkParams(
                            array(
                                'redirect' => 1,
                            ),
                            false
                        )
                    )
                );
                
                // Go to the next step
                header( 'Location: ' . $nextLink );
                exit();
            }
            
            // Template markers
            $markers                         = array();
            
            // Sets the header
            $markers[ '###HEADER###' ]       = $this->_api->fe_makeStyledContent(
                'h2',
                'header',
                $this->_conf[ 'proof.' ][ 'header' ]
            );
            
            // Sets the description
            $markers[ '###DESCRIPTION###' ]  = $this->_api->fe_makeStyledContent(
                'div',
                'description',
                $this->pi_RTEcssText( $this->_conf[ 'proof.' ][ 'description' ] )
            );
            
            // Creates the fields
            $markers[ '###FIELDS###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'fields',
                $this->_formFields( self::$_proofFields, '###PROOF_FIELDS###' )
            );
            
            // Sets the submit button
            $markers[ '###SUBMIT###' ]       = $this->_submitButton();
            
            // Full form
            $form                            = $this->_formTag( $this->_api->fe_renderTemplate( $markers, '###PROOF_MAIN###' ), array( 'proof' ) );
            
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
    protected function _processProofFiles()
    {
        // Absolute path to the upload directory
        $uploadDir  = t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->extKey ) );
        
        // Prefix for the files
        $filePrefix = md5( uniqid( rand(), true) );
        
        // Move the files to the upload directory
        move_uploaded_file( $_FILES[ $this->prefixId ][ 'tmp_name' ][ 'age_proof' ],    $uploadDir . DIRECTORY_SEPARATOR . $filePrefix . '-age.jpg' );
        move_uploaded_file( $_FILES[ $this->prefixId ][ 'tmp_name' ][ 'school_proof' ], $uploadDir . DIRECTORY_SEPARATOR . $filePrefix . '-school.jpg' );
        
        // Updates the profile
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'profiles' ],
            'uid=' . $this->_profile[ 'uid' ],
            array(
                'age_proof'    => $filePrefix . '-age.jpg',
                'school_proof' => $filePrefix . '-school.jpg'
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
