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
 * Plugin 'Flash header' for the 'img_pageheader' extension.
 *
 * @author      Jean-David Gadina (macmade@gadlab.net)
 * @version     1.0
 */

// Typo3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

class tx_imgpageheader_pi1 extends tslib_pibase
{
    // Same as class name
    var $prefixId      = 'tx_imgpageheader_pi1';
    
    // Path to this script relative to the extension dir
    var $scriptRelPath = 'pi1/class.tx_imgpageheader_pi1.php';
    
    // The extension key
    var $extKey        = 'img_pageheader';
    
    // Upload directory
    var $uploadDir     = 'uploads/tx_imgpageheader/';
    
    /**
     * Returns the content object of the plugin.
     *
     * This function initialises the plugin "tx_flashpageheader_pi1", and
     * launches the needed functions to correctly display the plugin.
     *
     * @param       $content            The content object
     * @param       $conf               The TS setup
     * @return      The content of the plugin.
     * @see         buildHeaderCode
     */
    function main( $content, $conf )
    {
        // Set class confArray TS from the function
        $this->conf = $conf;
        
        // Load LOCAL_LANG values
        $this->pi_loadLL();
        
        // Build content
        $content = $this->buildHeaderCode();
        
        // Return content
        return $this->pi_wrapInBaseClass( $content );
    }

    /**
     * Returns the code for the header file.
     *
     * This function creates the header picture.
     *
     * @return  An IMG tag.
     * @see     getHeaderFile
     * @see     createImgFromTS
     */
    function buildHeaderCode()
    {
        // Get header file
        $picture     = $this->getHeaderFile( 'tx_imgpageheader_picture' );
        
        // Creating valid pathe
        $picturePath = str_replace(
            PATH_site,
            '',
            t3lib_div::getFileAbsFileName( $picture )
        );
        
        // Storage
        $htmlCode    = array();
        
        if( $this->conf[ 'processImg' ] ) {
            
            // Replacement image
            $imgTSConfig = $this->createImgFromTS( $picturePath );
            
            // Flash code
            $htmlCode[]  = $this->cObj->IMAGE( $imgTSConfig );
            
        } else {
            
            $htmlCode[] = '<img src="'
                        . $picturePath
                        . '" alt="" width="'
                        . $this->conf[ 'width' ]
                        . '" height="'
                        . $this->conf[ 'height' ]
                        . '" />';
        }
        
        // Return content
        return implode( chr( 10 ), $htmlCode );
    }

    /**
     * Return the header file
     *
     * This function checks if a header file is associated with the page. If not,
     * depending of the plugin settings, it checks for a recursive header in the top
     * pages, or returns the default header file.
     *
     * @return      The header file path
     */
    function getHeaderFile( $field )
    {
        if( empty( $GLOBALS[ 'TSFE' ]->page[ $field ] ) ) {
            
            // Default header
            $headerFile = $this->conf[ 'defaultPicture' ];
            
            // Checking for a recursive header
            if( $this->conf[ 'recursive' ] == 1 ) {
                
                foreach( $GLOBALS[ 'TSFE' ]->config[ 'rootLine' ] as $topPage ) {
                    
                    // Recursive header found
                    if ( !empty( $topPage[ $field ] ) ) {
                        
                        $headerFile = $this->uploadDir . $topPage[ $field ];
                    }
                }
            }
        } else {
        
            // Page specific header
            $headerFile = $this->uploadDir . $GLOBALS[ 'TSFE' ]->page[ $field ];
        }
        
        // Return header file
        return $headerFile;
    }

    /**
     * Returns an image ressource array.
     *
     * This function creates the full CObject array for the header picture. It
     * takes values from the constants and setup fields, and adds the specified
     * picture field to the array.
     *
     * @param       $picturePath			The path of the picture to create
     * @return      The image ressource array
     */
    function createImgFromTS( $picturePath )
    {
        // Get image CObject form TS
        $imgTSConfig = $this->conf[ 'picture.' ];
        
        // Adding XY parameters
        $imgTSConfig[ 'file.' ][ 'XY' ]            = $this->conf[ 'width' ] . ',' . $this->conf[ 'height' ];
        
        // Add IMAGE object
        $imgTSConfig[ 'file.' ][ '10' ]            = 'IMAGE';
        
        // Add IMAGE file
        $imgTSConfig[ 'file.' ][ '10.' ][ 'file' ] = $picturePath;
        
        // Return IMAGE CObject
        return $imgTSConfig;
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/img_pageheader/pi1/class.tx_imgpageheader_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/img_pageheader/pi1/class.tx_imgpageheader_pi1.php']);
}
