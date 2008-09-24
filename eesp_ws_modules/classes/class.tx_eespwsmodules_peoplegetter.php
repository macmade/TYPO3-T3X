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

# $Id$

/**
 * EESP WS people getter class
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */
class tx_eespwsmodules_peopleGetter implements Iterator
{
    // Current position for the iterator methods
    protected $_iteratorIndex   = 0;
    
    // SOAP client
    protected $_soap            = NULL;
    
    // SimpleXML object
    protected $_xml             = NULL;
    
    // WSDL file URL
    protected $_wsdl            = '';
    
    // Name of the SOAP operation
    protected $_operation       = '';
    
    // SOAP arguments
    protected $_soapArgs        = array();
    
    // Result type, from the SOAP operation
    protected $_resultType      = 0;
    
    /***************************************************************
     * SECTION 1:
     * 
     * PHP methods
     ***************************************************************/
    
    /**
     * Class constructor
     * 
     * @param   string  $wsdl           The URL of the WSDL file
     * @param   string  $operation      The SOAP action to perform
     * @return  NULL
     */
    public function __construct( $wsdl, $operation )
    {
        // Sets the WSDL and SOAP properties
        $this->_wsdl        = ( string )$wsdl;
        $this->_operation   = ( string )$operation;
        
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
        // Storage for the current person
        $person = new stdClass();
        
        // Sets the person properties
        $person->lastName  = $this->_xml->NAMES->NAME[ $this->_iteratorIndex ]->NOM;
        $person->firstName = $this->_xml->NAMES->NAME[ $this->_iteratorIndex ]->PRENOM;
        $person->number    = $this->_xml->NAMES->NAME[ $this->_iteratorIndex ]->NUMBER;
        
        // Returns the current person
        return $person;
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
        // Checks the XML structure and the result type
        if( !is_object( $this->_xml ) || ( $this->_resultType !== 0 && $this->_resultType !== 3 ) ) {
            
            return false;
        }
        
        return isset( $this->_xml->NAMES->NAME[ $this->_iteratorIndex ] );
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
            $this->_xml        = new SimpleXMLElement( $result->FourD_arg0 );
            
            // Sets the result type
            $this->_resultType = ( int )$this->_xml->RESULT;
            
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
    
    /**
     * Gets the result type
     * 
     * @return  int The result type (0: one person foud - 1: unknow person - 2: request error - 3: many persons)
     */
    public function getResultType()
    {
        return $this->_resultType;
    }
    
    /**
     * Gets the number of people
     * 
     * @return  int The number of people
     */
    public function getNumberOfPeople()
    {
        // Checks the result
        if( isset( $this->_xml ) && ( $this->_resultType === 0 || $this->_resultType === 3 ) ) {
           
           // Returns the number of entries
           return count( $this->_xml->NAMES->NAME );
        }
        
        // No result
        return 0;
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/classes/class.tx_eespwsmodules_peoplegetter.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/classes/class.tx_eespwsmodules_peoplegetter.php']);
}
