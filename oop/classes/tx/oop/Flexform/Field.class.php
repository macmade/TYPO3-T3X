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
 * Abstract class for the flexform fields
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
abstract class tx_oop_Flexform_Field
{
    /**
     * The type of the field
     */
    protected $_fieldType  = '';
    
    /**
     * The name of the field
     */
    protected $_name       = '';
    
    /**
     * The name of the sheet
     */
    protected $_sheetName  = '';
    
    /**
     * The file with the language labels
     */
    protected $_langFile   = '';
    
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
     * @param   string  The name of the field
     * @param   string  The name of the sheet
     * @param   string  The reference of the file with the labels for the flexform (typically: EXT:extkey/path/to/lang/file)
     * @return  NULL
     */
    public function __construct( $name, $sheetName, $langFile )
    {
        $this->_name      = ( string )$name;
        $this->_sheetName = ( string )$sheetName;
        $this->_langFile  = ( string )$langFile;
        
        // Field can be excluded by default
        $this->_properties[ 'exclude' ] = true;
        
        // Field label
        $this->_properties[ 'label' ]   = 'LLL:'
                                        . $this->_langFile
                                        . ':'
                                        . $this->_sheetName
                                        . '.'
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
    protected function _writeConfigObject( array $configSection, SimpleXMLElement $xml )
    {
        foreach( $configSection as $key => $value ) {
            
            if( is_array( $value ) ) {
                
                if( is_int( $key ) ) {
                    
                    $child            = $xml->addChild( 'numIndex' );
                    $child[ 'index' ] = $key;
                    
                } else {
                    
                    $child = $xml->addChild( $key );
                }
                
                $child[ 'type' ] = 'array';
                
                $this->_writeConfigObject( $value, $child );
                
            } else {
                
                if( is_int( $key ) ) {
                    
                    $xml->numIndex[ $key ]            = $value;
                    $xml->numIndex[ $key ][ 'index' ] = $key;
                    
                } else {
                    
                    $xml->$key = $value;
                }
            }
        }
    }
    
    /**
     * 
     */
    public function addFieldXmlObject( SimpleXMLElement $sheetRoot )
    {
        $name  = $this->_name;
        
        foreach( $this->_properties as $key => $value ) {
            
            $sheetRoot->el->$name->TCEforms->$key = $value;
        }
        
        $sheetRoot->el->$name->TCEforms->config->type = $this->_fieldType;
        
        $this->_writeConfigObject(
            $this->_config,
            $sheetRoot->el->$name->TCEforms->config
        );
    }
}
