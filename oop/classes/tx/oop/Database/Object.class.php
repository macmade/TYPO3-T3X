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
 * TYPO3 database object class
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  tx.oop.Database
 */
final class tx_oop_Database_Object implements ArrayAccess, Iterator
{
    /**
     * A flag to know wether the needed static variables are set
     */
    private static $_hasStatic  = false;
    
    /**
     * The instance of the database class (tx_oop_Database_Layer)
     */
    protected static $_db       = NULL;
    
    /**
     * The instance of the string utilities class (tx_oop_String_Utils)
     */
    protected static $_str      = NULL;
    
    /**
     * A reference to the t3lib_DB object
     */
    protected static $_t3Db     = NULL;
    
    /**
     * A reference to the TCA description array
     */
    protected static $_tcaDescr = array();
    
    /**
     * A reference to the TCA array
     */
    protected static $_tca      = array();
    
    /**
     * The informations about the database tables
     */
    protected static $_tables   = array();
    
    /**
     * The informations about the database tables' fields
     */
    protected static $_fields   = array();
    
    /**
     * The name of the TYPO3 table
     */
    protected $_tableName       = '';
    
    /**
     * The default primary key for the database table
     */
    protected $_pKey            = 'uid';
    
    /**
     * The ID of the record (if any)
     */
    protected $_id              = 0;
    
    /**
     * The database record
     */
    protected $_record          = array();
    
    /**
     * The database record (modified version)
     */
    protected $_updatedRecord   = array();
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct( $tableName, $id = 0 )
    {
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $this->_tableName = ( string )$tableName;
        $this->_id        = ( int )$id;
        
        if( !isset( self::$_tables[ $this->_tableName ] ) ) {
            
            throw new tx_oop_Database_Object_Exception(
                'The table \'' . $this->_tableName . '\' does not exist in the database',
                tx_oop_Database_Object_Exception::EXCEPTION_NO_TABLE
            );
        }
        
        if( !isset( self::$_fields[ $this->_tableName ] ) ) {
            
            self::$_fields[ $this->_tableName ] = array();
            
            $fields = self::$_t3Db->admin_get_fields( $tableName );
            
            if( is_array( $fields ) ) {
                
                foreach( $fields as $key => $value ) {
                    
                    self::$_fields[ $this->_tableName ][ $key ] = true;
                }
            }
        }
        
        if( $this->_id > 0 ) {
            
            $record    = self::$_db->getRecord( $this->_tableName, $this->_id );
            
            if( is_object( $record ) ) {
                
                $this->_id = $record->uid;
                
                foreach( $record as $key => $value ) {
                    
                    $this->_record[ $key ] = $value;
                }
                
            } else {
                
                throw new tx_oop_Database_Object_Exception(
                    'The record #' . $this->_id . ' for table \'' . $this->_tableName . '\' does not exist in the database',
                    tx_oop_Database_Object_Exception::EXCEPTION_NO_RECORD
                );
            }
        }
    }
    
    public function __get( $name )
    {
        $name = ( string )$name;
        
        if( !isset( self::$_fields[ $this->_tableName ][ $name ] ) ) {
            
            throw new tx_oop_Database_Object_Exception(
                'The field \'' . $name . '\' does not exist in the table table \'' . $this->_tableName . '\'',
                tx_oop_Database_Object_Exception::EXCEPTION_NO_FIELD
            );
        }
        
        if( isset( $this->_updatedRecord[ $name ] ) ) {
            
            return $this->_updatedRecord[ $name ];
            
        } elseif( isset( $this->_record[ $name ] ) ) {
            
            return $this->_record[ $name ];
        }
        
        return false;
    }
    
    public function __set( $name, $value )
    {
        $name = ( string )$name;
        
        if( !isset( self::$_fields[ $this->_tableName ][ $name ] ) ) {
            
            throw new tx_oop_Database_Object_Exception(
                'The field \'' . $name . '\' does not exist in the table table \'' . $this->_tableName . '\'',
                tx_oop_Database_Object_Exception::EXCEPTION_NO_FIELD
            );
        }
        
        if( $name === $this->_pKey ) {
            
            throw new tx_oop_Database_Object_Exception(
                'The primary key cannot be set',
                tx_oop_Database_Object_Exception::EXCEPTION_SET_UID
            );
        }
        
        $this->_updatedRecord[ $name ] = $value;
    }
    
    public function __isset( $name )
    {
        return isset( $this->_record[ $name ] );
    }
    
    public function __unset( $name )
    {
        unset( $this->_record[ $name ] );
        unset( $this->_updatedRecord[ $name ] );
    }
    
    public function offsetGet( $name )
    {
        return $this->$name;
    }
    
    public function offsetSet( $name, $value )
    {
        $this->$name = $value;
    }
    
    public function offsetExists( $name )
    {
        return isset( $this->$name );
    }
    
    public function offsetUnset( $name )
    {
        unset( $this->$name );
    }
    
    /**
     * 
     */
    public function current()
    {
        $key = key( $this->_record );
        
        return ( isset( $this->_updatedRecord[ $key ] ) ) ? $this->_updatedRecord[ $key ] : $this->_record[ $key ];
    }
    
    /**
     * 
     */
    public function next()
    {
        next( $this->_record );
    }
    
    /**
     * 
     */
    public function key()
    {
        return key( $this->_record );
    }
    
    /**
     * 
     */
    public function valid()
    {
        if( next( $this->_record ) !== false ) {
            
            prev( $this->_record );
        }
        
        return false;
    }
    
    /**
     * 
     */
    public function rewind()
    {
        reset( $this->_record );
    }
    
    /**
     * 
     */
    private static function _setStaticVars()
    {
        self::$_db       = tx_oop_Database_Layer::getInstance();
        self::$_str      = tx_oop_String_Utils::getInstance();
        self::$_t3Db     = $GLOBALS[ 'TYPO3_DB' ];
        self::$_tcaDescr = $GLOBALS[ 'TCA_DESCR' ];
        self::$_tca      = $GLOBALS[ 'TCA' ];
        
        $tables = self::$_t3Db->admin_get_tables();
        
        foreach( $tables as $key => $value ) {
            
            self::$_tables[ $key ] = true;
        }
        
        self::$_hasStatic = true;
    }
    
    public static function updateAvailableTables()
    {
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $tables = self::$_t3Db->admin_get_tables();
        
        foreach( $tables as $key => $value ) {
            
            $this->_tables[ $key ] = true;
        }
    }
    
    public static function getObjects( $tableName, array $keys, $orderBy = '' )
    {
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $tableName = ( string )$tableName;
        $records   = self::$_db->getRecords( $tableName, $keys, $orderBy );
        
        foreach( $records as $id => $row ) {
            
            $object      = new self( $tableName );
            $object->_id = $id;
            
            foreach( $row as $field => $value ) {
                
                $object->_record[ $field ] = $value;
            }
            
            $records[ $id ] = $object;
        }
        
        return $records;
    }
    
    public static function getObjectsByFields( $tableName, array $fieldsValues, $orderBy = '' )
    {
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $tableName = ( string )$tableName;
        $records   = self::$_db->getRecordsByFields( $tableName, $fieldsValues, $orderBy );
        
        foreach( $records as $id => $row ) {
            
            $object      = new self( $tableName );
            $object->_id = $id;
            
            foreach( $row as $field => $value ) {
                
                $object->_record[ $field ] = $value;
            }
            
            $records[ $id ] = $object;
        }
        
        return $records;
    }
    
    public static function getObjectsWhere( $tableName, $whereClause, $orderBy = '' )
    {
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $tableName = ( string )$tableName;
        $records   = self::$_db->getRecordsWhere( $tableName, $whereClause, $orderBy );
        
        foreach( $records as $id => $row ) {
            
            $object      = new self( $tableName );
            $object->_id = $id;
            
            foreach( $row as $field => $value ) {
                
                $object->_record[ $field ] = $value;
            }
            
            $records[ $id ] = $object;
        }
        
        return $records;
    }
    
    public function commit()
    {
        if( $this->_id > 0 ) {
            
            if( !count( $this->_updatedRecord ) ) {
                
                return true;
            }
            
            if( self::$_db->updateRecord( $this->_tableName, $this->_id, $this->_updatedRecord ) ) {
                
                $this->_record        = array_merge( $this->_record, $this->_updatedRecord );
                $this->_updatedRecord = array();
                
                return true;
            }
            
            return false;
            
        } else {
            
            if( self::$_db->insertRecord( $this->_tableName, $this->_updatedRecord ) ) {
                
                $this->_id                     = self::$_db->lastInsertId();
                $this->_record[ $this->_pKey ] = $this->_id;
                $this->_record                 = array_merge( $this->_record, $this->_updatedRecord );
                $this->_updatedRecord          = array();
                
                return true;
            }
            
            return false;
        }
    }
    
    public function delete( $deleteFromTable = false )
    {
        if( $this->_id > 0 ) {
            
            return self::$_db->deleteRecord( $this->_tableName, $this->_id, $deleteFromTable );
        }
        
        return false;
    }
    
    /**
     * 
     */
    public function getTableName()
    {
        return $this->_tableName;
    }
}
