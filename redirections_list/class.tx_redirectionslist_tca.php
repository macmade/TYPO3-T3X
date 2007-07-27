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
 * TCA helper class for extension 'redirections_list'.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *      57:     function getRedirections( &$params, &$pObj )
 * 
 *              TOTAL FUNCTIONS: 1
 */

class tx_redirectionslist_tca
{
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Base functions.
     ***************************************************************/
    
    function getRedirections( &$params, &$pObj )
    {
        // Gets a reference to the items
        $items =& $params[ 'items' ];
        
        // Removes the first entry
        // Needed in order to avoid a bug with the flexforms
        array_shift( $items );
        
        // Select the redirections from RealURL
        $res = $GLOBALS[ 'TYPO3_DB' ]->exec_SELECTquery(
            '*',
            'tx_realurl_redirects',
            'url_hash'
        );
        
        // Checks the DB resource
        if( $res ) {
            
            // Process each redirection
            while( $row = $GLOBALS[ 'TYPO3_DB' ]->sql_fetch_assoc( $res ) ) {
                
                // Adds the current redirection to the select items
                $items[] = array(
                    $row[ 'url' ] . ' -&gt; ' . $row[ 'destination' ],
                    $row[ 'url_hash' ]
                );
            }
        }
    }
}

// XCLASS inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/redirections_list/class.tx_redirectionslist_tca.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/redirections_list/class.tx_redirectionslist_tca.php']);
}
