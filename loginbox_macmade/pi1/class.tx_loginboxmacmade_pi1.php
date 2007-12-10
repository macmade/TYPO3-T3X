<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (info@macmade.net)
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
 * Plugin 'LoginBox / macmade.net' for the 'loginbox_macmade' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *     112:     function main( $content, $conf )
 *     293:     function setDefaultLangValues
 *     325:     function setConfig
 *     375:     function forgotPassword
 * 
 *              TOTAL FUNCTIONS: 4
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once(
    t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php'
);

class tx_loginboxmacmade_pi1 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_loginboxmacmade_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_loginboxmacmade_pi1.php';
    
    // The extension key
    var $extKey             = 'loginbox_macmade';
    
    // Version of the Developer API required
    var $apimacmade_version = 3.2;
    
    // Storage for GET/POST variables
    var $GP                 = array();
    
    // Configuration array
    var $conf               = array();
    
    // Developer API instance
    var $api                = NULL;
    
    // Flexform data
    var $piFlexForm         = '';
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_loginboxmacmade_pi1', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param       string      $content            The content object
     * @param       array       $conf               The TS setup
     * @return      string      The content of the plugin
     * @see         setDefaultLangValues
     * @see         setConfig
     * @see         forgotPassword
     */
    function main( $content, $conf )
    {
        // Disable cache - DEBUG ONLY
        #$GLOBALS['TSFE']->set_no_cache();
        
        // New instance of the macmade.net API
        $this->api  = new tx_apimacmade( $this );
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Set default plufin variables
        $this->pi_setPiVarDefaults();
        
        // Load LOCAL_LANG values
        $this->pi_loadLL();
        
        // Set default 
        $this->setDefaultLangValues();
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Store GET/POST variables
        $this->GP[ 'logintype' ]    = t3lib_div::GPvar( 'logintype' );
        $this->GP[ 'redirect_url' ] = t3lib_div::GPvar( 'redirect_url' );
        
        // Storage
        $htmlCode = array();
        
        // Check if a FE user is logged
        if( $GLOBALS[ 'TSFE' ]->loginUser ) {
            
            // Check login type
            switch( $this->GP[ 'logintype' ] ) {
                
                // Success
                case 'login':
                    
                    // Current login state
                    $state = 'success';
                    break;
                
                // Status
                default:
                    
                    // Current login state
                    $state = 'status';
                    break;
            }
            
        } else {
            
            // Check login type
            switch( $this->GP[ 'logintype' ] ) {
                
                // Error
                case 'login':
                
                    // Current login state
                    $state = 'error';
                    break;
                
                // Logout
                case 'logout':
                    
                    // Current login state
                    $state = 'logout';
                    break;
                
                // Welcome
                default:
                    
                    // Current login state
                    $state = 'welcome';
                    break;
            }
        }
        
        // Check for a redirection
        if( ( $state == 'success' || $state == 'logout' ) && $this->GP[ 'redirect_url' ] ) {
            
            // Redirect user to same page
            header( 'Location: ' . t3lib_div::locationHeaderUrl( $this->pi_getPageLink( $this->GP[ 'redirect_url' ] ) ) );
            
        } elseif ( $state == 'success' && $this->conf[ 'redirect' ] ) {
            
            // Redirect link
            $redirectLink = $this->cObj->typoLink_URL(
                array(
                    'parameter'    => $this->conf[ 'redirect' ],
                    'useCacheHash' => 1
                )
            );
            
            // Redirect user to specified page
            header( 'Location: ' . t3lib_div::locationHeaderUrl( $redirectLink ) );
            
        } else {
            
            // Forgot password
            if( $this->piVars[ 'forgot' ] ) {
                
                // Content
                $htmlCode[] = $this->forgotPassword();
                
            } else {
                
                // Add header
                $htmlCode[] = $this->api->fe_makeStyledContent(
                    'h2',
                    'header',
                    $this->conf[ $state . '.' ][ 'header' ]
                );
                
                // Add message
                $htmlCode[] = $this->api->fe_makeStyledContent(
                    'div',
                    $state,
                    $this->conf[ $state . '.' ][ 'message' ]
                );
                
                // Checks for MD5 support
                if( $this->conf[ 'kb_md5fepw' ] && t3lib_extMgm::isLoaded( 'kb_md5fepw' ) ) {
                    
                    // Includes the MD5 script
                    $this->api->fe_includeWebToolKitJs( 'md5' );
                    
                    // Content
                    $loginBox = $this->api->fe_buildLoginBox(
                        $this->conf[ 'pid' ],
                        $this->conf[ 'loginBox.' ][ 'inputSize' ],
                        $this->conf[ 'loginBox.' ][ 'method' ],
                        $this->conf[ 'loginBox.' ][ 'target' ],
                        $this->conf[ 'loginBox.' ][ 'wrap' ],
                        $this->conf[ 'loginBox.' ][ 'layout' ],
                        'pi_loginbox_',
                        $this->conf[ 'permaLogin' ],
                        true
                    );
                    
                    // Checks for hidden fields
                    if( !isset( $loginBox[ 'ts' ][ 'hiddenFields.' ] ) ) {
                        
                        // Create array for hidden fields
                        $loginBox[ 'ts' ][ 'hiddenFields.' ] = array();
                    }
                    
                    // Challenge
                    $challenge = md5( time() . getmypid() );
                    
                    // Inserts the challenge in the database
                    $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery(
                        'tx_kbmd5fepw_challenge',
                        array(
                            'challenge' => $challenge,
                            'tstamp'    => time()
                        )
                    );
                    
                    // Adds the challenge field
                    $loginBox[ 'ts' ][ 'hiddenFields.' ][ 'challenge' ]             = 'TEXT';
                    $loginBox[ 'ts' ][ 'hiddenFields.' ][ 'challenge.' ]            = array();
                    $loginBox[ 'ts' ][ 'hiddenFields.' ][ 'challenge.' ][ 'value' ] = $challenge;
                    
                    // Gets the form code
                    $formCode   = $this->cObj->FORM( $loginBox[ 'ts' ] );
                    
                    // Adds the challenge JS code
                    $this->challengeJsCode();
                    
                    // Submit action
                    $onSubmit   = 'onsubmit="' . $this->prefixId . '_superChallengedPassword( this ); return true;"';
                    
                    // Adds the form
                    $htmlCode[] = preg_replace(
                        '/form action="[^"]+"/',
                        'form ' . $onSubmit . 'action="' . $loginBox[ 'action' ] . '"',
                        $formCode
                    );
                    
                } else {
                    
                    // Content
                    $htmlCode[] = $this->api->fe_buildLoginBox(
                        $this->conf[ 'pid' ],
                        $this->conf[ 'loginBox.' ][ 'inputSize' ],
                        $this->conf[ 'loginBox.' ][ 'method' ],
                        $this->conf[ 'loginBox.' ][ 'target' ],
                        $this->conf[ 'loginBox.' ][ 'wrap' ],
                        $this->conf[ 'loginBox.' ][ 'layout' ],
                        'pi_loginbox_',
                        $this->conf[ 'permaLogin' ]
                    );
                }
                
                // Forgot password link
                if( $this->conf[ 'forgotpassword' ] && ( $state == 'error' || $state == 'welcome' || $state == 'logout' ) ) {
                    
                    // Link
                    $forgotPwdLink = $this->cObj->typoLink(
                        $this->pi_getLL( 'forgotpassword.header' ),
                        array(
                            'parameter'        => $GLOBALS[ 'TSFE' ]->id,
                            'useCacheHash'     => 1,
                            'additionalParams' => $this->api->fe_typoLinkParams(
                                array(
                                    'forgot' => 1
                                )
                            )
                        )
                    );
                    
                    // Add link
                    $htmlCode[]    = $this->api->fe_makeStyledContent(
                        'div',
                        'forgotpasswordLink',
                        $forgotPwdLink
                    );
                }
            }
            
            // Return content
            return $this->pi_wrapInBaseClass(
                implode( chr( 10 ), $htmlCode )
            );
        }
    }
    
    /**
     * Create language labels
     * 
     * This function gets all the needed language labels and puts them into
     * the extension's config array.
     * 
     * @return      boolean
     */
    function setDefaultLangValues()
    {
        // Login states
        $states = array(
            'welcome',
            'success',
            'error',
            'status',
            'logout'
        );
        
        // Process each state
        foreach( $states as $value ) {
            
            // Get local lang values
            $this->conf[ $value . '.' ] = array(
                'header'  => $this->pi_getLL( $value . '.header' ),
                'message' => $this->pi_getLL( $value . '.message' ),
            );
        }
        
        return true;
    }
    
    /**
     * Creates final configuration array
     * 
     * This function is used to merge the plugin configuration array with the flexforms,
     * by providing a mapping array.
     * 
     * @return      boolean
     */
    function setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array(
            'pid'            => 'sDEF:pages',
            'forgotpassword' => 'sDEF:forgotpassword',
            'permaLogin'     => 'sDEF:permalogin',
            'redirect'       => 'sDEF:redirect',
            'kb_md5fepw'     => 'sDEF:kb_md5fepw',
            'welcome.'       => array(
                'header'  => 'sWELCOME:header',
                'message' => 'sWELCOME:message'
            ),
            'success.'       => array(
                'header'  => 'sSUCCESS:header',
                'message' => 'sSUCCESS:message'
            ),
            'error.'         => array(
                'header'  => 'sERROR:header',
                'message' => 'sERROR:message'
            ),
            'status.'        => array(
                'header'  => 'sSTATUS:header',
                'message' => 'sSTATUS:message'
            ),
            'logout.'        => array(
                'header'  => 'sLOGOUT:header',
                'message' => 'sLOGOUT:message'
            )
        );
        
        // Override TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf);
        return true;
    }
    
    /**
     * Forgot password feature.
     * 
     * This function creates the section for the forgot password feature
     * of the loginbox.
     * 
     * @return      string      The section for the forgot password feature
     */
    function forgotPassword()
    {
        // Checks for MD5 support
        if( $this->conf[ 'kb_md5fepw' ] && t3lib_extMgm::isLoaded( 'kb_md5fepw' ) ) {            
            
            // Requires the helper class
            require_once( t3lib_extMgm::extPath( 'kb_md5fepw' ) . 'class.tx_kbmd5fepw_funcs.php' );;
            
            // Generates a new password
            $newPassword = tx_kbmd5fepw_funcs::generatePassword( $this->conf[ 'kb_md5fepw.' ][ 'generatedPasswordLength' ] );
        }
        
        // Storage
        $htmlCode   = array();
        
        // Add header
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'h2',
            'header',
            $this->pi_getLL( 'forgotpassword.header' )
        );
        
        // Check email input
        if( $this->piVars[ 'email' ] ) {
            
            // Check valid email
            if( t3lib_div::validEmail( $this->piVars[ 'email' ] ) ) {
                
                // MySQL WHERE clause
                $whereClause = 'email=\''
                             . addslashes( trim( $this->piVars[ 'email' ] ) )
                             . '\' AND pid='
                             . intval( $this->conf[ 'pid' ] )
                             . $this->cObj->enableFields( 'fe_users' );
                
                // MySQL query
                $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                    '*',
                    'fe_users',
                    $whereClause
                );
                
                // Get FE user
                if( $user = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                    
                    // Success message
                    $message  = '<strong>'
                              . $this->pi_getLL( 'forgotpassword.success' )
                              . '</strong>';
                    
                    // Success flag
                    $success  = 1;
                    
                    // Get username or real name
                    $username = ( $user[ 'name' ] ) ? $user[ 'name' ] : $user[ 'username' ];
                    
                    // Password
                    $password = ( isset( $newPassword ) ) ? $newPassword : $user[ 'password' ];
                    
                    // Create email message
                    $mailMsg  = sprintf(
                        $this->pi_getLL( 'forgotpassword.mailmsg' ),
                        $username,
                        t3lib_div::getIndpEnv( 'TYPO3_HOST_ONLY' ),
                        $user[ 'username' ],
                        $password,
                        t3lib_div::getIndpEnv( 'TYPO3_REQUEST_HOST' )
                      . '/'
                      . $this->api->fe_linkTP_unsetPIvars_url(
                            array(),
                            array(
                                'forgot',
                                'email',
                                'submit'
                            )
                        )
                    );
                    
                    // Send mail
                    $this->cObj->sendNotifyEmail(
                        $mailMsg,
                        $username
                      . ' <'
                      . trim( $this->piVars[ 'email' ] )
                      . '>',
                        '',
                        $this->conf[ 'mailer.' ][ 'from' ],
                        $this->conf[ 'mailer.' ][ 'fromName' ],
                        $this->conf[ 'mailer.' ][ 'replyTo' ]
                    );
                    
                } else {
                    
                    // No user
                    $message = '<strong>'
                             . $this->pi_getLL( 'forgotpassword.error.nouser' )
                             . '</strong>';
                }
                
            } else {
                
                // Message
                $message = '<strong>'
                         . $this->pi_getLL( 'forgotpassword.error.unvalidemail' )
                         . '</strong>';
            }
        } else {
            
            // Message
            $message = $this->pi_getLL( 'forgotpassword.message' );
        }
        
        // Add message
        $htmlCode[] = $this->api->fe_makeStyledContent(
            'div',
            'forgotpassword',
            $message
        );
        
        // Check mail
        if( $success ) {
            
            // Add login box
            $htmlCode[] = $this->api->fe_buildLoginBox(
                $this->conf[ 'pid' ],
                $this->conf[ 'loginBox.' ][ 'inputSize' ],
                $this->conf[ 'loginBox.' ][ 'method' ],
                $this->conf[ 'loginBox.' ][ 'target' ],
                $this->conf[ 'loginBox.' ][ 'wrap' ],
                $this->conf[ 'loginBox.' ][ 'layout' ],
                'pi_loginbox_',
                $this->conf[ 'permaLogin' ]
            );
            
        } else {
        
            // Start form
            $htmlCode[] = '<form action="'
                        . htmlspecialchars( t3lib_div::getIndpEnv( 'REQUEST_URI' ) )
                        . '" method="post" enctype="'
                        . $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ]
                        . '">';
            
            // Start paragraph
            $htmlCode[] = $this->api->fe_makeStyledContent(
                'p',
                'forgotpasswordForm',
                false,
                1,
                0,
                1
            );
            
            // Input text
            $htmlCode[] = $this->pi_getLL( 'forgotpassword.email' )
                        . ':&nbsp;';
            
            // Input
            $htmlCode[] = '<input name="'
                        . $this->prefixId
                        . '[email]" type="text" size="20">&nbsp;';
            
            // Submit
            $htmlCode[] = '<input name="'
                        . $this->prefixId
                        . '[submit]" type="submit" value="'
                        . $this->pi_getLL('forgotpassword.send')
                        . '"></p>';
            
            // End form
            $htmlCode[] = '</form>';
        }
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Adds JavaScript Code.
     * 
     * This function adds the javascript code used to challenge the password
     * field of the loginbox.
     * 
     * @return      Void.
     */
    function challengeJsCode()
    {
        // Storage
        $jsCode   = array();
        
        // JS code
        $jsCode[] = 'function ' . $this->prefixId . '_superChallengedPassword( formObject )';
        $jsCode[] = '{';
        $jsCode[] = '    if( formObject.pass.value && formObject.user.value && formObject.challenge.value ) {';
        $jsCode[] = '        var md5Password       = MD5( formObject.pass.value );';
        $jsCode[] = '        var superChallenge    = formObject.user.value + ":" + md5Password + ":" + formObject.challenge.value';
        $jsCode[] = '        formObject.pass.value = MD5( superChallenge );';
        $jsCode[] = '        return true;';
        $jsCode[] = '    }';
        $jsCode[] = '    return false;';
        $jsCode[] = '}';
        
        // Adds JS code
        $GLOBALS[ 'TSFE' ]->setJS(
            $this->prefixId,
            implode( chr( 10 ), $jsCode )
        ); 
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/loginbox_macmade/pi1/class.tx_loginboxmacmade_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/loginbox_macmade/pi1/class.tx_loginboxmacmade_pi1.php']);
}
