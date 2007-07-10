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
 * @version     1.0
 */
 
 /**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *      110:    public function __construct( $host = '', $port = '', $file = '' )
 *      123:    public function rewind
 *      133:    public function current
 *      143:    public function key
 *      153:    public function next
 *      163:    public function valid
 *      173:    private function _checkInfos
 *      184:    private function _fixHtmlEntities( $string )
 *      218:    public function getData
 *      336:    public function parseXml
 *      365:    public function setHost( $host )
 *      376:    public function setPort( $port )
 *      387:    public function setFile( $file )
 *      399:    public function getSockErrno
 *      409:    public function getSockErrstr
 *      419:    public function getResponseStatus
 *      429:    public function getResponseMsg
 *      439:    public function getResponseStatus
 *      449:    public function getResponseMsg
 * 
 *              TOTAL FUNCTIONS: 19
 */

final class tx_vdmunicipalities_import implements Iterator
{
    // The new line character for Unix sockets
    const SOCK_NL                = "\r\n";
    
    // The socket object used to retrieve data from the web service
    private $_socket             = NULL;
    
    // The error number from the socket
    private $_socketErrno        = 0;
    
    // The error message from the socket
    private $_socketErrstr       = '';
    
    // The HTTP status code from the response
    private $_responseStatus     = 0;
    
    // The HTTP status message from the response
    private $_responseStatusMsg  = 0;
    
    // The HTTP response headers
    private $_responseHeaders    = array();
    
    // The host used for the connection
    private $_host               = '';
    
    // The port used for the connection
    private $_port               = 0;
    
    // The XML file to get
    private $_file               = '';
    
    // The XML data
    private $_data               = '';
    
    // The SimpleXML object
    private $_xml                = NULL;
    
    // SimpleXML exception object
    private $_simpleXmlException = NULL;
    
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
    public function __construct( $host = '', $port = 0, $file = '' )
    {
        $this->_host = ( string )$host;
        $this->_port = ( int )$port;
        $file        = ( string )$file;
        $this->_file = ( substr( $file, 0, 1 ) != '/' ) ? '/' . $file : $file;
    }
    
    /**
     * Move position to the previous element (Iterator method)
     * 
     * @return  NULL
     */
    public function rewind() {
        
        $this->_iteratorIndex = 0;
    }
    
    /**
     * Get current element (Iterator method)
     * 
     * @return  SimpleXML   The municipality object
     */
    public function current() {
        
        return $this->_xml->xmlsql->canton->commune[ $this->_iteratorIndex ];
    }
    
    /**
     * Get current element's key (Iterator method)
     * 
     * @return  int     The municipality ID
     */
    public function key() {
        
        return ( int )$this->_xml->xmlsql->canton->commune[ $this->_iteratorIndex ][ 'no_com_can' ];
    }
    
    /**
     * Move position to the next element (Iterator method)
     * 
     * @return  NULL
     */
    public function next() {
        
        $this->_iteratorIndex++;
    }
    
    /**
     * Checks current element (Iterator method)
     * 
     * @return  boolean
     */
    public function valid() {
        
        return isset( $this->_xml->xmlsql->canton->commune[ $this->_iteratorIndex ] );
    }
    
    /**
     * Verify connection parameters
     * 
     * @return  boolean
     */
    private function _checkInfos()
    {
        return $this->_host && $this->_port && $this->_file;
    }
    
    /**
     * Converts HTML entities to their numerical equivalent
     * 
     * @param   $string string  The string to convert
     * @return  string  The string with numeric HTML entities
     */
    private function _fixHtmlEntities( $string )
    {
        // Static function variables
        static $translationTable;
        static $search;
        static $replace;
        
        // Checks if the translation table is already filled
        if( !is_array( $translationTable ) ) {
            
            // Gets the HTML translation table for HTML entities
            $translationTable = get_html_translation_table( HTML_ENTITIES );
            
            // Process each character
            foreach( $translationTable as $char => $entity ) {
                
                // HTML entity to search for
                $search[]  = $entity;
                
                // Replacement for the HTML entity
                $replace[] = '&#' . ord( $char ) . ';';
            }
        }
        
        // Replace HTML entities
        return str_replace( $search, $replace, $string );
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
        $this->_socketErrno        = 0;
        $this->_socketErrstr       = '';
        $this->_responseStatus     = 0;
        $this->_responseStatusMsg  = 0;
        $this->_responseHeaders    = array();
        $this->_data               = '';
        $this->_xml                = NULL;
        $this->_simpleXmlException = NULL;
        
        // Checks the required properties
        if( $this->_checkInfos() ) {
            
            // Opens a socket
            $this->_socket = fsockopen(
                $this->_host,
                $this->_port,
                $this->_socketErrno,
                $this->_socketErrstr
            );
            
            // Checks if the socket connection is valid
            if( $this->_socket ) {
                
                // Request for the file to get
                $request = 'GET ' . $this->_file . ' HTTP/1.1' . self::SOCK_NL
                         . 'Host:' . $this->_host . self::SOCK_NL
                         . 'Connection: Close' . self::SOCK_NL . self::SOCK_NL;
                
                // Puts the request in the socket
                fwrite( $this->_socket, $request );
                
                // Flags
                $headersSent    = false;
                $xmlDeclaration = false;
                
                // Gets the HTTP status from the response
                $status = explode( ' ', fgets( $this->_socket ) );
                
                // First part is not needed (it should only say HTTP/x.x)
                array_shift( $status );
                
                // HTTP status code
                $this->_responseStatus    = array_shift( $status );
                
                // HTTP status message
                $this->_responseStatusMsg = implode( ' ', $status );
                
                // Checks for a sucessfull response
                if( $this->_responseStatus == '200' ) {
                    
                    // Reads the response
                    while( !feof( $this->_socket ) ) {
                        
                        // Gets the current line
                        $line = fgets( $this->_socket );
                        
                        // Checks for the end of the HTTP headers
                        if( $line == self::SOCK_NL ) {
                            
                            // Sets the flag for headers and process next line
                            $headersSent = true;
                            continue;
                        }
                        
                        // Checks if we are reading HTTP headers or HTTP body
                        if( !$headersSent ) {
                            
                            // Gets and stores HTTP header parts
                            $headerParts                                 = explode( ': ', $line );
                            $this->_responseHeaders[ $headerParts[ 0 ] ] = $headerParts[ 1 ];
                            
                        } else {
                            
                            // Checks for the XML decaration
                            if( strstr( $line, '<?xml' ) ) {
                                
                                // Checks if the XML declaration has already been declared
                                // 
                                if( $xmlDeclaration == false ) {
                                    
                                    // Sets the XML declaration flag
                                    $xmlDeclaration = true;
                                    
                                } else {
                                    
                                    // Do not store additionnal XML declaration
                                    // This would make the parser crash!
                                    continue;
                                }
                            }
                            
                            // Decodes the HTML entities and stores the current line
                            $this->_data .= html_entity_decode( $line, false, 'UTF-8' );
                        }
                    }
                    
                    // Closes the socket
                    fclose( $this->_socket );
                    return true;
                }
                
                return false;
            }
            
            return false;
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
        // Checks for data from the socket
        if( $this->_data ) {
            
            // SimpleXML can produce exceptions
            try {
                
                // Creates a new SimpleXMLElement with the data from the socket
                $this->_xml = new SimpleXMLElement( $this->_data );
                
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
     * Sets the hostname used for the connection
     * 
     * @return  boolean
     */
    public function setHost( $host )
    {
        $this->_host = ( string )$host;
        return true;
    }
    
    /**
     * Sets the port number used for the connection
     * 
     * @return  boolean
     */
    public function setPort( $port )
    {
        $this->_port = ( string )$port;
        return true;
    }
    
    /**
     * Sets the file to get
     * 
     * @return  boolean
     */
    public function setFile( $file )
    {
        $file        = ( string )$file;
        $this->_file = ( substr( $file, 0, 1 ) != '/' ) ? '/' . $file : $file;
        return true;
    }
    
    /**
     * Gets the socket error number
     * 
     * @return  int The socket error number
     */
    public function getSockErrno()
    {
        return $this->_sockErrno;
    }
    
    /**
     * Gets the socket error message
     * 
     * @return  string  The socket error message
     */
    public function getSockErrstr()
    {
        return $this->_sockErrstr;
    }
    
    /**
     * Gets the HTTP status code
     * 
     * @return  int The HTTP status code
     */
    public function getResponseStatus()
    {
        return $this->_responseStatus;
    }
    
    /**
     * Gets the HTTP status message
     * 
     * @return  string  The HTTP status message
     */
    public function getResponseMsg()
    {
        return $this->_responseStatusMsg;
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
}
