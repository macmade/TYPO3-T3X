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
     * Option tags for the days
     */
    protected $_daysOptions      = '';
    
    /**
     * Option tags for the monthes
     */
    protected $_monthesOptions   = '';
    
    /**
     * Option tags for the years
     */
    protected $_yearsOptions     = '';
    
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
    protected function _getDaysOptions()
    {
        // Checks if the days options already exist
        if( !$this->_daysOptions ) {
            
            // Storage
            $options = array();
            
            // Process each day
            for( $i = 1; $i < 32; $i++ ) {
                
                // Adds the option tag
                $options[] = '<option value="'
                           . $i
                           . '">'
                           . $i
                           . '</option>';
            }
            
            // Stores the days options
            $this->_daysOptions = implode( $this->_NL, $options );
        }
        
        // Returns the option tags
        return $this->_daysOptions;
    }
    
    /**
     * 
     */
    protected function _getMonthesOptions()
    {
        // Checks if the monthes options already exist
        if( !$this->_monthesOptions ) {
            
            // Storage
            $options = array();
            
            // Process each month
            for( $i = 1; $i < 13; $i++ ) {
                
                // Month name
                $monthName = strftime( '%B', strtotime( '2008-' . $i . '-1' ) );
                
                // Adds the option tag
                $options[] = '<option value="'
                           . $i
                           . '">'
                           . $monthName
                           . '</option>';
            }
            
            // Stores the monthes options
            $this->_monthesOptions = implode( $this->_NL, $options );
        }
        
        // Returns the option tags
        return $this->_monthesOptions;
    }
    
    /**
     * 
     */
    protected function _getYearsOptions()
    {
        // Checks if the years options already exist
        if( !$this->_yearsOptions ) {
            
            // Storage
            $options     = array();
            
            // Current year
            $currentYear = date( 'Y' );
            
            // Lowest year
            $lowYear     = $currentYear - 101;
            
            // Process each year
            for( $i = $currentYear; $i > $lowYear; $i-- ) {
                
                // Adds the option tag
                $options[] = '<option value="'
                           . $i
                           . '">'
                           . $i
                           . '</option>';
            }
            
            // Stores the years options
            $this->_yearsOptions = implode( $this->_NL, $options );
        }
        
        // Returns the option tags
        return $this->_yearsOptions;
    }
    
    /**
     * 
     */
    public function countrySelect( $name )
    {
        // Select code
        $select = '<select name="'
                . $name
                . '" size="1">'
                . $this->_getCountriesOptions()
                . '</select>';
        
        // Returns the select box
        return $select;
    }
    
    /**
     * 
     */
    public function dateSelect( $name )
    {
        // Select code for the days
        $selectDays    = '<select name="'
                       . $name
                       . '[day]" size="1">'
                       . $this->_getDaysOptions()
                       . '</select>';
        
        // Select code for the days
        $selectMonthes = '<select name="'
                       . $name
                       . '[month]" size="1">'
                       . $this->_getMonthesOptions()
                       . '</select>';
        
        // Select code for the days
        $selectYears   = '<select name="'
                       . $name
                       . '[year]" size="1">'
                       . $this->_getYearsOptions()
                       . '</select>';
        
        // Returns the select box
        return $selectDays . ' ' . $selectMonthes . ' ' . $selectYears;
    }
}

// XClass inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_methodprovider.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/adler_contest/classes/class.tx_adlercontest_methodprovider.php']);
}

