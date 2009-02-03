<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (macmade@eosgarden.com)
 * All rights reserved
 * 
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * 
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

################################################################################
# TO DO:
# 
# - displayCond
# - defaultExtra
################################################################################

/**
 * Abstract class for the TCA field object classes
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
abstract class tx_oop_tcaField
{
    /**
     * The type of the field
     */
    protected $_fieldType  = '';
    
    /**
     * The TCA table object in which the current field is registered
     */
    protected $_table      = NULL;
    
    /**
     * The name of the field
     */
    protected $_name       = '';
    
    /**
     * The instance (table) name
     */
    private $_tableName    = '';
    
    /**
     * The extension key
     */
    private $_extKey       = '';
    
    /**
     * The properties of the field
     */
    protected $_properties = array();
    
    /**
     * The properties of config section of the field
     */
    protected $_config     = array();
    
    /**
     * Class constructor
     * 
     * @param   tx_oop_tcaTable The TCA table object in which the current field is registered
     * @param   string          The field name
     */
    public function __construct( tx_oop_tcaTable $table, $name )
    {
        // Stores the TCA table object
        $this->_table                   = $table;
        
        // Stores the field name
        $this->_name                    = ( string )$name;
        
        $this->_extKey                  = $this->_table->getExtensionKey();
        
        $this->_tableName               = $this->_table->getTableName();
        
        // Field can be excluded by default
        $this->_properties[ 'exclude' ] = true;
        
        // Field label
        $this->_properties[ 'label' ]   = 'LLL:EXT:'
                                        . $this->_extKey
                                        . '/lang/'
                                        . $this->_tableName
                                        . '.xml:'
                                        . $this->_name;
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        switch( ( string )$name ) {
            
            case 'label':
            case 'exclude':
            case 'l10n_mode':
            case 'l10n_display':
            case 'l10n_cat':
            case 'displayCond':
            case 'defaultExtras':
                
                return ( isset( $this->_properties[ $name ] ) ) ? $this->_properties[ $name ] : false;
                break;
                
            default:
                
                return ( isset( $this->_config[ $name ] ) ) ? $this->_config[ $name ] : false;
                break;
        }
    }
    
    /**
     * 
     */
    public function __set( $name, $value )
    {
        switch( ( string )$name ) {
            
            case 'label':
            case 'exclude':
            case 'l10n_mode':
            case 'l10n_display':
            case 'l10n_cat':
            case 'displayCond':
            case 'defaultExtras':
                
                $this->_properties[ $name ] = ( string )$value;
                break;
                
            default:
                
                $this->_config[ $name ] = $value;
                break;
        }
    }
    
    /**
     * 
     */
    public function __isset( $name )
    {
        switch( ( string )$name ) {
            
            case 'label':
            case 'exclude':
            case 'l10n_mode':
            case 'l10n_display':
            case 'l10n_cat':
            case 'displayCond':
            case 'defaultExtras':
                
                return isset( $this->_properties[ $name ] );
                
            default:
                
                return isset( $this->_config[ $name ] );
                break;
        }
    }
    
    /**
     * 
     */
    public function __unset( $name )
    {
        switch( ( string )$name ) {
            
            case 'label':
            case 'exclude':
            case 'l10n_mode':
            case 'l10n_display':
            case 'l10n_cat':
            case 'displayCond':
            case 'defaultExtras':
                
                unset( $this->_properties[ $name ] );
                
            default:
                
                unset( $this->_config[ $name ] );
                break;
        }
    }
    
    /**
     * 
     */
    public function toArray()
    {
        $field = array();
        
        foreach( $this->_properties as $propName => $propValue ) {
            
            $field[ $propName ] = $propValue;
        } 
        
        $field[ 'config' ] = array();
        
        foreach( $this->_config as $propName => $propValue ) {
            
            $field[ 'config' ][ $propName ] = $propValue;
        }
        
        $field[ 'config' ][ 'type' ] = $this->_fieldType;
        
        return $field;
    }
}
