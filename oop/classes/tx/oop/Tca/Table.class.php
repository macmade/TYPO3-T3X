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

################################################################################
# TO DO:
# 
# - types
# - palettes
# - typeicons
# - l10n
# - ws
# - delete
# - manual ordering
# - allowed on pages
# - save and new
# - showRecordFieldList
################################################################################

/**
 * TCA table object class
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_Tca_Table
{
    /**
     * An array with the TCA table instances
     */
    private static $_instances   = array();
    
    /**
     * The number of instances
     */
    private static $_nbInstances = 0;
    
    /**
     * Reserved field names
     */
    private static $_reserved    = array(
        'all'                => true,
        'and'                => true,
        'asensitive'         => true,
        'bigint'             => true,
        'both'               => true,
        'call'               => true,
        'cascade'            => true,
        'char'               => true,
        'character'          => true,
        'collate'            => true,
        'column'             => true,
        'condition'          => true,
        'connection'         => true,
        'continue'           => true,
        'convert'            => true,
        'crdate'             => true,
        'cruser_id'          => true,
        'current_date'       => true,
        'current_user'       => true,
        'cursor'             => true,
        'data'               => true,
        'databases'          => true,
        'day_minute'         => true,
        'decimal'            => true,
        'declare'            => true,
        'default'            => true,
        'delayed'            => true,
        'deleted'            => true,
        'desc'               => true,
        'describe'           => true,
        'deterministic'      => true,
        'distinctrow'        => true,
        'drop'               => true,
        'each'               => true,
        'else'               => true,
        'elseif'             => true,
        'endtime'            => true,
        'escaped'            => true,
        'exit'               => true,
        'explain'            => true,
        'fe_group'           => true,
        'fetch'              => true,
        'field'              => true,
        'float'              => true,
        'for'                => true,
        'from'               => true,
        'goto'               => true, 
        'group'              => true,
        'hidden'             => true,
        'hour_microsecond'   => true,
        'if'                 => true,
        'index'              => true,
        'inout'              => true,
        'insensitive'        => true,
        'int'                => true,
        'int3'               => true,
        'integer'            => true,
        'is'                 => true,
        'iterate'            => true,
        'key'                => true,
        'label'              => true,
        'leading'            => true,
        'leave'              => true,
        'like'               => true,
        'load'               => true,
        'lock'               => true,
        'longtext'           => true,
        'loop'               => true,
        'match'              => true,
        'mediumtext'         => true,
        'minute_second'      => true,
        'modifies'           => true,
        'natural'            => true,
        'null'               => true,
        'optimize'           => true,
        'or'                 => true,
        'out'                => true,
        'outer'              => true,
        'pid'                => true,
        'primary'            => true,
        'raid0'              => true,
        'reads'              => true,
        'real'               => true,
        'release'            => true,
        'repeat'             => true,
        'replace'            => true,
        'return'             => true,
        'rlike'              => true,
        'schema'             => true,
        'schemas'            => true,
        'second_microsecond' => true,
        'sensitive'          => true,
        'separator'          => true,
        'smallint'           => true,
        'sorting'            => true,
        'specific'           => true,
        'sql_cal_found_rows' => true,
        'sql'                => true,
        'sqlexception'       => true,
        'sqlstate'           => true,
        'sqlwarning'         => true,
        'starting'           => true,
        'starttime'          => true,
        'table'              => true,
        'terminated'         => true,
        'tinyint'            => true,
        'trailing'           => true,
        'trigger'            => true,
        'tstamp'             => true,
        'undo'               => true,
        'unlock'             => true,
        'upgrade'            => true,
        'usage'              => true,
        'utc_date'           => true,
        'values'             => true,
        'varcharacter'       => true,
        'where'              => true,
        'while'              => true,
        'write'              => true,
        'year_month'         => true,
        'uid'                => true
    );
    
    /**
     * The instance (table) name
     */
    private $_instanceName       = '';
    
    /**
     * The extension key
     */
    private $_extKey             = '';
    
    /**
     * The extension path (absolute)
     */
    private $_extPath            = '';
    
    /**
     * The extension path (relative)
     */
    private $_extRelPath         = '';
    
    /**
     * The properties of the TCA control section
     */
    private $_ctrl               = array();
    
    /**
     * The TCA fields (instances of tx_oop_Tca_Field)
     */
    private $_fields             = array();
    
    /**
     * The name of the user fields (not TYPO3 control fields)
     */
    private $_userFields         = array();
    
    /**
     * Class constructor
     * 
     * @param   string  The extension key
     * @param   string  The full table name
     * @return  NULL
     */
    private function __construct( $extKey, $tableName )
    {
        // Stores the extension key and full table (instance) name
        $this->_extKey                      = $extKey;
        $this->_instanceName                = $tableName;
        
        // Gets the extension paths (absolute and relative)
        $this->_extPath                     = t3lib_extMgm::extPath( $this->_extKey );
        $this->_extRelPath                  = t3lib_extMgm::extRelPath( $this->_extKey );
        
        // Sets the table title
        $this->_ctrl[ 'title' ]             = 'LLL:EXT:'
                                            . $this->_extKey
                                            . '/lang/'
                                            . $this->_instanceName
                                            . '.xml:'
                                            . $this->_instanceName;
        
        // By default, the field used as label is 'uid'
        $this->_ctrl[ 'label' ]             = 'uid';
        
        // Sets the icon file path (by default, in the extension's res/img directory)
        $this->_ctrl[ 'iconfile' ]          = $this->_extRelPath
                                            . 'res/img/'
                                            . $this->_instanceName
                                            . '.gif';
        
        // Sets the field used to store the creation date
        $this->_ctrl[ 'cdrate' ]            = 'crdate';
        
        // Sets the field used to store the modification date
        $this->_ctrl[ 'tstamp' ]            = 'tstamp';
        
        // Sets the field used to store the ID of the BE user who created the record
        $this->_ctrl[ 'cruser_id' ]         = 'cruser_id';
        
        // Special fields configuration array (for hidden, starttime, endtime and fe_group)
        $this->_ctrl[ 'enablecolumns' ]     = array();
        
        // String to prepend when a record is copied
        $this->_ctrl[ 'prependAtCopy' ]     = ' (copy %s)';
        
        // Use --div-- markers to create tabs in the TCE forms
        $this->_ctrl[ 'dividers2tabs' ]     = true;
        
        // By default, records are sorted by 'uid'
        $this->setDefaultSortBy( 'uid' );
    }
    
    /**
     * Gets a property of the table control section
     * 
     * @param   string  The name of the property
     * @return  mixed   The value of the property
     */
    public function __get( $name )
    {
        // Checks if the property exists
        if( isset( $this->_ctrl[ ( string )$name ] ) ) {
            
            // Returns the value of the property
            return $this->_ctrl[ ( string )$name ];
        }
        
        // No such property
        return false;
    }
    
    /**
     * Sets a property of the table control section
     * 
     * @param   string  The name of the property
     * @param   mixed   The value of the property
     * @return  NULL
     */
    public function __set( $name, $value )
    {
        $this->_ctrl[ ( string )$name ] = $value;
    }
    
    /**
     * Checks if a property of the table control section exists
     * 
     * @param   string  The name of the property
     * @return  boolean Wheter the property is defined
     */
    public function __isset( $name )
    {
        return isset( $this->_ctrl[ ( string )$name ] );
    }
    
    /**
     * Removes a property of the table control section
     * 
     * @param   string  The name of the property
     * @return  NULL
     */
    public function __unset( $name )
    {
        unset( $this->_ctrl[ ( string )$name ] );
    }
    
    /**
     * Converts the TCA table object to a serialized array
     * 
     * @return  string  The serialized array
     */
    public function __toString()
    {
        return serialize( $this->toArray() );
    }
    
    /**
     * Gets an instance of a TCA table
     * 
     * If the specified instance does not exist, this method will create it,
     * and then return it.
     * 
     * @param   string              The extension key
     * @param   string              The short name name of the table (will be prepended with the extension key, in accordance to the TYPO3 coding guidelines)
     * @return  tx_oop_Tca_Table    The instance of the TCA table
     */
    public static function getInstance( $extKey, $name )
    {
        // Checks if the requested instance already exists
        if( !isset( self::$_instances[ $name ] ) ) {
            
            // Creates the full table name
            $tableName                     = 'tx_'
                                           . str_replace( '_', '', $extKey )
                                           . '_'
                                           . $name;
            
            // Creates a new instance for the requested table
            self::$_instances[ $tableName ] = new self( ( string )$extKey, $tableName );
            self::$_nbInstances++;
        }
        
        // Returns the requested instance
        return self::$_instances[ $tableName ];
    }
    
    /**
     * Adds a field for the current instance (table)
     * 
     * @param   string                      The name of the field
     * @param   string                      The type of the field (see TYPO3 Core API, TCA section)
     * @return  tx_oop_Tca_Field            The TCA field instance
     * @throws  tx_oop_Tca_Table_Exception  If the field type is invalid
     */
    private function _addField( $name, $type )
    {
        // Checks the field type to get the field object class
        switch( $type ) {
            
            // Valid TYPO3 field types
            case 'input':
            case 'text':
            case 'check':
            case 'radio':
            case 'select':
            case 'group':
            case 'none':
            case 'passthrough':
            case 'user':
            case 'flex':
            case 'inline':
                
                // Field class
                $fieldClass = 'tx_oop_Tca_Field_' . ucfirst( $type );
                break;
            
            // Invalid field type
            default:
                
                // Exception, as the field type is not recognized
                throw new tx_oop_Tca_Table_Exception(
                    'The requested field type (' . $type . ') for field \'' . $name . '\' is not a valid TYPO3 TCA field.',
                    tx_oop_Tca_Table_Exception::EXCEPTION_INVALID_FIELD_TYPE
                );
        }
        
        // Create the field object and stores it to the fields array
        $this->_fields[ $name ] = new $fieldClass( $this, $name );
        
        // Returns the field object
        return $this->_fields[ $name ];
    }
    
    /**
     * Converts the current TCA object to an array, for use with TYPO3
     * 
     * @return  array   The TCA array for the current instance
     */
    public function toArray()
    {
        $tca = array(
            'ctrl'      => $this->_ctrl,
            'interface' => array(
                'showRecordFieldList' => implode( ',', $this->_userFields )
            ),
            'columns'   => array(),
            'types'     => array(),
            'palettes'  => array()
        );
        
        // Process each field
        foreach( $this->_fields as $fieldName => $fieldObject ) {
            
            // Converts the field object to a PHP array
            $tca[ 'columns' ][ $fieldName ] = $fieldObject->toArray();
        }
        
        return $tca;
    }
    
    /**
     * Stores the current TCA object to the TYPO3 TCA array
     * 
     * @return  array   The TCA array for the current instance (by reference)
     */
    public function &storeToTca()
    {
        // Converts and stores the TCA array for the current instance
        $GLOBALS[ 'TCA' ][ $this->_instanceName ] = $this->toArray();
        
        // Returns a reference to the stored TCA array
        return $GLOBALS[ 'TCA' ][ $this->_instanceName ];
    }
    
    /**
     * Gets the full table name for the current instance
     * 
     * @return  string  The full table name
     */
    public function getTableName()
    {
        return $this->_instanceName;
    }
    
    /**
     * Gets the full table name for the current instance
     * 
     * @return  string  The full table name
     */
    public function getExtensionKey()
    {
        return $this->_extKey;
    }
    
    /**
     * Adds a field for the current instance (table)
     * 
     * @param   string                      The name of the field
     * @param   string                      The type of the field (see TYPO3 Core API, TCA section)
     * @return  tx_oop_Tca_Field            The TCA field instance
     * @throws  tx_oop_Tca_Table_Exception  If the field name is reserved is invalid
     * @throws  tx_oop_Tca_Table_Exception  If the field name is reserved is reserved
     */
    public function addField( $name, $type )
    {
        // Ensure passed values are string
        $name = ( string )$name;
        $type = ( string )$type;
        
        // Checks if the field already exists
        if( isset( $this->_fields[ $name ] ) ) {
            
            // Returns the existing field
            return $this->_fields[ $name ];
        }
        
        // Checks the field name
        if( isset( self::$_reserved[ $name ] ) ) {
            
            // Reserved field name
            throw new tx_oop_Tca_Table_Exception(
                'The field name \'' . $name . '\' is reserved.',
                tx_oop_Tca_Table_Exception::EXCEPTION_RESERVED_FIELD_NAME
            );
        }
        
        // Registers the field name
        $this->_userFields[] = $name;
        
        // Creates and returns the field
        return $this->_addField( $name, $type );
    }
    
    /**
     * Sets the default 'SORT BY' clause for current instance (table)
     * 
     * @param   string  The name of the sort field
     * @param   boolean True if the sort is descending, otherwise false
     * @return  NULL
     */
    public function setDefaultSortBy( $name, $desc = false )
    {
        $this->_ctrl[ 'default_sortby' ] = ( ( boolean )$desc ) ? 'ORDER BY ' . $name . ' DESC' : 'ORDER BY ' . $name;
    }
    
    /**
     * Gets a specific field
     * 
     * @return  tx_oop_Tca_Field            The requested field
     * @throws  tx_oop_Tca_Table_Exception  If the field does not exist
     */
    public function getField( $name )
    {
        // Checks if the field exists
        if( isset( $this->_fields[ $name ] ) ) {
            
            // Returns the field object
            return $this->_fields[ $name ];
        }
        
        // No such field
        throw new tx_oop_Tca_Table_Exception(
            'The requested field does not exist (' . $name . '). You should first add it with the ' . __CLASS__ . '::addField() method.',
            tx_oop_Tca_Table_Exception::EXCEPTION_NO_FIELD
        );
    }
    
    /**
     * Adds the 'hidden' field
     * 
     * @param   boolean                 Wheter the checkbox is checked by default or not
     * @param   string                  The name of the field (default is 'hidden')
     * @return  tx_oop_Tca_Field_Check  The 'hidden' field object
     */
    public function addHiddenField( $checked = false, $name = 'hidden' )
    {
        // Checks if a field is already registered
        if( isset( $this->_ctrl[ 'enablecolumns' ][ 'disabled' ] ) ) {
            
            // Removes the previous field
            unset( $this->_fields[ $this->_ctrl[ 'enablecolumns' ][ 'disabled' ] ] );
        }
        
        // Creates the field
        $field          = $this->_addField( ( string )$name, 'check' );
        
        // Sets the default value
        $field->default = ( int )$checked;
        
        // Sets the field label
        $field->label   = 'LLL:EXT:lang/locallang_general.xml:LGL.hidden';
        
        // Checks for the 'enablecolumns' property
        if( !isset( $this->_ctrl[ 'enablecolumns' ] )
            || !is_array( $this->_ctrl[ 'enablecolumns' ] )
        ) {
            
            // Creates the array
            $this->_ctrl[ 'enablecolumns' ] = array();
        }
        
        // Enables the field
        $this->_ctrl[ 'enablecolumns' ][ 'disabled' ] = ( string )$name;
        
        // Returns the field
        return $field;
    }
    
    /**
     * Adds the 'starttime' field
     * 
     * @param   string                  The name of the field (default is 'starttime')
     * @return  tx_oop_Tca_Field_Input  The 'starttime' field object
     */
    public function addStartTimeField( $name = 'starttime' )
    {
        // Checks if a field is already registered
        if( isset( $this->_ctrl[ 'enablecolumns' ][ 'starttime' ] ) ) {
            
            // Removes the previous field
            unset( $this->_fields[ $this->_ctrl[ 'enablecolumns' ][ 'starttime' ] ] );
        }
        
        // Creates the field
        $field        = $this->_addField( ( string )$name, 'input' );
        
        // Sets the field label
        $field->label = 'LLL:EXT:lang/locallang_general.xml:LGL.starttime';
        
        // Add the eval rule
        $field->addEval( 'date' );
        
        // Configures the field options
        $field->size     = 8;
        $field->max      = 20;
        $field->checkbox = '0';
        $field->default  = '0';
        
        // Checks for the 'enablecolumns' property
        if( !isset( $this->_ctrl[ 'enablecolumns' ] )
            || !is_array( $this->_ctrl[ 'enablecolumns' ] )
        ) {
            
            // Creates the array
            $this->_ctrl[ 'enablecolumns' ] = array();
        }
        
        // Enables the field
        $this->_ctrl[ 'enablecolumns' ][ 'starttime' ] = ( string )$name;
        
        // Returns the field
        return $field;
    }
    
    /**
     * Adds the 'endtime' field
     * 
     * @param   string                  The name of the field (default is 'endtime')
     * @return  tx_oop_Tca_Field_Input  The 'endtime' field object
     */
    public function addEndTimeField( $name = 'endtime' )
    {
        // Checks if a field is already registered
        if( isset( $this->_ctrl[ 'enablecolumns' ][ 'endtime' ] ) ) {
            
            // Removes the previous field
            unset( $this->_fields[ $this->_ctrl[ 'enablecolumns' ][ 'endtime' ] ] );
        }
        
        // Creates the field
        $field           = $this->_addField( ( string )$name, 'input' );
        
        // Adds a checkbox
        $field->checkbox = true;
        
        // Sets the field label
        $field->label    = 'LLL:EXT:lang/locallang_general.xml:LGL.endtime';
        
        // Add the eval rule
        $field->addEval( 'date' );
        
        // Configures the field options
        $field->size     = 8;
        $field->max      = 20;
        $field->checkbox = '0';
        $field->default  = '0';
        
        // Sets the range
        $field->setRange(
            mktime( 3, 14, 7, 1, 19, 2038 ),
            mktime( 0, 0, 0, date( 'm' ) - 1, date( 'd' ), date( 'Y' ) )
        );
        
        // Checks for the 'enablecolumns' property
        if( !isset( $this->_ctrl[ 'enablecolumns' ] )
            || !is_array( $this->_ctrl[ 'enablecolumns' ] )
        ) {
            
            // Creates the array
            $this->_ctrl[ 'enablecolumns' ] = array();
        }
        
        // Enables the field
        $this->_ctrl[ 'enablecolumns' ][ 'endtime' ] = ( string )$name;
        
        // Returns the field
        return $field;
    }
    
    /**
     * Adds the 'fe_group' field
     * 
     * @param   string                  The name of the field (default is 'fe_group')
     * @return  tx_oop_Tca_Field_Select The 'fe_group' field object
     */
    public function addFeUserGroupField( $name = 'fe_group' )
    {
        // Checks if a field is already registered
        if( isset( $this->_ctrl[ 'enablecolumns' ][ 'fe_group' ] ) ) {
            
            // Removes the previous field
            unset( $this->_fields[ $this->_ctrl[ 'enablecolumns' ][ 'fe_group' ] ] );
        }
        
        // Creates the field
        $field = $this->_addField( ( string )$name, 'select' );
        
        // Sets the foreign table
        $field->foreign_table = 'fe_groups';
        
        // Sets the field label
        $field->label         = 'LLL:EXT:lang/locallang_general.xml:LGL.fe_groups';
        
        // Adds the select items
        $field->addItem(  0,        '' );
        $field->addItem( -1,        'LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login' );
        $field->addItem( -2,        'LLL:EXT:lang/locallang_general.xml:LGL.any_login' );
        $field->addItem( '--div--', 'LLL:EXT:lang/locallang_general.xml:LGL.usergroups' );
        
        // Checks for the 'enablecolumns' property
        if( !isset( $this->_ctrl[ 'enablecolumns' ] )
            || !is_array( $this->_ctrl[ 'enablecolumns' ] )
        ) {
            
            // Creates the array
            $this->_ctrl[ 'enablecolumns' ] = array();
        }
        
        // Enables the field
        $this->_ctrl[ 'enablecolumns' ][ 'fe_group' ] = ( string )$name;
        
        // Returns the field
        return $field;
    }
    
    /**
     * 
     */
    public function enableVersionning( $origUid = 't3_origuid' )
    {
        $this->_ctrl[ 'versioningWS' ] = true;
        $this->_ctrl[ 'origUid' ]      = $origUid;
        
        $label        = $this->_addField( 't3ver_label', 'input' );
        $label->label = 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel';
        $label->size  = 30;
        $label->max   = 30;
    }
    
    /**
     * 
     */
    public function enableLocalization( $languageField = 'sys_language_uid', $origPointerField = 'l10n_parent', $diffSourceField = 'l10n_diffsource' )
    {
        $this->_ctrl[ 'languageField' ]            = $languageField;
        $this->_ctrl[ 'transOrigPointerField' ]    = $origPointerField;
        $this->_ctrl[ 'transOrigDiffSourceField' ] = $diffSourceField;
        
        $language                      = $this->_addField( $languageField, 'select' );
        $language->label               = 'LLL:EXT:lang/locallang_general.xml:LGL.language';
        $language->foreign_table       = 'sys_language';
        $language->foreign_table_where = 'ORDER BY sys_language.title';
        $language->addItem( -1, 'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages' );
        $language->addItem(  0, 'LLL:EXT:lang/locallang_general.xml:LGL.default_value' );
        
        $origPointer                      = $this->_addField( $origPointerField, 'select' );
        $origPointer->label               = 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent';
        $origPointer->displayCond         = 'FIELD:' . $languageField . ':>:0';
        $origPointer->foreign_table       = $this->_instanceName;
        $origPointer->foreign_table_where = 'AND '
                                          . $this->_instanceName
                                          . '.pid=###CURRENT_PID### AND '
                                          . $this->_instanceName
                                          . '.' . $languageField
                                          . ' IN (-1,0)';
        $origPointer->addItem( 0, '' );
        
        $diffSource  = $this->_addField( $diffSourceField, 'passthrough' );
    }
}
