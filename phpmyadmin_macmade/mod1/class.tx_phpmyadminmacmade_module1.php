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
 * Module 'PHPMyAdmin' for the 'phpmyadmin_macmade' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */
class tx_phpmyadminmacmade_module1 extends t3lib_SCbase
{
    /**
     * Wether the static variables are set or not
     */
    private static $_hasStatic       = false;
    
    /**
     * The extension key
     */
    private static $_extKey          = '';
    
    /**
     * The extension directory
     */
    private static $_extDir          = '';
    
    /**
     * The backend user object
     */
    protected static $_beUser        = NULL;
    
    /**
     * The language object
     */
    protected static $_lang          = NULL;
    
    /**
     * The database object
     */
    protected static $_db            = NULL;
    
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
     * The TYPO3 client array
     */
    protected static $_client        = array();
    
    /**
     * The extension configuration array
     */
    protected static $_extConf       = array();
    
    /**
     * The back path (to the TYPO3 directory)
     */
    protected static $_backPath      = '';
    
    /**
     * The new line character
     */
    protected static $_NL            = '';
    
    /**
     * The TYPO3 document object
     */
    public $doc                      = NULL;
    
    /**
     * Class constructor
     * 
     * @return  NULL
     * @see     _setStaticVars
     */
    public function __construct()
    {
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
        }
        
        // Creates an instance of the TYPO3 document class
        $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
        
        // Sets the back path
        $this->doc->backPath = self::$_backPath;
    }
    
    /**
     * Returns the module content
     * 
     * @return  string  The module content
     * @see     _loadPhpMyAdmin
     */
    public function __toString()
    {
        // Access check
        $pageinfo = t3lib_BEfunc::readPageAccess(
            $this->id,
            $this->perms_clause
        );
        
        // Access to this module
        $access   = is_array( $pageinfo ) ? true : false;
        
        // Checks the module access
        if( ( $this->id && $access ) || ( self::$_beUser->user[ 'admin' ] && !$this->id ) ) {
            
            // Loads PHPMyAdmin
            return $this->_loadPhpMyAdmin();
            
        } else {
            
            // Storage
            $content             = array();
            
            // No access
            $this->doc           = t3lib_div::makeInstance( 'bigDoc' );
            $this->doc->backPath = self::$_backPath;
            
            // Adds the module content
            $content[]           = $this->doc->startPage( self::$_lang->getLL( 'title' ) );
            $content[]           = $this->doc->header( self::$_lang->getLL ( 'title' ) );
            $content[]           = $this->doc->spacer( 5 );
            $content[]           = self::$_lang->getLL( 'noaccess' );
            $content[]           = $this->doc->spacer( 10 );
            
            // Returns the module content
            return implode( self::$_NL, $content );
        }
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     */
    protected static function _setStaticVars()
    {
        // Gets the path
        $extPathInfo          = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
        
        // Removes the 'classes' directory
        array_pop( $extPathInfo );
        
        // Sets the extension key
        self::$_extKey        =  array_pop( $extPathInfo );
        
        // Sets the extension directory
        self::$_extDir        =  t3lib_extMgm::extPath( self::$_extKey );
        
        // Creates a reference to the backend user object
        self::$_beUser        =  $GLOBALS[ 'BE_USER' ];
        
        // Creates a reference to the lang object
        self::$_lang          =  $GLOBALS[ 'LANG' ];
        
        // Creates a reference to the database object
        self::$_db            =  $GLOBALS[ 'TYPO3_DB' ];
        
        // Creates a reference to the TCA array
        self::$_tca           =& $GLOBALS[ 'TCA' ];
        
        // Creates a reference to the TCA description array
        self::$_tcaDescr      =& $GLOBALS[ 'TCA_DESCR' ];
        
        // Creates a reference to the TYPO3 configuration array
        self::$_typo3ConfVars =& $GLOBALS[ 'TYPO3_CONF_VARS' ];
        
        // Creates a reference to the TYPO3 client array
        self::$_client        =& $GLOBALS[ 'CLIENT' ];
        
        // Sets the back path
        self::$_backPath      =  $GLOBALS[ 'BACK_PATH' ];
        
        // Sets the extension configuration
        self::$_extConf       =  unserialize( self::$_typo3ConfVars[ 'EXT' ][ 'extConf' ][ self::$_extKey ] );
        
        // Sets the new line character
        self::$_NL            =  chr( 10 );
        
        // Static variables are set
        self::$_hasStatic     =  true;
    }
    
    /**
     * Loads PHPMyAdmin
     * 
     * @return  string  The content from PHPMyAdmin
     */
    protected function _loadPhpMyAdmin()
    {
        return __CLASS__;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/phpmyadmin_macmade/mod1/class.tx_phpmyadminmacmade_module1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/phpmyadmin_macmade/mod1/tx_phpmyadminmacmade_module1.php']);
}
