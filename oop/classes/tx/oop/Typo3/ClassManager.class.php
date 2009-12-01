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
 * TYPO3 class manager
 * 
 * This class will handle every request to a class that belongs to a registered
 * prefix, by automatically loading the class file (thanx to the SPL).
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_Typo3_ClassManager
{
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * The loaded classes from this TYPO3
     */
    private $_loadedClasses   = array();
    
    /**
     * The path from which to load classes
     */
    private $_classDirs       = array();
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return NULL
     */
    private function __construct()
    {}
    
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
     * @return  tx_oop_Typo3_ClassManager   The unique instance of the class
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
        
        // Process each registered prefix
        foreach( $instance->_classDirs as $prefix => $infos ) {
        
            // Checks if the requested class belongs to a registered prefix
            if( strpos( $className, $prefix ) === 0 ) {
                
                // Tries to load the requested class
                $instance->_loadClass( $className, $prefix, $instance->_classDirs[ $prefix ] );
            }
        }
        
        // The requested class does not belong to a registered prefix
        return false;
    }
    
    /**
     * Loads a class from a TYPO3 directory
     * 
     * @param   string  The name of the class to load
     * @param   string  The class prefix
     * @param   array   The load informations
     * @return  boolean
     */
    private function _loadClass( $className, $prefix, array $infos )
    {
        if( $infos[ 1 ] === false ) {
            
            return $this->_loadFlatClass( $className, $infos[ 0 ] );
        }
        
        $relPath   = str_replace( '_', DIRECTORY_SEPARATOR, substr( $className, strlen( $prefix ) ) ) . '.class.php';
        $classPath = $infos[ 0 ] . $relPath;
        
        // Checks if the class file exists
        if( file_exists( $classPath ) ) {
            
            // Includes the class file
            require_once( $classPath );
            
            // Checks if the requested class is defined in the included file
            if( !class_exists( $className ) ) {
                
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
     * Loads a class from a TYPO3 directory
     * 
     * @param   string  The name of the class to load
     * @param   string  The directory in which the class is supposed to be
     * @return  boolean
     */
    private function _loadFlatClass( $className, $directory )
    {
        // Gets the class path
        $classPath = $directory . 'class.' . strtolower( $className ) . '.php';
        
        // Checks if the class file exists
        if( file_exists( $classPath ) ) {
            
            // Includes the class file
            require_once( $classPath );
            
            // Checks if the requested class is defined in the included file
            if( !class_exists( $className ) ) {
                
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
     * 
     */
    public function registerClassDir( $prefix, $path, $expandedHierarchy = false )
    {
        if( isset( $this->_classDirs[ $prefix ] ) ) {
            
            throw new tx_oop_Typo3_ClassManager_Exception(
                'The requested prefix (' . $prefix . ') is already registered.',
                tx_oop_Typo3_ClassManager_Exception::EXCEPTION_PREFIX_EXISTS
            );
        }
        
        if( !file_exists( $path ) || !is_dir( $path ) ) {
            
            throw new tx_oop_Typo3_ClassManager_Exception(
                'The requested directory (' . $path . ') does not exist.',
                tx_oop_Typo3_ClassManager_Exception::EXCEPTION_NO_DIRECTORY
            );
        }
        
        $this->_classDirs[ $prefix ] = array( $path, ( bool )$expandedHierarchy );
    }
    
    /**
     * Gets the loaded classes
     * 
     * @return  array   An array with the loaded classes
     */
    public function getLoadedClasses()
    {
        // Returns the loaded classes
        return $this->_loadedClasses;
    }
}
