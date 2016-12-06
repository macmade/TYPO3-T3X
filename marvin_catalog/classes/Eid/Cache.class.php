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

# $Id: Cache.class.php 103 2009-12-01 13:54:50Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * EID cache helper
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_Eid_Cache
{
    const SOCK_TIMEOUT = 2;
    const HTTP_PORT    = 80;
    const HTTP_VERSION = '1.1';
    
    protected $_ttl       = 86400;
    protected $_eid       = '';
    protected $_host      = '';
    protected $_eidUrl    = '';
    protected $_cacheDir  = '';
    protected $_cacheFile = '';
    protected $_content   = '';
    
    public function __construct( $eid )
    {
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] ) ) {
            
            $conf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] );
            
            if( isset( $conf[ 'eid_ttl' ] ) ) {
                
                $this->_ttl = ( int )$conf[ 'eid_ttl' ];
            }
        }
        
        $time             = time();
        $this->_eid       = ( string )$eid;
        $this->_eidUrl    = '/?eID=' . $this->_eid;
        $this->_host      = t3lib_div::getIndpEnv( 'TYPO3_HOST_ONLY' );
        $this->_port      = ( t3lib_div::getIndpEnv( 'TYPO3_SSL' ) ) ? 443 : 80;
        $this->_cacheDir  = t3lib_div::getFileAbsFileName( 'uploads/tx_marvincatalog/' );
        $this->_cacheFile = $this->_cacheDir . $this->_eid . '.xml';
        
        if( !file_exists( $this->_cacheFile ) || $time > filemtime( $this->_cacheFile ) + $this->_ttl ) {
            
            $this->_getEid();
        }
    }
    
    protected function _getEid()
    {
        $errno  = 0;
        $errstr = '';
        $sock   = fsockopen( $this->_host, self::HTTP_PORT, $errno, $errstr, self::SOCK_TIMEOUT );
        $nl     = chr( 13 ) . chr( 10 );
        
        if( !$sock ) {
            
            return;
        }
        
        $req = 'GET ' . $this->_eidUrl . ' HTTP/' . self::HTTP_VERSION . $nl
             . 'Host: ' . $this->_host . $nl
             . 'Connection: close' . $nl . $nl;
        
        fwrite( $sock, $req );
        
        $response = '';
        $encoding = '';
        $status   = substr( fgets( $sock, 128 ), -8, 6 );
        
        if( $status !== '200 OK' ) {
            
            return;
        }
        
        while( !feof( $sock ) ) {
            
            $line = fgets( $sock, 128 );
            
            if( substr( $line, 0, 18 ) === 'Transfer-Encoding:' ) {
                
                $encoding = substr( $line, 19, -2 );
            }
            
            if( $line === $nl ) {
                
                break;
            }
        }
        
        if( $encoding == 'chunked' ) {
            
            while( !feof( $sock ) ) {
                
                $chunkSize = fgets( $sock );
                $left      = hexdec( trim( $chunkSize ) );
                
                while( !feof( $sock ) && $left > 0 ) {
                    
                    $data      = fread( $sock, $left );
                    $response .= $data;
                    $left     -= strlen( $data );
                }
            }
            
        } else {
            
            while( !feof( $sock ) ) {
                
                $response .= fgets( $sock, 128 );
            }
        }
        
        if( $response ) {
            
            $offset = 0;
            
            file_put_contents( $this->_cacheFile, $response );
            
            $this->_content = $response;
        }
    }
    
    public function getContent()
    {
        if( !$this->_content && file_exists( $this->_cacheFile ) ) {
            
            $this->_content = file_get_contents( $this->_cacheFile );
        }
        
        return $this->_content;
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Eid/Cache.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/Eid/Cache.class.php']);
}
