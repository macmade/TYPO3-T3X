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

# $Id: MobileCategory.class.php 195 2010-01-27 08:57:11Z macmade $

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
class tx_marvincatalog_View_MobileCategory extends tx_marvincatalog_View_Base
{
    protected $_catalog    = NULL;
    protected $_categories = NULL;
    protected $_category   = NULL;
    protected $_watches    = array();
    
    public function __construct( tslib_piBase $plugin, $category )
    {
        parent::__construct( $plugin );
        
        $this->_catalog     = tx_marvincatalog_Magento_Catalog_Helper::getInstance();
        $this->_categories  = tx_marvincatalog_Magento_Category_Helper::getInstance();
        $this->_category    = $category;
        $this->_watches     = $this->_catalog->getWatchesForCategoryKey( $category->key );
        $title              = $this->_content->h2;
        $backLink           = $title->a;
        $backLink[ 'href' ] = $this->_link( array() );
        $watches            = $this->_content->div;
        
        $backLink->addTextData( $this->_lang->collections );
        $title->addTextData( ' &gt; ' .  $category->name );
        $this->_cssClass( 'watches', $watches );
        
        $magentoUrl = $this->_extConf[ 'magento_url' ];
        
        foreach($this->_watches as $key => $value ) {
            
            $imgPath         = $magentoUrl . 'media/catalog/product' . $value->image;
            $watch           = $watches->div;
            $watch->div      = $value->name;
            $link            = $watch->div->a;
            $link[ 'href' ]  = $this->_link( array( 'action' => 'mobileCatalog-watch', 'category' => $category->key, 'watch' => $value->key ) );
            $img             = $link->img;
            $img[ 'alt' ]    = $value->sku;
            $img[ 'title' ]  = $value->name;
            $img[ 'src' ]    = $imgPath;
            $img[ 'width' ]  = $this->_plugin->conf[ 'smallImgWidth' ];
            
            $this->_cssClass( 'watch', $watch );
        }
    }
}

// X-Class inclusion
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/MobileCategory.class.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/marvin_catalog/classes/View/MobileCategory.class.php']);
}
