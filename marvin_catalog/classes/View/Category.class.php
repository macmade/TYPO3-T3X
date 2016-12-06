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

# $Id: Category.class.php 194 2010-01-27 08:55:55Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Category view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_View_Category extends tx_marvincatalog_View_Base
{
    protected $_category    = NULL;
    protected $_watches     = array();
    protected $_collections = array();
    
    public function __construct( $plugin, $category )
    {
        parent::__construct( $plugin );
        
        $this->_collections = tx_marvincatalog_Magento_Category_Helper::getInstance()->getRootCategories();
        $this->_watches     = tx_marvincatalog_Magento_Catalog_Helper::getInstance()->getWatchesForCategoryKey( $category->key );
        $this->_category    = $category;
        $rootCategoryName   = '';
        
        if( isset( $this->_plugin->piVars[ 'collection' ] ) ) {
            
            $rootCategory = tx_marvincatalog_Magento_Category_Helper::getInstance()->getCategoryByKey( $this->_plugin->piVars[ 'collection' ] );
            
            if( $rootCategory ) {
                
                $rootCategoryName = ' - ' . $rootCategory->name;
            }
        }
        
        $this->_setPageTitle( strtoupper( $category->key ) . $rootCategoryName );
        
        $left  = $this->_content->div;
        $right = $this->_content->div;
        
        $this->_cssClass( 'collectionList', $left );
        $this->_cssClass( 'categoryList', $right );
        
        $this->_listCollections( $left );
        
        if( !count( $this->_watches ) ) {
            
            $error = $right->div;
            $error->addTextData( $this->_lang->errorNoRecord );
            
        } else {
        
            if( count( $this->_watches ) > $this->_plugin->conf[ 'numberOfWatchesPerPage' ] ) {
                
                $this->_initSlider( 'tx-marvincatalog-pi1-pages' );
            }
            
            $this->_listWatches( $right );
        }
    }
    
    protected function _listWatches( tx_oop_Xhtml_Tag $div )
    {
        $i     = 0;
        $pages = $div->div;
        $list  = $pages->ul;
        
        $this->_cssClass( 'pages', $pages );
        $this->_cssClass( 'list', $list );
        
        $pages[ 'id' ] = 'tx-marvincatalog-pi1-pages';
        
        foreach( $this->_watches as $watch ) {
            
            if( $i === 0 ) {
                
                $container = $list->li;
                $this->_cssClass( 'listPage', $container );
            }
            
            $div             = $container->div;
            $link            = $div->a;
            $link[ 'title' ] = $watch->sku;
            $link[ 'href' ] = $this->_link(
                array(
                    'category' => $this->_plugin->piVars[ 'category' ],
                    'watch'    => $watch->key,
                    'action'   => 'catalog-watch'
                )
            );
            
            $imgKey = $this->_plugin->conf[ 'watchListImageKey' ];
            $img    = $this->_getProductImagePath( ( isset( $watch->$imgKey ) ) ? $watch->$imgKey : $watch->image );
            
            $link->addTextData( $this->_createImage( $watch->sku, $img ) );
            $this->_cssClass( 'listElement', $div );
            
            $i++;
            
            if( $i >= $this->_plugin->conf[ 'numberOfWatchesPerPage' ] ) {
                
                $i = 0;
            }
        }
    }
    
    protected function _listCollections( tx_oop_Xhtml_Tag $container )
    {
        $div = $container->div;
        
        foreach( $this->_collections as $collection ) {
            
            $link           = $div->h2->a;
            $link[ 'href' ] = $this->_link( array( 'collection' => $collection->key ) );
            
            $link->addTextData( $collection->name );
            
            if( isset( $this->_plugin->piVars[ 'collection' ] ) && $collection->key == $this->_plugin->piVars[ 'collection' ] ) {
                
                if( isset( $collection->children ) && is_array( $collection->children ) ) {
                    
                    $sub = $div->div;
                    
                    $this->_cssClass( 'subCategories', $sub );
                    $this->_listCategories( $collection->children, $sub );
                }
            }
        }
    }
    
    protected function _listCategories( array $categories, tx_oop_Xhtml_Tag $container )
    {
        $magentoUrl = $this->_extConf[ 'magento_url' ];
        
        foreach( $categories as $category ) {
            
            $link            = $container->div->a;
            $link[ 'title' ] = $category->name;
            $link[ 'href' ]  = $this->_link(
                array(
                    'category' => $category->key,
                    'action'   => 'catalog-category'
                )
            );
            
            $link->addTextData( $category->name );
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/Category.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/Category.class.php']);
}
