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
 * Plugin 'TypoScript Object' for the 'tscobj' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     2.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *   44:		class tx_tscobj_pi1 extends tslib_pibase
 *  102:		function main( $content, array $conf )
 * 
 *				TOTAL FUNCTIONS: 1
 */

// TYPO3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

class tx_tscobj_pi1 extends tslib_pibase
{
    // Available content objets
    protected static $_cTypes = array(
        'HTML'             => true,
        'TEXT'             => true,
        'COBJ_ARRAY'       => true,
        'COA'              => true,
        'COA_INT'          => true,
        'FILE'             => true,
        'IMAGE'            => true,
        'IMG_RESOURCE'     => true,
        'CLEARGIF'         => true,
        'CONTENT'          => true,
        'RECORDS'          => true,
        'HMENU'            => true,
        'CTABLE'           => true,
        'OTABLE'           => true,
        'COLUMNS'          => true,
        'HRULER'           => true,
        'IMGTEXT'          => true,
        'CASE'             => true,
        'LOAD_REGISTER'    => true,
        'RESTORE_REGISTER' => true,
        'FORM'             => true,
        'SEARCHRESULT'     => true,
        'USER'             => true,
        'USER_INT'         => true,
        'PHP_SCRIPT'       => true,
        'PHP_SCRIPT_INT'   => true,
        'PHP_SCRIPT_EXT'   => true,
        'TEMPLATE'         => true,
        'MULTIMEDIA'       => true,
        'EDITPANEL'        => true
    );
    
    // TypoScript configuration array
    protected $_conf          = array();
    
    // Class name
    public $prefixId          = 'tx_tscobj_pi1';
    
    // Path to this script relative to the extension dir
    public $scriptRelPath     = 'pi1/class.tx_tscobj_pi1.php';
    
    // The extension key
    public $extKey            = 'tscobj';
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_tscobj_pi1', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param   string  $content    The plugin content
     * @param   array   $conf       The TS setup
     * @return  string  The content of the plugin
     */
    public function main( $content, array $conf )
    {
        // Stores the TypoScript configuration
        $this->_conf = $conf;
        
        // Sets the default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Load LOCAL_LANG values
        $this->pi_loadLL();
        
        // Initialize the flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Gets the TS object path
        $tsObjPath = $this->pi_getFFvalue(
            $this->cObj->data[ 'pi_flexform' ],
            'object'
        );
        
        // Check for a non empty value
        if( $tsObjPath ) {
            
            // Gets the complete TS template
            $tmpl     =& $GLOBALS[ 'TSFE' ]->tmpl->setup;
            
            // Final TS object storage
            $tsObj    =& $tmpl;
            
            // Gets the TS object hierarchy in template
            $tmplPath =  explode( '.', $tsObjPath );
            
            // TS heirarchy levels
            $tsLevels = count( $tmplPath );
            
            // Process the TS object hierarchy
            for( $i = 0; $i < $tsLevels; $i++ ) {
                
                // Checks if the TS object configuratio exists
                if( !isset( $tsObj[ $tmplPath[ $i ] . '.' ] ) ) {
                    
                    // The TS object configuration does not exist
                    $error = 1;
                    break;
                }
                
                // Checks if we are in the last level of the TS hierarchy
                if( $i == $tsLevels - 1 ) {
                    
                    // Gets the content type
                    $cType = ( isset( $tsObj[ $tmplPath[ $i ] ] ) ) ? $tsObj[ $tmplPath[ $i ] ] : false;
                }
                
                // Gets the TS object configuration array
                $tsObj =& $tsObj[ $tmplPath[ $i ] . '.' ];
            }
            
            // DEBUG ONLY - Shows the TS object
            #t3lib_div::debug( $cType, 'CONTENT TYPE' );
            #t3lib_div::debug( $tsObj, 'TS CONFIGURATION' );
            
            // Checks the object and the content type
            if ( isset( $error ) ) {
                
                // Object not found
                return '<strong>' . $this->pi_getLL( 'errors_notfound' ) . '</strong> (' . $tsObjPath . ')';
                
            } elseif( !$cType ) {
                
                // No content type
                return '<strong>' . $this->pi_getLL( 'errors.notype' ) . '</strong> (' . $cType . ')';
                
            } elseif( !isset( self::$_cTypes[ $cType ] ) ) {
                
                // Invalid content type
                return '<strong>' . $this->pi_getLL( 'errors.invalid' ) . '</strong> (' . $cType . ')';
                
            } else {
                
                // Checks for the htmlspecialchars option
                if( $this->pi_getFFvalue( $this->cObj->data[ 'pi_flexform' ], 'htmlspecialchars' ) ) {
                
                    // Renders the object
                    $code = nl2br( htmlspecialchars( $this->cObj->cObjGetSingle( $cType, $tsObj ) ) );
                    
                } else {
                    
                    // Renders the object
                    $code = $this->cObj->cObjGetSingle( $cType, $tsObj );
                }
                
                // Returns the object
                return $code;
            }
        }
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tscobj/pi1/class.tx_tscobj_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tscobj/pi1/class.tx_tscobj_pi1.php']);
}
