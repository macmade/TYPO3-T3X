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
 * Module 'TCA Tuner' for the 'tcatuner' extension.
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

// PHP5 only - This will avoid some E_STRICT errors
if( ( double )phpversion() > 5 ) {
    
    // Checks if the server timezone is defined
    if( !@ini_get( date.timezone ) ) {
        
        // Defines the timezone to avoid E_STRICT errors with the time/date functions
        date_default_timezone_set( 'Europe/Zurich' );
    }
}

// Unset any existing module configuration (if any)
unset( $GLOBALS[ 'MCONF' ] );

// Includes the module configuration
require( 'conf.php' );

// Includes the TYPO3 init script
require( $GLOBALS[ 'BACK_PATH' ] . 'init.php' );

// Includes the TYPO3 template class
require( $GLOBALS[ 'BACK_PATH' ] . 'template.php' );

// Includes the module's locallang file
$GLOBALS[ 'LANG' ]->includeLLFile( 'EXT:tcatuner/mod1/locallang.php' );

// Includes the TYPO3 module base class
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );

// Includes the TYPO3 Core Engine class
require_once( PATH_t3lib . 'class.t3lib_tcemain.php' );

// Checks the access to the module
$GLOBALS[ 'BE_USER' ]->modAccess( $MCONF, 1 );

// Includes the Developer API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

// DEBUG ONLY - Sets the error level to the highest possible value (PHP5)
// Unfortunately, TYPO3 won't work with those settings, but at least I'll try to make sure this module won't generate any error, even E_STRICT ones
error_reporting( E_ALL | E_STRICT );

class tx_tcatuner_module1 extends t3lib_SCbase
{
    // Reference to the BE user object
    var $beUser             = NULL;
    
    // Reference to the lang object
    var $lang               = NULL;
    
    // Document object
    var $doc                = NULL;
    
    // t3lib_div object
    var $div                = NULL;
    
    // t3lib_BEfunc object
    var $beFunc             = NULL;
    
    // t3lib_iconWorks object
    var $iconWorks          = NULL;
    
    // Current page row
    var $pageinfo           = array();
    
    // Reference to the TCA description array
    var $tcaDescr           = array();
    
    // Reference to the TCA array
    var $tca                = array();
    
    // Reference to the TCA array
    var $client             = array();
    
    // Reference to the TCA array
    var $typo3ConfVars      = array();
    
    // Tables informations
    var $tables             = array();
    
    // Page TSConfig
    var $pageTsConfig       = array();
    
    // Module icons
    var $icons              = array();
    
    // Back path
    var $backPath           = '';
    
    // New line character
    var $NL                 = '';
    
    // Developer API version
    var $apimacmade_version = 4.5;
    
    
    
    
    
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
        // Sets the new line character
        $this->NL = chr( 10 );
        
        // Gets a new instance of the Developer API
        // This will generate en E_STRICT error under PHP5... Sorry about this...
        $this->api = tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                &$this
            )
        );
        
        // Stores references to some global objects and arrays
        $this->beUser        =& $GLOBALS[ 'BE_USER' ];
        $this->lang          =& $GLOBALS[ 'LANG' ];
        $this->tcaDescr      =& $GLOBALS[ 'TCA_DESCR' ];
        $this->tca           =& $GLOBALS[ 'TCA' ];
        $this->client        =& $GLOBALS[ 'CLIENT' ];
        $this->typo3ConfVars =& $GLOBALS[ 'TYPO3_CONF_VARS' ];
        
        // Creates an instance of the t3lib_div class
        // This is not useful as t3lib_div is a static class, but in PHP5, this will avoid some E_STRICT errors
        $this->div = $this->api->newInstance( 't3lib_div' );
        
        // Creates an instance of the t3lib_BEfunc class
        // This is not useful as t3lib_div is a static class, but in PHP5, this will avoid some E_STRICT errors
        $this->beFunc = $this->api->newInstance( 't3lib_BEfunc' );
        
        // Creates an instance of the t3lib_BEfunc class
        // This is not useful as t3lib_div is a static class, but in PHP5, this will avoid some E_STRICT errors
        $this->iconWorks = $this->api->newInstance( 't3lib_iconWorks' );
        
        // Stores the back path
        $this->backPath      =  $GLOBALS[ 'BACK_PATH' ];
        
        // Get informations about the TYPO3 tables
        $this->getTables();
        
        // Get the icons used by the module
        $this->getIcons();
        
        // Init
        parent::init();
        
        return true;
    }
    
    /**
     * Creates the page.
     * 
     * This function creates the basic page in which the module will
     * take place.
     * 
     * @return      null
     */
    function main()
    {
        // Gets the page informations
        $this->pageinfo = $this->beFunc->readPageAccess( $this->id, $this->perms_clause );
        $access         = is_array( $this->pageinfo ) ? true : false;
        
        // Checks the access
        if( ( $this->id && $access ) || ( $this->beUser->user[ 'admin' ] && !$this->id ) ) {
            
            // Creates an instance of the document class
            $this->doc           = $this->api->newInstance( 'bigDoc' );
            
            // Sets the back path
            $this->doc->backPath = $this->backPath;
            
            // Adds the form tag
            $this->doc->form     = '<form action="" method="POST">';
            
            // JavaScript code
            $this->doc->JScode = '
                <script type="text/javascript" language="Javascript" charset="iso-8859-1">
                    <!--
                    script_ended = 0;
                    function jumpToUrl(URL) {
                        document.location = URL;
                    }
                    //-->
                </script>
                <script src="scripts.js" type="text/javascript" language="Javascript" charset="iso-8859-1"></script>
            ';
            
            // JavaScript post code
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
            
            // Builds the current path
            $headerSection = $this->doc->getHeader(
                'pages',
                $this->pageinfo,
                $this->pageinfo[ '_thePath' ]
            )
            . '<br>'
            . $this->lang->sL( 'LLL:EXT:lang/locallang_core.php:labels.path' )
            . ': '
            . $this->div->fixed_lgd_pre( $this->pageinfo[ '_thePath' ], 50 );
            
            // Starts the page content
            $this->content .= $this->doc->startPage( $this->lang->getLL( 'title' ) );
            
            // Adds the module title
            $this->content .= $this->doc->header( $this->lang->getLL( 'title' ) );
            
            // Spacer
            $this->content .= $this->doc->spacer( 5 );
            
            // Module's menu
            $this->content .= $this->doc->section(
                '',
                $this->doc->funcMenu(
                    $headerSection,
                    $this->beFunc->getFuncMenu(
                        $this->id,
                        'SET[function]',
                        $this->MOD_SETTINGS[ 'function' ],
                        $this->MOD_MENU[ 'function' ]
                    )
                )
            );
            
            // Divider
            $this->content .= $this->doc->divider( 5 );
            
            // Renders the module's content
            $this->moduleContent();
            
            // Adds the shortcut link
            if( $this->beUser->mayMakeShortcut() ) {
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
            
            // Spacer
            $this->content .= $this->doc->spacer(10);
            
            // Ends the page
            $this->content .= $this->doc->endPage();
            
        } else {
            
            // No access
            $this->doc           = $this->api->newInstance( 'bigDoc' );
            $this->doc->backPath = $this->backPath;
            $this->content      .= $this->doc->startPage( $this->lang->getLL( 'title' ) );
            $this->content      .= $this->doc->header( $this->lang->getLL ( 'title' ) );
            $this->content      .= $this->doc->spacer( 5 );
            $this->content      .= $this->lang->getLL( 'noaccess' );
            $this->content      .= $this->doc->spacer( 10 );
            $this->content      .= $this->doc->endPage();
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
        // Menu items
        $this->MOD_MENU = array (
            'function' => array (
                '1' => $this->lang->getLL( 'menu.func.1' ),
            )
        );
        
        // Process each TYPO3 table
        foreach( $this->tables as $key => $value ) {
            
            // Add the table to the menu
            $this->MOD_MENU[ 'function' ][ $key ] = $value[ 'title' ]
                                                  . ' ('
                                                  . $key
                                                  . ')';
        }
        
        // Creates the menu
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
    function getContent()
    {
        // Returns the content
        return $this->content;
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
        // Initialization of some internal variables
        $styleTag  = '';
        $classTag  = '';
        $paramsTag = '';
        
        // Checks for a style tag
        if ( is_array( $style ) ) {
            
            // Creates the class tag
            $styleTag = ' style="' . implode( '; ', $style ) . '"';
        }
        
        // Checks for a class tag
        if ( $class ) {
            
            // Creates the class tag
            $classTag = ' class="' . $class . '"';
        }
        
        // Checks for tag parameters
        if( count( $params ) ) {
            
            // Creates the tag parameters
            $paramsTag = ' ' . $this->api->div_writeTagParams( $params );
        }
        
        // Returns the HTML code
        return '<' . $tag . $classTag . $styleTag . $paramsTag . '>' . $text . '</' . $tag . '>';
    }
    
    /**
     * Creates the module's content.
     * 
     * This function creates the module's content.
     * 
     * @return		Void
     */
    function moduleContent()
    {
        // Checks for a page ID
        if( !$this->id ) {
            
            // No page selected
            $this->content .= $this->writeHtml(
                $this->writeHtml(
                    $this->lang->getLL( 'noPid' ),
                    'strong'
                )
            );
            return true;
        }
        
        // Checks for a table view
        if( isset( $this->MOD_SETTINGS[ 'function' ] )
            && isset( $this->tables[ $this->MOD_SETTINGS[ 'function' ] ] )
        ) {
            
            // Display the table
            $this->content .= $this->editTable( $this->MOD_SETTINGS[ 'function' ] );
            return true;
            
        } else {
            
            // Display the table list
            $this->content .= $this->showTables();
            return true;
        }
    }
    
    /**
     * Gets available table
     * 
     * This function is used to store the available table descriptions
     * in a class array ($this->tables). It memorize the table name as
     * the array key, and some of the TCA properties in a subarray.
     * 
     * @return		Void
     */
    function getTables()
    {
        // Traverse the TCA array
        foreach( $this->tca as $key => $value ) {
            
            // Load TCA for current table
            $this->div->loadTCA( $key );
            
            // Memorize table informations
            $this->tables[ $key ] = array(
                'title'  => $this->lang->sL( $this->tca[ $key ][ 'ctrl' ][ 'title' ] ),
                'fields' => count( $this->tca[ $key ][ 'columns' ] )
            );
        }
        
        // Sort tables array
        asort( $this->tables );
        
        // DEBUG ONLY - Show tables
        #$this->api->debug( $this->tables, 'TABLES' );
    }
    
    /**
     * 
     */
    function getIcons()
    {
        // Icons from the module
        $this->icons[ 'input' ]  = '<img src="gfx/input.gif" width="24" height="24" alt="" />';
        $this->icons[ 'text' ]   = '<img src="gfx/text.gif" width="24" height="24" alt="" />';
        $this->icons[ 'check' ]  = '<img src="gfx/check.gif" width="24" height="24" alt="" />';
        $this->icons[ 'select' ] = '<img src="gfx/select.gif" width="24" height="24" alt="" />';
        $this->icons[ 'group' ]  = '<img src="gfx/group.gif" width="24" height="24" alt="" />';
        $this->icons[ 'inline' ] = '<img src="gfx/inline.gif" width="24" height="24" alt="" />';
        
        // Icons from TYPO3
        $this->icons[ 'info' ]   = '<img '
                                 . $this->iconWorks->skinImg( $this->backPath, 'gfx/zoom2.gif' )
                                 . ' />';
        $this->icons[ 'edit' ]   = '<img '
                                 . $this->iconWorks->skinImg( $this->backPath, 'gfx/edit2.gif' )
                                 . ' />';
    }
    
    /**
     * 
     */
    function showTables()
    {
        // Table to edit, if clicked
        $editTable = $this->div->_GET( 'editTable' );
        
        // Checks for a table edit view
        if( $editTable && isset( $this->tables[ $editTable ] ) ) {
            
            // Display the table edit view
            return $this->editTable( $editTable );
        }
        
        // Counters
        $colorCount = 0;
        $rowCount   = 0;
        
        // Storage
        $htmlCode = array();
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 5 );
        
        // Header
        $htmlCode[] = $this->doc->sectionHeader( $this->lang->getLL( 'headers.tables' ) );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 5 );
        
        // Starts the table
        $htmlCode[] = '<table id="tableList" border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="'
                    . $this->doc->bgColor2
                    . '">';
        
        // Table headers
        $htmlCode[] = '<tr>';
        $htmlCode[] = '<td align="left" valign="middle"></td>';
        $htmlCode[] = '<td align="left" valign="middle"><strong>'
                    . $this->lang->getLL( 'headers.tables.title' )
                    . '</strong></td>';
        $htmlCode[] = '<td align="left" valign="middle"><strong>'
                    . $this->lang->getLL( 'headers.tables.name' )
                    . '</strong></td>';
        $htmlCode[] = '<td align="left" valign="middle"><strong>'
                    . $this->lang->getLL( 'headers.tables.fields' )
                    . '</strong></td>';
        $htmlCode[] = '<td align="left" valign="middle"></td>';
        $htmlCode[] = '</tr>';
        
        // Process each table
        foreach( $this->tables as $key => $value ) {
            
            // Changes the row color
            $colorCount = ($colorCount == 1) ? 0: 1;
            $color      = ($colorCount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor3;
            
            // Gets the table icon
            $iconTable  = $this->iconWorks->getIconImage(
                $key,
                array(),
                $this->backPath
            );
            
            // Edit view link
            $editLink   = $this->div->linkThisScript( array( 'editTable' => $key ) );
            
            // Row parameters
            $trParams = ' onmouseover="setRowColor(this,\''
                      . $rowCount
                      . '\',\'over\',\''
                      . $this->doc->bgColor5
                      . '\');" onmouseout="setRowColor(this,\''
                      . $rowCount
                      . '\',\'out\',\''
                      . $color
                      . '\');" onclick="setRowColor(this,\''
                      . $rowCount
                      . '\',\'click\',\''
                      . $this->doc->bgColor5
                      . '\',\''
                      . $color
                      . '\');" bgcolor="'
                      . $color
                      . '"';
            
            // Starts the row
            $htmlCode[] = $this->doc->sectionBegin();
            $htmlCode[] = '<tr' . $trParams . '">';
            
            // Table icon
            $htmlCode[] = '<td align="left" valign="middle">'
                        . $iconTable
                        . '</td>';
            
            // Table title
            $htmlCode[] = '<td align="left" valign="middle"><a href="'
                        . $editLink
                        . '" title="'
                        . $key
                        . '"><strong>'
                        . $value[ 'title' ]
                        . '</strong></a></td>';
            
            // Table name
            $htmlCode[] = '<td align="left" valign="middle">'
                        . $key
                        . '</td>';
            
            // Number of fields
            $htmlCode[] = '<td align="left" valign="middle">'
                        . $value[ 'fields' ]
                        . '</td>';
            
            // Edit icon
            $htmlCode[] = '<td align="left" valign="middle"><a href="'
                        . $editLink
                        . '" title="'
                        . $key
                        . '">'
                        . $this->icons[ 'edit' ]
                        . '</a></td>';
            
            // Ends the row
            $htmlCode[] = '</tr>';
            $htmlCode[] = $this->doc->sectionEnd();
            
            // Increases the row counter
            $rowCount++;
        }
        
        // Ends the table
        $htmlCode[] = '</table>';
        
        // Return the list of tables
        return implode( $this->NL, $htmlCode );
    }
    
    /**
     * 
     */
    function editTable( $tableName )
    {
        // Checks for a save action
        if( $this->div->_GP( 'saveTca' ) ) {
            
            // Save the changes
            $saveStatus = $this->writeHtml( $this->saveTca( $tableName ) );
        }
        
        // Gets the page TS config
        $this->pageTsConfig = $this->beFunc->getPagesTSconfig( $this->id );
        
        // Counters
        $colorCount = 0;
        $rowCount   = 0;
        
        // Table icon
        $iconTable  = $this->iconWorks->getIconImage(
            $tableName,
            array(),
            $this->backPath
        );
        
        // Storage
        $htmlCode = array();
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 5 );
        
        // Header
        $htmlCode[] = $this->doc->sectionHeader(
            $iconTable
          . ' '
          . $this->tables[ $tableName ][ 'title' ]
          . ' ('
          . $tableName
          . ')'
        );
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Checks for a save status
        if( isset( $saveStatus ) ) {
            
            // Adds the save status
            $htmlCode[] = $saveStatus;
            $htmlCode[] = $this->doc->spacer( 10 );
        }
        
        // Submit
        $htmlCode[] = $this->writeHtml(
            '<input name="saveTca" type="submit" value="'
          . $this->lang->getLL( 'submit' )
          . '" />'
        );
        
        // Checks if a field must be shown
        if( $showField = $this->div->_GP( 'showField' ) ) {
            
            // Shows the field details
            $htmlCode[] = $this->writeHtml( $this->showField( $tableName, $showField ) );
        }
        
        // Spacer
        $htmlCode[] = $this->doc->spacer( 20 );
        
        // Starts the table
        $htmlCode[] = '<table id="fieldList" border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="'
                    . $this->doc->bgColor2
                    . '">';
        
        // Table headers
        $htmlCode[] = '<tr>';
        $htmlCode[] = '<td align="left" valign="middle"></td>';
        $htmlCode[] = '<td align="left" valign="middle"><strong>'
                    . $this->lang->getLL( 'headers.fields.title' )
                    . '</strong></td>';
        $htmlCode[] = '<td align="left" valign="middle"><strong>'
                    . $this->lang->getLL( 'headers.fields.name' )
                    . '</strong></td>';
        $htmlCode[] = '<td align="left" valign="middle"><strong>'
                    . $this->lang->getLL( 'headers.fields.type' )
                    . '</strong></td>';
        $htmlCode[] = '<td align="left" valign="middle"><strong>'
                    . $this->lang->getLL( 'headers.fields.options' )
                    . '</strong></td>';
        $htmlCode[] = '<td align="left" valign="middle"></td>';
        $htmlCode[] = '</tr>';
        
        // Process each field
        foreach( $this->tca[ $tableName ][ 'columns' ] as $key => $value ) {
            
            // Checks if the field type is editable
            if( $value[ 'config' ][ 'type' ] == 'input'
                || $value[ 'config' ][ 'type' ] == 'text'
                || $value[ 'config' ][ 'type' ] == 'check'
                || $value[ 'config' ][ 'type' ] == 'select'
                || $value[ 'config' ][ 'type' ] == 'group'
                || $value[ 'config' ][ 'type' ] == 'inline'
            ) {
                
                // Changes the row color
                $colorCount = ($colorCount == 1) ? 0: 1;
                $color      = ($colorCount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor3;
                
                // Row parameters
                $trParams = ' onmouseover="setRowColor(this,\''
                          . $rowCount
                          . '\',\'over\',\''
                          . $this->doc->bgColor5
                          . '\');" onmouseout="setRowColor(this,\''
                          . $rowCount . '\',\'out\',\''
                          . $color
                          . '\');" onclick="setRowColor(this,\''
                          . $rowCount
                          . '\',\'click\',\''
                          . $this->doc->bgColor5
                          . '\',\''
                          . $color
                          . '\');" bgcolor="'
                          . $color
                          . '"';
                
                // Info link
                $infoLink = $this->div->linkThisScript( array( 'showField' => $key ) );
                
                // Starts the row
                $htmlCode[] = $this->doc->sectionBegin();
                $htmlCode[] = '<tr' . $trParams . '">';
                
                // Field icon
                $htmlCode[] = '<td align="left" valign="middle">'
                            . $this->icons[ $value[ 'config' ][ 'type' ] ]
                            . '</td>';
                
                // Field label
                $htmlCode[] = '<td align="left" valign="middle"><strong>'
                            . $this->lang->sL( $value[ 'label' ] )
                            . '</strong></td>';
                
                // Field name
                $htmlCode[] = '<td align="left" valign="middle">'
                            . $key
                            . '</td>';
                
                // Field type
                $htmlCode[] = '<td align="left" valign="middle">'
                            . $value[ 'config' ][ 'type' ]
                            . '</td>';
                
                // Editable field options
                $htmlCode[] = '<td align="left" valign="middle">'
                            . $this->fieldOptions( $tableName, $key, $value[ 'config' ][ 'type' ] )
                            . '</td>';
                
                // Info icon
                $htmlCode[] = '<td align="left" valign="middle"><a href="'
                            . $infoLink
                            . '" title="'
                            . $key
                            . '">'
                            . $this->icons[ 'info' ]
                            . '</a></td>';
                
                // Ends the row
                $htmlCode[] = '</tr>';
                $htmlCode[] = $this->doc->sectionEnd();
                
                // Increases the row counter
                $rowCount++;
            }
        }
        
        // Ends the table
        $htmlCode[] = '</table>';
        
        // Returns the field list
        return implode( $this->NL, $htmlCode );
    }
    
    /**
     * 
     */
    function fieldOptions( $tableName, $fieldName, $fieldType )
    {
        // Checks the field type
        switch( $fieldType ) {
            
            // Input
            case 'input':
                
                // Sets the editable options
                $options = array(
                    'size',
                    'max'
                );
                break;
            
            // Textarea
            case 'text':
                
                // Sets the editable options
                $options = array(
                    'cols',
                    'rows',
                    'wrap'
                );
                break;
            
            // Checkbox
            case 'check':
                
                // Sets the editable options
                $options = array(
                    'cols',
                    'showIfRTE'
                );
                break;
            
            // Select menu
            case 'select':
                
                // Sets the editable options
                $options = array(
                    'size',
                    'autoSizeMax',
                    'maxitems',
                    'minitems'
                );
                break;
            
            // Group field
            case 'group':
                
                // Sets the editable options
                $options = array(
                    'size',
                    'autoSizeMax',
                    'max_size',
                    'show_thumbs',
                    'maxitems',
                    'minitems'
                );
                break;
            
            // Inline relation
            case 'inline':
                
                // Sets the editable options
                $options = array(
                    'appearance',
                    'foreign_label',
                    'foreign_selector',
                    'foreign_unique',
                    'maxitems',
                    'minitems',
                    'size',
                    'autoSizeMax',
                    'symmetric_label'
                );
                break;
            
            default:
                
                return '';
        }
        
        // Counter for the row colors
        $colorCount = 0;
        
        // Storage
        $htmlCode = array();
        
        // Starts the table
        $htmlCode[] = '<table style="margin: 5px;" border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="'
                    . $this->doc->bgColor2
                    . '">';
        
        // Process each editable option
        foreach( $options as $fieldOption ) {
            
            // Changes the row color
            $colorCount = ($colorCount == 1) ? 0: 1;
            $color      = ($colorCount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor3;
            
            // Row parameters
            $trParams = ' bgcolor="' . $color . '"';
            
            // ID of the input
            $inputId    = 'TCA_'
                        . $tableName
                        . '_'
                        . $fieldName
                        . '_'
                        . $fieldOption;
            
            // Name of the input
            $inputName  = 'TCA['
                        . $tableName
                        . ']['
                        . $fieldName
                        . ']['
                        . $fieldOption
                        . '][NEW]';
            
            // Name of the hidden input (for the orginal value)
            $hiddenName = 'TCA['
                        . $tableName
                        . ']['
                        . $fieldName
                        . ']['
                        . $fieldOption
                        . '][ORIG]';
            
            // Label
            $label      = '<label for="'
                        . $inputId
                        . '">'
                        . $fieldOption
                        . ':</label>';
            
            // Current value
            $value      = $this->getConfigValue( $tableName, $fieldName, $fieldOption );
            
            // Input tag
            $input      = '<input type="text" id="'
                        . $inputId
                        . '" name="'
                        . $inputName
                        . '" value="'
                        . $value
                        . '" size="10" />';
            
            // Hidden input
            $hidden     = '<input type="hidden" name="'
                        . $hiddenName
                        . '" value="'
                        . $value
                        . '" />';
            
            // Starts the row
            $htmlCode[] = '<tr>';
            
            // Label
            $htmlCode[] = '<td bgcolor="' . $this->doc->bgColor4 . '" width="50%" align="left" valign="middle"><strong>'
                        . $label
                        . '</strong></td>';
            
            // Fields
            $htmlCode[] = '<td bgcolor="' . $this->doc->bgColor3 . '" width="50%" align="left" valign="middle">'
                        . $input
                        . $hidden
                        .'</td>';
            
            // Ends the row
            $htmlCode[] = '</tr>';
        }
        
        // Ends the table
        $htmlCode[] = '</table>';
        
        // Returns the editable options
        return implode( $this->NL, $htmlCode );
    }
    
    /**
     * 
     */
    function getConfigValue( $tableName, $fieldName, $option )
    {
        // Checks for a default value
        if( isset( $this->pageTsConfig[ 'TCEFORM.' ][ $tableName . '.' ][ $fieldName . '.' ][ 'config.' ][ $option ] ) ) {
            
            // Sets the value from the TS config
            $value = $this->pageTsConfig[ 'TCEFORM.' ][ $tableName . '.' ][ $fieldName . '.' ][ 'config.' ][ $option ];
        
        } elseif( isset( $this->tca[ $tableName ][ 'columns' ][ $fieldName ][ 'config' ][ $option ] ) ) {
            
            // Sets the value from the TCA
            $value = $this->tca[ $tableName ][ 'columns' ][ $fieldName ][ 'config' ][ $option ];
            
        } else {
            
            // No default value
            $value = '';
        }
        
        // Returns the actual value
        return $value;
    }
    
    /**
     * 
     */
    function saveTca( $tableName )
    {
        // Incoming TCA data
        $data = $this->div->_GP( 'TCA' );
        
        // Checks for the table name
        if( !isset( $data[ $tableName ] ) ) {
            
            // Invalid table name
            return $this->writeHtml( $this->lang->getLL( 'submit.error' ), 'strong', 'typo3-red' );
        }
        
        // Storage
        $ts = array();
        
        // Flag to track the changes
        $changes = false;
        
        // Process each field
        foreach( $data[ $tableName ] as $fieldName => $options ) {
            
            // Process each option
            foreach( $options as $optionName => $optionValues ) {
                
                // Checks for a change in the configuration
                if( $optionValues[ 'NEW' ] != $optionValues[ 'ORIG' ] ) {
                    
                    // Sets the change flag
                    $changes = true;
                    
                    // Adds the TS config code
                    $ts[ $fieldName . '.config.' . $optionName ] = $optionValues[ 'NEW' ];
                }
            }
        }
        
        // Checks for a change in the configuration
        if( $changes ) {
            
            // Writes the TS config code in the current page
            $this->beFunc->updatePagesTSconfig(
                $this->id,
                $ts,
                'TCEFORM.' . $tableName . '.'
            );
            
            // Success
            return $this->writeHtml( $this->lang->getLL( 'submit.success' ), 'strong' );
        }
        
        // No changes were made
        return $this->writeHtml( $this->lang->getLL( 'submit.nochange' ), 'strong' );
    }
    
    /**
     * 
     */
    function showField( $tableName, $fieldName )
    {
        // Storage
        $htmlCode = array();
        
        // Checks for a valid field
        if( isset( $this->tca[ $tableName ][ 'columns' ][ $fieldName ] )
            && is_array( $this->tca[ $tableName ][ 'columns' ][ $fieldName ] )
        ) {
            
            // Starts the section
            $htmlCode[] = $this->doc->sectionBegin();
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Header
            $htmlCode[] = $this->doc->sectionHeader(
                $this->icons[ 'info' ] . ' ' . sprintf( $this->lang->getLL( 'headers.fields.infos' ), $tableName . '.' . $fieldName )
            );
            
            // Spacer
            $htmlCode[] = $this->doc->spacer( 10 );
            
            // Shows the field
            $htmlCode[] = $this->printFieldLevel( $this->tca[ $tableName ][ 'columns' ][ $fieldName ] );
            
            // Ends the section
            $htmlCode[] = $this->doc->sectionEnd();
        }
        
        // Returns the field details
        return implode( $this->NL, $htmlCode );
    }
    
    /**
     * 
     */
    function printFieldLevel( $array )
    {
        // Storage
        $htmlCode = array();
        
        // Checks for values
        if( count( $array ) ) {
            
            // Starts the table
            $htmlCode[] = '<table id="fieldDetails" border="0" width="100%" cellspacing="1" cellpadding="5" align="center" bgcolor="'
                        . $this->doc->bgColor2
                        . '">';
            
            // Process the array
            foreach( $array as $key => $value ) {
                
                // Starts the row
                $htmlCode[] = '<tr>';
                
                // Label
                $htmlCode[] = '<td bgcolor="'
                            . $this->doc->bgColor4
                            . '" align="left" valign="middle"><strong>'
                            . $key
                            . '</strong></td>';
                
                // Checks the value
                if( is_array( $value ) ) {
                    
                    // Shows the sub array
                    $htmlCode[] = '<td bgcolor="'
                                . $this->doc->bgColor3
                                . '" align="left" valign="middle">'
                                . $this->printFieldLevel( $value )
                                . '</td>';
                    
                } else {
                    
                    // Shows the value
                    $htmlCode[] = '<td bgcolor="'
                                . $this->doc->bgColor3
                                . '" align="left" valign="middle">'
                                . htmlspecialchars( $value )
                                . '</td>';
                }
                
                // Ends the row
                $htmlCode[] = '</tr>';
            }
            
            // Ends the table
            $htmlCode[] = '</table>';
        }
        
        // Returns the field details
        return implode( $this->NL, $htmlCode );
    }
}

// Checks for an XClass (necessary to avoid an E_NOTICE error)
if( defined( 'TYPO3_MODE' ) && isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ TYPO3_MODE ][ 'XCLASS' ][ 'ext/tcatuner/mod1/index.php' ] ) ) {
    
    // XClass inclusion
    if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tcatuner/mod1/index.php'])	{
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tcatuner/mod1/index.php']);
    }
}

// Creates an instance of the module
// This will generate en E_STRICT error under PHP5... Sorry about this...
$GLOBALS[ 'SOBE' ] = tx_apimacmade::newInstance( 'tx_tcatuner_module1' );

// Initializes the module
$GLOBALS[ 'SOBE' ]->init();

// Checks for include files
if( is_array( $GLOBALS[ 'SOBE' ]->include_once ) ) {
    
    // Process includes
    foreach( $GLOBALS[ 'SOBE' ]->include_once as $includeFile ) {
        
        // Checks the file
        if( file_exists( $includeFile ) ) {
            
            // Includes the file
            include_once( $includeFile );
            
        } else {
            
            // Error message
            trigger_error( 'File "' . $includeFile . '" failed to be included. Aborting current script', E_USER_ERROR );
            
            // Aborts the script
            exit();
        }
    }
}

// Creates the module's view
$GLOBALS[ 'SOBE' ]->main();

// Prints the module's content
print $GLOBALS[ 'SOBE' ]->getContent();

// Cleanup
unset( $GLOBALS[ 'MCONF' ] );
unset( $GLOBALS[ 'SOBE' ] );

