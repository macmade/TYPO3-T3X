<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 netinfluence                                                        #
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

# $Id: class.tx_netmailing_pi1.php 32 2010-01-07 16:12:06Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin PI1 for the 'netmailing' extension.
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  netmailing
 */
class tx_netmailing_pi1 extends tslib_piBase
{
    /**
     * The HTTP version to use
     */
    protected $_httpVersion                     = '1.1';
    
    /**
     * The API host
     */
    protected $_apiHost                         = 'api.createsend.com';
    
    /**
     * The API port number
     */
    protected $_apiPort                         = 80;
    
    /**
     * The URL to the subscriber add method
     */
    protected $_apiSubscriberAddUrl             = '/api/api.asmx/Subscriber.AddAndResubscribe';
    
    /**
     * The name of the SOAP action to add a subscriber with custom fields
     */
    protected $_soapSubscriberAddCustomAction   = 'Subscriber.AddWithCustomFields';
    
    /**
     * The URL of the SOAP action to add a subscriber with custom fields
     */
    protected $_soapSubscriberAddCustomUrl      = '/api/Subscriber.AddWithCustomFields';
    
    /**
     * The SOAP API URL
     */
    protected $_soapApiUrl                      = '/api/api.asmx';
    
    /**
     * The SOAP API XML namespace
     */
    protected $_soapXmlNs                       = 'http://api.createsend.com/api/';
    
    /**
     * The API key
     */
    protected $_apiKey                          = '';
    
    /**
     * The list ID
     */
    protected $_listId                          = '';
    
    /**
     * The size of the input
     */
    protected $_inputSize                       = 30;
    
    /**
     * The additionnal fields
     */
    protected $_fields                          = array();
    
    /**
     * The form's errors
     */
    protected $_errors                          = array();
    
    /**
     * The plugin's prefix
     */
    public $prefixId                            = __CLASS__;
    
    /**
     * The path to this script relative to the extension dir
     */
    public $scriptRelPath                       = 'pi1/class.tx_netmailing_pi1.php';
    
    /**
     * The extension key
     */
    public $extKey                              = 'netmailing';
    
    /**
     * CHash setting
     */
    public $pi_checkCHash                       = true;
    
    /**
     * Adds a subscriber
     * 
     * @param   string      The email address to add
     * @return  void
     * @throws  Exception   If the connection can't be established
     * @throws  Exception   If the HTTP response code is not 200
     * @throws  Exception   If the XML response can't be parsed
     * @throws  Exception   If an API error occured
     */
    protected function _addSubscriberWithCustomFields( $email )
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>'
             . '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">'
             . '<soap:Body>'
             . '<' . $this->_soapSubscriberAddCustomAction . ' xmlns="' . $this->_soapXmlNs . '">'
             . '<ApiKey>' . $this->_apiKey . '</ApiKey>'
             . '<ListID>' . $this->_listId . '</ListID>'
             . '<Email>' . $email . '</Email>';
        
        if( isset( $this->piVars[ 'Name' ] ) ) {
            
            $xml .= '<Name>' . $this->piVars[ 'Name' ] . '</Name>';
            
        } else {
            
            $xml .= '<Name></Name>';
        }
        
        // Gets the field names
        $fields = array_flip( array_keys( $this->_fields ) );
        
        // Remove the email and name fields
        unset( $fields[ 'email' ] );
        unset( $fields[ 'Name' ] );
        
        $xml .= '<CustomFields>';
        
        // Process the remaining custom fields
        foreach( $fields as $key => $void ) {
            
            $xml .= '<SubscriberCustomField>'
                 .  '<Key>' . $key . '</Key>'
                 .  '<Value>' . $this->piVars[ $key ] . '</Value>'
                 .  '</SubscriberCustomField>';
        }
        
        $xml .= '</CustomFields>'
             .  '</Subscriber.AddWithCustomFields>'
             .  '</soap:Body>'
             .  '</soap:Envelope>';
        
        // Establish a connection to the API host
        $connect = fsockopen( $this->_apiHost, $this->_apiPort );
        
        // Checks if the connection was established
        if( !$connect ) {
            
            // Connection error
            throw new Exception( $this->pi_getLL( 'connectionError' ) );
        }
        
        // Newline character
        $nl   = chr( 13 ) . chr( 10 );
        
        // HTTP request
        $req  = 'POST ' . $this->_soapApiUrl . ' HTTP/' . $this->_httpVersion . $nl
              . 'Host: ' . $this->_apiHost . $nl
              . 'Content-Type: text/xml; charset=utf-8' . $nl
              . 'Content-Length:' . strlen( $xml ) . $nl
              . 'SOAPAction: "http://' . $this->_apiHost . $this->_soapSubscriberAddCustomUrl . '"' . $nl . $nl
              . $xml;
        
        // Writes the request in the socket
        fwrite( $connect, $req );
        
        // Gets the HTTP status code
        $status = substr( fgets( $connect, 128 ), -8, 6 );
        
        // Checks the response status
        if( $status !== '200 OK' ) {
            
            // Connection error
            throw new Exception( $this->pi_getLL( 'badHttpStatus' ) );
        }
        
        // Content length
        $length = 0;
        
        // Process the response
        while( !feof( $connect ) ) {
            
            // Gets a line
            $line = fgets( $connect, 128 );
            
            // Checks for the content length header
            if( substr( $line, 0, 16 ) === 'Content-Length: ' ) {
                
                $length = ( int )substr( $line, 16 );
            }
            
            // Checks for the end of the headers
            if( $line === $nl ) {
                
                // Headers are sent - The response will follow
                break;
            }
        }
        
        // Gets the response
        $response = fgets( $connect, $length + 1 );
        
        // Tries to parse the response
        try {
            
            $code    = -1;
            $message = '';
            
            // Creates an XML object
            $xml = new DomDocument();
            
            // Loads the XML response
            $xml->loadXML( $response );
            
            $codes    = $xml->getElementsByTagName( 'Code' );
            $messages = $xml->getElementsByTagName( 'Message' );
            
            if( is_object( $codes ) && $codes instanceof DOMNodeList ) {
                
                $code = $codes->item( 0 )->firstChild->wholeText;
            }
            
            if( is_object( $messages ) && $messages instanceof DOMNodeList ) {
                
                $message = $messages->item( 0 )->firstChild->wholeText;
            }
            
        } catch( Exception $e ) {
            
            // XML error
            throw new Exception( $this->pi_getLL( 'badXmlResponse' ) );
        }
        
        // Checks the response code
        if( ( int )$code !== 0 ) {
            
            // API error
            throw new Exception( $message );
        }
    }
    
    /**
     * Adds a subscriber
     * 
     * @param   string      The email address to add
     * @param   string      The subscriber's name
     * @return  void
     * @throws  Exception   If the connection can't be established
     * @throws  Exception   If the HTTP response code is not 200
     * @throws  Exception   If the XML response can't be parsed
     * @throws  Exception   If an API error occured
     */
    protected function _addSubscriber( $email, $name )
    {
        // Establish a connection to the API host
        $connect = fsockopen( $this->_apiHost, $this->_apiPort );
        
        // Subscription REST URL
        $url = sprintf(
            $this->_apiSubscriberAddUrl . '?ApiKey=%s&ListID=%s&Email=%s&Name=%s',
            $this->_apiKey,
            $this->_listId,
            urlencode( ( string )$email ),
            urlencode( ( string )$name )
        );
        
        // Checks if the connection was established
        if( !$connect ) {
            
            // Connection error
            throw new Exception( $this->pi_getLL( 'connectionError' ) );
        }
        
        // Newline character
        $nl   = chr( 13 ) . chr( 10 );
        
        // HTTP request
        $req  = 'GET ' . $url . ' HTTP/' . $this->_httpVersion . $nl
              . 'Host: ' . $this->_apiHost . $nl
              . 'Connection: Close' . $nl . $nl;
        
        // Writes the request in the socket
        fwrite( $connect, $req );
        
        // Storage for the response data
        $response    = '';
        
        // Whether the HTTP headers are sent
        $headersSent = false;
        
        // Gets the HTTP status code
        $status      = substr( fgets( $connect, 128 ), -8, 6 );
        
        // Checks the response status
        if( $status !== '200 OK' ) {
            
            // Connection error
            throw new Exception( $this->pi_getLL( 'badHttpStatus' ) );
        }
        
        // Process the response
        while( !feof( $connect ) ) {
            
            // Gets a line
            $line = fgets( $connect, 128 );
            
            // Checks if the HTTP headers are sent
            if( $headersSent ) {
                
                // Adds the line to the response
                $response .= $line;
            }
            
            // Checks for the end of the headers
            if( $line === $nl ) {
                
                // Headers are sent - The response will follow
                $headersSent = true;
            }
        }
        
        // Tries to parse the response
        try {
            
            // Creates an XML object from the response
            $xml = @simplexml_load_string( $response );
            
        } catch( Exception $e ) {
            
            // XML error
            throw new Exception( $this->pi_getLL( 'badXmlResponse' ) );
        }
        
        // Checks the response code
        if( ( int )$xml->Code != 0 ) {
            
            // API error
            throw new Exception( $xml->Message );
        }
    }
    
    /**
     * Sets the configuration, for TS and flexform
     * 
     * @return  NULL
     */
    protected function _setConfig()
    {
        // Flexform settings
        $this->_apiKey = $this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'apiKey', 'sDEF' );
        $this->_listId = $this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'listId', 'sDEF' );
        
        // TS settings
        $this->_httpVersion                   = ( isset( $this->conf[ 'api.' ][ 'httpVersion' ] )                && $this->conf[ 'api.' ][ 'httpVersion' ] )                ? ( string )$this->conf[ 'api.' ][ 'httpVersion' ]                : $this->_httpVersion;
        $this->_apiHost                       = ( isset( $this->conf[ 'api.' ][ 'host' ] )                       && $this->conf[ 'api.' ][ 'host' ] )                       ? ( string )$this->conf[ 'api.' ][ 'host' ]                       : $this->_apiHost;
        $this->_apiPort                       = ( isset( $this->conf[ 'api.' ][ 'port' ] )                       && $this->conf[ 'api.' ][ 'port' ] )                       ? ( int )$this->conf[ 'api.' ][ 'port' ]                          : $this->_apiPort;
        $this->_apiSubscriberAddUrl           = ( isset( $this->conf[ 'api.' ][ 'subscriberAddUrl' ] )           && $this->conf[ 'api.' ][ 'subscriberAddUrl' ] )           ? ( string )$this->conf[ 'api.' ][ 'subscriberAddUrl' ]           : $this->_apiSubscriberAddUrl;
        $this->_soapSubscriberAddCustomAction = ( isset( $this->conf[ 'soap.' ][ 'subscriberAddCustomAction' ] ) && $this->conf[ 'soap.' ][ 'subscriberAddCustomAction' ] ) ? ( string )$this->conf[ 'soap.' ][ 'subscriberAddCustomAction' ] : $this->_soapSubscriberAddCustomAction;
        $this->_soapSubscriberAddCustomUrl    = ( isset( $this->conf[ 'soap.' ][ 'subscriberAddCustomUrl' ] )    && $this->conf[ 'soap.' ][ 'subscriberAddCustomUrl' ] )    ? ( string )$this->conf[ 'soap.' ][ 'subscriberAddCustomUrl' ]    : $this->_soapSubscriberAddCustomUrl;
        $this->_soapApiUrl                    = ( isset( $this->conf[ 'soap.' ][ 'apiUrl' ] )                    && $this->conf[ 'soap.' ][ 'apiUrl' ] )                    ? ( string )$this->conf[ 'soap.' ][ 'apiUrl' ]                    : $this->_soapApiUrl;
        $this->_soapXmlNs                     = ( isset( $this->conf[ 'soap.' ][ 'xmlns' ] )                     && $this->conf[ 'soap.' ][ 'xmlns' ] )                     ? ( string )$this->conf[ 'soap.' ][ 'xmlns' ]                     : $this->_soapXmlNs;
        $this->_inputSize                     = ( isset( $this->conf[ 'inputSize' ] )                            && $this->conf[ 'inputSize' ] )                            ? ( int )$this->conf[ 'inputSize' ]                               : $this->_inputSize;
        
        
        /**
         * Gets the additionnal fields
         */
        $this->_getFields();
    }
    
    /**
     * Gets informations about the additionnal fields, from the plugin's
     * flexform.
     * 
     * @return  NULL
     */
    protected function _getFields()
    {
        // Adds the email to the field list
        $this->_fields[ 'email' ] = array( $this->pi_getLL( 'email' ), true );
        
        // Checks the flexform structure
        if( !is_array( $this->cObj->data[ 'pi_flexform' ] ) || !isset( $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sFIELDS' ][ 'lDEF' ][ 'fields' ][ 'el' ] ) ) {
            
            return;
        }
        
        // Gets the flexform array for the fields
        $fields = $this->cObj->data[ 'pi_flexform' ][ 'data' ][ 'sFIELDS' ][ 'lDEF' ][ 'fields' ][ 'el' ];
        
        // Process each field
        foreach( $fields as $key => $value ) {
            
            $field    = $value[ 'field' ][ 'el' ][ 'name' ][ 'vDEF' ];
            $label    = $value[ 'field' ][ 'el' ][ 'label' ][ 'vDEF' ];
            $required = ( bool )$value[ 'field' ][ 'el' ][ 'required' ][ 'vDEF' ];
            
            // Adds the fields informations
            $this->_fields[ $field ] = array( $label, $required );
        }
    }
    
    /**
     * Displays the available fields
     * 
     * @return  string  The labels and inputs for the available fields
     */
    protected function _displayFields()
    {
        // Container DIV
        $content = '<div class="tx-netmailing-pi1-fields">';
        
        // Process each field
        foreach( $this->_fields as $name => $options ) {
            
            // Input value
            $value    = ( isset( $this->piVars[ $name ] ) ) ? $this->piVars[ $name ] : '';
            
            // Checks if we have an error message to display
            $error = ( isset( $this->_errors[ $name ] ) ) ? '<div class="tx-netmailing-pi1-field-error">' . $this->_errors[ $name ] . '</div>' : '';
            
            // Field layout
            $content .= '<div class="tx-netmailing-pi1-field">'
                     .  $error
                     .  '<div class="tx-netmailing-pi1-field-label">'
                     .  '<label for="' . $this->_prefixId . '_' . $name . '">' . $options[ 0 ] . '</label>'
                     .  '</div>'
                     .  '<div class="tx-netmailing-pi1-field-input">'
                     .  '<input name="' . $this->prefixId . '[' . $name . ']" id="' . $this->prefixId . '_' . $name . '" type="text" value="' . $value . '" size="' . $this->_inputSize . '" />'
                     .  '</div>'
                     .  '</div>';
        }
        
        // Ends the container DIV
        $content .= '</div>';
        
        // Returns the fields
        return $content;
    }
    
    /**
     * Validates the subscription form
     * 
     * @return  void
     */
    protected function _validateForm()
    {
        // Process each field
        foreach( $this->_fields as $field => $options ) {
            
            // Checks if the field is required
            if( $options[ 1 ] === false ) {
                
                // Field is not required
                continue;
            }
            
            // Checks if we have a field value
            if( !isset( $this->piVars[ $field ] ) || !$this->piVars[ $field ] ) {
                
                // No field value
                $this->_errors[ $field ] = $this->pi_getLL( 'errorRequired' );
                
            } elseif( $field === 'email' && !t3lib_div::validEmail( $this->piVars[ $field ] ) ) {
                
                // Invalid email
                $this->_errors[ $field ] = $this->pi_getLL( 'errorEmail' );
            }
        }
    }
    
    /**
     * Returns the content of the plugin
     * 
     * This function initialises the plugin 'tx_netmailing_pi1', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param   string  The content object
     * @param   array   The TS setup
     * @return  string  The content of the plugin
     */
    public function main( $content, array $conf )
    {
        // Plugin shouldn't be cached
        $this->pi_USER_INT_obj = 1;
        
        // Stores the TS configuration
        $this->conf = $conf;
        
        // Plugin initialization
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_initPIflexForm();
        
        // Sets the final configuraton
        $this->_setConfig();
        
        // Subscription form
        $form = '<form id="' . $this->prefixId . '_form" name="' . $this->prefixId . '_form" action="' . $this->pi_getPageLink( $GLOBALS[ 'TSFE' ]->id ) . '" method="post">';
        
        // Checks if we have additionnal fields
        if( count( $this->_fields ) > 1 ) {
            
            // Checks if the form was submitted
            if( isset( $this->piVars[ 'submit' ] ) ) {
                
                // Validates the form
                $this->_validateForm();
            }
            
            // If the form was submitted, check if we have errors
            if( isset( $this->piVars[ 'submit' ] ) && !count( $this->_errors ) ) {
                
                // Tries to add the subscription
                try {
                    
                    // Checks if we should use a SOAP request for the custom fields (if other fields than 'Name')
                    if( count( $this->_fields ) === 2 && isset( $this->_fields[ 'Name' ] ) ) {
                        
                        // Adds the subscription
                        $this->_addSubscriber( $this->piVars[ 'email' ], $this->piVars[ 'Name' ] );
                        
                    } else {
                        
                        // Adds the subscription with custom fields, using SOAP
                        $this->_addSubscriberWithCustomFields( $this->piVars[ 'email' ] );
                    }
                    
                    // Confirmation message
                    $content = '<div class="tx-netmailing-pi1-confirm">' . $this->pi_getLL( 'confirm' ) . '</div>';
                    
                } catch( Exception $e ) {
                    
                    // Error
                    $content = '<div class="tx-netmailing-pi1-error">' . $e->getMessage() . '</div>';
                }
                
            } else {
                
                // Form with additionnal fields
                $content .= $form . '<h2>' . $this->pi_getLL( 'subscribe' ) . '</h2>'
                         .  $this->_displayFields()
                         .  '<div class="tx-netmailing-pi1-submit">'
                         .  '<input name="' . $this->prefixId . '[submit]" id="' . $this->prefixId . '_submit" type="submit" value="' . $this->pi_getLL( 'submit' ) . '" />'
                         .  '</div></form>';
            }
            
        } else {
            
            // Checks if the form was submitted
            if( isset( $this->piVars[ 'submit' ] ) && isset( $this->piVars[ 'email' ] ) ) {
                
                // Tries to add the subscription
                try {
                    
                    // Adds the subscription
                    $this->_addSubscriber( $this->piVars[ 'email' ], '' );
                    
                    // Confirmation message
                    $content = '<div class="tx-netmailing-pi1-confirm">' . $this->pi_getLL( 'confirm' ) . '</div>';
                    
                } catch( Exception $e ) {
                    
                    // Error
                    $content = '<div class="tx-netmailing-pi1-error">' . $e->getMessage() . '</div>';
                }
                
            } else {
                
                // Default input value
                $value = $this->pi_getLL( 'inputValue' );
                
                // Simple layout - email only
                $content .= $form . '<div class="tx-netmailing-pi1-simple"><div class="tx-netmailing-pi1-label">'
                         .  $this->pi_getLL( 'subscribe' )
                         .  '</div>'
                         .  '<div class="tx-netmailing-pi1-email">'
                         .  '<input name="' . $this->prefixId . '[email]" id="' . $this->prefixId . '_email" type="text" value="' . $value . '" size="' . $this->_inputSize . '" onfocus="this.value = ( this.value == \'' . $value . '\' )  ? \'\' : this.value;" onblur="this.value = ( this.value == \'\' )  ? \'' . $value . '\' : this.value;" />'
                         .  '</div>'
                         .  '<div class="tx-netmailing-pi1-submit">'
                         .  '<input name="' . $this->prefixId . '[submit]" id="' . $this->prefixId . '_submit" type="submit" value="' . $this->pi_getLL( 'submit' ) . '" />'
                         .  '</div></div></form>';
            }
        }
        
        // Returns the plugin's content
        return $this->pi_wrapInBaseClass( $content );
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/netmailing/pi1/class.tx_netmailing_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/netmailing/pi1/class.tx_netmailing_pi1.php']);
}
