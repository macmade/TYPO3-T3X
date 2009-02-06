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

// Includes the TYPO3 TCE classe
require_once( PATH_t3lib . 'class.t3lib_tcemain.php' );

/**
 * TYPO3 Core Engine helper class
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_Typo3_Core_Engine
{
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * The TCE object
     */
    private $_tce             = NULL;
    
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
        // Checks if the TYPO3 backend is active
        if( TYPO3_MODE !== 'BE' ) {
            
            // The TCE is only available for the backend
            throw new tx_oop_Typo3_Core_Engine_Exception(
                'The TCE is only available in a backend context.',
                tx_oop_Typo3_Core_Engine_Exception::EXCEPTION_NO_BACKEND
            );
        }
        
        // Creates an instance of the TCE
        $this->_tce                      = t3lib_div::makeInstance( 't3lib_TCEmain' );
        $this->_tce->stripslashes_values = 0;
    }
    
    /**
     * PHP method calls overloading
     * 
     * This method will reroute all the call on this object to the TCE object.
     * 
     * @param   string                              The name of the called method
     * @param   array                               The arguments for the called method
     * @return  mixed                               The result of the TCE method called
     * @throws  tx_oop_Typo3_Core_Engine_Exception  If the called method does not exist
     */
    public function __call( $name, array $args = array() )
    {
        // Checks if the method can be called
        if( !is_callable( array( $this->_tce, $name ) ) ) {
            
            // Called method does not exist
            throw new tx_oop_Typo3_Core_Engine_Exception(
                'The method \'' . $name . '\' cannot be called on the TCE object',
                tx_oop_Typo3_Core_Engine_Exception::EXCEPTION_BAD_METHOD
            );
        }
        
        // Gets the number of arguments
        $argCount = count( $args );
        
        // We won't use call_user_func_array, as it cannot return references
        switch( $argCount ) {
            
            case 1:
                
                return $this->_tce->$name( $args[ 0 ] );
                break;
            
            case 2:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ] );
                break;
            
            case 3:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ] );
                break;
            
            case 4:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
                break;
            
            case 5:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ] );
                break;
            
            case 6:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ] );
                break;
            
            case 7:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ] );
                break;
            
            case 8:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ] );
                break;
            
            case 9:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ], $args[ 8 ] );
                break;
            
            case 10:
                
                return $this->_tce->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ], $args[ 8 ], $args[ 9 ] );
                break;
            
            default:
                
                return $this->_tce->$name();
                break;
        }
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        return $this->_tce->$name;
    }
    
    /**
     * 
     */
    public function __set( $name, $value )
    {
        $this->_tce->$name = $value;
    }
    
    /**
     * 
     */
    public function __isset( $name )
    {
        return isset( $this->_tce->$name );
    }
    
    /**
     * 
     */
    public function __unset( $name )
    {
        unset( $this->_tce->$name );
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
     * @return  tx_oop_Database_Layer   The unique instance of the class
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
    public function processData( array $data )
    {
        $this->_tce->start( $data, array() );
        $this->_tce->process_datamap();
    }
    
    /**
     * 
     */
    public function processCommand( array $cmd )
    {
        $this->_tce->start( array(), $cmd );
        $this->_tce->process_cmdmap();
    }
}

