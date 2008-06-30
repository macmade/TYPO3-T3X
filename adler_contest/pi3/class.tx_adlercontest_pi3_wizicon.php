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
 * Class that adds the wizard icon.
 *
 * @author		Jean-David Gadina (info@macmade.net)
 * @version		3.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *   43:		class tx_dropdownsitemap_pi3_wizicon
 *   65:		public function __construct
 *   99:		protected function _getLabel( $label )
 *  110:		public function proc( array $wizardItems )
 * 
 *				TOTAL FUNCTIONS: 3
 */

class tx_adlercontest_pi3_wizicon
{
    // Extension key
    protected static $_extKey = '';
    
    // Plugin name
    protected static $_piName = '';
    
    // Plugin number
    protected static $_piNum  = '';
    
    // Language object
    protected static $_lang   = NULL;
    
    // Language labels
    protected static $_labels = array();
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Checks for the language object
        if( !is_object( self::$_lang ) ) {
            
            // Gets the current path
            $extPathInfo   = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
            
            // Sets the plugin number
            self::$_piNum  = array_pop( $extPathInfo );
            
            // Sets the extension key
            self::$_extKey = array_pop( $extPathInfo );
            
            // Sets the plugin name
            self::$_piName = str_replace( '_', '', self::$_extKey );
            
            // Gets the language object
            self::$_lang   = $GLOBALS[ 'LANG' ];
            
            // Gets the language labels
            self::$_labels = self::$_lang->includeLLFile(
                'EXT:' . self::$_extKey . '/lang/wiz_' . self::$_piNum . '.xml',
                false
            );
        }
    }
    
    /**
     * Gets a locallang label
     * 
     * @param   string  $label  The name of the label to get
     * @return  string  The locallang label
     */
    protected function _getLabel( $label )
    {
        return self::$_lang->getLLL( $label, self::$_labels );
    }
    
    /**
     * Process the wizard items array
     *
     * @param   array   $wizardItems    The wizard items
     * @return  array   The modified array with the wizard items
     */
    public function proc( array $wizardItems )
    {
        // Wizard item
        $wizardItems[ 'plugins_' . self::$_piName . '_' . self::$_piNum ] = array(
            
            // Icon
            'icon'        => t3lib_extMgm::extRelPath( self::$_extKey ) . 'res/wiz_' . self::$_piNum . '.gif',
            
            // Title
            'title'       => $this->_getLabel( 'pi_title' ),
            
            // Description
            'description' => $this->_getLabel( 'pi_plus_wiz_description' ),
            
            // Parameters
            'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=' . self::$_extKey . '_' . self::$_piNum
        );
        
        // Returns the wizard items
        return $wizardItems;
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3_wizicon.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/pi3/class.tx_adlercontest_pi3_wizicon.php']);
}
