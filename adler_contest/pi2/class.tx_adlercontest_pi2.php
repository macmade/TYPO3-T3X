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

# $Id$

/** 
 * Plugin 'Profile' for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the frontend plugin base class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_pibase.php' );

class tx_adlercontest_pi2 extends tx_adlercontest_piBase
{
    /**
     * Form fields for the proof documents
     */
    protected static $_proofFields   = array(
        'age_proof'    => array( 'type' => 'file', 'ext' => 'jpg,jpeg', 'size' => 2048 ),
        'school_proof' => array( 'type' => 'file', 'ext' => 'jpg,jpeg', 'size' => 2048 ),
        'later'        => array( 'type' => 'checkbox', 'optionnal' => true )
    );
    
    /**
     * Form fields for the project
     */
    protected static $_projectFields = array(
        'project' => array( 'type' => 'file', 'ext' => 'jpg,jpeg', 'size' => 2048 ),
    );
    
    /**
     * The user row
     */
    protected $_user                 = array();
    
    /**
     * The profile row
     */
    protected $_profile              = array();
    
    /**
     * Configuration mapping array (between TS and Flex)
     */
    protected $_configMap            = array(
        'pid'            => 'sDEF:pages',
        'home.'          => array(
            'header'      => 'sHOME:header',
            'description' => 'sHOME:description'
        ),
        'proof.'         => array(
            'header'      => 'sPROOF:header',
            'description' => 'sPROOF:description'
        ),
        'projectUpload.' => array(
            'header'      => 'sPROJECT:header',
            'description' => 'sPROJECT:description'
        ),
        'projectView.' => array(
            'header'      => 'sVIEW:header',
            'description' => 'sVIEW:description'
        )
    );
    
    /**
     * 
     */
    protected function _getContent()
    {
        // Tries to get the user
        if( $this->_getUser() ) {
            
            // Template markers
            $markers                 = array();
            
            // Creates the menu
            $markers[ '###MENU###' ] = $this->_createMenu();
            
            // Checks the view type
            if( isset( $this->piVars[ 'menu' ] ) && $this->piVars[ 'menu' ] == 1 ) {
                
                // Proof documents
                #$markers[ '###CONTENT###' ] = $this->_proofDocuments();
                
            } elseif( isset( $this->piVars[ 'menu' ] ) && $this->piVars[ 'menu' ] == 2 ) {
                
                // Project submission
                $markers[ '###CONTENT###' ] = $this->_uploadProject();
                
            } elseif( isset( $this->piVars[ 'menu' ] ) && $this->piVars[ 'menu' ] == 3 ) {
                
                // Review
                $markers[ '###CONTENT###' ] = $this->_projectView();
                
            } else {
                
                // Default content
                $markers[ '###CONTENT###' ] = $this->_uploadProject();
            }
            
            // Returns the plugin content
            return $this->_api->fe_renderTemplate( $markers, '###PROFILE_MAIN###' );
            
        }
        
        // No content
        return '';
    }
    
    /**
     * 
     */
    protected function _getUser()
    {
        // Checks for a connected user
        if( !self::$_tsfe->loginUser ) {
            
            // No access
            return false;
        }
        
        // Checks the storage page
        if( self::$_tsfe->fe_user->user[ 'pid' ] != $this->_conf[ 'pid' ] ) {
            
            // No access
            return false;
        }
        
        // Tries to select a profile
        $res = self::$_db->exec_SELECTquery(
            '*',
            self::$_dbTables[ 'profiles' ],
            'id_fe_users='
          . self::$_tsfe->fe_user->user[ 'uid' ]
          . ' AND pid='
          . $this->_conf[ 'pid' ]
          . $this->cObj->enableFields( self::$_dbTables[ 'profiles' ] )
        );
        
        // Checks if a profile exists
        if( $res && $profile = self::$_db->sql_fetch_assoc( $res ) ) {
            
            // Stores the user and it's profile
            $this->_user    = self::$_tsfe->fe_user->user;
            $this->_profile = $profile;
            
            // Access granted
            return true;
        }
        
        // No access
        return false;
    }
    
    /**
     * 
     */
    protected function _createMenu()
    {
        // Start index for the menu items (depends on the existence of the proof documents)
        #$itemsStart = ( $this->_profile[ 'age_proof' ] && $this->_profile[ 'school_proof' ] ) ? 2 : 1;
        $itemsStart = 2;
        
        // Number of menu items (depends on the existence of the project)
        $itemsNum   = ( $this->_profile[ 'project' ] ) ? 3 : 2;
        
        // Storage for the menu items
        $items      = array();
        
        // Process the menu items
        for( $i = $itemsStart; $i < $itemsNum + 1 ; $i++ ) {
            
            // Item CSS class
            $itemClass = ( isset( $this->piVars[ 'menu' ] ) && $this->piVars[ 'menu' ] == $i ) ? 'menu-item-act' : 'menu-item';
            
            // Creates the link for the current item
            $itemLink  = $this->cObj->typoLink(
                $this->pi_getLL( 'menu-' . $i ),
                array(
                    'parameter'        => self::$_tsfe->id,
                    'useCacheHash'     => 1,
                    'additionalParams' => $this->_api->fe_typoLinkParams(
                        array(
                            'menu' => $i
                        ),
                        false
                    )
                )
            );
            
            // Adds the item
            $items[]   = $this->_api->fe_makeStyledContent( 'li', $itemClass, $itemLink );
        }
        
        // Returns the menu
        return $this->_api->fe_makeStyledContent( 'ul', 'menu', implode( self::$_NL, $items ) );
    }
    
    ############################################################################
    # Home
    ############################################################################
    
    /**
     * 
     */
    protected function _home()
    {
        // Template markers
        $markers                        = array();
        
        // Sets the header
        $markers[ '###HEADER###' ]      = $this->_api->fe_makeStyledContent(
            'h2',
            'header',
            $this->_conf[ 'home.' ][ 'header' ]
        );
        
        // Sets the description
        $markers[ '###DESCRIPTION###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'description',
            $this->pi_RTEcssText( $this->_conf[ 'home.' ][ 'description' ] )
        );
        
        // Returns the section
        return $this->_api->fe_renderTemplate( $markers, '###HOME_MAIN###' );
    }
    
    ############################################################################
    # Proof documents
    ############################################################################
    
    protected function _proofDocuments()
    {
        // Checks if the documents will be uploaded later
        if( isset( $this->piVars[ 'submit' ] ) && isset( $this->piVars[ 'later' ] ) && $this->piVars[ 'later' ] ) {
        
            // Next step URL
            $nextLink = self::$_typo3Url . $this->cObj->typoLink_URL(
                array(
                    'parameter'    => self::$_tsfe->id,
                    'useCacheHash' => 1
                )
            );
            
            // Go to the next step
            header( 'Location: ' . $nextLink );
            exit();
        }
        
        // Validation callbacks
        $validCallbacks = array(
            'age_proof'    => '_checkUploadType',
            'school_proof' => '_checkUploadType'
        );
        
        // Checks the submission, if any
        if( $this->_formValid( self::$_proofFields, $validCallbacks ) ) {
            
            // Process the files
            $this->_processProofFiles();
        
            // Next step URL
            $nextLink = self::$_typo3Url . $this->cObj->typoLink_URL(
                array(
                    'parameter'    => self::$_tsfe->id,
                    'useCacheHash' => 1
                )
            );
            
            // Go to the next step
            header( 'Location: ' . $nextLink );
            exit();
        }
        
        // Template markers
        $markers                         = array();
        
        // Sets the header
        $markers[ '###HEADER###' ]       = $this->_api->fe_makeStyledContent(
            'h2',
            'header',
            $this->_conf[ 'proof.' ][ 'header' ]
        );
        
        // Sets the description
        $markers[ '###DESCRIPTION###' ]  = $this->_api->fe_makeStyledContent(
            'div',
            'description',
            $this->pi_RTEcssText( $this->_conf[ 'proof.' ][ 'description' ] )
        );
        
        // Creates the fields
        $markers[ '###FIELDS###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'fields',
            $this->_formFields( self::$_proofFields, '###PROOF_FIELDS###' )
        );
        
        // Sets the submit button
        $markers[ '###SUBMIT###' ]       = $this->_submitButton();
        
        // Full form
        $form                            = $this->_formTag( $this->_api->fe_renderTemplate( $markers, '###PROOF_MAIN###' ), array( 'menu' ) );
        
        // Returns the form
        return $form;
    }
    
    /**
     * 
     */
    protected function _processProofFiles()
    {
        // Absolute path to the upload directory
        $uploadDir  = t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->extKey ) );
        
        // Prefix for the files
        $filePrefix = md5( uniqid( rand(), true) );
        
        // Move the files to the upload directory
        move_uploaded_file( $_FILES[ $this->prefixId ][ 'tmp_name' ][ 'age_proof' ],    $uploadDir . DIRECTORY_SEPARATOR . $filePrefix . '-age.jpg' );
        move_uploaded_file( $_FILES[ $this->prefixId ][ 'tmp_name' ][ 'school_proof' ], $uploadDir . DIRECTORY_SEPARATOR . $filePrefix . '-school.jpg' );
        
        // Removes the old files
        unlink ( $this->_profile[ 'age_proof' ] );
        unlink ( $this->_profile[ 'school_proof' ] );
        
        // Updates the profile
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'profiles' ],
            'uid=' . $this->_profile[ 'uid' ],
            array(
                'age_proof'    => $filePrefix . '-age.jpg',
                'school_proof' => $filePrefix . '-school.jpg'
            )
        );
    }
    
    ############################################################################
    # Upload project
    ############################################################################
    
    protected function _uploadProject()
    {
        // Validation callbacks
        $validCallbacks = array(
            'project' => '_checkUploadType',
        );
        
        // Checks the submission, if any
        if( $this->_formValid( self::$_projectFields, $validCallbacks ) ) {
            
            // Process the files
            $this->_processProjectFile();
        
            // Next step URL
            $nextLink = self::$_typo3Url . $this->cObj->typoLink_URL(
                array(
                    'parameter'    => self::$_tsfe->id,
                    'useCacheHash' => 1,
                    'additionalParams' => $this->_api->fe_typoLinkParams(
                        array(
                            'menu' => 3
                        ),
                        false
                    )
                )
            );
            
            // Go to the next step
            header( 'Location: ' . $nextLink );
            exit();
        }
        
        // Template markers
        $markers                         = array();
        
        // Sets the header
        $markers[ '###HEADER###' ]       = $this->_api->fe_makeStyledContent(
            'h2',
            'header',
            $this->_conf[ 'projectUpload.' ][ 'header' ]
        );
        
        // Sets the description
        $markers[ '###DESCRIPTION###' ]  = $this->_api->fe_makeStyledContent(
            'div',
            'description',
            $this->pi_RTEcssText( $this->_conf[ 'projectUpload.' ][ 'description' ] )
        );
        
        // Creates the fields
        $markers[ '###FIELDS###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'fields',
            $this->_formFields( self::$_projectFields, '###PROJECT_FIELDS###' )
        );
        
        // Sets the submit button
        $markers[ '###SUBMIT###' ]       = $this->_submitButton();
        
        // Full form
        $form                            = $this->_formTag( $this->_api->fe_renderTemplate( $markers, '###PROJECT_MAIN###' ), array( 'menu' ) );
        
        // Returns the form
        return $form;
    }
    
    /**
     * 
     */
    protected function _processProjectFile()
    {
        // Absolute path to the upload directory
        $uploadDir  = t3lib_div::getFileAbsFileName( 'uploads/tx_' . str_replace( '_', '', $this->extKey ) );
        
        // Prefix for the files
        $filePrefix = md5( uniqid( rand(), true) );
        
        // Move the files to the upload directory
        move_uploaded_file( $_FILES[ $this->prefixId ][ 'tmp_name' ][ 'project' ], $uploadDir . DIRECTORY_SEPARATOR . $filePrefix . '-project.jpg' );
        
        // Removes the old file
        unlink( $this->_profile[ 'project' ] );
        
        // Updates the profile
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'profiles' ],
            'uid=' . $this->_profile[ 'uid' ],
            array(
                'project' => $filePrefix . '-project.jpg',
            )
        );
    }
    
    ############################################################################
    # Project view
    ############################################################################
    
    protected function _projectView()
    {
        // Includes lightbox
        $this->_api->fe_includeLightBoxJs();
        
        // Template markers
        $markers                        = array();
        
        // Sets the header
        $markers[ '###HEADER###' ]      = $this->_api->fe_makeStyledContent(
            'h2',
            'header',
            $this->_conf[ 'projectView.' ][ 'header' ]
        );
        
        // Sets the description
        $markers[ '###DESCRIPTION###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'description',
            $this->pi_RTEcssText( $this->_conf[ 'projectView.' ][ 'description' ] )
        );
        
        // Creates the picture
        $picture                        = $this->_api->fe_createImageObjects(
            $this->_profile[ 'project' ],
            $this->_conf[ 'projectView.' ][ 'picture.' ],
            self::$_uploadDirectory
        );
        
        // Creates the lightbox link
        $pictureLink                    = '<a href="'
                                        . self::$_uploadDirectory . $this->_profile[ 'project' ]
                                        . '" title="'
                                        . $this->pi_getLL( 'enlarge' )
                                        . '" rel="lightbox">'
                                        . $picture
                                        . '</a>';
        
        // Sets the picture
        $markers[ '###PROJECT###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'project',
            $pictureLink
        );
        
        // Returns the section
        return $this->_api->fe_renderTemplate( $markers, '###VIEW_MAIN###' );
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi2/class.tx_adlercontest_pi2.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi2/class.tx_adlercontest_pi2.php']);
}
