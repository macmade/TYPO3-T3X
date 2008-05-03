<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2004 macmade.net
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

/**
 * Flexform helper class
 *
 * This class is part of the Developer API (api_macmade) extension, and is
 * only available for PHP5.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - PHP methods
 *              public function __construct( &$xmlData )
 * 
 * SECTION:     2 - SPL Iterator methods
 *              public function rewind
 *              public function current
 *              public function key
 *              public function next
 *              public function valid
 * 
 * SECTION:     3 - Public class methods
 *              public function soapRequest
 *              public function setSoapArg( $name, $value )
 *              public function unsetSoapArg( $name )
 * 
 *              TOTAL FUNCTIONS: 10
 */

class tx_eespwsmodules_singleGetter implements Iterator
{
    // Current position for the iterator methods
    protected $_iteratorIndex    = 0;
    
    // SOAP client
    protected $_soap             = NULL;
    
    // SimpleXML object
    protected $_xml              = NULL;
    
    // SimpleXML object for the current section
    protected $_currentSection   = NULL;
    
    // WSDL file URL
    protected $_wsdl             = '';
    
    // Name of the SOAP operation
    protected $_operation        = '';
    
    // SOAP arguments
    protected $_soapArgs         = array();
    
    /***************************************************************
     * SECTION 1:
     * 
     * PHP methods
     ***************************************************************/
    
    /**
     * Class constructor
     * 
     * @param   string  $xmlData    The flexform XML data
     * @return  NULL
     */
    public function __construct( $wsdl, $operation )
    {
        // Sets the WSDL and SOAP properties
        $this->_wsdl      = ( string )$wsdl;
        $this->_operation = ( string )$operation;
        
        try {
            
            // Creates the SOAP client
            $this->_soap = new SoapClient( $this->_wsdl );
            
        } catch( Exception $e ) {
            
            // Cannot create the SOAP client
            throw $e;
        }
    }
    
    /***************************************************************
     * SECTION 2:
     * 
     * SPL Iterator methods
     ***************************************************************/
    
    /**
     * Move position to the first element (Iterator method)
     * 
     * @return  NULL
     */
    public function rewind()
    {
        $this->_iteratorIndex = 0;
    }
    
    /**
     * Get current element (Iterator method)
     * 
     * @return  SimpleXML   The municipality object
     */
    public function current()
    {
        // Storage
        $section = new stdClass();
        
        // Section header
        $section->title   = $this->_currentSection->HEADER;
        
        // Section content
        $section->content = array();
        
        // Process each node in the current section
        foreach( $this->_currentSection->children() as $key => $value ) {
            
            // Do not stores the header here
            if( $key === 'HEADER' ) {
                
                // Process the next node
                continue;
            }
            
            if( $key === 'ENSEIGNANT' || $key === 'RESPONSABLE' ) {
                
                // Checks if the storage place already exists
                if( !isset( $section->content[ $key ] ) ) {
                    
                    // Creates the storage for people names
                    $section->content[ $key ] = array();
                }
                
                // Storage for the person
                $person = array();
                
                // Process the current person
                foreach( $value->children() as $subKey => $subValue ) {
                    
                    // Adds the node value
                    $person[ $subKey ] = ( string )$subValue;
                }
                
                // Adds the person
                $section->content[ $key ][] = $person;
                
            } else {
                
                // Stores the value
                $section->content[ $key ] = ( string )$value;
            }
        }
        
        // Returns the section
        return $section;
    }
    
    /**
     * Get current element's key (Iterator method)
     * 
     * @return  int     The municipality ID
     */
    public function key()
    {
        return $this->_iteratorIndex;
    }
    
    /**
     * Move position to the next element (Iterator method)
     * 
     * @return  NULL
     */
    public function next()
    {
        $this->_iteratorIndex++;
    }
    
    /**
     * Checks current element (Iterator method)
     * 
     * @return  boolean
     */
    public function valid()
    {
        // Checks the XML structure
        if( !is_object( $this->_xml ) ) {
            
            return false;
        }
        
        // Section name
        $section = 'SECTION_' . $this->_iteratorIndex;
        
        // Checks the XML structure
        if( isset( $this->_xml->$section ) ) {
            
            // Stores the current section
            $this->_currentSection = $this->_xml->$section;
            
            return true;
        }
        
        return false;
    }
    
    /***************************************************************
     * SECTION 3:
     * 
     * Public class methods
     ***************************************************************/
    
    /**
     * Performs the SOAP request
     * 
     * @return  boolean
     */
    public function soapRequest()
    {
        // SOAP operation
        $operation = $this->_operation;
        
        // Calls the SOAP operation
        $result    = $this->_soap->$operation( $this->_soapArgs );
        
        // Checks the result
        if( !is_object( $result ) || !isset( $result->FourD_arg0 ) ) {
            
            // No response
            throw new Exception( 'No ressponse from the SOAP server' );
        }
        
        // Removes the SOAP client as it's not needed anymore
        $this->_soap = NULL;
        
        try {
            
            // Parses the XML data
            $this->_xml = @new SimpleXMLElement( $result->FourD_arg0 );
            return true;
            
        } catch( Exception $e ) {
            
            // Cannot parse the XML data
            throw $e;
        }
    }
    
    /**
     * Adds a SOAP argument for the operation
     * 
     * @param   string  $name   The name of the argument
     * @param   string  $value  The value of the argument
     * @return  boolean
     */
    public function setSoapArg( $name, $value )
    {
        $this->_soapArgs[ ( string )$name ] = ( string )$value;
        return true;
    }
    
    /**
     * Removes a SOAP argument for the operation
     * 
     * @param   string  $name   The name of the argument
     * @return  boolean
     */
    public function unsetSoapArg( $name )
    {
        unset( $this->_soapArgs[ ( string )$name ] );
        return true;
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/class.tx_eespwsmodules_singlegetter.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/class.tx_eespwsmodules_singlegetter.php']);
}
