<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (macmade@eosgarden.com)
 * All rights reserved
 * 
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * 
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Class to create HTML comments
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_xhtmlComment extends tx_oop_xhtmlTag implements ArrayAccess
{
    /**
     * The text of the comment
     */
    protected $_comment = '';
    
    /**
     * Class constructor
     * 
     * @return  NULL
     * @see     tx_oop_xhtmlTag::__construct
     */
    public function __construct( $text )
    {
        // Sets the comment text
        $this->_comment = $text;
        
        // Calls the parent constructor
        parent::__construct( '' );
    }
    
    /**
     * Returns the HTML comment
     * 
     * @param   boolean Wheter the output must be XML compliant
     * @param   int     The indentation level
     * @return  string  The HTML comment, if $xmlCompliant is false, otherwise a blank string
     */
    protected function _output( $xmlCompliant = false, $level = 0 )
    {
        // Checks if the output must be XML compliant
        if( !$xmlCompliant ) {
            
            // Returns the HTML comment
            $indent = str_pad( '', $level, self::$_TAB );
            return self::$_NL . $indent . '<!-- ' . $this->_comment . ' -->' . self::$_NL . $indent;
        }
        
        // Do not return the HTML comment when the output must be XML compliant
        return '';
    }
}
