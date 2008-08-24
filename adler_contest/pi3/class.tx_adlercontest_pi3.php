<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 macmade.net
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
 * Plugin 'Registration' for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the TYPO3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Includes the method provider class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_methodprovider.php' );

// Includes the macmade.net API class
require_once ( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_adlercontest_pi3 extends tslib_pibase
{
    /**
     * The database object
     */
    protected static $_db       = NULL;
    
    /**
     * The method provider object
     */
    protected static $_mp       = NULL;
    
    /**
     * Database tables
     */
    protected static $_dbTables = array(
        'users'     => 'fe_users',
        'profiles'  => 'tx_adlercontest_users',
    );
    
    /**
     * The new line character
     */
    protected static $_NL       = '';
    
    /**
     * The TYPO3 site URL
     */
    protected static $_typo3Url = '';
    
    /**
     * The instance of the Developer API
     */
    protected $_api             = NULL;
    
    /**
     * The TypoScript configuration array
     */
    protected $_conf            = array();
    
    /**
     * The flexform data
     */
    protected $_piFlexForm      = '';
    
    /**
     * The upload directory
     */
    protected $_uploadDirectory = '';
    
    /**
     * Current URL
     */
    protected $_url             = '';
    
    /**
     * The current date
     */
    protected $_currentDate     = '';
    
    /**
     * The class name
     */
    public $prefixId            = 'tx_adlercontest_pi3';
    
    /**
     * The path to this script relative to the extension directory
     */
    public $scriptRelPath       = 'pi3/class.tx_adlercontest_pi3.php';
    
    /**
     * The extension key
     */
    public $extKey              = 'adler_contest';
    
    /**
     * Wether to check plugin hash
     */
    public $pi_checkCHash       = true;
    
    /**
     * The required version of the macmade.net API
     */
    public $apimacmade_version  = 4.5;
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_tscobj_pi3', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param   string  $content    The plugin content
     * @param   array   $conf       The TS setup
     * @return  string  The content of the plugin
     * @see     _userProfile
     * @see     _uploadDocuments
     * @see     _registrationForm
     */
    public function main( $content, array $conf )
    {
        // Checks if the new line character is already set
        if( !self::$_NL ) {
            
            // Sets the new line character
            self::$_NL = chr( 10 );
        }
        
        // Checks if the site URL is already set
        if( !self::$_typo3Url ) {
            
            // Sets the site URL
            self::$_typo3Url = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' );
        }
        
        // Checks if the DB object already exists
        if( !is_object( self::$_db ) ) {
            
            // Gets a reference to the database object
            self::$_db = $GLOBALS[ 'TYPO3_DB' ];
        }
        
        // Checks if the DB object already exists
        if( !is_object( self::$_mp ) ) {
            
            // Gets a reference to the database object
            self::$_mp = tx_adlercontest_methodProvider::getInstance();
        }
        
        // Stores the TypoScript configuration
        $this->_conf            = $conf;
        
        // Gets the current URL
        $this->_url             = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        // Gets the current date
        $this->_currentDate     = time();
        
        // Sets the upload directory
        $this->_uploadDirectory = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->extKey ) )
        );
        
        // Gets a new instance of the macmade.net API
        $this->_api             = tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                $this
            )
        );
        
        // Sets the default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Loads the LOCAL_LANG values
        $this->pi_loadLL();
        
        // Initialize the flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Stores the flexform informations
        $this->_piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Sets the final configuration (TS or FF)
        $this->_setConfig();
        
        // Initialize the template object
        $this->_api->fe_initTemplate( $this->_conf[ 'templateFile' ] );
        
        return __CLASS__;
    }
    
    /**
     * Sets the configuration array
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return  NULL
     */
    protected function _setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array(
            'pid' => 'sDEF:pages'
        );
        
        // Ovverride TS setup with flexform
        $this->_conf = $this->_api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->_conf,
            $this->_piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->_api->debug( $this->_conf, $this->prefixId . ': configuration array' );
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3.php']);
}
