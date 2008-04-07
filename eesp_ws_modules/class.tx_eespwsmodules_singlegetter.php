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
 *              public function __get( $fieldName )
 * 
 * SECTION:     3 - Public class methods
 *              public function soapRequest
 *              public function setSoapArg( $name, $value )
 *              public function unsetSoapArg( $name )
 * 
 *              TOTAL FUNCTIONS: 10
 */

class tx_eespwsmodules_singleGetter
{
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
    
    /**
     * PHP getter method
     * 
     * @param   string  $name       The property name
     * @return  mixed   The requested property
     */
    public function __get( $name )
    {
        if( is_object( $this->_xml ) ) {
            
            switch( $name ) {
                
                case 'title':
                    
                    return ( string )$this->_xml->title;
                
                case 'formation':
                    
                    return ( string )$this->_xml->pec_06->formation;
                
                case 'level':
                    
                    return ( string )$this->_xml->pec_06->niveau;
                
                case 'organisation':
                    
                    return ( string )$this->_xml->pec_06->organisation;
                
                case 'language':
                    
                    return ( string )$this->_xml->pec_06->langue;
                
                case 'prerequisites':
                    
                    return ( string )$this->_xml->pec_06->prerequis;
                
                case 'goals':
                    
                    return ( string )$this->_xml->pec_06->objectifs;
                
                case 'content':
                    
                    return ( string )$this->_xml->pec_06->contenu;
                
                case 'evaluation':
                    
                    return ( string )$this->_xml->pec_06->evaluation;
                
                case 'remediation':
                    
                    return ( string )$this->_xml->pec_06->remediation;
                
                case 'comments':
                    
                    return ( string )$this->_xml->pec_06->remarques;
                
                case 'bibliography':
                    
                    return ( string )$this->_xml->pec_06->bibliographie;
                
                case 'number':
                    
                    return $this->_xml[ 'hes_code' ] . '-' . $this->_xml[ 'code' ];
                
                case 'domain':
                    
                    return $this->_xml[ 'hes_domain' ];
                
                case 'type':
                    
                    return $this->_xml[ 'type' ];
                
                case 'credits':
                    
                    return $this->_xml[ 'credits' ];
                
                case 'incharge':
                    
                    $inCharge = array();
                    $xPath    = $this->_xml->xpath( 'responsabilities/person[@resp="pedagogy"]' );
                    
                    if( is_array( $xPath ) ) {
                        
                        foreach( $xPath as $person ) {
                            
                            $inCharge[] = $person->lastname . ' ' . $person->firstname;
                        }
                        
                    }
                    
                    return $inCharge;
                
                case 'sections':
                    
                    $sections = array();
                    
                    foreach( $this->_xml->sections->attributes() as $name => $value ) {
                        
                        if( ( string )$value === '1' ) {
                            
                            $sections[] = strtoupper( $name );
                        }
                    }
                    
                    return $sections;
                
                default:
                    
                    return false;
            }
        }
        
        return false;
    }
    
    /***************************************************************
     * SECTION 2:
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
            $this->_xml         = @new SimpleXMLElement( $result->FourD_arg0 );
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
