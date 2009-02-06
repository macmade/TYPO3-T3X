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
 * TYPO3 database layer class
 * 
 * The goal of the class is to provide TYPO3 with the functionnalities of
 * PDO (PHP Data Object).
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_Database_Layer
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
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return  NULL
     * @throws  tx_oop_Database_Layer_Exception If the PDO class is not available
     * @throws  tx_oop_Database_Layer_Exception If the PDO object cannot be created
     */
    private function __construct()
    {
        // Checks if PDO is available
        if( !class_exists( 'PDO' ) ) {
            
            // PDO is not available
            throw new tx_oop_Database_Layer_Exception(
                'PDO is not available',
                tx_oop_Database_Layer_Exception::EXCEPTION_NO_CONNECTION
            );
        }
        
        // Gets the available PDO drivers
        $this->_drivers = array_flip( PDO::getAvailableDrivers() );
        
        // Sets the default connection infos
        $driver = 'mysql';
        $user   = TYPO3_db_username;
        $pass   = TYPO3_db_password;
        $host   = TYPO3_db_host;
        $db     = TYPO3_db;
        
        // Checks if we are using the DBAL extension (meaning we are not using MySQL)
        if( t3lib_extMgm::isLoaded( 'dbal' ) ) {
            
            if( !isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'dbal' ][ 'handlerCfg' ][ '_DEFAULT' ][ 'type' ] ) ) {
                
                // Error - Bad DBAL configuration
                throw new tx_oop_Database_Layer_Exception(
                    'Invalid configuration of the \'dbal\' extension.',
                    tx_oop_Database_Layer_Exception::EXCEPTION_DBAL_INVALID_CONF
                );
            }
            
            // Gets the DBAL configuration
            $dbalConf = $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'dbal' ][ 'handlerCfg' ][ '_DEFAULT' ];
            
            // Checks if the DBAL is configured to use MySQL
            if( $dbalConf[ 'type' ] === 'native' ) {
                
                // No need for a particular setup
                break;
            }
            
            // Checks the DBAL configuration
            if( !isset( $dbalConf[ 'config' ][ 'driver' ] )
                || !isset( $dbalConf[ 'config' ][ 'username' ] )
                || !isset( $dbalConf[ 'config' ][ 'password' ] )
                || !isset( $dbalConf[ 'config' ][ 'host' ] )
                || !isset( $dbalConf[ 'config' ][ 'database' ] )
            ) {
                
                // Error - Bad DBAL configuration
                throw new tx_oop_Database_Layer_Exception(
                    'Invalid configuration of the \'dbal\' extension.',
                    tx_oop_Database_Layer_Exception::EXCEPTION_DBAL_INVALID_CONF
                );
            }
            
            // Sets the connection infos from the DBAL configuration
            $driver = $dbalConf[ 'config' ][ 'driver' ];
            $user   = $dbalConf[ 'config' ][ 'username' ];
            $pass   = $dbalConf[ 'config' ][ 'password' ];
            $host   = $dbalConf[ 'config' ][ 'host' ];
            $db     = $dbalConf[ 'config' ][ 'database' ];
        }
        
        // Checks if PDO supports the Drupal database driver
        if( !isset( $this->_drivers[ $driver ] ) ) {
            
            // Error - Driver not available
            throw new tx_oop_Database_Layer_Exception(
                'Driver ' . $driver . ' is not available in PDO',
                tx_oop_Database_Layer_Exception::EXCEPTION_NO_PDO_DRIVER
            );
        }
        
        // Stores the full DSN
        $this->_dsn = $driver . ':host=' . $host . ';dbname=' . $db;
        
        // Checks for a connection port
        if( isset( $dbalConf[ 'config' ][ 'port' ] ) ) {
            
            // Adds the port number to the DSN
            $this->_dsn .= ';port=' . $dbalConf[ 'config' ][ 'port' ];
        }
        
        try {
            
            // Creates the PDO object
            $this->_pdo = new PDO( $this->_dsn, $user, $pass );
            
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
     * @param   string                          The name of the called method
     * @param   array                           The arguments for the called method
     * @return  mixed                           The result of the PDO method called
     * @throws  tx_oop_Database_Layer_Exception If the called method does not exist
     */
    public function __call( $name, array $args = array() )
    {
        // Checks if the method can be called
        if( !is_callable( array( $this->_pdo, $name ) ) ) {
            
            // Called method does not exist
            throw new tx_oop_Database_Layer_Exception(
                'The method \'' . $name . '\' cannot be called on the PDO object',
                tx_oop_Database_Layer_Exception::EXCEPTION_BAD_METHOD
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
    public function getRecord( $table, $id, $enableFields = true )
    {
        // WHERE clause for the enable fields
        $enableFieldsAddWhere = '';
        
        // Checks the TYPO3 mode and if we have to care about the enable fields
        if( $enableFields && TYPO3_MODE === 'BE' ) {
            
            // Gets the enable fields WHERE clause
            $enableFieldsAddWhere = t3lib_BEfunc::BEenableFields( $table );
            
        } elseif( $enableFields && TYPO3_MODE === 'BE' ) {
            
            // Should we show the hidden records (meaning we have a BE session running)
            $showHidden = ( $table === 'pages' ) ? $GLOBALS[ 'TSFE' ]->showHiddenPage : $GLOBALS[ 'TSFE' ]->showHiddenRecords;
            
            // Gets the enable fields WHERE clause
            $enableFieldsAddWhere = $GLOBALS[ 'TSFE' ]->sys_page->enableFields(
                $table,
                $showHidden
            );
        }
        
        // Primary key
        $pKey   = 'uid';
        
        // Parameters for the PDO query
        $params = array(
            ':id'      => $id
        );
        
        // Prepares the PDO query
        $query = $this->prepare(
            'SELECT * FROM ' . $table . '
             WHERE ' . $pKey . ' = :id'
           . $enableFieldsAddWhere . '
             LIMIT 1'
        );
        
        // Executes the PDO query
        $query->execute( $params );
        
        // Returns the record
        return $query->fetchObject();
    }
    
    /**
     * 
     */
    public function getRecordsByFields( $table, array $fieldsValues, $orderBy = '', $enableFields = true  )
    {
        // If $orderBy is not specified, checks if we have a default ORDER BY clause for the table
        if( !$orderBy && isset( $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'default_sortby' ] ) ) {
            
            // Default ORDER BY clause, as in the TCA
            $orderBy = ' ' . $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'default_sortby' ];
            
        } elseif( $orderBy ) {
            
            // Specified ORDER BY clause
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        
        // WHERE clause for the enable fields
        $enableFieldsAddWhere = '';
        
        // Checks the TYPO3 mode and if we have to care about the enable fields
        if( $enableFields && TYPO3_MODE === 'BE' ) {
            
            // Gets the enable fields WHERE clause
            $enableFieldsAddWhere = t3lib_BEfunc::BEenableFields( $table );
            
        } elseif( $enableFields && TYPO3_MODE === 'BE' ) {
            
            // Should we show the hidden records (meaning we have a BE session running)
            $showHidden = ( $table === 'pages' ) ? $GLOBALS[ 'TSFE' ]->showHiddenPage : $GLOBALS[ 'TSFE' ]->showHiddenRecords;
            
            // Gets the enable fields WHERE clause
            $enableFieldsAddWhere = $GLOBALS[ 'TSFE' ]->sys_page->enableFields(
                $table,
                $showHidden
            );
        }
        
        // Primary key
        $pKey   = 'uid';
        
        // Starts the query
        $sql = 'SELECT * FROM ' . $table . ' WHERE ';
        
        // Parameters for the PDO query
        $params = array();
        
        // Process each field to check
        foreach( $fieldsValues as $fieldName => $fieldValue ) {
            
            // Adds the parameter
            $params[ ':' . $fieldName ] = $fieldValue;
            
            // Adds the statement
            $sql .= $fieldName . ' = :' . $fieldName . ' AND ';
        }
        
        // Removes the last 'AND'
        $sql = substr( $sql, 0, -5 );
        
        // Adds the WHERE clause for the enable fields
        $sql .= $enableFieldsAddWhere;
        
        // Adds the ORDER BY clause
        $sql .= $orderBy;
        
        // Prepares the PDO query
        $query = $this->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $params );
        
        // Storage
        $rows = array();
        
        // Process each row
        while( $row = $query->fetchObject() ) {
            
            // Stores the current row
            $rows[ $row->$pKey ] = $row;
        }
        
        // Returns the rows
        return $rows;
    }
    
    /**
     * 
     */
    public function insertRecord( $table, array $values )
    {
        // Parameters for the PDO query
        $params = array();
        
        // Gets the current time
        $time   = time();
        
        // SQL for the insert statement
        $sql    = 'INSERT INTO ' . $table . ' SET';
        
        // Checks if we have a creation date field in the specified table
        if( !isset( $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'crdate' ] ) ) {
            
            // Modification date field name
            $crdate = $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'crdate' ];
            
            // Adds the current time to the parameters
            $params[ 'crdate' ] = $time;
            
            // Adds the modification date in the SQL query
            $sql .= ' ' . $crdate . ' = :crdate,';
        }
        
        // Checks if we have a modification date field in the specified table
        if( !isset( $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'tstamp' ] ) ) {
            
            // Modification date field name
            $tstamp = $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'tstamp' ];
            
            // Adds the current time to the parameters
            $params[ 'tstamp' ] = $time;
            
            // Adds the modification date in the SQL query
            $sql .= ' ' . $tstamp . ' = :tstamp,';
        }
        
        // Checks if we have a creation user field in the specified table and if we are in a BE environment
        if( TYPO3_MODE === 'BE' && !isset( $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'cruser_id' ] ) ) {
            
            // Modification date field name
            $crUserId = $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'cruser_id' ];
            
            // Adds the current time to the parameters
            $params[ 'cruser_id' ] = $GLOBALS[ 'BE_USER' ]->user[ 'uid' ];
            
            // Adds the modification date in the SQL query
            $sql .= ' ' . $crUserId . ' = :cruser_id,';
        }
        
        // Process each value
        foreach( $values as $fieldName => $value ) {
            
            // Adds the PDO parameter for the current value
            $params[ ':' . $fieldName ] = $value;
            
            // Adds the update statement for the current value
            $sql .= ' ' . $fieldName . ' = :' . $fieldName . ',';
        }
        
        // Removes the last comma
        $sql  = substr( $sql, 0, -1 );
        
        // Prepares the PDO query
        $query = $this->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $params );
        
        // Returns the insert ID
        return $this->lastInsertId();
    }
    
    /**
     * 
     */
    public function updateRecord( $table, $id, array $values )
    {
        // Primary key
        $pKey   = 'uid';
        
        // Parameters for the PDO query
        $params = array(
            ':' . $pKey => $id
        );
        
        // SQL for the update statement
        $sql    = 'UPDATE ' . $table . ' SET';
        
        // Checks if we have a modification date field in the specified table
        if( !isset( $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'tstamp' ] ) ) {
            
            // Modification date field name
            $tstamp = $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'tstamp' ];
            
            // Adds the current time to the parameters
            $params[ 'tstamp' ] = time();
            
            // Adds the modification date in the SQL query
            $sql .= ' ' . $tstamp . ' = :tstamp,';
        }
        
        // Process each value
        foreach( $values as $fieldName => $value ) {
            
            // Adds the PDO parameter for the current value
            $params[ ':' . $fieldName ] = $value;
            
            // Adds the update statement for the current value
            $sql .= ' ' . $fieldName . ' = :' . $fieldName . ',';
        }
        
        // Removes the last comma
        $sql  = substr( $sql, 0, -1 );
        
        // Adds the where clause
        $sql .= ' WHERE ' . $pKey . ' = :' . $pKey;
        
        // Prepares the PDO query
        $query = $this->prepare( $sql );
        
        // Executes the PDO query
        return $query->execute( $params );
    }
    
    /**
     * 
     */
    public function deleteRecord( $table, $id, $deleteFromTable = false )
    {
        // Checks if we should really delete the record, or just set the delete flag
        if( $deleteFromTable ) {
            
            // Primary key
            $pKey   = 'uid';
            
            // Parameters for the PDO query
            $params = array(
                ':id' => $id
            );
            
            // SQL for the update statement
            $sql = 'DELETE FROM ' . $table . ' WHERE ' . $pKey . ' = :id';
            
            // Prepares the PDO query
            $query = $this->prepare( $sql );
            
            // Executes the PDO query
            return $this->execute( $params );
        }
        
        // Checks if we have a delete flag in the specified table
        if( !isset( $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'delete' ] ) ) {
            
            // No, delete the record
            return $this->deleteRecord( $table, $id, true );
        }
        
        // Just sets the delete flag
        return $this->updateRecord(
            $table,
            $id,
            array(
                $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'delete' ] => 1
            )
        );
    }
    
    /**
     * 
     */
    public function removeDeletedRecords( $table )
    {
        // Checks if we have a delete flag in the specified table
        if( !isset( $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'delete' ] ) ) {
            
            // No, exits the methods
            return NULL;
        }
        
        // Gets the field name for the delete flag
        $deleteField = $GLOBALS[ 'TCA' ][ $table ][ 'ctrl' ][ 'delete' ];
        
        // Prepares the PDO query
        $query = $this->prepare(
            'DELETE FROM ' . $table . ' WHERE ' . $deleteField . ' = 1'
        );
        
        // Executes the PDO query
        return $this->execute( $params );
    }
}
