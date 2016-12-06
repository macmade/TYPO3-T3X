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
 * Frontend plugin PI1 for the 'twitter_login' extension.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  twitter_login
 */
class tx_twitterlogin_pi1 extends tx_oop_Plugin_Base
{
    /**
     * 
     */
    const TABLE_USERS    = 'fe_users';
    
    /**
     * 
     */
    const TABLE_GROUPS   = 'fe_groups';
    
    /**
     * 
     */
    const TABLE_PROFILES = 'tx_twitterlogin_users';
    
    /**
     * Whether the static variables are set
     */
    private static $_hasStatic = false;
    
    /**
     * 
     */
    protected static $_utils   = NULL;
    
    /**
     * 
     */
    public $_consumerKey       = '';
    
    /**
     * 
     */
    public $_consumerSecret    = '';
    
    /**
     * 
     */
    public $_callback          = '';
    
    /**
     * 
     */
    protected $_storagePage    = 0;
    
    /**
     * 
     */
    protected $_userGroup      = 0;
    
    /**
     * 
     */
    protected $_template       = NULL;
    
    /**
     * 
     */
    protected $_user           = NULL;
    
    /**
     * 
     */
    protected $_profile        = NULL;
    
    /**
     * 
     */
    protected $_oAuth          = NULL;
    
    /**
     * 
     */
    protected $_extConf        = array();
    
    /**
     * 
     */
    protected $_redirect        = 0;
    
    /**
     * 
     */
    public function __construct()
    {
        // Calls the parent constructor
        parent::__construct();
        
        // Checks if the static variables are set
        if( self::$_hasStatic === false ) {
            
            // Sets the needed static variables
            self::_setStaticVars();
        }
    }
    
    /**
     * 
     */
    private static function _setStaticVars()
    {
        // Gets the instance of the Twitter utilities
        self::$_utils     = tx_twitterlogin_utils::getInstance();
        
        // The static variables are set
        self::$_hasStatic = true;
    }
    
    /**
     * 
     */
    protected function _checkSettings( tx_oop_Xhtml_Tag $content )
    {
        // Checks the TypoScript configuration
        if(    !isset( $this->conf[ 'button' ] )
            || !isset( $this->conf[ 'templateFile' ] )
        ) {
            
            // Error - Invalid TypoScript settings
            $content->strong = $this->_lang->errorTs;
            return false;
        }
        
        // Checks the flexform settings
        if(    !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'storagePage' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'userGroup' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sOAUTH' ][ 'lDEF' ][ 'consumerKey' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sOAUTH' ][ 'lDEF' ][ 'consumerSecret' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sOAUTH' ][ 'lDEF' ][ 'callback' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sOAUTH' ][ 'lDEF' ][ 'afterLoginRedirect' ][ 'vDEF' ] )
        ) {
            
            // Error - Invalid flexform settings
            $content->strong = $this->_lang->errorFlex;
            return false;
        }
        
        // Stores the flexform settings
        $this->_consumerKey    = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sOAUTH' ][ 'lDEF' ][ 'consumerKey' ][ 'vDEF' ];
        $this->_consumerSecret = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sOAUTH' ][ 'lDEF' ][ 'consumerSecret' ][ 'vDEF' ];
        $this->_callback       = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sOAUTH' ][ 'lDEF' ][ 'callback' ][ 'vDEF' ];
        $this->_storagePage    = ( int )$this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'storagePage' ][ 'vDEF' ];
        $this->_userGroup      = ( int )$this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'userGroup' ][ 'vDEF' ];
        $this->_redirect       = ( int )$this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sOAUTH' ][ 'lDEF' ][ 'afterLoginRedirect' ][ 'vDEF' ];
        
        // Gets the extension configuration
        $this->_extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ $this->extKey ] );
        
        // Configuration is OK
        return true;
    }
    
    /**
     * 
     */
    protected function _getPluginContent( tx_oop_Xhtml_Tag $content )
    {
        // Checks the plugin settings (TS & flex)
        if( !$this->_checkSettings( $content ) ) {
            
            // Invalid settings
            return;
        }
        
        // Checks if a FE user is logged
        if( $GLOBALS[ 'TSFE' ]->loginUser == 1 ) {
            
            return;
        }
        
        $this->_oAuth = new tx_oop_OAuth_Authentication( $this->_consumerKey, $this->_consumerSecret );
        
        $this->_oAuth->setRequestUrl( 'http://twitter.com/oauth/request_token' );
        $this->_oAuth->setAuthorizationUrl( 'http://twitter.com/oauth/authenticate' );
        $this->_oAuth->setAccessUrl( 'http://twitter.com/oauth/access_token' );
        $this->_oAuth->setCallbackUrl( $this->_callback );
        
        // Gets the frontend template
        $this->_template = $this->_getFrontendTemplate( $this->conf[ 'templateFile' ] );
        
        if( isset( $this->piVars[ 'confirm' ] ) || isset( $this->piVars[ 'submit' ] ) ) {
            
            $this->_confirmTwitterAccess( $content );
            
        } else {
            
            $this->_createButton( $content );
        }
    }
    
    /**
     * 
     */
    protected function _confirmTwitterAccess( tx_oop_Xhtml_Tag $content )
    {
        $accessToken = ( isset( $this->piVars[ 'accessToken' ] ) ) ? $this->piVars[ 'accessToken' ] : $this->_oAuth->getAccessToken();
        
        if( !$accessToken ) {
            
            return;
        }
        
        // Tries to get a profile (existing user)
        $profiles = tx_oop_Database_Object::getObjectsWhere( self::TABLE_PROFILES, 'deleted=0 AND access_token=' . self::$_t3Db->fullQuoteStr( $accessToken, self::TABLE_PROFILES ) );
        
        // Checks if a profile was found
        if( count( $profiles ) ) {
            
            $profile = array_shift( $profiles );
            
            $this->_user = new tx_oop_Database_Object( self::TABLE_USERS, $profile->id_fe_users );
            
            // Yes - Updates the Twitter user
            #$this->_updateUserProfile();
            
            // Checks if a FE user is already logged
            if( $GLOBALS[ 'TSFE' ]->loginUser != 1 ) {
                
                // Creates the FE session
                $this->_feLogin( $this->_user );
            }
            
            header( 'Location: ' . t3lib_div::locationHeaderUrl( $this->pi_getPageLink( $this->_redirect, '', array( 'no_cache' => 1 ) ) ) );
            exit();
            
        } else {
            
            $this->_formErrors = new stdClass();
            
            // Checks if the email form was submitted
            if( isset( $this->piVars[ 'submit' ] ) && $this->_checkRegistrationForm() ) {
                
                // Yes - Creates the user profile
                $this->_createUser();
                
                // Checks if a FE user is already logged
                if( $GLOBALS[ 'TSFE' ]->loginUser != 1 ) {
                    
                    // Creates the FE session
                    $this->_feLogin( $this->_user );
                }
                
                header( 'Location: ' . t3lib_div::locationHeaderUrl( $this->pi_getPageLink( $this->_redirect, '', array( 'no_cache' => 1 ) ) ) );
                exit();
                
            } else {
                
                // Asks for the user email
                $this->_askForInfos( $accessToken );
                
                $content->div = $this->_template->infosForm;
            }
        }
    }
    
    /**
     * 
     */
    protected function _createButton( tx_oop_Xhtml_Tag $content )
    {
        $this->_oAuth->getRequestToken();
        
        $imageConf = array(
            'file' => $this->conf[ 'button' ]
        );
        
        $link           = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'href' ] = $this->_oAuth->getAuthorizationHref();
        
        $link->addTextData( $this->cObj->IMAGE( $imageConf ) );
        
        $this->_template->twitterConnect->button = $link;
        
        $content->div = $this->_template->twitterConnect;
    }
    
    /**
     * 
     */
    protected function _createUser()
    {
        $userAlreadyExists = false;
        
        $users = tx_oop_Database_Object::getObjectsByFields( self::TABLE_USERS, array( 'email' => $this->piVars[ 'email' ] ) );
        
        if( count( $users ) ) {
            
            $this->_user       = array_shift( $users );
            $userAlreadyExists = true;
            
        } else {
        
            // Creates the FE user object
            $this->_user            = new tx_oop_Database_Object( self::TABLE_USERS );
            $this->_user->pid       = $this->_storagePage;
            $this->_user->usergroup = $this->_userGroup;
            $this->_user->username  = $this->piVars[ 'email' ];
            $this->_user->email     = $this->piVars[ 'email' ];
            $this->_user->title     = $this->piVars[ 'nickname' ];
            $this->_user->password  = md5( uniqid( microtime(), true ) );
            
            // Inserts the FE user object
            $this->_user->commit();
        }
        
        // Creates the Facebook profile object
        $this->_profile               = new tx_oop_Database_Object( self::TABLE_PROFILES );
        $this->_profile->pid          = $this->_storagePage;
        $this->_profile->id_fe_users  = $this->_user->uid;
        $this->_profile->access_token = $this->piVars[ 'accessToken' ];
        
        // Inserts the Facebook profile object
        $this->_profile->commit();
        
        // Update the Facebook user profile
        #$this->_updateUserProfile();
        
        if( $userAlreadyExists == false ) {
                
            $ymProfile = new tx_oop_Database_Object( 'tx_ymregister_users' );
            
            $ymProfile->age                         = $this->piVars[ 'age' ];
            $ymProfile->nickname                    = $this->piVars[ 'nickname' ];
            #$ymProfile->lastname                    = $this->_profile->last_name;
            #$ymProfile->firstname                   = $this->_profile->first_name;
            #$ymProfile->id_tx_facebookconnect_users = $this->_profile->uid;
            $ymProfile->id_fe_users                 = $this->_user->uid;
            $ymProfile->pid                         = $this->_storagePage;
            
            $ymProfile->commit();
        }
    }
    
    /**
     * 
     */
    protected function _updateUserProfile()
    {
        
    }
    
    /**
     * 
     */
    protected function _checkRegistrationForm()
    {
        // Email is required, must be valid, and must be unique
        if( !isset( $this->piVars[ 'email' ] ) || !$this->piVars[ 'email' ] ) {
            
            // No value
            $this->_formErrors->email = $this->_lang->errorFieldRequired;
            
        } elseif( !t3lib_div::validEmail( $this->piVars[ 'email' ] ) ) {
            
            // Invalid email
            $this->_formErrors->email = $this->_lang->errorInvalidEmail;
        }
        
        // Age is required and must be numeric
        if( !isset( $this->piVars[ 'age' ] ) || !$this->piVars[ 'age' ] ) {
            
            // No value
            $this->_formErrors->age = $this->_lang->errorFieldRequired;
            
        } elseif( !is_numeric( $this->piVars[ 'age' ] ) ) {
            
            // Invalid value
            $this->_formErrors->age = $this->_lang->errorAgeNotNumeric;
        }
        
        // Nickname must be unique if specified
        if( isset( $this->piVars[ 'nickname' ] ) && $this->piVars[ 'nickname' ] ) {
            
            // Tries to get a user with the same nickname
            $users = self::$_db->getRecordsByFields(
                self::TABLE_USERS,
                array(
                    'pid'   => $this->_storagePage,
                    'title' => $this->piVars[ 'nickname' ]
                )
            );
            
            // Checks if we already have a user
            if( count( $users ) ) {
            
                // Nickname is already registered
                $this->_formErrors->nickname = $this->_lang->errorNicknameExist;
            }
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
     * 
     */
    protected function _askForInfos( $token )
    {
        // Gets the template section
        $section  = $this->_template->infosForm;
        
        // Sets the text labels
        $section->title         = $this->_lang->infosFormTitle;
        $section->message       = $this->_lang->infosFormMessage;
        $section->labelEmail    = $this->_lang->infosFormLabelEmail;
        $section->labelAge      = $this->_lang->infosFormLabelAge;
        $section->labelNickname = $this->_lang->infosFormLabelNickname;
        $section->labelSubmit   = $this->_lang->infosFormLabelSubmit;
        
        // Sets the form action parameter
        $section->formAction = $this->pi_getPageLink( self::$_tsfe->id, '', array( 'no_cache' => 1, __CLASS__ . '[accessToken]' => $token ) );
        
        // Creates the hidden field for the form action detection
        $action                = new tx_oop_Xhtml_Tag( 'input' );
        $action[ 'type' ]      = 'hidden';
        $action[ 'name' ]      = __CLASS__ . '[submit]';
        $action[ 'value' ]     = 1;
        $section->hiddenFields = $action;
        
        $section->nameFieldEmail = __CLASS__ . '[email]';
        $section->nameFieldAge = __CLASS__ . '[age]';
        $section->nameFieldNickname = __CLASS__ . '[nickname]';
        
        $section->errorEmail    = $this->_formErrors->email;
        $section->errorAge      = $this->_formErrors->age;
        $section->errorNickname = $this->_formErrors->nickname;
        
        $section->valueEmail    = ( isset( $this->piVars[ 'email' ] ) )    ? $this->piVars[ 'email' ]    : '';
        $section->valueAge      = ( isset( $this->piVars[ 'age' ] ) )      ? $this->piVars[ 'age' ]      : '';
        $section->valueNickname = ( isset( $this->piVars[ 'nickname' ] ) ) ? $this->piVars[ 'nickname' ] : '';
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_login/pi1/class.tx_twitterlogin_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_login/pi1/class.tx_twitterlogin_pi1.php']);
}
