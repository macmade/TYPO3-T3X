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
 * Module 'Adler / Contest' for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     1.0
 */

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

// Includes the backend module base class
require_once( t3lib_extMgm::extPath( 'adler_contest' ) . 'classes/class.tx_adlercontest_scbase.php' );

class tx_adlercontest_module1 extends tx_adlercontest_scBase
{
    /**
     * The frontend users stored on the current page
     */
    protected $_users     = array();
    
    /**
     * The user profiles stored on the current page
     */
    protected $_profiles  = array();
    
    /**
     * The available menu items
     */
    protected $_menuItems = array(
        1 => 'menu.func.1',
        2 => 'menu.func.2'
    );
    
    /**
     * 
     */
    protected function _getContent()
    {
        // Checks the tables to see if something can be displayed
        if( !$this->_getTables() ) {
            
            // No content
            return self::$_lang->getLL( 'error.no-content' );
        }
        
        // Checks the selection for the menu
        switch( $this->MOD_SETTINGS[ 'function' ] ) {
            
            // Shows the registered users
            default:
                
                return $this->_showUsers();
                break;
        }
    }
    
    /**
     * 
     */
    protected function _getTables()
    {
        // Gets the frontend users
        $users    = t3lib_BEfunc::getRecordsByField(
            self::$_dbTables[ 'users' ],
            'pid',
            $this->id
        );
        
        // Order by clause for the profiles
        $orderBy  = ( $this->MOD_SETTINGS[ 'function' ] == 1 ) ? 'crdate DESC' : 'lastname,firstname';
        
        // Gets the user profiles
        $profiles = t3lib_BEfunc::getRecordsByField(
            self::$_dbTables[ 'profiles' ],
            'pid',
            $this->id,
            '',
            '',
            $orderBy
        );
        
        // Checks for users and profiles
        if( is_array( $users ) && is_array( $profiles ) ) {
            
            // Process each user
            foreach( $users as $user ) {
                
                // Stores the current user
                $this->_users[ $user[ 'uid' ] ] = $user;
            }
            
            // Process each profile
            foreach( $profiles as $profile ) {
                
                // Stores the current profile
                $this->_profiles[ $profile[ 'uid' ] ] = $profile;
            }
            
            // Users and profiles were found
            return true;
        }
        
        // No user or profile
        return false;
    }
    
    /**
     * 
     */
    protected function _checkUserForDisplay( array $user )
    {
        // Only show confirmed users
        if( isset( $this->_modVars[ 'confirmed' ] )
            && $this->_modVars[ 'confirmed' ] == 1
            && $user[ 'confirm_token' ]
        ) {
            
            // User cannot be displayed
            return false;
        }
        
        // Only show unconfirmed users
        if( isset( $this->_modVars[ 'confirmed' ] )
            && $this->_modVars[ 'confirmed' ] == 2
            && !$user[ 'confirm_token' ]
        ) {
            
            // User cannot be displayed
            return false;
        }
        
        // Only show validated users
        if( isset( $this->_modVars[ 'validated' ] )
            && $this->_modVars[ 'validated' ] == 1
            && !$user[ 'validated' ]
        ) {
            
            // User cannot be displayed
            return false;
        }
        
        // Only show unvalidated users
        if( isset( $this->_modVars[ 'validated' ] )
            && $this->_modVars[ 'validated' ] == 2
            && $user[ 'validated' ]
        ) {
            
            // User cannot be displayed
            return false;
        }
        
        // Only show users with proof documents
        if( isset( $this->_modVars[ 'proof' ] )
            && $this->_modVars[ 'proof' ] == 1
            && ( !$user[ 'age_proof' ] || !$user[ 'school_proof' ] )
        ) {
            
            // User cannot be displayed
            return false;
        }
        
        // Only show users without proof documents
        if( isset( $this->_modVars[ 'proof' ] )
            && $this->_modVars[ 'proof' ] == 2
            && $user[ 'age_proof' ]
            && $user[ 'school_proof' ]
        ) {
            
            // User cannot be displayed
            return false;
        }
        
        // Only show users with a project
        if( isset( $this->_modVars[ 'project' ] )
            && $this->_modVars[ 'project' ] == 1
            && !$user[ 'project' ]
        ) {
            
            // User cannot be displayed
            return false;
        }
        
        // Only show users without a project
        if( isset( $this->_modVars[ 'project' ] )
            && $this->_modVars[ 'project' ] == 2
            && $user[ 'project' ]
        ) {
            
            // User cannot be displayed
            return false;
        }
        
        // User can be displayed
        return true;
    }
    
    /**
     * 
     */
    protected function _showUsers()
    {
        // Adds the view options
        $this->_viewOptions();
        
        // Starts the table
        $this->_content[] = $this->_tag(
            'table',
            '',
            array(
                'border'      => '0',
                'width'       => '100%',
                'align'       => 'center',
                'cellpadding' => '2',
                'cellspacing' => '1'
            ),
            array(
                'background-color' => $this->doc->bgColor2
            ),
            true
        );
        
        // Starts the table headers
        $this->_content[] = $this->_tag( 'tr', '', array(), array(), true );
        
        // Styles for the table headers
        $headerStyles     = array(
            'font-weight' => 'bold'
        );
        
        // Parameters for the table columns
        $trParams         = array(
            'align'  => 'left',
            'valign' => 'middle'
        );
        
        // Parameters for alternate rows
        $alternateRows    = array(
            array(
                'params' => array(
                    'onmouseover' => 'SOBE.changeBgColor( this, \'' . $this->doc->bgColor3 . '\' );',
                    'onmouseout'  => 'SOBE.changeBgColor( this, \'' . $this->doc->bgColor4 . '\' );',
                    'onclick'     => 'SOBE.changeBgColor( this, \'' . $this->doc->bgColor3 . '\' ); SOBE.setCheck( this );'
                ),
                'styles' => array(
                    'background-color' => $this->doc->bgColor4
                )
            ),
            array(
                'params' => array(
                    'onmouseover' => 'SOBE.changeBgColor( this, \'' . $this->doc->bgColor3 . '\' );',
                    'onmouseout'  => 'SOBE.changeBgColor( this, \'' . $this->doc->bgColor5 . '\' );',
                    'onclick'     => 'SOBE.changeBgColor( this, \'' . $this->doc->bgColor3 . '\' ); SOBE.setCheck( this );'
                ),
                'styles' => array(
                    'background-color' => $this->doc->bgColor5
                )
            )
        );
        
        // Adds the table headers
        $this->_content[] = $this->_tag( 'td', '',                                                                   $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', '',                                                                   $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', '',                                                                   $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', $this->_getFieldLabel( self::$_dbTables[ 'profiles' ], 'lastname' ),  $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', $this->_getFieldLabel( self::$_dbTables[ 'profiles' ], 'firstname' ), $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', self::$_lang->getLL( 'headers.confirmed' ),                           $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', self::$_lang->getLL( 'headers.validated' ),                           $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', self::$_lang->getLL( 'headers.proof' ),                               $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', self::$_lang->getLL( 'headers.project' ),                             $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', self::$_lang->getLL( 'headers.registration' ),                        $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', $this->_getFieldLabel( self::$_dbTables[ 'profiles' ], 'birthdate' ), $trParams, $headerStyles );
        $this->_content[] = $this->_tag( 'td', '',                                                                   $trParams, $headerStyles );
        
        // Counter for rows
        $rowCount         = 0;
        
        // Icon
        $viewIcon         = $this->_skinImg( 'info.gif' );
        $errorIcon        = $this->_skinImg( 'error.gif' );
        $okIcon           = $this->_skinImg( 'ok.gif' );
        
        // Process each profile
        foreach( $this->_profiles as $uid => $profile ) {
            
            // Checks if the user must be displayed
            if( !$this->_checkUserForDisplay( $profile ) ) {
                
                // Process the next user
                continue;
            }
            
            // Confirmation state
            $confirmed        = ( $profile[ 'confirm_token' ] )                           ? $errorIcon : $okIcon;
            
            // Validation state
            $validated        = ( $profile[ 'validated' ] )                               ? $okIcon    : $errorIcon;
            
            // Proof documents state
            $proof            = ( $profile[ 'age_proof' ] && $profile[ 'school_proof' ] ) ? $okIcon    : $errorIcon;
            
            // Project state
            $project          = ( $profile[ 'project' ] )                                 ? $okIcon    : $errorIcon;
            
            // Birthdate
            $birthDate        = ( $profile[ 'confirm_token' ] )                           ? ''         : date( self::$_dateFormat, $profile[ 'birthdate' ] );
            
            // Checkbox
            $check            = $this->_tag(
                'input',
                '',
                array(
                    'type' => 'checkbox',
                    'name' => __CLASS__ . '[users][' . $uid . ']'
                )
            );
            
            // Starts the row
            $this->_content[] = $this->_tag( 'tr', '', $alternateRows[ $rowCount ][ 'params' ], $alternateRows[ $rowCount ][ 'styles' ], true );
            
            // Adds the view link
            $this->_content[] = $this->_tag( 'td', $viewIcon, $trParams );
            
            // Adds the checkbox
            $this->_content[] = $this->_tag( 'td', $check, $trParams );
            
            // Adds the icons
            $this->_content[] = $this->_tag( 'td', $this->_api->be_getRecordCSMIcon( self::$_dbTables[ 'profiles' ], $profile, self::$_backPath ), $trParams );
            
            // Adds the full name
            $this->_content[] = $this->_tag( 'td', $profile[ 'lastname' ], $trParams );
            $this->_content[] = $this->_tag( 'td', $profile[ 'firstname' ], $trParams );
            
            // Adds the confirmation state
            $this->_content[] = $this->_tag( 'td', $confirmed, $trParams );
            
            // Adds the validation state
            $this->_content[] = $this->_tag( 'td', $validated, $trParams );
            
            // Adds the proof documents state
            $this->_content[] = $this->_tag( 'td', $proof, $trParams );
            
            // Adds the project state
            $this->_content[] = $this->_tag( 'td', $project, $trParams );
            
            // Adds the registration date
            $this->_content[] = $this->_tag( 'td', date( self::$_dateFormat . ' / ' . self::$_hourFormat, $profile[ 'crdate' ] ), $trParams );
            
            // Adds the user birth date
            $this->_content[] = $this->_tag( 'td', $birthDate, $trParams );
            
            // Adds the edit icons
            $this->_content[] = $this->_tag( 'td', $this->_api->be_buildRecordIcons( 'show,edit', self::$_dbTables[ 'profiles' ], $profile[ 'uid' ] ), $trParams );
            
            // Ends the row
            $this->_content[] = $this->_endTag();
            
            // Changes the row counter
            $rowCount         = ( $rowCount === 0 ) ? 1 : 0;
        }
        
        // Ends the table headers
        $this->_content[] = $this->_endTag();
        
        // Ends the table
        $this->_content[] = $this->_endTag();
    }
    
    /**
     * 
     */
    protected function _viewOptions()
    {
        // Starts the field set
        $this->_content[] = $this->_tag(
            'fieldset',
            '',
            array(),
            array(),
            true
        );
        
        // Adds the fieldset title
        $this->_content[] = $this->_tag(
            'legend',
            self::$_lang->getLL( 'options' )
        );
        
        // Confirmed select
        $this->_content[] = $this->_createSelect(
            'confirmed',
            self::$_lang->getLL( 'options.confirmed' ),
            array(
                self::$_lang->getLL( 'options.confirmed.all' ),
                self::$_lang->getLL( 'options.confirmed.yes' ),
                self::$_lang->getLL( 'options.confirmed.no' )
            )
        );
        
        // Validated select
        $this->_content[] = $this->_createSelect(
            'validated',
            self::$_lang->getLL( 'options.validated' ),
            array(
                self::$_lang->getLL( 'options.validated.all' ),
                self::$_lang->getLL( 'options.validated.yes' ),
                self::$_lang->getLL( 'options.validated.no' )
            )
        );
        // Proof select
        $this->_content[] = $this->_createSelect(
            'proof',
            self::$_lang->getLL( 'options.proof' ),
            array(
                self::$_lang->getLL( 'options.proof.all' ),
                self::$_lang->getLL( 'options.proof.yes' ),
                self::$_lang->getLL( 'options.proof.no' )
            )
        );
        
        // Project select
        $this->_content[] = $this->_createSelect(
            'project',
            self::$_lang->getLL( 'options.project' ),
            array(
                self::$_lang->getLL( 'options.project.all' ),
                self::$_lang->getLL( 'options.project.yes' ),
                self::$_lang->getLL( 'options.project.no' )
            )
        );
        
        // Adds the submit
        $this->_content[] = $this->_tag(
            'div',
            $this->_tag(
                'input',
                '',
                array(
                    'type'  => 'submit',
                    'value' => self::$_lang->getLL( 'options.submit' )
                )
            ),
            array(),
            array(
                'margin-left' => '150px'
            )
        );
        
        // Closes the fieldset
        $this->_content[] = $this->_endTag();
        $this->_content[] = $this->doc->spacer( 20 );
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/mod1/class.tx_adlercontest_module1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/mod1/class.tx_adlercontest_module1.php']);
}
