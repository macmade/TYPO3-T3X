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
 * Class for the flexform 'select' field
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_Flexform_Field_Section_Element_Field_Select extends tx_oop_Flexform_Field_Select
{
    /**
     * The section name
     */
    protected $_sectionName = '';
    
    /**
     * The section element name
     */
    protected $_elementName = '';
    
    /**
     * Class constructor
     * 
     * @param   string  The name of the field
     * @param   string  The name of the sheet
     * @param   string  The reference of the file with the labels for the flexform (typically: EXT:extkey/path/to/lang/file)
     * @return  NULL
     */
    public function __construct(  $name, $elementName, $sectionName, $sheetName, $langFile )
    {
        // Calls the parent constructor
        parent::__construct( $name, $sheetName, $langFile );
        
        // Stores the section and the element name
        $this->_sectionName             = ( string )$sectionName;
        $this->_elementName             = ( string )$elementName;
        
        // Field label
        $this->_properties[ 'label' ]   = 'LLL:'
                                        . $this->_langFile
                                        . ':'
                                        . $this->_sheetName
                                        . '.'
                                        . $this->_sectionName
                                        . '.'
                                        . $this->_elementName
                                        . '.'
                                        . $this->_name;
    }
    
    /**
     * 
     */
    public function addItem( $value, $label = '' )
    {
        if( !isset( $this->_config[ 'items' ] ) || !is_array( $this->_config[ 'items' ] ) ) {
            
            $this->_config[ 'items' ] = array();
        }
        
        $labelPrefix = 'LLL:'
                     . $this->_langFile
                     . ':'
                     . $this->_sheetName
                     . '.'
                     . $this->_sectionName
                     . '.'
                     . $this->_elementName
                     . '.';
        $label       = ( $label ) ? $labelPrefix . $label : $labelPrefix . $this->_name . '.I.' . $value;
        
        $this->_config[ 'items' ][] = array( $label, $value );
    }
}
