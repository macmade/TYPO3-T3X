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
 * Number related utilities
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_Number_Utils
{
    /**
     * Class constructor
     * 
     * The class constructor is private as all methods from this class are
     * static.
     * 
     * @return  NULL
     */
    private function __construct()
    {}
    
    /**
     * Ensures a number is in a specified range
     * 
     * This method forces the specified number into the boundaries of a
     * minimum and maximum number.
     * 
     * @param   numbe   The number to check
     * @param   number  The minimum value
     * @param   number  The maximum value
     * @param   boolean Evaluates the number as an integer
     * @return  number  A number in the specified range
     */
    public static function inRange( $number, $min, $max, $int = false )
    {
        // Converts the number to an integer if required
        if( $int ) {
            
            $number = ( int )$number;
        }
        
        // Checks the number
        if( $number > $max ) {
            
            // Number is bigger than maximum value
            $number = $max;
            
        } elseif( $number < $min ) {
            
            // Number is smaller than minimal value
            $number = $min;
        }
        
        // Returns the number
        return $number;
    }
}
