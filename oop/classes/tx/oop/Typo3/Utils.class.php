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
    private static function _getVarType( &$var )
    {
        $type = 'unknown';
        
        if( is_object( $var ) ) {
            
            $type = 'object';
            
        } elseif( is_resource( $var ) ) {
            
            $type = 'ressource';
            
        } elseif( is_array( $var ) ) {
            
            $type = 'array';
            
        } elseif( is_string( $var ) ) {
            
            $type = 'string';
            
        } elseif( is_int( $var ) ) {
            
            $type = 'int';
            
        } elseif( is_link( $var ) ) {
            
            $type = 'link';
            
        } elseif( is_float( $var ) ) {
            
            $type = 'float';
            
        } elseif( is_null( $var ) ) {
            
            $type = 'null';
            
        } elseif( is_bool( $var ) ) {
            
            $type = 'boolean';
        }
        
        return $type;
    }
    
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
     * 
     */
    public static function viewArray( array $array, $return = false )
    {
        $commonStyle          = 'font-family: Verdana, sans-serif; font-size: 10px; color: #898989; ';
        
        $container            = new tx_oop_Xhtml_Tag( 'div' );
        $container[ 'style' ] = $commonStyle;
        
        $container->comment( 'PHP array debug  - start' );
        
        $table                = $container->table;
        $table[ 'style' ]     = 'background-color: #EDF5FA; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        
        $container->comment( 'PHP array debug - end' );
        
        foreach( $array as $key => &$value ) {
            
            $varType = self::_getVarType( $value );
            
            $row = $table->tr;
            
            $labelCol = $row->td;
            $dataCol  = $row->td;
            
            $labelCol[ 'width' ] = '20%';
            $labelCol[ 'style' ] = $commonStyle . 'background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
            $dataCol[ 'style' ]  = $commonStyle . 'background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
            
            $label            = $labelCol->strong;
            $label[ 'style' ] = 'color: #0062A0;';
            $labelCol->span   = ': ' . $varType;
            $label->addTextData( $key );
            
            if( is_array( $value ) ) {
                
                $dataCol->addTextData( self::viewArray( $value, true ) );
                
            } elseif( is_object( $value ) ) {
                
                $dataCol->div->pre = print_r( $value, true );
                
            } elseif( is_bool( $value ) ) {
                
                $value = ( $value ) ? 'true' : 'false';
                $dataCol->addTextData( $value );
                
            } else {
                
                $dataCol->addTextData( $value );
            }
        }
        
        if( $return ) {
            
            return $container;
        }
        
        print ( string )$container;
    }
    
    /**
     * 
     */
    public function debug( $var, $return = false, $header = 'Debug informations' )
    {
        $commonStyle          = 'font-family: Verdana, sans-serif; font-size: 10px; color: #898989; ';
        
        $container            = new tx_oop_Xhtml_Tag( 'div' );
        $container[ 'style' ] = $commonStyle . 'background-color: #EDF5FA; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        
        $container->comment( 'PHP variable debug  - start' );
        
        $headerSection            = $container->div;
        $headerSection[ 'style' ] = $commonStyle . 'text-align: center; background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        $headerText               = $headerSection->strong;
        $headerText[ 'style' ]    = 'color: #0062A0; font-size: 15px';
        $headerText->addTextData( $header );
        
        $typeSection            = $container->div;
        $typeSection[ 'style' ] = $commonStyle . 'background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        $typeText               = $typeSection->strong;
        $typeText[ 'style' ]    = 'color: #0062A0;';
        $typeSection->span      = self::_getVarType( $var );
        $typeText->addTextData( 'Variable type:' );
        
        $container->comment( 'PHP variable debug - end' );
        
        $dataDiv            = $container->div;
        $dataDiv[ 'style' ] = $commonStyle . 'background-color: #FFFFFF; border: solid 1px #D3E7F4; margin: 2px; padding: 2px;';
        
        if( is_array( $var ) ) {
            
            $dataDiv->addTextData( self::viewArray( $var, true ) );
            
        } elseif( is_object( $var ) ) {
                
                $dataDiv->pre = print_r( $var, true );
                
            } elseif( is_bool( $value ) ) {
                
                $value = ( $var ) ? 'true' : 'false';
                $dataDiv->addTextData( $value );
                
            } else {
                
                $dataDiv->addTextData( $var );
            }
        
        if( $return ) {
            
            return $container;
        }
        
        print ( string )$container;
    }
}
