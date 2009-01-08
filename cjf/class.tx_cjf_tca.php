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
 * TCA helper class for extension 'cjf'.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

class tx_cjf_tca
{
    /**
     * Display help.
     * 
     * @param   array   &$params    The parameters of the form
     * @param   object  &$pObj      Reference to the parent object
     * @return  void
     */
    function numberInput( &$params, &$pObj )
    {
        // Storage
        $htmlCode = array();
        $jsFunc   = array();
        
        // Input parameters
        $inputName     = $params[ 'itemFormElName' ];
        $inputValue    = htmlspecialchars( $params[ 'itemFormElValue' ] );
        $inputOnChange = htmlspecialchars( implode( '', $params[ 'fieldChangeFunc' ] ) );
        $inputId       = uniqid( $params[ 'table' ] . '_' );
        $wizInputId    = uniqid( $params[ 'table' ] . '_' );
        
        // Icon
        $icon = '<img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/goback.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="top">';
        
        // Current row
        $row =& $params[ 'row' ];
        
        // JavaScript function
        $jsFunc[] = '<script type="text/javascript">';
        $jsFunc[] = 'function ' . $inputId . '() {';
        $jsFunc[] = 'oldVal   = Number( document.getElementById( "' . $inputId . '" ).value );';
        $jsFunc[] = 'addVal   = Number( document.getElementById( "' . $wizInputId . '" ).value );';
        $jsFunc[] = 'newVal   = oldVal + addVal;';
        $jsFunc[] = 'if( ( newVal >= 0 ) ) {';
        $jsFunc[] = 'document.getElementById( "' . $inputId . '" ).value = newVal;';
        $jsFunc[] = 'document.getElementById( "' . $wizInputId . '" ).value = 0;';
        $jsFunc[] = '} else {';
        $jsFunc[] = 'alert( "Error" );';
        $jsFunc[] = '}';
        $jsFunc[] = '}';
        $jsFunc[] = '</script>';
        
        // Add JavaScript function
        $htmlCode[] = implode( chr( 10 ), $jsFunc );
        
        // Add input field
        $htmlCode[] = '<input size="10" id="' . $inputId . '" name="' . $inputName . '" value="' . $inputValue . '" onchange="' . $inputOnChange . '"' . $params['onFocus'] . ' />';
        
        // Add icon
        $htmlCode[] = '<a href="#" onclick="javascript:' . $inputId . '();">' . $icon . '</a>';
        
        // Add wizard field
        $htmlCode[] = '<input name="' . $wizInputId . '" id="' . $wizInputId . '" size="5" value="0" />';
        
        // Return code
        return implode( chr( 10 ), $htmlCode );
    }
}

/**
 * XClass inclusion.
 */
if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/cjf/class.tx_cjf_tca.php' ] ) {
    include_once( $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/cjf/class.tx_cjf_tca.php' ] );
}
