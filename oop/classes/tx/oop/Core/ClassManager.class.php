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
 * Class manager
 * 
 * This class will handle every request to a class from this project,
 * by automatically loading the class file (thanx to the SPL).
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_Core_ClassManager
{
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * The loaded classes from this project
     */
    private $_loadedClasses   = array();
    
    /**
     * The available top packages
     */
    private $_packages        = array();
    
    /**
     * The directory which contains the classes
     */
    private $_classDir        = '';
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return NULL
     */
    private function __construct()
    {
        // Stores the directory containing the classes
        $this->_classDir = realpath( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' )
                         . DIRECTORY_SEPARATOR;
        
        // Creates a directory iterator in the directory containing this file
        $dirIterator     = new DirectoryIterator( $this->_classDir );
        
        // Adds this class to the loaded classes array
        $this->_loadedClasses[ __CLASS__ ] = __FILE__;
        
        // Process each directory
        foreach( $dirIterator as $file ) {
            
            // Checks if the file is a PHP class file
            if( substr( $file, strlen( $file ) - 10 ) === '.class.php' ) {
                
                // Stores the file name, with it's full path
                $this->_packages[ ( string )$file ] = $file->getPathName();
                
                // Process the next file
                continue;
            }
            
            // Checks if the file is a directory
            if( !$file->isDir() ) {
                
                // File - Process the next file
                continue;
            }
            
            // Checks if the directory is hidden
            if( substr( $file, 0, 1 ) === '.' ) {
                
                // Hidden - Process the next file
                continue;
            }
            
            // Stores the directory name, with it's full path
            $this->_packages[ ( string )$file ] = $file->getPathName();
        }
    }
    
    /**
     * Clones an instance of the class
     * 
     * A call to this method will produce an exception, as the class cannot
     * be cloned (singleton).
     * 
     * @return  NULL
     * @throws  tx_oop_Core_Singleton_Exception Always, as the class cannot be cloned (singleton)
     */
    public function __clone()
    {
        throw new tx_oop_Core_Singleton_Exception(
            'Class ' . __CLASS__ . ' cannot be cloned',
            tx_oop_Core_Singleton_Exception::EXCEPTION_CLONE
        );
    }
    
    /**
     * Gets the unique class instance
     * 
     * This method is used to get the unique instance of the class
     * (singleton). If no instance is available, it will create it.
     * 
     * @return  tx_oop_Core_ClassManager    The unique instance of the class
     */
    public static function getInstance()
    {
        // Checks if the unique instance already exists
        if( !is_object( self::$_instance ) ) {
            
            // Creates the unique instance
            self::$_instance = new self();
        }
        
        // Returns the unique instance
        return self::$_instance;
    }
    
    /**
     * SPL autoload method
     * 
     * When registered with the spl_autoload_register() function, this method
     * will be called each time a class cannot be found, and will try to
     * load it.
     * 
     * @param   string  The name of the class to load
     * @return  boolean
     * @see     getInstance
     * @see     _loadClass
     */
    public static function autoLoad( $className )
    {
        // Instance of this class
        static $instance = NULL;
        
        // Checks if the instance of the class has already been fetched
        if( !is_object( $instance ) ) {
            
            // Gets the instance of this class
            $instance = self::getInstance();
        }
        
        // Checks if the class belongs to the 'tx_oop' package
        if( substr( $className, 0, 7 ) === 'tx_oop_' ) {
            
            // Gets the class root package
            $rootPkg = substr( $className, 7, strpos( $className, '_', 7 ) - 7 );
            
            // Checks if the requested class belongs to this project
            if( isset( $instance->_packages[ $rootPkg ] )
                || isset( $instance->_packages[ $className . '.class.php' ] )
            ) {
                
                // Loads the class
                return $instance->_loadClass( $className );
                
            }
        }
        
        // The requested class does not belong to this project
        return false;
    }
    
    /**
     * Loads a class from this project
     * 
     * @param   string  The name of the class to load
     * @return  boolean
     */
    private function _loadClass( $className )
    {
        // Gets the class path
        $classPath = $this->_classDir
                   . str_replace( '_', DIRECTORY_SEPARATOR, substr( $className, 7 ) )
                   . '.class.php';
        
        // Checks if the class file exists
        if( file_exists( $classPath ) ) {
            
            // Includes the class file
            require_once( $classPath );
            
            // Checks if the requested class is an interface
            if( substr( $className, -9 ) === 'Interface' ) {
                
                // Checks if the interface is defined
                if( !interface_exists( $className ) ) {
                    
                    // Error message
                    $errorMsg = 'The interface '
                              . $className
                              . ' is not defined in file '
                              . $classPath;
                    
                    // The class is not defined
                    trigger_error( $errorMsg, E_USER_ERROR );
                }
                
            } elseif( !class_exists( $className ) ) {
                
                // Error message
                $errorMsg = 'The class '
                          . $className
                          . ' is not defined in file '
                          . $classPath;
                
                // The class is not defined
                trigger_error( $errorMsg, E_USER_ERROR );
            }
            
            // Adds the class to the loaded classes array
            $this->_loadedClasses[ $className ] = $classPath;
            
            // Class was successfully loaded
            return true;
        }
        
        // Class file was not found
        return false;
    }
    
    /**
     * Gets the loaded classes from this project
     * 
     * @return  array   An array with the loaded classes
     */
    public function getLoadedClasses()
    {
        // Returns the loaded classes from this project
        return $this->_loadedClasses;
    }
}
