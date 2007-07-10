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
 * TCA helper class for extension 'toc_macmade'.
 *
 * @author		Jean-David Gadina (info@macmade.net)
 * @version		1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 * SECTION:		1 - MAIN
 *      64:		function contentElements( &$params, &$pObj )
 *     166:		function contentTypes( &$params, &$pObj )
 * 
 *				TOTAL FUNCTIONS: 2
 */

// Developer API class
require_once( t3lib_extMgm::extPath( 'api_macmade' ) . 'class.tx_apimacmade.php' );

class tx_tocmacmade_tca
{
    
    // Version of the Developer API required
    var $apimacmade_version = 2.8;
    
    /***************************************************************
     * SECTION 1 - MAIN
     *
     * Base functions.
     ***************************************************************/
    
    /**
     * List content elements.
     * 
     * @param		&$params			The parameters of the form
     * @param		&$pObj				Reference to the parent object
     * @return		NULL
     */
    function contentElements( &$params, &$pObj )
    {
        
        // Unset items
        $params[ 'items' ] = array();
        
        // Deny sign icon
        $icon = t3lib_iconWorks::skinImg( $GLOBALS['BACK_PATH'], 'gfx/icon_fatalerror.gif', '', 1);
        
        // Content element ID
        $uid = $params[ 'row' ][ 'uid' ];
        
        // Content table
        $cTable = $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'contentTable' ];
        
        // Page ID
        $pid = $params[ 'row' ][ 'pid' ];
        
        // Page row
        $page = t3lib_BEfunc::getRecord( 'pages', $pid );
        
        // Content XML data
        $xmlData = $page[ 'tx_templavoila_flex' ];
        
        // Convert XML data to an array
        $dataArray = tx_apimacmade::div_xml2array( $xmlData, 0 );
        
        // Check data array
        if ( $xmlData && isset( $dataArray[ 'T3FlexForms' ][ 'data' ][ 'sDEF' ][ 'lDEF' ] ) ) {
            
            // Languages
            $languages = array_shift( $dataArray[ 'T3FlexForms' ][ 'data' ][ 'sDEF' ][ 'lDEF' ] );
            
            // Current language
            $sysLang = $params[ 'row' ][ 'sys_language_uid' ];
            
            // Check if language is the default one
            if ( $sysLang == 0 ) {
                
                // Content elements (comma list)
                $elements =& $languages[ 'vDEF' ];
                
            } else {
                
                // Get site language record
                $lang = t3lib_BEfunc::getRecord( 'sys_language', $sysLang );
                
                // Language ISO code ID
                $isoCodeId = $lang[ 'static_lang_isocode' ];
                
                // Get language from static info tables
                $staticLang = t3lib_BEfunc::getRecord( 'static_languages', $isoCodeId );
                
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
        
            // Check for elements
            if ( $elements ) {
                
                // Store elements array
                $contents = explode( ',', $elements );
                
                // Process content elements
                foreach( $contents as $contentId ) {
                    
                    // Do not include current record
                    if ( $contentId == $uid ) {
                        continue;
                    }
                    
                    // Get content record
                    $rec = t3lib_BEfunc::getRecord( $cTable, $contentId );
                    
                    // Check for a record header
                    if ( $header =& $rec[ 'header' ] ) {
                        
                        // Add content element
                        $params[ 'items' ][] = array( $header, $contentId, $icon );
                    }
                }
            }
        }
    }
    
    /**
     * List content element types.
     * 
     * @param		&$params			The parameters of the form
     * @param		&$pObj				Reference to the parent object
     * @return		NULL
     */
    function contentTypes( &$params, &$pObj )
    {
        global $LANG;
        
        // Include tt_content & templavoila locallang files
        $LANG->includeLLFile('EXT:cms/locallang_ttc.xml');
        $LANG->includeLLFile('EXT:templavoila/locallang_db.xml');
        
        // Deny sign icon
        $icon = t3lib_iconWorks::skinImg( $GLOBALS['BACK_PATH'], 'gfx/icon_fatalerror.gif', '', 1);
        
        // Content types
        $params[ 'items' ] = array(
            array( $LANG->getLL( 'CType.I.0' ), 'header', $icon ),
            array( $LANG->getLL( 'CType.I.1' ), 'text', $icon  ),
            array( $LANG->getLL( 'CType.I.2' ), 'textpic', $icon  ),
            array( $LANG->getLL( 'CType.I.3' ), 'image', $icon  ),
            array( $LANG->getLL( 'CType.I.4' ), 'bullets', $icon  ),
            array( $LANG->getLL( 'CType.I.5' ), 'table', $icon  ),
            array( $LANG->getLL( 'CType.I.6' ), 'uploads', $icon  ),
            array( $LANG->getLL( 'CType.I.7' ), 'multimedia', $icon  ),
            array( $LANG->getLL( 'CType.I.8' ), 'mailform', $icon  ),
            array( $LANG->getLL( 'CType.I.9' ), 'search', $icon  ),
            array( $LANG->getLL( 'CType.I.10' ), 'login', $icon  ),
            array( $LANG->getLL( 'CType.I.11' ), 'splash', $icon  ),
            array( $LANG->getLL( 'CType.I.12' ), 'menu', $icon  ),
            array( $LANG->getLL( 'CType.I.13' ), 'shortcut', $icon  ),
            array( $LANG->getLL( 'CType.I.14' ), 'list', $icon  ),
            array( $LANG->getLL( 'CType.I.15' ), 'script', $icon  ),
            array( $LANG->getLL( 'CType.I.16' ), 'div', $icon  ),
            array( $LANG->getLL( 'CType.I.17' ), 'html', $icon  ),
            array( $LANG->getLL( 'tt_content.CType_pi1' ), 'templavoila_pi1', $icon  ),
        );
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/toc_macmade/class.tx_tocmacmade_tca.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/toc_macmade/class.tx_tocmacmade_tca.php']);
}
?>
