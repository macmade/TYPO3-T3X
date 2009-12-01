<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 eosgarden - Jean-David Gadina (macmade@eosgarden.com)               #
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
 * TYPO3 environment object
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_Typo3_Environment
{
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * 
     */
    private $_envVars         = array();
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return  NULL
     */
    private function __construct()
    {
        $this->_envVars[ 'REQUEST_URI' ]          = t3lib_div::getIndpEnv( 'REQUEST_URI' );
        $this->_envVars[ 'HTTP_HOST' ]            = t3lib_div::getIndpEnv( 'HTTP_HOST' );
        $this->_envVars[ 'SCRIPT_NAME' ]          = t3lib_div::getIndpEnv( 'SCRIPT_NAME' );
        $this->_envVars[ 'PATH_INFO' ]            = t3lib_div::getIndpEnv( 'PATH_INFO' );
        $this->_envVars[ 'QUERY_STRING' ]         = t3lib_div::getIndpEnv( 'QUERY_STRING' );
        $this->_envVars[ 'HTTP_REFERER' ]         = t3lib_div::getIndpEnv( 'HTTP_REFERER' );
        $this->_envVars[ 'REMOTE_ADDR' ]          = t3lib_div::getIndpEnv( 'REMOTE_ADDR' );
        $this->_envVars[ 'REMOTE_HOST' ]          = t3lib_div::getIndpEnv( 'REMOTE_HOST' );
        $this->_envVars[ 'HTTP_USER_AGENT' ]      = t3lib_div::getIndpEnv( 'HTTP_USER_AGENT' );
        $this->_envVars[ 'HTTP_ACCEPT_LANGUAGE' ] = t3lib_div::getIndpEnv( 'HTTP_ACCEPT_LANGUAGE' );
        $this->_envVars[ 'SCRIPT_FILENAME' ]      = t3lib_div::getIndpEnv( 'SCRIPT_FILENAME' );
        $this->_envVars[ 'TYPO3_HOST_ONLY' ]      = t3lib_div::getIndpEnv( 'TYPO3_HOST_ONLY' );
        $this->_envVars[ 'TYPO3_PORT' ]           = t3lib_div::getIndpEnv( 'TYPO3_PORT' );
        $this->_envVars[ 'TYPO3_REQUEST_HOST' ]   = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_HOST' );
        $this->_envVars[ 'TYPO3_REQUEST_URL' ]    = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        $this->_envVars[ 'TYPO3_REQUEST_SCRIPT' ] = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_SCRIPT' );
        $this->_envVars[ 'TYPO3_REQUEST_DIR' ]    = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_DIR' );
        $this->_envVars[ 'TYPO3_SITE_URL' ]       = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' );
        $this->_envVars[ 'TYPO3_SITE_SCRIPT' ]    = t3lib_div::getIndpEnv( 'TYPO3_SITE_SCRIPT' );
        $this->_envVars[ 'TYPO3_DOCUMENT_ROOT' ]  = t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' );
        $this->_envVars[ 'TYPO3_SSL' ]            = t3lib_div::getIndpEnv( 'TYPO3_SSL' );
        $this->_envVars[ 'TYPO3_PROXY' ]          = t3lib_div::getIndpEnv( 'TYPO3_PROXY' );
    }
    
    /**
     * Clones an instance of the class
     * 
     * A call to this method will produce an exception, as the class cannot
     * be cloned (singleton).
     * 
     * @return  NULL
     * @throws  tx_oop_Core_Singleton_Exception Always, as the class cannot be cloned (singleton)
     */
    public function __clone()
    {
        throw new tx_oop_Core_Singleton_Exception(
            'Class ' . __CLASS__ . ' cannot be cloned',
            tx_oop_Core_Singleton_Exception::EXCEPTION_CLONE
        );
    }
    
    /**
     * Gets the unique class instance
     * 
     * This method is used to get the unique instance of the class
     * (singleton). If no instance is available, it will create it.
     * 
     * @return  tx_oop_Typo3_Environment    The unique instance of the class
     */
    public static function getInstance()
    {
        // Checks if the unique instance already exists
        if( !is_object( self::$_instance ) ) {
            
            // Creates the unique instance
            self::$_instance = new self();
        }
        
        // Returns the unique instance
        return self::$_instance;
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        $name = ( string )$name;
        
        if( !isset( $this->_envVars[ $name ] ) ) {
            
            throw new tx_oop_Typo3_Environment_Exception(
                
            );
        }
        
        return $this->_envVars[ $name ];
    }
}
