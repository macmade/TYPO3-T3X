<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (info@macmade.net)
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
 * A class to import swiss districts data from a web service.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.1
 */
 
 /**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *       57:        fincal class tx_vdmunicipalities_import
 *       97:        public function __construct( $wsdl = '', $soapOperation = '', $soapParameters = '', $xmlNodes = '' )
 *      111:        public function rewind
 *      121:        public function current
 *      136:        public function key
 *      151:        public function next
 *      161:        public function valid
 *      176:        private function _checkInfos
 *      190:        public function getData
 *      304:        public function parseXml
 *      364:        public function setWsdl( $file )
 *      375:        public function setSoapOperation( $name )
 *      386:        public function setSoapParameters( $parameters )
 *      397:        public function setXmlNodes( $nodes )
 *      408:        public function getSimpleXmlExceptionCode
 *      418:        public function getSimpleXmlExceptionMsg
 *      428:        public function getSoapExceptionCode
 *      438:        public function getSoapExceptionMsg
 * 
 *              TOTAL FUNCTIONS: 17
 */

final class tx_vdmunicipalities_import implements Iterator
{
    // SimpleXML object
    private $_xml                = NULL;
    
    // SimpleXML exception object
    private $_simpleXmlException = NULL;
    
    // SOAP client object
    private $_soapClient         = NULL;
    
    // SOAP exception object
    private $_soapException      = NULL;
    
    // Location of the WSDL file
    private $_wsdl               = '';
    
    // SOAP operation to use
    private $_soapOperation      = '';
    
    // SOAP result
    private $_soapResult         = array();
    
    // SOAP parameters to pass to the operation
    private $_soapParameters     = array();
    
    // XML nodes to find the municipalities nodes
    private $_xmlNodes           = array();
        
    // Current position for the iterator methods
    private $_iteratorIndex      = 0;
    
    /**
     * Class constructor
     * 
     * @param   $host   string  The hostname used for the connection
     * @param   $port   int     The port used for the connection
     * @param   $file   string  The file to fetch
     * @return  NULL
     */
    public function __construct( $wsdl = '', $soapOperation = '', $soapParameters = '', $xmlNodes = '' )
    {
        // Set properties
        $this->_wsdl           = ( string )$wsdl;
        $this->_soapOperation  = ( string )$soapOperation;
        $this->_soapParameters = explode( ',', ( string )$soapParameters );
        $this->_xmlNodes       = explode( ',', ( string )$xmlNodes );
    }
    
    /**
     * Move position to the previous element (Iterator method)
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
        // Only process if XML response from SOAP has been processed
        if( !is_object( $this->_xml ) ) {
            return false;
        }
        
        return $this->_xml->commune[ $this->_iteratorIndex ];
    }
    
    /**
     * Get current element's key (Iterator method)
     * 
     * @return  int     The municipality ID
     */
    public function key()
    {
        // Only process if XML response from SOAP has been processed
        if( !is_object( $this->_xml ) ) {
            return false;
        }
        
        return ( int )$this->_xml->commune[ $this->_iteratorIndex ][ 'no_com_can' ];
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
        // Only process if XML response from SOAP has been processed
        if( !is_object( $this->_xml ) ) {
            return false;
        }
        
        return isset( $this->_xml->commune[ $this->_iteratorIndex ] );
    }
    
    /**
     * Verify connection parameters
     * 
     * @return  boolean
     */
    private function _checkInfos()
    {
        return $this->_wsdl &&
               $this->_soapOperation &&
               count( $this->_soapParameters ) &&
               count( $this->_xmlNodes );
    }
    
    /**
     * Connects to the web service and gets the file with the XML data
     * 
     * @return  boolean
     * @see     _checkInfos
     */
    public function getData()
    {
        // Reset some properties
        $this->_data               = '';
        $this->_xml                = NULL;
        $this->_simpleXmlException = NULL;
        
        // Checks the required properties
        if( $this->_checkInfos() ) {
            
            // SOAP client can produce excpetions
            try {
                
                // Creates a new SOAP client
                $this->_soapClient = new SoapClient( $this->_wsdl );
                
                // Checks the number of parameters to pass to the operation
                switch( count( $this->_soapParameters ) ) {
                    
                    case 1:
                        
                        $data = $this->_soapClient->{$this->_soapOperation}(
                            $this->_soapParameters[ 0 ]
                        );
                        break;
                    
                    case 2:
                        
                        $data = $this->_soapClient->{$this->_soapOperation}(
                            $this->_soapParameters[ 0 ],
                            $this->_soapParameters[ 1 ]
                        );
                        break;
                    
                    case 3:
                        
                        $data = $this->_soapClient->{$this->_soapOperation}(
                            $this->_soapParameters[ 0 ],
                            $this->_soapParameters[ 1 ],
                            $this->_soapParameters[ 2 ]
                        );
                        break;
                    
                    case 4:
                        
                        $data = $this->_soapClient->{$this->_soapOperation}(
                            $this->_soapParameters[ 0 ],
                            $this->_soapParameters[ 1 ],
                            $this->_soapParameters[ 2 ],
                            $this->_soapParameters[ 3 ]
                        );
                        break;
                    
                    case 5:
                        
                        $data = $this->_soapClient->{$this->_soapOperation}(
                            $this->_soapParameters[ 0 ],
                            $this->_soapParameters[ 1 ],
                            $this->_soapParameters[ 2 ],
                            $this->_soapParameters[ 3 ],
                            $this->_soapParameters[ 4 ]
                        );
                        break;
                }
                
                // Gets each line of the result string
                $this->_soapResult = explode( chr( 10 ), $data );
                
            } catch( Exception $exception ) {
                
                // Stores the exception object
                $this->_soapException = $exception;
                return false;
            }
            
            // XML declaration flag
            $xmlDeclaration = false;
            
            // Process each line of the SOAP result to correct errors
            foreach( $this->_soapResult as $index => $line ) {
                
                // Checks for the XML decaration
                if( strstr( $line, '<?xml' ) ) {
                    
                    // Checks if the XML declaration has already been declared
                    if( $xmlDeclaration == false ) {
                        
                        // Sets the XML declaration flag
                        $xmlDeclaration = true;
                        
                    } else {
                        
                        // Do not store additionnal XML declaration
                        // This would make the parser crash!
                        unset( $this->_soapResult[ $index ] );
                        continue;
                    }
                }
                
                // Decodes the HTML entities and stores the current line
                $this->_soapResult[ $index ] = html_entity_decode( $line, false, 'UTF-8' );
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Parses the XML data using SimpleXML
     * 
     * @retrun  boolean
     */
    public function parseXml()
    {
        // Checks for data from SOAP
        if( count( $this->_soapResult ) ) {
            
            // SimpleXML can produce exceptions
            try {
                
                // Creates a new SimpleXMLElement with the data from SOAP
                $xml = new SimpleXMLElement( implode( chr( 10 ), $this->_soapResult ) );
                
                // Checks the number of nodes
                switch( count( $this->_xmlNodes ) ) {
                    
                    case 1:
                        
                        $this->_xml = $xml;
                        break;
                    
                    case 2:
                        
                        $this->_xml = $xml->{$this->_xmlNodes[ 1 ]};
                        break;
                    
                    case 3:
                        
                        $this->_xml = $xml->{$this->_xmlNodes[ 1 ]}->{$this->_xmlNodes[ 2 ]};
                        break;
                    
                    case 4:
                        
                        $this->_xml = $xml->{$this->_xmlNodes[ 1 ]}->{$this->_xmlNodes[ 2 ]}->{$this->_xmlNodes[ 3 ]};
                        break;
                    
                    case 5:
                        
                        $this->_xml = $xml->{$this->_xmlNodes[ 1 ]}->{$this->_xmlNodes[ 2 ]}->{$this->_xmlNodes[ 3 ]}->{$this->_xmlNodes[ 4 ]};
                        break;
                }
                
                return true;
                
            } catch( Exception $exception ) {
                
                // Stores the exception object
                $this->_simpleXmlException = $exception;
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Sets the WSDL file to use
     * 
     * @return  boolean
     */
    public function setWsdl( $file )
    {
        $this->_wsdl = ( string )$file;
        return true;
    }
    
    /**
     * Sets the SOAP operation to call
     * 
     * @return  boolean
     */
    public function setSoapOperation( $name )
    {
        $this->_soapOperation = ( string )$name;
        return true;
    }
    
    /**
     * Sets the SOAP parameters that will be passed to the operation
     * 
     * @return  boolean
     */
    public function setSoapParameters( $parameters )
    {
        $this->_soapParameters = explode( ',', ( string )$parameters );
        return true;
    }
    
    /**
     * Sets the XML structure required to find the municipalities nodes
     * 
     * @return  boolean
     */
    public function setXmlNodes( $nodes )
    {
        $this->_xmlNodes = explode( ',', ( string )$nodes );
        return true;
    }
    
    /**
     * Gets the SimpleXML exception number
     * 
     * @return  int The SimpleXML exception number
     */
    public function getSimpleXmlExceptionCode()
    {
        return $this->_simpleXmlException->getCode();
    }
    
    /**
     * Gets the SimpleXML exception message
     * 
     * @return  string  The SimpleXML exception message
     */
    public function getSimpleXmlExceptionMsg()
    {
        return $this->_simpleXmlException->getMessage();
    }
    
    /**
     * Gets the SOAP exception number
     * 
     * @return  int The SOAP exception number
     */
    public function getSoapExceptionCode()
    {
        return $this->_soapException->getCode();
    }
    
    /**
     * Gets the SOAP exception message
     * 
     * @return  string  The SOAP exception message
     */
    public function getSoapExceptionMsg()
    {
        return $this->_soapException->getMessage();
    }
}
