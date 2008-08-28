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

// Includes the TYPO3 module classe
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );

// Includes the macmade.net API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

abstract class tx_adlercontest_scBase extends t3lib_SCbase
{
    /**
     * The method used to get the module content
     */
    abstract protected function _getContent();
    
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
     * The database tables used by this extension
     */
    protected static $_dbTables      = array(
        'users'    => 'fe_users',
        'profiles' => 'tx_adlercontest_users'
    );
    
    /**
     * The back path (to the TYPO3 directory)
     */
    protected static $_backPath      = '';
    
    /**
     * The new line character
     */
    protected static $_NL            = '';
    
    /**
     * The date format
     */
    protected static $_dateFormat    = 'd.m.Y';
    
    /**
     * The name of the module class
     */
    private $_modClass               = '';
    
    /**
     * The hour format
     */
    protected static $_hourFormat    = 'H:i';
    
    /**
     * The instance of the Developer API
     */
    protected $_api                  = NULL;
    
    /**
     * The current page's row
     */
    protected $_page                 = array();
    
    /**
     * The page informations
     */
    protected $_pageInfos            = array();
    
    /**
     * The module content
     */
    protected $_content              = array();
    
    /**
     * Storage for the started HTML tags
     */
    protected $_startedTags          = array();
    
    /**
     * The GET/POST variables from this module
     */
    protected $_modVars              = array();
    
    /**
     * The TYPO3 document object
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
        // Sets the module name
        $this->_modName = get_class( $this );
        
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
        
        // Gets the module variables
        $this->_modVars           = t3lib_div::_GP( $this->_modName );
    }
    
    /**
     * 
     */
    public function __toString()
    {
        // Writes the full page
        $content = $this->doc->startPage( self::$_lang->getLL( 'title' ), $this->_modName )
                 . $this->doc->header( self::$_lang->getLL ( 'title' ), $this->_modName )
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
    protected function _tag( $tag, $content = '', array $params = array(), array $styles = array(), $onlyStart = false )
    {
        // Storage
        $tagParams    = '';
        $inlineStyles = '';
        
        // Process each parameter
        foreach( $params as $key => $value ) {
            
            // The style parameter is not allowed
            if( $key === 'style' ) {
                
                // Process the next parameter
                continue;
            }
            
            // Adds the parameter
            $tagParams .= ' ' . $key . '="' . $value . '"';
        }
        
        // Checks for styles
        if( count( $styles ) ) {
            
            // Adds the style parameter
            $inlineStyles .= ' style="';
            
            // Process each style
            foreach( $styles as $key => $value ) {
                
                // Adds the style
                $inlineStyles .= $key . ': ' . $value . ';';
            }
            
            // Closes the style parameter
            $inlineStyles .= '"';
        }
        
        // Full tag
        $fullTag = '<' . $tag . $tagParams . $inlineStyles;
        
        // Checks the write mode
        if( $onlyStart ) {
            
            // Only the start tag
            $fullTag .= '>';
            
            // Registers the tag as open
            $this->_startedTags[] = $tag;
            
        } elseif( $content || $tag === 'script' ) {
            
            // Adds the content and closes the tag
            $fullTag .= '>' . $content . '</' . $tag . '>';
            
        } else {
            
            // Closes the tag
            $fullTag .= ' />';
        }
        
        // Returns the tag
        return $fullTag;
    }
    
    /**
     * 
     */
    protected function _endTag()
    {
        // Checks for open tags
        if( count( $this->_startedTags ) ) {
            
            // Gets and removes the last tag
            $tag = array_pop( $this->_startedTags );
            
            // Returns the closing tag
            return '</' . $tag . '>';
        }
        
        // No open tag
        return '';
    }
    
    /**
     * 
     */
    protected function _getFieldLabel( $table, $field )
    {
        // Checks if the table exists in the TCA
        if( !isset( self::$_tca[ $table ] ) ) {
            
            // The table does not exist
            return '';
        }
        
        // Checks if the TCA is loaded
        if( !isset( self::$_tca[ $table ][ 'columns' ] ) ) {
            
            // Loads the TCA
            t3lib_div::loadTCA( $table );
        }
        
        // Checks if the field exists in the TCA
        if( !isset( self::$_tca[ $table ][ 'columns' ][ $field ] ) ) {
            
            // The field does not exist
            return '';
        }
        
        // Returns the label
        return self::$_lang->sL( self::$_tca[ $table ][ 'columns' ][ $field ][ 'label' ] );
    }
    
    /**
     * 
     */
    public function main()
    {
        // Gets the page informations
        $this->_pageInfos =  t3lib_BEfunc::readPageAccess(
            $this->id,
            $this->perms_clause
        );
        
        // Checks the module access
        if( ( $this->id && is_array( $this->_pageInfos ) ) || ( self::$_beUser->user[ 'admin' ] && !$this->id ) ) {
            
            // Creates the main form tag
            $this->doc->form = $this->_tag(
                'form',
                '',
                array(
                    'action'  => '',
                    'method'  => 'post',
                    'enctype' => self::$_typo3ConfVars[ 'SYS' ][ 'form_enctype' ],
                    'id'      => $this->_modName,
                    'name'    => $this->_modName
                ),
                array(),
                true
            );
            
            // JavaScript files
            $this->doc->JScode   = $this->_tag(
                'script',
                '',
                array(
                    'src'     => '../res/js/mod1.js',
                    'type'    => 'text/javascript',
                    'charset' => 'utf-8',
                )
            );
            $this->doc->postCode = $this->_tag(
                'script',
                '',
                array(
                    'src'     => '../res/js/mod1-postcode.js',
                    'type'    => 'text/javascript',
                    'charset' => 'utf-8',
                )
            );
            
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
                $header
              . $this->_tag( 'br' )
              . $path,
                $funcMenu
            );
            
            // Adds the full header
            $this->_content[] = $this->doc->section( '', $headerSection );
            $this->_content[] = $this->doc->divider( 5 );
            $this->_content[] = $this->doc->spacer( 20 );
            
            // Creates the module content
            $this->_content[] = $this->_getContent();
            
            // Checks if shortcuts are allowed
            if( self::$_beUser->mayMakeShortcut() ) {
                
                // Creates the shortcut
                $shortcut = $this->doc->makeShortcutIcon(
                    'id',
                    implode( ',', array_keys( $this->MOD_MENU ) ),
                    $this->MCONF[ 'name' ]
                );
                
                // Adds the shortcut
                $this->_content[] = $this->doc->spacer( 20 )
                                  . $this->doc->section( '', $shortcut );
            }
            
        } else {
            
            // No access
            $this->_content[] = self::$_lang->getLL( 'error.no-access' );
        }
    }
    
    /**
     * 
     */
    public function menuConfig()
    {
        // Prepares the array with the menu items
        $this->MOD_MENU = array(
            'function' => array()
        );
        
        // Process each menu item
        foreach( $this->_menuItems as $key => $value ) {
            
            // Adds the menu item
            $this->MOD_MENU[ 'function' ][ $key ] = self::$_lang->getLL( $value );
        }
        
        // Creates the menu
        parent::menuConfig();
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_scbase.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_scbase.php']);
}
