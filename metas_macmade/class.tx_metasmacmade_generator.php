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
 * Meta tags generation class for the 'metas_macmade' extension.
 * 
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *      42:     class tx_metasmacmade_generator
 *      61:     function tx_metasmacmade_generator
 *      73:     function afterTitleHook
 *      93:     function getMetaTags
 * 
 *              TOTAL FUNCTIONS: 3
 */
class tx_metasmacmade_generator
{
    // Extension configuration
    var $extConf               = array();
    
    // New line character
    var $NL                    = '';
    
    // Tab line character
    var $TAB                   = '';
    
    // Flag for the Dublin Core meta tags
    var $hasDublinCoreMetaTags = false;
    
    /**
     * Class constructor
     * 
     * @return  null
     */
    function tx_metasmacmade_generator()
    {
        // Gets the extension configuration
        $this->extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'metas_macmade' ] );
        
        // Sets the new line character
        $this->NL  = chr( 10 );
        
        // Sets the tabulation character
        $this->TAB = chr( 9 );
    }
    
    function afterTitleHook()
    {
        // Gets the meta tags
        $pageMetas = '<!-- META TAGS: begin -->'
                   . $this->NL
                   . $this->NL
                   . implode( $this->NL, $this->getMetaTags() )
                   . $this->NL
                   . $this->NL
                   . '<!-- META TAGS: end -->';
        
        // Returns the meta tags code for the page
        return $pageMetas;
    }
    
    /**
     * Gets the meta tags for the current page
     * 
     * @return  mixed   An array containing the meta tags for the current page, otherwise false
     */
    function getMetaTags()
    {
        // Storage
        $metaTags = array();
        
        // Default language
        $lang = 'lDEF';
        
        // Checks for a specific language
        if( $GLOBALS[ 'TSFE' ]->sys_page->sys_language_uid > 0 ) {
            
            // Checks for the language configuration
            if( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'config.' ][ 'language' ] ) && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'config.' ][ 'language' ] ) {
                
                // Sets the language
                $lang = 'l' . strtoupper( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'config.' ][ 'language' ] );
                
            } elseif( isset( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'page.' ][ 'config.' ][ 'language' ] ) && $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'page.' ][ 'config.' ][ 'language' ] ) {
                
                // Sets the language
                $lang = 'l' . strtoupper( $GLOBALS[ 'TSFE' ]->tmpl->setup[ 'page.' ][ 'config.' ][ 'language' ] );
            }
        }
        
        // Process the rootline
        foreach( $GLOBALS[ 'TSFE' ]->rootLine as $page ) {
            
            // Checks for XML code
            if( $page[ 'tx_metasmacmade_metas' ] ) {
                
                // Gets an array with the flexform structure
                $flexArray = t3lib_div::xml2array( $page[ 'tx_metasmacmade_metas' ] );
                
                // Process each sheet
                foreach( $flexArray[ 'data' ] as $sheetId => $fields ) {
                    
                    // Checks if we are processing the custom meta tags
                    if( $sheetId == 'sCUSTOM' ) {
                        
                        // Checks for custom meta tags
                        if( isset( $fields[ $lang ][ 'custom' ][ 'el' ] ) ) {
                            
                            // Process each custom meta tag
                            foreach( $fields[ $lang ][ 'custom' ][ 'el' ] as $customMeta ) {
                                
                                // Checks the meta informations
                                if( isset( $customMeta[ 'meta' ][ 'el' ][ 'name' ][ 'vDEF' ] )
                                    && isset( $customMeta[ 'meta' ][ 'el' ][ 'value' ][ 'vDEF' ] )
                                    && $customMeta[ 'meta' ][ 'el' ][ 'name' ][ 'vDEF' ]
                                    && $customMeta[ 'meta' ][ 'el' ][ 'value' ][ 'vDEF' ]
                                ) {
                                    
                                    // Gets the meta name and content
                                    $customMetaName    = $customMeta[ 'meta' ][ 'el' ][ 'name' ][ 'vDEF' ];
                                    $customMetaContent = $customMeta[ 'meta' ][ 'el' ][ 'value' ][ 'vDEF' ];
                                    
                                    // Adds the custom meta tag
                                    $metaTags[ $customMetaName ] = $this->TAB
                                                                 . '<meta name="'
                                                                 . $customMetaName
                                                                 . '" content="'
                                                                 . $customMetaContent
                                                                 . '" />';
                                }
                            }
                        }
                        
                        // Process the next sheet
                        continue;
                    }
                    
                    // Prefix for the meta tag name
                    $metaPrefix = '';
                    
                    // Checks if we are processing Dublin Core elements
                    if( $sheetId == 'sDCMI-BASE' || $sheetId == 'sDCMI-OTHERS' || $sheetId == 'sDCMI-REFINEMENTS' ) {
                        
                        // Adds the Dublin Core prefix to the meta name
                        $metaPrefix = 'DC.';
                    }
                    
                    // Checks the structure
                    if( isset( $fields[ $lang ] ) && is_array( $fields[ $lang ] ) ) {
                        
                        // Process each field
                        foreach( $fields[ $lang ] as $metaName => $metaValue ) {
                            
                            // Checks the current meta tag
                            if( !isset( $metaTags[ $metaPrefix . $metaName ] )
                                && isset( $metaValue[ 'vDEF' ] )
                                && $metaValue[ 'vDEF' ]
                            ) {
                                
                                // Checks the prefix
                                if( $metaPrefix === 'DC.' ) {
                                    
                                    // Sets the Dublin Core flag
                                    $this->hasDublinCoreMetaTags = true;
                                }
                                
                                // Checks if the current element is a Dublin Core refinement
                                if( $sheetId == 'sDCMI-REFINEMENTS' ) {
                                    
                                    // Corrects the meta tag name
                                    $metaName = str_replace( '-', '.', $metaName );
                                }
                                
                                // Adds the meta tag
                                $metaTags[ $metaPrefix . $metaName ] = $this->TAB
                                                                     . '<meta name="'
                                                                     . $metaPrefix . $metaName
                                                                     . '" content="'
                                                                     . $metaValue[ 'vDEF' ]
                                                                     . '" />';
                            }
                        }
                    }
                }
            }
                                
            // Checks if meta tags must be search in the parent pages
            if( !isset( $this->extConf[ 'recursiveMetaTags' ] ) || !$this->extConf[ 'recursiveMetaTags' ] ) {
                
                // Aborts the loop
                break;
            }
        }
        
        // Checks for meta tags
        if( count( $metaTags ) ) {
            
            // Returns the meta tags array
            return $metaTags;
        }
        
        // No meta tags for this page
        return false;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metas_macmade/class.tx_metasmacmade_generator.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metas_macmade/class.tx_metasmacmade_generator.php']);
}
