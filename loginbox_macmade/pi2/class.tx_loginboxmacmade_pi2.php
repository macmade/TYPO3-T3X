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
 * @version     1.1
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *      98:     function main( $content, $conf )
 * 
 *              TOTAL FUNCTIONS: 1
 */

// Typo3 FE plugin class
require_once(PATH_tslib . 'class.tslib_pibase.php');

// Developer API class
require_once(t3lib_extMgm::extPath('api_macmade') . 'class.tx_apimacmade.php');

class tx_loginboxmacmade_pi2 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_loginboxmacmade_pi2';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi2/class.tx_loginboxmacmade_pi2.php';
    
    // The extension key
    var $extKey             = 'loginbox_macmade';
    
    // Version of the Developer API required
    var $apimacmade_version = 4.4;
    
    // Configuration array
    var $conf               = array();
    
    // Developer API instance
    var $api                = NULL;
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_loginboxmacmade_pi2', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param		string      $content			The content object
     * @param		array       $conf				The TS setup
     * @return		string      The content of the plugin
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
        
        // Template load and init
        $this->api->fe_initTemplate( $this->conf[ 'template' ] );
        
        // Template markers
        $templateMarkers = array();
        
        // Form URL
        $formUrl = $this->api->fe_linkTP_keepPIvars_url(
            array(),
            0,
            0,
            $this->conf[ 'loginPage' ]
        );
        
        // Overwriting template markers
        $templateMarkers[ '###FORM_URL###' ]     = $formUrl;
        $templateMarkers[ '###FORM_ENCTYPE###' ] = $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ];
        $templateMarkers[ '###EXT_PATH###' ]     = t3lib_extMgm::extRelPath( 'loginbox_macmade' );
        
        if( $GLOBALS[ 'TSFE' ]->loginUser ) {
            
            // Template section to render
            $section = '###LOGOUT###';
            
            // Redirection
            if( empty( $this->conf[ 'redirectLogout' ] ) ) {
                
                $redirect = $this->api->fe_linkTP_keepPIvars_url();
                
            } else {
                
                $redirect = $this->api->fe_linkTP_keepPIvars_url(
                    array(),
                    0,
                    0,
                    $this->conf[ 'redirectLogout' ]
                );
            }
            
            // Overwriting template markers
            $templateMarkers[ '###USERNAME###' ]      = $GLOBALS[ 'TSFE' ]->fe_user->user[ 'username' ];
            $templateMarkers[ '###HIDDEN_FIELDS###' ] = '<input name="logintype" type="hidden" value="logout" /><input name="redirect_url" type="hidden" value="'
                                                      . $redirect
                                                      . '" /><input name="pid" type="hidden" value="'
                                                      . $this->conf[ 'feUsersPID' ]
                                                      . '" />';
            
        } else {
            
            // Template section to render
            $section = '###LOGIN###';
            
            // Redirection
            if( empty( $this->conf[ 'redirectLogin' ] ) ) {
                
                $redirect = $this->api->fe_linkTP_keepPIvars_url();
                
            } else {
                
                $redirect = $this->api->fe_linkTP_keepPIvars_url(
                    array(),
                    0,
                    0,
                    $this->conf[ 'redirectLogin' ]
                );
            }
            
            // Overwriting template markers
            $templateMarkers[ '###USERNAME###' ]      = 'user';
            $templateMarkers[ '###PASSWORD###' ]      = 'pass';
            $templateMarkers[ '###HIDDEN_FIELDS###' ] = '<input name="logintype" type="hidden" value="login" /><input name="redirect_url" type="hidden" value="'
                                                      . $redirect
                                                      . '" /><input name="pid" type="hidden" value="'
                                                      . $this->conf[ 'feUsersPID' ]
                                                      . '" />';
            
            // Checks for MD5 support
            if( $this->conf[ 'kb_md5fepw' ] && t3lib_extMgm::isLoaded( 'kb_md5fepw' ) ) {
                
                // Includes the MD5 script
                $this->api->fe_includeWebToolKitJs( 'md5' );
                
                // Adds the challenge JS code
                $this->challengeJsCode();
                
                // Submit action
                $onSubmit                            = $this->prefixId
                                                     . '_superChallengedPassword( this ); return true;';
                
                // Form URL
                $templateMarkers[ '###FORM_URL###' ] = $formUrl
                                                     . '" onsubmit="'
                                                     . $onSubmit;
                
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
                
                // Adds the challenge hidden fields
                $templateMarkers[ '###HIDDEN_FIELDS###' ] .= '<input name="challenge" type="hidden" value="'
                                                          .  $challenge
                                                          .  '" />';
            }
        }
        
        // Return section
        return $this->pi_wrapInBaseClass(
            $this->api->fe_renderTemplate(
                $templateMarkers,
                $section
            )
        );
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
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/loginbox_macmade/pi2/class.tx_loginboxmacmade_pi2.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/loginbox_macmade/pi2/class.tx_loginboxmacmade_pi2.php']);
}
