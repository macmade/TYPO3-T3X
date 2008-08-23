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
 * Module 'Adler / Contest' for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Loads the module configuration
unset( $MCONF );
require( 'conf.php' );

// Includes the TYPO3 initialization files
require( $BACK_PATH . 'init.php' );
require( $BACK_PATH . 'template.php' );

// Includes the TYPO3 module classes
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );
require_once( PATH_t3lib . 'class.t3lib_tcemain.php' );

// Includes the language labels
$LANG->includeLLFile( 'EXT:adler_contest/lang/mod1.xml' );

// Checks the access to the module
$BE_USER->modAccess( $MCONF, 1 );

// Includes the macmade.net API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_adlercontest_module1 extends t3lib_SCbase
{
    /**
     * The backend user object
     */
    protected static $_beUser        = NULL;
    
    /**
     * The language object
     */
    protected static $_lang          = NULL;
    
    /**
     * The TCA array
     */
    protected static $_tca           = array();
    
    /**
     * The TCA description array
     */
    protected static $_tcaDescr      = array();
    
    /**
     * The TYPO3 configuration array
     */
    protected static $_typo3ConfVars = array();
    
    /**
     * The TYPO3 client attay
     */
    protected static $_client        = array();
    
    /**
     * The back path (to the TYPO3 directory)
     */
    protected static $_backPath      = '';
    
    /**
     * The new line character
     */
    protected static $_NL            = '';
    
    /**
     * The instance of the Developer API
     */
    protected $_api                  = NULL;
    
    /**
     * The current page's row
     */
    protected $_page                 = array();
    
    /**
     * The module content
     */
    protected $_pageInfos            = array();
    
    /**
     * 
     */
    protected $_content              = array();
    
    /**
     * 
     */
    public $doc                      = NULL;
    
    /**
     * The required version of the macmade.net API
     */
    public $apimacmade_version       = 4.5;
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Checks if the reference to the backend user object already exists
        if( !is_object( self::$_beUser ) ) {
            
            // Creates a reference to the backend user object
            self::$_beUser        =  $GLOBALS[ 'BE_USER' ];
        }
        
        // Checks if the reference to lang object already exists
        if( !is_object( self::$_lang ) ) {
            
            // Creates a reference to the lang object
            self::$_lang          =  $GLOBALS[ 'LANG' ];
        }
        
        // Checks if the reference to the TCA array already exists
        if( !count( self::$_tca ) ) {
            
            // Creates a reference to the TCA array
            self::$_tca           =& $GLOBALS[ 'TCA' ];
        }
        
        // Checks if the reference to the TCA description array already exists
        if( !count( self::$_tcaDescr ) ) {
            
            // Creates a reference to the TCA description array
            self::$_tcaDescr      =& $GLOBALS[ 'TCA_DESCR' ];
        }
        
        // Checks if the reference to the TYPO3 configuration array already exists
        if( !count( self::$_typo3ConfVars ) ) {
            
            // Creates a reference to the TYPO3 configuration array
            self::$_typo3ConfVars =& $GLOBALS[ 'TYPO3_CONF_VARS' ];
        }
        
        // Checks if the reference to the TYPO3 client array already exists
        if( !count( self::$_client ) ) {
            
            // Creates a reference to the TYPO3 client array
            self::$_client        =& $GLOBALS[ 'CLIENT' ];
        }
        
        // Checks if the back path already exists
        if( !self::$_backPath ) {
            
            // Sets the back path
            self::$_backPath      =  $GLOBALS[ 'BACK_PATH' ];
        }
        
        // Checks if the new line character already exists
        if( !self::$_NL ) {
            
            // Sets the new line character
            self::$_NL            =  chr( 10 );
        }
        
        // Checks for an active page
        if( $this->id ) {
            
            // Gets the active page row
            $this->_page          =  t3lib_BEfunc::getRecord(
                $this->id
            );
        }
        
        // Gets a new instance of the macmade.net API
        $this->_api               =  tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                $this
            )
        );
        
        // Creates an instance of the TYPO3 document class
        $this->doc                = t3lib_div::makeInstance( 'bigDoc' );
        
        // Sets the back path
        $this->doc->backPath      = self::$_backPath;
    }
    
    /**
     * 
     */
    public function __toString()
    {
        // Writes the full page
        $content = $this->doc->startPage( self::$_lang->getLL( 'title' ) )
                 . $this->doc->header( self::$_lang->getLL ( 'title' ) )
                 . $this->doc->spacer( 5 )
                 . implode( self::$_NL, $this->_content )
                 . $this->doc->spacer( 10 )
                 . $this->doc->endPage();
        
        // Return the full page
        return $content;
    }
    
    /**
     * 
     */
    protected function _moduleContent()
    {
        return 'CONTENT GOES HERE...';
    }
    
    /**
     * 
     */
    public function main()
    {
        // Gets the page informations
        $this->_pageInfos         =  t3lib_BEfunc::readPageAccess(
            $this->id,
            $this->perms_clause
        );
        
        // Checks the module access
        if( ( $this->id && is_array( $this->_pageInfos ) ) || ( self::$_beUser->user[ 'admin' ] && !$this->id ) ) {
            
            // Creates the main form tag
            $this->doc->form = '<form action="" method="post" enctype="'
                             . self::$_typo3ConfVars[ 'SYS' ][ 'form_enctype' ]
                             . '" id="'
                             . __CLASS__
                             . '" name="'
                             . __CLASS__
                             . '">';
            
            // JavaScript files
            $this->doc->JScode   = '<script src="mod.js" type="text/javascript" charset="utf-8"></script>';
            $this->doc->postCode = '<script src="mod-postcode.js" type="text/javascript" charset="utf-8"></script>';
            
            // Initializes the context sensitive menu
            $this->_api->be_initCsm();
            
            // Gets the header
            $header        = $this->doc->getHeader(
                'pages',
                $this->_pageInfos,
                $this->_pageInfos[ '_thePath' ]
            );
            
            // Creates the path (rootline)
            $path          = self::$_lang->sL( 'LLL:EXT:lang/locallang_core.php:labels.path' )
                           . ': '
                           . t3lib_div::fixed_lgd_pre( $this->_pageInfos[ '_thePath' ], 50 );
            
            // Creates the function menu
            $funcMenu      = t3lib_BEfunc::getFuncMenu(
                $this->id,
                'SET[function]',
                $this->MOD_SETTINGS[ 'function' ],
                $this->MOD_MENU[ 'function' ]
            );
            
            // Creates the full header section
            $headerSection = $this->doc->funcMenu(
                $header . '<br />' . $path,
                $funcMenu
            );
            
            // Adds the full header
            $this->_content[] = $this->doc->section( '', $headerSection );
            $this->_content[] = $this->doc->divider( 5 );
            $this->_content[] = $this->doc->spacer( 20 );
            
            // Creates the module content
            $this->_content[] = $this->_moduleContent();
            
            // Checks if shortcuts are allowed
            if( self::$_beUser->mayMakeShortcut() ) {
                
                // Creates the shortcut
                $shortcut = $this->doc->makeShortcutIcon(
                    'id',
                    implode( ',', array_keys( $this->MOD_MENU ) ),
                    $this->MCONF[ 'name' ]
                );
                
                // Adds the shortcut
                $this->_content[] = $this->doc->spacer( 20 ) . $this->doc->section( '', $shortcut );;
            }
            
        } else {
            
            // No access
            $this->_content[] = self::$_lang->getLL( 'noaccess' );
        }
    }
    
    /**
     * 
     */
    public function menuConfig()
    {
        // Array with the menu items
        $this->MOD_MENU = array (
            'function' => array (
                '1' => self::$_lang->getLL( 'menu.func.1' )
            )
        );
        
        // Creates the menu
        parent::menuConfig();
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/mod1/index.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/mod1/index.php']);
}

// Creates the module instance
$SOBE = t3lib_div::makeInstance( 'tx_adlercontest_module1' );

// Initializes the module
$SOBE->init();

// Process each include file
foreach( $SOBE->include_once as $includeFile ) {
    
    // Includes the file
    include_once( $includeFile );
}

// Launches the module main method
$SOBE->main();

// Writes the module content
print $SOBE;
