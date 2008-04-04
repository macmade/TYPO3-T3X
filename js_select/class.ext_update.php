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
 * Class/Function for updating the extension from older versions.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *   45:    class ext_update
 *   70:    public function __construct
 *  109:    protected function _listPages
 *  225:    protected function _updatePages
 *  286:    public function access
 *  319:    public function main
 * 
 *          TOTAL FUNCTIONS: 5
 */

class ext_update
{
    // SQL result set for pages with selected JavaScripts
    protected static $_res = NULL;
    
    // Instance of the TYPO3 document class
    protected $_doc        = NULL;
    
    // Back path
    protected $_backPath   = '';
    
    // Date format
    protected $_dateFormat = '';
    
    // JS directory
    protected $_jsDir     = '';
    
    // New line character
    protected $_NL         = '';
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Sets the back path
        $this->_backPath   = $GLOBALS[ 'BACK_PATH' ];
        
        // Sets the date format
        $this->_dateFormat = $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'ddmmyy' ]
                           . ' - '
                           . $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'hhmm' ];
        
        // Sets the new line character
        $this->_NL         = chr( 10 );
        
        // New instance of the TYPO3 document class
        $this->_doc        = t3lib_div::makeInstance( 'bigDoc' );
        
        // Checks for the extension configuration
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'js_select' ] ) ) {
            
            // Gets the extension configuration
            $extConf       = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'js_select' ] );
            
            // Gets the JS directories
            $directories   = explode( ',', $extConf[ 'JSDIR' ] );
            
            // Sets the JS directory
            $this->_jsDir  = ( substr( $directories[ 0 ], -1 ) != '/' ) ? $directories[ 0 ] . '/': $directories[ 0 ];
        }
    }
    
    /**
     * List the pages that need an update
     * 
     * This function will create a list of the pages which contains JavaScripts,
     * which are not prefixed by the JS directory. Those pages will need an
     * update, has the extension now stores the relative path of the JavaScripts.
     * 
     * @return  string  The list of the pages to update
     */
    protected function _listPages()
    {
        // Storage
        $htmlCode   = array();
        
        // Page icons
        $pageIcons  = array();
        
        // Starts the form
        $htmlCode[] = '<form action="'
                    . t3lib_div::linkThisScript()
                    . '" method="post" id="updateJsSelect" name="updateJsSelect">';
        
        // Infos
        $htmlCode[] = '<div>'
                    . '<img '
                    . t3lib_iconWorks::skinImg( $this->_backPath, 'gfx/icon_note.gif', '' )
                    . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;'
                    . '<strong>Note:</strong><br />'
                    . 'The following pages need to be updated in order to be compatible with the new version of the "js_select" extension.<br />'
                    . 'Please click on the button below to start the update process.<br />'
                    . '</div>';
        
        // Divider
        $htmlCode[] = $this->_doc->spacer( 10 );
        
        // Submit button
        $htmlCode[] = '<div><input name="submit" type="submit" value="Update the pages" /></div>';
        
        // Divider
        $htmlCode[] = $this->_doc->spacer( 10 );
        $htmlCode[] = $this->_doc->divider( 5 );
        $htmlCode[] = $this->_doc->spacer( 10 );
        
        // Starts the table
        $htmlCode[] = '<table border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="'
                    . $this->_doc->bgColor2
                    . '">';
        
        // Table headers
        $htmlCode[] = '<tr>';
        $htmlCode[] = '<th align="left" valign="middle"></td>';
        $htmlCode[] = '<th align="left" valign="middle"><strong>UID:</strong></td>';
        $htmlCode[] = '<th align="left" valign="middle"><strong>PID:</strong></td>';
        $htmlCode[] = '<th align="left" valign="middle"><strong>Title:</strong></td>';
        $htmlCode[] = '<th align="left" valign="middle"><strong>Selected JavaScripts:</strong></td>';
        $htmlCode[] = '<th align="left" valign="middle"><strong>Creation date:</strong></td>';
        $htmlCode[] = '<th align="left" valign="middle"><strong>Modification date:</strong></td>';
        $htmlCode[] = '</tr>';
        
        // Counter
        $counter    = 0;
        
        // Process each page
        while( $page = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( self::$_res ) ) {
            
            // Checks for the page icon
            if( !isset( $pageIcons[ $page[ 'doktype' ] ] ) ) {
                
                // Gets the icon for the current page type
                $pageIcons[ $page[ 'doktype' ] ] = t3lib_iconWorks::getIconImage( 'pages', $page, $this->_backPath );
            }
            
            // Row color
            $color      = ( $counter == 0 ) ? $this->_doc->bgColor3 : $this->_doc->bgColor4;
            
            // Label start tag
            $labelStart = '<label for="page_' . $page[ 'uid' ] . '">';
            
            // Starts the row
            $htmlCode[] = '<tr bgcolor="'
                        . $color
                        . '">';
            
            // Hidden input for the page ID
            $hidden     = '<input name="pages[]" id="page_'
                        . $page[ 'uid' ]
                        . '" type="hidden" value="'
                        . $page[ 'uid' ]
                        . '" />';
            
            // Page fields
            $htmlCode[] = '<td align="left" valign="middle">' . $labelStart . $pageIcons[ $page[ 'doktype' ] ]                    . '</label></td>';
            $htmlCode[] = '<td align="left" valign="middle">' . $hidden . $page[ 'uid' ]                                          . '</td>';
            $htmlCode[] = '<td align="left" valign="middle">' . $page[ 'pid' ]                                                    . '</td>';
            $htmlCode[] = '<td align="left" valign="middle">' . $labelStart . '<strong>' . $page[ 'title' ]                       . '</strong></label></td>';
            $htmlCode[] = '<td align="left" valign="middle">' . str_replace( ',', '<br />', $page[ 'tx_jsselect_javascripts' ] )  . '</td>';
            $htmlCode[] = '<td align="left" valign="middle">' . date( $this->_dateFormat, $page[ 'crdate' ] )                     . '</td>';
            $htmlCode[] = '<td align="left" valign="middle">' . date( $this->_dateFormat, $page[ 'tstamp' ] )                     . '</td>';
            
            // Ends the row
            $htmlCode[] = '</tr>';
            
            // Sets the counter
            $counter    = ( $counter == 1 ) ? 0 : 1;
        }
        
        // Ends the table
        $htmlCode[] = '</table>';
        
        // Ends the form
        $htmlCode[] = '</form>';
        
        // Return content
        return implode( $this->_NL, $htmlCode );
    }
    
    /**
     * Updates the pages
     * 
     * This function will update all the pages contained in the POST request
     * and add the JS directory to each selected JavaScripts. The page's
     * modification time will also be changed.
     * 
     * @return  string  A confirmation message
     */
    protected function _updatePages()
    {
        // Gets the list of pages to update
        $pages  = t3lib_div::_POST( 'pages' );
        
        // Checks for pages
        if( is_array( $pages ) && count( $pages ) ) {
            
            // Process each server
            foreach( $pages as $uid ) {
                
                // Gets the current page
                $page             = t3lib_BEfunc::getRecord( 'pages', $uid, $fields = 'tstamp,tx_jsselect_javascripts' );
                
                // Sets the modification time
                $page[ 'tstamp' ] = time();
                
                // Gets the selected JS files
                $jsFiles         = explode( ',', $page[ 'tx_jsselect_javascripts' ] );
                
                // Process each JS file
                foreach( $jsFiles as $key => $value ) {
                    
                    // Prefix with the JS directory
                    $jsFiles[ $key ] = $this->_jsDir . $value;
                }
                
                // Updates the selected JavaScripts
                $page[ 'tx_jsselect_javascripts' ] = implode( ',', $jsFiles );
                
                // Updates the page
                $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery(
                    'pages',
                    'uid=' . $uid,
                    $page
                );
            }
        }
        
        // Confirmation message
        $message = '<div>'
                 . '<img '
                 . t3lib_iconWorks::skinImg( $this->_backPath, 'gfx/icon_note.gif', '' )
                 . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;'
                 . '<strong>Success:</strong><br />'
                 . 'The pages were successfully updated. The update option won\'t appear anymore in the extension manager.<br />'
                 . 'Thank you for using the "js_select" extension.'
                 . '</div>';
        
        // Returns the confirmation message
        return $message;
    }
    
    /**
     * Checks if an update is needed.
     * 
     * This function check if old sitemap records are present in the database. It is
     * used to display the update menu in the Typo3 extension manager.
     * 
     * @return  boolean
     */
    public function access()
    {
        // Checks for pages with JavaScripts
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            'uid,pid,crdate,tstamp,title,doktype,tx_jsselect_javascripts',
            'pages',
            'tx_jsselect_javascripts != "" AND tx_jsselect_javascripts NOT LIKE "%/%"',
            '',
            'title'
        );
        
        // Checks the SQL result set
        if( !$res ) {
            
            // No update needed
            return false;
        }
        
        // Stores the SQL result set
        self::$_res = $res;
        
        // Returns true if pages were found
        return ( $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) ) ? true : false;
    }
    
    /**
     * Update extension
     * 
     * This is the main function for updating the dropdown sitemap extension. It is
     * used to display a list of the old records, and to update them.
     * 
     * @return	The content of the class
     */
    public function main()
    {
        // Checks if the form has been submitted
        if( t3lib_div::_POST( 'submit' ) ) {
            
            // Updates the pages
            return $this->_updatePages();
        }
        
        // Default view - List pages which needs an update
        return $this->_listPages();
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/js_select/class.ext_update.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/js_select/class.ext_update.php']);
}
