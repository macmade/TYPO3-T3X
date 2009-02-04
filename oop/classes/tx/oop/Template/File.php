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

/**
 * Smarty template class
 * 
 * The goal of the class is to provide TYPO3 with the functionnalities of
 * Smarty (htto://www.smarty.net/).
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_Template_File
{
    /**
     * Wether the static variables are set or not
     */
    protected static $_hasStatic    = false;
    
    /**
     * The Smarty cache directory
     */
    protected static $_cacheDir     = '';
    
    /**
     * The Smarty compiled templates directory
     */
    protected static $_compiledDir  = '';
    
    /**
     * The Smarty object
     */
    protected $_smarty              = NULL;
    
    /**
     * The template directory
     */
    protected $_tmplDir             = '';
    
    /**
     * Class constructor
     * 
     * @return  NULL
     * @see     _setStaticVars
     * @throws  tx_oop_Template_File_Exception  If the cache directory does not exist
     * @throws  tx_oop_Template_File_Exception  If the cache directory is not writeable
     * @throws  tx_oop_Template_File_Exception  If the compiled templates directory does not exist
     * @throws  tx_oop_Template_File_Exception  If the compiled templates directory is not writeable
     * @throws  tx_oop_Template_File_Exception  If the Smarty class file does not exist
     * @throws  tx_oop_Template_File_Exception  If the Smarty class is not defined
     * @throws  tx_oop_Template_File_Exception  If the template directory for the module does not exists
     */
    public function __construct( $tmplDir )
    {
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
            
            // Checks if the cache directory exists
            if( !file_exists( self::$_cacheDir ) || !is_dir( self::$_cacheDir ) ) {
                
                // Error - The cache directory does not exist
                throw new tx_oop_Template_File_Exception(
                    'The Smarty cache directory does not exist (path: ' . self::$_cacheDir . ')',
                    tx_oop_Template_File_Exception::EXCEPTION_NO_DIRECTORY
                );
            }
            
            // Checks if the cache directory is writeable
            if( !is_writeable( self::$_cacheDir ) ) {
                
                // Error - The cache directory is not writeable
                throw new tx_oop_Template_File_Exception(
                    'The Smarty cache directory is not writeable (path: ' . self::$_cacheDir . ')',
                    tx_oop_Template_File_Exception::EXCEPTION_DIRECTORY_NOT_WRITEABLE
                );
            }
            
            // Checks if the compiled templates directory exists
            if( !file_exists( self::$_compiledDir ) || !is_dir( self::$_compiledDir ) ) {
                
                // Error - The compiled templates directory does not exist
                throw new tx_oop_Template_File_Exception(
                    'The Smarty compiled templates directory does not exist (path: ' . self::$_compiledDir . ')',
                    tx_oop_Template_File_Exception::EXCEPTION_NO_DIRECTORY
                );
            }
            
            // Checks if the compiled templates directory is writeable
            if( !is_writeable( self::$_compiledDir ) ) {
                
                // Error - The compiled templates directory is not writeable
                throw new tx_oop_Template_File_Exception(
                    'The Smarty compiled templates directory is not writeable (path: ' . self::$_compiledDir . ')',
                    tx_oop_Template_File_Exception::EXCEPTION_DIRECTORY_NOT_WRITEABLE
                );
            }
            
            // Path to the Smarty class
            $smartyPath = t3lib_extMgm::extPath( 'oop' )
                        . 'res'
                        . DIRECTORY_SEPARATOR
                        . 'php'
                        . DIRECTORY_SEPARATOR
                        . 'smarty'
                        . DIRECTORY_SEPARATOR
                        . 'Smarty.class.php';
            
            // Checks if the Smarty class file exists
            if( !file_exists( $smartyPath ) ) {
                
                // Error - The class file does not exists
                throw new tx_oop_Template_File_Exception(
                    'The Smarty class file does not exist (path: ' . $smartyPath . ')',
                    tx_oop_Template_File_Exception::EXCEPTION_NO_SMARTY_CLASS_FILE
                );
            }
            
            // Includes the Smarty class file
            require_once( $smartyPath );
            
            // Checks if the Smarty class is defined
            if( !class_exists( 'Smarty' ) ) {
                
                // Error - The Smarty class in not defined
                throw new tx_oop_Template_File_Exception(
                    'The Smarty class is not defined in file ' . $smartyPath . ')',
                    tx_oop_Template_File_Exception::EXCEPTION_NO_SMARTY_CLASS
                );
            }
        }
        
        // Checks if the templates directory exists
        if( !file_exists( $tmplDir ) || !is_dir( $tmplDir ) ) {
            
            // Error - The template directory does not exist
            throw new tx_oop_Template_File_Exception(
                'The template directory does not exist (path: ' . $tmplDir . ')',
                tx_oop_Template_File_Exception::EXCEPTION_NO_DIRECTORY
            );
        }
        
        // Stores the template directory
        $this->_tmplDir              = $tmplDir;
        
        // Creates the Smarty instance
        $this->_smarty               = new Smarty();
        
        // Sets the Smarty directories
        $this->_smarty->template_dir = $this->_tmplDir;
        $this->_smarty->compile_dir  = self::$_compiledDir;
        $this->_smarty->cache_dir    = self::$_cacheDir;
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getInstance
     */
    private static function _setStaticVars()
    {
        // Sets the Smarty directories
        self::$_cacheDir     = t3lib_div::getFileAbsFileName( 'uploads/tx_oop/smarty/cache/' );
        self::$_compiledDir  = t3lib_div::getFileAbsFileName( 'uploads/tx_oop/smarty/compiled/' );
        
        // Static variables are set
        self::$_hasStatic    = true;
    }
    
    /**
     * PHP method calls overloading
     * 
     * This method will reroute all the call on this object to the PDO object.
     * 
     * @param   string                          The name of the called method
     * @param   array                           The arguments for the called method
     * @return  mixed                           The result of the PDO method called
     * @throws  tx_oop_Template_File_Exception  If the called method does not exist
     */
    public function __call( $name, array $args = array() )
    {
        // Checks if the method can be called
        if( !is_callable( array( $this->_smarty, $name ) ) ) {
            
            // Called method does not exist
            throw new tx_oop_Template_File_Exception(
                'The method \'' . $name . '\' cannot be called on the Smarty object',
                tx_oop_Template_File_Exception::EXCEPTION_BAD_METHOD
            );
        }
        
        // Gets the number of arguments
        $argCount = count( $args );
        
        // Checks the number of arguments, to avoid using the call_user_func_array() function
        switch( $argCount ) {
            
            case 0:
                
                return $this->_smarty->$name();
                break;
            
            case 1:
                
                return $this->_smarty->$name( $args[ 0 ] );
                break;
            
            case 2:
                
                return $this->_smarty->$name( $args[ 0 ], $args[ 1 ] );
                break;
            
            case 3:
                
                return $this->_smarty->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ] );
                break;
            
            case 4:
                
                return $this->_smarty->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
                break;
                break;
            
            case 5:
                
                return $this->_smarty->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] , $args[ 4 ] );
                break;
            
            default:
                
                return call_user_func_array( $this->_smarty, $args );
                break;
        }
    }
}
