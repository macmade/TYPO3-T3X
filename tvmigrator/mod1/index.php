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
 * Module 'TV Migrator' for the 'tvmigrator' extension.
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
$LANG->includeLLFile( 'EXT:tvmigrator/mod1/locallang.php' );
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
require_once( PATH_t3lib . 'class.t3lib_tcemain.php' );
$BE_USER->modAccess( $MCONF, 1 );

// Developer API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_tvmigrator_module1 extends t3lib_SCbase
{
    
    // Page informations
    var $pageinfo;
    
    // Developer API version
    var $apimacmade_version = 2.8;
    
    // Current page record
    var $page               = array();
    
    // Table for content elements
    var $contentTable       = '';
    
    // Page columns
    var $columns            = array(
        'center' => 0,
        'left'   => 1,
        'right'  => 2,
        'border' => 3
    );
    
    var $dataStructSelect   = '';
    
    
    
    
    
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
        
        if( is_array( $this->page ) ) {
            
            // Storage
            $htmlCode   = array();
                
            // Build content
            $htmlCode[] = $this->mapView();
            $htmlCode[] = $this->showContentElements();
            
            // Add content
            $this->content .= implode( chr( 10 ), $htmlCode );
        }
    }
    
    function mapView()
    {
        global $LANG;
        
        // Storage
        $htmlCode   = array();
        
        // Get module POST data
        $modData    = t3lib_div::_POST( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        // Header
        $htmlCode[] = $this->doc->spacer( 10 );
        $htmlCode[] = $this->writeHTML( $LANG->getLL( 'map' ), 'h2' );
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Map action was launched
        if( isset( $modData[ 'map' ] ) ) {
            
            // Get recursivity
            $recursive = ( isset( $modData[ 'levels' ] ) ) ? $modData[ 'levels' ] : 0;
            
            // Map content elements and confirm
            $this->mapElements( $this->id );
            
            // Remap subpages if requested
            if( $recursive > 0 ) {
                
                $this->mapElementsOnSubPages( $this->id, $recursive );
            }
            
            // Add confirmation
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'map.confirmation' ), 'div', 'typo3-red' );
            
        } else {
            
            // Start DIV
            $htmlCode[] = '<div style="border: dashed 1px #666666; padding: 10px; background-color: ' . $this->doc->bgColor4 . ';">';
            
            // Process columns
            for( $i = 0; $i < 4; $i++ ) {
                
                // Column name
                $columnName = $this->writeHTML( $LANG->getLL( 'column.I.' . $i ), 'strong' );
                
                // DS field
                $dataStructureSelect = $this->createDataStructSelect( $GLOBALS[ 'MCONF' ][ 'name' ] . '[column_' . $i . ']' );
                
                $htmlCode[] = $this->writeHTML(
                    sprintf( $LANG->getLL( 'map.columns' ), $columnName ) . ' ' . $dataStructureSelect,
                    'div',
                    false,
                    array( 'margin-bottom: 15px' )
                );
            }
            
            // Start select menu for recursivity
            $levelSelect   = array();
            $levelSelect[] = '<select name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[levels]">';
            
            // Create options for 10 levels
            for( $i = 0; $i <= 10; $i++ ) {
                
                // Check level
                if( $i == 0 ) {
                    
                    // Current page only
                    $label = $LANG->getLL( 'map.recursive.I.current' );
                    
                } elseif( $i == 1 ) {
                    
                    // One level
                    $label = $LANG->getLL( 'map.recursive.I.single' );
                    
                } else {
                    
                    // Multiple levels (up to 10)
                    $label = sprintf( $LANG->getLL( 'map.recursive.I.multiple' ), $i );
                }
                
                // Add option
                $levelSelect[] = '<option value="' . $i . '">' . $label . '</option>';
            }
            
            // End select
            $levelSelect[] = '</select>';
            
            // Add map options
            $htmlCode[]  = $this->writeHTML( '<input type="hidden" value="1" name="' . $GLOBALS[ 'MCONF' ][ 'name' ] . '[map]" />' );
            $htmlCode[]  = $this->writeHTML( '<input type="submit" value="' . $LANG->getLL( 'map.link' ) . '" />' );
            $htmlCode[]  = $this->doc->spacer( 10 );
            $htmlCode[]  = $this->writeHTML( $LANG->getLL( 'map.recursive' ) . ' ' . implode( chr( 10 ), $levelSelect ) );
            
            // End DIV
            $htmlCode[]  = '</div>';
        }
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
    
    function createDataStructSelect( $name )
    {
        if( empty( $this->dataStructSelect ) ) {
            
            $id = $this->getDsId( $this->id );
            
            $ds = t3lib_BEfunc::getRecord( 'tx_templavoila_datastructure', $id );
            
            if( is_array( $ds ) ) {
                
                $dataArray = t3lib_div::xml2array( $ds[ 'dataprot' ] );
                $fields    = $dataArray[ 'ROOT' ][ 'el' ];
                
                $select = array();
                
                $select[] = '<select name="' . $name . '">';
                
                foreach( $fields as $key => $value ) {
                    
                    if( $value[ 'tx_templavoila' ][ 'eType' ] == 'ce' ) {
                        
                        $select[] = '<option value="' . $key . '">' . $value[ 'tx_templavoila' ][ 'title' ] . ' (' . $key . ')</option>';
                    }
                }
                
                $select[] = '</select>';
                
                $this->dataStructSelect = implode( chr( 10 ), $select );
                
            }
        }
        
        return $this->dataStructSelect;
    }
    
    function getDsId( $pid )
    {
        $page = t3lib_BEfunc::getRecord( 'pages', $pid );
        
        if( !empty( $page[ 'tx_templavoila_ds' ] ) ) {
            
            $pid = $page[ 'tx_templavoila_ds' ];
            
        } else {
            
            $pid = $this->getDsId( $page[ 'pid' ] );
        }
        
        return $pid;
    }
    
    function mapElementsOnSubPages( $pid, $recursive = 0, $level = 0 )
    {
        // Increase level
        $level++;
        
        // Get subpages
        $subPages = t3lib_BEfunc::getRecordsByField( 'pages', 'pid', $pid );
        
        // Check for subpages
        if( is_array( $subPages ) && count( $subPages ) ) {
            
            // Process each subpage
            foreach( $subPages as $subPage ) {
                
                // Map elements
                $this->mapElements( $subPage[ 'uid' ] );
                
                // Check if subpages must be processed as well
                if( $level < $recursive ) {
                    
                    // Process subpages
                    $this->mapElementsOnSubPages( $subPage[ 'uid' ], $recursive, $level );
                }
            }
        }
    }
    
    function mapElements( $pid )
    {
        global $LANG;
        
        // Storage
        $fields = array();
        
        // Get module POST data
        $modData = t3lib_div::_POST( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        // Process columns
        for( $i = 0; $i < 4; $i++ ) {
            
            // TemplaVoila field
            $tvField = $modData[ 'column_' . $i ];
            
            // Only process filled fields
            if( !empty( $tvField ) && $tvField != 'field_' ) {
                
                // Check if storage place exists
                if( !isset( $fields[ $tvField ][ 'vDEF' ] ) ) {
                    
                    // Create storage array for content elements
                    $fields[ $tvField ] = array(
                        'vDEF' => array()
                    );
                }
                
                // Get content elements
                $records = t3lib_BEfunc::getRecordsByField( $this->contentTable, 'colPos', $i, ' AND pid=' . $pid . ' AND sys_language_uid=0' );
                
                // Check for records
                if( $records ) {
                    
                    // Process records
                    foreach( $records as $record ) {
                        
                        // Add record ID
                        $fields[ $tvField ][ 'vDEF' ][] = $record[ 'uid' ];
                    }
                }
            }
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
        
        // Create update data
        $updateData = array(
            'tstamp'              => time(),
            'tx_templavoila_flex' => $xmlFlex
        );
        
        // Update page
        // TCE is not used because of the TemplaVoila hooks!!!
        $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( 'pages', 'uid=' . $pid, $updateData );
        
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
                    $value = implode( ',', $value );
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
        
        // Header
        $htmlCode[]     = $this->doc->spacer( 10 );
        $htmlCode[]     = $this->writeHTML( $LANG->getLL( 'list.elements' ), 'h2' );
        
        foreach( $this->columns as $key => $value ) {
            
            // Field header
            $htmlCode[] = $this->doc->spacer( 10 );
            $htmlCode[] = $this->writeHTML( $LANG->getLL( 'column.I.' . $value ), 'h3', false, array( 'border: dashed 1px #666666', 'padding: 10px', 'background-color: ' . $this->doc->bgColor ) );
            $htmlCode[] = $this->doc->divider( 5 );
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Get records for column
            $records = t3lib_BEfunc::getRecordsByField( $this->contentTable, 'colPos', $value, ' AND pid=' . $this->id . ' AND sys_language_uid=0');
            
            // Check for records
            if( $records ) {
                
                // Start table
                $htmlCode[]  = '<table border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
                $headerStyle = array( 'font-weight: bold' );
                
                // Table headers
                $htmlCode[] = '<tr>';
                $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.uid' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.type' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.title' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'list.content' ), 'td', false, $headerStyle );
                $htmlCode[] = $this->writeHTML( '', 'td', false, $headerStyle );
                $htmlCode[] = '</tr>';
                
                // Counter
                $counter = 0;
                
                foreach( $records as $record ) {
                    
                    // Storage
                    $row = array();
                        
                    // Record icon
                    $icon       = $this->api->be_getRecordCSMIcon( $this->contentTable, $record, $GLOBALS[ 'BACK_PATH' ] );
                    
                    // Record title
                    $title      = $record[ 'header' ];
                    
                    // Record content field (if available)
                    $content    = nl2br( $this->api->div_crop( $record[ 'bodytext' ], 500 ) );
                    
                    // Edit icons
                    $editIcons  = $this->api->be_buildRecordIcons( 'show,edit,delete', $this->contentTable, $record[ 'uid' ] );
                    
                    // Color
                    $color = ( $counter == 0 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                    
                    $row[] = $this->writeHTML( $icon, 'td' );
                    $row[] = $this->writeHTML( $record[ 'uid' ], 'td' );
                    $row[] = $this->writeHTML( $record[ 'CType' ], 'td' );
                    $row[] = $this->writeHTML( $this->writeHTML( $title, 'strong' ), 'td' );
                    $row[] = $this->writeHTML( $content, 'td' );
                    $row[] = $this->writeHTML( $editIcons, 'td' );
                    
                    // Add row
                    $htmlCode[] = '<tr bgcolor="' . $color . '">' . implode ( chr( 10 ), $row ) . '</tr>';
                    
                    $counter = ( $counter == 0 ) ? 1 : 0;
                }
                
                // End table
                $htmlCode[] = '</table>';

            } else {
                
                // Error - No content elements
                $htmlCode[] = $this->writeHTML( $LANG->getLL( 'error.noContent' ), 'div', 'typo3-red' );
            }
        }
        
        return implode( chr( 10 ), $htmlCode );
    }
}

// XCLASS inclusion
if( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/tvmigrator/mod1/index.php' ] ) {
    include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/tvmigrator/mod1/index.php' ] );
}

// Make instance
$SOBE = t3lib_div::makeInstance( 'tx_tvmigrator_module1' );
$SOBE->init();

// Include files
foreach( $SOBE->include_once as $INC_FILE ) {
    include_once( $INC_FILE );
}

// Output module
$SOBE->main();
$SOBE->printContent();
?>
