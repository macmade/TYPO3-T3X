<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (info@macmade.net)
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

/** 
 * TSLib Menu extension
 * 
 * The goal of this extension class is to correct some bugs, optimize some
 * statements, avoid PHP errors (even E_NOTICE) and provide a clean code base.
 * For this last one, some variables have been renamed in order to respect the
 * camelCaps rule. The code formatting also tries to follow the Zend coding
 * guidelines.
 * 
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.1
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *      66:     class ux_tslib_menu
 *      76:     function ux_tslib_menu
 *      96:     function function link( $key, $altTarget = '', $typeOverride = '' )
 *     107:     class ux_tslib_gmenu
 *     117:     function ux_tslib_gmenu
 *     137:     function function link( $key, $altTarget = '', $typeOverride = '' )
 *     148:     class ux_tslib_imgmenu
 *     158:     function ux_tslib_imgmenu
 *     178:     function function link( $key, $altTarget = '', $typeOverride = '' )
 *     189:     class ux_tslib_jsmenu
 *     199:     function ux_tslib_jsmenu
 *     219:     function function link( $key, $altTarget = '', $typeOverride = '' )
 *     230:     class ux_tslib_tmenu
 *     240:     function ux_tslib_tmenu
 *     260:     function function link( $key, $altTarget = '', $typeOverride = '' )
 * 
 *              TOTAL FUNCTIONS: 10
 */

// Include the TSLib patcher class
require_once( t3lib_extMgm::extPath( 'tslib_patcher' ) . 'class.tx_tslibpatcher.php' );

class ux_tslib_menu extends tslib_menu
{
    // Instance of the TSLib patcher class
    var $ux_tslibPatcher = NULL;
    
    /**
     * Class constructor
     * 
     * @return  null
     */
    function ux_tslib_menu()
    {
        // New instance of the TSLib patcher class
        $this->ux_tslibPatcher = t3lib_div::makeInstance( 'tx_tslibpatcher' );
        
        // Creates an instance of tslib_cObj
        $this->ux_tslibPatcher->cObjInstance();
    }
    
    /**
     * Redefinition of method link to correct cHash calculation which doesn't work!
     * 
     * @param   int         $key            Pointer to a key in the $this->menuArr array where the value for that key represents the menu item we are linking to (page record)
     * @param   string      $altTarget      Alternative target
     * @param   int         $typeOverride   Alternative type
     * @return  string      An array with A-tag attributes as key/value pairs (HREF, TARGET and onClick)
     */
    function link( $key, $altTarget = '', $typeOverride = '' )
    {
        // Sets the menu object
        $this->ux_tslibPatcher->setMenuObject( $this );
        
        // Calls the common link method
        return $this->ux_tslibPatcher->tsMenuLink(
            $key,
            $altTarget,
            $typeOverride
        );
    }
}

class ux_tslib_gmenu extends tslib_gmenu
{
    // Instance of the TSLib patcher class
    var $ux_tslibPatcher = NULL;
    
    /**
     * Class constructor
     * 
     * @return  null
     */
    function ux_tslib_gmenu()
    {
        // New instance of the TSLib patcher class
        $this->ux_tslibPatcher = t3lib_div::makeInstance( 'tx_tslibpatcher' );
        
        // Creates an instance of tslib_cObj
        $this->ux_tslibPatcher->cObjInstance();
    }
    
    /**
     * Redefinition of method link to correct cHash calculation which doesn't work!
     * 
     * @param   int         $key            Pointer to a key in the $this->menuArr array where the value for that key represents the menu item we are linking to (page record)
     * @param   string      $altTarget      Alternative target
     * @param   int         $typeOverride   Alternative type
     * @return  string      An array with A-tag attributes as key/value pairs (HREF, TARGET and onClick)
     */
    function link( $key, $altTarget = '', $typeOverride = '' )
    {
        // Sets the menu object
        $this->ux_tslibPatcher->setMenuObject( $this );
        
        // Calls the common link method
        return $this->ux_tslibPatcher->tsMenuLink(
            $key,
            $altTarget,
            $typeOverride
        );
    }
}

class ux_tslib_imgmenu extends tslib_imgmenu
{
    // Instance of the TSLib patcher class
    var $ux_tslibPatcher = NULL;
    
    /**
     * Class constructor
     * 
     * @return  null
     */
    function ux_tslib_imgmenu()
    {
        // New instance of the TSLib patcher class
        $this->ux_tslibPatcher = t3lib_div::makeInstance( 'tx_tslibpatcher' );
        
        // Creates an instance of tslib_cObj
        $this->ux_tslibPatcher->cObjInstance();
    }
    
    /**
     * Redefinition of method link to correct cHash calculation which doesn't work!
     * 
     * @param   int         $key            Pointer to a key in the $this->menuArr array where the value for that key represents the menu item we are linking to (page record)
     * @param   string      $altTarget      Alternative target
     * @param   int         $typeOverride   Alternative type
     * @return  string      An array with A-tag attributes as key/value pairs (HREF, TARGET and onClick)
     */
    function link( $key, $altTarget = '', $typeOverride = '' )
    {
        // Sets the menu object
        $this->ux_tslibPatcher->setMenuObject( $this );
        
        // Calls the common link method
        return $this->ux_tslibPatcher->tsMenuLink(
            $key,
            $altTarget,
            $typeOverride
        );
    }
}

class ux_tslib_jsmenu extends tslib_jsmenu
{
    // Instance of the TSLib patcher class
    var $ux_tslibPatcher = NULL;
    
    /**
     * Class constructor
     * 
     * @return  null
     */
    function ux_tslib_jsmenu()
    {
        // New instance of the TSLib patcher class
        $this->ux_tslibPatcher = t3lib_div::makeInstance( 'tx_tslibpatcher' );
        
        // Creates an instance of tslib_cObj
        $this->ux_tslibPatcher->cObjInstance();
    }
    
    /**
     * Redefinition of method link to correct cHash calculation which doesn't work!
     * 
     * @param   int         $key            Pointer to a key in the $this->menuArr array where the value for that key represents the menu item we are linking to (page record)
     * @param   string      $altTarget      Alternative target
     * @param   int         $typeOverride   Alternative type
     * @return  string      An array with A-tag attributes as key/value pairs (HREF, TARGET and onClick)
     */
    function link( $key, $altTarget = '', $typeOverride = '' )
    {
        // Sets the menu object
        $this->ux_tslibPatcher->setMenuObject( $this );
        
        // Calls the common link method
        return $this->ux_tslibPatcher->tsMenuLink(
            $key,
            $altTarget,
            $typeOverride
        );
    }
}

class ux_tslib_tmenu extends tslib_tmenu
{
    // Instance of the TSLib patcher class
    var $ux_tslibPatcher = NULL;
    
    /**
     * Class constructor
     * 
     * @return  null
     */
    function ux_tslib_tmenu()
    {
        // New instance of the TSLib patcher class
        $this->ux_tslibPatcher = t3lib_div::makeInstance( 'tx_tslibpatcher' );
        
        // Creates an instance of tslib_cObj
        $this->ux_tslibPatcher->cObjInstance();
    }
    
    /**
     * Redefinition of method link to correct cHash calculation which doesn't work!
     * 
     * @param   int         $key            Pointer to a key in the $this->menuArr array where the value for that key represents the menu item we are linking to (page record)
     * @param   string      $altTarget      Alternative target
     * @param   int         $typeOverride   Alternative type
     * @return  string      An array with A-tag attributes as key/value pairs (HREF, TARGET and onClick)
     */
    function link( $key, $altTarget = '', $typeOverride = '' )
    {
        // Sets the menu object
        $this->ux_tslibPatcher->setMenuObject( $this );
        
        // Calls the common link method
        return $this->ux_tslibPatcher->tsMenuLink(
            $key,
            $altTarget,
            $typeOverride
        );
    }
}

/**
 * XClass inclusion.
 */
if( defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tslib_menu.php' ]) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tslib_menu.php' ]);
}
