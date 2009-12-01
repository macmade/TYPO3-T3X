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
 * Data registry object
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  tx.oop.Data
 */
final class tx_oop_Data_Registry
{
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * The registry array
     */
    private static $_registry = array();
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return  NULL
     */
    private function __construct()
    {}
    
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
     * @return  tx_oop_Data_Registry    The unique instance of the class
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
    public function add( $variable, $name = '' )
    {
        $name = ( string )$name;
        
        if( is_object( $variable ) && !$name ) {
            
            $name = get_class( $object );
            
        } elseif( !$name ) {
            
            throw new tx_oop_Data_Registry_Exception(
                'A name must be supplied',
                tx_oop_Data_Registry_Exception::EXCEPTION_NO_NAME
            );
        }
        
        if( isset( $this->_registry[ $name ] ) ) {
            
            throw new tx_oop_Data_Registry_Exception(
                'A data with the name \'' . $name . '\' already exists in the registry',
                tx_oop_Data_Registry_Exception::EXCEPTION_VARIABLE_EXISTS
            );
        }
        
        $this->_registry[ $name ] = $variable;
    }
    
    /**
     * 
     */
    public function &get( $name )
    {
        $name = ( string )$name;
        
        if( !isset( $this->_registry[ $name ] ) ) {
            
            throw new tx_oop_Data_Registry_Exception(
                'The requested data \'' . $name . '\' does not exist in the registry',
                tx_oop_Data_Registry_Exception::EXCEPTION_NO_VARIABLE
            );
        }
        
        return $this->_registry[ $name ];
    }
    
    /**
     * 
     */
    public function isset( $name )
    {
        return isset( $this->_registry[ ( string )$name ] );
    }
}
