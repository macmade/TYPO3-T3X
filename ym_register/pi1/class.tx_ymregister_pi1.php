<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 netinfluence - Jean-David Gadina (macmade@netinfluence.com)         #
# All rights reserved                                                          #
#                                                                              #
# This script is part of the TYPO3 project. The TYPO3 project is free          #
# software. You can redistribute it and/or modify it under the terms of the    #
# GNU General Public License as published by the Free Software Foundation,     #
# either version 2 of the License, or (at your option) any later version.      #
#                                                                              #
# The GNU General Public License can be found at:                              #
# http://www.gnu.org/copyleft/gpl.html.                                        #
#                                                                              #
# This script is distributed in the hope that it will be useful, but WITHOUT   #
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or        #
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for    #
# more details.                                                                #
#                                                                              #
# This copyright notice MUST APPEAR in all copies of the script!               #
################################################################################

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin PI1 for the 'ym_register' extension.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  ym_register
 */
class tx_ymregister_pi1 extends tx_oop_Plugin_Base
{
    /**
     * The TYPO3 frontend users table name
     */
    const TABLE_USERS     = 'fe_users';
    
    /**
     * The TYPO3 frontend groups table name
     */
    const TABLE_GROUPS    = 'fe_groups';
    
    /**
     * The users profile table name
     */
    const TABLE_PROFILES = 'tx_ymregister_users';
    
    /**
     * The states table name
     */
    const TABLE_STATES   = 'tx_netdata_states';
    
    /**
     * The cities table name
     */
    const TABLE_CITIES   = 'tx_netdata_cities';
    
    /**
     * An object (stdClass) with the registration form errors
     */
    protected $_formErrors    = NULL;
    
    /**
     * The ID of the page in which to store the users and profiles
     */
    protected $_storagePage   = 0;
    
    /**
     * The ID of the page with the terms & conditions
     */
    protected $_termsPage     = 0;
    
    /**
     * The usergroup for the created users
     */
    protected $_userGroup     = 0;
    
    /**
     * The confirmation hash for the new users
     */
    protected $_confirmHash   = '';
    
    /**
     * The subject of the confirmation email
     */
    protected $_emailSubject  = '';
    
    /**
     * The from email address for the confirmation email
     */
    protected $_emailFrom     = '';
    
    /**
     * The name of the from email address for the confirmation email
     */
    protected $_emailFromName = '';
    
    /**
     * The reply-to address for the confirmation email
     */
    protected $_emailReplyTo  = '';
    
    /**
     * The message of the confirmation email
     */
    protected $_emailMessage  = '';
    
    /**
     * Creates the registration form
     * 
     * @param   tx_oop_Frontend_Template_Section    The template section for the registration form
     * @return  NULL
     */
    protected function _registrationForm( tx_oop_Frontend_Template_Section $tmpl )
    {
        // Action parameter for the form tag
        $tmpl->formAction = $this->pi_getPageLink( self::$_tsfe->id, '', array( 'no_cache' => 1 ) );
        
        // Fills the form labels
        $tmpl->labelFirstName = $this->_lang->labelFirstName;
        $tmpl->labelLastName  = $this->_lang->labelLastName;
        $tmpl->labelEmail     = $this->_lang->labelEmail;
        $tmpl->labelAge       = $this->_lang->labelAge;
        $tmpl->labelNickname  = $this->_lang->labelNickname;
        $tmpl->labelPassword1 = $this->_lang->labelPassword1;
        $tmpl->labelPassword2 = $this->_lang->labelPassword2;
        $tmpl->labelState     = $this->_lang->labelState;
        $tmpl->labelSubmit    = $this->_lang->labelSubmit;
        $tmpl->labelPhone     = $this->_lang->labelPhone;
        $tmpl->labelAddress   = $this->_lang->labelAddress;
        
        // Fills the names of the form elements
        $tmpl->nameFieldLastName  = __CLASS__ . '[user][lastName]';
        $tmpl->nameFieldFirstName = __CLASS__ . '[user][firstName]';
        $tmpl->nameFieldAge       = __CLASS__ . '[user][age]';
        $tmpl->nameFieldEmail     = __CLASS__ . '[user][email]';
        $tmpl->nameFieldNickname  = __CLASS__ . '[user][nickname]';
        $tmpl->nameFieldPassword1 = __CLASS__ . '[user][password1]';
        $tmpl->nameFieldPassword2 = __CLASS__ . '[user][password2]';
        $tmpl->nameFieldState     = __CLASS__ . '[user][state]';
        $tmpl->nameFieldPhone     = __CLASS__ . '[user][phone]';
        $tmpl->nameFieldAddress   = __CLASS__ . '[user][address]';
        $tmpl->nameFieldTerms     = __CLASS__ . '[user][terms]';
        $tmpl->nameFieldZip       = __CLASS__ . '[user][zip]';
        
        // Fills the error messages (if available)
        $tmpl->errorLastName  = $this->_formErrors->lastName;
        $tmpl->errorFirstName = $this->_formErrors->firstName;
        $tmpl->errorAge       = $this->_formErrors->age;
        $tmpl->errorEmail     = $this->_formErrors->email;
        $tmpl->errorNickname  = $this->_formErrors->nickname;
        $tmpl->errorPassword1 = $this->_formErrors->password1;
        $tmpl->errorPassword2 = $this->_formErrors->password2;
        $tmpl->errorState     = $this->_formErrors->state;
        $tmpl->errorPhone     = $this->_formErrors->phone;
        $tmpl->errorAddress   = $this->_formErrors->address;
        $tmpl->errorTerms     = $this->_formErrors->terms;
        
        // Fills the form values (if available)
        $tmpl->valueLastName  = ( isset( $this->piVars[ 'user' ][ 'lastName' ] ) )  ? $this->piVars[ 'user' ][ 'lastName' ]  : '';
        $tmpl->valueFirstName = ( isset( $this->piVars[ 'user' ][ 'firstName' ] ) ) ? $this->piVars[ 'user' ][ 'firstName' ] : '';
        $tmpl->valueEmail     = ( isset( $this->piVars[ 'user' ][ 'email' ] ) )     ? $this->piVars[ 'user' ][ 'email' ]     : '';
        $tmpl->valueAge       = ( isset( $this->piVars[ 'user' ][ 'age' ] ) )       ? $this->piVars[ 'user' ][ 'age' ]       : '';
        $tmpl->valueNickname  = ( isset( $this->piVars[ 'user' ][ 'nickname' ] ) )  ? $this->piVars[ 'user' ][ 'nickname' ]  : '';
        $tmpl->valuePhone     = ( isset( $this->piVars[ 'user' ][ 'phone' ] ) )     ? $this->piVars[ 'user' ][ 'phone' ]     : '';
        $tmpl->valueAddress   = ( isset( $this->piVars[ 'user' ][ 'address' ] ) )   ? $this->piVars[ 'user' ][ 'address' ]   : '';
        $tmpl->valueZip       = ( isset( $this->piVars[ 'user' ][ 'zip' ] ) )       ? $this->piVars[ 'user' ][ 'zip' ]       : '';
        
        // Creates the hidden field for the form action detection
        $action             = new tx_oop_Xhtml_Tag( 'input' );
        $action[ 'type' ]   = 'hidden';
        $action[ 'name' ]   = __CLASS__ . '[submit]';
        $action[ 'value' ]  = 1;
        $tmpl->hiddenFields = $action;
        
        
        $termsLink             = new tx_oop_Xhtml_Tag( 'a' );
        $termsLink[ 'href' ]   = $this->pi_getPageLink( $this->_termsPage );
        $termsLink[ 'target' ] = '_blank';
        
        $termsLink->addTextData( $this->_lang->labelTerms );
        
        $tmpl->labelTerms = ( string )$termsLink;
        
        // Creates the option tags for the states
        $tmpl->states        = $this->_getStatesOptions();
        
        // Create the cities select menu, if necessary
        $tmpl->cities        = '<div id="' . __CLASS__ . '_cities">' . $this->_getCitiesSelect() . '</div>';
        
        // Adds the 'onchange' JavaScript action for the states select menu
        $tmpl->onChangeState = 'tx_ymregister_Utils.getInstance().getCities( \''
                             . t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' )
                             . '?eID='
                             . $this->extKey
                             . '_getCities\', this )';
    }
    
    /**
     * Gets the option tags for the states select menu
     * 
     * @return  string  The option tags
     */
    protected function _getStatesOptions()
    {
        // Empty option at start
        $options = '<option value=""></option>';
        
        // Gets the available states for Switzerland (41)
        $states = self::$_db->getRecordsByFields(
            self::TABLE_STATES,
            array( 'country_code' => 41 ),
            'fullname'
        );
        
        // Process each state
        foreach( $states as $uid => $row ) {
            
            // Selected state, if the form is redrawn
            $selected = ( isset( $this->piVars[ 'user' ][ 'state' ] ) && $this->piVars[ 'user' ][ 'state' ] == $uid ) ? ' selected="selected"' : '';
            
            // Creates the option tag
            $options .= '<option value="' . $uid . '"' . $selected . '>' . htmlentities( $row->fullname ) . '</option>';
        }
        
        // Return the option tags
        return $options;
    }
    
    /**
     * Gets the select menu for the cities
     * 
     * @return  string  The select menu
     */
    protected function _getCitiesSelect()
    {
        // Checks if we have a selected state
        if( !isset( $this->piVars[ 'user' ][ 'state' ] ) || !$this->piVars[ 'user' ][ 'state' ] ) {
            
            // No state - Do not display the cities select
            return;
        }
        
        // Gets the available cities for the selected state
        $cities = self::$_db->getRecordsByFields(
            self::TABLE_CITIES,
            array( 'id_tx_netdata_states' => ( int )$this->piVars[ 'user' ][ 'state' ] ),
            'fullname'
        );
        
        // Checks if we have cities
        if( !count( $cities ) ) {
            
            // No available city
            return;
        }
        
        // Creates the select menu
        $select           = new tx_oop_Xhtml_Tag( 'select' );
        $select[ 'size' ] = 1;
        $select[ 'id' ]   = 'tx-ymregister-pi1-field-city';
        $select[ 'name' ] = 'tx_ymregister_pi1[user][city]';
        
        // Empty option at start
        $select->option;
        
        // Process each city
        foreach( $cities as $uid => $city ) {
            
            // Creates the option tag
            $option            = $select->option;
            $option[ 'value' ] = $uid;
            
            // Checks if we have a previously selected city (if the form is redrawn)
            if( isset( $this->piVars[ 'user' ][ 'city' ] ) && $this->piVars[ 'user' ][ 'city' ] == $uid ) {
                
                // Adds the selected parameter
                $option[ 'selected' ] = 'selected';
            }
            
            // Adds the city name
            $option->addTextData( htmlentities( $city->fullname ) );
        }
        
        // Returns the cities select menu
        return $select;
    }
    
    /**
     * Checks the fields of the registration form
     * 
     * If errors are detected, a property will be added to the _formErrors
     * (stdClass) property, with the error message to display.
     * 
     * @return  boolean True if the form contains no error, otherwise false
     */
    protected function _checkRegistrationForm()
    {
        // Last name is required
        if( !isset( $this->piVars[ 'user' ][ 'lastName' ] ) || !$this->piVars[ 'user' ][ 'lastName' ] ) {
            
            // No value
            $this->_formErrors->lastName = $this->_lang->errorFieldRequired;
        }
        
        // First name is required
        if( !isset( $this->piVars[ 'user' ][ 'firstName' ] ) || !$this->piVars[ 'user' ][ 'firstName' ] ) {
            
            // No value
            $this->_formErrors->firstName = $this->_lang->errorFieldRequired;
        }
        
        // Address is required
        if( !isset( $this->piVars[ 'user' ][ 'address' ] ) || !$this->piVars[ 'user' ][ 'address' ] ) {
            
            // No value
            $this->_formErrors->address = $this->_lang->errorFieldRequired;
        }
        
        // Age is required and must be numeric
        if( !isset( $this->piVars[ 'user' ][ 'age' ] ) || !$this->piVars[ 'user' ][ 'age' ] ) {
            
            // No value
            $this->_formErrors->age = $this->_lang->errorFieldRequired;
            
        } elseif( !is_numeric( $this->piVars[ 'user' ][ 'age' ] ) ) {
            
            // Invalid value
            $this->_formErrors->age = $this->_lang->errorAgeNotNumeric;
        }
        
        // ZIP code is required
        if( !isset( $this->piVars[ 'user' ][ 'zip' ] ) || !$this->piVars[ 'user' ][ 'zip' ] ) {
            
            // No value
            $this->_formErrors->state = $this->_lang->errorFieldRequired;
        }
        
        // State is required
        if( !isset( $this->piVars[ 'user' ][ 'state' ] ) || !$this->piVars[ 'user' ][ 'state' ] ) {
            
            // No value
            $this->_formErrors->state = $this->_lang->errorFieldRequired;
        }
        
        // City is required
        if( !isset( $this->piVars[ 'user' ][ 'city' ] ) || !$this->piVars[ 'user' ][ 'city' ] ) {
            
            // No value
            $this->_formErrors->state = $this->_lang->errorFieldRequired;
        }
        
        // Terms is required
        if( !isset( $this->piVars[ 'user' ][ 'terms' ] ) ) {
            
            // No value
            $this->_formErrors->terms = $this->_lang->errorFieldRequired;
        }
        
        // Email is required, must be valid, and must be unique
        if( !isset( $this->piVars[ 'user' ][ 'email' ] ) || !$this->piVars[ 'user' ][ 'email' ] ) {
            
            // No value
            $this->_formErrors->email = $this->_lang->errorFieldRequired;
            
        } elseif( !t3lib_div::validEmail( $this->piVars[ 'user' ][ 'email' ] ) ) {
            
            // Invalid email
            $this->_formErrors->email = $this->_lang->errorInvalidEmail;
            
        } else {
            
            // Tries to get a user with the same email
            $users = self::$_db->getRecordsWhere(
                self::TABLE_USERS,
                'pid=' . $this->_storagePage . ' AND username="' . $this->piVars[ 'user' ][ 'email' ] . '" AND deleted=0'
            );
            
            // Checks if we already have a user
            if( count( $users ) ) {
                
                // Email is already registered
                $this->_formErrors->email = $this->_lang->errorEmailExist;
            }
        }
        
        // Nickname must be unique if specified
        if( isset( $this->piVars[ 'user' ][ 'nickname' ] ) && $this->piVars[ 'user' ][ 'nickname' ] ) {
            
            // Tries to get a user with the same nickname
            $users = self::$_db->getRecordsByFields(
                self::TABLE_PROFILES,
                array(
                    'pid'      => $this->_storagePage,
                    'nickname' => $this->piVars[ 'user' ][ 'nickname' ]
                )
            );
            
            // Checks if we already have a user
            if( count( $users ) ) {
            
                // Nickname is already registered
                $this->_formErrors->nickname = $this->_lang->errorNicknameExist;
            }
        }
        
        // Password is required, and must be written twice
        if( !isset( $this->piVars[ 'user' ][ 'password1' ] ) || !$this->piVars[ 'user' ][ 'password1' ] ) {
            
            // No value
            $this->_formErrors->password1 = $this->_lang->errorFieldRequired;
            
        }
        
        // Password is required, and must be written twice
        if( !isset( $this->piVars[ 'user' ][ 'password2' ] ) || !$this->piVars[ 'user' ][ 'password2' ] ) {
            
            // No value (confirmation)
            $this->_formErrors->password2 = $this->_lang->errorFieldRequired;
            
        } elseif( $this->piVars[ 'user' ][ 'password1' ] !== $this->piVars[ 'user' ][ 'password2' ] ) {
            
            // No match
            $this->_formErrors->password2 = $this->_lang->errorPasswordNoMatch;
        }
        
        // Validation status
        $status = true;
        
        // Process each property of the form errors object
        foreach( $this->_formErrors as $field => $errorString ) {
            
            // Creates a new DIV tag
            $error            = new tx_oop_Xhtml_Tag( 'div' );
            $error[ 'class' ] = $this->_cssClass( 'error' );
            
            // Adds the error text
            $error->addTextData( $errorString );
            
            // The property now contains the displayable error message
            $this->_formErrors->$field = $error;
            
            // Errors were detected
            $status = false;
        }
        
        // Returns the validation status
        return $status;
    }
    
    /**
     * Creates the frontend user with its profile
     * 
     * @return  NULL
     */
    protected function _createUser()
    {
        // Creates a new user and profile
        $user    = new tx_oop_Database_Object( self::TABLE_USERS );
        $profile = new tx_oop_Database_Object( self::TABLE_PROFILES );
        
        // Fills the user data
        $user->pid       = $this->_storagePage;
        $user->usergroup = $this->_userGroup;
        $user->username  = $this->piVars[ 'user' ][ 'email' ];
        $user->email     = $this->piVars[ 'user' ][ 'email' ];
        $user->password  = $this->piVars[ 'user' ][ 'password1' ];
        $user->name      = $this->piVars[ 'user' ][ 'firstName' ] . ' ' . $this->piVars[ 'user' ][ 'lastName' ];
        $user->title     = $this->piVars[ 'user' ][ 'nickname' ];
        $user->disable   = 1;
        
        // Inserts the user in the database
        $user->commit();
        
        // Fills the profile data
        $profile->pid                  = $this->_storagePage;
        $profile->id_fe_users          = $user->uid;
        $profile->firstname            = $this->piVars[ 'user' ][ 'firstName' ];
        $profile->lastname             = $this->piVars[ 'user' ][ 'lastName' ];
        $profile->nickname             = $this->piVars[ 'user' ][ 'nickname' ];
        $profile->id_tx_netdata_states = $this->piVars[ 'user' ][ 'state' ];
        $profile->id_tx_netdata_cities = $this->piVars[ 'user' ][ 'city' ];
        $profile->phone                = $this->piVars[ 'user' ][ 'phone' ];
        $profile->address              = $this->piVars[ 'user' ][ 'address' ];
        $profile->age                  = $this->piVars[ 'user' ][ 'age' ];
        $profile->zip                  = $this->piVars[ 'user' ][ 'zip' ];
        $profile->confirm_hash         = $this->_confirmHash;
        
        // Inserts the profile in the database
        $profile->commit();
        
        $movies   = self::$_tsfe->fe_user->getKey( 'ses', 'movieUserId' );
        $pictures = self::$_tsfe->fe_user->getKey( 'ses', 'imageUserId' );
        
        if( $movies ) {
            
            self::$_t3Db->exec_UPDATEquery( 'tx_ymvideo_xml', 'usr_id=' . self::$_t3Db->fullQuoteStr( $movies, 'tx_ymvideo_xml' ), array( 'usr_id' => $user->uid ) );
            self::$_tsfe->fe_user->setKey( 'ses', 'movieUserId', '0' );
        }
        
        if( $pictures ) {
            
            self::$_t3Db->exec_UPDATEquery( 'tx_image_xml', 'usr_id=' . self::$_t3Db->fullQuoteStr( $pictures, 'tx_image_xml' ), array( 'usr_id' => $user->uid ) );
            self::$_tsfe->fe_user->setKey( 'ses', 'imageUserId', '0' );
        }
    }
    
    /**
     * Sends a confirmation email to the user
     * 
     * @return  NULL
     */
    protected function _sendConfirmationEmail()
    {
        // Creates the link for the account activation
        $confirmLink = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) . $this->cObj->typoLink_URL(
            array(
                'parameter'        => self::$_tsfe->id,
                'useCacheHash'     => 0,
                'no_cache'         => 1,
                'additionalParams' => '&' . __CLASS__ . '[confirmUser]=' . $this->_confirmHash
            )
        );
        
        // Full email message, with the variables replaced with the user data
        $message = preg_replace(
            array(
                '/\${firstName}/',
                '/\${lastName}/',
                '/\${confirmLink}/',
                '/\${username}/',
                '/\${password}/'
            ),
            array(
                $this->piVars[ 'user' ][ 'firstName' ],
                $this->piVars[ 'user' ][ 'lastName' ],
                $confirmLink,
                $this->piVars[ 'user' ][ 'email' ],
                $this->piVars[ 'user' ][ 'password1' ]
            ),
            $this->_emailMessage
        );
        
        // Email headers
        $headers = array(
            'From: '     . $this->_emailFromName . ' <' . $this->_emailFrom . '>',
            'Reply-To: ' . $this->_emailReplyTo
        );
        
        // Sends the confirmation email
        t3lib_div::plainMailEncoded(
            $this->piVars[ 'user' ][ 'email' ],
            $this->_emailSubject,
            $message,
            implode( chr( 10 ), $headers )
        );
    }
    
    /**
     * Activates the user account
     * 
     * @param   string                              The confirmation hash
     * @param   tx_oop_Frontend_Template_Section    The template section for the output
     * @return  NULL
     */
    protected function _confirmUser( $hash, $tmpl )
    {
        // Gets the available profiles for the confirmation hash
        $profiles = tx_oop_Database_Object::getObjectsByFields(
            self::TABLE_PROFILES,
            array(
                'pid'          => $this->_storagePage,
                'confirm_hash' => $hash
            )
        );
        
        // Sets the title in the template
        $tmpl->confirmTitle = $this->_lang->confirmTitle;
        
        // Checks if users were found
        if( !count( $profiles ) ) {
            
            // Error - No such user
            $tmpl->confirmText = $this->_lang->confirmError;
            return;
        }
        
        // Gets the user profile
        $profile = array_shift( $profiles );
        
        // Unhide the frontend user
        self::$_t3Db->exec_UPDATEquery(
            self::TABLE_USERS,
            'uid=' . $profile->id_fe_users,
            array( 'disable' => 0, 'tstamp' => time() )
        );
        
        // Updates the profile
        $profile->confirm_hash = '';
        $profile->commit();
        
        // Gets the frontend user object
        $user = new tx_oop_Database_Object( self::TABLE_USERS, $profile->id_fe_users );
        
        // Logs the current user
        $this->_feLogin( $user );
        
        // Adds the confirmation text in the template
        $tmpl->confirmText = sprintf( $this->_lang->confirmText, $user->username );
    }
    
    /**
     * Checks the plugin flexform settings
     * 
     * @param   tx_oop_Xhtml_Tag    The plugin's content container
     * @return  boolean             True if the settings are OK, otherwise false
     */
    protected function _checkFlexformSettings( tx_oop_Xhtml_Tag $content )
    {
        if( !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'storagePage' ][ 'vDEF' ] ) ) {
            
            $content->strong = $this->_lang->errorNoStoragePage;
            return false;
        }
        if( !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'terms' ][ 'vDEF' ] ) ) {
            
            $content->strong = $this->_lang->errorNoTermsPage;
            return false;
        }
        
        if( !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'userGroup' ][ 'vDEF' ] ) ) {
            
            $content->strong = $this->_lang->errorNoUserGroup;
            return false;
        }
        
        if( !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'subject' ][ 'vDEF' ] ) ) {
            
            $content->strong = $this->_lang->errorNoEmailSubject;
            return false;
        }
        
        if( !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'from' ][ 'vDEF' ] ) ) {
            
            $content->strong = $this->_lang->errorNoEmailFrom;
            return false;
        }
        
        if( !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'fromName' ][ 'vDEF' ] ) ) {
            
            $content->strong = $this->_lang->errorNoEmailFromName;
            return false;
        }
        
        if( !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'replyTo' ][ 'vDEF' ] ) ) {
            
            $content->strong = $this->_lang->errorNoEmailReplyTo;
            return false;
        }
        
        if( !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'message' ][ 'vDEF' ] ) ) {
            
            $content->strong = $this->_lang->errorNoEmailMessage;
            return false;
        }
        
        $this->_storagePage   = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'storagePage' ][ 'vDEF' ];
        $this->_termsPage     = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'terms' ][ 'vDEF' ];
        $this->_userGroup     = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'userGroup' ][ 'vDEF' ];
        $this->_emailSubject  = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'subject' ][ 'vDEF' ];
        $this->_emailFrom     = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'from' ][ 'vDEF' ];
        $this->_emailFromName = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'fromName' ][ 'vDEF' ];
        $this->_emailReplyTo  = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'replyTo' ][ 'vDEF' ];
        $this->_emailMessage  = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sMAILER' ][ 'lDEF' ][ 'message' ][ 'vDEF' ];
        
        return true;
    }
    
    /**
     * Gets the plugin content
     * 
     * @param   tx_oop_Xhtml_Tag    The plugin's content container
     * @return  NULL
     */
    protected function _getPluginContent( tx_oop_Xhtml_Tag $content )
    {
        // Checks the plugin settings
        if( $this->_checkFlexformSettings( $content ) === false ) {
            
            // Settings error
            return;
        }
        
        if( !isset( $this->conf[ 'templateFile' ] ) ) {
            
            $content->strong = $this->_lang->errorTmplNotDefined;
            return;
        }
        
        try {
            
            $template = $this->_getFrontendTemplate( $this->conf[ 'templateFile' ] );
            
        } catch( tx_oop_Frontend_Template_Exception $e ) {
            
            if( $e->getCode() === tx_oop_Frontend_Template_Exception::EXCEPTION_TEMPLATE_NOT_FOUND ) {
                
                $content->strong = sprintf( $this->_lang->errorTmplNotFound, $this->conf[ 'templateFile' ] );
                return;
            }
            
            throw $e;
        }
        
        if( isset( $this->piVars[ 'confirmUser' ] ) ) {
            
            $this->_confirmUser( $this->piVars[ 'confirmUser' ], $template->confirmation );
            
        } else {
            
            $this->_includeJQuery();
            
            $GLOBALS[ 'TSFE' ]->additionalHeaderData[ __CLASS__ ] = '<script type="text/javascript" charset="utf-8" src="'
                                                                  . t3lib_extMgm::siteRelPath( $this->extKey )
                                                                  . 'res/js/scripts.js"></script>';
            
            $this->_formErrors = new stdClass();
            
            if( isset( $this->piVars[ 'submit' ] ) && $this->_checkRegistrationForm() ) {
                
                $this->_confirmHash = md5( uniqid( microtime(), true ) );
                
                $this->_createUser();
                $this->_sendConfirmationEmail();
                
                $template->userFormConfirm->confirmTitle = $this->_lang->formConfirmTitle;
                $template->userFormConfirm->confirmText  = $this->_lang->formConfirmText;
                
            } else {
                
                $this->_registrationForm( $template->userForm );
            }
        }
        
        $content->div = ( string )$template;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ym_register/pi1/class.tx_ymregister_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ym_register/pi1/class.tx_ymregister_pi1.php']);
}
