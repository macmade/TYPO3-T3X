<?php
/***************************************************************
 * Copyright notice
 * 
 * (c) 2004 Jean-David Gadina (macmade@eosgarden.com)
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

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Language class
 *
 * @author      Jean-David Gadina <macmade@eosgarden.com>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  oop
 */
final class tx_oop_lang
{
    /**
     * 
     */
    private static $_instances   = array();
    
    /**
     * 
     */
    private static $_nbInstances = 0;
    
    /**
     * 
     */
    private static $_currentLang = '';
    
    /**
     * 
     */
    private $_labels             = array();
    
    /**
     * 
     */
    private $_instanceName       = '';
    
    /**
     * 
     */
    private $_langFilePath       = '';
    
    /**
     * 
     */
    private function __construct( $langFile )
    {
        $this->_instanceName = $langFile;
        
        $this->_langFilePath = t3lib_div::getFileAbsFileName( $langFile );
        
        if( $this->_langFilePath === '' ) {
            
            throw new tx_oop_langException(
                'Language file not found (' . $langFile . ')',
                tx_oop_langException::EXCEPTION_NO_LANG_FILE
            );
        }
        
        try {
            
            $xml = simplexml_load_file( $this->_langFilePath );
            
            foreach( $xml->data->languageKey as $langSection ) {
                
                $this->_labels[ ( string )$langSection[ 'index' ] ] = array();
                
                foreach( $langSection as $label ) {
                    
                    $this->_labels[ ( string )$langSection[ 'index' ] ][ ( string )$label[ 'index' ] ] = ( string )$label;
                }
            }
            
        } catch( Exception $e ) {
            
            throw new tx_oop_langException(
                $e->getMessage(),
                tx_oop_langException::EXCEPTION_BAD_XML
            );
        }
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        if( $this->_labels[ $this->_currentLang ][ $name ] ) {
            
            return $this->_labels[ $this->_currentLang ][ $name ];
            
        } elseif( isset( $this->_labels[ 'default' ][ $name ] ) ) {
            
            return $this->_labels[ 'default' ][ $name ];
            
        } else {
            
            return '[LABEL: ' . $name . ']';
        }
    }
    
    /**
     * 
     */
    public static function getInstance( $langFile )
    {
        if( self::$_nbInstances === 0 ) {
            
            self::_setCurrentLanguage();
        }
        
        if( isset( self::$_instances[ $langFile ] ) ) {
            
            return self::$_instances[ $langFile ];
            
        } else {
            
            self::$_instances[ $langFile ] = new self( $langFile );
            
            self::$_nbInstances++;
        }
    }
    
    /**
     * 
     */
    public static function _setCurrentLanguage()
    {
        if( defined( 'TYPO3_MODE' ) && TYPO3_MODE === 'BE' ) {
            
            self::$_currentLang = $GLOBALS[ 'LANG' ]->lang;
            
        } elseif( defined( 'TYPO3_MODE' ) && TYPO3_MODE === 'FE' ) {
            
            self::$_currentLang = $GLOBALS[ 'TSFE' ]->config[ 'config' ][ 'language' ];
        }
    }
}
