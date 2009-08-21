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
 * Frontend template
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_Frontend_Template implements ArrayAccess
{
    /**
     * A flag to know wether the needed static variables are set
     */
    private static $_hasStatic  = false;
    
    /**
     * The instance of the string utilities class (tx_oop_String_Utils)
     */
    protected static $_str      = NULL;
    
    /**
     * The instance of the tslib_cObj class
     */
    protected $_cObj            = NULL;
    
    /**
     * The template resource
     */
    protected $_templateContent = NULL;
    
    /**
     * The template sections
     */
    protected $_sections        = array();
    
    /**
     * Class constructor
     * 
     * @param   tslib_cObj  The instance of tslib_cObj
     * @param   string      The path to the template file
     * @return  NULL
     */
    public function __construct( tslib_cObj $cObj, $templateFilePath )
    {
        if( !self::$_hasStatic ) {
            
            self::_setStaticVars();
        }
        
        // Stores the content object
        $this->_cObj = $cObj;
        
        // Initializes the template content object
        $this->_initTemplate( ( string )$templateFilePath );
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        // Ensures we have a string
        $name = ( string )$name;
        
        // Checks if the section already exist
        if( !isset( $this->_sections[ $name ] ) ) {
            
            // Creates a new section
            $this->_sections[ $name ] = new tx_oop_Frontend_Template_Section( $name, $this->_cObj, $this->_templateContent );
        }
        
        // Returns the template section
        return $this->_sections[ $name ];
    }
    
    /**
     * 
     */
    public function __set( $name, $value )
    {
        $this->_sections[ $name ] = ( string )$value;
    }
    
    /**
     * 
     */
    public function __isset( $name )
    {
        return isset( $this->_sections[ $name ] );
    }
    
    /**
     * 
     */
    public function __unset( $name )
    {
        unset( $this->_sections[ $name ] );
    }
    
    /**
     * 
     */
    public function __toString()
    {
        $content = '';
        
        foreach( $this->_sections as $key => $value ) {
            
            $content .= self::$_str->NL
                     .  '<!-- Start of template section \'' . $key . '\' -->'
                     .  self::$_str->NL
                     .  ( string )$value
                     .  self::$_str->NL
                     .  '<!-- End of template section \'' . $key . '\' -->'
                     .  self::$_str->NL;
        }
        
        return $content;
    }
    
    /**
     * 
     */
    public function offsetGet( $name )
    {
        return $this->$name;
    }
    
    /**
     * 
     */
    public function offsetSet( $name, $value )
    {
        $this->$name = $value;
    }
    
    /**
     * 
     */
    public function offsetExists( $name )
    {
        return isset( $this->$name );
    }
    
    /**
     * 
     */
    public function offsetUnset( $name )
    {
        unset( $this->$name );
    }
    
    /**
     * 
     */
    private static function _setStaticVars()
    {
        self::$_str       = tx_oop_String_Utils::getInstance();
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
        // Checks if the template file exists
        if( !file_exists( tx_oop_Typo3_Utils::getSystemPath( $templateFilePath ) ) ) {
            
            // The template file does not exist
            throw new tx_oop_Frontend_Template_Exception(
                'The template file does not exist (path: ' . $templateFilePath . ')',
                tx_oop_Frontend_Template_Exception::EXCEPTION_TEMPLATE_NOT_FOUND
            );
        }
        
        // Loads and stores the template file
        $this->_templateContent = $this->_cObj->fileResource( $templateFilePath );
    }
}
