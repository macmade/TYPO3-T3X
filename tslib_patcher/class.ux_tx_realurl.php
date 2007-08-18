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
 * RealURL extension
 * All of this code comes from the original tx_realurl class, written by
 * Martin Poelstra (martin@beryllium.net). Only minor changes are applied.
 * 
 * The goal of this extension class is to correct some bugs, optimize some
 * statements, avoid PHP errors (even E_NOTICE) and provide a clean code base.
 * For this last one, some variables have been renamed in order to respect the
 * camelCaps rule. The code formatting also tries to follow the Zend coding
 * guidelines.
 * 
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *      49:     class ux_tx_realurl
 *      58:     function encodeSpURL_cHashCache( $newUrl, &$paramKeyValues )
 * 
 *              TOTAL FUNCTIONS: 1
 */

class ux_tx_realurl extends tx_realurl
{
    
    /**
     * Redefinition of method encodeSpURL_cHashCache to keep the cHash parameter.
     * 
     * @param   string      $newUrl         Speaking URL path (being hashed to an integer and cHash value related to this)
     * @param   array       $paramKeyValues Params array, passed by reference
     * @return  null
     */
    function encodeSpURL_cHashCache( $newUrl, &$paramKeyValues )
    {
        // Checks if cHash is the last parameter
        if ( isset( $paramKeyValues[ 'cHash' ] ) && count( $paramKeyValues ) === 1 ) {
            
            // Look if a cHash is already there for this SpURL
            $spUrlHash = hexdec( substr( md5( $newUrl ), 0, 7 ) );
            $res       = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                'chash_string',
                'tx_realurl_chashcache',
                'spurl_hash=' . $spUrlHash
            );
            
            // If nothing is found, lets insert
            if ( !$res || !$GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) )
            {
                
                // Prepare fields to insert
                $insertArray = array(
                    'spurl_hash'   => $spUrlHash,
                    'chash_string' => $paramKeyValues[ 'cHash' ]
                );
                
                // Insert the new id<->alias relation
                $GLOBALS[ 'TYPO3_DB' ]->exec_INSERTquery(
                    'tx_realurl_chashcache',
                    $insertArray
                );
                
            } elseif( $res ) {
                
                // The cHash has been found. Gets the row
                $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res );
                
                // Checks if an update is needed
                if ( $row['chash_string'] != $paramKeyValues[ 'cHash' ] ) {
                    
                    // Prepare fields to update
                    $insertArray = array(
                        'chash_string' => $paramKeyValues[ 'cHash' ]
                    );
                    
                    // Updates the row
                    $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                        'tx_realurl_chashcache',
                        'spurl_hash=' . $spUrlHash,
                        $insertArray
                    );
                }
            }
        }
    }
}

/**
 * XClass inclusion.
 */
if( defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tx_realurl.php' ]) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tx_realurl.php' ]);
}
