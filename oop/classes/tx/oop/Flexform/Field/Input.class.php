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
 * Class for the flexform 'input' field
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  tx.oop.Flexform.Field
 */
class tx_oop_Flexform_Field_Input extends tx_oop_Flexform_Field
{
    /**
     * The type of the field
     */
    protected $_fieldType = 'input';
    
    /**
     * Adds an eval rule to the field
     * 
     * @param   string  The rule type (required , trim, date, datetime, time, timesec, year, int, upper, lower, alpha, num, alphanum, alphanum_x, nospace, md5, is_in, password, double2, unique, uniqueInPid, tx_*)
     */
    public function addEval( $rule )
    {
        if( !isset( $this->_config[ 'eval' ] ) ) {
            
            $this->_config[ 'eval' ] = '';
            
        }
        
        if(    $rule === 'required'
            || $rule === 'trim'
            || $rule === 'date'
            || $rule === 'datetime'
            || $rule === 'time'
            || $rule === 'timesec'
            || $rule === 'year'
            || $rule === 'int'
            || $rule === 'upper'
            || $rule === 'lower'
            || $rule === 'alpha'
            || $rule === 'num'
            || $rule === 'alphanum'
            || $rule === 'alphanum_x'
            || $rule === 'nospace'
            || $rule === 'md5'
            || $rule === 'is_in'
            || $rule === 'password'
            || $rule === 'double2'
            || $rule === 'unique'
            || $rule === 'uniqueInPid'
            || substr( $rule, 0, 3 ) === 'tx_'
        ) {
            
            $this->_config[ 'eval' ] = ( strlen( $this->_config[ 'eval' ] ) ) ? ',' . $rule : $rule;
            
        } else {
            
            throw new tx_oop_Tca_Field_Input_Exception(
                'The \'\' eval rule is not a valid eval rule',
                tx_oop_Tca_Field_Input_Exception::EXCEPTION_INVALID_EVAL
            );
        }
    }
    
    /**
     * 
     */
    public function setRange( $lower, $upper )
    {
        $this->_config[ 'range' ] = array(
            'upper' => $upper,
            'lower' => $lower
        );
    }
}
