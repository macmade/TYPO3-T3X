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
 * All of this code comes from the original tslib_menu class, written by
 * Kasper Skaarhoj (kasperYYYY@typo3.com). Only minor changes are applied.
 * 
 * The goal of this extension class is to correct some bugs, optimize some
 * statements, avoid PHP errors (even E_NOTICE) and provide a clean code base.
 * For this last one, some variables have been renamed in order to respect the
 * camelCaps rule. The code formatting also tries to follow the Zend coding
 * guidelines.
 * 
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *        :     
 * 
 *              TOTAL FUNCTIONS: 
 */

class ux_tslib_menu extends tslib_menu
{
    
    
}

/**
 * XClass inclusion.
 */
if( defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tslib_menu.php' ]) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE][ 'XCLASS' ][ 'ext/tslib_patcher/class.ux_tslib_menu.php' ]);
}
