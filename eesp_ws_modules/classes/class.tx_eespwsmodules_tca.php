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
 * TCA helper for extension eesp_ws_modules
 *
 * @author		Jean-David Gadina <info@macmade.net>
 * @version		1.0
 */

// Loads the Developer API
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_eespwsmodules_tca
{
    // Storage for the SOAP operations
    private static $soapOperations = array();
    
    /**
     * Fills the parameters array with the SOAP operations 
     * 
     * @param   array   &$params    The parameters of the form
     * @param   object  &$pObj      A reference to the parent object
     * @return  NULL
     */
    public function getSoapOperations( &$params, &$pObj )
    {
        // Checks if the SOAP operations already exists
        if( !count( self::$soapOperations ) ) {
            
            try {
                
                // Gets an instance of the flexform helper
                $flex = tx_apimacmade::getPhp5Class( 'flexform', array( &$params[ 'row' ][ 'pi_flexform' ] ) );
                
                // Sets the working sheet
                $flex->setDefaultSheet( 'sWSDL' );
                
                // Gets the WSDL URL
                if( $url = $flex->wsdl_url ) {
                    
                    // Creates a new SOAP client
                    $soapClient = new SoapClient( $url );
                    
                    // Gets the available operations
                    $functions  = $soapClient->__getFunctions();
                    
                    // Process each operation
                    foreach( $functions as $soapOperation ) {
                        
                        // Beginning of the operation name
                        $start = strpos( $soapOperation, ' ' ) + 1;
                        
                        // Gets only the operation name
                        $operationName = substr(
                            $soapOperation,
                            $start,
                            strpos( $soapOperation, '(' ) - $start
                        );
                        
                        // Adds the item
                        self::$soapOperations[] = array( $operationName, $operationName );
                    }
                }
                
            } catch( Exception $e ) {
                
                // Nothing here as we don't want any error message
            }
        }
        
        // Adds the items array
        $params[ 'items' ] = self::$soapOperations;
    }
}

/**
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/classes/class.tx_eespwsmodules_tca.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/classes/class.tx_eespwsmodules_tca.php']);
}
