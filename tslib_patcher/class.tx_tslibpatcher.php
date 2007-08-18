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
 * General utilities methods for the tslib_patcher extension
 * 
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *      49:     class tx_tslibpatcher
 *      58:     function cHashCheck( &$params, &$ref )
 * 
 *              TOTAL FUNCTIONS: 1
 */

class tx_tslibpatcher
{
    /**
     * Enforce the check for a valid cHash parameter
     * 
     * The method has been developped by Popy (popy.dev@gmail.com), for his
     * pp_chashchecker extension. The code has been reformatted in order to
     * follow the coding conventions used in this current extesion.
     * 
     * Thanx a lot to Popy for his excellent TYPO3 work.
     * 
     * @author  Popy    popy.dev@gmail.com
     * @param   array   $params     The URL parameters array, passed by reference (see TSFE->generatePage_postProcessing)
     * @param   object  $ref        The TSFE object, passed by reference
     * @return  null
     */
    function cHashCheck( &$params, &$ref )
    {
        // Gets the _GET array
        $getVars     = t3lib_div::_GET();
        
        // Gets the cHash parameters array
        $cHashParams = t3lib_div::cHashParams(
            t3lib_div::implodeArrayForUrl( '', $getVars )
        );
        
        // Checks for a valid cHash parameters array
        if( is_array( $cHashParams ) ) {
            
            // Computes the correct cHash
            $cHash       = t3lib_div::shortMD5( serialize( $cHashParams ) );
            
            // Length of the cHash parameters array
            $cHashParamsLength = count( $cHashParams );
            
            // Checks the cHash parameters array to determine if the cHash is needed
            if( $cHashParamsLength > 2 || ( $cHashParamsLength == 2 && ( !isset( $cHashParams[ 'encryptionKey' ] ) || !isset( $cHashParams[ '' ] ) ) ) ) {
                
                // Checks if there is a cHash value
                if( !$res->cHash ) {
                    
                    // No cHash
                    $ref->reqCHash();
                }
            }
        }
    }
}

/**
 * XClass inclusion.
 */
if( defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.tx_tslibpatcher.php' ]) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.tx_tslibpatcher.php' ]);
}
