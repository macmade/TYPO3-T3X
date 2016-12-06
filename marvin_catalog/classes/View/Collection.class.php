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

# $Id: Collection.class.php 194 2010-01-27 08:55:55Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * Collection view
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
class tx_marvincatalog_View_Collection extends tx_marvincatalog_View_Base
{
    protected $_categories        = NULL;
    protected $_collections       = NULL;
    protected $_defaultCollection = 0;
    protected $_preload           = NULL;
    
    public function __construct( tslib_piBase $plugin, $collections, $defaultCollection )
    {
        parent::__construct( $plugin );
        
        $this->_categories        = tx_marvincatalog_Magento_Category_Helper::getInstance();
        $this->_collections       = $collections;
        $this->_defaultCollection = $defaultCollection;
        $this->_preload           = new tx_oop_Xhtml_Tag( 'script' );
        $this->_preload[ 'type' ] = 'text/javascript';
        
        $this->_preload->addTextData( chr( 10 ) . '// <![CDATA[' . chr( 10 ) );
        
        $left  = $this->_content->div;
        $right = $this->_content->div;
        
        $this->_cssClass( 'collectionList', $left );
        $this->_cssClass( 'collectionImage', $right );
        
        $defaultCollection = $this->_categories->getCategory( $this->_defaultCollection );
        
        $this->_setPageTitle( $defaultCollection->name );
        $this->_showImage( $defaultCollection, $right );
        $this->_listCollections( $left );
        
        $this->_preload->addTextData( '// ]]>' . chr( 10 ) );
        
        $GLOBALS[ 'TSFE' ]->additionalHeaderData[ __METHOD__ ] = ( string )$this->_preload;
    }
    
    protected function _showImage( $collection, tx_oop_Xhtml_Tag $container )
    {
        $magentoUrl   = $this->_extConf[ 'magento_url' ];
        $imgPath      = '/media/catalog/category/' . $collection->image;
        $img          = $container->img;
        $img[ 'src' ] = $magentoUrl . $imgPath;
        $img[ 'id' ]  = $this->_plugin->prefixId . '_collectionImage';
    }
    
    protected function _listCollections( tx_oop_Xhtml_Tag $container )
    {
        $div        = $container->div;
        $magentoUrl = $this->_extConf[ 'magento_url' ];
        
        foreach( $this->_collections as $collection ) {
            
            $link                  = $div->h2->a;
            $link[ 'title' ]       = $collection->name;
            $link[ 'href' ]        = $this->_link( array( 'collection' => $collection->key ) );
            
            $link->addTextData( $collection->name );
            
            if( $collection->image ) {
                
                $imgPath               = $magentoUrl . '/media/catalog/category/' . $collection->image;
                $link[ 'onmouseover' ] = '$( \'#' . $this->_plugin->prefixId . '_collectionImage\' ).attr( \'src\', \'' . $imgPath . '\' );';
                
                $preload = 'var '
                         . $this->_plugin->prefixId
                         . '_catImg'
                         . $i
                         . ' = new Image();'
                         . chr( 10 )
                         . $this->_plugin->prefixId
                         . '_catImg'
                         . $i
                         . '.src = \''
                         . $imgPath
                         . '\''
                         . chr( 10 );
            }
            
            if( $collection->id == $this->_defaultCollection ) {
                
                if( isset( $collection->children ) && is_array( $collection->children ) ) {
                    
                    $sub = $div->div;
                    
                    $this->_cssClass( 'subCategories', $sub );
                    $this->_listCategories( $collection, $sub );
                }
            }
        }
    }
    
    protected function _listCategories( $collection, tx_oop_Xhtml_Tag $container )
    {
        $magentoUrl = $this->_extConf[ 'magento_url' ];
        $i          = 0;
        
        foreach( $collection->children as $category ) {
            
            $link            = $container->div->a;
            $link[ 'title' ] = $category->name;
            $link[ 'href' ]  = $this->_link(
                array(
                    'collection' => $collection->key,
                    'category'   => $category->key,
                    'action'     => 'catalog-category'
                )
            );
            
            $link->addTextData( $category->name );
            
            if( $category->image ) {
                
                $imgPath = $magentoUrl . '/media/catalog/category/' . $category->image;
                $preload = 'var '
                         . $this->_plugin->prefixId
                         . '_catImg'
                         . $i
                         . ' = new Image();'
                         . chr( 10 )
                         . $this->_plugin->prefixId
                         . '_catImg'
                         . $i
                         . '.src = \''
                         . $imgPath
                         . '\''
                         . chr( 10 );
                
                $this->_preload->addTextData( $preload );
                
                $link[ 'onmouseover' ] = '$( \'#' . $this->_plugin->prefixId . '_collectionImage\' ).attr( \'src\', \'' . $imgPath . '\' );';
                
                $i++;
            }
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/Collection.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/Collection.class.php']);
}
