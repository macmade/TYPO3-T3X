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
 * Plugin 'Vote' for the 'adler_contest' extension.
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

class tx_adlercontest_pi3 extends tx_adlercontest_piBase
{
    /**
     * The TypoScript configuration array
     */
    protected $_conf                 = array();
    
    /**
     * The user row
     */
    protected $_user                 = array();
    
    /**
     * The flexform data
     */
    protected $_piFlexForm           = '';
    
    /**
     * The class name
     */
    public $prefixId                 = 'tx_adlercontest_pi3';
    
    /**
     * The path to this script relative to the extension directory
     */
    public $scriptRelPath            = 'pi3/class.tx_adlercontest_pi3.php';
    
    /**
     * The extension key
     */
    public $extKey                   = 'adler_contest';
    
    /**
     * Wether to check plugin hash
     */
    public $pi_checkCHash            = true;
    
    /**
     * Returns the content object of the plugin.
     * 
     * This function initialises the plugin 'tx_tscobj_pi3', and
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
            $markers                        = array();
            
            // Sets the header
            $markers[ '###HEADER###' ]      = $this->_api->fe_makeStyledContent(
                'h2',
                'header',
                $this->pi_RTEcssText( $this->_conf[ 'texts.' ][ 'header' ] )
            );
            
            // Final description, with tags replaced
            $description = preg_replace(
                array(
                    '/\${name}/'
                ),
                array(
                    $this->_user[ 'name' ],
                ),
                $this->_conf[ 'texts.' ][ 'description' ]
            );
            
            // Sets the description
            $markers[ '###DESCRIPTION###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'description',
                $this->pi_RTEcssText( $description )
            );
            
            // Returns the vote view
            return $this->pi_wrapInBaseClass( $this->_api->fe_renderTemplate( $markers, '###VOTE_MAIN###' ) );
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
            'pid'        => 'sDEF:pages',
            'group'      => 'sDEF:group',
            'criterias.' => array(
                '1' => 'sCRITERIAS:criteria_1',
                '2' => 'sCRITERIAS:criteria_2',
                '3' => 'sCRITERIAS:criteria_3',
                '4' => 'sCRITERIAS:criteria_4',
                '5' => 'sCRITERIAS:criteria_5'
            ),
            'texts.'     => array(
                'header'      => 'sTEXTS:header',
                'description' => 'sTEXTS:description'
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
        if( self::$_tsfe->fe_user->user[ 'pid' ]          != $this->_conf[ 'pid' ]
            && self::$_tsfe->fe_user->user[ 'usergroup' ] != $this->_conf[ 'group' ]
        ) {
            
            // No access
            return false;
        }
        
        // Stores the frontend user
        $this->_user = self::$_tsfe->fe_user->user;
        
        // Access granted
        return true;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3.php']);
}
