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

# $Id: Shortener.class.php 103 2009-12-01 13:54:50Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * URL shortener (bit.ly)
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_Bitly_Url_Shortener
{
    const SHORTEN_API_HOST = 'api.bit.ly';
    const SHORTEN_API_URL  = '/shorten';
    const SHORTEN_API_PORT = 80;
    const HTTP_VERSION     = '1.1';
    const SOCK_TIMEOUT     = 2;
    
    protected $_login   = '';
    protected $_apiKey  = '';
    protected $_version = '2.0.1';
    protected $_format  = 'xml';
    
    /**
     * 
     */
    public function __construct( $login, $apiKey, $version = '2.0.1', $format = 'xml' )
    {
        $this->_login   = ( string )$login;
        $this->_apiKey  = ( string )$apiKey;
        $this->_version = ( string )$version;
        $this->_format  = ( string )$format;
    }
    
    /**
     * 
     */
    public function getVersion()
    {
        return $this->_version;
    }
    
    /**
     * 
     */
    public function getFormat()
    {
        return $this->_format;
    }
    
    /**
     * 
     */
    public function setVersion( $version )
    {
        $this->_version = ( string )$version;
    }
    
    /**
     * 
     */
    public function setFormat( $format )
    {
        $this->_format = ( string )$format;
    }
    
    /**
     * 
     */
    public function shorten( $url )
    {
        $url = self::SHORTEN_API_URL
             . '?version='
             . $this->_version
             . '&apiKey='
             . $this->_apiKey
             . '&login='
             . $this->_login
             . '&format='
             . $this->_format
             . '&longUrl='
             . urlencode( ( string )$url );
        
        $errno  = 0;
        $errstr = '';
        $nl     = chr( 13 ) . chr( 10 );
        $sock   = fsockopen(
            self::SHORTEN_API_HOST,
            self::SHORTEN_API_PORT,
            $errno,
            $errstr,
            self::SOCK_TIMEOUT
        );
        
        if( !$sock ) {
            
            return $url;
        }
        
        $req = 'GET ' . $url . ' HTTP/' . self::HTTP_VERSION . $nl
             . 'Host: ' . self::SHORTEN_API_HOST . $nl
             . 'Connection: close' . $nl . $nl;
        
        fwrite( $sock, $req );
        
        $response    = '';
        $headersSent = false;
        $status      = substr( fgets( $sock, 128 ), -8, 6 );
        
        if( $status !== '200 OK' ) {
        
           return $url;
        }
        
        while( !feof( $sock ) ) {
        
           $line = fgets( $sock, 128 );
        
           if( $headersSent ) {
        
               $response .= $line;
           }
        
           if( $line === $nl ) {
        
               $headersSent = true;
           }
        }
        
        if( !$response ) {
            
            return $url;
        }
        
        try {
            
            $xml = @simplexml_load_string( $response );
            
            if( $xml->errorCode != 0 ) {
                
                return $url;
            }
            
            return ( string )$xml->results->nodeKeyVal->shortUrl;
            
        } catch( Exception $e ) {
            
            return $url;
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Bitly/Url/Shortener.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Bitly/Url/Shortener.class.php']);
}
