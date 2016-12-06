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

# $Id: Base.class.php 194 2010-01-27 08:55:55Z macmade $

// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * View abstract
 *
 * @author      Jean-David Gadina <macmade@netinfluence.ch>
 * @version     1.0
 * @package     TYPO3
 * @subpackage  marvin_catalog
 */
abstract class tx_marvincatalog_View_Base
{
    private static $_hasStatic = false;
    protected static $_tsfe    = NULL;
    protected $_content        = NULL;
    protected $_plugin         = NULL;
    protected $_lang           = NULL;
    protected $_extConf        = array();
    
    public function __construct( tslib_piBase $plugin )
    {
        if( self::$_hasStatic === false ) {
            
            self::_setStaticVars();
        }
        
        $this->_plugin  = $plugin;
        $this->_content = new tx_oop_Xhtml_Tag( 'div' );
        $this->_lang    = tx_oop_Lang_Getter::getInstance( 'EXT:' . $this->_plugin->extKey . '/lang/' . get_class( $this ) . '.xml' );
        
        $this->_cssClass( 'catalog', $this->_content );
        
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] ) ) {
            
            $this->_extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'marvin_catalog' ] );
        }
    }
    
    public function __toString()
    {
        return ( string )$this->_content;
    }
    
    private static function _setStaticVars()
    {
        self::$_tsfe      = $GLOBALS[ 'TSFE' ];
        self::$_hasStatic = true;
    }
    
    protected function _getRootline()
    {
        $categories = tx_marvincatalog_Magento_Category_Helper::getInstance();
        $catalog    = tx_marvincatalog_Magento_Catalog_Helper::getInstance();
        $collection = $categories->getCategoryByKey( $this->_plugin->piVars[ 'collection' ] );
        $items      = array();
        $rootline   = new tx_oop_Xhtml_Tag( 'div' );
        $list       = $rootline->ul;
        
        $this->_cssClass( 'rootline', $rootline );
        
        $link            = new tx_oop_Xhtml_Tag( 'a' );
        $link[ 'title' ] = $this->_lang->collection;
        $link[ 'href' ]  = $this->_link( array( 'collection' => false ) );
        
        $link->addTextData( $this->_lang->collection );
        
        $items[] = $link;
        
        if( $this->_plugin->piVars[ 'collection' ] === 'mood-generator' ) {
            
            array_shift( $items );
            
            $link            = new tx_oop_Xhtml_Tag( 'a' );
            $link[ 'title' ] = $this->_lang->collection;
            $link[ 'href' ]  = $this->_link( array(), true, $this->_plugin->conf[ 'moodGeneratorPid' ] );
            
            $link->addTextData( $this->_lang->moodGenerator );
            
            $items[] = $link;
            
        } else {
        
            if( $collection ) {
                
                $link            = new tx_oop_Xhtml_Tag( 'a' );
                $link[ 'title' ] = $collection->name;
                $link[ 'href' ]  = $this->_link();
                
                $link->addTextData( $collection->name );
                
                $items[] = $link;
            }
            
            if( isset( $this->_plugin->piVars[ 'category' ] ) ) {
                
                $category = $categories->getCategoryByKey( $this->_plugin->piVars[ 'category' ] );
                
                if( $category ) {
                    
                    $link            = new tx_oop_Xhtml_Tag( 'a' );
                    $link[ 'title' ] = $category->name;
                    $link[ 'href' ]  = $this->_link(
                        array(
                            'action'   => 'catalog-category',
                            'category' => $this->_plugin->piVars[ 'category' ]
                        )
                    );
                    
                    $link->addTextData( $category->name );
                    
                    $items[] = $link;
                }
            }
        }
        
        if( isset( $this->_plugin->piVars[ 'watch' ] ) ) {
                
            $watch = $catalog->getWatchByKey( $this->_plugin->piVars[ 'watch' ] );
            
            if( $watch ) {
                
                $link            = new tx_oop_Xhtml_Tag( 'a' );
                $link[ 'title' ] = $watch->sku;
                $link[ 'href' ]  = $this->_link(
                    array(
                        'action'   => 'catalog-watch',
                        'category' => $this->_plugin->piVars[ 'category' ],
                        'watch'    => $this->_plugin->piVars[ 'watch' ]
                    )
                );
                
                $link->addTextData( $watch->sku );
                
                $items[] = $link;
            }
        }
        
        $split = self::$_tsfe->tmpl->splitConfArray( array( $this->_plugin->conf[ 'rootlineItemWrap' ] ), count( $items ) );
        
        foreach( $items as $key => $value ) {
            
            $list->li->div = $this->_plugin->cObj->wrap( ( string )$value, $split[ $key ][ 0 ] );
            
        }
        
        return $rootline;
    }
    
    public function getContent()
    {
        return $this->_content;
    }
    
    protected function _link( array $params = array(), $cache = true, $id = 0 )
    {
        $id = ( $id === 0 ) ? self::$_tsfe->id : ( int )$id;
        
        $ts = array(
            'parameter' => $id
        );
        
        if( ( boolean )$cache === false ) {
            
            $ts[ 'no_cache' ] = 1;
            
        } else {
            
            $ts[ 'useCacheHash' ] = 1;
        }
        
        if( !isset( $params[ 'collection' ] ) && isset( $this->_plugin->piVars[ 'collection' ] ) ) {
            
            $params[ 'collection' ] = $this->_plugin->piVars[ 'collection' ];
        }
        
        if( count( $params ) ) {
            
            $query = '';
            
            foreach( $params as $key => $value ) {
                
                if( $key == 'collection' && $value === false ) {
                    
                    continue;
                }
                
                $query .= '&' . $this->_plugin->prefixId . '[' . $key . ']' . '=' . $value;
            }
            
            $ts[ 'additionalParams' ] = $query;
        }
        
        return $this->_plugin->cObj->typolink_URL( $ts );
    }
    
    protected function _getCategoryImagePath( $name )
    {
        $dir = $this->_extConf[ 'magento_dir' ]
             . 'media'
             . DIRECTORY_SEPARATOR
             . 'catalog'
             . DIRECTORY_SEPARATOR
             . 'category'
             . DIRECTORY_SEPARATOR;
        
        return $dir . $name;
    }
    
    protected function _getProductImagePath( $name )
    {
        $dir = $this->_extConf[ 'magento_dir' ]
             . 'media'
             . DIRECTORY_SEPARATOR
             . 'catalog'
             . DIRECTORY_SEPARATOR
             . 'product'
             . DIRECTORY_SEPARATOR;
        
        return $dir . $name;
    }
    
    protected function _createImage( $name, $file )
    {
        $conf    = $this->_plugin->conf[ 'imgConf.' ];
        $imgNum  = $this->_plugin->conf[ 'imgConfImageNumber' ];
        $textNum = $this->_plugin->conf[ 'imgConfTextNumber' ];
        
        $conf[ 'altText' ]   = $name;
        $conf[ 'titleText' ] = $name;
        
        $conf[ 'file.' ][ $imgNum . '.' ][ 'file' ]  = $file;
        $conf[ 'file.' ][ $textNum . '.' ][ 'text' ] = $name;
        
        return $this->_plugin->cObj->IMAGE( $conf );
    }
    
    protected function _cssClass( $name, tx_oop_Xhtml_Tag $tag )
    {
        $piNum          = substr( $this->_plugin->prefixId, strrpos( $this->_plugin->prefixId, '_' ) + 1 );
        $tag[ 'class' ] = 'tx-marvincatalog-' . $piNum . '-' . $name;
    }
    
    protected function _initSlider( $id )
    {
        $slider           = new tx_oop_Xhtml_Tag( 'script' );
        $slider[ 'type' ] = 'text/javascript';
        $slider->addTextData(
            '
            $( document ).ready(
                function()
                { 
                    $( "#' . $id . '" ).easySlider(
                        {
                            prevId:         \'tx-marvincatalog-pi1-slider-previous\',
                            nextId:         \'tx-marvincatalog-pi1-slider-next\',
                            prevText:       \'<span>' . $this->_lang->previousPage . '</span>\',
                            nextText:       \'<span>' . $this->_lang->nextPage . '</span>\',
                            controlsBefore: \'<div id="tx-marvincatalog-pi1-slider-controls">\',
                            controlsAfter:  \'</div>\',
                            speed:          ' . $this->_plugin->conf[ 'sliderSpeed' ] . '
                        }
                    );
                }
            );
            '
        );
        
        self::$_tsfe->additionalHeaderData[ $this->_plugin->prefixId . '_sliderInit' ] = ( string )$slider;
    }
    
    protected function _setPageTitle( $title, $replace = false, $separator = ' - ' )
    {
        $title     = ( string )$title;
        $separator = ( string )$separator;
        
        if( ( boolean )$replace == true ) {
            
            $GLOBALS[ 'TSFE' ]->page[ 'title' ] = $title;
            $GLOBALS[ 'TSFE' ]->indexedDocTitle = $title;
            
        } else {
            
            $GLOBALS[ 'TSFE' ]->page[ 'title' ] .= $separator . $title;
            $GLOBALS[ 'TSFE' ]->indexedDocTitle .= $separator . $title;
        }
        
        if( isset( $this->_plugin->conf[ 'titleAddText' ] ) && $this->_plugin->conf[ 'titleAddText' ] ) {
            
            $GLOBALS[ 'TSFE' ]->page[ 'title' ] .= ' ' . $this->_plugin->conf[ 'titleAddText' ];
        }
    }
}
