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
 * Plugin 'Vote' for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the frontend plugin base class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_pibase.php' );

class tx_adlercontest_pi3 extends tx_adlercontest_piBase
{
    /**
     * The user row
     */
    protected $_user                 = array();
    
    /**
     * The project row
     */
    protected $_project              = array();
    
    /**
     * Configuration mapping array (between TS and Flex)
     */
    protected $_configMap            = array(
        'pid'        => 'sDEF:pages',
        'group'      => 'sDEF:group',
        'criterias.' => array(
            '1' => 'sCRITERIAS:criteria_1',
            '2' => 'sCRITERIAS:criteria_2',
            '3' => 'sCRITERIAS:criteria_3',
            '4' => 'sCRITERIAS:criteria_4',
            #'5' => 'sCRITERIAS:criteria_5'
        ),
        'texts.'     => array(
            'header'      => 'sTEXTS:header',
            'description' => 'sTEXTS:description'
        )
    );
    
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
     * 
     */
    protected function _getContent()
    {
        // Tries to get the user
        if( $this->_getUser() ) {
            
            // Template markers
            $markers = array();
            
            // Checks the submission
            if( isset( $this->piVars[ 'submit' ] ) && $this->_checkSubmit() ) {
                
                // Inserts the vote
                $this->_insertVote();
                
                // Removes the plugin variable, so another project can be selected
                unset( $this->piVars[ 'project' ] );
            }
            
            // Try to select a project
            if( $this->_selectProject() ) {
                
                // Project vote view
                $markers[ '###PROJECT###' ] = $this->_voteView();
                
            } else {
                
                // No project available
                $markers[ '###PROJECT###' ] = $this->_api->fe_makeStyledContent(
                    'div',
                    'no-project',
                    $this->pi_getLL( 'no-project' )
                );
            }
            
            // Sets the header
            $markers[ '###HEADER###' ]      = $this->_api->fe_makeStyledContent(
                'h2',
                'header',
                $this->_conf[ 'texts.' ][ 'header' ]
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
            return $this->_api->fe_renderTemplate( $markers, '###VOTE_MAIN###' );
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
        ;
        // Checks the storage page
        if( self::$_tsfe->fe_user->user[ 'pid' ]          != $this->_conf[ 'pid' ]
            || self::$_tsfe->fe_user->user[ 'usergroup' ] != $this->_conf[ 'group' ]
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
        // Checks for an incoming project
        if( isset( $this->piVars[ 'project' ] ) ) {
            
            // Gets the specified project
            $this->_project = $this->pi_getRecord(
                self::$_dbTables[ 'profiles' ],
                $this->piVars[ 'project' ]
            );
            
            return true;
        }
        
        // Storage for the projects that were already voted by this user
        $voted    = array();
        
        // Selects the user votes
        $resVotes = self::$_db->exec_SELECTquery(
            'id_tx_adlercontest_users',
            self::$_dbTables[ 'votes' ],
            'pid='
          . $this->_conf[ 'pid' ]
          . ' AND id_fe_users='
          . $this->_user[ 'uid' ]
          . $this->cObj->enableFields( self::$_dbTables[ 'votes' ] )
        );
        
        // Process all the votes
        while( $vote = self::$_db->sql_fetch_assoc( $resVotes ) ) {
            
            // Adds the project ID to the list of disallowed projects
            $voted[] = $vote[ 'id_tx_adlercontest_users' ];
        }
        
        // Where clause to select the projects
        $projectsWhere = 'pid='
                       . $this->_conf[ 'pid' ]
                       . ' AND validated AND project';
        
        // Checks if the user as already voted on some projects
        if( count( $voted ) ) {
            
            // Disallow to projects already noted by the user
            $projectsWhere .= ' AND uid NOT IN (' . implode( ',', $voted ) . ')';
        }
        
        // Counts the available users
        $resProjects = self::$_db->exec_SELECTquery(
            '*',
            self::$_dbTables[ 'profiles' ],
            $projectsWhere . $this->cObj->enableFields( self::$_dbTables[ 'profiles' ] ),
            'votes,rand()'
        );
        
#        // Storage for the profile query parts
#        $queryParts   = array();
#        
#        // Builds the query for the profiles
#        $queryParts[] = 'SELECT profile.*';
#        $queryParts[] = 'FROM ' . self::$_dbTables[ 'profiles' ] . ' as profile, ' . self::$_dbTables[ 'votes' ] . ' as vote';
#        $queryParts[] = 'WHERE vote.id_' . self::$_dbTables[ 'profiles' ] . ' = profile.uid';
#        $queryParts[] = 'AND profile.project';
#        $queryParts[] = 'AND profile.validated';
#        $queryParts[] = str_replace( self::$_dbTables[ 'profiles' ], 'profile', $this->cObj->enableFields( self::$_dbTables[ 'profiles' ] ) );
#        $queryParts[] = 'AND profile.uid NOT IN (';
#        $queryParts[] = '   SELECT vote.id_' . self::$_dbTables[ 'profiles' ];
#        $queryParts[] = '   FROM ' . self::$_dbTables[ 'votes' ] . ' as vote';
#        $queryParts[] = '   WHERE vote.id_' . self::$_dbTables[ 'users' ] . ' = ' . $this->_user[ 'uid' ];
#        $queryParts[] = ');';
#        
#        // Executes the query
#        $resProjects = self::$_db->sql_query( implode( ' ', $queryParts ) );
        
        // Checks the results
        if( $resProjects && self::$_db->sql_num_rows( $resProjects ) ) {
            
            // Gets and stores a project
            $this->_project = self::$_db->sql_fetch_assoc( $resProjects );
            
            // Projects are available
            return true;
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
        
        // Storage for hidden fields
        $hidden                      = array();
        
        // Adds the project ID
        $hidden[]                    = '<input type="hidden" value="'
                                     . $this->_project[ 'uid' ]
                                     . '" name="'
                                     . $this->prefixId
                                     . '[project]">';
        
        // Creates the criteria picture
        $this->_criteriaPicture      = $this->_api->fe_createImageObjects(
            $this->_conf[ 'projectView.' ][ 'criteriaOff' ],
            $this->_conf[ 'projectView.' ][ 'criteriaPicture.' ]
        );
        
        // Process each criterias
        for( $i = 1; $i < 5; $i++ ) {
            
            // Adds the notes for the current criteria
            $markers[ '###CRITERIA_' . $i . '###' ] = $this->_api->fe_makeStyledContent(
                'div',
                'criteria',
                $this->_buildCriteria( $i )
            );
            
            // Adds the hidden field for the current criteria
            $hidden[]                               = '<input type="hidden" value="0" name="'
                                                    . $this->prefixId
                                                    . '[criteria_'
                                                    . $i
                                                    . ']" id="'
                                                    . $this->prefixId
                                                    . '-criteria-'
                                                    . $i
                                                    . '-hidden">';
        }
        
        // Adds the hidden fields
        $markers[ '###HIDDEN###' ]  = implode( self::$_NL, $hidden );
        
        // Adds the error message, if applicable
        $markers[ '###ERROR###' ]   = ( isset( $this->piVars[ 'project' ] ) ) ? $this->_api->fe_makeStyledContent( 'div', 'form-error', $this->pi_getLL( 'error-no-value' ) ) : '';
        
        // Creates the project picture
        $picture                    = $this->_api->fe_createImageObjects(
            $this->_project[ 'project' ],
            $this->_conf[ 'projectView.' ][ 'projectPicture.' ],
            self::$_uploadDirectory
        );
        
        // Creates the lightbox link
        $pictureLink                = '<a href="'
                                    . self::$_uploadDirectory . $this->_project[ 'project' ]
                                    . '" title="'
                                    . $this->pi_getLL( 'enlarge' )
                                    . '" rel="lightbox">'
                                    . $picture
                                    . '</a>';
        
        // Adds the picture
        $markers[ '###PICTURE###' ] = $this->_api->fe_makeStyledContent(
            'div',
            'picture',
            $pictureLink
        );
        
        // Adds the submit button
        $markers[ '###SUBMIT###' ]  = $this->_submitButton();
        
        // Returns the vote view
        return $this->_formTag( $this->_api->fe_renderTemplate( $markers, '###PROJECT_MAIN###' ) );
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
        
        // Process each value
        for( $i = 1; $i < 11; $i++ ) {
            
            // Adds the picture
            $values[] = '<a href="javascript:'
                      . $this->prefixId
                      . '.setCriteriaValue( ' . $i . ', ' . $index . ', \'' . $this->_criteriaOn . '\', \'' . $this->_criteriaOff . '\' );" id="'
                      . $this->prefixId . '-criteria-' . $index . '-' . $i
                      . '" title="' . $i . '">'
                      . $this->_criteriaPicture
                      . '</a>';
            
        }
        
        // Returns the pictures
        return implode( self::$_NL, $values );
    }
    
    protected function _checkSubmit()
    {
        // Checks the values from the form
        if( isset( $this->piVars[ 'project' ] )
            && isset( $this->piVars[ 'criteria_1' ] )
            && isset( $this->piVars[ 'criteria_2' ] )
            && isset( $this->piVars[ 'criteria_3' ] )
            && isset( $this->piVars[ 'criteria_4' ] )
            #&& isset( $this->piVars[ 'criteria_5' ] )
            && $this->piVars[ 'project' ]
            && $this->piVars[ 'criteria_1' ]
            && $this->piVars[ 'criteria_2' ]
            && $this->piVars[ 'criteria_3' ]
            && $this->piVars[ 'criteria_4' ]
            #&& $this->piVars[ 'criteria_5' ]
        ) {
            
            // Values are OK
            return true;
        }
        
        // Missing value
        return false;
    }
    
    /**
     * 
     */
    protected function _insertVote()
    {
        // Gets the related project
        $project = $this->pi_getRecord(
            self::$_dbTables[ 'profiles' ],
            $this->piVars[ 'project' ]
        );
        
        // Increments the number of votes
        self::$_db->exec_UPDATEquery(
            self::$_dbTables[ 'profiles' ],
            $project[ 'uid' ],
            array(
                'votes' => $project[ 'votes' ] + 1
            )
        );
        
        // Storage for the database
        $vote                               = array();
        
        // Current time
        $time                               = time();
        
        // Average note
        $note                               = ( $this->piVars[ 'criteria_1' ]
                                              + $this->piVars[ 'criteria_2' ]
                                              + $this->piVars[ 'criteria_3' ]
                                              + $this->piVars[ 'criteria_4' ]
                                              + $this->piVars[ 'criteria_5' ]
                                              ) / 5;
        
        // Sets the vote fields
        $vote[ 'pid' ]                      = $this->_conf[ 'pid' ];
        $vote[ 'crdate' ]                   = $time;
        $vote[ 'tstamp' ]                   = $time;
        $vote[ 'criteria_1' ]               = ( int )$this->piVars[ 'criteria_1' ];
        $vote[ 'criteria_2' ]               = ( int )$this->piVars[ 'criteria_2' ];
        $vote[ 'criteria_3' ]               = ( int )$this->piVars[ 'criteria_3' ];
        $vote[ 'criteria_4' ]               = ( int )$this->piVars[ 'criteria_4' ];
        #$vote[ 'criteria_5' ]               = ( int )$this->piVars[ 'criteria_5' ];
        $vote[ 'note' ]                     = $note;
        $vote[ 'id_fe_users' ]              = $this->_user[ 'uid' ];
        $vote[ 'id_tx_adlercontest_users' ] = $project[ 'uid' ];
        
        // Inserts the vote
        self::$_db->exec_INSERTquery( self::$_dbTables[ 'votes' ], $vote );
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3.php']);
}
