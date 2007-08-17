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
 *      49:     class ux_tslib_cObj
 *      61:     function typoLink( $linkText, $conf )
 * 
 *              TOTAL FUNCTIONS: 1
 */

class ux_tslib_cObj extends tslib_cObj
{
    
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
            $JsWindowParts  = array();
            $JsWindowParams = '';
            $onClick        = '';
            
            // Checks the target
            if( $forceTarget && ereg( '^([0-9]+)x([0-9]+)(:(.*)|.*)$', $forceTarget, $JsWindowParts ) ) {
                
                // Take all pre-configured and inserted parameters and compile parameter list, including width + height
                $JsWindowTempParamsArray = t3lib_div::trimExplode( ',', strtolower( $conf[ 'JSwindow_params' ] . ',' . $JsWindowParts[ 4 ] ), 1 );
                $JsWindowParamsArray     = array();
                
                // 
                
                // Process each JavaScript parameter
                foreach( $JsWindowTempParamsArray as $JsValue ) {
                    
                    // Stores the parameter
                    list( $jsKey, $JsValue )       = explode( '=', $JsValue );
                    $JsWindowParamsArray[ $jsKey ] = $jsKey . '=' . $JsValue;
                }
                
                // Add width/height if necessary
                $JsWindowParamsArray[ 'width' ]  = ( isset( $JsWindowParts[ 1 ] ) ) ? 'width='  . $JsWindowParts[ 1 ] : '';
                $JsWindowParamsArray[ 'height' ] = ( isset( $JsWindowParts[ 2 ] ) ) ? 'height=' . $JsWindowParts[ 2 ] : '';
                
                // Implodes the JavaScript parameters
                $JsWindowParams = implode( ',', $JsWindowParamsArray );
                
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
                
                $isLocalFile = 0;
                $fileChar    = ( int )strpos( $linkParam, '/' );
                $urlChar     = ( int )strpos( $linkParam, '.' );
                
                // Detects if a file is found in site-root (or is a 'virtual' simulateStaticDocument file!) and if so it will be treated like a normal file
                list( $rootFileDat ) = explode( '?', rawurldecode( $linkParam ) );
                $containsSlash       = strstr( $rootFileDat, '/' );
                $rFD_fI              = pathinfo( $rootFileDat );
                
                if( trim( $rootFileDat ) && !$containsSlash && ( @is_file( PATH_site . $rootFileDat ) || t3lib_div::inList( 'php,html,htm', strtolower( $rFD_fI[ 'extension' ] ) ) ) ) {
                    
                    $isLocalFile = 1;
                    
                } elseif( $containsSlash ) {
                    
                    // Adding this so realurl directories are linked right (non-existing)
                    $isLocalFile = 2;		
                }
                
                // URL (external): If doubleSlash or if a '.' comes before a '/'
                if( $urlParts[ 'scheme' ] || ( $isLocalFile != 1 && $urlChar && ( !$containsSlash || $urlChar < $fileChar ) ) ) {
                    
                    $target = isset($conf[ 'extTarget' ]) ? $conf[ 'extTarget' ] : $GLOBALS[ 'TSFE' ]->extTarget;
                    
                    if( $conf[ 'extTarget.' ] ) {
                        
                        $target = $this->stdWrap( $target, $conf[ 'extTarget.' ] );
                    }
                    
                    if( $forceTarget) {
                        
                        $target = $forceTarget;
                    }
                    
                    if( $linkText == '' ) {
                        
                        $linkText = $linkParam;
                    }
                    
                    if( !$urlParts[ 'scheme' ] ) {
                        
                        $scheme = 'http://';
                        
                    } else {
                        
                        $scheme = '';
                    }
                    
                    if( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'jumpurl_enable' ] ) {
                        
                        $this->lastTypoLinkUrl = $GLOBALS[ 'TSFE' ]->absRefPrefix
                                               . $GLOBALS[ 'TSFE' ]->config[ 'mainScript' ]
                                               . $initPage
                                               . '&jumpurl='
                                               . rawurlencode( $scheme . $linkParam )
                                               . $GLOBALS[ 'TSFE' ]->getMethodUrlIdToken;
                        
                    } else {
                        
                        $this->lastTypoLinkUrl = $scheme . $linkParam;
                    }
                    
                    $this->lastTypoLinkTarget        = $target;
                    $finalTagParts[ 'url' ]          = $this->lastTypoLinkUrl;
                    $finalTagParts[ 'targetParams' ] = ( $target ) ? ' target="' . $target . '"' : '';
                    $finalTagParts[ 'TYPE' ]         = 'url';
                    
                } elseif( $containsSlash || $isLocalFile ) {
                    
                    // File (internal)
                    $splitLinkParam = explode( '?', $linkParam );
                    
                    if( @file_exists( rawurldecode( $splitLinkParam[ 0 ] ) ) || $isLocalFile ) {
                        
                        if( $linkText == '') {
                            
                            $linkText = rawurldecode( $linkParam );
                        }
                        
                        if( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'jumpurl_enable' ] ) {
                            
                            $this->lastTypoLinkUrl = $GLOBALS[ 'TSFE' ]->absRefPrefix
                                                   . $GLOBALS[ 'TSFE' ]->config[ 'mainScript' ]
                                                   . $initPage
                                                   . '&jumpurl='
                                                   . rawurlencode( $linkParam )
                                                   . $GLOBALS[ 'TSFE' ]->getMethodUrlIdToken;
                            
                        } else {
                            
                            $this->lastTypoLinkUrl = $GLOBALS[ 'TSFE' ]->absRefPrefix . $linkParam;
                        }
                        
                        if( $forceTarget ) {
                            
                            $target = $forceTarget;
                        }
                        
                        $this->lastTypoLinkTarget        = $target;
                        $finalTagParts[ 'url' ]          = $this->lastTypoLinkUrl;
                        $finalTagParts[ 'targetParams' ] = $target ? ' target="' . $target . '"' : '';
                        $finalTagParts[ 'TYPE' ]         = 'file';
                        
                    } else {
                        
                        $GLOBALS[ 'TT' ]->setTSlogMessage(
                            'typolink(): File \''
                          . $splitLinkParam[ 0 ]
                          . '\' did not exist, so \''
                          . $linkText
                          . '\' was not linked.',
                            1
                        );
                        
                        return $linkText;
                    }
                } else {
                    
                    // Integer or alias (alias is without slashes or periods or commas, that is 'nospace,alphanum_x,lower,unique' according to definition in $TCA!)
                    if( isset( $conf[ 'no_cache.' ] ) ) {
                        
                        $conf[ 'no_cache' ] = $this->stdWrap( $conf[ 'no_cache' ], $conf[ 'no_cache.' ] );
                    }
                    
                    $link_params_parts = explode( '#', $linkParam );
                    
                    // Link-data del
                    $linkParam         = trim( $link_params_parts[ 0 ] );
                    
                    if( !strcmp( $linkParam, '' ) ) {
                        
                        // If no id or alias is given
                        $linkParam = $GLOBALS[ 'TSFE' ]->id;
                    }
                    
                    if( $link_params_parts[ 1 ] && !$sectionMark ) {
                        
                        $sectionMark = trim( $link_params_parts[ 1 ] );
                        $sectionMark = ( t3lib_div::testInt( $sectionMark ) ? '#c' : '#' ) . $sectionMark;
                    }
                    
                    // Splitting the parameter by ',' and if the array counts more than 1 element it's a id/type/? pair
                    unset( $theTypeP );
                    
                    $pairParts = t3lib_div::trimExplode( ',', $linkParam );
                    
                    if( count( $pairParts ) > 1 ) {
                        
                        $linkParam  = $pairParts[ 0 ];
                        
                        // Overruling 'type'
                        $theTypeP   = $pairParts[ 1 ];
                    }
                    
                    // Checking if the id-parameter is an alias
                    if( !t3lib_div::testInt( $linkParam ) ) {
                        
                        $linkParam = $GLOBALS[ 'TSFE' ]->sys_page->getPageIdFromAlias( $linkParam );
                    }
                    
                    // Looking up the page record to verify its existence
                    $disableGroupAccessCheck = $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages' ] ? true : false;
                    $page                    = $GLOBALS[ 'TSFE' ]->sys_page->getPage( $linkParam, $disableGroupAccessCheck );
                    
                    if( count( $page ) ) {
                    
                        // MointPoints, look for closest MPvar
                        $MPvarAcc = array();
                        
                        if( !$GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'MP_disableTypolinkClosestMPvalue' ] ) {
                            
                            $temp_MP = $this->getClosestMPvalueForPage( $page[ 'uid' ], true );
                            
                            if( $temp_MP) {
                                
                                $MPvarAcc[ 'closest' ] = $temp_MP;
                            }
                        }
                        
                        // Look for overlay Mount Point
                        $mount_info = $GLOBALS[ 'TSFE' ]->sys_page->getMountPointInfo( $page[ 'uid' ], $page );
                        
                        if( is_array( $mount_info ) && $mount_info[ 'overlay' ] ) {
                            
                            $page = $GLOBALS[ 'TSFE' ]->sys_page->getPage( $mount_info[ 'mount_pid' ], $disableGroupAccessCheck );
                            
                            if( !count( $page ) ) {
                                
                                $GLOBALS[ 'TT' ]->setTSlogMessage(
                                    'typolink(): Mount point \''
                                  . $mount_info[ 'mount_pid' ]
                                  . '\' was not available, so \''
                                  . $linkText
                                  . '\' was not linked.',
                                    1
                                );
                                
                                return $linkText;
                            }
                            
                            $MPvarAcc[ 're-map' ] = $mount_info[ 'MPvar' ];
                        }
                        
                        // Setting title if blank value to link
                        if( $linkText == '' ) {
                            
                            $linkText = $page[ 'title' ];
                        }
                        
                        // Query Params
                        $addQueryParams  = $conf[ 'addQueryString' ] ? $this->getQueryArguments( $conf[ 'addQueryString.' ] ) : '';
                        $addQueryParams .= trim( $this->stdWrap( $conf[ 'additionalParams' ], $conf[ 'additionalParams.' ] ) );
                        
                        if( substr( $addQueryParams, 0, 1 ) != '&' ) {
                            
                            $addQueryParams = '';
                        }
                        
                        // Here's the patch from Popy for calculating the cHash. Let's hope it will be integrated.
                        if( $conf[ 'useCacheHash' ] && trim( $GLOBALS[ 'TSFE' ]->linkVars . $addQueryParams ) ) {
                            
                            $pA              = t3lib_div::cHashParams( $GLOBALS[ 'TSFE' ]->linkVars . $addQueryParams );
                            $addQueryParams .= '&cHash=' . t3lib_div::shortMD5( serialize( $pA ) );
                        }
                        
                        $tCR_domain = '';
                        
                        // Mount pages are always local and never link to another domain
                        if( count( $MPvarAcc ) ) {
                            
                            // Add "&MP" var
                            $addQueryParams .= '&MP=' . rawurlencode( implode( ',', $MPvarAcc ) );
                            
                        } elseif( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkCheckRootline' ] ) && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkCheckRootline' ] ) {
                            
                            // This checks if the linked id is in the rootline of this site and if not it will find the domain for that ID and prefix it
                            $tCR_rootline = $GLOBALS[ 'TSFE' ]->sys_page->getRootLine( $page[ 'uid' ] );
                            $tCR_flag     = 0;
                            
                            foreach ( $tCR_rootline as $tCR_data ) {
                                
                                if( $tCR_data[ 'uid' ] == $GLOBALS[ 'TSFE' ]->tmpl->rootLine[ 0 ][ 'uid' ] ) {
                                    
                                    // OK, it was in rootline!
                                    $tCR_flag = 1;
                                    break;
                                }
                            }
                            
                            if( !$tCR_flag ) {
                                
                                foreach ( $tCR_rootline as $tCR_data ) {
                                    
                                    $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
                                        '*',
                                        'sys_domain',
                                        'pid=' . intval( $tCR_data[ 'uid' ] ) . ' AND redirectTo=\'\'' . $this->enableFields( 'sys_domain' ),
                                        '',
                                        'sorting'
                                    );
                                    
                                    if( $res && $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                                        
                                        $tCR_domain = preg_replace( '/\/$/', '', $row[ 'domainName' ] );
                                        break;
                                    }
                                }
                            }
                        }
                        
                        // If other domain, overwrite
                        if( strlen( $tCR_domain ) ) {
                            
                            $target = isset($conf[ 'extTarget' ]) ? $conf[ 'extTarget' ] : $GLOBALS[ 'TSFE' ]->extTarget;
                            
                            if( $conf[ 'extTarget.' ] ) {
                                
                                $target = $this->stdWrap( $target, $conf[ 'extTarget.' ] );
                            }
                            
                            if( $forceTarget) {
                                
                                $target = $forceTarget;
                            }
                            
                            $LD[ 'target' ]        = $target;
                            $this->lastTypoLinkUrl = $this->URLqMark(
                                                        'http://' . $tCR_domain . '/index.php?id=' . $page[ 'uid' ],
                                                        $addQueryParams
                                                     )
                                                   . $sectionMark;
                            
                        } else {
                            
                            // Internal link
                            if( $forceTarget) {
                                
                                $target = $forceTarget;
                            }
                            
                            $LD = $GLOBALS[ 'TSFE' ]->tmpl->linkData(
                                    $page,
                                    $target,
                                    $conf[ 'no_cache' ],
                                    '',
                                    '',
                                    $addQueryParams,
                                    $theTypeP
                                 );
                            $this->lastTypoLinkUrl = $this->URLqMark( $LD[ 'totalURL' ], '' ) . $sectionMark;
                        }
                        
                        $this->lastTypoLinkTarget = $LD[ 'target' ];
                        $targetPart               = ( $LD[ 'target' ] ) ? ' target="' . $LD[ 'target' ] . '"' : '';
                        
                        // If sectionMark is set, there is no baseURL AND the current page is the page the link is to, check if there are any additional parameters and is not, drop the url
                        if( $sectionMark
                            && !trim( $addQueryParams )
                            && $page[ 'uid' ] == $GLOBALS[ 'TSFE' ]->id
                            && !$GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'baseURL' ]
                        ) {
                            
                            list( , $URLparams ) = explode( '?', $this->lastTypoLinkUrl );
                            list( $URLparams )   = explode( '#', $URLparams );
                            
                            parse_str( $URLparams . $LD[ 'orig_type' ], $URLparamsArray );
                            
                            // Type nums must match as well as page ids
                            if( intval( $URLparamsArray[ 'type' ] ) == $GLOBALS[ 'TSFE' ]->type ) {
                                
                                unset( $URLparamsArray[ 'id' ] );
                                unset( $URLparamsArray[ 'type' ] );
                                
                                // If there are no parameters left, set the new url
                                if( !count( $URLparamsArray ) ) {
                                    
                                    $this->lastTypoLinkUrl = $sectionMark;
                                }
                            }
                        }
                        
                        // If link is to a access restricted page which should be redirected, then find new URL
                        if( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages' ]
                            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages' ] !== 'NONE'
                            && !$GLOBALS[ 'TSFE' ]->checkPageGroupAccess( $page )
                        ) {
                            
                            $thePage               = $GLOBALS[ 'TSFE' ]->sys_page->getPage( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages' ] );
                            $addParams             = $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'typolinkLinkAccessRestrictedPages_addParams' ];
                            $addParams             = str_replace( '###RETURN_URL###', rawurlencode( $this->lastTypoLinkUrl ), $addParams );
                            $addParams             = str_replace( '###PAGE_ID###', $page[ 'uid' ], $addParams );
                            $LD                    = $GLOBALS[ 'TSFE' ]->tmpl->linkData( $thePage, $target, '', '', '', $addParams, $theTypeP );
                            $this->lastTypoLinkUrl = $this->URLqMark( $LD[ 'totalURL' ], '' );
                        }
                        
                        // Rendering the tag.
                        $finalTagParts[ 'url' ]          = $this->lastTypoLinkUrl;
                        $finalTagParts[ 'targetParams' ] = $targetPart;
                        $finalTagParts[ 'TYPE' ]         = 'page';
                        
                    } else {
                        
                        $GLOBALS[ 'TT' ]->setTSlogMessage(
                            'typolink(): Page id \''
                          . $linkParam
                          . '\' was not found, so \''
                          . $linkText
                          . '\' was not linked.',
                            1
                        );
                        
                        return $linkText;
                    }
                }
            }
            
            if( $forceTitle ) {
                
                $title = $forceTitle;
            }
            
            if( $JsWindowParams ) {
                
                // Create TARGET-attribute only if the right doctype is used
                if( !t3lib_div::inList( 'xhtml_strict,xhtml_11,xhtml_2', $GLOBALS[ 'TSFE' ]->xhtmlDoctype ) ) {
                    
                    $target = ' target="FEopenLink"';
                    
                } else {
                    
                    $target = '';
                }
                
                $onClick = 'vHWin = window.open( \''
                         . $GLOBALS[ 'TSFE' ]->baseUrlWrap($finalTagParts[ 'url' ])
                         . '\', \'FEopenLink\', \''
                         . $JsWindowParams
                         . '\' ); vHWin.focus(); return false;';
                         
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
                
                if( $GLOBALS[ 'TSFE' ]->spamProtectEmailAddresses === 'ascii' && $finalTagParts[ 'TYPE' ] === 'mailto' ) {
                    
                    $res = '<a href="'
                         . $finalTagParts[ 'url' ]
                         . '"'
                         . ( $title ? ' title="' . $title . '"' : '' )
                         . $finalTagParts[ 'targetParams' ]
                         . ( $linkClass ? ' class="' . $linkClass . '"' : '' )
                         . $finalTagParts[ 'aTagParams' ]
                         . '>';
                
                } else {
                    
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
            
                // Call user function:
            if( $conf[ 'userFunc' ] ) {
                
                $finalTagParts[ 'TAG' ] = $res;
                $res                    = $this->callUserFunction( $conf[ 'userFunc' ], $conf[ 'userFunc.' ], $finalTagParts );
            }
            
            // If flag "returnLastTypoLinkUrl" set, then just return the latest URL made
            if( $conf[ 'returnLast' ] ) {
                
                switch( $conf[ 'returnLast' ] ) {
                    
                    case 'url':
                        
                        return $this->lastTypoLinkUrl;
                    break;
                    
                    case 'target':
                        
                        return $this->lastTypoLinkTarget;
                    break;
                }
            }
            
            if( $conf[ 'ATagBeforeWrap' ] ) {
                
                return $res . $this->wrap( $linkText, $conf[ 'wrap' ] ) . '</a>';
                
            } else {
                
                return $this->wrap( $res . $linkText . '</a>', $conf[ 'wrap' ] );
            }
            
        } else {
            
            // No parameter in the TypoLink. Returns the text only
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
