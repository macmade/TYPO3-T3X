<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2004 macmade.net
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
 * Addition of an item to the clickmenu
 *
 * @author		Jean-David Gadina (info@macmade.net)
 * @version		2.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *       44:    class tx_setpagetype_cm1
 *      113:    public function __construct
 *      157:    protected static function _getLabels
 *      173:    protected static function _getIcons
 *      199:    public function main( clickMenu $backRef, array $menuItems, $table, $uid )
 * 
 *              TOTAL FUNCTIONS: 4
 */

class tx_setpagetype_cm1
{
    // Backend user object
    protected static $_beUser    = NULL;
    
    // Language object
    protected static $_lang      = NULL;
    
    // TCA array
    protected static $_tca       = array();
    
    // Page doktypes
    protected static $_dokTypes  = array(
        '1'   => 'standard',
        '6'   => 'backendusersection',
        '4'   => 'shortcut',
        '7'   => 'mountpoint',
        '3'   => 'link',
        '254' => 'sysfolder',
        '255' => 'recycler',
        '199' => 'spacer'
    );
    
    // Language labels to get
    protected static $_getLabels = array(
        'dokType.1'      => 'LLL:EXT:lang/locallang_tca.php:doktype.I.0',
        'dokType.6'      => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.4',
        'dokType.4'      => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.2',
        'dokType.7'      => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.5',
        'dokType.3'      => 'LLL:EXT:lang/locallang_general.php:LGL.external',
        'dokType.254'    => 'LLL:EXT:lang/locallang_tca.php:doktype.I.1',
        'dokType.255'    => 'LLL:EXT:lang/locallang_tca.php:doktype.I.2',
        'dokType.199'    => 'LLL:EXT:cms/locallang_tca.php:pages.doktype.I.7',
        'cm1.hideInMenu' => 'LLL:EXT:setpagetype/locallang.xml:cm1_hideInMenu',
        'cm1.showInMenu' => 'LLL:EXT:setpagetype/locallang.xml:cm1_showInMenu',
        'cm1.title'      => 'LLL:EXT:setpagetype/locallang.xml:cm1_title'
    );
    
    // Language labels
    protected static $_labels    = array();
    
    // Icons to get
    protected static $_getIcons  = array(
        'dokType.1'      => 'gfx/i/pages.gif',
        'dokType.6'      => 'gfx/i/be_users_section.gif',
        'dokType.4'      => 'gfx/i/pages_shortcut.gif',
        'dokType.7'      => 'gfx/i/pages_mountpoint.gif',
        'dokType.3'      => 'gfx/i/pages_link.gif',
        'dokType.254'    => 'gfx/i/sysf.gif',
        'dokType.255'    => 'gfx/i/recycler.gif',
        'dokType.199'    => 'gfx/i/spacer_icon.gif',
        'cm1.hideInMenu' => 'gfx/i/pages_notinmenu.gif',
        'cm1.showInMenu' => 'gfx/i/pages.gif'
    );
    
    // Icons
    protected static $_icons     = array();
    
    // Storage for the CSM items
    protected $_csmItems         = array();
    
    // Sublevel name
    protected $_subName          = '';
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Checks for the backend user object
        if( !is_object( self::$_beUser ) ) {
            
            // Gets a reference to the backend user object
            self::$_beUser = $GLOBALS[ 'BE_USER' ];
        }
        
        // Checks for the language object
        if( !is_object( self::$_lang ) ) {
            
            // Gets a reference to the language object
            self::$_lang   = $GLOBALS[ 'LANG' ];
        }
        
        // Checks for the TCA array
        if( !count( self::$_tca ) ) {
            
            // Gets a reference to the TCA array
            self::$_tca =& $GLOBALS[ 'TCA' ];
        }
        
        // Checks for the language labels
        if( !count( self::$_labels ) ) {
            
            self::_getLabels();
        }
        
        // Checks for the icons
        if( !count( self::$_icons ) ) {
            
            self::_getIcons();
        }
        
        // Sublevel name
        $this->_subName = t3lib_div::_GP( 'subname' );
    }
    
    /**
     * Gets the language labels
     * 
     * @return  NULL
     */
    protected static function _getLabels()
    {
        // Process each label to get
        foreach( self::$_getLabels as $key => $value ) {
            
            // Stores the label
            self::$_labels[ $key ] = self::$_lang->sL( $value );
        }
    }
    
    
    /**
     * Gets the icons
     * 
     * @return  NULL
     */
    protected static function _getIcons()
    {
        // Process each icon to get
        foreach( self::$_getIcons as $key => $value ) {
            
            // Stores the icon
            self::$_icons[ $key ] = t3lib_iconWorks::skinImg(
                $GLOBALS[ 'BACK_PATH' ],
                $value,
                ''
            );
        }
    }
    
    /**
     * Adds items to the context sensitive menu
     * 
     * This function adds an item to the TYPO3 CSM, with a sublevel
     * to directly define a page doktype.
     * 
     * @param   object  $backRef    The click menu object
     * @param   array   $menuItems  An array with the exisiting menu items
     * @param   string  $table      The table of the click item
     * @param   int     $uid        The UID of the click item
     * @return  array   An array with menu items
     */
    public function main( clickMenu $backRef, array $menuItems, $table, $uid )
    {
        // Detects the menu level
        if( !isset( $backRef->cmLevel ) || !$backRef->cmLevel ) {
            
            // Checks if the clicked item is a page and is editable
            if( isset( $backRef->editOK ) && $backRef->editOK && $table == 'pages' ) {
                
                // Adds a spacer
                $this->_csmItems[]                       = 'spacer';
                
                // Adds a container
                $this->_csmItems[ __CLASS__ ] = $backRef->linkItem(
                    self::$_labels[ 'cm1.title' ],
                    $backRef->excludeIcon( '' ),
                    'top.loadTopMenu(\'' . t3lib_div::linkThisScript() . '&cmLevel=1&subname=' . __CLASS__ . '\'); return false;',
                    0,
                    1
                );
                
                // Resets the menu items array
                reset( $menuItems );
                
                // Counter
                $c = 0;
                
                // Process each menu item
                foreach( $menuItems as $key => $value ) {
                    
                    // Increases the counter
                    $c++;
                    
                    // Finds the "cut" item
                    if( !strcmp( $key, 'cut' ) ) {
                        
                        // Exit
                        break;
                    }
                }
                
                // Inserts the menu items from this extension
                array_splice( $menuItems, $c, 0, $this->_csmItems );
            }
            
        } elseif( $this->_subName === __CLASS__ ) {
            
            // Checks if the clicked item is a page and is editable
            if( isset( $backRef->editOK ) && $backRef->editOK && $table == 'pages' ) {
                        
                // Location
                $location                         = 'top.content' . ( ( $backRef->listFrame && !$backRef->alwaysContentFrame ) ? '.list_frame' : '' );
                
                // Frame location
                $frameLocation                    = $backRef->frameLocation( $location . '.document' );
                
                // Verification code
                $veriCode                         = self::$_beUser->veriCode();
                
                // Not in menu icon
                $iconsHideInMenu                  = '<img '
                                                  . self::$_icons[ 'cm1.hideInMenu' ]
                                                  . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
                
                // Show in menus icon
                $iconsShowInMenu                  = '<img '
                                                  . self::$_icons[ 'cm1.showInMenu' ]
                                                  . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
                        
                // Creates the on click parameter for the hide in menu option
                $hideInMenuOnClick                = 'if( ' . $location . ' ) { '
                                                  . $location . '.document.location = top.TS.PATH_typo3 + \'tce_db.php?'
                                                  . 'redirect=\' + top.rawurlencode( ' . $frameLocation . ' ) + '
                                                  . '\'&data[pages][' . $uid . '][nav_hide]=1'
                                                  . '&prErr=1'
                                                  . '&vC=' . $veriCode . '\'; '
                                                  . 'hideCM(); '
                                                  . '}';
                
                // Creates the on click parameter for the show in menu option
                $showInMenuOnClick                = 'if( ' . $location . ' ) { '
                                                  . $location . '.document.location = top.TS.PATH_typo3 + \'tce_db.php?'
                                                  . 'redirect=\' + top.rawurlencode( ' . $frameLocation . ' ) + '
                                                  . '\'&data[pages][' . $uid . '][nav_hide]=0'
                                                  . '&prErr=1'
                                                  . '&vC=' . $veriCode . '\'; '
                                                  . 'hideCM(); '
                                                  . '}';
                
                // Adds the hide in menu element
                $this->_csmItems[ 'hideinmenu' ]  = $backRef->linkItem(
                    self::$_labels[ 'cm1.hideInMenu' ],
                    $backRef->excludeIcon( $iconsHideInMenu ),
                    $hideInMenuOnClick,
                    0
                );
                
                // Adds the show in menu element
                $this->_csmItems[ 'showinmenu' ] = $backRef->linkItem(
                    self::$_labels[ 'cm1.showInMenu' ],
                    $backRef->excludeIcon( $iconsShowInMenu ),
                    $showInMenuOnClick,
                    0
                );
                
                // Adds a spacer
                $this->_csmItems[]                = 'spacer';
                
                // Process each page type
                foreach( self::$_dokTypes as $key => $value ) {
                    
                    // Checks the user permissions
                    if( self::$_beUser->isAdmin() || t3lib_div::inList( self::$_beUser->groupData[ 'pagetypes_select' ], $key ) ) {
                        
                        // Item icon
                        $icon     = '<img '
                                  . self::$_icons[ 'dokType.' . $key ]
                                  . ' alt="" hspace="0" vspace="0" border="0" align="middle">';
                        
                        // Creates the on click parameter
                        $onClick  = 'if( ' . $location . ' ) { '
                                  . $location . '.document.location = top.TS.PATH_typo3 + \'tce_db.php?'
                                  . 'redirect=\' + top.rawurlencode( ' . $frameLocation . ' ) + '
                                  . '\'&data[pages][' . $uid . '][doktype]=' . $key
                                  . '&prErr=1'
                                  . '&vC=' . $veriCode . '\'; '
                                  . 'hideCM(); '
                                  . '}';
                                                
                        // Adds the menu item
                        $this->_csmItems[ $value ] = $backRef->linkItem(
                            self::$_labels[ 'dokType.' . $key ],
                            $backRef->excludeIcon( $icon ),
                            $onClick,
                            0
                        );
                    }
                }
                
                // Overwrites the menu items
                $menuItems = $this->_csmItems;
            }
        }
        
        // Returns the menu items
        return $menuItems;
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/setpagetype/class.tx_setpagetype_cm1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/setpagetype/class.tx_setpagetype_cm1.php']);
}
