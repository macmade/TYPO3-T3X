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
 * Class/Function for updating the extension from older versions.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *         :    class ext_update
 *         :    function access
 *         :    function main
 *         :    function selectOldRecords
 * 
 *              TOTAL FUNCTIONS: 3
 */

class ext_update
{
    var $table   = '';
    var $records = array();
    
    function ext_update()
    {
        $this->table   = $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'contentTable' ];
        $this->records = t3lib_BEfunc::getRecordsByField(
            $this->table,
            'list_type',
            'vd_geomap_pi1'
        );
    }
    
    /**
     * Check if an update is needed.
     * 
     * This function check if old sitemap records are present in the database. It is
     * used to display the update menu in the Typo3 extension manager.
     * 
     * @return	boolean
     */
    function access()
    {
        if( is_array( $this->records ) && count( $this->records ) ) {
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Update extension
     * 
     * This is the main function for updating the dropdown sitemap extension. It is
     * used to display a list of the old records, and to update them.
     * 
     * @return	The content of the class
     */
    function main()
    {
        // New instance of the document class
        $this->doc  = t3lib_div::makeInstance( 'bigDoc' );
        
        // Count records
        $recNum     = count( $this->records );
        
        // Counters
        $colorcount = 0;
        
        // Storage
        $htmlCode   = array();
        
        // Check action
        if ( t3lib_div::_GP( 'update' ) ) {
            
            // Infos
            $htmlCode[] = '<p><img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], 'gfx/icon_note.gif', '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;<strong>Here are the results of the update process.</strong><br />If all the records were successfully updated, you won\'t see this page anymore in the extension manager.</p>';
            
            // Divider
            $htmlCode[] = $this->doc->divider( 5 );
            
            // Start table
            $htmlCode[] = '<table id="recList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
            $htmlCode[] = '<tr>';
            $htmlCode[] = '<td align="left" valign="middle"></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Title</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Page</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Path</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Status</strong></td>';
            $htmlCode[] = '</tr>';
                
            // Update fields
            $update = array(
                'list_type' => 'vd_geomap_prototype_pi1',
            );
            
            // Process records
            foreach( $this->records as $row ) {
                
                // Change row color
                $colorcount  = ( $colorcount == 1 ) ? 0                    : 1;
                $color       = ( $colorcount == 1 ) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                
                // Build row parameters
                $tr_params   = ' bgcolor="' . $color . '"';
                
                // Page row
                $page        = t3lib_BEfunc::getRecord( 'pages', $row[ 'pid' ] );
                
                // Where clause to select old sitemap records
                $whereClause = 'uid=' . $row[ 'uid' ];
                
                // Status icon
                $status      = ( $GLOBALS[ 'TYPO3_DB' ]->exec_UPDATEquery( $this->table, $whereClause, $update ) ) ? 'gfx/icon_ok2.gif' : 'gfx/icon_warning.gif';

                // Start row
                $htmlCode[]  = '<tr' . $tr_params . '>'; 
                
                // Fields
                $htmlCode[]  = '<td align="left" valign="middle">' . t3lib_iconWorks::getIconImage( 'tt_content', $row, $GLOBALS[ 'BACK_PATH' ] ) . '</td>';
                $htmlCode[]  = '<td align="left" valign="middle"><strong>' . $row[ 'header' ] . '</strong> (' . $row[ 'uid' ] .')</td>';
                $htmlCode[]  = '<td align="left" valign="middle">' . $page[ 'title' ] . ' (' . $page[ 'uid' ] . ')</td>';
                $htmlCode[]  = '<td align="left" valign="middle">' . t3lib_BEfunc::getRecordPath( $row[ 'pid' ], '', 50 ) . '</td>';
                $htmlCode[]  = '<td align="left" valign="middle"><img ' . t3lib_iconWorks::skinImg( $GLOBALS[ 'BACK_PATH' ], $status, '' ) . ' alt="" hspace="0" vspace="0" border="0" align="middle"></td>';
                
                // End row
                $htmlCode[] = '</tr>';
            }
            
            // End table
            $htmlCode[] = '</table>';
            
        } else {
            
            // Infos
            $htmlCode[] = '<p><img ' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_note.gif','') . ' alt="" hspace="0" vspace="0" border="0" align="middle">&nbsp;<strong>Some of the database records need to be updated in order to use the new version of the extension.</strong><br />Please click the button below to update the records listed here.</p>';
            
            // Spacer
            $htmlCode[] = $this->doc->spacer(5);
            
            // Submit
            $htmlCode[] = '<input name="update" type="submit" value="Update database (' . $recNum . ' record(s) affected)">';
            
            // GET variables
            $CMD = t3lib_div::_GP('CMD');
            $SET = t3lib_div::_GP('SET');
            
            // Hidden inputs to preserve GET variables
            $htmlCode[] = '<input name="id" type="hidden" value="' . t3lib_div::_GP('id') . '">';
            $htmlCode[] = '<input name="CMD[showExt]" type="hidden" value="' . $CMD['showExt'] . '">';
            $htmlCode[] = '<input name="SET[singleDetails]" type="hidden" value="' . $SET['singleDetails'] . '">';
            
            // Divider
            $htmlCode[] = $this->doc->divider(5);
            
            // Start table
            $htmlCode[] = '<table id="recList" border="0" width="100%" cellspacing="1" cellpadding="2" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
            $htmlCode[] = '<tr>';
            $htmlCode[] = '<td align="left" valign="middle"></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Title</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Page</strong></td>';
            $htmlCode[] = '<td align="left" valign="middle"><strong>Path</strong></td>';
            $htmlCode[] = '</tr>';
            
            // Show records
            foreach( $this->records as $row ) {
                
                // Change row color
                $colorcount = ($colorcount == 1) ? 0: 1;
                $color = ($colorcount == 1) ? $this->doc->bgColor4 : $this->doc->bgColor5;
                
                // Build row parameters
                $tr_params = ' bgcolor="' . $color . '"';
                
                // Start row
                $htmlCode[] = '<tr' . $tr_params . '>';
                
                // Page row
                $page = t3lib_BEfunc::getRecord('pages',$row['pid']);
                
                // Fields
                $htmlCode[] = '<td align="left" valign="middle">' . t3lib_iconWorks::getIconImage('tt_content',$row,$GLOBALS['BACK_PATH']) . '</td>';
                $htmlCode[] = '<td align="left" valign="middle"><strong>' . $row['header'] . '</strong> (' . $row['uid'] .')</td>';
                $htmlCode[] = '<td align="left" valign="middle">' . $page['title'] . ' (' . $page['uid'] . ')</td>';
                $htmlCode[] = '<td align="left" valign="middle">' . t3lib_BEfunc::getRecordPath($row['pid'],'',50) . '</td>';
                
                // End row
                $htmlCode[] = '</tr>';
            }
            
            // End table
            $htmlCode[] = '</table>';
        }
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_geomap_prototype/class.ext_update.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_geomap_prototype/class.ext_update.php']);
}
