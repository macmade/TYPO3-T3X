<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2009 Jean-David Gadina (macmade@eosgarden.com)
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
require_once( PATH_tslib . 'class.tslib_pibase.php' );

/**
 * Abstract class for the TYPO3 frontend plugins
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
abstract class tx_oop_Plugin_Base extends tslib_pibase
{
    /**
     * The method used to get the plugin content, which has to be declared
     * in the child classes
     */
    abstract protected function _getPluginContent( tx_oop_Xhtml_Tag $content );
    
    /**
     * A flag to know wether the needed static variables are set
     */
    private static $_hasStatic  = false;
    
    /**
     * The instance of the database object (tx_oop_Database_Layer)
     */
    protected static $_db       = NULL;
    
    /**
     * A reference to the t3lib_DB object
     */
    protected static $_t3Db     = NULL;
    
    /**
     * 
     */
    protected static $_t3Lang   = NULL;
    
    /**
     * A reference to the TCA description array
     */
    protected static $_tcaDescr = array();
    
    /**
     * A reference to the TCA array
     */
    protected static $_tca      = array();
    
    /**
     * A reference to the client informations array
     */
    protected static $client    = array();
    
    /**
     * A reference to the TYPO3 configuration variables array
     */
    protected static $_t3Conf   = array();
    
    /**
     * The ASCII new line character
     */
    protected static $_NL       = '';
    
    /**
     * The ASCII tabulation character
     */
    protected static $_TAB      = '';
    
    /**
     * 
     */
    protected $_lang            = NULL;
    
    /**
     * A reflection object for the backend module
     */
    private $_reflection        = NULL;
    
    /**
     * 
     */
    private $_content           = NULL;
    
    /**
     * The module number
     */
    private $_pluginNumber      = 0;
    
    /**
     * The name of the module (child) class
     */
    private $_pluginClass       = '';
    
    /**
     * 
     */
    private $_extKey            = '';
    
    /**
     * 
     */
    private $_extDir            = '';
    
    /**
     * 
     */
    private $_uploadDirectory   = '';
    
    /**
     * 
     */
    public function __construct()
    {
        $this->_pluginClass = get_class( $this );
        
        $this->_reflection = new ReflectionObject( $this );
        
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $this->_pluginNumber  = substr( $this->_pluginClass, -1 );
        
        $extPathInfo = explode( DIRECTORY_SEPARATOR, $this->_reflection->getFileName() );
        
        array_pop( $extPathInfo );
        array_pop( $extPathInfo );
        
        $this->_extKey = array_pop( $extPathInfo );
        
        $this->_extDir = t3lib_extMgm::extPath( $this->_extKey );
        
        $this->_uploadDirectory = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->_extKey ) . '/' )
        );
        
        $this->_lang = tx_oop_Lang_Getter::getInstance( 'EXT:' . $this->_extKey . '/lang/pi' . $this->_moduleNumber . '.xml' );
        
        $this->_content = new tx_oop_Xhtml_Tag( 'div' );
    }
    
    /**
     * 
     */
    private static function _setStaticVars()
    {
        self::$_db        =  tx_oop_Database_Layer::getInstance();
        self::$_t3Db      =  $GLOBALS[ 'TYPO3_DB' ];
        self::$_t3Lang    =  $GLOBALS[ 'LANG' ];
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
    public function main()
    {
        return ( string )$this->_content;
    }
}
