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
 * TSLib Menu extension
 * All of this code comes from the original tslib_menu class, written by
 * Kasper Skaarhoj (kasperYYYY@typo3.com). Only minor changes are applied.
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
 * SECTION:     1 - MAIN
 *      55:     class ux_tslib_menu
 *      58:     class ux_tslib_gmenu
 *      61:     class ux_tslib_imgmenu
 *      64:     class ux_tslib_jsmenu
 *      67:     class ux_tslib_tmenu
 *      77:     function ux_tslib_tmenu
 *      91:     function function link( $key, $altTarget = '', $typeOverride = '' )
 * 
 *              TOTAL FUNCTIONS: 2
 */

class ux_tslib_menu extends tslib_menu
{}

class ux_tslib_gmenu extends tslib_gmenu
{}

class ux_tslib_imgmenu extends tslib_imgmenu
{}

class ux_tslib_jsmenu extends tslib_jsmenu
{}

class ux_tslib_tmenu extends tslib_tmenu
{
    // Reference to a tslib_cObj instance
    var $ux_cObj = NULL;
    
    /**
     * Class constructor
     * 
     * @return  null
     */
    function ux_tslib_tmenu()
    {
        // Gets a reference to the tslib_cObj object
        $this->ux_cObj =& $GLOBALS[ 'TSFE' ]->cObj;
    }
    
    /**
     * Redefinition of method link to correct cHash calculation which doen't work!
     * 
     * @param   int         $key            Pointer to a key in the $this->menuArr array where the value for that key represents the menu item we are linking to (page record)
     * @param   string      $altTarget      Alternative target
     * @param   int         $typeOverride   Alternative type
     * @return  string      An array with A-tag attributes as key/value pairs (HREF, TARGET and onClick)
     */
    function link( $key, $altTarget = '', $typeOverride = '' )
    {
        // Mount points
        $mountPointVar    = $this->getMPvar( $key );
        $mountPointParams = ( $mountPointVar ) ? '&MP=' . rawurlencode( $mountPointVar ) : '';
        
        // Page ID override
        if( ( isset( $this->mconf[ 'overrideId' ] ) && $this->mconf[ 'overrideId' ] ) || ( isset( $this->menuArr[ $key ][ 'overrideId' ] ) && $this->menuArr[ $key ][ 'overrideId' ] ) ) {
            
            // Storage
            $overrideArray = array();
            
            // If a user script returned the value overrideId in the menu array we use that as page id
            $overrideArray[ 'uid' ]   = ( $this->mconf[ 'overrideId' ] ) ? $this->mconf[ 'overrideId' ] : $this->menuArr[ $key ][ 'overrideId' ];
            $overrideArray[ 'alias' ] = '';
            
            // Clears the MP parameters since ID was changed.
            $mountPointParams = '';
            
        } else {
            
            // No override
            $overrideArray = '';
        }
        
        // Sets the main target
        $mainTarget = ( $altTarget ) ? $altTarget : $this->mconf[ 'target' ];
        
        // Creates the link
        if( $this->mconf['collapse'] && $this->isActive( $this->menuArr[$key]['uid'], $this->getMPvar( $key ) ) ) {
            
            // Gets the page row
            $thePage  = $this->sys_page->getPage( $this->menuArr[ $key ][ 'pid' ] );
            
            // Gets the link data
            $linkData = $this->tmpl->linkData(
                $thePage,
                $mainTarget,
                '',
                '',
                $overrideArray,
                $this->mconf[ 'addParams' ] . $mountPointParams . $this->menuArr[ $key ][ '_ADD_GETVARS' ],
                $typeOverride
            );
            
        } else {
            
            // Gets the link data
            $linkData = $this->tmpl->linkData(
                $this->menuArr[ $key ],
                $mainTarget,
                '',
                '',
                $overrideArray,
                $this->mconf[ 'addParams' ] . $mountPointParams . $this->I[ 'val' ][ 'additionalParams' ] . $this->menuArr[ $key ][ '_ADD_GETVARS' ],
                $typeOverride
            );
            
            // Gets the link with the typoLink method, in order to enable the cache hash
            $typoLinkUrl = $this->ux_cObj->typoLink_URL(
                array(
                    'parameter'        => $this->menuArr[ $key ][ 'uid' ],
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
        if( $this->menuArr[ $key ][ 'doktype' ] == 3
            && $this->menuArr[ $key ][ 'urltype' ] == 3
            && t3lib_div::validEmail( $this->menuArr[ $key ][ 'url' ] )
        ) {
           
           // No target for email links
            $linkData[ 'target' ]   = '';
            
            // Create mailto-link using tslib_cObj::typolink (concerning spamProtectEmailAddresses)
            $linkData[ 'totalURL' ] = $this->parent_cObj->typoLink_URL(
                array(
                    'parameter' => $this->menuArr[ $key ][ 'url' ]
                )
            );
        }
        
        // Manipulation in case of access restricted pages
        $this->changeLinksForAccessRestrictedPages(
            $linkData,
            $this->menuArr[ $key ],
            $mainTarget,
            $typeOverride
        );
        
        // Overriding URL / Target if set to do so
        if( isset( $this->menuArr[ $key ][ '_OVERRIDE_HREF' ] ) ) {
            
            // Replace the URL
            $linkData[ 'totalURL' ] = $this->menuArr[ $key ][ '_OVERRIDE_HREF' ];
            
            // Checks for an override target
            if( isset( $this->menuArr[ $key ][ '_OVERRIDE_TARGET' ] ) ) {
                
                // Replace the target
                $linkData[ 'target' ] = $this->menuArr[ $key ][ '_OVERRIDE_TARGET' ];
            }
        }
        
        // JavaScript link
        $onClick = '';
        
        // Checks if the window should open in a popup
        if( isset( $this->mconf[ 'JSWindow' ] ) && $this->mconf[ 'JSWindow' ] ) {
            
            // JavaScript configuration
            $conf                   = isset( $this->mconf[ 'JSWindow.' ] ) ? $this->mconf[ 'JSWindow.' ] : array();
            
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
if( defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tslib_menu.php' ]) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tslib_menu.php' ]);
}
