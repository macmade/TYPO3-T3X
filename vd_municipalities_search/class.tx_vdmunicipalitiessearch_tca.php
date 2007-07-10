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
 * TCA helper for extension vd_municipalities_search
 *
 * @author		Jean-David Gadina <info@macmade.net>
 * @version		1.0
 */

class tx_vdmunicipalitiessearch_tca {
    
    /**
     * Adds municipalities to the selector.
     * 
     * @param   &$params    array   The parameters of the form
     * @param   &$pObj      object  Reference to the parent object
     * @return  NULL
     */
    function getMunicipalities(&$params,&$pObj)
    {
        // Select municipalities
        $records = t3lib_BEfunc::getRecordsByField(
            'tx_vdmunicipalities_municipalities',
            'pid',
            0,
            '',
            '',
            'name_lower'
        );
        
        // Check query result
        if( is_array( $records ) ) {
            
            // Process municipalities
            foreach( $records as $item ) {
                
                // Add item to selector
                $params[ 'items' ][] = array(
                    $item[ 'name_lower' ] . ' (' . $item[ 'id_municipality' ] . ')',
                    $item[ 'id_municipality' ]
                );
            }
        }
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_municipalities_search/class.tx_vdmunicipalitiessearch_tca.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/vd_municipalities_search/class.tx_vdmunicipalitiessearch_tca.php']);
}
