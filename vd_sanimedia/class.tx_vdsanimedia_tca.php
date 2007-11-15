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
 * TCA helper for extension vd_sanimedia
 *
 * @author		Jean-David Gadina <info@macmade.net>
 * @version		1.0
 */

class tx_vdsanimedia_tca
{
    /**
     * Fills the parameters array with the page fields
     * 
     * @param   array   &$params    The parameters of the form
     * @param   object  &$pObj      A reference to the parent object
     * @return  NULL
     */
    function getPageFields( &$params, &$pObj )
    {
        // Get fields
        $fields = $GLOBALS[ 'TYPO3_DB' ]->admin_get_fields( 'pages' );
        
        // Sorts the fields array
        ksort( $fields );
        
        // Process each field
        foreach( $fields as $key => $value ) {
            
            // Adds the item
            $params[ 'items' ][] = array( $key, $key );
        }
    }
    
    /**
     * Gets a comma list of the sanimedia sysfolders
     * 
     * @return  string  A comma list of the sysfolders UIDs
     */
    function getStoragePid()
    {
        // Select Sanimedia sysfolders
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            'uid',
            'pages',
            'module="sanimedia"'
        );
        
        // Storage
        $pidList = array();
        
        // Check DB ressource
        if( $res && $GLOBALS[ 'TYPO3_DB' ]->sql_num_rows( $res ) > 0 ) {
            
            // Process each sysfolder
            while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                
                // Adds the UID
                $pidList[] = $row[ 'uid' ];
            }
            
            // Returns as a comma list
            return implode( ',', $pidList );
        }
        
        // No storage PID
        return false;
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_sanimedia/class.tx_vdsanimedia_tca.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_sanimedia/class.tx_vdsanimedia_tca.php']);
}
