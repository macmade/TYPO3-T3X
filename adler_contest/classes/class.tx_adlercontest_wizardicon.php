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
 * Abstract class for the plugin wizard icon classes.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     3.0
 */
abstract class tx_adlercontest_wizardIcon
{
    /**
     * Wether the static variables are set or not
     */
    private static $_hasStatic  = false;
    
    /**
     * The extension key
     */
    protected static $_extKey   = '';
    
    /**
     * The plugin name
     */
    protected static $_piName   = '';
    
    /**
     * The language object
     */
    protected static $_lang     = NULL;
    
    /**
     * The plugin number
     */
    protected $_piNum           = '';
    
    /**
     * The language labels
     */
    protected $_labels          = array();
    
    /**
     * The wizard class file (must be declared in the child classes)
     */
    protected $_wizardClassFile = __FILE__;
    
    /**
     * Class constructor
     * 
     * @return  NULL
     * @see     _setStaticVars
     */
    public function __construct()
    {
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
        }
        
        // Gets the current path
        $extPathInfo   = explode( DIRECTORY_SEPARATOR, dirname( $this->_wizardClassFile ) );
        
        // Sets the plugin number
        $this->_piNum  = array_pop( $extPathInfo );
        
        // Gets the language labels
        $this->_labels = self::$_lang->includeLLFile(
            'EXT:' . self::$_extKey . '/lang/wiz_' . $this->_piNum . '.xml',
            false
        );
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     */
    private static function _setStaticVars()
    {
        // Gets the current path
        $extPathInfo      = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
        
        // Removes the 'classes' directory
        array_pop( $extPathInfo );
        
        // Sets the extension key
        self::$_extKey    = array_pop( $extPathInfo );
        
        // Sets the plugin name
        self::$_piName    = str_replace( '_', '', self::$_extKey );
        
        // Gets the language object
        self::$_lang      = $GLOBALS[ 'LANG' ];
        
        // Static variables are set
        self::$_hasStatic = true;
    }
    
    /**
     * Gets a locallang label
     * 
     * @param   string  $label  The name of the label to get
     * @return  string  The locallang label
     */
    protected function _getLabel( $label )
    {
        return self::$_lang->getLLL( $label, $this->_labels );
    }
    
    /**
     * Adds the plugin wizard icon to the wizard items array
     *
     * @param   array   $wizardItems    The wizard items
     * @return  array   The modified array with the wizard items
     * @see     _getLabel
     */
    public function proc( array $wizardItems )
    {
        // Wizard item
        $wizardItems[ 'plugins_tx_' . self::$_piName . '_' . $this->_piNum ] = array(
            
            // Icon
            'icon'        => t3lib_extMgm::extRelPath( self::$_extKey ) . 'res/img/wiz_' . $this->_piNum . '.gif',
            
            // Title
            'title'       => $this->_getLabel( 'pi_title' ),
            
            // Description
            'description' => $this->_getLabel( 'pi_plus_wiz_description' ),
            
            // Parameters
            'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=' . self::$_extKey . '_' . $this->_piNum
        );
        
        // Returns the wizard items
        return $wizardItems;
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_wizardicon.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_wizardicon.php']);
}
