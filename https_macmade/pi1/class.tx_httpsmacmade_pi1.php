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
 * Plugin 'HTTPS Enforcer / macmade.net' for the 'https_macmade' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

 /**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *      71:     function main($content,$conf)
 * 
 *              TOTAL FUNCTIONS: 1
 */

// Typo3 FE plugin class
require_once( PATH_tslib.'class.tslib_pibase.php' );

class tx_httpsmacmade_pi1 extends tslib_pibase {
    
    // Same as class name
    var $prefixId      = 'tx_httpsmacmade_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath = 'pi1/class.tx_httpsmacmade_pi1.php';
    
    // The extension key
    var $extKey        = 'https_macmade';
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Base functions.
     ***************************************************************/
    
    /**
     * HTTPS enforcer
     * 
     * This function is used to check the current page protocol and
     * to redirect to an HTTP or HTTPS protocol if necessary.
     * 
     * @param       string  $content        The content object
     * @param       array   $conf           The TS setup
     * @return      null
     */
    function main( $content, $conf )
    {
        
        // Current URL
        $url    = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        // URL infos
        $infos  = parse_url( $url );
        
        // URL scheme
        $scheme = $infos[ 'scheme' ];
        
        // Store scheme in TSFE
        $GLOBALS[ 'TSFE' ]->applicationData[ $this->prefixId ] = array( 'scheme' => $scheme );
        
        // Global SSL enforce mode (set on TS template)
        $g = (int)$conf[ 'mode' ];
        
        // Local SSL enforce mode (set on page)
        $l = (int)$GLOBALS[ 'TSFE' ]->page[ 'tx_httpsmacmade_enforcemode '];
        
        // Current mode
        $c = ( $scheme == 'https' ) ? 1 : 2;
        
        // Check modes
        if ( $l && $l != $c ) {
            
            // Switch to local mode
            $switch = $l;
            
        } else if ( $g && $g != $c && $l != $c ) {
            
            // Switch to global mode
            $switch = $g;
        }
        
        // Check if the protocol must be changed
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

/** 
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/https_macmade/pi1/class.tx_httpsmacmade_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/https_macmade/pi1/class.tx_httpsmacmade_pi1.php']);
}
?>
