<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 eosgarden - Jean-David Gadina (macmade@eosgarden.com)               #
# All rights reserved                                                          #
#                                                                              #
# This script is part of the TYPO3 project. The TYPO3 project is free          #
# software. You can redistribute it and/or modify it under the terms of the    #
# GNU General Public License as published by the Free Software Foundation,     #
# either version 2 of the License, or (at your option) any later version.      #
#                                                                              #
# The GNU General Public License can be found at:                              #
# http://www.gnu.org/copyleft/gpl.html.                                        #
#                                                                              #
# This script is distributed in the hope that it will be useful, but WITHOUT   #
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or        #
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for    #
# more details.                                                                #
#                                                                              #
# This copyright notice MUST APPEAR in all copies of the script!               #
################################################################################

# $Id$

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * TYPO3 miscellanii utilities
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_Typo3_Utils
{
    /**
     * 
     */
    private function __construct()
    {}
    
    /**
     * 
     */
    public static function typo3ModeCheck()
    {
        // Security check
        if( !defined( 'TYPO3_MODE' ) ) {
            
            // TYPO3 is not running
            trigger_error(
                'This script cannot be used outside TYPO3',
                E_USER_ERROR
            );
        }
    }
    
    /**
     * 
     */
    public static function addSaveAndNewButton( $extKey, $table )
    {
        $tableName = 'tx_' . str_replace( '_', '', $extKey ) . '_' . $table;
        t3lib_extMgm::addUserTSConfig( 'options.saveDocNew.'. $tableName . ' = 1' );
    }
    
    /**
     * 
     */
    public static function addBackendModule( $extKey, $section, $modNum = 1 )
    {
        // Backend options
        if( TYPO3_MODE === 'BE' ) {
            
            // Name of the module
            $modName = 'tx' . str_replace( '_', '', $extKey ) . 'M' . $modNum;
            
            // Adds the backend module
            t3lib_extMgm::addModule(
                $section,
                $modName,
                '',
                t3lib_extMgm::extPath( $extKey ) . 'mod1/'
            );
        }
    }
    
    /**
     * 
     */
    public static function addPluginWizardIcon( $extKey, $piNum = 1 )
    {
        // Backend options
        if( TYPO3_MODE === 'BE' ) {
            
            // Wizard icon class name
            $wizClass = 'tx_' . str_replace( '_', '', $extKey ) . '_pi' . $piNum . '_wizicon';
            
            // Adds the wozard icon
            $GLOBALS[ 'TBE_MODULES_EXT' ][ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ $wizClass ] = t3lib_extMgm::extPath( $extKey ) . 'classes/class.' . $wizClass . '.php';
         }
    }
    
    /**
     * 
     */
    public function addFrontendPlugin( $extKey, $piNum = 1, $cache = true, $type = 'list_type' )
    {
        // Plugin class name
        $piClass = 'tx_' . str_replace( '_', '', $extKey ) . '_pi' . $piNum;
        
        // Adds the frontend plugin
        t3lib_extMgm::addPItoST43(
            $extKey,
            'pi' . $piNum . '/class.' . $piClass . '.php',
            '_pi' . $piNum,
            $type,
            $cache
        );
    }
    
    /**
     * 
     */
    public function configureFrontendPlugin( $extKey, $piNum = 1, $flex = true, $type = 'list_type' )
    {
        // Loads the 'tt_content' TCA
        t3lib_div::loadTCA( 'tt_content' );
        
        // Checks if a flexform must be added
        if( $flex ) {
        
            // Plugin options
            $GLOBALS[ 'TCA' ][ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $extKey . '_pi' . $piNum ] = 'layout,select_key,pages,recursive';
            
            // Add flexform field to plugin options
            $GLOBALS[ 'TCA' ][ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ $extKey . '_pi' . $piNum ]     = 'pi_flexform';
            
            // Adds the flexform data structure
            t3lib_extMgm::addPiFlexFormValue(
                $extKey . '_pi' . $piNum,
                'FILE:EXT:' . $extKey . '/flex/pi' . $piNum . '.xml'
            );
            
        } else {
            
            // Plugin options
            $GLOBALS[ 'TCA' ][ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ $extKey . '_pi' . $piNum ] = 'layout,select_key';
        }
        
        // Adds the frontend plugin
        t3lib_extMgm::addPlugin(
            array(
                'LLL:EXT:' . $extKey . '/lang/tt_content.xml:' . $type . '.pi' . $piNum,
                $extKey . '_pi' . $piNum
            ),
            $type
        );
    }
    
    /**
     * Process a TYPO3 path
     * 
     * This method will process TYPO3 path prefixes, like 'EXT:'.
     * 
     * @param   string  The path to process
     * @return  string  The processed path
     */
    public static function getWebPath( $path )
    {
        // Matches array for the preg_match function
        $matches = array();
        
        // Checks if the path is relative to an extension
        preg_match( '/^EXT:([^\/]+)/', $path, $matches );
        
        // Checks if an extension was found
        if( isset( $matches[ 1 ] ) ) {
            
            // Gets the extension path
            $extPath = t3lib_extMgm::siteRelPath( $matches[ 1 ] );
            
            // Replaces the extension path
            $path    = str_replace( 'EXT:' . $matches[ 1 ] . '/', $extPath, $path );
        }
        
        // Returns the processed path
        return $path;
    }
    
    /**
     * 
     */
    public static function getSystemPath( $path )
    {
        return str_replace( '/', DIRECTORY_SEPARATOR, t3lib_div::getIndpEnv( 'TYPO3_DOCUMENT_ROOT' ) . DIRECTORY_SEPARATOR . self::getWebPath( $path ) );
    }
}
