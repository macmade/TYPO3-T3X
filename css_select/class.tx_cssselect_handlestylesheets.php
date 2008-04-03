<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2008 macmade.net - Jean-David Gadina (info@macmade.net)
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
 * Class/Function which manipulates the item-array for table/field pages_tx_cssselect_stylesheets.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     2.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *     :    class tx_cssselect_handleStylesheets
 *     :    public function main( &$params, $pObj )
 * 
 *          TOTAL FUNCTIONS: 1
 */

class tx_cssselect_handleStylesheets
{
    /**
     * Adds items to the stylesheet selector.
     * 
     * This function reads all the CSS file in the defined stylesheet
     * directory, and adds the references to the selector.
     * 
     * @param   array   &$params    The parameters of the form
     * @param   object  $pObj       The parent object
     * @return  NULL
     */
    public function main( array &$params, $pObj )
    {
        // Checks for the extension configuration
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'css_select' ] ) ) {
            
            // Gets the extension configuration
            $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'css_select' ] );
            
            // Gets the page TSConfig for the current page
            $tsConf  = t3lib_BEfunc::getPagesTSconfig( $params[ 'row' ][ 'uid' ] );
            
            // Checks for a configuration in the page TSConfig
            if( isset( $tsConf[ 'tx_cssselect.' ][ 'cssDir' ] ) ) {
                
                // Gets the CSS directories from the page TSConfig
                $cssDirs = explode( ',', $tsConf[ 'tx_cssselect.' ][ 'cssDir' ] );
                
            } else {
                
                // Gets the CSS directories from the extension configuration
                $cssDirs = explode( ',', $extConf[ 'CSSDIR' ] );
            }
            
            // Process each CSS directory
            foreach( $cssDirs as $dir ) {
                
                // Ignore leading and trailing white space
                $dir = trim( $dir );
                
                // Adds the trailing slash if necessary
                $dir = ( substr( $dir, -1 ) != '/' ) ? $dir . '/' : $dir;
                
                // Gets the absolute path
                $readPath = t3lib_div::getFileAbsFileName( $dir );
                
                // Checks if the directory exists
                if( file_exists( $readPath ) && is_dir( $readPath ) ) {
                                        
                    // Gets all the CSS files in the current directory
                    $cssFiles = t3lib_div::getFilesInDir( $readPath, $extConf[ 'CSSEXT' ], 0, 1 );
                    
                    // Process each CSS file
                    foreach( $cssFiles as $css ) {
                        
                        // Adds the CSS file to the parameters array
                        $params[ 'items' ][ $css . $dir ] = array(
                            $css . ' (' . $dir . ')',
                            $dir . $css,
                            'EXT:css_select/res/css.gif'
                        );
                    }
                }
            }
            
            // Sorts the CSS files by name
            ksort( $params[ 'items' ] );
        }
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/css_select/class.tx_cssselect_handlestylesheets.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/css_select/class.tx_cssselect_handlestylesheets.php']);
}
