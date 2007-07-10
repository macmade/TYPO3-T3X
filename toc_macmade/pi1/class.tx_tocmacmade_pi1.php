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
 * Plugin 'Table of contents' for the 'toc_macmade' extension.
 *
 * @author		Jean-David Gadina (info@macmade.net)
 * @version		1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:		1 - MAIN
 *     118:		function main($content,$conf)
 *     165:     function setConfig
 *     188:     function processPageData
 *     265:     function createToc
 * 
 *				TOTAL FUNCTIONS: 4
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_tocmacmade_pi1 extends tslib_pibase
{
    
    
    
    
    
    /***************************************************************
     * SECTION 0 - VARIABLES
     *
     * Class variables for the plugin.
     ***************************************************************/
    
    // Same as class name
    var $prefixId           = 'tx_tocmacmade_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath      = 'pi1/class.tx_tocmacmade_pi1.php';
    
    // The extension key
    var $extKey             = 'toc_macmade';
    
    // Version of the Developer API required
    var $apimacmade_version = 2.8;
    
    // Page object
    var $page               = array();
    
    // Page content elements
    var $elements           = array();
    
    // System language ID
    var $lang               = 0;
    
    // Configuration array
    var $conf               = array();
    
    // COntent table
    var $cTable             = '';
    
    // Excluded contents
    var $excludedContents   = array();
        
    // Excluded types
    var $excludedTypes      = array();
    
    
    
    
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Functions for the initialization and the output of the plugin.
     ***************************************************************/
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_tocmacmade_pi1', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param		$content			The content object
     * @param		$conf				The TS setup
     * @return		The content of the plugin
     * @see         processPageData
     * @see         createToc
     */
    function main( $content, $conf )
    {
        
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // New instance of the Developer API
        $this->api = new tx_apimacmade( $this );
        
        // Set default plufin variables
        $this->pi_setPiVarDefaults();
        
        // Load LOCAL_LANG values
        $this->pi_loadLL();
        
        // Init flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Store flexform informations
        $this->piFlexForm =& $this->cObj->data[ 'pi_flexform' ];
        
        // Set final configuration (TS or FF)
        $this->setConfig();
        
        // System language ID
        $this->lang = $GLOBALS[ 'TSFE' ]->sys_page->sys_language_uid;
        
        // Content table
        $this->cTable = $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'contentTable' ];
        
        // Process page data & create table of contents
        if ( $this->processPageData() && $toc = $this->createToc() ) {
            
            // Return content
            return $this->pi_wrapInBaseClass( $toc );
        }
    }
    
    /**
     * Set configuration array
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return		Void
     */
    function setConfig() {
        
        // Mapping array for PI flexform
        $flex2conf = array(
            'excludeContents' => 'sDEF:exclude_content',
            'excludeTypes'    => 'sDEF:exclude_type',
        );
        
        // Ovverride TS setup with flexform
        $this->conf = $this->api->fe_mergeTSconfFlex( $flex2conf, $this->conf, $this->piFlexForm );
        
        // DEBUG ONLY - Output configuration array
        #$this->api->debug($this->conf,'Table of contents: configuration array');
    }
    
    /**
     * Process page data
     * 
     * This function is used to get informations about the content elements
     * of the page in the current frontend language.
     * 
     * @return      boolean
     */
    function processPageData()
    {
        
        // Excluded contents
        $this->excludedContents = array_flip( explode( ',', $this->conf[ 'excludeContents' ] ) );
        
        // Excluded types
        $this->excludedTypes = array_flip( explode( ',', $this->conf[ 'excludeTypes' ] ) );
        
        // Reference to the page data
        $this->page =& $GLOBALS[ 'TSFE' ]->page;
        
        // Content elements XML data structure
        $xmlData =& $this->page[ 'tx_templavoila_flex' ];
        
        // Array width content elements data structure
        $dataArray = $this->api->div_xml2array( $xmlData, 0 );
        
        // Check structure of elements data
        if ( isset( $dataArray[ 'T3FlexForms' ][ 'data' ][ 'sDEF' ] ) ) {
            
            // Language sheets
            $languages = array_shift( $dataArray[ 'T3FlexForms' ][ 'data' ][ 'sDEF' ][ 'lDEF' ] );
            
            // Check if language is the default one
            if ( $this->lang == 0 ) {
                
                // Content elements (comma list)
                $elements =& $languages[ 'vDEF' ];
                
            } else {
                
                // Get site language record
                $lang = $this->pi_getRecord( 'sys_language', $this->lang );
                
                // Language ISO code ID
                $isoCodeId = $lang[ 'static_lang_isocode' ];
                
                // Get language from static info tables
                $staticLang = $this->pi_getRecord( 'static_languages', $isoCodeId );
                
                // Check language
                if ( $staticLang ) {
                    
                    // Language ISO code
                    $isoCode = $staticLang[ 'lg_iso_2' ];
                    
                    // DS language sheet identifier
                    $llSheet = 'v' . $isoCode;
                    
                    // Content elements (comma list)
                    $elements =& $languages[ $llSheet ];
                }
            }
        }
        
        // Check for elements
        if ( $elements ) {
            
            // Store elements array
            $this->elements = explode( ',', $elements );
            return true;
            
        } else {
            
            // Cannot find elements
            return false;
        }
    }
    
    /**
     * Create table of contents
     * 
     * This function is used to create the full table of contents.
     * 
     * @return      The full table of contents
     */
    function createToc()
    {
        
        // Storage
        $htmlCode = array();
        
        // Process elements
        foreach( $this->elements as $uid ) {
            
            // Do not include table of contents
            if ( $uid == $this->cObj->data[ 'uid' ] ) {
                continue;
            }
            
            // Excluded elements
            if ( isset( $this->excludedContents[ $uid ] ) ) {
                continue;
            }
            
            // Get content record
            $record = $this->pi_getRecord( $this->cTable, $uid );
            
            if ( is_array( $record ) ) {
                
                // Excluded types
                if ( isset( $this->excludedTypes[ $record[ 'CType' ] ] ) ) {
                    continue;
                }
                
                // Check for a record header
                if ( $header =& $record[ 'header' ] ) {
                    
                    // Anchor ID
                    $anchorId = '#' . $this->conf[ 'anchorPrefix' ] . $uid;
                    
                    // Link to content element
                    $href = $this->pi_linkTP_keepPIvars_url() . $anchorId;
                    
                    // Typolink configuration
                    $typoLinkConf = array(
                        'parameter' => $href,
                        'title' => $header,
                    );
                    
                    // Link
                    $link = $this->cObj->typoLink( $header, $typoLinkConf );
                    
                    // List item additionnal parameters
                    $liConf = array(
                        'id' => $this->prefixId . '-element-' . $uid,
                    );
                    
                    // Add list item
                    $htmlCode[] = $this->api->fe_makeStyledCOntent( 'li', 'element', $link, 1, 0, 0, $liConf );
                }
               }
        }
        
        // Check for content elements
        if ( count( $htmlCode ) ) {
            
            // Return full table of contents
            return $this->api->fe_makeStyledCOntent( 'ul', 'toc', implode( chr( 10 ), $htmlCode) );
            
        } else {
            
            // No content element
            return false;
        }
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/toc_macmade/pi1/class.tx_tocmacmade_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/toc_macmade/pi1/class.tx_tocmacmade_pi1.php']);
}
?>
