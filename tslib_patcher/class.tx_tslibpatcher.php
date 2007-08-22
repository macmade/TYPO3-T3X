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
 * @version     1.1
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *      44:     class tx_tslibpatcher
 *      67:     function cHashCheck( &$params, &$ref )
 *     104:     function cObjInstance
 *     128:     function setMenuObject( &$menuObj )
 *     158:     function tsMenuLink( $key, $altTarget = '', $typeOverride = '' )
 * 
 *              TOTAL FUNCTIONS: 4
 */

class tx_tslibpatcher
{
    
    // An instance of tslib_cObj
    var $cObj    = NULL;
    
    // An instance of a TYPO3 menu class
    var $menuObj = NULL;
    
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
    
    /**
     * Gets a tslib_cObj instance
     * 
     * @return  boolean
     */
    function cObjInstance()
    {
        // Checks for a tslib_cObj instance in TSFE
        if( isset( $GLOBALS[ 'TSFE' ]->cObj ) && is_object( $GLOBALS[ 'TSFE' ]->cObj ) ) {
            
            // Gets a reference to the tslib_cObj object
            $this->cObj =& $GLOBALS[ 'TSFE' ]->cObj;
            
        } elseif( isset( $GLOBALS[ 'TSFE' ]->page ) && is_array( $GLOBALS[ 'TSFE' ]->page ) ) {
            
            // Creates a new instance of tslib_cObj
            $this->cObj = t3lib_div::makeInstance( 'tslib_cObj' );
            $this->cObj->start( $GLOBALS[ 'TSFE' ]->page, 'pages' );
        }
        
        return is_object( $this->cObj );
    }
    
    /**
     * Sets the menu object
     * 
     * @param   object      $menuObj        The used menu object (passed by reference)
     * @return  boolean
     */
    function setMenuObject( &$menuObj )
    {
        // Checks argument
        if( is_object( $menuObj ) && !is_object( $this->menuObj ) ) {
            
            // Sets the menu object
            $this->menuObj =& $menuObj;
        }
        
        return is_object( $this->menuObj );
    }
    
    /**
     * Redefinition of method link for TS menus to correct cHash calculation
     * which doesn't work!
     * 
     * All of this code comes from the original tslib_menu class, written by
     * Kasper Skaarhoj (kasperYYYY@typo3.com). Only minor changes are applied.
     * 
     * The goal of this method is to correct some bugs, optimize some
     * statements, avoid PHP errors (even E_NOTICE) and provide a clean code base.
     * For this last one, some variables have been renamed in order to respect the
     * camelCaps rule. The code formatting also tries to follow the Zend coding
     * guidelines.
     * 
     * @param   int         $key            Pointer to a key in the $this->menuArr array where the value for that key represents the menu item we are linking to (page record)
     * @param   string      $altTarget      Alternative target
     * @param   int         $typeOverride   Alternative type
     * @return  string      An array with A-tag attributes as key/value pairs (HREF, TARGET and onClick)
     */
    function tsMenuLink( $key, $altTarget = '', $typeOverride = '' )
    {
        // Mount points
        $mountPointVar    = $this->menuObj->getMPvar( $key );
        $mountPointParams = ( $mountPointVar ) ? '&MP=' . rawurlencode( $mountPointVar ) : '';
        
        // Page ID override
        if( ( isset( $this->menuObj->mconf[ 'overrideId' ] ) && $this->menuObj->mconf[ 'overrideId' ] ) || ( isset( $this->menuObj->menuArr[ $key ][ 'overrideId' ] ) && $this->menuObj->menuArr[ $key ][ 'overrideId' ] ) ) {
            
            // Storage
            $overrideArray = array();
            
            // If a user script returned the value overrideId in the menu array we use that as page id
            $overrideArray[ 'uid' ]   = ( $this->menuObj->mconf[ 'overrideId' ] ) ? $this->menuObj->mconf[ 'overrideId' ] : $this->menuObj->menuArr[ $key ][ 'overrideId' ];
            $overrideArray[ 'alias' ] = '';
            
            // Clears the MP parameters since ID was changed.
            $mountPointParams = '';
            
        } else {
            
            // No override
            $overrideArray = '';
        }
        
        // Sets the main target
        $mainTarget = ( $altTarget ) ? $altTarget : $this->menuObj->mconf[ 'target' ];
        
        // Creates the link
        if( $this->menuObj->mconf['collapse'] && $this->menuObj->isActive( $this->menuObj->menuArr[$key]['uid'], $this->menuObj->getMPvar( $key ) ) ) {
            
            // Gets the page row
            $thePage  = $this->menuObj->sys_page->getPage( $this->menuObj->menuArr[ $key ][ 'pid' ] );
            
            // Gets the link data
            $linkData = $this->menuObj->tmpl->linkData(
                $thePage,
                $mainTarget,
                '',
                '',
                $overrideArray,
                $this->menuObj->mconf[ 'addParams' ] . $mountPointParams . $this->menuObj->menuArr[ $key ][ '_ADD_GETVARS' ],
                $typeOverride
            );
            
        } else {
            
            // Gets the link data
            $linkData = $this->menuObj->tmpl->linkData(
                $this->menuObj->menuArr[ $key ],
                $mainTarget,
                '',
                '',
                $overrideArray,
                $this->menuObj->mconf[ 'addParams' ] . $mountPointParams . $this->menuObj->I[ 'val' ][ 'additionalParams' ] . $this->menuObj->menuArr[ $key ][ '_ADD_GETVARS' ],
                $typeOverride
            );
            
            // Gets the link with the typoLink method, in order to enable the cache hash
            $typoLinkUrl = $this->cObj->typoLink_URL(
                array(
                    'parameter'        => $this->menuObj->menuArr[ $key ][ 'uid' ],
                    'useCacheHash'     => 1,
                    'additionalParams' => $linkData[ 'linkVars' ]
                )
            );
            
            // Gets the cHash
            $cHash                  = preg_replace(
                '/.*[?&]cHash=([^&$]+)/',
                '$1',
                $typoLinkUrl
            );
            
            // Removes any existing cHash in the linkVars
            $linkData[ 'totalURL' ] = preg_replace(
                '/[?&]cHash=[^&$]+/',
                '',
                $linkData[ 'totalURL' ]
            );
            
            // Adds the cHash if available
            if( $cHash != $typoLinkUrl ) {
                
                if( strstr( $linkData[ 'totalURL' ], '?' ) ) {
                    
                    $linkData[ 'totalURL' ] .= '&cHash=' . $cHash;
                    
                } else {
                    
                    $linkData[ 'totalURL' ] .= '?cHash=' . $cHash;
                }
            }
        }
        
        // Overrides the URL if using "External URL" as doktype with a valid e-mail address
        if( $this->menuObj->menuArr[ $key ][ 'doktype' ] == 3
            && $this->menuObj->menuArr[ $key ][ 'urltype' ] == 3
            && t3lib_div::validEmail( $this->menuObj->menuArr[ $key ][ 'url' ] )
        ) {
           
           // No target for email links
            $linkData[ 'target' ]   = '';
            
            // Create mailto-link using tslib_cObj::typolink (concerning spamProtectEmailAddresses)
            $linkData[ 'totalURL' ] = $this->menuObj->parent_cObj->typoLink_URL(
                array(
                    'parameter' => $this->menuObj->menuArr[ $key ][ 'url' ]
                )
            );
        }
        
        // Manipulation in case of access restricted pages
        $this->menuObj->changeLinksForAccessRestrictedPages(
            $linkData,
            $this->menuObj->menuArr[ $key ],
            $mainTarget,
            $typeOverride
        );
        
        // Overriding URL / Target if set to do so
        if( isset( $this->menuObj->menuArr[ $key ][ '_OVERRIDE_HREF' ] ) ) {
            
            // Replace the URL
            $linkData[ 'totalURL' ] = $this->menuObj->menuArr[ $key ][ '_OVERRIDE_HREF' ];
            
            // Checks for an override target
            if( isset( $this->menuObj->menuArr[ $key ][ '_OVERRIDE_TARGET' ] ) ) {
                
                // Replace the target
                $linkData[ 'target' ] = $this->menuObj->menuArr[ $key ][ '_OVERRIDE_TARGET' ];
            }
        }
        
        // JavaScript link
        $onClick = '';
        
        // Checks if the window should open in a popup
        if( isset( $this->menuObj->mconf[ 'JSWindow' ] ) && $this->menuObj->mconf[ 'JSWindow' ] ) {
            
            // JavaScript configuration
            $conf                   = isset( $this->menuObj->mconf[ 'JSWindow.' ] ) ? $this->menuObj->mconf[ 'JSWindow.' ] : array();
            
            // Stores the URL
            $url                    = $linkData['totalURL'];
            
            // Replace the URL
            $linkData[ 'totalURL' ] = '#';
            
            // On click JavaScript link
            $onClick                = 'openPic( \''
                                    . $GLOBALS[ 'TSFE' ]->baseUrlWrap( $url )
                                    . '\', \''
                                    . ( ( isset( $conf[ 'newWindow' ] ) && $conf[ 'newWindow' ] ) ? md5( $url ) : 'theNewPage' )
                                    . '\', \''
                                    . $conf[ 'params' ]
                                    . '\' ); return false;';
            
            // Adds the required JavaScript
            $GLOBALS[ 'TSFE' ]->setJS( 'openPic' );
        }
        
        // Storage for the result
        $list              = array();
        
        // Adds the HREF
        $list[ 'HREF' ]    = strlen( $linkData[ 'totalURL' ] ) ? $linkData[ 'totalURL' ] : $GLOBALS[ 'TSFE' ]->baseUrl;
        
        // Adds the target
        $list[ 'TARGET' ]  = $linkData[ 'target' ];
        
        // Adds the on click link if available
        $list[ 'onClick' ] = $onClick;
        
        // Return the result
        return $list;
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tslib_patcher/class.tx_tslibpatcher.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tslib_patcher/class.tx_tslibpatcher.php']);
}
