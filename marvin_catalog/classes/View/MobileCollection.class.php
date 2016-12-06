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

# $Id: MobileCollection.class.php 195 2010-01-27 08:57:11Z macmade $

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
class tx_marvincatalog_View_MobileCollection extends tx_marvincatalog_View_Base
{
    protected $_categories        = NULL;
    protected $_collections       = NULL;
    
    public function __construct( tslib_piBase $plugin, $collections )
    {
        parent::__construct( $plugin );
        
        $this->_categories  = tx_marvincatalog_Magento_Category_Helper::getInstance();
        $this->_collections = $collections;
        $top                = $this->_content->a;
        $top[ 'name' ]      = $this->_plugin->prefixId . '_top';
        $collections        = $this->_content->div;
        
        $lady     = $collections->div;
        $gent     = $collections->div;
        $lady->h2 = $this->_lang->lady;
        $gent->h2 = $this->_lang->gent;
        
        $this->_cssClass( 'collection', $collections );
        $this->_cssClass( 'gender', $lady );
        $this->_cssClass( 'gender', $gent );
        
        $magentoUrl = $this->_extConf[ 'magento_url' ];
        
        foreach( $this->_collections as $key => $value ) {
            
            $gender = substr( $value->key, 0, strpos( $value->key, '-' ) );
            $type   = substr( $value->key, strpos( $value->key, '-' ) + 1 );
            
            if( ( $gender !== 'lady' && $gender !== 'gent' ) || ( $type !== 'mechanical' && $type !== 'quartz' ) ) {
                
                continue;
            }
            
            $img             = $this->_plugin->conf[ 'buttons.' ][ $type . '.' ][ 'image.' ];
            $div             = $$gender;
            $anchorName      = $this->_plugin->prefixId . '_' . $value->key;
            $link            = $div->div->a;
            $link[ 'title' ] = $value->name;
            $link[ 'href' ]  = $this->_link( array() ) . '#' . $anchorName;
            
            $link->addTextData( $this->_plugin->cObj->IMAGE( $img ) );
            
            $collection         = $this->_content->div;
            $anchor             = $collection->a;
            $anchor[ 'name' ]   = $anchorName;
            $title              = $collection->h2;
            $backLink           = $title->a;
            $backLink[ 'href' ] = $this->_link( array() ) . '#' . $top[ 'name' ];
            
            $this->_cssClass( 'categoryTitle', $title );
            $backLink->addTextData( $this->_lang->collections );
            $title->addTextData( ' &gt; ' . $value->name );
            
            if( !isset( $value->children ) ) {
                
                continue;
            }
            
            $categories = $collection->div;
            
            $this->_cssClass( 'categories', $categories );
            
            foreach( $value->children as $subKey => $subValue ) {
                
                $imgPath         = $magentoUrl . '/media/catalog/category/' . $subValue->image;
                $category        = $categories->div;
                $category->div   = $subValue->name;
                $link            = $category->div->a;
                $link[ 'href' ]  = $this->_link( array( 'action' => 'mobileCatalog-category', 'category' => $subValue->key ) );
                $link[ 'title' ] = $subValue->name;
                $img             = $link->img;
                $img[ 'alt' ]    = $subValue->key;
                $img[ 'title' ]  = $subValue->name;
                $img[ 'src' ]    = $imgPath;
                $img[ 'width' ]  = $this->_plugin->conf[ 'smallImgWidth' ];
                
                $this->_cssClass( 'category', $category );
            }
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/MobileCollection.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/MobileCollection.class.php']);
}
