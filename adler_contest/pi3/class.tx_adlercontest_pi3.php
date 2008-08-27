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
     * The project row
     */
    protected $_project              = array();
    
    /**
     * The number of available users
     */
    protected $_usersNum             = 0;
    
    /**
     * The number of available projects
     */
    protected $_projectsNum          = 0;
    
    /**
     * The flexform data
     */
    protected $_piFlexForm           = '';
    
    /**
     * The picture for the criterias values
     */
    protected $_criteriaPicture      = '';
    
    /**
     * The source for the 'on' criteria value picture
     */
    protected $_criteriaOn           = '';
    
    /**
     * The source for the 'off' criteria value picture
     */
    protected $_criteriaff           = '';
    
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
            $markers = array();
            
            // Try to select a project
            if( $this->_selectProject() ) {
                
                // Project vote view
                $markers[ '###PROJECT###' ] = $this->_voteView();
                
            } else {
                
                // No project available
                $markers[ '###PROJECT###' ] = $this->pi_getLL( 'no-project' );
            }
            
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
    
    /**
     * 
     */
    protected function _selectProject()
    {
        // Selects the available users
        $resUsers    = self::$_db->exec_SELECTquery(
            'uid',
            self::$_dbTables[ 'users' ],
            'pid='
          . $this->_conf[ 'pid' ]
          . ' AND usergroup IN ('
          . $this->_conf[ 'group' ]
          . ')'
        );
        
        // Counts the available users
        $resProjects = self::$_db->exec_SELECTquery(
            '*',
            self::$_dbTables[ 'profiles' ],
            'pid='
          . $this->_conf[ 'pid' ]
          . ' AND validated AND project AND NOT votes',
          'rand()'
        );
        
        // Checks the results
        if( $resUsers && $resProjects ) {
            
            // Counts the results
            $this->_usersNum    = ( int )self::$_db->sql_num_rows( $resUsers );
            $this->_projectsNum = ( int )self::$_db->sql_num_rows( $resProjects );
            
            // Number of available projects for this user
            $availableProjects  = ( int )( $this->_projectsNum / $this->_usersNum );
            
            // Checks for available projects
            if( $availableProjects > 0 ) {
                
                // Gets and stores a project
                $this->_project = self::$_db->sql_fetch_assoc( $resProjects );
                
                // Projects are available
                return true;
            }
            
            // No available project
            return false;
        }
        
        // No available project
        return false;
    }
    
    /**
     * 
     */
    protected function _voteView()
    {
        // Includes the prototype script
        $this->_api->fe_includePrototypeJs();
        
        // Includes the lightbox script
        $this->_api->fe_includeLightBoxJs();
        
        // Includes the plugin script
        $this->_includePluginJs();
        
        // Sets the picture sources for the criteria values
        $this->_criteriaOn           = $this->_processPath( $this->_conf[ 'projectView.' ][ 'criteriaOn' ] );
        $this->_criteriaOff          = $this->_processPath( $this->_conf[ 'projectView.' ][ 'criteriaOff' ] );
        
        // Template markers
        $markers                     = array();
        
        // Creates the criteria picture
        $this->_criteriaPicture      = $this->_api->fe_createImageObjects(
            $this->_conf[ 'projectView.' ][ 'criteriaOff' ],
            $this->_conf[ 'projectView.' ][ 'criteriaPicture.' ]
        );
        
        // Process each criterias
        for( $i = 1; $i < 6; $i++ ) {
            
            $markers[ '###CRITERIA_' . $i . '###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'criteria',
                $this->_buildCriteria( $i )
            );
        }
        
        // Creates the project picture
        $picture                    = $this->_api->fe_createImageObjects(
            $this->_project[ 'project' ],
            $this->_conf[ 'projectView.' ][ 'projectPicture.' ],
            $this->_uploadDirectory . '/'
        );
        
        // Creates the lightbox link
        $pictureLink                = '<a href="'
                                    . $this->_uploadDirectory . '/' . $this->_project[ 'project' ]
                                    . '" title="'
                                    . $this->pi_getLL( 'enlarge' )
                                    . '" rel="lightbox">'
                                    . $picture
                                    . '</a>';
        
        // Sets the picture
        $markers[ '###PICTURE###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'picture',
            $pictureLink
        );
        
        // Returns the vote view
        return $this->_api->fe_makeStyledContent(
            'div',
            'project',
            $this->_api->fe_renderTemplate( $markers, '###PROJECT_MAIN###' )
        );
    }
    
    /**
     * 
     */
    protected function _buildCriteria( $index )
    {
        // Template markers
        $markers = array();
        
        // Criteria label
        $markers[ '###LABEL###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'criteria-label',
            $this->_conf[ 'criterias.' ][ $index ]
        );
        
        // Adds the criteria values
        $markers[ '###VALUES###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'criteria-values',
            $this->_buildCriteriaValues( $index ),
            true,
            false,
            false,
            array(
                'id' => $this->prefixId . '-criteria-' . $index
            )
        );
        
        // Returns the criteria
        return $this->_api->fe_renderTemplate( $markers, '###CRITERIA_MAIN###' );
    }
    
    /**
     * 
     */
    protected function _buildCriteriaValues( $index )
    {
        // Storage
        $values = array();
        
        // Process each note
        for( $i = 1; $i < 11; $i++ ) {
            
            $values[] = '<a href="javascript:'
                      . $this->prefixId
                      . '.setCriteriaValue( ' . $i . ', ' . $index . ', \'' . $this->_criteriaOn . '\', \'' . $this->_criteriaOff . '\' );" id="'
                      . $this->prefixId . '-criteria-' . $index . '-' . $i
                      . '" title="' . $i . '">'
                      . $this->_criteriaPicture
                      . '</a>';
        }
        
        return implode( self::$_NL, $values );
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3.php']);
}
