<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2005 macmade.net
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
 * TCA helper class for extension 'vd_geomap_prototype'.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *      57:     function showHelp( &$params, &$pObj )
 * 
 *              TOTAL FUNCTIONS: 1
 */

class tx_vdgeomapprototype_tca
{
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Base functions.
     ***************************************************************/
    
    /**
     * Display help.
     * 
     * @param       array       &$params            The parameters of the form
     * @param       object      &$pObj              Reference to the parent object
     * @return      NULL
     */
    function showHelp( &$params, &$pObj )
    {
        // Include lang file
        $GLOBALS[ 'LANG' ]->includeLLFile(
            'EXT:vd_geomap_prototype/locallang_db.php'
        );
        
        // Write code
        $htmlCode = ''
                  . '<div class="typo3-view-help">'
                  . '<h2>'
                  . $GLOBALS[ 'LANG' ]->getLL( 'tt_content.flexform_pi1.s_help.help.title.I.0' )
                  . '</h2>'
                  . '<p class="c-nav">'
                  . $GLOBALS[ 'LANG' ]->getLL( 'tt_content.flexform_pi1.s_help.help.content.I.0' )
                  . '</p>'
                  . '<h2>'
                  . $GLOBALS[ 'LANG' ]->getLL( 'tt_content.flexform_pi1.s_help.help.title.I.1' )
                  . '</h2>'
                  . '<p class="c-nav">'
                  . $GLOBALS[ 'LANG' ]->getLL( 'tt_content.flexform_pi1.s_help.help.content.I.1' )
                  . '</p>'
                  #. '<div><img src="'
                  #. t3lib_extMgm::extRelPath( 'vd_geomap_prototype' )
                  #. 'res/help.jpg" alt="Geomap" width="500" height="290" /></div>'
                  . '</div>';
        
        // Return code
        return $htmlCode;
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_geomap_prototype/class.tx_vdgeomapprototype_tca.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_geomap_prototype/class.tx_vdgeomapprototype_tca.php']);
}
