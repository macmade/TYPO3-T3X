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
 * Class/Function which generates an XML menu
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

// Include classes that are needed by this one
require_once( PATH_tslib . 'class.tslib_menu.php' );
require_once( PATH_tslib . 'class.tslib_content.php' );

class tx_xmlmenumacmade_menu
{
    // TSLib template instance
    var $templateObject   = NULL;
    
    // TSLib page select instance
    var $pageSelectObject = NULL;
    
    // TSLib TMENU instance
    var $menuObject       = NULL;
    
    // Plugin configuration
    var $conf             = array();
    
    // Menu items configuration
    var $menuConf         = array();
    
    // Rootline
    var $rootLine         = array();
    
    // Current page ID
    var $pageId           = 0;
    
    /**
     * 
     */
    function tx_xmlmenumacmade_menu()
    {
        // Gets a reference to the template object
        $this->templateObject          =& $GLOBALS[ 'TSFE' ]->tmpl;
        
        // Gets a reference to the page select object
        $this->pageSelectObject        =& $GLOBALS[ 'TSFE' ]->sys_page;
        
        // Gets a reference to the rootline
        $this->rootLine                =& $GLOBALS[ 'TSFE' ]->config[ 'rootLine' ];
        
        // Gets a reference to the plugin configuration array
        $this->conf                    =& $this->templateObject->setup[ 'plugin.' ][ 'tx_xmlmenumacmade_pi1.' ];
        
        // Gets the current page ID
        $this->pageId                  =  $GLOBALS[ 'TSFE' ]->id;
        
        // Creates a new instance of TSLib TMENU
        $this->menuObject              =  t3lib_div::makeInstance('tslib_tmenu');
        
        // Sets the parent object for the menu
        $this->menuObject->parent_cObj =& $this;
        
        // Builds the configuration for each level of the menu
        $this->buildMenuConf();
    }
    
    function buildMenuConf()
    {
        // Sets the options of the HMENU object
        $this->menuConf[ 'excludeUidList' ]   = $this->conf[ 'excludeList' ];
        $this->menuConf[ 'excludeDoktypes' ]  = $this->conf[ 'excludeDoktypes' ];
        $this->menuConf[ 'includeNotInMenu' ] = $this->conf[ 'includeNotInMenu' ];
        
        // Sets the starting level
        $this->menuConf[ 'entryLevel' ] = ( $this->conf[ 'startMenuOnCurrentPage' ] ) ? count( $this->rootLine ) - 1  : $this->conf[ 'entryLevel' ];
        
        // Gets an array with the fields to add
        $fieldList = explode( ',', str_replace( ' ', '', $this->conf[ 'fields' ] ) );
        
        // Storage for fields
        $fields = '';
        
        // CDATA tags
        $cdataStart = ( $this->conf[ 'cdata' ] == 1 ) ? '<![CDATA[' : '';
        $cdataEnd   = ( $this->conf[ 'cdata' ] == 1 ) ? ']]>'       : '';
        
        // Process each field
        foreach( $fieldList as $fieldName ) {
            
            // Do not process the page title, as it will automatically be added
            if( $fieldName != 'title' ) {
                
                // Adds current field
                $fields .= '<'
                        .  $fieldName
                        .  '>'
                        . $cdataStart
                        . '{field:'
                        .  $fieldName
                        .  '}'
                        . $cdataEnd
                        . '</'
                        .  $fieldName
                        .  '>';
            }
        }
        
        // Checks if the page link must be added
        if( $this->conf[ 'addPageLink' ] == 1 ) {
            
            // Gets the URL of the TYPO3 site
            $typo3Url = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' );
            
            $getVars = explode( ',', str_replace( ' ', '', $this->conf[ 'keepGetVars' ] ) );
            
            $queryString = '';
            
            foreach( $getVars as $varName ) {
                
                if( $varValue = t3lib_div::_GET( $varName ) ) {
                    
                    $queryString .= '&amp;' . $varName . '=' . $varValue;
                }
            }
            
            $link = '<'
                  . $this->conf[ 'tags.' ][ 'pageLink' ]
                  . '>'
                  . $cdataStart
                  . $typo3Url
                  . 'index.php?id={field:uid}'
                  . $queryString
                  . $cdataEnd
                  . '</'
                  . $this->conf[ 'tags.' ][ 'pageLink' ]
                  . '>';
            
        } else {
            
            // No link
            $link = '';
        }
        
        // Process each level which has to be generated
        for( $i = 1; $i < ( $this->conf[ 'levels' ] + 1 ); $i++ ) {
            
            // Creates a TMENU object
            $this->menuConf[ $i ]       = 'TMENU';
            $this->menuConf[ $i . '.' ] = array();
            
            // Checks if the sections must be expanded
            if( $this->conf[ 'expAll' ] == 1 ) {
                
                // Expands all sections
                $this->menuConf[ $i . '.' ][ 'expAll' ] = 1;
            }
            
            // Activates the normal state
            $this->menuConf[ $i . '.' ][ 'NO' ]  = 1;
            $this->menuConf[ $i . '.' ][ 'NO.' ] = array();
            
            // Do not link the menu item
            $this->menuConf[ $i . '.' ][ 'NO.' ][ 'doNotLinkIt' ] = 1;
            
            // Container tag for the page
            $this->menuConf[ $i . '.' ][ 'NO.' ][ 'wrapItemAndSub' ] = '<'
                                                                     . $this->conf[ 'tags.' ][ 'page' ]
                                                                     . '>|</'
                                                                     . $this->conf[ 'tags.' ][ 'page' ]
                                                                     . '>';
            // Adds the page data
            $this->menuConf[ $i . '.' ][ 'NO.' ][ 'stdWrap.' ][ 'dataWrap' ] = '<'
                                                                             . $this->conf[ 'tags.' ][ 'pageData' ]
                                                                             . '><title>'
                                                                             . $cdataStart
                                                                             . '|'
                                                                             . $cdataEnd
                                                                             . '</title>'
                                                                             . $link
                                                                             . $fields
                                                                             . '</'
                                                                             . $this->conf[ 'tags.' ][ 'pageData' ]
                                                                             . '>';
            
            // Activates the additionnal states
            $this->menuConf[ $i . '.' ][ 'IFSUB' ]    = 1;
            $this->menuConf[ $i . '.' ][ 'ACT' ]      = 1;
            $this->menuConf[ $i . '.' ][ 'ACTIFSUB' ] = 1;
            $this->menuConf[ $i . '.' ][ 'CUR' ]      = 1;
            $this->menuConf[ $i . '.' ][ 'CURIFSUB' ] = 1;
            $this->menuConf[ $i . '.' ][ 'SPC' ]      = 1;
            
            // Configures the additionnal states as the normal state
            $this->menuConf[ $i . '.' ][ 'IFSUB.' ]    = $this->menuConf[ $i . '.' ][ 'NO.' ];
            $this->menuConf[ $i . '.' ][ 'ACT.' ]      = $this->menuConf[ $i . '.' ][ 'NO.' ];
            $this->menuConf[ $i . '.' ][ 'ACTIFSUB.' ] = $this->menuConf[ $i . '.' ][ 'NO.' ];
            $this->menuConf[ $i . '.' ][ 'CUR.' ]      = $this->menuConf[ $i . '.' ][ 'NO.' ];
            $this->menuConf[ $i . '.' ][ 'CURIFSUB.' ] = $this->menuConf[ $i . '.' ][ 'NO.' ];
            $this->menuConf[ $i . '.' ][ 'SPC.' ]      = $this->menuConf[ $i . '.' ][ 'NO.' ];
            
            // Configuration for pages that have subpages
            $this->menuConf[ $i . '.' ][ 'IFSUB.' ][ 'wrapItemAndSub' ]    = '<'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . ' '
                                                                           . $this->conf[ 'attribs.' ][ 'ifsub' ]
                                                                           . '="1">|</'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . '>';
            
            // Configuration for pages that are active
            $this->menuConf[ $i . '.' ][ 'ACT.' ][ 'wrapItemAndSub' ]      = '<'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . ' '
                                                                           . $this->conf[ 'attribs.' ][ 'act' ]
                                                                           . '="1">|</'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . '>';
            
            // Configuration for pages that have subpages and are active
            $this->menuConf[ $i . '.' ][ 'ACTIFSUB.' ][ 'wrapItemAndSub' ] = '<'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . ' '
                                                                           . $this->conf[ 'attribs.' ][ 'act' ]
                                                                           . '="1" '
                                                                           . $this->conf[ 'attribs.' ][ 'ifsub' ]
                                                                           . '="1">|</'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . '>';
            
            // Configuration for the current page
            $this->menuConf[ $i . '.' ][ 'CUR.' ][ 'wrapItemAndSub' ]      = '<'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . ' '
                                                                           . $this->conf[ 'attribs.' ][ 'cur' ]
                                                                           . '="1">|</'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . '>';
            
            // Configuration for the current page, if it has subpages
            $this->menuConf[ $i . '.' ][ 'CURIFSUB.' ][ 'wrapItemAndSub' ] = '<'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . ' '
                                                                           . $this->conf[ 'attribs.' ][ 'cur' ]
                                                                           . '="1" '
                                                                           . $this->conf[ 'attribs.' ][ 'ifsub' ]
                                                                           . '="1">|</'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . '>';
            
            // Configuration for pages that are spacers
            $this->menuConf[ $i . '.' ][ 'SPC.' ][ 'wrapItemAndSub' ]      = '<'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . ' '
                                                                           . $this->conf[ 'attribs.' ][ 'spc' ]
                                                                           . '="1">|</'
                                                                           . $this->conf[ 'tags.' ][ 'page' ]
                                                                           . '>';
            
            // Checks if we are on a sublevel
            if( $i > 1 ) {
                
                // Adds a container for the pages
                $this->menuConf[ $i . '.' ][ 'wrap' ] = '<'
                                                      . $this->conf[ 'tags.' ][ 'subPages' ]
                                                      . '>|</'
                                                      . $this->conf[ 'tags.' ][ 'subPages' ]
                                                      . '>';
            }
        }
    }
    
    function makeMenu()
    {
        // Starts the menu object
        $this->menuObject->start(
            $this->templateObject,
            $this->pageSelectObject,
            false,
            $this->menuConf,
            1
        );
        
        // Creates the menu
        $this->menuObject->makeMenu();
        return true;
    }
    
    function getXml()
    {
        // Storage
        $xmlMenu = '';
        
        // Check if the XML declaration must be included
        if( $this->conf[ 'xmlDeclaration' ] == 1 ) {
            
            // Includes the XML declaration
            $xmlMenu .= '<'
                     .  '?xml version="1.0" encoding="'
                     .  $this->conf[ 'xmlEncoding' ]
                     . '"?>';
        }
        
        // Writes the full menu
        $xmlMenu .= '<'
                  . $this->conf[ 'tags.' ][ 'root' ]
                  . '>'
                  . $this->menuObject->writeMenu()
                  . '</'
                  . $this->conf[ 'tags.' ][ 'root' ]
                  . '>';
        
        // Returns the full XML code
        return $xmlMenu;
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xmlmenu_macmade/class.tx_xmlmenumacmade_menu.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xmlmenu_macmade/class.tx_xmlmenumacmade_menu.php']);
}
