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

# $Id: class.tx_cachecontrolheader_controller.php 36 2010-05-20 13:28:41Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Cache-Control Header controller
 * 
 * @author      Jean-David Gadina <macmade@netinfluence.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  cache_control_header
 */
class tx_cachecontrolheader_controller
{
    /**
     * Possible values for the cache-control header
     */
    const CACHE_RESPONSE_DEFAULT            = 0x00;
    const CACHE_RESPONSE_NONE               = 0x01;
    const CACHE_RESPONSE_PUBLIC             = 0x02;
    const CACHE_RESPONSE_PRIVATE            = 0x03;
    const CACHE_RESPONSE_NO_CACHE           = 0x04;
    const CACHE_RESPONSE_NO_STORE           = 0x05;
    const CACHE_RESPONSE_NO_TRANSFORM       = 0x06;
    const CACHE_RESPONSE_MUST_REVALIDATE    = 0x07;
    const CACHE_RESPONSE_PROXY_REVALIDATE   = 0x08;
    
    /**
     * The instance of tslib_fe
     */
    protected $_tsfe = NULL;
    
    /**
     * The TS configuration array
     */
    protected $_conf = array();
    
    /**
     * The current page row
     */
    protected $_page = array();
    
    /**
     * Adds the cache-control header if necessary
     * 
     * @param   array   The parameters passed to the hook (tslib/class.tslib_fe.php - isOutputting)
     * @return  NULL
     */
    public function processDirective( array $params )
    {
        $this->_tsfe = $params[ 'pObj' ];
        $this->_conf = $this->_tsfe->tmpl->setup[ 'plugin.' ][ 'tx_cachecontrolheader_pi1.' ];
        $this->_page = $this->_tsfe->page;
        $key         = ( $this->_tsfe->loginUser ) ? 'directive_feuser' : 'directive';
        $directive   = ( int )$this->_page[ 'tx_cachecontrolheader_' . $key ];
        
        if( $directive === self::CACHE_RESPONSE_DEFAULT )
        {
            $directive = ( int )$this->_conf[ $key ];
        }
        
        switch( $directive )
        {
            case self::CACHE_RESPONSE_PUBLIC:
                
                header( 'Cache-Control: public' );
                break;
                
            case self::CACHE_RESPONSE_PRIVATE:
                
                header( 'Cache-Control: private' );
                break;
                
            case self::CACHE_RESPONSE_NO_CACHE:
                
                header( 'Cache-Control: no-cache' );
                break;
                
            case self::CACHE_RESPONSE_NO_STORE:
                
                header( 'Cache-Control: no-store' );
                break;
                
            case self::CACHE_RESPONSE_NO_TRANSFORM:
                
                header( 'Cache-Control: no-transform' );
                break;
                
            case self::CACHE_RESPONSE_MUST_REVALIDATE:
                
                header( 'Cache-Control: must-revalidate' );
                break;
                
            case self::CACHE_RESPONSE_PROXY_REVALIDATE:
                
                header( 'Cache-Control: proxy-revalidate' );
                break;
                
            default:
                
                break;
        }
    }
}

// Crappy X-Class declaration, to avoid that stupid warning with the TYPO3 extension manager...
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cache_control_header/Classes/class.tx_cachecontrolheader_controller.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cache_control_header/Classes/class.tx_cachecontrolheader_controller.php']);
}
