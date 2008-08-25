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
 * Plugin 'Registration' for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the TYPO3 FE plugin class
require_once( PATH_tslib . 'class.tslib_pibase.php' );

// Includes the frontend plugin base class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_pibase.php' );

class tx_adlercontest_pi2 extends tx_adlercontest_piBase
{
    /**
     * Form fields for the proof documents
     */
    protected static $_proofFields = array(
        'age_proof'    => array( 'type' => 'file', 'ext' => 'pdf', 'size' => 2048 ),
        'school_proof' => array( 'type' => 'file', 'ext' => 'pdf', 'size' => 2048 ),
        'later'        => array( 'type' => 'checkbox', 'optionnal' => true )
    );
    
    /**
     * The TypoScript configuration array
     */
    protected $_conf               = array();
    
    /**
     * The user row
     */
    protected $_user               = array();
    
    /**
     * The profile row
     */
    protected $_profile            = array();
    
    /**
     * The flexform data
     */
    protected $_piFlexForm         = '';
    
    /**
     * The class name
     */
    public $prefixId               = 'tx_adlercontest_pi2';
    
    /**
     * The path to this script relative to the extension directory
     */
    public $scriptRelPath          = 'pi2/class.tx_adlercontest_pi2.php';
    
    /**
     * The extension key
     */
    public $extKey                 = 'adler_contest';
    
    /**
     * Wether to check plugin hash
     */
    public $pi_checkCHash          = true;
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_tscobj_pi2', and
     * launches the needed functions to correctly display the plugin.
     * 
     * @param   string  $content    The plugin content
     * @param   array   $conf       The TS setup
     * @return  string  The content of the plugin
     * @see     _userProfile
     * @see     _uploadDocuments
     * @see     _registrationForm
     */
    public function main( $content, array $conf )
    {
        // Stores the TypoScript configuration
        $this->_conf = $conf;
        
        // Sets the default plugin variables
        $this->pi_setPiVarDefaults();
        
        // Loads the LOCAL_LANG values
        $this->pi_loadLL();
        
        // Initialize the flexform configuration of the plugin
        $this->pi_initPIflexForm();
        
        // Stores the flexform informations
        $this->_piFlexForm = $this->cObj->data[ 'pi_flexform' ];
        
        // Sets the final configuration (TS or FF)
        $this->_setConfig();
        
        // Initialize the template object
        $this->_api->fe_initTemplate( $this->_conf[ 'templateFile' ] );
        
        // Tries to get the user
        if( $this->_getUser() ) {
            
            // Template markers
            $markers                    = array();
            
            // Creates the menu
            $markers[ '###MENU###' ]    = $this->_createMenu();
            
            // Checks the view type
            if( isset( $this->piVars[ 'menu' ] ) && $this->piVars[ 'menu' ] == 1 ) {
                
                // Proof documents
                $markers[ '###CONTENT###' ] = $this->_proofDocuments();
                
            } elseif( isset( $this->piVars[ 'menu' ] ) && $this->piVars[ 'menu' ] == 2 ) {
                
                // Project submission
                $markers[ '###CONTENT###' ] = '2';
                
            } elseif( isset( $this->piVars[ 'menu' ] ) && $this->piVars[ 'menu' ] == 3 ) {
                
                // Review
                $markers[ '###CONTENT###' ] = '3';
                
            } else {
                
                // Default content
                $markers[ '###CONTENT###' ] = '';
            }
            
            // Returns the plugin content
            return $this->pi_wrapInBaseClass( $this->_api->fe_renderTemplate( $markers, '###PROFILE_MAIN###' ) );
            
        }
        
        // No content
        return '';
    }
    
    /**
     * Sets the configuration array
     * 
     * This function is used to set the final configuration array of the
     * plugin, by providing a mapping array between the TS & the flexform
     * configuration.
     * 
     * @return  NULL
     */
    protected function _setConfig()
    {
        // Mapping array for PI flexform
        $flex2conf = array(
            'pid'    => 'sDEF:pages',
            'proof.' => array(
                'header'      => 'sPROOF:header',
                'description' => 'sPROOF:description'
            )
        );
        
        // Ovverride TS setup with flexform
        $this->_conf = $this->_api->fe_mergeTSconfFlex(
            $flex2conf,
            $this->_conf,
            $this->_piFlexForm
        );
        
        // DEBUG ONLY - Output configuration array
        #$this->_api->debug( $this->_conf, $this->prefixId . ': configuration array' );
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
        $itemsStart = ( $this->_profile[ 'age_proof' ] && $this->_profile[ 'school_proof' ] ) ? 2 : 1;
        
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
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi2/class.tx_adlercontest_pi2.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi2/class.tx_adlercontest_pi2.php']);
}
