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
 * Plugin 'CountDown / macmade.net' for the 'countdown_macmade' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:     1 - MAIN
 *     104:     function main( $content, $conf )
 *     148:     function setConfig
 *     175:     function buildCounter
 * 
 *              TOTAL FUNCTIONS: 3
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_countdownmacmade_pi1 extends tslib_pibase
{
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_countdownmacmade_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_countdownmacmade_pi1.php';
    
    // The extension key
    var $extKey             = 'countdown_macmade';
    
    // Version of the Developer API required
    var $apimacmade_version = 4.4;
    
    // Check plugin hash
    var $pi_checkCHash      = true;
    
    // Configuration array
    var $conf               = array();
    
    // Plugin variables
    var $piVars             = array();
    
    // Instance of the Developer API
    var $api                = NULL;
    
    // New line character
    var $NL                 = '';
    
    // Upload folder
    var $uploadDir          = 'uploads/tx_countdownmacmade/';
    
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
     * @param   string  $content    The content object
     * @param   array   $conf       The TS setup
     * @return  string  The content of the plugin.
     */
    function main( &$content, &$conf )
    {
        // New instance of the macmade.net API
        $this->api =& tx_apimacmade::newInstance(
            'tx_apimacmade',
            array(
                &$this
            )
        );
        
        // Set default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Load locallang labels
        $this->pi_loadLL();
        
        // Sets the new line character
        $this->NL = chr( 10 );
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // Returns the content
        return $this->pi_wrapInBaseClass( $this->buildCounter() );
    }
    
    /**
     * Set configuration array
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return  null
     */
    function setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array(
            'year'       => 'sDEF:year',
            'month'      => 'sDEF:month',
            'day'        => 'sDEF:day',
            'hours'      => 'sDEF:hours',
            'minutes'    => 'sDEF:minutes',
            'hide'       => 'sDEF:hide',
            'backColor'  => 'sLAYOUT:back_color',
            'frontColor' => 'sLAYOUT:front_color',
            'textColor'  => 'sLAYOUT:text_color',
            'image'      => 'sLAYOUT:image'
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->conf,
            $this->piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug( $this->conf, 'CountDown / macmade.net: configuration array' );
    }
    
    /**
     * Builds the counter
     * 
     * @return  string  The counter code
     */
    function buildCounter()
    {
        // Target date
        $targetDate  = strtotime( $this->conf[ 'year' ] . '-' . $this->conf[ 'month' ] . '-' . $this->conf[ 'day' ] . ' ' . $this->conf[ 'hours' ] . ':' . $this->conf[ 'minutes' ] );
        
        // Number of miliseconds to target date
        $miliseconds = $targetDate - time();
        
        // Checks if the date is in the future
        if( $miliseconds < 1 && $this->conf[ 'hide' ] ) {
            
            // Do not display the counter
            return '';
        }
        
        // Storage
        $htmlCode = array();
        
        // Main script source
        $scriptSrc = t3lib_extMgm::siteRelPath( $this->extKey ) . 'res/counter.js';
        
        // Adds the main script
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ $this->prefixId ] = '<script src="' . $scriptSrc . '"type="text/javascript" charset="utf-8"></script>';
        
        // Checks for an image
        if( $this->conf[ 'image' ] ) {
            
            // Creates the image
            $image = $this->api->fe_createImageObjects(
                $this->conf[ 'image' ],
                $this->conf[ 'image.' ],
                $this->uploadDir
            );
            
            // Adds the image
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'image', $image );
        }
        
        // Counter parts
        $parts = array( 'days', 'hours', 'minutes', 'seconds' );
        
        // Process each counter part
        foreach( $parts as $key => $value ) {
            
            // Content of the part
            $content = $this->api->fe_makeStyledContent(
                'div',
                'time',
                '0',
                1,
                false,
                false,
                array(
                    'id'    => 'tx-countdownmacmade-pi1-' . $value . '-' . $this->cObj->data[ 'uid' ],
                    'style' => 'color: ' . $this->conf[ 'textColor' ] . '; background-color: ' . $this->conf[ 'frontColor' ]
                )
            );
            
            // Label of the part
            $label = $this->api->fe_makeStyledContent( 'div', 'label', $this->pi_getLL( $value ) );
            
            // Adds the part
            $htmlCode[] = $this->api->fe_makeStyledContent( 'div', 'box', $content . $label );
        }
        
        // Launch script
        $script = array(
            'var ' . $this->prefixId . '             = new tx_countdownmacmade_pi1_counter( ' . $miliseconds . ' );',
            $this->prefixId . '.infos.days      = "tx-countdownmacmade-pi1-days' . '-' . $this->cObj->data[ 'uid' ] . '";',
            $this->prefixId . '.infos.hours     = "tx-countdownmacmade-pi1-hours' . '-' . $this->cObj->data[ 'uid' ] . '";',
            $this->prefixId . '.infos.minutes   = "tx-countdownmacmade-pi1-minutes' . '-' . $this->cObj->data[ 'uid' ] . '";',
            $this->prefixId . '.infos.seconds   = "tx-countdownmacmade-pi1-seconds' . '-' . $this->cObj->data[ 'uid' ] . '";',
            $this->prefixId . '.init();'
        );
        
        // Adds the launch script
        $htmlCode[] = '<script type="text/javascript" charset="utf-8">'
                    . $this->NL
                    . '// <![CDATA['
                    . $this->NL
                    . implode( $this->NL, $script )
                    . $this->NL
                    . '// ]] >'
                    . $this->NL
                    . '</script>';
        
        // Returns the counter
        return $this->api->fe_makeStyledContent(
            'div',
            'counter',
            implode( $this->NL, $htmlCode ),
            1,
            false,
            false,
            array(
                'style' => 'color: ' . $this->conf[ 'textColor' ] . '; background-color: ' . $this->conf[ 'backColor' ]
            )
        );
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/countdown_macmade/pi1/class.tx_countdownmacmade_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/countdown_macmade/pi1/class.tx_countdownmacmade_pi1.php']);
}
?>
