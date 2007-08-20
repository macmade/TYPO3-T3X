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
 * TSLib Content extension
 * All of this code comes from the original tslib_cObj class, written by
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
 *      50:     class ux_tslib_cObj
 *      61:     function typoLink( $linkText, $conf )
 *     119:     function ux_getRealUrlDefaultPreVars
 * 
 *              TOTAL FUNCTIONS: 2
 */

class ux_tslib_cObj extends tslib_cObj
{
    // RealURL default preVars
    var $ux_realUrlDefaultPreVars    = '';
    
    // Flag to know if the RealURL default preVars have already been computed
    var $ux_hasRealUrlDefaultPreVars = false;
    
    /**
     * 
     */
    function ux_getRealUrlDefaultPreVars()
    {
        // Gets a reference to the registered extension data
        $extData =& $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'tslib_patcher' ];
        
        // Checks if variables have already been defined and checks the extension configuration
        if( !$this->ux_realUrlDefaultPreVars
            && isset( $extData[ 'config' ] )
            && isset( $extData[ 'config' ][ 'realurl' ] )
            && $extData[ 'config' ][ 'realurl' ]
            && t3lib_extMgm::isLoaded( 'realurl' )
            && isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'realurl' ] )
            && isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'config.' ][ 'tx_realurl_enable' ] )
            && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'config.' ][ 'tx_realurl_enable' ] == 1
        ) {
            
            // Gets a reference to the RealURL configuration array
            $realUrlConf =& $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'realurl' ];
            
            // Checks if the default configuration exists
            if( is_array( $realUrlConf ) && isset( $realUrlConf[ '_DEFAULT' ][ 'preVars' ] ) ) {
                
                // Process each preVars of the default host
                foreach( $realUrlConf[ '_DEFAULT' ][ 'preVars' ] as $var ) {
                    
                    // Checks for a default value which is not yet in _GET
                    if( isset( $var[ 'GETvar' ] )
                        && isset( $var[ 'valueDefault' ] )
                        && isset( $var[ 'valueMap' ][ $var[ 'valueDefault' ] ] )
                        && !isset( $extData[ 'getVars' ][ $var[ 'GETvar' ] ] )
                    ) {
                        
                        // Adds the variable
                        $this->ux_realUrlDefaultPreVars .= '&'
                                                        .  $var[ 'GETvar' ]
                                                        . '='
                                                        .  $var[ 'valueMap' ][ $var[ 'valueDefault' ] ];
                    }
                }
            }
            
            $this->ux_hasRealUrlDefaultPreVars = true;
            return true;
        }
            
            $this->ux_hasRealUrlDefaultPreVars = true;
            return false;
    }
    
    /**
     * Redefinition of method typoLink to correct cHash calculation which doen't work!
     * See http://bugs.typo3.org/view.php?id=5161 for details.
     * Thanx a lot to Popy for providing the patch. ; )
     * 
     * @param   string      $linkText   The string (text) to link
     * @param   array       $conf       TypoScript configuration (see link below)
     * @return  string      A link-wrapped string.
     */
    function typoLink( $linkText, $conf )
    {
        // Storage for A tag parts
        $finalTagParts                 = array();
        
        // Gets tha additionnal attributes for the A tag
        $finalTagParts[ 'aTagParams' ] = $this->getATagParams( $conf );
        
        // Checks the parameter option
        if( !isset( $conf[ 'parameter' ] ) ) {
            
            // Create variable in order to avoid E_NOTICE errors
            $conf[ 'parameter' ] = false;
        }
        
        // Checks the subconfiguration of the parameter TS option
        if( !isset( $conf[ 'parameter.' ] ) || !is_array( $conf[ 'parameter.' ] ) ) {
            
            // Create variable in order to avoid E_NOTICE errors
            $conf[ 'parameter.' ] = false;
        }
        
        // Checks the section option
        if( !isset( $conf[ 'section' ] ) ) {
            
            // Create variable in order to avoid E_NOTICE errors
            $conf[ 'section' ] = false;
        }
        
        // Checks the subconfiguration of the section TS option
        if( !isset( $conf[ 'section.' ] ) || !is_array( $conf[ 'section.' ] ) ) {
            
            // Create variable in order to avoid E_NOTICE errors
            $conf[ 'section.' ] = false;
        }
        
        // TypoLink parameter configuration
        $linkParam                = trim( $this->stdWrap( $conf[ 'parameter' ], $conf[ 'parameter.' ] ) );
        
        // TypoLink section configuration
        $sectionMark              = trim( $this->stdWrap( $conf[ 'section' ], $conf[ 'section.' ] ) );
        
        // Creates the anchor if available
        $sectionMark              = $sectionMark ? ( t3lib_div::testInt( $sectionMark ) ? '#c' : '#' ) . $sectionMark : '';
        
        // Link to the current page
        $initPage                 = '?id='
                                  . $GLOBALS[ 'TSFE' ]->id
                                  . '&type='
                                  . $GLOBALS[ 'TSFE' ]->type;
        
        // Storage for class properties
        $this->lastTypoLinkUrl    = '';
        $this->lastTypoLinkTarget = '';
        
        // Checks the TypoLink parameter
        if( $linkParam ) {
            
            // Gets the TypoLink parameters parts
            $linkParamArray = t3lib_div::unQuoteFilenames( $linkParam, true );
            
            // Checks for the link-handler keyword
            list( $linkHandlerKeyword, $linkHandlerValue ) = explode( ':', trim( $linkParamArray[ 0 ] ), 2 );
            
            // Checks for a defined handler
            if( isset( $TYPO3_CONF_VARS[ 'SC_OPTIONS' ][ 'tslib/class.tslib_content.php' ][ 'typolinkLinkHandler' ][ $linkHandlerKeyword ] ) && strcmp( $linkHandlerValue, '' ) ) {
                
                // The the handler object
                $linkHandlerObj =& t3lib_div::getUserObj(
                    $TYPO3_CONF_VARS[ 'SC_OPTIONS' ][ 'tslib/class.tslib_content.php' ][ 'typolinkLinkHandler' ][ $linkHandlerKeyword ]
                );
                
                // Checks the object
                if( is_object( $linkHandlerObj ) ) {
                    
                    // Returns the result from the handler
                    return $linkHandlerObj->main(
                        $linkText,
                        $conf,
                        $linkHandlerKeyword,
                        $linkHandlerValue,
                        $linkParam,
                        $this
                    );
                }
            }
            
            // Link parameter value
            $linkParam = trim( $linkParamArray[ 0 ] );
            
            // Link class
            $linkClass = ( isset( $linkParamArray[ 2 ] ) ) ? trim( $linkParamArray[ 2 ] ) : '';
            
            // The '-' character means 'no class'. Necessary in order to specify a title as fourth parameter without setting the target or class!
            if( $linkClass === '-' ) {
                
                $linkClass = '';
            }
            
            // Target value
            $forceTarget = ( isset( $linkParamArray[ 1 ] ) ) ? trim( $linkParamArray[ 1 ] ) : '';
            
            // Title value
            $forceTitle  = ( isset( $linkParamArray[ 3 ] ) ) ? trim( $linkParamArray[ 3 ] ) : '';
            
            // The '-' character means 'no target'. Necessary in order to specify a class as third parameter without setting the target!
            if( $forceTarget === '-' ) {
                
                $forceTarget = '';
            }
            
            // Check if the target is coded as a JS open window link
            $jsWindowParts  = array();
            $jsWindowParams = '';
            $onClick        = '';
            
            // Checks the target
            if( $forceTarget && ereg( '^([0-9]+)x([0-9]+)(:(.*)|.*)$', $forceTarget, $jsWindowParts ) ) {
                
                // Take all pre-configured and inserted parameters and compile parameter list, including width + height
                $jsWindowTempParamsArray = t3lib_div::trimExplode( ',', strtolower( $conf[ 'JSwindow_params' ] . ',' . $jsWindowParts[ 4 ] ), 1 );
                $jsWindowParamsArray     = array();
                
                // 
                
                // Process each JavaScript parameter
                foreach( $jsWindowTempParamsArray as $jsValue ) {
                    
                    // Stores the parameter
                    list( $jsKey, $jsValue )       = explode( '=', $jsValue );
                    $jsWindowParamsArray[ $jsKey ] = $jsKey . '=' . $jsValue;
                }
                
                // Add width/height if necessary
                $jsWindowParamsArray[ 'width' ]  = ( isset( $jsWindowParts[ 1 ] ) ) ? 'width='  . $jsWindowParts[ 1 ] : '';
                $jsWindowParamsArray[ 'height' ] = ( isset( $jsWindowParts[ 2 ] ) ) ? 'height=' . $jsWindowParts[ 2 ] : '';
                
                // Implodes the JavaScript parameters
                $jsWindowParams = implode( ',', $jsWindowParamsArray );
                
                // Resets the target since we will use onClick
                $forceTarget    = '';
            }
            
            // Internal target
            $target = isset( $conf[ 'target' ] ) ? $conf[ 'target' ] : $GLOBALS[ 'TSFE' ]->intTarget;
            
            // Checks for a target subconfigurations
            if( isset( $conf[ 'target.' ] ) && is_array( $conf[ 'target.' ] ) ) {
                
                // StdWrap for target
                $target = $this->stdWrap( $target, $conf[ 'target.' ] );
            }
            
            // Title tag
            $title = ( isset( $conf[ 'title' ] ) ) ? $conf[ 'title' ] : false;
            
            // Checks title subconfiguration
            if( isset( $conf[ 'title.' ] ) && is_array( $conf[ 'title.' ] ) ) {
                
                // StdWrap for title
                $title = $this->stdWrap( $title, $conf[ 'title.' ] );
            }
            
            // Parses the URL
            $urlParts = parse_url( $linkParam );
            
            // Detecting kind of link
            if( strstr( $linkParam, '@' ) && ( !isset( $urlParts[ 'scheme' ] ) || $urlParts[ 'scheme' ] === 'mailto' ) ) {
                
                // Email address
                $linkParam                                = eregi_replace( '^mailto:', '', $linkParam );
                list( $this->lastTypoLinkUrl, $linkText ) = $this->getMailTo( $linkParam, $linktext, $initPage );
                $finalTagParts[ 'url' ]                   = $this->lastTypoLinkUrl;
                $finalTagParts[ 'TYPE' ]                  = 'mailto';
                
            } else {
                
                // Default value
                $isLocalFile = 0;
                
                // Gets the position of some characters in the link
                $fileChar    = ( int )strpos( $linkParam, '/' );
                $urlChar     = ( int )strpos( $linkParam, '.' );
                
                // Detects if a file is found in site-root (or is a 'virtual' simulateStaticDocument file). If so, it will be treated as a normal file
                list( $rootFileData ) = explode( '?', rawurldecode( $linkParam ) );
                $containsSlash        = strstr( $rootFileData, '/' );
                $rootFileDataInfos    = pathinfo( $rootFileData );
                
                // Checks if the file is a local file
                if( trim( $rootFileData )
                    && !$containsSlash
                    && ( @is_file( PATH_site . $rootFileData ) || t3lib_div::inList( 'php,html,htm', strtolower( $rootFileDataInfos[ 'extension' ] ) ) )
                ) {
                    
                    // Sets the flag
                    $isLocalFile = 1;
                    
                } elseif( $containsSlash ) {
                    
                    // Adding this so RealURL directories are linked right (non-existing)
                    $isLocalFile = 2;		
                }
                
                // Checks for an external URL (doubleSlash or '.' before a '/')
                if( $urlParts[ 'scheme' ] || ( $isLocalFile != 1 && $urlChar && ( !$containsSlash || $urlChar < $fileChar ) ) ) {
                    
                    // Sets the target for external URLs
                    $target = isset($conf[ 'extTarget' ]) ? $conf[ 'extTarget' ] : $GLOBALS[ 'TSFE' ]->extTarget;
                    
                    // Checks for a subconfiguration for the external target
                    if( isset( $conf[ 'extTarget.' ] ) ) {
                        
                        // Process the subconfiguration
                        $target = $this->stdWrap( $target, $conf[ 'extTarget.' ] );
                    }
                    
                    // Checks if the target should be forced
                    if( $forceTarget) {
                        
                        // Forces the target
                        $target = $forceTarget;
                    }
                    
                    // Checks for a link text
                    if( $linkText == '' ) {
                        
                        // No link text, so sets the URL as link text
                        $linkText = $linkParam;
                    }
                    
                    // Checks for an URL scheme
                    if( !$urlParts[ 'scheme' ] ) {
                        
                        // No scheme, so the default is http
                        $scheme = 'http://';
                        
                    } else {
                        
                        // The scheme is already present
                        $scheme = '';
                    }
                    
                    // Checks for jump URL settings
                    if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'jumpurl_enable' ] )
                        && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'jumpurl_enable' ]
                    ) {
                        
                        // Sets the last typoLink
                        $this->lastTypoLinkUrl = $GLOBALS[ 'TSFE' ]->absRefPrefix
                                               . $GLOBALS[ 'TSFE' ]->config[ 'mainScript' ]
                                               . $initPage
                                               . '&jumpurl='
                                               . rawurlencode( $scheme . $linkParam )
                                               . $GLOBALS[ 'TSFE' ]->getMethodUrlIdToken;
                        
                    } else {
                        
                        // Sets the last typoLink
                        $this->lastTypoLinkUrl = $scheme . $linkParam;
                    }
                    
                    // Sets the last typoLink target
                    $this->lastTypoLinkTarget        = $target;
                    
                    // Sets the A tag parts
                    $finalTagParts[ 'url' ]          = $this->lastTypoLinkUrl;
                    $finalTagParts[ 'targetParams' ] = ( $target ) ? ' target="' . $target . '"' : '';
                    $finalTagParts[ 'TYPE' ]         = 'url';
                    
                } elseif( $containsSlash || $isLocalFile ) {
                    
                    // File (internal)
                    $splitLinkParam = explode( '?', $linkParam );
                    
                    // Checks if the file exists
                    if( @file_exists( rawurldecode( $splitLinkParam[ 0 ] ) ) || $isLocalFile ) {
                        
                        // Checks for a link text
                        if( $linkText == '') {
                            
                            // No link text, so sets the URL as link text
                            $linkText = rawurldecode( $linkParam );
                        }
                        
                        // Checks for jump URL settings
                        if( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'jumpurl_enable' ] ) {
                            
                            // Sets the last typoLink
                            $this->lastTypoLinkUrl = $GLOBALS[ 'TSFE' ]->absRefPrefix
                                                   . $GLOBALS[ 'TSFE' ]->config[ 'mainScript' ]
                                                   . $initPage
                                                   . '&jumpurl='
                                                   . rawurlencode( $linkParam )
                                                   . $GLOBALS[ 'TSFE' ]->getMethodUrlIdToken;
                            
                        } else {
                            
                            // Sets the last typoLink
                            $this->lastTypoLinkUrl = $GLOBALS[ 'TSFE' ]->absRefPrefix . $linkParam;
                        }
                        
                        // Checks if the target should be forced
                        if( $forceTarget ) {
                            
                            // Forces the target
                            $target = $forceTarget;
                        }
                        
                        // Sets the last typoLink target
                        $this->lastTypoLinkTarget        = $target;
                        
                        // Sets the A tag parts
                        $finalTagParts[ 'url' ]          = $this->lastTypoLinkUrl;
                        $finalTagParts[ 'targetParams' ] = $target ? ' target="' . $target . '"' : '';
                        $finalTagParts[ 'TYPE' ]         = 'file';
                        
                    } else {
                        
                        // The file does not exists. Puts a message in the log
                        $GLOBALS[ 'TT' ]->setTSlogMessage(
                            'typolink(): File \''
                          . $splitLinkParam[ 0 ]
                          . '\' did not exist, so \''
                          . $linkText
                          . '\' was not linked.',
                            1
                        );
                        
                        // Returns only the link text
                        return $linkText;
                    }
                } else {
                    
                    // Checks for the no cache settings
                    if( isset( $conf[ 'no_cache.' ] ) ) {
                        
                        // Applies the no cache settings
                        $conf[ 'no_cache' ] = $this->stdWrap( $conf[ 'no_cache' ], $conf[ 'no_cache.' ] );
                    }
                    
                    // Gets the parts of the link parameter
                    $linkParamsParts = explode( '#', $linkParam );
                    
                    // Gets the link data
                    $linkParam         = trim( $linkParamsParts[ 0 ] );
                    
                    // Checks the link data
                    if( !strcmp( $linkParam, '' ) ) {
                        
                        // No ID nor alias, so the link is the current page
                        $linkParam = $GLOBALS[ 'TSFE' ]->id;
                    }
                    
                    // Checks for an anchor to a content element
                    if( isset( $linkParamsParts[ 1 ] ) && $linkParamsParts[ 1 ]  && !$sectionMark ) {
                        
                        // Adds the section mark
                        $sectionMark = trim( $linkParamsParts[ 1 ] );
                        $sectionMark = ( t3lib_div::testInt( $sectionMark ) ? '#c' : '#' ) . $sectionMark;
                    }
                    
                    // Splits the parameter to see if there is a type parameter
                    $pairParts = t3lib_div::trimExplode( ',', $linkParam );
                    
                    // Checks for a type
                    if( isset( $pairParts[ 1 ] ) ) {
                        
                        // Sets the link parameter
                        $linkParam     = $pairParts[ 0 ];
                        
                        // Override the 'type' parameter
                        $pageTypeParam = $pairParts[ 1 ];
                    }
                    
                    // Checking if the id-parameter is an alias
                    if( !t3lib_div::testInt( $linkParam ) ) {
                        
                        // Gets the page ID for the alias
                        $linkParam = $GLOBALS[ 'TSFE' ]->sys_page->getPageIdFromAlias( $linkParam );
                    }
                    
                    // Looks up the page record to verify its existence
                    $disableGroupAccessCheck = $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages' ] ? true : false;
                    $page                    = $GLOBALS[ 'TSFE' ]->sys_page->getPage( $linkParam, $disableGroupAccessCheck );
                    
                    // Checks the page
                    if( count( $page ) ) {
                    
                        // MointPoints, so looks for the closest MPvar
                        $mpVarArray = array();
                        
                        // Checks the configuration for the MPvar
                        if( !isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'MP_disableTypolinkClosestMPvalue' ] )
                            || !$GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'MP_disableTypolinkClosestMPvalue' ]
                        ) {
                            
                            // Gets the closest MPvar
                            $tempMp = $this->getClosestMPvalueForPage( $page[ 'uid' ], true );
                            
                            // Checks for a result
                            if( $tempMp) {
                                
                                // Sets the closest MPvar
                                $mpVarArray[ 'closest' ] = $tempMp;
                            }
                        }
                        
                        // Looks for an mount point overlay
                        $mountInfos = $GLOBALS[ 'TSFE' ]->sys_page->getMountPointInfo( $page[ 'uid' ], $page );
                        
                        // Checks the results
                        if( is_array( $mountInfos ) && isset( $mountInfos[ 'overlay' ] ) && $mountInfos[ 'overlay' ] ) {
                            
                            // Gets the page
                            $page = $GLOBALS[ 'TSFE' ]->sys_page->getPage( $mountInfos[ 'mount_pid' ], $disableGroupAccessCheck );
                            
                            // Checks the page
                            if( !count( $page ) ) {
                                
                                // No page. Puts a message in the logs
                                $GLOBALS[ 'TT' ]->setTSlogMessage(
                                    'typolink(): Mount point \''
                                  . $mountInfos[ 'mount_pid' ]
                                  . '\' was not available, so \''
                                  . $linkText
                                  . '\' was not linked.',
                                    1
                                );
                                
                                // Returns the link text only
                                return $linkText;
                            }
                            
                            // Sets the 're-map' information
                            $mpVarArray[ 're-map' ] = $mountInfos[ 'MPvar' ];
                        }
                        
                        // Checks for a link text
                        if( $linkText == '' ) {
                            
                            // No link text, so use the page title
                            $linkText = $page[ 'title' ];
                        }
                        
                        // Adds the query string if configured
                        $addQueryParams  = ( $conf['addQueryString'] ) ? $this->getQueryArguments( $conf[ 'addQueryString.' ] ) : '';
                        
                        // Add the additionnal parameters if any
                        $addQueryParams .= trim( $this->stdWrap( $conf[ 'additionalParams' ], $conf[ 'additionalParams.' ] ) );
                        
                        // Original not working code
                        #if( substr( $addQueryParams, 0, 1 ) != '&' ) {
                        #    
                        #    $addQueryParams = '';
                        #    
                        #} elseif( $conf[ 'useCacheHash' ] ) {
                        #    
                        #    $cHashParams     = t3lib_div::cHashParams( $addQueryParams . $GLOBALS[ 'TSFE' ]->linkVars );
                        #    $addQueryParams .= '&cHash='.t3lib_div::shortMD5( serialize( $cHashParams ) );
                        #}
                        
                        // Checks if the default variables for RealURL has already been computed
                        if( !$this->ux_hasRealUrlDefaultPreVars ) {
                            
                            // Gets the RealURL default variables
                            $this->ux_getRealUrlDefaultPreVars();
                        }
                        
                        // Checks the validity of the additionnal parameters
                        if ( substr( $addQueryParams, 0, 1 ) != '&' ) {
                            
                            // Invalid, so remove them
                            $addQueryParams = '';
                        }
                        
                        // Checks if the cHash parameter must be computed
                        // This is the patch from Popy (popy.dev@gmail.com)
                        // See http://bugs.typo3.org/view.php?id=5161
                        if ( $conf[ 'useCacheHash' ] && trim( $this->ux_realUrlDefaultPreVars . $GLOBALS[ 'TSFE' ]->linkVars . $addQueryParams ) ) {
                            
                            // Gets the GET variables needed to compute the cHash
                            $cHashParams = t3lib_div::cHashParams( $this->ux_realUrlDefaultPreVars . $GLOBALS[ 'TSFE' ]->linkVars . $addQueryParams );
                            
                            // Removes the blank key - Don't know why it's there...
                            unset( $cHashParams[ '' ] );
                            
                            // Adds the cHash parameter
                            $addQueryParams .= '&cHash=' . t3lib_div::shortMD5( serialize( $cHashParams ) );
                        }
                        
                        // Storage for the domain name
                        $domain = '';
                        
                        // Mount pages are always local and never link to another domain
                        if( count( $mpVarArray ) ) {
                            
                            // Adds the MPvar
                            $addQueryParams .= '&MP=' . rawurlencode( implode( ',', $mpVarArray ) );
                            
                        } elseif( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkCheckRootline' ] ) && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkCheckRootline' ] ) {
                            
                            // Gets the rootLine
                            $rootLine   = $GLOBALS[ 'TSFE' ]->sys_page->getRootLine( $page[ 'uid' ] );
                            
                            // Rootline flag
                            $inRootLine = 0;
                            
                            // Process the pages in the rootLine
                            foreach( $rootLine as $rootLineData ) {
                                
                                // Checks the page ID
                                if( $rootLineData[ 'uid' ] == $GLOBALS[ 'TSFE' ]->tmpl->rootLine[ 0 ][ 'uid' ] ) {
                                    
                                    // The page is in the rootline
                                    $inRootLine = 1;
                                    break;
                                }
                            }
                            
                            // The page was not found in the rootLine
                            if( !$inRootLine ) {
                                
                                // Process the pages in the rootLine
                                foreach( $rootLine as $rootLineData ) {
                                    
                                    // Gets the domain related to the page
                                    $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                                        '*',
                                        'sys_domain',
                                        'pid=' . intval( $rootLineData[ 'uid' ] ) . ' AND redirectTo=\'\'' . $this->enableFields( 'sys_domain' ),
                                        '',
                                        'sorting'
                                    );
                                    
                                    // Checks for a domain
                                    if( $res && $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                                        
                                        // Removes the trailing slash
                                        $domain = preg_replace( '/\/$/', '', $row[ 'domainName' ] );
                                        break;
                                    }
                                }
                            }
                        }
                        
                        // Overwrites the current domain if another must be used
                        if( strlen( $domain ) ) {
                            
                            // Sets the external target
                            $target = isset($conf[ 'extTarget' ]) ? $conf[ 'extTarget' ] : $GLOBALS[ 'TSFE' ]->extTarget;
                            
                            // Checks for a target subconfigurations
                            if( $conf[ 'extTarget.' ] ) {
                                
                                // Process the target subconfiguration
                                $target = $this->stdWrap( $target, $conf[ 'extTarget.' ] );
                            }
                            
                            // Checks if the target must be forced
                            if( $forceTarget) {
                                
                                // Forces the target
                                $target = $forceTarget;
                            }
                            
                            // Sets the target
                            $linkData[ 'target' ]  = $target;
                            
                            // Sets the last typoLink
                            $this->lastTypoLinkUrl = $this->URLqMark(
                                                        'http://' . $domain . '/index.php?id=' . $page[ 'uid' ],
                                                        $addQueryParams
                                                     )
                                                   . $sectionMark;
                            
                        } else {
                            
                            // Checks if the target must be forced
                            if( $forceTarget) {
                                
                                // Forces the target
                                $target = $forceTarget;
                            }
                            
                            // Sets the link data
                            $linkData = $GLOBALS[ 'TSFE' ]->tmpl->linkData(
                                    $page,
                                    $target,
                                    $conf[ 'no_cache' ],
                                    '',
                                    '',
                                    $addQueryParams,
                                    $pageTypeParam
                                 );
                            
                            // Sets the last typoLink
                            $this->lastTypoLinkUrl = $this->URLqMark( $linkData[ 'totalURL' ], '' ) . $sectionMark;
                        }
                        
                            // Sets the last typoLink target
                        $this->lastTypoLinkTarget = $linkData[ 'target' ];
                        
                        // Target attribute for the A tag
                        $targetPart               = ( $linkData[ 'target' ] ) ? ' target="' . $linkData[ 'target' ] . '"' : '';
                        
                        // Checks if the sectionMark is set, if there is no baseURL and if the link is to the current page
                        if( $sectionMark
                            && !trim( $addQueryParams )
                            && $page[ 'uid' ] == $GLOBALS[ 'TSFE' ]->id
                            && !$GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'baseURL' ]
                        ) {
                            
                            // Gets the URL parameters
                            list( , $urlParams ) = explode( '?', $this->lastTypoLinkUrl );
                            list( $urlParams )   = explode( '#', $urlParams );
                            
                            // Parses the query string
                            parse_str( $urlParams . $linkData[ 'orig_type' ], $urlParamsArray );
                            
                            // Checks the typeNum
                            if( intval( $urlParamsArray[ 'type' ] ) == $GLOBALS[ 'TSFE' ]->type ) {
                                
                                // Removes the id and type parameters
                                unset( $urlParamsArray[ 'id' ] );
                                unset( $urlParamsArray[ 'type' ] );
                                
                                // Checks for other parameters
                                if( !count( $urlParamsArray ) ) {
                                    
                                    // Sets the last typoLink
                                    $this->lastTypoLinkUrl = $sectionMark;
                                }
                            }
                        }
                        
                        // Checks if the access to the page is restricted
                        if( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages' ]
                            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages' ] !== 'NONE'
                            && !$GLOBALS[ 'TSFE' ]->checkPageGroupAccess( $page )
                        ) {
                            
                            // Gets tha page
                            $thePage               = $GLOBALS[ 'TSFE' ]->sys_page->getPage( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages' ] );
                            
                            // Gets the parameters
                            $addParams             = isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages_addParams' ] ) ? $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages_addParams' ] : '';
                            
                            // Replace markers in the parameters
                            $addParams             = str_replace( '###RETURN_URL###', rawurlencode( $this->lastTypoLinkUrl ), $addParams );
                            $addParams             = str_replace( '###PAGE_ID###', $page[ 'uid' ], $addParams );
                            
                            // Gets the link data
                            $linkData              = $GLOBALS[ 'TSFE' ]->tmpl->linkData( $thePage, $target, '', '', '', $addParams, $pageTypeParam );
                            
                            // Sets the last typoLink
                            $this->lastTypoLinkUrl = $this->URLqMark( $linkData[ 'totalURL' ], '' );
                        }
                        
                        // Renders the tag
                        $finalTagParts[ 'url' ]          = $this->lastTypoLinkUrl;
                        $finalTagParts[ 'targetParams' ] = $targetPart;
                        $finalTagParts[ 'TYPE' ]         = 'page';
                        
                    } else {
                        
                        // PAge not found, puts a message in the log
                        $GLOBALS[ 'TT' ]->setTSlogMessage(
                            'typolink(): Page id \''
                          . $linkParam
                          . '\' was not found, so \''
                          . $linkText
                          . '\' was not linked.',
                            1
                        );
                        
                        // Returns the link text only
                        return $linkText;
                    }
                }
            }
            
            // Checks if the title must be forced
            if( $forceTitle ) {
                
                // Forces the title
                $title = $forceTitle;
            }
            
            // Checks for JavaScript parameters
            if( $jsWindowParams ) {
                
                // Creates the target attribute only if the right doctype is used
                if( !t3lib_div::inList( 'xhtml_strict,xhtml_11,xhtml_2', $GLOBALS[ 'TSFE' ]->xhtmlDoctype ) ) {
                    
                    // Sets the target
                    $target = ' target="FEopenLink"';
                    
                } else {
                    
                    // No target
                    $target = '';
                }
                
                // Builds the onclick parameter
                $onClick = 'vHWin = window.open( \''
                         . $GLOBALS[ 'TSFE' ]->baseUrlWrap($finalTagParts[ 'url' ])
                         . '\', \'FEopenLink\', \''
                         . $jsWindowParams
                         . '\' ); vHWin.focus(); return false;';
                
                // Builds the A tag
                $res     = '<a href="'
                         . htmlspecialchars( $finalTagParts[ 'url' ] )
                         . '"'
                         . $target
                         .' onclick="'
                         . htmlspecialchars( $onClick )
                         . '"'
                         . ( $title     ? ' title="' . $title . '"'     : '' )
                         . ( $linkClass ? ' class="' . $linkClass . '"' : '' )
                         . $finalTagParts[ 'aTagParams' ]
                         . '>';
                         
            } else {
                
                // Checks for an email, and if it must be spam-protected
                if( $GLOBALS[ 'TSFE' ]->spamProtectEmailAddresses === 'ascii' && $finalTagParts[ 'TYPE' ] === 'mailto' ) {
                    
                    // Builds the A tag
                    $res = '<a href="'
                         . $finalTagParts[ 'url' ]
                         . '"'
                         . ( $title ? ' title="' . $title . '"' : '' )
                         . $finalTagParts[ 'targetParams' ]
                         . ( $linkClass ? ' class="' . $linkClass . '"' : '' )
                         . $finalTagParts[ 'aTagParams' ]
                         . '>';
                
                } else {
                    
                    // Builds the A tag
                    $res = '<a href="'
                         . htmlspecialchars( $finalTagParts[ 'url' ] )
                         . '"'
                         . ( $title ? ' title="' . $title . '"' : '' )
                         . $finalTagParts[ 'targetParams' ]
                         . ( $linkClass ? ' class="' . $linkClass . '"' : '' )
                         . $finalTagParts[ 'aTagParams' ]
                         . '>';
                }
            }
            
            // Checks for a user defined function
            if( isset( $conf[ 'userFunc' ] ) && $conf[ 'userFunc' ] ) {
                
                // Stores the A tag
                $finalTagParts[ 'TAG' ] = $res;
                
                // User function parameters
                $userFuncParams         = ( isset( $conf[ 'userFunc.' ] ) ) ? $conf[ 'userFunc.' ] : array();
                
                // Calls the user function
                $res                    = $this->callUserFunction(
                    $conf[ 'userFunc' ],
                    $userFuncParams,
                    $finalTagParts
                );
            }
            
            // Checks if the last typoLink must be returned
            if( $conf[ 'returnLast' ] ) {
                
                // Checks configuration
                switch( $conf[ 'returnLast' ] ) {
                    
                    // Returns the URL
                    case 'url':
                        
                        return $this->lastTypoLinkUrl;
                        break;
                    
                    // Returns the target
                    case 'target':
                        
                        return $this->lastTypoLinkTarget;
                        break;
                }
            }
            
            // Checks the wrap type
            if( isset( $conf[ 'ATagBeforeWrap' ] ) && $conf[ 'ATagBeforeWrap' ] ) {
                
                // The A tag is before the wrap
                return $res . $this->wrap( $linkText, $conf[ 'wrap' ] ) . '</a>';
                
            } else {
                
                // Thw wrap is before the A tag
                return $this->wrap( $res . $linkText . '</a>', $conf[ 'wrap' ] );
            }
            
        } else {
            
            // No parameter in the typoLink. Returns the link text only
            return $linkText;
        }
    }
}

/**
 * XClass inclusion.
 */
if( defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tslib_cobj.php' ]) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tslib_cobj.php' ]);
}
