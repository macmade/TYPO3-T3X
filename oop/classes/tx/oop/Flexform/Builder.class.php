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
 * Flexform builder class
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_Flexform_Builder
{
    /**
     * An array with the flexform sheets
     */
    protected $_sheets       = array();
    
    /**
     * Wheter to disable the handling of localizations
     */
    protected $_langDisable  = true;
    
    /**
     * Wheter the localizations are bound to the defaut values
     */
    protected $_langChildren = false;
    
    /**
     * Class constructor
     * 
     * @param   boolean Wheter to disable the handling of localizations
     * @param   boolean Wheter the localizations are bound to the defaut values
     * @return  NULL
     */
    public function __construct( $langDisable = true, $langChildren = false )
    {
        $this->_langDisable  = $langDisable;
        $this->_langChildren = $langChildren;
    }
    
    /**
     * Converts the flexform object to an XML string
     * 
     * @return  string  The XML for the flexform
     */
    public function __toString()
    {
        // Gets the simple XML object
        $xml = $this->createXmlObject();
        
        // Returns the XML code
        return $xml->asXML();
    }
    
    /**
     * Gets the flexform as a simple XML object
     * 
     * @return  SimpleXMLElement    The flexform XML object
     */
    public function createXmlObject()
    {
        // Creates the XML object
        $xml                     = new SimpleXMLElement( '<T3DataStructure></T3DataStructure>' );
        
        // Adds the localization options
        $xml->meta->langDisable  = ( int )$this->_langDisable;
        $xml->meta->langChildren = ( int )$this->_langChildren;
        
        // Process each sheet
        foreach( $this->_sheets as $key => $value ) {
            
            // Creates the sheet XML object
            $value->createXmlObject( $xml );
        }
        
        // Returns the XML object
        return $xml;
    }
    
    /**
     * Adds a sheet to the flexform
     * 
     * @param   string                              The name of the sheet
     * @return  tx_oop_Flexform_Sheet               The sheet object
     * @throws  tx_oop_Flexform_Builder_Exception   If the sheet already exists
     */
    public function addSheet( $name )
    {
        // Checks if the sheet already exists
        if( isset( $this->_sheets[ $name ] ) ) {
            
            // The sheet already exists
            throw new tx_oop_Flexform_Builder_Exception(
                'The sheet \'' . $name . '\' already exists. Please use the ' . __CLASS__ . '::getSheet() method instead.',
                tx_oop_Flexform_Builder_Exception::EXCEPTION_SHEET_ALREADY_EXISTS
            );
        }
        
        // Creates the sheet
        $this->_sheets[ $name ] = new tx_oop_Flexform_Sheet( $name );
        
        // Returns the sheet
        return $this->_sheets[ $name ];
    }
    
    /**
     * Gets a flexform sheet
     * 
     * @param   string                              The name of the sheet
     * @return  tx_oop_Flexform_Sheet               The sheet object
     * @throws  tx_oop_Flexform_Builder_Exception   If the sheet does not exist
     */
    public function getSheet( $name )
    {
        // Checks if the sheet exists
        if( isset( $this->_sheets[ $name ] ) ) {
            
            // Returns the sheet
            return $this->_sheets[ $name ];
        }
        
        // No such field
        throw new tx_oop_Flexform_Builder_Exception(
            'The requested sheet does not exist (' . $name . '). You should first add it with the ' . __CLASS__ . '::addSheet() method.',
            tx_oop_Flexform_Builder_Exception::EXCEPTION_NO_SHEET
        );
    }
    
    /**
     * Decides wheter to disable the handling of localizations
     * 
     * @param   boolean If true, the handling of localizations will be disabled
     * @return  NULL
     */
    public function setLangDisable( $value )
    {
        $this->_langDisable = ( boolean )$value;
    }
    
    /**
     * Decides wheter the localizations are bound to the defaut values
     * 
     * @param   boolean If true, the localizations will be bound to the defaut values
     * @return  NULL
     */
    public function setLangChildren( $value )
    {
        $this->_langChildren = ( boolean )$value;
    }
}
