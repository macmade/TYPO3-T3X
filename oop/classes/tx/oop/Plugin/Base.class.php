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
     * The instance of the string utilities class (tx_oop_String_Utils)
     */
    protected static $_str       = NULL;
    
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
    protected static $_client   = array();
    
    /**
     * A reference to the TYPO3 configuration variables array
     */
    protected static $_t3Conf   = array();
    
    /**
     * 
     */
    private $_templateContent   = NULL;
    
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
    protected $_time            = 0;
    
    /**
     * 
     */
    protected $_url             = '';
    
    /**
     * The class name (needed by tslib_piBase)
     */
    public $prefixId            = '';
    
    /**
     * The script path, relative to the extension(needed by tslib_piBase)
     */
    public $scriptRelPath       = '';
    
    /**
     * The extension key (needed by tslib_piBase)
     */
    public $extKey              = '';
    
    /**
     * 
     */
    public $conf                = array();
    
    /**
     * 
     */
    public function __construct()
    {
        $this->prefixId = get_class( $this );
        
        $this->_reflection = new ReflectionObject( $this );
        
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        $this->_pluginNumber  = substr( $this->prefixId, -1 );
        
        $extPathInfo = explode( DIRECTORY_SEPARATOR, $this->_reflection->getFileName() );
        
        $pluginFile = array_pop( $extPathInfo );
        $pluginDir  = array_pop( $extPathInfo );
        
        $this->scriptRelPath = $pluginDir . '/' . $pluginFile;
        
        $this->extKey  = array_pop( $extPathInfo );
        
        $this->_extDir = t3lib_extMgm::extPath( $this->extKey );
        
        $this->_uploadDirectory = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->extKey ) . '/' )
        );
        
        $this->_url = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        $this->_time = time();
        
        $this->_lang = tx_oop_Lang_Getter::getInstance( 'EXT:' . $this->extKey . '/lang/pi' . $this->_pluginNumber . '.xml' );
        
        $this->_content = new tx_oop_Xhtml_Tag( 'div' );
    }
    
    /**
     * 
     */
    private static function _setStaticVars()
    {
        self::$_db        = tx_oop_Database_Layer::getInstance();
        self::$_str       = tx_oop_String_Utils::getInstance();
        self::$_t3Db      = $GLOBALS[ 'TYPO3_DB' ];
        self::$_t3Lang    = $GLOBALS[ 'LANG' ];
        self::$_tcaDescr  = $GLOBALS[ 'TCA_DESCR' ];
        self::$_tca       = $GLOBALS[ 'TCA' ];
        self::$_client    = $GLOBALS[ 'CLIENT' ];
        self::$_t3Conf    = $GLOBALS[ 'TYPO3_CONF_VARS' ];
        self::$_hasStatic = true;
    }
    
    /**
     * Loads a template file.
     * 
     * This function reads a template file and store it as a
     * C-Object.
     * 
     * @param   string  The path of the template file to load
     * @return  NULL
     */
    protected function _initTemplate( $templateFilePath )
    {
        // Loads and stores the template file
        $this->_templateContent = $this->cObj->fileResource( $templateFilePath );
    }
    
    /**
     * Renders a template section.
     * 
     * This function analyzes the template C-Object, previously set by
     * the _initTemplate() method and substitute the specified section with
     * the specified subsections.
     * 
     * @param   array                           The markers array
     * @param   string                          The name of the section to substitute
     * @return  string                          The processed template section
     * @throws  tx_oop_Plugin_Base_Exception    If the template is not loaded
     */
    protected function _renderTemplate( array $templateMarkers, $templateSection )
    {
        // Checks if the template is loaded
        if( !$this->_templateContent ) {
            
            // The template object is not loaded
            throw new tx_oop_Plugin_Base_Exception(
                'The template object does not seem to be loaded. Please load it with the ' . __CLASS__ . '::_initTemplate() method first.',
                tx_oop_Plugin_Base_Exception::EXCEPTION_TEMPLATE_NOT_LOADED
            );
        }
            
        // Gets the template subparts
        $subpart = $this->cObj->getSubpart(
            $this->_templateContent,
            $templateSection
        );
        
        // Returns the substituted section
        return $this->cObj->substituteMarkerArrayCached(
            $subpart,
            array(),
            $templateMarkers,
            array()
        );
    }
    
    /**
     * 
     */
    public function main( $content, $conf )
    {
        // Stores the TypoScript configuration
        $this->conf = $conf;
        
        // Sets the default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Initialize the flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Creates the plugin content (from the child class)
        $this->_getPluginContent( $this->_content );
        
        // Returns the plugin content
        return ( string )$this->_content;
    }
}
