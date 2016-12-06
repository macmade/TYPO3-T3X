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
 * Twitter authentication for TYPO3
 *
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  twitter_login
 */
final class tx_twitterlogin_authentication implements ArrayAccess, Iterator
{
    /**
     * The HTTP protocol version for the request on the Twitter API
     */
    const HTTP_VERSION  = '1.1';
    
    /**
     * The hostname for the Twitter API
     */
    const TWITTER_HOST  = 'twitter.com';
    
    /**
     * The port for the Twitter API connection
     */
    const TWITTER_PORT  = 80;
    
    /**
     * The URL of the Twitter REST service to verify the user's credidentials
     */
    const TWITTER_LOGIN = '/account/verify_credentials.xml';
    
    /**
     * Whether the static variables are set
     */
    private static $_hasStatic  = false;
    
    /**
     * Whether to use CURL to connect to the twitter API
     */
    protected static $_useCurl  = false;
    
    /**
     * The Twitter utilities
     */
    protected static $_utils    = NULL;
    
    /**
     * The TYPO3 frontend user object (active record, tx_oop_Database_Object)
     */
    protected $_typo3User       = NULL;
    
    /**
     * The Twitter user object (active record, tx_oop_Database_Object)
     */
    protected $_twitterUser     = NULL;
    
    /**
     * The authentication object (service)
     */
    protected $_auth            = NULL;
    
    /**
     * The name of the user trying to log-in
     */
    protected $_username        = '';
    
    /**
     * The password of the user trying to log-in
     */
    protected $_password        = '';
    
    /**
     * 
     */
    protected function __construct( tx_oop_Database_Object $typo3User, tx_oop_Database_Object $twitterUser, tx_sv_authbase $auth )
    {
        $this->_typo3User   = $typo3User;
        $this->_twitterUser = $twitterUser;
        $this->_auth        = $auth;
        $this->_username    = $auth->login[ 'uname' ];
        $this->_password    = $auth->login[ 'uident_text' ];
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        $name = ( string )$name;
        
        return ( isset( $this->_typo3User[ $name ] ) ) ?  $this->_typo3User[ $name ] : '';
    }
    
    /**
     * 
     */
    public function __set( $name, $value )
    {
        $this->_typo3User[ ( string )$name ] = $value;
    }
    
    /**
     * 
     */
    public function __isset( $name )
    {
        return isset( $this->_typo3User[ ( string )$name ] );
    }
    
    /**
     * 
     */
    public function __unset( $name )
    {
        unset( $this->_typo3User[ ( string )$name ] );
    }
    
    /**
     * 
     */
    public function offsetGet( $offset )
    {
        return $this->$offset;
    }
    
    /**
     * 
     */
    public function offsetSet( $offset, $value )
    {
        $this->$offset = $value;
    }
    
    /**
     * 
     */
    public function offsetExists( $offset )
    {
        return isset( $this->$offset );
    }
    
    /**
     * 
     */
    public function offsetUnset( $offset )
    {
        unset( $this->$offset );
    }
    
    /**
     * 
     */
    public function current()
    {
        $key = key( $this->_typo3User );
        
        return $this->_typo3User[ $key ];
    }
    
    /**
     * 
     */
    public function next()
    {
        next( $this->_typo3User );
    }
    
    /**
     * 
     */
    public function key()
    {
        return key( $this->_typo3User );
    }
    
    /**
     * 
     */
    public function valid()
    {
        if( next( $this->_typo3User ) !== false ) {
            
            prev( $this->_typo3User );
        }
        
        return false;
    }
    
    /**
     * 
     */
    public function rewind()
    {
        reset( $this->_typo3User );
    }
    
    /**
     * 
     */
    private function _setStaticVars( tx_sv_authbase $auth )
    {
        // Checks for the extension global configuration
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'twitter_login' ] ) ) {
            
            // Gets the configuration as an array
            $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'twitter_login' ] );
            
            // Checks if the connection method is defined
            if( isset( $extConf[ 'connection' ] ) && $extConf[ 'connection' ] === 'curl' ) {
                
                // We will use CURL
                self::$_useCurl = true;
            }
        }
        
        // Gets the instance of the Twitter utilities
        self::$_utils = tx_twitterlogin_utils::getInstance();
        
        // Static variables are set
        self::$_hasStatic = true;
    }
    
    /**
     * 
     */
    public static function getAuthenticationObject( tx_sv_authbase $auth )
    {
        // Checks if the static variables are set
        if( self::$_hasStatic === false ) {
            
            // Sets the needed static variables
            self::_setStaticVars( $auth );
        }
        
        // Tries to get a TYPO3 frontend user
        $frontendUsers = tx_oop_Database_Object::getObjectsWhere(
            $auth->db_user[ 'table' ],
            $auth->db_user[ 'username_column' ]
          . '="'
          . $auth->login[ 'uname' ]
          . '"'
          . $auth->db_user[ 'enable_clause' ]
          . $auth->db_user[ 'check_pid_clause' ]
        );
        
        // Checks if a TYPO3 frontend user was found
        if( count( $frontendUsers ) ) {
            
            // Gets the TYPO3 frontend user
            $typo3User    = array_shift( $frontendUsers );
            
            // Tries to get a Twitter user
            $twitterUsers = tx_oop_Database_Object::getObjectsByFields( 'tx_twitterlogin_users', array( 'fe_users_id' => $typo3User[ 'uid' ] ) );
            
            // Checks if a Twitter user was found
            // If not, that would mean the TYPO3 user account is not connected to Twitter (it just has the same screen name)!
            if( !count( $twitterUsers ) ) {
                
                return NULL;
            }
            
            // Gets the Twitter user
            $twitterUser = array_shift( $twitterUsers );
            
        } else {
            
            // Creates the TYPO3 and Twitter users objects, so we'll be able to save them to the database if the user is authenticated
            $typo3User   = new tx_oop_Database_Object( $auth->db_user[ 'table' ] );
            $twitterUser = new tx_oop_Database_Object( 'tx_twitterlogin_users' );
        }
        
        // Returns a new authentication object
        return new self( $typo3User, $twitterUser, $auth );
    }
    
    /**
     * 
     */
    public function authenticateUser()
    {
        // Gets the user informations from the Twitter API
        $infos = self::$_utils->getTwitterUserInfos( $this->_username, $this->_password );
        
        // Checks if we have a valid reponse
        if( !is_object( $infos ) ) {
            
            return false;
        }
        
        // Does the TYPO3 user already exist?
        if( !isset( $this->_typo3User->uid ) ) {
            
            // No, the user will be created as we have valid credidentials from Twitter
            // Loads the TypoScript template, so we can access the service configuration
            $GLOBALS[ 'TSFE' ]->checkAlternativeIdMethods();
            $GLOBALS[ 'TSFE' ]->clear_preview();
            $GLOBALS[ 'TSFE' ]->determineId();
            $GLOBALS[ 'TSFE' ]->initTemplate();
            $GLOBALS[ 'TSFE' ]->getFromCache();
            $GLOBALS[ 'TSFE' ]->getConfigArray();
            
            // Checks if we have a valid configuration array
            if( !isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_twitterlogin_sv1.' ] ) ) {
                
                return false;
            }
            
            // Gets the TypoScript configuration array
            $conf = $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_twitterlogin_sv1.' ];
            
            // Checks if we have valid configuration options
            if( !isset( $conf[ 'storagePage' ] ) || !isset( $conf[ 'usergroup' ] ) ) {
                
                return false;
            }
            
            // Required fields for the TYPO3 user
            $this->_typo3User->pid       = ( int )$conf[ 'storagePage' ];
            $this->_typo3User->usergroup = $conf[ 'usergroup' ];
            $this->_typo3User->username  = $this->_username;
            $this->_typo3User->password  = $this->_password;
            
            // Inserts the TYPO3 user in the database
            $this->_typo3User->commit();
            
            // Required fields for the Twitter user
            $this->_twitterUser->pid         = ( int )$conf[ 'storagePage' ];
            $this->_twitterUser->fe_users_id = $this->_typo3User->uid;
            
        } else {
            
            // Updates the frontend user password
            $this->_typo3User->password  = $this->_password;
            
            // Updates the TYPO3 user in the database
            $this->_typo3User->commit();
        }
        // Updates the local Twitter informations
        self::$_utils->updateUserRecordFromXml( $this->_twitterUser, $infos );
        
        // Stores the Twitter user to the database
        $this->_twitterUser->commit();
        
        // The user is authenticated
        return true;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_login/classes/class.tx_twitterlogin_authentication.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/twitter_login/sv1/classes.tx_twitterlogin_authentication.php']);
}
