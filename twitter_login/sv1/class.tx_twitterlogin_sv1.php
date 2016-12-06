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

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Twitter authentication service for TYPO3
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  twitter_login
 */
class tx_twitterlogin_sv1 extends tx_sv_authbase
{
    /**
     * Whether the static variables are set
     */
    private static $_hasStatic  = false;
    
    /**
     * Whether to stop following authentication services
     */
    protected static $_stopAuth = false;
    
    /**
     * 
     */
    public function __construct()
    {
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
        // Checks for the extension's configuration
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'twitter_login' ] ) ) {
            
            // Gets the extension's configuration array
            $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'twitter_login' ] );
            
            // Checks if the  following authentication services must be allowed or not
            if( isset( $extConf[ 'stopAuth' ] ) ) {
                
                // Stores the configuration value
                self::$_stopAuth = ( boolean )$extConf[ 'stopAuth' ];
            }
        }
        
        // Static variables are set
        self::$_hasStatic = true;
    }
    
    /**
     * 
     */
    public function getUser()
    {
        return tx_twitterlogin_authentication::getAuthenticationObject( $this );
    }
    
    /**
     * 
     */
    public function authUser( $user )
    {
        // Return statuses
        $status        = 0;
        $errorStatus   = ( self::$_stopAuth === false ) ? 100 : 0;
        $successStatus = 200;
        
        // Checks if we have a Twitter user
        if( is_object( $user ) && ( $user instanceof tx_twitterlogin_authentication ) && $user->authenticateUser() ) {
            
            // User has authenticated via Twitter
            return $successStatus;
        }
        
        // Not a Twitter user
        return $errorStatus;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_login/sv1/class.tx_twitterlogin_sv1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_login/sv1/class.tx_twitterlogin_sv1.php']);
}
