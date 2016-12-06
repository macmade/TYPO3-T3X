<?php
################################################################################
#                                                                              #
#                               COPYRIGHT NOTICE                               #
#                                                                              #
# (c) 2009 netinfluence                                                        #
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

# $Id: Helper.class.php 160 2009-12-14 13:43:11Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Magento catalog helper object
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
final class tx_marvincatalog_Magento_Catalog_Helper
{
    const EID = 'marvin_catalog_catalog';
    
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance      = NULL;
    
    /**
     * The EID cache object
     */
    private $_eid                  = NULL;
    
    /**
     * The categories helper
     */
    private $_categories           = NULL;
    
    /**
     * The XML data
     */
    private $_xml                  = NULL;
    
    /**
     * The catalog watches
     */
    private $_watches              = array();
    
    /**
     * The catalog watches by URL key
     */
    private $_watchesByKey         = array();
    
    /**
     * The catalog watches, by category
     */
    private $_watchesByCategory    = array();
    
    /**
     * The catalog watches, by category key
     */
    private $_watchesByCategoryKey = array();
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return  NULL
     */
    private function __construct()
    {
        $this->_eid        = new tx_marvincatalog_Eid_Cache( self::EID );
        $this->_categories = tx_marvincatalog_Magento_Category_Helper::getInstance();
        
        try {
            
            $this->_xml = simplexml_load_string( $this->_eid->getContent() );
            
            if( $this->_xml ) {
                
                $this->_processCatalog();
            }
            
        } catch( Exception $e ) {}
    }
    
    /**
     * Clones an instance of the class
     * 
     * A call to this method will produce an exception, as the class cannot
     * be cloned (singleton).
     * 
     * @return  NULL
     * @throws  tx_oop_Core_Singleton_Exception Always, as the class cannot be cloned (singleton)
     */
    public function __clone()
    {
        throw new tx_oop_Core_Singleton_Exception(
            'Class ' . __CLASS__ . ' cannot be cloned',
            tx_oop_Core_Singleton_Exception::EXCEPTION_CLONE
        );
    }
    
    /**
     * Gets the unique class instance
     * 
     * This method is used to get the unique instance of the class
     * (singleton). If no instance is available, it will create it.
     * 
     * @return  tx_marvincatalog_Magento_Category_Helper    The unique instance of the class
     */
    public static function getInstance()
    {
        // Checks if the unique instance already exists
        if( !is_object( self::$_instance ) ) {
            
            // Creates the unique instance
            self::$_instance = new self();
        }
        
        // Returns the unique instance
        return self::$_instance;
    }
    
    /**
     * 
     */
    protected function _processCatalog()
    {
        foreach( $this->_xml as $key => $value ) {
            
            $watch              = new stdClass();
            $watch->sku         = ( string )$value[ 'sku' ];
            $watch->tag         = ( string )$value[ 'tag' ];
            $watch->name        = ( string )$value->name;
            $watch->movement    = ( string )$value->watch_move;
            $watch->display     = ( string )$value->watch_display;
            $watch->watchCase   = ( string )$value->watch_case;
            $watch->dial        = ( string )$value->watch_dial;
            $watch->glass       = ( string )$value->watch_glass;
            $watch->atm         = ( string )$value->watch_atm;
            $watch->bracelet    = ( string )$value->watch_bracelet;
            $watch->key         = ( string )$value->url_key;
            $watch->description = ( string )$value->description;
            $categories         = explode( ',', ( string )$value->categories );
            
            if( isset( $value->image ) ) {
                
                $watch->image = ( string )$value->image;
            }
            
            if( isset( $value->thumbnail ) ) {
                
                $watch->thumbnail = ( string )$value->thumbnail;
            }
            
            if( isset( $value->small_image ) ) {
                
                $watch->smallImage = ( string )$value->small_image;
            }
            
            if( isset( $value->watch_catalog_image ) ) {
                
                $watch->catalogImage = ( string )$value->watch_catalog_image;
            }
            
            if( isset( $value->watch_details_image ) ) {
                
                $watch->detailsImage = ( string )$value->watch_details_image;
            }
            
            $this->_watches[ $watch->sku ]      = $watch;
            $this->_watchesByKey[ $watch->key ] = $watch;
            
            foreach( $categories as $categoryId ) {
                
                $category    = $this->_categories->getCategory( $categoryId );
                $categoryKey = $category->key;
                
                if( !isset( $this->_watchesByCategory[ $categoryId ] ) ) {
                    
                    $this->_watchesByCategory[ $categoryId ] = array();
                }
                
                if( !isset( $this->_watchesByCategoryKey[ $categoryKey ] ) ) {
                    
                    $this->_watchesByCategoryKey[ $categoryKey ] = array();
                }
                
                $this->_watchesByCategory[ $categoryId ][ $watch->sku ] = $watch;
                $this->_watchesByCategoryKey[ $categoryKey ][ $watch->sku ] = $watch;
                
                ksort( $this->_watchesByCategory[ $categoryId ] );
                ksort( $this->_watchesByCategoryKey[ $categoryKey ] );
            }
        }
    }
    
    /**
     * 
     */
    public function getWatch( $sku )
    {
        if( !isset( $this->_watches[ ( string )$sku ] ) ) {
            
            return NULL;
        }
        
        return $this->_watches[ ( string )$sku ];
    }
    
    /**
     * 
     */
    public function getWatchByKey( $key )
    {
        if( !isset( $this->_watchesByKey[ ( string )$key ] ) ) {
            
            return NULL;
        }
        
        return $this->_watchesByKey[ ( string )$key ];
    }
    
    /**
     * 
     */
    public function getWatchesForCategory( $id )
    {
        if( !isset( $this->_watchesByCategory[ ( int )$id ] ) ) {
            
            return array();
        }
        
        return $this->_watchesByCategory[ ( int )$id ];
    }
    
    /**
     * 
     */
    public function getWatchesForCategoryKey( $key )
    {
        if( !isset( $this->_watchesByCategoryKey[ ( string )$key ] ) ) {
            
            return array();
        }
        
        return $this->_watchesByCategoryKey[ ( string )$key ];
    }
}
