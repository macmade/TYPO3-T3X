<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2009 Jean-David Gadina (macmade@eosgarden.com)
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
 * OOP documentation menu item for the TYPO3 backend
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
class tx_oop_doxygenMenuItem implements backend_toolbarItem
{
    /**
     * The TYPO3 backend object
     */
    private $_beObject;
    
    /**
     * Class constructor
     * 
     * @params  TYPO3backend    The TYPO3 backend object (the '&' is not useful as we are running PHP5, but it's needed to be compatible with the 'backend_toolbarItem' interface)
     * @return  NULL
     */
    public function __construct( TYPO3backend &$beObject = NULL )
    {
        // Stores the TYPO3 backend object
        $this->_beObject = $beObject;
    }
    
    /**
     * Checks the access to this menu item (only for the admin users)
     * 
     * @return  boolean
     */
    public function checkAccess()
    {
        return $GLOBALS[ 'BE_USER' ]->isAdmin();
    }
    
    /**
     * Renders the menu item
     * 
     * @return  tx_oop_Xhtml_Tag    The link to the Doxygen documentation
     */
    public function render()
    {
        // Creates the icon
        $icon             = new tx_oop_Xhtml_Tag( 'img' );
        $icon[ 'src' ]    = t3lib_extMgm::extRelPath( 'oop' ) . 'res/img/oop.gif';
        $icon[ 'width' ]  = 16;
        $icon[ 'height' ] = 16;
        $icon[ 'alt' ]    = 'OOP Doxygen';
        $icon[ 'style' ]  = 'margin-left: 5px; margin-right: 5px;';
        
        // Creates the link
        $link             = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'href' ]   = t3lib_extMgm::extRelPath( 'oop' ) . 'doc/doxygen/';
        $link[ 'class' ]  = 'toolbar-item';
        $link[ 'target' ] = '_blank';
        $link[ 'title' ]  = 'TYPO3 OOP Doxygen';
        
        // Adds the icon and the text to the link
        $link->addTextData( $icon );
        $link->addTextData( $text );
        
        // Returns the link
        return $link;
    }
    
    /**
     * Returns additional attributes for the list item in the toolbar
     * 
     * @return  string  The additionnal attributes for the list item
     */
    public function getAdditionalAttributes()
    {
        return ' id="' . __CLASS__ . '"';
    }
}
