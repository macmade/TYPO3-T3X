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
 * Abstract class for the plugin wizard icon classes
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
abstract class tx_oop_Plugin_Wizard_Icon
{
    /**
     * The extension key
     */
    private $_extKey     = '';
    
    /**
     * The plugin name
     */private  $_piName = '';
    
    /**
     * The plugin number
     */
    private $_piNum      = '';
    
    /**
     * The language object
     */
    private $_lang       = NULL;
    
    /**
     * The language object
     */
    private $_reflection = NULL;
    
    /**
     * Class constructor
     * 
     * @return  NULL
     * @see     _setStaticVars
     */
    public function __construct()
    {
        // Creates a relfection object for the current instance
        $this->_reflection = new ReflectionObject( $this );
        
        // Gets the current path
        $extPathInfo = explode( DIRECTORY_SEPARATOR, $this->_reflection->getFileName() );
        
        // Removes the file name and the 'classes' directory
        array_pop( $extPathInfo );
        array_pop( $extPathInfo );
        
        // Sets the extension key
        $this->_extKey = array_pop( $extPathInfo );
        
        // Sets the plugin name
        $this->_piName = str_replace( '_', '', $this->_extKey );
        
        // Sets the plugin number
        $this->_piNum  = substr( str_replace( 'tx_' . $this->_piName . '_', '', get_class( $this ) ), 0, -8 );
        
        // Creates the language object
        $this->_lang = tx_oop_Lang_Getter::getInstance( 'EXT:' . $this->_extKey . '/lang/wiz-' . $this->_piNum . '.xml' );
    }
    
    /**
     * Adds the plugin wizard icon to the wizard items array
     *
     * @param   array   The wizard items
     * @return  array   The modified array with the wizard items
     */
    public function proc( array $wizardItems )
    {
        // Wizard item
        $wizardItems[ 'plugins_tx_' . $this->_piName . '_' . $this->_piNum ] = array(
            
            // Icon
            'icon'        => t3lib_extMgm::extRelPath( $this->_extKey ) . 'res/img/wiz-' . $this->_piNum . '.gif',
            
            // Title
            'title'       => $this->_lang->title,
            
            // Description
            'description' => $this->_lang->description,
            
            // Parameters
            'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=' . $this->_extKey . '_' . $this->_piNum
        );
        
        // Returns the wizard items
        return $wizardItems;
    }
}
