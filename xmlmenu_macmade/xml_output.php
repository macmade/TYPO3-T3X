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
 * XML page generation script for the 'xmlmenu_macmade' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net) / macmade.net
 * @version     1.0
 */

// Includes the XML menu class
require_once(
    t3lib_extMgm::extPath( 'xmlmenu_macmade' )
  . 'class.tx_xmlmenumacmade_menu.php'
);

// Creates a new instance of the XML menu
$xmlMenu = t3lib_div::makeInstance( 'tx_xmlmenumacmade_menu' );

// Generates the XML menu
$xmlMenu->makeMenu();

// Checks if the XML content type header must be added
if( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'plugin.' ][ 'tx_xmlmenumacmade_pi1.' ][ 'xmlContentTypeHeader' ] == 1 ) {
    
    // Adds the XML header
    header( 'Content-type: text/xml' );
}

// Outputs the XML menu
print $xmlMenu->getXml();

// Aborts the script to prevent additionnal data to be written
exit();
