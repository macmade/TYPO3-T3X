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

# $Id: Watch.class.php 194 2010-01-27 08:55:55Z macmade $

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
class tx_marvincatalog_View_Watch extends tx_marvincatalog_View_Base
{
    protected $_catalog    = NULL;
    protected $_categories = NULL;
    protected $_collection = NULL;
    protected $_category   = NULL;
    protected $_watch      = NULL;
    
    public function __construct( tslib_piBase $plugin, $watch )
    {
        parent::__construct( $plugin );
        
        if( !( $watch instanceof stdClass ) ) {
            
            $error = $this->_content->div;
            $error->addTextData( $this->_lang->errorNoRecord );
            
        } else {
            
            $this->_catalog    = tx_marvincatalog_Magento_Catalog_Helper::getInstance();
            $this->_categories = tx_marvincatalog_Magento_Category_Helper::getInstance();
            $this->_collection = $this->_categories->getCategory( ( int )$this->_plugin->conf[ 'category' ] );
            $this->_category   = $this->_categories->getCategoryByKey( $this->_plugin->piVars[ 'category' ] );
            
            $this->_watch = $watch;
            $details      = $this->_content->div;
            $top          = $details->div;
            $left         = $details->div;
            $right        = $details->div;
            
            if( isset( $this->_plugin->piVars[ 'collection' ] ) ) {
                
                $rootCategory = $this->_categories->getCategoryByKey( $this->_plugin->piVars[ 'collection' ] );
                
                if( $rootCategory ) {
                    
                    $rootCategoryName = ' - ' . $rootCategory->name;
                }
            }
            
            $this->_setPageTitle( strtoupper( $this->_watch->sku ) . ' - ' . $this->_category->name . $rootCategoryName );
            
            $this->_cssClass( 'details', $details );
            $this->_cssClass( 'top', $top );
            $this->_cssClass( 'left', $left );
            $this->_cssClass( 'right', $right );
            
            $top->addChildNode( $this->_getRootline() );
            
            $this->_showNavigation( $top );
            $this->_showWatchDetails( $left );
            $this->_showWatchImage( $right );
            $this->_showWatchButtons( $left );
        }
    }
    
    protected function _showNavigation( tx_oop_Xhtml_Tag $container )
    {
        $watches = $this->_catalog->getWatchesForCategory( $this->_category->id );
        $navig   = $container->div;
        
        $this->_cssClass( 'navig-prevNext', $navig );
        
        if( !count( $watches ) ) {
            
            return;
        }
        
        $prev    = NULL;
        $next    = NULL;
        $sku     = '';
        
        foreach( $watches as $key => $value ) {
            
            if( $value->sku === $this->_watch->sku ) {
                
                $prev = ( $sku ) ? $watches[ $sku ] : true;
                
            } elseif( $prev && !$next ) {
                
                $next = $watches[ $key ];
            }
            
            $sku = $key;
        }
        
        #$buttonPrev = $navig->div;
        #$buttonNext = $navig->div;
        
        if( is_object( $prev ) ) {
            
            $buttonPrev = $navig->div;
            
            $link            = $buttonPrev->a;
            $link[ 'title' ] = $prev->sku;
            $link[ 'href' ]  = $this->_link(
                array(
                    'category' => $this->_plugin->piVars[ 'category' ],
                    'watch'    => $prev->key,
                    'action'   => 'catalog-watch'
                )
            );
            
            $link->addTextData( $this->_createButtonImage( 'previous', $buttonPrev ) );
            
        } else {
            
            #$buttonPrev->addTextData( $this->_createButtonImage( 'previousOff', $buttonPrev ) );
        }
        
        if( is_object( $next ) ) {
            
            $buttonNext = $navig->div;
            
            $link            = $buttonNext->a;
            $link[ 'title' ] = $next->sku;
            $link[ 'href' ]  = $this->_link(
                array(
                    'category' => $this->_plugin->piVars[ 'category' ],
                    'watch'    => $next->key,
                    'action'   => 'catalog-watch'
                )
            );
            
            $link->addTextData( $this->_createButtonImage( 'next', $buttonNext ) );
            
        } else {
            
            #$buttonNext->addTextData( $this->_createButtonImage( 'nextOff', $buttonNext ) );
        }
    }
    
    protected function _showWatchDetails( tx_oop_Xhtml_Tag $container )
    {
        $div = $container->div;
        
        $this->_cssClass( 'details', $div );
        
        $div->h2  = $this->_watch->sku;
        
        if( $this->_watch->description && $this->_watch->description !== 'Description' ) {
            
            $div->h3  = $this->_lang->story;
            $div->div = $this->_watch->description;
        }
        
        $div->h3  = $this->_lang->technical;
        $infos    = $div->div;
        $this->_cssClass( 'infosTable', $infos );
        
        $movement = $infos->div;
        $features = $infos->div;
        $case     = $infos->div;
        $glass    = $infos->div;
        $atm      = $infos->div;
        $bracelet = $infos->div;
        $dial     = $infos->div;
        
        $movementLabel = $movement->div;
        $featuresLabel = $features->div;
        $caseLabel     = $case->div;
        $glassLabel    = $glass->div;
        $atmLabel      = $atm->div;
        $braceletLabel = $bracelet->div;
        $dialLabel     = $dial->div;
        
        $this->_cssClass( 'label', $movementLabel );
        $this->_cssClass( 'label', $featuresLabel );
        $this->_cssClass( 'label', $caseLabel );
        $this->_cssClass( 'label', $glassLabel );
        $this->_cssClass( 'label', $atmLabel );
        $this->_cssClass( 'label', $braceletLabel );
        $this->_cssClass( 'label', $dialLabel );
        
        $movementLabel->addTextData( $this->_lang->movement );
        $featuresLabel->addTextData( $this->_lang->features );
        $caseLabel->addTextData( $this->_lang->getLabel( 'case' ) );
        $glassLabel->addTextData( $this->_lang->glass );
        $atmLabel->addTextData( $this->_lang->atm );
        $braceletLabel->addTextData( $this->_lang->bracelet );
        $dialLabel->addTextData( $this->_lang->dial );
        
        $movement->div = $this->_watch->movement;
        $features->div = $this->_watch->display;
        $case->div = $this->_watch->watchCase;
        $glass->div = $this->_watch->glass;
        $atm->div = $this->_watch->atm;
        $bracelet->div = $this->_watch->bracelet;
        $dial->div = $this->_watch->dial;
    }
    
    protected function _showWatchImage( tx_oop_Xhtml_Tag $container )
    {
        $div              = $container->div;
        $div[ 'id' ]      = $this->_plugin->prefixId . '_detailImage';
        
        $this->_cssClass( 'detailImage', $div );
        
        $imgKey          = $this->_plugin->conf[ 'watchDetailsImageKey' ];
        $magentoUrl      = $this->_extConf[ 'magento_url' ];
        $imgPath         = '/media/catalog/product/' . $this->_watch->$imgKey;
        $img             = new tx_oop_Xhtml_Tag( 'img' );
        $img[ 'src' ]    = $magentoUrl . $imgPath;
        
        if( $this->_plugin->conf[ 'disableZoom' ] ) {
            
            $link = $div;
            
        } else {
            
            $link             = $div->a;
            $link[ 'href' ]  = $img[ 'src' ];
            
            $script           = $div->script;
            $script[ 'type' ] = 'text/javascript';
            
            $script->addTextData(
                '$( "#' . $div[ 'id' ] . ' a" ).fancybox( { overlayShow: false } );'
            );
        }
        
        if(    isset( $this->_plugin->conf[ 'detailImage.' ][ 'maxWidth' ] )
            && $this->_plugin->conf[ 'detailImage.' ][ 'maxWidth' ]
        ) {
            
            $img[ 'width' ] = $this->_plugin->conf[ 'detailImage.' ][ 'maxWidth' ];
        }
        
        if(    isset( $this->_plugin->conf[ 'detailImage.' ][ 'maxHeight' ] )
            && $this->_plugin->conf[ 'detailImage.' ][ 'maxHeight' ]
        ) {
            
            $img[ 'height' ] = $this->_plugin->conf[ 'detailImage.' ][ 'maxHeight' ];
        }
        
        $link->addChildNode( $img );
        
        $imgPath = t3lib_extMgm::siteRelPath( $this->_plugin->extKey ) . 'res/js/jquery-lightbox/images/';
    }
    
    protected function _showWatchButtons( tx_oop_Xhtml_Tag $container )
    {
        $buttons = $container->div;
        
        $this->_cssClass( 'buttons', $buttons );
        
        $this->_twitterButton( $buttons->div );
        $this->_facebookButton( $buttons->div );
        $this->_sendButton( $buttons->div );
        #$this->_shopButton( $buttons->div );
    }
    
    protected function _createButtonImage( $name, tx_oop_Xhtml_Tag $container )
    {
        $img  = new tx_oop_Xhtml_Tag( 'span' );
        $conf = $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'image.' ];
        $text = '';
        
        if(    isset( $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'class' ] )
            && $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'class' ]
        ) {
            $container[ 'class' ] = $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'class' ];
        }
        
        if(    isset( $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'text' ] )
            && $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'text' ]
        ) {
            $text = new tx_oop_Xhtml_Tag( 'span' );
            
            $text->addTextData( $this->_plugin->conf[ 'buttons.' ][ $name . '.' ][ 'text' ] );
            $this->_cssClass( 'button-text', $text );
        }
        
        $img->addTextData( $this->_plugin->cObj->IMAGE( $conf ) );
        $this->_cssClass( 'button-image', $img );
        
        return $img . $text;
    }
    
    protected function _facebookButton( tx_oop_Xhtml_Tag $container )
    {
        $url            = urlencode( t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' ) );
        $link           = $container->a;
        $link[ 'href' ] = 'http://www.facebook.com/share.php?u=' . $url;
        
        $link->addTextData( $this->_createButtonImage( 'facebook', $container ) );
    }
    
    protected function _sendButton( tx_oop_Xhtml_Tag $container )
    {
        $link             = $container->a;
        $link[ 'href' ]   = $this->_link( array( 'action' => 'catalog-sendEmail', 'watch' => $this->_plugin->piVars[ 'watch' ], 'category' => $this->_plugin->piVars[ 'category' ] ), false );
        
        $link->addTextData( $this->_createButtonImage( 'send', $container ) );
    }
    
    protected function _twitterButton( tx_oop_Xhtml_Tag $container )
    {
        $conf = $this->_plugin->conf[ 'bitly.' ];
        
        if( !$conf[ 'login' ] || ! $conf[ 'apiKey' ] ) {
            
            return;
        }
        
        $url            = new tx_marvincatalog_Bitly_Url_Shortener( $conf[ 'login' ], $conf[ 'apiKey' ] );
        $status         = sprintf( $this->_plugin->conf[ 'twitterStatus' ], $url->shorten( t3lib_div::getIndpEnv( 'TYPO3_REQUEST_URL' ) ) );
        $link           = $container->a;
        $link[ 'href' ] = 'http://twitter.com/home?status=' . urlencode( $status );
        
        $link->addTextData( $this->_createButtonImage( 'twitter', $container ) );
    }
    
    protected function _shopButton( tx_oop_Xhtml_Tag $container )
    {
        $url            = 'index.php/' . $this->_collection->key . '/' . $this->_category->key . '/' . $this->_watch->key . '.html';
        $link           = $container->a;
        $link[ 'href' ] = $this->_extConf[ 'magento_url' ] . $url;
        
        $link->addTextData( $this->_createButtonImage( 'shop', $container ) );
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/Watch.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/Watch.class.php']);
}
