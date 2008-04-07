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
 * Plugin 'JavaScript Selector' for the 'css_select' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     3.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *   48:    class tx_jsselect_pi1
 *   79:    protected function __construct
 *   93:    protected function _buildIndex
 *  127:    protected function _buildScripts
 *  159:    protected function _getJSFiles
 *  235:    public function main( $content, $conf )
 * 
 *          TOTAL FUNCTIONS: 5
 */

// Requires the TYPO3 FE plugin base
require_once( PATH_tslib . 'class.tslib_pibase.php' );

class tx_jsselect_pi1 extends tslib_pibase
{
    // Extension configuration array
    protected $_extConf   = array();
    
    // TypoScript configuration array
    protected $_conf      = array();
    
    // CSS files to load
    protected $_jsFiles  = array();
    
    // New line character
    protected $_NL        = '';
    
    // Tabulation character
    protected $_TAB       = '';
    
    // Class name
    public $prefixId      = 'tx_jsselect_pi1';
    
    // Path to this script relative to the TYPO3 extension directory
    public $scriptRelPath = 'pi1/class.tx_jsselect_pi1.php';
    
    // The extension key
    public $extKey        = 'js_select';
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Sets the new line character
        $this->_NL  = chr( 10 );
        
        // Sets the tabulation character
        $this->_TAB = chr( 9 );
    }
    
    /**
     * Builds the index of the page JavaScripts.
     * 
     * @return  string  An index of the JavaScripts
     */
    protected function _buildIndex()
    {
        // Storage
        $index = array();
        
        // Index counter
        $i     = 1;
        
        // Process each stylesheet
        foreach( $this->_jsFiles as $key => $value ) {
            
            // Adds the stylesheet to the index
            $index[] = $this->_TAB 
                     . ' * '
                     . $i
                     . ') '
                     . $value[ 'file' ]
                     . ' (page ID: '
                     . $value[ 'pid' ]
                     . ')';
            
            // Increments the index counter
            $i++;
        }
        
        // Returns the index
        return implode( $this->_NL, $index );
    }
    
    /**
     * Builds JS <script> tags
     * 
     * @return  string A <script> tag for each script
     */
    protected function _buildScripts()
    {
        // Storage for scripts
        $scripts = array();
        
        // Tag parameters
        $defer   = ( isset( $this->_conf[ 'defer' ] )   && $this->_conf[ 'defer' ] )   ? ' defer="defer"'                               : '';
        $type    = ( isset( $this->_conf[ 'type' ] )    && $this->_conf[ 'type' ] )    ? ' type="'    . $this->_conf[ 'type' ]    . '"' : '';
        $charset = ( isset( $this->_conf[ 'charset' ] ) && $this->_conf[ 'charset' ] ) ? ' charset="' . $this->_conf[ 'charset' ] . '"' : '';

        // Builds script tags
        foreach( $this->_jsFiles as $key => $value ) {
            
            // Adds the current script
            $scripts[] = '<script src="' . $value[ 'file' ] . '"' . $type . $charset . $defer . '></script>';
        }
        
        // Returns the <script> tags
        return implode( $this->_NL, $scripts );
    }
    
    /**
     * Returns all the selected JS file
     * 
     * This function returns the specified JS files for the current page. It
     * also checks, if needed, for selected JavaScripts on the top pages.
     * 
     * @return  mixed   If JS files are found, an array with the JS files relative paths. Otherwise false.
     */
    protected function _getJSFiles()
    {            
        // Storage for the JS files
        $files = array();
        
        // Checks if the recursive option si set
        if( isset( $this->_conf[ 'recursive' ] ) && $this->_conf[ 'recursive' ] ) {
            
            // Check each top page
            foreach( $GLOBALS[ 'TSFE' ]->config[ 'rootLine' ] as $topPage ) {
                
                // Checks the inheritance mode
                if( $topPage[ 'tx_jsselect_inheritance' ] == 1 && $GLOBALS[ 'TSFE' ]->id != $topPage[ 'uid' ] ) {
                    
                    // Process the next page
                    continue;
                    
                } elseif( $topPage[ 'tx_jsselect_inheritance' ] == 2 ) {
                    
                    // Erase stored JavaScripts
                    $files = array();
                    
                    // Checks the current PID
                    if( $GLOBALS[ 'TSFE' ]->id != $topPage[ 'uid' ] ) {
                                                
                        // Process the next page
                        continue;
                    }
                }
                
                // Checks if a JavaScript is specified
                // Thanx to Wolfgang Klinger for the debug
                if( $topPage[ 'tx_jsselect_javascripts' ] ) {
                    
                    // Gets the selected JS files for the current page
                    $pageFiles = explode( ',', $topPage[ 'tx_jsselect_javascripts' ] );
                    
                    // Process each selected file
                    foreach( $pageFiles as $file ) {
                        
                        // Adds the selected JavaScript
                        $files[ $file ] = array(
                            'file' => $file,
                            'pid'  => $topPage[ 'uid' ]
                        );
                    }
                }
            }
            
        } elseif( $GLOBALS[ 'TSFE' ]->page[ 'tx_jsselect_javascripts' ] ) {
            
            // Gets the selected JS files for the current page
            $pageFiles = explode( ',', $GLOBALS[ 'TSFE' ]->page[ 'tx_jsselect_javascripts' ] );
            
            // Process each selected file
            foreach( $pageFiles as $file ) {
                
                // Adds the selected JavaScript
                $files[ $file ] = array(
                    'file' => $file,
                    'pid'  => $GLOBALS[ 'TSFE' ]->page[ 'uid' ]
                );
            }
        }
        
        // Returns the JS files if any, otherwise false
        return ( count( $files ) ) ? $files : false;
    }
    
    /**
     * Adds one or more JavaScript(s) to the TYPO3 page headers.
     * 
     * @param   string  $content    The content object
     * @param   array   $conf       The TS setup
     * @return  string  The additionnal header data
     * @see     _getJSFiles
     * @see     _buildIndex
     * @see     _buildIScripts
     */
    public function main( $content, array $conf )
    {
        // Checks for the extension configuration
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'js_select' ] ) ) {
            
            // Stores the TS configuration array
            $this->_conf     = $conf;
            
            // Stores the extension configuration
            $this->_extConf  = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'js_select' ] );
            
            // Gets the JS files
            if( $this->_jsFiles = $this->_getJSFiles() ) {
                
                // Storage for the header data
                $headerData = array();
                
                // Checks if a comment must be included
                if( isset( $conf[ 'jsComments' ] ) && $conf[ 'jsComments' ] ) {
                    
                    // Adds the JS comment
                    $headerData[] = '<!--';
                    $headerData[] = $this->_TAB . '/***************************************************************';
                    $headerData[] = $this->_TAB . ' * Scripts added by plugin "tx_jsselect_pi1"';
                    $headerData[] = $this->_TAB . ' * ';
                    $headerData[] = $this->_TAB . ' * Index:';
                    $headerData[] = $this->_buildIndex();
                    $headerData[] = $this->_TAB . ' ***************************************************************/';
                    $headerData[] = '-->';
                }
                    
                // Adds the Scripts
                $headerData[] = $this->_buildScripts();
                
                // Return the header data
                return implode( $this->_NL, $headerData ) . $this->_NL;
            }
        }
    }
}

/** 
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/js_select/pi1/class.tx_jsselect_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/js_select/pi1/class.tx_jsselect_pi1.php']);
}
