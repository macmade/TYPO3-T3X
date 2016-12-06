<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 netinfluence                                                        #
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

# $Id: Layer.class.php 103 2009-12-01 13:54:50Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Magento database layer
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
final class tx_marvincatalog_Magento_Database_Layer
{
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * The PDO object for the TYPO3 database
     */
    private $_pdo             = NULL;
    
    /**
     * The available PDO drivers
     */
    private $_drivers         = array();
    
    /**
     * The distinguised server name for the TYPO3 database
     */
    private $_dsn             = '';
    
    /**
     * 
     */
    private $_driver          = 'mysql';
    
    /**
     * 
     */
    private $_host            = 'localhost';
    
    /**
     * 
     */
    private $_user            = 'magento';
    
    /**
     * 
     */
    private $_password        = 'magento';
    
    /**
     * 
     */
    private $_database        = 'magento';
    
    /**
     * 
     */
    private $_tablePrefix     = '';
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return  NULL
     * @throws  tx_marvincatalog_Magento_Database_Layer_Exception   If the PDO class is not available
     * @throws  tx_marvincatalog_Magento_Database_Layer_Exception   If the PDO object cannot be created
     */
    private function __construct()
    {
        // Checks if PDO is available
        if( !class_exists( 'PDO' ) ) {
            
            // PDO is not available
            throw new tx_marvincatalog_Magento_Database_Layer_Exception(
                'PDO is not available',
                tx_marvincatalog_Magento_Database_Layer_Exception::EXCEPTION_NO_PDO
            );
        }
        
        // Gets the available PDO drivers
        $this->_drivers = array_flip( PDO::getAvailableDrivers() );
        
        // Checks if the extension's configuration values are available
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] ) ) {
            
            // Gets the extension configuration
            $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] );
            
            // Sets the database driver if available
            if( isset( $extConf[ 'db_driver' ] ) ) {
                
                $this->_driver = $extConf[ 'db_driver' ];
            }
            
            // Sets the database host if available
            if( isset( $extConf[ 'db_host' ] ) ) {
                
                $this->_host = $extConf[ 'db_host' ];
            }
            
            // Sets the database user if available
            if( isset( $extConf[ 'db_user' ] ) ) {
                
                $this->_user = $extConf[ 'db_user' ];
            }
            
            // Sets the database password if available
            if( isset( $extConf[ 'db_pass' ] ) ) {
                
                $this->_password = $extConf[ 'db_pass' ];
            }
            
            // Sets the database name if available
            if( isset( $extConf[ 'db_name' ] ) ) {
                
                $this->_database = $extConf[ 'db_name' ];
            }
            
            // Sets the table prefix if available
            if( isset( $extConf[ 'db_prefix' ] ) ) {
                
                $this->_tablePrefix = $extConf[ 'db_prefix' ];
            }
        }
        
        // Checks if PDO supports the Drupal database driver
        if( !isset( $this->_drivers[ $this->_driver ] ) ) {
            
            // Error - Driver not available
            throw new tx_oop_Database_Layer_Exception(
                'Driver ' . $this->_driver . ' is not available in PDO',
                tx_oop_Database_Layer_Exception::EXCEPTION_NO_PDO_DRIVER
            );
        }
        
        // Stores the full DSN
        $this->_dsn = $this->_driver . ':host=' . $this->_host . ';dbname=' . $this->_database;
        
        try {
            
            // Creates the PDO object
            $this->_pdo = new PDO( $this->_dsn, $this->_user, $this->_password );
            
        } catch( Exception $e ) {
            
            // The PDO object cannot be created - Reroute the exception
            throw new tx_oop_Database_Layer_Exception(
                $e->getMessage(),
                tx_oop_Database_Layer_Exception::EXCEPTION_NO_CONNECTION
            );
        }
    }
    
    /**
     * Class destructor
     * 
     * This method will close the PDO connection to the TYPO3 database.
     * 
     * @return  NULL
     */
    public function __destruct()
    {
        $this->_pdo = NULL;
    }
    
    /**
     * PHP method calls overloading
     * 
     * This method will reroute all the call on this object to the PDO object.
     * 
     * @param   string                                              The name of the called method
     * @param   array                                               The arguments for the called method
     * @return  mixed                                               The result of the PDO method called
     * @throws  tx_marvincatalog_Magento_Database_Layer_Exception   If the called method does not exist
     */
    public function __call( $name, array $args = array() )
    {
        // Checks if the method can be called
        if( !is_callable( array( $this->_pdo, $name ) ) ) {
            
            // Called method does not exist
            throw new tx_marvincatalog_Magento_Database_Layer_Exception(
                'The method \'' . $name . '\' cannot be called on the PDO object',
                tx_marvincatalog_Magento_Database_Layer_Exception::EXCEPTION_BAD_METHOD
            );
        }
        
        // Gets the number of arguments
        $argCount = count( $args );
        
        // We won't use call_user_func_array, as it cannot return references
        switch( $argCount ) {
            
            case 1:
                
                return $this->_pdo->$name( $args[ 0 ] );
                break;
            
            case 2:
                
                return $this->_pdo->$name( $args[ 0 ], $args[ 1 ] );
                break;
            
            case 3:
                
                return $this->_pdo->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ] );
                break;
            
            case 4:
                
                return $this->_pdo->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
                break;
                break;
            
            case 5:
                
                return $this->_pdo->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ] );
                break;
            
            default:
                
                return $this->_pdo->$name();
                break;
        }
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
     * @return  tx_marvincatalog_Magento_Database_Layer     The unique instance of the class
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
     * Gets a Magento table name (with the prefix, if any)
     * 
     * @param   string  The name of the Magento table (without prefix)
     * @return  string  The real name of the Magento table
     */
    public function getTableName( $name )
    {
        return $this->_tablePrefix . $name;
    }
}
