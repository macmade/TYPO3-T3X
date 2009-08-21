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
 * Frontend template section
 * 
 * This class is not supposed to be instanciated by itself. Please use the
 * tx_oop_Frontend_Template_File class instead.
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_Frontend_Template_Section implements ArrayAccess
{
    /**
     * The instance of the tslib_cObj class
     */
    protected $_cObj            = NULL;
    
    /**
     * The template resource
     */
    protected $_templateContent = NULL;
    
    /**
     * The section markers
     */
    protected $_markers         = array();
    
    /**
     * The section name
     */
    protected $_name            = '';
    
    /**
     * Class constructor
     * 
     * @param   string      The section's name
     * @param   tslib_cObj  The instance of tslib_cObj
     * @param   string      The template content
     * @return  NULL
     */
    public function __construct( $name, tslib_cObj $cObj, $templateContent )
    {
        // Stores the section name
        $this->_name            = ( string )$name;
        
        // Stores the content object
        $this->_cObj            = $cObj;
        
        // Stores the template content
        $this->_templateContent = $templateContent;
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        // Ensures we have a string
        $name = '###' . ( string )$name . '###';
        
        // Checks if the marker already exist
        if( !isset( $this->_markers[ $name ] ) ) {
            
            // No such marker
            return '';
        }
        
        // Returns the marker's value
        return $this->_markers[ $name ];
    }
    
    /**
     * 
     */
    public function __set( $name, $value )
    {
        // Ensures we have a string
        $name = '###' . ( string )$name . '###';
        
        // Stores the marker value
        $this->_markers[ $name ] = ( string )$value;
    }
    
    /**
     * 
     */
    public function __isset( $name )
    {
        // Ensures we have a string
        $name = '###' . ( string )$name . '###';
        
        // Checks for the marker
        return isset( $this->_markers[ $name ] );
    }
    
    /**
     * 
     */
    public function __unset( $name )
    {
        // Ensures we have a string
        $name = '###' . ( string )$name . '###';
        
        // Removes the marker
        unset( $this->_markers[ $name ] );
    }
    
    /**
     * 
     */
    public function __toString()
    {
        // Gets the template subpart
        $subpart = $this->_cObj->getSubpart(
            $this->_templateContent,
            $this->_name
        );
        
        // Returns the substituted section
        return $this->_cObj->substituteMarkerArrayCached(
            $subpart,
            array(),
            $this->_markers,
            array()
        );
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
    public function render()
    {
        return ( string )$this;
    }
}
