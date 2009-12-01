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
 * Class for the flexform 'section' field
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  tx.oop.Flexform.Field
 */
class tx_oop_Flexform_Field_Section extends tx_oop_Flexform_Field
{
    /**
     * The type of the field
     */
    protected $_fieldType = 'array';
    
    /**
     * The elements (types) of the section
     */
    protected $_elements  = array();
    
    /**
     * 
     */
    public function addFieldXmlObject( SimpleXMLElement $el )
    {
        $name                             = $this->_name;
        $el->$name->type                  = $this->_fieldType;
        $el->$name->section               = 1;
        $el->$name->tx_templavoila->title = 'LLL:'
                                          . $this->_langFile
                                          . ':'
                                          . $this->_sheetName
                                          . '.'
                                          . $this->_name;
        $subEl                            = $el->$name->addChild( 'el' );
        
        foreach( $this->_elements as $key => $value ) {
            
            $value->addElementXmlObject( $subEl );
        }
    }
    
    /**
     * Adds an element to the section
     * 
     * @param   string                                  The name of the element
     * @return  tx_oop_Flexform_Field_Section_Element   The flexform section element object
     * @throws  tx_oop_Flexform_Field_Section_Exception If the element already exists
     */
    public function addElement( $name )
    {
        // Checks if the field already exists
        if( isset( $this->_elements[ $name ] ) ) {
            
            // The field already exists
            throw new tx_oop_Flexform_Field_Section_Exception(
                'The element \'' . $name . '\' already exists. Please use the ' . __CLASS__ . '::getElement() method instead.',
                tx_oop_Flexform_Field_Section_Exception::EXCEPTION_ELEMENT_ALREADY_EXISTS
            );
        }
        
        // Creates the field object
        $this->_elements[ $name ] = new tx_oop_Flexform_Field_Section_Element( $name, $this->_name, $this->_sheetName, $this->_langFile );
        
        // Returns the field
        return $this->_elements[ $name ];
    }
    
    /**
     * Gets a element from the section
     * 
     * @param   string                                  The name of the element
     * @return  tx_oop_Flexform_Field_Section_Element   The flexform section element object
     * @throws  tx_oop_Flexform_Field_Section_Exception If the element does not exists
     */
    public function getElement( $name )
    {
        // Checks if the field exists
        if( isset( $this->_elements[ $name ] ) ) {
            
            // Returns the field
            return $this->_elements[ $name ];
        }
        
        // No such field
        throw new tx_oop_Flexform_Field_Section_Exception(
            'The requested element does not exist (' . $name . '). You should first add it with the ' . __CLASS__ . '::addElement() method.',
            tx_oop_Flexform_Field_Section_Exception::EXCEPTION_NO_ELEMENT
        );
    }
}
