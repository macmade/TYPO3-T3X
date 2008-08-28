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
 * Frontend plugin base for the 'adler_contest' extension.
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

abstract class tx_adlercontest_piBase extends tslib_pibase
{
    /**
     * The method used to get the plugin content
     */
    abstract protected function _getContent();
    
    /**
     * Wether the static variables are set or not
     */
    private static $_hasStatic         = false;
    
    /**
     * Wether the plugin script has been included or not
     */
    private static $_scriptIncluded    = false;
    
    /**
     * The extension key
     */
    private static $_extKey            = '';
    
    /**
     * The database object
     */
    protected static $_db              = NULL;
    
    /**
     * The method provider object
     */
    protected static $_mp              = NULL;
    
    /**
     * The TypoScript frontend object
     */
    protected static $_tsfe            = NULL;
    
    /**
     * The upload directory
     */
    protected static $_uploadDirectory = '';
    
    /**
     * Database tables
     */
    protected static $_dbTables        = array(
        'users'     => 'fe_users',
        'profiles'  => 'tx_adlercontest_users',
        'votes'     => 'tx_adlercontest_votes'
    );
    
    /**
     * The new line character
     */
    protected static $_NL              = '';
    
    /**
     * The TYPO3 site URL
     */
    protected static $_typo3Url        = '';
    
    /**
     * The instance of the macmade.net API
     */
    protected $_api                    = NULL;
    
    /**
     * The TypoScript configuration array
     */
    protected $_conf                   = array();
    
    /**
     * Storage for the form errors
     */
    protected $_errors                 = array(); 
    
    /**
     * Configuration mapping array (between TS and Flex)
     */
    protected $_configMap              = array();
    
    /**
     * The flexform data
     */
    protected $_piFlexForm             = '';
    
    /**
     * Current URL
     */
    protected $_url                    = '';
    
    /**
     * The current date
     */
    protected $_currentDate            = '';
    
    /**
     * The required version of the macmade.net API
     */
    public $apimacmade_version         = 4.5;
    
    /**
     * The plugin class name
     */
    public $prefixId                   = '';
    
    /**
     * The path to the plugin script relative to the extension directory
     */
    public $scriptRelPath              = '';
    
    /**
     * The extension key
     */
    public $extKey                     = '';
    
    /**
     * Wether to check plugin hash
     */
    public $pi_checkCHash              = true;
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Calls the parent constructor
        parent::__construct();
        
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
        }
        
        // Sets the plugin class name
        $this->prefixId         = get_class( $this );
        
        // Sets the extension key
        $this->extKey           = self::$_extKey;
        
        // Gets the plugin number
        $piNum                  = substr( $this->prefixId, strrpos( $this->prefixId, '_' ) + 1 );
        
        // Sets the relative path
        $this->scriptRelPath    = $piNum
                                . '/class.'
                                . $this->prefixId
                                . '.php';
        
        // Gets the current URL
        $this->_url             = t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' );
        
        // Gets the current date
        $this->_currentDate     = time();
        
        // Gets a new instance of the macmade.net API
        $this->_api             = tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                $this
            )
        );
    }
    
    /**
     * 
     */
    private static function _setStaticVars()
    {
        // Gets the path
        $extPathInfo      = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
        
        // Removes the 'pi#' directory
        array_pop( $extPathInfo );
        
        // Sets the extension key
        self::$_extKey    = array_pop( $extPathInfo );
        
        // Sets the new line character
        self::$_NL        = chr( 10 );
        
        // Sets the site URL
        self::$_typo3Url  = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' );
        
        // Gets a reference to the database object
        self::$_db        = $GLOBALS[ 'TYPO3_DB' ];
        
        // Gets a reference to the method provider object
        self::$_mp        = tx_adlercontest_methodProvider::getInstance();
        
        // Gets a reference to the TypoScript frontend object
        self::$_tsfe      = $GLOBALS[ 'TSFE' ];
        
        // Sets the upload directory
        self::$_uploadDirectory = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', self::$_extKey ) )
        );
        
        // Static variables are set
        self::$_hasStatic = true;
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
    private function _setConfig()
    {
        // Checks for configuration mapping infos
        if( count( $this->_configMap ) ) {
            
            // Ovverride TS setup with flexform
            $this->_conf = $this->_api->fe_mergeTSconfFlex(
                $this->_configMap,
                $this->_conf,
                $this->_piFlexForm
            );
        }
        
        // DEBUG ONLY - Output configuration array
        #$this->_api->debug( $this->_conf, $this->prefixId . ': configuration array' );
    }
    
    /**
     * Creates an HTML form tag
     * 
     * @param   string  $content    The content to put in the form tag (the fields)
     * @param   array   $keepPiVars An array with the name of the plugin variables to keep
     * @param   boolean $cache      If sets, the action URL will be cached
     * @param   string  $name       The name parameter of the form
     * @param   string  $class      The CSS class of the form
     * @return  string  The form tag
     */
    protected function _formTag( $content, array $keepPiVars = array(), $cache = false, $name = 'form', $class = 'form' )
    {
        // Storage for additional parameters
        $addParams = array();
        
        // Checks for plugin variables to keep
        if( count( $keepPiVars ) ) {
            
            // Process each variable
            foreach( $keepPiVars as $piVar ) {
                
                // Checks for a value
                if( !isset( $this->piVars[ $piVar ] ) ) {
                    
                    // No value
                    continue;
                }
                
                // Adds the variable to the additionnal parameters
                $addParams[] = '&' . $this->prefixId . '[' . $piVar . ']=' . $this->piVars[ $piVar ];
            }
        }
        
        // TypoLink parameters for the form action
        $typoLink = array(
            'parameter'        => self::$_tsfe->id,
            'useCacheHash'     => 1,
            'additionalParams' => implode( '', $addParams )
        );
        
        // Checks if the form must be uncached
        if( !$cache ) {
            
            // No cache
            $typoLink[ 'useCacheHash' ] = 0;
            $typoLink[ 'no_cache' ]     = 1;
        }
        
        // Returns the form tag
        return $this->_api->fe_makeStyledContent(
            'form',
            $class,
            $content,
            true,
            false,
            false,
            array(
                'method'  => $this->_conf[ 'formMethod' ],
                'enctype' => $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'form_enctype' ],
                'id'      => $this->prefixId . '_' . $name,
                'name'    => $this->prefixId . '_' . $name,
                'action'  => $this->cObj->typoLink_URL( $typoLink )
            )
        );
    }
    
    /**
     * Creates form fields
     * 
     * @param   array   $fields             An array with the fields to create
     * @param   string  $templateSection    The template section to render
     * @return  string  The form fields
     */
    protected function _formFields( array $fields, $templateSection )
    {
        // Template markers
        $markers = array();
        
        // Process each field
        foreach( $fields as $fieldName => $fieldOptions ) {
            
            // Error message, if any
            $error              = ( isset( $this->_errors[ $fieldName ] ) ) ? $this->_api->fe_makeStyledContent( 'div', 'form-error', $this->_errors[ $fieldName ] ) : '';
            
            // Marker for the template
            $marker             = '###' . strtoupper( $fieldName ) . '###';
            
            // ID of the input
            $inputId            = $this->prefixId . '_' . $fieldName;
            
            // Field label
            $label              = $this->_api->fe_makeStyledContent(
                'div',
                'label',
                '<label for="' . $inputId . '">' . $this->pi_getLL( 'label-' . $fieldName ) . '</label>'
            );
            
            // Checks the input type
            switch( $fieldOptions[ 'type' ] ) {
                
                // Country select
                case 'country':
                    
                    // Input tag
                    $input = $this->_api->fe_makeStyledContent(
                        'div',
                        'input',
                        self::$_mp->countrySelect( $this->prefixId . '[' . $fieldName . ']' )
                    );
                    break;
                
                // Date select
                case 'date':
                    
                    // Input tag
                    $input = $this->_api->fe_makeStyledContent(
                        'div',
                        'input',
                        self::$_mp->dateSelect( $this->prefixId . '[' . $fieldName . ']' )
                    );
                    break;
                
                // Radio buttons
                case 'radio':
                    
                    // Storage
                    $radios = array();
                    
                    // Checks for items
                    if( isset( $fieldOptions[ 'items' ] ) && is_array( $fieldOptions[ 'items' ] ) ) {
                        
                        // Process each item
                        foreach( $fieldOptions[ 'items' ] as $radioValue ) {
                            
                            // Label for the radio item
                            $radioLabel = $this->_api->fe_makeStyledContent(
                                'span',
                                'radio-label',
                                $this->pi_getLL( 'label-' . $fieldName . '-' . $radioValue, $radioValue )
                            );
                            
                            // Radio item
                            $radioItem  = $this->_api->fe_makeStyledContent(
                                'span',
                                'radio-item',
                                '<input type="radio" name="'
                              . $this->prefixId . '[' . $fieldName . ']'
                              . '" value="'
                              . $radioValue
                              . '" id="'
                              . $inputId
                              . '" />'
                            );
                            
                            // Adds the full radio item
                            $radios[]   = $radioLabel . ' ' . $radioItem;
                        }
                    }
                    
                    // Input tag
                    $input = $this->_api->fe_makeStyledContent(
                        'div',
                        'input',
                        implode( self::$_NL, $radios )
                    );
                    break;
                
                // Default processing
                default:
                    
                    // Input value
                    $value = ( isset( $this->piVars[ $fieldName ] ) && $fieldOptions[ 'type' ] === 'text' ) ? ' value="' . $this->piVars[ $fieldName ] . '"' : '';
                    
                    // Input tag
                    $input = $this->_api->fe_makeStyledContent(
                        'div',
                        'input',
                        '<input type="'
                      . $fieldOptions[ 'type' ]
                      . '" name="'
                      . $this->prefixId . '[' . $fieldName . ']'
                      . '" id="'
                      . $inputId
                      . '"'
                      . ( ( $fieldOptions[ 'type' ] === 'text' || $fieldOptions[ 'type' ] === 'password' ) ? ' size="' . $this->_conf[ 'inputSize' ] . '"' : '' )
                      . $value
                      . ' />'
                    );
                    break;
            }
            
            // Field with label
            $field              = $this->_api->fe_makeStyledContent(
                'div',
                'field',
                $label . $input
            );
            
            // Sets the template marker
            $markers[ $marker ] = $error . $field;
        }
        
        // Renders the template markers and returns the result
        return $this->_api->fe_renderTemplate( $markers, $templateSection );
    }
    
    /**
     * Validates a form
     * 
     * @param   array   $fields     The fields to validate
     * @param   array   $callbacks  An array with the callbacks for specific field checks
     * @return  boolean True if the form is valid, otherwise false
     */
    protected function _formValid( array $fields, array $callbacks = array() )
    {
        // Checks if the form has been submitted
        if( !isset( $this->piVars[ 'submit' ] ) ) {
            
            // Form has not been submitted
            return false;
        }
        
        // Default error
        $defaultError = $this->pi_getLL( 'error-required' );
        
        // Process each field
        foreach( $fields as $fieldName => $fieldOptions ) {
            
            // Checks if the field is optionnal
            if( isset( $fieldOptions[ 'optionnal' ] ) && $fieldOptions[ 'optionnal' ] ) {
                
                // Next field
                continue;
            }
            
            // Field specific error
            $fieldError = $this->pi_getLL( 'error-' . $fieldName );
            
            // Error message for the current field
            $error      = ( $fieldError ) ? $fieldError : $defaultError;
            
            // Special processing for the file inputs
            if( $fieldOptions[ 'type' ] === 'file' ) {
                
                // Checks if the field is empty
                if( !isset( $_FILES[ $this->prefixId ][ 'name' ][ $fieldName ] ) || empty( $_FILES[ $this->prefixId ][ 'name' ][ $fieldName ] ) ) {
                    
                    // Stores the error message
                    $this->_errors[ $fieldName ] = $error;
                }
                
            } else {
                
                // Checks if the field is empty
                if( !isset( $this->piVars[ $fieldName ] ) || empty( $this->piVars[ $fieldName ] ) ) {
                    
                    // Stores the error message
                    $this->_errors[ $fieldName ] = $error;
                }
            }
                
            // Checks if an error is already set
            if( isset( $this->_errors[ $fieldName ] ) ) {
                
                // Next field
                continue;
            }
            
            // Checks for a specific callback
            if( isset( $callbacks[ $fieldName ] ) ) {
                
                // Calls the callback function
                if( $error = $this->$callbacks[ $fieldName ]( $fieldName, $fieldOptions ) ) {
                    
                    // Stores the error
                    $this->_errors[ $fieldName ] = $error;
                }
            }
        }
        
        // Returns the check state
        return ( count( $this->_errors ) ) ? false : true;
    }
    
    /**
     * 
     */
    protected function _checkUploadType( $fieldName, array $fieldOptions )
    {
        // List of allowed extensions
        $allowedExt = explode( ',', $fieldOptions[ 'ext' ] );
        
        // File name
        $fileName   = $_FILES[ $this->prefixId ][ 'name' ][ $fieldName ];
        
        // File extension
        $fileExt    = strtolower( substr( $fileName, strrpos( $fileName, '.' ) + 1 ) );
        
        // Checks the file extension
        if( !in_array( $fileExt, $allowedExt ) ) {
            
            // Wrong extension
            return $this->pi_getLL( 'error-file-ext' );
        }
        
        // Checks the file size
        if( $_FILES[ $this->prefixId ][ 'size' ][ $fieldName ] > ( $fieldOptions[ 'size' ] * 1024 ) ) {
            
            // File to large
            return $this->pi_getLL( 'error-file-size' );
        }
        
        return false;
    }
    
    /**
     * 
     */
    protected function _submitButton()
    {
        return $this->_api->fe_makeStyledContent(
            'div',
            'submit',
            '<input name="'
          . $this->prefixId
          . '[submit]" id="'
          . $this->prefixId
          . '_submit" type="submit" value="'
          . $this->pi_getLL( 'submit', 'submit' )
          . '" />'
        );
    }
    
    /**
     * 
     */
    protected function _includePluginJs()
    {
        // Checks if the script has already been included
        if( self::$_scriptIncluded ) {
            
            // Script has already been included
            return true;
        }
        
        // Extension path
        $extPath    = t3lib_extMgm::siteRelPath( $this->extKey );
        
        // Plugin number
        $piNum      = substr( $this->prefixId, strrpos( $this->prefixId, '_' ) + 1 );
        
        // Script path
        $scriptPath = $extPath . 'res/js/' . $piNum . '.js';
        
        // Adds the plugin script
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ $this->prefixId . '-piScript' ] = '<script src="'
                                                                                  . $scriptPath
                                                                                  . '" type="text/javascript" charset="utf-8"></script>';
        
        return true;
    }
    
    /**
     * 
     */
    protected function _processPath( $path )
    {
        // Matches array for the preg_match function
        $matches = array();
        
        // Checks if the path is relative to an extension
        preg_match( '/^EXT:([^\/]+)/', $path, $matches );
        
        // Checks if an extension was found
        if( isset( $matches[ 1 ] ) ) {
            
            // Gets the extension path
            $extPath = t3lib_extMgm::siteRelPath( $matches[ 1 ] );
            
            // Replaces the extension path
            $path    = str_replace( 'EXT:' . $matches[ 1 ] . '/', $extPath, $path );
        }
        
        // Returns the processed path
        return $path;
    }
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_tscobj_pi1', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param   string  $content    The plugin content
     * @param   array   $conf       The TS setup
     * @return  string  The content of the plugin
     */
    public function main( $content, array $conf )
    {
        // Stores the TypoScript configuration
        $this->_conf = $conf;
        
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
        
        // Checks for a template file
        if( isset( $this->_conf[ 'templateFile' ] ) ) {
            
            // Initializes the template object
            $this->_api->fe_initTemplate( $this->_conf[ 'templateFile' ] );
        }
        
        // Returns the plugin view
        return $this->pi_wrapInBaseClass( $this->_getContent() );
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_pibase.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_pibase.php']);
}
