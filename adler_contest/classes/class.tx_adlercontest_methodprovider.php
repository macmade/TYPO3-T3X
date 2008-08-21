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
 * Method provider for the 'adler_contest' extension.
 *
 * @author      Jean-David Gadina (info@macmade.net)
 * @version     1.0
 */

class tx_adlercontest_methodProvider
{
    /**
     * The unique instance of the class (singleton)
     */
    protected static $_instance  = NULL;
    
    /**
     * The TYPO3 database object
     */
    protected $_db               = NULL;
    
    /**
     * The new line character
     */
    protected $_NL               = '';
    
    /**
     * Option tags for the countries
     */
    protected $_countriesOptions = '';
    
    /**
     * 
     */
    protected function __construct()
    {
        // Sets the new line character
        $this->_NL = chr( 10 );
        
        // Gets a reference to the database object
        $this->_db = $GLOBALS[ 'TYPO3_DB' ];
    }
    
    /**
     * 
     */
    public function __clone()
    {
        // Class cloning is disabled
        throw new Exception( 'Class' . __CLASS__ . 'cannot be cloned' );
    }
    
    /**
     * 
     */
    public static function getInstance()
    {
        // Checks if the unique instance already exists
        if( !is_object( self::$_instance ) ) {
            
            // Creates the unique instance (singleton)
            self::$_instance = new self();
        }
        
        // Returns the unique instance
        return self::$_instance;
    }
    
    /**
     * 
     */
    protected function _getCountriesOptions()
    {
        // Checks if the countries options already exist
        if( !$this->_countriesOptions ) {
            
            // Selects countries
            $res = $this->_db->exec_SELECTquery(
                'uid,cn_short_en',
                'static_countries',
                'uid',
                '',
                'cn_short_en'
            );
            
            // Checks the SQL ressource
            if( !$res ) {
                
                // DB error
                throw new Exception( 'Cannot get countries' );
            }
            
            // Storage
            $options = array();
            
            // Process each country
            while( $country = $this->_db->sql_fetch_assoc( $res ) ) {
                
                // Adds the option tag
                $options[] = '<option value="'
                          . $country[ 'uid' ]
                          . '">'
                          . $country[ 'cn_short_en' ]
                          . '</option>';
            }
            
            // Stores the coutries options
            $this->_countriesOptions = implode( $this->_NL, $options );
        }
        
        // Returns the option tags
        return $this->_countriesOptions;
    }
    
    /**
     * 
     */
    public function _countrySelect( $name )
    {
        // Select code
        $select = '<select name="'
                . $this->prefixId
                . '['
                . $name
                . ']" size="1">'
                . $this->_getCountriesOptions()
                . '</select>';
        
        // Returns the select box
        return $select;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_methodprovider.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_methodprovider.php']);
}

