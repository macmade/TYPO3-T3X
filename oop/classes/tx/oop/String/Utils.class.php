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
 * String utilities
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_String_Utils
{
    /**
     * 
     */
    private static $_instance = NULL;
    
    /**
     * 
     */
    private $_asciiTable      = array();
    
    /**
     * 
     */
    private function __construct()
    {
        for( $i = 0; $i < 33; $i++ ) {
            
            $this->_asciiTable = chr( $i );
        }
    }
    
    /**
     * 
     */
    public static function getInstance()
    {
        if( !is_object( self::$_instance ) ) {
            
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Unify line breaks
     * 
     * This method converts Macintosh & DOS line breaks to standard Unix
     * line breaks. This means replacing CR (u000D / chr(13)) and CR + LF
     * (u000D + u000A / chr(13) + chr( 10 )) by LF (u000A / chr( 10 )). It also
     * replace LF + CR (u000A + u000D / chr( 10 ) + chr(13)) sequences. By
     * default, the function erases all ASCII null characters (u0000 / chr(0)).
     * 
     * @param   string  The text to process
     * @param   boolean If set, erases ASCII null characters
     * @return  string  The text with standard Unix line breaks
     */
    public function unifyLineBreaks( $text, $stripNull = true )
    {
        // Strip ASCII null character?
        if( $stripNull ) {
            
            // Erases ASCII null characters
            $text = str_replace( $this->_asciiTable[ 0 ], '', $text );
        }
        
        // DOS CR + LF (u000D + u000A / chr(13) + chr( 10 ))
        $text = str_replace(
            $this->_asciiTable[ 13 ] . $this->_asciiTable[ 10 ],
            $this->_asciiTable[ 10 ],
            $text
        );
        
        // LF + CR (u000A + u000D / chr( 10 ) + chr(13))
        $text = str_replace(    
            $this->_asciiTable[ 10 ] . $this->_asciiTable[ 13 ],
            $this->_asciiTable[ 10 ],
            $text
        );
        
        // Macintosh CR (u000D / chr(13))
        $text = str_replace(
            $this->_asciiTable[ 13 ],
            $this->_asciiTable[ 10 ],
            $text
        );
        
        // Return text
        return $text;
    }
    
    /**
     * Gets an HTML list from a string
     * 
     * @param   string              The string to process
     * @param   string              The separator for the list items
     * @param   string              The list tag (ul or ol)
     * @return  tx_oop_Xhtml_Tag    The HTML list
     */
    public function strToList( $str, $sep = ',', $listType = 'ul' )
    {
        // Gets all the list items
        $items = explode( $sep, $str );
        
        // Creates the list tag
        $list = new tx_oop_Xhtml_Tag( $listType );
        
        // Process each list item
        foreach( $items as $item ) {
            
            // Adds the list item to the list tag
            $list->li = trim( $item );
        }
        
        // Returns the list tag
        return $list;
    }
    
    /**
     * Gets an HTML list from a array
     * 
     * @param   string              The array to process
     * @param   string              The list tag (ul or ol)
     * @return  tx_oop_Xhtml_Tag    The HTML list
     */
    public function arrayToList( array $array, $listType = 'ul' )
    {
        // Creates the list tag
        $list = new tx_oop_Xhtml_Tag( $listType );
        
        // Process each list item
        foreach( $array as $item ) {
            
            // Checks if the current item is an array
            if( is_array( $item ) ) {
                
                // Creates a sub-list
                $list->li->addChild( $this->arrayToList( $item, $listType ) );
                
            } else {
                
                // Adds the list item to the list tag
                $list->li = trim( $item );
            }
        }
        
        // Returns the list tag
        return $list;
    }
    
    /**
     * Crops a string.
     * 
     * This function is used to crop a string to a specified number of
     * characters. By default, it crops the string after an entire word, and not
     * in the middle of a word. It also strips by default all HTML tags before
     * cropping, to avoid display problems.
     * 
     * @param   string  The string to crop
     * @param   int     The number of characters to keep
     * @param   string  The string to add after the cropped string
     * @param   boolean If set, don't crop in a middle of a word
     * @param   boolean If set, removes all HTML tags from the string before cropping
     * @return  string  The cropped string
     */
    function crop( $str, $chars, $endString = '...', $crop2space = true, $stripTags = true )
    {
        // Checks the string length
        if( strlen( $str ) < $chars ) {
            
            // Returns the string
            return $str;
        }
        
        // Remove HTML tags?
        if( $stripTags ) {
            
            // Removes all tags
            $str = strip_tags( $str );
        }
        
        // Checks the string length
        if( strlen( $str ) < $chars ) {
            
            // Returns the string
            return $str;
            
        } else {
            
            // Substring
            $str = substr( $str, 0, $chars );
            
            // Crops only after a word?
            if( $crop2space && strstr( $str, ' ' ) ) {
                
                // Position of the last space
                $cropPos = strrpos( $str, ' ' );
                
                // Crops the string
                $str     = substr( $str, 0, $cropPos );
            }
            
            // Returns the string
            return $str . $endString;
        }
    }
}
