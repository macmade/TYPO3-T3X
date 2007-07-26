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
 * Module 'Ressource location' for the 'terminal' extension.
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
require_once( 'conf.php' );
require_once( $BACK_PATH . 'init.php' );
require_once( $BACK_PATH . 'template.php' );
$LANG->includeLLFile( 'EXT:terminal/mod1/locallang.xml' );
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
$BE_USER->modAccess( $MCONF, 1 );

require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class  tx_terminal_module1 extends t3lib_SCbase
{
    // Page informations
    var $pageinfo           = array();
    
    // Extension configuration
    var $extConf            = array();
    
    // Developer API
    var $api                = NULL;
    
    // Required developer API version
    var $apimacmade_version = 3.0;
    
    // Command prompt
    var $prompt             = '';
    
    // Current working directory
    var $cwd                = '';
    
    
    
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
        
        // Get extension configuration
        $this->extConf = unserialize( $TYPO3_CONF_VARS['EXT']['extConf']['terminal'] );
        
        // New instance of the developer API
        $this->api     = new tx_apimacmade( $this );
        
        // Terminal prompt
        $this->prompt  = t3lib_div::getIndpEnv( 'TYPO3_HOST_ONLY' )
                       . ': '
                       . $GLOBALS[ 'BE_USER' ]->user[ 'username' ]
                       . '$';
        
        // Session data
        $this->sessionData();
        
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
        
        // Access check
        $this->pageinfo = t3lib_BEfunc::readPageAccess( $this->id, $this->perms_clause );
        $access = is_array( $this->pageinfo ) ? 1 : 0;
        
        if( ( $this->id && $access ) || ( $BE_USER->user[ 'admin' ] && !$this->id ) ) {
            
            if( t3lib_div::_GET( 'ajaxCall' ) ) {
                
                $this->processCommand();
            }
            
            // Draw the header
            $this->doc              = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath    = $BACK_PATH;
            $this->doc->form        = '<form action="" method="POST" id="shellForm">';
            $this->doc->inDocStyles = $this->buildCssStyles();
            
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
                <script src="scripts.js" type="text/javascript" charset="utf-8"></script>
                <script type="text/javascript" language="Javascript" charset="iso-8859-1">
                    <!--
                    script_ended = 1;
                    if(top.fsMod) {
                        top.fsMod.recentIds["web"] = ' . intval( $this->id ) . ';
                    }
                    shell.setPrompt( "' . $this->prompt . '" );
                    shell.setAjaxUrl( "' . t3lib_div::linkThisScript() . '" );
                    //-->
                </script>
            ';
            
            // Include the prototype JS framework
            $this->api->be_includePrototypeJs();
            
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
            $this->content .= $this->doc->section( '', $headerSection );
            $this->content .= $this->doc->divider( 5 );
            
            // Render content
            $this->moduleContent();
            
            // Adds shortcut
            if( $BE_USER->mayMakeShortcut() ) {
                $this->content .= $this->doc->spacer( 20 )
                               .  $this->doc->section(
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
     * Prints the page.
     * 
     * This function closes the page, and writes the final
     * rendered content.
     * 
     * @return      Void
     */
    function printContent()
    {
        // Ends the page and writes module content
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
    function writeHtml( $text, $tag = 'div', $class = false, $style = false, $params = array() )
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
     * @return	NULL
     * @see     wsConnect
     * @see     writeHtml
     */
    function moduleContent()
    {
        global $LANG;
        
        // Storage
        $htmlCode       = array();
        
        // Spacer
        $htmlCode[]     = $this->doc->spacer( 10 );
        
        // Path to working directory
        $cwd = $this->writeHtml(
            $this->cwd,
            'span',
            false,
            false,
            array(
                'id' => 'cwdPath'
            )
        );
        
        // Working directory
        $htmlCode[]     = $this->writeHtml(
            $LANG->getLL( 'cwd' ) . ' ' . $cwd,
            'div',
            false,
            false,
            array(
                'id' => 'cwd'
            )
        );
        
        // Spacer
        $htmlCode[]     = $this->doc->spacer( 10 );
        
        // Terminal
        $htmlCode[]     = $this->buildTerminal();
        
        // Spacer
        $htmlCode[]     = $this->doc->spacer( 10 );
        
        // Shell history
        $htmlCode[]     = $LANG->getLL( 'history' ) . ' <select id="history" name="history" onChange="shell.exec( this.options[ this.selectedIndex ].value )"></select>';
        
        // Add content
        $this->content .= implode( chr( 10 ), $htmlCode );
    }
    
    function buildTerminal()
    {
        // Storage
        $htmlCode   = array();
        
        // Shell result
        $htmlCode[] = '<div id="result"></div>';
        
        // Spacer
        $htmlCode[]     = $this->doc->spacer( 5 );
        
        // Prompt
        $htmlCode[] = $this->writeHtml(
            $this->prompt,
            'span',
            'prompt'
        );
        
        // Shell command
        $htmlCode[] = '<input name="command" id="command" type="text" size="30" />';
        
        // Return terminal
        return $this->writeHtml(
            implode( chr( 10 ), $htmlCode ),
            'div',
            'shell'
        );
    }
    
    function buildCssStyles()
    {
        $css   = array();
        
        $css[] = '.shell {';
        $css[] = '  color: ' . $this->extConf[ 'foreground' ] . ';';
        $css[] = '  background-color: ' . $this->extConf[ 'background' ] . ';';
        $css[] = '  padding: 10px;';
        $css[] = '}';
        $css[] = '.shell, .shell * {';
        $css[] = '  font-family: monospace;';
        $css[] = '  font-size: ' . $this->extConf[ 'fontSize' ] . ';';
        $css[] = '}';
        $css[] = '#result {';
        $css[] = '  height: 400px;';
        $css[] = '  overflow: auto;';
        $css[] = '}';
        $css[] = '#result DIV {';
        $css[] = '  color: ' . $this->extConf[ 'foreground' ] . ';';
        $css[] = '  background-color: ' . $this->extConf[ 'background' ] . ';';
        $css[] = '}';
        $css[] = '#command {';
        $css[] = '  color: ' . $this->extConf[ 'foreground' ] . ';';
        $css[] = '  background-color: ' . $this->extConf[ 'background' ] . ';';
        $css[] = '  border: none;';
        $css[] = '  font-weight: bold;';
        $css[] = '}';
        $css[] = '.prompt {';
        $css[] = '  color: ' . $this->extConf[ 'prompt' ] . ';';
        $css[] = '  background-color: ' . $this->extConf[ 'background' ] . ';';
        $css[] = '}';
        $css[] = '.commandPrompt {';
        $css[] = '  text-decoration: blink;';
        $css[] = '}';
        $css[] = '.command {';
        $css[] = '  font-weight: bold;';
        $css[] = '}';
        $css[] = '.result {';
        $css[] = '  white-space: pre;';
        $css[] = '}';
        
        return implode( chr( 10 ), $css );
    }
    
    function processCommand()
    {
        global $BE_USER;
        
        $cmd = t3lib_div::_GET( 'command' );
        
        if( $cmd == '' ) {
            
            print $this->cwd . chr( 10 );
            exit();
        }
        
        $descriptorSpec = array(
            0 => array( 'pipe', 'r' ),
            1 => array( 'pipe', 'w' ),
            2 => array( 'pipe', 'r' )
        );
        
        $return = '';
        $error  = '';
        
        $commands = explode( ' && ', $cmd );
        
        foreach( $commands as $command ) {
            
            if( preg_match( '/^\s*cd\s*$/', $command ) ) {
                
                $this->cwd = PATH_site;
            }
            
            if( preg_match( '/\s*cd ([^\s]+)\s*/', $command, $matches ) ) {
                
                $dir = $matches[ 1 ];
                
                if( substr( $dir, 0, 1 ) == '/' ) {
                    
                    $this->cwd = $matches[ 1 ];
                    
                } else {
                    
                    $this->cwd = $this->cwd . $matches[ 1 ];
                    
                    if( substr( $this->cwd, strlen( $this->cwd ) - 1, 1 ) != '/' ) {
                        
                        $this->cwd .= '/';
                    }
                }
            }
            
            $this->cwd = preg_replace( '/\/\/+/', '/', $this->cwd );
            
            $BE_USER->pushModuleData(
                $GLOBALS[ 'MCONF' ][ 'name' ],
                array(
                    'cwd' => $this->cwd
                )
            );
            
            $process = proc_open(
                $command,
                $descriptorSpec,
                $pipes,
                $this->cwd,
                $_ENV
            );
            
            if( is_resource( $process ) ) {
                
                $stdin  = $pipes[0];
                $stdout = $pipes[1];
                $stderr = $pipes[2];
                
                while( !feof( $stdout ) ) {
                    
                    $return .= fgets( $stdout );
                }
                
                while( !feof( $stderr ) ) {
                    
                    $error .= fgets( $stderr );
                }
                
                fclose( $stdin );
                fclose( $stdout );
                fclose( $stderr );
                
                proc_close( $process );
                
                if( empty( $error ) ) {
                    
                    print $return;
                    
                } else {
                    
                    print $error;
                    exit();
                }
           }
        }
        
        exit();
    }
    
    function sessionData()
    {
        global $BE_USER;
        
        $data = $BE_USER->getModuleData( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        if( !isset( $data[ 'cwd' ] ) ) {
            
            $data[ 'cwd' ] = PATH_site;
        }
        
        $this->cwd = $data[ 'cwd' ];
        
        return true;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/terminal/mod1/index.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/terminal/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance( 'tx_terminal_module1' );
$SOBE->init();

// Include files
foreach( $SOBE->include_once as $INC_FILE ) {
    include_once( $INC_FILE );
}

// Launch module
$SOBE->main();
$SOBE->printContent();
