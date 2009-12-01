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
 * Class for the flexform section element
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  tx.oop.Flexform.Field.Section
 */
class tx_oop_Flexform_Field_Section_Element extends tx_oop_Flexform_Field_Section implements tx_oop_Flexform_Field_Section_Element_Interface
{
    /**
     * The name of the section
     */
    protected $_sectionName = '';
    
    /**
     * An array with the flexform fields/sections
     */
    protected $_fields      = array();
    
    /**
     * Class constructor
     * 
     * @param   string  The name of the field
     * @param   string  The name of the sheet
     * @param   string  The reference of the file with the labels for the flexform (typically: EXT:extkey/path/to/lang/file)
     * @return  NULL
     */
    public function __construct( $name, $sectionName, $sheetName, $langFile )
    {
        $this->_sectionName = ( string )$sectionName;
        parent::__construct( $name, $sheetName, $langFile );
    }
    
    /**
     * 
     */
    public function addElementXmlObject( SimpleXMLElement $el )
    {
        $name                             = $this->_name;
        $el->$name->type                  = $this->_fieldType;
        $el->$name->tx_templavoila->title = 'LLL:'
                                          . $this->_langFile
                                          . ':'
                                          . $this->_sheetName
                                          . '.'
                                          . $this->_sectionName
                                          . '.'
                                          . $this->_name;
        $subEl                            = $el->$name->addChild( 'el' );
        
        foreach( $this->_fields as $key => $value ) {
            
            $value->addFieldXmlObject( $subEl );
        }
    }
    
    /**
     * Adds a field to the element
     * 
     * @param   string                                          The name of the field
     * @param   string                                          The type of the field
     * @return  tx_oop_Flexform_Field                           The flexform field object
     * @throws  tx_oop_Flexform_Field_Section_Element_Exception If the field already exists
     * @throws  tx_oop_Flexform_Field_Section_Element_Exception If the field type is invalid
     */
    public function addField( $name, $type )
    {
        // Checks if the field already exists
        if( isset( $this->_fields[ $name ] ) ) {
            
            // The field already exists
            throw new tx_oop_Flexform_Field_Section_Element_Exception(
                'The field \'' . $name . '\' already exists. Please use the ' . __CLASS__ . '::getField() method instead.',
                tx_oop_Flexform_Field_Section_Element_Exception::EXCEPTION_FIELD_ALREADY_EXISTS
            );
        }
        
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
            case 'inline':
                
                // Field class
                $fieldClass = 'tx_oop_Flexform_Field_Section_Element_FIeld_' . ucfirst( $type );
                break;
            
            // Invalid field type
            default:
                
                // Exception, as the field type is not recognized
                throw new tx_oop_Flexform_Field_Section_Element_Exception(
                    'The requested field type (' . $type . ') for field \'' . $name . '\' is not a valid TYPO3 flexform field.',
                    tx_oop_Flexform_Field_Section_Element_Exception::EXCEPTION_INVALID_FIELD_TYPE
                );
        }
        
        // Creates the field object
        $this->_fields[ $name ] = new $fieldClass( $name, $this->_name, $this->_sectionName, $this->_sheetName, $this->_langFile );
        
        // Returns the field
        return $this->_fields[ $name ];
    }
    
    /**
     * Gets a field from the element
     * 
     * @param   string                                          The name of the field
     * @return  tx_oop_Flexform_Field                           The flexform field object
     * @throws  tx_oop_Flexform_Field_Section_Element_Exception If the field does not exists
     */
    public function getField( $name )
    {
        // Checks if the field exists
        if( isset( $this->_fields[ $name ] ) ) {
            
            // Returns the field
            return $this->_fields[ $name ];
        }
        
        // No such field
        throw new tx_oop_Flexform_Field_Section_Element_Exception(
            'The requested field does not exist (' . $name . '). You should first add it with the ' . __CLASS__ . '::addField() method.',
            tx_oop_Flexform_Field_Section_Element_Exception::EXCEPTION_NO_FIELD
        );
    }
}
