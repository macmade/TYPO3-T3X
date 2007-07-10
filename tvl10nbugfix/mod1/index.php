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
 * Module 'TV L10N BugFix' for the 'tvl10nbugfix' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - INIT
 *        :     function init
 *        : function main
 * 
 * SECTION:     2 - MAIN
 *        :     function menuConfig
 *        :     function moduleContent
 * 
 *              TOTAL FUNCTIONS: 
 */

// Default initialization of the module
unset( $MCONF );
require( 'conf.php' );
require( $BACK_PATH . 'init.php' );
require( $BACK_PATH . 'template.php' );
$LANG->includeLLFile( 'EXT:tvl10nbugfix/mod1/locallang.php' );
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
require_once( PATH_t3lib . 'class.t3lib_tcemain.php' );
$BE_USER->modAccess( $MCONF, 1 );

// Developer API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_tvl10nbugfix_module1 extends t3lib_SCbase
{
    
    // Page informations
    var $pageinfo;
    
    // Developer API version
    var $apimacmade_version = 2.8;
    
    // Current page record
    var $page               = array();
    
    // Current flex structure
    var $flex               = array();
    
    // Data structure for default lanuage
    var $lDef               = array();
    
    // Table for content elements
    var $contentTable       = '';
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - INIT
     *
     * Base module functions.
     ***************************************************************/
    
    /**
     * Initialization of the class.
     * 
     * @return      Void
     */
    function init()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        
        $this->contentTable = $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'contentTable' ];
        
        // Init
        parent::init();
    }
    
    /**
     * Creates the page.
     * 
     * This function creates the basic page in which the module will
     * take place.
     * 
     * @return      Void
     */
    function main()
    {
        global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
        
        // New instance of the Developer API
        $this->api = new tx_apimacmade( $this );
        
        // Access check
        $this->pageinfo = t3lib_BEfunc::readPageAccess( $this->id, $this->perms_clause );
        $access = is_array( $this->pageinfo ) ? 1 : 0;
        
        if( ( $this->id && $access ) || ( $BE_USER->user[ 'admin' ] && !$this->id ) ) {
            
            // Draw the header
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = $BACK_PATH;
            $this->doc->form     = '<form action="" method="POST">';
            
            // JavaScript
            $this->doc->JScode = '
                <script type="text/javascript" language="Javascript" charset="iso-8859-1">
                    <!--
                    script_ended = 0;
                    function jumpToUrl(URL) {
                        document.location = URL;
                    }
                    //-->
                </script>
            ';
            $this->doc->postCode = '
                <script type="text/javascript" language="Javascript" charset="iso-8859-1">
                    <!--
                    script_ended = 1;
                    if(top.fsMod) {
                        top.fsMod.recentIds["web"] = ' . intval( $this->id ) . ';
                    }
                    //-->
                </script>
            ';
            
            // Initialize CSM
            $this->api->be_initCsm();
            
            // Build current path
            $headerSection = $this->doc->getHeader(
                'pages',
                $this->pageinfo,
                $this->pageinfo[ '_thePath' ]
            )
            . '<br>'
            . $LANG->sL( 'LLL:EXT:lang/locallang_core.php:labels.path' )
            . ': '
            . t3lib_div::fixed_lgd_pre( $this->pageinfo[ '_thePath' ], 50 );
            
            // Start page content
            $this->content .= $this->doc->startPage( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->header( $LANG->getLL( 'title' ) );
            $this->content .= $this->doc->spacer( 5 );
            $this->content .= $this->doc->section(
                '',
                $this->doc->funcMenu(
                    $headerSection,
                    t3lib_BEfunc::getFuncMenu(
                        $this->id,
                        'SET[function]',
                        $this->MOD_SETTINGS[ 'function' ],
                        $this->MOD_MENU[ 'function' ]
                    )
                )
            );
            $this->content .= $this->doc->divider( 5 );
            
            // Render content
            $this->moduleContent();
            
            // Adds shortcut
            if( $BE_USER->mayMakeShortcut() ) {
                $this->content .= $this->doc->spacer( 20 )
                               . $this->doc->section(
                                    '',
                                    $this->doc->makeShortcutIcon(
                                        'id',
                                        implode( ',', array_keys( $this->MOD_MENU ) ),
                                        $this->MCONF[ 'name' ]
                                    )
                                );
            }
            
            $this->content .= $this->doc->spacer(10);
            
        } else {
            
            // No access
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = $BACK_PATH;
            $this->content      .= $this->doc->startPage( $LANG->getLL( 'title' ) );
            $this->content      .= $this->doc->header( $LANG->getLL ( 'title' ) );
            $this->content      .= $this->doc->spacer( 5 );
            $this->content      .= $LANG->getLL( 'noaccess' );
            $this->content      .= $this->doc->spacer( 10 );
        }
    }
    
    
    
    
    
    /***************************************************************
     * SECTION 2 - MAIN
     *
     * Main module functions.
     ***************************************************************/
    
    /**
     * Creates the module's menu.
     * 
     * This function creates the module's menu.
     * 
     * @return		Void
     */
    function menuConfig()
    {
        global $LANG;
            
        // Menu items
        $this->MOD_MENU = array (
            'function' => array (
                '1' => $LANG->getLL( 'menu.func.1' )
            )
        );
        
        // Creates menu
        parent::menuConfig();
    }
    
    /**
     * Prints the page.
     * 
     * This function closes the page, and writes the final
     * rendered content.
     * 
     * @return      Void
     */
    function printContent()
    {
        $this->content .= $this->doc->endPage();
        echo( $this->content );
    }
    
    /**
     * Writes some code.
     * 
     * This function writes a text wrapped inside an HTML tag.
     * @param       string      $text               The text to write
     * @param       string      $tag                The HTML tag to write
     * @param       array       $style              An array containing the CSS styles for the tag
     * @return      string      The text wrapped in the HTML tag
     */
    function writeHTML( $text, $tag = 'div', $class = false, $style = false, $params = array() )
    {
        
        // Create style tag
        if ( is_array( $style ) ) {
            $styleTag = ' style="' . implode( '; ', $style ) . '"';
        }
        
        // Create class tag
        if ( isset( $class ) ) {
            $classTag = ' class="' . $class . '"';
        }
        
        // Tag parameters
        if( count( $params ) ) {
            $paramsTags = ' ' . $this->api->div_writeTagParams( $params );
        }
        
        // Return HTML text
        return '<' . $tag . $classTag . $styleTag . $paramsTags . '>' . $text . '</' . $tag . '>';
    }
    
    /**
     * Creates the module's content.
     * 
     * This function creates the module's content.
     * 
     * @return		Void
     */
    function moduleContent() {
        global $LANG;
        
        // Get current page record
        $this->page = t3lib_BEfunc::getRecord( 'pages', $this->id );
        
        // Storage
        $htmlCode   = array();
        
        // Checks if current page contains flex data
        if( !empty( $this->page[ 'tx_templavoila_flex' ] ) ) {
            
            // Converts flex data to a PHP array
            $this->flex = t3lib_div::xml2array( $this->page[ 'tx_templavoila_flex' ] );
            
            // Check data fields
            if( isset( $this->flex[ 'data' ][ 'sDEF' ][ 'lDEF' ] ) && count( $this->flex[ 'data' ][ 'sDEF' ][ 'lDEF' ] ) ) {
                
                // Store default language sheet
                $this->lDef = $this->flex[ 'data' ][ 'sDEF' ][ 'lDEF' ];
                
                // Build content
                $htmlCode[] = $this->rebuildView();
                $htmlCode[] = $this->showContentElements();
                
            } else {
                
                // Error - No data in flex structure
                $htmlCode[] = $this->doc->spacer( 10 );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'error' ), 'h2', 'typo3-red' );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'error.noFlexData' ), 'h2', 'typo3-red' );
            }
            
        } else {
            
            // Error - Invalid flex structure
            $htmlCode[] = $this->doc->spacer( 10 );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'error' ), 'h2', 'typo3-red' );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'error.noFlex' ), 'div', 'typo3-red' );
        }
        
        // Add content
        $this->content .= implode( chr( 10 ), $htmlCode );
    }
    
    function rebuildView()
    {
        global $LANG;
        
        // Storage
        $htmlCode   = array();
        
        // Get module POST data
        $modData    = t3lib_div::_POST( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        // Header
        $htmlCode[] = $this->doc->spacer( 10 );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'rebuild' ), 'h2' );
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Rebuild action was launched
        if( isset( $modData[ 'rebuild' ] ) ) {
            
            // Get recursivity
            $recursive = ( isset( $modData[ 'levels' ] ) ) ? $modData[ 'levels' ] : 0;
            
            // Rebuild structure and confirm
            $this->rebuildMapping( $this->id );
            
            // Rebuild subpages if requested
            if( $recursive > 0 ) {
                
                $this->rebuildMappingOnSubPages( $this->id, $recursive );
            }
            
            // Add confirmation
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'rebuild.confirmation' ), 'div', 'typo3-red' );
            
        } else {
            
            // Start DIV
            $htmlCode[] = '<div style="border: dashed 1px #666666; padding: 10px; background-color: ' . $this->doc->bgColor4 . ';">';
            
            // Start select menu for recursivity
            $levelSelect   = array();
            $levelSelect[] = '<select name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[levels]">';
            
            // Create options for 10 levels
            for( $i = 0; $i <= 10; $i++ ) {
                
                // Check level
                if( $i == 0 ) {
                    
                    // Current page only
                    $label = $LANG->getLL( 'rebuild.recursive.I.current' );
                    
                } elseif( $i == 1 ) {
                    
                    // One level
                    $label = $LANG->getLL( 'rebuild.recursive.I.single' );
                    
                } else {
                    
                    // Multiple levels (up to 10)
                    $label = sprintf( $LANG->getLL( 'rebuild.recursive.I.multiple' ), $i );
                }
                
                // Add option
                $levelSelect[] = '<option value="' . $i . '">' . $label . '</option>';
            }
            
            // End select
            $levelSelect[] = '</select>';
            
            // Add rebuild options
            $htmlCode[]  = $this->writeHTML( '<input type="hidden" value="1" name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[rebuild]" />' );
            $htmlCode[]  = $this->writeHTML( '<input type="submit" value="' . $LANG->getLL( 'rebuild.link' ) . '" onclick="' . htmlspecialchars('return confirm(\'' . $LANG->getLL( 'rebuild.confirm' ) . '\');') . '" />' );
            $htmlCode[]  = $this->doc->spacer( 10 );
            $htmlCode[]  = $this->writeHTML( $LANG->getLL( 'rebuild.recursive' ) . ' ' . implode( chr( 10 ), $levelSelect ) );
            $htmlCode[]  = $this->doc->spacer( 10 );
            $htmlCode[]  = $this->writeHTML( $this->writeHTML( $LANG->getLL( 'rebuild.warning' ), 'strong' ), 'div', 'typo3-red' );
            $htmlCode[]  = '</div>';
        }
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    function rebuildMappingOnSubPages( $pid, $recursive = 0, $level = 0 )
    {
        // Increase level
        $level++;
        
        // Get subpages
        $subPages = t3lib_BEfunc::getRecordsByField( 'pages', 'pid', $pid );
        
        // Check for subpages
        if( is_array( $subPages ) && count( $subPages ) ) {
            
            // Process each subpage
            foreach( $subPages as $subPage ) {
                
                // Rebuild structure
                $this->rebuildMapping( $subPage[ 'uid' ], $subPage[ 'tx_templavoila_flex' ] );
                
                // Check if subpages must be processed as well
                if( $level < $recursive ) {
                    
                    // Process subpages
                    $this->rebuildMappingOnSubPages( $subPage[ 'uid' ], $recursive, $level );
                }
            }
        }
    }
    
    function rebuildMapping( $pid, $flexData = '' )
    {
        global $LANG;
        
        // Check which page is currently processed
        if( $pid == $this->id ) {
            
            // Current page - LDEF has already been checked
            $lDef = $this->lDef;
            
        } else {
            
            // Subpage - Use passed flex structure
            if( !empty( $flexData ) ) {
                
                // Convert XML data to an array
                $flex = t3lib_div::xml2array( $flexData );
                
                // Check data fields
                if( isset( $flex[ 'data' ][ 'sDEF' ][ 'lDEF' ] ) && count( $flex[ 'data' ][ 'sDEF' ][ 'lDEF' ] ) ) {
                    
                    // Data is valid - Store language part
                    $lDef = $flex[ 'data' ][ 'sDEF' ][ 'lDEF' ];
                    
                } else {
                    
                    // No data - Do not process
                    return false;
                }
                
            } else {
                
                // Invalid structure - Do not process
                return false;
            }
        }
        
        // Storage
        $fields = array();
        
        // Get language overlays
        $langOverlay = t3lib_BEfunc::getRecordsByField(
            'pages_language_overlay',
            'pid',
            $pid,
            'AND hidden=0'
        );
        
        // Process each fields of structure
        foreach( $lDef as $key => $value ) {
            
            // Storage for cleaned structure
            $dataStruct  = array(
                'vDEF' => array()
            );
            
            // Check for content elements in the default language
            if( isset( $value[ 'vDEF' ] ) && !empty( $value[ 'vDEF' ] ) && $value[ 'vDEF' ] != 'Array' ) {
                
                // Add default language items
                $dataStruct[ 'vDEF' ] = $value[ 'vDEF' ];
                
                // Get default language items as an array
                $records              = explode( ',', $value[ 'vDEF' ] );
                
            } else {
                
                // Empty array
                $records = array();
            }
            
            // Check for language overlays
            if( is_array( $langOverlay ) && count( $langOverlay ) ) {
                
                // Process each language overlay
                foreach( $langOverlay as $localizedPage ) {
                    
                    // Lang ID
                    $langId                 = $localizedPage[ 'sys_language_uid' ];
                    
                    // Language record
                    $language               = t3lib_BEfunc::getRecord( 'sys_language', $langId );
                    
                    // Language record from static info tables
                    $isoCode                = t3lib_BEfunc::getRecord( 'static_languages', $language[ 'static_lang_isocode' ] );
                    
                    // Flex language identifier
                    $langKey                = 'v' . $isoCode[ 'lg_iso_2' ];
                    
                    // Storage
                    $contentElements        = array();
                    $dataStruct[ $langKey ] = array();
                    
                    // Check for an existing value
                    if( isset( $value[ $langKey ] ) && !empty( $value[ $langKey ] ) ) {
                        
                        // Get an array with existing records
                        $existingRecords = explode( ',', $value[ $langKey ] );
                        
                        // Check for existing records
                        if( is_array( $existingRecords ) && count( $existingRecords ) ) {
                            
                             // Process existing records
                             foreach( $existingRecords as $mappedUid ) {
                                
                                // Get record
                                $mappedRecord = t3lib_BEfunc::getRecord( $this->contentTable, $mappedUid );
                                
                                // Keep record if it's not a translation (specific element for a language)
                                if( is_array( $mappedRecord ) && empty( $mappedRecord[ 'l18n_parent' ] ) ) {
                                    
                                    // Add record to new structure
                                    $contentElements[] = $mappedUid;
                                }
                            }
                        }
                    }
                    
                    // Process each record in the default language
                    foreach( $records as $uid ) {
                        
                        // Get localized content elements
                        $localized = t3lib_BEfunc::getRecordsByField(
                            $this->contentTable,
                            'pid',
                            $pid,
                            ' AND sys_language_uid=' . $langId . ' AND l18n_parent=' . $uid
                        );
                        
                        // Check for localized content elements
                        if( is_array( $localized ) ) {
                            
                            // Get first element (there shouldn't be more than one, but who knows...)
                            $contentElements[] = $localized[ 0 ][ 'uid' ];
                        }
                    }
                    
                    // Add elements in structure for current language overlay
                    $dataStruct[ $langKey ] = implode( ',', $contentElements );
                }
            }
            
            // Add structure for current field
            $fields[ $key ] = $dataStruct;
        }
        
        // Create full flex structure
        $fullFlexForm = array(
            'T3FlexForms' => array(
                'data' => array(
                    'sDEF' => array(
                        'lDEF' => $fields
                    )
                )
            )
        );
        
        // Convert flex structure to XML source
        $xmlFlex = $this->createFlexForm( $fullFlexForm );
        
        // Create TCE data
        $tceData = array(
            'pages' => array(
                $pid => array(
                    'tx_templavoila_flex' => $xmlFlex
                )
            )
        );
        
        // Create a TCE object, and process data
        $tce                      = t3lib_div::makeInstance( 't3lib_TCEmain' );
        $tce->stripslashes_values = 0;
        $tce->start( $tceData, array() );
        $tce->process_datamap();
        #t3lib_div::debug( $tceData, 'PID: ' . $pid );
        // Check if processed page is the current one
        if( $pid == $this->id ) {
            
            // Store new flex structure
            $this->flex = t3lib_div::xml2array( $xmlFlex );
            $this->lDef = $this->flex[ 'data' ][ 'sDEF' ][ 'lDEF' ];
        }
        
        // Return confirmation
        return true;
    }
    
    function createFlexform( $dataArray, $level = 0 )
    {
        // Storage
        $xml      = '';
        
        // ASCII characters
        $tabLevel = str_repeat( chr( 9 ), $level );
        $newLine  = chr( 10 );
        
        // Process each element in the data array
        foreach( $dataArray as $key => $value ) {
            
            // Check level
            switch( $level ) {
                
                // T3FlexForms or data
                case 0:
                case 1:
                    $tagName = $key;
                    $index   = '';
                    break;
                
                // Sheet
                case 2:
                    $tagName = 'sheet';
                    $index   = ' index="' . $key . '"';
                    break;
                
                // Language
                case 3:
                    $tagName = 'language';
                    $index   = ' index="' . $key . '"';
                    break;
                
                // Field
                case 4:
                    $tagName = 'field';
                    $index   = ' index="' . $key . '"';
                    break;
                
                // Values
                case 5:
                    $tagName = 'value';
                    $index   = ' index="' . $key . '"';
                    break;
            }
            
            // Check value type
            if( is_array( $value ) && count( $value ) ) {
                
                // Array with sub elements
                $xml .= ( $level > 0 ) ? $newLine : '';
                $xml .= $tabLevel
                     .  '<' . $tagName . $index . '>'
                     .  $this->createFlexForm( $value, $level + 1 )
                     .  $newLine
                     .  $tabLevel
                     . '</' . $tagName . '>';
                
            } else {
                
                // Data node
                $nodeContent = ( is_string( $value ) || is_numeric( $value ) ) ? $value : '';
                $xml        .= $newLine
                            .  $tabLevel
                            .  '<' . $tagName . $index . '>'
                            .  $nodeContent
                            .  '</' . $tagName . '>';
            }
            
        }
        
        // Return XML data
        return $xml;
    }
    
    function showContentElements()
    {
        global $LANG;
        
        // Storage
        $htmlCode       = array();
        $languages      = array();
        $languagesIcons = array();
        $isoCodes       = array();
        
        // Header
        $htmlCode[]     = $this->doc->spacer( 10 );
        $htmlCode[]     = $this->writeHTML( $LANG->getLL( 'list.elements' ), 'h2' );
        
        // Get TSConfig for current page
        $pageTsConfig   = t3lib_BEfunc::getPagesTSconfig( $this->id );
        
        // Flag icon for default language
        $flag           = ( isset( $pageTsConfig[ 'mod.' ][ 'SHARED.' ][ 'defaultLanguageFlag' ] ) ) ? $pageTsConfig[ 'mod.' ][ 'SHARED.' ][ 'defaultLanguageFlag' ] : false;
        $flagIcon       = ( $flag ) ? '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/flags/' . $flag, '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">' : '';
        
        // Get language overlays for current page
        $langOverlay    = t3lib_BEfunc::getRecordsByField(
                            'pages_language_overlay',
                            'pid',
                            $this->id,
                            'AND hidden=0'
                          );
        
        // Process each field in the flex structure
        foreach(  $this->lDef as $key => $value ) {
            
            // Field header
            $htmlCode[] = $this->doc->spacer( 10 );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.field' ) . ' &lt;' . $key . '&gt;', 'h3', false, array( 'border: dashed 1px #666666', 'padding: 10px', 'background-color: ' . $this->doc->bgColor ) );
            $htmlCode[] = $this->doc->divider( 5 );
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Status icons
            $iconOk     = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_ok2.gif' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
            $iconError  = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_fatalerror.gif' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
            
            // Check for content elements in the default language
            if( isset( $value[ 'vDEF' ] ) && !empty( $value[ 'vDEF' ] ) ) {
                
                // Get content elements in an array
                $records = explode( ',', $value[ 'vDEF' ] );
                
                // Check for records
                if( count( $records ) ) {
                    
                    // Start table
                    $htmlCode[]  = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
                    $headerStyle = array( 'font-weight: bold' );
                    
                    // Table headers
                    $htmlCode[] = '<tr>';
                    $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
                    $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
                    $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.uid' ), 'td', false, $headerStyle );
                    $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.type' ), 'td', false, $headerStyle );
                    $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.title' ), 'td', false, $headerStyle );
                    $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.content' ), 'td', false, $headerStyle );
                    $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
                    $htmlCode[] = '</tr>';
                    
                    // Process each record
                    foreach( $records as $uid ) {
                        
                        // Storage
                        $row        = array();
                        
                        // Get record
                        $record     = t3lib_BEfunc::getRecord( $this->contentTable, $uid );
                        
                        // Record icon
                        $icon       = $this->api->be_getRecordCSMIcon( $this->contentTable, $record, $GLOBALS[ 'BACK_PATH' ] );
                        
                        // Record title
                        $title      = $record[ 'header' ];
                        
                        // Record content field (if available)
                        $content    = nl2br( $this->api->div_crop( $record[ 'bodytext' ], 500 ) );
                        
                        // Edit icons
                        $editIcons  = $this->api->be_buildRecordIcons( 'show,edit,delete', $this->contentTable, $uid );
                        
                        // Add record columns and row
                        $row[]      = $this->writeHTML( $icon, 'td' );
                        $row[]      = $this->writeHTML( $flagIcon, 'td', false, $headerStyle );
                        $row[]      = $this->writeHTML( $uid, 'td' );
                        $row[]      = $this->writeHTML( $record[ 'CType' ], 'td' );
                        $row[]      = $this->writeHTML( $this->writeHTML( $title, 'strong' ), 'td' );
                        $row[]      = $this->writeHTML( $content, 'td' );
                        $row[]      = $this->writeHTML( $editIcons, 'td' );
                        $htmlCode[] = '<tr bgcolor="' . $this->doc->bgColor4 . '">' . implode ( chr( 10 ), $row ) . '</tr>';
                        
                        // Check for language overlays
                        if( is_array( $langOverlay ) && count( $langOverlay ) ) {
                            
                            // Process each language overlay
                            foreach( $langOverlay as $localizedPage ) {
                                
                                // ID of the language
                                $langId = $localizedPage[ 'sys_language_uid' ];
                                
                                // Cache for languages
                                if( !isset( $languages[ $langId ] ) ) {
                                    
                                    // Get language record
                                    $languages[ $langId ]      = t3lib_BEfunc::getRecord( 'sys_language', $langId );
                                    
                                    // Flag icon
                                    $langFlag                  = $languages[ $langId ][ 'flag' ];
                                    $languagesIcons[ $langId ] = ( $langFlag ) ? '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/flags/' . $langFlag, '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">' : '';
                                    
                                    // Get language record from static info tables
                                    $isoCodes[ $langId ]       = t3lib_BEfunc::getRecord( 'static_languages', $languages[ $langId ][ 'static_lang_isocode' ] );
                                }
                                
                                // Try to get a localized record
                                $localizedRecords = t3lib_BEfunc::getRecordsByField(
                                    $this->contentTable,
                                    'pid',
                                    $this->id,
                                    ' AND sys_language_uid=' . $langId . ' AND l18n_parent=' . $uid
                                );
                                
                                // Check for a valid localized record
                                if( is_array( $localizedRecords ) && count( $localizedRecords ) ) {
                                    
                                    // Process each localized record (there should be only one, but who knows...)
                                    foreach( $localizedRecords as $translation ) {
                                        
                                        // Storage
                                        $l10nRow   = array();
                                        
                                        // Record title
                                        $title     = $translation[ 'header' ];
                        
                                        // Record icon
                                        $icon      = $this->api->be_getRecordCSMIcon( $this->contentTable, $translation, $GLOBALS[ 'BACK_PATH' ] );
                                        
                                        // Record content field (if available)
                                        $content   = nl2br( $this->api->div_crop( $translation[ 'bodytext' ], 500 ) );
                                        
                                        // Edit icons
                                        $editIcons = $this->api->be_buildRecordIcons( 'show,edit,delete', $this->contentTable, $translation[ 'uid' ] );
                                        
                                        // Flex language identifier
                                        $flexKey   = 'v' . $isoCodes[ $langId ][ 'lg_iso_2' ];
                                        
                                        // Mapping state for the localization
                                        $mapped    = ( isset( $value[ $flexKey ] ) ) ? explode( ',', $value[ $flexKey ] ) : array();
                                        $state     = ( in_array( $translation[ 'uid' ], $mapped ) ) ? $iconOk : $iconError;
                                        
                                        // Add columns and row for localized record
                                        $l10nRow[]  = $this->writeHTML( '', 'td' );
                                        $l10nRow[]  = $this->writeHTML( $icon, 'td' );
                                        $l10nRow[]  = $this->writeHTML( $state, 'td', false, $headerStyle );
                                        $l10nRow[]  = $this->writeHTML( $languagesIcons[ $langId ], 'td', false, $headerStyle );
                                        $l10nRow[]  = $this->writeHTML( $this->writeHTML( $title, 'strong' ), 'td' );
                                        $l10nRow[]  = $this->writeHTML( $content, 'td' );
                                        $l10nRow[]  = $this->writeHTML( $editIcons, 'td' );
                                        $htmlCode[] = '<tr bgcolor="' . $this->doc->bgColor5 . '">' . implode ( chr( 10 ), $l10nRow ) . '</tr>';
                                    }
                                }
                            }
                        }
                    }
                    
                    // End table
                    $htmlCode[] = '</table>';
                    
                } else {
                    
                    // Error - No content elements
                    $htmlCode[] = $this->writeHTML( $LANG->getLL( 'error.noContent' ), 'div', 'typo3-red' );
                }
                
            } else {
                
                // Error - No content elements
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'error.noContent' ), 'div', 'typo3-red' );
            }
        }
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
}

// XCLASS inclusion
if( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/tvl10nbugfix/mod1/index.php' ] ) {
    include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/tvl10nbugfix/mod1/index.php' ] );
}

// Make instance
$SOBE = t3lib_div::makeInstance( 'tx_tvl10nbugfix_module1' );
$SOBE->init();

// Include files
foreach( $SOBE->include_once as $INC_FILE ) {
    include_once( $INC_FILE );
}

// Output module
$SOBE->main();
$SOBE->printContent();
?>
