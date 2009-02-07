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
 * TYPO3 backend session data class
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
abstract class tx_oop_Session_Data_Base implements ArrayAccess, Iterator
{
    /**
     * 
     */
    abstract public static function getInstance( $name );
    
    /**
     * 
     */
    abstract protected function _getUser();
    
    /**
     * 
     */
    abstract protected function _getSessionData();
    
    /**
     * 
     */
    abstract protected function _updateSessionData();
    
    /**
     * 
     */
    private static $_instances   = array();
    
    /**
     * 
     */
    private static $_nbInstances = 0;
    
    /**
     * 
     */
    private $_iteratorIndex      = 0;
    
    /**
     * 
     */
    protected $_user               = NULL;
    
    /**
     * 
     */
    protected $_instanceName     = '';
    
    /**
     * 
     */
    protected $_data             = array();
    
    /**
     * 
     */
    private function __construct( $name )
    {
        $this->_instanceName = $name;
        $this->_user         = $this->_getUser();
        $this->_data         = $this->_getSessionData();
        
        if( !is_array( $this->_data ) ) {
            
            $this->_data = array();
        }
    }
    
    /**
     * 
     */
    public function __toString()
    {
        return serialize( $this->_data );
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        return ( isset( $this->_data[ $name ] ) ) ? $this->_data[ $name ] : false;
    }
    
    /**
     * 
     */
    public function __set( $name, $value )
    {
        $this->_data[ $name ] = $value;
        $this->_updateSessionData();
    }
    
    /**
     * 
     */
    public function __isset( $name )
    {
        return isset( $this->_data[ $name ] );
    }
    
    /**
     * 
     */
    public function __unset( $name )
    {
        unset( $this->_data[ $name ] );
        $this->_updateSessionData();
    }
    
    /**
     * 
     */
    public function offsetGet( $name )
    {
        return ( isset( $this->_data[ $name ] ) ) ? $this->_data[ $name ] : false;
    }
    
    /**
     * 
     */
    public function offsetSet( $name, $value )
    {
        $this->_data[ $name ] = $value;
        $this->_updateSessionData();
    }
    
    /**
     * 
     */
    public function offsetExists( $name )
    {
        return isset( $this->_data[ $name ] );
    }
    
    /**
     * 
     */
    public function offsetUnset( $name )
    {
        unset( $this->_data[ $name ] );
        $this->_updateSessionData();
    }
    
    /**
     * 
     */
    public function key()
    {
        return key( $this->_data );
    }
    
    /**
     * 
     */
    public function current()
    {
        return current( $this->_data );
    }
    
    /**
     * 
     */
    public function next()
    {
        next( $this->_data );
        $this->_iteratorIndex++;
    }
    
    /**
     * 
     */
    public function valid()
    {
        return ( $this->_iteratorIndex < count( $this->_data ) ) ? true : false;
    }
    
    /**
     * 
     */
    public function rewind()
    {
        reset( $this->_data );
    }
    
    /**
     * 
     */
    protected static function _getInstance( $class, $name )
    {
        if( !isset( self::$_instances[ $name ] ) ) {
            
            self::$_instances[ $name ] = new $class( $name );
            self::$_nbInstances++;
        }
        
        return self::$_instances[ $name ];
    }
}
