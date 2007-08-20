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
 *      50:     class ux_tx_realurl
 *      60:     function encodeSpURL_cHashCache( $newUrl, &$paramKeyValues )
 *     121:     function function decodeSpURL( $params, $ref )
 * 
 *              TOTAL FUNCTIONS: 2
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
        if( isset( $paramKeyValues[ 'cHash' ] ) && count( $paramKeyValues ) === 1 ) {
            
            // Look if a cHash is already there for this SpURL
            $spUrlHash = hexdec( substr( md5( $newUrl ), 0, 7 ) );
            $res       = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                'chash_string',
                'tx_realurl_chashcache',
                'spurl_hash=' . $spUrlHash
            );
            
            // If nothing is found, lets insert
            if( !$res || !$GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) )
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
                if( $row['chash_string'] != $paramKeyValues[ 'cHash' ] ) {
                    
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
    
	 /**
     * Redefinition of method decodeSpURL to remove the cHash parameter from the
     * cached _GET parameters.
     * 
     * @param   array       $params         Speaking URL path (being hashed to an integer and cHash value related to this)
     * @param   object      $ref             Params array, passed by reference
     * @return  null
     */
    function decodeSpURL( $params, $ref )
    {
        // Checks the log configuration
        if( TYPO3_DLOG ) {
            
            // Adds log message
            t3lib_div::devLog(
                'Entering decodeSpURL',
                'realurl',
                -1
            );
        }
        // Sets the parent object reference ($GLOBALS[ 'TSFE' ])
        $this->pObj =& $params[ 'pObj' ];
        
        // Initializes the configuration
        $this->setConfig();
        
        // Checks for a redirect
        if( $this->pObj->siteScript
            && substr( $this->pObj->siteScript, 0, 9 ) != 'index.php'
            && substr( $this->pObj->siteScript, 0, 1 ) != '?'
        ) {
            
            // Gets the path which is above the current site url
            $speakingUriPath = $this->pObj->siteScript;
            
            // Appends the trailing slash if necessary
            if( isset( $this->extConf[ 'init' ][ 'appendMissingSlash' ] )
                && $this->extConf[ 'init' ][ 'appendMissingSlash' ]
            ) {
                
                // Checks if the trailing slash is present
                if( substr( $speakingUriPath, -1 ) == '/' ) {
                    
                    switch( ( string )$this->extConf[ 'init' ][ 'appendMissingSlash' ] ) {
                        
                        // Only if there is no filename
                        case 'ifNotFile':
                            
                            // Checks for a filename
                            if( !ereg( '\/[^\/]+\.[^\/]+$', '/' . $speakingUriPath ) ) {
                                
                                // Adds the trailing slash
                                $speakingUriPath     .= '/';
                                $this->appendedSlash  = true;
                            }
                            
                            break;
                        
                        // Default processing
                        default:
                            
                            // Adds the trailing slash
                            $speakingUriPath     .= '/';
                            $this->appendedSlash  = true;
                            break;
                    }
                }
            }
            
            // Checks for a 'simulateStaticDocument' request
            $fileRef = t3lib_div::split_fileref( $speakingUriPath );
            
            // Attempt to resolve the page ID if it does not exist yet and if the page is on the root level
            // See http://bugs.typo3.org/view.php?id=1530
            if( !t3lib_div::testInt( $this->pObj->id )
                && $fileRef[ 'path' ] == ''
                && isset( $this->extConf[ 'fileName']['defaultToHTMLsuffixOnPrev' ] )
                && $this->extConf[ 'fileName']['defaultToHTMLsuffixOnPrev' ]
                && isset( $this->extConf[ 'init']['respectSimulateStaticURLs' ] )
                && $this->extConf[ 'init']['respectSimulateStaticURLs' ]
            ) {
                
                // Puts a message in the logs
                $GLOBALS[ 'TT' ]->setTSlogMessage(
                    'decodeSpURL: ignoring respectSimulateStaticURLs due defaultToHTMLsuffixOnPrev for the root level page!)',
                    2)
                ;
                
                // Disables 'respectSimulateStaticURLs'
                $this->extConf[ 'init' ][ 'respectSimulateStaticURLs' ] = false;
            }
            
            // Path exists and 'respectSimulateStaticURLs' is set
            if( !$this->extConf[ 'init' ][ 'respectSimulateStaticURLs' ]
                || $fileRef[ 'path' ]
            ) {
                
                // Checks the log configuration
                if( TYPO3_DLOG ) {
                    
                    // Adds log message
                    t3lib_div::devLog(
                        'RealURL powered decoding (TM) starting!',
                        'realurl'
                    );
                }
                
                // Parses and sets the path
                $uriParts        = parse_url( $speakingUriPath );
                $speakingUriPath = $this->speakingURIpath_procValue = $uriParts[ 'path' ];
                
                // Redirection if needed
                $this->decodeSpURL_checkRedirects( $speakingUriPath );
                
                // Looks for cached informations
                $cachedInfo = $this->decodeSpURL_decodeCache( $speakingUriPath );
                
                // If no cached informations were found, create them
                if( !is_array( $cachedInfo ) ) {
                    
                    // Decodes the URL
                    $cachedInfo = $this->decodeSpURL_doDecode(
                        $speakingUriPath,
                        $this->extConf[ 'init' ][ 'enableCHashCache' ]
                    );
                    
                    // Stores the cached informations
                    $this->decodeSpURL_decodeCache(
                        $speakingUriPath,
                        $cachedInfo
                    );
                }
                
                // Jumps to admin if configured
                $this->decodeSpURL_jumpAdmin_goBackend( $cachedInfo[ 'id' ] );
                
                // Removes the cHash from cached informations has it should never been overwritten
                unset( $cachedInfo[ 'GET_VARS' ][ 'cHash' ] );
                
                // Sets the informations in TSFE
                $this->pObj->mergingWithGetVars( $cachedInfo[ 'GET_VARS' ] );
                $this->pObj->id = $cachedInfo[ 'id' ];
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
