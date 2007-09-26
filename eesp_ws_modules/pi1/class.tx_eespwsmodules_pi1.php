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
 * Plugin 'Gallery / macmade.net' for the 'eesp_ws_modules' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.1
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *     117:     function main( $content, $conf )
 * 
 *              TOTAL FUNCTIONS: 
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_eespwsmodules_pi1 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_eespwsmodules_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_eespwsmodules_pi1.php';
    
    // The extension key
    var $extKey             = 'eesp_ws_modules';
    
    // Version of the Developer API required
    var $apimacmade_version = 3.4;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Configuration array
    var $conf               = array();
    
    // Plugin variables
    var $piVars             = array();
    
    // Instance of the Developer API
    var $api                = NULL;
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin "tx_mozsearch_pi1", and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param       $content            The content object
     * @param       $conf               The TS setup
     * @return      The content of the plugin.
     */
    function main( $content, $conf )
    {
        // New instance of the macmade.net API
        $this->api = new tx_apimacmade( $this );
        
        // Set default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Load locallang labels
        $this->pi_loadLL();
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Return content
        return $this->pi_wrapInBaseClass( 'Hello World!' );
    }
    
    /**
     * Set configuration array
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return      Void
     */
    function setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array();
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex( $flex2conf, $this->conf, $this->piFlexForm );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'VD / Municipalities Search: configuration array');
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/pi1/class.tx_eespwsmodules_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eesp_ws_modules/pi1/class.tx_eespwsmodules_pi1.php']);
}
?>
