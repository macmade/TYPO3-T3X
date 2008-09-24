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
 * EESP WS modules list getter class
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */
class tx_eespwsmodules_listGetter implements Iterator
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
    
    // SimpleXML object for the current module
    protected $_currentModule   = NULL;
    
    // Current module ID
    protected $_currentId       = '';
    
    // Current module date
    protected $_currentDate     = '';
    
    // Current module comments
    protected $_currentComments = '';
    
    // Get modules by module, otherwise by date
    protected $_getByModule     = false;
    
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
     * @param   boolean $getByModule    Is set, get modules by module, otherwise by date
     * @return  NULL
     */
    public function __construct( $wsdl, $operation, $getByModule = false )
    {
        // Sets the WSDL and SOAP properties
        $this->_wsdl        = ( string )$wsdl;
        $this->_operation   = ( string )$operation;
        
        // Sets the getter mode
        $this->_getByModule = $getByModule;
        
        try {
            
            // Creates the SOAP client
            $this->_soap = new SoapClient( $this->_wsdl );
            
        } catch( Exception $e ) {
            
            // Cannot create the SOAP client
            throw $e;
        }
    }
    
    /**
     * PHP getter method
     * 
     * @param   string  $name       The property name
     * @return  mixed   The requested property
     */
    public function __get( $name )
    {
        // Checks if we have a current module
        if( is_object( $this->_currentModule ) ) {
            
            // Checks the property
            switch( $name ) {
                
                // Module's date
                case 'date':
                    
                    return $this->_currentDate;
                
                // Modules identifier (from 4D)
                case 'id':
                    
                    return $this->_currentId;
                
                // Comments for the current date
                case 'comments':
                    
                    return $this->_currentComments;
                
                // Module's number
                case 'number':
                    
                    return ( string )$this->_currentModule->MODULE_NR;
                
                // Number of ECTS credits
                case 'credits':
                    
                    return ( string )$this->_currentModule->CREDITS;
                
                // Module's domain
                case 'domain':
                    
                    return ( string )$this->_currentModule->DOMAINE;
                
                // Module's title
                case 'title':
                    
                    return ( string )$this->_currentModule->TITRE_HES;
                
                // Module's section
                case 'section':
                    
                    return ( string )$this->_currentModule->FILIERE;
                
                // Module's type
                case 'type':
                    
                    return ( string )$this->_currentModule->CARACTERE;
                
                // Module's subcode
                case 'subcode':
                    
                    return ( string )$this->_currentModule->MODULE_SUBCODE;
                
                // Person(s) in charge
                case 'incharge':
                    
                    // Storage
                    $incharge = array();
                    
                    // Process each person
                    foreach( $this->_currentModule->RESPONSABLES->children() as $person ) {
                        
                        // Adds the person
                        $incharge[] = ( string )$person;
                    }
                    
                    // Returns the array with the persons in charge
                    return $incharge;
                
                // Default processing
                default:
                    
                    // No such property
                    return false;
            }
        }
        
        // No module available
        return false;
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
        return $this->_currentDate;
    }
    
    /**
     * Get current element's key (Iterator method)
     * 
     * @return  int     The municipality ID
     */
    public function key()
    {
        return $this->_currentId;
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
        
        // Checks the getter mode
        if( $this->_getByModule ) {
            
            // Checks the XML structure
            if( isset( $this->_xml->MODULES->MODULE[ $this->_iteratorIndex ] ) ) {
                
                // Sets the informations about the current module
                $this->_currentModule = $this->_xml->MODULES->MODULE[ $this->_iteratorIndex ];
                $this->_currentId     = ( string )$this->_currentModule->MODULE_ID;
                
                // Checks the PHP version
                if( version_compare( PHP_VERSION, '5.2.4', '<' ) ) {
                    
                    // XPath expression to select the module's dates
                    $xPath = 'DATES/date[@moduleID="' . $this->_currentId . '"]';
                    
                } else {
                    
                    // XPath expression to select the module's dates
                    $xPath = 'date[@moduleID="' . $this->_currentId . '"]';
                }
                
                // Select all dates for the current module
                $modDates = $this->_xml->DATES->xpath( $xPath );
                
                // Storage
                $this->_currentDate     = array();
                $this->_currentComments = array();
                
                // Process each date
                foreach( $modDates as $date ) {
                    
                    // Date
                    $ts = ( string )$date[ 'ts' ];
                    
                    // Sets informations about dates
                    $this->_currentDate[]          = $ts;
                    $this->_currentComments[ $ts ] = ( string )$date[ 'remarque' ];
                }
                
                return true;
            }
            
            return false;
            
        } else {
            
            // Checks the XML structure
            if( isset( $this->_xml->DATES->date[ $this->_iteratorIndex ] ) ) {
                
                // Sets the informations for the current module
                $this->_currentDate     = ( string )$this->_xml->DATES->date[ $this->_iteratorIndex ][ 'ts' ];
                $this->_currentId       = ( string )$this->_xml->DATES->date[ $this->_iteratorIndex ][ 'moduleID' ];
                $this->_currentComments = ( string )$this->_xml->DATES->date[ $this->_iteratorIndex ][ 'remarque' ];
                
                // Checks the PHP version
                if( version_compare( PHP_VERSION, '5.2.4', '<' ) ) {
                    
                    // XPath expression to select the current module
                    $xPath = 'MODULES/MODULE[MODULE_ID="' . $this->_currentId . '"]';
                    
                } else {
                    
                    // XPath expression to select the current module
                    $xPath = 'MODULE[MODULE_ID="' . $this->_currentId . '"]';
                }
                
                // Stores the current module
                $this->_currentModule   = array_shift( $this->_xml->MODULES->xpath( $xPath ) );
                
                return true;
            }
            
            return false;
        }
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
            $this->_xml = new SimpleXMLElement( $result->FourD_arg0 );
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
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/classes/class.tx_eespwsmodules_listgetter.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/classes/class.tx_eespwsmodules_listgetter.php']);
}
