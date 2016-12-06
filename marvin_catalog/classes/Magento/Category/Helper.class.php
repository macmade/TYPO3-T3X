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

# $Id: Helper.class.php 103 2009-12-01 13:54:50Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Magento category helper object
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
final class tx_marvincatalog_Magento_Category_Helper
{
    const EID = 'marvin_catalog_categories';
    
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance           = NULL;
    
    /**
     * The EID cache object
     */
    private static $_eid                = NULL;
    
    /**
     * The XML data
     */
    private static $_xml                = NULL;
    
    /**
     * The catalog categories
     */
    private static $_categories         = array();
    
    /**
     * The catalog categories (by url keys)
     */
    private static $_categoriesByKeys   = array();
    
    /**
     * The catalog categories hierarchy
     */
    private static $_hierarchy          = array();
    
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
        $this->_eid = new tx_marvincatalog_Eid_Cache( self::EID );
        
        try {
            
            $this->_xml = simplexml_load_string( $this->_eid->getContent() );
            
            if( $this->_xml ) {
                
                $this->_processCategories();
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
    protected function _processCategories()
    {
        foreach( $this->_xml as $key => $value ) {
            
            $id       = ( int )$value[ 'id' ];
            $key      = ( string )$value->url_key;
            $parentId = ( int )$value->parent_id;
            
            if( !isset( $this->_categories[ $id ] ) ) {
                
                $this->_categories[ $id ]       = new stdClass();
                $this->_categoriesByKey[ $key ] = $this->_categories[ $id ];
            }
            
            if( $parentId !== 0 && !isset( $this->_categories[ $parentId ] ) ) {
            
                $this->_categories[ $parentId ] = new stdClass();
            }
            
            $category       = $this->_categories[ $id ];
            $category->id   = $id;
            $category->name = ( string )$value->name;
            $category->key  = ( string )$value->url_key;
            
            if( isset( $value->image ) ) {
                
                $category->image = ( string )$value->image;
            }
            
            if( $parentId !== 0 ) {
                
                $parent = $this->_categories[ $parentId ];
                
                if( !isset( $parent->children ) ) {
                    
                    $parent->children = array();
                }
                
                $parent->children[] = $category;
                
            } else {
                
                $this->_hierarchy[] = $category;
            }
        }
    }
    
    /**
     * 
     */
    public function getCategory( $id )
    {
        if( !isset( $this->_categories[ ( int )$id ] ) ) {
            
            return NULL;
        }
        
        return $this->_categories[ ( int )$id ];
    }
    
    /**
     * 
     */
    public function getCategoryByKey( $key )
    {
        if( !isset( $this->_categoriesByKey[ ( string )$key ] ) ) {
            
            return NULL;
        }
        
        return $this->_categoriesByKey[ ( string )$key ];
    }
    
    /**
     * 
     */
    public function getAllCategories()
    {
        return $this->_hierarchy;
    }
    
    /**
     * 
     */
    public function getRootCategories()
    {
        $categories = array();
        
        if( count( $this->_hierarchy ) ) {
            
            $root = $this->_hierarchy[ 0 ];
            
            if( isset( $root->children ) && count( $root>children ) ) {
                
                $catalog = $root->children[ 0 ];
                
                if( isset( $catalog->children ) ) {
                    
                    $categories = $catalog->children;
                }
            }
        }
        
        return $categories;
    }
}
