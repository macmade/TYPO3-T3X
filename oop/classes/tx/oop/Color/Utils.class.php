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
 * Color utilities class
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_Color_Utils
{
    /**
     * The color method to use (RGB, HSL or HSV)
     */
    protected $_colorMethod = 'RGB';
    
    /**
     * 
     */
    public function setColorMethod( $method )
    {
        // Converts the method to uppercase
        $method = strtoupper( $method );
        
        // Checks the color method
        switch( $method ) {
            
            // Hue Saturation Value
            case 'HSV':
                
                $this->_colorMethod = 'HSV';
                break;
            
            // Hue Saturation Luminosity
            case 'HSL':
                
                $this->_colorMethod = 'HSL';
                break;
            
            // Default - Red Green Blue
            default:
                
                $this->_colorMethod = 'RGB';
                break;
        }
    }
    
    /**
     * Creates an hexadecimal color
     * 
     * This method is used to create an hexadecimal color representation from
     * RGB (Red-Green-Blue), HSL (Hue-Saturation-Luminance) or HSV
     * (Hue-Saturation-Value) values.
     * 
     * @param   number  The first value (red or hue, depending of the method)
     * @param   number  The second value (green or saturation, depending of the method)
     * @param   number  The third value (blue, luminosity or value, depending of the method)
     * @param   boolean Return value in uppercase
     * @return  string  The hexadecimal value of the color
     */
    public function createHexColor( $v1, $v2, $v3, $uppercase = false )
    {
        // Check color creation method
        if( $this->_colorMethod === 'HSL' ) {
            
            // Convert colors
            $colors = tx_oop_Color_Converter::hslToRgb( $v1, $v2, $v3 );
            
            // Set converted values
            $v1 = $colors[ 'R' ];
            $v2 = $colors[ 'G' ];
            $v3 = $colors[ 'B' ];
            
        } elseif( $this->_colorMethod === 'HSV' ) {
            
            // Convert colors
            $colors = tx_oop_Color_Converter::hsvToRgb( $v1, $v2, $v3 );
            
            // Set converted values
            $v1 = $colors[ 'R' ];
            $v2 = $colors[ 'G' ];
            $v3 = $colors[ 'B' ];
        }
        
        // Convert each color into hexadecimal
        $R = dechex( tx_oop_Number_Utils::inRange( $v1, 0, 255 ) );
        $G = dechex( tx_oop_Number_Utils::inRange( $v2, 0, 255 ) );
        $B = dechex( tx_oop_Number_Utils::inRange( $v3, 0, 255 ) );
        
        // Complete each color if needed
        $R = ( strlen( $R ) === 1 ) ? '0' . $R : $R;
        $G = ( strlen( $G ) === 1 ) ? '0' . $G : $G;
        $B = ( strlen( $B ) === 1 ) ? '0' . $B : $B;
        
        // Create full hexadecimal color
        $color =  $R . $G . $B;
        
        // Upper or lower case
        $color = ( $uppercase ) ? strtoupper( $color ) : strtolower( $color );
        
        // Return color
        return '#' . $color;
    }
    
    /**
     * Modifies an hexadecimal color
     * 
     * This method is used to modify an hexadecimal color representation by
     * adding RGB (Red-Green-Blue), HSL (Hue-Saturation-Luminance) or HSV
     * (Hue-Saturation-Value) values.
     * 
     * @param   string  The original color (hexadecimal)
     * @param   number  The first value (red or hue, depending of the method)
     * @param   number  The second value (green or saturation, depending of the method)
     * @param   number  The third value (blue, luminosity or value, depending of the method)
     * @param   boolean Return value in uppercase
     * @return  string  The hexadecimal value of the modified color
     */
    public function modifyHexColor( $color, $v1, $v2, $v3, $uppercase = false )
    {
        // Erase the # character if present
        $color = ( substr( $color, 0, 1 ) === '#' ) ? substr( $color, 1, strlen( $color ) ) : $color;
        
        // Check color length (3 or 6)
        if( strlen( $color ) === 3 ) {
            
            // Extract RGB values from the hexadecimal color
            $R = hexdec( substr( $color, 0, 1 ) );
            $G = hexdec( substr( $color, 1, 1 ) );
            $B = hexdec( substr( $color, 2, 1 ) );
            
        } elseif( strlen( $color ) === 6 ) {
            
            // Extract RGB values from the hexadecimal color
            $R = hexdec( substr( $color, 0, 2 ) );
            $G = hexdec( substr( $color, 2, 2 ) );
            $B = hexdec( substr( $color, 4, 2 ) );
        }
        
        // Check modification method
        if( $this->_colorMethod === 'HSL' ) {
            
            // Convert colors
            $colors = tx_oop_Color_Converter::rgbToHsl( $R, $G, $B );
            
            // Create modified color
            return $this->createHexColor(
                $colors[ 'H' ] + $v1,
                $colors[ 'S' ] + $v2,
                $colors[ 'L' ] + $v3,
                'HSL',
                $uppercase
            );
            
        } elseif( $this->_colorMethod === 'HSV' ) {
            
            // Convert colors
            $colors = tx_oop_Color_Converter::rgbToHsv( $R,$G,$B );
            
            // Create modified color
            return $this->createHexColor(
                $colors[ 'H' ] + $v1,
                $colors[ 'S' ] + $v2,
                $colors[ 'V' ] + $v3,
                'HSV',
                $uppercase
            );
            
        } else {
            
            // Create modified color
            return $this->createHexColor(
                $R + $v1,
                $G + $v2,
                $B + $v3,
                'RGB',
                $uppercase
            );
        }
    }
}
