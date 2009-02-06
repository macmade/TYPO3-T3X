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

// Includes the TYPO3 plugin base classe
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
     * 
     */
    private static $_oopPath                 = '';
    
    /**
     * 
     */
    private static $_oopRelativePath         = '';
    
    /**
     * 
     */
    private static $_oopSiteRelativePath     = '';
    
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
    protected static $_db                    = NULL;
    
    /**
     * The instance of the string utilities class (tx_oop_String_Utils)
     */
    protected static $_str                   = NULL;
    
    /**
     * A reference to the t3lib_DB object
     */
    protected static $_t3Db                  = NULL;
    
    /**
     * 
     */
    protected static $_t3Lang                = NULL;
    
    /**
     * A reference to the TCA description array
     */
    protected static $_tcaDescr              = array();
    
    /**
     * A reference to the TCA array
     */
    protected static $_tca                   = array();
    
    /**
     * A reference to the client informations array
     */
    protected static $_client                = array();
    
    /**
     * A reference to the TYPO3 configuration variables array
     */
    protected static $_t3Conf                = array();
    
    /**
     * 
     */
    private $_templateContent                = NULL;
    
    /**
     * 
     */
    private $_cssPrefix                      = '';
    
    /**
     * 
     */
    protected $_lang                         = NULL;
    
    /**
     * A reflection object for the frontend plugin
     */
    private $_reflection                     = NULL;
    
    /**
     * 
     */
    private $_content                        = NULL;
    
    /**
     * The plugin number
     */
    private $_pluginNumber                   = 0;
    
    /**
     * 
     */
    private $_extDir                         = '';
    
    /**
     * 
     */
    private $_uploadDirectory                = '';
    
    /**
     * 
     */
    protected $_time                         = 0;
    
    /**
     * 
     */
    protected $_url                          = '';
    
    /**
     * The class name (needed by tslib_piBase)
     */
    public $prefixId                         = '';
    
    /**
     * The script path, relative to the extension(needed by tslib_piBase)
     */
    public $scriptRelPath                    = '';
    
    /**
     * The extension key (needed by tslib_piBase)
     */
    public $extKey                           = '';
    
    /**
     * 
     */
    public $conf                             = array();
    
    /**
     * 
     */
    public function __construct()
    {
        $this->prefixId = get_class( $this );
        
        $this->_cssPrefix = str_replace( '_', '-', $this->prefixId );
        
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
        
        $this->_content            = new tx_oop_Xhtml_Tag( 'div' );
        $this->_content[ 'class' ] = $this->_cssPrefix;
    }
    
    /**
     * 
     */
    private static function _setStaticVars()
    {
        self::$_db                  = tx_oop_Database_Layer::getInstance();
        self::$_str                 = tx_oop_String_Utils::getInstance();
        self::$_t3Db                = $GLOBALS[ 'TYPO3_DB' ];
        self::$_t3Lang              = $GLOBALS[ 'LANG' ];
        self::$_tcaDescr            = $GLOBALS[ 'TCA_DESCR' ];
        self::$_tca                 = $GLOBALS[ 'TCA' ];
        self::$_client              = $GLOBALS[ 'CLIENT' ];
        self::$_t3Conf              = $GLOBALS[ 'TYPO3_CONF_VARS' ];
        self::$_oopPath             = t3lib_extMgm::extPath( 'oop' );
        self::$_oopRelativePath     = t3lib_extMgm::extRelPath( 'oop' );
        self::$_oopSiteRelativePath = t3lib_extMgm::siteRelPath( 'oop' );
        self::$_hasStatic           = true;
    }
    
    /**
     * 
     */
    private function _addJs( $path )
    {
        $js                 = new tx_oop_Xhtml_Tag( 'script' );
        $js[ 'type' ]       = 'text/javascript';
        $js[ 'charset' ]    = 'utf-8';
        $js[ 'src' ]        = self::$_oopSiteRelativePath . 'res/javascript/' . $path;
        
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ 'tx_oop_' . $path ] = ( string )$js;
    }
    
    /**
     * 
     */
    protected function _cssClass( $name, tx_oop_Xhtml_Tag $tag = NULL )
    {
        if( $tag ) {
            
            $tag[ 'class' ] = $this->_cssPrefix . '-' . $name;
            
        } else {
            
            return $this->_cssPrefix . '-' . $name;
        }
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
     * Creates a select menu with countries from the 'static_countries' table
     * 
     * @param   string              The name of the select
     * @param   boolean             Wheter to add an empty option at start
     * @return  tx_oop_Xhtml_Tag    The select menu with the countries
     */
    protected function _countrySelect( $name, $emptyOptionAtStart = true )
    {
        $select = new tx_oop_Xhtml_Tag( 'select' );
        $select[ 'name' ] = $this->prefixId . '[' . $name . ']';
        $select[ 'size' ] = 1;
        
        if( $emptyOptionAtStart ) {
            
            $empty            = $select->option;
            $empty[ 'value' ] = '';
        }
        
        // SQL instruction to select the countries
        $sql = 'SELECT uid,cn_short_en FROM static_countries WHERE uid ORDER BY cn_short_en';
        
        // Prepares the PDO query
        $query = self::$_db->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( array() );
        
        // Process each row
        while( $row = $query->fetchObject() ) {
            
            // Adds an option tag
            $option            = $select->option;
            $option[ 'value' ] = $row->uid;
            $option->addTextData( $row->cn_short_en );
        }
        
        // Returns the select box
        return $select;
    }
    
    /**
     * Establish a frontend user session
     * 
     * @param   array   The FE user row from the database
     * @return  NULL
     */
    protected function _feLogin( $user )
    {
        // Ensures the user variable is an array (may be an object with PDO)
        $user = ( array )$user;
        
        // Fills POST variables with login infos
        $_POST[ 'logintype' ] = 'login';
        $_POST[ 'user' ]      = $user[ 'username' ];
        $_POST[ 'pass' ]      = $user[ 'password' ];
        $_POST[ 'pid' ]       = $user[ 'pid' ];
        
        // Initializes the FE user
        $GLOBALS[ 'TSFE' ]->initFEuser();
        
        // Cleans up the POST variables
        unset( $_POST[ 'logintype' ] );
        unset( $_POST[ 'user' ] );
        unset( $_POST[ 'pass' ] );
        unset( $_POST[ 'pid' ] );
    }
    
    /**
     * Creates a thumbnail
     * 
     * @param   string  The file path (relative to the upload directory). May be a comma list of path
     * @param   int     The thumbnail maximum width
     * @param   int     The thumbnail maximum height
     * @param   boolean Wheter to add a link to display the original image in a new window
     * @param   int     The maximum width of the new window, if the $newWindow is set
     * @return  string  The thumbnail picture(s)
     */
    protected function _createThumbnail( $file, $width = 100, $height = 100, $newWindow = false, $newWindowWidth = 800 )
    {
        $tsConf = array(
            'file.' => array(
                'maxW' => $width,
                'maxH' => $height
            )
        );
        
        if( $newWindow ) {
            
            $tsConf[ 'imageLinkWrap' ]                             = 1;
            $tsConf[ 'imageLinkWrap.' ]                            = array();
            $tsConf[ 'imageLinkWrap.' ][ 'enable' ]                = 1;
            $tsConf[ 'imageLinkWrap.' ][ 'wrap' ]                  = '<a href="javascript:self.close();">|</a>';
            $tsConf[ 'imageLinkWrap.' ][ 'width' ]                 = $newWindowWidth . 'm';
            $tsConf[ 'imageLinkWrap.' ][ 'JSwindow' ]              = 1;
            $tsConf[ 'imageLinkWrap.' ][ 'JSwindow.' ]             = array();
            $tsConf[ 'imageLinkWrap.' ][ 'JSwindow.' ][ 'expand' ] = '20,20';
        }
        
        $paths   = explode( ',', $file );
        $content = '';
        
        foreach( $paths as $path ) {
            
            $tsConf[ 'file' ] = $this->_uploadDirectory . $path;
            $content         .= $this->cObj->IMAGE( $tsConf );
        }
        
        return $content;
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
