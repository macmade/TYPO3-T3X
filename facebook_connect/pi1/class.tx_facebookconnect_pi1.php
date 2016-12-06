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

// Includes the Facebook API
require_once( t3lib_extMgm::extPath( 'facebook_connect' ) . 'lib/facebook-platform/facebook.php' );

/**
 * Frontend plugin PI1 for the 'facebook_connect' extension.
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  facebook_connect
 */
class tx_facebookconnect_pi1 extends tx_oop_Plugin_Base
{
    /**
     * 
     */
    const TABLE_USERS            = 'fe_users';
    
    /**
     * 
     */
    const TABLE_GROUPS           = 'fe_groups';
    
    /**
     * 
     */
    const TABLE_PROFILES         = 'tx_facebookconnect_users';
    
    /**
     * 
     */
    protected $_appKey           = '';
    
    /**
     * 
     */
    protected $_appSecret        = '';
    
    /**
     * 
     */
    protected $_xdReceiver       = '';
    
    /**
     * 
     */
    protected $_storagePage      = 0;
    
    /**
     * 
     */
    protected $_userGroup        = 0;
    
    /**
     * 
     */
    protected $_onLogin          = 0;
    
    /**
     * 
     */
    protected $_afterLogin       = 0;
    
    /**
     * 
     */
    protected $_facebook         = NULL;
    
    /**
     * 
     */
    protected $_template         = NULL;
    
    /**
     * 
     */
    protected $_user             = NULL;
    
    /**
     * 
     */
    protected $_profile          = NULL;
    
    /**
     * 
     */
    protected $_formErrors       = NULL;
    
    /**
     * 
     */
    protected $_noNewUserMessage = true;
    
    /**
     * 
     */
    protected function _checkSettings( tx_oop_Xhtml_Tag $content )
    {
        // Checks the TypoScript configuration
        if(    !isset( $this->conf[ 'facebookJs' ] )
            || !isset( $this->conf[ 'connectButton' ] )
            || !isset( $this->conf[ 'crossDomainCommunicationChannelFile' ] )
            || !isset( $this->conf[ 'templateFile' ] )
            || !isset( $this->conf[ 'btn' ] )
        ) {
            
            // Error - Invalid TypoScript settings
            $content->strong = $this->_lang->errorTs;
            return false;
        }
        
        // Checks the flexform settings
        if(    !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'pages' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'userGroup' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'appKey' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'appSecret' ][ 'vDEF' ] )
            || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'noNewUserMessage' ][ 'vDEF' ] )
        ) {
            
            // Error - Invalid flexform settings
            $content->strong = $this->_lang->errorFlex;
            return false;
        }
        
        // Gets the absolute path of the cross domain communication channel file
        $xdReceiver = t3lib_div::getFileAbsFileName( $this->conf[ 'crossDomainCommunicationChannelFile' ] );
        
        // Checks if the file exists
        if( !$xdReceiver ) {
            
            // Error - The cross domain communication channel file does not exist
            $content->strong = $this->_lang->errorXdReceiver;
            return false;
        }
        
        // Relative path to the cross domain communication channel file
        $this->_xdReceiver = '/' . substr( $xdReceiver, strlen( PATH_site ) );
        
        // Stores the flexform settings
        $this->_appKey           = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'appKey' ][ 'vDEF' ];
        $this->_appSecret        = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'appSecret' ][ 'vDEF' ];
        $this->_noNewUserMessage = ( bool )$this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'noNewUserMessage' ][ 'vDEF' ];
        $this->_storagePage      = ( int )$this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'pages' ][ 'vDEF' ];
        $this->_userGroup        = ( int )$this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sDEF' ][ 'lDEF' ][ 'userGroup' ][ 'vDEF' ];
        $this->_onLogin          = ( int )$this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sREDIRECT' ][ 'lDEF' ][ 'onLogin' ][ 'vDEF' ];
        $this->_afterLogin       = ( int )$this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sREDIRECT' ][ 'lDEF' ][ 'afterLogin' ][ 'vDEF' ];
        
        // Configuration is OK
        return true;
    }
    
    /**
     * 
     */
    protected function _addJavaScriptCode()
    {
        // Creates the script tag for the Facebook JavaScript library
        $facebookJs              = new tx_oop_Xhtml_Tag( 'script' );
        $facebookJs[ 'type' ]    = 'text/javascript';
        $facebookJs[ 'charset' ] = 'utf-8';
        $facebookJs[ 'src' ]     = $this->conf[ 'facebookJs' ];
        
        // Adds the script tag to the page's headers
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ __CLASS__ . '_FeatureLoader' ] = ( string )$facebookJs;
        
        // Creates the script tag for the Facebook initialization
        $localJs              = new tx_oop_Xhtml_Tag( 'script' );
        $localJs[ 'type' ]    = 'text/javascript';
        $localJs[ 'charset' ] = 'utf-8';
        
        // Facebook initialization code
        $localJs->addTextData( '/* <![CDATA[ */' );
        $localJs->addTextData( 'FB.init( \'' . $this->_appKey . '\', \'' . $this->_xdReceiver .  '\' );' );
        $localJs->addTextData( '/* ]]> */' );
        
        // Adds the script tag to the page's headers
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ __CLASS__ . '_init' ] = ( string )$localJs;
    }
    
    /**
     * 
     */
    protected function _createConnectButton()
    {
        $conf = array(
            'file' => $this->conf[ 'btn' ]
        );
        
        if( $this->_onLogin > 0 ) {
            
            $callBack = 'function() { window.location = \'' . self::$_env->TYPO3_SITE_URL . $this->pi_getPageLink( $this->_onLogin, '', array( 'no_cache' => 1 ) ) . '\'; }';
            
        } else {
            
            $callBack = 'null';
        }
        
        $img = $this->cObj->IMAGE( $conf );
        
        $link           = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'href' ] = 'javascript:FB.Connect.requireSession( ' . $callBack . ', true );';
        
        $link->addTextData( $img );
        
        return $link;
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
            
            // Do not show the connect button is a user is already logged
            return;
        }
        
        // Adds the required JavaScript code
        $this->_addJavaScriptCode();
        
        // Tries to get a Facebook user ID
        $this->_facebook = new Facebook( $this->_appKey, $this->_appSecret );
        
        // Gets the frontend template
        $this->_template = $this->_getFrontendTemplate( $this->conf[ 'templateFile' ] );
        
        // Creates a div tag for the connect button
        $button = new tx_oop_Xhtml_Tag( 'div' );
        
        // Adds a CSS class
        $this->_cssClass( 'button', $button );
        
        // Adds the Facebook connect button
        $button->addTextData( $this->_createConnectButton() );
        
        // Adds the button to the template section
        $this->_template->facebookConnect->button = $button;
        
        // Template section to render
        $section = 'facebookConnect';
        
        // Checks if a user is connected
        if( ( int )$this->_facebook->user > 0 ) {
            
            // Tries to get a profile (existing user)
            $profiles = tx_oop_Database_Object::getObjectsByFields( self::TABLE_PROFILES, array( 'facebook_uid' => $this->_facebook->user ) );
            
            // Checks if a profile was found
            if( count( $profiles ) ) {
                
                // Yes - Updates the Facebook user
                $this->_updateUserProfile();
                
                if( $this->_afterLogin > 0 && self::$_tsfe->id != $this->_afterLogin ) {
                    
                    // Checks if a FE user is already logged
                    if( $GLOBALS[ 'TSFE' ]->loginUser != 1 ) {
                        
                        // Creates the FE session
                        $this->_feLogin( $this->_user );
                    }
                    
                    header( 'Location: ' . t3lib_div::locationHeaderUrl( $this->pi_getPageLink( $this->_afterLogin, '', array( 'no_cache' => 1 ) ) ) );
                    exit();
                }
                
            } elseif( !$this->_noNewUserMessage ) {
                
                $this->_formErrors = new stdClass();
                
                // Checks if the email form was submitted
                if( isset( $this->piVars[ 'submit' ] ) &&  $this->_checkRegistrationForm() ) {
                    
                    // Yes - Creates the user profile
                    $this->_createUser();
                    
                    // Checks if a FE user is already logged
                    if( $GLOBALS[ 'TSFE' ]->loginUser != 1 ) {
                        
                        // Creates the FE session
                        $this->_feLogin( $this->_user );
                        
                        if( $this->_afterLogin > 0 && self::$_tsfe->id != $this->_afterLogin ) {
                            
                            header( 'Location: ' . t3lib_div::locationHeaderUrl( $this->pi_getPageLink( $this->_afterLogin, '', array( 'no_cache' => 1 ) ) ) );
                            exit();
                        }
                    }
                    
                } else {
                    
                    // Asks for the user email
                    $this->_askForInfos();
                    
                    // The email form must be rendered instead of the connect button
                    $section = 'infosForm';
                }
            }
        }
        
        // Renders the template section
        $content->div = $this->_template->$section;
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
        $this->_profile->facebook_uid = $this->_facebook->user;
    
        // Inserts the Facebook profile object
        $this->_profile->commit();
        
        // Update the Facebook user profile
        $this->_updateUserProfile();
        
        if( $userAlreadyExists == false ) {
                
            $ymProfile = new tx_oop_Database_Object( 'tx_ymregister_users' );
            
            $ymProfile->age                         = $this->piVars[ 'age' ];
            $ymProfile->nickname                    = $this->piVars[ 'nickname' ];
            $ymProfile->lastname                    = $this->_profile->last_name;
            $ymProfile->firstname                   = $this->_profile->first_name;
            $ymProfile->id_tx_facebookconnect_users = $this->_profile->uid;
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
        if( !is_object( $this->_profile ) ) {
            
            $profiles = tx_oop_Database_Object::getObjectsByFields( self::TABLE_PROFILES, array( 'facebook_uid' => $this->_facebook->user ) );
            
            if( !count( $profiles ) ) {
                
                return;
            }
            
            $this->_profile = array_shift( $profiles );
        }
        
        if( !is_object( $this->_user ) ) {
            
            $this->_user = new tx_oop_Database_Object( self::TABLE_USERS, $this->_profile->id_fe_users );
        }
        
        $profileHelp = new tx_facebookconnect_profileHelper( $this->_facebook, $this->_profile );
        
        try {
            
            $profileHelp->updateProfileInfos();
            
            $this->_user->name = $this->_profile->fullname;
            $this->_user->commit();
            
        } catch( Exception $e ) {}
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
    protected function _askForInfos()
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
        $section->formAction = $this->pi_getPageLink( self::$_tsfe->id, '', array( 'no_cache' => 1 ) );
        
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
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/facebook_connect/pi1/class.tx_facebookconnect_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/facebook_connect/pi1/class.tx_facebookconnect_pi1.php']);
}
