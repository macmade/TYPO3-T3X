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

# $Id: class.tx_marvinshopform_pi1.php 184 2010-01-05 08:12:05Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Frontend plugin PI1 for the 'marvin_shopform' extension.
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_shopform
 */
class tx_marvinshopform_pi1 extends tslib_piBase
{
    /**
     * The HTTP version to use
     */
    protected $_httpVersion         = '1.1';
    
    /**
     * The API host
     */
    protected $_apiHost             = 'api.createsend.com';
    
    /**
     * The API port number
     */
    protected $_apiPort             = 80;
    
    /**
     * The URL to the subscriber add method
     */
    protected $_apiSubscriberAddUrl = '/api/api.asmx/Subscriber.AddAndResubscribe';
    
    /**
     * The API key
     */
    protected $_apiKey              = '';
    
    /**
     * The list ID
     */
    protected $_listId              = '';
    
    /**
     * The size of the input
     */
    protected $_inputSize           = 30;
    
    protected $_errors              = array();
    
    /**
     * The plugin's prefix
     */
    public $prefixId                = __CLASS__;
    
    /**
     * The path to this script relative to the extension dir
     */
    public $scriptRelPath           = 'pi1/class.tx_marvinshopform_pi1.php';
    
    /**
     * The extension key
     */
    public $extKey                  = 'marvin_shopform';
    
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
    protected function _addSubscriber( $email, $name )
    {
        // Establish a connection to the API host
        $connect = fsockopen( $this->_apiHost, $this->_apiPort );
        
        // Subscription REST URL
        $url     = sprintf(
            $this->_apiSubscriberAddUrl . '?ApiKey=%s&ListID=%s&Email=%s&Name=%s',
            $this->_apiKey,
            $this->_listId,
            urlencode( ( string )$email ),
            urlencode( ( string )$name )
        );
        
        // Checks if the connection was established
        if( !$connect ) {
            
            // Connection error
            return;
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
            return;
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
            return;
        }
        
        // Checks the response code
        if( ( int )$xml->Code != 0 ) {
            
            // API error
            return;
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
        $this->_httpVersion         = ( isset( $this->conf[ 'api.' ][ 'httpVersion' ] )      && $this->conf[ 'api.' ][ 'httpVersion' ] )      ? ( string )$this->conf[ 'api.' ][ 'httpVersion' ]      : $this->_httpVersion;
        $this->_apiHost             = ( isset( $this->conf[ 'api.' ][ 'host' ] )             && $this->conf[ 'api.' ][ 'host' ] )             ? ( string )$this->conf[ 'api.' ][ 'host' ]             : $this->_apiHost;
        $this->_apiPort             = ( isset( $this->conf[ 'api.' ][ 'port' ] )             && $this->conf[ 'api.' ][ 'port' ] )             ? ( int )$this->conf[ 'api.' ][ 'port' ]                : $this->_apiPort;
        $this->_apiSubscriberAddUrl = ( isset( $this->conf[ 'api.' ][ 'subscriberAddUrl' ] ) && $this->conf[ 'api.' ][ 'subscriberAddUrl' ] ) ? ( string )$this->conf[ 'api.' ][ 'subscriberAddUrl' ] : $this->_apiSubscriberAddUrl;
        $this->_inputSize           = ( isset( $this->conf[ 'inputSize' ] )                  && $this->conf[ 'inputSize' ] )                  ? ( int )$this->conf[ 'inputSize' ]                     : $this->_inputSize;
    }
    
    protected function _validateForm()
    {
        if( !isset( $this->piVars[ 'name' ] ) || !$this->piVars[ 'name' ] ) {
            
            $this->_errors[ 'name' ] = $this->pi_getLL( 'errorRequired' );
        }
        
        if( !isset( $this->piVars[ 'email' ] ) || !$this->piVars[ 'email' ] ) {
            
            $this->_errors[ 'email' ] = $this->pi_getLL( 'errorRequired' );
            
        } elseif( !t3lib_div::validEmail( $this->piVars[ 'email' ] ) ) {
            
            $this->_errors[ 'email' ] = $this->pi_getLL( 'errorInvalidEmail' );
        }
        
        if( count( $this->_errors ) ) {
            
            return false;
            
        } else {
            
            return true;
        }
    }
    
    protected function _createForm()
    {
        $html  = '<h2>' . $this->pi_getLL( 'title' ) . '</h2>'
               . '<div class="tx-marvinshopform-pi1-message">' . $this->pi_getLL( 'message' ) . '</div>'
               . '<form id="' . $this->prefixId . '_form" name="' . $this->prefixId . '_form" action="' . $this->pi_getPageLink( $GLOBALS[ 'TSFE' ]->id ) . '" method="post">'
               . '<div class="tx-marvinshopform-pi1-field">';
        
        if( isset( $this->_errors[ 'name' ] ) ) {
            
            $html .= '<div class="tx-marvinshopform-pi1-error">' . $this->_errors[ 'name' ] . '</div>';
        }
        
        $value = ( isset( $this->piVars[ 'name' ] ) ) ? $this->piVars[ 'name' ] : '';
        
        $html .= '<div class="tx-marvinshopform-pi1-label">'
              .  '<label for="' . $this->prefixId . '_name">' . $this->pi_getLL( 'name' ) . '</label>'
              .  '</div>'
              .  '<div class="tx-marvinshopform-pi1-input">'
              .  '<input name="' . $this->prefixId . '[name]" id="' . $this->prefixId . '_name" type="text" size="' . $this->_inputSize . '" value="' . $value . '" />'
              .  '</div>'
              .  '</div>'
              .  '<div class="tx-marvinshopform-pi1-field">';
        
        if( isset( $this->_errors[ 'email' ] ) ) {
            
            $html .= '<div class="tx-marvinshopform-pi1-error">' . $this->_errors[ 'email' ] . '</div>';
        }
        
        $value = ( isset( $this->piVars[ 'email' ] ) ) ? $this->piVars[ 'email' ] : '';
        
        $html .= '<div class="tx-marvinshopform-pi1-label">'
              .  '<label for="' . $this->prefixId . '_email">' . $this->pi_getLL( 'email' ) . '</label>'
              .  '</div>'
              .  '<div class="tx-marvinshopform-pi1-input">'
              .  '<input name="' . $this->prefixId . '[email]" id="' . $this->prefixId . '_email" type="text" size="' . $this->_inputSize . '" value="' . $value . '" />'
              .  '</div>'
              .  '</div>'
              .  '<div class="tx-marvinshopform-pi1-submit">'
              .  '<input name="' . $this->prefixId . '[submit]" id="' . $this->prefixId . '_submit" type="submit" value="' . $this->pi_getLL( 'submit' ) . '" />'
              .  '</div>'
              .  '</form>';
        
        return $html;
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
        // Stores the TS configuration
        $this->conf = $conf;
        
        // Plugin initialization
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_initPIflexForm();
        
        // Sets the final configuraton
        $this->_setConfig();
        
        // Checks if the form was submitted
        if( isset( $this->piVars[ 'submit' ] ) && $this->_validateForm() ) {
            
            $this->_addSubscriber( $this->piVars[ 'email' ], $this->piVars[ 'name' ] );
            
            $content = '<h2>' . $this->pi_getLL( 'title' ) . '</h2>'
                     . '<div class="tx-marvinshopform-pi1-message">' . $this->pi_getLL( 'confirm' ) . '</div>';
            
        } else {
            
            $content = $this->_createForm();
        }
        
        // Returns the plugin's content
        return $this->pi_wrapInBaseClass( $content );
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_shopform/pi1/class.tx_marvinshopform_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_shopform/pi1/class.tx_marvinshopform_pi1.php']);
}
