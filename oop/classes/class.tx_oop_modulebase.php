<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (macmade@eosgarden.com)
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

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the TYPO3 module classe
require_once( PATH_t3lib . 'class.t3lib_scbase.php' );

/**
 * Abstract class for the TYPO3 backend modules
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
abstract class tx_oop_moduleBase extends t3lib_SCbase
{
    /**
     * The method used to get the module content, which has to be declared
     * in the child classes
     */
    abstract protected function _getModuleContent( tx_oop_xhtmlTag $content );
    
    abstract protected function _getMenuItems();
    
    /**
     * A flag to know wether the needed static variables are set
     */
    private static $_hasStatic   = false;
    
    /**
     * A reference to the t3lib_DB object
     */
    protected static $_db        = NULL;
    
    /**
     * 
     */
    protected static $_t3Lang    = NULL;
    
    /**
     * A reference to the t3lib_beUserAuth object
     */
    protected static $_beUser    = NULL;
    
    /**
     * A reference to the TCA description array
     */
    protected static $_tcaDescr  = array();
    
    /**
     * A reference to the TCA array
     */
    protected static $_tca       = array();
    
    /**
     * A reference to the client informations array
     */
    protected static $client     = array();
    
    /**
     * A reference to the TYPO3 configuration variables array
     */
    protected static $_t3Conf    = array();
    
    /**
     * The ASCII new line character
     */
    protected static $_NL        = '';
    
    /**
     * The ASCII tabulation character
     */
    protected static $_TAB       = '';
    
    /**
     * A reflection object for the backend module
     */
    private $_reflection         = NULL;
    
    /**
     * 
     */
    private $_content            = NULL;
    
    /**
     * The buttons for the TYPO3 backend module
     */
    private $_buttons            = array(
        'csh'      => '',
        'save'     => '',
        'shortcut' => ''
    );
    
    /**
     * The module number
     */
    private $_moduleNumber       = 0;
    
    /**
     * The name of the module (child) class
     */
    private $_moduleClass        = '';
    
    /**
     * The name of the module
     */
    private $_moduleName         = '';
    
    /**
     * The section of the module
     */
    private $_moduleSection      = '';
    
    /**
     * 
     */
    private $_modulePath         = '';
    
    /**
     * 
     */
    private $_moduleRelativePath = '';
    
    /**
     * 
     */
    private $_extKey             = '';
    
    /**
     * 
     */
    private $_extPath            = '';
    
    /**
     * 
     */
    private $_extRelativePath    = '';
    
    /**
     * 
     */
    private $_uploadDirectory    = '';
    
    /**
     * 
     */
    private $_pageStart          = '';
    
    /**
     * 
     */
    private $_pageEnd            = '';
    
    /**
     * 
     */
    protected $_lang             = NULL;
    
    /**
     * 
     */
    protected $_pageInfos        = array();
    
    /**
     * 
     */
    protected $_modVars          = array();
    
    /**
     * 
     */
    protected $_backPath         = '';
    
    /**
     * 
     */
    public $doc                  = NULL;
    
    /**
     * 
     */
    public function __construct()
    {
        $this->_moduleClass = get_class( $this );
        
        $this->_reflection = new ReflectionObject( $this );
        
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $this->_backPath = $GLOBALS[ 'BACK_PATH' ];
        
        $this->_modVars = t3lib_div::_GP( $this->_moduleClass );
        
        foreach( $this->include_once as $includeFile ) {
            
            require_once( $includeFile );
        }
        
        $this->_moduleName    = $GLOBALS[ 'MCONF' ][ 'name' ];
        $this->_moduleNumber  = substr( $this->_moduleName, -1 );
        $this->_moduleSection = substr( $this->_moduleName, 0, strpos( $this->_moduleName, '_' ) );
        
        $extPathInfo = explode( DIRECTORY_SEPARATOR, $this->_reflection->getFileName() );
        
        array_pop( $extPathInfo );
        array_pop( $extPathInfo );
        
        $this->_extKey             = array_pop( $extPathInfo );
        
        $this->_extPath            = t3lib_extMgm::extPath( $this->_extKey );
        
        $this->_extRelativePath    = t3lib_extMgm::extRelPath( $this->_extKey );
        
        $this->_modulePath         = $this->_extPath . 'mod' . $this->_moduleNumber;
        
        $this->_moduleRelativePath = $this->_extRelativePath . 'mod1/';
        
        $this->_uploadDirectory    = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->_extKey ) . '/' )
        );
        
        $this->_lang               = tx_oop_lang::getInstance( 'EXT:' . $this->_extKey . '/lang/mod' . $this->_moduleNumber . '.xml' );
        
        $this->_content            = new tx_oop_xhtmlTag( 'div' );
    }
    
    /**
     * 
     */
    public function __toString()
    {
        $content                = '';
        $markers                = array();
        $markers[ 'CONTENT' ]   = ( string )$this->_content;
        $markers[ 'FUNC_MENU' ] = t3lib_BEfunc::getFuncMenu(
            $this->id,
            'SET[function]',
            $this->MOD_SETTINGS[ 'function' ],
            $this->MOD_MENU[ 'function' ]
        );
        
        $content                = $this->doc->startPage( $this->_lang->title )
                                . $this->doc->moduleBody( $this->_pageInfos, $this->_buttons, $markers )
                                . $this->doc->endPage();
        
        return $this->doc->insertStylesAndJS( $content );
    }
    
    /**
     * 
     */
    private static function _setStaticVars()
    {
        self::$_db        =  $GLOBALS[ 'TYPO3_DB' ];
        self::$_t3Lang    =  $GLOBALS[ 'LANG' ];
        self::$_beUser    =  $GLOBALS[ 'BE_USER' ];
        self::$_tcaDescr  =& $GLOBALS[ 'TCA_DESCR' ];
        self::$_tca       =& $GLOBALS[ 'TCA' ];
        self::$client     =& $GLOBALS[ 'CLIENT' ];
        self::$_t3Conf    =& $GLOBALS[ 'TYPO3_CONF_VARS' ];
        self::$_NL        =  chr( 10 );
        self::$_TAB       =  chr( 9 );
        self::$_hasStatic =  true;
    }
    
    /**
     * Creates a thumbnail
     * 
     * @param   string  $file   The file path (relative to the upload directory)
     * @param   int     $width  The thumbnail maximum width
     * @param   int     $height The thumbnail maximum height
     * @return  tx_oop_xhtmlTag The thumbnail picture
     */
    protected function _createThumbnail( $file, $width = 100, $height = 100 )
    {
        // Gets the file absolute path
        $fileAbsPath  = PATH_site . $this->_uploadDirectory . $file;
        
        // Security check
        $fileCheck    = basename( $fileAbsPath)
                      . ':'
                      . filemtime( $fileAbsPath )
                      . ':'
                      . self::$_typo3ConfVars[ 'SYS' ][ 'encryptionKey' ];
        
        
        // URL that will generate the thumbnail
        $url          = self::$_backPath
                      . 'thumbs.php?size='
                      . $width
                      . 'x'
                      . $height
                      . '&file='
                      . rawurlencode( '../' . self::$_uploadDirectory . $file )
                      . '&md5sum='
                      . t3lib_div::shortMD5( $fileCheck );
        
        // Creates the image tag
        $img          = tx_oop_xhtmlTag( 'img' );
        $img[ 'src' ] = htmlspecialchars( $url );
        $img[ 'alt' ] = $file;
        
        // Returns the thumbnail
        return $img;
    }
    
    /**
     * Creates a link with module variables
     * 
     * @param   string  $text           The link text
     * @param   array   $params         The module variables to set, as key/value pairs
     * @param   array   $keepModVars    The module variables to keep
     * @return  tx_oop_xhtmlTag         The link
     */
    protected function _link( $text, array $params = array(), $keepModVars = false )
    {
        // Base url
        $url = ( $keepModVars ) ? t3lib_div::linkThisScript( array( $this->_moduleName => $this->_modVars ) ) : t3lib_div::linkThisScript();
        
        // Process each parameter
        foreach( $params as $key => $value ) {
            
            // Adds the current parameter
            $url .= '&' . $this->_moduleName . '[' . $key . ']=' . $value;
        }
        
        // Creates the full link
        $link              = new tx_oop_xhtmlTag( 'a' );
        $link[ 'href' ]    = $url;
        
        // Adds the linked text
        $link->addTextData( $text );
        
        // Returns the link
        return $link;
    }
    
    /**
     * Creates a link to a function of the menu
     * 
     * @param   string  $text       The link text
     * @param   int     $function   The menu function ID
     * @return  tx_oop_xhtmlTag     The link
     */
    protected function _functionLink( $text, $function )
    {
        // Creates the URL
        $url = t3lib_div::linkThisScript(
            array(
                'SET' => array(
                    'function' => $function
                )
            )
        );
        
        // Creates the full link
        $link              = new tx_oop_xhtmlTag( 'a' );
        $link[ 'href' ]    = $url;
        
        // Adds the linked text
        $link->addTextData( $text );
        
        // Returns the link
        return $link;
    }
    
    /**
     * Creates a link to the edit view of a record
     * 
     * @param   string  $table  The table name
     * @param   int     $uid    The ID of the record
     * @param   string  $text   The link text
     * @return  tx_oop_xhtmlTag The link
     */
    protected function _editLink( $table, $uid, $text )
    {
        // On click action
        $onClick = t3lib_BEfunc::editOnClick(
            '&edit[' . $table . '][' . $uid . ']=edit',
            $this->_backPath
        );
        
        // Creates the full link
        $link              = new tx_oop_xhtmlTag( 'a' );
        $link[ 'href' ]    = '#';
        $link[ 'onclick' ] = htmlspecialchars( $onClick );
        
        // Adds the linked text
        $link->addTextData( $text );
        
        // Returns the link
        return $link;
    }
    
    /**
     * Gets an icon from the extension
     * 
     * @param   string  $name   The icon name
     * @return  tx_oop_xhtmlTag The image tag
     */
    protected function _skinImg( $name )
    {
        // Gets the file absolute path
        $fileAbsPath = $this->_extPath
                     . 'res/img/'
                     . $name;
        
        // Checks if the requested file exists
        if( file_exists( $fileAbsPath ) && is_readable( $fileAbsPath ) ) {
            
            // Gets the image size
            $size   = getimagesize( $fileAbsPath );
            
            // Creates the image tag
            $img             = new tx_oop_xhtmlTag( 'img' );
            $img[ 'src' ]    =  $this->_backPath . $this->_extRelativePath . 'res/img/' . $name;
            $img[ 'width' ]  = $size[ 0 ];
            $img[ 'height' ] = $size[ 1 ];
            $img[ 'alt' ]    = $name;
            $img[ 'hspace' ] = '0';
            $img[ 'vspace' ] = '0';
            $img[ 'border' ] = '0';
            $img[ 'align' ]  = 'middle';
            
            // Returns the image tag
            return $img;
        }
        
        // File not found
        return '';
    }
    
    /**
     * Gets an icon from TYPO3
     * 
     * @param   string  $name   The icon name
     * @return  tx_oop_xhtmlTag The image tag
     */
    protected function _t3SkinImg( $name )
    {
        // Gets the image source
        $imgSrc = t3lib_iconWorks::skinImg( $this->_backPath, $name, '', 1 );
        
        // Gets the file absolute path
        $fileAbsPath = $GLOBALS[ 'TBE_STYLES' ][ 'skinImgAutoCfg' ][ 'absDir' ] . $name;
        
        // Checks if the requested file exists
        if( file_exists( $fileAbsPath ) && is_readable( $fileAbsPath ) ) {
            
            // Gets the image size
            $size   = getimagesize( $fileAbsPath );
            
            // Creates the image tag
            $img             = new tx_oop_xhtmlTag( 'img' );
            $img[ 'src' ]    = $imgSrc;
            $img[ 'width' ]  = $size[ 0 ];
            $img[ 'height' ] = $size[ 1 ];
            $img[ 'alt' ]    = $name;
            $img[ 'hspace' ] = '0';
            $img[ 'vspace' ] = '0';
            $img[ 'border' ] = '0';
            $img[ 'align' ]  = 'middle';
            
            // Returns the image tag
            return $img;
        }
        
        // File not found
        return '';
    }
    
    /**
     * 
     */
    protected function _addButton( $name, $file, $fromSkin = true )
    {
        // Creates the button image
        $img = ( $fromSkin ) ? $this->_t3SkinImg( $file ) : $this->_skinImg( $file );
        
        // URL
        $url = t3lib_div::linkThisScript( array( $this->_moduleName => array( 'actions' => array( $name => true ) ) ) );
        
        // Creates the full link
        $link              = new tx_oop_xhtmlTag( 'a' );
        $link[ 'href' ]    = $url;
        
        // Adds the button image
        $link->addTextData( $img );
        
        // Adds the button
        $this->_buttons[ $name ] = $link;
    }
    
    /**
     * 
     */
    protected function _checkAction( $name )
    {
        return isset( $this->_modVars[ 'actions' ][ $name ] );
    }
    
    /**
     * 
     */
    public function menuConfig()
    {
        $this->MOD_MENU = array(
            'function' => $this->_getMenuItems()
        );
        
        parent::menuConfig();
    }
    
    /**
     * 
     */
    public function createModulePage()
    {
        parent::init();
        
        $this->_pageInfos = t3lib_BEfunc::readPageAccess(
            $this->id,
            $this->perms_clause
        );
        
        $access = is_array( $this->_pageInfos ) ? true : false;
        
        $this->doc = t3lib_div::makeInstance( 'template' );
        
        $this->doc->setModuleTemplate( $this->_extPath . 'res/html/mod' . $this->_moduleNumber . '.html' );
        
        $this->doc->backPath = $this->_backPath;
        
        $this->_buttons[ 'csh' ] = t3lib_BEfunc::cshItem(
            '_MOD_' . $this->_moduleSection . '_func',
            '',
            $this->_backPath
        );
        
        $this->_addButton( 'save', 'gfx/savedok.gif' );
        
        if( self::$_beUser->mayMakeShortcut() ) {
            
            $this->_buttons[ 'shortcut' ] = $this->doc->makeShortcutIcon(
                '',
                'function',
                $this->_moduleName
            );
        }
        
        // Checks wether the current BE user can access this module
        if( ( $this->id && $access )
            || ( self::$_beUser->user[ 'admin' ] && !$this->id )
        ) {
            
            $contextMenuParts = $this->doc->getContextMenuCode();
            
            $this->doc->bodyTagAdditions = $contextMenuParts[ 1 ];
            
            // Creates the module's form tag
            $form                  = new tx_oop_xhtmlTag( 'form' );
            $form[ 'action' ]      = '';
            $form[ 'method' ]      = 'post';
            $form[ 'enctype' ]     = self::$_t3Conf[ 'SYS' ][ 'form_enctype' ];
            $form[ 'name' ]        = $this->_moduleName . '_form';
            $form[ 'id' ]          = $this->_moduleName . '_form';
            
            $this->doc->form       = ( string )$form;
            
            $jsCode                = new tx_oop_xhtmlTag( 'script' );
            $jsCode[ 'type' ]      = 'text/javascript';
            $jsCode[ 'charset' ]   = 'utf-8';
            $jsCodeData                       = '// <![CDATA['
                                   . self::$_NL
                                   . 'var script_ended = 0;'
                                   . self::$_NL
                                   . 'function jumpToUrl( URL ) {'
                                   . self::$_NL
                                   . '    document.location = URL;'
                                   . self::$_NL
                                   . '}'
                                   . self::$_NL
                                   . '// ]]>';
            
            $jsCode->addTextData( $jsCodeData );
            
            $this->doc->JScode     = ( string )$jsCode . self::$_NL . $contextMenuParts[ 0 ];
            
            $postCode              = new tx_oop_xhtmlTag( 'script' );
            $postCode[ 'type' ]    = 'text/javascript';
            $postCode[ 'charset' ] = 'utf-8';
            $postCode[ 'src' ]     = '';
            
            // Adds some JavaScript code
            $postCodeData          = '// <![CDATA['
                                   . self::$_NL
                                   . 'script_ended = 1;'
                                   . self::$_NL
                                   . 'if( top.fsMod ) {'
                                   . self::$_NL
                                   . '    top.fsMod.recentIds[ \'' . $this->_moduleSection . '\' ] = 0;'
                                   . self::$_NL
                                   . '}'
                                   . self::$_NL
                                   . '// ]]>';
            
            $postCode->addTextData( $jsCodeData );
            
            $this->doc->postCode   = ( string )$postCode . self::$_NL . $contextMenuParts[ 2 ];
            
            $this->_content->comment( 'Start of module content' );
            
            $this->_getModuleContent( $this->_content->div );
            
            $this->_content->comment( 'End of module content' );
        }
    }
}
