<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 eosgarden - Jean-David Gadina (macmade@eosgarden.com)               #
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

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the TYPO3 module classe
require_once( PATH_tslib . 'class.tslib_pibase.php' );

/**
 * Plugin 'HTTPS Enforcer / macmade.net' for the 'https_macmade' extension.
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  https_macmade
 */

// Includes the TYPO3 frontend plugin base class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

class tx_httpsmacmade_pi1 extends tslib_pibase
{
    /**
     * The class name
     */
    public $prefixId      = __CLASS__;
    
    // The path to this script relative to the extension directory
    public $scriptRelPath = 'pi1/class.tx_httpsmacmade_pi1.php';
    
    // The extension key
    public $extKey        = 'https_macmade';
    
    /**
     * HTTPS enforcer
     * 
     * This function is used to check the current page protocol and
     * to redirect to an HTTP or HTTPS protocol if necessary.
     * 
     * @param   string  The content object
     * @param   array   The TS configuration array
     * @return  NULL
     */
    function main( $content, $conf )
    {
        // Gets the current URL
        $url    = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        // Gets the URL infos
        $infos  = parse_url( $url );
        
        // Gets the URL scheme
        $scheme = $infos[ 'scheme' ];
        
        // Stores the scheme in TSFE
        $GLOBALS[ 'TSFE' ]->applicationData[ $this->prefixId ] = array( 'scheme' => $scheme );
        
        // Global SSL enforce mode (set on TS template)
        $g = (int)$conf[ 'mode' ];
        
        // Local SSL enforce mode (set on page)
        $l = (int)$GLOBALS[ 'TSFE' ]->page[ 'tx_httpsmacmade_enforcemode'];
        
        // Current mode
        $c = ( $scheme == 'https' ) ? 1 : 2;
        
        // Checks the modes
        if ( $l && $l != $c ) {
            
            // Switch to local mode
            $switch = $l;
            
        } else if ( $g && $g != $c && $l != $c ) {
            
            // Switch to global mode
            $switch = $g;
        }
        
        // Checks if the protocol must be changed
        if ( isset( $switch ) ) {
            
            // Protocol to use
            $protocol = ( $switch == 1 ) ? 'https' : 'http';
            
            // Redirect URL
            $redirect = $protocol . substr( $url, strlen( $scheme ) );
            
            // Redirection
            header( 'Location: ' . $redirect );
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/https_macmade/pi1/class.tx_httpsmacmade_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/https_macmade/pi1/class.tx_httpsmacmade_pi1.php']);
}
