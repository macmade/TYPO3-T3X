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
 * TSLib Page Generation extension
 * All of this code comes from the original TSpagegen class, written by
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
 *        :     class TSpagegen
 *        :     function renderContent
 *        :     function renderContentWithHeader
 * 
 *              TOTAL FUNCTIONS: 2
 */
class ux_TSpagegen extends TSpagegen
{
    // Page content
    var $pageContent           = '';
    
    // Extension configuration
    var $extConf               = array();
    
    // New line character
    var $NL                    = '';
    
    // Tab line character
    var $TAB                   = '';
    
    // Extension key
    var $extKey                = 'pagegen_macmade';
    
    /**
     * Class constructor
     * 
     * @return  null
     */
    function ux_TSpagegen()
    {
        // Checks for the extension configuration array
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'pagegen_macmade' ][ 'config' ] )
            && is_array( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'pagegen_macmade' ][ 'config' ] )
        ) {
            
            // Stores a reference of the extension configuration
            $this->extConf =& $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ 'pagegen_macmade' ][ 'config' ];
        }
        
        // Sets the new line character
        $this->NL  = chr( 10 );
        
        // Sets the tabulation character
        $this->TAB = chr( 9 );
    }
    
    /**
     * 
     */
    function renderContent()
    {
        // Checks the extension configuration
        if( !count( $this->extConf ) ) {
            
            // Renders the page content
            TSpagegen::renderContent();
            return true;
        }
        
        // Message
        $GLOBALS[ 'TT' ]->incStackPointer();
        $GLOBALS[ 'TT' ]->push( $GLOBALS['TSFE']->sPre, 'PAGE' );
        
        // Gets the page content
        $this->pageContent = $GLOBALS[ 'TSFE' ]->cObj->cObjGet( $GLOBALS[ 'TSFE' ]->pSetup );
        
        // Checks for the wrap settings
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'wrap' ] ) ) {
            
            // Process the wrap
            $this->pageContent = $GLOBALS[ 'TSFE' ]->cObj->wrap( $this->pageContent, $GLOBALS[ 'TSFE' ]->pSetup[ 'wrap' ] );
        }
        
        // Checks for the stdWrap settings
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'stdWrap.' ] )
            && is_array( $GLOBALS[ 'TSFE' ]->pSetup[ 'stdWrap.' ] )
        ) {
            
            // Process the stdWrap
            $this->pageContent = $GLOBALS[ 'TSFE' ]->cObj->stdWrap( $this->pageContent, $GLOBALS[ 'TSFE' ]->pSetup[ 'stdWrap.' ] );
        }
        
        // Checks if the page headers must be disabled
        if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'disableAllHeaderCode' ] )
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'disableAllHeaderCode' ]
        ) {
            
            // Adds the content
            $GLOBALS[ 'TSFE' ]->content = $this->pageContent;
            
        } else {
            
            // Rebders the content with the page headers
            $this->renderContentWithHeader();
        }
        
        // Message
        $GLOBALS[ 'TT' ]->pull( ( $GLOBALS[ 'TT' ]->LR ) ? $GLOBALS[ 'TSFE' ]->content : '' );
        $GLOBALS[ 'TT' ]->decStackPointer();
    }
    
    /**
     * 
     */
    function renderContentWithHeader()
    {
        // Custom content
        $customContent = '';
        
        // Checks for a custom header comment
        if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'headerComment' ] )
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'headerComment' ]
        ) {
            
            // Stores the custom content
            $customContent = $this->NL
                           . $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'headerComment' ]
                           . $this->NL;
        }
        
        // Gets the charset
        $charset = $GLOBALS['TSFE']->metaCharset;
        
        // Resets the content variables
        $GLOBALS[ 'TSFE' ]->content = '';
        $htmlTagAttributes          = array();
        
        // Sets the HTML lang tag
        $htmlLang                   = ( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_langKey' ] ) ) ? $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_langKey' ] : $this->extConf[ 'htmlLangTag' ];
        
        // Sets the content direction
        if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_dir' ] )
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_dir' ]
        ) {
            
            // Adds the attribute
            $htmlTagAttributes[ 'dir' ] = htmlspecialchars( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_dir' ] );
        }

        // Doctype parts storage
        $docTypeParts = array();
        
        // Checks for the XML prologue configuration
        if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'xmlprologue' ] )
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'xmlprologue' ]
        ) {
            
            // Custom value
            $xmlPrologue = ( string )$GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'xmlprologue' ];
            
        } else {
            
            // Default value
            $xmlPrologue = $this->extConf[ 'xmlPrologue' ];
        }
        
        // Possible values for the XML prologue
        switch( $xmlPrologue ) {
            
            // No XML prologue
            case 'none':
            break;
            
            // XML 1.0
            case 'xml_10':
                
                // Stores the prologue
                $docTypeParts[] = '<?xml version="1.0" encoding="' . $charset . '"?>';
            break;
            
            // XML 1.1
            case 'xml_11':
                
                // Stores the prologue
                $docTypeParts[] = '<?xml version="1.1" encoding="' . $charset . '"?>';
            break;
            
            // No prologue defined
            case '':
                
                // Checks if we are rendering XHTML
                if( $GLOBALS['TSFE']->xhtmlVersion ) {
                    
                    // Stores the prologue
                    $docTypeParts[] = '<?xml version="1.0" encoding="' . $charset . '"?>';
                }
            break;
            
            // Custom prologue
            default:
                
                // Stores the prologue
                $docTypeParts[] = $xmlPrologue;
            break;
        }
        
        // Checks for the DTD configuration
        if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'doctype' ] )
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'doctype' ]
        ) {
            
            // Custom value
            $docType = ( string )$GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'doctype' ];
            
        } else {
            
            // Default value
            $docType = $this->extConf[ 'htmlDocType' ];
        }
        
        // Possible values for the DTD
        switch( $docType ) {
            
            // XHTML Transitional
            case 'xhtml_trans':
                
                // Stores the doctype
                $docTypeParts[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            break;
            
            // XHTML Strict
            case 'xhtml_strict':
                
                // Stores the doctype
                $docTypeParts[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            break;
            
            // XHTML Frames
            case 'xhtml_frames':
                
                // Stores the doctype
                $docTypeParts[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
            break;
            
            // XHTML Basic
            case 'xhtml_basic':
                
                // Stores the doctype
                $docTypeParts[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">';
            break;
            
            // XHTML 1.1
            case 'xhtml_11':
                
                // Stores the doctype
                $docTypeParts[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
            break;
            
            // XHTML 2
            case 'xhtml_2':
                
                // Stores the doctype
                $docTypeParts[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 2.0//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml2.dtd">';
            break;
            
            // No doctype
            case 'none':
            break;
            
            // Custom doctype
            default:
                
                // Stores the doctype
                $docTypeParts[] = $docType;
            break;
        }
        
        // Checks if we are rendering XHTML
        if( $GLOBALS[ 'TSFE' ]->xhtmlVersion ) {
            
            // Adds the language attributes
            $htmlTagAttributes[ 'xmlns' ]    = 'http://www.w3.org/1999/xhtml';
            $htmlTagAttributes[ 'xml:lang' ] = $htmlLang;
            
            // Checks the XHTML version
            if( $GLOBALS[ 'TSFE' ]->xhtmlVersion < 110 ) {
                
                // Adds the lang attribute
                $htmlTagAttributes[ 'lang' ] = $htmlLang;
            }
        }
        
        // Checks if the XML prologue must be placed after the doctype
        if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'doctypeSwitch' ] )
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'doctypeSwitch' ]
        ) {
            
            // Reverse the array
            $docTypeParts = array_reverse( $docTypeParts );
        }
        
        // Checks for doctypes elements
        if( count( $docTypeParts ) ) {
            
            // Adds the doctype parts
            $GLOBALS[ 'TSFE' ]->content .= implode( $this->NL, $docTypeParts )
                                        .  $this->NL;
        }
        
        // Attributes for the HTML tag
        $htmlAttr = '';
        
        // Checks if the HTML attributes must be forced
        if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_setParams' ] )
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_setParams' ]
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_setParams' ] != 'none'
        ) {
            
            // Sets the attributes for the HTML tag
            $htmlAttr = ' ' . $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'htmlTag_setParams' ];
            
        } else {
            
            // Sets the attributes for the HTML tag
            $htmlAttr = ' ' . t3lib_div::implodeAttributes( $htmlTagAttributes );
        }
        
        // Starts the HTML tag
        $GLOBALS[ 'TSFE' ]->content .= '<html' .  $htmlAttr . '>';
        
        // Creates the HEAD tag
        $headTag = ( isset( $GLOBALS[ 'TSFE']->pSetup[ 'headTag' ] ) && $GLOBALS[ 'TSFE']->pSetup[ 'headTag' ] ) ? $GLOBALS[ 'TSFE' ]->pSetup[ 'headTag' ] : '<head>';
        
        // Adds the HEAD tag
        $GLOBALS[ 'TSFE' ]->content .= $this->NL . $headTag;
        
        // Sets the content-type meta
        $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                    .  $this->TAB
                                    .  '<meta http-equiv="Content-Type" content="text/html; charset='
                                    .  $charset
                                    .  '" />';
        
        // Gets the page title
        $pageTitle = ( $GLOBALS[ 'TSFE' ]->altPageTitle ) ? $GLOBALS[ 'TSFE' ]->altPageTitle : $GLOBALS[ 'TSFE' ]->page[ 'title' ];
        
        // Gets the configuration for the page title        
        $noPageTitle    = ( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'noPageTitle' ] ) && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'noPageTitle' ] )       ? $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'noPageTitle' ]    : false;
        $pageTitleFirst = ( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'pageTitleFirst' ] ) && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'pageTitleFirst' ] ) ? $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'pageTitleFirst' ] : false;
        
        // Gets the content for the page title
        $titleTagContent = $GLOBALS[ 'TSFE' ]->tmpl->printTitle(
            $pageTitle,
            $noPageTitle,
            $pageTitleFirst
        );
        
        // Checks for a custom title function
        if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'titleTagFunction' ] )
            && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'titleTagFunction' ]
        ) {
            
            // Calls the custom function
            $titleTagContent = $GLOBALS[ 'TSFE' ]->cObj->callUserFunction(
                $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'titleTagFunction' ],
                array(),
                $titleTagContent
            );
        }
        
        // Checks for a title, and checks if it must be printed
        if( strlen( $titleTagContent ) && ( !isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'noPageTitle' ] ) || intval( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'noPageTitle' ] ) !== 2 ) ) {
            
            // Adds the title tag
            $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                        .  $this->TAB
                                        .  '<title>'
                                        .  htmlspecialchars( $titleTagContent )
                                        . '</title>'
                                        .  $this->NL;
        }
        
        // Adds the TYPO3 HTML comment
        $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                    .  '<!-- '
                                    .  $customContent
                                    .  $this->NL
                                    .  $this->TAB
                                    . 'This website is powered by TYPO3 - inspiring people to share!'
                                    .  $this->NL
                                    .  $this->TAB
                                    . 'TYPO3 is a free open source Content Management Framework initially created by Kasper Skaarhoj and licensed under GNU/GPL.'
                                    .  $this->NL
                                    .  $this->TAB
                                    . 'TYPO3 is copyright 1998-2006 of Kasper Skaarhoj. Extensions are copyright of their respective owners.'
                                    .  $this->NL
                                    .  $this->TAB
                                    . 'Information and contribution at http://typo3.com/ and http://typo3.org/'
                                    .  $this->NL
                                    . '-->'
                                    .  $this->NL;
        
        // After title hook
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $this->extKey ][ 'afterTitleHook' ] )
            && is_array( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $this->extKey ][ 'afterTitleHook' ] )
            && count( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $this->extKey ][ 'afterTitleHook' ] )
        ) {
            
            // Process each hook
            foreach( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXTCONF' ][ $this->extKey ][ 'afterTitleHook' ] as $classRef ) {
                
                // Gets the hook object
                $hookObj =& t3lib_div::getUserObj( $classRef );
                
                // Checks if the method exists
                if( method_exists( $hookObj, 'afterTitleHook' ) ) {
                    
                    $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                                .  $hookObj->afterTitleHook()
                                                .  $this->NL;
                }
            }
        }
        
        // Checks for a base URL
        if( $GLOBALS[ 'TSFE' ]->baseUrl ) {
            
            // Adds the base URL
            $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                        .  $this->TAB
                                        .  '<base href="'
                                        .  htmlspecialchars( $GLOBALS[ 'TSFE' ]->baseUrl )
                                        .  '" />';
        }
        
        // Checks for the favicon configuration
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'shortcutIcon' ] )
            && $GLOBALS[ 'TSFE' ]->pSetup[ 'shortcutIcon' ]
        ) {
            
            // Path to the favicon
            $favicon = $GLOBALS[ 'TSFE' ]->tmpl->getFileName( $GLOBALS[ 'TSFE' ]->pSetup[ 'shortcutIcon' ] );
            
            // Adds the favicon
            $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                        .  $this->TAB
                                        .  '<link rel="SHORTCUT ICON" href="'
                                        .  htmlspecialchars( $favicon )
                                        .  '" />';
        }
        
        // Checks for plugins TS configuration
        if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ] )
            && is_array ($GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ] )
        ) {
            
            // Storage for CSS styles
            $tempCssStyles = array();
            
            // Process each plugin
            foreach( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ] as $key => $tsConf) {
                
                // Checks the configuration array
                if( is_array( $tsConf )
                    && isset( $tsConf[ '_CSS_DEFAULT_STYLE' ] )
                    && trim( $tsConf[ '_CSS_DEFAULT_STYLE' ] )
                ) {
                    
                    // CSS comment
                    $tempCssStyles[] = '/***************************************************************************'
                                     . $this->NL
                                     . ' * Default CSS styles for the "'
                                     . substr( $key, 0, -1 )
                                     . '" plugin'
                                     . $this->NL
                                     . ' **************************************************************************/'
                                     . $this->NL
                                     . $tsConf[ '_CSS_DEFAULT_STYLE' ];
                }
            }
            
            // Checks for CSS styles
            if( count( $tempCssStyles ) ) {
                
                // Checks if the CSS styles must be placed in an external stylesheet
                if( isset( $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'inlineStyle2TempFile' ] ) 
                    && $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'inlineStyle2TempFile' ]
                ) {
                    
                    // Creates the external stylesheet code
                    $tempCssExternalStyles = '/* <![CDATA[ */'
                                           .  $this->NL
                                           .  '<!--'
                                           .  $this->NL
                                           .  $this->NL
                                           .  implode( $this->NL, $tempCssStyles )
                                           .  $this->NL
                                           .  $this->NL
                                           .  '-->'
                                           .  $this->NL
                                           .  '/* ]]> */';
                    
                    // Creates an external stylesheet
                    $GLOBALS[ 'TSFE' ]->content .= $this->TAB
                                                .  ux_TSpagegen::inline2TempFile( $tempCssExternalStyles, 'css' );
                    
                    // Free some memory
                    unset( $tempCssStyles );
                    unset( $tempCssExternalStyles );
                    
                } else {
                    
                    // Adds the CSS style in the page's HTML code
                    $GLOBALS['TSFE']->content .= $this->NL
                                              .  $this->NL
                                              .  '<!-- DEFAULT STYLES: begin -->'
                                              .  $this->NL
                                              .  $this->NL
                                              .  $this->TAB
                                              .  '<style type="text/css">'
                                              .  $this->NL
                                              .  $this->TAB
                                              .  '/* <![CDATA[ */'
                                              .  $this->NL
                                              .  $this->TAB
                                              .  '<!--'
                                              .  $this->NL
                                              .  $this->NL
                                              .  implode( $this->NL, $tempCssStyles )
                                              .  $this->NL
                                              .  $this->NL
                                              .  $this->TAB
                                              .  '-->'
                                              .  $this->NL
                                              .  $this->TAB
                                              .  '/* ]]> */'
                                              .  $this->NL
                                              .  $this->TAB
                                              . '</style>'
                                              .  $this->NL
                                              .  $this->NL
                                              .  '<!-- DEFAULT STYLES: end -->'
                                              .  $this->NL;
                    
                    // Free some memory
                    unset( $tempCssStyles );
                }
            }
        }
        
        // Stylesheet from the TS setup
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'stylesheet' ] ) && $GLOBALS[ 'TSFE' ]->pSetup[ 'stylesheet' ] ) {
            
            // Gets the stylesheet file name
            if( $styleSheetFile = $GLOBALS[ 'TSFE' ]->tmpl->getFileName( $GLOBALS[ 'TSFE' ]->pSetup[ 'stylesheet' ] ) ) {
                
                // Adds the stylesheet file
                $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                            .  $this->TAB
                                            .  '<link rel="stylesheet" type="text/css" href="'
                                            .  htmlspecialchars( $styleSheetFile )
                                            .  '" />';
            }
        }
        
        // Checks for CSS files to include
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ] ) && is_array( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ] ) ) {
            
            // Comment
            $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                        .  $this->NL
                                        .  '<!-- INCLUDE CSS: begin -->'
                                        .  $this->NL;
            
            // Process each CSS file
            foreach( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ] as $key => $includeCssFile ) {
                
                // Only process valid entries
                if( is_string( $includeCssFile ) ) {
                    
                    // Gets the CSS file path
                    if( $styleSheetFile = $GLOBALS[ 'TSFE' ]->tmpl->getFileName( $includeCssFile ) ) {
                        
                        // Checks how the stylesheet must be included
                        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'import' ] )
                            && $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'import' ]
                        ) {
                            
                            // Checks if the path is relative
                            if( substr( $styleSheetFile, 0, 1 ) != '/' ) {
                                
                                // Fix for MSIE6
                                $styleSheetFile = t3lib_div::dirname( t3lib_div::getIndpEnv( 'SCRIPT_NAME' ) ) . '/' . $styleSheetFile;
                            }
                            
                            // Gets the CSS media if defined
                            $cssMedia = ( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'media' ] ) ) ? ' ' . htmlspecialchars( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'media' ] ) : '';
                            
                            // Adds an import CSS instruction
                            $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                                        .  $this->TAB
                                                        .  '<style type="text/css">'
                                                        .  $this->NL
                                                        .  $this->TAB
                                                        .  '/* <![CDATA[ */'
                                                        .  $this->NL
                                                        .  $this->TAB
                                                        .  '<!--'
                                                        .  $this->NL
                                                        .  $this->TAB
                                                        .  $this->TAB
                                                        .  '@import url("'
                                                        .   htmlspecialchars( $styleSheetFile )
                                                        .  '")'
                                                        .  $cssMedia
                                                        .  ';'
                                                        .  $this->NL
                                                        .  $this->TAB
                                                        .  '-->'
                                                        .  $this->NL
                                                        .  $this->TAB
                                                        .  '/* ]]> */'
                                                        .  $this->NL
                                                        .  $this->TAB
                                                        .  '</style>';
                            
                        } else {
                            
                            // CSS title
                            $cssTitle = ( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'title' ] ) )     ? ' title="' . htmlspecialchars( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'title' ] ) . '"' : '';
                            
                            // CSS media
                            $cssMedia = ( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'media' ] ) )     ? ' media="' . htmlspecialchars( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'media' ] ) . '"' : '';
                            
                            // CSS rel
                            $cssRel   = ( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeCSS.' ][ $key . '.' ][ 'alternate' ] ) ) ? 'alternate stylesheet'                                                                                      : 'stylesheet';
                            
                            // Adds a LINK tag
                            $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                                        .  $this->TAB
                                                        .  '<link rel="'
                                                        .  $cssRel
                                                        .  '" type="text/css" href="'
                                                        .  htmlspecialchars( $styleSheetFile )
                                                        .  '"'
                                                        .  $cssTitle
                                                        .  $cssMedia
                                                        .  ' />';
                        }
                    }
                }
            }
            
            // Comment
            $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                        .  $this->NL
                                        .  '<!-- INCLUDE CSS: end -->'
                                        .  $this->NL;
        }
        
        // Inline styles
        $style  = '';
        
        // Checks for inline styles
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'CSS_inlineStyle' ] )
            && $tempInlineStyles = trim( $GLOBALS[ 'TSFE' ]->pSetup[ 'CSS_inlineStyle' ] )
        ) {
            
            $style = '/***************************************************************************'
                   . $this->NL
                   . ' * Page inline CSS styles'
                   . $this->NL
                   . ' **************************************************************************/'
                   . $this->NL
                   . $tempInlineStyles
                   .  $this->NL;
        }
        
        // Checks for RTE classes
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'insertClassesFromRTE' ] ) && $GLOBALS[ 'TSFE' ]->pSetup[ 'insertClassesFromRTE' ] ) {
            
            // Gets the TSConfig for the current page
            $pageTSConfig =  $GLOBALS[ 'TSFE' ]->getPagesTSconfig();
            
            // Checks for RTE classes
            if( isset( $pageTSConfig[ 'RTE.' ][ 'classes.' ] ) && is_array( $pageTSConfig[ 'RTE.' ][ 'classes.' ] ) ) {
                
                // Process the RTE classes
                foreach( $pageTSConfig[ 'RTE.' ][ 'classes.' ] as $rteClassName => $rteClassStyles ) {
                    
                    // Checks the value
                    if( isset( $rteClassStyles[ 'value' ] ) && $rteClassStyles[ 'value' ] ) {
                        
                        // Adds the CSS styles
                        $style .= $this->NL
                               .  substr( $rteClassName, 0, -1 )
                               .  ' {'
                               .  $this->NL
                               .  $this->TAB
                               .  $rteClassStyles[ 'value' ]
                               .  $this->NL
                               .  $this->TAB
                               .  '}';
                    }
                }
            }
            
                

            if($GLOBALS['TSFE']->pSetup['insertClassesFromRTE.']['add_mainStyleOverrideDefs'] && is_array($pageTSConfig['RTE.']['default.']['mainStyleOverride_add.']))	{
                $mSOa_tList = t3lib_div::trimExplode(',',strtoupper($GLOBALS['TSFE']->pSetup['insertClassesFromRTE.']['add_mainStyleOverrideDefs']),1);
                foreach ($pageTSConfig['RTE.']['default.']['mainStyleOverride_add.'] as $mSOa_key=>$mSOa_value)	{
                    if(!is_array($mSOa_value) && (in_array('*',$mSOa_tList)||in_array($mSOa_key,$mSOa_tList)))	{
                        $style.='
'.$mSOa_key.' {'.$mSOa_value.'}';
                    }
                }
            }
        }

            // Setting body tag margins in CSS:
        if(isset($GLOBALS['TSFE']->pSetup['bodyTagMargins']) && $GLOBALS['TSFE']->pSetup['bodyTagMargins.']['useCSS'])	{
            $margins = intval($GLOBALS['TSFE']->pSetup['bodyTagMargins']);
            $style.='
    BODY {margin: '.$margins.'px '.$margins.'px '.$margins.'px '.$margins.'px;}';
        }

        if($GLOBALS['TSFE']->pSetup['noLinkUnderline'])	{
            $style.='
    A:link {text-decoration: none}
    A:visited {text-decoration: none}
    A:active {text-decoration: none}';
        }
        if(trim($GLOBALS['TSFE']->pSetup['hover']))	{
            $style.='
    A:hover {color: '.trim($GLOBALS['TSFE']->pSetup['hover']).';}';
        }
        if(trim($GLOBALS['TSFE']->pSetup['hoverStyle']))	{
            $style.='
    A:hover {'.trim($GLOBALS['TSFE']->pSetup['hoverStyle']).'}';
        }
        if($GLOBALS['TSFE']->pSetup['smallFormFields'])	{
            $style.='
    SELECT {  font-family: Verdana, Arial, Helvetica; font-size: 10px }
    TEXTAREA  {  font-family: Verdana, Arial, Helvetica; font-size: 10px}
    INPUT   {  font-family: Verdana, Arial, Helvetica; font-size: 10px }';
        }
        if($GLOBALS['TSFE']->pSetup['adminPanelStyles'])	{
            $style.='

    /* Default styles for the Admin Panel */
    TABLE.typo3-adminPanel { border: 1px solid black; background-color: #F6F2E6; }
    TABLE.typo3-adminPanel TR.typo3-adminPanel-hRow TD { background-color: #9BA1A8; }
    TABLE.typo3-adminPanel TR.typo3-adminPanel-itemHRow TD { background-color: #ABBBB4; }
    TABLE.typo3-adminPanel TABLE, TABLE.typo3-adminPanel TD { border: 0px; }
    TABLE.typo3-adminPanel TD FONT { font-family: verdana; font-size: 10px; color: black; }
    TABLE.typo3-adminPanel TD A FONT { font-family: verdana; font-size: 10px; color: black; }
    TABLE.typo3-editPanel { border: 1px solid black; background-color: #F6F2E6; }
    TABLE.typo3-editPanel TD { border: 0px; }
            ';
        }

        if( trim( $style ) ) {
            if($GLOBALS['TSFE']->config['config']['inlineStyle2TempFile'])	{
                $GLOBALS['TSFE']->content.=TSpagegen::inline2TempFile($style, 'css');
            } else {
                $GLOBALS['TSFE']->content.='
    <style type="text/css">
        /*<![CDATA[*/
    <!--'.$style.'
    -->
        /*]]>*/
    </style>';
            }
        }

        // Include JavaScript files
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeJS.' ] ) && is_array( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeJS.' ] ) ) {
            
            // Process JavaScript files
            foreach( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeJS.' ] as $key => $javaScriptFile ) {
                
                // Only process valid entries
                if( is_string( $javaScriptFile ) ) {
                    
                    // Gets the file
                    if( $jsFileName = $GLOBALS[ 'TSFE' ]->tmpl->getFileName( $JSfile ) ) {
                        
                        // Type attribute for the SCRIPT tag
                        $type = ( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'includeJS.' ][ $key . '.' ][ 'type' ] ) && $GLOBALS[ 'TSFE' ]->pSetup[ 'includeJS.' ][ $key . '.' ][ 'type' ] ) ? $GLOBALS[ 'TSFE' ]->pSetup[ 'includeJS.' ][ $key . '.' ][ 'type' ] : 'text/javascript';
                        
                        // Adds the SCRIPT tag
                        $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                                    .  $this->TAB
                                                    .  '<script src="'
                                                    .  htmlspecialchars( $jsFileName )
                                                    .  '" type="'
                                                    .  htmlspecialchars( $type )
                                                    .  '"></script>';
                    }
                }
            }
        }
        
        
        
        
        
        // Checks for header data
        if( isset( $GLOBALS[ 'TSFE' ]->pSetup[ 'headerData.' ] ) && is_array( $GLOBALS[ 'TSFE' ]->pSetup[ 'headerData.' ] ) ) {
            
            // Adds the header data
            $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                        .  $GLOBALS[ 'TSFE' ]->cObj->cObjGet( $GLOBALS[ 'TSFE' ]->pSetup[ 'headerData.' ], 'headerData.' );
        }
        
        
        $conf=$GLOBALS['TSFE']->pSetup['meta.'];
        if(is_array($conf))	{
            reset($conf);
            while(list($theKey,$theValue)=each($conf))	{
                if(!strstr($theKey,'.') || !isset($conf[substr($theKey,0,-1)]))	{		// Only if 1) the property is set but not the value itself, 2) the value and/or any property
                    if(strstr($theKey,'.'))	{
                        $theKey = substr($theKey,0,-1);
                    }
                    $val = $GLOBALS['TSFE']->cObj->stdWrap($conf[$theKey],$conf[$theKey.'.']);
                    $key = $theKey;
                    if(trim($val))	{
                        $a='name';
                        if(strtolower($key)=='refresh')	{$a='http-equiv';}
                        $GLOBALS['TSFE']->content.= '
    <meta '.$a.'="'.$key.'" content="'.htmlspecialchars(trim($val)).'" />';
                    }
                }
            }
        }
        
        unset( $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'JSCode' ] );
        unset( $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'JSImgCode' ] );
        
        if(is_array($GLOBALS['TSFE']->config['INTincScript']))	{
                // Storing the JSCode and JSImgCode vars...
            $GLOBALS['TSFE']->additionalHeaderData['JSCode'] = $GLOBALS['TSFE']->JSCode;
            $GLOBALS['TSFE']->additionalHeaderData['JSImgCode'] = $GLOBALS['TSFE']->JSImgCode;
            $GLOBALS['TSFE']->config['INTincScript_ext']['divKey'] = $GLOBALS['TSFE']->uniqueHash();
            $GLOBALS['TSFE']->config['INTincScript_ext']['additionalHeaderData'] = $GLOBALS['TSFE']->additionalHeaderData;	// Storing the header-data array
            $GLOBALS['TSFE']->config['INTincScript_ext']['additionalJavaScript'] = $GLOBALS['TSFE']->additionalJavaScript;	// Storing the JS-data array
            $GLOBALS['TSFE']->config['INTincScript_ext']['additionalCSS'] = $GLOBALS['TSFE']->additionalCSS;	// Storing the Style-data array
            
            $GLOBALS['TSFE']->additionalHeaderData = array('<!--HD_'.$GLOBALS['TSFE']->config['INTincScript_ext']['divKey'].'-->');	// Clearing the array
            $GLOBALS['TSFE']->divSection.= '<!--TDS_'.$GLOBALS['TSFE']->config['INTincScript_ext']['divKey'].'-->';
        } else {
            $GLOBALS['TSFE']->INTincScript_loadJSCode();
        }
        $JSef = TSpagegen::JSeventFunctions();
        
            // Adding default Java Script:
        $_scriptCode = '
        
        // Browser informations
        var browserName = navigator.appName;
        var browserVer  = parseInt( navigator.appVersion );
        var version     = "";
        var msie4       = ( browserName == "Microsoft Internet Explorer" && browserVer >= 4 );
        
        // Checks the browser
        if( ( browserName == "Netscape" && browserVer >= 3 ) || msie4 || browserName=="Konqueror" || browserName=="Opera" ) {
            
            // Sets the browser version
            version = "n3";
            
        } else {
            
            // Sets the browser version
            version = "n2";
        }
        
        /**
         * Blurs a link
         * 
         * @param   object  The HTML link object
         * @return  Void
         */
        function blurLink( theObject )
        {
            // Checks for MSIE
            if( msie4 ) {
                
                // Blurs the object
                theObject.blur();
            }
        }
        ';
        
        // Checks for the spam protection settings
        if( $GLOBALS[ 'TSFE' ]->spamProtectEmailAddresses && $GLOBALS[ 'TSFE' ]->spamProtectEmailAddresses !== 'ascii' ) {
            
            $_scriptCode.= '
            
        /**
         * 
         */
        function decryptCharcode(n,start,end,offset)	{
            n = n + offset;
            if(offset > 0 && n > end)	{
                n = start + (n - end - 1);
            } else if(offset < 0 && n < start)	{
                n = end - (start - n - 1);
            }
            return String.fromCharCode(n);
        }
            // decrypt string
        function decryptString(enc,offset)	{
            var dec = "";
            var len = enc.length;
            for(var i=0; i < len; i++)	{
                var n = enc.charCodeAt(i);
                if(n >= 0x2B && n <= 0x3A)	{
                    dec += decryptCharcode(n,0x2B,0x3A,offset);	// 0-9 . , - + / :
                } else if(n >= 0x40 && n <= 0x5A)	{
                    dec += decryptCharcode(n,0x40,0x5A,offset);	// A-Z @
                } else if(n >= 0x61 && n <= 0x7A)	{
                    dec += decryptCharcode(n,0x61,0x7A,offset);	// a-z
                } else {
                    dec += enc.charAt(i);
                }
            }
            return dec;
        }
            // decrypt spam-protected emails
        function linkTo_UnCryptMailto(s)	{
            location.href = decryptString(s,'.($GLOBALS['TSFE']->spamProtectEmailAddresses*-1).');
        }
        ';
        }
        
        if(!$GLOBALS['TSFE']->config['config']['removeDefaultJS']) {
                // NOTICE: The following code must be kept synchronized with "tslib/default.js"!!!
            $GLOBALS['TSFE']->content.='
    <script type="text/javascript">
        /*<![CDATA[*/
    <!--'.$_scriptCode.'
    // -->
        /*]]>*/
    </script>';
        } elseif($GLOBALS['TSFE']->config['config']['removeDefaultJS']==='external')	{
            $GLOBALS['TSFE']->content.= TSpagegen::inline2TempFile($_scriptCode, 'js');
        }
        
        $GLOBALS['TSFE']->content.= chr(10).implode($GLOBALS['TSFE']->additionalHeaderData,chr(10)).'
'.$JSef[0].'
</head>';
        if($GLOBALS['TSFE']->pSetup['frameSet.'])	{
            $fs = t3lib_div::makeInstance('tslib_frameset');
            $GLOBALS['TSFE']->content.= $fs->make($GLOBALS['TSFE']->pSetup['frameSet.']);
            $GLOBALS['TSFE']->content.= chr(10).'<noframes>'.chr(10);
        }
        
            // Bodytag:
        $defBT = $GLOBALS['TSFE']->pSetup['bodyTagCObject'] ? $GLOBALS['TSFE']->cObj->cObjGetSingle($GLOBALS['TSFE']->pSetup['bodyTagCObject'],$GLOBALS['TSFE']->pSetup['bodyTagCObject.'],'bodyTagCObject') : '';
        if(!$defBT)	$defBT = $GLOBALS['TSFE']->defaultBodyTag;
        $bodyTag = $GLOBALS['TSFE']->pSetup['bodyTag'] ? $GLOBALS['TSFE']->pSetup['bodyTag'] : $defBT;
        if($bgImg=$GLOBALS['TSFE']->cObj->getImgResource($GLOBALS['TSFE']->pSetup['bgImg'],$GLOBALS['TSFE']->pSetup['bgImg.']))	{
            $bodyTag = ereg_replace('>$','',trim($bodyTag)).' background="'.$GLOBALS["TSFE"]->absRefPrefix.$bgImg[3].'">';
        }
        
        if(isset($GLOBALS['TSFE']->pSetup['bodyTagMargins']))	{
            $margins = intval($GLOBALS['TSFE']->pSetup['bodyTagMargins']);
            if($GLOBALS['TSFE']->pSetup['bodyTagMargins.']['useCSS'])	{
                // Setting margins in CSS, see above
            } else {
                $bodyTag = ereg_replace('>$','',trim($bodyTag)).' leftmargin="'.$margins.'" topmargin="'.$margins.'" marginwidth="'.$margins.'" marginheight="'.$margins.'">';
            }
        }
        
        if(trim($GLOBALS['TSFE']->pSetup['bodyTagAdd']))	{
            $bodyTag = ereg_replace('>$','',trim($bodyTag)).' '.trim($GLOBALS['TSFE']->pSetup['bodyTagAdd']).'>';
        }
        
        if(count($JSef[1]))	{	// Event functions:
            $bodyTag = ereg_replace('>$','',trim($bodyTag)).' '.trim(implode(' ',$JSef[1])).'>';
        }
        $GLOBALS['TSFE']->content.= chr(10).$bodyTag;
        
        
        // Div-sections
        if($GLOBALS['TSFE']->divSection)	{
            $GLOBALS['TSFE']->content.= chr(10).$GLOBALS['TSFE']->divSection;
        }
        
        // Adds the page content
        $GLOBALS['TSFE']->content .= $this->NL
                                  .  $this->pageContent;
        
        // Ends the BODY tag
        $GLOBALS[ 'TSFE' ]->content .= $this->NL
                                    .  '</body>';
        
        // Checks for a frameset
        if( $GLOBALS[ 'TSFE' ]->pSetup[ 'frameSet.' ] ) {
            
            // Ends the NOFRAMES tag
            $GLOBALS['TSFE']->content .= $this->NL
                                      .  '</noframes>';
        }
        
        // Ends the HTML tag
        $GLOBALS['TSFE']->content .= $this->NL
                                  .  '</html>';
    }
}

// XCLASS inclusion
if(defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pagegen_macmade/class.ux_tspagegen.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pagegen_macmade/class.ux_tspagegen.php']);
}
