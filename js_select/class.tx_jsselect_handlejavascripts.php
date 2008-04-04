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
 * Class/Function which manipulates the item-array for table/field pages_tx_jsselect_javascripts.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     2.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *   42:    class tx_jsselect_handleJavascripts
 *   60:    protected function _addJavaScripts( $path, $relDir, $recursive = false )
 *  136:    public function main( array &$params, $pObj )
 * 
 *          TOTAL FUNCTIONS: 2
 */

class tx_jsselect_handleJavascripts
{
    // Extensions for JS files
    protected $_jsExt = array();
    
    // Items for the select menu
    protected $_items = array();
    
    /**
     * Adds JS files to the items array
     * 
     * This function will add the names of all the JS files contained in the
     * specified directory.
     * 
     * @param   string  $path       The absolute path of the directory to read
     * @param   string  $relDir     The path of the directory, relative to the TYPO3 site
     * @param   boolean $recursive  If this is set, the JS files contained in sub-directories will also be added
     */
    protected function _addJavaScripts( $path, $relDir, $recursive = false )
    {
        // SPL iterator class to use, depending of the recursivity settings
        $iteratorClass     = ( $recursive ) ? 'RecursiveDirectoryIterator' : 'DirectoryIterator';
        
        // New instance of the directory iterator class
        $directoryIterator = new $iteratorClass( $path );
        
        // New instance of the iterator iterator class
        $iterator          = new RecursiveIteratorIterator( $directoryIterator );
        
        // Storage for files
        $files             = array();
        
        // Process each file
        foreach( $iterator as $file ) {
            
            // Checks if the current file is a directory
            if( $file->isDir() ) {
                
                // Do not process directories
                continue;
            }
            
            // Gets the file name
            $fileName = $file->getFilename();
            
            // Gets the position of the extension
            $dotPos   = strrpos( $fileName, '.' );
            
            // Checks for an extension
            if( !$dotPos ) {
                
                // No extension - Process the next file
                continue;
            }
            
            // Gets the file extension
            $ext = substr( $fileName, $dotPos + 1 );
            
            // Checks for a valid extension
            if( !isset( $this->_jsExt[ $ext ] ) ) {
                
                // Invalid extension - Process the next file
                continue;
            }
            
            // Gets the file path relative to the given directory
            $fileRelPath = str_replace( $path, $relDir, $file->getPath() . '/' );
            
            // File ID, used to sort the JS files in the items array
            $fileId = $fileName . '-' . $fileRelPath; 
            
            // Adds the current JS file to the parameters array
            $this->_items[ $fileId ] = array(
                $fileName . ' (' . $fileRelPath . ')',  // Label
                $fileRelPath . $fileName,               // Value
                'EXT:js_select/res/js.gif'              // Icon
            );
        }
        
        // Returns the JS files
        return $files;
    }
    
    /**
     * Adds items to the JavaScript selector.
     * 
     * This function reads all the JS file in the defined JavaScript
     * directory, and adds the references to the selector.
     * 
     * @param   array   &$params    The parameters of the form
     * @param   object  $pObj       The parent object
     * @return  NULL
     * @see     _addJavaScripts
     */
    public function main( array &$params, $pObj )
    {
        // Stores a reference to the items array
        $this->_items =& $params[ 'items' ];
        
        // Checks for the extension configuration
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'js_select' ] ) ) {
            
            // Gets the extension configuration
            $extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'js_select' ] );
            
            // Gets the page TSConfig for the current page
            $tsConf  = t3lib_BEfunc::getPagesTSconfig( $params[ 'row' ][ 'uid' ] );
            
            // Checks for a configuration in the page TSConfig
            if( isset( $tsConf[ 'tx_jsselect.' ][ 'jsDir' ] ) ) {
                
                // Gets the JS directories from the page TSConfig
                $jsDirs = explode( ',', $tsConf[ 'tx_jsselect.' ][ 'jsDir' ] );
                
            } else {
                
                // Gets the JS directories from the extension configuration
                $jsDirs = explode( ',', $extConf[ 'JSDIR' ] );
            }
            
            // Stores the JS extensions
            $jsExt        = explode( ',', $extConf[ 'JSEXT' ] );
            
            // Stores the list of JS extensions
            $this->_jsExt = array_flip( $jsExt );
            
            // Process each JS directory
            foreach( $jsDirs as $dir ) {
                
                // Ignore leading and trailing white space
                $dir = trim( $dir );
                
                // Adds the trailing slash if necessary
                $dir = ( substr( $dir, -1 ) != '/' ) ? $dir . '/' : $dir;
                
                // Gets the absolute path
                $readPath = t3lib_div::getFileAbsFileName( $dir );
                
                // Checks if the directory exists
                if( file_exists( $readPath ) && is_dir( $readPath ) ) {
                    
                    // Gets all the available JS files
                    $jsFiles = $this->_addJavaScripts( $readPath, $dir, ( boolean )$extConf[ 'ALLOWSUBDIRS' ] );
                }
            }
            
            // Sorts the JS files by name
            ksort( $params[ 'items' ] );
        }
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/js_select/class.tx_jsselect_handlejavascripts.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/js_select/class.tx_jsselect_handlejavascripts.php']);
}
