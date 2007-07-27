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
 * Module 'Terminal' for the 'terminal' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - INIT
 *     166:     function init
 *     200:     function main
 * 
 * SECTION:     2 - MAIN
 *     332:     function printContent
 *     348:     function writeHtml( $text, $tag = 'div', $class = false, $style = false, $params = array() )
 *     379:     function moduleContent
 *     442:     function shellHistory
 *     482:     function buildShortcuts
 *     536:     function buildTerminal
 *     570:     function buildCssStyles
 *     655:     function processCommand
 *     828:     function sessionData
 * 
 *              TOTAL FUNCTIONS: 11
 */

// Default initialization of the module
unset( $MCONF );
require_once( 'conf.php' );
require_once( $BACK_PATH . 'init.php' );
require_once( $BACK_PATH . 'template.php' );
$LANG->includeLLFile( 'EXT:terminal/mod1/locallang.xml' );
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
$BE_USER->modAccess( $MCONF, 1 );

// Include the Developer API class
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
    
    // Shell commands
    var $commands           = array();
    
    // Function proc_open is available
    var $procOpen           = false;
    
    // Shortcuts
    var $shortcuts          = array(
        'processes' => array(
            'icon'    => 'top.png',
            'command' => 'top -l1'
        ),
        'diskUse' => array(
            'icon'    => 'df.png',
            'command' => 'df -h'
        ),
        'networking' => array(
            'icon'    => 'ifconfig.png',
            'command' => 'ifconfig'
        ),
        'pathInfo' => array(
            'icon'    => 'pwd.png',
            'command' => 'pwd'
        ),
        'userName' => array(
            'icon'    => 'whoami.png',
            'command' => 'whoami'
        ),
        'date' => array(
            'icon'    => 'date.png',
            'command' => 'date'
        ),
        'listing' => array(
            'icon'    => 'ls.png',
            'command' => 'ls -alh'
        ),
        'home' => array(
            'icon'    => 'site.png',
            'command' => 'cd && ls -alh'
        ),
        'fileadmin' => array(
            'icon'    => 'fileadmin.png',
            'command' => 'cd && cd fileadmin && ls -alh'
        ),
        'typo3conf' => array(
            'icon'    => 'typo3conf.png',
            'command' => 'cd && cd typo3conf && ls -alh'
        ),
        'uploads' => array(
            'icon'    => 'uploads.png',
            'command' => 'cd && cd uploads && ls -alh'
        ),
        't3lib' => array(
            'icon'    => 't3lib.png',
            'command' => 'cd && cd t3lib && ls -alh'
        ),
        'typo3' => array(
            'icon'    => 'typo3.png',
            'command' => 'cd && cd typo3 && ls -alh'
        ),
        'typo3temp' => array(
            'icon'    => 'typo3temp.png',
            'command' => 'cd && cd typo3temp && ls -alh'
        ),
    );
    
    
    
    
    
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
        $this->extConf  = unserialize( $TYPO3_CONF_VARS['EXT']['extConf']['terminal'] );
        
        // New instance of the developer API
        $this->api      = new tx_apimacmade( $this );
        
        // Detect proc_open
        $this->procOpen = function_exists( 'proc_open' );
        
        // Terminal prompt
        $this->prompt   = t3lib_div::getIndpEnv( 'TYPO3_HOST_ONLY' )
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
            
            // Checks for an Ajax call
            if( t3lib_div::_GET( 'ajaxCall' ) && $this->procOpen ) {
                
                // Process the command from ajax
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
            
            // proc_open has been detected
            if( $this->procOpen ) {
                
                // Render content
                $this->moduleContent();
                
            } else {
                
                $this->content .= $this->doc->spacer( 10 );
                $this->content .= $this->writeHtml(
                    $LANG->getLL( 'noProcOpen' ),
                    'div',
                    'typo3-red'
                );
            }
            
            // Adds shortcut
            #if( $BE_USER->mayMakeShortcut() ) {
            #    $this->content .= $this->doc->spacer( 20 )
            #                   .  $this->doc->section(
            #                        '',
            #                        $this->doc->makeShortcutIcon(
            #                            'id',
            #                            implode( ',', array_keys( $this->MOD_MENU ) ),
            #                            $this->MCONF[ 'name' ]
            #                        )
            #                      );
            #}
            
            // Spacer
            $this->content .= $this->doc->spacer( 10 );
            
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
        $htmlCode[]     = $this->shellHistory();
        
        // Spacer
        $htmlCode[]     = $this->doc->spacer( 10 );
        
        // Checks for the shortcuts
        if( $this->extConf[ 'shortcuts' ] ) {
            
            // Add shortcuts
            $htmlCode[]     = $this->buildShortcuts();
        }
        
        // Add content
        $this->content .= implode( chr( 10 ), $htmlCode );
    }
    
    /**
     * Builds the history section
     * 
     * @return  string  The history section
     */
    function shellHistory()
    {
        global $LANG;
        
        // Storage
        $htmlCode   = array();
        
        // Title
        $htmlCode[] = $LANG->getLL( 'history' );
        
        // Start select
        $htmlCode[] = ' <select id="history" name="history" onChange="shell.exec( this.options[ this.selectedIndex ].value )">';
        
        // Process each stored command
        foreach( $this->commands as $command ) {
            
            // Add option
            $htmlCode[] = '<option value="'
                        . $command
                        . '">'
                        . $command
                        . '</option>';
        }
        
        // End select
        $htmlCode[] = '</select>';
        
        // Return history section
        return $this->writeHtml(
            implode( chr( 10 ), $htmlCode ),
            'div',
            'history'
        );
    }
    
    /**
     * Builds the shortcuts section
     * 
     * @return  string  The shortcuts section
     */
    function buildShortcuts()
    {
        global $LANG;
        
        // Storage
        $htmlCode   = array();
        
        // Title
        $htmlCode[] = $this->writeHtml( $LANG->getLL( 'shortcuts' ) );
        $htmlCode[] = $this->doc->spacer( 10 );
        
        // Process each shortcut
        foreach( $this->shortcuts as $key => $value ) {
            
            // Shortcut label
            $label = $LANG->getLL( 'shortcuts.' . $key );
            
            // Shortcut icon
            $icon  = '<img src="../res/'
                   . $value[ 'icon' ]
                   . '" alt="" width="16" height="16" />';
            
            // Full link
            $link  = '<a href="#console" onclick="shell.exec( \''
                   . $value[ 'command' ]
                   . '\' );" title="'
                   . $value[ 'command' ]
                   . '">'
                   . $label
                   . '</a>';
            
            // Add shortcut
            $htmlCode[] = $this->writeHtml(
                $icon . $link,
                'div',
                'shortcut'
            );
        }
        
        // Return shortcut section
        return $this->writeHtml(
            $this->writeHtml(
                implode( chr( 10 ), $htmlCode ),
                'div',
                'shortcuts'
            )
        );
    }
    
    /**
     * Builds the terminal section
     * 
     * @return  string  The terminal section
     */
    function buildTerminal()
    {
        // Storage
        $htmlCode   = array();
        
        // Shell result
        $htmlCode[] = '<a name="console"></a><div id="result"></div>';
        
        // Spacer
        $htmlCode[]     = $this->doc->spacer( 5 );
        
        // Prompt
        $htmlCode[] = $this->writeHtml(
            $this->prompt,
            'span',
            'prompt'
        );
        
        // Shell command
        $htmlCode[] = '<input name="command" id="command" type="text" size="50" />';
        
        // Return terminal
        return $this->writeHtml(
            implode( chr( 10 ), $htmlCode ),
            'div',
            'shell'
        );
    }
    
    /**
     * Builds the module's CSS styles
     * 
     * @return  boolean
     */
    function buildCssStyles()
    {
        // Storage
        $css   = array();
        
        // Shell
        $css[] = '.shell {';
        $css[] = '  color: ' . $this->extConf[ 'foreground' ] . ';';
        $css[] = '  background-color: ' . $this->extConf[ 'background' ] . ';';
        $css[] = '  padding: 10px;';
        $css[] = '}';
        
        // Common properties
        $css[] = '.shell, .shell * {';
        $css[] = '  font-family: monospace;';
        $css[] = '  font-size: ' . $this->extConf[ 'fontSize' ] . ';';
        $css[] = '}';
        
        // Result container
        $css[] = '#result {';
        $css[] = '  height: 400px;';
        $css[] = '  overflow: auto;';
        $css[] = '}';
        
        // Result
        $css[] = '#result DIV {';
        $css[] = '  color: ' . $this->extConf[ 'foreground' ] . ';';
        $css[] = '  background-color: ' . $this->extConf[ 'background' ] . ';';
        $css[] = '}';
        
        // Command line
        $css[] = '#command {';
        $css[] = '  color: ' . $this->extConf[ 'foreground' ] . ';';
        $css[] = '  background-color: ' . $this->extConf[ 'background' ] . ';';
        $css[] = '  border: none;';
        $css[] = '  font-weight: bold;';
        $css[] = '}';
        
        // Command prompt
        $css[] = '.prompt {';
        $css[] = '  color: ' . $this->extConf[ 'prompt' ] . ';';
        $css[] = '  background-color: ' . $this->extConf[ 'background' ] . ';';
        $css[] = '}';
        
        // Shell command
        $css[] = '.command {';
        $css[] = '  font-weight: bold;';
        $css[] = '}';
        
        // Shell result
        $css[] = '.result {';
        $css[] = '  white-space: pre;';
        $css[] = '}';
        
        // Container for shortcuts
        $css[] = '.shortcuts {';
        $css[] = '  overflow: hidden;';
        $css[] = '}';
        
        // Shortcut item
        $css[] = '.shortcut {';
        $css[] = '  float: left;';
        $css[] = '  width: 230px;';
        $css[] = '  margin-right: 5px;';
        $css[] = '}';
        
        // Shortcut pictures
        $css[] = '.shortcut IMG {';
        $css[] = '  margin-right: 5px;';
        $css[] = '}';
        
        // Returns the CSS styles
        return implode( chr( 10 ), $css );
    }
    
    /**
     * Process a command
     * 
     * This function is used to call a shell command when requested by
     * an ajax request. It displays the result of the command, then
     * aborts the script. The last line of the output is the current
     * working directory.
     * 
     * @return  NULL
     */
    function processCommand()
    {
        global $BE_USER;
        
        // Gets the command
        $cmd = t3lib_div::_GET( 'command' );
        
        // Checks for an empty command
        if( $cmd == '' ) {
            
            // Prints the current workind directory and exit
            print chr( 10 ) . $this->cwd;
            exit();
        }
        
        // Checks if the command must be kept in the history
        if( $this->extConf[ 'history' ] ) {
            
            // Adds the command
            array_unshift( $this->commands, $cmd );
        }
        
        // Only keep 50 entries in history
        $this->commands = array_slice( $this->commands, 0, 50 );
        
        // Process pipes
        $descriptorSpec = array(
            0 => array( 'pipe', 'r' ),
            1 => array( 'pipe', 'w' ),
            2 => array( 'pipe', 'r' )
        );
        
        // Storage
        $return = '';
        $error  = '';
        
        // Gets multiple commands
        $commands = explode( ' && ', $cmd );
        
        // Process each command
        foreach( $commands as $command ) {
            
            // Command is 'cd'
            if( preg_match( '/^\s*cd\s*$/', $command ) ) {
                
                // Home is TYPO3 site
                $this->cwd = PATH_site;
            }
            
            // Change directory command
            if( preg_match( '/\s*cd ([^\s]+)\s*/', $command, $matches ) ) {
                
                // DIrectory to change
                $dir = $matches[ 1 ];
                
                // Checks for an absolute path
                if( substr( $dir, 0, 1 ) == '/' ) {
                    
                    // Sets the current directory
                    $this->cwd = $matches[ 1 ];
                    
                } else {
                    
                    // Sets the current directory
                    $this->cwd = $this->cwd . $matches[ 1 ];
                }
            }
            
            // Adds a trailing slash if necessary
            if( substr( $this->cwd, strlen( $this->cwd ) - 1, 1 ) != '/' ) {
                
                $this->cwd .= '/';
            }
            
            // Normalize the path
            $this->cwd = preg_replace( '/\/\/+/', '/', $this->cwd );
            $this->cwd = str_replace( '/./', '/', $this->cwd );
            
            // Get path parts
            $cwdParts = explode( '/', $this->cwd );
            $cwd      = array();
            
            // Process each part of the path
            foreach( $cwdParts as $key => $value  ) {
                
                // Previous directory
                if( $value == '..' ) {
                    
                    // Removes last directory
                    array_pop( $cwd );
                    
                } else {
                    
                    // Stores current directory
                    $cwd[] = $value;
                }
            }
            
            // Rebuilds the path
            $this->cwd = implode( '/', $cwd );
            
            // Stores commands and working directory in session data
            $BE_USER->pushModuleData(
                $GLOBALS[ 'MCONF' ][ 'name' ],
                array(
                    'cwd'      => $this->cwd,
                    'commands' => $this->commands
                )
            );
            
            // Open process
            $process = proc_open(
                $command,
                $descriptorSpec,
                $pipes,
                $this->cwd,
                $_ENV
            );
            
            // Checks the process
            if( is_resource( $process ) ) {
                
                // Process pipes
                $stdin  = $pipes[0];
                $stdout = $pipes[1];
                $stderr = $pipes[2];
                
                // Process and stores the result
                while( !feof( $stdout ) ) {
                    
                    $return .= fgets( $stdout );
                }

                // Process and stores errors
                while( !feof( $stderr ) ) {
                    
                    $error .= fgets( $stderr );
                }
                
                // Close process pipes
                fclose( $stdin );
                fclose( $stdout );
                fclose( $stderr );
                
                // Close the process
                proc_close( $process );
                
                // Checks for errors
                if( empty( $error ) ) {
                    
                    // Display results
                    print $return;
                    
                } else {
                    
                    // Display errors, current working directory and exit
                    print $error;
                    print chr( 10 ) . $this->cwd;
                    exit();
                }
           }
        }
        
        // Prints the current working directory and exit
        print chr( 10 ) . $this->cwd;
        exit();
    }
    
    /**
     * Gets the session data for this module
     * 
     * @return  boolean
     */
    function sessionData()
    {
        global $BE_USER;
        
        // Get module session data
        $data = $BE_USER->getModuleData( $GLOBALS[ 'MCONF' ][ 'name' ] );
        
        // Checks for working directory
        if( !isset( $data[ 'cwd' ] ) ) {
            
            // Default is TYPO3 site
            $data[ 'cwd' ] = PATH_site;
        }
        
        // Checks for commands
        if( !isset( $data[ 'commands' ] ) || $this->extConf[ 'history' ] == 0 ) {
            
            // Empty array
            $data[ 'commands' ] = array();
        }
        
        // Stores session data for other methods
        $this->cwd      = $data[ 'cwd' ];
        $this->commands = $data[ 'commands' ];
        
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
