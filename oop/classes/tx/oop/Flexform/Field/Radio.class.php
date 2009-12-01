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
 * Class for the flexform 'radio' field
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  tx.oop.Flexform.Field
 */
class tx_oop_Flexform_Field_Radio extends tx_oop_Flexform_Field
{
    /**
     * The type of the field
     */
    protected $_fieldType = 'radio';
    
    /**
     * 
     */
    public function addItem( $value, $label = '' )
    {
        if( !isset( $this->_config[ 'items' ] ) || !is_array( $this->_config[ 'items' ] ) ) {
            
            $this->_config[ 'items' ] = array();
        }
        
        $label = ( $label ) ? $label : $this->_name . '.I.' . $value;
        
        $this->_config[ 'items' ] = array( $label, $value );
    }
}
