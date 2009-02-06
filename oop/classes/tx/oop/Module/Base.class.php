<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 eosgarden - Jean-David Gadina (macmade@eosgarden.com)               #
# All rights reserved                                                          #
#                                                                              #
# This script is part of the TYPO3 project. The TYPO3 project is free          #
# software. You can redistribute it and/or modify it under the terms of the    #
# GNU General Public License as published by the Free Software Foundation,     #
# either version 2 of the License, or (at your option) any later version.      #
#                                                                              #
# The GNU General Public License can be found at:                              #
# http://www.gnu.org/copyleft/gpl.html.                                        #
#                                                                              #
# This script is distributed in the hope that it will be useful, but WITHOUT   #
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or        #
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for    #
# more details.                                                                #
#                                                                              #
# This copyright notice MUST APPEAR in all copies of the script!               #
################################################################################

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
abstract class tx_oop_Module_Base extends t3lib_SCbase
{
    /**
     * The method used to get the module content, which has to be declared
     * in the child classes
     */
    abstract protected function _getModuleContent( tx_oop_Xhtml_Tag $content );
    
    abstract protected function _setMenuItems( array &$items );
    
    /**
     * A flag to know wether the needed static variables are set
     */
    private static $_hasStatic   = false;
    
    /**
     * Whether the jQuery JS framework has been included
     */
    private static $_hasJQuery               = false;
    
    /**
     * Whether the jQuery UI framework has been included
     */
    private static $_hasJQueryUi             = false;
    
    /**
     * Whether the Mootools JS framework has been included
     */
    private static $_hasMootools             = false;
    
    /**
     * Whether the Prototype JS framework has been included
     */
    private static $_hasPrototype            = false;
    
    /**
     * Whether the Scriptaculous JS framework has been included
     */
    private static $_hasScriptaculous        = false;
    
    /**
     * The webtoolkit scripts that have been included
     */
    private static $_webtoolkitLoadedScripts = array();
    
    /**
     * The jQuery plugins that have been included
     */
    private static $_jQueryLoadedPlugins     = array();
    
    /**
     * The dependancies for the jQuery plugins
     */
    private static $_jQueryPluginsDeps       = array(
        'accordion' => array(
            'dimensions'
        )
    );
    
    /**
     * The instance of the database object (tx_oop_Database_Layer)
     */
    protected static $_db        = NULL;
    
    /**
     * A reference to the t3lib_DB object
     */
    protected static $_t3Db      = NULL;
    
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
     * 
     */
    protected $_lang             = NULL;
    
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
    protected $_pageInfos        = array();
    
    /**
     * 
     */
    protected $_modVars          = array();
    
    /**
     * 
     */
    protected $_modData          = NULL;
    
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
        
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $this->_moduleClass   = get_class( $this );
        $this->_reflection    = new ReflectionObject( $this );
        $this->_moduleName    = $GLOBALS[ 'MCONF' ][ 'name' ];
        $this->_moduleNumber  = substr( $this->_moduleName, -1 );
        $this->_moduleSection = substr( $this->_moduleName, 0, strpos( $this->_moduleName, '_' ) );
        
        $this->_backPath = $GLOBALS[ 'BACK_PATH' ];
        
        $this->_modVars = t3lib_div::_GP( $this->_moduleName );
        
        foreach( $this->include_once as $includeFile ) {
            
            require_once( $includeFile );
        }
        
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
        
        $this->_lang               = tx_oop_Lang_Getter::getInstance( 'EXT:' . $this->_extKey . '/lang/mod' . $this->_moduleNumber . '.xml' );
        $this->_modData            = tx_oop_Module_Data::getInstance( $this->_moduleName );
        $this->_content            = new tx_oop_Xhtml_Tag( 'div' );
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
        self::$_db        =  tx_oop_Database_Layer::getInstance();
        self::$_t3Db      =  $GLOBALS[ 'TYPO3_DB' ];
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
     * 
     */
    private function _addJs( $path )
    {
        $js                 = new tx_oop_Xhtml_Tag( 'script' );
        $js[ 'type' ]       = 'text/javascript';
        $js[ 'charset' ]    = 'utf-8';
        $js[ 'src' ]        = $this->_backPath . $this->_extRelativePath . $path;
        
        $this->doc->JScode .= self::$_NL . $js;
    }
    
    /**
     * Creates a thumbnail
     * 
     * @param   string  $file       The file path (relative to the upload directory)
     * @param   int     $width      The thumbnail maximum width
     * @param   int     $height     The thumbnail maximum height
     * @return  tx_oop_Xhtml_Tag    The thumbnail picture
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
        $img          = tx_oop_Xhtml_Tag( 'img' );
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
     * @return  tx_oop_Xhtml_Tag        The link
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
        $link              = new tx_oop_Xhtml_Tag( 'a' );
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
     * @return  tx_oop_Xhtml_Tag    The link
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
        $link              = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'href' ]    = $url;
        
        // Adds the linked text
        $link->addTextData( $text );
        
        // Returns the link
        return $link;
    }
    
    /**
     * Creates a link to the edit view of a record
     * 
     * @param   string  $table      The table name
     * @param   int     $uid        The ID of the record
     * @param   string  $text       The link text
     * @return  tx_oop_Xhtml_Tag    The link
     */
    protected function _editLink( $table, $uid, $text )
    {
        // On click action
        $onClick = t3lib_BEfunc::editOnClick(
            '&edit[' . $table . '][' . $uid . ']=edit',
            $this->_backPath
        );
        
        // Creates the full link
        $link              = new tx_oop_Xhtml_Tag( 'a' );
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
     * @param   string  $name       The icon name
     * @param   array               The align parameter of the IMG tag
     * @return  tx_oop_Xhtml_Tag    The image tag
     */
    protected function _skinImg( $name, $align = 'bottom' )
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
            $img             = new tx_oop_Xhtml_Tag( 'img' );
            $img[ 'src' ]    = $this->_backPath . $this->_extRelativePath . 'res/img/' . $name;
            $img[ 'width' ]  = $size[ 0 ];
            $img[ 'height' ] = $size[ 1 ];
            $img[ 'alt' ]    = $name;
            $img[ 'hspace' ] = '0';
            $img[ 'vspace' ] = '0';
            $img[ 'border' ] = '0';
            $img[ 'align' ]  = $align;
            
            // Returns the image tag
            return $img;
        }
        
        // File not found
        return '';
    }
    
    /**
     * Gets an icon from TYPO3
     * 
     * @param   string  $name       The icon name
     * @param   array               The align parameter of the IMG tag
     * @return  tx_oop_Xhtml_Tag    The image tag
     */
    protected function _t3SkinImg( $name, $align = 'bottom' )
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
            $img             = new tx_oop_Xhtml_Tag( 'img' );
            $img[ 'src' ]    = $imgSrc;
            $img[ 'width' ]  = $size[ 0 ];
            $img[ 'height' ] = $size[ 1 ];
            $img[ 'alt' ]    = $name;
            $img[ 'hspace' ] = '0';
            $img[ 'vspace' ] = '0';
            $img[ 'border' ] = '0';
            $img[ 'align' ]  = $align;
            
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
        $link              = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'href' ]    = $url;
        
        // Adds the button image
        $link->addTextData( $img );
        
        // Adds the button
        $this->_buttons[ $name ] = $link;
    }
    
    /**
     * Gets the CSM menu for a record
     * 
     * This method creates an icon of the requested record with a
     * Context Sensitive Menu (CSM).
     * 
     * @param   string              The table of the record
     * @param   array               The record's row
     * @param   array               The align parameter of the IMG tag
     * @return  tx_oop_Xhtml_Tag    The icon with a CSM menu link
     */
    protected function _recordIcon( $table, $row, $align = 'bottom' )
    {
        // Ensures the record's row is an array (may be an stdClass object)
        $row = ( array )$row;
        
        // Gets the record icon
        $icon = t3lib_iconWorks::getIconImage(
            $table,
            $row,
            $this->_backPath,
            'align="' . $bottom . '"'
        );
        
        // On click action
        $onClick = $this->doc->wrapClickMenuOnIcon(
            $icon,
            $table,
            $row[ 'uid' ],
            1,
            '',
            '',
            true
        );

        // Creates the link
        $link              = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'onclick' ] = htmlspecialchars( $onClick );
        
        // Checks if we have to add the context menu event
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'BE' ][ 'useOnContextMenuHandler' ] )
            && $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'BE' ][ 'useOnContextMenuHandler' ]
        ) {
            
            // Adds the context menu event
            $link[ 'oncontextmenu' ] = htmlspecialchars( $onClick );
        }
        
        // Adds the icon to the link
        $link->addTextData( $icon );
        
        // Returns the link
        return $link;
    }
    
    protected function _editIcon( $table, $id, $align = 'bottom' )
    {
        $icon              = $this->_t3SkinImg( 'gfx/edit2.gif', $align );
        $link              = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'href' ]    = '#';
        $link[ 'onclick' ] = htmlspecialchars(
            t3lib_BEfunc::editOnClick(
                '&edit[' . $table . '][' . $id . ']=edit',
                $this->_backPath
            )
        );
        
        $link->addTextData( $icon );
        
        return $link;
    }
    
    protected function _infoIcon( $table, $id, $align = 'bottom' )
    {
        $icon              = $this->_t3SkinImg( 'gfx/zoom2.gif', $align );
        $link              = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'href' ]    = '#';
        $link[ 'onclick' ] = 'top.launchView( \''
                           .  $table
                           .  '\', \''
                           .  $id
                           .  '\' ); return false;';
        
        $link->addTextData( $icon );
        
        return $link;
    }
    
    protected function _deleteIcon( $table, $id, $confirmDialog = true, $align = 'bottom' )
    {
        $icon              = $this->_t3SkinImg( 'gfx/garbage.gif', $align );
        $link              = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'href' ]    = htmlspecialchars(
            $this->doc->issueCommand(
                '&cmd[' . $table . ']['. $id .'][delete]=1'
            )
        );
        
        if( $confirmDialog ) {
            
            $link[ 'onclick' ] = htmlspecialchars(
                'return confirm( \''
              . self::$_t3Lang->sL( 'LLL:EXT:lang/locallang_alt_doc.xml:deleteWarning' )
              . '\' );'
            );
        }
        
        $link->addTextData( $icon );
        
        return $link;
    }
    
    /**
     * Gets the label for a database field
     * 
     * @param   string  The table name
     * @param   string  The field name
     * @return  string  The label
     */
    protected function _getFieldLabel( $table, $field )
    {
        // Checks if the table exists in the TCA
        if( !isset( $GLOBALS[ 'TCA' ][ $table ] ) ) {
            
            // The table does not exist
            return '';
        }
        
        // Checks if the TCA is loaded
        if( !isset( $GLOBALS[ 'TCA' ][ $table ][ 'columns' ] ) ) {
            
            // Loads the TCA
            t3lib_div::loadTCA( $table );
        }
        
        // Checks if the field exists in the TCA
        if( !isset( $GLOBALS[ 'TCA' ][ $table ][ 'columns' ][ $field ] ) ) {
            
            // The field does not exist
            return '';
        }
        
        // Returns the label
        return self::$_t3Lang->sL( $GLOBALS[ 'TCA' ][ $table ][ 'columns' ][ $field ][ 'label' ] );
    }
    
    
    
    /**
     * Creates a select menu
     * 
     * @param   string              The name of the select
     * @param   string              The label for the select menu
     * @param   array               The items to place on the menu, as value/label pairs
     * @return  tx_oop_Xhtml_Tag    The select menu
     */
    protected function _createSelect( $name, $label, array $items )
    {
        // Creates the container DIV
        $container            = new tx_oop_Xhtml_Tag( 'div' );
        $container[ 'style' ] = 'overflow: hidden; height: 100%;';
        
        // Creates the label and it's container DIV
        $labelDiv             = $container->div;
        $labelDiv[ 'style' ]  = 'float: left; width: 150px; font-weight: bold;';
        $label                = $labelDiv->label;
        $label[ 'for' ]       = $this->_moduleName . '-' . $name;
        
        // Creates the select and it's container DIV
        $selectDiv            = $container->div;
        $selectDiv[ 'style' ] = 'float: left;';
        $select               = $selectDiv->select;
        $select[ 'name' ]     = $this->_moduleName . '[' . $name . ']';
        $select[ 'id' ]       = $this->_moduleName . '-' . $name;
        
        // Process the items
        foreach( $items as $value => $label ) {
            
            // Creates the option tag
            $option            = $select->option;
            $option[ 'value' ] = $value;
            
            // Checks the module variables
            if( $this->_modVars[ $name ] == $value ) {
                
                // The current item is selected
                $option[ 'selected' ] = 'selected';
            }
        }
        
        // Returns the full select
        return $container;
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
    protected function _getActiveMenuItem()
    {
        return $this->MOD_SETTINGS[ 'function' ];
    }
    
    /**
     * Includes the jQuery JS framework
     * 
     * @return  NULL
     */
    protected function _includeJQuery()
    {
        // Only includes the script once
        if( self::$_hasJQuery === false ) {
            
            // Adds the JS script
            $this->_addJs( 'jquery/jquery.js' );
            
            // Script has been included
            self::$_hasJQuery = true;
        }
    }
    
    /**
     * Includes the jQuery UI JS framework
     * 
     * @return  NULL
     */
    protected function _includeJQueryUi()
    {
        // Only includes the script once
        if( self::$_hasJQueryUi === false ) {
            
            // Adds the JS script
            $this->_addJs( 'jquery-ui/jquery-ui.js' );
            
            // Script has been included
            self::$_hasJQueryUi = true;
        }
    }
    
    /**
     * Includes the Mootools JS framework
     * 
     * @return  NULL
     */
    protected function _includeMootools()
    {
        // Only includes the script once
        if( self::$_hasMootools === false ) {
            
            // Adds the JS script
            $this->_addJs( 'mootools/mootools.js' );
            
            // Script has been included
            self::$_hasMootools = true;
        }
    }
    
    /**
     * Includes the Prototype JS framework
     * 
     * @return  NULL
     */
    protected function _includePrototype()
    {
        // Only includes the script once
        if( self::$_hasPrototype === false ) {
            
            // Adds the JS script
            $this->_addJs( 'prototype/prototype.js' );
            
            // Script has been included
            self::$_hasPrototype = true;
        }
    }
    
    /**
     * Includes the Scriptaculous JS framework
     * 
     * @return  NULL
     * @see     _includePrototype
     */
    protected function _includeScriptaculous()
    {
        // Only includes the script once
        if( self::$_hasScriptaculous === false ) {
            
            // Includes the Prototype JS framework
            $this->_includePrototype();
            
            // Adds the JS script
            $this->_addJs( 'scriptaculous/scriptaculous.js' );
            
            // Script has been included
            self::$_hasScriptaculous = true;
        }
    }
    
    /**
     * Includes a Webtoolkit script
     * 
     * Available scripts are:
     * - base64
     * - crc32
     * - md5
     * - sha1
     * - sha256
     * - url
     * - utf8
     * 
     * @param   string  The name of the script to include
     * @return  NULL
     */
    protected function _includeWebtoolkitScript( $script )
    {
        // Only includes the script once
        if( !isset( self::$_webtoolkitLoadedScripts[ $script ] ) ) {
            
            // Adds the JS script
            $this->_addJs( 'webtoolkit/' . $script . '.js' );
            
            // Script has been included
            self::$_webtoolkitLoadedScripts[ $script ] = true;
        }
    }
    
    /**
     * Includes a jQuery plugin
     * 
     * Available plugins are:
     * - accordion
     * - dimensions
     * 
     * @param   string  The name of the plugin to include
     * @return  NULL
     */
    protected function _includeJQueryPlugin( $plugin )
    {
        // Only includes the script once
        if( !isset( self::$_jQueryLoadedPlugins[ $plugin ] ) ) {
            
            // Checks for dependancies
            if( isset( self::$_jQueryPluginsDeps[ $plugin ] ) ) {
                
                // Process each dependancy
                foreach( self::$_jQueryPluginsDeps[ $plugin ] as $deps ) {
                    
                    // Includes the plugin
                    $this->_includeJQueryPlugin( $deps );
                }
            }
            
            // Adds the JS script
            $this->_addJs( 'jquery/jquery' . $plugin . '.js' );
            
            // Script has been included
            self::$_jQueryLoadedPlugins[ $plugin ] = true;
        }
    }
    
    /**
     * 
     */
    public function menuConfig()
    {
        $this->MOD_MENU = array(
            'function' => array()
        );
        
        $this->_setMenuItems( $this->MOD_MENU[ 'function' ] );
        
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
            $form                  = new tx_oop_Xhtml_Tag( 'form' );
            $form[ 'action' ]      = '';
            $form[ 'method' ]      = 'post';
            $form[ 'enctype' ]     = self::$_t3Conf[ 'SYS' ][ 'form_enctype' ];
            $form[ 'name' ]        = $this->_moduleName . '_form';
            $form[ 'id' ]          = $this->_moduleName . '_form';
            
            $this->doc->form       = ( string )$form;
            
            $jsCode                = new tx_oop_Xhtml_Tag( 'script' );
            $jsCode[ 'type' ]      = 'text/javascript';
            $jsCode[ 'charset' ]   = 'utf-8';
            $jsCodeData            = '// <![CDATA['
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
            
            $postCode              = new tx_oop_Xhtml_Tag( 'script' );
            $postCode[ 'type' ]    = 'text/javascript';
            $postCode[ 'charset' ] = 'utf-8';
            
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
